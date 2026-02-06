<?php
declare(strict_types=1);

namespace App\Service\Soutenance;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Soutenance\CritereEvaluation;
use App\Entity\Soutenance\Jury;
use App\Entity\Soutenance\DecisionJury;
use App\Entity\Soutenance\NoteSoutenance;
use App\Entity\Soutenance\ResultatFinal;
use App\Entity\Soutenance\Salle;
use App\Entity\Soutenance\Soutenance;
use App\Entity\Soutenance\StatutSoutenance;
use App\Entity\Soutenance\TypePv;
use App\Entity\Student\Etudiant;
use App\Entity\User\Utilisateur;
use App\Repository\Soutenance\NoteSoutenanceRepository;
use App\Repository\Soutenance\ResultatFinalRepository;
use App\Repository\Soutenance\SoutenanceRepository;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class SoutenanceService
{
    private EntityManagerInterface $entityManager;
    private SoutenanceRepository $soutenanceRepository;
    private NoteSoutenanceRepository $noteRepository;
    private ResultatFinalRepository $resultatRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SoutenanceRepository $soutenanceRepository,
        NoteSoutenanceRepository $noteRepository,
        ResultatFinalRepository $resultatRepository
    ) {
        $this->entityManager = $entityManager;
        $this->soutenanceRepository = $soutenanceRepository;
        $this->noteRepository = $noteRepository;
        $this->resultatRepository = $resultatRepository;
    }

    public function schedule(
        Jury $jury,
        Etudiant $etudiant,
        Salle $salle,
        DateTimeInterface $dateSoutenance,
        DateTimeInterface $heureDebut,
        int $dureeMinutes,
        string $theme,
        Utilisateur $programmeur,
        ?string $observations = null
    ): Soutenance {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $soutenance = new Soutenance();
            $soutenance->setJury($jury)
                ->setEtudiant($etudiant)
                ->setSalle($salle)
                ->setDateSoutenance($dateSoutenance)
                ->setHeureDebut($heureDebut)
                ->setDureeMinutes($dureeMinutes)
                ->setHeureFin($this->computeHeureFin($heureDebut, $dureeMinutes))
                ->setThemeSoutenance($theme)
                ->setStatutSoutenance(StatutSoutenance::PROGRAMMEE)
                ->setObservations($observations)
                ->setProgrammeur($programmeur)
                ->setDateCreation($now)
                ->setDateModification($now);

            $this->entityManager->persist($soutenance);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $soutenance;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function reschedule(
        Soutenance $soutenance,
        DateTimeInterface $dateSoutenance,
        DateTimeInterface $heureDebut,
        ?Salle $salle = null,
        ?int $dureeMinutes = null
    ): Soutenance {
        $this->entityManager->beginTransaction();

        try {
            $minutes = $dureeMinutes ?? $soutenance->getDureeMinutes();
            if ($salle !== null) {
                $soutenance->setSalle($salle);
            }

            $soutenance->setDateSoutenance($dateSoutenance)
                ->setHeureDebut($heureDebut)
                ->setDureeMinutes($minutes)
                ->setHeureFin($this->computeHeureFin($heureDebut, $minutes))
                ->setStatutSoutenance(StatutSoutenance::REPORTEE)
                ->setDateModification(new DateTimeImmutable());

            $this->entityManager->persist($soutenance);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $soutenance;
    }

    public function cancel(Soutenance $soutenance, ?string $observations = null): Soutenance
    {
        $this->entityManager->beginTransaction();

        try {
            $soutenance->setStatutSoutenance(StatutSoutenance::ANNULEE)
                ->setObservations($observations)
                ->setDateModification(new DateTimeImmutable());

            $this->entityManager->persist($soutenance);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $soutenance;
    }

    public function start(Soutenance $soutenance): Soutenance
    {
        $this->entityManager->beginTransaction();

        try {
            $soutenance->setStatutSoutenance(StatutSoutenance::EN_COURS)
                ->setDateModification(new DateTimeImmutable());

            $this->entityManager->persist($soutenance);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $soutenance;
    }

    public function complete(Soutenance $soutenance): Soutenance
    {
        $this->entityManager->beginTransaction();

        try {
            $soutenance->setStatutSoutenance(StatutSoutenance::TERMINEE)
                ->setDateModification(new DateTimeImmutable());

            if ($soutenance->getHeureFin() === null) {
                $soutenance->setHeureFin(new DateTimeImmutable());
            }

            $this->entityManager->persist($soutenance);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $soutenance;
    }

    public function recordNote(
        Soutenance $soutenance,
        CritereEvaluation $critere,
        string $note,
        Utilisateur $utilisateur,
        ?string $commentaire = null
    ): NoteSoutenance {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $noteSoutenance = new NoteSoutenance();
            $noteSoutenance->setSoutenance($soutenance)
                ->setCritere($critere)
                ->setNote($note)
                ->setCommentaire($commentaire)
                ->setUtilisateurSaisie($utilisateur)
                ->setDateSaisie($now);

            $this->entityManager->persist($noteSoutenance);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $noteSoutenance;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function calculateFinalResult(
        Soutenance $soutenance,
        AnneeAcademique $anneeAcademique,
        Etudiant $etudiant,
        Utilisateur $validateur,
        string $moyenneM1,
        ?string $moyenneS1M2 = null
    ): ResultatFinal {
        $this->entityManager->beginTransaction();

        try {
            $notes = $this->noteRepository->findBySoutenance($soutenance->getIdSoutenance() ?? 0);
            $total = 0.0;
            $count = 0;

            foreach ($notes as $note) {
                if (!$note instanceof NoteSoutenance) {
                    continue;
                }
                $total += (float)$note->getNote();
                $count++;
            }

            $noteMemoire = $count > 0 ? $total / $count : 0.0;
            $m1 = (float)$moyenneM1;
            $m2 = $moyenneS1M2 !== null ? (float)$moyenneS1M2 : null;
            $moyenneFinale = $m2 !== null ? (($noteMemoire + $m1 + $m2) / 3) : (($noteMemoire + $m1) / 2);

            $resultat = new ResultatFinal();
            $resultat->setEtudiant($etudiant)
                ->setAnneeAcademique($anneeAcademique)
                ->setSoutenance($soutenance)
                ->setNoteMemoire($this->formatAmount($noteMemoire))
                ->setMoyenneM1($this->formatAmount($m1))
                ->setMoyenneS1M2($m2 !== null ? $this->formatAmount($m2) : null)
                ->setMoyenneFinale($this->formatAmount($moyenneFinale))
                ->setTypePv(TypePv::STANDARD)
                ->setDecisionJury($moyenneFinale >= 10 ? DecisionJury::ADMIS : DecisionJury::REFUSE)
                ->setDateDeliberation(new DateTimeImmutable())
                ->setValide(true)
                ->setValidateur($validateur)
                ->setDateCreation(new DateTimeImmutable());

            $this->entityManager->persist($resultat);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $resultat;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    private function computeHeureFin(DateTimeInterface $heureDebut, int $dureeMinutes): DateTimeInterface
    {
        return DateTimeImmutable::createFromInterface($heureDebut)
            ->add(new DateInterval('PT' . $dureeMinutes . 'M'));
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
