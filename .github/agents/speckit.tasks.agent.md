---
description: Generate an actionable, dependency-ordered tasks.md for CheckMaster features based on available design artifacts.
handoffs: 
  - label: Analyze For Consistency
    agent: speckit.analyze
    prompt: Run a CheckMaster project analysis for consistency
    send: true
  - label: Implement Project
    agent: speckit.implement
    prompt: Start the CheckMaster implementation in phases (PHP 8.0+ MVC++)
    send: true
---

## User Input

```text
$ARGUMENTS
```

You **MUST** consider the user input before proceeding (if not empty).

## CheckMaster Task Generation Context

### Development Cycle Strict Order
1. **Database First**: Migrations → Seeds
2. **Core (TDD if requested)**: Tests → Models → Services  
3. **Interface**: Validators → Controllers → Routes → Views
4. **Integration**: Permissions → Notifications → Audit → Documents
5. **Quality**: PHPStan → PHP-CS-Fixer → PHPUnit

### CheckMaster File Structure

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
    └── Model.php (base class)

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

### CheckMaster-Specific Task Patterns

**For Workflow Features**:
```markdown
### Phase X: User Story N - [Workflow State Change]
- [ ] T0XX [USN] Add workflow state to workflow_etats table (migration)
- [ ] T0XX [USN] Add transitions to workflow_transitions table (migration)
- [ ] T0XX [P] [USN] Update ServiceWorkflow to handle new state
- [ ] T0XX [USN] Add transition validation in {Feature}Validator
- [ ] T0XX [USN] Implement state change in Service{Feature}
- [ ] T0XX [USN] Add audit logging for state transitions
- [ ] T0XX [P] [USN] Create notification template for transition
- [ ] T0XX [USN] Test workflow integration
```

**For Permission-Protected Features**:
```markdown
### Phase X: User Story N - [Protected Action]
- [ ] T0XX [USN] Add traitement entry in traitement table (seed)
- [ ] T0XX [USN] Add action entry in action table (seed)
- [ ] T0XX [USN] Link to groupes in rattacher table (seed)
- [ ] T0XX [P] [USN] Implement permission check in controller
- [ ] T0XX [USN] Add PermissionMiddleware to route
- [ ] T0XX [USN] Test permission denial scenarios
```

**For Document Generation**:
```markdown
### Phase X: User Story N - [Document Type]
- [ ] T0XX [USN] Create PDF template in ressources/templates/pdf/
- [ ] T0XX [USN] Add document type to documents_generes config
- [ ] T0XX [P] [USN] Implement generation in ServicePdf
- [ ] T0XX [USN] Calculate and store SHA256 hash
- [ ] T0XX [USN] Archive document with integrity check
- [ ] T0XX [P] [USN] Send notification with download link
- [ ] T0XX [USN] Test PDF generation and archiving
```

**For CRUD Operations**:
```markdown
### Phase X: User Story N - [Entity CRUD]
- [ ] T0XX [USN] Create entity table (migration)
- [ ] T0XX [P] [USN] Create Model in app/Models/{Entity}.php
- [ ] T0XX [P] [USN] Create Validator in app/Validators/{Entity}Validator.php
- [ ] T0XX [USN] Implement Service{Entity} with CRUD methods
- [ ] T0XX [USN] Add audit logging for write operations
- [ ] T0XX [USN] Create Controller with Hashids routing
- [ ] T0XX [P] [USN] Create views (index, create, edit)
- [ ] T0XX [USN] Test CRUD operations end-to-end
```

**For Commission/Voting Features**:
```markdown
### Phase X: User Story N - [Vote/Commission]
- [ ] T0XX [USN] Add session to sessions_commission (if new)
- [ ] T0XX [USN] Add vote tracking table (migration)
- [ ] T0XX [P] [USN] Implement vote logic with round limit (3 max)
- [ ] T0XX [USN] Check unanimity calculation
- [ ] T0XX [USN] Add escalation trigger on round 3 failure
- [ ] T0XX [P] [USN] Send notifications for vote rounds
- [ ] T0XX [USN] Generate PV with signatures
- [ ] T0XX [USN] Test vote scenarios (unanimity, escalation)
```

**For Financial Operations**:
```markdown
### Phase X: User Story N - [Payment/Pénalité]
- [ ] T0XX [USN] Add financial record table (if needed)
- [ ] T0XX [P] [USN] Implement calculation logic in Service{Finance}
- [ ] T0XX [USN] Validate payment status in workflow gate
- [ ] T0XX [USN] Generate reçu PDF with TCPDF
- [ ] T0XX [USN] Archive reçu with SHA256
- [ ] T0XX [P] [USN] Send confirmation email
- [ ] T0XX [USN] Update student financial dashboard
- [ ] T0XX [USN] Test payment flow and reçu generation
```

### CheckMaster Quality Gates

After each User Story phase, include verification tasks:

```markdown
### Phase X+1: User Story N - Quality Assurance
- [ ] T0XX [P] [USN] Run PHPStan level 6+ analysis
- [ ] T0XX [P] [USN] Run PHP-CS-Fixer for PSR-12
- [ ] T0XX [P] [USN] Verify ServiceAudit calls on writes
- [ ] T0XX [P] [USN] Check Hashids usage in URLs
- [ ] T0XX [P] [USN] Verify prepared statements (no raw SQL)
- [ ] T0XX [P] [USN] Test permission checks
- [ ] T0XX [P] [USN] Validate e() escaping in views
- [ ] T0XX [USN] Integration test for complete user flow
```

## Outline

