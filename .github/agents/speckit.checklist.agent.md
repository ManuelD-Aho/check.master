---
description: Generate a custom checklist for the current CheckMaster feature based on user requirements - validates requirement quality, not implementation.
---

## Checklist Purpose: "Unit Tests for English"

**CRITICAL CONCEPT**: Checklists are **UNIT TESTS FOR REQUIREMENTS WRITING** - they validate the quality, clarity, and completeness of requirements in a given domain.

**NOT for verification/testing**:

- ❌ NOT "Verify the button clicks correctly"
- ❌ NOT "Test error handling works"
- ❌ NOT "Confirm the API returns 200"
- ❌ NOT checking if code/implementation matches the spec

**FOR requirements quality validation**:

- ✅ "Are workflow state transitions defined for all candidature operations?" (completeness)
- ✅ "Is 'validated by scolarité' quantified with specific criteria?" (clarity)
- ✅ "Are permission requirements consistent across all admin actions?" (consistency)
- ✅ "Are Hashids routing requirements defined for all entity URLs?" (coverage)
- ✅ "Does the spec define what happens when ServiceWorkflow transition fails?" (edge cases)

**Metaphor**: If your spec is code written in English, the checklist is its unit test suite. You're testing whether the requirements are well-written, complete, unambiguous, and ready for implementation - NOT whether the implementation works.

## CheckMaster Domain-Specific Checklist Categories

When generating checklists for CheckMaster features, consider these standard categories:

### 1. Architecture Compliance Checklist (architecture.md)

Validates spec adherence to CheckMaster Constitution:

```markdown
- [ ] CHK001 Are all configuration requirements defined as DB parameters (not PHP constants)?
- [ ] CHK002 Are all permission checks specified using groupe_utilisateur → traitement → action mappings?
- [ ] CHK003 Are all entity IDs specified to use Hashids in URLs?
- [ ] CHK004 Is Argon2id specified for any password handling?
- [ ] CHK005 Are all SQL operations specified to use prepared statements?
- [ ] CHK006 Is ServiceAudit logging specified for all write operations?
- [ ] CHK007 Are Controller responsibilities limited to validation + service + response?
- [ ] CHK008 Is business logic specified to reside in Services (not Controllers)?
- [ ] CHK009 Are transactions specified for multi-table operations?
- [ ] CHK010 Is dependency injection specified via constructor?
```

### 2. Workflow Checklist (workflow.md)

Validates workflow-related requirements:

```markdown
- [ ] CHK001 Are all affected workflow states explicitly listed?
- [ ] CHK002 Is the source state defined for each transition?
- [ ] CHK003 Is the target state defined for each transition?
- [ ] CHK004 Are transition conditions specified (who can trigger, when)?
- [ ] CHK005 Are workflow gates clearly defined (what blocks transition)?
- [ ] CHK006 Is ServiceWorkflow::effectuerTransition specified for state changes?
- [ ] CHK007 Are notification requirements defined for each transition?
- [ ] CHK008 Is workflow_historique snapshot specified for audit?
- [ ] CHK009 Are escalation scenarios defined for délai dépassés?
- [ ] CHK010 Is rollback behavior specified if transition fails?
```

### 3. Permission & Access Checklist (permissions.md)

Validates access control requirements:

```markdown
- [ ] CHK001 Are all user groups needing access explicitly listed?
- [ ] CHK002 Is each group mapped to a traitement entry?
- [ ] CHK003 Are required actions (Consulter/Créer/Modifier/etc.) specified per group?
- [ ] CHK004 Is ServicePermission::verifier specified before restricted actions?
- [ ] CHK005 Are temporary roles defined with validity periods (if applicable)?
- [ ] CHK006 Is fallback behavior specified for permission denial?
- [ ] CHK007 Are permission cache invalidation rules defined?
- [ ] CHK008 Is the resource code (traitement) clearly identified?
- [ ] CHK009 Are admin override capabilities specified (if any)?
- [ ] CHK010 Is audit logging specified for permission grants/revocations?
```

### 4. Notification Checklist (notifications.md)

Validates communication requirements:

