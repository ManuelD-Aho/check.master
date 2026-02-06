<?php
declare(strict_types=1);

namespace App\Entity\System;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categorie_fonctionnalite', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_categorie_fonctionnalite_code', columns: ['code_categorie'])])]
class CategorieFonctionnalite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_categorie', type: 'integer')]
    private ?int $idCategorie = null;

    #[ORM\Column(name: 'code_categorie', type: 'string', length: 50)]
    private string $codeCategorie;

    #[ORM\Column(name: 'libelle_categorie', type: 'string', length: 100)]
    private string $libelleCategorie;

    #[ORM\Column(name: 'description_categorie', type: 'text', nullable: true)]
    private ?string $descriptionCategorie = null;

    #[ORM\Column(name: 'icone_categorie', type: 'string', length: 50, nullable: true)]
    private ?string $iconeCategorie = null;

    #[ORM\Column(name: 'ordre_affichage', type: 'integer')]
    private int $ordreAffichage = 0;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Fonctionnalite::class)]
    private Collection $fonctionnalites;

    public function __construct()
    {
        $this->fonctionnalites = new ArrayCollection();
    }

    public function getIdCategorie(): ?int
    {
        return $this->idCategorie;
    }

    public function getCodeCategorie(): string
    {
        return $this->codeCategorie;
    }

    public function setCodeCategorie(string $codeCategorie): self
    {
        $this->codeCategorie = $codeCategorie;

        return $this;
    }

    public function getLibelleCategorie(): string
    {
        return $this->libelleCategorie;
    }

    public function setLibelleCategorie(string $libelleCategorie): self
    {
        $this->libelleCategorie = $libelleCategorie;

        return $this;
    }

    public function getDescriptionCategorie(): ?string
    {
        return $this->descriptionCategorie;
    }

    public function setDescriptionCategorie(?string $descriptionCategorie): self
    {
        $this->descriptionCategorie = $descriptionCategorie;

        return $this;
    }

    public function getIconeCategorie(): ?string
    {
        return $this->iconeCategorie;
    }

    public function setIconeCategorie(?string $iconeCategorie): self
    {
        $this->iconeCategorie = $iconeCategorie;

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

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getFonctionnalites(): Collection
    {
        return $this->fonctionnalites;
    }

    public function addFonctionnalite(Fonctionnalite $fonctionnalite): self
    {
        if (!$this->fonctionnalites->contains($fonctionnalite)) {
            $this->fonctionnalites->add($fonctionnalite);
            $fonctionnalite->setCategorie($this);
        }

        return $this;
    }

    public function removeFonctionnalite(Fonctionnalite $fonctionnalite): self
    {
        $this->fonctionnalites->removeElement($fonctionnalite);

        return $this;
    }
}
