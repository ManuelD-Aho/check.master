<?php

declare(strict_types=1);

/**
 * Définition des routes de l'application
 * Utilise le routeur natif CheckMaster
 * @var \Src\Router $router
 */

// Pages publiques
$router->map('GET', '/', 'AccueilController#index', 'home');
$router->map('GET', '/accueil', 'AccueilController#index', 'accueil');

// Authentification
$router->map('GET|POST', '/connexion', 'AuthController#login', 'login');
$router->map('GET', '/logout', 'AuthController#logout', 'logout');
$router->map('GET|POST', '/forgot-password', 'AuthController#forgotPassword', 'forgot_password');
$router->map('GET|POST', '/change-password', 'AuthController#changePassword', 'change_password');

// Admin - Sessions Management
$router->map('GET', '/admin/sessions', 'Admin\\SessionsController#index', 'admin_sessions');
$router->map('GET', '/api/admin/sessions', 'Admin\\SessionsController#list', 'api_admin_sessions');
$router->map('POST', '/api/admin/sessions/[i:id]/kill', 'Admin\\SessionsController#kill', 'api_admin_sessions_kill');

// Dashboard
$router->map('GET', '/dashboard', 'DashboardController#index', 'dashboard');

// Gestion Utilisateurs
$router->map('GET', '/users', 'UsersController#index', 'users_list');
$router->map('GET|POST', '/users/create', 'UsersController#create', 'users_create');
$router->map('GET|POST', '/users/[i:id]/edit', 'UsersController#edit', 'users_edit');
$router->map('POST', '/users/[i:id]/delete', 'UsersController#delete', 'users_delete');

// Etudiants
$router->map('GET', '/etudiants', 'EtudiantsController#index', 'etudiants_list');
$router->map('GET', '/etudiants/[i:id]', 'EtudiantsController#show', 'etudiants_show');

// Soutenances
$router->map('GET', '/soutenances', 'SoutenancesController#index', 'soutenances_list');
$router->map('GET', '/soutenances/planning', 'SoutenancesController#planning', 'soutenances_planning');

// API Routes
$router->map('GET', '/api/users', 'Api\UsersController#index', 'api_users');
