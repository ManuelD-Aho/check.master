<?php
declare(strict_types=1);

// Script runner pour exécuter toutes les migrations et seeds SQL
// Placez ce fichier à la racine du projet (déjà fait par ce script).
// Il lit la configuration dans app/config/database.php et crée
// une table de suivi `applied_sql_files` pour ne pas ré-exécuter
// des fichiers déjà appliqués.

// Note: Ce script exécute le contenu brut des fichiers .sql via PDO::exec.
// Si vous avez des fichiers utilisant des DELIMITER pour procédures stockées,
// adaptez le fichier manuellement (ce runner n'est pas conçu pour gérer
// les blocs DELIMITER complexes).

$root = __DIR__;
$configPath = $root . '/app/config/database.php';
if (!file_exists($configPath)) {
    fwrite(STDERR, "Fichier de configuration introuvable: $configPath\n");
    exit(1);
}

/** @var array $dbConfig */
$dbConfig = require $configPath;
$default = $dbConfig['default'] ?? 'mysql';
if (!isset($dbConfig['connections'][$default])) {
    fwrite(STDERR, "Connexion DB par défaut non trouvée dans la config.\n");
    exit(1);
}
$conn = $dbConfig['connections'][$default];

$host = $conn['host'] ?? '127.0.0.1';
$port = $conn['port'] ?? '3306';
$database = $conn['database'] ?? null;
$user = $conn['username'] ?? $conn['user'] ?? 'root';
$pass = $conn['password'] ?? '';
$charset = $conn['charset'] ?? 'utf8mb4';

if (!$database) {
    fwrite(STDERR, "Nom de la base de données non renseigné dans la config.\n");
    exit(1);
}

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $database, $charset);
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    fwrite(STDERR, "Échec de connexion à la base de données: " . $e->getMessage() . "\n");
    exit(1);
}

// Crée une table de suivi si elle n'existe pas
$createTableSql = "CREATE TABLE IF NOT EXISTS applied_sql_files (
    filename VARCHAR(255) NOT NULL PRIMARY KEY,
    executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET={$charset};";
$pdo->exec($createTableSql);

// Collections pour rapport
$appliedFiles = [];
$skippedFiles = [];
$failedFiles = [];

function runDirectory(PDO $pdo, string $dirPath, string $label, array &$appliedFiles, array &$skippedFiles, array &$failedFiles): void
{
    if (!is_dir($dirPath)) {
        echo "Répertoire non trouvé : $dirPath\n";
        return;
    }

    $files = glob(rtrim($dirPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.sql');
    usort($files, 'strnatcmp');

    if (count($files) === 0) {
        echo "Aucun fichier .sql dans $dirPath\n";
        return;
    }

    echo "--- $label (" . count($files) . " fichiers) ---\n";

    foreach ($files as $file) {
        $filename = basename($file);

        // Vérifier si déjà appliqué
        $stmt = $pdo->prepare('SELECT 1 FROM applied_sql_files WHERE filename = ?');
        $stmt->execute([$filename]);
        if ($stmt->fetch()) {
            echo "Skipped: $filename (déjà appliqué)\n";
            $skippedFiles[] = $filename;
            continue;
        }

        echo "Applying: $filename... ";
        $sql = file_get_contents($file);
        if ($sql === false) {
            echo "ERREUR: impossible de lire le fichier\n";
            $failedFiles[$filename] = 'Lecture fichier impossible';
            continue;
        }

        try {
            // Désactiver temporairement les contraintes FK pour permettre l'exécution
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

            // Exécute le SQL brut (sans transaction, car les DDL font des commits implicites)
            $pdo->exec($sql);

            // Marquer comme appliqué
            $ins = $pdo->prepare('INSERT INTO applied_sql_files (filename) VALUES (?)');
            $ins->execute([$filename]);

            // Réactiver les contraintes
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

            echo "Done.\n";
            $appliedFiles[] = $filename;
        } catch (PDOException $e) {
            // Pas de rollback car pas de transaction explicite
             // Réactiver FK checks au cas où
             try {
                 $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
             } catch (Exception $inner) {
                 // ignore
             }

             $msg = $e->getMessage();
             echo "\nErreur lors de l'exécution de $filename: $msg\n";
             $failedFiles[$filename] = $msg;
             // Continuer avec les autres fichiers (ne pas exit)
             continue;
         }
    }
}

// Chemins relatifs depuis la racine du projet
$migrationsDir = $root . '/database/migrations';
$seedsDir = $root . '/database/seeds';

runDirectory($pdo, $migrationsDir, 'MIGRATIONS', $appliedFiles, $skippedFiles, $failedFiles);
runDirectory($pdo, $seedsDir, 'SEEDS', $appliedFiles, $skippedFiles, $failedFiles);

// Rapport final
echo "\n--- RÉSUMÉ ---\n";
echo "Appliqués (" . count($appliedFiles) . "):\n";
foreach ($appliedFiles as $f) { echo " - $f\n"; }

echo "\nSautés (" . count($skippedFiles) . "):\n";
foreach ($skippedFiles as $f) { echo " - $f\n"; }

echo "\nÉchecs (" . count($failedFiles) . "):\n";
foreach ($failedFiles as $f => $reason) { echo " - $f : $reason\n"; }

if (count($failedFiles) > 0) {
    echo "\nTerminé avec erreurs. Voir la liste ci-dessus.\n";
    exit(1);
}

echo "\nTerminé avec succès. Base de données : $database\n";

return 0;
