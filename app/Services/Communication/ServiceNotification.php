<?php

declare(strict_types=1);

namespace App\Services\Communication;

use App\Models\NotificationTemplate;
use App\Models\NotificationQueue;
use App\Models\NotificationHistorique;
use App\Models\Utilisateur;
use App\Services\Security\ServiceAudit;
use Src\Exceptions\NotificationException;

/**
 * Service Notification
 * 
 * Gestion des notifications multi-canaux (Email, SMS, Messagerie interne).
 * Supporte 71 templates et le traitement par file d'attente.
 * 
 * @see PRD Section Communication
 */
class ServiceNotification
{
    /**
     * Envoie une notification à partir d'un code template
     *
     * @param string $codeTemplate Code du template
     * @param int[] $destinataireIds IDs des utilisateurs destinataires
     * @param array $variables Variables à substituer dans le template
     * @param int $priorite Priorité (1=haute, 5=normale, 10=basse)
     */
    public static function envoyerParCode(
        string $codeTemplate,
        array $destinataireIds,
        array $variables = [],
        int $priorite = NotificationQueue::PRIORITE_NORMALE
    ): int {
        $template = NotificationTemplate::findByCode($codeTemplate);
        if ($template === null) {
            // Template non trouvé - ne pas bloquer mais logger
            error_log("Template de notification non trouvé: {$codeTemplate}");
            return 0;
        }

        return self::envoyerDepuisTemplate($template, $destinataireIds, $variables, $priorite);
    }

    /**
     * Envoie une notification à partir d'un template
     */
    public static function envoyerDepuisTemplate(
        NotificationTemplate $template,
        array $destinataireIds,
        array $variables = [],
        int $priorite = NotificationQueue::PRIORITE_NORMALE
    ): int {
        $count = 0;

        foreach ($destinataireIds as $destinataireId) {
            // Ajouter à la file d'attente
            NotificationQueue::ajouter(
                $template->getId(),
                $destinataireId,
                $template->canal ?? NotificationTemplate::CANAL_EMAIL,
                $variables,
                $priorite
            );
            $count++;
        }

        return $count;
    }

    /**
     * Envoie une notification directe (sans template)
     */
    public static function envoyerDirecte(
        string $canal,
        int $destinataireId,
        string $sujet,
        string $corps,
        int $priorite = NotificationQueue::PRIORITE_NORMALE
    ): bool {
        $user = Utilisateur::find($destinataireId);
        if ($user === null) {
            return false;
        }

        $result = false;

        switch ($canal) {
            case NotificationTemplate::CANAL_EMAIL:
                $result = ServiceCourrier::envoyerEmail(
                    $user->login_utilisateur ?? '',
                    $sujet,
                    $corps
                );
                break;

            case NotificationTemplate::CANAL_MESSAGERIE:
                $result = ServiceMessagerie::envoyerSysteme(
                    $destinataireId,
                    $sujet,
                    $corps
                );
                break;

            case NotificationTemplate::CANAL_SMS:
                // SMS non implémenté pour l'instant
                $result = false;
                break;
        }

        // Enregistrer dans l'historique
        self::enregistrerHistorique($destinataireId, $canal, $sujet, $result);

        return $result;
    }

    /**
     * Traite la file d'attente des notifications
     */
    public static function traiterFileAttente(int $limite = 50): array
    {
        $resultats = [
            'traites' => 0,
            'succes' => 0,
            'echecs' => 0,
        ];

        $notifications = NotificationQueue::enAttente($limite);

        foreach ($notifications as $notification) {
            $notification->marquerEnCours();

            try {
                $success = self::traiterNotification($notification);

                if ($success) {
                    $notification->marquerEnvoyee();
                    $resultats['succes']++;
                } else {
                    $notification->marquerEchec('Échec d\'envoi');
                    $resultats['echecs']++;
                }
            } catch (\Exception $e) {
                $notification->marquerEchec($e->getMessage());
                $resultats['echecs']++;
            }

            $resultats['traites']++;
        }

        return $resultats;
    }

    /**
     * Traite une notification individuelle
     */
    private static function traiterNotification(NotificationQueue $notification): bool
    {
        $template = $notification->template();
        $destinataire = $notification->destinataire();

        if ($template === null || $destinataire === null) {
            return false;
        }

        $variables = $notification->getVariables();
        $sujet = $template->compilerSujet($variables);
        $corps = $template->compilerCorps($variables);

        $result = false;
        $canal = $notification->canal ?? $template->canal;

        switch ($canal) {
            case NotificationTemplate::CANAL_EMAIL:
                $email = $destinataire->login_utilisateur ?? '';
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $result = ServiceCourrier::envoyerEmail($email, $sujet, $corps);
                }
                break;

            case NotificationTemplate::CANAL_MESSAGERIE:
                $result = ServiceMessagerie::envoyerSysteme(
                    $destinataire->getId(),
                    $sujet,
                    $corps
                );
                break;

            case NotificationTemplate::CANAL_SMS:
                // SMS non implémenté
                $result = false;
                break;
        }

        // Enregistrer dans l'historique
        self::enregistrerHistorique(
            $destinataire->getId(),
            $canal,
            $sujet,
            $result,
            $template->getId()
        );

        return $result;
    }

    /**
     * Enregistre une notification dans l'historique
     */
    private static function enregistrerHistorique(
        int $destinataireId,
        string $canal,
        string $sujet,
        bool $succes,
        ?int $templateId = null
    ): void {
        $historique = new NotificationHistorique([
            'destinataire_id' => $destinataireId,
            'template_id' => $templateId,
            'canal' => $canal,
            'sujet' => $sujet,
            'statut' => $succes ? 'Envoye' : 'Echec',
            'envoye_le' => $succes ? date('Y-m-d H:i:s') : null,
        ]);
        $historique->save();
    }

    /**
     * Envoie une notification urgente (priorité haute, traitement immédiat)
     */
    public static function envoyerUrgent(
        string $codeTemplate,
        array $destinataireIds,
        array $variables = []
    ): int {
        return self::envoyerParCode(
            $codeTemplate,
            $destinataireIds,
            $variables,
            NotificationQueue::PRIORITE_HAUTE
        );
    }

    /**
     * Retourne les notifications en attente
     */
    public static function getEnAttente(int $limite = 100): array
    {
        return NotificationQueue::enAttente($limite);
    }

    /**
     * Retourne les notifications échouées
     */
    public static function getEchouees(): array
    {
        return NotificationQueue::echouees();
    }

    /**
     * Retente l'envoi des notifications échouées
     */
    public static function retenterEchouees(): int
    {
        $echouees = NotificationQueue::echouees();
        $count = 0;

        foreach ($echouees as $notification) {
            $notification->retenter();
            $count++;
        }

        return $count;
    }

    /**
     * Nettoie les anciennes notifications
     */
    public static function nettoyer(int $joursRetention = 30): int
    {
        return NotificationQueue::nettoyer($joursRetention);
    }

    /**
     * Retourne les statistiques de notifications
     */
    public static function getStatistiques(): array
    {
        return NotificationQueue::statistiques();
    }
}
