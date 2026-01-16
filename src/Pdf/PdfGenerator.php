<?php

declare(strict_types=1);

namespace Src\Pdf;

use TCPDF;
use Mpdf\Mpdf;
use Src\Exceptions\AppException;

/**
 * PDF Generator - Génération avancée de PDF
 * 
 * Support:
 * - TCPDF pour documents simples
 * - mPDF pour documents complexes avec CSS
 * - Templates HTML
 * - Headers/Footers personnalisés
 * - Watermarks
 * - Signatures numériques
 * - Génération par batch
 * - Compression
 * - Métadonnées
 * 
 * @package Src\Pdf
 */
class PdfGenerator
{
    private array $config;
    private string $engine = 'tcpdf'; // tcpdf ou mpdf
    private $pdf;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'orientation' => 'P', // P=Portrait, L=Landscape
            'unit' => 'mm',
            'format' => 'A4',
            'unicode' => true,
            'encoding' => 'UTF-8',
            'margins' => [15, 15, 15, 15], // left, top, right, bottom
            'author' => 'CheckMaster',
            'title' => 'Document',
            'subject' => '',
            'keywords' => '',
            'compress' => true
        ], $config);

        $this->engine = $config['engine'] ?? 'tcpdf';
    }

    /**
     * Générer un PDF à partir d'HTML
     *
     * @param string $html Contenu HTML
     * @param array $options Options
     * @return string Contenu PDF binaire
     * @throws AppException
     */
    public function generateFromHtml(string $html, array $options = []): string
    {
        if ($this->engine === 'mpdf') {
            return $this->generateWithMpdf($html, $options);
        }

        return $this->generateWithTcpdf($html, $options);
    }

    /**
     * Générer avec TCPDF
     *
     * @param string $html HTML
     * @param array $options Options
     * @return string PDF
     */
    private function generateWithTcpdf(string $html, array $options): string
    {
        $pdf = new TCPDF(
            $this->config['orientation'],
            $this->config['unit'],
            $this->config['format'],
            $this->config['unicode'],
            $this->config['encoding']
        );

        // Métadonnées
        $pdf->SetCreator($this->config['author']);
        $pdf->SetAuthor($this->config['author']);
        $pdf->SetTitle($options['title'] ?? $this->config['title']);
        $pdf->SetSubject($this->config['subject']);
        $pdf->SetKeywords($this->config['keywords']);

        // Marges
        [$left, $top, $right, $bottom] = $this->config['margins'];
        $pdf->SetMargins($left, $top, $right);
        $pdf->SetAutoPageBreak(true, $bottom);

        // Header/Footer personnalisés
        if (isset($options['header'])) {
            $pdf->setHeaderData('', 0, $options['header']['title'] ?? '', $options['header']['string'] ?? '');
        } else {
            $pdf->setPrintHeader(false);
        }

        if (!isset($options['footer']) || !$options['footer']) {
            $pdf->setPrintFooter(false);
        }

        // Police par défaut
        $pdf->SetFont($options['font'] ?? 'helvetica', '', $options['font_size'] ?? 10);

        // Ajouter page
        $pdf->AddPage();

        // Watermark
        if (isset($options['watermark'])) {
            $this->addWatermark($pdf, $options['watermark']);
        }

        // Contenu HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Compression
        if ($this->config['compress']) {
            $pdf->setCompression(true);
        }

        return $pdf->Output('', 'S');
    }

    /**
     * Générer avec mPDF
     *
     * @param string $html HTML
     * @param array $options Options
     * @return string PDF
     * @throws AppException
     */
    private function generateWithMpdf(string $html, array $options): string
    {
        try {
            $mpdfConfig = [
                'mode' => $this->config['encoding'],
                'format' => $this->config['format'],
                'orientation' => $this->config['orientation'],
                'margin_left' => $this->config['margins'][0],
                'margin_top' => $this->config['margins'][1],
                'margin_right' => $this->config['margins'][2],
                'margin_bottom' => $this->config['margins'][3],
                'tempDir' => sys_get_temp_dir()
            ];

            $pdf = new Mpdf($mpdfConfig);

            // Métadonnées
            $pdf->SetCreator($this->config['author']);
            $pdf->SetAuthor($this->config['author']);
            $pdf->SetTitle($options['title'] ?? $this->config['title']);
            $pdf->SetSubject($this->config['subject']);
            $pdf->SetKeywords($this->config['keywords']);

            // Header/Footer
            if (isset($options['header_html'])) {
                $pdf->SetHTMLHeader($options['header_html']);
            }

            if (isset($options['footer_html'])) {
                $pdf->SetHTMLFooter($options['footer_html']);
            }

            // Watermark
            if (isset($options['watermark'])) {
                $pdf->SetWatermarkText($options['watermark']);
                $pdf->showWatermarkText = true;
            }

            // CSS
            if (isset($options['css'])) {
                $pdf->WriteHTML($options['css'], \Mpdf\HTMLParserMode::HEADER_CSS);
            }

            // Contenu
            $pdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

            return $pdf->Output('', 'S');

        } catch (\Exception $e) {
            throw new AppException("Erreur génération PDF: " . $e->getMessage());
        }
    }

    /**
     * Ajouter un watermark
     *
     * @param TCPDF $pdf Instance PDF
     * @param string $text Texte
     * @return void
     */
    private function addWatermark(TCPDF $pdf, string $text): void
    {
        $pdf->SetAlpha(0.3);
        $pdf->SetFont('helvetica', 'B', 50);
        $pdf->SetTextColor(200, 200, 200);
        $pdf->StartTransform();
        $pdf->Rotate(45, 150, 150);
        $pdf->Text(50, 150, $text);
        $pdf->StopTransform();
        $pdf->SetAlpha(1);
    }

    /**
     * Générer un PDF à partir d'un template
     *
     * @param string $templatePath Chemin du template
     * @param array $data Données
     * @param array $options Options
     * @return string PDF
     */
    public function generateFromTemplate(string $templatePath, array $data, array $options = []): string
    {
        if (!file_exists($templatePath)) {
            throw new AppException("Template introuvable: {$templatePath}");
        }

        // Charger le template
        $html = file_get_contents($templatePath);

        // Remplacer les variables
        foreach ($data as $key => $value) {
            $html = str_replace('{{' . $key . '}}', htmlspecialchars((string) $value), $html);
        }

        return $this->generateFromHtml($html, $options);
    }

    /**
     * Générer plusieurs PDFs en batch
     *
     * @param array $items Items à générer
     * @param callable $callback Callback de génération
     * @return array Tableau de PDFs générés
     */
    public function generateBatch(array $items, callable $callback): array
    {
        $results = [];

        foreach ($items as $item) {
            try {
                $results[] = $callback($this, $item);
            } catch (\Exception $e) {
                $results[] = ['error' => $e->getMessage()];
            }
        }

        return $results;
    }

    /**
     * Sauvegarder le PDF
     *
     * @param string $pdfContent Contenu PDF
     * @param string $filepath Chemin de destination
     * @return bool Succès
     */
    public function save(string $pdfContent, string $filepath): bool
    {
        $dir = dirname($filepath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return file_put_contents($filepath, $pdfContent) !== false;
    }

    /**
     * Forcer le téléchargement
     *
     * @param string $pdfContent Contenu PDF
     * @param string $filename Nom du fichier
     * @return void
     */
    public function download(string $pdfContent, string $filename): void
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfContent));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        echo $pdfContent;
        exit;
    }

    /**
     * Afficher inline dans le navigateur
     *
     * @param string $pdfContent Contenu PDF
     * @param string $filename Nom du fichier
     * @return void
     */
    public function inline(string $pdfContent, string $filename): void
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfContent));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        echo $pdfContent;
        exit;
    }

    /**
     * Fusionner plusieurs PDFs
     *
     * @param array $pdfFiles Chemins des PDFs
     * @return string PDF fusionné
     * @throws AppException
     */
    public function merge(array $pdfFiles): string
    {
        // Nécessite pdftk ou similaire
        // Implémentation simplifiée
        throw new AppException("Fusion PDF non implémentée - nécessite extension externe");
    }

    /**
     * Obtenir les infos d'un PDF
     *
     * @param string $pdfPath Chemin du PDF
     * @return array Informations
     */
    public function getInfo(string $pdfPath): array
    {
        if (!file_exists($pdfPath)) {
            throw new AppException("PDF introuvable: {$pdfPath}");
        }

        return [
            'size' => filesize($pdfPath),
            'mime' => mime_content_type($pdfPath),
            'modified' => date('Y-m-d H:i:s', filemtime($pdfPath))
        ];
    }
}
