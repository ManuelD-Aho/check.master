<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'salle')]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_salle', type: 'integer')]
    private ?int $idSalle = null;

    #[ORM\Column(name: 'code_salle', type: 'string', length: 20, unique: true)]
    private string $codeSalle;

    #[ORM\Column(name: 'libelle_salle', type: 'string', length: 100)]
    private string $libelleSalle;

    #[ORM\Column(name: 'capacite', type: 'integer', nullable: true)]
    private ?int $capacite = null;

    #[ORM\Column(name: 'equipements', type: 'string', length: 255, nullable: true)]
    private ?string $equipements = null;

    #[ORM\Column(name: 'batiment', type: 'string', length: 100, nullable: true)]
    private ?string $batiment = null;

    #[ORM\Column(name: 'etage', type: 'string', length: 20, nullable: true)]
    private ?string $etage = null;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\OneToMany(mappedBy: 'salle', targetEntity: Soutenance::class)]
    private Collection $soutenances;

    public function __construct()
    {
        $this->soutenances = new ArrayCollection();
    }

    public function getIdSalle(): ?int
    {
        return $this->idSalle;
    }

    public function getCodeSalle(): string
    {
        return $this->codeSalle;
    }

    public function setCodeSalle(string $codeSalle): self
    {
        $this->codeSalle = $codeSalle;

        return $this;
    }

    public function getLibelleSalle(): string
    {
        return $this->libelleSalle;
    }

    public function setLibelleSalle(string $libelleSalle): self
    {
        $this->libelleSalle = $libelleSalle;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(?int $capacite): self
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getEquipements(): ?string
    {
        return $this->equipements;
    }

    public function setEquipements(?string $equipements): self
    {
        $this->equipements = $equipements;

        return $this;
    }

    public function getBatiment(): ?string
    {
        return $this->batiment;
    }

    public function setBatiment(?string $batiment): self
    {
        $this->batiment = $batiment;

        return $this;
    }

    public function getEtage(): ?string
    {
        return $this->etage;
    }

    public function setEtage(?string $etage): self
    {
        $this->etage = $etage;

        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getSoutenances(): Collection
    {
        return $this->soutenances;
    }

    public function addSoutenance(Soutenance $soutenance): self
    {
        if (!$this->soutenances->contains($soutenance)) {
            $this->soutenances->add($soutenance);
            $soutenance->setSalle($this);
        }

        return $this;
    }

    public function removeSoutenance(Soutenance $soutenance): self
    {
        if ($this->soutenances->removeElement($soutenance)) {
            if ($soutenance->getSalle() === $this) {
                $soutenance->setSalle(null);
            }
        }

        return $this;
    }
}
