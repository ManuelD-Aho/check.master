# CheckMaster UFHB 2.0 - PRD Master

**Version**: 2.0.0  
**Date**: 2025-12-24  
**Statut**: Approuvé  
**Auteur**: CheckMaster Team

---

## Table des Matières

1. [Vue d'Ensemble](#vue-densemble)
2. [Acteurs du Système](#acteurs-du-système)
3. [Architecture Technique](#architecture-technique)
4. [Workflow Principal](#workflow-principal)
5. [Modules du Système](#modules-du-système)
6. [Fonctionnalités Transversales](#fonctionnalités-transversales)
7. [Base de Données](#base-de-données)
8. [Sécurité](#sécurité)
9. [Critères de Succès Globaux](#critères-de-succès-globaux)
10. [Documents Générés](#documents-générés-13-types)
11. [Hors Périmètre](#hors-périmètre-v1)
12. [Références](#références)

---

## Vue d'Ensemble

CheckMaster est un système de gestion académique complet pour l'UFR Mathématiques et Informatique de l'Université Félix Houphouët-Boigny (UFHB). Il gère l'intégralité du cycle de vie étudiant, de l'inscription jusqu'à la délivrance du diplôme, en passant par la validation des rapports de stage et l'organisation des soutenances.

### Caractéristiques Principales

Le système est conçu pour être **100% Database-Driven** (autarcie totale), permettant toute configuration via l'interface sans modification du code source.

### Chiffres Clés

| Métrique | Valeur |
|----------|--------|
| Tables base de données | 67 |
| États workflow | 14 |
| Groupes utilisateurs | 13 |
| Templates notifications | 71 |
| Types documents PDF | 13 |
| Paramètres configuration | ~170 |
| Fonctionnalités désactivables | 27 |

### Principes Fondamentaux

1. **Autarcie Totale** : Aucune dépendance externe obligatoire
2. **Single Source of Truth** : Une seule source de données par élément
3. **Sécurité Par Défaut** : Toutes permissions DENY ALL par défaut
4. **Séparation des Responsabilités** : Architecture MVC++ stricte
5. **Convention Over Configuration** : Standardisation maximale (PSR-12)
6. **Auditabilité Totale** : Double journalisation pour toute action critique
7. **Versioning Strict** : Migrations numérotées séquentielles

---

## Acteurs du Système

### Acteurs Principaux (13 Groupes)

| # | Groupe | Niveau | Responsabilités |
|---|--------|--------|-----------------|
| 1 | **Administrateur** | 5 | Contrôle total du système, configuration, utilisateurs |
| 2 | **Secrétaire** | 6 | Gestion documentaire, archivage |
| 3 | **Communication** | 7 | Vérification format des rapports |
| 4 | **Scolarité** | 8 | Paiements, candidatures, inscriptions |
| 5 | **Resp. Filière** | 9 | Supervision filière MIAGE |
| 6 | **Resp. Niveau** | 10 | Gestion Master 2 |
| 7 | **Commission** | 11 | Évaluation rapports, votes |
| 8 | **Enseignant** | 12 | Supervision, participation jury |
| 9 | **Étudiant** | 13 | Rédaction rapport, soumissions |
| 10 | **Président Commission** | - | Constitution des jurys |
| 11 | **Président Jury** | Temp. | Saisie notes jour J |
| 12 | **Directeur Mémoire** | - | Direction scientifique |
| 13 | **Encadreur Pédagogique** | - | Accompagnement étudiant |

### Acteurs Système
- **Système** : Actions automatiques (notifications, calculs, alertes)
- **Cron Jobs** : Traitements planifiés (rappels, escalades)

### Matrice des Responsabilités (RACI)

| Action | Admin | Scolarité | Commission | Étudiant | Système |
|--------|-------|-----------|------------|----------|---------|
| Création compte utilisateur | R | C | - | - | A |
| Validation candidature | I | R | - | C | A |
| Évaluation rapport | I | - | R | I | A |
| Vote commission | - | - | R | - | A |
| Saisie notes soutenance | - | - | R (Prés.) | - | A |
| Génération documents | - | C | - | C | R |
| Archivage | A | I | - | - | R |

*R: Responsable, A: Approbateur, C: Consulté, I: Informé*

---

## Architecture Technique

### Stack Imposée

```
PHP 8.0+ (strict types) + MySQL 8.0+ + Architecture MVC++ Native
                              ↓
Requête → Routeur (Hashids) → Middleware → Contrôleur → Service → Modèle → DB
```

### Dépendances Autorisées (~12MB total)

| Package | Version | Usage |
|---------|---------|-------|
| hashids/hashids | ^4.0 | Obfuscation IDs |
| symfony/validator | ^6.0 | Validation données |
| symfony/http-foundation | ^6.0 | Gestion requêtes |
| symfony/cache | ^6.0 | Cache multi-niveaux |
| mpdf/mpdf | ^8.0 | Génération PDF avancé |
| tecnickcom/tcpdf | ^6.0 | Génération PDF simple |
| phpoffice/phpspreadsheet | ^1.0 | Import/Export Excel |
| phpmailer/phpmailer | ^6.0 | Envoi emails |
| monolog/monolog | ^3.0 | Logging |

### Extensions PHP Requises

`pdo_mysql`, `mbstring`, `openssl`, `intl`, `gd`, `zip`, `fileinfo`, `json`

### Structure des Dossiers

```
check.master/
├── app/
│   ├── Controllers/       # Contrôleurs (max 50 lignes)
│   ├── Services/          # Logique métier (stateless)
│   │   ├── Core/          # Services système
│   │   ├── Security/      # Authentification, permissions
│   │   ├── Workflow/      # Machine à états
│   │   └── ...
│   ├── Models/            # Modèles ORM (67 tables)
│   ├── Validators/        # Validation Symfony
│   ├── Middleware/        # Pipeline de traitement
│   ├── Orm/               # ORM léger maison
│   ├── Policies/          # Règles d'autorisation
│   └── config/            # Configuration runtime
├── src/
│   ├── Http/              # Request/Response
│   ├── Support/           # Helpers (Auth, etc.)
│   └── Exceptions/        # Exceptions typées
├── database/
│   ├── migrations/        # Migrations versionnées
│   └── seeds/             # Données initiales
├── docs/
│   └── prd/               # PRD par module
├── public/                # Point d'entrée (index.php)
├── ressources/
│   ├── views/             # Templates PHP
│   └── templates/pdf/     # Templates PDF
├── storage/               # Fichiers générés
└── tests/                 # PHPUnit
```

---

## Workflow Principal

### Machine à États (14 États)

```
┌─────────────┐
│   INSCRIT   │
└──────┬──────┘
       │ Soumission candidature
       ▼
┌─────────────────────┐
│ CANDIDATURE_SOUMISE │
└──────────┬──────────┘
           │ Validation Scolarité (paiement + docs)
           ▼
┌────────────────────────┐
│ VERIFICATION_SCOLARITE │
└──────────┬─────────────┘
           │ Validation Communication (format)
           ▼
┌─────────────────────┐
│ FILTRE_COMMUNICATION │
└──────────┬──────────┘
           │ Passage en commission
           ▼
┌────────────────────────┐
│ EN_ATTENTE_COMMISSION  │
└──────────┬─────────────┘
           │ Session programmée
           ▼
┌──────────────────────────┐
│ EN_EVALUATION_COMMISSION │◄──┐
└──────────┬───────────────┘   │ Corrections demandées
           │ Unanimité obtenue │
           ├───────────────────┘
           ▼
┌─────────────────┐
│ RAPPORT_VALIDE  │
└───────┬─────────┘
        │ Attribution encadreurs
        ▼
┌──────────────────────────┐
│ ATTENTE_AVIS_ENCADREUR   │
└──────────┬───────────────┘
           │ Avis favorable
           ▼
┌─────────────────┐
│ PRET_POUR_JURY  │
└───────┬─────────┘
        │ Constitution jury
        ▼
┌──────────────────────┐
│ JURY_EN_CONSTITUTION │
└──────────┬───────────┘
           │ 5 membres acceptent
           ▼
┌──────────────────────┐
│ SOUTENANCE_PLANIFIEE │
└──────────┬───────────┘
           │ Jour J
           ▼
┌─────────────────────┐
│ SOUTENANCE_EN_COURS │
└──────────┬──────────┘
           │ Notes validées
           ▼
┌──────────────────────┐
│ SOUTENANCE_TERMINEE  │
└──────────┬───────────┘
           │ Corrections finales validées
           ▼
┌─────────────────────┐
│ DIPLOME_DELIVRE (T) │
└─────────────────────┘
```

### États Spéciaux
- **ABANDON** : État terminal déclaratif
- **ESCALADE_DOYEN** : Blocage commission après 3 tours
- **CORRECTIONS_DEMANDEES** : Boucle de correction rapport

### Délais et SLA par État

| État | Délai Max | Alerte 50% | Alerte 80% | Escalade |
|------|-----------|------------|------------|----------|
| VERIFICATION_SCOLARITE | 5 jours | J+2 | J+4 | Resp. Scolarité |
| FILTRE_COMMUNICATION | 3 jours | J+1 | J+2 | Resp. Comm. |
| EN_EVALUATION_COMMISSION | Session mensuelle | - | - | Président Commission |
| ATTENTE_AVIS_ENCADREUR | 15 jours | J+7 | J+12 | Directeur Mémoire |
| JURY_EN_CONSTITUTION | 10 jours | J+5 | J+8 | Président Commission |
| Corrections post-soutenance | 10 jours | J+5 | J+8 | Encadreur |

### Transitions Automatiques

| Déclencheur | Action | Notification |
|-------------|--------|--------------|
| Paiement complet | Déblocage candidature | Étudiant |
| Validation scolarité | Déblocage rédaction rapport | Étudiant |
| Unanimité commission | Validation rapport | Étudiant + Encadreurs |
| 5 jurés acceptent | Planification soutenance | Tous |
| Notes validées | Génération PV | Admin |

---

## Modules du Système

### Structure Modulaire

| Module | PRD | Description |
|--------|-----|-------------|
| Authentification & Utilisateurs | `01_authentication_users.md` | Sessions, permissions, rôles |
| Entités Académiques | `02_academic_entities.md` | Étudiants, enseignants, UE |
| Workflow & Commission | `03_workflow_commission.md` | États, transitions, votes |
| Mémoire & Soutenance | `04_thesis_defense.md` | Rapports, jury, notes |
| Communication | `05_communication.md` | Notifications, messagerie |
| Documents & Archives | `06_documents_archives.md` | PDF, archivage, historisation |
| Financier | `07_financial.md` | Paiements, pénalités |
| Administration | `08_administration.md` | Configuration, audit |

### Dépendances Inter-Modules

```
┌───────────────────┐
│ Authentification  │◄───────────────────────────────────┐
└─────────┬─────────┘                                    │
          │                                              │
          ▼                                              │
┌───────────────────┐     ┌───────────────────┐          │
│    Permissions    │◄────│   Administration  │          │
└─────────┬─────────┘     └───────────────────┘          │
          │                                              │
          ├──────────────────┬───────────────────┐       │
          ▼                  ▼                   ▼       │
┌───────────────────┐ ┌───────────────┐ ┌─────────────┐  │
│     Workflow      │ │  Communication │ │  Documents  │  │
└─────────┬─────────┘ └───────┬───────┘ └──────┬──────┘  │
          │                   │                │         │
          ▼                   ▼                ▼         │
┌──────────────────────────────────────────────────────┐ │
│                  Mémoire & Soutenance                │─┘
└──────────────────────────────────────────────────────┘
          │
          ▼
┌───────────────────┐     ┌───────────────────┐
│  Entités Acad.    │─────│    Financier      │
└───────────────────┘     └───────────────────┘
```

---

## Fonctionnalités Transversales

### Services Primordiaux

| Service | Criticité | Fonction |
|---------|-----------|----------|
| ServiceAudit | 🔴 Critique | Traçabilité complète, snapshots JSON |
| ServiceAuthentification | 🔴 Critique | Connexion sécurisée, sessions |
| ServicePermission | 🔴 Critique | RBAC, cache, rôles temporaires |
| ServiceNotification | 🔴 Critique | Multi-canal (Email, SMS, Messagerie) |
| ServiceWorkflow | 🔴 Critique | Machine à états, transitions |
| ServiceEscalade | 🔴 Critique | Médiation, déblocage |
| ServiceCalendrier | 🟠 Élevé | Disponibilités, conflits |
| ServicePdf | 🟠 Élevé | 13 types documents |
| ServiceArchivage | 🟠 Élevé | Intégrité SHA256 |
| ServiceSignature | 🟡 Moyen | Optionnel, OTP |

### Règles Métier Non-Négociables

1. **Gate Critique** : L'onglet "Rédaction rapport" invisible tant que `état != candidature_validée`
2. **Création Utilisateur** : L'entité métier (étudiant/enseignant) DOIT exister AVANT le compte
3. **Numéro Carte** : Format `CI01552852` (VARCHAR 20), unique, non modifiable
4. **Archivage** : Hash SHA256 obligatoire, documents inaltérables
5. **Audit** : Double journalisation (fichier + base), pas de suppression

### Règles de Validation

| Entité | Règle | Erreur |
|--------|-------|--------|
| Numéro carte étudiant | Format alphanumérique, max 20 chars | "Format numéro carte invalide" |
| Email | Format valide + unique | "Email déjà utilisé" |
| Mot de passe | Min 8 chars, 1 maj, 1 min, 1 chiffre, 1 spécial | "Mot de passe trop faible" |
| Date soutenance | Future et pas conflit | "Conflit de planification" |
| Note | Entre 0 et 20 | "Note invalide" |

---

## Base de Données

### Schéma Global (67 Tables)

#### Section 1 : Authentification & Utilisateurs (10 tables)
- `utilisateurs` - Comptes utilisateurs
- `sessions_actives` - Sessions multi-appareils
- `codes_temporaires` - Codes OTP Président Jury
- `groupes` - Groupes avec niveaux hiérarchiques
- `utilisateurs_groupes` - Association N:N
- `roles_temporaires` - Rôles contextuels (jour J)
- `ressources` - Ressources protégées
- `permissions` - CRUD par groupe/ressource
- `permissions_cache` - Cache 5 minutes
- `pister` - Table d'audit inaltérable

#### Section 2 : Entités Académiques (12 tables)
- `etudiants` - Fiches étudiants
- `enseignants` - Corps enseignant
- `personnel_admin` - Personnel administratif
- `entreprises` - Entreprises partenaires
- `specialites` - Domaines d'expertise
- `grades` - Grades académiques
- `fonctions` - Fonctions administratives
- `annee_academique` - Calendrier académique
- `semestre` - Semestres
- `niveau_etude` - Niveaux (L1, M2, etc.)
- `ue` - Unités d'enseignement
- `ecue` - Éléments constitutifs d'UE

#### Section 3 : Workflow & Dossiers (12 tables)
- `workflow_etats` - 14 états possibles
- `workflow_transitions` - Transitions autorisées
- `workflow_historique` - Historique transitions
- `workflow_alertes` - Alertes SLA
- `dossiers_etudiants` - Dossier par étudiant/année
- `candidatures` - Informations stage
- `rapports_etudiants` - Rapports avec versioning
- `sessions_commission` - Sessions mensuelles
- `votes_commission` - Votes par membre
- `annotations_rapport` - Annotations contextuelles
- `escalades` - Escalades actives
- `escalades_actions` - Actions sur escalades

#### Section 4 : Jury & Soutenance (4 tables)
- `jury_membres` - 5 membres par soutenance
- `soutenances` - Planification
- `notes_soutenance` - Notes par critère
- `decisions_jury` - Décisions finales

#### Section 5 : Financier (3 tables)
- `paiements` - Versements étudiants
- `penalites` - Pénalités de retard
- `exonerations` - Exonérations accordées

#### Section 6 : Communication (5 tables)
- `notification_templates` - 71 templates
- `notifications_queue` - File d'attente
- `notifications_historique` - Historique envois
- `email_bounces` - Gestion bounces
- `messages_internes` - Messagerie interne

#### Section 7 : Documents & Archives (8 tables)
- `documents_generes` - Métadonnées PDF
- `archives` - Archives avec intégrité
- `historique_entites` - Snapshots JSON
- `critere_evaluation` - Critères de notation
- `mentions` - Mentions (Passable → Excellent)
- `roles_jury` - Rôles (Président, Rapporteur, etc.)
- `salles` - Salles de soutenance
- `statut_jury` - Statuts acceptation

#### Section 8 : Configuration (7 tables)
- `configuration_systeme` - ~170 paramètres
- `traitement` - Fonctionnalités/écrans
- `action` - Actions CRUD
- `rattacher` - Permissions groupe/traitement/action
- `type_utilisateur` - Types utilisateur
- `groupe_utilisateur` - Groupes
- `niveau_acces_donnees` - Niveaux d'accès
- `escalade_niveaux` - Configuration escalade
- `imports_historiques` - Historique imports
- `stats_cache` - Cache statistiques
- `maintenance_mode` - Mode maintenance
- `migrations` - Migrations exécutées

---

## Sécurité

### Hashage des Mots de Passe

```php
// Algorithme: PASSWORD_ARGON2ID (PHP 8.0+)
password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,  // 64 MB
    'time_cost' => 4,        // 4 itérations
    'threads' => 3           // 3 threads parallèles
]);
```

### Protection Brute-Force

| Tentatives | Délai | Action |
|------------|-------|--------|
| 3 | 1 minute | Verrouillage temporaire |
| 5 | 15 minutes | Verrouillage prolongé |
| 10 | 24 heures | Verrouillage + Alerte admin |

### Tokens de Session

- **Génération** : `bin2hex(random_bytes(64))` (128 caractères)
- **Durée** : 8 heures (configurable)
- **Stockage** : Table `sessions_actives`
- **Invalidation** : Logout, expiration, force-logout admin

### Code Temporaire Président Jury

- **Format** : 8 caractères alphanumériques (sans 0/O, 1/I)
- **Validité** : 06h00 à 23h59 du jour J uniquement
- **Canal** : SMS prioritaire + Email backup
- **Usage** : Unique (reconnexion possible même code)

---

## Critères de Succès Globaux

### Performance
- Temps de réponse < 200ms pour 95% des requêtes
- Support de 500 utilisateurs simultanés
- Génération PDF < 5 secondes

### Fiabilité
- Disponibilité 99.5% (hors maintenance planifiée)
- Zéro perte de données sur 10 ans
- Récupération < 4 heures après incident

### Sécurité
- Aucun accès non autorisé détecté
- 100% des actions critiques auditées
- Conformité RGPD

### Utilisabilité
- Taux d'abandon formulaires < 5%
- Formation utilisateur < 2 heures
- Support mobile (responsive)

---

## Documents Générés (13 Types)

| Type | Générateur | Usage |
|------|------------|-------|
| Reçu de paiement | TCPDF | Après versement |
| Reçu de pénalité | TCPDF | Après paiement pénalité |
| Bulletin de notes | TCPDF | Fin semestre |
| Attestation inscription | TCPDF | Sur demande |
| PV Commission | mPDF | Fin session |
| PV Soutenance | mPDF | Après délibération |
| Convocation Commission | TCPDF | Avant session |
| Convocation Jury | TCPDF | Avant soutenance |
| Fiche notation | TCPDF | Jour soutenance |
| Attestation réussite | mPDF | Post-soutenance |
| Attestation diplôme | mPDF | Fin processus |
| Relevé de notes | TCPDF | Sur demande |
| Page de garde rapport | mPDF | Soumission |

---

## Hors Périmètre (V1)

- Intégration SI universitaire externe (APOGEE)
- Application mobile native
- Détection de plagiat automatique
- Blockchain pour diplômes
- Multi-langue (français uniquement)
- Paiement en ligne

---

## Glossaire

| Terme | Définition |
|-------|------------|
| **Candidature** | Demande de soumission de rapport de stage |
| **Commission** | Groupe d'enseignants évaluant les rapports |
| **Dossier** | Ensemble des documents d'un étudiant pour une année |
| **Encadreur Pédagogique** | Enseignant accompagnant l'étudiant |
| **Directeur Mémoire** | Responsable scientifique du mémoire |
| **Gate** | Point de contrôle bloquant dans le workflow |
| **Hashid** | ID obfusqué pour les URLs |
| **PV** | Procès-Verbal de commission ou soutenance |
| **SLA** | Service Level Agreement (délai maximum) |
| **Workflow** | Machine à états gérant le parcours étudiant |

---

## Références

- [Constitution](../constitution.md) - Principes non-négociables
- [Workflows](../workflows.md) - Documentation processus
- [Workbench](../workbench.md) - Guide implémentation
- [Roadmap](../roadmap.md) - Plan d'implémentation
- [Base de données](../../database/migrations/001_create_complete_database.sql) - Schéma complet

---

## Historique des Modifications

| Version | Date | Auteur | Changements |
|---------|------|--------|-------------|
| 1.0.0 | 2025-12-14 | CheckMaster Team | Version initiale |
| 2.0.0 | 2025-12-24 | CheckMaster Team | Exhaustivité complète : architecture, BDD, sécurité, glossaire |
