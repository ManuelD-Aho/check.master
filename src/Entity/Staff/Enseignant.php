<?php

declare(strict_types=1);

namespace App\Entity\Staff;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'enseignant')]
class Enseignant
{
    #[ORM\Id]
    #[ORM\Column(name: 'matricule_enseignant', type: 'string', length: 20)]
    private string $matriculeEnseignant;

    #[ORM\Column(name: 'nom_enseignant', type: 'string', length: 100)]
    private string $nomEnseignant;

    #[ORM\Column(name: 'prenom_enseignant', type: 'string', length: 100)]
    private string $prenomEnseignant;

    #[ORM\Column(name: 'email_enseignant', type: 'string', length: 255, unique: true)]
    private string $emailEnseignant;

    #[ORM\Column(name: 'telephone_enseignant', type: 'string', length: 20, nullable: true)]
    private ?string $telephoneEnseignant = null;

    #[ORM\ManyToOne(targetEntity: Specialite::class, inversedBy: 'enseignants')]
    #[ORM\JoinColumn(name: 'id_specialite', referencedColumnName: 'id_specialite', nullable: true)]
    private ?Specialite $specialite = null;

    #[ORM\Column(name: 'type_enseignant', type: 'string', enumType: TypeEnseignant::class, length: 20, options: ['default' => 'permanent'])]
    private TypeEnseignant $typeEnseignant = TypeEnseignant::Permanent;

    #[ORM\Column(name: 'actif', type: 'boolean', options: ['default' => true])]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeInterface $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeInterface $dateModification;

    public function __construct()
    {
        $now = new DateTimeImmutable();
        $this->dateCreation = $now;
        $this->dateModification = $now;
    }

    public function getMatriculeEnseignant(): string
    {
        return $this->matriculeEnseignant;
    }

    public function setMatriculeEnseignant(string $matriculeEnseignant): self
    {
        $this->matriculeEnseignant = $matriculeEnseignant;

        return $this;
    }

    public function getNomEnseignant(): string
    {
        return $this->nomEnseignant;
    }

    public function setNomEnseignant(string $nomEnseignant): self
    {
        $this->nomEnseignant = $nomEnseignant;

        return $this;
    }

    public function getPrenomEnseignant(): string
    {
        return $this->prenomEnseignant;
    }

    public function setPrenomEnseignant(string $prenomEnseignant): self
    {
        $this->prenomEnseignant = $prenomEnseignant;

        return $this;
    }

    public function getEmailEnseignant(): string
    {
        return $this->emailEnseignant;
    }

    public function setEmailEnseignant(string $emailEnseignant): self
    {
        $this->emailEnseignant = $emailEnseignant;

        return $this;
    }

    public function getTelephoneEnseignant(): ?string
    {
        return $this->telephoneEnseignant;
    }

    public function setTelephoneEnseignant(?string $telephoneEnseignant): self
    {
        $this->telephoneEnseignant = $telephoneEnseignant;

        return $this;
    }

    public function getSpecialite(): ?Specialite
    {
        return $this->specialite;
    }

    public function setSpecialite(?Specialite $specialite): self
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getTypeEnseignant(): TypeEnseignant
    {
        return $this->typeEnseignant;
    }

    public function setTypeEnseignant(TypeEnseignant $typeEnseignant): self
    {
        $this->typeEnseignant = $typeEnseignant;

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

    public function getDateCreation(): DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }
}
