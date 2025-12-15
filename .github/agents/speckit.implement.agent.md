---
description: Exécuter le plan d'implémentation CheckMaster en traitant et exécutant toutes les tâches définies dans tasks.md (PHP 8.0+ MVC++)
---

## Entrée Utilisateur

```text
$ARGUMENTS
```

Vous **DEVEZ** prendre en compte l'entrée utilisateur avant de procéder (si non vide).

## Standards Implémentation CheckMaster (OBLIGATOIRES)

### Gates Qualité Code - Chaque Tâche Doit Passer

**Style Code PHP** :
```bash
# Doit passer avant de considérer tâche complète
composer run fix       # PHP-CS-Fixer (PSR-12)
composer run stan      # PHPStan niveau 6+
composer run test      # PHPUnit (si tests existent)
```

**Patterns Obligatoires** :
1. **Types Stricts** : Chaque fichier PHP commence par `<?php\n\ndeclare(strict_types=1);`
2. **Type Hints** : 100% typé (paramètres, retours, propriétés)
3. **Wrapper Request** : Utiliser classe `Request`, jamais `$_POST`/`$_GET`
4. **Échappement** : Utiliser helper `e()` pour sortie, jamais `echo` brut
5. **SQL** : Requêtes préparées uniquement, PAS de concaténation chaînes
6. **Audit** : Appeler `ServiceAudit::log()` pour toutes opérations écriture
7. **Hashids** : Utiliser pour tous les IDs entités dans URLs
8. **Permissions** : Vérifier via `ServicePermission::verifier()` avant actions
9. **Transactions** : Utiliser pour opérations multi-tables
10. **Exceptions** : Exceptions typées (ValidationException, NotFoundException, etc.)

### Templates Code CheckMaster

**Template Contrôleur** :
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
        // 1. Obtenir données
        $data = Request::all();
        
        // 2. Valider
        $validator = new {Feature}Validator();
        $errors = $validator->validate($data);
        if ($errors) {
            throw new ValidationException($errors);
        }
        
        // 3. Appeler service (logique métier + audit + notifications)
        $result = $this->service{Feature}->action($id, $data);
        
        // 4. Retourner réponse
        return JsonResponse::success($result, 'Action complétée');
    }
}
```

**Template Service** :
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
        // 1. Charger entité
        $entity = {Entity}::find($id);
        if (!$entity) {
            throw new NotFoundException('{Entity} non trouvé');
        }
        
        // 2. Logique métier
        DB::beginTransaction();
        try {
            // Effectuer opérations
            $entity->field = $data['field'];
            $entity->save();
            
            // 3. Piste audit
            ServiceAudit::log('Action effectuée', '{entity}', $id, [
                'before' => $snapshotBefore,
                'after' => $entity->toArray()
            ]);
            
            // 4. Workflow (si applicable)
            if ($workflowChange) {
                ServiceWorkflow::effectuerTransition($dossierId, 'transition_code', Auth::id());
            }
            
            // 5. Notifications (si applicable)
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

**Template Modèle** :
```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

class {Entity} extends Model
{
    protected string $table = '{nom_table}';
    protected string $primaryKey = 'id_{nom_table}';
    
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

**Template Validateur** :
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

**Template Migration** :
```sql
-- Migration: 0XX_description.sql
-- Date: AAAA-MM-JJ
-- Objectif: [Description]

-- Créer table
CREATE TABLE IF NOT EXISTS nom_table (
    id_nom_table INT PRIMARY KEY AUTO_INCREMENT,
    field1 VARCHAR(255) NOT NULL,
    field2 TEXT,
    field3_json JSON,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Index
    INDEX idx_field1 (field1),
    INDEX idx_actif (actif),
    
    -- Clés étrangères
    CONSTRAINT fk_table_other FOREIGN KEY (other_id) 
        REFERENCES other_table(id_other_table) 
        ON DELETE RESTRICT
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insérer dans suivi migrations
INSERT INTO migrations (migration_name, executed_at) 
VALUES ('0XX_description', NOW());
```

### Implémentations Spécifiques CheckMaster

**Intégration Workflow** :
```php
// Vérifier état actuel
$dossier = DossierEtudiant::where(['etudiant_id' => $etudiantId])->first();
if ($dossier->etat_actuel->code_etat !== 'etat_attendu') {
    throw new ForbiddenException('État workflow invalide');
}

// Effectuer transition
ServiceWorkflow::effectuerTransition(
    $dossier->id_dossier,
    'code_transition',
    Auth::id(),
    'Commentaire optionnel'
);
```

**Vérification Permissions** :
```php
// Dans Contrôleur ou Middleware
if (!ServicePermission::verifier(Auth::id(), 'code_ressource', 'code_action')) {
    throw new ForbiddenException('Permission refusée');
}
```

**Envoi Notifications** :
```php
ServiceNotification::envoyer('code_template', [
    'destinataires' => [$userId1, $userId2],
    'variables' => [
        'nom' => $etudiant->nom_etu,
        'date' => date('d/m/Y'),
        'lien' => url('/chemin')
    ]
]);
```

**Génération PDF** :
```php
// Document simple (TCPDF)
$pdf = ServicePdf::generer('recu_paiement', [
    'etudiant' => $etudiant,
    'montant' => $montant,
    'date' => date('d/m/Y')
]);

// Document complexe (mPDF)
$pdf = ServicePdf::genererAvance('rapport_commission', [
    'session' => $session,
    'rapports' => $rapports,
    'membres' => $membres
], true); // true = mPDF

// Archiver avec intégrité
$hash = hash('sha256', $pdf);
Archive::create([
    'type_document' => 'recu',
    'contenu' => $pdf,
    'hash_sha256' => $hash,
    'entite_type' => 'paiement',
    'entite_id' => $paiementId
]);
```

**Accès Configuration** :
```php
// Lire config
$smtpHost = ServiceParametres::get('notify.email.smtp_host', 'localhost');
$delaiMax = ServiceParametres::get('workflow.sla.scolarite_days', 5);

// Écrire config (admin uniquement)
ServiceParametres::set('workflow.escalade.enabled', true);
```

## Aperçu

1. Exécuter `.specify/scripts/powershell/check-prerequisites.ps1 -Json -RequireTasks -IncludeTasks` depuis racine repo et parser FEATURE_DIR et liste AVAILABLE_DOCS. Tous les chemins doivent être absolus. Pour apostrophes dans args comme "J'implémente", utiliser syntaxe échappement : ex 'J'\''implémente' (ou guillemets si possible : "J'implémente").

2. **Vérifier statut checklists** (si FEATURE_DIR/checklists/ existe) :
   - Scanner tous les fichiers checklist dans le répertoire checklists/
   - Pour chaque checklist, compter :
     - Éléments totaux : Toutes les lignes correspondant à `- [ ]` ou `- [X]` ou `- [x]`
     - Éléments complétés : Lignes correspondant à `- [X]` ou `- [x]`
     - Éléments incomplets : Lignes correspondant à `- [ ]`
   - Créer une table statut :

