---
description: Convert existing CheckMaster tasks into actionable, dependency-ordered GitHub issues based on available design artifacts.
tools: ['github/github-mcp-server/issue_write']
---

## User Input

```text
$ARGUMENTS
```

You **MUST** consider the user input before proceeding (if not empty).

## CheckMaster Issue Template Guidelines

When creating GitHub issues from CheckMaster tasks, use these templates:

### Standard Task Issue Template

```markdown
## Task: [Task Description]

**Task ID**: T0XX  
**Phase**: [Setup/Core/Interface/Integration/Quality]  
**User Story**: [USN] (if applicable)  
**Parallel**: [Yes/No]  
**Priority**: [P1/P2/P3]

### Description
[Brief description of what needs to be implemented]

### Acceptance Criteria
- [ ] [Specific deliverable 1]
- [ ] [Specific deliverable 2]
- [ ] [Specific deliverable 3]

### Technical Details
**Files to Create/Modify**:
- `[file path 1]`
- `[file path 2]`

**Dependencies**:
- [ ] Task T0XX must complete first
- [ ] Depends on Service/Model/Table

**CheckMaster Standards**:
- [ ] Strict types declared (`declare(strict_types=1);`)
- [ ] 100% type hints (parameters, returns, properties)
- [ ] ServiceAudit logging (if write operation)
- [ ] ServicePermission check (if restricted)
- [ ] Prepared statements (no raw SQL)
- [ ] e() escaping in views
- [ ] Hashids in URLs
- [ ] PHPStan level 6+ passes
- [ ] PHP-CS-Fixer (PSR-12) passes

### Related
- Spec: [link to spec.md section]
- Plan: [link to plan.md section]
- Parent Issue: #XX (if part of epic)

### Labels
`task`, `phase-[phase]`, `[module]`, `[priority]`
```

### Database Migration Issue Template

```markdown
## Migration: [Table/Feature Name]

**Task ID**: T0XX  
**Migration Number**: 0XX  
**Type**: [Create Table/Alter Table/Add Seed Data]

### Description
[What database changes are being made]

### Migration Details
**File**: `database/migrations/0XX_description.sql`

**Tables Affected**:
- [table_name]: [CREATE/ALTER/SEED]

**Changes**:
- Add table `[name]` with columns [list]
- Add FK to `[table]`([column])
- Add indexes on [columns]

### Acceptance Criteria
- [ ] Migration file created with sequential number
- [ ] Table naming follows snake_case convention
- [ ] Primary key named `id_tablename`
- [ ] Foreign keys include ON DELETE RESTRICT
- [ ] Indexes added for FK and search columns
- [ ] Migration entry added to migrations table
- [ ] Migration runs successfully on clean DB
- [ ] Migration is idempotent (can run multiple times)

### Rollback Plan
[Describe how to revert if needed]

### Labels
`database`, `migration`, `phase-setup`
```

### Service Implementation Issue Template

```markdown
## Service: Service[Name]

**Task ID**: T0XX  
**Service**: `App\Services\[Module]\Service[Name]`  
**User Story**: [USN]

### Description
Implement business logic for [feature description]

### Acceptance Criteria
- [ ] Service class created at `app/Services/[Module]/Service[Name].php`
- [ ] Constructor DI for dependencies
- [ ] Public methods with full type hints
- [ ] PHPDoc on all public methods
- [ ] ServiceAudit logging for writes
- [ ] Transactions for multi-table operations
- [ ] Exception handling (typed exceptions)
- [ ] Stateless implementation (no properties storing state)

### Methods to Implement
```php
public function methodName(Type $param): ReturnType;
```

**Business Rules**:
- [Rule 1]
- [Rule 2]

**Integrations**:
- ServiceWorkflow (if workflow changes)
- ServiceNotification (if notifications)
- ServicePermission (if access checks)
- ServiceAudit (if data writes)

### Testing
- [ ] Unit test created at `tests/Unit/Services/Service[Name]Test.php`
- [ ] Mock dependencies
- [ ] Test happy path
- [ ] Test error scenarios
- [ ] Test transaction rollback

### Labels
`service`, `business-logic`, `user-story-[N]`, `[priority]`
```

### Controller Implementation Issue Template

