<?php

declare(strict_types=1);

namespace Src\Http;

/**
 * Wrapper pour les données de requête HTTP
 * 
 * Fournit un accès unifié aux données POST, GET, FILES et headers.
 * Interdit l'accès direct aux superglobales.
 */
class Request
{
    private static ?self $instance = null;
    private array $get;
    private array $post;
    private array $files;
    private array $server;
    private array $cookies;

    private function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
    }

    /**
     * Retourne l'instance singleton
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Réinitialise l'instance (utile pour les tests)
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Retourne toutes les données POST et GET fusionnées
     */
    public static function all(): array
    {
        $instance = self::getInstance();
        return array_merge($instance->get, $instance->post);
    }

    /**
     * Récupère une valeur depuis POST ou GET
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $instance = self::getInstance();
        return $instance->post[$key] ?? $instance->get[$key] ?? $default;
    }

    /**
     * Récupère une valeur POST uniquement
     */
    public static function post(string $key, mixed $default = null): mixed
    {
        $instance = self::getInstance();
        return $instance->post[$key] ?? $default;
    }

    /**
     * Récupère une valeur GET uniquement
     */
    public static function query(string $key, mixed $default = null): mixed
    {
        $instance = self::getInstance();
        return $instance->get[$key] ?? $default;
    }

    /**
     * Vérifie si une clé existe
     */
    public static function has(string $key): bool
    {
        $instance = self::getInstance();
        return isset($instance->post[$key]) || isset($instance->get[$key]);
    }

    /**
     * Retourne l'adresse IP du client
     */
    public static function ip(): string
    {
        $instance = self::getInstance();

        // Vérifier les headers de proxy
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if (!empty($instance->server[$header])) {
                $ip = $instance->server[$header];
                // X-Forwarded-For peut contenir plusieurs IPs
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * Retourne le User-Agent
     */
    public static function userAgent(): string
    {
        $instance = self::getInstance();
        return $instance->server['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Retourne la méthode HTTP
     */
    public static function method(): string
    {
        $instance = self::getInstance();
        return strtoupper($instance->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Vérifie si la requête est POST
     */
    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    /**
     * Vérifie si la requête est GET
     */
    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }

    /**
     * Vérifie si c'est une requête AJAX
     */
    public static function isAjax(): bool
    {
        $instance = self::getInstance();
        return ($instance->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    /**
     * Retourne l'URI de la requête (sans le base path)
     */
    public static function uri(): string
    {
        $instance = self::getInstance();
        $uri = $instance->server['REQUEST_URI'] ?? '/';
        
        // Retirer la query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Détecter et retirer le base path (ex: /check.master)
        $scriptName = $instance->server['SCRIPT_NAME'] ?? '';
        $basePath = dirname($scriptName);
        
        // Liste des bases paths potentiels à retirer
        // On inclut le basePath de SCRIPT_NAME et son parent si on est dans /public
        $basePaths = [$basePath];
        $normBasePath = str_replace('\\', '/', $basePath);
        if (basename($normBasePath) === 'public') {
            $basePaths[] = dirname($normBasePath);
        }

        foreach ($basePaths as $bp) {
            $bp = str_replace('\\', '/', $bp);
            if ($bp !== '/' && $bp !== '\\' && $bp !== '' && $bp !== '.') {
                $bp = rtrim($bp, '/');
                if (str_starts_with($uri, $bp)) {
                    $uri = substr($uri, strlen($bp));
                    break;
                }
            }
        }

        // Retirer index.php s'il est présent au début de l'URI résultante
        if (str_starts_with($uri, '/index.php')) {
            $uri = substr($uri, 10);
        }
        
        // S'assurer que l'URI commence par /
        if ($uri === '' || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }
        
        return $uri;
    }

    /**
     * Retourne le base path de l'application (ex: /check.master)
     */
    public static function basePath(): string
    {
        $instance = self::getInstance();
        $scriptName = $instance->server['SCRIPT_NAME'] ?? '';
        $basePath = str_replace('\\', '/', dirname($scriptName));
        
        // Si on est dans un sous-répertoire public, on remonte d'un cran
        if (basename($basePath) === 'public') {
            $basePath = dirname($basePath);
        }
        
        $basePath = str_replace('\\', '/', $basePath);
        if ($basePath === '/' || $basePath === '\\' || $basePath === '.') {
            return '';
        }
        
        return rtrim($basePath, '/');
    }

    /**
     * Retourne un fichier uploadé
     */
    public static function file(string $key): ?array
    {
        $instance = self::getInstance();
        return $instance->files[$key] ?? null;
    }

    /**
     * Vérifie si un fichier a été uploadé
     */
    public static function hasFile(string $key): bool
    {
        $instance = self::getInstance();
        return isset($instance->files[$key]) && $instance->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Retourne un cookie
     */
    public static function cookie(string $key, mixed $default = null): mixed
    {
        $instance = self::getInstance();
        return $instance->cookies[$key] ?? $default;
    }

    /**
     * Retourne un header HTTP
     */
    public static function header(string $name): ?string
    {
        $instance = self::getInstance();
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $instance->server[$key] ?? null;
    }

    /**
     * Retourne une valeur de session
     */
    public static function session(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Retourne l'URL complète
     */
    public static function fullUrl(): string
    {
        $instance = self::getInstance();
        $scheme = (!empty($instance->server['HTTPS']) && $instance->server['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $instance->server['HTTP_HOST'] ?? 'localhost';
        $uri = $instance->server['REQUEST_URI'] ?? '/';
        return "{$scheme}://{$host}{$uri}";
    }

    /**
     * Retourne les données JSON du body (pour API)
     */
    public static function json(): ?array
    {
        $content = file_get_contents('php://input');
        if (empty($content)) {
            return null;
        }
        $data = json_decode($content, true);
        return is_array($data) ? $data : null;
    }
}
