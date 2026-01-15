<?php

declare(strict_types=1);

/**
 * Point d'entrée de l'application CheckMaster
 * 
 * Ce fichier est le front controller pour l'hébergement Apache/LWS/WAMP.
 * Toutes les requêtes sont redirigées vers ce fichier via .htaccess.
 */

// Charger le point d'entrée principal depuis la racine du projet
require_once dirname(__DIR__) . '/index.php';