     ```text
     | Checklist | Total | Complétés | Incomplets | Statut |
     |-----------|-------|-----------|------------|--------|
     | ux.md     | 12    | 12        | 0          | ✓ PASS |
     | test.md   | 8     | 5         | 3          | ✗ FAIL |
     | security.md | 6   | 6         | 0          | ✓ PASS |
     ```

   - Calculer statut global :
     - **PASS** : Toutes les checklists ont 0 éléments incomplets
     - **FAIL** : Une ou plusieurs checklists ont éléments incomplets

   - **Si une checklist est incomplète** :
     - Afficher la table avec comptages éléments incomplets
     - **ARRÊTER** et demander : « Certaines checklists sont incomplètes. Voulez-vous procéder à l'implémentation quand même ? (oui/non) »
     - Attendre réponse utilisateur avant de continuer
     - Si utilisateur dit "non" ou "attendre" ou "stop", arrêter exécution
     - Si utilisateur dit "oui" ou "procéder" ou "continuer", procéder à étape 3

   - **Si toutes les checklists sont complètes** :
     - Afficher la table montrant toutes checklists passées
     - Procéder automatiquement à étape 3

3. Charger et analyser le contexte implémentation :
   - **REQUIS** : Lire tasks.md pour la liste complète des tâches et le plan d'exécution
   - **REQUIS** : Lire plan.md pour stack technique, architecture et structure fichiers
   - **SI EXISTE** : Lire data-model.md pour entités et relations
   - **SI EXISTE** : Lire contracts/ pour spécifications API et exigences tests
   - **SI EXISTE** : Lire research.md pour décisions techniques et contraintes
   - **SI EXISTE** : Lire quickstart.md pour scénarios intégration

4. **Vérification Setup Projet** :
   - **REQUIS** : Créer/vérifier fichiers ignore basés sur setup projet réel :

   **Logique Détection & Création** :
   - Vérifier si la commande suivante réussit pour déterminer si le repository est un repo git (créer/vérifier .gitignore si oui) :

     ```sh
     git rev-parse --git-dir 2>/dev/null
     ```

   - Vérifier si Dockerfile* existe ou Docker dans plan.md → créer/vérifier .dockerignore
   - Vérifier si .eslintrc* existe → créer/vérifier .eslintignore
   - Vérifier si eslint.config.* existe → s'assurer que les entrées `ignores` de la config couvrent patterns requis
   - Vérifier si .prettierrc* existe → créer/vérifier .prettierignore
   - Vérifier si .npmrc ou package.json existe → créer/vérifier .npmignore (si publication)
   - Vérifier si fichiers terraform (*.tf) existent → créer/vérifier .terraformignore
   - Vérifier si .helmignore nécessaire (charts helm présents) → créer/vérifier .helmignore

   **Si fichier ignore existe déjà** : Vérifier qu'il contient patterns essentiels, ajouter uniquement patterns critiques manquants
   **Si fichier ignore manquant** : Créer avec ensemble complet patterns pour technologie détectée

