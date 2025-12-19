<?php

declare(strict_types=1);

namespace Tests\Unit\Orm;

use App\Orm\Model;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour le trait Relations
 */
class RelationsTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        // Table utilisateurs
        $this->pdo->exec('
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255)
            )
        ');

        // Table profils (1:1 avec users)
        $this->pdo->exec('
            CREATE TABLE profiles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                bio TEXT
            )
        ');

        // Table posts (1:N avec users)
        $this->pdo->exec('
            CREATE TABLE posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                title VARCHAR(255)
            )
        ');

        // Table roles
        $this->pdo->exec('
            CREATE TABLE roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255)
            )
        ');

        // Table pivot role_user (N:M)
        $this->pdo->exec('
            CREATE TABLE role_user (
                user_id INTEGER,
                role_id INTEGER
            )
        ');

        // Insérer données de test
        $this->pdo->exec("INSERT INTO users (name) VALUES ('Alice')");
        $this->pdo->exec("INSERT INTO users (name) VALUES ('Bob')");
        $this->pdo->exec("INSERT INTO profiles (user_id, bio) VALUES (1, 'Bio of Alice')");
        $this->pdo->exec("INSERT INTO posts (user_id, title) VALUES (1, 'Post 1 by Alice')");
        $this->pdo->exec("INSERT INTO posts (user_id, title) VALUES (1, 'Post 2 by Alice')");
        $this->pdo->exec("INSERT INTO posts (user_id, title) VALUES (2, 'Post by Bob')");
        $this->pdo->exec("INSERT INTO roles (name) VALUES ('Admin')");
        $this->pdo->exec("INSERT INTO roles (name) VALUES ('Editor')");
        $this->pdo->exec("INSERT INTO role_user (user_id, role_id) VALUES (1, 1)");
        $this->pdo->exec("INSERT INTO role_user (user_id, role_id) VALUES (1, 2)");

        Model::setConnection($this->pdo);
    }

    // =========================================================================
    // Tests hasOne
    // =========================================================================

    /**
     * @test
     */
    public function testHasOneReturnsRelatedModel(): void
    {
        $user = UserModel::find(1);
        $profile = $user->profile();

        $this->assertInstanceOf(ProfileModel::class, $profile);
        $this->assertEquals('Bio of Alice', $profile->bio);
    }

    /**
     * @test
     */
    public function testHasOneReturnsNullWhenNoRelation(): void
    {
        $user = UserModel::find(2);
        $profile = $user->profile();

        $this->assertNull($profile);
    }

    /**
     * @test
     */
    public function testHasOneCachesResult(): void
    {
        $user = UserModel::find(1);

        $profile1 = $user->profile();
        $profile2 = $user->profile();

        $this->assertSame($profile1, $profile2);
    }

    // =========================================================================
    // Tests hasMany
    // =========================================================================

    /**
     * @test
     */
    public function testHasManyReturnsArrayOfModels(): void
    {
        $user = UserModel::find(1);
        $posts = $user->posts();

        $this->assertIsArray($posts);
        $this->assertCount(2, $posts);
        $this->assertInstanceOf(PostModel::class, $posts[0]);
    }

    /**
     * @test
     */
    public function testHasManyReturnsEmptyArrayWhenNoRelations(): void
    {
        // Créer un utilisateur sans posts
        $this->pdo->exec("INSERT INTO users (name) VALUES ('Charlie')");

        $user = UserModel::find(3);
        $posts = $user->posts();

        $this->assertIsArray($posts);
        $this->assertEmpty($posts);
    }

    /**
     * @test
     */
    public function testHasManyCachesResult(): void
    {
        $user = UserModel::find(1);

        $posts1 = $user->posts();
        $posts2 = $user->posts();

        $this->assertSame($posts1, $posts2);
    }

    // =========================================================================
    // Tests belongsTo
    // =========================================================================

    /**
     * @test
     */
    public function testBelongsToReturnsRelatedModel(): void
    {
        $post = PostModel::find(1);
        $user = $post->user();

        $this->assertInstanceOf(UserModel::class, $user);
        $this->assertEquals('Alice', $user->name);
    }

    /**
     * @test
     */
    public function testBelongsToReturnsNullWhenForeignKeyNull(): void
    {
        // Créer un post sans user_id
        $this->pdo->exec("INSERT INTO posts (title) VALUES ('Orphan Post')");

        $post = PostModel::find(4);
        $user = $post->user();

        $this->assertNull($user);
    }

    // =========================================================================
    // Tests belongsToMany
    // =========================================================================

    /**
     * @test
     */
    public function testBelongsToManyReturnsArrayOfModels(): void
    {
        $user = UserModel::find(1);
        $roles = $user->roles();

        $this->assertIsArray($roles);
        $this->assertCount(2, $roles);
        $this->assertInstanceOf(RoleModel::class, $roles[0]);
    }

    /**
     * @test
     */
    public function testBelongsToManyReturnsEmptyArrayWhenNoRelations(): void
    {
        $user = UserModel::find(2);
        $roles = $user->roles();

        $this->assertIsArray($roles);
        $this->assertEmpty($roles);
    }

    // =========================================================================
    // Tests Relation Helpers
    // =========================================================================

    /**
     * @test
     */
    public function testSetRelationManually(): void
    {
        $user = new UserModel(['name' => 'Test']);
        $profile = new ProfileModel(['bio' => 'Test Bio']);

        $user->setRelation('profile', $profile);

        $this->assertSame($profile, $user->getRelation('profile'));
    }

    /**
     * @test
     */
    public function testGetRelationReturnsNullWhenNotLoaded(): void
    {
        $user = new UserModel(['name' => 'Test']);

        $this->assertNull($user->getRelation('profile'));
    }

    /**
     * @test
     */
    public function testRelationLoadedReturnsTrueWhenLoaded(): void
    {
        $user = UserModel::find(1);
        $user->profile();

        $this->assertTrue($user->relationLoaded('profile'));
    }

    /**
     * @test
     */
    public function testRelationLoadedReturnsFalseWhenNotLoaded(): void
    {
        $user = UserModel::find(1);

        $this->assertFalse($user->relationLoaded('profile'));
    }

    /**
     * @test
     */
    public function testGetRelationsReturnsAllLoadedRelations(): void
    {
        $user = UserModel::find(1);
        $user->profile();
        $user->posts();

        $relations = $user->getRelations();

        $this->assertArrayHasKey('profile', $relations);
        $this->assertArrayHasKey('posts', $relations);
    }

    /**
     * @test
     */
    public function testGetTableReturnsTableName(): void
    {
        $user = new UserModel();

        $this->assertEquals('users', $user->getTable());
    }

    /**
     * @test
     */
    public function testGetPrimaryKeyReturnsPrimaryKeyName(): void
    {
        $user = new UserModel();

        $this->assertEquals('id', $user->getPrimaryKey());
    }

    // =========================================================================
    // Tests Eager Loading
    // =========================================================================

    /**
     * @test
     */
    public function testWithLoadsRelationsEagerly(): void
    {
        $users = UserModel::with('posts');

        $this->assertIsArray($users);
        $this->assertGreaterThan(0, count($users));

        // Vérifier que la relation est chargée
        $this->assertTrue($users[0]->relationLoaded('posts'));
    }

    /**
     * @test
     */
    public function testWithMultipleRelations(): void
    {
        $users = UserModel::with(['posts', 'profile']);

        $this->assertTrue($users[0]->relationLoaded('posts'));
        $this->assertTrue($users[0]->relationLoaded('profile'));
    }
}

// =============================================================================
// Modèles de test
// =============================================================================

class UserModel extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    protected array $fillable = ['name'];

    public function profile(): ?Model
    {
        return $this->hasOne(ProfileModel::class, 'user_id');
    }

    public function posts(): array
    {
        return $this->hasMany(PostModel::class, 'user_id');
    }

    public function roles(): array
    {
        return $this->belongsToMany(
            RoleModel::class,
            'role_user',
            'user_id',
            'role_id'
        );
    }
}

class ProfileModel extends Model
{
    protected string $table = 'profiles';
    protected string $primaryKey = 'id';
    protected array $fillable = ['user_id', 'bio'];

    public function user(): ?Model
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
}

class PostModel extends Model
{
    protected string $table = 'posts';
    protected string $primaryKey = 'id';
    protected array $fillable = ['user_id', 'title'];

    public function user(): ?Model
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
}

class RoleModel extends Model
{
    protected string $table = 'roles';
    protected string $primaryKey = 'id';
    protected array $fillable = ['name'];
}
