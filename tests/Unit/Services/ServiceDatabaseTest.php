<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Core\ServiceDatabase;

/**
 * Tests unitaires pour ServiceDatabase
 * 
 * @covers \App\Services\Core\ServiceDatabase
 */
class ServiceDatabaseTest extends TestCase
{
    /**
     * @test
     * Test de la structure de la classe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceDatabase::class));
    }

    /**
     * @test
     * Test de la méthode connect
     */
    public function testConnectMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'connect'));

        $reflection = new \ReflectionMethod(ServiceDatabase::class, 'connect');
        $params = $reflection->getParameters();

        $this->assertEquals('host', $params[0]->getName());
        $this->assertEquals('database', $params[1]->getName());
        $this->assertEquals('username', $params[2]->getName());
        $this->assertEquals('password', $params[3]->getName());
    }

    /**
     * @test
     * Test de la méthode connectFromEnv
     */
    public function testConnectFromEnvMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'connectFromEnv'));
    }

    /**
     * @test
     * Test de la méthode getConnection
     */
    public function testGetConnectionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'getConnection'));
    }

    /**
     * @test
     * Test de la méthode isConnected
     */
    public function testIsConnectedMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'isConnected'));
    }

    /**
     * @test
     * Test de la méthode disconnect
     */
    public function testDisconnectMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'disconnect'));
    }

    /**
     * @test
     * Test de la méthode beginTransaction
     */
    public function testBeginTransactionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'beginTransaction'));
    }

    /**
     * @test
     * Test de la méthode commit
     */
    public function testCommitMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'commit'));
    }

    /**
     * @test
     * Test de la méthode rollBack
     */
    public function testRollBackMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'rollBack'));
    }

    /**
     * @test
     * Test de la méthode inTransaction
     */
    public function testInTransactionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'inTransaction'));
    }

    /**
     * @test
     * Test de la méthode query
     */
    public function testQueryMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'query'));

        $reflection = new \ReflectionMethod(ServiceDatabase::class, 'query');
        $params = $reflection->getParameters();

        $this->assertEquals('sql', $params[0]->getName());
        $this->assertEquals('params', $params[1]->getName());
    }

    /**
     * @test
     * Test de la méthode fetchAll
     */
    public function testFetchAllMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'fetchAll'));
    }

    /**
     * @test
     * Test de la méthode fetchOne
     */
    public function testFetchOneMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'fetchOne'));
    }

    /**
     * @test
     * Test de la méthode fetchColumn
     */
    public function testFetchColumnMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'fetchColumn'));
    }

    /**
     * @test
     * Test de la méthode insert
     */
    public function testInsertMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'insert'));

        $reflection = new \ReflectionMethod(ServiceDatabase::class, 'insert');
        $params = $reflection->getParameters();

        $this->assertEquals('table', $params[0]->getName());
        $this->assertEquals('data', $params[1]->getName());
    }

    /**
     * @test
     * Test de la méthode update
     */
    public function testUpdateMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'update'));

        $reflection = new \ReflectionMethod(ServiceDatabase::class, 'update');
        $params = $reflection->getParameters();

        $this->assertEquals('table', $params[0]->getName());
        $this->assertEquals('data', $params[1]->getName());
        $this->assertEquals('where', $params[2]->getName());
    }

    /**
     * @test
     * Test de la méthode delete
     */
    public function testDeleteMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'delete'));

        $reflection = new \ReflectionMethod(ServiceDatabase::class, 'delete');
        $params = $reflection->getParameters();

        $this->assertEquals('table', $params[0]->getName());
        $this->assertEquals('where', $params[1]->getName());
    }

    /**
     * @test
     * Test de la méthode count
     */
    public function testCountMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'count'));
    }

    /**
     * @test
     * Test de la méthode exists
     */
    public function testExistsMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'exists'));
    }

    /**
     * @test
     * Test de la méthode lastInsertId
     */
    public function testLastInsertIdMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'lastInsertId'));
    }

    /**
     * @test
     * Test de la méthode transaction
     */
    public function testTransactionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'transaction'));

        $reflection = new \ReflectionMethod(ServiceDatabase::class, 'transaction');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('callback', $params[0]->getName());
    }

    /**
     * @test
     * Test de la méthode escapeIdentifier
     */
    public function testEscapeIdentifierMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'escapeIdentifier'));
    }

    /**
     * @test
     * Test de la méthode ping
     */
    public function testPingMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'ping'));
    }

    /**
     * @test
     * Test de la méthode info
     */
    public function testInfoMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceDatabase::class, 'info'));
    }

    /**
     * @test
     * Test que toutes les méthodes sont statiques
     */
    public function testToutesMethodesStatiques(): void
    {
        $methods = [
            'connect',
            'connectFromEnv',
            'getConnection',
            'isConnected',
            'disconnect',
            'beginTransaction',
            'commit',
            'rollBack',
            'inTransaction',
            'query',
            'fetchAll',
            'fetchOne',
            'fetchColumn',
            'insert',
            'update',
            'delete',
            'count',
            'exists',
            'lastInsertId',
            'transaction',
            'escapeIdentifier',
            'ping',
            'info',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceDatabase::class, $method);
            $this->assertTrue(
                $reflection->isStatic(),
                "La méthode {$method} devrait être statique"
            );
        }
    }
}
