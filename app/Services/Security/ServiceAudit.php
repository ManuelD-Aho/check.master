<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\Pister;
use Src\Http\Request;
use Src\Support\Auth;

/**
 * Service d'Audit (Traçabilité)
 * 
 * Journalise toutes les actions critiques avec double logging :
 * - Table pister (DB)
 * - Fichier log (via error_log, configurable pour Monolog)
 * 
 * @see Constitution VI - Auditabilité Totale
 */
class ServiceAudit
{
    /**
     * Enregistre une action d'audit
     *
     * @param string $action Type d'action (voir Pister::ACTION_*)
     * @param string|null $entiteType Type d'entité concernée
     * @param int|null $entiteId ID de l'entité
     * @param array|null $snapshot Données avant/après la modification
     */
    public static function log(
        string $action,
        ?string $entiteType = null,
        ?int $entiteId = null,
        ?array $snapshot = null
    ): void {
        // Récupérer les informations de contexte
        $userId = Auth::id();
        $ip = Request::ip();
        $userAgent = Request::userAgent();

        // Enregistrement en base de données (source principale)
        try {
            Pister::enregistrer(
                $action,
                $userId,
                $entiteType,
                $entiteId,
                $snapshot,
                $ip,
                $userAgent
            );
        } catch (\Exception $e) {
            // En cas d'erreur DB, on log quand même dans le fichier
            self::logFichier($action, $userId, $entiteType, $entiteId, $snapshot, $ip, $e->getMessage());
        }

        // Double logging fichier (backup)
        self::logFichier($action, $userId, $entiteType, $entiteId, $snapshot, $ip);
    }

    /**
     * Log dans un fichier (backup et debug)
     */
    private static function logFichier(
        string $action,
        ?int $userId,
        ?string $entiteType,
        ?int $entiteId,
        ?array $snapshot,
        ?string $ip,
        ?string $erreur = null
    ): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => $action,
            'user_id' => $userId,
            'entite_type' => $entiteType,
            'entite_id' => $entiteId,
            'ip' => $ip,
        ];

        if ($snapshot !== null) {
            $logEntry['snapshot_keys'] = array_keys($snapshot);
        }

        if ($erreur !== null) {
            $logEntry['db_error'] = $erreur;
        }

        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE);

        // Utiliser le répertoire de logs standard
        $logDir = dirname(__DIR__, 3) . '/storage/logs';
        $logFile = $logDir . '/audit-' . date('Y-m-d') . '.log';

        // Créer le répertoire si nécessaire
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        error_log($logLine . PHP_EOL, 3, $logFile);
    }

    /**
     * Log de connexion réussie
     */
    public static function logLogin(int $userId): void
    {
        self::log(Pister::ACTION_LOGIN, 'utilisateur', $userId);
    }

    /**
     * Log de déconnexion
     */
    public static function logLogout(int $userId): void
    {
        self::log(Pister::ACTION_LOGOUT, 'utilisateur', $userId);
    }

    /**
     * Log d'échec de connexion
     */
    public static function logLoginEchec(string $login, string $raison): void
    {
        self::log(Pister::ACTION_LOGIN_ECHEC, 'tentative', null, [
            'login' => $login,
            'raison' => $raison,
        ]);
    }

    /**
     * Log de déconnexion forcée (par admin)
     */
    public static function logDeconnexionForcee(int $userId, int $sessionId, int $adminId): void
    {
        self::log(Pister::ACTION_DECONNEXION_FORCEE, 'session', $sessionId, [
            'utilisateur_deconnecte' => $userId,
            'admin_id' => $adminId,
        ]);
    }

    /**
     * Log de création d'entité
     */
    public static function logCreation(string $entiteType, int $entiteId, array $donnees): void
    {
        self::log(Pister::ACTION_CREATION, $entiteType, $entiteId, [
            'apres' => $donnees,
        ]);
    }

    /**
     * Log de modification d'entité
     */
    public static function logModification(string $entiteType, int $entiteId, array $avant, array $apres): void
    {
        self::log(Pister::ACTION_MODIFICATION, $entiteType, $entiteId, [
            'avant' => $avant,
            'apres' => $apres,
        ]);
    }

    /**
     * Log de suppression d'entité
     */
    public static function logSuppression(string $entiteType, int $entiteId, array $donnees): void
    {
        self::log(Pister::ACTION_SUPPRESSION, $entiteType, $entiteId, [
            'avant' => $donnees,
        ]);
    }
}