```markdown
- [ ] CHK001 Are all notification triggers clearly defined?
- [ ] CHK002 Is the notification template code specified?
- [ ] CHK003 Are recipient roles/groups explicitly listed?
- [ ] CHK004 Are email subject and body placeholders defined?
- [ ] CHK005 Is messagerie interne backup specified?
- [ ] CHK006 Is ServiceNotification::envoyer usage specified?
- [ ] CHK007 Are notification variables (dynamic data) listed?
- [ ] CHK008 Is bounce handling behavior specified?
- [ ] CHK009 Are retry rules defined for failed sends?
- [ ] CHK010 Is notification historique archiving specified?
```

### 5. Document Generation Checklist (documents.md)

Validates PDF generation requirements:

```markdown
- [ ] CHK001 Is document type clearly identified (reçu, PV, bulletin, etc.)?
- [ ] CHK002 Is PDF engine (TCPDF vs mPDF) specified based on complexity?
- [ ] CHK003 Are all document data requirements (variables) listed?
- [ ] CHK004 Is PDF template location specified (ressources/templates/pdf/)?
- [ ] CHK005 Is SHA256 hash calculation specified for archiving?
- [ ] CHK006 Is archive table storage (archives) specified?
- [ ] CHK007 Are download permissions (who can access) defined?
- [ ] CHK008 Is regeneration capability specified (from snapshots)?
- [ ] CHK009 Are document integrity verification rules defined?
- [ ] CHK010 Is notification of document availability specified?
```

### 6. Financial Operations Checklist (financial.md)

Validates payment/pénalité requirements:

```markdown
- [ ] CHK001 Are calculation rules explicitly defined (formulas, amounts)?
- [ ] CHK002 Is configuration source specified (finance.* parameters)?
- [ ] CHK003 Are payment recording triggers defined?
- [ ] CHK004 Is pénalité calculation logic specified (delays, rates)?
- [ ] CHK005 Is reçu generation specified (TCPDF + archiving)?
- [ ] CHK006 Are financial gate checks defined (workflow blockers)?
- [ ] CHK007 Is payment status tracking specified?
- [ ] CHK008 Is student financial dashboard update specified?
- [ ] CHK009 Are exonération rules defined (if applicable)?
- [ ] CHK010 Is financial audit logging specified?
```

### 7. Commission/Voting Checklist (commission.md)

Validates commission evaluation requirements:

```markdown
- [ ] CHK001 Is voting mechanism specified (unanimité/majorité)?
- [ ] CHK002 Is the 3-round maximum enforced?
- [ ] CHK003 Is escalation to Dean specified after round 3?
- [ ] CHK004 Are vote tracking requirements defined (sessions_commission)?
- [ ] CHK005 Are notification requirements defined per voting round?
- [ ] CHK006 Is PV generation specified (template, signatures)?
- [ ] CHK007 Are member assignment rules defined?
- [ ] CHK008 Is quorum requirement specified?
- [ ] CHK009 Are conflict resolution procedures defined?
- [ ] CHK010 Is vote anonymity/visibility specified?
```

### 8. Data Model Checklist (data.md)

Validates entity/table requirements:

```markdown
- [ ] CHK001 Are all entity names defined in snake_case?
- [ ] CHK002 Is primary key format specified (id_tablename)?
- [ ] CHK003 Are all foreign keys defined with ON DELETE behavior?
- [ ] CHK004 Are required indexes specified (FK, search columns)?
- [ ] CHK005 Are unique constraints defined where needed?
- [ ] CHK006 Are JSON column schemas defined (if using JSON type)?
- [ ] CHK007 Are migration file names specified (0XX_description)?
- [ ] CHK008 Are table relationships clearly documented?
- [ ] CHK009 Is data validation specified (NOT NULL, ranges, formats)?
- [ ] CHK010 Are audit columns specified (created_at, updated_at)?
```

### 9. Security Checklist (security.md)

Validates security requirements:

