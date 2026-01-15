<?php

declare(strict_types=1);

/**
 * Définition des routes de l'application
 * Utilise le routeur natif CheckMaster
 * @var \Src\Router $router
 */

// ============================================================================
// Pages publiques
// ============================================================================
$router->map('GET', '/', 'AccueilController#index', 'home');
$router->map('GET', '/accueil', 'AccueilController#index', 'accueil');

// ============================================================================
// Authentification
// ============================================================================
$router->map('GET|POST', '/connexion', 'AuthController#login', 'login');
$router->map('GET|POST', '/login', 'AuthController#login', 'login_alias');
$router->map('GET', '/logout', 'AuthController#logout', 'logout');
$router->map('GET|POST', '/forgot-password', 'AuthController#forgotPassword', 'forgot_password');
$router->map('GET|POST', '/change-password', 'AuthController#changePassword', 'change_password');

// ============================================================================
// Dashboard (tableau de bord principal)
// ============================================================================
$router->map('GET', '/dashboard', 'DashboardController#index', 'dashboard');

// ============================================================================
// Admin - Gestion des sessions
// ============================================================================
$router->map('GET', '/admin/sessions', 'Admin\\SessionsController#index', 'admin_sessions');
$router->map('GET', '/api/admin/sessions', 'Admin\\SessionsController#list', 'api_admin_sessions');
$router->map('POST', '/api/admin/sessions/[i:id]/kill', 'Admin\\SessionsController#kill', 'api_admin_sessions_kill');

// ============================================================================
// Admin - Dashboard et paramètres
// ============================================================================
$router->map('GET', '/admin/dashboard', 'Admin\\DashboardController#index', 'admin_dashboard');
$router->map('GET', '/admin/parametres', 'Admin\\ParametresController#index', 'admin_parametres');
$router->map('GET', '/admin/audit', 'Admin\\AuditController#index', 'admin_audit');
$router->map('GET', '/admin/archives', 'Admin\\ArchivesController#index', 'admin_archives');
$router->map('GET', '/admin/backup', 'Admin\\BackupController#index', 'admin_backup');
$router->map('GET', '/admin/permissions', 'Admin\\PermissionsController#index', 'admin_permissions');
$router->map('GET', '/admin/referentiels', 'Admin\\ReferentielsController#index', 'admin_referentiels');
$router->map('GET', '/admin/utilisateurs', 'Admin\\UtilisateursController#index', 'admin_utilisateurs');

// ============================================================================
// Scolarité - Gestion des étudiants et inscriptions
// ============================================================================
$router->map('GET', '/scolarite/dashboard', 'Scolarite\\DashboardController#index', 'scolarite_dashboard');
$router->map('GET', '/scolarite/etudiants', 'Scolarite\\EtudiantsController#index', 'scolarite_etudiants');
$router->map('GET', '/scolarite/etudiants/[i:id]', 'Scolarite\\EtudiantsController#show', 'scolarite_etudiant_show');
$router->map('GET', '/scolarite/candidatures', 'Scolarite\\CandidaturesController#index', 'scolarite_candidatures');
$router->map('GET', '/scolarite/inscriptions', 'Scolarite\\InscriptionsController#index', 'scolarite_inscriptions');
$router->map('GET', '/scolarite/notes', 'Scolarite\\NotesController#index', 'scolarite_notes');
$router->map('GET', '/scolarite/paiements', 'Scolarite\\PaiementsController#index', 'scolarite_paiements');
$router->map('GET', '/scolarite/penalites', 'Scolarite\\PenalitesController#index', 'scolarite_penalites');
$router->map('GET', '/scolarite/reclamations', 'Scolarite\\ReclamationsController#index', 'scolarite_reclamations');

// ============================================================================
// Etudiant - Espace étudiant
// ============================================================================
$router->map('GET', '/etudiant/dashboard', 'Etudiant\\DashboardController#index', 'etudiant_dashboard');
$router->map('GET', '/etudiant/candidature', 'Etudiant\\CandidatureController#index', 'etudiant_candidature');
$router->map('GET', '/etudiant/rapport', 'Etudiant\\RapportController#index', 'etudiant_rapport');
$router->map('GET', '/etudiant/notes', 'Etudiant\\NotesController#index', 'etudiant_notes');
$router->map('GET', '/etudiant/profil', 'Etudiant\\ProfilController#index', 'etudiant_profil');
$router->map('GET', '/etudiant/reclamations', 'Etudiant\\ReclamationsController#index', 'etudiant_reclamations');

// ============================================================================
// Commission - Gestion des évaluations
// ============================================================================
$router->map('GET', '/commission/dashboard', 'Commission\\DashboardController#index', 'commission_dashboard');
$router->map('GET', '/commission/sessions', 'Commission\\SessionsController#index', 'commission_sessions');
$router->map('GET', '/commission/evaluations', 'Commission\\EvaluationsController#index', 'commission_evaluations');
$router->map('GET', '/commission/votes', 'Commission\\VotesController#index', 'commission_votes');
$router->map('GET', '/commission/pv', 'Commission\\PvController#index', 'commission_pv');
$router->map('GET', '/commission/archives', 'Commission\\ArchivesController#index', 'commission_archives');

// ============================================================================
// Soutenance - Gestion des soutenances
// ============================================================================
$router->map('GET', '/soutenance/planning', 'Soutenance\\PlanningController#index', 'soutenance_planning');
$router->map('GET', '/soutenance/jury', 'Soutenance\\JuryController#index', 'soutenance_jury');
$router->map('GET', '/soutenance/convocations', 'Soutenance\\ConvocationsController#index', 'soutenance_convocations');
$router->map('GET', '/soutenance/notes', 'Soutenance\\NotesController#index', 'soutenance_notes');

// ============================================================================
// Secrétariat - Gestion des dossiers
// ============================================================================
$router->map('GET', '/secretariat/dashboard', 'Secretariat\\DashboardController#index', 'secretariat_dashboard');
$router->map('GET', '/secretariat/dossiers', 'Secretariat\\DossiersController#index', 'secretariat_dossiers');

// ============================================================================
// Communication - Rapports et checklists
// ============================================================================
$router->map('GET', '/communication/rapports', 'Communication\\RapportsController#index', 'communication_rapports');
$router->map('GET', '/communication/checklist', 'Communication\\ChecklistController#index', 'communication_checklist');
