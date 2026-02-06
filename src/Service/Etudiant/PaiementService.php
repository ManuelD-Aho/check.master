<?php

declare(strict_types=1);

namespace App\Service\Etudiant;

use App\Entity\Student\Echeance;
use App\Entity\Student\Inscription;
use App\Entity\Student\StatutEcheance;
use App\Entity\Student\Versement;
use App\Repository\Student\EcheanceRepository;
use App\Repository\Student\InscriptionRepository;
use App\Repository\Student\VersementRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class PaiementService
{
    private EntityManagerInterface $entityManager;
    private VersementRepository $versementRepository;
    private EcheanceRepository $echeanceRepository;
    private InscriptionRepository $inscriptionRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        VersementRepository $versementRepository,
        EcheanceRepository $echeanceRepository,
        InscriptionRepository $inscriptionRepository
    ) {
        $this->entityManager = $entityManager;
        $this->versementRepository = $versementRepository;
        $this->echeanceRepository = $echeanceRepository;
        $this->inscriptionRepository = $inscriptionRepository;
    }

    public function recordPayment(int $inscriptionId, array $data): Versement
    {
        $inscription = $this->inscriptionRepository->find($inscriptionId);

        if (!$inscription instanceof Inscription) {
            throw new \InvalidArgumentException('Inscription non trouvée avec l\'id : ' . $inscriptionId);
        }

        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $versement = new Versement();
            $versement->setInscription($inscription)
                ->setMontantVersement($this->formatAmount((float) $data['montant']))
                ->setDateVersement($data['date_versement'] ?? $now)
                ->setTypeVersement($data['type_versement'])
                ->setMethodePaiement($data['methode_paiement'])
                ->setReferencePaiement($data['reference'] ?? null)
                ->setCommentaire($data['commentaire'] ?? null)
                ->setDateCreation($now);

            if (isset($data['utilisateur_saisie'])) {
                $versement->setUtilisateurSaisie($data['utilisateur_saisie']);
            }

            $nouveauPaye = (float) $inscription->getMontantPaye() + (float) $data['montant'];
            $inscription->setMontantPaye($this->formatAmount($nouveauPaye));
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

    public function getPaymentHistory(int $inscriptionId): array
    {
        return $this->versementRepository->findByInscription($inscriptionId);
    }

    public function calculateResteAPayer(int $inscriptionId): float
    {
        $inscription = $this->inscriptionRepository->find($inscriptionId);

        if (!$inscription instanceof Inscription) {
            return 0.0;
        }

        $total = (float) $inscription->getMontantInscription() + (float) $inscription->getMontantScolarite();
        $paye = (float) $inscription->getMontantPaye();

        return max(0.0, $total - $paye);
    }

    public function calculateStatut(int $inscriptionId): string
    {
        $inscription = $this->inscriptionRepository->find($inscriptionId);

        if (!$inscription instanceof Inscription) {
            return 'non_inscrit';
        }

        $reste = $this->calculateResteAPayer($inscriptionId);
        $paye = (float) $inscription->getMontantPaye();

        if ($reste <= 0.0) {
            return 'solde';
        }

        if ($paye > 0.0) {
            return 'partiel';
        }

        return 'non_inscrit';
    }

    public function getEcheances(int $inscriptionId): array
    {
        return $this->echeanceRepository->findByInscription($inscriptionId);
    }

    public function createEcheance(int $inscriptionId, array $data): Echeance
    {
        $inscription = $this->inscriptionRepository->find($inscriptionId);

        if (!$inscription instanceof Inscription) {
            throw new \InvalidArgumentException('Inscription non trouvée avec l\'id : ' . $inscriptionId);
        }

        $this->entityManager->beginTransaction();

        try {
            $echeance = new Echeance();
            $echeance->setInscription($inscription)
                ->setNumeroEcheance((int) $data['numero_echeance'])
                ->setMontantEcheance($this->formatAmount((float) $data['montant']))
                ->setDateEcheance($data['date_echeance'])
                ->setStatutEcheance($data['statut'] ?? StatutEcheance::EnAttente)
                ->setMontantPaye('0.00');

            $this->entityManager->persist($echeance);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $echeance;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
