<?php

declare(strict_types=1);

namespace App\Repository\Report;

use App\Entity\Report\CommentaireRapport;
use App\Entity\Report\TypeCommentaire;
use App\Repository\AbstractRepository;

class CommentaireRapportRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return CommentaireRapport::class;
    }

    public function findByRapport(int $rapportId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CommentaireRapport::class, 'c')
            ->join('c.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->setParameter('rapportId', $rapportId)
            ->getQuery()
            ->getResult();
    }

    public function findPublicComments(int $rapportId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CommentaireRapport::class, 'c')
            ->join('c.rapport', 'r')
            ->where('r.idRapport = :rapportId')
            ->andWhere('c.estPublic = :estPublic')
            ->setParameter('rapportId', $rapportId)
            ->setParameter('estPublic', true)
            ->getQuery()
            ->getResult();
    }

    public function findByType(string $type): array
    {
        return $this->findBy(['typeCommentaire' => TypeCommentaire::from($type)]);
    }
}
