---
description: Perform a non-destructive cross-artifact consistency and quality analysis across spec.md, plan.md, and tasks.md after task generation for CheckMaster features.
---

## User Input

```text
$ARGUMENTS
```

You **MUST** consider the user input before proceeding (if not empty).

## CheckMaster-Specific Analysis Rules

### Constitution Compliance (CRITICAL Checks)

**Every analysis MUST verify these CheckMaster mandates**:

1. **DB-Driven Architecture**:
   - [ ] Configuration NOT in PHP files (must use configuration_systeme table)
   - [ ] Permissions NOT hardcoded (must use rattacher + permissions tables)
   - [ ] Menus constructed from traitement + rattacher tables
   - [ ] Workflow states in workflow_etats/transitions tables

2. **Security Standards**:
   - [ ] All entity IDs use Hashids in URLs (no raw integers)
   - [ ] Passwords use Argon2id (no md5, sha1, bcrypt)
   - [ ] SQL uses prepared statements (no string concatenation)
   - [ ] Views use `e()` escaping (no raw echo)
   - [ ] All forms have CSRF tokens
   - [ ] ServiceAudit called for write operations

3. **Architecture Layers**:
   - [ ] Controllers max 50 lines (validation + service + response only)
   - [ ] Business logic in Services (not Controllers or Views)
   - [ ] Models extend App\Orm\Model
   - [ ] Dependency injection via constructor
   - [ ] Transactions for multi-table operations

4. **CheckMaster Integration**:
   - [ ] Workflow changes use ServiceWorkflow::effectuerTransition()
   - [ ] Permissions checked via ServicePermission::verifier()
   - [ ] Notifications via ServiceNotification::envoyer()
   - [ ] Configuration via ServiceParametres::get()
   - [ ] PDF generation with SHA256 archiving
   - [ ] Document types match 13 defined types

5. **Database Standards**:
   - [ ] Table names `snake_case` 
   - [ ] Primary key always `id_tablename`
   - [ ] Foreign keys with ON DELETE RESTRICT
   - [ ] Migrations sequential (0XX_description.sql)
   - [ ] Never modify existing migrations
   - [ ] Indexes on FK + search columns

### CheckMaster Domain Validation

**Cross-check these domain patterns**:

**Workflow Consistency**:
```markdown
- [ ] Feature touches candidature/rapport/soutenance?
  - [ ] Spec defines which workflow states involved
  - [ ] Plan maps states to transitions
  - [ ] Tasks include workflow_etats/transitions updates
  - [ ] Tasks call ServiceWorkflow for state changes
  - [ ] Notifications defined for each transition
```

**Permission Mapping**:
```markdown
- [ ] Feature requires user access control?
  - [ ] Spec defines which user groups need access
  - [ ] Plan identifies traitement + action entries
  - [ ] Tasks include seed data for rattacher table
  - [ ] Controllers check ServicePermission
  - [ ] Middleware applies PermissionMiddleware
```

**Document Generation**:
```markdown
- [ ] Feature generates PDF documents?
  - [ ] Spec defines document type (simple/complex)
  - [ ] Plan chooses TCPDF or mPDF appropriately
  - [ ] Tasks create template in ressources/templates/pdf/
  - [ ] Tasks calculate SHA256 hash
  - [ ] Tasks archive with integrity check
  - [ ] Tasks trigger download notification
```

**Financial Operations**:
```markdown
- [ ] Feature involves payments/pénalités?
  - [ ] Spec defines amounts, calculation rules
  - [ ] Plan maps to paiements/penalites tables
  - [ ] Tasks generate reçu PDFs
  - [ ] Tasks archive financial documents
  - [ ] Tasks update student financial status
  - [ ] Gate checks payment status before workflow advance
```

**Commission/Voting**:
```markdown
- [ ] Feature involves commission decisions?
  - [ ] Spec defines vote logic (unanimity/majority)
  - [ ] Plan handles 3-round maximum with escalation
  - [ ] Tasks track votes in sessions_commission
  - [ ] Tasks trigger Dean escalation after round 3
  - [ ] Tasks generate PV documents
  - [ ] Tasks send round notifications
```

