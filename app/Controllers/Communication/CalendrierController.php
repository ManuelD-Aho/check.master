<?php

declare(strict_types=1);

namespace App\Controllers\Communication;

use App\Services\Soutenance\ServiceCalendrier;
use Src\Http\Response;
use Src\Http\Request;

/**
 * Contrôleur du calendrier
 * 
 * Gestion du planning et des disponibilités.
 * 
 * @see PRD 05 - Communication
 */
class CalendrierController
{
    private ServiceCalendrier $serviceCalendrier;

    public function __construct()
    {
        $this->serviceCalendrier = new ServiceCalendrier();
    }

    /**
     * Retourne les salles disponibles
     */
    public function sallesDisponibles(): Response
    {
        $date = Request::get('date');
        $heureDebut = Request::get('heure_debut');
        $heureFin = Request::get('heure_fin');

        if (empty($date) || empty($heureDebut) || empty($heureFin)) {
            return Response::json(['error' => 'La date et les heures sont requises'], 422);
        }

        $salles = $this->serviceCalendrier->getSallesDisponibles($date, $heureDebut, $heureFin);

        return Response::json([
            'success' => true,
            'data' => $salles,
        ]);
    }

    /**
     * Retourne le planning d'une journée
     */
    public function planning(string $date): Response
    {
        // Valider le format de la date
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return Response::json(['error' => 'Format de date invalide (YYYY-MM-DD)'], 422);
        }

        $planning = $this->serviceCalendrier->getPlanningJour($date);

        return Response::json([
            'success' => true,
            'data' => $planning,
        ]);
    }

    /**
     * Vérifie les conflits pour une planification
     */
    public function verifierConflits(): Response
    {
        $dossierId = (int) Request::post('dossier_id');
        $date = Request::post('date');
        $heureDebut = Request::post('heure_debut');
        $heureFin = Request::post('heure_fin');
        $salleId = Request::post('salle_id');

        if (empty($date) || empty($heureDebut) || empty($heureFin)) {
            return Response::json(['error' => 'La date et les heures sont requises'], 422);
        }

        $conflits = [];

        // Vérifier conflit de salle
        if ($salleId) {
            if ($this->serviceCalendrier->salleOccupee((int) $salleId, $date, $heureDebut, $heureFin)) {
                $conflits[] = [
                    'type' => 'salle',
                    'message' => 'La salle est déjà occupée à cette date et heure',
                ];
            }
        }

        // Vérifier conflits de jury si dossier spécifié
        if ($dossierId) {
            $conflitsJury = $this->serviceCalendrier->verifierConflitsJury($dossierId, $date, $heureDebut, $heureFin);
            foreach ($conflitsJury as $membre) {
                $conflits[] = [
                    'type' => 'jury',
                    'message' => "{$membre} a une autre soutenance à cette date et heure",
                ];
            }
        }

        return Response::json([
            'success' => true,
            'data' => [
                'conflits' => $conflits,
                'a_conflits' => !empty($conflits),
            ],
        ]);
    }
}
