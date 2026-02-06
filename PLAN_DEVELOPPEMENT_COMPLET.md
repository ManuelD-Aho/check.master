# PLAN DE DÉVELOPPEMENT COMPLET
## Plateforme MIAGE-GI - De A à Z Sans Interruption

**Généré le:** 04/02/2026  
**Projet:** Plateforme de Gestion des Stages et Soutenances de Master  
**Département:** MIAGE-GI - Université Félix Houphouët-Boigny (Côte d'Ivoire)

---

## TABLE DES MATIÈRES

1. [Résumé Exécutif](#1-résumé-exécutif)
2. [Phase 0 - Prérequis & Configuration](#2-phase-0---prérequis--configuration)
3. [Phase 1 - Fondations (Core)](#3-phase-1---fondations-core)
4. [Phase 2 - Module Utilisateurs & RBAC](#4-phase-2---module-utilisateurs--rbac)
5. [Phase 3 - Module Étudiants & Inscriptions](#5-phase-3---module-étudiants--inscriptions)
6. [Phase 4 - Module Candidatures Stage](#6-phase-4---module-candidatures-stage)
7. [Phase 5 - Module Rédaction & Rapports](#7-phase-5---module-rédaction--rapports)
8. [Phase 6 - Module Commission](#8-phase-6---module-commission)
9. [Phase 7 - Module Jurys & Soutenances](#9-phase-7---module-jurys--soutenances)
10. [Phase 8 - Module Génération Documents PDF](#10-phase-8---module-génération-documents-pdf)
11. [Phase 9 - Module Paramétrage Système](#11-phase-9---module-paramétrage-système)
12. [Phase 10 - Tests & Déploiement](#12-phase-10---tests--déploiement)
13. [Annexes](#13-annexes)

---

## 1. RÉSUMÉ EXÉCUTIF

### 1.1 Stack Technique

| Composant | Technologie |
|-----------|-------------|
| Backend | PHP 8.4 |
| Frontend | HTML5, CSS3, JavaScript ES6+, AJAX |
| Base de données | MySQL 8.0+ |
| Serveur | Apache (hébergement mutualisé) |
| ORM | Doctrine 3.0 |
| Routage | nikic/fast-route |
| Workflows | symfony/workflow |

### 1.2 Contraintes Clés

- ❌ **Aucune modal** → Navigation par écrans dédiés
- ❌ **Pas de SSH/CLI** → Vendor committé, migrations via web
- ❌ **Pas de workers** → Tout synchrone
- ✅ **Data-Driven** → Tout en base de données
- ✅ **RBAC complet** → Permissions par groupe
- ✅ **Zero duplication** → DRY, Single Source of Truth

### 1.3 Statistiques du Projet

| Métrique | Quantité |
|----------|----------|
| Modules fonctionnels | 8 |
| Tables SQL | 50+ |
| Entités Doctrine | 40+ |
| Contrôleurs | 50+ |
| Services | 40+ |
| Règles de gestion | 146 |
| Scénarios de test | 55 |
| Documents générables | 9 types |

---

## 2. PHASE 0 - PRÉREQUIS & CONFIGURATION

**Durée estimée:** 2-3 jours  
**Bloquants à résoudre avant de commencer:**

### 2.1 Éléments à Obtenir (BLOQUANTS)

| Élément | Responsable | Statut |
|---------|-------------|--------|
| Logo UFHB (PNG/SVG) | Administration | ⏳ À fournir |
| Logo UFR MI (PNG/SVG) | Administration | ⏳ À fournir |
| Credentials SMTP (host, port, user, pass) | IT | ⏳ À fournir |
| Accès BDD (host, name, user, pass) | Hébergeur | ⏳ À fournir |
| Domaine/URL production | IT | ⏳ À fournir |

### 2.2 Décisions à Prendre

| Question | Proposition | Décision |
|----------|-------------|----------|
| Éditeur WYSIWYG | TinyMCE (gratuit, complet) | ⏳ À confirmer |
| Montant scolarité M1 | 500 000 FCFA | ⏳ À confirmer |
| Montant scolarité M2 | 600 000 FCFA | ⏳ À confirmer |
| Durée minimum stage | 90 jours calendaires | ⏳ À confirmer |
| Grade min. président jury | Professeur Titulaire | ⏳ À confirmer |

### 2.3 Actions Phase 0

```
□ [0.1] Créer repository Git
□ [0.2] Créer l'arborescence des dossiers (voir section 13.1)
□ [0.3] Initialiser composer.json avec toutes les dépendances
□ [0.4] Configurer .env.example avec toutes les variables
□ [0.5] Configurer .htaccess pour réécriture URLs
□ [0.6] Créer le fichier phinx.php pour les migrations
□ [0.7] Préparer les assets statiques (CSS de base, JS utilitaires)
```

---

## 3. PHASE 1 - FONDATIONS (CORE)

**Durée estimée:** 5-7 jours  
**Dépendances:** Phase 0 complète

### 3.1 Objectif

Mettre en place l'infrastructure de base de l'application MVC.

### 3.2 Fichiers à Créer

#### 3.2.1 Configuration (config/)

| Fichier | Contenu |
|---------|---------|
| `app.php` | Configuration générale (debug, timezone, locale) |
| `container.php` | Conteneur de dépendances Symfony DI |
| `database.php` | Connexion Doctrine ORM |
| `routes.php` | Définition des routes FastRoute |
| `middlewares.php` | Pipeline PSR-15 (session, CSRF, auth, permissions) |
| `services.php` | Enregistrement de tous les services |

#### 3.2.2 Core (src/)

| Fichier | Responsabilité |
|---------|----------------|
| `App.php` | Classe principale, bootstrap |
| `Controller/AbstractController.php` | Méthodes utilitaires communes |
| `Middleware/SessionMiddleware.php` | Gestion sessions PHP natives |
| `Middleware/CsrfMiddleware.php` | Protection CSRF |
| `Middleware/MaintenanceModeMiddleware.php` | Mode maintenance |
| `Helper/DateHelper.php` | Manipulation dates (Carbon) |
| `Helper/NumberHelper.php` | Calculs et formatage nombres |
| `Helper/StringHelper.php` | Manipulation textes |
| `Exception/*.php` | Exceptions personnalisées |

#### 3.2.3 Point d'Entrée (public/)

```php
// public/index.php
<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// Chargement .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Bootstrap conteneur
$container = require __DIR__ . '/../config/container.php';

// Pipeline middlewares
$app = $container->get('app');
$request = $container->get('request_factory')->createFromGlobals();
$response = $app->handle($request);

// Émission réponse
$container->get('response_emitter')->emit($response);
```

### 3.3 Tâches Phase 1

```
□ [1.1] Installer toutes les dépendances Composer (35 packages)
□ [1.2] Configurer Doctrine ORM (connexion, metadata, proxy)
□ [1.3] Implémenter FastRoute avec le routeur
□ [1.4] Configurer le pipeline Middleland (middlewares)
□ [1.5] Créer AbstractController avec méthodes de base
□ [1.6] Implémenter le système de templates PHP
□ [1.7] Créer les layouts de base (base.php, admin.php, etudiant.php)
□ [1.8] Implémenter le cache fichiers (PSR-16)
□ [1.9] Configurer Monolog pour les logs
□ [1.10] Créer les helpers (Date, Number, String, File, Url)
□ [1.11] Implémenter gestion des erreurs (Whoops en dev)
□ [1.12] Tester le bootstrap complet (route simple OK)
```

---

## 4. PHASE 2 - MODULE UTILISATEURS & RBAC

**Durée estimée:** 7-10 jours  
**Dépendances:** Phase 1 complète

### 4.1 Objectif

Authentification sécurisée et système de permissions complet basé sur les groupes.

### 4.2 Entités Doctrine

| Entité | Table | Champs clés |
|--------|-------|-------------|
| `TypeUtilisateur` | type_utilisateur | id, lib |
| `GroupeUtilisateur` | groupe_utilisateur | id, lib, id_type |
| `NiveauAccesDonnees` | niveau_acces_donnees | id, lib |
| `Utilisateur` | utilisateur | id, nom, login, mdp, id_GU, statut, 2fa_secret |
| `CategorieFonctionnalite` | categories_fonctionnalites | id, code, lib, icone, ordre |
| `Fonctionnalite` | fonctionnalites | id, code, lib, url, id_categorie |
| `Permission` | permissions | id, id_GU, id_fonctionnalite, peut_voir/creer/modifier/supprimer |
| `RouteAction` | route_actions | id, route_pattern, http_method, action_crud |
| `AuthRateLimit` | auth_rate_limits | id, action, ip, attempts, blocked_until |

### 4.3 Services à Implémenter

| Service | Responsabilités |
|---------|-----------------|
| `AuthenticationService` | Login, logout, vérification password (Argon2id) |
| `AuthorizationService` | Vérification permissions, cache permissions |
| `PasswordService` | Hashage, reset password, génération aléatoire |
| `TwoFactorService` | Génération secret TOTP, vérification code |
| `JwtService` | Création/validation tokens JWT |
| `RateLimiterService` | Protection brute-force (5 tentatives / 15 min) |

### 4.4 Middlewares

| Middleware | Logique |
|------------|---------|
| `AuthenticationMiddleware` | Vérifie session/JWT, redirige vers /login |
| `PermissionMiddleware` | Match route → permission requise → vérification |
| `RateLimitMiddleware` | Bloque si trop de tentatives |

### 4.5 Contrôleurs

| Contrôleur | Routes |
|------------|--------|
| `LoginController` | GET/POST /login |
| `LogoutController` | GET /logout |
| `PasswordResetController` | GET/POST /mot-de-passe-oublie, /reset-password/{token} |
| `TwoFactorController` | GET/POST /2fa |
| `Admin/UtilisateurController` | CRUD /admin/utilisateurs |
| `Admin/GroupeController` | CRUD /admin/groupes |
| `Admin/PermissionController` | GET/POST /admin/permissions (matrice) |

### 4.6 Tâches Phase 2

```
□ [2.1] Créer les 8 entités Doctrine avec annotations/attributes
□ [2.2] Créer migration initiale (20250201_create_users_tables.php)
□ [2.3] Créer les seeders (types, groupes par défaut, super admin)
□ [2.4] Implémenter AuthenticationService avec Argon2id
□ [2.5] Implémenter AuthorizationService avec cache
□ [2.6] Implémenter PasswordService avec génération aléatoire
□ [2.7] Implémenter TwoFactorService (TOTP)
□ [2.8] Implémenter JwtService pour tokens de session
□ [2.9] Implémenter RateLimiterService
□ [2.10] Créer AuthenticationMiddleware
□ [2.11] Créer PermissionMiddleware
□ [2.12] Créer les templates auth (login, 2fa, reset)
□ [2.13] Créer le CRUD utilisateurs complet
□ [2.14] Créer le CRUD groupes
□ [2.15] Créer la matrice de permissions (interface grille)
□ [2.16] Implémenter AuditMiddleware (logging actions)
□ [2.17] Tests: login, permissions, rate limiting
```

---

## 5. PHASE 3 - MODULE ÉTUDIANTS & INSCRIPTIONS

**Durée estimée:** 8-10 jours  
**Dépendances:** Phase 2 complète

### 5.1 Objectif

Gestion complète des étudiants, inscriptions, paiements, notes M1/S1M2 et génération des comptes.

### 5.2 Entités Doctrine

| Entité | Table | Relations |
|--------|-------|-----------|
| `Etudiant` | etudiants | → Inscription[], Candidature, Rapport |
| `Inscription` | inscriptions | → Etudiant, NiveauEtude, AnneeAcademique, Versement[], Echeance[] |
| `Versement` | versements | → Inscription |
| `Echeance` | echeances | → Inscription |
| `Note` | notes | → Etudiant, UE, ECUE |
| `AnneeAcademique` | annee_academique | → Inscription[], UE[] |
| `NiveauEtude` | niveau_etude | → Semestre[], Inscription[] |
| `Semestre` | semestre | → NiveauEtude, UE[] |
| `UniteEnseignement` | ue | → Semestre, ECUE[], Note[] |
| `ElementConstitutif` | ecue | → UE, Note[] |

### 5.3 Services à Implémenter

| Service | Responsabilités |
|---------|-----------------|
| `EtudiantService` | CRUD étudiant, recherche, import/export CSV |
| `MatriculeGenerator` | Génération matricule (format: MIAGE-GI-AAAA-XXXX) |
| `InscriptionService` | Création inscription, calcul reste à payer |
| `PaiementService` | Enregistrement versements, calcul statut |
| `NoteCalculationService` | Calcul moyenne S1M2, validation notes |

### 5.4 Workflow Inscription

```
Création Étudiant → Inscription Année → Paiement(s) → Notes M1/S1M2 → Génération Compte Utilisateur
```

### 5.5 Contrôleurs

| Contrôleur | Écrans |
|------------|--------|
| `EtudiantController` | Liste, Création, Fiche, Modification, Import |
| `InscriptionController` | Formulaire inscription, Historique |
| `VersementController` | Enregistrement paiement, Reçu |
| `EcheanceController` | Définition échéancier |
| `NoteController` | Saisie notes M1, S1M2 par UE/ECUE |

### 5.6 Documents Générés

- **Reçu de paiement** (PDF A5) : Après chaque versement
- **Bulletin provisoire** (PDF A4) : Notes M1/S1M2 avec watermark "PROVISOIRE"

### 5.7 Tâches Phase 3

```
□ [3.1] Créer les 10 entités Doctrine (Academic + Student)
□ [3.2] Créer migration (20250202_create_students_tables.php)
□ [3.3] Créer seeders (niveaux, semestres, UE/ECUE exemples)
□ [3.4] Implémenter EtudiantService avec validation
□ [3.5] Implémenter MatriculeGenerator (format configurable)
□ [3.6] Implémenter InscriptionService
□ [3.7] Implémenter PaiementService (tranches, calculs)
□ [3.8] Implémenter EtudiantValidator (respect/validation)
□ [3.9] Créer CRUD Étudiants (index, create, show, edit)
□ [3.10] Créer écran inscription avec sélection année/niveau
□ [3.11] Créer écran enregistrement versement
□ [3.12] Créer écran saisie notes (M1, S1M2)
□ [3.13] Implémenter génération compte utilisateur depuis étudiant
□ [3.14] Implémenter envoi email credentials (PHPMailer)
□ [3.15] Implémenter import CSV étudiants (league/csv)
□ [3.16] Implémenter export CSV étudiants
□ [3.17] Créer template PDF reçu (TCPDF)
□ [3.18] Créer template PDF bulletin provisoire
□ [3.19] Tests: création étudiant → inscription → paiement → notes → compte
```

---

## 6. PHASE 4 - MODULE CANDIDATURES STAGE

**Durée estimée:** 5-7 jours  
**Dépendances:** Phase 3 complète

### 6.1 Objectif

Permettre aux étudiants de soumettre leurs informations de stage et gérer le processus de validation.

### 6.2 Entités Doctrine

| Entité | Table | Statuts possibles |
|--------|-------|-------------------|
| `Candidature` | candidature_soutenance | brouillon, soumise, validee, rejetee |
| `InformationStage` | informations_stage | - |
| `Entreprise` | entreprises | - |
| `ResumeCandidature` | resume_candidature | Snapshot JSON |

### 6.3 Workflow Candidature

```
[brouillon] --soumettre--> [soumise] --valider--> [validee] ✓ Déblocage rapport
                                    |
                                    --rejeter--> [rejetee] --re_soumettre--> [soumise]
```

### 6.4 Configuration Symfony Workflow

```php
// config/workflows/candidature.php
$definition = (new DefinitionBuilder())
    ->addPlaces(['brouillon', 'soumise', 'validee', 'rejetee'])
    ->addTransition(new Transition('soumettre', 'brouillon', 'soumise'))
    ->addTransition(new Transition('valider', 'soumise', 'validee'))
    ->addTransition(new Transition('rejeter', 'soumise', 'rejetee'))
    ->addTransition(new Transition('re_soumettre', 'rejetee', 'soumise'))
    ->build();
```

### 6.5 Services à Implémenter

| Service | Responsabilités |
|---------|-----------------|
| `CandidatureService` | CRUD, transitions workflow, snapshots |
| `EntrepriseService` | Gestion référentiel entreprises |

### 6.6 Contrôleurs

| Contrôleur | Espace | Routes |
|------------|--------|--------|
| `Etudiant/CandidatureController` | Étudiant | /etudiant/candidature/* |
| `Admin/CandidatureAdminController` | Admin | /admin/candidatures/* |

### 6.7 Écrans Étudiant

1. **Index** : Vue statut actuel (brouillon/soumise/validée/rejetée)
2. **Formulaire** : Saisie informations stage (entreprise, dates, sujet, encadrant)
3. **Récapitulatif** : Lecture seule après soumission

### 6.8 Écrans Admin

1. **Liste** : Candidatures en attente de validation (filtre par statut)
2. **Détail** : Visualisation complète
3. **Validation** : Boutons Valider/Rejeter avec commentaire

### 6.9 Tâches Phase 4

```
□ [4.1] Créer les 4 entités Doctrine (Stage/)
□ [4.2] Créer migration (20250203_create_stages_tables.php)
□ [4.3] Configurer Symfony Workflow pour candidature
□ [4.4] Implémenter CandidatureService avec transitions
□ [4.5] Implémenter CandidatureValidator
□ [4.6] Créer WorkflowRegistry central
□ [4.7] Créer écran étudiant: index candidature
□ [4.8] Créer écran étudiant: formulaire candidature (AJAX save)
□ [4.9] Créer écran étudiant: récapitulatif
□ [4.10] Créer écran admin: liste candidatures en attente
□ [4.11] Créer écran admin: validation/rejet avec motif
□ [4.12] Implémenter événements (CandidatureSubmittedEvent, etc.)
□ [4.13] Implémenter listeners (envoi emails)
□ [4.14] Créer templates emails (soumission, validation, rejet)
□ [4.15] Tests: workflow complet candidature
```

---

## 7. PHASE 5 - MODULE RÉDACTION & RAPPORTS

**Durée estimée:** 10-12 jours  
**Dépendances:** Phase 4 complète

### 7.1 Objectif

Éditeur de texte intégré pour la rédaction du rapport de stage avec versioning et workflow de validation.

### 7.2 Entités Doctrine

| Entité | Table | Description |
|--------|-------|-------------|
| `Rapport` | rapport_etudiants | Document principal |
| `VersionRapport` | (nouvelle) | Historique des versions |
| `ModeleRapport` | (nouvelle) | Templates de départ |
| `CommentaireRapport` | (nouvelle) | Retours vérificateurs |
| `ValidationRapport` | valider | Décisions validation |

### 7.3 Workflow Rapport

```
[brouillon] --soumettre--> [soumis] --approuver--> [approuve] --transferer--> [en_commission]
                                   |
                                   --retourner--> [retourne] --re_soumettre--> [soumis]
```

### 7.4 Éditeur WYSIWYG (TinyMCE)

**Fonctionnalités requises:**
- Formatage texte (gras, italique, souligné)
- Titres (H1-H4)
- Listes (puces, numérotées)
- Images (upload avec limite 2Mo)
- Tableaux
- Sauvegarde automatique (30 secondes)
- Mode plein écran

**Sécurité:**
- Nettoyage HTML avec HTMLPurifier
- Whitelist de balises autorisées
- Suppression scripts/événements

### 7.5 Services à Implémenter

| Service | Responsabilités |
|---------|-----------------|
| `RapportService` | CRUD, transitions, génération PDF |
| `ContentSanitizerService` | Nettoyage HTML (HTMLPurifier) |
| `VersioningService` | Création versions, diff |
| `RapportPdfGeneratorService` | Conversion HTML → PDF (TCPDF) |

### 7.6 Contrôleurs

| Contrôleur | Routes |
|------------|--------|
| `Etudiant/RapportController` | /etudiant/rapport/* |
| `Admin/RapportAdminController` | /admin/rapports/* |

### 7.7 Écrans Étudiant

1. **Index** : Accès conditionnel (vérifie candidature validée)
2. **Choix modèle** : Sélection template de départ
3. **Éditeur** : TinyMCE + auto-save + informations rapport
4. **Lecture seule** : Après soumission (PDF intégré)

### 7.8 Écrans Admin (Vérificateur)

1. **Liste** : Rapports soumis en attente de vérification
2. **Visualisation** : Lecture PDF + commentaires
3. **Décision** : Approuver ou Retourner avec commentaire

### 7.9 Tâches Phase 5

```
□ [5.1] Créer les 5 entités Doctrine (Report/)
□ [5.2] Créer migration (20250204_create_reports_tables.php)
□ [5.3] Configurer Symfony Workflow pour rapport
□ [5.4] Implémenter RapportService avec transitions
□ [5.5] Implémenter ContentSanitizerService (HTMLPurifier)
□ [5.6] Implémenter VersioningService
□ [5.7] Configurer TinyMCE (JS: editor.js)
□ [5.8] Implémenter sauvegarde automatique (AJAX: autosave.js)
□ [5.9] Implémenter upload images (max 2Mo, resize)
□ [5.10] Créer écran choix modèle
□ [5.11] Créer écran éditeur avec TinyMCE
□ [5.12] Créer écran lecture seule
□ [5.13] Créer écran admin: liste rapports à vérifier
□ [5.14] Créer écran admin: visualisation + commentaire
□ [5.15] Implémenter génération PDF rapport (TCPDF)
□ [5.16] Implémenter événements (RapportSubmittedEvent, etc.)
□ [5.17] Créer templates emails rapport
□ [5.18] Tests: workflow complet rapport
```

---

## 8. PHASE 6 - MODULE COMMISSION

**Durée estimée:** 8-10 jours  
**Dépendances:** Phase 5 complète

### 8.1 Objectif

Évaluation des rapports par 4 membres avec vote unanime requis, puis assignation des encadrants.

### 8.2 Entités Doctrine

| Entité | Table | Description |
|--------|-------|-------------|
| `MembreCommission` | (nouvelle) | Liste des 4 membres |
| `EvaluationRapport` | evaluations_rapports | Vote par membre |
| `AffectationEncadrant` | affecter | Directeur mémoire + Encadreur péda |
| `SessionCommission` | (nouvelle) | Regroupement par session |
| `CompteRendu` | compte_rendu | PV de la commission |
| `CompteRenduRapport` | compte_rendu_rapport | Liaison CR ↔ Rapports |

### 8.3 Workflow Commission

```
[en_attente_evaluation] --evaluer--> [en_cours_evaluation] --voter(4x)--> [vote_complet]
                                                                               |
                        ┌──────────────────────────────────────────────────────┴──────────┐
                        ↓                                ↓                                ↓
               [vote_unanime_oui]              [vote_unanime_non]            [vote_non_unanime]
                        |                                |                                |
                        ↓                                ↓                                ↓
              [assigner_encadrants]           [retourne_etudiant]            [relance_vote]
                        |                           (→ Module 4)                   (cycle++)
                        ↓
               [pret_pour_pv]
```

### 8.4 Règle d'Unanimité

```
SI count(OUI) == 4 → vote_unanime_oui → Assignation encadrants
SI count(NON) == 4 → vote_unanime_non → Retour étudiant
SINON → vote_non_unanime → Relance du vote (nouveau cycle)
```

### 8.5 Services à Implémenter

| Service | Responsabilités |
|---------|-----------------|
| `CommissionService` | Gestion membres, sessions |
| `EvaluationService` | Création évaluations vides lors transfert |
| `VoteService` | Soumission vote, calcul résultat, relance |
| `AssignationService` | Attribution encadrants, validation règles |

### 8.6 Contrôleurs

| Contrôleur | Routes |
|------------|--------|
| `Commission/EvaluationController` | /commission/evaluer/* (membres) |
| `Admin/MembreCommissionController` | /admin/commission/membres |
| `Admin/AssignationController` | /admin/commission/assignation |
| `Admin/PvCommissionController` | /admin/commission/pv |

### 8.7 Écrans Commission (Membres)

1. **Liste rapports** : Rapports à évaluer avec indicateur vote
2. **Évaluation** : Lecture rapport + vote OUI/NON + commentaire

### 8.8 Écrans Admin

1. **Gestion membres** : CRUD membres commission
2. **Suivi votes** : Tableau progression par rapport
3. **Assignation** : Formulaire DM + EP après vote OUI unanime
4. **Rédaction PV** : Éditeur pour compte-rendu + sélection rapports

### 8.9 Tâches Phase 6

```
□ [6.1] Créer les 6 entités Doctrine (Commission/)
□ [6.2] Créer migration (20250205_create_commission_tables.php)
□ [6.3] Configurer Symfony Workflow pour commission
□ [6.4] Implémenter CommissionService
□ [6.5] Implémenter VoteService avec calcul unanimité
□ [6.6] Implémenter AssignationService avec validation
□ [6.7] Créer écran commission: liste rapports à évaluer
□ [6.8] Créer écran commission: évaluation + vote
□ [6.9] Créer écran admin: gestion membres commission
□ [6.10] Créer écran admin: suivi progression votes
□ [6.11] Créer écran admin: assignation encadrants
□ [6.12] Créer écran admin: rédaction PV commission
□ [6.13] Implémenter génération PDF PV (TCPDF)
□ [6.14] Implémenter événements (VoteSubmittedEvent, etc.)
□ [6.15] Créer templates emails (progression vote, assignation)
□ [6.16] Tests: workflow vote unanime OUI/NON/mixte
```

---

## 9. PHASE 7 - MODULE JURYS & SOUTENANCES

**Durée estimée:** 10-12 jours  
**Dépendances:** Phase 6 complète

### 9.1 Objectif

Validation aptitude, composition jury, programmation soutenances, saisie notes et délibération finale.

### 9.2 Entités Doctrine

| Entité | Table | Description |
|--------|-------|-------------|
| `AptitudeSoutenance` | (nouvelle) | Validation encadreur péda |
| `Jury` | composer_jury | Le jury de 5 membres |
| `RoleJury` | roles_jury | Président, Examinateur, etc. |
| `CompositionJury` | (nouvelle) | Membres du jury |
| `Soutenance` | programmer | Date, heure, salle |
| `Salle` | salles | Lieux disponibles |
| `CritereEvaluation` | critere_evaluation | Critères de notation |
| `BaremeCritere` | correspondre | Barème par année |
| `NoteSoutenance` | evaluer | Notes par critère |
| `ResultatFinal` | (nouvelle) | Moyenne finale, mention |
| `Mention` | mentions | Très Bien, Bien, etc. |
| `DecisionJury` | decisions_jury | Admis, Ajourné |

### 9.3 Workflow Soutenance

```
[encadrants_assignes] --valider_aptitude--> [aptitude_validee]
                                                    |
                                                    ↓
[jury_compose] <--composer_jury-- [aptitude_validee]
        |
        ↓
[soutenance_programmee] --effectuer--> [soutenance_effectuee] --saisir_notes--> [notes_saisies]
                                                                                        |
                                                                                        ↓
                                                                                  [delibere]
                                                                                        |
                                                                                        ↓
                                                                              Génération PV finaux
```

### 9.4 Composition du Jury (5 membres)

| Rôle | Source | Modifiable |
|------|--------|------------|
| Président | Enseignant grade min. | ✅ Oui |
| Directeur de mémoire | Assignation commission | ❌ Non |
| Encadreur pédagogique | Assignation commission | ❌ Non |
| Maître de stage | Entreprise | ✅ Oui |
| Examinateur | Enseignant | ✅ Oui |

### 9.5 Formules de Calcul

**Annexe 2 (Standard):**
```
Note Finale = ((Moyenne M1 × 2) + (Moyenne S1 M2 × 3) + (Note Mémoire × 3)) / 8
```

**Annexe 3 (Simplifié):**
```
Note Finale = ((Moyenne M1 × 1) + (Note Mémoire × 2)) / 3
```

### 9.6 Services à Implémenter

| Service | Responsabilités |
|---------|-----------------|
| `AptitudeService` | Validation/refus aptitude par encadreur |
| `JuryService` | Composition jury, vérification contraintes |
| `PlanningService` | Programmation, détection conflits |
| `NotationService` | Saisie notes par critère |
| `MoyenneCalculationService` | Calcul moyennes (brick/math) |
| `DeliberationService` | Résultat final, mention |

### 9.7 Contrôleurs

| Contrôleur | Routes |
|------------|--------|
| `Encadreur/AptitudeController` | /encadreur/aptitude/* |
| `Admin/JuryController` | /admin/soutenance/jurys |
| `Admin/PlanningController` | /admin/soutenance/planning |
| `Admin/NotationController` | /admin/soutenance/notation |
| `Admin/DeliberationController` | /admin/soutenance/deliberation |

### 9.8 Écrans Encadreur Pédagogique

1. **Liste étudiants** : Étudiants encadrés avec statut aptitude
2. **Validation aptitude** : Bouton Apte/Non apte avec commentaire

### 9.9 Écrans Admin

1. **Composition jury** : Sélection 5 membres avec validation
2. **Planning** : Calendrier avec créneaux disponibles
3. **Programmation** : Définition date/heure/salle + détection conflits
4. **Notation** : Saisie notes par critère (grille)
5. **Délibération** : Calcul automatique + choix type PV
6. **Tableau général** : Vue récapitulative toutes soutenances

### 9.10 Tâches Phase 7

```
□ [7.1] Créer les 12 entités Doctrine (Soutenance/)
□ [7.2] Créer migration (20250206_create_soutenance_tables.php)
□ [7.3] Créer seeders (rôles jury, critères, mentions)
□ [7.4] Configurer Symfony Workflow pour soutenance
□ [7.5] Implémenter AptitudeService
□ [7.6] Implémenter JuryService avec validation composition
□ [7.7] Implémenter PlanningService avec détection conflits
□ [7.8] Implémenter NotationService avec validation barèmes
□ [7.9] Implémenter MoyenneCalculationService (brick/math)
□ [7.10] Implémenter DeliberationService
□ [7.11] Créer écran encadreur: liste étudiants
□ [7.12] Créer écran encadreur: validation aptitude
□ [7.13] Créer écran admin: composition jury
□ [7.14] Créer écran admin: planning soutenances
□ [7.15] Créer écran admin: notation par critères
□ [7.16] Créer écran admin: délibération
□ [7.17] Créer écran admin: tableau récapitulatif
□ [7.18] Implémenter événements soutenance
□ [7.19] Tests: workflow complet soutenance
```

---

## 10. PHASE 8 - MODULE GÉNÉRATION DOCUMENTS PDF

**Durée estimée:** 5-7 jours  
**Dépendances:** Phase 7 complète

### 10.1 Objectif

Génération de tous les documents officiels au format PDF.

### 10.2 Documents à Générer

| Document | Format | Déclencheur |
|----------|--------|-------------|
| Reçu de paiement | A5 | Après versement |
| Bulletin provisoire | A4 | Sur demande |
| Page de garde rapport | A4 | Soumission rapport |
| PV Commission | A4 | Création PV |
| Planning soutenances | A4 | Programmation |
| Annexe 1 (Grille notation) | A4 | Après notes |
| Annexe 2 (PV Standard) | A4 | Délibération |
| Annexe 3 (PV Simplifié) | A4 | Délibération |
| Convocation soutenance | A4 | Programmation |

### 10.3 Services à Implémenter

| Service | Responsabilités |
|---------|-----------------|
| `PdfGeneratorService` | Service de base (TCPDF) |
| `RecuGeneratorService` | Génération reçus |
| `BulletinGeneratorService` | Génération bulletins |
| `RapportPdfGeneratorService` | Conversion rapport HTML→PDF |
| `PvCommissionGeneratorService` | PV commission |
| `PlanningGeneratorService` | Tableau plannings |
| `Annexe1GeneratorService` | Grille d'évaluation |
| `Annexe2GeneratorService` | PV jury standard |
| `Annexe3GeneratorService` | PV jury simplifié |

### 10.4 Structure PDF Type

```
┌─────────────────────────────────────┐
│  [Logo UFHB]  EN-TÊTE  [Logo UFR]   │
│           Bandeau MIAGE-GI          │
├─────────────────────────────────────┤
│                                     │
│            CONTENU                  │
│                                     │
├─────────────────────────────────────┤
│          PIED DE PAGE               │
│    Page X/Y  •  Référence           │
└─────────────────────────────────────┘
```

### 10.5 Tâches Phase 8

```
□ [8.1] Créer PdfGeneratorService de base
□ [8.2] Configurer TCPDF (fonts, marges, en-têtes)
□ [8.3] Intégrer logos (après réception)
□ [8.4] Implémenter RecuGeneratorService
□ [8.5] Implémenter BulletinGeneratorService
□ [8.6] Implémenter PvCommissionGeneratorService
□ [8.7] Implémenter PlanningGeneratorService
□ [8.8] Implémenter Annexe1GeneratorService
□ [8.9] Implémenter Annexe2GeneratorService
□ [8.10] Implémenter Annexe3GeneratorService
□ [8.11] Créer templates PDF (templates/pdf/*.php)
□ [8.12] Implémenter numérotation référence (par type, par année)
□ [8.13] Implémenter stockage documents (storage/documents/)
□ [8.14] Créer écran téléchargement documents
□ [8.15] Tests: génération de chaque type de document
```

---

## 11. PHASE 9 - MODULE PARAMÉTRAGE SYSTÈME

**Durée estimée:** 5-7 jours  
**Dépendances:** Phases 1-8 complètes

### 11.1 Objectif

Configuration globale de l'application sans modification de code.

### 11.2 Catégories de Paramètres

| Catégorie | Exemples |
|-----------|----------|
| **Application** | Nom, logo, email expéditeur, mode maintenance |
| **Email** | SMTP host/port/user/pass, templates |
| **Sécurité** | 2FA obligatoire, durée session, rate limiting |
| **Académique** | Années, niveaux, semestres, filières |
| **Pédagogique** | UE, ECUE, crédits, barèmes |
| **Soutenances** | Rôles jury, critères, mentions |
| **UI** | Menus, sous-menus, ordre, icônes |
| **Messages** | Libellés, erreurs, emails |

### 11.3 Entités Système

| Entité | Table |
|--------|-------|
| `AppSetting` | app_settings |
| `Message` | messages |
| `EmailTemplate` | (nouvelle) |
| `Piste` | pister |

### 11.4 Services à Implémenter

| Service | Responsabilités |
|---------|-----------------|
| `SettingsService` | CRUD paramètres, cache |
| `EncryptionService` | Chiffrement données sensibles (defuse/php-encryption) |
| `AuditService` | Logging actions utilisateur |
| `MenuService` | Construction dynamique menus |

### 11.5 Contrôleurs Admin

| Contrôleur | Routes |
|------------|--------|
| `ApplicationController` | /admin/parametrage/application |
| `AnneeAcademiqueController` | /admin/parametrage/annees |
| `NiveauEtudeController` | /admin/parametrage/niveaux |
| `UeController` | /admin/parametrage/ue |
| `CritereEvaluationController` | /admin/parametrage/criteres |
| `MenuController` | /admin/parametrage/menus |
| `MessageController` | /admin/parametrage/messages |
| `AuditController` | /admin/maintenance/audit |

### 11.6 Tâches Phase 9

```
□ [9.1] Créer les entités System (AppSetting, Message, etc.)
□ [9.2] Créer migration (20250207_create_settings_tables.php)
□ [9.3] Implémenter SettingsService avec cache
□ [9.4] Implémenter EncryptionService (chiffrement SMTP password)
□ [9.5] Implémenter AuditService (logging complet)
□ [9.6] Implémenter MenuService (construction dynamique)
□ [9.7] Créer écran paramétrage application
□ [9.8] Créer CRUD années académiques
□ [9.9] Créer CRUD niveaux d'étude
□ [9.10] Créer CRUD UE/ECUE
□ [9.11] Créer CRUD critères évaluation + barèmes
□ [9.12] Créer CRUD salles
□ [9.13] Créer gestion menus (catégories, fonctionnalités)
□ [9.14] Créer gestion messages système
□ [9.15] Créer écran visualisation audit trail
□ [9.16] Créer écran maintenance (cache, mode maintenance)
□ [9.17] Tests: modification paramètres + impact
```

---

## 12. PHASE 10 - TESTS & DÉPLOIEMENT

**Durée estimée:** 5-7 jours  
**Dépendances:** Toutes les phases complètes

### 12.1 Types de Tests

| Type | Outil | Couverture |
|------|-------|------------|
| Unitaires | PHPUnit | Services, Validators, Helpers |
| Intégration | PHPUnit | Repositories, Workflows |
| Fonctionnels | PHPUnit | Contrôleurs, API |

### 12.2 Scénarios de Test Prioritaires

1. **Parcours complet étudiant** : Inscription → Candidature → Rapport → Soutenance
2. **Workflow candidature** : Soumission, validation, rejet, resoumission
3. **Workflow rapport** : Rédaction, soumission, retour, approbation
4. **Vote commission** : Unanime OUI, unanime NON, mixte
5. **Soutenance** : Jury, programmation, notes, délibération

### 12.3 Checklist Pré-Déploiement

```
□ [10.1] Tous les tests passent
□ [10.2] Pas d'erreurs LSP
□ [10.3] Configuration .env.production prête
□ [10.4] Logos intégrés
□ [10.5] SMTP configuré et testé
□ [10.6] BDD de production créée
□ [10.7] Migrations appliquées
□ [10.8] Seeders exécutés (données initiales)
□ [10.9] Super admin créé
□ [10.10] Mode debug désactivé
□ [10.11] Cache warmup effectué
```

### 12.4 Déploiement (Hébergement Mutualisé)

1. **Upload FTP** : Tous les fichiers (vendor inclus)
2. **Configuration** : Créer .env depuis .env.example
3. **BDD** : Import schema.sql via phpMyAdmin
4. **Permissions** : storage/ en 755
5. **Test** : Accès /login, création session

### 12.5 Tâches Phase 10

```
□ [10.1] Écrire tests unitaires services critiques
□ [10.2] Écrire tests intégration workflows
□ [10.3] Écrire tests fonctionnels parcours complet
□ [10.4] Exécuter tous les tests
□ [10.5] Corriger les erreurs trouvées
□ [10.6] Préparer fichiers déploiement
□ [10.7] Configurer environnement production
□ [10.8] Déployer sur serveur
□ [10.9] Exécuter migrations production
□ [10.10] Tester en production
□ [10.11] Former les administrateurs
```

---

## 13. ANNEXES

### 13.1 Arborescence Complète

```
miage-platform/
├── .env
├── .env.example
├── .gitignore
├── .htaccess
├── composer.json
├── phinx.php
├── README.md
│
├── config/
│   ├── app.php
│   ├── container.php
│   ├── database.php
│   ├── routes.php
│   ├── middlewares.php
│   ├── services.php
│   └── workflows/
│       ├── candidature.php
│       ├── rapport.php
│       ├── commission.php
│       └── soutenance.php
│
├── database/
│   ├── migrations/
│   ├── seeds/
│   └── schema.sql
│
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
│       ├── css/
│       ├── js/
│       ├── images/
│       └── vendors/
│
├── src/
│   ├── App.php
│   ├── Controller/
│   ├── Entity/
│   ├── Repository/
│   ├── Service/
│   ├── Middleware/
│   ├── Validator/
│   ├── Event/
│   ├── EventListener/
│   ├── Helper/
│   └── Exception/
│
├── storage/
│   ├── cache/
│   ├── documents/
│   ├── logs/
│   ├── uploads/
│   └── sessions/
│
├── templates/
│   ├── layout/
│   ├── components/
│   ├── auth/
│   ├── admin/
│   ├── etudiant/
│   ├── encadreur/
│   ├── commission/
│   ├── pdf/
│   ├── email/
│   └── error/
│
├── tests/
│   ├── Unit/
│   ├── Integration/
│   └── Functional/
│
└── vendor/
```

### 13.2 Dépendances Composer

```json
{
  "require": {
    "php": "^8.4",
    "nikic/fast-route": "^1.3",
    "middlewares/fast-route": "^2.0",
    "oscarotero/middleland": "^1.0",
    "psr/http-server-middleware": "^1.0",
    "symfony/http-foundation": "^7.0",
    "nyholm/psr7": "^1.8",
    "symfony/http-client": "^7.0",
    "league/uri": "^7.0",
    "symfony/workflow": "^7.0",
    "symfony/expression-language": "^7.0",
    "symfony/dependency-injection": "^7.0",
    "symfony/event-dispatcher": "^7.0",
    "symfony/options-resolver": "^7.0",
    "opis/closure": "^4.0",
    "symfony/security-core": "^7.0",
    "symfony/security-http": "^7.0",
    "symfony/password-hasher": "^7.0",
    "symfony/security-csrf": "^7.0",
    "lcobucci/jwt": "^5.0",
    "spomky-labs/otphp": "^11.0",
    "symfony/rate-limiter": "^7.0",
    "defuse/php-encryption": "^2.4",
    "ezyang/htmlpurifier": "^4.17",
    "doctrine/orm": "^3.0",
    "doctrine/dbal": "^4.0",
    "robmorgan/phinx": "^0.14",
    "psr/simple-cache": "^3.0",
    "laravel/scout": "^10.0",
    "respect/validation": "^2.3",
    "egulias/email-validator": "^4.0",
    "nesbot/carbon": "^3.0",
    "brick/math": "^0.12",
    "symfony/string": "^7.0",
    "phpoffice/phpword": "^1.2",
    "tecnickcom/tcpdf": "^6.6",
    "phpmailer/phpmailer": "^6.9",
    "league/csv": "^9.0",
    "monolog/monolog": "^3.5",
    "filp/whoops": "^2.15",
    "jenssegers/agent": "^2.6",
    "pagerfanta/pagerfanta": "^4.0",
    "vlucas/phpdotenv": "^5.6"
  }
}
```

### 13.3 Estimation Totale

| Phase | Durée | Cumul |
|-------|-------|-------|
| Phase 0 - Prérequis | 2-3 jours | 3 jours |
| Phase 1 - Fondations | 5-7 jours | 10 jours |
| Phase 2 - Utilisateurs/RBAC | 7-10 jours | 20 jours |
| Phase 3 - Étudiants | 8-10 jours | 30 jours |
| Phase 4 - Candidatures | 5-7 jours | 37 jours |
| Phase 5 - Rapports | 10-12 jours | 49 jours |
| Phase 6 - Commission | 8-10 jours | 59 jours |
| Phase 7 - Soutenances | 10-12 jours | 71 jours |
| Phase 8 - Documents PDF | 5-7 jours | 78 jours |
| Phase 9 - Paramétrage | 5-7 jours | 85 jours |
| Phase 10 - Tests/Déploiement | 5-7 jours | 92 jours |

**Estimation totale: 75-92 jours ouvrés (3.5-4.5 mois)**

### 13.4 Points Bloquants à Résoudre

| # | Élément | Criticité | Action |
|---|---------|-----------|--------|
| 1 | Logo UFHB | BLOQUANT | Fournir fichier |
| 2 | Logo UFR MI | BLOQUANT | Fournir fichier |
| 3 | Credentials SMTP | BLOQUANT | Configurer |
| 4 | Accès BDD | BLOQUANT | Fournir |
| 5 | Domaine production | BLOQUANT | Définir |
| 6 | Montants scolarité | IMPORTANT | Confirmer |
| 7 | Liste UE/ECUE M2 | IMPORTANT | Fournir |
| 8 | Grade min. président | IMPORTANT | Confirmer |
| 9 | Barèmes notation | IMPORTANT | Confirmer |
| 10 | Éditeur WYSIWYG | MOYEN | Choisir |


---

## FIN DU DOCUMENT

**Ce plan est COMPLET et permet de développer l'application de A à Z sans interruption, à condition de résoudre les bloquants listés.**

*Document généré automatiquement - Plateforme MIAGE-GI*
