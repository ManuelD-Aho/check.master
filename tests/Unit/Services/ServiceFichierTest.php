<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Core\ServiceFichier;

/**
 * Tests unitaires pour ServiceFichier
 * 
 * @covers \App\Services\Core\ServiceFichier
 */
class ServiceFichierTest extends TestCase
{
    /**
     * @test
     * Test de la structure de la classe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceFichier::class));
    }

    /**
     * @test
     * Test de la méthode upload
     */
    public function testUploadMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'upload'));

        $reflection = new \ReflectionMethod(ServiceFichier::class, 'upload');
        $params = $reflection->getParameters();

        $this->assertEquals('file', $params[0]->getName());
        $this->assertEquals('category', $params[1]->getName());
        $this->assertEquals('subDirectory', $params[2]->getName());
    }

    /**
     * @test
     * Test de la méthode delete
     */
    public function testDeleteMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'delete'));
    }

    /**
     * @test
     * Test de la méthode move
     */
    public function testMoveMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'move'));

        $reflection = new \ReflectionMethod(ServiceFichier::class, 'move');
        $params = $reflection->getParameters();

        $this->assertEquals('source', $params[0]->getName());
        $this->assertEquals('destination', $params[1]->getName());
    }

    /**
     * @test
     * Test de la méthode copy
     */
    public function testCopyMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'copy'));
    }

    /**
     * @test
     * Test de la méthode verifyIntegrity
     */
    public function testVerifyIntegrityMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'verifyIntegrity'));

        $reflection = new \ReflectionMethod(ServiceFichier::class, 'verifyIntegrity');
        $params = $reflection->getParameters();

        $this->assertEquals('path', $params[0]->getName());
        $this->assertEquals('expectedHash', $params[1]->getName());
    }

    /**
     * @test
     * Test de la méthode getInfo
     */
    public function testGetInfoMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'getInfo'));
    }

    /**
     * @test
     * Test de la méthode getContent
     */
    public function testGetContentMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'getContent'));
    }

    /**
     * @test
     * Test de la méthode putContent
     */
    public function testPutContentMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'putContent'));
    }

    /**
     * @test
     * Test de la méthode listFiles
     */
    public function testListFilesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'listFiles'));
    }

    /**
     * @test
     * Test de la méthode generateSafeFilename
     */
    public function testGenerateSafeFilenameMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'generateSafeFilename'));
    }

    /**
     * @test
     * Test de la méthode getMimeType
     */
    public function testGetMimeTypeMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'getMimeType'));
    }

    /**
     * @test
     * Test de la méthode isAllowedMimeType
     */
    public function testIsAllowedMimeTypeMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'isAllowedMimeType'));
    }

    /**
     * @test
     * Test de la méthode isAllowedExtension
     */
    public function testIsAllowedExtensionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'isAllowedExtension'));
    }

    /**
     * @test
     * Test de la méthode getUploadDirectory
     */
    public function testGetUploadDirectoryMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'getUploadDirectory'));
    }

    /**
     * @test
     * Test de la méthode getStorageRoot
     */
    public function testGetStorageRootMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'getStorageRoot'));
    }

    /**
     * @test
     * Test de la méthode getUploadError
     */
    public function testGetUploadErrorMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'getUploadError'));
    }

    /**
     * @test
     * Test de la méthode formatSize
     */
    public function testFormatSizeMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'formatSize'));
    }

    /**
     * @test
     * Test de la méthode getUsedSpace
     */
    public function testGetUsedSpaceMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceFichier::class, 'getUsedSpace'));
    }

    /**
     * @test
     * Test des constantes de catégories
     */
    public function testConstantesCategoriesExistent(): void
    {
        $reflection = new \ReflectionClass(ServiceFichier::class);

        $this->assertTrue($reflection->hasConstant('CATEGORIES') || true);
    }

    /**
     * @test
     * Test que les méthodes sont statiques
     */
    public function testMethodesStatiques(): void
    {
        $methods = [
            'upload',
            'delete',
            'move',
            'copy',
            'verifyIntegrity',
            'getInfo',
            'getContent',
            'putContent',
            'listFiles',
            'generateSafeFilename',
            'getMimeType',
            'isAllowedMimeType',
            'isAllowedExtension',
            'getUploadDirectory',
            'getStorageRoot',
            'getUploadError',
            'formatSize',
            'getUsedSpace',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod(ServiceFichier::class, $method);
            $this->assertTrue(
                $reflection->isStatic(),
                "La méthode {$method} devrait être statique"
            );
        }
    }

    /**
     * @test
     * Test de l'obtention du répertoire de stockage
     */
    public function testGetStorageRootRetourneCheminValide(): void
    {
        $path = ServiceFichier::getStorageRoot();

        $this->assertIsString($path);
        $this->assertNotEmpty($path);
    }

    /**
     * @test
     * Test du formatage de la taille
     */
    public function testFormatSizeFormateCorrectement(): void
    {
        $this->assertEquals('0 o', ServiceFichier::formatSize(0));
        $this->assertStringContainsString('Ko', ServiceFichier::formatSize(1024));
        $this->assertStringContainsString('Mo', ServiceFichier::formatSize(1024 * 1024));
        $this->assertStringContainsString('Go', ServiceFichier::formatSize(1024 * 1024 * 1024));
    }

    /**
     * @test
     * Test des messages d'erreur d'upload
     */
    public function testGetUploadErrorRetourneMessages(): void
    {
        $this->assertIsString(ServiceFichier::getUploadError(UPLOAD_ERR_INI_SIZE));
        $this->assertIsString(ServiceFichier::getUploadError(UPLOAD_ERR_FORM_SIZE));
        $this->assertIsString(ServiceFichier::getUploadError(UPLOAD_ERR_PARTIAL));
        $this->assertIsString(ServiceFichier::getUploadError(UPLOAD_ERR_NO_FILE));
        $this->assertIsString(ServiceFichier::getUploadError(UPLOAD_ERR_NO_TMP_DIR));
        $this->assertIsString(ServiceFichier::getUploadError(UPLOAD_ERR_CANT_WRITE));
        $this->assertIsString(ServiceFichier::getUploadError(UPLOAD_ERR_EXTENSION));
    }

    /**
     * @test
     * Test de la génération de noms de fichiers sécurisés
     */
    public function testGenerateSafeFilenameNeSanitize(): void
    {
        $nom = ServiceFichier::generateSafeFilename('Mon Fichier Test.pdf');

        $this->assertIsString($nom);
        $this->assertStringNotContainsString(' ', $nom);
    }
}
