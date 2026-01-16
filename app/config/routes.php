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
// PRD 03 - Commission - API Sessions
// ============================================================================
$router->map('GET', '/api/commission/sessions', 'Commission\\SessionsController#list', 'api_commission_sessions');
$router->map('GET', '/api/commission/sessions/[i:id]', 'Commission\\SessionsController#show', 'api_commission_session_show');
$router->map('POST', '/api/commission/sessions', 'Commission\\SessionsController#store', 'api_commission_session_store');
$router->map('PUT', '/api/commission/sessions/[i:id]', 'Commission\\SessionsController#update', 'api_commission_session_update');
$router->map('POST', '/api/commission/sessions/[i:id]/demarrer', 'Commission\\SessionsController#demarrer', 'api_commission_session_demarrer');
$router->map('POST', '/api/commission/sessions/[i:id]/terminer', 'Commission\\SessionsController#terminer', 'api_commission_session_terminer');
$router->map('POST', '/api/commission/sessions/[i:id]/annuler', 'Commission\\SessionsController#annuler', 'api_commission_session_annuler');
$router->map('POST', '/api/commission/sessions/[i:id]/membres', 'Commission\\SessionsController#ajouterMembre', 'api_commission_session_membre_add');
$router->map('DELETE', '/api/commission/sessions/[i:id]/membres/[i:membreId]', 'Commission\\SessionsController#retirerMembre', 'api_commission_session_membre_remove');
$router->map('POST', '/api/commission/sessions/[i:id]/voter', 'Commission\\SessionsController#voter', 'api_commission_session_voter');
$router->map('GET', '/api/commission/sessions/[i:id]/statistiques', 'Commission\\SessionsController#statistiques', 'api_commission_session_stats');

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

// ============================================================================
// PRD 02 - Entités Académiques - Vues
// ============================================================================
$router->map('GET', '/academique/etudiants', 'Academique\\EntitesAcademiquesController#indexEtudiants', 'academique_etudiants');
$router->map('GET', '/academique/enseignants', 'Academique\\EntitesAcademiquesController#indexEnseignants', 'academique_enseignants');
$router->map('GET', '/academique/entreprises', 'Academique\\EntitesAcademiquesController#indexEntreprises', 'academique_entreprises');

// ============================================================================
// PRD 02 - Entités Académiques - API Étudiants
// ============================================================================
$router->map('GET', '/api/academique/etudiants', 'Academique\\EntitesAcademiquesController#listEtudiants', 'api_academique_etudiants');
$router->map('GET', '/api/academique/etudiants/[i:id]', 'Academique\\EntitesAcademiquesController#showEtudiant', 'api_academique_etudiant_show');
$router->map('POST', '/api/academique/etudiants', 'Academique\\EntitesAcademiquesController#storeEtudiant', 'api_academique_etudiant_store');
$router->map('PUT', '/api/academique/etudiants/[i:id]', 'Academique\\EntitesAcademiquesController#updateEtudiant', 'api_academique_etudiant_update');
$router->map('DELETE', '/api/academique/etudiants/[i:id]', 'Academique\\EntitesAcademiquesController#desactiverEtudiant', 'api_academique_etudiant_delete');
$router->map('POST', '/api/academique/etudiants/import', 'Academique\\EntitesAcademiquesController#importEtudiants', 'api_academique_etudiants_import');
$router->map('GET', '/api/academique/etudiants/export', 'Academique\\EntitesAcademiquesController#exportEtudiants', 'api_academique_etudiants_export');
$router->map('GET', '/api/academique/etudiants/statistiques', 'Academique\\EntitesAcademiquesController#statistiquesEtudiants', 'api_academique_etudiants_stats');