```markdown
- [ ] CHK001 Is Argon2id specified for password hashing?
- [ ] CHK002 Are prepared statements specified for all SQL?
- [ ] CHK003 Is e() escaping specified for all view output?
- [ ] CHK004 Are CSRF tokens specified for all forms?
- [ ] CHK005 Is rate limiting specified for sensitive endpoints?
- [ ] CHK006 Is input validation specified (Symfony Validator)?
- [ ] CHK007 Is ServiceAudit specified for security-relevant actions?
- [ ] CHK008 Are permission checks specified before data access?
- [ ] CHK009 Is session management specified (timeout, invalidation)?
- [ ] CHK010 Are sensitive data handling rules defined (PII, credentials)?
```

### 10. Integration Checklist (integration.md)

Validates Service integration requirements:

```markdown
- [ ] CHK001 Are all dependent Services explicitly listed?
- [ ] CHK002 Is ServiceWorkflow integration specified (if workflow changes)?
- [ ] CHK003 Is ServiceNotification integration specified (if notifications)?
- [ ] CHK004 Is ServicePermission integration specified (if access control)?
- [ ] CHK005 Is ServiceAudit integration specified (if data writes)?
- [ ] CHK006 Is ServiceParametres integration specified (if configuration)?
- [ ] CHK007 Is ServicePdf integration specified (if documents)?
- [ ] CHK008 Are error handling behaviors defined for Service failures?
- [ ] CHK009 Are retry/fallback strategies specified?
- [ ] CHK010 Is transaction coordination specified across Services?
```

## User Input

```text
$ARGUMENTS
```

You **MUST** consider the user input before proceeding (if not empty).

## Execution Steps

1. **Setup**: Run `.specify/scripts/powershell/check-prerequisites.ps1 -Json` from repo root and parse JSON for FEATURE_DIR and AVAILABLE_DOCS list.
   - All file paths must be absolute.
   - For single quotes in args like "I'm Groot", use escape syntax: e.g 'I'\''m Groot' (or double-quote if possible: "I'm Groot").

2. **Clarify intent (dynamic)**: Derive up to THREE initial contextual clarifying questions (no pre-baked catalog). They MUST:
   - Be generated from the user's phrasing + extracted signals from spec/plan/tasks
   - Only ask about information that materially changes checklist content
   - Be skipped individually if already unambiguous in `$ARGUMENTS`
   - Prefer precision over breadth

   Generation algorithm:
   1. Extract signals: feature domain keywords (e.g., auth, latency, UX, API), risk indicators ("critical", "must", "compliance"), stakeholder hints ("QA", "review", "security team"), and explicit deliverables ("a11y", "rollback", "contracts").
   2. Cluster signals into candidate focus areas (max 4) ranked by relevance.
   3. Identify probable audience & timing (author, reviewer, QA, release) if not explicit.
   4. Detect missing dimensions: scope breadth, depth/rigor, risk emphasis, exclusion boundaries, measurable acceptance criteria.
   5. Formulate questions chosen from these archetypes:
      - Scope refinement (e.g., "Should this include integration touchpoints with X and Y or stay limited to local module correctness?")
      - Risk prioritization (e.g., "Which of these potential risk areas should receive mandatory gating checks?")
      - Depth calibration (e.g., "Is this a lightweight pre-commit sanity list or a formal release gate?")
      - Audience framing (e.g., "Will this be used by the author only or peers during PR review?")
      - Boundary exclusion (e.g., "Should we explicitly exclude performance tuning items this round?")
      - Scenario class gap (e.g., "No recovery flows detected—are rollback / partial failure paths in scope?")

   Question formatting rules:
   - If presenting options, generate a compact table with columns: Option | Candidate | Why It Matters
   - Limit to A–E options maximum; omit table if a free-form answer is clearer
   - Never ask the user to restate what they already said
   - Avoid speculative categories (no hallucination). If uncertain, ask explicitly: "Confirm whether X belongs in scope."

   Defaults when interaction impossible:
   - Depth: Standard
   - Audience: Reviewer (PR) if code-related; Author otherwise
   - Focus: Top 2 relevance clusters

   Output the questions (label Q1/Q2/Q3). After answers: if ≥2 scenario classes (Alternate / Exception / Recovery / Non-Functional domain) remain unclear, you MAY ask up to TWO more targeted follow‑ups (Q4/Q5) with a one-line justification each (e.g., "Unresolved recovery path risk"). Do not exceed five total questions. Skip escalation if user explicitly declines more.