```markdown
## Controller: [Name]Controller

**Task ID**: T0XX  
**Controller**: `App\Controllers\[Module]\[Name]Controller`  
**User Story**: [USN]

### Description
Handle HTTP requests for [feature description]

### Acceptance Criteria
- [ ] Controller created at `app/Controllers/[Module]/[Name]Controller.php`
- [ ] Constructor DI for Service
- [ ] Methods ≤50 lines
- [ ] Validation + Service + Response pattern only
- [ ] JsonResponse or View returns
- [ ] Request wrapper (never $_POST/$_GET)
- [ ] PermissionMiddleware applied
- [ ] Hashids routing configured

### Methods to Implement
```php
public function action(int $id): JsonResponse;
```

**Responsibilities**:
1. Get data from Request
2. Validate via Validator
3. Call Service method
4. Return JsonResponse

**Routes**:
- `POST /[module]/{hash}/[action]`
- `GET /[module]/{hash}/[action]`

### Permissions
- **Traitement**: [ID]
- **Action**: [Consulter/Créer/Modifier/Supprimer]
- **Groups**: [List of groupe_utilisateur IDs]

### Labels
`controller`, `http`, `user-story-[N]`, `[priority]`
```

### Workflow Integration Issue Template

```markdown
## Workflow: [Transition Name]

**Task ID**: T0XX  
**Transition**: [état_source] → [état_cible]  
**User Story**: [USN]

### Description
Implement workflow state transition for [feature]

### Acceptance Criteria
- [ ] État added to workflow_etats table
- [ ] Transition added to workflow_transitions table
- [ ] ServiceWorkflow::effectuerTransition called
- [ ] Transition conditions validated
- [ ] workflow_historique snapshot recorded
- [ ] Notifications triggered
- [ ] Permission check enforced
- [ ] Gate conditions verified

### Workflow Details
**Source State**: `[état_source]`  
**Target State**: `[état_cible]`  
**Transition Code**: `[transition_code]`

**Conditions**:
- [Condition 1]
- [Condition 2]

**Triggers**:
- [What action triggers this transition]

**Side Effects**:
- Update [related entity]
- Notify [user groups]
- Generate [document]

### Notifications
- Template: `[template_code]`
- Recipients: [user groups/roles]
- Channels: Email, Messagerie interne

### Labels
`workflow`, `state-machine`, `user-story-[N]`, `[priority]`
```

### Issue Labeling Strategy

**Standard Labels**:
- `task` - Regular implementation task
- `database` - Database migration/seed
- `service` - Service layer implementation
- `controller` - Controller implementation
- `workflow` - Workflow/state machine
- `notification` - Notification/communication
- `document` - PDF generation/archiving
- `security` - Security-related task
- `permission` - Permission/access control
- `financial` - Payment/pénalité features

**Phase Labels**:
- `phase-setup` - Infrastructure setup
- `phase-foundational` - Blocking prerequisites
- `phase-core` - Core business logic
- `phase-interface` - UI/Controllers
- `phase-integration` - Service integration
- `phase-quality` - Testing/QA

**Priority Labels**:
- `P1` - Must have (MVP)
- `P2` - Should have
- `P3` - Nice to have

**Module Labels**:
- `scolarite` - Scolarité module
- `commission` - Commission module
- `communication` - Communication module
- `soutenance` - Soutenance/Jury module
- `etudiant` - Student features
- `admin` - Administration

**User Story Labels**:
- `user-story-1` - US1 tasks
- `user-story-2` - US2 tasks
- etc.

## Outline

1. Run `.specify/scripts/powershell/check-prerequisites.ps1 -Json -RequireTasks -IncludeTasks` from repo root and parse FEATURE_DIR and AVAILABLE_DOCS list. All paths must be absolute. For single quotes in args like "I'm Groot", use escape syntax: e.g 'I'\''m Groot' (or double-quote if possible: "I'm Groot").
1. From the executed script, extract the path to **tasks**.
1. Get the Git remote by running:

```bash
git config --get remote.origin.url
```

> [!CAUTION]
> ONLY PROCEED TO NEXT STEPS IF THE REMOTE IS A GITHUB URL

1. For each task in the list, use the GitHub MCP server to create a new issue in the repository that is representative of the Git remote.

> [!CAUTION]
> UNDER NO CIRCUMSTANCES EVER CREATE ISSUES IN REPOSITORIES THAT DO NOT MATCH THE REMOTE URL
