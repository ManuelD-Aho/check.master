<?php

declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\User\TypeUtilisateur;
use App\Repository\AbstractRepository;

class TypeUtilisateurRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return TypeUtilisateur::class;
    }

    public function findByCode(string $code): ?TypeUtilisateur
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['codeTypeUtilisateur' => $code]);
    }
}
