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

// ============================================================================
// PRD 04 - Mémoire & Soutenance - Candidatures API
// ============================================================================
$router->map('GET', '/api/candidatures', 'Soutenance\\CandidaturesController#list', 'api_candidatures');
$router->map('GET', '/api/candidatures/[i:id]', 'Soutenance\\CandidaturesController#show', 'api_candidature_show');
$router->map('POST', '/api/candidatures', 'Soutenance\\CandidaturesController#store', 'api_candidature_store');
$router->map('PUT', '/api/candidatures/[i:id]', 'Soutenance\\CandidaturesController#update', 'api_candidature_update');
$router->map('POST', '/api/candidatures/[i:id]/soumettre', 'Soutenance\\CandidaturesController#soumettre', 'api_candidature_soumettre');
$router->map('POST', '/api/candidatures/[i:id]/valider', 'Soutenance\\CandidaturesController#valider', 'api_candidature_valider');
$router->map('POST', '/api/candidatures/[i:id]/rejeter', 'Soutenance\\CandidaturesController#rejeter', 'api_candidature_rejeter');
$router->map('POST', '/api/candidatures/[i:id]/complements', 'Soutenance\\CandidaturesController#demanderComplements', 'api_candidature_complements');
$router->map('GET', '/api/candidatures/statistiques', 'Soutenance\\CandidaturesController#statistiques', 'api_candidatures_stats');

// ============================================================================
// PRD 04 - Mémoire & Soutenance - Jury API
// ============================================================================
$router->map('GET', '/api/jury', 'Soutenance\\JuryController#list', 'api_jury');
$router->map('GET', '/api/jury/[i:id]', 'Soutenance\\JuryController#show', 'api_jury_show');
$router->map('POST', '/api/jury', 'Soutenance\\JuryController#store', 'api_jury_store');
$router->map('POST', '/api/jury/[i:id]/accepter', 'Soutenance\\JuryController#accepter', 'api_jury_accepter');
$router->map('POST', '/api/jury/[i:id]/refuser', 'Soutenance\\JuryController#refuser', 'api_jury_refuser');
$router->map('DELETE', '/api/jury/[i:id]', 'Soutenance\\JuryController#retirer', 'api_jury_retirer');
$router->map('GET', '/api/jury/dossier/[i:id]', 'Soutenance\\JuryController#parDossier', 'api_jury_dossier');
$router->map('GET', '/api/jury/enseignants-disponibles', 'Soutenance\\JuryController#enseignantsDisponibles', 'api_jury_enseignants');
$router->map('GET', '/api/jury/mes-invitations', 'Soutenance\\JuryController#mesInvitations', 'api_jury_invitations');
$router->map('GET', '/api/jury/statistiques', 'Soutenance\\JuryController#statistiques', 'api_jury_stats');

// ============================================================================
// PRD 04 - Mémoire & Soutenance - Soutenances API
// ============================================================================
$router->map('GET', '/api/soutenances', 'Soutenance\\SoutenancesController#list', 'api_soutenances');
$router->map('GET', '/api/soutenances/[i:id]', 'Soutenance\\SoutenancesController#show', 'api_soutenance_show');
$router->map('POST', '/api/soutenances', 'Soutenance\\SoutenancesController#store', 'api_soutenance_store');
$router->map('POST', '/api/soutenances/[i:id]/demarrer', 'Soutenance\\SoutenancesController#demarrer', 'api_soutenance_demarrer');
$router->map('POST', '/api/soutenances/[i:id]/terminer', 'Soutenance\\SoutenancesController#terminer', 'api_soutenance_terminer');
$router->map('POST', '/api/soutenances/[i:id]/reporter', 'Soutenance\\SoutenancesController#reporter', 'api_soutenance_reporter');
$router->map('POST', '/api/soutenances/[i:id]/corrections', 'Soutenance\\SoutenancesController#demanderCorrections', 'api_soutenance_corrections');
$router->map('POST', '/api/soutenances/[i:id]/valider-corrections', 'Soutenance\\SoutenancesController#validerCorrections', 'api_soutenance_valider_corrections');
$router->map('GET', '/api/soutenances/[i:id]/pv', 'Soutenance\\SoutenancesController#telechargerPV', 'api_soutenance_pv');
$router->map('GET', '/api/soutenances/planning/jour', 'Soutenance\\SoutenancesController#planningJour', 'api_soutenances_jour');
$router->map('GET', '/api/soutenances/planning/a-venir', 'Soutenance\\SoutenancesController#aVenir', 'api_soutenances_a_venir');
$router->map('GET', '/api/soutenances/statistiques', 'Soutenance\\SoutenancesController#statistiques', 'api_soutenances_stats');