// ============================================================================
// PRD 02 - Entités Académiques - API Enseignants
// ============================================================================
$router->map('GET', '/api/academique/enseignants', 'Academique\\EntitesAcademiquesController#listEnseignants', 'api_academique_enseignants');
$router->map('GET', '/api/academique/enseignants/[i:id]', 'Academique\\EntitesAcademiquesController#showEnseignant', 'api_academique_enseignant_show');
$router->map('POST', '/api/academique/enseignants', 'Academique\\EntitesAcademiquesController#storeEnseignant', 'api_academique_enseignant_store');
$router->map('PUT', '/api/academique/enseignants/[i:id]', 'Academique\\EntitesAcademiquesController#updateEnseignant', 'api_academique_enseignant_update');
$router->map('GET', '/api/academique/enseignants/statistiques', 'Academique\\EntitesAcademiquesController#statistiquesEnseignants', 'api_academique_enseignants_stats');

// ============================================================================
// PRD 02 - Entités Académiques - API Personnel
// ============================================================================
$router->map('GET', '/api/academique/personnel', 'Academique\\EntitesAcademiquesController#listPersonnel', 'api_academique_personnel');
$router->map('POST', '/api/academique/personnel', 'Academique\\EntitesAcademiquesController#storePersonnel', 'api_academique_personnel_store');
$router->map('PUT', '/api/academique/personnel/[i:id]', 'Academique\\EntitesAcademiquesController#updatePersonnel', 'api_academique_personnel_update');

// ============================================================================
// PRD 02 - Entités Académiques - API Entreprises
// ============================================================================
$router->map('GET', '/api/academique/entreprises', 'Academique\\EntitesAcademiquesController#listEntreprises', 'api_academique_entreprises');
$router->map('GET', '/api/academique/entreprises/[i:id]', 'Academique\\EntitesAcademiquesController#showEntreprise', 'api_academique_entreprise_show');
$router->map('POST', '/api/academique/entreprises', 'Academique\\EntitesAcademiquesController#storeEntreprise', 'api_academique_entreprise_store');
$router->map('PUT', '/api/academique/entreprises/[i:id]', 'Academique\\EntitesAcademiquesController#updateEntreprise', 'api_academique_entreprise_update');

// ============================================================================
// PRD 02 - Entités Académiques - API Années Académiques
// ============================================================================
$router->map('GET', '/api/academique/annees', 'Academique\\EntitesAcademiquesController#listAnnees', 'api_academique_annees');
$router->map('GET', '/api/academique/annees/[i:id]', 'Academique\\EntitesAcademiquesController#showAnnee', 'api_academique_annee_show');
$router->map('POST', '/api/academique/annees', 'Academique\\EntitesAcademiquesController#storeAnnee', 'api_academique_annee_store');
$router->map('POST', '/api/academique/annees/[i:id]/activer', 'Academique\\EntitesAcademiquesController#activerAnnee', 'api_academique_annee_activer');

// ============================================================================
// PRD 02 - Entités Académiques - API Structure Pédagogique (UE/ECUE)
// ============================================================================
$router->map('GET', '/api/academique/ue', 'Academique\\EntitesAcademiquesController#listUe', 'api_academique_ue');
$router->map('POST', '/api/academique/ue', 'Academique\\EntitesAcademiquesController#storeUe', 'api_academique_ue_store');
$router->map('PUT', '/api/academique/ue/[i:id]', 'Academique\\EntitesAcademiquesController#updateUe', 'api_academique_ue_update');
$router->map('POST', '/api/academique/ecue', 'Academique\\EntitesAcademiquesController#storeEcue', 'api_academique_ecue_store');

// ============================================================================
// PRD 02 - Entités Académiques - API Référentiels
// ============================================================================
$router->map('GET', '/api/academique/grades', 'Academique\\EntitesAcademiquesController#listGrades', 'api_academique_grades');
$router->map('GET', '/api/academique/fonctions', 'Academique\\EntitesAcademiquesController#listFonctions', 'api_academique_fonctions');
$router->map('GET', '/api/academique/specialites', 'Academique\\EntitesAcademiquesController#listSpecialites', 'api_academique_specialites');
$router->map('GET', '/api/academique/niveaux', 'Academique\\EntitesAcademiquesController#listNiveaux', 'api_academique_niveaux');

