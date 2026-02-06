<?php
declare(strict_types=1);

namespace App\Service\Etudiant;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Student\Etudiant;
use App\Entity\Student\Inscription;
use App\Entity\Student\StatutInscription;
use App\Repository\Student\InscriptionRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Throwable;

class EtudiantService
{
    private EntityManagerInterface $entityManager;
    private InscriptionRepository $inscriptionRepository;
    private ObjectRepository $etudiantRepository;

    public function __construct(EntityManagerInterface $entityManager, InscriptionRepository $inscriptionRepository)
    {
        $this->entityManager = $entityManager;
        $this->inscriptionRepository = $inscriptionRepository;
        $this->etudiantRepository = $entityManager->getRepository(Etudiant::class);
    }

    public function getProfile(string $matricule): ?Etudiant
    {
        $etudiant = $this->etudiantRepository->find($matricule);
        return $etudiant instanceof Etudiant ? $etudiant : null;
    }

    public function getInscriptionForCurrentYear(string $matricule): ?Inscription
    {
        $annee = $this->getActiveAnnee();
        if ($annee === null || $annee->getIdAnneeAcademique() === null) {
            return null;
        }

        return $this->inscriptionRepository->findByEtudiantAndAnnee($matricule, $annee->getIdAnneeAcademique());
    }

    public function getScolariteStatus(string $matricule): array
    {
        $inscription = $this->getInscriptionForCurrentYear($matricule);

        if ($inscription === null) {
            return [
                'inscription' => null,
                'statut' => null,
                'montant_inscription' => null,
                'montant_scolarite' => null,
                'montant_paye' => null,
                'reste_a_payer' => null
            ];
        }

        $reste = $this->calculateResteAPayer($inscription);

        return [
            'inscription' => $inscription,
            'statut' => $inscription->getStatutInscription(),
            'montant_inscription' => $inscription->getMontantInscription(),
            'montant_scolarite' => $inscription->getMontantScolarite(),
            'montant_paye' => $inscription->getMontantPaye(),
            'reste_a_payer' => $reste
        ];
    }

    public function updateProfile(string $matricule, array $data): ?Etudiant
    {
        $etudiant = $this->getProfile($matricule);
        if ($etudiant === null) {
            return null;
        }

        $this->entityManager->beginTransaction();

        try {
            if (isset($data['nomEtudiant'])) {
                $etudiant->setNomEtudiant((string)$data['nomEtudiant']);
            }
            if (isset($data['prenomEtudiant'])) {
                $etudiant->setPrenomEtudiant((string)$data['prenomEtudiant']);
            }
            if (isset($data['emailEtudiant'])) {
                $etudiant->setEmailEtudiant((string)$data['emailEtudiant']);
            }
            if (array_key_exists('telephoneEtudiant', $data)) {
                $etudiant->setTelephoneEtudiant($data['telephoneEtudiant'] !== null ? (string)$data['telephoneEtudiant'] : null);
            }
            if (isset($data['dateNaissance']) && $data['dateNaissance'] instanceof \DateTimeInterface) {
                $etudiant->setDateNaissance($data['dateNaissance']);
            }
            if (isset($data['lieuNaissance'])) {
                $etudiant->setLieuNaissance((string)$data['lieuNaissance']);
            }
            if (isset($data['genre']) && $data['genre'] instanceof \App\Entity\Student\Genre) {
                $etudiant->setGenre($data['genre']);
            }
            if (array_key_exists('nationalite', $data)) {
                $etudiant->setNationalite($data['nationalite'] !== null ? (string)$data['nationalite'] : null);
            }
            if (array_key_exists('adresse', $data)) {
                $etudiant->setAdresse($data['adresse'] !== null ? (string)$data['adresse'] : null);
            }
            if (isset($data['promotion'])) {
                $etudiant->setPromotion((string)$data['promotion']);
            }
            if (array_key_exists('photoProfil', $data)) {
                $etudiant->setPhotoProfil($data['photoProfil'] !== null ? (string)$data['photoProfil'] : null);
            }
            if (isset($data['filiere'])) {
                $etudiant->setFiliere($data['filiere']);
            }
            if (isset($data['actif'])) {
                $etudiant->setActif((bool)$data['actif']);
            }

            $etudiant->setDateModification(new DateTimeImmutable());
            $this->entityManager->persist($etudiant);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $etudiant;
    }

    public function isEligibleForStage(string $matricule): bool
    {
        $etudiant = $this->getProfile($matricule);
        if ($etudiant === null || !$etudiant->isActif()) {
            return false;
        }

        $inscription = $this->getInscriptionForCurrentYear($matricule);
        if ($inscription === null) {
            return false;
        }

        $statut = $inscription->getStatutInscription();

        return in_array($statut, [StatutInscription::Solde, StatutInscription::Partiel], true);
    }

    private function getActiveAnnee(): ?AnneeAcademique
    {
        $annee = $this->entityManager->getRepository(AnneeAcademique::class)->findOneBy(['estActive' => true]);
        return $annee instanceof AnneeAcademique ? $annee : null;
    }

    private function calculateResteAPayer(Inscription $inscription): string
    {
        $total = (float)$inscription->getMontantInscription() + (float)$inscription->getMontantScolarite();
        $paye = (float)$inscription->getMontantPaye();
        $reste = $total - $paye;
        return number_format(max(0, $reste), 2, '.', '');
    }
}
