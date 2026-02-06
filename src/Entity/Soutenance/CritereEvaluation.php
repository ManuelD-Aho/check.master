<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'critere_evaluation')]
class CritereEvaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_critere', type: 'integer')]
    private ?int $idCritere = null;

    #[ORM\Column(name: 'code_critere', type: 'string', length: 50, unique: true)]
    private string $codeCritere;

    #[ORM\Column(name: 'libelle_critere', type: 'string', length: 100)]
    private string $libelleCritere;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'ordre_affichage', type: 'integer')]
    private int $ordreAffichage;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\OneToMany(mappedBy: 'critere', targetEntity: BaremeCritere::class)]
    private Collection $baremes;

    #[ORM\OneToMany(mappedBy: 'critere', targetEntity: NoteSoutenance::class)]
    private Collection $notesSoutenance;

    public function __construct()
    {
        $this->baremes = new ArrayCollection();
        $this->notesSoutenance = new ArrayCollection();
    }

    public function getIdCritere(): ?int
    {
        return $this->idCritere;
    }

    public function getCodeCritere(): string
    {
        return $this->codeCritere;
    }

    public function setCodeCritere(string $codeCritere): self
    {
        $this->codeCritere = $codeCritere;

        return $this;
    }

    public function getLibelleCritere(): string
    {
        return $this->libelleCritere;
    }

    public function setLibelleCritere(string $libelleCritere): self
    {
        $this->libelleCritere = $libelleCritere;

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

    public function getOrdreAffichage(): int
    {
        return $this->ordreAffichage;
    }

    public function setOrdreAffichage(int $ordreAffichage): self
    {
        $this->ordreAffichage = $ordreAffichage;

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

    public function getBaremes(): Collection
    {
        return $this->baremes;
    }

    public function addBareme(BaremeCritere $baremeCritere): self
    {
        if (!$this->baremes->contains($baremeCritere)) {
            $this->baremes->add($baremeCritere);
            $baremeCritere->setCritere($this);
        }

        return $this;
    }

    public function removeBareme(BaremeCritere $baremeCritere): self
    {
        if ($this->baremes->removeElement($baremeCritere)) {
            if ($baremeCritere->getCritere() === $this) {
                $baremeCritere->setCritere(null);
            }
        }

        return $this;
    }

    public function getNotesSoutenance(): Collection
    {
        return $this->notesSoutenance;
    }

    public function addNoteSoutenance(NoteSoutenance $noteSoutenance): self
    {
        if (!$this->notesSoutenance->contains($noteSoutenance)) {
            $this->notesSoutenance->add($noteSoutenance);
            $noteSoutenance->setCritere($this);
        }

        return $this;
    }

    public function removeNoteSoutenance(NoteSoutenance $noteSoutenance): self
    {
        if ($this->notesSoutenance->removeElement($noteSoutenance)) {
            if ($noteSoutenance->getCritere() === $this) {
                $noteSoutenance->setCritere(null);
            }
        }

        return $this;
    }
}
