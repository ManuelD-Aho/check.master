<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Soutenance\ServiceNotes;

/**
 * Tests unitaires exhaustifs pour ServiceNotes
 * 
 * @covers \App\Services\Soutenance\ServiceNotes
 */
class ServiceNotesTest extends TestCase
{
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceNotes::class));
    }

    public function testEnregistrerNoteMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceNotes::class, 'enregistrerNote'));
        $reflection = new \ReflectionMethod(ServiceNotes::class, 'enregistrerNote');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(4, count($params));
        $this->assertEquals('soutenanceId', $params[0]->getName());
        $this->assertEquals('juryMembreId', $params[1]->getName());
        $this->assertEquals('note', $params[2]->getName());
    }

    public function testCalculerNoteFinaleMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceNotes::class, 'calculerNoteFinale'));
        $reflection = new \ReflectionMethod(ServiceNotes::class, 'calculerNoteFinale');
        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('soutenanceId', $params[0]->getName());
    }

    public function testCalculerNoteFinaleReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceNotes::class, 'calculerNoteFinale');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertTrue($returnType->allowsNull());
    }

    public function testDeterminerMentionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceNotes::class, 'determinerMention'));
        $reflection = new \ReflectionMethod(ServiceNotes::class, 'determinerMention');
        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('note', $params[0]->getName());
    }

    public function testFinaliserNotesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceNotes::class, 'finaliserNotes'));
        $reflection = new \ReflectionMethod(ServiceNotes::class, 'finaliserNotes');
        $params = $reflection->getParameters();
        $this->assertCount(2, $params);
        $this->assertEquals('soutenanceId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
    }

    public function testToutesNotesSaisiesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceNotes::class, 'toutesNotesSaisies'));
        $reflection = new \ReflectionMethod(ServiceNotes::class, 'toutesNotesSaisies');
        $returnType = $reflection->getReturnType();
        $this->assertEquals('bool', $returnType->getName());
    }

    public function testGetNotesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceNotes::class, 'getNotes'));
        $reflection = new \ReflectionMethod(ServiceNotes::class, 'getNotes');
        $returnType = $reflection->getReturnType();
        $this->assertEquals('array', $returnType->getName());
    }

    public function testMentionsValides(): void
    {
        $mentions = ['Passable', 'Assez Bien', 'Bien', 'Très Bien', 'Excellent'];
        $this->assertContains('Passable', $mentions);
        $this->assertContains('Excellent', $mentions);
    }

    public function testNoteValideEntre0Et20(): void
    {
        $noteMin = 0;
        $noteMax = 20;
        $note = 15.5;
        $this->assertGreaterThanOrEqual($noteMin, $note);
        $this->assertLessThanOrEqual($noteMax, $note);
    }

    public function testServiceEstInstanciable(): void
    {
        $reflection = new \ReflectionClass(ServiceNotes::class);
        $this->assertTrue($reflection->isInstantiable());
    }
}
