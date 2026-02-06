<?php
declare(strict_types=1);

use App\Controller\Auth\LoginController;
use App\Controller\Auth\PasswordController;
use App\Controller\Auth\TwoFactorController;
use App\Controller\Auth\FirstLoginController;
use App\Controller\Admin\DashboardController as AdminDashboardController;
use App\Controller\Admin\UtilisateurController;
use App\Controller\Admin\EtudiantController as AdminEtudiantController;
use App\Controller\Admin\EnseignantController;
use App\Controller\Admin\InscriptionController as AdminInscriptionController;
use App\Controller\Admin\CandidatureController as AdminCandidatureController;
use App\Controller\Admin\RapportController as AdminRapportController;
use App\Controller\Admin\CommissionController as AdminCommissionController;
use App\Controller\Admin\SoutenanceController as AdminSoutenanceController;
use App\Controller\Admin\ParametresController;
use App\Controller\Etudiant\DashboardController as EtudiantDashboardController;
use App\Controller\Etudiant\ProfilController;
use App\Controller\Etudiant\ScolariteController;
use App\Controller\Etudiant\CandidatureController as EtudiantCandidatureController;
use App\Controller\Etudiant\RapportController as EtudiantRapportController;
use App\Controller\Etudiant\SoutenanceController as EtudiantSoutenanceController;
use App\Controller\Commission\DashboardController as CommissionDashboardController;
use App\Controller\Commission\RapportController as CommissionRapportController;
use App\Controller\Commission\SessionController;
use App\Controller\Encadreur\DashboardController as EncadreurDashboardController;
use App\Controller\Encadreur\EtudiantController as EncadreurEtudiantController;
use App\Controller\Encadreur\RapportController as EncadreurRapportController;
use App\Controller\Encadreur\AptitudeController;
use App\Controller\Api\EntrepriseApiController;
use App\Controller\Api\EtudiantApiController;
use App\Controller\Api\EnseignantApiController;
use App\Controller\Api\RapportApiController;