// ============================================================================
// PRD 04 - Mémoire & Soutenance - Notes API
// ============================================================================
$router->map('GET', '/api/notes/soutenance/[i:id]', 'Soutenance\\NotesController#parSoutenance', 'api_notes_soutenance');
$router->map('POST', '/api/notes', 'Soutenance\\NotesController#store', 'api_note_store');
$router->map('POST', '/api/notes/finaliser/[i:id]', 'Soutenance\\NotesController#finaliser', 'api_notes_finaliser');

// ============================================================================
// PRD 04 - Mémoire & Soutenance - Rapports Étudiants API
// ============================================================================
$router->map('GET', '/api/rapports', 'Rapport\\RapportsController#list', 'api_rapports');
$router->map('GET', '/api/rapports/[i:id]', 'Rapport\\RapportsController#show', 'api_rapport_show');
$router->map('POST', '/api/rapports', 'Rapport\\RapportsController#store', 'api_rapport_store');
$router->map('PUT', '/api/rapports/[i:id]', 'Rapport\\RapportsController#update', 'api_rapport_update');
$router->map('POST', '/api/rapports/[i:id]/soumettre', 'Rapport\\RapportsController#soumettre', 'api_rapport_soumettre');
$router->map('POST', '/api/rapports/[i:id]/upload', 'Rapport\\RapportsController#uploadFichier', 'api_rapport_upload');
$router->map('GET', '/api/rapports/[i:id]/versions', 'Rapport\\RapportsController#versions', 'api_rapport_versions');
$router->map('POST', '/api/rapports/[i:id]/nouvelle-version', 'Rapport\\RapportsController#creerVersion', 'api_rapport_nouvelle_version');

// ============================================================================
// PRD 04 - Mémoire & Soutenance - Annotations API
// ============================================================================
$router->map('GET', '/api/annotations/rapport/[i:id]', 'Rapport\\AnnotationsController#parRapport', 'api_annotations_rapport');
$router->map('POST', '/api/annotations', 'Rapport\\AnnotationsController#store', 'api_annotation_store');
$router->map('PUT', '/api/annotations/[i:id]', 'Rapport\\AnnotationsController#update', 'api_annotation_update');
$router->map('DELETE', '/api/annotations/[i:id]', 'Rapport\\AnnotationsController#destroy', 'api_annotation_delete');

// ============================================================================
// PRD 05 - Communication - Messagerie API
// ============================================================================
$router->map('GET', '/api/messages/recus', 'Communication\\MessagerieController#recus', 'api_messages_recus');
$router->map('GET', '/api/messages/envoyes', 'Communication\\MessagerieController#envoyes', 'api_messages_envoyes');
$router->map('GET', '/api/messages/[i:id]', 'Communication\\MessagerieController#show', 'api_message_show');
$router->map('POST', '/api/messages', 'Communication\\MessagerieController#store', 'api_message_store');
$router->map('POST', '/api/messages/[i:id]/lu', 'Communication\\MessagerieController#marquerLu', 'api_message_lu');
$router->map('POST', '/api/messages/[i:id]/repondre', 'Communication\\MessagerieController#repondre', 'api_message_repondre');
$router->map('DELETE', '/api/messages/[i:id]', 'Communication\\MessagerieController#destroy', 'api_message_delete');
$router->map('GET', '/api/messages/non-lus/count', 'Communication\\MessagerieController#compterNonLus', 'api_messages_non_lus');

// ============================================================================
// PRD 05 - Communication - Conversations Dossiers API
// ============================================================================
$router->map('GET', '/api/conversations/mes', 'Communication\\ConversationsController#mesConversations', 'api_conversations_mes');
$router->map('GET', '/api/conversations/dossier/[i:id]', 'Communication\\ConversationsController#parDossier', 'api_conversation_dossier');
$router->map('POST', '/api/conversations/dossier/[i:id]/message', 'Communication\\ConversationsController#envoyerMessage', 'api_conversation_message');
$router->map('POST', '/api/conversations/dossier/[i:id]/lu', 'Communication\\ConversationsController#marquerLue', 'api_conversation_lu');

