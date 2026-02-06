<?php
declare(strict_types=1);

namespace App\Service\Stage;

use App\Entity\Stage\Entreprise;
use App\Repository\Stage\EntrepriseRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class EntrepriseService
{
    private EntityManagerInterface $entityManager;
    private EntrepriseRepository $entrepriseRepository;

    public function __construct(EntityManagerInterface $entityManager, EntrepriseRepository $entrepriseRepository)
    {
        $this->entityManager = $entityManager;
        $this->entrepriseRepository = $entrepriseRepository;
    }

    public function findOrCreate(string $raisonSociale, array $data = []): Entreprise
    {
        $existing = $this->entrepriseRepository->findByRaisonSociale($raisonSociale);
        if (!empty($existing)) {
            $entreprise = $existing[0];
            return $entreprise instanceof Entreprise ? $entreprise : $this->createEntreprise($raisonSociale, $data);
        }

        return $this->createEntreprise($raisonSociale, $data);
    }

    public function update(Entreprise $entreprise, array $data): Entreprise
    {
        $this->entityManager->beginTransaction();

        try {
            if (isset($data['raisonSociale'])) {
                $entreprise->setRaisonSociale((string)$data['raisonSociale']);
            }
            if (array_key_exists('sigle', $data)) {
                $entreprise->setSigle($data['sigle'] !== null ? (string)$data['sigle'] : null);
            }
            if (array_key_exists('secteurActivite', $data)) {
                $entreprise->setSecteurActivite($data['secteurActivite'] !== null ? (string)$data['secteurActivite'] : null);
            }
            if (array_key_exists('adresse', $data)) {
                $entreprise->setAdresse($data['adresse'] !== null ? (string)$data['adresse'] : null);
            }
            if (array_key_exists('ville', $data)) {
                $entreprise->setVille($data['ville'] !== null ? (string)$data['ville'] : null);
            }
            if (isset($data['pays'])) {
                $entreprise->setPays((string)$data['pays']);
            }
            if (array_key_exists('telephone', $data)) {
                $entreprise->setTelephone($data['telephone'] !== null ? (string)$data['telephone'] : null);
            }
            if (array_key_exists('email', $data)) {
                $entreprise->setEmail($data['email'] !== null ? (string)$data['email'] : null);
            }
            if (array_key_exists('siteWeb', $data)) {
                $entreprise->setSiteWeb($data['siteWeb'] !== null ? (string)$data['siteWeb'] : null);
            }
            if (array_key_exists('description', $data)) {
                $entreprise->setDescription($data['description'] !== null ? (string)$data['description'] : null);
            }
            if (isset($data['actif'])) {
                $entreprise->setActif((bool)$data['actif']);
            }

            $entreprise->setDateModification(new DateTimeImmutable());
            $this->entityManager->persist($entreprise);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $entreprise;
    }

    public function search(string $term): array
    {
        $term = trim($term);
        if ($term === '') {
            return $this->entrepriseRepository->findAll();
        }

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('e')
            ->from(Entreprise::class, 'e')
            ->where('LOWER(e.raisonSociale) LIKE :term')
            ->orWhere('LOWER(e.sigle) LIKE :term')
            ->orWhere('LOWER(e.secteurActivite) LIKE :term')
            ->setParameter('term', '%' . strtolower($term) . '%');

        return $qb->getQuery()->getResult();
    }

    public function getActive(): array
    {
        return $this->entrepriseRepository->findActive();
    }

    private function createEntreprise(string $raisonSociale, array $data): Entreprise
    {
        $this->entityManager->beginTransaction();

        try {
            $now = new DateTimeImmutable();
            $entreprise = new Entreprise();
            $entreprise->setRaisonSociale($raisonSociale)
                ->setSigle($data['sigle'] ?? null)
                ->setSecteurActivite($data['secteurActivite'] ?? null)
                ->setAdresse($data['adresse'] ?? null)
                ->setVille($data['ville'] ?? null)
                ->setPays($data['pays'] ?? $entreprise->getPays())
                ->setTelephone($data['telephone'] ?? null)
                ->setEmail($data['email'] ?? null)
                ->setSiteWeb($data['siteWeb'] ?? null)
                ->setDescription($data['description'] ?? null)
                ->setActif(isset($data['actif']) ? (bool)$data['actif'] : true)
                ->setDateCreation($now)
                ->setDateModification($now);

            $this->entityManager->persist($entreprise);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $entreprise;
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
