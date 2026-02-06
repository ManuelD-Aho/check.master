# Arborescence Complète - Architecture MVC

## 1. Structure racine du projet

```
miage-platform/
├── .env                          # Variables d'environnement (ne pas commiter)
├── .env.example                  # Template des variables d'environnement
├── .gitignore                    # Fichiers à ignorer par Git
├── .htaccess                     # Configuration Apache (réécriture)
├── composer.json                 # Dépendances PHP
├── composer.lock                 # Versions verrouillées
├── phinx.php                     # Configuration Phinx (migrations)
├── README.md                     # Documentation projet
│
├── config/                       # Configuration de l'application
│   ├── app.php                   # Configuration générale
│   ├── container.php             # Conteneur de dépendances (DI)
│   ├── database.php              # Configuration base de données
│   ├── routes.php                # Définition des routes
│   ├── middlewares.php           # Pipeline de middlewares
│   ├── services.php              # Enregistrement des services
│   └── workflows/                # Configuration des workflows
│       ├── candidature.php       # Workflow candidature
│       ├── rapport.php           # Workflow rapport
│       ├── commission.php        # Workflow commission
│       └── soutenance.php        # Workflow soutenance
│
├── database/                     # Scripts base de données
│   ├── migrations/               # Migrations Phinx
│   │   ├── 20250101000001_create_users_tables.php
│   │   ├── 20250101000002_create_students_tables.php
│   │   ├── 20250101000003_create_stages_tables.php
│   │   ├── 20250101000004_create_reports_tables.php
│   │   ├── 20250101000005_create_commission_tables.php
│   │   ├── 20250101000006_create_soutenance_tables.php
│   │   └── 20250101000007_create_settings_tables.php
│   ├── seeds/                    # Données initiales
│   │   ├── TypesUtilisateurSeeder.php
│   │   ├── GroupesUtilisateurSeeder.php
│   │   ├── PermissionsSeeder.php
│   │   ├── MenusSeeder.php
│   │   ├── NiveauxEtudeSeeder.php
│   │   ├── RolesJurySeeder.php
│   │   ├── CriteresEvaluationSeeder.php
│   │   └── MentionsSeeder.php
│   └── schema.sql                # Schéma SQL complet (référence)
│
├── public/                       # Dossier web accessible
│   ├── index.php                 # Point d'entrée unique
│   ├── .htaccess                 # Réécriture vers index.php
│   └── assets/                   # Ressources statiques
│       ├── css/
│       │   ├── app.css           # Styles principaux
│       │   ├── admin.css         # Styles back-office
│       │   ├── etudiant.css      # Styles espace étudiant
│       │   └── components/       # Composants CSS
│       │       ├── buttons.css
│       │       ├── forms.css
│       │       ├── tables.css
│       │       ├── cards.css
│       │       ├── alerts.css
│       │       └── navigation.css
│       ├── js/
│       │   ├── app.js            # JavaScript principal
│       │   ├── ajax.js           # Utilitaires AJAX
│       │   ├── validation.js     # Validation formulaires
│       │   └── modules/          # Scripts par module
│       │       ├── editor.js     # Éditeur de texte riche
│       │       ├── autosave.js   # Sauvegarde automatique
│       │       ├── datepicker.js # Sélecteur de dates
│       │       ├── autocomplete.js
│       │       └── permissions-matrix.js
│       ├── images/
│       │   ├── logos/
│       │   │   ├── logo_ufhb.png
│       │   │   ├── logo_ufr_mi.png
│       │   │   └── favicon.ico
│       │   └── icons/
│       └── vendors/              # Bibliothèques JS/CSS tierces
│           ├── fontawesome/
│           ├── tinymce/          # Éditeur WYSIWYG
│           └── flatpickr/        # Datepicker
│
├── src/                          # Code source PHP
│   ├── App.php                   # Classe principale Application
│   │
│   ├── Controller/               # Contrôleurs (C de MVC)
│   │   ├── AbstractController.php
│   │   │
│   │   ├── Auth/                 # Module 1 - Authentification
│   │   │   ├── LoginController.php
│   │   │   ├── LogoutController.php
│   │   │   ├── PasswordResetController.php
│   │   │   └── TwoFactorController.php
│   │   │
│   │   ├── Admin/                # Back-office administration
│   │   │   ├── DashboardController.php
│   │   │   │
│   │   │   ├── Utilisateur/      # Gestion utilisateurs
│   │   │   │   ├── UtilisateurController.php
│   │   │   │   ├── GroupeController.php
│   │   │   │   └── PermissionController.php
│   │   │   │
│   │   │   ├── Etudiant/         # Gestion étudiants
│   │   │   │   ├── EtudiantController.php
│   │   │   │   ├── InscriptionController.php
│   │   │   │   ├── VersementController.php
│   │   │   │   ├── EcheanceController.php
│   │   │   │   └── NoteController.php
│   │   │   │
│   │   │   ├── Candidature/      # Validation candidatures
│   │   │   │   └── CandidatureAdminController.php
│   │   │   │
│   │   │   ├── Rapport/          # Vérification rapports
│   │   │   │   └── RapportAdminController.php
│   │   │   │
│   │   │   ├── Commission/       # Gestion commission
│   │   │   │   ├── MembreCommissionController.php
│   │   │   │   ├── AssignationController.php
│   │   │   │   └── PvCommissionController.php
│   │   │   │
│   │   │   ├── Soutenance/       # Gestion soutenances
│   │   │   │   ├── JuryController.php
│   │   │   │   ├── PlanningController.php
│   │   │   │   ├── NotationController.php
│   │   │   │   └── DeliberationController.php
│   │   │   │
│   │   │   ├── Document/         # Génération documents
│   │   │   │   └── DocumentController.php
│   │   │   │
│   │   │   ├── Parametrage/      # Configuration système
│   │   │   │   ├── ApplicationController.php
│   │   │   │   ├── AnneeAcademiqueController.php
│   │   │   │   ├── NiveauEtudeController.php
│   │   │   │   ├── SemestreController.php
│   │   │   │   ├── FiliereController.php
│   │   │   │   ├── UeController.php
│   │   │   │   ├── EcueController.php
│   │   │   │   ├── GradeController.php
│   │   │   │   ├── FonctionController.php
│   │   │   │   ├── RoleJuryController.php
│   │   │   │   ├── CritereEvaluationController.php
│   │   │   │   ├── SalleController.php
│   │   │   │   ├── EntrepriseController.php
│   │   │   │   ├── MenuController.php
│   │   │   │   └── MessageController.php
│   │   │   │
│   │   │   └── Maintenance/      # Maintenance système
│   │   │       ├── AuditController.php
│   │   │       ├── StatistiqueController.php
│   │   │       ├── CacheController.php
│   │   │       └── MaintenanceModeController.php
│   │   │
│   │   ├── Etudiant/             # Espace étudiant
│   │   │   ├── DashboardEtudiantController.php
│   │   │   ├── CandidatureController.php
│   │   │   ├── RapportController.php
│   │   │   └── SuiviController.php
│   │   │
│   │   ├── Encadreur/            # Espace encadreur pédagogique
│   │   │   └── AptitudeController.php
│   │   │
│   │   ├── Commission/           # Espace membres commission
│   │   │   └── EvaluationController.php
│   │   │
│   │   └── Api/                  # API endpoints (AJAX)
│   │       ├── AutocompleteController.php
│   │       ├── ValidationController.php
│   │       └── UploadController.php
│   │
│   ├── Entity/                   # Entités Doctrine (M de MVC)
│   │   ├── User/
│   │   │   ├── Utilisateur.php
│   │   │   ├── TypeUtilisateur.php
│   │   │   ├── GroupeUtilisateur.php
│   │   │   ├── NiveauAccesDonnees.php
│   │   │   ├── Permission.php
│   │   │   └── AuthRateLimit.php
│   │   │
│   │   ├── Academic/
│   │   │   ├── AnneeAcademique.php
│   │   │   ├── NiveauEtude.php
│   │   │   ├── Semestre.php
│   │   │   ├── Filiere.php
│   │   │   ├── UniteEnseignement.php
│   │   │   └── ElementConstitutif.php
│   │   │
│   │   ├── Student/
│   │   │   ├── Etudiant.php
│   │   │   ├── Inscription.php
│   │   │   ├── Versement.php
│   │   │   ├── Echeance.php
│   │   │   └── Note.php
│   │   │
│   │   ├── Staff/
│   │   │   ├── Enseignant.php
│   │   │   ├── PersonnelAdmin.php
│   │   │   ├── Grade.php
│   │   │   ├── Fonction.php
│   │   │   └── Specialite.php
│   │   │
│   │   ├── Stage/
│   │   │   ├── Candidature.php
│   │   │   ├── InformationStage.php
│   │   │   ├── Entreprise.php
│   │   │   └── ResumeCandidature.php
│   │   │
│   │   ├── Report/
│   │   │   ├── Rapport.php
│   │   │   ├── VersionRapport.php
│   │   │   ├── ModeleRapport.php
│   │   │   ├── CommentaireRapport.php
│   │   │   └── ValidationRapport.php
│   │   │
│   │   ├── Commission/
│   │   │   ├── MembreCommission.php
│   │   │   ├── EvaluationRapport.php
│   │   │   ├── AffectationEncadrant.php
│   │   │   ├── SessionCommission.php
│   │   │   ├── CompteRendu.php
│   │   │   └── CompteRenduRapport.php
│   │   │
│   │   ├── Soutenance/
│   │   │   ├── AptitudeSoutenance.php
│   │   │   ├── Jury.php
│   │   │   ├── RoleJury.php
│   │   │   ├── CompositionJury.php
│   │   │   ├── Soutenance.php
│   │   │   ├── Salle.php
│   │   │   ├── CritereEvaluation.php
│   │   │   ├── BaremeCritere.php
│   │   │   ├── NoteSoutenance.php
│   │   │   ├── ResultatFinal.php
│   │   │   ├── Mention.php
│   │   │   └── DecisionJury.php
│   │   │
│   │   ├── System/
│   │   │   ├── CategorieFonctionnalite.php
│   │   │   ├── Fonctionnalite.php
│   │   │   ├── RouteAction.php
│   │   │   ├── AppSetting.php
│   │   │   ├── Message.php
│   │   │   ├── EmailTemplate.php
│   │   │   └── Piste.php
│   │   │
│   │   └── Document/
│   │       ├── GeneratedDocument.php
│   │       └── DocumentArchive.php
│   │
│   ├── Repository/               # Repositories Doctrine
│   │   ├── AbstractRepository.php
│   │   ├── User/
│   │   │   ├── UtilisateurRepository.php
│   │   │   ├── GroupeUtilisateurRepository.php
│   │   │   └── PermissionRepository.php
│   │   ├── Student/
│   │   │   ├── EtudiantRepository.php
│   │   │   ├── InscriptionRepository.php
│   │   │   └── NoteRepository.php
│   │   ├── Stage/
│   │   │   ├── CandidatureRepository.php
│   │   │   └── EntrepriseRepository.php
│   │   ├── Report/
│   │   │   └── RapportRepository.php
│   │   ├── Commission/
│   │   │   ├── EvaluationRapportRepository.php
│   │   │   └── CompteRenduRepository.php
│   │   ├── Soutenance/
│   │   │   ├── JuryRepository.php
│   │   │   ├── SoutenanceRepository.php
│   │   │   └── ResultatFinalRepository.php
│   │   └── System/
│   │       ├── FonctionnaliteRepository.php
│   │       ├── AppSettingRepository.php
│   │       └── PisteRepository.php
│   │
│   ├── Service/                  # Services métier
│   │   ├── Auth/
│   │   │   ├── AuthenticationService.php
│   │   │   ├── AuthorizationService.php
│   │   │   ├── PasswordService.php
│   │   │   ├── TwoFactorService.php
│   │   │   ├── JwtService.php
│   │   │   └── RateLimiterService.php
│   │   │
│   │   ├── Academic/
│   │   │   ├── AnneeAcademiqueService.php
│   │   │   └── NoteCalculationService.php
│   │   │
│   │   ├── Student/
│   │   │   ├── EtudiantService.php
│   │   │   ├── InscriptionService.php
│   │   │   ├── PaiementService.php
│   │   │   └── MatriculeGenerator.php
│   │   │
│   │   ├── Stage/
│   │   │   ├── CandidatureService.php
│   │   │   └── EntrepriseService.php
│   │   │
│   │   ├── Report/
│   │   │   ├── RapportService.php
│   │   │   ├── ContentSanitizerService.php
│   │   │   └── VersioningService.php
│   │   │
│   │   ├── Commission/
│   │   │   ├── CommissionService.php
│   │   │   ├── EvaluationService.php
│   │   │   ├── VoteService.php
│   │   │   └── AssignationService.php
│   │   │
│   │   ├── Soutenance/
│   │   │   ├── AptitudeService.php
│   │   │   ├── JuryService.php
│   │   │   ├── PlanningService.php
│   │   │   ├── NotationService.php
│   │   │   ├── MoyenneCalculationService.php
│   │   │   └── DeliberationService.php
│   │   │
│   │   ├── Document/
│   │   │   ├── DocumentGeneratorService.php
│   │   │   ├── PdfGeneratorService.php
│   │   │   ├── RecuGeneratorService.php
│   │   │   ├── BulletinGeneratorService.php
│   │   │   ├── RapportPdfGeneratorService.php
│   │   │   ├── PvCommissionGeneratorService.php
│   │   │   ├── PlanningGeneratorService.php
│   │   │   ├── Annexe1GeneratorService.php
│   │   │   ├── Annexe2GeneratorService.php
│   │   │   ├── Annexe3GeneratorService.php
│   │   │   └── PvFinalGeneratorService.php
│   │   │
│   │   ├── Email/
│   │   │   ├── EmailService.php
│   │   │   └── TemplateRenderer.php
│   │   │
│   │   ├── System/
│   │   │   ├── SettingsService.php
│   │   │   ├── EncryptionService.php
│   │   │   ├── CacheService.php
│   │   │   ├── AuditService.php
│   │   │   └── MenuService.php
│   │   │
│   │   └── Workflow/
│   │       ├── WorkflowRegistry.php
│   │       └── WorkflowEventSubscriber.php
│   │
│   ├── Middleware/               # Middlewares PSR-15
│   │   ├── SessionMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   ├── AuthenticationMiddleware.php
│   │   ├── PermissionMiddleware.php
│   │   ├── MaintenanceModeMiddleware.php
│   │   ├── RateLimitMiddleware.php
│   │   └── AuditMiddleware.php
│   │
│   ├── Validator/                # Validateurs personnalisés
│   │   ├── AbstractValidator.php
│   │   ├── EtudiantValidator.php
│   │   ├── CandidatureValidator.php
│   │   ├── RapportValidator.php
│   │   ├── JuryValidator.php
│   │   ├── SoutenanceValidator.php
│   │   └── NoteValidator.php
│   │
│   ├── Event/                    # Événements système
│   │   ├── User/
│   │   │   ├── UserCreatedEvent.php
│   │   │   ├── UserLoginEvent.php
│   │   │   └── UserBlockedEvent.php
│   │   ├── Student/
│   │   │   ├── EtudiantCreatedEvent.php
│   │   │   └── InscriptionCreatedEvent.php
│   │   ├── Stage/
│   │   │   ├── CandidatureSubmittedEvent.php
│   │   │   ├── CandidatureValidatedEvent.php
│   │   │   └── CandidatureRejectedEvent.php
│   │   ├── Report/
│   │   │   ├── RapportSubmittedEvent.php
│   │   │   ├── RapportApprovedEvent.php
│   │   │   └── RapportReturnedEvent.php
│   │   ├── Commission/
│   │   │   ├── VoteSubmittedEvent.php
│   │   │   ├── VoteCompleteEvent.php
│   │   │   └── EncadrantsAssignedEvent.php
│   │   └── Soutenance/
│   │       ├── AptitudeValidatedEvent.php
│   │       ├── JuryComposedEvent.php
│   │       ├── SoutenanceScheduledEvent.php
│   │       └── DeliberationCompletedEvent.php
│   │
│   ├── EventListener/            # Listeners d'événements
│   │   ├── User/
│   │   │   ├── SendCredentialsListener.php
│   │   │   └── LogLoginListener.php
│   │   ├── Stage/
│   │   │   └── NotifyCandidatureListener.php
│   │   ├── Report/
│   │   │   └── NotifyRapportStatusListener.php
│   │   ├── Commission/
│   │   │   ├── NotifyVoteProgressListener.php
│   │   │   └── UnlockReportOnRejectListener.php
│   │   └── Soutenance/
│   │       └── SendConvocationListener.php
│   │
│   ├── Helper/                   # Fonctions utilitaires
│   │   ├── DateHelper.php
│   │   ├── StringHelper.php
│   │   ├── NumberHelper.php
│   │   ├── FileHelper.php
│   │   └── UrlHelper.php
│   │
│   └── Exception/                # Exceptions personnalisées
│       ├── AuthenticationException.php
│       ├── AuthorizationException.php
│       ├── ValidationException.php
│       ├── NotFoundException.php
│       ├── WorkflowException.php
│       └── DocumentGenerationException.php
│
├── storage/                      # Stockage fichiers (non versionné)
│   ├── cache/                    # Cache applicatif
│   │   ├── config/
│   │   ├── views/
│   │   └── permissions/
│   ├── documents/                # Documents générés
│   │   ├── recus/
│   │   ├── bulletins/
│   │   ├── rapports/
│   │   ├── pv_commission/
│   │   ├── planning/
│   │   └── pv_finaux/
│   ├── logs/                     # Fichiers de log
│   │   ├── app.log
│   │   ├── audit.log
│   │   └── errors.log
│   ├── uploads/                  # Fichiers uploadés
│   │   ├── photos/
│   │   └── temp/
│   └── sessions/                 # Sessions PHP
│
├── templates/                    # Templates (V de MVC)
│   ├── layout/                   # Layouts principaux
│   │   ├── base.php              # Layout de base
│   │   ├── admin.php             # Layout back-office
│   │   ├── etudiant.php          # Layout espace étudiant
│   │   ├── auth.php              # Layout pages auth
│   │   └── error.php             # Layout pages erreur
│   │
│   ├── components/               # Composants réutilisables
│   │   ├── header.php
│   │   ├── footer.php
│   │   ├── sidebar.php
│   │   ├── breadcrumb.php
│   │   ├── pagination.php
│   │   ├── alert.php
│   │   ├── table.php
│   │   ├── form/
│   │   │   ├── input.php
│   │   │   ├── select.php
│   │   │   ├── textarea.php
│   │   │   ├── checkbox.php
│   │   │   ├── radio.php
│   │   │   ├── file.php
│   │   │   └── datepicker.php
│   │   └── card.php
│   │
│   ├── auth/                     # Pages authentification
│   │   ├── login.php
│   │   ├── two_factor.php
│   │   ├── forgot_password.php
│   │   ├── reset_password.php
│   │   └── first_login.php
│   │
│   ├── admin/                    # Pages back-office
│   │   ├── dashboard.php
│   │   │
│   │   ├── utilisateur/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   └── show.php
│   │   │
│   │   ├── etudiant/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   ├── show.php
│   │   │   ├── import.php
│   │   │   ├── inscription.php
│   │   │   ├── versement.php
│   │   │   └── bulletin.php
│   │   │
│   │   ├── candidature/
│   │   │   ├── index.php
│   │   │   ├── show.php
│   │   │   ├── validate.php
│   │   │   └── reject.php
│   │   │
│   │   ├── rapport/
│   │   │   ├── verification.php
│   │   │   ├── show.php
│   │   │   └── approuves.php
│   │   │
│   │   ├── commission/
│   │   │   ├── membres.php
│   │   │   ├── assignation.php
│   │   │   ├── pv/
│   │   │   │   ├── index.php
│   │   │   │   ├── create.php
│   │   │   │   └── show.php
│   │   │
│   │   ├── soutenance/
│   │   │   ├── jurys.php
│   │   │   ├── composer_jury.php
│   │   │   ├── planning.php
│   │   │   ├── programmer.php
│   │   │   ├── notation.php
│   │   │   ├── deliberation.php
│   │   │   └── tableau.php
│   │   │
│   │   └── parametrage/
│   │       ├── application.php
│   │       ├── email.php
│   │       ├── securite.php
│   │       ├── annees.php
│   │       ├── niveaux.php
│   │       ├── semestres.php
│   │       ├── filieres.php
│   │       ├── ue.php
│   │       ├── ecue.php
│   │       ├── grades.php
│   │       ├── fonctions.php
│   │       ├── roles_jury.php
│   │       ├── criteres.php
│   │       ├── salles.php
│   │       ├── entreprises.php
│   │       ├── menus/
│   │       │   ├── categories.php
│   │       │   ├── fonctionnalites.php
│   │       │   └── permissions.php
│   │       └── messages/
│   │           ├── libelles.php
│   │           └── emails.php
│   │
│   ├── etudiant/                 # Pages espace étudiant
│   │   ├── dashboard.php
│   │   ├── candidature/
│   │   │   ├── index.php
│   │   │   ├── formulaire.php
│   │   │   └── recapitulatif.php
│   │   ├── rapport/
│   │   │   ├── choisir_modele.php
│   │   │   ├── editeur.php
│   │   │   ├── informations.php
│   │   │   └── lecture.php
│   │   └── suivi/
│   │       └── index.php
│   │
│   ├── encadreur/                # Pages espace encadreur
│   │   ├── etudiants.php
│   │   └── aptitude.php
│   │
│   ├── commission/               # Pages espace commission
│   │   ├── rapports.php
│   │   ├── evaluer.php
│   │   ├── votes.php
│   │   └── deliberation.php
│   │
│   ├── pdf/                      # Templates PDF
│   │   ├── recu.php
│   │   ├── bulletin.php
│   │   ├── page_garde_rapport.php
│   │   ├── pv_commission.php
│   │   ├── planning_soutenances.php
│   │   ├── annexe1.php
│   │   ├── annexe2.php
│   │   └── annexe3.php
│   │
│   ├── email/                    # Templates email
│   │   ├── user_created.php
│   │   ├── password_reset.php
│   │   ├── candidature_submitted.php
│   │   ├── candidature_validated.php
│   │   ├── candidature_rejected.php
│   │   ├── rapport_submitted.php
│   │   ├── rapport_approved.php
│   │   ├── rapport_returned.php
│   │   ├── vote_progress.php
│   │   ├── encadrants_assigned.php
│   │   ├── convocation_soutenance.php
│   │   └── pv_sent.php
│   │
│   └── error/                    # Pages d'erreur
│       ├── 403.php
│       ├── 404.php
│       ├── 500.php
│       └── maintenance.php
│
├── tests/                        # Tests automatisés
│   ├── Unit/
│   │   ├── Service/
│   │   ├── Validator/
│   │   └── Helper/
│   ├── Integration/
│   │   ├── Repository/
│   │   └── Workflow/
│   └── Functional/
│       ├── Controller/
│       └── Api/
│
└── vendor/                       # Dépendances Composer (commité pour mutualisé)
```

