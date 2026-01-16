<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\ConfigurationSysteme;

class ConfigurationSystemeTest extends TestCase
{
    public function testConstantesTypesDefinies(): void
    {
        $this->assertEquals('string', ConfigurationSysteme::TYPE_STRING);
        $this->assertEquals('int', ConfigurationSysteme::TYPE_INT);
        $this->assertEquals('float', ConfigurationSysteme::TYPE_FLOAT);
        $this->assertEquals('boolean', ConfigurationSysteme::TYPE_BOOLEAN);
        $this->assertEquals('json', ConfigurationSysteme::TYPE_JSON);
    }

    public function testMethodeFindByCleExiste(): void
    {
        $this->assertTrue(method_exists(ConfigurationSysteme::class, 'findByCle'));
        
        $reflection = new \ReflectionMethod(ConfigurationSysteme::class, 'findByCle');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeParGroupeExiste(): void
    {
        $this->assertTrue(method_exists(ConfigurationSysteme::class, 'parGroupe'));
        
        $reflection = new \ReflectionMethod(ConfigurationSysteme::class, 'parGroupe');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeModifiablesUIExiste(): void
    {
        $this->assertTrue(method_exists(ConfigurationSysteme::class, 'modifiablesUI'));
    }

    public function testMethodeToutesExiste(): void
    {
        $this->assertTrue(method_exists(ConfigurationSysteme::class, 'toutes'));
    }

    public function testMethodeGetExiste(): void
    {
        $this->assertTrue(method_exists(ConfigurationSysteme::class, 'get'));
        
        $reflection = new \ReflectionMethod(ConfigurationSysteme::class, 'get');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeGetValeurTypeeExiste(): void
    {
        $this->assertTrue(method_exists(ConfigurationSysteme::class, 'getValeurTypee'));
    }

    public function testMethodeSetExiste(): void
    {
        $this->assertTrue(method_exists(ConfigurationSysteme::class, 'set'));
        
        $reflection = new \ReflectionMethod(ConfigurationSysteme::class, 'set');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeSupprimerExiste(): void
    {
        $this->assertTrue(method_exists(ConfigurationSysteme::class, 'supprimer'));
    }

    public function testMethodeGetGroupesExiste(): void
    {
        $this->assertTrue(method_exists(ConfigurationSysteme::class, 'getGroupes'));
    }

    public function testMethodeExisteExiste(): void
    {
        $this->assertTrue(method_exists(ConfigurationSysteme::class, 'existe'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(ConfigurationSysteme::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new ConfigurationSysteme([]);
        $this->assertEquals('configuration_systeme', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(ConfigurationSysteme::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new ConfigurationSysteme([]);
        $this->assertEquals('id_config', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(ConfigurationSysteme::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new ConfigurationSysteme([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('cle_config', $fillable);
        $this->assertContains('valeur_config', $fillable);
        $this->assertContains('type_valeur', $fillable);
        $this->assertContains('groupe_config', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('modifiable_ui', $fillable);
    }
}
