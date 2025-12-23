<?php

declare(strict_types=1);

namespace Src\Support;

/**
 * Gestionnaire de configuration
 * 
 * Gère la configuration DB-driven avec cache.
 * Charge les paramètres depuis la base de données et les fichiers.
 */
class ConfigManager
{
    /**
     * Instance singleton
     */
    private static ?self $instance = null;

    /**
     * Cache des valeurs de configuration
     *
     * @var array<string, mixed>
     */
    private array $cache = [];

    /**
     * Indique si le cache est chargé
     */
    private bool $loaded = false;

    /**
     * Connexion PDO
     */
    private ?\PDO $pdo = null;

    /**
     * Nom de la table de configuration
     */
    private string $tableName = 'parametres';

    /**
     * Configuration depuis les fichiers
     *
     * @var array<string, mixed>
     */
    private array $fileConfig = [];

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
     * Configure le gestionnaire
     */
    public function configure(\PDO $pdo, string $tableName = 'parametres'): self
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Charge une configuration depuis un fichier
     */
    public function loadFile(string $path, string $namespace = ''): self
    {
        if (!file_exists($path)) {
            return $this;
        }

        $config = require $path;
        
        if (!is_array($config)) {
            return $this;
        }

        if ($namespace !== '') {
            $this->fileConfig[$namespace] = $config;
        } else {
            $this->fileConfig = array_merge($this->fileConfig, $config);
        }

        return $this;
    }

    /**
     * Charge toutes les configurations depuis un répertoire
     */
    public function loadDirectory(string $directory): self
    {
        if (!is_dir($directory)) {
            return $this;
        }

        $files = glob($directory . '/*.php');
        
        foreach ($files as $file) {
            $namespace = pathinfo($file, PATHINFO_FILENAME);
            $this->loadFile($file, $namespace);
        }

        return $this;
    }

    /**
     * Charge les paramètres depuis la base de données
     */
    public function loadFromDatabase(): self
    {
        if ($this->pdo === null || $this->loaded) {
            return $this;
        }

        try {
            $stmt = $this->pdo->query(
                "SELECT cle, valeur, type FROM {$this->tableName} WHERE actif = 1"
            );
            
            if ($stmt === false) {
                return $this;
            }

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $key = $row['cle'];
                $value = $this->castValue($row['valeur'], $row['type'] ?? 'string');
                $this->cache[$key] = $value;
            }

            $this->loaded = true;
        } catch (\PDOException $e) {
            // Silencieux si la table n'existe pas encore
        }

        return $this;
    }

    /**
     * Récupère une valeur de configuration
     *
     * @param string $key Clé avec notation pointée (ex: 'app.name')
     * @param mixed $default Valeur par défaut
     */
    public function get(string $key, mixed $default = null): mixed
    {
        // 1. Chercher dans le cache DB
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        // 2. Chercher dans la config fichier avec notation pointée
        $value = $this->getFromArray($this->fileConfig, $key);
        if ($value !== null) {
            return $value;
        }

        // 3. Chercher dans la DB si pas encore chargé
        if ($this->pdo !== null && !$this->loaded) {
            $this->loadFromDatabase();
            if (isset($this->cache[$key])) {
                return $this->cache[$key];
            }
        }

        return $default;
    }

    /**
     * Définit une valeur de configuration en base de données
     *
     * @param string $key Clé
     * @param mixed $value Valeur
     * @param string $type Type de la valeur (string, int, float, bool, json)
     */
    public function set(string $key, mixed $value, string $type = 'string'): bool
    {
        if ($this->pdo === null) {
            return false;
        }

        $stringValue = $this->valueToString($value, $type);

        try {
            // Upsert
            $stmt = $this->pdo->prepare(
                "INSERT INTO {$this->tableName} (cle, valeur, type, updated_at) 
                 VALUES (:cle, :valeur, :type, NOW())
                 ON DUPLICATE KEY UPDATE valeur = :valeur2, type = :type2, updated_at = NOW()"
            );

            $result = $stmt->execute([
                'cle' => $key,
                'valeur' => $stringValue,
                'type' => $type,
                'valeur2' => $stringValue,
                'type2' => $type,
            ]);

            if ($result) {
                $this->cache[$key] = $value;
            }

            return $result;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Supprime une valeur de configuration
     */
    public function forget(string $key): bool
    {
        unset($this->cache[$key]);

        if ($this->pdo === null) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare(
                "DELETE FROM {$this->tableName} WHERE cle = :cle"
            );
            return $stmt->execute(['cle' => $key]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Vérifie si une clé existe
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Retourne toutes les configurations d'un namespace
     *
     * @return array<string, mixed>
     */
    public function all(?string $namespace = null): array
    {
        if ($namespace === null) {
            return array_merge($this->fileConfig, $this->cache);
        }

        // Filtrer par préfixe
        $result = [];
        $prefix = $namespace . '.';
        
        foreach ($this->cache as $key => $value) {
            if (str_starts_with($key, $prefix)) {
                $shortKey = substr($key, strlen($prefix));
                $result[$shortKey] = $value;
            }
        }

        if (isset($this->fileConfig[$namespace])) {
            $result = array_merge($result, $this->fileConfig[$namespace]);
        }

        return $result;
    }

    /**
     * Vide le cache
     */
    public function clearCache(): self
    {
        $this->cache = [];
        $this->loaded = false;
        return $this;
    }

    /**
     * Récupère une valeur depuis un tableau avec notation pointée
     */
    private function getFromArray(array $array, string $key): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        $keys = explode('.', $key);
        $current = $array;

        foreach ($keys as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return null;
            }
            $current = $current[$segment];
        }

        return $current;
    }

    /**
     * Cast une valeur selon son type
     */
    private function castValue(string $value, string $type): mixed
    {
        return match ($type) {
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'bool', 'boolean' => in_array(strtolower($value), ['true', '1', 'yes', 'on'], true),
            'json', 'array' => json_decode($value, true) ?? [],
            default => $value,
        };
    }

    /**
     * Convertit une valeur en string pour stockage
     */
    private function valueToString(mixed $value, string $type): string
    {
        if ($type === 'json' || $type === 'array') {
            return json_encode($value) ?: '[]';
        }

        if ($type === 'bool' || $type === 'boolean') {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    /**
     * Helper statique pour récupérer une valeur
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        return self::getInstance()->get($key, $default);
    }
}
