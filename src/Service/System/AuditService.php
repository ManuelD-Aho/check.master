<?php
declare(strict_types=1);

namespace App\Service\System;

use App\Entity\System\AuditLog;
use App\Entity\System\AuditStatutAction;
use App\Entity\User\Utilisateur;
use DateTimeImmutable;

class AuditService
{
    private object $entityManager;
    private object $logger;

    public function __construct(object $entityManager, object $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function log(
        string $action,
        string $statut,
        ?int $userId = null,
        ?string $table = null,
        ?int $recordId = null,
        ?array $before = null,
        ?array $after = null,
        ?string $details = null
    ): void {
        try {
            $log = new AuditLog();

            if ($userId !== null) {
                $user = $this->entityManager->getReference(Utilisateur::class, $userId);
                if ($user instanceof Utilisateur) {
                    $log->setUtilisateur($user);
                }
            }

            $log->setAction($action);
            $log->setStatutAction(AuditStatutAction::tryFrom($statut) ?? AuditStatutAction::Tentative);
            $log->setTableConcernee($table);
            $log->setIdEnregistrement($recordId);
            $log->setDonneesAvant($before);
            $log->setDonneesApres($after);
            $log->setDetails($details);
            $log->setDateCreation(new DateTimeImmutable());

            $this->entityManager->persist($log);
            $this->entityManager->flush();

            $this->logger->info('audit', [
                'action' => $action,
                'statut' => $statut,
                'user_id' => $userId,
                'table' => $table,
                'record_id' => $recordId,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('audit_failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function logLogin(int $userId, bool $success, string $ip): void
    {
        $details = null;

        try {
            $details = json_encode(['ip' => $ip], JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
        }

        $this->log('auth.login', $success ? 'succes' : 'echec', $userId, null, null, null, null, $details);
    }

    public function logLogout(int $userId): void
    {
        $this->log('auth.logout', 'succes', $userId);
    }

    public function logDataChange(int $userId, string $table, int $recordId, array $before, array $after): void
    {
        $this->log('data.change', 'succes', $userId, $table, $recordId, $before, $after);
    }

    public function getRecentLogs(int $limit = 100): array
    {
        return $this->entityManager->getRepository(AuditLog::class)->findBy([], ['dateCreation' => 'DESC'], $limit);
    }
}
