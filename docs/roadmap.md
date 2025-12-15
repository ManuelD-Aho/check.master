# Plan de Route CheckMaster - Guide Complet d'Implémentation

**Version** : 1.0.0  
**Date** : 2024-12-14  
**Objectif** : Réaliser l'application CheckMaster de bout en bout sans hallucination

---

## 📋 Table des Matières

1. [Vue d'Ensemble du Projet](#1-vue-densemble-du-projet)
2. [Sources de Vérité (Anti-Hallucination)](#2-sources-de-vérité-anti-hallucination)
3. [Phases d'Implémentation](#3-phases-dimplémentation)
4. [Workflow SpecKit Détaillé](#4-workflow-speckit-détaillé)
5. [Ordre d'Implémentation des Modules](#5-ordre-dimplémentation-des-modules)
6. [Checklist Globale](#6-checklist-globale)
7. [Règles Non-Négociables](#7-règles-non-négociables)

---

## 1. Vue d'Ensemble du Projet

### 1.1 Qu'est-ce que CheckMaster ?

CheckMaster est un système de gestion académique pour la supervision des mémoires de Master comprenant :
- **14 états workflow** : INSCRIT → DIPLOME_DELIVRE
- **13 groupes utilisateurs** : Administrateur, Scolarité, Commission, Étudiant, etc.
- **71 templates email** : Notifications automatisées
- **13 types PDF** : Reçus, PV, Bulletins, Attestations
- **67 tables** : Schéma base de données complet
- **~170 paramètres configuration** : Tout DB-driven

### 1.2 Architecture Technique Imposée

```
PHP 8.0+ (strict types) + MySQL 8.0+ + Architecture MVC++ Native
                              ↓
Requête → Routeur (Hashids) → Middleware → Contrôleur → Service → Modèle → DB
```

**Dépendances Autorisées (~12MB total)** :
- hashids/hashids, symfony/validator, symfony/http-foundation, symfony/cache
- mpdf/mpdf, tecnickcom/tcpdf, phpoffice/phpspreadsheet
- phpmailer/phpmailer, monolog/monolog

---

## 2. Sources de Vérité (Anti-Hallucination)

### ⚠️ RÈGLE FONDAMENTALE
> **Ne JAMAIS inventer. TOUJOURS consulter les fichiers sources avant d'implémenter.**

### 2.1 Fichiers Constitution & Principes

| Fichier | Contenu | Quand Consulter |
|---------|---------|-----------------|
| `.specify/memory/constitution.md` | Principes non-négociables | AVANT chaque décision architecture |
| `.specify/memory/Synthèse.txt` | Analyse complète (11K lignes) | Pour comprendre métier & services |
| `docs/workflows.md` | 14 états, gates, commission | Pour tout workflow |
| `docs/workbench.md` | Patterns code & commandes | Pour templates code |

### 2.2 PRD (Product Requirements Documents)

| PRD | Périmètre | Dépendances |
|-----|-----------|-------------|
| `docs/prd/00_master_prd.md` | Vision globale, acteurs, workflow | Aucune |
| `docs/prd/01_authentication_users.md` | Auth, sessions, permissions | 00_master |
| `docs/prd/02_academic_entities.md` | Étudiants, enseignants, UE/ECUE | 00_master, 01_auth |
| `docs/prd/03_workflow_commission.md` | États, transitions, votes | 01_auth, 02_academic |
| `docs/prd/04_thesis_defense.md` | Candidatures, jury, soutenance | 03_workflow |
| `docs/prd/05_communication.md` | Notifications, messagerie | 01_auth |
| `docs/prd/06_documents_archives.md` | PDF, archivage, signatures | Tous |
| `docs/prd/07_financial.md` | Paiements, pénalités | 02_academic |
| `docs/prd/08_administration.md` | Config, audit, réclamations | Tous |

### 2.3 Base de Données

| Fichier | Contenu |
|---------|---------|
| `database/migrations/001_create_complete_database.sql` | 67 tables, relations, index |

### 2.4 Agents SpecKit (Workflow Automatisé)

| Agent | Commande | Fonction |
|-------|----------|----------|
| `speckit.specify` | `/speckit.specify` | Créer spec depuis description |
| `speckit.clarify` | `/speckit.clarify` | Clarifier ambiguïtés spec |
| `speckit.plan` | `/speckit.plan` | Générer plan technique |
| `speckit.tasks` | `/speckit.tasks` | Décomposer en tâches |
| `speckit.checklist` | `/speckit.checklist` | Valider qualité exigences |
| `speckit.analyze` | `/speckit.analyze` | Vérifier cohérence |
| `speckit.implement` | `/speckit.implement` | Exécuter implémentation |
| `speckit.constitution` | `/speckit.constitution` | Mettre à jour constitution |
| `speckit.taskstoissues` | `/speckit.taskstoissues` | Créer issues GitHub |

---

## 3. Phases d'Implémentation

### Phase 0 : Préparation Infrastructure (Semaine 1)

```
┌─────────────────────────────────────────────────────────────────┐
│  PHASE 0 : FONDATIONS                                           │
├─────────────────────────────────────────────────────────────────┤
│  □ 0.1 Vérifier environnement (PHP 8.0+, MySQL 8.0+, Composer)  │
│  □ 0.2 Exécuter migration 001_create_complete_database.sql     │
│  □ 0.3 Configurer autoload PSR-4                                │
│  □ 0.4 Installer dépendances Composer                           │
│  □ 0.5 Configurer .gitignore                                    │
│  □ 0.6 Setup PHPStan, PHP-CS-Fixer                              │
│  □ 0.7 Créer structure dossiers MVC++                           │
└─────────────────────────────────────────────────────────────────┘
```

**Fichiers à créer** :
```
app/
├── Controllers/
├── Services/
│   └── Core/
│       ├── ServiceAudit.php
│       ├── ServicePermission.php
│       ├── ServiceWorkflow.php
│       ├── ServiceNotification.php
│       ├── ServiceParametres.php
│       └── ServicePdf.php
├── Models/
├── Validators/
├── Middleware/
└── Orm/
    └── Model.php
```

### Phase 1 : Module Authentification (Semaines 2-3)

**Source** : `docs/prd/01_authentication_users.md`

```
┌─────────────────────────────────────────────────────────────────┐
│  PHASE 1 : AUTHENTIFICATION & UTILISATEURS                      │
├─────────────────────────────────────────────────────────────────┤
│  □ 1.1 Modèle Utilisateur + Hashids                             │
│  □ 1.2 ServiceAuthentification (Argon2id)                       │
│  □ 1.3 Gestion sessions (sessions_actives)                      │
│  □ 1.4 Protection brute-force (codes_temporaires)               │
│  □ 1.5 Middleware AuthMiddleware                                │
│  □ 1.6 ServicePermission (groupe → traitement → action)         │
│  □ 1.7 Rôles temporaires (roles_temporaires)                    │
│  □ 1.8 Vue connexion + validation CSRF                          │
│  □ 1.9 Tests unitaires authentification                         │
└─────────────────────────────────────────────────────────────────┘
```

**Vérifications obligatoires** :
- [ ] Mot de passe Argon2id
- [ ] Sessions avec expiration
- [ ] Verrouillage après 5 échecs
- [ ] Audit login/logout dans pister

### Phase 2 : Entités Académiques (Semaines 4-5)

**Source** : `docs/prd/02_academic_entities.md`

```
┌─────────────────────────────────────────────────────────────────┐
│  PHASE 2 : ENTITÉS ACADÉMIQUES                                  │
├─────────────────────────────────────────────────────────────────┤
│  □ 2.1 Modèles : Etudiant, Enseignant, PersonnelAdmin          │
│  □ 2.2 Modèles structure : AnneeAcademique, Niveau, UE, ECUE    │
│  □ 2.3 Services CRUD avec audit                                 │
│  □ 2.4 Contrôleurs avec Hashids                                 │
│  □ 2.5 Validateurs Symfony                                      │
│  □ 2.6 Vues gestion (index, create, edit)                       │
│  □ 2.7 Import Excel (PhpSpreadsheet)                            │
│  □ 2.8 Tests unitaires entités                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Phase 3 : Workflow & Commission (Semaines 6-8)

**Source** : `docs/prd/03_workflow_commission.md`

```
┌─────────────────────────────────────────────────────────────────┐
│  PHASE 3 : WORKFLOW & COMMISSION                                 │
├─────────────────────────────────────────────────────────────────┤
│  □ 3.1 ServiceWorkflow::effectuerTransition()                   │
│  □ 3.2 Tables workflow_etats, workflow_transitions              │
│  □ 3.3 Historique avec snapshots (workflow_historique)          │
│  □ 3.4 Gates critiques (bloquer si conditions non remplies)     │
│  □ 3.5 Sessions commission (sessions_commission)                │
│  □ 3.6 Système votes 3 tours + unanimité                        │
│  □ 3.7 Escalade au Doyen après tour 3                           │
│  □ 3.8 Alertes workflow (workflow_alertes)                      │
│  □ 3.9 Tests workflow complet                                   │
└─────────────────────────────────────────────────────────────────┘
```

**14 États à implémenter** :
1. INSCRIT
2. CANDIDATURE_SOUMISE
3. VERIFICATION_SCOLARITE
4. FILTRE_COMMUNICATION
5. EN_ATTENTE_COMMISSION
6. EN_EVALUATION_COMMISSION
7. RAPPORT_VALIDE
8. ATTENTE_AVIS_ENCADREUR
9. PRET_POUR_JURY
10. JURY_EN_CONSTITUTION
11. SOUTENANCE_PLANIFIEE
12. SOUTENANCE_EN_COURS
13. SOUTENANCE_TERMINEE
14. DIPLOME_DELIVRE

### Phase 4 : Thèse & Soutenance (Semaines 9-11)

**Source** : `docs/prd/04_thesis_defense.md`

```
┌─────────────────────────────────────────────────────────────────┐
│  PHASE 4 : THÈSE & SOUTENANCE                                    │
├─────────────────────────────────────────────────────────────────┤
│  □ 4.1 Dossier étudiant (dossiers_etudiants)                    │
│  □ 4.2 Candidatures avec validation                              │
│  □ 4.3 Rédaction rapport (versioning)                           │
│  □ 4.4 Annotations rapport (annotations_rapport)                 │
│  □ 4.5 Constitution jury (5 membres minimum)                     │
│  □ 4.6 Planification soutenance (détection conflits)            │
│  □ 4.7 Saisie notes (notes_soutenance)                          │
│  □ 4.8 Calcul mention automatique                               │
│  □ 4.9 Délibération jury                                         │
│  □ 4.10 Tests scénarios soutenance                              │
└─────────────────────────────────────────────────────────────────┘
```

### Phase 5 : Communication (Semaines 12-13)

**Source** : `docs/prd/05_communication.md`

```
┌─────────────────────────────────────────────────────────────────┐
│  PHASE 5 : COMMUNICATION                                         │
├─────────────────────────────────────────────────────────────────┤
│  □ 5.1 ServiceNotification::envoyer()                           │
│  □ 5.2 71 templates email (notification_templates)              │
│  □ 5.3 File notifications (notifications_queue)                 │
│  □ 5.4 Historique (notifications_historique)                    │
│  □ 5.5 Gestion bounces (email_bounces)                          │
│  □ 5.6 Messagerie interne (messages_internes)                   │
│  □ 5.7 Calendrier académique                                    │
│  □ 5.8 Tests envoi notifications                                │
└─────────────────────────────────────────────────────────────────┘
```

### Phase 6 : Documents & Archives (Semaines 14-15)

**Source** : `docs/prd/06_documents_archives.md`

```
┌─────────────────────────────────────────────────────────────────┐
│  PHASE 6 : DOCUMENTS & ARCHIVES                                  │
├─────────────────────────────────────────────────────────────────┤
│  □ 6.1 ServicePdf avec TCPDF/mPDF                               │
│  □ 6.2 13 types documents (reçus, PV, bulletins...)            │
│  □ 6.3 Templates PDF (ressources/templates/pdf/)                │
│  □ 6.4 Calcul hash SHA256                                        │
│  □ 6.5 Table archives avec intégrité                            │
│  □ 6.6 Vérification périodique intégrité                        │
│  □ 6.7 Historisation entités (historique_entites)               │
│  □ 6.8 Système brouillons                                        │
│  □ 6.9 Signatures électroniques (optionnel)                     │
│  □ 6.10 Tests génération PDF                                    │
└─────────────────────────────────────────────────────────────────┘
```

**13 Types Documents** :
1. Reçu paiement
2. Reçu pénalité
3. Bulletin notes
4. PV commission
5. PV soutenance
6. Convocation
7. Attestation diplôme
8. Rapport évaluation
9. Bulletin provisoire
10. Certificat scolarité
11. Lettre jury
12. Attestation stage
13. Bordereau transmission

### Phase 7 : Module Financier (Semaines 16-17)

**Source** : `docs/prd/07_financial.md`

```
┌─────────────────────────────────────────────────────────────────┐
│  PHASE 7 : FINANCIER                                             │
├─────────────────────────────────────────────────────────────────┤
│  □ 7.1 Table paiements                                          │
│  □ 7.2 Pénalités (calcul automatique)                           │
│  □ 7.3 Exonérations (totales/partielles)                        │
│  □ 7.4 Gate financière workflow                                 │
│  □ 7.5 Génération reçus TCPDF                                   │
│  □ 7.6 Tableau de bord financier étudiant                       │
│  □ 7.7 Configuration montants (DB-driven)                       │
│  □ 7.8 Tests calculs financiers                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Phase 8 : Administration (Semaines 18-19)

**Source** : `docs/prd/08_administration.md`

```
┌─────────────────────────────────────────────────────────────────┐
│  PHASE 8 : ADMINISTRATION                                        │
├─────────────────────────────────────────────────────────────────┤
│  □ 8.1 Configuration système (configuration_systeme)           │
│  □ 8.2 ~170 paramètres organisés par préfixe                    │
│  □ 8.3 ServiceParametres::get/set                               │
│  □ 8.4 27 fonctionnalités désactivables                         │
│  □ 8.5 ServiceAudit complet (table pister)                      │
│  □ 8.6 Double logging (Monolog + DB)                            │
│  □ 8.7 System réclamations                                       │
│  □ 8.8 Import/Export données                                     │
│  □ 8.9 Maintenance planifiée                                     │
│  □ 8.10 Tests administration                                    │
└─────────────────────────────────────────────────────────────────┘
```

### Phase 9 : Intégration & Tests (Semaines 20-21)

```
┌─────────────────────────────────────────────────────────────────┐
│  PHASE 9 : INTÉGRATION & TESTS                                   │
├─────────────────────────────────────────────────────────────────┤
│  □ 9.1 Tests intégration end-to-end                             │
│  □ 9.2 Tests workflow complet (INSCRIT → DIPLOME)               │
│  □ 9.3 Tests permissions tous groupes                           │
│  □ 9.4 Tests génération tous PDFs                               │
│  □ 9.5 Tests notifications email                                │
│  □ 9.6 PHPStan niveau 6+ sur tout le code                       │
│  □ 9.7 PHP-CS-Fixer PSR-12                                      │
│  □ 9.8 Couverture tests >80%                                    │
│  □ 9.9 Documentation API                                         │
│  □ 9.10 Guide déploiement                                       │
└─────────────────────────────────────────────────────────────────┘
```

---

## 4. Workflow SpecKit Détaillé

### 4.1 Pour Chaque Fonctionnalité

```
┌──────────────────────────────────────────────────────────────────────────┐
│                     WORKFLOW SPECKIT COMPLET                              │
└──────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌──────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 1 : SPÉCIFICATION                                                  │
│  Commande : /speckit.specify "Description fonctionnalité"                │
│  Résultat : specs/<numero>-<nom>/spec.md                                 │
│  Vérifier : Pas de détails techniques, focus utilisateur                 │
└──────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌──────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 2 : CLARIFICATION                                                  │
│  Commande : /speckit.clarify                                             │
│  Résultat : Ambiguïtés résolues dans spec.md                            │
│  Vérifier : Max 5 questions, réponses intégrées                         │
└──────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌──────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 3 : PLANIFICATION                                                  │
│  Commande : /speckit.plan                                                │
│  Résultat : plan.md, data-model.md, contracts/, research.md             │
│  Vérifier : Constitution respectée, stack technique correct             │
└──────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌──────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 4 : GÉNÉRATION TÂCHES                                              │
│  Commande : /speckit.tasks                                               │
│  Résultat : tasks.md avec tâches ordonnées                              │
│  Vérifier : Format checkbox, IDs séquentiels, chemins fichiers          │
└──────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌──────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 5 : CHECKLIST QUALITÉ                                              │
│  Commande : /speckit.checklist                                           │
│  Résultat : checklists/<domaine>.md                                     │
│  Vérifier : Tests exigences, pas implémentation                         │
└──────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌──────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 6 : ANALYSE COHÉRENCE                                              │
│  Commande : /speckit.analyze                                             │
│  Résultat : Rapport anomalies (ne modifie rien)                         │
│  Vérifier : 0 issues CRITIQUES avant implémentation                     │
└──────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌──────────────────────────────────────────────────────────────────────────┐
│  ÉTAPE 7 : IMPLÉMENTATION                                                 │
│  Commande : /speckit.implement                                           │
│  Résultat : Code produit selon tasks.md                                 │
│  Vérifier : Chaque tâche cochée [X], tests passent                      │
└──────────────────────────────────────────────────────────────────────────┘
```

### 4.2 Exemple Concret

```bash
# 1. Créer spec pour gestion candidatures
/speckit.specify "Permettre aux étudiants de soumettre leur candidature 
avec informations stage et upload rapport préliminaire"

# 2. Clarifier points ambigus
/speckit.clarify

# 3. Générer plan technique
/speckit.plan

# 4. Décomposer en tâches
/speckit.tasks

# 5. Créer checklist qualité
/speckit.checklist

# 6. Vérifier cohérence
/speckit.analyze

# 7. Implémenter
/speckit.implement
```

---

## 5. Ordre d'Implémentation des Modules

### 5.1 Graphe des Dépendances

```
                    ┌─────────────────┐
                    │  00_Master PRD  │
                    └────────┬────────┘
                             │
              ┌──────────────┼──────────────┐
              ▼              ▼              ▼
    ┌─────────────────┐ ┌─────────────┐ ┌───────────────┐
    │ 01_Auth_Users   │ │ 05_Comm.    │ │ 08_Admin      │
    └────────┬────────┘ └──────┬──────┘ └───────────────┘
             │                 │
             ▼                 │
    ┌─────────────────┐        │
    │ 02_Academic     │        │
    └────────┬────────┘        │
             │                 │
             ▼                 │
    ┌─────────────────┐        │
    │ 03_Workflow     │◄───────┘
    └────────┬────────┘
             │
    ┌────────┴────────┐
    ▼                 ▼
┌─────────────────┐ ┌─────────────────┐
│ 04_Thesis       │ │ 07_Financial    │
└────────┬────────┘ └────────┬────────┘
         │                   │
         └─────────┬─────────┘
                   ▼
         ┌─────────────────┐
         │ 06_Documents    │
         └─────────────────┘
```

### 5.2 Ordre Séquentiel Recommandé

| # | Module | PRD | Bloquant Pour |
|---|--------|-----|---------------|
| 1 | Infrastructure | - | Tous |
| 2 | Authentification | 01 | 02, 03, 04, 05, 06, 07, 08 |
| 3 | Entités Académiques | 02 | 03, 04, 07 |
| 4 | Workflow Base | 03 | 04 |
| 5 | Communication | 05 | 04, 06 |
| 6 | Financier | 07 | 04 |
| 7 | Thèse & Soutenance | 04 | 06 |
| 8 | Documents & Archives | 06 | - |
| 9 | Administration | 08 | - |

---

## 6. Checklist Globale

### 6.1 Avant Chaque Implémentation

```markdown
## Pré-Implémentation Checklist

### Sources Consultées
- [ ] PRD du module lu intégralement
- [ ] Constitution checkée
- [ ] Tables DB concernées identifiées dans 001_create_complete_database.sql
- [ ] Workflow états impactés identifiés (si applicable)
- [ ] Groupes utilisateurs avec accès listés
- [ ] Notifications à envoyer identifiées

### Spécification
- [ ] /speckit.specify exécuté
- [ ] /speckit.clarify exécuté (si ambiguïtés)
- [ ] spec.md sans marqueurs [NÉCESSITE CLARIFICATION]

### Planification  
- [ ] /speckit.plan exécuté
- [ ] data-model.md généré
- [ ] Vérification Constitution passée

### Tâches
- [ ] /speckit.tasks exécuté
- [ ] Toutes tâches ont format checklist correct
- [ ] /speckit.analyze sans issues CRITIQUES
```

### 6.2 Pendant l'Implémentation

```markdown
## Implémentation Checklist

### Code Standards
- [ ] declare(strict_types=1); en début de fichier
- [ ] 100% type hints (paramètres, retours, propriétés)
- [ ] Controllers ≤50 lignes
- [ ] Logique métier dans Services
- [ ] Wrapper Request (jamais $_POST/$_GET)
- [ ] Échappement e() dans vues

### Sécurité
- [ ] Hashids pour IDs en URLs
- [ ] Requêtes SQL préparées
- [ ] Tokens CSRF sur formulaires
- [ ] Mots de passe Argon2id

### Audit & Workflow
- [ ] ServiceAudit::log() pour opérations écriture
- [ ] ServicePermission::verifier() avant actions restreintes
- [ ] ServiceWorkflow::effectuerTransition() pour changements état
- [ ] ServiceNotification::envoyer() pour notifications

### Documents
- [ ] SHA256 calculé pour PDFs générés
- [ ] Archivage dans table archives
- [ ] Template dans ressources/templates/pdf/
```

### 6.3 Après Implémentation

```markdown
## Post-Implémentation Checklist

### Qualité
- [ ] PHPStan niveau 6+ passe
- [ ] PHP-CS-Fixer PSR-12 passe
- [ ] Tests unitaires créés
- [ ] Tests passent

### Documentation
- [ ] PHPDoc sur méthodes publiques
- [ ] Commentaires sur logique complexe
- [ ] Tâches cochées [X] dans tasks.md

### Validation
- [ ] Fonctionnalité testée manuellement
- [ ] Permissions vérifiées pour tous groupes concernés
- [ ] Notifications envoyées correctement
- [ ] PDFs générés correctement (si applicable)
```

---

## 7. Règles Non-Négociables

### 7.1 Architecture

| Règle | Vérification |
|-------|--------------|
| **DB-Driven** | JAMAIS de config en PHP, TOUJOURS table configuration_systeme |
| **Permissions DB** | JAMAIS hardcodées, TOUJOURS tables rattacher + traitement + action |
| **Menus DB** | Construits depuis traitement + rattacher |
| **Workflow DB** | États dans workflow_etats, transitions dans workflow_transitions |

### 7.2 Sécurité

| Règle | Vérification |
|-------|--------------|
| **Hashids** | TOUS les IDs entités en URLs |
| **Argon2id** | TOUS les mots de passe |
| **Requêtes préparées** | JAMAIS de concaténation SQL |
| **Échappement** | TOUJOURS `e()` dans vues |
| **CSRF** | TOUS les formulaires |
| **Audit** | TOUTES les opérations d'écriture journalisées |

### 7.3 Code

| Règle | Vérification |
|-------|--------------|
| **Contrôleurs** | Max 50 lignes, validation + service + réponse |
| **Services** | Logique métier, stateless, DI constructeur |
| **Modèles** | Étendent App\Orm\Model, pas de logique |
| **Transactions** | Pour toute opération multi-tables |
| **Types** | 100% typés (paramètres, retours, propriétés) |

### 7.4 Nomenclature

| Élément | Convention |
|---------|------------|
| Tables | `snake_case` |
| Clé primaire | `id_nomtable` |
| Classes | `PascalCase` |
| Méthodes | `camelCase` |
| Variables | `$camelCase` |
| Constantes | `UPPER_SNAKE_CASE` |
| Migrations | `0XX_description.sql` |

---

## 📌 Récapitulatif Fichiers Clés

```
check.master/
├── .specify/memory/
│   ├── constitution.md        ← PRINCIPES NON-NÉGOCIABLES
│   └── Synthèse.txt           ← ANALYSE COMPLÈTE (11K lignes)
│
├── .github/agents/
│   ├── speckit.specify.agent.md
│   ├── speckit.clarify.agent.md
│   ├── speckit.plan.agent.md
│   ├── speckit.tasks.agent.md
│   ├── speckit.checklist.agent.md
│   ├── speckit.analyze.agent.md
│   ├── speckit.implement.agent.md
│   ├── speckit.constitution.agent.md
│   └── speckit.taskstoissues.agent.md
│
├── database/migrations/
│   └── 001_create_complete_database.sql  ← 67 TABLES
│
├── docs/
│   ├── workflows.md           ← 14 ÉTATS WORKFLOW
│   ├── workbench.md           ← PATTERNS CODE
│   └── prd/
│       ├── 00_master_prd.md   ← VISION GLOBALE
│       ├── 01_authentication_users.md
│       ├── 02_academic_entities.md
│       ├── 03_workflow_commission.md
│       ├── 04_thesis_defense.md
│       ├── 05_communication.md
│       ├── 06_documents_archives.md
│       ├── 07_financial.md
│       └── 08_administration.md
│
└── docs/roadmap.md            ← CE FICHIER
```

---

## ⚡ Commandes Rapides

```bash
# Qualité code
composer run fix       # PHP-CS-Fixer PSR-12
composer run stan      # PHPStan niveau 6+
composer run test      # PHPUnit

# SpecKit
/speckit.specify "description"
/speckit.clarify
/speckit.plan
/speckit.tasks
/speckit.checklist
/speckit.analyze
/speckit.implement

# Git
git checkout -b feature/XX-nom-fonctionnalité
git commit -m "feat(module): description"
```

---

**FIN DU PLAN DE ROUTE**

> 💡 **Rappel** : Ce document est votre GPS. En cas de doute, consultez les fichiers sources référencés plutôt que d'inventer.
