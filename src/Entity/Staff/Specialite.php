<?php

declare(strict_types=1);

namespace App\Entity\Staff;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'specialite')]
class Specialite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_specialite', type: 'integer')]
    private ?int $idSpecialite = null;

    #[ORM\Column(name: 'code_specialite', type: 'string', length: 20, unique: true)]
    private string $codeSpecialite;

    #[ORM\Column(name: 'libelle_specialite', type: 'string', length: 100)]
    private string $libelleSpecialite;

    #[ORM\Column(name: 'actif', type: 'boolean', options: ['default' => true])]
    private bool $actif = true;

    #[ORM\OneToMany(mappedBy: 'specialite', targetEntity: Enseignant::class)]
    private Collection $enseignants;

    public function __construct()
    {
        $this->enseignants = new ArrayCollection();
    }

    public function getIdSpecialite(): ?int
    {
        return $this->idSpecialite;
    }

    public function getCodeSpecialite(): string
    {
        return $this->codeSpecialite;
    }

    public function setCodeSpecialite(string $codeSpecialite): self
    {
        $this->codeSpecialite = $codeSpecialite;

        return $this;
    }

    public function getLibelleSpecialite(): string
    {
        return $this->libelleSpecialite;
    }

    public function setLibelleSpecialite(string $libelleSpecialite): self
    {
        $this->libelleSpecialite = $libelleSpecialite;

        return $this;
    }

    public function getActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getEnseignants(): Collection
    {
        return $this->enseignants;
    }

    public function addEnseignant(Enseignant $enseignant): self
    {
        if (!$this->enseignants->contains($enseignant)) {
            $this->enseignants->add($enseignant);
            $enseignant->setSpecialite($this);
        }

        return $this;
    }

    public function removeEnseignant(Enseignant $enseignant): self
    {
        if ($this->enseignants->removeElement($enseignant)) {
            if ($enseignant->getSpecialite() === $this) {
                $enseignant->setSpecialite(null);
            }
        }

        return $this;
    }
}