3. **Understand user request**: Combine `$ARGUMENTS` + clarifying answers:
   - Derive checklist theme (e.g., security, review, deploy, ux)
   - Consolidate explicit must-have items mentioned by user
   - Map focus selections to category scaffolding
   - Infer any missing context from spec/plan/tasks (do NOT hallucinate)

4. **Load feature context**: Read from FEATURE_DIR:
   - spec.md: Feature requirements and scope
   - plan.md (if exists): Technical details, dependencies
   - tasks.md (if exists): Implementation tasks

   **Context Loading Strategy**:
   - Load only necessary portions relevant to active focus areas (avoid full-file dumping)
   - Prefer summarizing long sections into concise scenario/requirement bullets
   - Use progressive disclosure: add follow-on retrieval only if gaps detected
   - If source docs are large, generate interim summary items instead of embedding raw text

5. **Generate checklist** - Create "Unit Tests for Requirements":
   - Create `FEATURE_DIR/checklists/` directory if it doesn't exist
   - Generate unique checklist filename:
     - Use short, descriptive name based on domain (e.g., `ux.md`, `api.md`, `security.md`)
     - Format: `[domain].md`
     - If file exists, append to existing file
   - Number items sequentially starting from CHK001
   - Each `/speckit.checklist` run creates a NEW file (never overwrites existing checklists)

   **CORE PRINCIPLE - Test the Requirements, Not the Implementation**:
   Every checklist item MUST evaluate the REQUIREMENTS THEMSELVES for:
   - **Completeness**: Are all necessary requirements present?
   - **Clarity**: Are requirements unambiguous and specific?
   - **Consistency**: Do requirements align with each other?
   - **Measurability**: Can requirements be objectively verified?
   - **Coverage**: Are all scenarios/edge cases addressed?

   **Category Structure** - Group items by requirement quality dimensions:
   - **Requirement Completeness** (Are all necessary requirements documented?)
   - **Requirement Clarity** (Are requirements specific and unambiguous?)
   - **Requirement Consistency** (Do requirements align without conflicts?)
   - **Acceptance Criteria Quality** (Are success criteria measurable?)
   - **Scenario Coverage** (Are all flows/cases addressed?)
   - **Edge Case Coverage** (Are boundary conditions defined?)
   - **Non-Functional Requirements** (Performance, Security, Accessibility, etc. - are they specified?)
   - **Dependencies & Assumptions** (Are they documented and validated?)
   - **Ambiguities & Conflicts** (What needs clarification?)

   **HOW TO WRITE CHECKLIST ITEMS - "Unit Tests for English"**:

   ❌ **WRONG** (Testing implementation):
   - "Verify landing page displays 3 episode cards"
   - "Test hover states work on desktop"
   - "Confirm logo click navigates home"

   ✅ **CORRECT** (Testing requirements quality):
   - "Are the exact number and layout of featured episodes specified?" [Completeness]
   - "Is 'prominent display' quantified with specific sizing/positioning?" [Clarity]
   - "Are hover state requirements consistent across all interactive elements?" [Consistency]
   - "Are keyboard navigation requirements defined for all interactive UI?" [Coverage]
   - "Is the fallback behavior specified when logo image fails to load?" [Edge Cases]
   - "Are loading states defined for asynchronous episode data?" [Completeness]
   - "Does the spec define visual hierarchy for competing UI elements?" [Clarity]

   **ITEM STRUCTURE**:
   Each item should follow this pattern:
   - Question format asking about requirement quality
   - Focus on what's WRITTEN (or not written) in the spec/plan
   - Include quality dimension in brackets [Completeness/Clarity/Consistency/etc.]
   - Reference spec section `[Spec §X.Y]` when checking existing requirements
   - Use `[Gap]` marker when checking for missing requirements

   **EXAMPLES BY QUALITY DIMENSION**:

   Completeness:
   - "Are error handling requirements defined for all API failure modes? [Gap]"
   - "Are accessibility requirements specified for all interactive elements? [Completeness]"
   - "Are mobile breakpoint requirements defined for responsive layouts? [Gap]"

   Clarity:
   - "Is 'fast loading' quantified with specific timing thresholds? [Clarity, Spec §NFR-2]"
   - "Are 'related episodes' selection criteria explicitly defined? [Clarity, Spec §FR-5]"
   - "Is 'prominent' defined with measurable visual properties? [Ambiguity, Spec §FR-4]"

   Consistency:
   - "Do navigation requirements align across all pages? [Consistency, Spec §FR-10]"
   - "Are card component requirements consistent between landing and detail pages? [Consistency]"

   Coverage:
   - "Are requirements defined for zero-state scenarios (no episodes)? [Coverage, Edge Case]"
   - "Are concurrent user interaction scenarios addressed? [Coverage, Gap]"
   - "Are requirements specified for partial data loading failures? [Coverage, Exception Flow]"

   Measurability:
   - "Are visual hierarchy requirements measurable/testable? [Acceptance Criteria, Spec §FR-1]"
   - "Can 'balanced visual weight' be objectively verified? [Measurability, Spec §FR-2]"

   **Scenario Classification & Coverage** (Requirements Quality Focus):
   - Check if requirements exist for: Primary, Alternate, Exception/Error, Recovery, Non-Functional scenarios
   - For each scenario class, ask: "Are [scenario type] requirements complete, clear, and consistent?"
   - If scenario class missing: "Are [scenario type] requirements intentionally excluded or missing? [Gap]"
   - Include resilience/rollback when state mutation occurs: "Are rollback requirements defined for migration failures? [Gap]"

   **Traceability Requirements**:
   - MINIMUM: ≥80% of items MUST include at least one traceability reference
   - Each item should reference: spec section `[Spec §X.Y]`, or use markers: `[Gap]`, `[Ambiguity]`, `[Conflict]`, `[Assumption]`
   - If no ID system exists: "Is a requirement & acceptance criteria ID scheme established? [Traceability]"

   **Surface & Resolve Issues** (Requirements Quality Problems):
   Ask questions about the requirements themselves:
   - Ambiguities: "Is the term 'fast' quantified with specific metrics? [Ambiguity, Spec §NFR-1]"
   - Conflicts: "Do navigation requirements conflict between §FR-10 and §FR-10a? [Conflict]"
   - Assumptions: "Is the assumption of 'always available podcast API' validated? [Assumption]"
   - Dependencies: "Are external podcast API requirements documented? [Dependency, Gap]"
   - Missing definitions: "Is 'visual hierarchy' defined with measurable criteria? [Gap]"

   **Content Consolidation**:
   - Soft cap: If raw candidate items > 40, prioritize by risk/impact
   - Merge near-duplicates checking the same requirement aspect
   - If >5 low-impact edge cases, create one item: "Are edge cases X, Y, Z addressed in requirements? [Coverage]"

   **🚫 ABSOLUTELY PROHIBITED** - These make it an implementation test, not a requirements test:
   - ❌ Any item starting with "Verify", "Test", "Confirm", "Check" + implementation behavior
   - ❌ References to code execution, user actions, system behavior
   - ❌ "Displays correctly", "works properly", "functions as expected"
   - ❌ "Click", "navigate", "render", "load", "execute"
   - ❌ Test cases, test plans, QA procedures
   - ❌ Implementation details (frameworks, APIs, algorithms)

   **✅ REQUIRED PATTERNS** - These test requirements quality:
   - ✅ "Are [requirement type] defined/specified/documented for [scenario]?"
   - ✅ "Is [vague term] quantified/clarified with specific criteria?"
   - ✅ "Are requirements consistent between [section A] and [section B]?"
   - ✅ "Can [requirement] be objectively measured/verified?"
   - ✅ "Are [edge cases/scenarios] addressed in requirements?"
   - ✅ "Does the spec define [missing aspect]?"