### CheckMaster-Specific Findings

**Look for these common issues**:

**CRITICAL Issues**:
- Using Laravel/Symfony Full Stack (CheckMaster is native MVC++)
- Node.js dependencies (CheckMaster is PHP-only)
- Redis/Memcached as required dependencies (only Symfony Cache)
- Raw SQL queries (must use prepared statements)
- Controllers with business logic (>50 lines, complex logic)
- Hardcoded permissions (must be DB-driven)
- Raw integer IDs in URLs (must use Hashids)
- Missing ServiceAudit calls for data writes

**HIGH Issues**:
- Workflow state changes without ServiceWorkflow
- Permission checks missing ServicePermission
- Notifications not using ServiceNotification
- Configuration in PHP files (must use DB)
- PDF without SHA256 archiving
- Modifications to existing migrations
- Missing transaction wrappers for multi-table ops
- Non-typed properties/parameters/returns

**MEDIUM Issues**:
- Inconsistent table naming (not snake_case)
- Missing indexes on search/FK columns
- Direct $_POST/$_GET usage (must use Request wrapper)
- Views with raw echo (must use e() helper)
- Services not stateless (storing state)
- Missing PHPDoc on public methods
- Validator not using Symfony constraints

**LOW Issues**:
- Inconsistent variable naming
- Missing comments on complex logic
- Non-PSR-12 compliant formatting
- Suboptimal query patterns

## Goal

Identify inconsistencies, duplications, ambiguities, and underspecified items across the three core artifacts (`spec.md`, `plan.md`, `tasks.md`) before implementation. This command MUST run only after `/speckit.tasks` has successfully produced a complete `tasks.md`.

## Operating Constraints

**STRICTLY READ-ONLY**: Do **not** modify any files. Output a structured analysis report. Offer an optional remediation plan (user must explicitly approve before any follow-up editing commands would be invoked manually).

**Constitution Authority**: The project constitution (`.specify/memory/constitution.md`) is **non-negotiable** within this analysis scope. Constitution conflicts are automatically CRITICAL and require adjustment of the spec, plan, or tasks—not dilution, reinterpretation, or silent ignoring of the principle. If a principle itself needs to change, that must occur in a separate, explicit constitution update outside `/speckit.analyze`.

## Execution Steps

### 1. Initialize Analysis Context

Run `.specify/scripts/powershell/check-prerequisites.ps1 -Json -RequireTasks -IncludeTasks` once from repo root and parse JSON for FEATURE_DIR and AVAILABLE_DOCS. Derive absolute paths:

- SPEC = FEATURE_DIR/spec.md
- PLAN = FEATURE_DIR/plan.md
- TASKS = FEATURE_DIR/tasks.md

Abort with an error message if any required file is missing (instruct the user to run missing prerequisite command).
For single quotes in args like "I'm Groot", use escape syntax: e.g 'I'\''m Groot' (or double-quote if possible: "I'm Groot").

### 2. Load Artifacts (Progressive Disclosure)

Load only the minimal necessary context from each artifact:

**From spec.md:**

- Overview/Context
- Functional Requirements
- Non-Functional Requirements
- User Stories
- Edge Cases (if present)

**From plan.md:**

- Architecture/stack choices
- Data Model references
- Phases
- Technical constraints

**From tasks.md:**

- Task IDs
- Descriptions
- Phase grouping
- Parallel markers [P]
- Referenced file paths

**From constitution:**

- Load `.specify/memory/constitution.md` for principle validation

### 3. Build Semantic Models

Create internal representations (do not include raw artifacts in output):

- **Requirements inventory**: Each functional + non-functional requirement with a stable key (derive slug based on imperative phrase; e.g., "User can upload file" → `user-can-upload-file`)
- **User story/action inventory**: Discrete user actions with acceptance criteria
- **Task coverage mapping**: Map each task to one or more requirements or stories (inference by keyword / explicit reference patterns like IDs or key phrases)
- **Constitution rule set**: Extract principle names and MUST/SHOULD normative statements

