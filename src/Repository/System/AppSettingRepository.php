<?php

declare(strict_types=1);

namespace App\Repository\System;

use App\Entity\System\AppSetting;
use App\Repository\AbstractRepository;

class AppSettingRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return AppSetting::class;
    }

    public function findByKey(string $key): ?AppSetting
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findOneBy(['settingKey' => $key]);
    }

    public function findByGroupe(string $groupe): array
    {
        return $this->entityManager->getRepository($this->getEntityClass())
            ->findBy(['category' => $groupe]);
    }

    public function findModifiableSettings(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from($this->getEntityClass(), 's')
            ->where('s.isSensitive = :isSensitive')
            ->setParameter('isSensitive', false)
            ->getQuery()
            ->getResult();
    }
}
