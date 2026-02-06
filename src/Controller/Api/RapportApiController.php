<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\Rapport\RapportService;

class RapportApiController extends AbstractController
{
    private RapportService $rapportService;

    public function __construct(RapportService $rapportService)
    {
        $this->rapportService = $rapportService;
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
