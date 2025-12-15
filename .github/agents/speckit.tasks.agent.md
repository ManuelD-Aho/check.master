---
description: Générer un tasks.md actionnable et ordonné par dépendances pour les fonctionnalités CheckMaster basé sur les artefacts de conception disponibles.
handoffs: 
  - label: Analyser pour Cohérence
    agent: speckit.analyze
    prompt: Exécuter une analyse de cohérence projet CheckMaster
    send: true
  - label: Implémenter Projet
    agent: speckit.implement
    prompt: Démarrer l'implémentation CheckMaster par phases (PHP 8.0+ MVC++)
    send: true
---

## Entrée Utilisateur

```text
$ARGUMENTS
```

Vous **DEVEZ** prendre en compte l'entrée utilisateur avant de procéder (si non vide).

## Contexte Génération Tâches CheckMaster

### Ordre Strict Cycle Développement
1. **Base de Données D'abord** : Migrations → Seeds
2. **Core (TDD si demandé)** : Tests → Modèles → Services
3. **Interface** : Validateurs → Contrôleurs → Routes → Vues
4. **Intégration** : Permissions → Notifications → Audit → Documents
5. **Qualité** : PHPStan → PHP-CS-Fixer → PHPUnit

### Structure Fichiers CheckMaster

```
app/
├── Controllers/
│   └── {Module}/
│       └── {Feature}Controller.php
├── Services/
│   └── {Module}/
│       └── Service{Feature}.php
├── Models/
│   └── {Entity}.php
├── Validators/
│   └── {Feature}Validator.php
├── Middleware/
│   └── {Feature}Middleware.php
└── Orm/
    └── Model.php (classe base)

database/
├── migrations/
│   └── 0XX_description.sql
└── seeds/
    └── 0XX_description.sql

ressources/
├── views/
│   └── modules/
│       └── {module}/
│           └── {feature}/
│               ├── index.php
│               ├── create.php
│               └── edit.php
└── templates/
    └── pdf/
        └── {template}.php

tests/
├── Unit/
│   ├── Services/
│   ├── Models/
│   └── Validators/
└── Integration/
    └── Controllers/
```

### Patterns Tâches Spécifiques CheckMaster

**Pour Fonctionnalités Workflow** :
```markdown
### Phase X : User Story N - [Changement État Workflow]
- [ ] T0XX [USN] Ajouter état workflow dans table workflow_etats (migration)
- [ ] T0XX [USN] Ajouter transitions dans table workflow_transitions (migration)
- [ ] T0XX [P] [USN] Mettre à jour ServiceWorkflow pour gérer nouvel état
- [ ] T0XX [USN] Ajouter validation transition dans {Feature}Validator
- [ ] T0XX [USN] Implémenter changement état dans Service{Feature}
- [ ] T0XX [USN] Ajouter journalisation audit pour transitions état
- [ ] T0XX [P] [USN] Créer template notification pour transition
- [ ] T0XX [USN] Tester intégration workflow
```

**Pour Fonctionnalités Protégées par Permissions** :
```markdown
### Phase X : User Story N - [Action Protégée]
- [ ] T0XX [USN] Ajouter entrée traitement dans table traitement (seed)
- [ ] T0XX [USN] Ajouter entrée action dans table action (seed)
- [ ] T0XX [USN] Lier aux groupes dans table rattacher (seed)
- [ ] T0XX [P] [USN] Implémenter vérification permission dans contrôleur
- [ ] T0XX [USN] Ajouter PermissionMiddleware à la route
- [ ] T0XX [USN] Tester scénarios refus permission
```

**Pour Génération Documents** :
```markdown
### Phase X : User Story N - [Type Document]
- [ ] T0XX [USN] Créer template PDF dans ressources/templates/pdf/
- [ ] T0XX [USN] Ajouter type document dans config documents_generes
- [ ] T0XX [P] [USN] Implémenter génération dans ServicePdf
- [ ] T0XX [USN] Calculer et stocker hash SHA256
- [ ] T0XX [USN] Archiver document avec vérification intégrité
- [ ] T0XX [P] [USN] Envoyer notification avec lien téléchargement
- [ ] T0XX [USN] Tester génération PDF et archivage
```

**Pour Opérations CRUD** :
```markdown
### Phase X : User Story N - [CRUD Entité]
- [ ] T0XX [USN] Créer table entité (migration)
- [ ] T0XX [P] [USN] Créer Modèle dans app/Models/{Entity}.php
- [ ] T0XX [P] [USN] Créer Validateur dans app/Validators/{Entity}Validator.php
- [ ] T0XX [USN] Implémenter Service{Entity} avec méthodes CRUD
- [ ] T0XX [USN] Ajouter journalisation audit pour opérations écriture
- [ ] T0XX [USN] Créer Contrôleur avec routage Hashids
- [ ] T0XX [P] [USN] Créer vues (index, create, edit)
- [ ] T0XX [USN] Tester opérations CRUD de bout en bout
```