// ============================================================================
// PRD 03 - Workflow & Commission - Vues
// ============================================================================
$router->map('GET', '/workflow', 'Workflow\\WorkflowController#index', 'workflow_index');
$router->map('GET', '/workflow/escalades', 'Workflow\\WorkflowController#indexEscalades', 'workflow_escalades');

// ============================================================================
// PRD 03 - Workflow - API États & Transitions
// ============================================================================
$router->map('GET', '/api/workflow/etats', 'Workflow\\WorkflowController#listEtats', 'api_workflow_etats');
$router->map('GET', '/api/workflow/etats/[i:id]', 'Workflow\\WorkflowController#showEtat', 'api_workflow_etat_show');
$router->map('GET', '/api/workflow/transitions', 'Workflow\\WorkflowController#listTransitions', 'api_workflow_transitions');
$router->map('GET', '/api/workflow/statistiques', 'Workflow\\WorkflowController#statistiquesWorkflow', 'api_workflow_statistiques');
$router->map('GET', '/api/workflow/transitions/recentes', 'Workflow\\WorkflowController#transitionsRecentes', 'api_workflow_transitions_recentes');

// ============================================================================
// PRD 03 - Workflow - API Dossiers Workflow
// ============================================================================
$router->map('GET', '/api/workflow/dossiers/[i:id]/transitions', 'Workflow\\WorkflowController#transitionsPossibles', 'api_workflow_dossier_transitions');
$router->map('POST', '/api/workflow/dossiers/[i:id]/transition', 'Workflow\\WorkflowController#effectuerTransition', 'api_workflow_dossier_transition');
$router->map('GET', '/api/workflow/dossiers/[i:id]/historique', 'Workflow\\WorkflowController#historiqueDossier', 'api_workflow_dossier_historique');
$router->map('GET', '/api/workflow/dossiers/retard', 'Workflow\\WorkflowController#dossiersEnRetard', 'api_workflow_dossiers_retard');

// ============================================================================
// PRD 03 - Workflow - API Alertes SLA
// ============================================================================
$router->map('GET', '/api/workflow/alertes', 'Workflow\\WorkflowController#alertesSLA', 'api_workflow_alertes');

// ============================================================================
// PRD 03 - Workflow - API Escalades
// ============================================================================
$router->map('GET', '/api/workflow/escalades', 'Workflow\\WorkflowController#listEscalades', 'api_workflow_escalades');
$router->map('GET', '/api/workflow/escalades/mes', 'Workflow\\WorkflowController#mesEscalades', 'api_workflow_mes_escalades');
$router->map('GET', '/api/workflow/escalades/statistiques', 'Workflow\\WorkflowController#statistiquesEscalades', 'api_workflow_escalades_stats');
$router->map('GET', '/api/workflow/escalades/[i:id]', 'Workflow\\WorkflowController#showEscalade', 'api_workflow_escalade_show');
$router->map('POST', '/api/workflow/escalades', 'Workflow\\WorkflowController#storeEscalade', 'api_workflow_escalade_store');
$router->map('POST', '/api/workflow/escalades/[i:id]/prendre-en-charge', 'Workflow\\WorkflowController#prendreEnChargeEscalade', 'api_workflow_escalade_prendre');
$router->map('POST', '/api/workflow/escalades/[i:id]/action', 'Workflow\\WorkflowController#ajouterActionEscalade', 'api_workflow_escalade_action');
$router->map('POST', '/api/workflow/escalades/[i:id]/resoudre', 'Workflow\\WorkflowController#resoudreEscalade', 'api_workflow_escalade_resoudre');
$router->map('POST', '/api/workflow/escalades/[i:id]/escalader', 'Workflow\\WorkflowController#escaladerNiveauSuperieur', 'api_workflow_escalade_escalader');
$router->map('POST', '/api/workflow/escalades/[i:id]/fermer', 'Workflow\\WorkflowController#fermerEscalade', 'api_workflow_escalade_fermer');

