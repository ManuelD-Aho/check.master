<?php
declare(strict_types=1);

namespace App\Service\Rapport;

class ContentSanitizerService
{
    private const ALLOWED_TAGS = '<p><br><strong><em><u><h1><h2><h3><h4><ul><ol><li><table><tr><td><th><thead><tbody><a><img><blockquote><pre><code><span><div><sub><sup>';

    public function sanitize(string $html): string
    {
        $clean = strip_tags($html, self::ALLOWED_TAGS);
        $clean = preg_replace('/\bon\w+\s*=\s*"[^"]*"/i', '', $clean);
        $clean = preg_replace('/\bon\w+\s*=\s*\'[^\']*\'/i', '', $clean);
        $clean = preg_replace('/\bon\w+\s*=\s*[^\s>]*/i', '', $clean);
        $clean = preg_replace('/j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:/iu', '', $clean);

        return $clean;
    }

    public function stripAll(string $html): string
    {
        return strip_tags($html);
    }

    public function countWords(string $text): int
    {
        $plain = $this->extractPlainText($text);
        if ($plain === '') {
            return 0;
        }

        return str_word_count($plain);
    }

    public function estimatePages(int $wordCount, int $wordsPerPage = 300): int
    {
        if ($wordCount <= 0) {
            return 0;
        }

        return (int)ceil($wordCount / $wordsPerPage);
    }

    public function extractPlainText(string $html): string
    {
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
