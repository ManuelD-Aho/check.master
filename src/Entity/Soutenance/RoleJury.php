<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'role_jury')]
class RoleJury
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_role_jury', type: 'integer')]
    private ?int $idRoleJury = null;

    #[ORM\Column(name: 'code_role', type: 'string', length: 50, unique: true)]
    private string $codeRole;

    #[ORM\Column(name: 'libelle_role', type: 'string', length: 100)]
    private string $libelleRole;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'ordre_affichage', type: 'integer')]
    private int $ordreAffichage;

    #[ORM\Column(name: 'est_obligatoire', type: 'boolean')]
    private bool $estObligatoire = true;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\OneToMany(mappedBy: 'roleJury', targetEntity: CompositionJury::class)]
    private Collection $compositions;

    public function __construct()
    {
        $this->compositions = new ArrayCollection();
    }

    public function getIdRoleJury(): ?int
    {
        return $this->idRoleJury;
    }

    public function getCodeRole(): string
    {
        return $this->codeRole;
    }

    public function setCodeRole(string $codeRole): self
    {
        $this->codeRole = $codeRole;

        return $this;
    }

    public function getLibelleRole(): string
    {
        return $this->libelleRole;
    }

    public function setLibelleRole(string $libelleRole): self
    {
        $this->libelleRole = $libelleRole;

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

    public function isEstObligatoire(): bool
    {
        return $this->estObligatoire;
    }

    public function setEstObligatoire(bool $estObligatoire): self
    {
        $this->estObligatoire = $estObligatoire;

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

    public function getCompositions(): Collection
    {
        return $this->compositions;
    }

    public function addComposition(CompositionJury $compositionJury): self
    {
        if (!$this->compositions->contains($compositionJury)) {
            $this->compositions->add($compositionJury);
            $compositionJury->setRoleJury($this);
        }

        return $this;
    }

    public function removeComposition(CompositionJury $compositionJury): self
    {
        if ($this->compositions->removeElement($compositionJury)) {
            if ($compositionJury->getRoleJury() === $this) {
                $compositionJury->setRoleJury(null);
            }
        }

        return $this;
    }
}
