<?php

declare(strict_types=1);

namespace Src\Support;

use Hashids\Hashids;

/**
 * Gestionnaire Hashids centralisé
 * 
 * Fournit une interface unifiée pour l'encodage/décodage des IDs
 * avec support de différents contextes (par entité).
 */
class HashidManager
{
    /**
     * Instance singleton
     */
    private static ?self $instance = null;

    /**
     * Instance Hashids par défaut
     */
    private ?Hashids $defaultHashids = null;

    /**
     * Instances Hashids par contexte
     *
     * @var array<string, Hashids>
     */
    private array $contextHashids = [];

    /**
     * Sel par défaut
     */
    private string $salt = 'checkmaster-default-salt';

    /**
     * Longueur minimale par défaut
     */
    private int $minLength = 8;

    /**
     * Alphabet personnalisé
     */
    private string $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

    /**
     * Mapping des sels par contexte/entité
     *
     * @var array<string, string>
     */
    private array $contextSalts = [
        'etudiant' => 'cm-etu-salt',
        'enseignant' => 'cm-ens-salt',
        'dossier' => 'cm-dos-salt',
        'paiement' => 'cm-pay-salt',
        'soutenance' => 'cm-sou-salt',
        'commission' => 'cm-com-salt',
        'jury' => 'cm-jur-salt',
        'document' => 'cm-doc-salt',
        'archive' => 'cm-arc-salt',
        'notification' => 'cm-not-salt',
    ];

    /**
     * Constructeur privé (singleton)
     */
    private function __construct()
    {
        // Charger la configuration si disponible
        $this->loadConfig();
    }

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
     * Réinitialise l'instance (utile pour les tests)
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Charge la configuration depuis les fichiers config
     */
    private function loadConfig(): void
    {
        // Essayer de charger depuis la config si disponible
        if (function_exists('config')) {
            $salt = config('security.hashids.salt');
            if (is_string($salt) && $salt !== '') {
                $this->salt = $salt;
            }

            $minLength = config('security.hashids.min_length');
            if (is_int($minLength) && $minLength > 0) {
                $this->minLength = $minLength;
            }
        }
    }

    /**
     * Configure le gestionnaire
     *
     * @param array<string, mixed> $config
     */
    public function configure(array $config): self
    {
        if (isset($config['salt']) && is_string($config['salt'])) {
            $this->salt = $config['salt'];
        }

        if (isset($config['min_length']) && is_int($config['min_length'])) {
            $this->minLength = max(1, $config['min_length']);
        }

        if (isset($config['alphabet']) && is_string($config['alphabet'])) {
            $this->alphabet = $config['alphabet'];
        }

        if (isset($config['context_salts']) && is_array($config['context_salts'])) {
            $this->contextSalts = array_merge($this->contextSalts, $config['context_salts']);
        }

        // Réinitialiser les instances pour appliquer la nouvelle config
        $this->defaultHashids = null;
        $this->contextHashids = [];

        return $this;
    }

    /**
     * Retourne l'instance Hashids par défaut
     */
    private function getDefault(): Hashids
    {
        if ($this->defaultHashids === null) {
            $this->defaultHashids = new Hashids($this->salt, $this->minLength, $this->alphabet);
        }

        return $this->defaultHashids;
    }

    /**
     * Retourne l'instance Hashids pour un contexte donné
     */
    private function getForContext(string $context): Hashids
    {
        if (!isset($this->contextHashids[$context])) {
            $salt = $this->contextSalts[$context] ?? ($this->salt . '-' . $context);
            $this->contextHashids[$context] = new Hashids($salt, $this->minLength, $this->alphabet);
        }

        return $this->contextHashids[$context];
    }

    /**
     * Encode un ID
     *
     * @param int $id ID à encoder
     * @param string|null $context Contexte optionnel (entité)
     */
    public function encode(int $id, ?string $context = null): string
    {
        $hashids = $context !== null ? $this->getForContext($context) : $this->getDefault();
        return $hashids->encode($id);
    }

