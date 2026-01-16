<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\StatCache;

class StatCacheTest extends TestCase
{
    public function testConstantesTtlDefinies(): void
    {
        $this->assertEquals(3600, StatCache::TTL_DEFAUT);
        $this->assertEquals(300, StatCache::TTL_COURT);
        $this->assertEquals(86400, StatCache::TTL_LONG);
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(StatCache::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new StatCache([]);
        $this->assertEquals('stats_cache', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(StatCache::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new StatCache([]);
        $this->assertEquals('id_stat', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(StatCache::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new StatCache([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('cle_stat', $fillable);
        $this->assertContains('valeur_json', $fillable);
        $this->assertContains('expire_le', $fillable);
    }
}
