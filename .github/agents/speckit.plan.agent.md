---
description: Execute the implementation planning workflow using the plan template to generate design artifacts for CheckMaster.
handoffs: 
  - label: Create Tasks
    agent: speckit.tasks
    prompt: Break the plan into tasks for CheckMaster (PHP 8.0+ native MVC++, MySQL, DB-Driven)
    send: true
  - label: Create Checklist
    agent: speckit.checklist
    prompt: Create a CheckMaster checklist for the following domain...
---

## User Input

```text
$ARGUMENTS
```

You **MUST** consider the user input before proceeding (if not empty).

## CheckMaster Architecture Constraints (NON-NEGOTIABLE)

### Stack Technique Imposée
- **PHP**: 8.0+ strict types (`declare(strict_types=1);` mandatory)
- **Database**: MySQL 8.0+ / MariaDB 10.5+
- **Framework**: NONE - Native MVC++ architecture
- **Dependencies**: Whitelist only (~12MB total):
  - hashids/hashids
  - symfony/validator
  - symfony/http-foundation
  - symfony/cache
  - mpdf/mpdf
  - tecnickcom/tcpdf
  - phpoffice/phpspreadsheet
  - phpmailer/phpmailer
  - monolog/monolog
- **Environment**: Windows (dev) + Linux mutualisé LWS (prod)
- **NO Node.js, NO Redis required, NO heavy frameworks**

### Architecture Layers (Strict Separation)
```
Request → Router (Hashids) → Middleware Pipeline → Controller → Service → Model → Database
```

**Controller Rules**:
- Max 50 lines per method
- Validation + Service call + Response ONLY
- ZERO business logic
- Use Request wrapper (never $_POST/$_GET)
- Return JsonResponse or View

**Service Rules**:
- Stateless, pure business logic
- Constructor dependency injection
- Must call ServiceAudit for write operations
- Transactional for multi-table operations
- Testable (mockable dependencies)

**Model Rules**:
- Extend App\Orm\Model
- Property types declared
- Relations defined
- No business logic

### Database Principles
1. **DB-Driven Everything**: Configuration, permissions, workflows, menus, templates ALL in database
2. **67 Tables Schema**: Use existing structure, add migrations for new tables
3. **Naming**: `snake_case` tables/columns, Primary Key always `id_tablename`
4. **Foreign Keys**: Explicit with ON DELETE RESTRICT
5. **Indexes**: FK + search columns + fulltext where needed
6. **Migrations**: Sequential numbered files `database/migrations/0XX_description.sql`
7. **Never modify existing migrations** - always create new ones

### Security Mandates
- **Passwords**: Argon2id only
- **SQL**: Prepared statements mandatory
- **URLs**: Hashids for all entity IDs (`/{module}/{hash}`)
- **Output**: `e()` escaping helper for all views
- **CSRF**: Tokens on all forms
- **Rate Limiting**: On auth/sensitive endpoints
- **Audit**: Double logging (Monolog file + pister table)

### CheckMaster-Specific Patterns

**Workflow Integration**:
- Every feature touching candidature/rapport must consider workflow states
- Use ServiceWorkflow::effectuerTransition() for state changes
- Define allowed transitions in workflow_transitions table
- Update workflow_historique with snapshots

**Permission Checks**:
- Check via ServicePermission::verifier($userId, $ressource, $action)
- Cache permissions (5 min TTL, invalidate on changes)
- Respect groupe_utilisateur → traitement → action mappings
- Support rôles temporaires (président jury day-of access)

**Notification Pattern**:
- Use ServiceNotification::envoyer($template, $destinataires, $variables)
- Multi-canal: Email primary + Messagerie interne backup
- Track bounces → retry logic
- Templates stored in notification_templates table

**Document Generation**:
- Simple documents: TCPDF
- Complex documents (CSS3): mPDF  
- Calculate SHA256 hash on generation
- Store in archives with hash for integrity
- Templates in `ressources/templates/pdf/`

**Configuration Access**:
- ServiceParametres::get('cle.config', $default)
- Cache configuration (invalidate on admin changes)
- ~170 parameters organized by prefix (workflow.*, notify.*, etc.)

## Outline

1. **Setup**: Run `.specify/scripts/powershell/setup-plan.ps1 -Json` from repo root and parse JSON for FEATURE_SPEC, IMPL_PLAN, SPECS_DIR, BRANCH. For single quotes in args like "I'm Groot", use escape syntax: e.g 'I'\''m Groot' (or double-quote if possible: "I'm Groot").

2. **Load context**: Read FEATURE_SPEC and `.specify/memory/constitution.md`. Load IMPL_PLAN template (already copied).

3. **Execute plan workflow**: Follow the structure in IMPL_PLAN template to:
   - Fill Technical Context (mark unknowns as "NEEDS CLARIFICATION")
   - Fill Constitution Check section from constitution
   - Evaluate gates (ERROR if violations unjustified)
   - Phase 0: Generate research.md (resolve all NEEDS CLARIFICATION)
   - Phase 1: Generate data-model.md, contracts/, quickstart.md
   - Phase 1: Update agent context by running the agent script
   - Re-evaluate Constitution Check post-design

4. **Stop and report**: Command ends after Phase 2 planning. Report branch, IMPL_PLAN path, and generated artifacts.

## Phases

### Phase 0: Outline & Research

1. **Extract unknowns from Technical Context** above:
   - For each NEEDS CLARIFICATION → research task
   - For each dependency → best practices task
   - For each integration → patterns task

