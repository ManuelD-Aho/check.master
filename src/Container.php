<?php

declare(strict_types=1);

namespace Src;

use Closure;
use ReflectionClass;
use ReflectionParameter;
use ReflectionNamedType;
use RuntimeException;

/**
 * Conteneur d'injection de dépendances léger
 * 
 * Fournit:
 * - Singleton et transient bindings
 * - Auto-wiring basé sur la réflexion
 * - Résolution des dépendances
 */
class Container
{
    /**
     * Instance singleton du conteneur
     */
    private static ?Container $instance = null;

    /**
     * Bindings enregistrés
     *
     * @var array<string, array{concrete: mixed, shared: bool}>
     */
    private array $bindings = [];

    /**
     * Instances singleton résolues
     *
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * Alias de classes
     *
     * @var array<string, string>
     */
    private array $aliases = [];

    /**
     * Stack de résolution (pour détecter les dépendances circulaires)
     *
     * @var array<string>
     */
    private array $resolutionStack = [];

    /**
     * Retourne l'instance singleton du conteneur
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Définit l'instance globale du conteneur
     */
    public static function setInstance(?Container $container): void
    {
        self::$instance = $container;
    }

    /**
     * Enregistre un binding dans le conteneur
     *
     * @param string $abstract Nom abstrait (interface ou classe)
     * @param mixed $concrete Implémentation concrète (classe, closure, ou instance)
     * @param bool $shared Si true, sera un singleton
     */
    public function bind(string $abstract, mixed $concrete = null, bool $shared = false): self
    {
        $concrete = $concrete ?? $abstract;

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared,
        ];

        // Supprimer l'instance existante si on rebind
        unset($this->instances[$abstract]);