**Pour Fonctionnalités Commission/Vote** :
```markdown
### Phase X : User Story N - [Vote/Commission]
- [ ] T0XX [USN] Ajouter session dans sessions_commission (si nouvelle)
- [ ] T0XX [USN] Ajouter table suivi votes (migration)
- [ ] T0XX [P] [USN] Implémenter logique vote avec limite tours (3 max)
- [ ] T0XX [USN] Vérifier calcul unanimité
- [ ] T0XX [USN] Ajouter déclencheur escalade sur échec tour 3
- [ ] T0XX [P] [USN] Envoyer notifications pour tours de vote
- [ ] T0XX [USN] Générer PV avec signatures
- [ ] T0XX [USN] Tester scénarios vote (unanimité, escalade)
```

**Pour Opérations Financières** :
```markdown
### Phase X : User Story N - [Paiement/Pénalité]
- [ ] T0XX [USN] Ajouter table enregistrement financier (si nécessaire)
- [ ] T0XX [P] [USN] Implémenter logique calcul dans Service{Finance}
- [ ] T0XX [USN] Valider statut paiement dans gate workflow
- [ ] T0XX [USN] Générer PDF reçu avec TCPDF
- [ ] T0XX [USN] Archiver reçu avec SHA256
- [ ] T0XX [P] [USN] Envoyer email confirmation
- [ ] T0XX [USN] Mettre à jour tableau de bord financier étudiant
- [ ] T0XX [USN] Tester flux paiement et génération reçu
```

### Gates Qualité CheckMaster

Après chaque phase User Story, inclure tâches vérification :

```markdown
### Phase X+1 : User Story N - Assurance Qualité
- [ ] T0XX [P] [USN] Exécuter analyse PHPStan niveau 6+
- [ ] T0XX [P] [USN] Exécuter PHP-CS-Fixer pour PSR-12
- [ ] T0XX [P] [USN] Vérifier appels ServiceAudit sur écritures
- [ ] T0XX [P] [USN] Vérifier usage Hashids dans URLs
- [ ] T0XX [P] [USN] Vérifier requêtes préparées (pas de SQL brut)
- [ ] T0XX [P] [USN] Tester vérifications permissions
- [ ] T0XX [P] [USN] Valider échappement e() dans vues
- [ ] T0XX [USN] Test intégration pour flux utilisateur complet
```

## Aperçu

1. **Setup** : Exécuter `.specify/scripts/powershell/check-prerequisites.ps1 -Json` depuis racine repo et parser FEATURE_DIR et liste AVAILABLE_DOCS. Tous les chemins doivent être absolus. Pour apostrophes dans args comme "J'organise", utiliser syntaxe échappement : ex 'J'\''organise' (ou guillemets si possible : "J'organise").

2. **Charger documents conception** : Lire depuis FEATURE_DIR :
   - **Requis** : plan.md (stack technique, bibliothèques, structure), spec.md (user stories avec priorités)
   - **Optionnel** : data-model.md (entités), contracts/ (endpoints API), research.md (décisions), quickstart.md (scénarios test)
   - Note : Tous les projets n'ont pas tous les documents. Générer tâches basées sur ce qui est disponible.

3. **Exécuter workflow génération tâches** :
   - Charger plan.md et extraire stack technique, bibliothèques, structure projet
   - Charger spec.md et extraire user stories avec leurs priorités (P1, P2, P3, etc.)
   - Si data-model.md existe : Extraire entités et mapper aux user stories
   - Si contracts/ existe : Mapper endpoints aux user stories
   - Si research.md existe : Extraire décisions pour tâches setup
   - Générer tâches organisées par user story (voir Règles Génération Tâches ci-dessous)
   - Générer graphe dépendances montrant ordre complétion user story
   - Créer exemples exécution parallèle par user story
   - Valider complétude tâches (chaque user story a toutes les tâches nécessaires, testable indépendamment)

