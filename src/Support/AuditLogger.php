<?php

declare(strict_types=1);

namespace Src\Support;

/**
 * Journalisation d'audit
 * 
 * Logger spécialisé pour les pistes d'audit conformes aux exigences CheckMaster.
 */
class AuditLogger
{
    /**
     * Instance singleton
     */
    private static ?self $instance = null;

    /**
     * Connexion PDO
     */
    private ?\PDO $pdo = null;

    /**
     * Nom de la table d'audit
     */
    private string $tableName = 'pister';

    /**
     * ID utilisateur courant
     */
    private ?int $userId = null;

    /**
     * Adresse IP courante
     */
    private ?string $ipAddress = null;

    /**
     * User Agent courant
     */
    private ?string $userAgent = null;

    /**
     * Constructeur privé (singleton)
     */
    private function __construct() {}

    /**
     * Retourne l'instance singleton
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Configure le logger
     */
    public function configure(\PDO $pdo, string $tableName = 'pister'): self
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Définit l'utilisateur courant
     */
    public function setUser(?int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Définit l'adresse IP
     */
    public function setIpAddress(?string $ip): self
    {
        $this->ipAddress = $ip;
        return $this;
    }

    /**
     * Définit le User Agent
     */
    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Initialise les informations de contexte automatiquement
     */
    public function initContext(): self
    {
        $this->ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Essayer de récupérer l'utilisateur courant
        if (function_exists('auth_id')) {
            $this->userId = auth_id();
        }

        return $this;
    }

    /**
     * Enregistre une action d'audit
     *
     * @param string $action Description de l'action
     * @param string $entiteType Type d'entité concernée (ex: 'dossier', 'paiement')
     * @param int|null $entiteId ID de l'entité
     * @param array<string, mixed> $details Détails supplémentaires (before/after)
     */
    public function log(
        string $action,
        string $entiteType,
        ?int $entiteId = null,
        array $details = []
    ): bool {
        if ($this->pdo === null) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO {$this->tableName} 
                (action, entite_type, entite_id, utilisateur_id, details, ip_address, user_agent, created_at)
                VALUES 
                (:action, :entite_type, :entite_id, :utilisateur_id, :details, :ip_address, :user_agent, NOW())
            ");

            return $stmt->execute([
                'action' => $action,
                'entite_type' => $entiteType,
                'entite_id' => $entiteId,
                'utilisateur_id' => $this->userId,
                'details' => !empty($details) ? json_encode($details, JSON_UNESCAPED_UNICODE) : null,
                'ip_address' => $this->ipAddress,
                'user_agent' => $this->userAgent,
            ]);
        } catch (\PDOException $e) {
            // Log l'erreur mais ne pas faire échouer l'opération principale
            error_log("Audit log failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enregistre une création
     *
     * @param string $entiteType Type d'entité
     * @param int $entiteId ID créé
     * @param array<string, mixed> $data Données créées
     */
    public function logCreate(string $entiteType, int $entiteId, array $data = []): bool
    {
        return $this->log(
            "Création de {$entiteType}",
            $entiteType,
            $entiteId,
            ['after' => $this->sanitizeData($data)]
        );
    }

    /**
     * Enregistre une modification
     *
     * @param string $entiteType Type d'entité
     * @param int $entiteId ID modifié
     * @param array<string, mixed> $before État avant
     * @param array<string, mixed> $after État après
     */
    public function logUpdate(string $entiteType, int $entiteId, array $before, array $after): bool
    {
        // Calculer les différences
        $changes = $this->calculateChanges($before, $after);

        if (empty($changes)) {
            return true; // Pas de changement réel
        }

        return $this->log(
            "Modification de {$entiteType}",
            $entiteType,
            $entiteId,
            [
                'before' => $this->sanitizeData($before),
                'after' => $this->sanitizeData($after),
                'changes' => $changes,
            ]
        );
    }

    /**
     * Enregistre une suppression
     *
     * @param string $entiteType Type d'entité
     * @param int $entiteId ID supprimé
     * @param array<string, mixed> $data Données supprimées
     */
    public function logDelete(string $entiteType, int $entiteId, array $data = []): bool
    {
        return $this->log(
            "Suppression de {$entiteType}",
            $entiteType,
            $entiteId,
            ['before' => $this->sanitizeData($data)]
        );
    }

    /**
     * Enregistre une connexion
     */
    public function logLogin(int $userId, bool $success, string $reason = ''): bool
    {
        $this->userId = $userId;
        return $this->log(
            $success ? 'Connexion réussie' : 'Tentative de connexion échouée',
            'authentification',
            $userId,
            ['success' => $success, 'reason' => $reason]
        );
    }

    /**
     * Enregistre une déconnexion
     */
    public function logLogout(int $userId): bool
    {
        return $this->log(
            'Déconnexion',
            'authentification',
            $userId
        );
    }

    /**
     * Enregistre une transition de workflow
     */
    public function logWorkflowTransition(
        int $dossierId,
        string $fromState,
        string $toState,
        string $transitionCode,
        ?string $comment = null
    ): bool {
        return $this->log(
            "Transition workflow: {$fromState} -> {$toState}",
            'workflow',
            $dossierId,
            [
                'from_state' => $fromState,
                'to_state' => $toState,
                'transition' => $transitionCode,
                'comment' => $comment,
            ]
        );
    }

    /**
     * Enregistre un accès à un document
     */
    public function logDocumentAccess(int $documentId, string $action = 'consultation'): bool
    {
        return $this->log(
            "Accès document: {$action}",
            'document',
            $documentId,
            ['action' => $action]
        );
    }

    /**
     * Enregistre un export de données
     *
     * @param string $exportType Type d'export (excel, csv, pdf)
     * @param array<string, mixed> $filters Filtres appliqués
     * @param int $count Nombre d'enregistrements exportés
     */
    public function logExport(string $exportType, array $filters, int $count): bool
    {
        return $this->log(
            "Export {$exportType}",
            'export',
            null,
            [
                'type' => $exportType,
                'filters' => $filters,
                'count' => $count,
            ]
        );
    }

    /**
     * Enregistre une alerte de sécurité
     */
    public function logSecurityAlert(string $alertType, array $details = []): bool
    {
        return $this->log(
            "Alerte sécurité: {$alertType}",
            'securite',
            null,
            array_merge(['alert_type' => $alertType], $details)
        );
    }

    /**
     * Calcule les changements entre deux états
     *
     * @return array<string, array{old: mixed, new: mixed}>
     */
    private function calculateChanges(array $before, array $after): array
    {
        $changes = [];
        $allKeys = array_unique(array_merge(array_keys($before), array_keys($after)));

        foreach ($allKeys as $key) {
            $oldValue = $before[$key] ?? null;
            $newValue = $after[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$key] = ['old' => $oldValue, 'new' => $newValue];
            }
        }

        return $changes;
    }

    /**
     * Sanitize les données sensibles avant stockage
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function sanitizeData(array $data): array
    {
        $sensitiveFields = ['password', 'mot_de_passe', 'mdp', 'token', 'secret', 'api_key'];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }

        return $data;
    }

    /**
     * Recherche dans les logs d'audit
     *
     * @param array<string, mixed> $filters
     * @param int $limit
     * @param int $offset
     * @return array<int, array<string, mixed>>
     */
    public function search(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        if ($this->pdo === null) {
            return [];
        }

        $where = ['1=1'];
        $params = [];

        if (isset($filters['entite_type'])) {
            $where[] = 'entite_type = :entite_type';
            $params['entite_type'] = $filters['entite_type'];
        }

        if (isset($filters['entite_id'])) {
            $where[] = 'entite_id = :entite_id';
            $params['entite_id'] = $filters['entite_id'];
        }

        if (isset($filters['utilisateur_id'])) {
            $where[] = 'utilisateur_id = :utilisateur_id';
            $params['utilisateur_id'] = $filters['utilisateur_id'];
        }

        if (isset($filters['date_from'])) {
            $where[] = 'created_at >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }

        if (isset($filters['date_to'])) {
            $where[] = 'created_at <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }

        if (isset($filters['action'])) {
            $where[] = 'action LIKE :action';
            $params['action'] = '%' . $filters['action'] . '%';
        }

        $sql = "SELECT * FROM {$this->tableName} 
                WHERE " . implode(' AND ', $where) . "
                ORDER BY created_at DESC
                LIMIT {$limit} OFFSET {$offset}";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Helper statique pour log rapide
     *
     * @param array<string, mixed> $details
     */
    public static function record(
        string $action,
        string $entiteType,
        ?int $entiteId = null,
        array $details = []
    ): bool {
        return self::getInstance()->log($action, $entiteType, $entiteId, $details);
    }
}
