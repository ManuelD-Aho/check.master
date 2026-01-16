<?php

declare(strict_types=1);

namespace Src\Excel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Src\Exceptions\AppException;

/**
 * Excel Handler - Manipulation avancée de fichiers Excel
 * 
 * Fonctionnalités:
 * - Lecture/écriture Excel (XLSX, XLS, CSV)
 * - Styles et formatage
 * - Formules
 * - Graphiques
 * - Images
 * - Multi-feuilles
 * - Export de données
 * - Import avec validation
 * - Templates
 * 
 * @package Src\Excel
 */
class ExcelHandler
{
    private ?Spreadsheet $spreadsheet = null;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'default_font' => 'Arial',
            'default_font_size' => 11,
            'header_font_size' => 12,
            'header_bold' => true,
            'auto_size_columns' => true
        ], $config);
    }

    /**
     * Créer un nouveau spreadsheet
     *
     * @return self
     */
    public function create(): self
    {
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName($this->config['default_font'])
            ->setSize($this->config['default_font_size']);

        return $this;
    }

    /**
     * Charger un fichier Excel existant
     *
     * @param string $filepath Chemin du fichier
     * @return self
     * @throws AppException
     */
    public function load(string $filepath): self
    {
        if (!file_exists($filepath)) {
            throw new AppException("Fichier Excel introuvable: {$filepath}");
        }

        try {
            $reader = new XlsxReader();
            $this->spreadsheet = $reader->load($filepath);
            return $this;
        } catch (\Exception $e) {
            throw new AppException("Erreur chargement Excel: " . $e->getMessage());
        }
    }

    /**
     * Obtenir la feuille active
     *
     * @return \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public function getActiveSheet()
    {
        if ($this->spreadsheet === null) {
            $this->create();
        }

        return $this->spreadsheet->getActiveSheet();
    }

    /**
     * Créer une nouvelle feuille
     *
     * @param string $title Titre de la feuille
     * @return self
     */
    public function createSheet(string $title): self
    {
        if ($this->spreadsheet === null) {
            $this->create();
        }

        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle($title);
        $this->spreadsheet->setActiveSheetIndex($this->spreadsheet->getSheetCount() - 1);

        return $this;
    }

    /**
     * Sélectionner une feuille par son index
     *
     * @param int $index Index
     * @return self
     */
    public function selectSheet(int $index): self
    {
        $this->spreadsheet->setActiveSheetIndex($index);
        return $this;
    }

    /**
     * Écrire des données dans une cellule
     *
     * @param string $cell Cellule (ex: 'A1')
     * @param mixed $value Valeur
     * @return self
     */
    public function setCellValue(string $cell, $value): self
    {
        $this->getActiveSheet()->setCellValue($cell, $value);
        return $this;
    }

    /**
     * Écrire un tableau de données
     *
     * @param array $data Données (tableau 2D)
     * @param string $startCell Cellule de départ
     * @param bool $headers Première ligne = headers
     * @return self
     */
    public function writeArray(array $data, string $startCell = 'A1', bool $headers = true): self
    {
        $sheet = $this->getActiveSheet();
        $sheet->fromArray($data, null, $startCell);

        if ($headers && !empty($data)) {
            $this->styleHeaders($startCell, count($data[0]));
        }

        if ($this->config['auto_size_columns']) {
            $this->autoSizeColumns();
        }

        return $this;
    }

    /**
     * Lire les données d'une feuille
     *
     * @param int|null $sheetIndex Index de la feuille
     * @param bool $firstRowHeaders Première ligne = headers
     * @return array Données
     */
    public function readSheet(?int $sheetIndex = null, bool $firstRowHeaders = true): array
    {
        if ($sheetIndex !== null) {
            $this->selectSheet($sheetIndex);
        }

        $sheet = $this->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        if ($firstRowHeaders && !empty($data)) {
            $headers = array_shift($data);
            $result = [];

            foreach ($data as $row) {
                $assoc = [];
                foreach ($headers as $col => $header) {
                    $assoc[$header] = $row[$col] ?? null;
                }
                $result[] = $assoc;
            }

            return $result;
        }

        return $data;
    }

    /**
     * Styler les headers
     *
     * @param string $startCell Cellule de départ
     * @param int $columnCount Nombre de colonnes
     * @return self
     */
    public function styleHeaders(string $startCell, int $columnCount): self
    {
        $sheet = $this->getActiveSheet();
        [$startCol, $startRow] = Coordinate::coordinateFromString($startCell);
        $endCol = Coordinate::stringFromColumnIndex(
            Coordinate::columnIndexFromString($startCol) + $columnCount - 1
        );

        $range = "{$startCol}{$startRow}:{$endCol}{$startRow}";

        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => $this->config['header_bold'],
                'size' => $this->config['header_font_size']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9E2F3']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        return $this;
    }

    /**
     * Auto-dimensionner les colonnes
     *
     * @return self
     */
    public function autoSizeColumns(): self
    {
        $sheet = $this->getActiveSheet();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $column = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return $this;
    }

    /**
     * Appliquer des filtres automatiques
     *
     * @param string|null $range Range (null = auto-detect)
     * @return self
     */
    public function setAutoFilter(?string $range = null): self
    {
        $sheet = $this->getActiveSheet();

        if ($range === null) {
            $range = $sheet->calculateWorksheetDimension();
        }

        $sheet->setAutoFilter($range);

        return $this;
    }

    /**
     * Figer les volets
     *
     * @param string $cell Cellule de gel
     * @return self
     */
    public function freezePane(string $cell): self
    {
        $this->getActiveSheet()->freezePane($cell);
        return $this;
    }

    /**
     * Ajouter une formule
     *
     * @param string $cell Cellule
     * @param string $formula Formule (ex: '=SUM(A1:A10)')
     * @return self
     */
    public function setFormula(string $cell, string $formula): self
    {
        $this->getActiveSheet()->setCellValue($cell, $formula);
        return $this;
    }

    /**
     * Fusionner des cellules
     *
     * @param string $range Range (ex: 'A1:C1')
     * @return self
     */
    public function mergeCells(string $range): self
    {
        $this->getActiveSheet()->mergeCells($range);
        return $this;
    }

    /**
     * Définir la largeur d'une colonne
     *
     * @param string $column Colonne
     * @param float $width Largeur
     * @return self
     */
    public function setColumnWidth(string $column, float $width): self
    {
        $this->getActiveSheet()->getColumnDimension($column)->setWidth($width);
        return $this;
    }

    /**
     * Définir la hauteur d'une ligne
     *
     * @param int $row Ligne
     * @param float $height Hauteur
     * @return self
     */
    public function setRowHeight(int $row, float $height): self
    {
        $this->getActiveSheet()->getRowDimension($row)->setRowHeight($height);
        return $this;
    }

    /**
     * Appliquer un style personnalisé
     *
     * @param string $range Range
     * @param array $styleArray Style
     * @return self
     */
    public function applyStyle(string $range, array $styleArray): self
    {
        $this->getActiveSheet()->getStyle($range)->applyFromArray($styleArray);
        return $this;
    }

    /**
     * Exporter vers un fichier
     *
     * @param string $filepath Chemin de destination
     * @param string $format Format (xlsx, csv, html)
     * @return bool Succès
     * @throws AppException
     */
    public function export(string $filepath, string $format = 'xlsx'): bool
    {
        if ($this->spreadsheet === null) {
            throw new AppException("Aucun spreadsheet à exporter");
        }

        try {
            $dir = dirname($filepath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            switch (strtolower($format)) {
                case 'xlsx':
                    $writer = new Xlsx($this->spreadsheet);
                    break;
                case 'csv':
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($this->spreadsheet);
                    break;
                case 'html':
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($this->spreadsheet);
                    break;
                default:
                    throw new AppException("Format non supporté: {$format}");
            }

            $writer->save($filepath);
            return true;

        } catch (\Exception $e) {
            throw new AppException("Erreur export Excel: " . $e->getMessage());
        }
    }

    /**
     * Télécharger le fichier
     *
     * @param string $filename Nom du fichier
     * @param string $format Format
     * @return void
     */
    public function download(string $filename, string $format = 'xlsx'): void
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Exporter vers CSV en mémoire
     *
     * @return string Contenu CSV
     */
    public function toCSV(): string
    {
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($this->spreadsheet);

        ob_start();
        $writer->save('php://output');
        $csv = ob_get_clean();

        return $csv;
    }

    /**
     * Importer depuis CSV
     *
     * @param string $filepath Chemin CSV
     * @param string $delimiter Délimiteur
     * @return array Données
     */
    public function importCSV(string $filepath, string $delimiter = ','): array
    {
        if (!file_exists($filepath)) {
            throw new AppException("Fichier CSV introuvable: {$filepath}");
        }

        $data = [];
        if (($handle = fopen($filepath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Valider les données d'un fichier Excel
     *
     * @param array $rules Règles de validation
     * @return array Erreurs
     */
    public function validate(array $rules): array
    {
        $errors = [];
        $data = $this->readSheet();

        foreach ($data as $rowIndex => $row) {
            foreach ($rules as $column => $rule) {
                $value = $row[$column] ?? null;

                if ($rule['required'] ?? false) {
                    if (empty($value)) {
                        $errors[] = "Ligne " . ($rowIndex + 2) . ": {$column} requis";
                    }
                }

                if (isset($rule['type'])) {
                    switch ($rule['type']) {
                        case 'email':
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $errors[] = "Ligne " . ($rowIndex + 2) . ": {$column} email invalide";
                            }
                            break;
                        case 'numeric':
                            if (!is_numeric($value)) {
                                $errors[] = "Ligne " . ($rowIndex + 2) . ": {$column} doit être numérique";
                            }
                            break;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Obtenir le spreadsheet
     *
     * @return Spreadsheet|null
     */
    public function getSpreadsheet(): ?Spreadsheet
    {
        return $this->spreadsheet;
    }

    /**
     * Obtenir les métadonnées
     *
     * @return array Métadonnées
     */
    public function getMetadata(): array
    {
        if ($this->spreadsheet === null) {
            return [];
        }

        $properties = $this->spreadsheet->getProperties();

        return [
            'creator' => $properties->getCreator(),
            'last_modified_by' => $properties->getLastModifiedBy(),
            'created' => $properties->getCreated(),
            'modified' => $properties->getModified(),
            'title' => $properties->getTitle(),
            'description' => $properties->getDescription(),
            'subject' => $properties->getSubject(),
            'keywords' => $properties->getKeywords(),
            'category' => $properties->getCategory(),
            'sheet_count' => $this->spreadsheet->getSheetCount()
        ];
    }

    /**
     * Définir les métadonnées
     *
     * @param array $metadata Métadonnées
     * @return self
     */
    public function setMetadata(array $metadata): self
    {
        $properties = $this->spreadsheet->getProperties();

        if (isset($metadata['creator'])) {
            $properties->setCreator($metadata['creator']);
        }
        if (isset($metadata['title'])) {
            $properties->setTitle($metadata['title']);
        }
        if (isset($metadata['description'])) {
            $properties->setDescription($metadata['description']);
        }
        if (isset($metadata['subject'])) {
            $properties->setSubject($metadata['subject']);
        }
        if (isset($metadata['keywords'])) {
            $properties->setKeywords($metadata['keywords']);
        }

        return $this;
    }
}