    /**
     * Encode plusieurs IDs
     *
     * @param array<int> $ids IDs à encoder
     * @param string|null $context Contexte optionnel
     */
    public function encodeMany(array $ids, ?string $context = null): string
    {
        $hashids = $context !== null ? $this->getForContext($context) : $this->getDefault();
        return $hashids->encode(...$ids);
    }

    /**
     * Décode un hash en ID
     *
     * @param string $hash Hash à décoder
     * @param string|null $context Contexte optionnel
     * @return int|null ID décodé ou null si invalide
     */
    public function decode(string $hash, ?string $context = null): ?int
    {
        $hashids = $context !== null ? $this->getForContext($context) : $this->getDefault();
        $result = $hashids->decode($hash);

        if (empty($result)) {
            return null;
        }

        return (int) $result[0];
    }

    /**
     * Décode un hash en plusieurs IDs
     *
     * @param string $hash Hash à décoder
     * @param string|null $context Contexte optionnel
     * @return array<int> IDs décodés
     */
    public function decodeMany(string $hash, ?string $context = null): array
    {
        $hashids = $context !== null ? $this->getForContext($context) : $this->getDefault();
        $result = $hashids->decode($hash);

        return array_map('intval', $result);
    }

    /**
     * Décode ou retourne la valeur par défaut
     *
     * @param string $hash Hash à décoder
     * @param int $default Valeur par défaut
     * @param string|null $context Contexte optionnel
     */
    public function decodeOr(string $hash, int $default, ?string $context = null): int
    {
        return $this->decode($hash, $context) ?? $default;
    }

    /**
     * Vérifie si un hash est valide
     *
     * @param string $hash Hash à vérifier
     * @param string|null $context Contexte optionnel
     */
    public function isValid(string $hash, ?string $context = null): bool
    {
        return $this->decode($hash, $context) !== null;
    }

    /**
     * Encode un ID d'étudiant
     */
    public function encodeEtudiant(int $id): string
    {
        return $this->encode($id, 'etudiant');
    }

    /**
     * Décode un hash d'étudiant
     */
    public function decodeEtudiant(string $hash): ?int
    {
        return $this->decode($hash, 'etudiant');
    }

    /**
     * Encode un ID d'enseignant
     */
    public function encodeEnseignant(int $id): string
    {
        return $this->encode($id, 'enseignant');
    }

    /**
     * Décode un hash d'enseignant
     */
    public function decodeEnseignant(string $hash): ?int
    {
        return $this->decode($hash, 'enseignant');
    }

    /**
     * Encode un ID de dossier
     */
    public function encodeDossier(int $id): string
    {
        return $this->encode($id, 'dossier');
    }

    /**
     * Décode un hash de dossier
     */
    public function decodeDossier(string $hash): ?int
    {
        return $this->decode($hash, 'dossier');
    }

    /**
     * Encode un ID de paiement
     */
    public function encodePaiement(int $id): string
    {
        return $this->encode($id, 'paiement');
    }

    /**
     * Décode un hash de paiement
     */
    public function decodePaiement(string $hash): ?int
    {
        return $this->decode($hash, 'paiement');
    }

    /**
     * Encode un ID de document
     */
    public function encodeDocument(int $id): string
    {
        return $this->encode($id, 'document');
    }

    /**
     * Décode un hash de document
     */
    public function decodeDocument(string $hash): ?int
    {
        return $this->decode($hash, 'document');
    }

    /**
     * Helper statique pour encoder rapidement
     */
    public static function hash(int $id, ?string $context = null): string
    {
        return self::getInstance()->encode($id, $context);
    }

    /**
     * Helper statique pour décoder rapidement
     */
    public static function unhash(string $hash, ?string $context = null): ?int
    {
        return self::getInstance()->decode($hash, $context);
    }
}
