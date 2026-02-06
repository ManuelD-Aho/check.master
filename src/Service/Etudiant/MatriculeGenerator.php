<?php

declare(strict_types=1);

namespace App\Service\Etudiant;

use App\Entity\Student\Etudiant;
use Doctrine\ORM\EntityManagerInterface;

class MatriculeGenerator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generate(int $anneeDebut): string
    {
        $sequence = $this->getNextSequence($anneeDebut);
        $padded = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

        return 'MIAGE-GI-' . $anneeDebut . '-' . $padded;
    }

    public function generateFromYear(string $anneeAcademique): string
    {
        $parts = explode('-', $anneeAcademique);
        $anneeDebut = (int) $parts[0];

        return $this->generate($anneeDebut);
    }

    public function isUnique(string $matricule): bool
    {
        $existing = $this->entityManager->getRepository(Etudiant::class)
            ->findOneBy(['matriculeEtudiant' => $matricule]);

        return $existing === null;
    }

    private function getNextSequence(int $anneeDebut): int
    {
        $prefix = 'MIAGE-GI-' . $anneeDebut . '-';

        $qb = $this->entityManager->createQueryBuilder();
        $count = $qb->select('COUNT(e.matriculeEtudiant)')
            ->from(Etudiant::class, 'e')
            ->where('e.matriculeEtudiant LIKE :prefix')
            ->setParameter('prefix', $prefix . '%')
            ->getQuery()
            ->getSingleScalarResult();

        return ((int) $count) + 1;
    }
}
