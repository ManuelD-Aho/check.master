<?php

declare(strict_types=1);

namespace App\Services\Documents;

use App\Models\ImportHistorique;
use App\Services\Security\ServiceAudit;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use Src\Exceptions\ImportException;

/**
 * Service Excel
 * 
 * Import/Export Excel avec PhpSpreadsheet.
 * Supporte XLSX et CSV.
 */
class ServiceExcel
{
    private const STORAGE_DIR = 'storage/exports';

    /**
     * Exporte des données vers Excel
     */
    public static function exporter(
        array $donnees,
        array $colonnes,
        string $nomFichier,
        ?int $utilisateurId = null
    ): string {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes
        $col = 'A';
        foreach ($colonnes as $colonne) {
            $sheet->setCellValue($col . '1', $colonne);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $col++;
        }

        // Données
        $row = 2;
        foreach ($donnees as $ligne) {
            $col = 'A';
            foreach ($colonnes as $key => $label) {
                $value = is_array($ligne) ? ($ligne[$key] ?? '') : ($ligne->$key ?? '');
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Auto-dimensionner les colonnes
        foreach (range('A', chr(ord('A') + count($colonnes) - 1)) as $colLetter) {
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Sauvegarder
        $chemin = self::getStoragePath() . '/' . $nomFichier . '_' . date('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($chemin);

        ServiceAudit::log('export_excel', 'fichier', null, [
            'nom_fichier' => $nomFichier,
            'lignes' => count($donnees),
        ]);

        return $chemin;
    }

    /**
     * Importe des données depuis Excel
     */
    public static function importer(
        string $cheminFichier,
        array $mappingColonnes,
        ?int $utilisateurId = null
    ): array {
        if (!file_exists($cheminFichier)) {
            throw new ImportException('Fichier non trouvé');
        }

        $extension = strtolower(pathinfo($cheminFichier, PATHINFO_EXTENSION));
        
        $reader = match ($extension) {
            'xlsx' => new XlsxReader(),
            'csv' => new CsvReader(),
            default => throw new ImportException("Extension non supportée: {$extension}"),
        };

        $spreadsheet = $reader->load($cheminFichier);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $donnees = [];
        $erreurs = [];

        // Lire les en-têtes (première ligne)
        $entetes = [];
        foreach (range('A', $highestColumn) as $col) {
            $entetes[$col] = $sheet->getCell($col . '1')->getValue();
        }

        // Lire les données
        for ($row = 2; $row <= $highestRow; $row++) {
            $ligne = [];
            $ligneValide = true;

            foreach ($mappingColonnes as $champDb => $colExcel) {
                $colLettre = self::trouverColonne($entetes, $colExcel);
                if ($colLettre !== null) {
                    $ligne[$champDb] = $sheet->getCell($colLettre . $row)->getValue();
                } else {
                    $ligneValide = false;
                    $erreurs[] = "Ligne {$row}: Colonne '{$colExcel}' non trouvée";
                }
            }

            if ($ligneValide && !empty(array_filter($ligne))) {
                $donnees[] = $ligne;
            }
        }

        // Enregistrer l'historique d'import
        $historique = new ImportHistorique([
            'type_import' => 'excel',
            'fichier_original' => basename($cheminFichier),
            'lignes_total' => $highestRow - 1,
            'lignes_succes' => count($donnees),
            'lignes_erreur' => count($erreurs),
            'erreurs_json' => json_encode($erreurs),
            'utilisateur_id' => $utilisateurId,
        ]);
        $historique->save();

        ServiceAudit::log('import_excel', 'fichier', $historique->getId(), [
            'lignes_importees' => count($donnees),
            'erreurs' => count($erreurs),
        ]);

        return [
            'donnees' => $donnees,
            'erreurs' => $erreurs,
            'total' => $highestRow - 1,
            'succes' => count($donnees),
        ];
    }

    /**
     * Trouve la lettre de colonne correspondant à un en-tête
     */
    private static function trouverColonne(array $entetes, string $nomColonne): ?string
    {
        foreach ($entetes as $col => $entete) {
            if (strcasecmp(trim((string) $entete), trim($nomColonne)) === 0) {
                return $col;
            }
        }
        return null;
    }

    /**
     * Génère un template Excel vide avec les colonnes attendues
     */
    public static function genererTemplate(array $colonnes, string $nomFichier): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $col = 'A';
        foreach ($colonnes as $colonne) {
            $sheet->setCellValue($col . '1', $colonne);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $chemin = self::getStoragePath() . '/template_' . $nomFichier . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($chemin);

        return $chemin;
    }

    /**
     * Retourne le chemin de stockage
     */
    private static function getStoragePath(): string
    {
        $path = dirname(__DIR__, 3) . '/' . self::STORAGE_DIR;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return $path;
    }
}
