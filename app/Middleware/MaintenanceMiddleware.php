<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Middleware de Maintenance
 * 
 * Bloque l'accès au site pendant la maintenance.
 */
class MaintenanceMiddleware
{
    private string $maintenanceFile;
    private array $allowedIps;
    private string $maintenanceView;

    public function __construct(
        ?string $maintenanceFile = null,
        array $allowedIps = [],
        string $maintenanceView = 'errors/maintenance'
    ) {
        $this->maintenanceFile = $maintenanceFile ?? dirname(__DIR__, 2) . '/storage/framework/down';
        $this->allowedIps = $allowedIps;
        $this->maintenanceView = $maintenanceView;
    }

    /**
     * Traite la requête
     */
    public function handle(callable $next): mixed
    {
        // Vérifier si le mode maintenance est activé
        if (!$this->estEnMaintenance()) {
            return $next();
        }

        // Vérifier si l'IP est autorisée
        if ($this->ipAutorisee()) {
            return $next();
        }

        // Afficher la page de maintenance
        return $this->afficherMaintenance();
    }

    /**
     * Vérifie si le site est en maintenance
     */
    private function estEnMaintenance(): bool
    {
        return file_exists($this->maintenanceFile);
    }

    /**
     * Vérifie si l'IP est autorisée
     */
    private function ipAutorisee(): bool
    {
        $clientIp = $this->getClientIp();
        return in_array($clientIp, $this->allowedIps, true) || $clientIp === '127.0.0.1';
    }

    /**
     * Récupère l'IP du client
     */
    private function getClientIp(): string
    {
        $headers = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                return explode(',', $_SERVER[$header])[0];
            }
        }
        return 'unknown';
    }

    /**
     * Affiche la page de maintenance
     */
    private function afficherMaintenance(): never
    {
        http_response_code(503);
        header('Retry-After: 3600');

        // Charger les données de maintenance
        $data = $this->getMaintenanceData();

        // Afficher la page
        echo $this->renderMaintenancePage($data);
        exit;
    }

    /**
     * Récupère les données de maintenance
     */
    private function getMaintenanceData(): array
    {
        if (!file_exists($this->maintenanceFile)) {
            return ['message' => 'Maintenance en cours'];
        }

        $content = file_get_contents($this->maintenanceFile);
        $data = json_decode($content, true);

        return $data ?: ['message' => 'Maintenance en cours'];
    }

    /**
     * Rendu de la page de maintenance
     */
    private function renderMaintenancePage(array $data): string
    {
        $message = htmlspecialchars($data['message'] ?? 'Maintenance en cours');
        $retour = $data['expected_return'] ?? null;

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - CheckMaster</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #1a1a2e; color: #eee; 
               display: flex; justify-content: center; align-items: center; 
               min-height: 100vh; margin: 0; }
        .container { text-align: center; padding: 2rem; }
        h1 { font-size: 3rem; margin-bottom: 1rem; }
        p { font-size: 1.2rem; opacity: 0.8; }
        .icon { font-size: 5rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔧</div>
        <h1>Maintenance en cours</h1>
        <p>{$message}</p>
        {$retour ? "<p>Retour prévu : {$retour}</p>" : ""}
    </div>
</body>
</html>
HTML;
    }

    /**
     * Active le mode maintenance
     */
    public static function activer(string $message = 'Maintenance en cours', ?string $retourPrevu = null): void
    {
        $file = dirname(__DIR__, 2) . '/storage/framework/down';
        $dir = dirname($file);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $data = [
            'message' => $message,
            'expected_return' => $retourPrevu,
            'activated_at' => date('Y-m-d H:i:s'),
        ];

        file_put_contents($file, json_encode($data));
    }

    /**
     * Désactive le mode maintenance
     */
    public static function desactiver(): void
    {
        $file = dirname(__DIR__, 2) . '/storage/framework/down';
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
