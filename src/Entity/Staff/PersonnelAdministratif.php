<?php

declare(strict_types=1);

namespace App\Entity\Staff;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'personnel_administratif')]
class PersonnelAdministratif
{
    #[ORM\Id]
    #[ORM\Column(name: 'matricule_personnel', type: 'string', length: 20)]
    private string $matriculePersonnel;

    #[ORM\Column(name: 'nom_personnel', type: 'string', length: 100)]
    private string $nomPersonnel;

    #[ORM\Column(name: 'prenom_personnel', type: 'string', length: 100)]
    private string $prenomPersonnel;

    #[ORM\Column(name: 'email_personnel', type: 'string', length: 255, unique: true)]
    private string $emailPersonnel;

    #[ORM\Column(name: 'telephone_personnel', type: 'string', length: 20, nullable: true)]
    private ?string $telephonePersonnel = null;

    #[ORM\Column(name: 'poste', type: 'string', length: 100, nullable: true)]
    private ?string $poste = null;

    #[ORM\Column(name: 'date_embauche', type: 'date', nullable: true)]
    private ?DateTimeInterface $dateEmbauche = null;

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

    public function getMatriculePersonnel(): string
    {
        return $this->matriculePersonnel;
    }

    public function setMatriculePersonnel(string $matriculePersonnel): self
    {
        $this->matriculePersonnel = $matriculePersonnel;

        return $this;
    }

    public function getNomPersonnel(): string
    {
        return $this->nomPersonnel;
    }

    public function setNomPersonnel(string $nomPersonnel): self
    {
        $this->nomPersonnel = $nomPersonnel;

        return $this;
    }

    public function getPrenomPersonnel(): string
    {
        return $this->prenomPersonnel;
    }

    public function setPrenomPersonnel(string $prenomPersonnel): self
    {
        $this->prenomPersonnel = $prenomPersonnel;

        return $this;
    }

    public function getEmailPersonnel(): string
    {
        return $this->emailPersonnel;
    }

    public function setEmailPersonnel(string $emailPersonnel): self
    {
        $this->emailPersonnel = $emailPersonnel;

        return $this;
    }

    public function getTelephonePersonnel(): ?string
    {
        return $this->telephonePersonnel;
    }

    public function setTelephonePersonnel(?string $telephonePersonnel): self
    {
        $this->telephonePersonnel = $telephonePersonnel;

        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(?string $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    public function getDateEmbauche(): ?DateTimeInterface
    {
        return $this->dateEmbauche;
    }

    public function setDateEmbauche(?DateTimeInterface $dateEmbauche): self
    {
        $this->dateEmbauche = $dateEmbauche;

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
