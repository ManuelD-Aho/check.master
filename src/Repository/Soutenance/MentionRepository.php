<?php

declare(strict_types=1);

namespace App\Repository\Soutenance;

use App\Entity\Soutenance\Mention;
use App\Repository\AbstractRepository;

class MentionRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return Mention::class;
    }

    public function findByCode(string $code): ?Mention
    {
        return $this->findOneBy(['codeMention' => $code]);
    }

    public function findByMoyenne(float $moyenne): ?Mention
    {
        return $this->entityManager->createQueryBuilder()
            ->select('m')
            ->from(Mention::class, 'm')
            ->where('m.seuilMinimum <= :moyenne')
            ->andWhere('m.seuilMaximum >= :moyenne')
            ->setParameter('moyenne', $moyenne)
            ->orderBy('m.ordre', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