1. **Setup**: Run `.specify/scripts/powershell/check-prerequisites.ps1 -Json` from repo root and parse FEATURE_DIR and AVAILABLE_DOCS list. All paths must be absolute. For single quotes in args like "I'm Groot", use escape syntax: e.g 'I'\''m Groot' (or double-quote if possible: "I'm Groot").

2. **Load design documents**: Read from FEATURE_DIR:
   - **Required**: plan.md (tech stack, libraries, structure), spec.md (user stories with priorities)
   - **Optional**: data-model.md (entities), contracts/ (API endpoints), research.md (decisions), quickstart.md (test scenarios)
   - Note: Not all projects have all documents. Generate tasks based on what's available.

3. **Execute task generation workflow**:
   - Load plan.md and extract tech stack, libraries, project structure
   - Load spec.md and extract user stories with their priorities (P1, P2, P3, etc.)
   - If data-model.md exists: Extract entities and map to user stories
   - If contracts/ exists: Map endpoints to user stories
   - If research.md exists: Extract decisions for setup tasks
   - Generate tasks organized by user story (see Task Generation Rules below)
   - Generate dependency graph showing user story completion order
   - Create parallel execution examples per user story
   - Validate task completeness (each user story has all needed tasks, independently testable)

4. **Generate tasks.md**: Use `.specify/templates/tasks-template.md` as structure, fill with:
   - Correct feature name from plan.md
   - Phase 1: Setup tasks (project initialization)
   - Phase 2: Foundational tasks (blocking prerequisites for all user stories)
   - Phase 3+: One phase per user story (in priority order from spec.md)
   - Each phase includes: story goal, independent test criteria, tests (if requested), implementation tasks
   - Final Phase: Polish & cross-cutting concerns
   - All tasks must follow the strict checklist format (see Task Generation Rules below)
   - Clear file paths for each task
   - Dependencies section showing story completion order
   - Parallel execution examples per story
   - Implementation strategy section (MVP first, incremental delivery)

5. **Report**: Output path to generated tasks.md and summary:
   - Total task count
   - Task count per user story
   - Parallel opportunities identified
   - Independent test criteria for each story
   - Suggested MVP scope (typically just User Story 1)
   - Format validation: Confirm ALL tasks follow the checklist format (checkbox, ID, labels, file paths)

Context for task generation: $ARGUMENTS

The tasks.md should be immediately executable - each task must be specific enough that an LLM can complete it without additional context.

## Task Generation Rules

**CRITICAL**: Tasks MUST be organized by user story to enable independent implementation and testing.

**Tests are OPTIONAL**: Only generate test tasks if explicitly requested in the feature specification or if user requests TDD approach.

### Checklist Format (REQUIRED)

Every task MUST strictly follow this format:

```text
- [ ] [TaskID] [P?] [Story?] Description with file path
```

**Format Components**:

1. **Checkbox**: ALWAYS start with `- [ ]` (markdown checkbox)
2. **Task ID**: Sequential number (T001, T002, T003...) in execution order
3. **[P] marker**: Include ONLY if task is parallelizable (different files, no dependencies on incomplete tasks)
4. **[Story] label**: REQUIRED for user story phase tasks only
   - Format: [US1], [US2], [US3], etc. (maps to user stories from spec.md)
   - Setup phase: NO story label
   - Foundational phase: NO story label  
   - User Story phases: MUST have story label
   - Polish phase: NO story label
5. **Description**: Clear action with exact file path

**Examples**:

- ✅ CORRECT: `- [ ] T001 Create project structure per implementation plan`
- ✅ CORRECT: `- [ ] T005 [P] Implement authentication middleware in src/middleware/auth.py`
- ✅ CORRECT: `- [ ] T012 [P] [US1] Create User model in src/models/user.py`
- ✅ CORRECT: `- [ ] T014 [US1] Implement UserService in src/services/user_service.py`
- ❌ WRONG: `- [ ] Create User model` (missing ID and Story label)
- ❌ WRONG: `T001 [US1] Create model` (missing checkbox)
- ❌ WRONG: `- [ ] [US1] Create User model` (missing Task ID)
- ❌ WRONG: `- [ ] T001 [US1] Create model` (missing file path)

### Task Organization

1. **From User Stories (spec.md)** - PRIMARY ORGANIZATION:
   - Each user story (P1, P2, P3...) gets its own phase
   - Map all related components to their story:
     - Models needed for that story
     - Services needed for that story
     - Endpoints/UI needed for that story
     - If tests requested: Tests specific to that story
   - Mark story dependencies (most stories should be independent)

2. **From Contracts**:
   - Map each contract/endpoint → to the user story it serves
   - If tests requested: Each contract → contract test task [P] before implementation in that story's phase

3. **From Data Model**:
   - Map each entity to the user story(ies) that need it
   - If entity serves multiple stories: Put in earliest story or Setup phase
   - Relationships → service layer tasks in appropriate story phase

4. **From Setup/Infrastructure**:
   - Shared infrastructure → Setup phase (Phase 1)
   - Foundational/blocking tasks → Foundational phase (Phase 2)
   - Story-specific setup → within that story's phase

### Phase Structure

- **Phase 1**: Setup (project initialization)
- **Phase 2**: Foundational (blocking prerequisites - MUST complete before user stories)
- **Phase 3+**: User Stories in priority order (P1, P2, P3...)
  - Within each story: Tests (if requested) → Models → Services → Endpoints → Integration
  - Each phase should be a complete, independently testable increment
- **Final Phase**: Polish & Cross-Cutting Concerns
