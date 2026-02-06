<?php
declare(strict_types=1);

namespace App\Service\Workflow;

class WorkflowRegistry
{
    private string $configPath;

    /** @var array<string, array> */
    private array $workflows = [];

    public function __construct(string $configPath)
    {
        $this->configPath = rtrim($configPath, '/\\');
    }

    public function get(string $name): array
    {
        if (isset($this->workflows[$name])) {
            return $this->workflows[$name];
        }

        $file = $this->configPath . DIRECTORY_SEPARATOR . $name . '.php';

        if (!is_file($file)) {
            throw new \InvalidArgumentException(sprintf('Workflow configuration "%s" not found.', $name));
        }

        $config = require $file;

        if (!is_array($config)) {
            throw new \InvalidArgumentException(sprintf('Workflow configuration "%s" must return an array.', $name));
        }

        $this->workflows[$name] = $config;

        return $config;
    }

    public function has(string $name): bool
    {
        return is_file($this->configPath . DIRECTORY_SEPARATOR . $name . '.php');
    }

    /**
     * @return list<string>
     */
    public function getAvailableWorkflows(): array
    {
        $names = [];
        $pattern = $this->configPath . DIRECTORY_SEPARATOR . '*.php';

        foreach (glob($pattern) ?: [] as $file) {
            $names[] = basename($file, '.php');
        }

        sort($names);

        return $names;
    }

    public function canTransition(string $workflowName, string $currentState, string $transition): bool
    {
        $config = $this->get($workflowName);
        $transitions = $config['transitions'] ?? [];

        if (!isset($transitions[$transition])) {
            return false;
        }

        $from = $transitions[$transition]['from'] ?? [];

        if (is_string($from)) {
            return $from === $currentState;
        }

        if (is_array($from)) {
            return in_array($currentState, $from, true);
        }

        return false;
    }

    /**
     * @return list<string>
     */
    public function getAvailableTransitions(string $workflowName, string $currentState): array
    {
        $config = $this->get($workflowName);
        $transitions = $config['transitions'] ?? [];
        $available = [];

        foreach ($transitions as $name => $definition) {
            $from = $definition['from'] ?? [];

            if (is_string($from) && $from === $currentState) {
                $available[] = $name;
            } elseif (is_array($from) && in_array($currentState, $from, true)) {
                $available[] = $name;
            }
        }

        return $available;
    }
}
