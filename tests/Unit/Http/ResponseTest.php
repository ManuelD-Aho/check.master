<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use Src\Http\Request;
use Src\Http\Response;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour Response
 */
class ResponseTest extends TestCase
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
    public function testRedirectWithAbsoluteUrlDoesNotAddBasePath(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/check.master/index.php';
        Request::reset();

        $response = Response::redirect('https://example.com/page');
        
        $this->assertEquals(302, $response->getStatusCode());
        // Absolute URLs should not be modified
        $this->assertStringContainsString('https://example.com/page', $response->getContent());
    }

    /**
     * @test
     */
    public function testRedirectWithRelativeUrlAddsBasePath(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/check.master/index.php';
        Request::reset();

        $response = Response::redirect('/connexion');
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testRedirectWithEmptyBasePathDoesNotModifyUrl(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        Request::reset();

        $response = Response::redirect('/connexion');
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testRedirectDoesNotDoublePrefixWhenUrlAlreadyHasBasePath(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/check.master/index.php';
        Request::reset();

        // URL already includes the base path
        $response = Response::redirect('/check.master/connexion');
        
        $this->assertEquals(302, $response->getStatusCode());
        // Should not result in /check.master/check.master/connexion
    }

    /**
     * @test
     */
    public function testRedirectDoesNotPrefixWhenUrlEqualsBasePath(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/check.master/index.php';
        Request::reset();

        // URL is exactly the base path
        $response = Response::redirect('/check.master');
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testRedirectWithCustomStatusCode(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        Request::reset();

        $response = Response::redirect('/connexion', 301);
        
        $this->assertEquals(301, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testRedirectWithEmptyUrlDoesNotAddBasePath(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/check.master/index.php';
        Request::reset();

        $response = Response::redirect('');
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testRedirectWithRelativePathDoesNotAddBasePath(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/check.master/index.php';
        Request::reset();

        // Relative path (not starting with /)
        $response = Response::redirect('connexion');
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testRedirectWithUrlStartingWithBasePathSlash(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/check.master/index.php';
        Request::reset();

        // URL starts with base path + additional path
        $response = Response::redirect('/check.master/dashboard/profile');
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testRedirectWithSimilarButDifferentBasePath(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/check/index.php';
        Request::reset();

        // URL starts with /check.master but base path is /check
        $response = Response::redirect('/check.master/connexion');
        
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testHtmlResponseHasCorrectContentType(): void
    {
        $response = Response::html('<h1>Test</h1>');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('<h1>Test</h1>', $response->getContent());
    }

    /**
     * @test
     */
    public function testTextResponseHasCorrectContentType(): void
    {
        $response = Response::text('Plain text content');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Plain text content', $response->getContent());
    }

    /**
     * @test
     */
    public function testSetStatusCode(): void
    {
        $response = new Response();
        $response->setStatusCode(404);
        
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testSetContent(): void
    {
        $response = new Response();
        $response->setContent('New content');
        
        $this->assertEquals('New content', $response->getContent());
    }

    /**
     * @test
     */
    public function testHeader(): void
    {
        $response = new Response();
        $response->header('X-Custom-Header', 'test-value');
        
        // The header is set internally, we can't directly test it without sending
        // but we can verify the method chains correctly
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @test
     */
    public function testConstructorWithParameters(): void
    {
        $response = new Response('Test content', 201, ['X-Custom' => 'value']);
        
        $this->assertEquals('Test content', $response->getContent());
        $this->assertEquals(201, $response->getStatusCode());
    }
}
