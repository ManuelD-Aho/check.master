<?php
declare(strict_types=1);

if (!class_exists('TCPDF')) {
    class TCPDF
    {
        public function __construct(string $orientation = 'P', string $unit = 'mm', string|array $format = 'A4', bool $unicode = true, string $encoding = 'UTF-8', bool $diskcache = false)
        {
        }

        public function SetCreator(string $creator): void
        {
        }

        public function SetAuthor(string $author): void
        {
        }

        public function SetMargins(float $left, float $top, float $right): void
        {
        }

        public function SetAutoPageBreak(bool $auto, float $margin = 0): void
        {
        }

        public function setImageScale(float $scale): void
        {
        }

        public function setPrintHeader(bool $val): void
        {
        }

        public function setPrintFooter(bool $val): void
        {
        }

        public function SetFont(string $family, string $style = '', float $size = 0): void
        {
        }

        public function setLanguageArray(array $lang): void
        {
        }

        public function AddPage(): void
        {
        }

        public function getNumPages(): int
        {
            return 1;
        }

        public function setPage(int $page): void
        {
        }

        public function SetY(float $y): void
        {
        }

        public function SetX(float $x): void
        {
        }

        public function SetXY(float $x, float $y): void
        {
        }

        public function Cell(float $w, float $h = 0, string $txt = '', int $border = 0, int $ln = 0, string $align = '', bool $fill = false, string $link = '', int $stretch = 0): void
        {
        }

        public function MultiCell(float $w, float $h, string $txt, int|string $border = 0, string $align = '', bool $fill = false, int $ln = 1): void
        {
        }

        public function Line(float $x1, float $y1, float $x2, float $y2): void
        {
        }

        public function getPageWidth(): float
        {
            return 0.0;
        }

        public function getAliasNumPage(): string
        {
            return '{PAGENO}';
        }

        public function getAliasNbPages(): string
        {
            return '{nb}';
        }

        public function Image(string $file, float $x = 0, float $y = 0, float $w = 0, float $h = 0): void
        {
        }

        public function writeHTML(string $html, bool $ln = true, bool $fill = false, bool $reseth = true, bool $cell = false, string $align = ''): void
        {
        }

        public function Output(string $name = 'doc.pdf', string $dest = 'I'): void
        {
        }
    }
}