   **Patterns Communs par Technologie** (depuis stack technique plan.md) :
   - **Node.js/JavaScript/TypeScript** : `node_modules/`, `dist/`, `build/`, `*.log`, `.env*`
   - **Python** : `__pycache__/`, `*.pyc`, `.venv/`, `venv/`, `dist/`, `*.egg-info/`
   - **Java** : `target/`, `*.class`, `*.jar`, `.gradle/`, `build/`
   - **C#/.NET** : `bin/`, `obj/`, `*.user`, `*.suo`, `packages/`
   - **Go** : `*.exe`, `*.test`, `vendor/`, `*.out`
   - **Ruby** : `.bundle/`, `log/`, `tmp/`, `*.gem`, `vendor/bundle/`
   - **PHP** : `vendor/`, `*.log`, `*.cache`, `*.env`
   - **Rust** : `target/`, `debug/`, `release/`, `*.rs.bk`, `*.rlib`, `*.prof*`, `.idea/`, `*.log`, `.env*`
   - **Kotlin** : `build/`, `out/`, `.gradle/`, `.idea/`, `*.class`, `*.jar`, `*.iml`, `*.log`, `.env*`
   - **C++** : `build/`, `bin/`, `obj/`, `out/`, `*.o`, `*.so`, `*.a`, `*.exe`, `*.dll`, `.idea/`, `*.log`, `.env*`
   - **C** : `build/`, `bin/`, `obj/`, `out/`, `*.o`, `*.a`, `*.so`, `*.exe`, `Makefile`, `config.log`, `.idea/`, `*.log`, `.env*`
   - **Swift** : `.build/`, `DerivedData/`, `*.swiftpm/`, `Packages/`
   - **R** : `.Rproj.user/`, `.Rhistory`, `.RData`, `.Ruserdata`, `*.Rproj`, `packrat/`, `renv/`
   - **Universel** : `.DS_Store`, `Thumbs.db`, `*.tmp`, `*.swp`, `.vscode/`, `.idea/`

   **Patterns Spécifiques Outils** :
   - **Docker** : `node_modules/`, `.git/`, `Dockerfile*`, `.dockerignore`, `*.log*`, `.env*`, `coverage/`
   - **ESLint** : `node_modules/`, `dist/`, `build/`, `coverage/`, `*.min.js`
   - **Prettier** : `node_modules/`, `dist/`, `build/`, `coverage/`, `package-lock.json`, `yarn.lock`, `pnpm-lock.yaml`
   - **Terraform** : `.terraform/`, `*.tfstate*`, `*.tfvars`, `.terraform.lock.hcl`
   - **Kubernetes/k8s** : `*.secret.yaml`, `secrets/`, `.kube/`, `kubeconfig*`, `*.key`, `*.crt`

5. Parser structure tasks.md et extraire :
   - **Phases tâches** : Setup, Tests, Core, Integration, Polish
   - **Dépendances tâches** : Règles exécution séquentielle vs parallèle
   - **Détails tâches** : ID, description, chemins fichiers, marqueurs parallèles [P]
   - **Flux exécution** : Ordre et exigences dépendances

6. Exécuter implémentation en suivant le plan de tâches :
   - **Exécution phase par phase** : Compléter chaque phase avant de passer à la suivante
   - **Respecter dépendances** : Exécuter tâches séquentielles dans l'ordre, tâches parallèles [P] peuvent s'exécuter ensemble
   - **Suivre approche TDD** : Exécuter tâches test avant leurs tâches implémentation correspondantes
   - **Coordination basée fichiers** : Tâches affectant mêmes fichiers doivent s'exécuter séquentiellement
   - **Points contrôle validation** : Vérifier complétion chaque phase avant de procéder

7. Règles exécution implémentation :
   - **Setup d'abord** : Initialiser structure projet, dépendances, configuration
   - **Tests avant code** : Si vous devez écrire tests pour contrats, entités et scénarios intégration
   - **Développement core** : Implémenter modèles, services, commandes CLI, endpoints
   - **Travail intégration** : Connexions base données, middleware, logging, services externes
   - **Polish et validation** : Tests unitaires, optimisation performance, documentation

8. Suivi progression et gestion erreurs :
   - Rapporter progression après chaque tâche complétée
   - Arrêter exécution si tâche non-parallèle échoue
   - Pour tâches parallèles [P], continuer avec tâches réussies, rapporter celles échouées
   - Fournir messages erreur clairs avec contexte pour debug
   - Suggérer prochaines étapes si implémentation ne peut procéder
   - **IMPORTANT** Pour tâches complétées, s'assurer de cocher la tâche [X] dans le fichier tasks.

9. Validation complétion :
   - Vérifier que toutes les tâches requises sont complétées
   - Vérifier que fonctionnalités implémentées correspondent à la spécification originale
   - Valider que tests passent et couverture atteint exigences
   - Confirmer que l'implémentation suit le plan technique
   - Rapporter statut final avec résumé du travail complété

Note : Cette commande assume qu'une décomposition complète des tâches existe dans tasks.md. Si les tâches sont incomplètes ou manquantes, suggérer d'exécuter `/speckit.tasks` d'abord pour régénérer la liste des tâches.
