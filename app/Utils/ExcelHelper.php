<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Helper pour Excel
 * 
 * Utilitaires pour l'import/export Excel via PhpSpreadsheet.
 */
class ExcelHelper
{
    /**
     * Colonnes Excel (A-Z, AA-AZ, etc.)
     */
    private const COLUMNS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Crée un fichier Excel simple
     *
     * @param array<string> $headers En-têtes
     * @param array<array<mixed>> $data Données
     * @param string $filename Nom du fichier (sans extension)
     * @return string Chemin du fichier créé
     */
    public static function create(array $headers, array $data, string $filename = 'export'): string
    {
        // Vérifier que PhpSpreadsheet est disponible
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            throw new \RuntimeException('PhpSpreadsheet n\'est pas installé');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Ajouter les en-têtes
        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueByColumnAndRow($col, 1, $header);
            // Mettre en gras
            $sheet->getStyleByColumnAndRow($col, 1)->getFont()->setBold(true);
            $col++;
        }

        // Ajouter les données
        $row = 2;
        foreach ($data as $rowData) {
            $col = 1;
            foreach ($rowData as $value) {
                $sheet->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $row++;
        }

        // Auto-dimensionner les colonnes
        foreach (range('A', self::getColumnLetter(count($headers))) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Créer le fichier
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filepath = sys_get_temp_dir() . '/' . $filename . '_' . date('YmdHis') . '.xlsx';
        $writer->save($filepath);

        return $filepath;
    }

    /**
     * Lit un fichier Excel
     *
     * @param bool $hasHeaders Indique si la première ligne contient les en-têtes
     * @return array{headers: array<string>, data: array<array<mixed>>}
     */
    public static function read(string $filepath, bool $hasHeaders = true): array
    {
        if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            throw new \RuntimeException('PhpSpreadsheet n\'est pas installé');
        }

        if (!file_exists($filepath)) {
            throw new \InvalidArgumentException('Fichier non trouvé: ' . $filepath);
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filepath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (empty($rows)) {
            return ['headers' => [], 'data' => []];
        }

        $headers = [];
        $data = [];

        if ($hasHeaders) {
            $headers = array_map('trim', (array) array_shift($rows));
            foreach ($rows as $row) {
                $data[] = array_combine($headers, $row) ?: [];
            }
        } else {
            $data = $rows;
        }

        return [
            'headers' => $headers,
            'data' => $data,
        ];
    }

    /**
     * Exporte un tableau associatif en Excel
     *
     * @param array<array<string, mixed>> $data Données avec clés = en-têtes
     * @param string $filename Nom du fichier
     * @return string Chemin du fichier créé
     */
    public static function export(array $data, string $filename = 'export'): string
    {
        if (empty($data)) {
            return self::create([], [], $filename);
        }

        // Extraire les en-têtes depuis les clés
        $headers = array_keys((array) reset($data));

        // Convertir en tableau indexé
        $rows = [];
        foreach ($data as $row) {
            $rows[] = array_values((array) $row);
        }

        return self::create($headers, $rows, $filename);
    }

    /**
     * Exporte une collection avec style
     *
     * @param array<array<string, mixed>> $data
     * @param array<string, string> $columnMapping Mapping nom => libellé
     * @param string $title Titre de la feuille
     * @return string Chemin du fichier
     */
    public static function exportStyled(array $data, array $columnMapping, string $filename = 'export', string $title = ''): string
    {
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            throw new \RuntimeException('PhpSpreadsheet n\'est pas installé');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (!empty($title)) {
            $sheet->setTitle(mb_substr($title, 0, 31)); // Max 31 caractères
        }

        $startRow = 1;

        // Ajouter un titre si présent
        if (!empty($title)) {
            $sheet->setCellValue('A1', $title);
            $sheet->mergeCells('A1:' . self::getColumnLetter(count($columnMapping)) . '1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $startRow = 3;
        }

        // Ajouter les en-têtes
        $col = 1;
        foreach ($columnMapping as $key => $label) {
            $cellCoord = self::getColumnLetter($col) . $startRow;
            $sheet->setCellValue($cellCoord, $label);
            $sheet->getStyle($cellCoord)->getFont()->setBold(true);
            $sheet->getStyle($cellCoord)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E0E0E0');
            $col++;
        }

        // Ajouter les données
        $row = $startRow + 1;
        foreach ($data as $item) {
            $col = 1;
            foreach (array_keys($columnMapping) as $key) {
                $value = $item[$key] ?? '';
                $sheet->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $row++;
        }

        // Bordures
        $lastCol = self::getColumnLetter(count($columnMapping));
        $lastRow = $row - 1;
        $range = "A{$startRow}:{$lastCol}{$lastRow}";
        $sheet->getStyle($range)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Auto-dimensionner
        foreach (range('A', $lastCol) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Créer le fichier
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filepath = sys_get_temp_dir() . '/' . $filename . '_' . date('YmdHis') . '.xlsx';
        $writer->save($filepath);

        return $filepath;
    }

    /**
     * Valide un fichier Excel
     *
     * @param array<string> $requiredColumns Colonnes requises
     * @return array{valid: bool, errors: array<string>}
     */
    public static function validate(string $filepath, array $requiredColumns = []): array
    {
        $errors = [];

        // Vérifier l'extension
        $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['xlsx', 'xls', 'csv'], true)) {
            $errors[] = 'Format de fichier non supporté';
            return ['valid' => false, 'errors' => $errors];
        }

        // Vérifier que le fichier peut être lu
        try {
            $result = self::read($filepath);
        } catch (\Exception $e) {
            $errors[] = 'Impossible de lire le fichier: ' . $e->getMessage();
            return ['valid' => false, 'errors' => $errors];
        }

        // Vérifier les colonnes requises
        if (!empty($requiredColumns)) {
            $headers = array_map('strtolower', array_map('trim', $result['headers']));
            foreach ($requiredColumns as $required) {
                if (!in_array(strtolower($required), $headers, true)) {
                    $errors[] = "Colonne manquante: {$required}";
                }
            }
        }

        // Vérifier qu'il y a des données
        if (empty($result['data'])) {
            $errors[] = 'Le fichier ne contient pas de données';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Compte le nombre de lignes dans un fichier Excel
     */
    public static function countRows(string $filepath, bool $excludeHeader = true): int
    {
        $result = self::read($filepath);
        $count = count($result['data']);

        return $excludeHeader ? $count : $count + 1;
    }

    /**
     * Convertit un numéro de colonne en lettre
     */
    public static function getColumnLetter(int $column): string
    {
        $letter = '';

        while ($column > 0) {
            $mod = ($column - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $column = (int) (($column - $mod) / 26);
        }

        return $letter;
    }

    /**
     * Convertit une lettre de colonne en numéro
     */
    public static function getColumnNumber(string $letter): int
    {
        $letter = strtoupper($letter);
        $number = 0;
        $length = strlen($letter);

        for ($i = 0; $i < $length; $i++) {
            $number = $number * 26 + (ord($letter[$i]) - 64);
        }

        return $number;
    }

    /**
     * Génère un fichier Excel vide avec template
     *
     * @param array<string, string> $columns Colonnes avec descriptions
     * @return string Chemin du fichier
     */
    public static function createTemplate(array $columns, string $filename = 'template'): string
    {
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            throw new \RuntimeException('PhpSpreadsheet n\'est pas installé');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template');

        // En-têtes
        $col = 1;
        foreach (array_keys($columns) as $header) {
            $cellCoord = self::getColumnLetter($col) . '1';
            $sheet->setCellValue($cellCoord, $header);
            $sheet->getStyle($cellCoord)->getFont()->setBold(true);
            $sheet->getStyle($cellCoord)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle($cellCoord)->getFont()->getColor()->setRGB('FFFFFF');
            $col++;
        }

        // Descriptions (ligne 2)
        $col = 1;
        foreach ($columns as $description) {
            $cellCoord = self::getColumnLetter($col) . '2';
            $sheet->setCellValue($cellCoord, $description);
            $sheet->getStyle($cellCoord)->getFont()->setItalic(true)->setSize(9);
            $sheet->getStyle($cellCoord)->getFont()->getColor()->setRGB('808080');
            $col++;
        }

        // Auto-dimensionner
        $lastCol = self::getColumnLetter(count($columns));
        foreach (range('A', $lastCol) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Créer le fichier
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filepath = sys_get_temp_dir() . '/' . $filename . '_template.xlsx';
        $writer->save($filepath);

        return $filepath;
    }
}
