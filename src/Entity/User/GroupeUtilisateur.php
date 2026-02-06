<?php
declare(strict_types=1);

namespace App\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'groupe_utilisateur', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_groupe_utilisateur_code', columns: ['code_groupe'])])]
class GroupeUtilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_groupe_utilisateur', type: 'integer')]
    private ?int $idGroupeUtilisateur = null;

    #[ORM\Column(name: 'code_groupe', type: 'string', length: 50)]
    private string $codeGroupe;

    #[ORM\Column(name: 'libelle_groupe', type: 'string', length: 100)]
    private string $libelleGroupe;

    #[ORM\ManyToOne(targetEntity: TypeUtilisateur::class, inversedBy: 'groupesUtilisateurs')]
    #[ORM\JoinColumn(name: 'id_type_utilisateur', referencedColumnName: 'id_type_utilisateur')]
    private TypeUtilisateur $typeUtilisateur;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'est_modifiable', type: 'boolean')]
    private bool $estModifiable = true;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateModification;

    #[ORM\OneToMany(mappedBy: 'groupeUtilisateur', targetEntity: Utilisateur::class)]
    private Collection $utilisateurs;

    #[ORM\OneToMany(mappedBy: 'groupeUtilisateur', targetEntity: Permission::class)]
    private Collection $permissions;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    public function getIdGroupeUtilisateur(): ?int
    {
        return $this->idGroupeUtilisateur;
    }

    public function getCodeGroupe(): string
    {
        return $this->codeGroupe;
    }

    public function setCodeGroupe(string $codeGroupe): self
    {
        $this->codeGroupe = $codeGroupe;

        return $this;
    }

    public function getLibelleGroupe(): string
    {
        return $this->libelleGroupe;
    }

    public function setLibelleGroupe(string $libelleGroupe): self
    {
        $this->libelleGroupe = $libelleGroupe;

        return $this;
    }

    public function getTypeUtilisateur(): TypeUtilisateur
    {
        return $this->typeUtilisateur;
    }

    public function setTypeUtilisateur(TypeUtilisateur $typeUtilisateur): self
    {
        $this->typeUtilisateur = $typeUtilisateur;

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

    public function isEstModifiable(): bool
    {
        return $this->estModifiable;
    }

    public function setEstModifiable(bool $estModifiable): self
    {
        $this->estModifiable = $estModifiable;

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

    public function getDateModification(): \DateTimeImmutable
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTimeImmutable $dateModification): self
    {
        $this->dateModification = $dateModification;

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
            $utilisateur->setGroupeUtilisateur($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        $this->utilisateurs->removeElement($utilisateur);

        return $this;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
            $permission->setGroupeUtilisateur($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        $this->permissions->removeElement($permission);

        return $this;
    }
}
