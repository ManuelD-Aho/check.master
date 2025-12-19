<?php

declare(strict_types=1);

/**
 * Point d'entrée de l'application CheckMaster
 */

use Src\Kernel;
use Src\Http\Request;

// 1. Initialisation (Autoload, Config, etc.)
require_once __DIR__ . '/app/Config/bootstrap.php';

// 2. Création de la requête
$request = Request::getInstance();

// 3. Initialisation du noyau
$kernel = new Kernel();

// 4. Traitement de la requête
$response = $kernel->handle($request);

// 5. Envoi de la réponse
$response->send();
