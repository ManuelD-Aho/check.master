<?php
declare(strict_types=1);

namespace App\Service\Email;

use App\Service\System\EncryptionService;
use App\Service\System\SettingsService;
use DateTimeInterface;
use PDO;
use RuntimeException;

class EmailService
{
    private TemplateRenderer $renderer;
    private SettingsService $settings;
    private EncryptionService $encryption;
    private object $logger;
    private array $smtpConfig;

    public function __construct(
        TemplateRenderer $renderer,
        SettingsService $settings,
        EncryptionService $encryption,
        object $logger,
        array $smtpConfig
    ) {
        $this->renderer = $renderer;
        $this->settings = $settings;
        $this->encryption = $encryption;
        $this->logger = $logger;
        $this->smtpConfig = $smtpConfig;
    }

    public function send(
        string $to,
        string $subject,
        string $template,
        array $data = [],
        array $attachments = []
    ): bool {
        $html = $this->renderTemplate($template, $data);

        if ($html === null) {
            return false;
        }

        $text = $this->convertToText($html);

        try {
            $mailer = $this->createMailer();
            [$fromEmail, $fromName] = $this->resolveFrom();

            if ($fromEmail === '') {
                throw new RuntimeException('Missing from email address.');
            }

            $mailer->setFrom($fromEmail, $fromName);
            $mailer->addAddress($to);
            $mailer->Subject = $subject;
            $mailer->isHTML(true);
            $mailer->Body = $html;
            $mailer->AltBody = $text;

            $this->addAttachments($mailer, $attachments);

            if (!$mailer->send()) {
                throw new RuntimeException($mailer->ErrorInfo ?: 'SMTP send failed.');
            }

            return true;
        } catch (\Throwable $e) {
            $this->logger->error('Email send failed', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template,
                'error' => $e->getMessage(),
            ]);

            $this->queueEmail($to, $subject, $template, $data, $attachments, $html, $text, $e->getMessage());

            return false;
        }
    }

    public function sendPasswordReset(string $email, string $token, string $userName): bool
    {
        $appUrl = (string) $this->settings->get('app_url', $_ENV['APP_URL'] ?? '');
        $resetUrl = rtrim($appUrl, '/');
        $resetUrl = $resetUrl === '' ? '' : $resetUrl . '/reset-password?token=' . urlencode($token);

        return $this->send(
            $email,
            'Password reset request',
            'password_reset',
            [
                'email' => $email,
                'token' => $token,
                'userName' => $userName,
                'resetUrl' => $resetUrl,
            ]
        );
    }

    public function sendAccountCreated(string $email, string $userName, string $temporaryPassword): bool
    {
        return $this->send(
            $email,
            'Your account has been created',
            'account_created',
            [
                'email' => $email,
                'userName' => $userName,
                'temporaryPassword' => $temporaryPassword,
            ]
        );
    }

    public function sendCandidatureValidated(string $email, string $studentName): bool
    {
        return $this->send(
            $email,
            'Candidature validated',
            'candidature_validated',
            [
                'email' => $email,
                'studentName' => $studentName,
            ]
        );
    }

    public function sendCandidatureRejected(string $email, string $studentName, string $reason): bool
    {
        return $this->send(
            $email,
            'Candidature rejected',
            'candidature_rejected',
            [
                'email' => $email,
                'studentName' => $studentName,
                'reason' => $reason,
            ]
        );
    }

    public function sendRapportApproved(string $email, string $studentName): bool
    {
        return $this->send(
            $email,
            'Report approved',
            'rapport_approved',
            [
                'email' => $email,
                'studentName' => $studentName,
            ]
        );
    }

    public function sendRapportReturned(string $email, string $studentName, string $comments): bool
    {
        return $this->send(
            $email,
            'Report returned',
            'rapport_returned',
            [
                'email' => $email,
                'studentName' => $studentName,
                'comments' => $comments,
            ]
        );
    }

    public function sendSoutenanceScheduled(
        string $email,
        string $studentName,
        DateTimeInterface $date,
        string $salle,
        string $theme
    ): bool {
        return $this->send(
            $email,
            'Soutenance scheduled',
            'soutenance_scheduled',
            [
                'email' => $email,
                'studentName' => $studentName,
                'date' => $date->format('Y-m-d'),
                'time' => $date->format('H:i'),
                'dateTime' => $date->format('Y-m-d H:i'),
                'salle' => $salle,
                'theme' => $theme,
            ]
        );
    }

    public function sendCommissionNotification(array $emails, string $sessionInfo): bool
    {
        $success = true;

        foreach ($emails as $email) {
            if (!is_string($email) || trim($email) === '') {
                $success = false;
                continue;
            }

            $sent = $this->send(
                $email,
                'Commission session notification',
                'commission_notification',
                [
                    'email' => $email,
                    'sessionInfo' => $sessionInfo,
                ]
            );

            if (!$sent) {
                $success = false;
            }
        }

        return $success;
    }

    public function sendResultatFinal(string $email, string $studentName, string $decision, ?string $mention): bool
    {
        return $this->send(
            $email,
            'Final result',
            'resultat_final',
            [
                'email' => $email,
                'studentName' => $studentName,
                'decision' => $decision,
                'mention' => $mention ?? '',
            ]
        );
    }

    private function renderTemplate(string $template, array $data): ?string
    {
        try {
            return $this->renderer->render($template, $data);
        } catch (\Throwable $e) {
            $this->logger->error('Email template render failed', [
                'template' => $template,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function createMailer(): object
    {
        $mailerClass = 'PHPMailer\\PHPMailer\\PHPMailer';
        $mailer = new $mailerClass(true);
        $mailer->isSMTP();
        $mailer->Host = (string) ($this->smtpConfig['host'] ?? '');
        $mailer->Port = (int) ($this->smtpConfig['port'] ?? 587);

        $username = (string) ($this->smtpConfig['username'] ?? '');
        $password = (string) ($this->smtpConfig['password'] ?? '');

        if ($username !== '' || $password !== '') {
            $mailer->SMTPAuth = true;
            $mailer->Username = $username;
            $mailer->Password = $password;
        } else {
            $mailer->SMTPAuth = false;
        }

        $encryption = strtolower((string) ($this->smtpConfig['encryption'] ?? ''));

        if ($encryption === 'tls' || $encryption === 'starttls') {
            $mailer->SMTPSecure = 'tls';
        } elseif ($encryption === 'ssl' || $encryption === 'smtps') {
            $mailer->SMTPSecure = 'ssl';
        } else {
            $mailer->SMTPSecure = '';
        }

        $mailer->CharSet = 'UTF-8';

        return $mailer;
    }

    private function resolveFrom(): array
    {
        $fromEmail = (string) $this->settings->get('email_from', $this->smtpConfig['from_email'] ?? '');
        $fromName = (string) $this->settings->get('email_from_name', $this->smtpConfig['from_name'] ?? '');

        if ($fromEmail === '') {
            $fromEmail = (string) ($this->smtpConfig['username'] ?? '');
        }

        if ($fromName === '') {
            $fromName = (string) $this->settings->get('app_name', 'Notification');
        }

        return [$fromEmail, $fromName];
    }

    private function addAttachments(object $mailer, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            if (is_string($attachment)) {
                if (is_file($attachment)) {
                    $mailer->addAttachment($attachment);
                }
                continue;
            }

            if (!is_array($attachment)) {
                continue;
            }

            $path = $attachment['path'] ?? null;

            if (!is_string($path) || $path === '' || !is_file($path)) {
                continue;
            }

            $name = $attachment['name'] ?? '';
            $type = $attachment['type'] ?? '';

            if ($name !== '' && $type !== '') {
                $mailer->addAttachment($path, $name, 'base64', $type);
            } elseif ($name !== '') {
                $mailer->addAttachment($path, $name);
            } else {
                $mailer->addAttachment($path);
            }
        }
    }

    private function convertToText(string $html): string
    {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        return trim($text);
    }

    private function queueEmail(
        string $to,
        string $subject,
        string $template,
        array $data,
        array $attachments,
        string $html,
        string $text,
        string $errorMessage
    ): void {
        try {
            $pdo = $this->createPdo();
            $stmt = $pdo->prepare(
                'INSERT INTO email_queue (recipient, subject, body_html, body_text, template, data_json, attachments_json, error_message, created_at)
                 VALUES (:recipient, :subject, :body_html, :body_text, :template, :data_json, :attachments_json, :error_message, NOW())'
            );

            $stmt->execute([
                ':recipient' => $to,
                ':subject' => $subject,
                ':body_html' => $html,
                ':body_text' => $text,
                ':template' => $template,
                ':data_json' => $this->encodeJson($data),
                ':attachments_json' => $this->encodeJson($attachments),
                ':error_message' => $errorMessage,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Email queue insert failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function createPdo(): PDO
    {
        $host = (string) ($_ENV['DB_HOST'] ?? 'localhost');
        $port = (int) ($_ENV['DB_PORT'] ?? 3306);
        $name = (string) ($_ENV['DB_NAME'] ?? 'miage_platform');
        $user = (string) ($_ENV['DB_USER'] ?? 'root');
        $password = (string) ($_ENV['DB_PASS'] ?? '');
        $charset = (string) ($_ENV['DB_CHARSET'] ?? 'utf8mb4');

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $name, $charset);
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return $pdo;
    }

    private function encodeJson(array $payload): string
    {
        try {
            return json_encode($payload, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return '[]';
        }
    }
}
