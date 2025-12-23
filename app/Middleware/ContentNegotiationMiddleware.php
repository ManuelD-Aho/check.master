<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;

/**
 * Middleware Content Negotiation
 * 
 * Gère la négociation de contenu HTTP (Accept, Accept-Language, Accept-Encoding).
 * Détermine le format de réponse optimal basé sur les préférences du client.
 */
class ContentNegotiationMiddleware
{
    /**
     * Types de contenu supportés avec leurs qualités par défaut
     */
    private const SUPPORTED_CONTENT_TYPES = [
        'application/json' => 1.0,
        'text/html' => 0.9,
        'text/plain' => 0.5,
        'application/xml' => 0.3,
    ];

    /**
     * Langues supportées avec leurs qualités par défaut
     */
    private const SUPPORTED_LANGUAGES = [
        'fr' => 1.0,     // Français (langue principale)
        'fr-FR' => 1.0,
        'en' => 0.8,     // Anglais
        'en-US' => 0.8,
        'en-GB' => 0.8,
    ];

    /**
     * Encodages supportés
     */
    private const SUPPORTED_ENCODINGS = [
        'gzip',
        'deflate',
        'identity',
    ];

    /**
     * Type de contenu négocié
     */
    private static ?string $negotiatedContentType = null;

    /**
     * Langue négociée
     */
    private static ?string $negotiatedLanguage = null;

    /**
     * Encodage négocié
     */
    private static ?string $negotiatedEncoding = null;

    /**
     * Exécute le middleware
     *
     * @param callable $next La fonction suivante dans la chaîne
     * @return Response|mixed Réponse HTTP
     */
    public function handle(callable $next): mixed
    {
        // Négocier le type de contenu
        self::$negotiatedContentType = $this->negotiateContentType();
        
        // Négocier la langue
        self::$negotiatedLanguage = $this->negotiateLanguage();
        
        // Négocier l'encodage
        self::$negotiatedEncoding = $this->negotiateEncoding();

        // Stocker dans les globales pour accès facile
        $GLOBALS['content_type'] = self::$negotiatedContentType;
        $GLOBALS['language'] = self::$negotiatedLanguage;
        $GLOBALS['encoding'] = self::$negotiatedEncoding;

        // Exécuter la requête
        $response = $next();

        // Ajouter les en-têtes de négociation
        return $this->addNegotiationHeaders($response);
    }

    /**
     * Négocie le type de contenu basé sur l'en-tête Accept
     */
    private function negotiateContentType(): string
    {
        $acceptHeader = Request::header('Accept');
        
        if ($acceptHeader === null || $acceptHeader === '' || $acceptHeader === '*/*') {
            return 'application/json'; // Défaut pour API
        }

        $preferences = $this->parseAcceptHeader($acceptHeader);
        
        // Trouver le meilleur match
        $bestMatch = null;
        $bestQuality = 0;

        foreach (self::SUPPORTED_CONTENT_TYPES as $type => $serverQuality) {
            foreach ($preferences as $clientType => $clientQuality) {
                if ($this->matchContentType($clientType, $type)) {
                    $combinedQuality = $clientQuality * $serverQuality;
                    if ($combinedQuality > $bestQuality) {
                        $bestQuality = $combinedQuality;
                        $bestMatch = $type;
                    }
                }
            }
        }

        return $bestMatch ?? 'application/json';
    }

    /**
     * Négocie la langue basée sur l'en-tête Accept-Language
     */
    private function negotiateLanguage(): string
    {
        $acceptLanguage = Request::header('Accept-Language');
        
        if ($acceptLanguage === null || $acceptLanguage === '' || $acceptLanguage === '*') {
            return 'fr'; // Défaut français
        }

        $preferences = $this->parseAcceptHeader($acceptLanguage);
        
        // Trouver le meilleur match
        $bestMatch = null;
        $bestQuality = 0;

        foreach (self::SUPPORTED_LANGUAGES as $lang => $serverQuality) {
            foreach ($preferences as $clientLang => $clientQuality) {
                if ($this->matchLanguage($clientLang, $lang)) {
                    $combinedQuality = $clientQuality * $serverQuality;
                    if ($combinedQuality > $bestQuality) {
                        $bestQuality = $combinedQuality;
                        $bestMatch = $lang;
                    }
                }
            }
        }

        // Normaliser (garder uniquement le code principal)
        if ($bestMatch !== null && str_contains($bestMatch, '-')) {
            $bestMatch = substr($bestMatch, 0, 2);
        }

        return $bestMatch ?? 'fr';
    }

    /**
     * Négocie l'encodage basé sur l'en-tête Accept-Encoding
     */
    private function negotiateEncoding(): string
    {
        $acceptEncoding = Request::header('Accept-Encoding');
        
        if ($acceptEncoding === null || $acceptEncoding === '') {
            return 'identity';
        }

        $preferences = $this->parseAcceptHeader($acceptEncoding);
        
        // Préférer gzip si disponible et supporté par le client
        foreach (['gzip', 'deflate', 'identity'] as $encoding) {
            if (isset($preferences[$encoding]) || isset($preferences['*'])) {
                if (in_array($encoding, self::SUPPORTED_ENCODINGS, true)) {
                    // Vérifier que l'extension est disponible
                    if ($encoding === 'gzip' && !function_exists('gzencode')) {
                        continue;
                    }
                    if ($encoding === 'deflate' && !function_exists('gzdeflate')) {
                        continue;
                    }
                    return $encoding;
                }
            }
        }

        return 'identity';
    }