---

## 2. Résumé par couche MVC

### 2.1 Model (Données)
- **Entités** : `src/Entity/` - 40+ classes Doctrine
- **Repositories** : `src/Repository/` - Accès données
- **Migrations** : `database/migrations/` - Évolution schéma

### 2.2 View (Présentation)
- **Templates** : `templates/` - 100+ fichiers PHP
- **Assets** : `public/assets/` - CSS, JS, images
- **Templates PDF** : `templates/pdf/` - Documents générés
- **Templates Email** : `templates/email/` - Notifications

### 2.3 Controller (Logique)
- **Contrôleurs** : `src/Controller/` - 50+ classes
- **Services** : `src/Service/` - Logique métier
- **Middlewares** : `src/Middleware/` - Pipeline requêtes
- **Events** : `src/Event/` + `src/EventListener/` - Découplage

---

## 3. Conventions de nommage

### 3.1 Fichiers PHP
- **Classes** : `PascalCase.php` → `EtudiantService.php`
- **Interfaces** : `PascalCaseInterface.php`
- **Traits** : `PascalCaseTrait.php`

### 3.2 Fichiers Template
- **Pages** : `snake_case.php` → `create.php`, `index.php`
- **Composants** : `snake_case.php` → `pagination.php`

### 3.3 Assets
- **CSS** : `kebab-case.css`
- **JS** : `kebab-case.js` ou `camelCase.js`

### 3.4 Base de données
- **Tables** : `snake_case` → `groupe_utilisateur`
- **Colonnes** : `snake_case` → `date_creation`