// ============================================================================
// PRD 05 - Communication - Notifications API
// ============================================================================
$router->map('GET', '/api/notifications', 'Communication\\NotificationsController#list', 'api_notifications');
$router->map('GET', '/api/notifications/historique', 'Communication\\NotificationsController#historique', 'api_notifications_historique');
$router->map('GET', '/api/notifications/templates', 'Communication\\NotificationsController#templates', 'api_notifications_templates');
$router->map('POST', '/api/notifications/envoyer', 'Communication\\NotificationsController#envoyer', 'api_notification_envoyer');
$router->map('POST', '/api/notifications/traiter-file', 'Communication\\NotificationsController#traiterFile', 'api_notifications_traiter');
$router->map('GET', '/api/notifications/statistiques', 'Communication\\NotificationsController#statistiques', 'api_notifications_stats');

// ============================================================================
// PRD 05 - Communication - Calendrier API
// ============================================================================
$router->map('GET', '/api/calendrier/salles-disponibles', 'Communication\\CalendrierController#sallesDisponibles', 'api_calendrier_salles');
$router->map('GET', '/api/calendrier/planning/[a:date]', 'Communication\\CalendrierController#planning', 'api_calendrier_planning');
$router->map('POST', '/api/calendrier/verifier-conflits', 'Communication\\CalendrierController#verifierConflits', 'api_calendrier_conflits');

// ============================================================================
// PRD 06 - Documents & Archives - Documents API
// ============================================================================
$router->map('GET', '/api/documents', 'Documents\\DocumentsController#list', 'api_documents');
$router->map('GET', '/api/documents/[i:id]', 'Documents\\DocumentsController#show', 'api_document_show');
$router->map('GET', '/api/documents/[i:id]/telecharger', 'Documents\\DocumentsController#telecharger', 'api_document_telecharger');
$router->map('POST', '/api/documents/generer', 'Documents\\DocumentsController#generer', 'api_document_generer');
$router->map('POST', '/api/documents/[i:id]/regenerer', 'Documents\\DocumentsController#regenerer', 'api_document_regenerer');
$router->map('GET', '/api/documents/types', 'Documents\\DocumentsController#types', 'api_documents_types');

// ============================================================================
// PRD 06 - Documents & Archives - Archives API
// ============================================================================
$router->map('GET', '/api/archives', 'Documents\\ArchivesController#list', 'api_archives');
$router->map('GET', '/api/archives/[i:id]', 'Documents\\ArchivesController#show', 'api_archive_show');
$router->map('POST', '/api/archives/[i:id]/verifier', 'Documents\\ArchivesController#verifierIntegrite', 'api_archive_verifier');
$router->map('POST', '/api/archives/verifier-tout', 'Documents\\ArchivesController#verifierTout', 'api_archives_verifier_tout');
$router->map('POST', '/api/archives/[i:id]/verrouiller', 'Documents\\ArchivesController#verrouiller', 'api_archive_verrouiller');
$router->map('GET', '/api/archives/statistiques', 'Documents\\ArchivesController#statistiques', 'api_archives_stats');

// ============================================================================
// PRD 06 - Documents & Archives - Brouillons API
// ============================================================================
$router->map('GET', '/api/brouillons', 'Documents\\BrouillonsController#list', 'api_brouillons');
$router->map('GET', '/api/brouillons/[a:type]/[i:ctx]/[a:code]', 'Documents\\BrouillonsController#recuperer', 'api_brouillon_get');
$router->map('POST', '/api/brouillons', 'Documents\\BrouillonsController#sauvegarder', 'api_brouillon_save');
$router->map('DELETE', '/api/brouillons/[i:id]', 'Documents\\BrouillonsController#supprimer', 'api_brouillon_delete');

// ============================================================================
// PRD 06 - Documents & Archives - Historique Entités API
// ============================================================================
$router->map('GET', '/api/historique/[a:type]/[i:id]', 'Documents\\HistoriqueController#show', 'api_historique_show');
$router->map('GET', '/api/historique/[a:type]/[i:id]/version/[i:version]', 'Documents\\HistoriqueController#version', 'api_historique_version');
$router->map('GET', '/api/historique/[a:type]/[i:id]/comparer', 'Documents\\HistoriqueController#comparer', 'api_historique_comparer');
$router->map('POST', '/api/historique/[a:type]/[i:id]/restaurer/[i:version]', 'Documents\\HistoriqueController#restaurer', 'api_historique_restaurer');