        return $this;
    }

    /**
     * Enregistre un singleton
     *
     * @param string $abstract Nom abstrait
     * @param mixed $concrete Implémentation concrète
     */
    public function singleton(string $abstract, mixed $concrete = null): self
    {
        return $this->bind($abstract, $concrete, true);
    }

    /**
     * Enregistre une instance existante
     *
     * @param string $abstract Nom abstrait
     * @param object $instance Instance à enregistrer
     */
    public function instance(string $abstract, object $instance): self
    {
        $this->instances[$abstract] = $instance;

        return $this;
    }

    /**
     * Enregistre un alias
     *
     * @param string $alias Alias
     * @param string $abstract Nom abstrait vers lequel pointer
     */
    public function alias(string $alias, string $abstract): self
    {
        $this->aliases[$alias] = $abstract;

        return $this;
    }

    /**
     * Vérifie si un binding existe
     */
    public function bound(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) 
            || isset($this->instances[$abstract])
            || isset($this->aliases[$abstract]);
    }

    /**
     * Résout une classe/interface depuis le conteneur
     *
     * @param string $abstract Nom abstrait à résoudre
     * @param array<string, mixed> $parameters Paramètres supplémentaires
     * @return mixed Instance résolue
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        // Résoudre les alias
        $abstract = $this->getAlias($abstract);

        // Retourner l'instance singleton si elle existe
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Détecter les dépendances circulaires
        if (in_array($abstract, $this->resolutionStack, true)) {
            throw new RuntimeException(
                "Dépendance circulaire détectée: " . implode(' -> ', $this->resolutionStack) . " -> {$abstract}"
            );
        }

        $this->resolutionStack[] = $abstract;

        try {
            $concrete = $this->getConcrete($abstract);
            $object = $this->build($concrete, $parameters);

            // Stocker si singleton
            if ($this->isShared($abstract)) {
                $this->instances[$abstract] = $object;
            }

            return $object;
        } finally {
            array_pop($this->resolutionStack);
        }
    }

    /**
     * Alias pour make()
     */
    public function get(string $abstract): mixed
    {
        return $this->make($abstract);
    }

    /**
     * Vérifie si le conteneur peut résoudre une classe
     */
    public function has(string $abstract): bool
    {
        return $this->bound($abstract) || class_exists($abstract);
    }

    /**
     * Construit une instance à partir d'une définition concrète
     *
     * @param mixed $concrete Classe, closure ou instance
     * @param array<string, mixed> $parameters Paramètres supplémentaires
     */
    private function build(mixed $concrete, array $parameters = []): mixed
    {
        // Si c'est une closure, l'exécuter
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        // Si c'est déjà un objet, le retourner
        if (is_object($concrete)) {
            return $concrete;
        }

        // Si ce n'est pas une classe, erreur
        if (!is_string($concrete) || !class_exists($concrete)) {
            throw new RuntimeException("Impossible de construire: {$concrete}");
        }

        $reflector = new ReflectionClass($concrete);

        // Vérifier que la classe est instanciable
        if (!$reflector->isInstantiable()) {
            throw new RuntimeException("La classe {$concrete} n'est pas instanciable");
        }

        $constructor = $reflector->getConstructor();

        // Pas de constructeur = instanciation simple
        if ($constructor === null) {
            return new $concrete();
        }

        // Résoudre les dépendances du constructeur
        $dependencies = $this->resolveDependencies(
            $constructor->getParameters(),
            $parameters
        );

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Résout les dépendances d'un constructeur
     *
     * @param array<ReflectionParameter> $dependencies Paramètres du constructeur
     * @param array<string, mixed> $parameters Paramètres fournis
     * @return array<mixed> Dépendances résolues
     */
    private function resolveDependencies(array $dependencies, array $parameters = []): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            $name = $dependency->getName();

            // Si un paramètre est fourni explicitement
            if (array_key_exists($name, $parameters)) {
                $results[] = $parameters[$name];
                continue;
            }

            // Essayer de résoudre par type
            $type = $dependency->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                try {
                    $results[] = $this->make($type->getName());
                    continue;
                } catch (RuntimeException $e) {
                    // Si impossible à résoudre, utiliser la valeur par défaut si disponible
                    if ($dependency->isDefaultValueAvailable()) {
                        $results[] = $dependency->getDefaultValue();
                        continue;
                    }
                    throw $e;
                }
            }

            // Valeur par défaut
            if ($dependency->isDefaultValueAvailable()) {
                $results[] = $dependency->getDefaultValue();
                continue;
            }

            // Permet null
            if ($dependency->allowsNull()) {
                $results[] = null;
                continue;
            }

            throw new RuntimeException(
                "Impossible de résoudre la dépendance [{$name}]"
            );
        }

        return $results;
    }

    /**
     * Retourne l'implémentation concrète pour un abstrait
     */
    private function getConcrete(string $abstract): mixed
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    /**
     * Vérifie si un binding est partagé (singleton)
     */
    private function isShared(string $abstract): bool
    {
        return isset($this->instances[$abstract])
            || (isset($this->bindings[$abstract]) && $this->bindings[$abstract]['shared'] === true);
    }

    /**
     * Résout un alias vers son abstrait
     */
    private function getAlias(string $abstract): string
    {
        return $this->aliases[$abstract] ?? $abstract;
    }

    /**
     * Appelle une méthode avec injection de dépendances
     *
     * @param callable|array{object|string, string} $callback Callable à appeler
     * @param array<string, mixed> $parameters Paramètres supplémentaires
     */
    public function call(callable|array $callback, array $parameters = []): mixed
    {
        if (is_array($callback)) {
            [$class, $method] = $callback;
            
            if (is_string($class)) {
                $class = $this->make($class);
            }
            
            $reflector = new \ReflectionMethod($class, $method);
            $dependencies = $this->resolveDependencies(
                $reflector->getParameters(),
                $parameters
            );
            
            return $reflector->invokeArgs($class, $dependencies);
        }

        if ($callback instanceof Closure) {
            $reflector = new \ReflectionFunction($callback);
            $dependencies = $this->resolveDependencies(
                $reflector->getParameters(),
                $parameters
            );
            
            return $callback(...$dependencies);
        }

        return $callback(...array_values($parameters));
    }

    /**
     * Vide tous les bindings et instances
     */
    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
        $this->aliases = [];
    }

    /**
     * Supprime une instance résolue
     */
    public function forgetInstance(string $abstract): void
    {
        unset($this->instances[$abstract]);
    }

    /**
     * Supprime toutes les instances résolues
     */
    public function forgetInstances(): void
    {
        $this->instances = [];
    }

    /**
     * Retourne tous les bindings enregistrés
     *
     * @return array<string, array{concrete: mixed, shared: bool}>
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * Enregistre un service provider
     */
    public function register(object $provider): void
    {
        if (method_exists($provider, 'register')) {
            $provider->register($this);
        }
    }

    /**
     * Boot des service providers
     */
    public function boot(object $provider): void
    {
        if (method_exists($provider, 'boot')) {
            $provider->boot($this);
        }
    }
}