### 4. Detection Passes (Token-Efficient Analysis)

Focus on high-signal findings. Limit to 50 findings total; aggregate remainder in overflow summary.

#### A. Duplication Detection

- Identify near-duplicate requirements
- Mark lower-quality phrasing for consolidation

#### B. Ambiguity Detection

- Flag vague adjectives (fast, scalable, secure, intuitive, robust) lacking measurable criteria
- Flag unresolved placeholders (TODO, TKTK, ???, `<placeholder>`, etc.)

#### C. Underspecification

- Requirements with verbs but missing object or measurable outcome
- User stories missing acceptance criteria alignment
- Tasks referencing files or components not defined in spec/plan

#### D. Constitution Alignment

- Any requirement or plan element conflicting with a MUST principle
- Missing mandated sections or quality gates from constitution

#### E. Coverage Gaps

- Requirements with zero associated tasks
- Tasks with no mapped requirement/story
- Non-functional requirements not reflected in tasks (e.g., performance, security)

#### F. Inconsistency

- Terminology drift (same concept named differently across files)
- Data entities referenced in plan but absent in spec (or vice versa)
- Task ordering contradictions (e.g., integration tasks before foundational setup tasks without dependency note)
- Conflicting requirements (e.g., one requires Next.js while other specifies Vue)

### 5. Severity Assignment

Use this heuristic to prioritize findings:

- **CRITICAL**: Violates constitution MUST, missing core spec artifact, or requirement with zero coverage that blocks baseline functionality
- **HIGH**: Duplicate or conflicting requirement, ambiguous security/performance attribute, untestable acceptance criterion
- **MEDIUM**: Terminology drift, missing non-functional task coverage, underspecified edge case
- **LOW**: Style/wording improvements, minor redundancy not affecting execution order

### 6. Produce Compact Analysis Report

Output a Markdown report (no file writes) with the following structure:

## Specification Analysis Report

| ID | Category | Severity | Location(s) | Summary | Recommendation |
|----|----------|----------|-------------|---------|----------------|
| A1 | Duplication | HIGH | spec.md:L120-134 | Two similar requirements ... | Merge phrasing; keep clearer version |

(Add one row per finding; generate stable IDs prefixed by category initial.)

**Coverage Summary Table:**

| Requirement Key | Has Task? | Task IDs | Notes |
|-----------------|-----------|----------|-------|

**Constitution Alignment Issues:** (if any)

**Unmapped Tasks:** (if any)

**Metrics:**

- Total Requirements
- Total Tasks
- Coverage % (requirements with >=1 task)
- Ambiguity Count
- Duplication Count
- Critical Issues Count

### 7. Provide Next Actions

At end of report, output a concise Next Actions block:

- If CRITICAL issues exist: Recommend resolving before `/speckit.implement`
- If only LOW/MEDIUM: User may proceed, but provide improvement suggestions
- Provide explicit command suggestions: e.g., "Run /speckit.specify with refinement", "Run /speckit.plan to adjust architecture", "Manually edit tasks.md to add coverage for 'performance-metrics'"

### 8. Offer Remediation

Ask the user: "Would you like me to suggest concrete remediation edits for the top N issues?" (Do NOT apply them automatically.)

## Operating Principles

### Context Efficiency

- **Minimal high-signal tokens**: Focus on actionable findings, not exhaustive documentation
- **Progressive disclosure**: Load artifacts incrementally; don't dump all content into analysis
- **Token-efficient output**: Limit findings table to 50 rows; summarize overflow
- **Deterministic results**: Rerunning without changes should produce consistent IDs and counts

### Analysis Guidelines

- **NEVER modify files** (this is read-only analysis)
- **NEVER hallucinate missing sections** (if absent, report them accurately)
- **Prioritize constitution violations** (these are always CRITICAL)
- **Use examples over exhaustive rules** (cite specific instances, not generic patterns)
- **Report zero issues gracefully** (emit success report with coverage statistics)

## Context

$ARGUMENTS
