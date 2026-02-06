<?php
declare(strict_types=1);

namespace App\Controller\Admin\Document;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use App\Service\Document\DocumentGeneratorService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DocumentController extends AbstractController
{
    private DocumentGeneratorService $documentGeneratorService;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        DocumentGeneratorService $documentGeneratorService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->documentGeneratorService = $documentGeneratorService;
    }

    public function index(Request $request): Response
    {
        $types = $this->documentGeneratorService->getAvailableTypes();

        return $this->render('admin/document/index', [
            'types' => $types,
            'user' => $this->getUser(),
        ]);
    }

    public function generate(Request $request): Response
    {
        $body = (array) $request->getParsedBody();
        $token = (string) ($body['csrf_token'] ?? '');

        if (!$this->validateCsrf($token)) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirect('/admin/documents');
        }

        $type = (string) ($body['type'] ?? '');

        if (!$this->documentGeneratorService->hasGenerator($type)) {
            $this->addFlash('error', 'Type de document inconnu.');
            return $this->redirect('/admin/documents');
        }

        try {
            $data = (array) ($body['data'] ?? []);
            $path = $this->documentGeneratorService->generate($type, $data);
            $this->addFlash('success', 'Document généré avec succès : ' . basename($path));
        } catch (\Throwable $e) {
            $this->addFlash('error', 'Erreur lors de la génération : ' . $e->getMessage());
        }

        return $this->redirect('/admin/documents');
    }
}
