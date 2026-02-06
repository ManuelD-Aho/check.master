<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\App;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = ($scriptName !== '/' && $scriptName !== '\\') ? rtrim($scriptName, '/\\') : '';

define('BASE_URL', $protocol . '://' . $host . $basePath);
define('TEMPLATE_DIR', __DIR__ . '/../templates');
define('ROOT_DIR', __DIR__ . '/..');
define('PUBLIC_DIR', __DIR__);
define('UPLOAD_DIR', __DIR__ . '/uploads');

if ($_ENV['APP_DEBUG'] === 'true') {
    $whoops = new Run;
    $whoops->pushHandler(new PrettyPageHandler);
    $whoops->register();
}

$container = require __DIR__ . '/../config/container.php';

$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory,
    $psr17Factory,
    $psr17Factory,
    $psr17Factory
);
$request = $creator->fromGlobals();

$app = $container->get(App::class);
$response = $app->handle($request);

$emitter = new SapiEmitter();
$emitter->emit($response);
