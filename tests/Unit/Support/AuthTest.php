<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Src\Support\Auth;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour Auth
 */
class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        Auth::reset();
    }

    /**
     * @test
     */
    public function testCheckReturnsFalseQuandNonConnecte(): void
    {
        $this->assertFalse(Auth::check());
    }

    /**
     * @test
     */
    public function testUserRetourneNullQuandNonConnecte(): void
    {
        $this->assertNull(Auth::user());
    }

    /**
     * @test
     */
    public function testIdRetourneNullQuandNonConnecte(): void
    {
        $this->assertNull(Auth::id());
    }

    /**
     * @test
     */
    public function testResetReinitialiseLEtat(): void
    {
        Auth::reset();
        $this->assertFalse(Auth::check());
    }
}
