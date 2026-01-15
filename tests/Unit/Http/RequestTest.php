<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Src\Http\Request;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour Request
 */
class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset the Request singleton before each test
        Request::reset();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Clean up after each test
        Request::reset();
    }

    /**
     * @test
     */
    public function testBasePathReturnsEmptyForRootDirectory(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        Request::reset();
        
        $this->assertEquals('', Request::basePath());
    }

    /**
     * @test
     */
    public function testBasePathReturnsEmptyForBackslashDirectory(): void
    {
        $_SERVER['SCRIPT_NAME'] = '\\index.php';
        Request::reset();
        
        $this->assertEquals('', Request::basePath());
    }

    /**
     * @test
     */
    public function testBasePathReturnsEmptyForDotDirectory(): void
    {
        $_SERVER['SCRIPT_NAME'] = './index.php';
        Request::reset();
        
        $this->assertEquals('', Request::basePath());
    }

    /**
     * @test
     */
    public function testBasePathReturnsCorrectPathForSubdirectory(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/check.master/index.php';
        Request::reset();
        
        $this->assertEquals('/check.master', Request::basePath());
    }

    /**
     * @test
     */
    public function testBasePathReturnsCorrectPathForDeepSubdirectory(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/var/www/myapp/index.php';
        Request::reset();
        
        $this->assertEquals('/var/www/myapp', Request::basePath());
    }

    /**
     * @test
     */
    public function testBasePathTrimsTrailingSlash(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/myapp/index.php';
        Request::reset();
        
        $basePath = Request::basePath();
        $this->assertEquals('/myapp', $basePath);
        $this->assertStringEndsNotWith('/', $basePath);
    }

    /**
     * @test
     */
    public function testUriRemovesBasePathFromRequestUri(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/check.master/index.php';
        $_SERVER['REQUEST_URI'] = '/check.master/connexion';
        Request::reset();
        
        $this->assertEquals('/connexion', Request::uri());
    }

    /**
     * @test
     */
    public function testUriRemovesQueryString(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/connexion?error=invalid';
        Request::reset();
        
        $this->assertEquals('/connexion', Request::uri());
    }

    /**
     * @test
     */
    public function testUriReturnsSlashForRoot(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['REQUEST_URI'] = '/';
        Request::reset();
        
        $this->assertEquals('/', Request::uri());
    }

    /**
     * @test
     */
    public function testUriAlwaysStartsWithSlash(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/check.master/index.php';
        $_SERVER['REQUEST_URI'] = '/check.master/';
        Request::reset();
        
        $uri = Request::uri();
        $this->assertStringStartsWith('/', $uri);
    }

    /**
     * @test
     */
    public function testMethodReturnsCorrectHttpMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        Request::reset();
        
        $this->assertEquals('POST', Request::method());
    }

    /**
     * @test
     */
    public function testIsPostReturnsTrueForPostRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        Request::reset();
        
        $this->assertTrue(Request::isPost());
    }

    /**
     * @test
     */
    public function testIsGetReturnsTrueForGetRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        Request::reset();
        
        $this->assertTrue(Request::isGet());
    }

    /**
     * @test
     */
    public function testIsAjaxReturnsTrueForXmlHttpRequest(): void
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        Request::reset();
        
        $this->assertTrue(Request::isAjax());
    }

    /**
     * @test
     */
    public function testIsAjaxReturnsFalseForNormalRequest(): void
    {
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        Request::reset();
        
        $this->assertFalse(Request::isAjax());
    }
}
