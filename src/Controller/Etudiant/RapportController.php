<?php
declare(strict_types=1);

namespace App\Controller\Etudiant;

use App\Controller\AbstractController;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class RapportController extends AbstractController
{
    public function index(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/index', [
            'matricule' => $matricule,
        ]);
    }

    public function create(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/create', [
            'matricule' => $matricule,
        ]);
    }

    public function edit(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/edit', [
            'matricule' => $matricule,
        ]);
    }

    public function save(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/save', [
            'matricule' => $matricule,
        ]);
    }

    public function submit(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/submit', [
            'matricule' => $matricule,
        ]);
    }

    public function show(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/show', [
            'matricule' => $matricule,
        ]);
    }

    public function versions(Request $request): ResponseInterface
    {
        $matricule = $this->getUser()?->getMatriculeEtudiant();

        if ($matricule === null || $matricule === '') {
            return new Response(403, ['Content-Type' => 'text/plain'], 'Acces refuse');
        }

        return $this->render('etudiant/rapport/versions', [
            'matricule' => $matricule,
        ]);
    }
}