4. **Générer tasks.md** : Utiliser `.specify/templates/tasks-template.md` comme structure, remplir avec :
   - Nom fonctionnalité correct depuis plan.md
   - Phase 1 : Tâches setup (initialisation projet)
   - Phase 2 : Tâches fondamentales (prérequis bloquants pour toutes user stories)
   - Phase 3+ : Une phase par user story (en ordre priorité depuis spec.md)
   - Chaque phase inclut : objectif story, critères test indépendants, tests (si demandés), tâches implémentation
   - Phase Finale : Polish & préoccupations transversales
   - Toutes les tâches doivent suivre le format checklist strict (voir Règles Génération Tâches ci-dessous)
   - Chemins fichiers clairs pour chaque tâche
   - Section dépendances montrant ordre complétion story
   - Exemples exécution parallèle par story
   - Section stratégie implémentation (MVP d'abord, livraison incrémentale)

5. **Rapport** : Produire chemin vers tasks.md généré et résumé :
   - Nombre total tâches
   - Nombre tâches par user story
   - Opportunités parallèles identifiées
   - Critères test indépendants pour chaque story
   - Scope MVP suggéré (typiquement juste User Story 1)
   - Validation format : Confirmer TOUTES les tâches suivent le format checklist (checkbox, ID, libellés, chemins fichiers)

Contexte pour génération tâches : $ARGUMENTS

Le tasks.md doit être immédiatement exécutable - chaque tâche doit être suffisamment spécifique pour qu'un LLM puisse la compléter sans contexte additionnel.

## Règles Génération Tâches

**CRITIQUE** : Les tâches DOIVENT être organisées par user story pour permettre implémentation et test indépendants.

**Les tests sont OPTIONNELS** : Ne générer tâches test que si explicitement demandé dans la spécification fonctionnalité ou si l'utilisateur demande approche TDD.

### Format Checklist (REQUIS)

Chaque tâche DOIT strictement suivre ce format :

```text
- [ ] [TaskID] [P?] [Story?] Description avec chemin fichier
```

**Composants Format** :

1. **Checkbox** : TOUJOURS commencer par `- [ ]` (checkbox markdown)
2. **ID Tâche** : Numéro séquentiel (T001, T002, T003...) en ordre d'exécution
3. **Marqueur [P]** : Inclure UNIQUEMENT si tâche est parallélisable (fichiers différents, pas de dépendances sur tâches incomplètes)
4. **Libellé [Story]** : REQUIS pour tâches phase user story uniquement
   - Format : [US1], [US2], [US3], etc. (mappe aux user stories de spec.md)
   - Phase setup : PAS de libellé story
   - Phase fondamentale : PAS de libellé story
   - Phases User Story : DOIT avoir libellé story
   - Phase polish : PAS de libellé story
5. **Description** : Action claire avec chemin fichier exact

**Exemples** :

- ✅ CORRECT : `- [ ] T001 Créer structure projet selon plan implémentation`
- ✅ CORRECT : `- [ ] T005 [P] Implémenter middleware authentification dans src/middleware/auth.py`
- ✅ CORRECT : `- [ ] T012 [P] [US1] Créer modèle User dans src/models/user.py`
- ✅ CORRECT : `- [ ] T014 [US1] Implémenter UserService dans src/services/user_service.py`
- ❌ FAUX : `- [ ] Créer modèle User` (manque ID et libellé Story)
- ❌ FAUX : `T001 [US1] Créer modèle` (manque checkbox)
- ❌ FAUX : `- [ ] [US1] Créer modèle User` (manque ID Tâche)
- ❌ FAUX : `- [ ] T001 [US1] Créer modèle` (manque chemin fichier)

### Organisation Tâches

1. **Depuis User Stories (spec.md)** - ORGANISATION PRIMAIRE :
   - Chaque user story (P1, P2, P3...) obtient sa propre phase
   - Mapper tous les composants liés à leur story :
     - Modèles nécessaires pour cette story
     - Services nécessaires pour cette story
     - Endpoints/UI nécessaires pour cette story
     - Si tests demandés : Tests spécifiques à cette story
   - Marquer dépendances story (la plupart des stories doivent être indépendantes)

2. **Depuis Contrats** :
   - Mapper chaque contrat/endpoint → à la user story qu'il sert
   - Si tests demandés : Chaque contrat → tâche test contrat [P] avant implémentation dans phase de cette story

3. **Depuis Modèle Données** :
   - Mapper chaque entité aux user story(ies) qui en ont besoin
   - Si entité sert plusieurs stories : Mettre dans story la plus tôt ou phase Setup
   - Relations → tâches couche service dans phase story appropriée

4. **Depuis Setup/Infrastructure** :
   - Infrastructure partagée → Phase Setup (Phase 1)
   - Tâches fondamentales/bloquantes → Phase Fondamentale (Phase 2)
   - Setup spécifique story → dans phase de cette story

### Structure Phases

- **Phase 1** : Setup (initialisation projet)
- **Phase 2** : Fondamentale (prérequis bloquants - DOIT compléter avant user stories)
- **Phase 3+** : User Stories en ordre priorité (P1, P2, P3...)
  - Dans chaque story : Tests (si demandés) → Modèles → Services → Endpoints → Intégration
  - Chaque phase doit être un incrément complet, testable indépendamment
- **Phase Finale** : Polish & Préoccupations Transversales
