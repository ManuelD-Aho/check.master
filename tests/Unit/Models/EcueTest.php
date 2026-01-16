<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Ecue;

class EcueTest extends TestCase
{
    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Ecue::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Ecue([]);
        $this->assertEquals('ecue', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Ecue::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Ecue([]);
        $this->assertEquals('id_ecue', $property->getValue($model));
    }
}
