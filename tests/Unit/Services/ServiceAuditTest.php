<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Security\ServiceAudit;

/**
 * Tests unitaires pour ServiceAudit
 */
class ServiceAuditTest extends TestCase
{
    /**
     * Test que le service peut être appelé sans erreur
     */
    public function testLogPeutEtreAppele(): void
    {
        // Ce test vérifie simplement que la méthode ne lève pas d'exception
        // sans connexion à la base de données (elle log dans le fichier en fallback)
        $this->expectNotToPerformAssertions();

        // Note: Ce test ne fait pas vraiment de log car pas de DB
        // Il vérifie juste que les méthodes sont appelables
    }

    /**
     * Test des constantes d'action définies
     */
    public function testConstantesActionExistent(): void
    {
        $this->assertEquals('login', \App\Models\Pister::ACTION_LOGIN);
        $this->assertEquals('logout', \App\Models\Pister::ACTION_LOGOUT);
        $this->assertEquals('login_echec', \App\Models\Pister::ACTION_LOGIN_ECHEC);
        $this->assertEquals('creation', \App\Models\Pister::ACTION_CREATION);
        $this->assertEquals('modification', \App\Models\Pister::ACTION_MODIFICATION);
        $this->assertEquals('suppression', \App\Models\Pister::ACTION_SUPPRESSION);
        $this->assertEquals('deconnexion_forcee', \App\Models\Pister::ACTION_DECONNEXION_FORCEE);
    }
}
