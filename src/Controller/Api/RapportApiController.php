<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Service\Auth\AuthenticationService;
use App\Service\Auth\AuthorizationService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\Rapport\RapportService;

class RapportApiController extends AbstractController
{
    private RapportService $rapportService;

    public function __construct(
        ContainerInterface $container,
        AuthenticationService $authenticationService,
        AuthorizationService $authorizationService,
        RapportService $rapportService
    ) {
        parent::__construct($container, $authenticationService, $authorizationService);
        $this->rapportService = $rapportService;
    }

    public function versions(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $versions = $this->rapportService->getVersions($id);

        return $this->json(['versions' => $versions]);
    }

    public function autoSave(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $user = $this->getUser();

        if (!$user || !isset($data['rapport_id']) || !isset($data['content'])) {
            return $this->json(['success' => false, 'error' => 'Donnees manquantes'], 400);
        }

        $saved = $this->rapportService->saveContent(
            (int) $data['rapport_id'],
            $data['content'],
            $user->getIdUtilisateur(),
            'auto_save'
        );

        return $this->json(['success' => $saved, 'timestamp' => date('H:i:s')]);
    }
}
