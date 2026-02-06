<?php
declare(strict_types=1);

namespace App\Service\Email;

use RuntimeException;

class TemplateRenderer
{
    private string $templatePath;

    public function __construct(string $templatePath)
    {
        $this->templatePath = rtrim($templatePath, '/\\') . DIRECTORY_SEPARATOR . 'email';
    }

    public function render(string $template, array $data = []): string
    {
        $path = $this->resolveTemplatePath($template);

        if (!is_file($path)) {
            throw new RuntimeException('Email template not found: ' . $template);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $path;
        $content = ob_get_clean();

        return is_string($content) ? $content : '';
    }

    public function exists(string $template): bool
    {
        $path = $this->resolveTemplatePath($template);
        return is_file($path);
    }

    private function resolveTemplatePath(string $template): string
    {
        $normalized = ltrim($template, '/\\');

        if (!str_ends_with($normalized, '.php')) {
            $normalized .= '.php';
        }

        return $this->templatePath . DIRECTORY_SEPARATOR . $normalized;
    }
}
