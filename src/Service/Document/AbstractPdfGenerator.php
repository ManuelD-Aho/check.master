<?php
declare(strict_types=1);

namespace App\Service\Document;

use App\Service\System\SettingsService;
use DateTimeImmutable;
use DateTimeInterface;
use RuntimeException;
use TCPDF;

abstract class AbstractPdfGenerator
{
    protected SettingsService $settings;
    protected string $storagePath;

    public function __construct(SettingsService $settings, string $storagePath)
    {
        $this->settings = $settings;
        $this->storagePath = rtrim($storagePath, '/\\');
    }

    abstract public function generate(array $data): string;

    protected function createPdf(): TCPDF
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('Application');
        $pdf->SetAuthor($this->getUniversityName());
        $pdf->SetMargins(15, 35, 15);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->setImageScale(1.25);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->setLanguageArray([
            'a_meta_charset' => 'UTF-8',
            'a_meta_language' => 'fr',
            'w_page' => 'page',
        ]);

        return $pdf;
    }

    protected function setHeader(TCPDF $pdf, string $title): void
    {
        $logo = $this->getLogoPath();
        $university = $this->getUniversityName();
        $subtitle = (string) $this->settings->get('university_subtitle', $this->settings->get('universite_sous_titre', ''));
        $address = $this->getUniversityAddress();
        $pages = $pdf->getNumPages();

        for ($page = 1; $page <= $pages; $page++) {
            $pdf->setPage($page);
            $pdf->SetY(8);
            $startX = 15;

            if ($logo !== '') {
                $pdf->Image($logo, 15, 8, 18);
                $startX = 38;
            }

            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetXY($startX, 8);
            $pdf->Cell(0, 5, $university, 0, 1, 'L');

            if ($subtitle !== '') {
                $pdf->SetFont('helvetica', '', 10);
                $pdf->SetX($startX);
                $pdf->Cell(0, 4, $subtitle, 0, 1, 'L');
            }

            if ($address !== '') {
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetX($startX);
                $pdf->MultiCell(0, 4, $address, 0, 'L');
            }

            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->SetY(25);
            $pdf->Cell(0, 8, $title, 0, 1, 'C');
            $pdf->Line(15, 33, $pdf->getPageWidth() - 15, 33);
        }
    }

    protected function setFooter(TCPDF $pdf): void
    {
        $pages = $pdf->getNumPages();
        for ($page = 1; $page <= $pages; $page++) {
            $pdf->setPage($page);
            $pdf->SetY(-15);
            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->Cell(0, 8, 'Page ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), 0, 0, 'C');
        }
    }

    protected function savePdf(TCPDF $pdf, string $filename): string
    {
        if (!is_dir($this->storagePath)) {
            if (!mkdir($this->storagePath, 0775, true) && !is_dir($this->storagePath)) {
                throw new RuntimeException('Cannot create documents directory.');
            }
        }

        $safeName = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $filename) ?? 'document';

        if (!str_ends_with(strtolower($safeName), '.pdf')) {
            $safeName .= '.pdf';
        }

        $path = $this->storagePath . DIRECTORY_SEPARATOR . $safeName;
        $pdf->Output($path, 'F');

        return $path;
    }

    protected function getLogoPath(): string
    {
        $keys = ['university_logo', 'universite_logo', 'logo_universite', 'app_logo', 'logo'];
        $logo = '';

        foreach ($keys as $key) {
            $value = (string) $this->settings->get($key, '');
            if ($value !== '') {
                $logo = $value;
                break;
            }
        }

        if ($logo === '') {
            return '';
        }

        if (is_file($logo)) {
            return $logo;
        }

        $base = dirname($this->storagePath);
        $candidate = $base . DIRECTORY_SEPARATOR . ltrim($logo, '/\\');

        return is_file($candidate) ? $candidate : '';
    }

    protected function formatDate(mixed $value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('d/m/Y');
        }

        if (is_int($value)) {
            return (new DateTimeImmutable('@' . $value))->format('d/m/Y');
        }

        if (is_string($value) && $value !== '') {
            try {
                return (new DateTimeImmutable($value))->format('d/m/Y');
            } catch (\Throwable) {
                return $value;
            }
        }

        return '';
    }

    protected function formatAmount(mixed $value, string $currency = 'FCFA'): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (is_string($value) && is_numeric($value)) {
            $value = (float) $value;
        }

        if (is_int($value) || is_float($value)) {
            $formatted = number_format((float) $value, 0, '.', ' ');
            return $formatted . ' ' . $currency;
        }

        return $this->stringValue($value);
    }

    protected function formatPerson(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            $full = $this->stringValue($value['nom_complet'] ?? $value['nomComplet'] ?? '');
            if ($full !== '') {
                return $full;
            }
            $nom = $this->stringValue($value['nom'] ?? '');
            $prenom = $this->stringValue($value['prenom'] ?? '');
            return trim($prenom . ' ' . $nom);
        }

        if (is_object($value)) {
            if (method_exists($value, 'getNomComplet')) {
                return $this->stringValue($value->getNomComplet());
            }
            $nom = method_exists($value, 'getNom') ? $this->stringValue($value->getNom()) : '';
            $prenom = method_exists($value, 'getPrenom') ? $this->stringValue($value->getPrenom()) : '';
            return trim($prenom . ' ' . $nom);
        }

        return $this->stringValue($value);
    }

    protected function pick(mixed $source, array $keys): string
    {
        if ($source === null) {
            return '';
        }

        foreach ($keys as $key) {
            if (is_array($source) && array_key_exists($key, $source)) {
                return $this->stringValue($source[$key]);
            }

            if (is_object($source)) {
                $method = 'get' . str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', (string) $key)));
                if (method_exists($source, $method)) {
                    return $this->stringValue($source->$method());
                }
                if (property_exists($source, (string) $key)) {
                    return $this->stringValue($source->$key);
                }
            }
        }

        return '';
    }

    protected function stringValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'Oui' : 'Non';
        }

        if (is_string($value)) {
            return trim($value);
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            $pieces = [];
            foreach ($value as $entry) {
                $text = $this->stringValue($entry);
                if ($text !== '') {
                    $pieces[] = $text;
                }
            }
            return implode(' ', $pieces);
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format('d/m/Y');
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return '';
    }

    protected function tableRow(string $label, string $value): string
    {
        return '<tr><td width="35%"><strong>' . htmlspecialchars($label, ENT_QUOTES) . '</strong></td><td width="65%">' . htmlspecialchars($value !== '' ? $value : '-', ENT_QUOTES) . '</td></tr>';
    }

    protected function listItems(array $items): string
    {
        if ($items === []) {
            return '<p>Aucun element.</p>';
        }

        $html = '<ul>';
        foreach ($items as $item) {
            $text = $this->stringValue($item);
            if ($text === '') {
                continue;
            }
            $html .= '<li>' . htmlspecialchars($text, ENT_QUOTES) . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    private function getUniversityName(): string
    {
        $value = (string) $this->settings->get('university_name', '');
        if ($value !== '') {
            return $value;
        }
        $value = (string) $this->settings->get('universite_nom', '');
        if ($value !== '') {
            return $value;
        }
        $value = (string) $this->settings->get('app_name', 'Universite');
        return $value !== '' ? $value : 'Universite';
    }

    private function getUniversityAddress(): string
    {
        $address = (string) $this->settings->get('university_address', $this->settings->get('universite_adresse', ''));
        $city = (string) $this->settings->get('university_city', $this->settings->get('universite_ville', ''));
        $country = (string) $this->settings->get('university_country', $this->settings->get('universite_pays', ''));

        $parts = array_filter([$address, $city, $country], static fn ($part) => $part !== '');

        return implode(' - ', $parts);
    }
}
