<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\CommissionVote;

class CommissionVoteTest extends TestCase
{
    public function testConstantesDecisionsDefinies(): void
    {
        $this->assertEquals('Valider', CommissionVote::DECISION_VALIDER);
        $this->assertEquals('A_revoir', CommissionVote::DECISION_A_REVOIR);
        $this->assertEquals('Rejeter', CommissionVote::DECISION_REJETER);
    }

    public function testMethodeSessionExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'session'));
    }

    public function testMethodeRapportExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'rapport'));
    }

    public function testMethodeMembreExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'membre'));
    }

    public function testMethodePourSessionExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'pourSession'));
        
        $reflection = new \ReflectionMethod(CommissionVote::class, 'pourSession');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodePourRapportExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'pourRapport'));
        
        $reflection = new \ReflectionMethod(CommissionVote::class, 'pourRapport');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeParMembreExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'parMembre'));
    }

    public function testMethodeTrouverExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'trouver'));
        
        $reflection = new \ReflectionMethod(CommissionVote::class, 'trouver');
        $params = $reflection->getParameters();
        
        $this->assertCount(4, $params);
        $this->assertEquals('sessionId', $params[0]->getName());
        $this->assertEquals('rapportId', $params[1]->getName());
        $this->assertEquals('membreId', $params[2]->getName());
        $this->assertEquals('tour', $params[3]->getName());
    }

    public function testMethodeADejaVoteExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'aDejaVote'));
    }

    public function testMethodeVoterExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'voter'));
        
        $reflection = new \ReflectionMethod(CommissionVote::class, 'voter');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeStatistiquesVoteExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'statistiquesVote'));
    }

    public function testMethodeNombreVotesExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'nombreVotes'));
    }

    public function testMethodeUnanimiteAtteinteExiste(): void
    {
        $this->assertTrue(method_exists(CommissionVote::class, 'unanimiteAtteinte'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(CommissionVote::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new CommissionVote([]);
        $this->assertEquals('votes_commission', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(CommissionVote::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new CommissionVote([]);
        $this->assertEquals('id_vote', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(CommissionVote::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new CommissionVote([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('session_id', $fillable);
        $this->assertContains('rapport_id', $fillable);
        $this->assertContains('membre_id', $fillable);
        $this->assertContains('tour', $fillable);
        $this->assertContains('decision', $fillable);
        $this->assertContains('commentaire', $fillable);
    }
}
