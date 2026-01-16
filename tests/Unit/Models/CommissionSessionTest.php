<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\CommissionSession;

class CommissionSessionTest extends TestCase
{
    public function testConstantesStatutsDefinies(): void
    {
        $this->assertEquals('Planifiee', CommissionSession::STATUT_PLANIFIEE);
        $this->assertEquals('En_cours', CommissionSession::STATUT_EN_COURS);
        $this->assertEquals('Terminee', CommissionSession::STATUT_TERMINEE);
        $this->assertEquals('Annulee', CommissionSession::STATUT_ANNULEE);
    }

    public function testMaxToursVote(): void
    {
        $this->assertEquals(3, CommissionSession::MAX_TOURS_VOTE);
    }

    public function testMethodeMembresExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'membres'));
    }

    public function testMethodeVotesExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'votes'));
    }

    public function testMethodePlanifieesExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'planifiees'));
        
        $reflection = new \ReflectionMethod(CommissionSession::class, 'planifiees');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeEnCoursExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'enCours'));
        
        $reflection = new \ReflectionMethod(CommissionSession::class, 'enCours');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeProchaineExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'prochaine'));
        
        $reflection = new \ReflectionMethod(CommissionSession::class, 'prochaine');
        $this->assertTrue($reflection->isStatic());
    }

    public function testMethodeEstPlanifieeExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'estPlanifiee'));
    }

    public function testMethodeEstEnCoursExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'estEnCours'));
    }

    public function testMethodeEstTermineeExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'estTerminee'));
    }

    public function testMethodeEstAnnuleeExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'estAnnulee'));
    }

    public function testMethodePeutVoterExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'peutVoter'));
    }

    public function testMethodeDemarrerExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'demarrer'));
    }

    public function testMethodeTerminerExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'terminer'));
    }

    public function testMethodeAnnulerExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'annuler'));
    }

    public function testMethodePasserAuTourSuivantExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'passerAuTourSuivant'));
    }

    public function testMethodeDoitEscaladerExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'doitEscalader'));
    }

    public function testMethodeGetRapportsEvaluesExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'getRapportsEvalues'));
    }

    public function testMethodeGetResultatsVoteExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'getResultatsVote'));
    }

    public function testMethodeUnanimiteAtteinteExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'unanimiteAtteinte'));
    }

    public function testMethodeMarquerPvGenereExiste(): void
    {
        $this->assertTrue(method_exists(CommissionSession::class, 'marquerPvGenere'));
    }

    public function testTableName(): void
    {
        $reflection = new \ReflectionClass(CommissionSession::class);
        $property = $reflection->getProperty('table');
        $property->setAccessible(true);
        
        $model = new CommissionSession([]);
        $this->assertEquals('sessions_commission', $property->getValue($model));
    }

    public function testPrimaryKey(): void
    {
        $reflection = new \ReflectionClass(CommissionSession::class);
        $property = $reflection->getProperty('primaryKey');
        $property->setAccessible(true);
        
        $model = new CommissionSession([]);
        $this->assertEquals('id_session', $property->getValue($model));
    }

    public function testFillableFields(): void
    {
        $reflection = new \ReflectionClass(CommissionSession::class);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        
        $model = new CommissionSession([]);
        $fillable = $property->getValue($model);
        
        $this->assertContains('date_session', $fillable);
        $this->assertContains('lieu', $fillable);
        $this->assertContains('statut', $fillable);
        $this->assertContains('tour_vote', $fillable);
        $this->assertContains('pv_genere', $fillable);
    }
}
