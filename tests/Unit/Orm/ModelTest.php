<?php

declare(strict_types=1);

namespace Tests\Unit\Orm;

use App\Orm\Model;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour la classe Model ORM
 */
class ModelTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer une base SQLite en mémoire pour les tests
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        // Créer une table de test
        $this->pdo->exec('
            CREATE TABLE test_models (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255),
                email VARCHAR(255),
                status VARCHAR(50) DEFAULT "active",
                created_at DATETIME
            )
        ');

        Model::setConnection($this->pdo);
    }

    // =========================================================================
    // Tests de Connexion
    // =========================================================================

    /**
     * @test
     */
    public function testSetConnectionConfiguresPdo(): void
    {
        Model::setConnection($this->pdo);

        // La connexion doit être utilisable
        $this->assertInstanceOf(PDO::class, $this->pdo);
    }

    /**
     * @test
     */
    public function testGetConnectionThrowsExceptionWhenNotConfigured(): void
    {
        // Réinitialiser la connexion via réflexion
        $reflection = new \ReflectionClass(TestModel::class);
        $property = $reflection->getProperty('pdo');
        $property->setAccessible(true);
        $property->setValue(null, null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Connexion base de données non configurée');

        TestModel::find(1);
    }

    // =========================================================================
    // Tests d'Attributs
    // =========================================================================

    /**
     * @test
     */
    public function testFillSetsOnlyFillableAttributes(): void
    {
        $model = new TestModel([
            'name' => 'Test',
            'email' => 'test@example.com',
            'non_fillable' => 'should_not_be_set',
        ]);

        $this->assertEquals('Test', $model->name);
        $this->assertEquals('test@example.com', $model->email);
        $this->assertNull($model->non_fillable);
    }

    /**
     * @test
     */
    public function testMagicGetReturnsAttributeValue(): void
    {
        $model = new TestModel(['name' => 'John']);

        $this->assertEquals('John', $model->name);
    }

    /**
     * @test
     */
    public function testMagicGetReturnsNullForUndefinedAttribute(): void
    {
        $model = new TestModel();

        $this->assertNull($model->undefined_attribute);
    }

    /**
     * @test
     */
    public function testMagicSetSetsAttributeValue(): void
    {
        $model = new TestModel();
        $model->name = 'Jane';

        $this->assertEquals('Jane', $model->name);
    }

    /**
     * @test
     */
    public function testMagicSetIgnoresNonFillableAttributes(): void
    {
        $model = new TestModel();
        $model->non_fillable = 'value';

        $this->assertNull($model->non_fillable);
    }

    /**
     * @test
     */
    public function testIssetReturnsTrueForSetAttribute(): void
    {
        $model = new TestModel(['name' => 'Test']);

        $this->assertTrue(isset($model->name));
    }

    /**
     * @test
     */
    public function testIssetReturnsFalseForUnsetAttribute(): void
    {
        $model = new TestModel();

        $this->assertFalse(isset($model->name));
    }

    /**
     * @test
     */
    public function testGetIdReturnsNullWhenNotSet(): void
    {
        $model = new TestModel();

        $this->assertNull($model->getId());
    }

    /**
     * @test
     */
    public function testGetIdReturnsIntValue(): void
    {
        $model = new TestModel();
        $model->fill(['id' => '42']);

        $this->assertSame(42, $model->getId());
    }

    /**
     * @test
     */
    public function testToArrayReturnsAllAttributes(): void
    {
        $model = new TestModel(['name' => 'Test', 'email' => 'test@test.com']);

        $array = $model->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Test', $array['name']);
        $this->assertEquals('test@test.com', $array['email']);
    }

    // =========================================================================
    // Tests CRUD - Create
    // =========================================================================

    /**
     * @test
     */
    public function testSaveInsertsNewRecord(): void
    {
        Model::setConnection($this->pdo);

        $model = new TestModel([
            'name' => 'New User',
            'email' => 'new@example.com',
        ]);

        $result = $model->save();

        $this->assertTrue($result);
        $this->assertNotNull($model->getId());
        $this->assertEquals(1, $model->getId());
    }

    /**
     * @test
     */
    public function testSaveReturnsFalseForEmptyData(): void
    {
        Model::setConnection($this->pdo);

        $model = new TestModel();
        $result = $model->save();

        $this->assertFalse($result);
    }

    // =========================================================================
    // Tests CRUD - Read
    // =========================================================================

    /**
     * @test
     */
    public function testFindReturnsModelWhenExists(): void
    {
        Model::setConnection($this->pdo);
        $this->insertTestRecord('User 1', 'user1@test.com');

        $model = TestModel::find(1);

        $this->assertInstanceOf(TestModel::class, $model);
        $this->assertEquals('User 1', $model->name);
    }

    /**
     * @test
     */
    public function testFindReturnsNullWhenNotExists(): void
    {
        Model::setConnection($this->pdo);

        $model = TestModel::find(999);

        $this->assertNull($model);
    }

    /**
     * @test
     */
    public function testAllReturnsArrayOfModels(): void
    {
        Model::setConnection($this->pdo);
        $this->insertTestRecord('User 1', 'user1@test.com');
        $this->insertTestRecord('User 2', 'user2@test.com');

        $models = TestModel::all();

        $this->assertIsArray($models);
        $this->assertCount(2, $models);
        $this->assertInstanceOf(TestModel::class, $models[0]);
    }

    /**
     * @test
     */
    public function testAllReturnsEmptyArrayWhenNoRecords(): void
    {
        Model::setConnection($this->pdo);

        $models = TestModel::all();

        $this->assertIsArray($models);
        $this->assertCount(0, $models);
    }

    /**
     * @test
     */
    public function testWhereFiltersRecords(): void
    {
        Model::setConnection($this->pdo);
        $this->insertTestRecord('Active User', 'active@test.com', 'active');
        $this->insertTestRecord('Inactive User', 'inactive@test.com', 'inactive');

        $models = TestModel::where(['status' => 'active']);

        $this->assertCount(1, $models);
        $this->assertEquals('Active User', $models[0]->name);
    }

    /**
     * @test
     */
    public function testWhereWithOperator(): void
    {
        Model::setConnection($this->pdo);
        $this->insertTestRecord('User 1', 'user1@test.com');
        $this->insertTestRecord('User 2', 'user2@test.com');
        $this->insertTestRecord('User 3', 'user3@test.com');

        $models = TestModel::where(['id' => ['>', 1]]);

        $this->assertCount(2, $models);
    }

    /**
     * @test
     */
    public function testFirstWhereReturnsFirstMatch(): void
    {
        Model::setConnection($this->pdo);
        $this->insertTestRecord('User 1', 'test@test.com');
        $this->insertTestRecord('User 2', 'test@test.com');

        $model = TestModel::firstWhere(['email' => 'test@test.com']);

        $this->assertInstanceOf(TestModel::class, $model);
        $this->assertEquals('User 1', $model->name);
    }

    /**
     * @test
     */
    public function testFirstWhereReturnsNullWhenNoMatch(): void
    {
        Model::setConnection($this->pdo);

        $model = TestModel::firstWhere(['email' => 'notfound@test.com']);

        $this->assertNull($model);
    }

    // =========================================================================
    // Tests CRUD - Update
    // =========================================================================

    /**
     * @test
     */
    public function testSaveUpdatesExistingRecord(): void
    {
        Model::setConnection($this->pdo);
        $this->insertTestRecord('Original', 'original@test.com');

        $model = TestModel::find(1);
        $model->name = 'Updated';
        $result = $model->save();

        $this->assertTrue($result);

        $updated = TestModel::find(1);
        $this->assertEquals('Updated', $updated->name);
    }

    // =========================================================================
    // Tests CRUD - Delete
    // =========================================================================

    /**
     * @test
     */
    public function testDeleteRemovesRecord(): void
    {
        Model::setConnection($this->pdo);
        $this->insertTestRecord('To Delete', 'delete@test.com');

        $model = TestModel::find(1);
        $result = $model->delete();

        $this->assertTrue($result);
        $this->assertNull(TestModel::find(1));
    }

    /**
     * @test
     */
    public function testDeleteReturnsFalseForNewModel(): void
    {
        Model::setConnection($this->pdo);

        $model = new TestModel(['name' => 'New']);
        $result = $model->delete();

        $this->assertFalse($result);
    }

    // =========================================================================
    // Tests Count
    // =========================================================================

    /**
     * @test
     */
    public function testCountReturnsTotal(): void
    {
        Model::setConnection($this->pdo);
        $this->insertTestRecord('User 1', 'user1@test.com');
        $this->insertTestRecord('User 2', 'user2@test.com');

        $count = TestModel::count();

        $this->assertEquals(2, $count);
    }

    /**
     * @test
     */
    public function testCountWithConditions(): void
    {
        Model::setConnection($this->pdo);
        $this->insertTestRecord('Active 1', 'active1@test.com', 'active');
        $this->insertTestRecord('Active 2', 'active2@test.com', 'active');
        $this->insertTestRecord('Inactive', 'inactive@test.com', 'inactive');

        $count = TestModel::count(['status' => 'active']);

        $this->assertEquals(2, $count);
    }

    // =========================================================================
    // Tests Transactions
    // =========================================================================

    /**
     * @test
     */
    public function testBeginTransactionStartsTransaction(): void
    {
        Model::setConnection($this->pdo);

        $result = TestModel::beginTransaction();

        $this->assertTrue($result);
        $this->assertTrue($this->pdo->inTransaction());

        $this->pdo->rollBack();
    }

    /**
     * @test
     */
    public function testCommitSavesChanges(): void
    {
        Model::setConnection($this->pdo);

        TestModel::beginTransaction();
        $this->insertTestRecord('Transaction User', 'trans@test.com');
        $result = TestModel::commit();

        $this->assertTrue($result);
        $this->assertNotNull(TestModel::find(1));
    }

    /**
     * @test
     */
    public function testRollBackRevertsChanges(): void
    {
        Model::setConnection($this->pdo);

        TestModel::beginTransaction();
        $this->insertTestRecord('Rollback User', 'rollback@test.com');
        $result = TestModel::rollBack();

        $this->assertTrue($result);
        $this->assertEquals(0, TestModel::count());
    }

    // =========================================================================
    // Tests Raw Queries
    // =========================================================================

    /**
     * @test
     */
    public function testRawExecutesPreparedStatement(): void
    {
        Model::setConnection($this->pdo);
        $this->insertTestRecord('Raw User', 'raw@test.com');

        $stmt = TestModel::raw(
            'SELECT * FROM test_models WHERE email = :email',
            ['email' => 'raw@test.com']
        );

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Raw User', $result['name']);
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    private function insertTestRecord(string $name, string $email, string $status = 'active'): void
    {
        $this->pdo->exec("INSERT INTO test_models (name, email, status) VALUES ('{$name}', '{$email}', '{$status}')");
    }
}

/**
 * Modèle de test concret pour les tests unitaires
 */
class TestModel extends Model
{
    protected string $table = 'test_models';
    protected string $primaryKey = 'id';
    protected array $fillable = ['name', 'email', 'status', 'created_at'];
}
