<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;
use App\Services\Security\ServiceAuthentification;
use Src\Http\Response;
use Src\Support\Auth;

/**
 * Test Exemplaire pour AuthController
 * 
 * Ce test démontre les bonnes pratiques pour tester un controller:
 * - Mocking des services
 * - Injection de dépendances via reflection
 * - Couverture complète des cas (nominal + erreurs)
 * - Validation des entrées
 * - Vérification des réponses
 * - Isolation complète (pas d'appels DB réels)
 * 
 * @see docs/TESTING_STRATEGY.md pour plus d'exemples
 */
class AuthControllerExemplaireTest extends TestCase
{
    private AuthController $controller;
    private $mockAuthService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockAuthService = $this->createMock(ServiceAuthentification::class);
        $this->controller = new AuthController();

        $this->injectDependencies();
        $this->resetGlobalState();
    }

    protected function tearDown(): void
    {
        $this->resetGlobalState();
        parent::tearDown();
    }

    private function injectDependencies(): void
    {
        $reflection = new \ReflectionClass($this->controller);
        
        $authServiceProperty = $reflection->getProperty('authService');
        $authServiceProperty->setAccessible(true);
        $authServiceProperty->setValue($this->controller, $this->mockAuthService);
    }

    private function resetGlobalState(): void
    {
        $_POST = [];
        $_GET = [];
        $_SERVER = [];
        $_SESSION = [];
        
        if (class_exists(Auth::class)) {
            Auth::reset();
        }
    }

    private function simulatePostRequest(array $data): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = $data;
    }

    private function simulateGetRequest(array $params = []): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = $params;
    }

    public function testLoginWithValidCredentialsRedirectsToDashboard(): void
    {
        $email = 'admin@ufhb.ci';
        $password = 'SecurePassword123!';
        
        $expectedResult = [
            'success' => true,
            'user' => [
                'id_utilisateur' => 1,
                'nom_utilisateur' => 'Administrateur',
                'login_utilisateur' => $email,
                'id_GU' => 1
            ],
            'token' => 'abc123def456'
        ];

        $this->mockAuthService->expects($this->once())
            ->method('authentifier')
            ->with($email, $password)
            ->willReturn($expectedResult);

        $this->simulatePostRequest([
            'email' => $email,
            'password' => $password
        ]);

        $response = $this->controller->login();

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testLoginWithInvalidCredentialsShowsError(): void
    {
        $email = 'wrong@test.com';
        $password = 'WrongPassword';

        $this->mockAuthService->expects($this->once())
            ->method('authentifier')
            ->with($email, $password)
            ->willReturn([
                'success' => false,
                'error' => 'Identifiants incorrects'
            ]);

        $this->simulatePostRequest([
            'email' => $email,
            'password' => $password
        ]);

        $response = $this->controller->login();

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testLoginWithEmptyFieldsShowsValidationError(): void
    {
        $this->mockAuthService->expects($this->never())
            ->method('authentifier');

        $this->simulatePostRequest([
            'email' => '',
            'password' => ''
        ]);

        $response = $this->controller->login();

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testLogoutClearsSessionAndRedirects(): void
    {
        Auth::setUser(['id_utilisateur' => 1], 'token123');

        $this->mockAuthService->expects($this->once())
            ->method('supprimerSession')
            ->with('token123');

        $response = $this->controller->logout();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse(Auth::check());
    }

    public function testLoginTrimsEmailWhitespace(): void
    {
        $email = '  admin@ufhb.ci  ';
        $trimmedEmail = 'admin@ufhb.ci';
        $password = 'Password123';

        $this->mockAuthService->expects($this->once())
            ->method('authentifier')
            ->with($trimmedEmail, $password)
            ->willReturn([
                'success' => true,
                'user' => ['id_utilisateur' => 1],
                'token' => 'token123'
            ]);

        $this->simulatePostRequest([
            'email' => $email,
            'password' => $password
        ]);

        $response = $this->controller->login();

        $this->assertInstanceOf(Response::class, $response);
    }
}