return [
    ['GET', '/login', [LoginController::class, 'showLoginForm']],
    ['POST', '/login', [LoginController::class, 'login']],
    ['GET', '/logout', [LoginController::class, 'logout']],
    ['GET', '/login/2fa', [TwoFactorController::class, 'showSetup']],
    ['POST', '/login/2fa', [TwoFactorController::class, 'verify']],
    ['GET', '/premiere-connexion', [FirstLoginController::class, 'showForm']],
    ['POST', '/premiere-connexion', [FirstLoginController::class, 'updatePassword']],
    ['GET', '/mot-de-passe/oublie', [PasswordController::class, 'showForgotForm']],
    ['POST', '/mot-de-passe/oublie', [PasswordController::class, 'sendResetLink']],
    ['GET', '/mot-de-passe/reinitialiser/{token}', [PasswordController::class, 'showResetForm']],
    ['POST', '/mot-de-passe/reinitialiser/{token}', [PasswordController::class, 'resetPassword']],

    ['GET', '/admin', [AdminDashboardController::class, 'index']],
    ['GET', '/admin/dashboard', [AdminDashboardController::class, 'index']],

    ['GET', '/admin/utilisateurs', [UtilisateurController::class, 'index']],
    ['GET', '/admin/utilisateurs/nouveau', [UtilisateurController::class, 'create']],
    ['POST', '/admin/utilisateurs', [UtilisateurController::class, 'store']],
    ['GET', '/admin/utilisateurs/{id}', [UtilisateurController::class, 'show']],
    ['GET', '/admin/utilisateurs/{id}/modifier', [UtilisateurController::class, 'edit']],
    ['POST', '/admin/utilisateurs/{id}', [UtilisateurController::class, 'update']],
    ['POST', '/admin/utilisateurs/{id}/toggle', [UtilisateurController::class, 'toggleStatus']],
    ['POST', '/admin/utilisateurs/{id}/debloquer', [UtilisateurController::class, 'unblock']],
    ['POST', '/admin/utilisateurs/{id}/reset-password', [UtilisateurController::class, 'resetPassword']],

    ['GET', '/admin/etudiants', [AdminEtudiantController::class, 'index']],
    ['GET', '/admin/etudiants/nouveau', [AdminEtudiantController::class, 'create']],
    ['POST', '/admin/etudiants', [AdminEtudiantController::class, 'store']],
    ['GET', '/admin/etudiants/{matricule}', [AdminEtudiantController::class, 'show']],
    ['GET', '/admin/etudiants/{matricule}/modifier', [AdminEtudiantController::class, 'edit']],
    ['POST', '/admin/etudiants/{matricule}', [AdminEtudiantController::class, 'update']],
    ['GET', '/admin/etudiants/import', [AdminEtudiantController::class, 'importForm']],
    ['POST', '/admin/etudiants/import', [AdminEtudiantController::class, 'import']],
    ['GET', '/admin/etudiants/export', [AdminEtudiantController::class, 'export']],

    ['GET', '/admin/enseignants', [EnseignantController::class, 'index']],
    ['GET', '/admin/enseignants/nouveau', [EnseignantController::class, 'create']],
    ['POST', '/admin/enseignants', [EnseignantController::class, 'store']],
    ['GET', '/admin/enseignants/{matricule}', [EnseignantController::class, 'show']],
    ['GET', '/admin/enseignants/{matricule}/modifier', [EnseignantController::class, 'edit']],
    ['POST', '/admin/enseignants/{matricule}', [EnseignantController::class, 'update']],

    ['GET', '/admin/inscriptions', [AdminInscriptionController::class, 'index']],
    ['GET', '/admin/inscriptions/{id}', [AdminInscriptionController::class, 'show']],
    ['GET', '/admin/inscriptions/etudiant/{matricule}', [AdminInscriptionController::class, 'byStudent']],
    ['GET', '/admin/inscriptions/nouvelle/{matricule}', [AdminInscriptionController::class, 'create']],
    ['POST', '/admin/inscriptions/{matricule}', [AdminInscriptionController::class, 'store']],

    ['GET', '/admin/candidatures', [AdminCandidatureController::class, 'index']],
    ['GET', '/admin/candidatures/{id}', [AdminCandidatureController::class, 'show']],
    ['POST', '/admin/candidatures/{id}/valider', [AdminCandidatureController::class, 'validate']],
    ['POST', '/admin/candidatures/{id}/rejeter', [AdminCandidatureController::class, 'reject']],

    ['GET', '/admin/rapports', [AdminRapportController::class, 'index']],
    ['GET', '/admin/rapports/{id}', [AdminRapportController::class, 'show']],
    ['POST', '/admin/rapports/{id}/approuver', [AdminRapportController::class, 'approve']],
    ['POST', '/admin/rapports/{id}/retourner', [AdminRapportController::class, 'returnReport']],
    ['POST', '/admin/rapports/transferer', [AdminRapportController::class, 'sendToCommission']],

    ['GET', '/admin/commission/membres', [AdminCommissionController::class, 'membres']],
    ['POST', '/admin/commission/membres', [AdminCommissionController::class, 'saveMembres']],
    ['GET', '/admin/commission/sessions', [AdminCommissionController::class, 'sessions']],
    ['GET', '/admin/commission/sessions/nouvelle', [AdminCommissionController::class, 'createSession']],
    ['POST', '/admin/commission/sessions', [AdminCommissionController::class, 'storeSession']],
    ['GET', '/admin/commission/sessions/{id}', [AdminCommissionController::class, 'showSession']],
    ['GET', '/admin/commission/sessions/{id}/pdf', [AdminCommissionController::class, 'downloadSessionPdf']],
    ['GET', '/admin/commission/assignation', [AdminCommissionController::class, 'assignation']],
    ['GET', '/admin/commission/assignation/{rapportId}', [AdminCommissionController::class, 'assignationForm']],
    ['POST', '/admin/commission/assignation/{rapportId}', [AdminCommissionController::class, 'assign']],

    ['GET', '/admin/soutenances', [AdminSoutenanceController::class, 'index']],
    ['GET', '/admin/soutenances/programmer', [AdminSoutenanceController::class, 'create']],
    ['POST', '/admin/soutenances', [AdminSoutenanceController::class, 'store']],
    ['GET', '/admin/soutenances/{id}', [AdminSoutenanceController::class, 'show']],
    ['GET', '/admin/soutenances/{id}/modifier', [AdminSoutenanceController::class, 'edit']],
    ['POST', '/admin/soutenances/{id}', [AdminSoutenanceController::class, 'update']],
    ['GET', '/admin/soutenances/jurys', [AdminSoutenanceController::class, 'jurys']],
    ['GET', '/admin/soutenances/jurys/{matricule}', [AdminSoutenanceController::class, 'composeJury']],
    ['POST', '/admin/soutenances/jurys/{matricule}', [AdminSoutenanceController::class, 'saveJury']],
    ['GET', '/admin/soutenances/planning', [AdminSoutenanceController::class, 'planning']],
    ['GET', '/admin/soutenances/planning/pdf', [AdminSoutenanceController::class, 'planningPdf']],
    ['GET', '/admin/soutenances/{id}/notation', [AdminSoutenanceController::class, 'notationForm']],
    ['POST', '/admin/soutenances/{id}/notation', [AdminSoutenanceController::class, 'saveNotation']],
    ['GET', '/admin/soutenances/deliberation', [AdminSoutenanceController::class, 'deliberation']],
    ['GET', '/admin/soutenances/{id}/deliberer', [AdminSoutenanceController::class, 'delibererForm']],
    ['POST', '/admin/soutenances/{id}/deliberer', [AdminSoutenanceController::class, 'deliberer']],

    ['GET', '/admin/parametres', [ParametresController::class, 'index']],
    ['GET', '/admin/parametres/application', [ParametresController::class, 'application']],
    ['POST', '/admin/parametres/application', [ParametresController::class, 'saveApplication']],
    ['GET', '/admin/parametres/annees', [ParametresController::class, 'annees']],
    ['GET', '/admin/parametres/annees/nouvelle', [ParametresController::class, 'createAnnee']],
    ['POST', '/admin/parametres/annees', [ParametresController::class, 'storeAnnee']],
    ['GET', '/admin/parametres/annees/{id}/modifier', [ParametresController::class, 'editAnnee']],
    ['POST', '/admin/parametres/annees/{id}', [ParametresController::class, 'updateAnnee']],
    ['POST', '/admin/parametres/annees/{id}/activer', [ParametresController::class, 'activateAnnee']],
    ['GET', '/admin/parametres/filieres', [ParametresController::class, 'filieres']],
    ['POST', '/admin/parametres/filieres', [ParametresController::class, 'saveFilieres']],
    ['GET', '/admin/parametres/niveaux', [ParametresController::class, 'niveaux']],
    ['POST', '/admin/parametres/niveaux', [ParametresController::class, 'saveNiveaux']],
    ['GET', '/admin/parametres/grades', [ParametresController::class, 'grades']],
    ['POST', '/admin/parametres/grades', [ParametresController::class, 'saveGrades']],
    ['GET', '/admin/parametres/fonctions', [ParametresController::class, 'fonctions']],
    ['POST', '/admin/parametres/fonctions', [ParametresController::class, 'saveFonctions']],
    ['GET', '/admin/parametres/salles', [ParametresController::class, 'salles']],
    ['GET', '/admin/parametres/salles/nouvelle', [ParametresController::class, 'createSalle']],
    ['POST', '/admin/parametres/salles', [ParametresController::class, 'storeSalle']],
    ['GET', '/admin/parametres/salles/{id}/modifier', [ParametresController::class, 'editSalle']],
    ['POST', '/admin/parametres/salles/{id}', [ParametresController::class, 'updateSalle']],
    ['GET', '/admin/parametres/roles-jury', [ParametresController::class, 'rolesJury']],
    ['POST', '/admin/parametres/roles-jury', [ParametresController::class, 'saveRolesJury']],
    ['GET', '/admin/parametres/criteres', [ParametresController::class, 'criteres']],
    ['POST', '/admin/parametres/criteres', [ParametresController::class, 'saveCriteres']],
    ['GET', '/admin/parametres/entreprises', [ParametresController::class, 'entreprises']],
    ['GET', '/admin/parametres/entreprises/nouvelle', [ParametresController::class, 'createEntreprise']],
    ['POST', '/admin/parametres/entreprises', [ParametresController::class, 'storeEntreprise']],
    ['GET', '/admin/parametres/entreprises/{id}/modifier', [ParametresController::class, 'editEntreprise']],
    ['POST', '/admin/parametres/entreprises/{id}', [ParametresController::class, 'updateEntreprise']],

    ['GET', '/etudiant', [EtudiantDashboardController::class, 'index']],
    ['GET', '/etudiant/dashboard', [EtudiantDashboardController::class, 'index']],

    ['GET', '/etudiant/profil', [ProfilController::class, 'show']],
    ['GET', '/etudiant/profil/modifier', [ProfilController::class, 'edit']],
    ['POST', '/etudiant/profil', [ProfilController::class, 'update']],

    ['GET', '/etudiant/scolarite', [ScolariteController::class, 'index']],

    ['GET', '/etudiant/candidature', [EtudiantCandidatureController::class, 'index']],
    ['GET', '/etudiant/candidature/formulaire', [EtudiantCandidatureController::class, 'formulaire']],
    ['POST', '/etudiant/candidature/sauvegarder', [EtudiantCandidatureController::class, 'sauvegarder']],
    ['POST', '/etudiant/candidature/soumettre', [EtudiantCandidatureController::class, 'soumettre']],
    ['GET', '/etudiant/candidature/recapitulatif', [EtudiantCandidatureController::class, 'recapitulatif']],

    ['GET', '/etudiant/rapport', [EtudiantRapportController::class, 'index']],
    ['GET', '/etudiant/rapport/nouveau', [EtudiantRapportController::class, 'choisirModele']],
    ['POST', '/etudiant/rapport/creer', [EtudiantRapportController::class, 'creer']],
    ['GET', '/etudiant/rapport/editeur', [EtudiantRapportController::class, 'editeur']],
    ['POST', '/etudiant/rapport/sauvegarder', [EtudiantRapportController::class, 'sauvegarder']],
    ['GET', '/etudiant/rapport/informations', [EtudiantRapportController::class, 'informations']],
    ['POST', '/etudiant/rapport/informations', [EtudiantRapportController::class, 'updateInformations']],
    ['POST', '/etudiant/rapport/soumettre', [EtudiantRapportController::class, 'soumettre']],
    ['GET', '/etudiant/rapport/voir', [EtudiantRapportController::class, 'voir']],
    ['GET', '/etudiant/rapport/telecharger', [EtudiantRapportController::class, 'telecharger']],

    ['GET', '/etudiant/soutenance', [EtudiantSoutenanceController::class, 'index']],

    ['GET', '/commission', [CommissionDashboardController::class, 'index']],
    ['GET', '/commission/dashboard', [CommissionDashboardController::class, 'index']],

    ['GET', '/commission/rapports', [CommissionRapportController::class, 'index']],
    ['GET', '/commission/rapports/{id}', [CommissionRapportController::class, 'show']],
    ['GET', '/commission/evaluer/{id}', [CommissionRapportController::class, 'evaluate']],
    ['POST', '/commission/evaluer/{id}', [CommissionRapportController::class, 'vote']],

    ['GET', '/commission/sessions', [SessionController::class, 'index']],
    ['GET', '/commission/sessions/{id}', [SessionController::class, 'show']],

    ['GET', '/encadreur', [EncadreurDashboardController::class, 'index']],
    ['GET', '/encadreur/dashboard', [EncadreurDashboardController::class, 'index']],

    ['GET', '/encadreur/etudiants', [EncadreurEtudiantController::class, 'index']],
    ['GET', '/encadreur/etudiants/{matricule}', [EncadreurEtudiantController::class, 'show']],

    ['GET', '/encadreur/rapports', [EncadreurRapportController::class, 'index']],
    ['GET', '/encadreur/rapports/{id}', [EncadreurRapportController::class, 'show']],
    ['POST', '/encadreur/rapports/{id}/commenter', [EncadreurRapportController::class, 'comment']],

    ['GET', '/encadreur/aptitude', [AptitudeController::class, 'index']],
    ['GET', '/encadreur/aptitude/{matricule}', [AptitudeController::class, 'form']],
    ['POST', '/encadreur/aptitude/{matricule}', [AptitudeController::class, 'validate']],

    ['GET', '/api/entreprises/search', [EntrepriseApiController::class, 'search']],
    ['GET', '/api/entreprises/{id}', [EntrepriseApiController::class, 'get']],
    ['POST', '/api/entreprises', [EntrepriseApiController::class, 'create']],

    ['GET', '/api/etudiants/search', [EtudiantApiController::class, 'search']],
    ['GET', '/api/etudiants/{matricule}', [EtudiantApiController::class, 'get']],

    ['GET', '/api/enseignants/search', [EnseignantApiController::class, 'search']],
    ['GET', '/api/enseignants/{matricule}', [EnseignantApiController::class, 'get']],

    ['GET', '/api/rapports/{id}/versions', [RapportApiController::class, 'versions']],
    ['POST', '/api/rapports/{id}/autosave', [RapportApiController::class, 'autoSave']],
];
