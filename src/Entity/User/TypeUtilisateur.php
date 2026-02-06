<?php
declare(strict_types=1);

namespace App\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'type_utilisateur', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_type_utilisateur_code', columns: ['code_type_utilisateur'])])]
class TypeUtilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_type_utilisateur', type: 'integer')]
    private ?int $idTypeUtilisateur = null;

    #[ORM\Column(name: 'code_type_utilisateur', type: 'string', length: 20)]
    private string $codeTypeUtilisateur;

    #[ORM\Column(name: 'libelle_type_utilisateur', type: 'string', length: 100)]
    private string $libelleTypeUtilisateur;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\OneToMany(mappedBy: 'typeUtilisateur', targetEntity: GroupeUtilisateur::class)]
    private Collection $groupesUtilisateurs;

    #[ORM\OneToMany(mappedBy: 'typeUtilisateur', targetEntity: Utilisateur::class)]
    private Collection $utilisateurs;

    public function __construct()
    {
        $this->groupesUtilisateurs = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
    }

    public function getIdTypeUtilisateur(): ?int
    {
        return $this->idTypeUtilisateur;
    }

    public function getCodeTypeUtilisateur(): string
    {
        return $this->codeTypeUtilisateur;
    }

    public function setCodeTypeUtilisateur(string $codeTypeUtilisateur): self
    {
        $this->codeTypeUtilisateur = $codeTypeUtilisateur;

        return $this;
    }

    public function getLibelleTypeUtilisateur(): string
    {
        return $this->libelleTypeUtilisateur;
    }

    public function setLibelleTypeUtilisateur(string $libelleTypeUtilisateur): self
    {
        $this->libelleTypeUtilisateur = $libelleTypeUtilisateur;

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

    public function getGroupesUtilisateurs(): Collection
    {
        return $this->groupesUtilisateurs;
    }

    public function addGroupeUtilisateur(GroupeUtilisateur $groupeUtilisateur): self
    {
        if (!$this->groupesUtilisateurs->contains($groupeUtilisateur)) {
            $this->groupesUtilisateurs->add($groupeUtilisateur);
            $groupeUtilisateur->setTypeUtilisateur($this);
        }

        return $this;
    }

    public function removeGroupeUtilisateur(GroupeUtilisateur $groupeUtilisateur): self
    {
        $this->groupesUtilisateurs->removeElement($groupeUtilisateur);

        return $this;
    }

    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setTypeUtilisateur($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        $this->utilisateurs->removeElement($utilisateur);

        return $this;
    }
}
