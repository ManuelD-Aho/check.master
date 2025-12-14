---
description: Execute the CheckMaster implementation plan by processing and executing all tasks defined in tasks.md (PHP 8.0+ MVC++)
---

## User Input

```text
$ARGUMENTS
```

You **MUST** consider the user input before proceeding (if not empty).

## CheckMaster Implementation Standards (MANDATORY)

### Code Quality Gates - Every Task Must Pass

**PHP Code Style**:
```bash
# Must pass before considering task complete
composer run fix       # PHP-CS-Fixer (PSR-12)
composer run stan      # PHPStan level 6+
composer run test      # PHPUnit (if tests exist)
```

**Mandatory Patterns**:
1. **Strict Types**: Every PHP file starts with `<?php\n\ndeclare(strict_types=1);`
2. **Type Hints**: 100% typed (parameters, returns, properties)
3. **Request Wrapper**: Use `Request` class, never `$_POST`/`$_GET`
4. **Escaping**: Use `e()` helper for output, never raw `echo`
5. **SQL**: Prepared statements only, NO string concatenation
6. **Audit**: Call `ServiceAudit::log()` for all write operations
7. **Hashids**: Use for all entity IDs in URLs
8. **Permissions**: Check via `ServicePermission::verifier()` before actions
9. **Transactions**: Use for multi-table operations
10. **Exceptions**: Typed exceptions (ValidationException, NotFoundException, etc.)

### CheckMaster Code Templates

**Controller Template**:
```php
<?php

declare(strict_types=1);

namespace App\Controllers\{Module};

use App\Services\{Module}\Service{Feature};
use App\Validators\{Feature}Validator;
use Src\Http\Request;
use Src\Http\JsonResponse;
use Src\Exceptions\ValidationException;

class {Feature}Controller
{
    public function __construct(
        private Service{Feature} $service{Feature}
    ) {}

    public function action(int $id): JsonResponse
    {
        // 1. Get data
        $data = Request::all();
        
        // 2. Validate
        $validator = new {Feature}Validator();
        $errors = $validator->validate($data);
        if ($errors) {
            throw new ValidationException($errors);
        }
        
        // 3. Call service (business logic + audit + notifications)
        $result = $this->service{Feature}->action($id, $data);
        
        // 4. Return response
        return JsonResponse::success($result, 'Action completed');
    }
}
```

**Service Template**:
```php
<?php

declare(strict_types=1);

namespace App\Services\{Module};

use App\Models\{Entity};
use App\Services\Core\ServiceAudit;
use App\Services\Core\ServiceNotification;
use App\Services\Core\ServiceWorkflow;
use Src\Database\DB;
use Src\Exceptions\NotFoundException;

class Service{Feature}
{
    public function action(int $id, array $data): mixed
    {
        // 1. Load entity
        $entity = {Entity}::find($id);
        if (!$entity) {
            throw new NotFoundException('{Entity} not found');
        }
        
        // 2. Business logic
        DB::beginTransaction();
        try {
            // Perform operations
            $entity->field = $data['field'];
            $entity->save();
            
            // 3. Audit trail
            ServiceAudit::log('Action performed', '{entity}', $id, [
                'before' => $snapshotBefore,
                'after' => $entity->toArray()
            ]);
            
            // 4. Workflow (if applicable)
            if ($workflowChange) {
                ServiceWorkflow::effectuerTransition($dossierId, 'transition_code', Auth::id());
            }
            
            // 5. Notifications (if applicable)
            ServiceNotification::envoyer('template_code', $destinataires, $variables);
            
            DB::commit();
            return $entity;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

**Model Template**:
```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

class {Entity} extends Model
{
    protected string $table = '{table_name}';
    protected string $primaryKey = 'id_{table_name}';
    
    protected array $fillable = [
        'field1',
        'field2',
    ];
    
    protected array $casts = [
        'json_field' => 'json',
        'bool_field' => 'boolean',
        'date_field' => 'datetime',
    ];
    
    // Relations
    public function relatedEntity(): ?RelatedEntity
    {
        return $this->belongsTo(RelatedEntity::class, 'fk_id');
    }
}
```

**Validator Template**:
```php
<?php

declare(strict_types=1);

namespace App\Validators;

use Symfony\Component\Validator\Constraints as Assert;

class {Feature}Validator
{
    public function rules(): array
    {
        return [
            'field' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
            'email' => [
                new Assert\Email(),
            ],
        ];
    }
    
    public function validate(array $data): array
    {
        $validator = \Src\Validation\ValidatorFactory::create();
        $violations = $validator->validate($data, new Assert\Collection($this->rules()));
        
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return $errors;
        }
        
        return [];
    }
}
```

**Migration Template**:
```sql
-- Migration: 0XX_description.sql
-- Date: YYYY-MM-DD
-- Purpose: [Description]

