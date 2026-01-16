<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\Admin\ServiceAdministration;
use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Import/Export Admin
 * 
 * Gestion des opérations d'import et export de données.
 * 
 * @see PRD 08 - Administration
 */
class ImportExportController
{
    private ServiceAdministration $service;

    public function __construct()
    {
        $this->service = new ServiceAdministration();
    }

    /**
     * Vue import/export
     */
    public function index(): Response
    {
        if (!$this->checkAccess('configuration', 'lire')) {
            return Response::redirect('/dashboard');
        }

        $historique = $this->service->getHistoriqueImports(1, 20);

        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/admin/import_export.php';
        return Response::html((string) ob_get_clean());
    }

    /**
     * API: Historique des imports
     */
    public function historiqueImports(): JsonResponse
    {
        if (!$this->checkAccess('configuration', 'lire')) {
            return JsonResponse::forbidden();
        }

        $page = max(1, (int) Request::query('page', 1));
        $data = $this->service->getHistoriqueImports($page, 20);
        return JsonResponse::success($data);
    }

    /**
     * API: Import étudiants
     */
    public function importEtudiants(): JsonResponse
    {
        if (!$this->checkAccess('etudiants', 'creer')) {
            return JsonResponse::forbidden();
        }

        $file = $_FILES['file'] ?? null;
        if ($file === null || $file['error'] !== UPLOAD_ERR_OK) {
            return JsonResponse::error('Fichier non reçu ou erreur de téléchargement');
        }

        try {
            $lignes = $this->lireFichier($file['tmp_name']);
            
            // Appeler le service d'import
            $serviceAcademique = new \App\Services\Academique\ServiceEntitesAcademiques();
            $resultats = $serviceAcademique->importerEtudiants($lignes, Auth::id() ?? 0);

            // Enregistrer l'import
            $this->service->enregistrerImport(
                'etudiants',
                $file['name'],
                $resultats['total'],
                $resultats['reussis'],
                $resultats['erreurs'],
                Auth::id() ?? 0
            );

            return JsonResponse::success($resultats, sprintf(
                'Import terminé: %d réussis, %d erreurs',
                $resultats['reussis'],
                count($resultats['erreurs'])
            ));
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Export étudiants
     */
    public function exportEtudiants(): Response
    {
        if (!$this->checkAccess('etudiants', 'exporter')) {
            return Response::redirect('/dashboard');
        }

        $promotion = Request::query('promotion') ?: null;
        
        $serviceAcademique = new \App\Services\Academique\ServiceEntitesAcademiques();
        $data = $serviceAcademique->exporterEtudiants($promotion);

        // Générer CSV
        $csv = $this->genererCsv($data, [
            'num_etu' => 'Numéro étudiant',
            'nom_etu' => 'Nom',
            'prenom_etu' => 'Prénom',
            'email_etu' => 'Email',
            'telephone_etu' => 'Téléphone',
            'date_naiss_etu' => 'Date de naissance',
            'lieu_naiss_etu' => 'Lieu de naissance',
            'genre_etu' => 'Genre',
            'promotion_etu' => 'Promotion',
        ]);

        $filename = 'etudiants_' . date('Y-m-d_His') . '.csv';
        return Response::csv($csv, $filename);
    }

    /**
     * API: Export enseignants
     */
    public function exportEnseignants(): Response
    {
        if (!$this->checkAccess('enseignants', 'exporter')) {
            return Response::redirect('/dashboard');
        }

        $sql = "SELECT e.*, g.lib_grade, f.lib_fonction, s.lib_specialite
                FROM enseignants e
                LEFT JOIN grades g ON g.id_grade = e.grade_id
                LEFT JOIN fonctions f ON f.id_fonction = e.fonction_id
                LEFT JOIN specialites s ON s.id_specialite = e.specialite_id
                WHERE e.actif = 1
                ORDER BY e.nom_ens, e.prenom_ens";

        $stmt = \App\Orm\Model::raw($sql, []);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $csv = $this->genererCsv($data, [
            'nom_ens' => 'Nom',
            'prenom_ens' => 'Prénom',
            'email_ens' => 'Email',
            'telephone_ens' => 'Téléphone',
            'lib_grade' => 'Grade',
            'lib_fonction' => 'Fonction',
            'lib_specialite' => 'Spécialité',
        ]);

        $filename = 'enseignants_' . date('Y-m-d_His') . '.csv';
        return Response::csv($csv, $filename);
    }

    /**
     * API: Télécharger un template d'import
     */
    public function downloadTemplate(string $type): Response
    {
        $templates = [
            'etudiants' => [
                'headers' => ['num_etu', 'nom_etu', 'prenom_etu', 'email_etu', 'telephone_etu', 'date_naiss_etu', 'lieu_naiss_etu', 'genre_etu', 'promotion_etu'],
                'example' => ['AB12345678', 'DUPONT', 'Jean', 'jean.dupont@email.com', '+22501020304', '2000-01-15', 'Abidjan', 'Homme', '2024-2025'],
            ],
            'enseignants' => [
                'headers' => ['nom_ens', 'prenom_ens', 'email_ens', 'telephone_ens', 'grade_id', 'fonction_id', 'specialite_id'],
                'example' => ['MARTIN', 'Pierre', 'pierre.martin@ufhb.ci', '+22507080910', '3', '1', '1'],
            ],
        ];

        if (!isset($templates[$type])) {
            return Response::redirect('/admin/import');
        }

        $template = $templates[$type];
        $csv = implode(';', $template['headers']) . "\n";
        $csv .= implode(';', $template['example']) . "\n";

        $filename = "template_{$type}.csv";
        return Response::csv($csv, $filename);
    }

    /**
     * Lit un fichier CSV
     */
    private function lireFichier(string $cheminFichier): array
    {
        $lignes = [];
        $handle = fopen($cheminFichier, 'r');
        if ($handle === false) {
            return [];
        }

        $headers = fgetcsv($handle, 0, ';');
        if ($headers === false) {
            fclose($handle);
            return [];
        }

        // Nettoyer les headers
        $headers = array_map(fn($h) => trim((string) $h), $headers);

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $ligne = [];
            foreach ($headers as $index => $header) {
                $ligne[$header] = isset($row[$index]) ? trim((string) $row[$index]) : '';
            }
            $lignes[] = $ligne;
        }

        fclose($handle);
        return $lignes;
    }

    /**
     * Génère un CSV à partir de données
     */
    private function genererCsv(array $data, array $colonnes): string
    {
        if (empty($data)) {
            return implode(';', array_values($colonnes)) . "\n";
        }

        $csv = implode(';', array_values($colonnes)) . "\n";

        foreach ($data as $row) {
            $ligne = [];
            foreach (array_keys($colonnes) as $colonne) {
                $valeur = $row[$colonne] ?? '';
                // Échapper les points-virgules et guillemets
                if (str_contains((string) $valeur, ';') || str_contains((string) $valeur, '"')) {
                    $valeur = '"' . str_replace('"', '""', (string) $valeur) . '"';
                }
                $ligne[] = $valeur;
            }
            $csv .= implode(';', $ligne) . "\n";
        }

        return $csv;
    }

    /**
     * Vérifie les permissions
     */
    private function checkAccess(string $ressource, string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, $ressource, $action);
    }
}