// ============================================================================
// PRD 07 - Financier - Paiements API
// ============================================================================
$router->map('GET', '/finance/paiements', 'Finance\\PaiementsController#index', 'finance_paiements');
$router->map('GET', '/api/finance/paiements', 'Finance\\PaiementsController#list', 'api_finance_paiements');
$router->map('GET', '/api/finance/paiements/[i:id]', 'Finance\\PaiementsController#show', 'api_finance_paiement_show');
$router->map('POST', '/api/finance/paiements', 'Finance\\PaiementsController#store', 'api_finance_paiement_store');
$router->map('POST', '/api/finance/paiements/[i:id]/annuler', 'Finance\\PaiementsController#annuler', 'api_finance_paiement_annuler');
$router->map('GET', '/api/finance/paiements/etudiant/[i:id]/solde', 'Finance\\PaiementsController#solde', 'api_finance_solde');
$router->map('GET', '/api/finance/paiements/etudiant/[i:id]/historique', 'Finance\\PaiementsController#historique', 'api_finance_historique');
$router->map('GET', '/api/finance/paiements/[i:id]/recu', 'Finance\\PaiementsController#telechargerRecu', 'api_finance_recu');
$router->map('POST', '/api/finance/paiements/[i:id]/regenerer-recu', 'Finance\\PaiementsController#regenererRecu', 'api_finance_regenerer_recu');
$router->map('GET', '/api/finance/paiements/statistiques', 'Finance\\PaiementsController#statistiques', 'api_finance_paiements_stats');

// ============================================================================
// PRD 07 - Financier - Pénalités API
// ============================================================================
$router->map('GET', '/finance/penalites', 'Finance\\PenalitesController#index', 'finance_penalites');
$router->map('GET', '/api/finance/penalites', 'Finance\\PenalitesController#list', 'api_finance_penalites');
$router->map('GET', '/api/finance/penalites/[i:id]', 'Finance\\PenalitesController#show', 'api_finance_penalite_show');
$router->map('POST', '/api/finance/penalites', 'Finance\\PenalitesController#store', 'api_finance_penalite_store');
$router->map('POST', '/api/finance/penalites/[i:id]/payer', 'Finance\\PenalitesController#payer', 'api_finance_penalite_payer');
$router->map('POST', '/api/finance/penalites/[i:id]/annuler', 'Finance\\PenalitesController#annuler', 'api_finance_penalite_annuler');
$router->map('POST', '/api/finance/penalites/calculer-auto', 'Finance\\PenalitesController#calculerAuto', 'api_finance_penalites_calcul');
$router->map('GET', '/api/finance/penalites/etudiant/[i:id]', 'Finance\\PenalitesController#parEtudiant', 'api_finance_penalites_etudiant');
$router->map('GET', '/api/finance/penalites/statistiques', 'Finance\\PenalitesController#statistiques', 'api_finance_penalites_stats');

// ============================================================================
// PRD 07 - Financier - Exonérations API
// ============================================================================
$router->map('GET', '/finance/exonerations', 'Finance\\ExonerationsController#index', 'finance_exonerations');
$router->map('GET', '/api/finance/exonerations', 'Finance\\ExonerationsController#list', 'api_finance_exonerations');
$router->map('GET', '/api/finance/exonerations/[i:id]', 'Finance\\ExonerationsController#show', 'api_finance_exoneration_show');
$router->map('POST', '/api/finance/exonerations', 'Finance\\ExonerationsController#store', 'api_finance_exoneration_store');
$router->map('POST', '/api/finance/exonerations/[i:id]/approuver', 'Finance\\ExonerationsController#approuver', 'api_finance_exoneration_approuver');
$router->map('POST', '/api/finance/exonerations/[i:id]/refuser', 'Finance\\ExonerationsController#refuser', 'api_finance_exoneration_refuser');
$router->map('POST', '/api/finance/exonerations/[i:id]/annuler', 'Finance\\ExonerationsController#annuler', 'api_finance_exoneration_annuler');
$router->map('GET', '/api/finance/exonerations/en-attente', 'Finance\\ExonerationsController#enAttente', 'api_finance_exonerations_attente');
$router->map('GET', '/api/finance/exonerations/etudiant/[i:id]', 'Finance\\ExonerationsController#parEtudiant', 'api_finance_exonerations_etudiant');
$router->map('GET', '/api/finance/exonerations/statistiques', 'Finance\\ExonerationsController#statistiques', 'api_finance_exonerations_stats');

// ============================================================================
// PRD 07 - Financier - Dashboard Étudiant
// ============================================================================
$router->map('GET', '/etudiant/finances', 'Etudiant\\FinancesController#index', 'etudiant_finances');
$router->map('GET', '/api/etudiant/finances/resume', 'Etudiant\\FinancesController#resume', 'api_etudiant_finances_resume');
