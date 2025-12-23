<?php

declare(strict_types=1);

namespace Src\Exceptions;

/**
 * Exception Notification
 * 
 * Lancée lors d'erreurs liées à l'envoi de notifications.
 */
class NotificationException extends AppException
{
    protected int $httpCode = 500;
    protected string $errorCode = 'NOTIFICATION_ERROR';

    private string $templateCode = '';
    private string $channel = '';

    /**
     * @param string $message Message d'erreur
     * @param string $templateCode Code du template de notification
     * @param string $channel Canal de notification (email, sms, etc.)
     */
    public function __construct(
        string $message = 'Erreur lors de l\'envoi de la notification',
        string $templateCode = '',
        string $channel = ''
    ) {
        $details = [];
        
        if ($templateCode !== '') {
            $details['template'] = $templateCode;
            $this->templateCode = $templateCode;
        }
        if ($channel !== '') {
            $details['channel'] = $channel;
            $this->channel = $channel;
        }

        parent::__construct($message, 500, 'NOTIFICATION_ERROR', $details);
    }

    /**
     * Crée une exception pour template non trouvé
     */
    public static function templateNotFound(string $templateCode): self
    {
        return new self(
            "Template de notification non trouvé: {$templateCode}",
            $templateCode
        );
    }

    /**
     * Crée une exception pour échec d'envoi
     */
    public static function sendFailed(string $templateCode, string $channel, string $reason = ''): self
    {
        $message = "Échec de l'envoi de notification via {$channel}";
        if ($reason !== '') {
            $message .= ": {$reason}";
        }
        
        return new self($message, $templateCode, $channel);
    }

    /**
     * Crée une exception pour destinataire invalide
     */
    public static function invalidRecipient(string $recipient): self
    {
        return new self("Destinataire invalide: {$recipient}");
    }

    /**
     * Crée une exception pour canal désactivé
     */
    public static function channelDisabled(string $channel): self
    {
        return new self(
            "Canal de notification désactivé: {$channel}",
            '',
            $channel
        );
    }

    /**
     * Retourne le code du template
     */
    public function getTemplateCode(): string
    {
        return $this->templateCode;
    }

    /**
     * Retourne le canal
     */
    public function getChannel(): string
    {
        return $this->channel;
    }
}
