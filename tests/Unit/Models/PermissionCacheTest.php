<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\PermissionCache;

class PermissionCacheTest extends TestCase
{
    public function testConstanteTtlDefinies(): void
    {
        $this->assertEquals(300, PermissionCache::TTL_SECONDES);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(PermissionCache::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new PermissionCache([]);
        $this->assertEquals('permissions_cache', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(PermissionCache::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new PermissionCache([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('utilisateur_id', $fillable);
        $this->assertContains('ressource_code', $fillable);
        $this->assertContains('permissions_json', $fillable);
        $this->assertContains('expire_le', $fillable);
    }
}