-- Create table
CREATE TABLE IF NOT EXISTS table_name (
    id_table_name INT PRIMARY KEY AUTO_INCREMENT,
    field1 VARCHAR(255) NOT NULL,
    field2 TEXT,
    field3_json JSON,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_field1 (field1),
    INDEX idx_actif (actif),
    
    -- Foreign keys
    CONSTRAINT fk_table_other FOREIGN KEY (other_id) 
        REFERENCES other_table(id_other_table) 
        ON DELETE RESTRICT
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert into migrations tracking
INSERT INTO migrations (migration_name, executed_at) 
VALUES ('0XX_description', NOW());
```

### CheckMaster-Specific Implementations

**Workflow Integration**:
```php
// Check current state
$dossier = DossierEtudiant::where(['etudiant_id' => $etudiantId])->first();
if ($dossier->etat_actuel->code_etat !== 'expected_state') {
    throw new ForbiddenException('Invalid workflow state');
}

// Perform transition
ServiceWorkflow::effectuerTransition(
    $dossier->id_dossier,
    'transition_code',
    Auth::id(),
    'Optional comment'
);
```

**Permission Checking**:
```php
// In Controller or Middleware
if (!ServicePermission::verifier(Auth::id(), 'resource_code', 'action_code')) {
    throw new ForbiddenException('Permission denied');
}
```

**Notification Sending**:
```php
ServiceNotification::envoyer('template_code', [
    'destinataires' => [$userId1, $userId2],
    'variables' => [
        'nom' => $etudiant->nom_etu,
        'date' => date('d/m/Y'),
        'lien' => url('/path')
    ]
]);
```

**PDF Generation**:
```php
// Simple document (TCPDF)
$pdf = ServicePdf::generer('recu_paiement', [
    'etudiant' => $etudiant,
    'montant' => $montant,
    'date' => date('d/m/Y')
]);

// Complex document (mPDF)
$pdf = ServicePdf::genererAvance('rapport_commission', [
    'session' => $session,
    'rapports' => $rapports,
    'membres' => $membres
], true); // true = mPDF

// Archive with integrity
$hash = hash('sha256', $pdf);
Archive::create([
    'type_document' => 'recu',
    'contenu' => $pdf,
    'hash_sha256' => $hash,
    'entite_type' => 'paiement',
    'entite_id' => $paiementId
]);
```

**Configuration Access**:
```php
// Read config
$smtpHost = ServiceParametres::get('notify.email.smtp_host', 'localhost');
$delaiMax = ServiceParametres::get('workflow.sla.scolarite_days', 5);

// Write config (admin only)
ServiceParametres::set('workflow.escalade.enabled', true);
```

## Outline

1. Run `.specify/scripts/powershell/check-prerequisites.ps1 -Json -RequireTasks -IncludeTasks` from repo root and parse FEATURE_DIR and AVAILABLE_DOCS list. All paths must be absolute. For single quotes in args like "I'm Groot", use escape syntax: e.g 'I'\''m Groot' (or double-quote if possible: "I'm Groot").

2. **Check checklists status** (if FEATURE_DIR/checklists/ exists):
   - Scan all checklist files in the checklists/ directory
   - For each checklist, count:
     - Total items: All lines matching `- [ ]` or `- [X]` or `- [x]`
     - Completed items: Lines matching `- [X]` or `- [x]`
     - Incomplete items: Lines matching `- [ ]`
   - Create a status table:

     ```text
     | Checklist | Total | Completed | Incomplete | Status |
     |-----------|-------|-----------|------------|--------|
     | ux.md     | 12    | 12        | 0          | ✓ PASS |
     | test.md   | 8     | 5         | 3          | ✗ FAIL |
     | security.md | 6   | 6         | 0          | ✓ PASS |
     ```

   - Calculate overall status:
     - **PASS**: All checklists have 0 incomplete items
     - **FAIL**: One or more checklists have incomplete items

   - **If any checklist is incomplete**:
     - Display the table with incomplete item counts
     - **STOP** and ask: "Some checklists are incomplete. Do you want to proceed with implementation anyway? (yes/no)"
     - Wait for user response before continuing
     - If user says "no" or "wait" or "stop", halt execution
     - If user says "yes" or "proceed" or "continue", proceed to step 3

   - **If all checklists are complete**:
     - Display the table showing all checklists passed
     - Automatically proceed to step 3

3. Load and analyze the implementation context:
   - **REQUIRED**: Read tasks.md for the complete task list and execution plan
   - **REQUIRED**: Read plan.md for tech stack, architecture, and file structure
   - **IF EXISTS**: Read data-model.md for entities and relationships
   - **IF EXISTS**: Read contracts/ for API specifications and test requirements
   - **IF EXISTS**: Read research.md for technical decisions and constraints
   - **IF EXISTS**: Read quickstart.md for integration scenarios

4. **Project Setup Verification**:
   - **REQUIRED**: Create/verify ignore files based on actual project setup:

   **Detection & Creation Logic**:
   - Check if the following command succeeds to determine if the repository is a git repo (create/verify .gitignore if so):

     ```sh
     git rev-parse --git-dir 2>/dev/null
     ```

   - Check if Dockerfile* exists or Docker in plan.md → create/verify .dockerignore
   - Check if .eslintrc* exists → create/verify .eslintignore
   - Check if eslint.config.* exists → ensure the config's `ignores` entries cover required patterns
   - Check if .prettierrc* exists → create/verify .prettierignore
   - Check if .npmrc or package.json exists → create/verify .npmignore (if publishing)
   - Check if terraform files (*.tf) exist → create/verify .terraformignore
   - Check if .helmignore needed (helm charts present) → create/verify .helmignore

   **If ignore file already exists**: Verify it contains essential patterns, append missing critical patterns only
   **If ignore file missing**: Create with full pattern set for detected technology

   **Common Patterns by Technology** (from plan.md tech stack):
   - **Node.js/JavaScript/TypeScript**: `node_modules/`, `dist/`, `build/`, `*.log`, `.env*`
   - **Python**: `__pycache__/`, `*.pyc`, `.venv/`, `venv/`, `dist/`, `*.egg-info/`
   - **Java**: `target/`, `*.class`, `*.jar`, `.gradle/`, `build/`
   - **C#/.NET**: `bin/`, `obj/`, `*.user`, `*.suo`, `packages/`
   - **Go**: `*.exe`, `*.test`, `vendor/`, `*.out`
   - **Ruby**: `.bundle/`, `log/`, `tmp/`, `*.gem`, `vendor/bundle/`
   - **PHP**: `vendor/`, `*.log`, `*.cache`, `*.env`
   - **Rust**: `target/`, `debug/`, `release/`, `*.rs.bk`, `*.rlib`, `*.prof*`, `.idea/`, `*.log`, `.env*`
   - **Kotlin**: `build/`, `out/`, `.gradle/`, `.idea/`, `*.class`, `*.jar`, `*.iml`, `*.log`, `.env*`
   - **C++**: `build/`, `bin/`, `obj/`, `out/`, `*.o`, `*.so`, `*.a`, `*.exe`, `*.dll`, `.idea/`, `*.log`, `.env*`
   - **C**: `build/`, `bin/`, `obj/`, `out/`, `*.o`, `*.a`, `*.so`, `*.exe`, `Makefile`, `config.log`, `.idea/`, `*.log`, `.env*`
   - **Swift**: `.build/`, `DerivedData/`, `*.swiftpm/`, `Packages/`
   - **R**: `.Rproj.user/`, `.Rhistory`, `.RData`, `.Ruserdata`, `*.Rproj`, `packrat/`, `renv/`
   - **Universal**: `.DS_Store`, `Thumbs.db`, `*.tmp`, `*.swp`, `.vscode/`, `.idea/`

   **Tool-Specific Patterns**:
   - **Docker**: `node_modules/`, `.git/`, `Dockerfile*`, `.dockerignore`, `*.log*`, `.env*`, `coverage/`
   - **ESLint**: `node_modules/`, `dist/`, `build/`, `coverage/`, `*.min.js`
   - **Prettier**: `node_modules/`, `dist/`, `build/`, `coverage/`, `package-lock.json`, `yarn.lock`, `pnpm-lock.yaml`
   - **Terraform**: `.terraform/`, `*.tfstate*`, `*.tfvars`, `.terraform.lock.hcl`
   - **Kubernetes/k8s**: `*.secret.yaml`, `secrets/`, `.kube/`, `kubeconfig*`, `*.key`, `*.crt`

5. Parse tasks.md structure and extract:
   - **Task phases**: Setup, Tests, Core, Integration, Polish
   - **Task dependencies**: Sequential vs parallel execution rules
   - **Task details**: ID, description, file paths, parallel markers [P]
   - **Execution flow**: Order and dependency requirements

6. Execute implementation following the task plan:
   - **Phase-by-phase execution**: Complete each phase before moving to the next
   - **Respect dependencies**: Run sequential tasks in order, parallel tasks [P] can run together  
   - **Follow TDD approach**: Execute test tasks before their corresponding implementation tasks
   - **File-based coordination**: Tasks affecting the same files must run sequentially
   - **Validation checkpoints**: Verify each phase completion before proceeding

7. Implementation execution rules:
   - **Setup first**: Initialize project structure, dependencies, configuration
   - **Tests before code**: If you need to write tests for contracts, entities, and integration scenarios
   - **Core development**: Implement models, services, CLI commands, endpoints
   - **Integration work**: Database connections, middleware, logging, external services
   - **Polish and validation**: Unit tests, performance optimization, documentation

8. Progress tracking and error handling:
   - Report progress after each completed task
   - Halt execution if any non-parallel task fails
   - For parallel tasks [P], continue with successful tasks, report failed ones
   - Provide clear error messages with context for debugging
   - Suggest next steps if implementation cannot proceed
   - **IMPORTANT** For completed tasks, make sure to mark the task off as [X] in the tasks file.

9. Completion validation:
   - Verify all required tasks are completed
   - Check that implemented features match the original specification
   - Validate that tests pass and coverage meets requirements
   - Confirm the implementation follows the technical plan
   - Report final status with summary of completed work

Note: This command assumes a complete task breakdown exists in tasks.md. If tasks are incomplete or missing, suggest running `/speckit.tasks` first to regenerate the task list.
