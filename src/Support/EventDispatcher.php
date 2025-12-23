<?php

declare(strict_types=1);

namespace Src\Support;

/**
 * Dispatcher d'événements
 * 
 * Système d'événements léger pour le découplage des composants.
 */
class EventDispatcher
{
    /**
     * Instance singleton
     */
    private static ?self $instance = null;

    /**
     * Listeners enregistrés
     *
     * @var array<string, array<int, array{callback: callable, priority: int}>>
     */
    private array $listeners = [];

    /**
     * Événements différés
     *
     * @var array<array{event: string, payload: mixed}>
     */
    private array $deferred = [];

    /**
     * Indique si l'exécution des listeners est activée
     */
    private bool $enabled = true;

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
     * Réinitialise l'instance
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Enregistre un listener pour un événement
     *
     * @param string $event Nom de l'événement
     * @param callable $callback Callback à exécuter
     * @param int $priority Priorité (plus élevé = exécuté en premier)
     */
    public function listen(string $event, callable $callback, int $priority = 0): self
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = [
            'callback' => $callback,
            'priority' => $priority,
        ];

        // Trier par priorité décroissante
        usort($this->listeners[$event], fn($a, $b) => $b['priority'] <=> $a['priority']);

        return $this;
    }

    /**
     * Alias pour listen()
     */
    public function on(string $event, callable $callback, int $priority = 0): self
    {
        return $this->listen($event, $callback, $priority);
    }

    /**
     * Enregistre un listener à exécuter une seule fois
     */
    public function once(string $event, callable $callback, int $priority = 0): self
    {
        $wrapper = function (mixed $payload) use ($event, $callback, &$wrapper): mixed {
            $this->removeListener($event, $wrapper);
            return $callback($payload);
        };

        return $this->listen($event, $wrapper, $priority);
    }

    /**
     * Supprime un listener spécifique
     */
    public function removeListener(string $event, callable $callback): self
    {
        if (!isset($this->listeners[$event])) {
            return $this;
        }

        $this->listeners[$event] = array_filter(
            $this->listeners[$event],
            fn($listener) => $listener['callback'] !== $callback
        );

        return $this;
    }

    /**
     * Supprime tous les listeners d'un événement
     */
    public function removeAllListeners(?string $event = null): self
    {
        if ($event === null) {
            $this->listeners = [];
        } else {
            unset($this->listeners[$event]);
        }

        return $this;
    }

    /**
     * Dispatch un événement
     *
     * @param string $event Nom de l'événement
     * @param mixed $payload Données de l'événement
     * @return mixed Payload modifié par les listeners
     */
    public function dispatch(string $event, mixed $payload = null): mixed
    {
        if (!$this->enabled) {
            return $payload;
        }

        if (!isset($this->listeners[$event])) {
            return $payload;
        }

        foreach ($this->listeners[$event] as $listener) {
            $result = ($listener['callback'])($payload);
            
            // Si le listener retourne false, on arrête la propagation
            if ($result === false) {
                break;
            }

            // Si le listener retourne une valeur, on met à jour le payload
            if ($result !== null) {
                $payload = $result;
            }
        }

        return $payload;
    }

    /**
     * Alias pour dispatch()
     */
    public function emit(string $event, mixed $payload = null): mixed
    {
        return $this->dispatch($event, $payload);
    }

    /**
     * Dispatch un événement à tous les listeners (ne s'arrête pas sur false)
     *
     * @param string $event Nom de l'événement
     * @param mixed $payload Données de l'événement
     * @return array<mixed> Résultats de tous les listeners
     */
    public function dispatchToAll(string $event, mixed $payload = null): array
    {
        if (!$this->enabled || !isset($this->listeners[$event])) {
            return [];
        }

        $results = [];
        foreach ($this->listeners[$event] as $listener) {
            $results[] = ($listener['callback'])($payload);
        }

        return $results;
    }

    /**
     * Diffère l'exécution d'un événement
     *
     * @param string $event Nom de l'événement
     * @param mixed $payload Données de l'événement
     */
    public function defer(string $event, mixed $payload = null): self
    {
        $this->deferred[] = [
            'event' => $event,
            'payload' => $payload,
        ];

        return $this;
    }

    /**
     * Exécute tous les événements différés
     */
    public function flushDeferred(): void
    {
        $deferred = $this->deferred;
        $this->deferred = [];

        foreach ($deferred as $item) {
            $this->dispatch($item['event'], $item['payload']);
        }
    }

    /**
     * Vérifie si un événement a des listeners
     */
    public function hasListeners(string $event): bool
    {
        return !empty($this->listeners[$event]);
    }

    /**
     * Retourne le nombre de listeners pour un événement
     */
    public function getListenerCount(string $event): int
    {
        return isset($this->listeners[$event]) ? count($this->listeners[$event]) : 0;
    }

    /**
     * Retourne tous les événements enregistrés
     *
     * @return array<string>
     */
    public function getEventNames(): array
    {
        return array_keys($this->listeners);
    }

    /**
     * Active/désactive l'exécution des listeners
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Vérifie si les listeners sont activés
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Enregistre des listeners depuis une classe subscriber
     *
     * @param object $subscriber Objet avec méthode getSubscribedEvents()
     */
    public function subscribe(object $subscriber): self
    {
        if (!method_exists($subscriber, 'getSubscribedEvents')) {
            return $this;
        }

        $events = $subscriber->getSubscribedEvents();

        foreach ($events as $event => $params) {
            if (is_string($params)) {
                // Simple: ['event' => 'method']
                $this->listen($event, [$subscriber, $params]);
            } elseif (is_array($params)) {
                if (is_string($params[0])) {
                    // Avec priorité: ['event' => ['method', priority]]
                    $method = $params[0];
                    $priority = $params[1] ?? 0;
                    $this->listen($event, [$subscriber, $method], $priority);
                } else {
                    // Multiple: ['event' => [['method1', priority1], ['method2', priority2]]]
                    foreach ($params as $listener) {
                        $method = $listener[0];
                        $priority = $listener[1] ?? 0;
                        $this->listen($event, [$subscriber, $method], $priority);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Helper statique pour dispatcher un événement rapidement
     */
    public static function fire(string $event, mixed $payload = null): mixed
    {
        return self::getInstance()->dispatch($event, $payload);
    }
}
