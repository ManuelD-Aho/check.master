<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Nyholm\Psr7\Response;
use App\Service\System\SettingsService;

class MaintenanceModeMiddleware implements MiddlewareInterface
{
    private SettingsService $settings;
    private array $allowedIps = ['127.0.0.1', '::1'];

    public function __construct(SettingsService $settings)
    {
        $this->settings = $settings;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $maintenanceMode = $this->settings->get('maintenance_mode', 'false') === 'true';
        
        if (!$maintenanceMode) {
            return $handler->handle($request);
        }

        $clientIp = $this->getClientIp($request);
        
        if (in_array($clientIp, $this->allowedIps)) {
            return $handler->handle($request);
        }

        $templatePath = __DIR__ . '/../../templates/error/maintenance.php';
        
        if (file_exists($templatePath)) {
            ob_start();
            include $templatePath;
            $content = ob_get_clean();
        } else {
            $content = '<h1>Site en maintenance</h1><p>Nous reviendrons bientôt.</p>';
        }

        return new Response(503, ['Content-Type' => 'text/html'], $content);
    }

    private function getClientIp(ServerRequestInterface $request): string
    {
        $serverParams = $request->getServerParams();
        
        if (!empty($serverParams['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $serverParams['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        
        return $serverParams['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