6. **Structure Reference**: Generate the checklist following the canonical template in `.specify/templates/checklist-template.md` for title, meta section, category headings, and ID formatting. If template is unavailable, use: H1 title, purpose/created meta lines, `##` category sections containing `- [ ] CHK### <requirement item>` lines with globally incrementing IDs starting at CHK001.

7. **Report**: Output full path to created checklist, item count, and remind user that each run creates a new file. Summarize:
   - Focus areas selected
   - Depth level
   - Actor/timing
   - Any explicit user-specified must-have items incorporated

**Important**: Each `/speckit.checklist` command invocation creates a checklist file using short, descriptive names unless file already exists. This allows:

- Multiple checklists of different types (e.g., `ux.md`, `test.md`, `security.md`)
- Simple, memorable filenames that indicate checklist purpose
- Easy identification and navigation in the `checklists/` folder

To avoid clutter, use descriptive types and clean up obsolete checklists when done.

## Example Checklist Types & Sample Items

**UX Requirements Quality:** `ux.md`

Sample items (testing the requirements, NOT the implementation):

- "Are visual hierarchy requirements defined with measurable criteria? [Clarity, Spec §FR-1]"
- "Is the number and positioning of UI elements explicitly specified? [Completeness, Spec §FR-1]"
- "Are interaction state requirements (hover, focus, active) consistently defined? [Consistency]"
- "Are accessibility requirements specified for all interactive elements? [Coverage, Gap]"
- "Is fallback behavior defined when images fail to load? [Edge Case, Gap]"
- "Can 'prominent display' be objectively measured? [Measurability, Spec §FR-4]"

