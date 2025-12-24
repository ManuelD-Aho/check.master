# CheckMaster - Implementation Workbench

**Version**: 1.0.0  
**Purpose**: Practical guide for implementing CheckMaster features

## Quick Reference

### Code Templates

**Controller** (≤50 lines):
```php
<?php
declare(strict_types=1);
namespace App\Controllers\Module;

use App\Services\Module\ServiceFeature;
use Src\Http\{Request, JsonResponse};

class FeatureController
{
    public function __construct(private ServiceFeature $service) {}
    
    public function action(): JsonResponse
    {
        $data = Request::all();
        $result = $this->service->execute($data);
        return JsonResponse::success($result);
    }
}
```

**Service** (Business Logic):
```php
<?php
declare(strict_types=1);
namespace App\Services\Module;

use App\Services\Security\ServiceAudit;
use App\Services\Workflow\ServiceWorkflow;
use Src\Database\DB;

class ServiceFeature
{
    public function execute(array $data): mixed
    {
        DB::beginTransaction();
        try {
            // Logic here
            ServiceAudit::log('Action', 'entity', $id, $data);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

### CheckMaster Patterns

**Workflow Transition**:
```php
ServiceWorkflow::effectuerTransition($dossierId, 'code', Auth::id());
```

**Permission Check**:
```php
if (!ServicePermission::verifier(Auth::id(), 'resource', 'action')) {
    throw new ForbiddenException();
}
```

**Notification**:
```php
ServiceNotification::envoyer('template_code', [
    'destinataires' => [$userId],
    'variables' => ['nom' => $nom, 'date' => $date]
]);
```

**Configuration**:
```php
$value = ServiceParametres::get('workflow.sla.days', 5);
```

### Commands

```bash
# Development
composer install
php -S localhost:8000 -t public/

# Quality
composer run fix    # PSR-12
composer run stan   # PHPStan level 6+
composer test       # PHPUnit

# Database
php bin/console migrate
php bin/console seed
```

## See Also

- **Constitution**: Core principles and constraints
- **Canvas**: Full technical architecture
- **Workflows**: Process documentation
- **Deployment**: Production deployment guide
