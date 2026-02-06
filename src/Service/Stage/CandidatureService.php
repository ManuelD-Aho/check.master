<?php
declare(strict_types=1);

namespace App\Service\Stage;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Stage\ActionHistorique;
use App\Entity\Stage\Candidature;
use App\Entity\Stage\HistoriqueCandidature;
use App\Entity\Stage\StatutCandidature;
use App\Entity\Student\Etudiant;
use App\Entity\User\Utilisateur;
use App\Repository\Stage\CandidatureRepository;
use App\Repository\Stage\HistoriqueCandidatureRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class CandidatureService
{
    private EntityManagerInterface $entityManager;
    private CandidatureRepository $candidatureRepository;
    private HistoriqueCandidatureRepository $historiqueRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CandidatureRepository $candidatureRepository,
        HistoriqueCandidatureRepository $historiqueRepository
    ) {
        $this->entityManager = $entityManager;
        $this->candidatureRepository = $candidatureRepository;
        $this->historiqueRepository = $historiqueRepository;
    }

    public function createCandidature(Etudiant $etudiant, AnneeAcademique $anneeAcademique, Utilisateur $auteur): Candidature
    {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $candidature = new Candidature();
            $candidature->setEtudiant($etudiant)
                ->setAnneeAcademique($anneeAcademique)
                ->setStatutCandidature(StatutCandidature::Brouillon)
                ->setNombreSoumissions(1)
                ->setDateCreation($now)
                ->setDateModification($now);

            $this->entityManager->persist($candidature);
            $this->entityManager->persist($this->buildHistorique($candidature, $auteur, ActionHistorique::Creation, null));
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $candidature;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function saveDraft(Candidature $candidature, Utilisateur $auteur, ?string $commentaire = null): Candidature
    {
        $this->entityManager->beginTransaction();

        try {
            $candidature->setStatutCandidature(StatutCandidature::Brouillon)
                ->setDateModification(new DateTimeImmutable());

            $this->entityManager->persist($candidature);
            $this->entityManager->persist($this->buildHistorique($candidature, $auteur, ActionHistorique::Modification, $commentaire));
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $candidature;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function submit(Candidature $candidature, Utilisateur $auteur): Candidature
    {
        if (!$this->canSubmit($candidature)) {
            return $candidature;
        }

        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $candidature->setStatutCandidature(StatutCandidature::Soumise)
                ->setDateSoumission($now)
                ->setDateModification($now)
                ->setNombreSoumissions($candidature->getNombreSoumissions() + 1);

            $this->entityManager->persist($candidature);
            $this->entityManager->persist($this->buildHistorique($candidature, $auteur, ActionHistorique::Soumission, null));
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $candidature;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function validate(Candidature $candidature, Utilisateur $validateur, ?string $commentaire = null): Candidature
    {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $candidature->setStatutCandidature(StatutCandidature::Validee)
                ->setDateTraitement($now)
                ->setValidateur($validateur)
                ->setCommentaireValidation($commentaire)
                ->setDateModification($now);

            $this->entityManager->persist($candidature);
            $this->entityManager->persist($this->buildHistorique($candidature, $validateur, ActionHistorique::Validation, $commentaire));
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $candidature;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function reject(Candidature $candidature, Utilisateur $validateur, string $commentaire): Candidature
    {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $candidature->setStatutCandidature(StatutCandidature::Rejetee)
                ->setDateTraitement($now)
                ->setValidateur($validateur)
                ->setCommentaireValidation($commentaire)
                ->setDateModification($now);

            $this->entityManager->persist($candidature);
            $this->entityManager->persist($this->buildHistorique($candidature, $validateur, ActionHistorique::Rejet, $commentaire));
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $candidature;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function canSubmit(Candidature $candidature): bool
    {
        return $candidature->getStatutCandidature() === StatutCandidature::Brouillon;
    }

    public function canEdit(Candidature $candidature): bool
    {
        return in_array($candidature->getStatutCandidature(), [StatutCandidature::Brouillon, StatutCandidature::Rejetee], true);
    }

    public function getHistorique(Candidature $candidature): array
    {
        if ($candidature->getIdCandidature() === null) {
            return [];
        }

        return $this->historiqueRepository->findByCandidature($candidature->getIdCandidature());
    }

    private function buildHistorique(
        Candidature $candidature,
        Utilisateur $auteur,
        ActionHistorique $action,
        ?string $commentaire
    ): HistoriqueCandidature {
        $historique = new HistoriqueCandidature();
        $historique->setCandidature($candidature)
            ->setAuteur($auteur)
            ->setAction($action)
            ->setCommentaire($commentaire)
            ->setSnapshotJson($this->buildSnapshot($candidature))
            ->setDateAction(new DateTimeImmutable());

        return $historique;
    }

    private function buildSnapshot(Candidature $candidature): array
    {
        return [
            'id' => $candidature->getIdCandidature(),
            'statut' => $candidature->getStatutCandidature()->value,
            'date_soumission' => $candidature->getDateSoumission()?->format('c'),
            'date_traitement' => $candidature->getDateTraitement()?->format('c'),
            'nombre_soumissions' => $candidature->getNombreSoumissions()
        ];
    }
}