    /**
     * Parse un en-tête Accept avec les qualités
     *
     * @return array<string, float>
     */
    private function parseAcceptHeader(string $header): array
    {
        $preferences = [];
        $parts = array_map('trim', explode(',', $header));

        foreach ($parts as $part) {
            $params = array_map('trim', explode(';', $part));
            $value = array_shift($params);
            
            if ($value === null || $value === '') {
                continue;
            }

            // Chercher le paramètre de qualité
            $quality = 1.0;
            foreach ($params as $param) {
                if (str_starts_with($param, 'q=')) {
                    $quality = (float) substr($param, 2);
                    break;
                }
            }

            $preferences[$value] = $quality;
        }

        // Trier par qualité décroissante
        arsort($preferences);

        return $preferences;
    }

    /**
     * Vérifie si un type de contenu client correspond à un type serveur
     */
    private function matchContentType(string $clientType, string $serverType): bool
    {
        // Match exact
        if ($clientType === $serverType) {
            return true;
        }

        // Match wildcard (*/* ou type/*)
        if ($clientType === '*/*') {
            return true;
        }

        $clientParts = explode('/', $clientType);
        $serverParts = explode('/', $serverType);

        if (count($clientParts) !== 2 || count($serverParts) !== 2) {
            return false;
        }

        // Match type/* avec type/subtype
        if ($clientParts[1] === '*' && $clientParts[0] === $serverParts[0]) {
            return true;
        }

        return false;
    }

    /**
     * Vérifie si une langue client correspond à une langue serveur
     */
    private function matchLanguage(string $clientLang, string $serverLang): bool
    {
        // Match exact
        if (strtolower($clientLang) === strtolower($serverLang)) {
            return true;
        }

        // Match de préfixe (fr correspond à fr-FR)
        $clientPrefix = strtolower(substr($clientLang, 0, 2));
        $serverPrefix = strtolower(substr($serverLang, 0, 2));

        return $clientPrefix === $serverPrefix;
    }

    /**
     * Ajoute les en-têtes de négociation à la réponse
     */
    private function addNegotiationHeaders(mixed $response): mixed
    {
        if (!$response instanceof Response) {
            return $response;
        }

        return $response
            ->header('Content-Language', self::$negotiatedLanguage ?? 'fr')
            ->header('Vary', 'Accept, Accept-Language, Accept-Encoding');
    }

    /**
     * Retourne le type de contenu négocié
     */
    public static function getContentType(): string
    {
        return self::$negotiatedContentType ?? 'application/json';
    }

    /**
     * Retourne la langue négociée
     */
    public static function getLanguage(): string
    {
        return self::$negotiatedLanguage ?? 'fr';
    }

    /**
     * Retourne l'encodage négocié
     */
    public static function getEncoding(): string
    {
        return self::$negotiatedEncoding ?? 'identity';
    }

    /**
     * Vérifie si le client accepte JSON
     */
    public static function acceptsJson(): bool
    {
        return self::$negotiatedContentType === 'application/json';
    }

    /**
     * Vérifie si le client accepte HTML
     */
    public static function acceptsHtml(): bool
    {
        return self::$negotiatedContentType === 'text/html';
    }

    /**
     * Vérifie si le client est francophone
     */
    public static function isFrench(): bool
    {
        $lang = self::$negotiatedLanguage ?? 'fr';
        return str_starts_with($lang, 'fr');
    }

    /**
     * Retourne une réponse adaptée au type de contenu négocié
     *
     * @param array<string, mixed> $data
     */
    public static function respond(array $data, int $statusCode = 200): Response
    {
        $contentType = self::getContentType();

        switch ($contentType) {
            case 'application/json':
                return new JsonResponse($data, $statusCode);

            case 'application/xml':
                return self::xmlResponse($data, $statusCode);

            case 'text/html':
                return Response::html(self::arrayToHtml($data), $statusCode);

            case 'text/plain':
            default:
                return Response::text(print_r($data, true), $statusCode);
        }
    }

    /**
     * Convertit un tableau en XML et retourne une réponse
     *
     * @param array<string, mixed> $data
     */
    private static function xmlResponse(array $data, int $statusCode): Response
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><response/>');
        self::arrayToXml($data, $xml);

        $response = new Response($xml->asXML() ?: '', $statusCode);
        return $response->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Convertit récursivement un tableau en XML
     *
     * @param array<string, mixed> $data
     */
    private static function arrayToXml(array $data, \SimpleXMLElement $xml): void
    {
        foreach ($data as $key => $value) {
            // Normaliser la clé pour XML
            if (is_numeric($key)) {
                $key = 'item';
            }
            $key = preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $key) ?? 'item';

            if (is_array($value)) {
                $child = $xml->addChild($key);
                if ($child !== null) {
                    self::arrayToXml($value, $child);
                }
            } else {
                $xml->addChild($key, htmlspecialchars((string) $value));
            }
        }
    }

    /**
     * Convertit un tableau en HTML simple
     *
     * @param array<string, mixed> $data
     */
    private static function arrayToHtml(array $data): string
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Response</title></head><body>';
        $html .= '<pre>' . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '') . '</pre>';
        $html .= '</body></html>';

        return $html;
    }

    /**
     * Réinitialise l'état (utile pour les tests)
     */
    public static function reset(): void
    {
        self::$negotiatedContentType = null;
        self::$negotiatedLanguage = null;
        self::$negotiatedEncoding = null;
        unset($GLOBALS['content_type'], $GLOBALS['language'], $GLOBALS['encoding']);
    }
}
