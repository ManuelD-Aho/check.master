---
description: Create or update the CheckMaster project constitution from interactive or provided principle inputs, ensuring all dependent templates stay in sync.
handoffs: 
  - label: Build Specification
    agent: speckit.specify
    prompt: Implement the feature specification based on the CheckMaster constitution (PHP 8.0+ MVC++, MySQL, DB-Driven). I want to build...
---

## User Input

```text
$ARGUMENTS
```

You **MUST** consider the user input before proceeding (if not empty).

## CheckMaster Constitution Context

### Existing Constitution Pillars (NON-NEGOTIABLE)

The CheckMaster constitution at `.specify/memory/constitution.md` establishes these core principles that **MUST NOT** be violated:

1. **Database-Driven Architecture**: All configuration, permissions, workflows, and menus in database (not PHP files)
2. **Single Source of Truth**: Each system element has exactly one authoritative data source
3. **Sécurité Par Défaut**: Deny-all permissions, Argon2id passwords, prepared statements, Hashids routing
4. **Séparation des Responsabilités**: Strict MVC++ with Controllers (≤50 lines), Services (business logic), Models (ORM)
5. **Convention Over Configuration**: PSR-12, strict naming (PascalCase classes, camelCase methods, snake_case DB)
6. **Auditabilité Totale**: Double logging (Monolog + pister table) with before/after snapshots
7. **Versioning Strict**: Sequential migrations, historique_entites for rollback

### Stack Constraints (IMMUTABLE)

**Allowed**:
- PHP 8.0+ (strict types mandatory)
- MySQL 8.0+ / MariaDB 10.5+
- 12 approved dependencies (~12MB total):
  - hashids, symfony/validator, symfony/http-foundation, symfony/cache
  - mpdf, tcpdf, phpoffice/phpspreadsheet, phpmailer, monolog

**Forbidden**:
- Laravel/Symfony Full Stack
- Node.js, Redis, Memcached as required dependencies
- Any framework exceeding 50MB
- Raw SQL queries (must use prepared statements)
- Logic in Controllers (must be in Services)
- Hardcoded permissions/config (must be DB-driven)

### CheckMaster-Specific Rules

**Workflow Management**:
- All states in workflow_etats table
- Transitions in workflow_transitions table
- ServiceWorkflow::effectuerTransition for all state changes
- Workflow gates block progress until conditions met

**Permission System**:
- 13 user groups (Administrateur, Scolarité, Commission, Étudiant, etc.)
- traitement → action → rattacher mappings
- ServicePermission::verifier before restricted operations
- Rôles temporaires (président jury day-of access)

**Document Generation**:
- 13 PDF types (reçus, PV, bulletins, attestations, etc.)
- TCPDF for simple, mPDF for complex CSS layouts
- SHA256 integrity hashing mandatory
- Archive with verificat ion périodique

**Notification System**:
- 71 email templates for workflow transitions
- Multi-channel: Email (primary) + Messagerie interne (backup)
- ServiceNotification::envoyer with template code
- Bounce tracking and retry logic

**Configuration**:
- ~170 parameters in configuration_systeme table
- Organized by prefix (workflow.*, notify.*, finance.*, etc.)
- ServiceParametres::get/set for access
- 27 désactivable features via config flags

**Financial Operations**:
- Paiements, pénalités, exonérations tables
- Reçu generation with TCPDF
- Financial gates in workflow (block if unpaid)
- Configuration-driven amounts and rules

### Amendment Guidelines for CheckMaster

When updating the constitution, respect these guidelines:

**Version Bumping**:
- **MAJOR**: Changing core architecture (DB-driven → file-based) - FORBIDDEN for CheckMaster
- **MINOR**: Adding new mandatory service (e.g., ServiceReclamation)
- **PATCH**: Clarifying existing principles, fixing typos

**Principle Addition Criteria**:
- Must address recurring cross-cutting concern
- Must be testable/verifiable in code review
- Must not contradict existing pillars
- Must apply broadly (not feature-specific)

**Consistency Propagation Required**:
After constitution updates, verify:
- `.specify/templates/plan-template.md` (Constitution Check section)
- `.specify/templates/spec-template.md` (scope requirements)
- `.specify/templates/tasks-template.md` (task types)
- `.github/prompts/*.md` (agent instructions)
- `.github/agents/*.md` (agent behaviors)

