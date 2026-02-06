<?php
declare(strict_types=1);

namespace App\Service\Etudiant;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Academic\NiveauEtude;
use App\Entity\Student\Echeance;
use App\Entity\Student\Etudiant;
use App\Entity\Student\Inscription;
use App\Entity\Student\MethodePaiement;
use App\Entity\Student\StatutEcheance;
use App\Entity\Student\StatutInscription;
use App\Entity\Student\TypeVersement;
use App\Entity\Student\Versement;
use App\Repository\Student\InscriptionRepository;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class InscriptionService
{
    private EntityManagerInterface $entityManager;
    private InscriptionRepository $inscriptionRepository;

    public function __construct(EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository)
    {
        $this->entityManager = $entityManager;
        $this->inscriptionRepository = $inscriptionRepository;
    }

    public function createInscription(
        Etudiant $etudiant,
        NiveauEtude $niveauEtude,
        AnneeAcademique $anneeAcademique,
        string $montantInscription,
        string $montantScolarite,
        int $nombreTranches,
        ?DateTimeInterface $dateInscription = null
    ): Inscription {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $inscription = new Inscription();
            $inscription->setEtudiant($etudiant)
                ->setNiveauEtude($niveauEtude)
                ->setAnneeAcademique($anneeAcademique)
                ->setDateInscription($dateInscription ?? $now)
                ->setMontantInscription($montantInscription)
                ->setMontantScolarite($montantScolarite)
                ->setNombreTranches(max(1, $nombreTranches))
                ->setMontantPaye('0.00')
                ->setStatutInscription(StatutInscription::EnAttente)
                ->setDateCreation($now)
                ->setDateModification($now);

            $this->entityManager->persist($inscription);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $inscription;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function recordVersement(
        Inscription $inscription,
        string $montant,
        DateTimeInterface $dateVersement,
        TypeVersement $typeVersement,
        MethodePaiement $methodePaiement,
        object $utilisateurSaisie,
        ?string $referencePaiement = null,
        ?string $commentaire = null
    ): Versement {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $versement = new Versement();
            $versement->setInscription($inscription)
                ->setMontantVersement($montant)
                ->setDateVersement($dateVersement)
                ->setTypeVersement($typeVersement)
                ->setMethodePaiement($methodePaiement)
                ->setReferencePaiement($referencePaiement)
                ->setUtilisateurSaisie($utilisateurSaisie)
                ->setCommentaire($commentaire)
                ->setDateCreation($now);

            $nouveauPaye = (float)$inscription->getMontantPaye() + (float)$montant;
            $inscription->setMontantPaye($this->formatAmount($nouveauPaye));
            $inscription->setStatutInscription($this->determineStatut($inscription));
            $inscription->setDateModification($now);

            $this->entityManager->persist($versement);
            $this->entityManager->persist($inscription);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $versement;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function generateEcheancier(
        Inscription $inscription,
        ?DateTimeInterface $dateDebut = null,
        int $intervalDays = 30
    ): array {
        $this->entityManager->beginTransaction();

        try {
            $startDate = $dateDebut ?? new DateTimeImmutable();
            $tranches = max(1, $inscription->getNombreTranches());
            $total = (float)$inscription->getMontantInscription() + (float)$inscription->getMontantScolarite();
            $montantParTranche = $this->formatAmount($total / $tranches);

            $echeances = [];
            for ($i = 1; $i <= $tranches; $i++) {
                $dateEcheance = DateTimeImmutable::createFromInterface($startDate)
                    ->add(new DateInterval('P' . ($intervalDays * ($i - 1)) . 'D'));

                $echeance = new Echeance();
                $echeance->setInscription($inscription)
                    ->setNumeroEcheance($i)
                    ->setMontantEcheance($montantParTranche)
                    ->setDateEcheance($dateEcheance)
                    ->setStatutEcheance(StatutEcheance::EnAttente)
                    ->setMontantPaye('0.00');

                $this->entityManager->persist($echeance);
                $echeances[] = $echeance;
            }

            $this->entityManager->flush();
            $this->entityManager->commit();

            return $echeances;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function updateStatutInscription(Inscription $inscription): StatutInscription
    {
        $this->entityManager->beginTransaction();

        try {
            $statut = $this->determineStatut($inscription);
            $inscription->setStatutInscription($statut)
                ->setDateModification(new DateTimeImmutable());
            $this->entityManager->persist($inscription);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $inscription->getStatutInscription();
    }

    public function getResteAPayer(Inscription $inscription): string
    {
        $total = (float)$inscription->getMontantInscription() + (float)$inscription->getMontantScolarite();
        $paye = (float)$inscription->getMontantPaye();
        $reste = $total - $paye;
        return $this->formatAmount(max(0, $reste));
    }

    private function determineStatut(Inscription $inscription): StatutInscription
    {
        $reste = (float)$this->getResteAPayer($inscription);
        $paye = (float)$inscription->getMontantPaye();

        if ($reste <= 0.0) {
            return StatutInscription::Solde;
        }

        if ($paye > 0.0) {
            return StatutInscription::Partiel;
        }

        return StatutInscription::EnAttente;
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