**API Requirements Quality:** `api.md`

Sample items:

- "Are error response formats specified for all failure scenarios? [Completeness]"
- "Are rate limiting requirements quantified with specific thresholds? [Clarity]"
- "Are authentication requirements consistent across all endpoints? [Consistency]"
- "Are retry/timeout requirements defined for external dependencies? [Coverage, Gap]"
- "Is versioning strategy documented in requirements? [Gap]"

**Performance Requirements Quality:** `performance.md`

Sample items:

- "Are performance requirements quantified with specific metrics? [Clarity]"
- "Are performance targets defined for all critical user journeys? [Coverage]"
- "Are performance requirements under different load conditions specified? [Completeness]"
- "Can performance requirements be objectively measured? [Measurability]"
- "Are degradation requirements defined for high-load scenarios? [Edge Case, Gap]"

**Security Requirements Quality:** `security.md`

Sample items:

- "Are authentication requirements specified for all protected resources? [Coverage]"
- "Are data protection requirements defined for sensitive information? [Completeness]"
- "Is the threat model documented and requirements aligned to it? [Traceability]"
- "Are security requirements consistent with compliance obligations? [Consistency]"
- "Are security failure/breach response requirements defined? [Gap, Exception Flow]"

## Anti-Examples: What NOT To Do

**❌ WRONG - These test implementation, not requirements:**

```markdown
- [ ] CHK001 - Verify landing page displays 3 episode cards [Spec §FR-001]
- [ ] CHK002 - Test hover states work correctly on desktop [Spec §FR-003]
- [ ] CHK003 - Confirm logo click navigates to home page [Spec §FR-010]
- [ ] CHK004 - Check that related episodes section shows 3-5 items [Spec §FR-005]
```

**✅ CORRECT - These test requirements quality:**

```markdown
- [ ] CHK001 - Are the number and layout of featured episodes explicitly specified? [Completeness, Spec §FR-001]
- [ ] CHK002 - Are hover state requirements consistently defined for all interactive elements? [Consistency, Spec §FR-003]
- [ ] CHK003 - Are navigation requirements clear for all clickable brand elements? [Clarity, Spec §FR-010]
- [ ] CHK004 - Is the selection criteria for related episodes documented? [Gap, Spec §FR-005]
- [ ] CHK005 - Are loading state requirements defined for asynchronous episode data? [Gap]
- [ ] CHK006 - Can "visual hierarchy" requirements be objectively measured? [Measurability, Spec §FR-001]
```

**Key Differences:**

- Wrong: Tests if the system works correctly
- Correct: Tests if the requirements are written correctly
- Wrong: Verification of behavior
- Correct: Validation of requirement quality
- Wrong: "Does it do X?"
- Correct: "Is X clearly specified?"