## Outline

You are updating the project constitution at `.specify/memory/constitution.md`. This file is a TEMPLATE containing placeholder tokens in square brackets (e.g. `[PROJECT_NAME]`, `[PRINCIPLE_1_NAME]`). Your job is to (a) collect/derive concrete values, (b) fill the template precisely, and (c) propagate any amendments across dependent artifacts.

Follow this execution flow:

1. Load the existing constitution template at `.specify/memory/constitution.md`.
   - Identify every placeholder token of the form `[ALL_CAPS_IDENTIFIER]`.
   **IMPORTANT**: The user might require less or more principles than the ones used in the template. If a number is specified, respect that - follow the general template. You will update the doc accordingly.

2. Collect/derive values for placeholders:
   - If user input (conversation) supplies a value, use it.
   - Otherwise infer from existing repo context (README, docs, prior constitution versions if embedded).
   - For governance dates: `RATIFICATION_DATE` is the original adoption date (if unknown ask or mark TODO), `LAST_AMENDED_DATE` is today if changes are made, otherwise keep previous.
   - `CONSTITUTION_VERSION` must increment according to semantic versioning rules:
     - MAJOR: Backward incompatible governance/principle removals or redefinitions.
     - MINOR: New principle/section added or materially expanded guidance.
     - PATCH: Clarifications, wording, typo fixes, non-semantic refinements.
   - If version bump type ambiguous, propose reasoning before finalizing.

3. Draft the updated constitution content:
   - Replace every placeholder with concrete text (no bracketed tokens left except intentionally retained template slots that the project has chosen not to define yet—explicitly justify any left).
   - Preserve heading hierarchy and comments can be removed once replaced unless they still add clarifying guidance.
   - Ensure each Principle section: succinct name line, paragraph (or bullet list) capturing non‑negotiable rules, explicit rationale if not obvious.
   - Ensure Governance section lists amendment procedure, versioning policy, and compliance review expectations.

4. Consistency propagation checklist (convert prior checklist into active validations):
   - Read `.specify/templates/plan-template.md` and ensure any "Constitution Check" or rules align with updated principles.
   - Read `.specify/templates/spec-template.md` for scope/requirements alignment—update if constitution adds/removes mandatory sections or constraints.
   - Read `.specify/templates/tasks-template.md` and ensure task categorization reflects new or removed principle-driven task types (e.g., observability, versioning, testing discipline).
   - Read each command file in `.specify/templates/commands/*.md` (including this one) to verify no outdated references (agent-specific names like CLAUDE only) remain when generic guidance is required.
   - Read any runtime guidance docs (e.g., `README.md`, `docs/quickstart.md`, or agent-specific guidance files if present). Update references to principles changed.

5. Produce a Sync Impact Report (prepend as an HTML comment at top of the constitution file after update):
   - Version change: old → new
   - List of modified principles (old title → new title if renamed)
   - Added sections
   - Removed sections
   - Templates requiring updates (✅ updated / ⚠ pending) with file paths
   - Follow-up TODOs if any placeholders intentionally deferred.

6. Validation before final output:
   - No remaining unexplained bracket tokens.
   - Version line matches report.
   - Dates ISO format YYYY-MM-DD.
   - Principles are declarative, testable, and free of vague language ("should" → replace with MUST/SHOULD rationale where appropriate).

7. Write the completed constitution back to `.specify/memory/constitution.md` (overwrite).

8. Output a final summary to the user with:
   - New version and bump rationale.
   - Any files flagged for manual follow-up.
   - Suggested commit message (e.g., `docs: amend constitution to vX.Y.Z (principle additions + governance update)`).

Formatting & Style Requirements:

- Use Markdown headings exactly as in the template (do not demote/promote levels).
- Wrap long rationale lines to keep readability (<100 chars ideally) but do not hard enforce with awkward breaks.
- Keep a single blank line between sections.
- Avoid trailing whitespace.

If the user supplies partial updates (e.g., only one principle revision), still perform validation and version decision steps.

If critical info missing (e.g., ratification date truly unknown), insert `TODO(<FIELD_NAME>): explanation` and include in the Sync Impact Report under deferred items.

Do not create a new template; always operate on the existing `.specify/memory/constitution.md` file.
