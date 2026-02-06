<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

use App\Entity\Staff\Enseignant;
use App\Entity\User\Utilisateur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'composition_jury')]
#[ORM\UniqueConstraint(name: 'uk_composition_jury_role', columns: ['id_jury', 'id_role_jury'])]
class CompositionJury
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_composition', type: 'integer')]
    private ?int $idComposition = null;

    #[ORM\ManyToOne(targetEntity: Jury::class, inversedBy: 'compositions')]
    #[ORM\JoinColumn(name: 'id_jury', referencedColumnName: 'id_jury', nullable: false)]
    private ?Jury $jury = null;

    #[ORM\ManyToOne(targetEntity: Enseignant::class)]
    #[ORM\JoinColumn(name: 'matricule_enseignant', referencedColumnName: 'matricule_enseignant', nullable: true)]
    private ?Enseignant $enseignant = null;

    #[ORM\Column(name: 'nom_externe', type: 'string', length: 100, nullable: true)]
    private ?string $nomExterne = null;

    #[ORM\Column(name: 'prenom_externe', type: 'string', length: 100, nullable: true)]
    private ?string $prenomExterne = null;

    #[ORM\Column(name: 'fonction_externe', type: 'string', length: 100, nullable: true)]
    private ?string $fonctionExterne = null;

    #[ORM\Column(name: 'email_externe', type: 'string', length: 255, nullable: true)]
    private ?string $emailExterne = null;

    #[ORM\Column(name: 'telephone_externe', type: 'string', length: 20, nullable: true)]
    private ?string $telephoneExterne = null;

    #[ORM\Column(name: 'entreprise_externe', type: 'string', length: 200, nullable: true)]
    private ?string $entrepriseExterne = null;

    #[ORM\ManyToOne(targetEntity: RoleJury::class, inversedBy: 'compositions')]
    #[ORM\JoinColumn(name: 'id_role_jury', referencedColumnName: 'id_role_jury', nullable: false)]
    private ?RoleJury $roleJury = null;

    #[ORM\Column(name: 'est_present', type: 'boolean', nullable: true)]
    private ?bool $estPresent = null;

    #[ORM\Column(name: 'commentaire', type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(name: 'date_affectation', type: 'datetime')]
    private DateTimeInterface $dateAffectation;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_affecteur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $affecteur = null;

    public function getIdComposition(): ?int
    {
        return $this->idComposition;
    }

    public function getJury(): ?Jury
    {
        return $this->jury;
    }

    public function setJury(?Jury $jury): self
    {
        $this->jury = $jury;

        return $this;
    }

    public function getEnseignant(): ?Enseignant
    {
        return $this->enseignant;
    }

    public function setEnseignant(?Enseignant $enseignant): self
    {
        $this->enseignant = $enseignant;

        return $this;
    }

    public function getNomExterne(): ?string
    {
        return $this->nomExterne;
    }

    public function setNomExterne(?string $nomExterne): self
    {
        $this->nomExterne = $nomExterne;

        return $this;
    }

    public function getPrenomExterne(): ?string
    {
        return $this->prenomExterne;
    }

    public function setPrenomExterne(?string $prenomExterne): self
    {
        $this->prenomExterne = $prenomExterne;

        return $this;
    }

    public function getFonctionExterne(): ?string
    {
        return $this->fonctionExterne;
    }

    public function setFonctionExterne(?string $fonctionExterne): self
    {
        $this->fonctionExterne = $fonctionExterne;

        return $this;
    }

    public function getEmailExterne(): ?string
    {
        return $this->emailExterne;
    }

    public function setEmailExterne(?string $emailExterne): self
    {
        $this->emailExterne = $emailExterne;

        return $this;
    }

    public function getTelephoneExterne(): ?string
    {
        return $this->telephoneExterne;
    }

    public function setTelephoneExterne(?string $telephoneExterne): self
    {
        $this->telephoneExterne = $telephoneExterne;

        return $this;
    }

    public function getEntrepriseExterne(): ?string
    {
        return $this->entrepriseExterne;
    }

    public function setEntrepriseExterne(?string $entrepriseExterne): self
    {
        $this->entrepriseExterne = $entrepriseExterne;

        return $this;
    }

    public function getRoleJury(): ?RoleJury
    {
        return $this->roleJury;
    }

    public function setRoleJury(?RoleJury $roleJury): self
    {
        $this->roleJury = $roleJury;

        return $this;
    }

    public function getEstPresent(): ?bool
    {
        return $this->estPresent;
    }

    public function setEstPresent(?bool $estPresent): self
    {
        $this->estPresent = $estPresent;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getDateAffectation(): DateTimeInterface
    {
        return $this->dateAffectation;
    }

    public function setDateAffectation(DateTimeInterface $dateAffectation): self
    {
        $this->dateAffectation = $dateAffectation;

        return $this;
    }

    public function getAffecteur(): ?Utilisateur
    {
        return $this->affecteur;
    }

    public function setAffecteur(?Utilisateur $affecteur): self
    {
        $this->affecteur = $affecteur;

        return $this;
    }
}
