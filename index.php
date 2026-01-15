<?php

declare(strict_types=1);

/**
 * Point d'entrée de l'application CheckMaster
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Src\Kernel;
use Src\Router;
use Src\Http\Request;

// 1. Initialisation (Autoload, Config, etc.)
require_once __DIR__ . '/app/config/bootstrap.php';

// 2. Création de la requête
$request = Request::getInstance();

// 3. Initialisation du noyau
$kernel = new Kernel();

// 4. Initialisation du routeur
$router = new Router();
$router->setKernel($kernel);
$router->loadRoutes(__DIR__ . '/app/config/routes.php');
$kernel->setRouter($router);

// 5. Traitement de la requête
$response = $kernel->handle($request);

// 6. Envoi de la réponse
$response->send();
