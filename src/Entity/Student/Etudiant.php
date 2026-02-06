<?php

declare(strict_types=1);

namespace App\Entity\Student;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'etudiant', indexes: [
    new ORM\Index(name: 'idx_etudiant_promotion', columns: ['promotion']),
    new ORM\Index(name: 'idx_etudiant_filiere', columns: ['id_filiere']),
], uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uk_etudiant_email', columns: ['email_etudiant']),
])]
class Etudiant
{
    #[ORM\Id]
    #[ORM\Column(name: 'matricule_etudiant', type: 'string', length: 20)]
    private string $matriculeEtudiant;

    #[ORM\Column(name: 'nom_etudiant', type: 'string', length: 100)]
    private string $nomEtudiant;

    #[ORM\Column(name: 'prenom_etudiant', type: 'string', length: 100)]
    private string $prenomEtudiant;

    #[ORM\Column(name: 'email_etudiant', type: 'string', length: 255, unique: true)]
    private string $emailEtudiant;

    #[ORM\Column(name: 'telephone_etudiant', type: 'string', length: 20, nullable: true)]
    private ?string $telephoneEtudiant = null;

    #[ORM\Column(name: 'date_naissance', type: 'date')]
    private \DateTimeInterface $dateNaissance;

    #[ORM\Column(name: 'lieu_naissance', type: 'string', length: 100)]
    private string $lieuNaissance;

    #[ORM\Column(name: 'genre', enumType: Genre::class)]
    private Genre $genre;

    #[ORM\Column(name: 'nationalite', type: 'string', length: 50, nullable: true, options: ['default' => 'Ivoirienne'])]
    private ?string $nationalite = null;

    #[ORM\Column(name: 'adresse', type: 'text', nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: 'promotion', type: 'string', length: 20)]
    private string $promotion;

    #[ORM\Column(name: 'photo_profil', type: 'string', length: 255, nullable: true)]
    private ?string $photoProfil = null;

    #[ORM\ManyToOne(targetEntity: 'App\\Entity\\Academic\\Filiere')]
    #[ORM\JoinColumn(name: 'id_filiere', referencedColumnName: 'id_filiere', nullable: false)]
    private $filiere;

    #[ORM\Column(name: 'actif', type: 'boolean', options: ['default' => true])]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeInterface $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeInterface $dateModification;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: Inscription::class)]
    private Collection $inscriptions;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    public function getMatriculeEtudiant(): string
    {
        return $this->matriculeEtudiant;
    }

    public function setMatriculeEtudiant(string $matriculeEtudiant): self
    {
        $this->matriculeEtudiant = $matriculeEtudiant;

        return $this;
    }

    public function getNomEtudiant(): string
    {
        return $this->nomEtudiant;
    }

    public function setNomEtudiant(string $nomEtudiant): self
    {
        $this->nomEtudiant = $nomEtudiant;

        return $this;
    }

    public function getPrenomEtudiant(): string
    {
        return $this->prenomEtudiant;
    }

    public function setPrenomEtudiant(string $prenomEtudiant): self
    {
        $this->prenomEtudiant = $prenomEtudiant;

        return $this;
    }

    public function getEmailEtudiant(): string
    {
        return $this->emailEtudiant;
    }

    public function setEmailEtudiant(string $emailEtudiant): self
    {
        $this->emailEtudiant = $emailEtudiant;

        return $this;
    }

    public function getTelephoneEtudiant(): ?string
    {
        return $this->telephoneEtudiant;
    }

    public function setTelephoneEtudiant(?string $telephoneEtudiant): self
    {
        $this->telephoneEtudiant = $telephoneEtudiant;

        return $this;
    }

    public function getDateNaissance(): \DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getLieuNaissance(): string
    {
        return $this->lieuNaissance;
    }

    public function setLieuNaissance(string $lieuNaissance): self
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    public function getGenre(): Genre
    {
        return $this->genre;
    }

    public function setGenre(Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(?string $nationalite): self
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getPromotion(): string
    {
        return $this->promotion;
    }

    public function setPromotion(string $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(?string $photoProfil): self
    {
        $this->photoProfil = $photoProfil;

        return $this;
    }

    public function getFiliere()
    {
        return $this->filiere;
    }

    public function setFiliere($filiere): self
    {
        $this->filiere = $filiere;

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

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): \DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setEtudiant($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            if ($inscription->getEtudiant() === $this) {
                $inscription->setEtudiant(null);
            }
        }

        return $this;
    }
}
