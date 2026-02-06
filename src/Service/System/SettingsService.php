<?php
declare(strict_types=1);

namespace App\Service\System;

use App\Entity\System\AppSetting;
use App\Entity\System\AppSettingType;
use App\Repository\System\AppSettingRepository;
use DateTimeImmutable;

class SettingsService
{
    private const CACHE_KEY = 'system_settings';

    private AppSettingRepository $repository;
    private CacheService $cache;
    private EncryptionService $encryption;
    private array $settings = [];
    private bool $loaded = false;

    public function __construct(
        AppSettingRepository $repository,
        CacheService $cache,
        EncryptionService $encryption
    ) {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->encryption = $encryption;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->loadSettings();

        if (!array_key_exists($key, $this->settings)) {
            return $default;
        }

        $entry = $this->settings[$key];
        $type = $entry['type'] ?? AppSettingType::String->value;

        return $this->decodeValue($entry['value'] ?? null, (string) $type, $default);
    }

    public function set(string $key, mixed $value): bool
    {
        try {
            $setting = $this->repository->findOneBy(['settingKey' => $key]);

            if (!$setting instanceof AppSetting) {
                $setting = new AppSetting();
                $setting->setSettingKey($key);
            }

            $existingType = null;

            try {
                $existingType = $setting->getSettingType();
            } catch (\Throwable) {
                $existingType = null;
            }

            $type = $existingType instanceof AppSettingType ? $existingType : $this->inferType($value);

            try {
                if ($setting->isSensitive()) {
                    $type = AppSettingType::Encrypted;
                }
            } catch (\Throwable) {
            }

            $storedValue = $this->encodeValue($value, $type);

            $setting->setSettingType($type);
            $setting->setSettingValue($storedValue);
            $setting->setUpdatedAt(new DateTimeImmutable());

            if ($type === AppSettingType::Encrypted) {
                $setting->setIsSensitive(true);
            }

            $this->repository->save($setting);

            $this->settings[$key] = [
                'value' => $storedValue,
                'type' => $type->value,
                'category' => $setting->getCategory(),
                'sensitive' => $type === AppSettingType::Encrypted,
            ];

            $this->cache->set(self::CACHE_KEY, $this->settings, 3600);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function getAll(): array
    {
        $this->loadSettings();
        $result = [];

        foreach ($this->settings as $key => $entry) {
            $type = $entry['type'] ?? AppSettingType::String->value;
            $result[$key] = $this->decodeValue($entry['value'] ?? null, (string) $type, null);
        }

        return $result;
    }

    public function getByGroupe(string $groupe): array
    {
        $this->loadSettings();
        $result = [];

        foreach ($this->settings as $key => $entry) {
            if (($entry['category'] ?? null) !== $groupe) {
                continue;
            }

            $type = $entry['type'] ?? AppSettingType::String->value;
            $result[$key] = $this->decodeValue($entry['value'] ?? null, (string) $type, null);
        }

        return $result;
    }

    public function refresh(): void
    {
        $this->cache->delete(self::CACHE_KEY);
        $this->settings = [];
        $this->loaded = false;
    }

    public function isMaintenanceMode(): bool
    {
        $value = $this->get('maintenance_mode', false);

        if (is_bool($value)) {
            return $value;
        }

        return (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function setMaintenanceMode(bool $enabled): void
    {
        $this->set('maintenance_mode', $enabled ? 'true' : 'false');
    }

    private function loadSettings(): void
    {
        if ($this->loaded) {
            return;
        }

        $cached = $this->cache->get(self::CACHE_KEY);

        if (is_array($cached)) {
            $this->settings = $cached;
            $this->loaded = true;
            return;
        }

        $this->settings = [];

        try {
            $items = $this->repository->findAll();
        } catch (\Throwable) {
            $this->loaded = true;
            return;
        }

        foreach ($items as $setting) {
            if (!$setting instanceof AppSetting) {
                continue;
            }

            $this->settings[$setting->getSettingKey()] = [
                'value' => $setting->getSettingValue(),
                'type' => $setting->getSettingType()->value,
                'category' => $setting->getCategory(),
                'sensitive' => $setting->isSensitive(),
            ];
        }

        $this->cache->set(self::CACHE_KEY, $this->settings, 3600);
        $this->loaded = true;
    }

    private function inferType(mixed $value): AppSettingType
    {
        return match (true) {
            is_bool($value) => AppSettingType::Boolean,
            is_int($value), is_float($value) => AppSettingType::Number,
            is_array($value) => AppSettingType::Json,
            default => AppSettingType::String,
        };
    }

    private function encodeValue(mixed $value, AppSettingType $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            AppSettingType::Encrypted => $this->encryption->encrypt(is_string($value)
                ? $value
                : json_encode($value, JSON_THROW_ON_ERROR)),
            AppSettingType::Json => json_encode($value, JSON_THROW_ON_ERROR),
            AppSettingType::Boolean => $value ? 'true' : 'false',
            AppSettingType::Number => (string) $value,
            default => (string) $value,
        };
    }

    private function decodeValue(?string $value, string $type, mixed $default): mixed
    {
        if ($value === null) {
            return $default;
        }

        try {
            return match ($type) {
                AppSettingType::Encrypted->value => $this->encryption->decrypt($value) ?? $default,
                AppSettingType::Json->value => json_decode($value, true, 512, JSON_THROW_ON_ERROR),
                AppSettingType::Boolean->value => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default,
                AppSettingType::Number->value => is_numeric($value)
                    ? (str_contains((string) $value, '.') ? (float) $value : (int) $value)
                    : $default,
                default => $value,
            };
        } catch (\Throwable) {
            return $default;
        }
    }
}
