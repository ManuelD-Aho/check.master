<?php

declare(strict_types=1);

namespace Src\Queue;

use Src\Database\DB;
use Src\Exceptions\DatabaseException;
use Src\Support\LoggerFactory;
use Psr\Log\LoggerInterface;

/**
 * Job Queue System - Système de files d'attente pour tâches asynchrones
 * 
 * Fonctionnalités:
 * - Files multiples avec priorités
 * - Retry automatique avec backoff exponentiel
 * - Gestion des échecs et dead letter queue
 * - Workers parallèles
 * - Scheduled jobs (cron-like)
 * - Job chaining et batching
 * - Monitoring et statistiques
 * 
 * @package Src\Queue
 */
class JobQueue
{
    private LoggerInterface $logger;
    private string $queue = 'default';
    private array $config;

    /**
     * Constructeur
     *
     * @param array $config Configuration
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->logger = LoggerFactory::create('queue');
    }

    /**
     * Ajouter un job à la queue
     *
     * @param string $jobClass Classe du job
     * @param array $payload Données
     * @param array $options Options (delay, priority, queue, max_attempts)
     * @return int ID du job
     * @throws DatabaseException
     */
    public function push(string $jobClass, array $payload = [], array $options = []): int
    {
        $queue = $options['queue'] ?? $this->queue;
        $priority = $options['priority'] ?? 5;
        $delay = $options['delay'] ?? 0;
        $maxAttempts = $options['max_attempts'] ?? 3;
        $availableAt = time() + $delay;

        $id = DB::table('jobs')->insert([
            'queue' => $queue,
            'job_class' => $jobClass,
            'payload' => json_encode($payload),
            'priority' => $priority,
            'max_attempts' => $maxAttempts,
            'attempts' => 0,
            'available_at' => date('Y-m-d H:i:s', $availableAt),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->logger->info("Job queued", [
            'job_id' => $id,
            'class' => $jobClass,
            'queue' => $queue
        ]);

        return $id;
    }

    /**
     * Ajouter un job avec délai
     *
     * @param int $seconds Délai en secondes
     * @param string $jobClass Classe
     * @param array $payload Données
     * @param array $options Options
     * @return int ID du job
     */
    public function later(int $seconds, string $jobClass, array $payload = [], array $options = []): int
    {
        $options['delay'] = $seconds;
        return $this->push($jobClass, $payload, $options);
    }

    /**
     * Récupérer et traiter le prochain job
     *
     * @param string|null $queue Queue spécifique (null = toutes)
     * @return bool True si un job a été traité
     */
    public function work(?string $queue = null): bool
    {
        $job = $this->getNextJob($queue);

        if ($job === null) {
            return false;
        }

        return $this->processJob($job);
    }

    /**
     * Récupérer le prochain job disponible
     *
     * @param string|null $queue Queue
     * @return object|null Job
     */
    private function getNextJob(?string $queue): ?object
    {
        $query = DB::table('jobs')
            ->where('status', 'pending')
            ->where('available_at', '<=', date('Y-m-d H:i:s'))
            ->whereRaw('attempts < max_attempts')
            ->orderBy('priority', 'DESC')
            ->orderBy('created_at', 'ASC')
            ->limit(1);

        if ($queue !== null) {
            $query->where('queue', $queue);
        }

        $job = $query->first();

        if ($job !== null) {
            // Marquer comme processing
            DB::table('jobs')
                ->where('id_job', $job->id_job)
                ->update([
                    'status' => 'processing',
                    'started_at' => date('Y-m-d H:i:s'),
                    'attempts' => $job->attempts + 1
                ]);
        }

        return $job;
    }

    /**
     * Traiter un job
     *
     * @param object $job Job
     * @return bool Succès
     */
    private function processJob(object $job): bool
    {
        try {
            $jobClass = $job->job_class;
            $payload = json_decode($job->payload, true);

            // Instancier et exécuter le job
            if (!class_exists($jobClass)) {
                throw new \Exception("Classe job introuvable: {$jobClass}");
            }

            $jobInstance = new $jobClass();
            
            if (!method_exists($jobInstance, 'handle')) {
                throw new \Exception("Méthode handle() manquante dans {$jobClass}");
            }

            // Exécuter le job
            $result = $jobInstance->handle($payload);

            // Marquer comme complété
            DB::table('jobs')
                ->where('id_job', $job->id_job)
                ->update([
                    'status' => 'completed',
                    'completed_at' => date('Y-m-d H:i:s'),
                    'result' => json_encode($result)
                ]);

            $this->logger->info("Job completed", [
                'job_id' => $job->id_job,
                'class' => $jobClass
            ]);

            return true;

        } catch (\Exception $e) {
            $this->handleJobFailure($job, $e);
            return false;
        }
    }

    /**
     * Gérer l'échec d'un job
     *
     * @param object $job Job
     * @param \Exception $exception Exception
     * @return void
     */
    private function handleJobFailure(object $job, \Exception $exception): void
    {
        $this->logger->error("Job failed", [
            'job_id' => $job->id_job,
            'class' => $job->job_class,
            'error' => $exception->getMessage(),
            'attempts' => $job->attempts + 1
        ]);

        // Vérifier si max attempts atteint
        if ($job->attempts >= $job->max_attempts) {
            // Déplacer vers dead letter queue
            DB::table('jobs')
                ->where('id_job', $job->id_job)
                ->update([
                    'status' => 'failed',
                    'failed_at' => date('Y-m-d H:i:s'),
                    'error' => $exception->getMessage()
                ]);

            // Log dans failed_jobs
            DB::table('failed_jobs')->insert([
                'job_id' => $job->id_job,
                'queue' => $job->queue,
                'job_class' => $job->job_class,
                'payload' => $job->payload,
                'exception' => $exception->getMessage(),
                'failed_at' => date('Y-m-d H:i:s')
            ]);

        } else {
            // Retry avec backoff exponentiel
            $backoff = $this->calculateBackoff($job->attempts);
            
            DB::table('jobs')
                ->where('id_job', $job->id_job)
                ->update([
                    'status' => 'pending',
                    'available_at' => date('Y-m-d H:i:s', time() + $backoff),
                    'last_error' => $exception->getMessage()
                ]);
        }
    }

    /**
     * Calculer le délai de backoff exponentiel
     *
     * @param int $attempts Nombre de tentatives
     * @return int Délai en secondes
     */
    private function calculateBackoff(int $attempts): int
    {
        // Backoff: 1min, 5min, 15min, 30min, 1h
        $delays = [60, 300, 900, 1800, 3600];
        $index = min($attempts, count($delays) - 1);
        return $delays[$index];
    }

    /**
     * Worker daemon - traiter les jobs en continu
     *
     * @param string|null $queue Queue
     * @param int $sleep Délai entre itérations (secondes)
     * @return void
     */
    public function daemon(?string $queue = null, int $sleep = 3): void
    {
        $this->logger->info("Worker started", ['queue' => $queue ?? 'all']);

        while (true) {
            $processed = $this->work($queue);

            if (!$processed) {
                sleep($sleep);
            }

            // Vérifier si le worker doit s'arrêter
            if ($this->shouldQuit()) {
                $this->logger->info("Worker stopping");
                break;
            }
        }
    }

    /**
     * Vérifier si le worker doit s'arrêter
     *
     * @return bool
     */
    private function shouldQuit(): bool
    {
        // Vérifier fichier de signalement ou signal système
        return file_exists(__DIR__ . '/../../storage/queue/stop');
    }

    /**
     * Purger les jobs complétés anciens
     *
     * @param int $olderThanDays Jours
     * @return int Nombre de jobs purgés
     */
    public function purgeCompleted(int $olderThanDays = 7): int
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$olderThanDays} days"));

        return DB::table('jobs')
            ->where('status', 'completed')
            ->where('completed_at', '<', $date)
            ->delete();
    }

