<?php

declare(strict_types=1);

namespace App\Services\Communication;

use App\Services\Core\ServiceParametres;
use App\Services\Security\ServiceAudit;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Service Courrier
 * 
 * Envoi d'emails via PHPMailer avec configuration SMTP.
 * Supporte HTML et pièces jointes.
 * 
 * @see Constitution III - Sécurité Par Défaut
 */
class ServiceCourrier
{
    private static ?PHPMailer $mailer = null;

    /**
     * Initialise le mailer PHPMailer
     */
    private static function getMailer(): PHPMailer
    {
        if (self::$mailer === null) {
            self::$mailer = new PHPMailer(true);

            // Configuration SMTP depuis les paramètres
            $smtpHost = ServiceParametres::get('notify.email.smtp_host', 'localhost');
            $smtpPort = (int) ServiceParametres::get('notify.email.smtp_port', 587);
            $smtpUser = ServiceParametres::get('notify.email.smtp_user', '');
            $smtpPass = ServiceParametres::get('notify.email.smtp_pass', '');
            $smtpSecure = ServiceParametres::get('notify.email.smtp_secure', 'tls');
            $fromEmail = ServiceParametres::get('notify.email.from_email', 'noreply@checkmaster.ci');
            $fromName = ServiceParametres::get('notify.email.from_name', 'CheckMaster');

            // Configuration du serveur
            self::$mailer->isSMTP();
            self::$mailer->Host = $smtpHost;
            self::$mailer->SMTPAuth = !empty($smtpUser);
            self::$mailer->Username = $smtpUser;
            self::$mailer->Password = $smtpPass;
            self::$mailer->SMTPSecure = $smtpSecure === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            self::$mailer->Port = $smtpPort;

            // Configuration de l'expéditeur
            self::$mailer->setFrom($fromEmail, $fromName);

            // Configuration du charset
            self::$mailer->CharSet = 'UTF-8';
            self::$mailer->Encoding = 'base64';
        }

        return self::$mailer;
    }

    /**
     * Réinitialise le mailer pour un nouvel envoi
     */
    private static function resetMailer(): void
    {
        $mailer = self::getMailer();
        $mailer->clearAddresses();
        $mailer->clearCCs();
        $mailer->clearBCCs();
        $mailer->clearAttachments();
        $mailer->clearCustomHeaders();
    }

    /**
     * Envoie un email simple
     */
    public static function envoyerEmail(
        string $destinataire,
        string $sujet,
        string $corps,
        bool $html = true
    ): bool {
        try {
            self::resetMailer();
            $mailer = self::getMailer();

            $mailer->addAddress($destinataire);
            $mailer->isHTML($html);
            $mailer->Subject = $sujet;
            $mailer->Body = $corps;

            if ($html) {
                // Version texte brut pour les clients qui ne supportent pas HTML
                $mailer->AltBody = strip_tags($corps);
            }

            $result = $mailer->send();

            ServiceAudit::log('envoi_email', 'email', null, [
                'destinataire' => $destinataire,
                'sujet' => $sujet,
                'succes' => $result,
            ]);

            return $result;
        } catch (PHPMailerException $e) {
            error_log("Erreur envoi email: " . $e->getMessage());

            // Enregistrer le bounce potentiel
            ServiceBounces::enregistrerEchec($destinataire, $e->getMessage());

            return false;
        }
    }

    /**
     * Envoie un email avec pièces jointes
     */
    public static function envoyerAvecPiecesJointes(
        string $destinataire,
        string $sujet,
        string $corps,
        array $piecesJointes,
        bool $html = true
    ): bool {
        try {
            self::resetMailer();
            $mailer = self::getMailer();

            $mailer->addAddress($destinataire);
            $mailer->isHTML($html);
            $mailer->Subject = $sujet;
            $mailer->Body = $corps;

            if ($html) {
                $mailer->AltBody = strip_tags($corps);
            }

            // Ajouter les pièces jointes
            foreach ($piecesJointes as $piece) {
                if (is_string($piece)) {
                    // Chemin de fichier
                    $mailer->addAttachment($piece);
                } elseif (is_array($piece)) {
                    // [chemin, nom] ou [chemin, nom, type]
                    $mailer->addAttachment(
                        $piece[0],
                        $piece[1] ?? '',
                        PHPMailer::ENCODING_BASE64,
                        $piece[2] ?? ''
                    );
                }
            }

            $result = $mailer->send();

            ServiceAudit::log('envoi_email_pj', 'email', null, [
                'destinataire' => $destinataire,
                'sujet' => $sujet,
                'pieces_jointes' => count($piecesJointes),
                'succes' => $result,
            ]);

            return $result;
        } catch (PHPMailerException $e) {
            error_log("Erreur envoi email avec PJ: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie un email à plusieurs destinataires
     */
    public static function envoyerMultiple(
        array $destinataires,
        string $sujet,
        string $corps,
        bool $html = true
    ): array {
        $resultats = [
            'envoyes' => 0,
            'echecs' => 0,
            'erreurs' => [],
        ];

        foreach ($destinataires as $destinataire) {
            $succes = self::envoyerEmail($destinataire, $sujet, $corps, $html);

            if ($succes) {
                $resultats['envoyes']++;
            } else {
                $resultats['echecs']++;
                $resultats['erreurs'][] = $destinataire;
            }
        }

        return $resultats;
    }

    /**
     * Envoie un email en copie cachée (BCC) pour mass mailing
     */
    public static function envoyerEnMasse(
        array $destinataires,
        string $sujet,
        string $corps,
        bool $html = true
    ): bool {
        if (empty($destinataires)) {
            return false;
        }

        try {
            self::resetMailer();
            $mailer = self::getMailer();

            // Premier destinataire en To
            $mailer->addAddress(array_shift($destinataires));

            // Reste en BCC
            foreach ($destinataires as $dest) {
                $mailer->addBCC($dest);
            }

            $mailer->isHTML($html);
            $mailer->Subject = $sujet;
            $mailer->Body = $corps;

            if ($html) {
                $mailer->AltBody = strip_tags($corps);
            }

            return $mailer->send();
        } catch (PHPMailerException $e) {
            error_log("Erreur envoi en masse: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si une adresse email est valide et peut recevoir des emails
     */
    public static function verifierAdresse(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Vérifier si l'email n'est pas bloqué
        return !ServiceBounces::estBloque($email);
    }

    /**
     * Teste la configuration SMTP
     */
    public static function testerConfiguration(): array
    {
        try {
            $mailer = self::getMailer();

            // Test de connexion SMTP
            $mailer->SMTPDebug = SMTP::DEBUG_CONNECTION;
            ob_start();
            $connected = $mailer->smtpConnect();
            $debug = ob_get_clean();
            $mailer->SMTPDebug = SMTP::DEBUG_OFF;

            if ($connected) {
                $mailer->smtpClose();
            }

            return [
                'succes' => $connected,
                'host' => $mailer->Host,
                'port' => $mailer->Port,
                'debug' => $debug,
            ];
        } catch (PHPMailerException $e) {
            return [
                'succes' => false,
                'erreur' => $e->getMessage(),
            ];
        }
    }
}
