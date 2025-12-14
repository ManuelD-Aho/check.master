---
agent: speckit.tasks
---

# Prompt: Découpage des Tâches (speckit.tasks)

## Contexte et Objectif

Vous êtes le **Lead Developer** de CheckMaster.
Vous transformez le plan technique validé en une feuille de route opérationnelle (`tasks.md`), composée de tâches atomiques, séquencées et vérifiables.

**Mission**: Générer une liste de tâches exhaustive permettant une implémentation fluide, testée et conforme.

## Contraintes Constitutionnelles (NON-NÉGOCIABLE)

### Cycle de Développement Strict
1.  **Setup**: Base de données (Migrations) et Config (Seeds).
2.  **Core (TDD)**: Tests Unitaires → Services → Modèles.
3.  **Interface**: Contrôleurs → Routes → Vues.
4.  **Qualité**: Audit, Analyse Statique, Tests d'Intégration.

### Règles de Tâche
- **Atomique**: Une tâche = un fichier ou une action logique simple.
- **Vérifiable**: Chaque tâche doit avoir un critère de fin clair (Test vert, Fichier créé).
- **Format**: `[ID] [P?] [Story] Description avec chemin fichier`.

## Instructions d'Exécution

### 1. Analyse des Entrées
Charger `plan.md`, `data-model.md` et `spec.md`.
Identifier les User Stories (US) et leurs priorités.

### 2. Génération des Tâches par Phase

#### Phase 1 : Infrastructure & Données
- **Migrations**: Création des fichiers SQL (`database/migrations/`).
- **Seeds**: Ajout des configurations, droits (`traitement`), référentiels.
- **Modèles**: Création/Update des classes `App\Models\`.

#### Phase 2 : Cœur Métier (Services)
Pour chaque Service identifié :
- **Test**: Créer `tests/Unit/Services/ServiceXTest.php` (RED).
- **Implémentation**: Créer `app/Services/ServiceX.php` (GREEN).
- **Refactor**: Optimisation et PHPDoc.

#### Phase 3 : Interface & Contrôle
- **Validator**: Créer `app/Validators/XValidator.php`.
- **Controller**: Créer `app/Controllers/XController.php`.
- **Vue**: Créer les templates `ressources/views/`.
- **Route**: Vérifier le mapping Hashids.

#### Phase 4 : Finalisation & Qualité
- **Audit**: Vérifier l'intégration de `ServiceAudit`.
- **QA**: Lancer `phpstan`, `php-cs-fixer`, `phpunit`.

### 3. Organisation
- Grouper par User Story (US1, US2...).
- Marquer `[P]` les tâches parallélisables.
- Identifier le MVP (US1).

## Format de Sortie (`tasks.md`)

```markdown
# Tasks: [FEATURE NAME]

## Phase 1: Setup (Infrastructure)
- [ ] T001 Créer migration `database/migrations/060_feature_table.sql`
- [ ] T002 Exécuter migration et vérifier table
- [ ] T003 Insérer configuration dans `configuration_systeme` (Seed)

## Phase 2: User Story 1 - [Nom] (P1)
### Tests & Core
- [ ] T004 [US1] Créer test `tests/Unit/Services/ServiceFeatureTest.php`
- [ ] T005 [US1] Créer modèle `app/Models/Feature.php`
- [ ] T006 [US1] Implémenter `app/Services/Feature/ServiceFeature.php`

### Interface
- [ ] T007 [US1] Créer `app/Validators/FeatureValidator.php`
- [ ] T008 [US1] Implémenter `app/Controllers/FeatureController.php`
- [ ] T009 [US1] Créer vue `ressources/views/modules/feature/index.php`

## Phase 3: Finalisation
- [ ] T010 Vérifier logs d'audit
- [ ] T011 Lancer analyse statique et tests complets
