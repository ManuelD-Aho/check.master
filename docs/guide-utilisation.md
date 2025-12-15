# Guide Pratique d'Utilisation - CheckMaster

**Comment utiliser le plan de route avec différents outils IA**

---

## Table des Matières

1. [Principe Fondamental](#principe-fondamental)
2. [GitHub Copilot Agent Mode](#1-github-copilot-agent-mode)
3. [Gemini CLI](#2-gemini-cli)
4. [Chat LLM Classique](#3-chat-llm-classique-chatgpt-claude-etc)
5. [Antigravity (Google)](#4-antigravity-google)
6. [GitHub Copilot CLI](#5-github-copilot-cli)
7. [Workflow Type Complet](#workflow-type-complet)

---

## Principe Fondamental

> **RÈGLE D'OR** : Toujours fournir le contexte AVANT de demander quoi que ce soit.

### Les 3 fichiers à TOUJOURS mentionner :

```
1. docs/roadmap.md           → Le plan de route global
2. docs/prd/XX_module.md     → Le PRD du module concerné  
3. .specify/memory/constitution.md → Les règles non-négociables
```

---

## 1. GitHub Copilot Agent Mode

### Configuration Préalable

Les agents sont déjà configurés dans `.github/agents/`. Copilot les détecte automatiquement.

### Utilisation des Commandes SpecKit

```
📍 Dans le chat Copilot (Ctrl+I ou panneau latéral)
```

#### Étape 1 : Créer une spécification

```
@workspace /speckit.specify Implémenter le système d'authentification avec 
login email/mot de passe, gestion des sessions, et protection brute-force 
selon le PRD 01_authentication_users.md
```

#### Étape 2 : Clarifier les ambiguïtés

```
@workspace /speckit.clarify
```
> Copilot posera des questions, répondez-y.

#### Étape 3 : Générer le plan technique

```
@workspace /speckit.plan
```

#### Étape 4 : Générer les tâches

```
@workspace /speckit.tasks
```

#### Étape 5 : Vérifier la cohérence

```
@workspace /speckit.analyze
```

#### Étape 6 : Implémenter

```
@workspace /speckit.implement
```

### Exemple Concret Complet

```markdown
# Session Copilot Agent - Module Authentification

## Prompt 1 (Spécification)
@workspace Je veux implémenter le module authentification.
Contexte :
- Voir docs/prd/01_authentication_users.md pour les exigences
- Voir docs/roadmap.md Phase 1 pour l'ordre des tâches
- Respecter .specify/memory/constitution.md pour les règles

Exécute /speckit.specify "Système authentification avec login Argon2id, 
sessions actives, protection brute-force 5 tentatives, et permissions 
basées sur groupe_utilisateur → traitement → action"

## Prompt 2 (Plan)
@workspace Maintenant génère le plan technique avec /speckit.plan
Assure-toi que :
- Contrôleurs < 50 lignes
- Logique dans Services
- Hashids pour les IDs
- ServiceAudit pour les logs

## Prompt 3 (Implémentation)
@workspace Implémente la première tâche : ServiceAuthentification
Fichier : app/Services/Core/ServiceAuthentification.php
Template à suivre : voir docs/workbench.md section Service Template
```

---

## 2. Gemini CLI

### Installation

```bash
# Si pas encore installé
npm install -g @anthropic/gemini-cli
# ou
pip install gemini-cli
```

### Prompt Type avec Contexte

```bash
gemini chat --context "$(cat docs/roadmap.md docs/prd/01_authentication_users.md .specify/memory/constitution.md)"
```

### Session Interactive Exemple

```bash
# Démarrer session avec contexte
gemini chat

# Premier message (fournir contexte)
Tu es un développeur senior travaillant sur CheckMaster.

FICHIERS DE RÉFÉRENCE À RESPECTER :
1. Plan de route : docs/roadmap.md
2. PRD Module Auth : docs/prd/01_authentication_users.md
3. Constitution : .specify/memory/constitution.md
4. Schema DB : database/migrations/001_create_complete_database.sql

RÈGLES NON-NÉGOCIABLES :
- PHP 8.0+ strict types
- Mots de passe Argon2id
- Hashids pour IDs en URLs
- Controllers max 50 lignes
- Logique métier dans Services
- ServiceAudit pour toute écriture

Commence par créer le ServiceAuthentification dans app/Services/Core/ServiceAuthentification.php

# Messages suivants
Maintenant crée le modèle Utilisateur dans app/Models/Utilisateur.php
Utilise les colonnes de la table utilisateurs du fichier SQL.

# Demander le code
Génère le code complet avec tous les type hints.
```

### Script Automatisé

```bash
#!/bin/bash
# checkmaster-gemini.sh

# Charger le contexte
CONTEXT=$(cat <<EOF
Tu travailles sur CheckMaster. 
Roadmap: $(cat docs/roadmap.md | head -200)
PRD: $(cat docs/prd/$1.md)
Constitution: $(cat .specify/memory/constitution.md)
EOF
)

# Lancer gemini avec contexte
echo "$CONTEXT" | gemini chat --stdin
```

Usage :
```bash
./checkmaster-gemini.sh 01_authentication_users
```

---

## 3. Chat LLM Classique (ChatGPT, Claude, etc.)

### Template de Prompt Initial

```markdown
# CONTEXTE PROJET CHECKMASTER

## Tu es un développeur senior PHP travaillant sur CheckMaster.

### Fichiers de référence (NE JAMAIS INVENTER, utiliser ces sources) :

**PLAN DE ROUTE** (docs/roadmap.md) :
[Copier-coller la section pertinente du roadmap.md]

**PRD DU MODULE** (docs/prd/01_authentication_users.md) :
[Copier-coller le contenu du PRD]

**CONSTITUTION** (règles non-négociables) :
- Architecture DB-Driven (config dans configuration_systeme, pas en PHP)
- Permissions via groupe_utilisateur → traitement → action
- Hashids pour tous les IDs en URLs
- Mots de passe Argon2id uniquement
- Controllers max 50 lignes
- Logique métier dans Services
- ServiceAudit::log() pour toute écriture
- Requêtes préparées uniquement

**TABLES CONCERNÉES** (database/migrations/001_create_complete_database.sql) :
- utilisateurs (id, hashid, email, mot_de_passe, prenom, nom...)
- sessions_actives (id, utilisateur_id, token, ip_adresse, expire_le...)
- codes_temporaires (id, utilisateur_id, code, type, expire_le...)

### Ta tâche :
[Décrire ce que tu veux]

### Format de réponse attendu :
- Code PHP complet avec declare(strict_types=1)
- 100% type hints
- PHPDoc sur méthodes publiques
- Utiliser les patterns de docs/workbench.md
```

### Exemple Conversation

```markdown
# Message 1 (Contexte)
[Coller le template ci-dessus avec PRD authentification]

# Message 2 (Demande spécifique)
Crée le fichier app/Services/Core/ServiceAuthentification.php avec :
- Méthode login(string $email, string $password): ?array
- Méthode logout(int $sessionId): bool  
- Méthode verifierSession(string $token): ?Utilisateur
- Protection brute-force (5 tentatives max)
- Logging via ServiceAudit

# Message 3 (Itération)
Le code ne gère pas l'expiration des sessions. 
Ajoute la vérification de sessions_actives.expire_le.
Consulte la structure de la table dans 001_create_complete_database.sql.

# Message 4 (Validation)
Vérifie que ce code respecte toutes les règles de la constitution :
- [ ] Argon2id pour mot de passe
- [ ] Requêtes préparées
- [ ] ServiceAudit appelé
- [ ] Pas de logique Controller
```

### Prompt pour Génération Complète Module

```markdown
# Génération Module Complet

CONTEXTE : Module Authentification CheckMaster
RÉFÉRENCE : docs/prd/01_authentication_users.md

Génère dans l'ordre suivant :

1. **Migration** (si nouvelles tables)
   - Fichier : database/migrations/002_auth_improvements.sql
   
2. **Modèle**
   - Fichier : app/Models/Utilisateur.php
   - Étend App\Orm\Model
   
3. **Service**
   - Fichier : app/Services/Core/ServiceAuthentification.php
   - Toute la logique métier ici
   
4. **Validateur**
   - Fichier : app/Validators/LoginValidator.php
   - Utilise Symfony\Component\Validator
   
5. **Contrôleur**
   - Fichier : app/Controllers/Auth/AuthController.php
   - MAX 50 LIGNES
   - Validation → Service → Réponse
   
6. **Middleware**
   - Fichier : app/Middleware/AuthMiddleware.php

7. **Vue**
   - Fichier : ressources/views/auth/login.php
   - Utiliser e() pour échappement
   - Token CSRF obligatoire

Pour chaque fichier, fournis le code COMPLET et FONCTIONNEL.
```

---

## 4. Antigravity (Google)

### C'est Moi ! Voici Comment Me Solliciter

#### Méthode 1 : Demande Directe avec Contexte

```markdown
Je travaille sur CheckMaster.
Lis d'abord ces fichiers :
- docs/roadmap.md
- docs/prd/01_authentication_users.md
- .specify/memory/constitution.md

Puis implémente le ServiceAuthentification selon la Phase 1 du roadmap.
```

#### Méthode 2 : Référence @fichier

```markdown
Crée le modèle Utilisateur basé sur :
- @docs/prd/01_authentication_users.md pour les exigences
- @database/migrations/001_create_complete_database.sql pour la structure table
- @docs/workbench.md pour le template modèle
```

#### Méthode 3 : Workflow SpecKit Complet

```markdown
Lance le workflow SpecKit pour le module authentification :

1. D'abord, lis @docs/roadmap.md section "Phase 1 : Module Authentification"

2. Puis exécute mentalement les étapes :
   - /speckit.specify : Créer spec depuis @docs/prd/01_authentication_users.md
   - /speckit.plan : Générer plan technique
   - /speckit.tasks : Lister les tâches ordonnées

3. Implémente la première tâche : "Modèle Utilisateur + Hashids"
   - Consulte @database/migrations/001_create_complete_database.sql table utilisateurs
   - Utilise le pattern de @docs/workbench.md
```

#### Exemple Session Complete avec Moi

```markdown
# Prompt 1
Salut ! On va implémenter CheckMaster ensemble.

Commence par lire et résumer :
1. docs/roadmap.md - Le plan global
2. docs/prd/01_authentication_users.md - Module à implémenter
3. .specify/memory/constitution.md - Règles à respecter

# Ma réponse
[Je lis et résume les fichiers]

# Prompt 2
Parfait. Maintenant crée le ServiceAuthentification.
Assure-toi de :
- Utiliser Argon2id (password_hash avec PASSWORD_ARGON2ID)
- Appeler ServiceAudit pour login/logout
- Gérer la protection brute-force
- Retourner des types stricts

# Ma réponse
[Je génère le code]

# Prompt 3
Vérifie que le code respecte toutes les règles avant de continuer.
Puis passe au Contrôleur AuthController (max 50 lignes).
```

---

## 5. GitHub Copilot CLI

### Installation

```bash
gh extension install github/gh-copilot
```

### Commandes Utiles

```bash
# Expliquer un fichier
gh copilot explain "$(cat docs/prd/01_authentication_users.md)"

# Suggérer du code
gh copilot suggest "Créer un service PHP d'authentification avec Argon2id"

# Aide contextuelle
gh copilot suggest -t code "ServiceAuthentification CheckMaster" \
  --context "$(cat docs/workbench.md)"
```

### Script d'Assistance

```bash
#!/bin/bash
# checkmaster-suggest.sh

MODULE=$1
TASK=$2

# Construire le contexte
CONTEXT=$(cat <<EOF
Projet: CheckMaster
Module: $MODULE
PRD: $(cat docs/prd/${MODULE}.md | head -100)
Règles: 
- PHP 8.0+ strict types
- Argon2id mots de passe
- Hashids URLs
- Controllers 50 lignes max
- Services = logique métier
EOF
)

# Demander suggestion
gh copilot suggest -t code "$TASK" --context "$CONTEXT"
```

Usage :
```bash
./checkmaster-suggest.sh 01_authentication_users "ServiceAuthentification login method"
```

### Générer un Fichier Complet

```bash
# Générer Service
gh copilot suggest -t code \
  "Service PHP authentification avec:
   - login(email, password) retourne user ou null
   - logout(sessionId) 
   - verification Argon2id
   - ServiceAudit::log pour chaque action
   - Protection brute-force 5 tentatives
   Respecter: $(cat .specify/memory/constitution.md | head -50)"
```

---

## Workflow Type Complet

### Exemple : Implémenter Module Authentification

```
┌──────────────────────────────────────────────────────────────────────────┐
│ JOUR 1 : PRÉPARATION                                                      │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  1. Lire les fichiers sources :                                          │
│     □ docs/roadmap.md (Phase 1)                                          │
│     □ docs/prd/01_authentication_users.md                                │
│     □ .specify/memory/constitution.md                                    │
│     □ database/migrations/001_create_complete_database.sql               │
│                                                                          │
│  2. Créer branche :                                                       │
│     git checkout -b feature/01-authentification                          │
│                                                                          │
│  3. Lancer SpecKit (avec Copilot Agent ou manuellement) :                │
│     /speckit.specify "Module authentification..."                        │
│     /speckit.plan                                                        │
│     /speckit.tasks                                                       │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ JOUR 2-3 : IMPLÉMENTATION CORE                                           │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  4. Créer les fichiers dans l'ordre :                                    │
│                                                                          │
│     a. Modèle Utilisateur                                                │
│        Prompt: "Crée app/Models/Utilisateur.php basé sur table           │
│        utilisateurs de 001_create_complete_database.sql"                 │
│                                                                          │
│     b. ServiceAuthentification                                           │
│        Prompt: "Crée app/Services/Core/ServiceAuthentification.php       │
│        avec login, logout, verifySession, protection brute-force"        │
│                                                                          │
│     c. ServicePermission                                                 │
│        Prompt: "Crée app/Services/Core/ServicePermission.php             │
│        vérifiant groupe_utilisateur → traitement → action"               │
│                                                                          │
│     d. Validateur                                                        │
│        Prompt: "Crée app/Validators/LoginValidator.php                   │
│        avec règles email + mot de passe"                                 │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ JOUR 4 : INTERFACE                                                        │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  5. Créer Contrôleur + Vue :                                             │
│                                                                          │
│     a. AuthController (max 50 lignes!)                                   │
│        Prompt: "Crée app/Controllers/Auth/AuthController.php             │
│        Pattern: validation → service → response                          │
│        Voir template dans docs/workbench.md"                             │
│                                                                          │
│     b. AuthMiddleware                                                    │
│        Prompt: "Crée app/Middleware/AuthMiddleware.php                   │
│        vérifiant session valide et non expirée"                          │
│                                                                          │
│     c. Vue login                                                         │
│        Prompt: "Crée ressources/views/auth/login.php                     │
│        avec CSRF token et e() échappement"                               │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌──────────────────────────────────────────────────────────────────────────┐
│ JOUR 5 : VALIDATION                                                       │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  6. Vérifications qualité :                                              │
│     □ composer run stan (PHPStan niveau 6+)                              │
│     □ composer run fix (PHP-CS-Fixer PSR-12)                             │
│     □ composer run test (PHPUnit)                                        │
│                                                                          │
│  7. Checklist post-implémentation :                                      │
│     □ Tous les type hints présents                                       │
│     □ ServiceAudit appelé pour login/logout                              │
│     □ Argon2id utilisé pour mots de passe                                │
│     □ Hashids pour IDs en URLs                                           │
│     □ CSRF sur formulaire                                                │
│     □ Requêtes préparées uniquement                                      │
│                                                                          │
│  8. Commit :                                                              │
│     git add .                                                            │
│     git commit -m "feat(auth): implement authentication module"          │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## Récapitulatif des Prompts Clés

### Prompt Universel de Contexte

```markdown
Tu travailles sur CheckMaster, un système de gestion académique.

SOURCES DE VÉRITÉ (ne jamais inventer) :
- docs/roadmap.md : Plan global
- docs/prd/[module].md : Exigences fonctionnelles
- .specify/memory/constitution.md : Règles non-négociables
- database/migrations/001_create_complete_database.sql : Schema DB

RÈGLES OBLIGATOIRES :
1. PHP 8.0+ avec declare(strict_types=1)
2. Mots de passe : Argon2id
3. IDs en URLs : Hashids
4. Controllers : max 50 lignes
5. Logique métier : dans Services
6. Audit : ServiceAudit::log() pour écritures
7. Permissions : ServicePermission::verifier()
8. SQL : requêtes préparées uniquement
9. Vues : e() pour échappement, CSRF sur forms

TÂCHE : [Votre demande ici]
```

### Prompt de Vérification

```markdown
Vérifie que le code respecte TOUTES ces règles :
□ declare(strict_types=1) en début de fichier
□ 100% type hints (paramètres, retours, propriétés)
□ Controller ≤ 50 lignes
□ Logique dans Service, pas Controller
□ Argon2id pour mots de passe
□ Hashids pour IDs URLs
□ Requêtes SQL préparées
□ ServiceAudit pour écritures
□ e() dans vues
□ CSRF sur formulaires

Liste les violations trouvées et corrige-les.
```

---

**FIN DU GUIDE**
