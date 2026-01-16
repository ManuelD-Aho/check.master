<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Mention;

class MentionTest extends TestCase
{
    public function testMethodeFindByCodeExiste(): void
    {
        $this->assertTrue(method_exists(Mention::class, 'findByCode'));
        
        $reflection = new \ReflectionMethod(Mention::class, 'findByCode');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeTrouverPourNoteExiste(): void
    {
        $this->assertTrue(method_exists(Mention::class, 'trouverPourNote'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(Mention::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new Mention([]);
        $this->assertEquals('mentions', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(Mention::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new Mention([]);
        $this->assertEquals('id_mention', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(Mention::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new Mention([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('code_mention', $fillable);
        $this->assertContains('libelle_mention', $fillable);
        $this->assertContains('note_min', $fillable);
        $this->assertContains('note_max', $fillable);
    }
}
