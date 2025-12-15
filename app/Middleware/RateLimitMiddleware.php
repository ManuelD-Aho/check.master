<?php

declare(strict_types=1);

namespace App\Middleware;

use Src\Http\Request;
use Src\Http\JsonResponse;

/**
 * Middleware Rate Limiting
 * 
 * Limite le nombre de requêtes par IP sur les routes sensibles.
 * Protège contre les attaques brute-force et DDoS.
 */
class RateLimitMiddleware
{
    /**
     * Limite de requêtes par minute par défaut
     */
    private const LIMITE_PAR_DEFAUT = 60;

    /**
     * Durée de la fenêtre en secondes
     */
    private const FENETRE_SECONDES = 60;

    /**
     * Clé de cache pour le compteur
     */
    private const CACHE_PREFIX = 'rate_limit:';

    private int $limite;

    public function __construct(int $limite = self::LIMITE_PAR_DEFAUT)
    {
        $this->limite = $limite;
    }

    /**
     * Exécute le middleware
     */
    public function handle(callable $next): mixed
    {
        $ip = Request::ip();
        $uri = Request::uri();
        $cle = self::CACHE_PREFIX . md5($ip . ':' . $uri);

        // Récupérer le compteur actuel
        $compteur = $this->getCompteur($cle);

        if ($compteur >= $this->limite) {
            return JsonResponse::tooManyRequests(
                'Trop de requêtes. Veuillez patienter une minute.'
            );
        }

        // Incrémenter le compteur
        $this->incrementerCompteur($cle);

        return $next();
    }

    /**
     * Récupère le compteur depuis le cache
     * Utilise les fichiers temporaires car pas de Redis disponible
     */
    private function getCompteur(string $cle): int
    {
        $fichier = $this->getFichierCache($cle);

        if (!file_exists($fichier)) {
            return 0;
        }

        $data = json_decode(file_get_contents($fichier), true);
        if ($data === null) {
            return 0;
        }

        // Vérifier si la fenêtre est expirée
        if (time() > $data['expire_a']) {
            unlink($fichier);
            return 0;
        }

        return $data['compteur'];
    }

    /**
     * Incrémente le compteur
     */
    private function incrementerCompteur(string $cle): void
    {
        $fichier = $this->getFichierCache($cle);
        $compteur = 1;
        $expireA = time() + self::FENETRE_SECONDES;

        if (file_exists($fichier)) {
            $data = json_decode(file_get_contents($fichier), true);
            if ($data !== null && time() <= $data['expire_a']) {
                $compteur = $data['compteur'] + 1;
                $expireA = $data['expire_a'];
            }
        }

        $data = [
            'compteur' => $compteur,
            'expire_a' => $expireA,
        ];

        file_put_contents($fichier, json_encode($data));
    }

    /**
     * Retourne le chemin du fichier de cache
     */
    private function getFichierCache(string $cle): string
    {
        $dir = dirname(__DIR__, 2) . '/storage/rate_limit';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir . '/' . $cle . '.json';
    }

    /**
     * Nettoie les fichiers de cache expirés (à appeler via cron)
     */
    public static function nettoyerCache(): int
    {
        $dir = dirname(__DIR__, 2) . '/storage/rate_limit';
        if (!is_dir($dir)) {
            return 0;
        }

        $count = 0;
        $now = time();

        foreach (glob($dir . '/*.json') as $fichier) {
            $data = json_decode(file_get_contents($fichier), true);
            if ($data === null || $now > $data['expire_a']) {
                unlink($fichier);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Crée un middleware avec une limite personnalisée
     */
    public static function withLimit(int $limite): self
    {
        return new self($limite);
    }
}
