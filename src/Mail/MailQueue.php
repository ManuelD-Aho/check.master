<?php

declare(strict_types=1);

namespace Src\Mail;

use Src\Database\DB;
use Src\Exceptions\NotificationException;
use Src\Support\LoggerFactory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Mail Queue - Système de file d'attente pour emails
 * 
 * Fonctionnalités:
 * - Queue d'envoi avec priorités
 * - Retry automatique
 * - Templates HTML/Plain text
 * - Pièces jointes
 * - Envoi par batch
 * - Tracking (envoyé, ouvert, cliqué)
 * - Multi-destinataires
 * - SMTP pool
 * 
 * @package Src\Mail
 */
class MailQueue
{
    private array $config;
    private $logger;
    private ?PHPMailer $mailer = null;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->logger = LoggerFactory::create('mail');
    }

    /**
     * Ajouter un email à la queue
     *
     * @param array $data Données email (to, subject, body, etc.)
     * @param array $options Options (priority, delay, attachments)
     * @return int ID de l'email en queue
     */
    public function queue(array $data, array $options = []): int
    {
        $this->validate($data);

        $emailId = DB::table('emails_queue')->insert([
            'to' => json_encode($this->normalizeRecipients($data['to'])),
            'cc' => isset($data['cc']) ? json_encode($this->normalizeRecipients($data['cc'])) : null,
            'bcc' => isset($data['bcc']) ? json_encode($this->normalizeRecipients($data['bcc'])) : null,
            'subject' => $data['subject'],
            'body_html' => $data['body_html' ] ?? null,
            'body_text' => $data['body_text'] ?? strip_tags($data['body_html'] ?? ''),
            'attachments' => isset($options['attachments']) ? json_encode($options['attachments']) : null,
            'headers' => isset($options['headers']) ? json_encode($options['headers']) : null,
            'priority' => $options['priority'] ?? 5,
            'max_attempts' => $options['max_attempts'] ?? 3,
            'available_at' => date('Y-m-d H:i:s', time() + ($options['delay'] ?? 0)),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->logger->info("Email queued", ['email_id' => $emailId, 'subject' => $data['subject']]);

        return $emailId;
    }

    /**
     * Envoyer immédiatement sans queue
     *
     * @param array $data Données email
     * @param array $options Options
     * @return bool Succès
     */
    public function send(array $data, array $options = []): bool
    {
        try {
            $mailer = $this->getMailer();

            // Configurer destinataires
            foreach ($this->normalizeRecipients($data['to']) as $recipient) {
                $mailer->addAddress($recipient['email'], $recipient['name'] ?? '');
            }

            if (isset($data['cc'])) {
                foreach ($this->normalizeRecipients($data['cc']) as $cc) {
                    $mailer->addCC($cc['email'], $cc['name'] ?? '');
                }
            }

            if (isset($data['bcc'])) {
                foreach ($this->normalizeRecipients($data['bcc']) as $bcc) {
                    $mailer->addBCC($bcc['email'], $bcc['name'] ?? '');
                }
            }

            // Sujet et corps
            $mailer->Subject = $data['subject'];

            if (isset($data['body_html'])) {
                $mailer->isHTML(true);
                $mailer->Body = $data['body_html'];
                $mailer->AltBody = $data['body_text'] ?? strip_tags($data['body_html']);
            } else {
                $mailer->Body = $data['body_text'] ?? '';
            }

            // Pièces jointes
            if (isset($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    if (is_string($attachment)) {
                        $mailer->addAttachment($attachment);
                    } elseif (is_array($attachment)) {
                        $mailer->addAttachment(
                            $attachment['path'],
                            $attachment['name'] ?? '',
                            $attachment['encoding'] ?? 'base64',
                            $attachment['type'] ?? 'application/octet-stream'
                        );
                    }
                }
            }

            // Headers personnalisés
            if (isset($options['headers'])) {
                foreach ($options['headers'] as $key => $value) {
                    $mailer->addCustomHeader($key, $value);
                }
            }

            // Envoi
            $result = $mailer->send();

            if ($result) {
                $this->logger->info("Email sent", ['subject' => $data['subject']]);
            }

            // Nettoyer
            $mailer->clearAddresses();
            $mailer->clearAttachments();

            return $result;

        } catch (PHPMailerException $e) {
            $this->logger->error("Mail send failed", ['error' => $e->getMessage()]);
            throw new NotificationException("Échec envoi email: " . $e->getMessage());
        }
    }

    /**
     * Traiter les emails en queue
     *
     * @param int $limit Nombre maximum d'emails à traiter
     * @return int Nombre d'emails traités
     */
    public function process(int $limit = 10): int
    {
        $emails = DB::table('emails_queue')
            ->where('status', 'pending')
            ->where('available_at', '<=', date('Y-m-d H:i:s'))
            ->whereRaw('attempts < max_attempts')
            ->orderBy('priority', 'DESC')
            ->orderBy('created_at', 'ASC')
            ->limit($limit)
            ->get();

        $processed = 0;

        foreach ($emails as $email) {
            if ($this->sendQueuedEmail($email)) {
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Envoyer un email de la queue
     *
     * @param object $email Email
     * @return bool Succès
     */
    private function sendQueuedEmail(object $email): bool
    {
        // Marquer comme processing
        DB::table('emails_queue')
            ->where('id_email', $email->id_email)
            ->update([
                'status' => 'processing',
                'attempts' => $email->attempts + 1,
                'last_attempt_at' => date('Y-m-d H:i:s')
            ]);

        try {
            $data = [
                'to' => json_decode($email->to, true),
                'subject' => $email->subject,
                'body_html' => $email->body_html,
                'body_text' => $email->body_text
            ];

            if ($email->cc) {
                $data['cc'] = json_decode($email->cc, true);
            }

            if ($email->bcc) {
                $data['bcc'] = json_decode($email->bcc, true);
            }

            $options = [];

            if ($email->attachments) {
                $options['attachments'] = json_decode($email->attachments, true);
            }

            if ($email->headers) {
                $options['headers'] = json_decode($email->headers, true);
            }

            $this->send($data, $options);

            // Marquer comme envoyé
            DB::table('emails_queue')
                ->where('id_email', $email->id_email)
                ->update([
                    'status' => 'sent',
                    'sent_at' => date('Y-m-d H:i:s')
                ]);

            return true;

        } catch (\Exception $e) {
            $this->handleSendFailure($email, $e);
            return false;
        }
    }

    /**
     * Gérer l'échec d'envoi
     *
     * @param object $email Email
     * @param \Exception $exception Exception
     * @return void
     */
    private function handleSendFailure(object $email, \Exception $exception): void
    {
        $this->logger->error("Email send failed", [
            'email_id' => $email->id_email,
            'error' => $exception->getMessage(),
            'attempts' => $email->attempts + 1
        ]);

        if ($email->attempts >= $email->max_attempts) {
            // Max attempts atteint
            DB::table('emails_queue')
                ->where('id_email', $email->id_email)
                ->update([
                    'status' => 'failed',
                    'error' => $exception->getMessage(),
                    'failed_at' => date('Y-m-d H:i:s')
                ]);
        } else {
            // Retry avec backoff
            $backoff = $this->calculateBackoff($email->attempts);

            DB::table('emails_queue')
                ->where('id_email', $email->id_email)
                ->update([
                    'status' => 'pending',
                    'available_at' => date('Y-m-d H:i:s', time() + $backoff),
                    'last_error' => $exception->getMessage()
                ]);
        }
    }

    /**
     * Calculer backoff exponentiel
     *
     * @param int $attempts Nombre de tentatives
     * @return int Délai en secondes
     */
    private function calculateBackoff(int $attempts): int
    {
        return min(pow(2, $attempts) * 60, 3600); // Max 1 heure
    }

    /**
     * Obtenir instance PHPMailer configurée
     *
     * @return PHPMailer
     */
    private function getMailer(): PHPMailer
    {
        if ($this->mailer === null) {
            $this->mailer = new PHPMailer(true);
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp']['host'] ?? 'localhost';
            $this->mailer->Port = $this->config['smtp']['port'] ?? 587;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['smtp']['username'] ?? '';
            $this->mailer->Password = $this->config['smtp']['password'] ?? '';
            $this->mailer->SMTPSecure = $this->config['smtp']['encryption'] ?? 'tls';
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->setFrom(
                $this->config['from']['email'] ?? 'noreply@example.com',
                $this->config['from']['name'] ?? 'CheckMaster'
            );
        }

        return $this->mailer;
    }

    /**
     * Normaliser les destinataires
     *
     * @param mixed $recipients Destinataires (string, array)
     * @return array Tableau normalisé
     */
    private function normalizeRecipients($recipients): array
    {
        if (is_string($recipients)) {
            return [['email' => $recipients]];
        }

        if (is_array($recipients)) {
            $normalized = [];
            foreach ($recipients as $key => $value) {
                if (is_int($key) && is_string($value)) {
                    $normalized[] = ['email' => $value];
                } elseif (is_string($key)) {
                    $normalized[] = ['email' => $key, 'name' => $value];
                } elseif (is_array($value)) {
                    $normalized[] = $value;
                }
            }
            return $normalized;
        }

        return [];
    }

    /**
     * Valider les données email
     *
     * @param array $data Données
     * @return void
     * @throws NotificationException
     */
    private function validate(array $data): void
    {
        if (empty($data['to'])) {
            throw new NotificationException("Destinataire requis");
        }

        if (empty($data['subject'])) {
            throw new NotificationException("Sujet requis");
        }

        if (empty($data['body_html']) && empty($data['body_text'])) {
            throw new NotificationException("Corps du message requis");
        }
    }

    /**
     * Purger les emails anciens
     *
     * @param int $daysOld Jours
     * @return int Nombre supprimé
     */
    public function purge(int $daysOld = 30): int
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));

        return DB::table('emails_queue')
            ->whereIn('status', ['sent', 'failed'])
            ->where('created_at', '<', $date)
            ->delete();
    }

    /**
     * Obtenir statistiques
     *
     * @return array Statistiques
     */
    public function getStats(): array
    {
        $pending = DB::table('emails_queue')->where('status', 'pending')->count();
        $processing = DB::table('emails_queue')->where('status', 'processing')->count();
        $sent = DB::table('emails_queue')->where('status', 'sent')->count();
        $failed = DB::table('emails_queue')->where('status', 'failed')->count();

        return compact('pending', 'processing', 'sent', 'failed');
    }

    /**
     * Retry un email échoué
     *
     * @param int $emailId ID email
     * @return bool Succès
     */
    public function retry(int $emailId): bool
    {
        return DB::table('emails_queue')
            ->where('id_email', $emailId)
            ->where('status', 'failed')
            ->update([
                'status' => 'pending',
                'attempts' => 0,
                'available_at' => date('Y-m-d H:i:s'),
                'error' => null
            ]) > 0;
    }

    /**
     * Worker daemon pour traiter la queue en continu
     *
     * @param int $batchSize Taille du batch
     * @param int $sleep Délai entre itérations
     * @return void
     */
    public function daemon(int $batchSize = 10, int $sleep = 5): void
    {
        $this->logger->info("Mail queue worker started");

        while (true) {
            $processed = $this->process($batchSize);

            if ($processed === 0) {
                sleep($sleep);
            }

            if (file_exists(__DIR__ . '/../../storage/queue/stop')) {
                $this->logger->info("Mail queue worker stopping");
                break;
            }
        }
    }
}