2. **Generate and dispatch research agents**:

   ```text
   For each unknown in Technical Context:
     Task: "Research {unknown} for {feature context}"
   For each technology choice:
     Task: "Find best practices for {tech} in {domain}"
   ```

3. **Consolidate findings** in `research.md` using format:
   - Decision: [what was chosen]
   - Rationale: [why chosen]
   - Alternatives considered: [what else evaluated]

**Output**: research.md with all NEEDS CLARIFICATION resolved

### Phase 1: Design & Contracts

**Prerequisites:** `research.md` complete

1. **Extract entities from feature spec** → `data-model.md`:
   - Entity name, fields, relationships
   - Validation rules from requirements
   - State transitions if applicable

2. **Generate API contracts** from functional requirements:
   - For each user action → endpoint
   - Use standard REST/GraphQL patterns
   - Output OpenAPI/GraphQL schema to `/contracts/`

3. **Agent context update**:
   - Run `.specify/scripts/powershell/update-agent-context.ps1 -AgentType copilot`
   - These scripts detect which AI agent is in use
   - Update the appropriate agent-specific context file
   - Add only new technology from current plan
   - Preserve manual additions between markers

**Output**: data-model.md, /contracts/*, quickstart.md, agent-specific file

## Key rules

- Use absolute paths
- ERROR on gate failures or unresolved clarifications
- **CheckMaster Constitution Check**: Every plan MUST verify:
  - [ ] No Node.js/NPM dependencies
  - [ ] No logic in Controllers (max 50 lines)
  - [ ] Hashids used for all public IDs
  - [ ] ServiceAudit called for write operations
  - [ ] ServicePermission checked before actions
  - [ ] Workflows use ServiceWorkflow transitions
  - [ ] Documents calculate SHA256 hashes
  - [ ] Configuration via ServiceParametres (DB)
  - [ ] Notifications via ServiceNotification
  - [ ] All SQL uses prepared statements
  - [ ] Passwords use Argon2id
  - [ ] Views use `e()` escaping
  - [ ] New tables have sequential migrations
  - [ ] Services are stateless and testable

### CheckMaster Tables Reference (67 tables)

**Core Authentication & Users**:
- utilisateurs, sessions_actives, codes_temporaires
- groupes, utilisateurs_groupes, roles_temporaires
- ressources, permissions, permissions_cache

**Academic Entities**:
- etudiants, enseignants, personnel_admin
- entreprises, specialites, grades, fonctions
- annee_academique, semestre, niveau_etude, ue, ecue

**Workflow & Process**:
- workflow_etats, workflow_transitions, workflow_historique, workflow_alertes
- dossiers_etudiants, candidatures, rapports_etudiants
- sessions_commission, votes_commission, annotations_rapport
- jury_membres, soutenances, notes_soutenance
- escalades, escalades_actions, escalade_niveaux

**Financial**:
- paiements, penalites, exonerations

**Communications**:
- notification_templates, notifications_queue, notifications_historique
- email_bounces, messages_internes

**Documents & Archives**:
- documents_generes, archives, historique_entites
- critere_evaluation, mentions, decisions_jury

**Configuration & Audit**:
- configuration_systeme, traitement, action, rattacher
- pister (audit trail)

**Référentiels**:
- roles_jury, statut_jury, salles
- type_utilisateur, groupe_utilisateur, niveau_acces_donnees
- niveau_approbation

### Data Model Template (data-model.md)

```markdown
# Data Model: [Feature Name]

## Tables Impacted

### Existing Tables Modified
- **Table**: `table_name`
  - **Changes**: Add column `new_column VARCHAR(255)`
  - **Migration**: `database/migrations/0XX_add_new_column.sql`
  - **Reason**: [Why needed for feature]

### New Tables Created
#### `new_table_name`
```sql
CREATE TABLE new_table_name (
    id_new_table INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(100) NOT NULL,
    description TEXT,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Purpose**: [Table purpose]
**Relationships**: 
- FK to `autre_table(id_autre)`
**Constraints**: [Business rules]

## Entity Relationships

```
[Draw relationships using ASCII or describe]
```

## Workflow States (if applicable)

- **Current State**: `state_name`
- **New States**: `new_state_1`, `new_state_2`
- **Transitions**:
  - `state_1` → `state_2` (Trigger: [event], Condition: [rule])

## Data Validation Rules

- Field `x`: Required, max 100 chars, alphanumeric
- Field `y`: Optional, must match `/regex/`
- Field `z`: Foreign key must exist in table_ref
```

### Contracts Template (contracts/)

For each Controller endpoint:

```php
/**
 * @route POST /module/{hash}/action
 * @middleware AuthMiddleware, PermissionMiddleware
 * @permission traitement_id=XX, action_id=YY
 * 
 * @param array $data Request body
 * @return JsonResponse
 * 
 * @throws ValidationException Invalid input
 * @throws NotFoundException Entity not found
 * @throws ForbiddenException Permission denied
 */
public function actionName(array $data): JsonResponse;
```

### Research Artifacts (research.md)

Document technical decisions:

```markdown
# Research: [Feature Name]

## Decision 1: [Topic]
**Chosen**: [Solution]
**Rationale**: [Why chosen over alternatives]
**Alternatives Considered**: 
- Option A: [Pros/Cons]
- Option B: [Pros/Cons]
**Impact**: [What this affects]

## Integration Points
- Service X: [How feature integrates]
- Table Y: [Data dependencies]

## Unknowns Resolved
- Question: [Original uncertainty]
- Resolution: [How resolved]
```
