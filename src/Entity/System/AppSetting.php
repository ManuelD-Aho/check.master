<?php
declare(strict_types=1);

namespace App\Entity\System;

use App\Entity\User\Utilisateur;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'app_setting')]
class AppSetting
{
    #[ORM\Id]
    #[ORM\Column(name: 'setting_key', type: 'string', length: 100)]
    private string $settingKey;

    #[ORM\Column(name: 'setting_value', type: 'text', nullable: true)]
    private ?string $settingValue = null;

    #[ORM\Column(name: 'setting_type', enumType: AppSettingType::class)]
    private AppSettingType $settingType;

    #[ORM\Column(name: 'category', type: 'string', length: 50, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(name: 'description', type: 'string', length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'is_sensitive', type: 'boolean')]
    private bool $isSensitive = false;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'appSettingsUpdated')]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id_utilisateur', nullable: true)]
    private ?Utilisateur $updatedBy = null;

    public function getSettingKey(): string
    {
        return $this->settingKey;
    }

    public function setSettingKey(string $settingKey): self
    {
        $this->settingKey = $settingKey;

        return $this;
    }

    public function getSettingValue(): ?string
    {
        return $this->settingValue;
    }

    public function setSettingValue(?string $settingValue): self
    {
        $this->settingValue = $settingValue;

        return $this;
    }

    public function getSettingType(): AppSettingType
    {
        return $this->settingType;
    }

    public function setSettingType(AppSettingType $settingType): self
    {
        $this->settingType = $settingType;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isSensitive(): bool
    {
        return $this->isSensitive;
    }

    public function setIsSensitive(bool $isSensitive): self
    {
        $this->isSensitive = $isSensitive;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedBy(): ?Utilisateur
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?Utilisateur $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