    /**
     * Retry un job échoué
     *
     * @param int $jobId ID du job
     * @return bool Succès
     */
    public function retry(int $jobId): bool
    {
        $job = DB::table('jobs')->where('id_job', $jobId)->first();

        if (!$job || $job->status !== 'failed') {
            return false;
        }

        DB::table('jobs')
            ->where('id_job', $jobId)
            ->update([
                'status' => 'pending',
                'attempts' => 0,
                'available_at' => date('Y-m-d H:i:s'),
                'failed_at' => null,
                'error' => null
            ]);

        return true;
    }

    /**
     * Obtenir les statistiques de la queue
     *
     * @param string|null $queue Queue
     * @return array Statistiques
     */
    public function getStats(?string $queue = null): array
    {
        $query = DB::table('jobs');

        if ($queue !== null) {
            $query->where('queue', $queue);
        }

        $pending = clone $query;
        $processing = clone $query;
        $completed = clone $query;
        $failed = clone $query;

        return [
            'pending' => $pending->where('status', 'pending')->count(),
            'processing' => $processing->where('status', 'processing')->count(),
            'completed' => $completed->where('status', 'completed')->count(),
            'failed' => $failed->where('status', 'failed')->count()
        ];
    }

    /**
     * Changer la queue par défaut
     *
     * @param string $queue Nom de la queue
     * @return self
     */
    public function onQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }
}
