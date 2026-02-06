<?php
declare(strict_types=1);

namespace App\Service\Auth;

use App\Entity\User\AuthRateLimit;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Throwable;

class RateLimiterService
{
    private EntityManager $entityManager;
    private int $maxAttempts;
    private int $lockoutDuration;

    public function __construct(EntityManager $entityManager, int $maxAttempts, int $lockoutDuration)
    {
        $this->entityManager = $entityManager;
        $this->maxAttempts = $maxAttempts;
        $this->lockoutDuration = $lockoutDuration;
    }

    public function check(string $action, string $ip, ?string $identifier = null): bool
    {
        try {
            $record = $this->findRecord($action, $ip, $identifier);
            if ($record === null) {
                return true;
            }

            $now = new DateTimeImmutable();

            if ($this->isBlockedRecord($record, $now)) {
                return false;
            }

            if ($this->isWindowExpired($record, $now)) {
                return true;
            }

            return $record->getTentatives() < $this->maxAttempts;
        } catch (Throwable) {
            return true;
        }
    }

    public function increment(string $action, string $ip, ?string $identifier = null): void
    {
        $now = new DateTimeImmutable();

        $record = $this->findRecord($action, $ip, $identifier);

        if ($record === null) {
            $record = new AuthRateLimit();
            $record->setAction($action)
                ->setAdresseIp($ip)
                ->setIdentifiant($identifier)
                ->setTentatives(1)
                ->setDebutFenetre($now)
                ->setDerniereTentative($now)
                ->setDateCreation($now)
                ->setDateModification($now);
        } else {
            if ($this->isWindowExpired($record, $now)) {
                $record->setTentatives(1)
                    ->setDebutFenetre($now)
                    ->setBloqueJusqu(null);
            } else {
                $record->setTentatives($record->getTentatives() + 1);
            }

            $record->setDerniereTentative($now)
                ->setDateModification($now);
        }

        if ($record->getTentatives() >= $this->maxAttempts) {
            $record->setBloqueJusqu($now->add(new DateInterval('PT' . $this->lockoutDuration . 'M')));
        }

        $this->entityManager->persist($record);
        $this->entityManager->flush();
    }

    public function reset(string $action, string $ip, ?string $identifier = null): void
    {
        $record = $this->findRecord($action, $ip, $identifier);

        if ($record === null) {
            return;
        }

        $this->entityManager->remove($record);
        $this->entityManager->flush();
    }

    public function isBlocked(string $action, string $ip): bool
    {
        $record = $this->findRecord($action, $ip, null);

        if ($record === null) {
            return false;
        }

        $now = new DateTimeImmutable();

        if ($this->isBlockedRecord($record, $now)) {
            return true;
        }

        if ($record->getBloqueJusqu() !== null) {
            $record->setBloqueJusqu(null)
                ->setTentatives(0)
                ->setDateModification($now);
            $this->entityManager->persist($record);
            $this->entityManager->flush();
        }

        return false;
    }

    public function getRemainingAttempts(string $action, string $ip): int
    {
        $record = $this->findRecord($action, $ip, null);

        if ($record === null) {
            return $this->maxAttempts;
        }

        $now = new DateTimeImmutable();

        if ($this->isBlockedRecord($record, $now)) {
            return 0;
        }

        if ($this->isWindowExpired($record, $now)) {
            return $this->maxAttempts;
        }

        return max(0, $this->maxAttempts - $record->getTentatives());
    }

    public function cleanExpired(): int
    {
        $now = new DateTimeImmutable();
        $threshold = $now->sub(new DateInterval('PT' . $this->lockoutDuration . 'M'));

        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(AuthRateLimit::class, 'r')
            ->where('r.bloqueJusqu < :now')
            ->orWhere('r.derniereTentative < :threshold')
            ->setParameter('now', $now)
            ->setParameter('threshold', $threshold);

        return (int)$qb->getQuery()->execute();
    }

    public function getRemainingBlockTime(string $action, string $ip): int
    {
        $record = $this->findRecord($action, $ip, null);

        if ($record === null || $record->getBloqueJusqu() === null) {
            return 0;
        }

        $now = new DateTimeImmutable();
        $blockUntil = $record->getBloqueJusqu();

        if ($blockUntil <= $now) {
            return 0;
        }

        $diff = $blockUntil->getTimestamp() - $now->getTimestamp();

        return (int)ceil($diff / 60);
    }

    private function findRecord(string $action, string $ip, ?string $identifier): ?AuthRateLimit
    {
        return $this->entityManager->getRepository(AuthRateLimit::class)->findOneBy([
            'action' => $action,
            'adresseIp' => $ip,
            'identifiant' => $identifier
        ]);
    }

    private function isBlockedRecord(AuthRateLimit $record, DateTimeImmutable $now): bool
    {
        $blockedUntil = $record->getBloqueJusqu();
        return $blockedUntil !== null && $blockedUntil > $now;
    }

    private function isWindowExpired(AuthRateLimit $record, DateTimeImmutable $now): bool
    {
        $windowEnd = $record->getDebutFenetre()->add(new DateInterval('PT' . $this->lockoutDuration . 'M'));
        return $now > $windowEnd;
    }
}
