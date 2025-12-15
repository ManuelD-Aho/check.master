<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Helper pour la génération de PDF
 * Nécessite une librairie comme TCPDF, DomPDF ou FPDF.
 * Ici instancié comme wrapper ou stub.
 */
class PdfHelper
{
    public static function generate(string $html, string $filename, string $orientation = 'P'): string
    {
        // Placeholder pour l'implémentation réelle
        // Enregistre le fichier temporairement ou retourne le contenu

        // Si aucune lib n'est installée, on peut juste logger ou simuler
        $path = dirname(__DIR__, 2) . '/storage/uploads/generated/' . $filename;
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $html); // Juste pour l'exemple
        return $path;
    }
}
