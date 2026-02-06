<?php
declare(strict_types=1);

namespace App\Service\Commission;

use App\Entity\Commission\AffectationEncadrant;
use App\Entity\Commission\RoleEncadrement;
use App\Entity\Report\Rapport;
use App\Entity\Staff\Enseignant;
use App\Entity\User\Utilisateur;
use App\Repository\Commission\AffectationEncadrantRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class AffectationService
{
    private EntityManagerInterface $entityManager;
    private AffectationEncadrantRepository $affectationRepository;

    public function __construct(EntityManagerInterface $entityManager, AffectationEncadrantRepository $affectationRepository)
    {
        $this->entityManager = $entityManager;
        $this->affectationRepository = $affectationRepository;
    }

    public function assignDirecteurMemoire(
        Rapport $rapport,
        Enseignant $enseignant,
        Utilisateur $affecteur,
        ?string $commentaire = null
    ): AffectationEncadrant {
        return $this->assignEncadrant($rapport, $enseignant, $affecteur, RoleEncadrement::DirecteurMemoire, $commentaire);
    }

    public function assignEncadreurPedagogique(
        Rapport $rapport,
        Enseignant $enseignant,
        Utilisateur $affecteur,
        ?string $commentaire = null
    ): AffectationEncadrant {
        return $this->assignEncadrant($rapport, $enseignant, $affecteur, RoleEncadrement::EncadreurPedagogique, $commentaire);
    }

    public function getAffectationsForRapport(Rapport $rapport): array
    {
        if ($rapport->getIdRapport() === null) {
            return [];
        }

        return $this->affectationRepository->findByRapport($rapport->getIdRapport());
    }

    private function assignEncadrant(
        Rapport $rapport,
        Enseignant $enseignant,
        Utilisateur $affecteur,
        RoleEncadrement $role,
        ?string $commentaire
    ): AffectationEncadrant {
        $this->entityManager->beginTransaction();

        try {
            $existing = match ($role) {
                RoleEncadrement::DirecteurMemoire => $rapport->getIdRapport() !== null
                    ? $this->affectationRepository->findDirecteurMemoire($rapport->getIdRapport())
                    : null,
                RoleEncadrement::EncadreurPedagogique => $rapport->getIdRapport() !== null
                    ? $this->affectationRepository->findEncadreurPedagogique($rapport->getIdRapport())
                    : null
            };

            $affectation = $existing ?? new AffectationEncadrant();
            $affectation->setRapport($rapport)
                ->setEnseignant($enseignant)
                ->setRoleEncadrement($role)
                ->setAffecteur($affecteur)
                ->setDateAffectation(new DateTimeImmutable())
                ->setCommentaire($commentaire);

            $this->entityManager->persist($affectation);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $affectation;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
