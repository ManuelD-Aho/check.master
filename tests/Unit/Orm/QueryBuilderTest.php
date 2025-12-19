<?php

declare(strict_types=1);

namespace Tests\Unit\Orm;

use App\Orm\QueryBuilder;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour le QueryBuilder
 */
class QueryBuilderTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $this->pdo->exec('
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255),
                email VARCHAR(255),
                status VARCHAR(50),
                age INTEGER,
                created_at DATETIME
            )
        ');

        $this->pdo->exec('
            CREATE TABLE posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                title VARCHAR(255)
            )
        ');

        // Insérer des données de test
        $this->pdo->exec("INSERT INTO users (name, email, status, age) VALUES ('Alice', 'alice@test.com', 'active', 25)");
        $this->pdo->exec("INSERT INTO users (name, email, status, age) VALUES ('Bob', 'bob@test.com', 'inactive', 30)");
        $this->pdo->exec("INSERT INTO users (name, email, status, age) VALUES ('Charlie', 'charlie@test.com', 'active', 35)");
        $this->pdo->exec("INSERT INTO posts (user_id, title) VALUES (1, 'Post by Alice')");
    }

    // =========================================================================
    // Tests SELECT
    // =========================================================================

    /**
     * @test
     */
    public function testTableCreatesBuilder(): void
    {
        $builder = QueryBuilder::table('users');

        $this->assertInstanceOf(QueryBuilder::class, $builder);
    }

    /**
     * @test
     */
    public function testSelectAllByDefault(): void
    {
        $sql = QueryBuilder::table('users')->toSql();

        $this->assertEquals('SELECT * FROM users', $sql);
    }

    /**
     * @test
     */
    public function testSelectSpecificColumns(): void
    {
        $sql = QueryBuilder::table('users')
            ->select('name', 'email')
            ->toSql();

        $this->assertEquals('SELECT name, email FROM users', $sql);
    }

    /**
     * @test
     */
    public function testSelectWithArrayColumns(): void
    {
        $sql = QueryBuilder::table('users')
            ->select(['id', 'name'])
            ->toSql();

        $this->assertEquals('SELECT id, name FROM users', $sql);
    }

    /**
     * @test
     */
    public function testDistinct(): void
    {
        $sql = QueryBuilder::table('users')
            ->distinct()
            ->select('status')
            ->toSql();

        $this->assertEquals('SELECT DISTINCT status FROM users', $sql);
    }

    // =========================================================================
    // Tests WHERE
    // =========================================================================

    /**
     * @test
     */
    public function testWhereWithEquality(): void
    {
        $builder = QueryBuilder::table('users')
            ->where('status', 'active');

        $sql = $builder->toSql();
        $bindings = $builder->getBindings();

        $this->assertStringContainsString('WHERE status = :status_0', $sql);
        $this->assertEquals('active', $bindings['status_0']);
    }

    /**
     * @test
     */
    public function testWhereWithOperator(): void
    {
        $builder = QueryBuilder::table('users')
            ->where('age', '>', 25);

        $sql = $builder->toSql();
        $bindings = $builder->getBindings();

        $this->assertStringContainsString('WHERE age > :age_0', $sql);
        $this->assertEquals(25, $bindings['age_0']);
    }

    /**
     * @test
     */
    public function testMultipleWhereClauses(): void
    {
        $builder = QueryBuilder::table('users')
            ->where('status', 'active')
            ->where('age', '>', 20);

        $sql = $builder->toSql();

        $this->assertStringContainsString('WHERE status = :status_0 AND age > :age_1', $sql);
    }

    /**
     * @test
     */
    public function testOrWhere(): void
    {
        $builder = QueryBuilder::table('users')
            ->where('status', 'active')
            ->orWhere('age', '>', 30);

        $sql = $builder->toSql();

        $this->assertStringContainsString('OR age > :age_1', $sql);
    }

    /**
     * @test
     */
    public function testWhereIn(): void
    {
        $builder = QueryBuilder::table('users')
            ->whereIn('id', [1, 2, 3]);

        $sql = $builder->toSql();

        $this->assertStringContainsString('id IN (:id_in_0_0, :id_in_1_1, :id_in_2_2)', $sql);
    }

    /**
     * @test
     */
    public function testWhereInWithEmptyArray(): void
    {
        $builder = QueryBuilder::table('users')
            ->whereIn('id', []);

        $sql = $builder->toSql();

        $this->assertStringContainsString('1 = 0', $sql);
    }

    /**
     * @test
     */
    public function testWhereNotIn(): void
    {
        $builder = QueryBuilder::table('users')
            ->whereNotIn('id', [1, 2]);

        $sql = $builder->toSql();

        $this->assertStringContainsString('NOT IN', $sql);
    }

    /**
     * @test
     */
    public function testWhereNull(): void
    {
        $sql = QueryBuilder::table('users')
            ->whereNull('created_at')
            ->toSql();

        $this->assertStringContainsString('created_at IS NULL', $sql);
    }

    /**
     * @test
     */
    public function testWhereNotNull(): void
    {
        $sql = QueryBuilder::table('users')
            ->whereNotNull('email')
            ->toSql();

        $this->assertStringContainsString('email IS NOT NULL', $sql);
    }

    /**
     * @test
     */
    public function testWhereBetween(): void
    {
        $builder = QueryBuilder::table('users')
            ->whereBetween('age', 20, 30);

        $sql = $builder->toSql();
        $bindings = $builder->getBindings();

        $this->assertStringContainsString('age BETWEEN', $sql);
        $this->assertCount(2, $bindings);
        $this->assertContains(20, $bindings);
        $this->assertContains(30, $bindings);
    }

    /**
     * @test
     */
    public function testWhereLike(): void
    {
        $builder = QueryBuilder::table('users')
            ->whereLike('name', '%Alice%');

        $sql = $builder->toSql();

        $this->assertStringContainsString('LIKE', $sql);
    }

    // =========================================================================
    // Tests JOIN
    // =========================================================================

    /**
     * @test
     */
    public function testInnerJoin(): void
    {
        $sql = QueryBuilder::table('users')
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->toSql();

        $this->assertStringContainsString('INNER JOIN posts ON users.id = posts.user_id', $sql);
    }

    /**
     * @test
     */
    public function testLeftJoin(): void
    {
        $sql = QueryBuilder::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->toSql();

        $this->assertStringContainsString('LEFT JOIN posts ON users.id = posts.user_id', $sql);
    }

    /**
     * @test
     */
    public function testRightJoin(): void
    {
        $sql = QueryBuilder::table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
            ->toSql();

        $this->assertStringContainsString('RIGHT JOIN posts ON users.id = posts.user_id', $sql);
    }

    // =========================================================================
    // Tests ORDER BY
    // =========================================================================

    /**
     * @test
     */
    public function testOrderByAsc(): void
    {
        $sql = QueryBuilder::table('users')
            ->orderBy('name', 'ASC')
            ->toSql();

        $this->assertStringContainsString('ORDER BY name ASC', $sql);
    }

    /**
     * @test
     */
    public function testOrderByDesc(): void
    {
        $sql = QueryBuilder::table('users')
            ->orderBy('created_at', 'DESC')
            ->toSql();

        $this->assertStringContainsString('ORDER BY created_at DESC', $sql);
    }

    /**
     * @test
     */
    public function testMultipleOrderBy(): void
    {
        $sql = QueryBuilder::table('users')
            ->orderBy('status', 'ASC')
            ->orderBy('name', 'DESC')
            ->toSql();

        $this->assertStringContainsString('ORDER BY status ASC, name DESC', $sql);
    }

    // =========================================================================
    // Tests GROUP BY / HAVING
    // =========================================================================

    /**
     * @test
     */
    public function testGroupBy(): void
    {
        $sql = QueryBuilder::table('users')
            ->select('status', 'COUNT(*) as count')
            ->groupBy('status')
            ->toSql();

        $this->assertStringContainsString('GROUP BY status', $sql);
    }

    /**
     * @test
     */
    public function testGroupByMultipleColumns(): void
    {
        $sql = QueryBuilder::table('users')
            ->groupBy('status', 'age')
            ->toSql();

        $this->assertStringContainsString('GROUP BY status, age', $sql);
    }

    /**
     * @test
     */
    public function testHaving(): void
    {
        $builder = QueryBuilder::table('users')
            ->select('status', 'COUNT(*) as count')
            ->groupBy('status')
            ->having('count', '>', 1);

        $sql = $builder->toSql();

        $this->assertStringContainsString('HAVING count >', $sql);
    }

    // =========================================================================
    // Tests LIMIT / OFFSET
    // =========================================================================

    /**
     * @test
     */
    public function testLimit(): void
    {
        $sql = QueryBuilder::table('users')
            ->limit(10)
            ->toSql();

        $this->assertStringContainsString('LIMIT 10', $sql);
    }

    /**
     * @test
     */
    public function testOffset(): void
    {
        $sql = QueryBuilder::table('users')
            ->limit(10)
            ->offset(5)
            ->toSql();

        $this->assertStringContainsString('LIMIT 10 OFFSET 5', $sql);
    }

    /**
     * @test
     */
    public function testPaginate(): void
    {
        $sql = QueryBuilder::table('users')
            ->paginate(2, 15)
            ->toSql();

        $this->assertStringContainsString('LIMIT 15 OFFSET 15', $sql);
    }

    /**
     * @test
     */
    public function testPaginateFirstPage(): void
    {
        $sql = QueryBuilder::table('users')
            ->paginate(1, 10)
            ->toSql();

        $this->assertStringContainsString('LIMIT 10 OFFSET 0', $sql);
    }

    // =========================================================================
    // Tests Execution
    // =========================================================================

    /**
     * @test
     */
    public function testGetReturnsResults(): void
    {
        $results = QueryBuilder::table('users')
            ->where('status', 'active')
            ->get($this->pdo);

        $this->assertIsArray($results);
        $this->assertCount(2, $results);
    }

    /**
     * @test
     */
    public function testFirstReturnsFirstResult(): void
    {
        $result = QueryBuilder::table('users')
            ->orderBy('name', 'ASC')
            ->first($this->pdo);

        $this->assertIsArray($result);
        $this->assertEquals('Alice', $result['name']);
    }

    /**
     * @test
     */
    public function testFirstReturnsNullWhenNoResults(): void
    {
        $result = QueryBuilder::table('users')
            ->where('status', 'nonexistent')
            ->first($this->pdo);

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function testCount(): void
    {
        $count = QueryBuilder::table('users')
            ->where('status', 'active')
            ->count($this->pdo);

        $this->assertEquals(2, $count);
    }

    /**
     * @test
     */
    public function testExists(): void
    {
        $exists = QueryBuilder::table('users')
            ->where('email', 'alice@test.com')
            ->exists($this->pdo);

        $this->assertTrue($exists);
    }

    /**
     * @test
     */
    public function testExistsReturnsFalse(): void
    {
        $exists = QueryBuilder::table('users')
            ->where('email', 'notfound@test.com')
            ->exists($this->pdo);

        $this->assertFalse($exists);
    }

    // =========================================================================
    // Tests Reset & Bindings
    // =========================================================================

    /**
     * @test
     */
    public function testReset(): void
    {
        $builder = QueryBuilder::table('users')
            ->select('name')
            ->where('status', 'active')
            ->limit(5);

        $builder->reset();

        $this->assertEquals('SELECT * FROM users', $builder->toSql());
        $this->assertEmpty($builder->getBindings());
    }

    /**
     * @test
     */
    public function testGetBindingsReturnsAllBindings(): void
    {
        $builder = QueryBuilder::table('users')
            ->where('status', 'active')
            ->where('age', '>', 20);

        $bindings = $builder->getBindings();

        $this->assertCount(2, $bindings);
    }
}