// ============================================================================
// PRD 08 - Administration - Réclamations
// ============================================================================
$router->map('GET', '/admin/reclamations', 'Admin\\ReclamationsController#index', 'admin_reclamations');
$router->map('GET', '/api/admin/reclamations', 'Admin\\ReclamationsController#list', 'api_admin_reclamations');
$router->map('GET', '/api/admin/reclamations/statistiques', 'Admin\\ReclamationsController#statistiques', 'api_admin_reclamations_stats');
$router->map('GET', '/api/admin/reclamations/[i:id]', 'Admin\\ReclamationsController#show', 'api_admin_reclamation_show');
$router->map('POST', '/api/admin/reclamations/[i:id]/prendre-en-charge', 'Admin\\ReclamationsController#prendreEnCharge', 'api_admin_reclamation_prendre');
$router->map('POST', '/api/admin/reclamations/[i:id]/resoudre', 'Admin\\ReclamationsController#resoudre', 'api_admin_reclamation_resoudre');
$router->map('POST', '/api/admin/reclamations/[i:id]/rejeter', 'Admin\\ReclamationsController#rejeter', 'api_admin_reclamation_rejeter');

// ============================================================================
// PRD 08 - Administration - Import/Export
// ============================================================================
$router->map('GET', '/admin/import', 'Admin\\ImportExportController#index', 'admin_import');
$router->map('GET', '/api/admin/imports/historique', 'Admin\\ImportExportController#historiqueImports', 'api_admin_imports_historique');
$router->map('POST', '/api/admin/import/etudiants', 'Admin\\ImportExportController#importEtudiants', 'api_admin_import_etudiants');
$router->map('GET', '/admin/export/etudiants', 'Admin\\ImportExportController#exportEtudiants', 'admin_export_etudiants');
$router->map('GET', '/admin/export/enseignants', 'Admin\\ImportExportController#exportEnseignants', 'admin_export_enseignants');
$router->map('GET', '/admin/template/[a:type]', 'Admin\\ImportExportController#downloadTemplate', 'admin_template_download');

// ============================================================================
// PRD 08 - Administration - Configuration Système (API complémentaire)
// ============================================================================
$router->map('GET', '/api/admin/parametres', 'Admin\\ParametresController#list', 'api_admin_parametres');
$router->map('GET', '/api/admin/parametres/[:cle]', 'Admin\\ParametresController#get', 'api_admin_parametre_get');
$router->map('POST', '/api/admin/parametres', 'Admin\\ParametresController#update', 'api_admin_parametre_update');

// ============================================================================
// PRD 08 - Administration - Audit Logs API
// ============================================================================
$router->map('GET', '/api/admin/audit', 'Admin\\AuditController#list', 'api_admin_audit');
$router->map('GET', '/api/admin/audit/statistiques', 'Admin\\AuditController#statistiques', 'api_admin_audit_stats');

// ============================================================================
// PRD 08 - Administration - Utilisateurs API
// ============================================================================
$router->map('GET', '/api/admin/utilisateurs', 'Admin\\UtilisateursController#list', 'api_admin_utilisateurs');
$router->map('GET', '/api/admin/utilisateurs/[i:id]', 'Admin\\UtilisateursController#show', 'api_admin_utilisateur_show');
$router->map('POST', '/api/admin/utilisateurs', 'Admin\\UtilisateursController#store', 'api_admin_utilisateur_store');
$router->map('PUT', '/api/admin/utilisateurs/[i:id]', 'Admin\\UtilisateursController#update', 'api_admin_utilisateur_update');
$router->map('POST', '/api/admin/utilisateurs/[i:id]/activer', 'Admin\\UtilisateursController#activer', 'api_admin_utilisateur_activer');
$router->map('POST', '/api/admin/utilisateurs/[i:id]/desactiver', 'Admin\\UtilisateursController#desactiver', 'api_admin_utilisateur_desactiver');
$router->map('POST', '/api/admin/utilisateurs/[i:id]/reset-password', 'Admin\\UtilisateursController#resetPassword', 'api_admin_utilisateur_reset_pwd');
$router->map('GET', '/api/admin/utilisateurs/statistiques', 'Admin\\UtilisateursController#statistiques', 'api_admin_utilisateurs_stats');
