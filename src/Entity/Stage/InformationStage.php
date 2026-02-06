<?php

declare(strict_types=1);

namespace App\Entity\Stage;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'information_stage')]
class InformationStage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_info_stage', type: 'integer')]
    private ?int $idInfoStage = null;

    #[ORM\OneToOne(targetEntity: Candidature::class)]
    #[ORM\JoinColumn(name: 'id_candidature', referencedColumnName: 'id_candidature', nullable: false, unique: true)]
    private Candidature $candidature;

    #[ORM\ManyToOne(targetEntity: Entreprise::class)]
    #[ORM\JoinColumn(name: 'id_entreprise', referencedColumnName: 'id_entreprise', nullable: false)]
    private Entreprise $entreprise;

    #[ORM\Column(name: 'sujet_stage', type: 'string', length: 255)]
    private string $sujetStage;

    #[ORM\Column(name: 'description_stage', type: 'text')]
    private string $descriptionStage;

    #[ORM\Column(name: 'objectifs_stage', type: 'text', nullable: true)]
    private ?string $objectifsStage = null;

    #[ORM\Column(name: 'technologies_utilisees', type: 'string', length: 500, nullable: true)]
    private ?string $technologiesUtilisees = null;

    #[ORM\Column(name: 'date_debut_stage', type: 'date_immutable')]
    private \DateTimeImmutable $dateDebutStage;

    #[ORM\Column(name: 'date_fin_stage', type: 'date_immutable')]
    private \DateTimeImmutable $dateFinStage;

    #[ORM\Column(name: 'duree_stage_jours', type: 'integer', insertable: false, updatable: false, options: ['generated' => 'STORED'])]
    private ?int $dureeStageJours = null;

    #[ORM\Column(name: 'nom_encadrant', type: 'string', length: 100)]
    private string $nomEncadrant;

    #[ORM\Column(name: 'prenom_encadrant', type: 'string', length: 100)]
    private string $prenomEncadrant;

    #[ORM\Column(name: 'fonction_encadrant', type: 'string', length: 100, nullable: true)]
    private ?string $fonctionEncadrant = null;

    #[ORM\Column(name: 'email_encadrant', type: 'string', length: 255)]
    private string $emailEncadrant;

    #[ORM\Column(name: 'telephone_encadrant', type: 'string', length: 20)]
    private string $telephoneEncadrant;

    #[ORM\Column(name: 'adresse_stage', type: 'text', nullable: true)]
    private ?string $adresseStage = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\Column(name: 'date_modification', type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $dateModification = null;

    public function getIdInfoStage(): ?int
    {
        return $this->idInfoStage;
    }

    public function setIdInfoStage(int $idInfoStage): self
    {
        $this->idInfoStage = $idInfoStage;

        return $this;
    }

    public function getCandidature(): Candidature
    {
        return $this->candidature;
    }

    public function setCandidature(Candidature $candidature): self
    {
        $this->candidature = $candidature;

        return $this;
    }

    public function getEntreprise(): Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getSujetStage(): string
    {
        return $this->sujetStage;
    }

    public function setSujetStage(string $sujetStage): self
    {
        $this->sujetStage = $sujetStage;

        return $this;
    }

    public function getDescriptionStage(): string
    {
        return $this->descriptionStage;
    }

    public function setDescriptionStage(string $descriptionStage): self
    {
        $this->descriptionStage = $descriptionStage;

        return $this;
    }

    public function getObjectifsStage(): ?string
    {
        return $this->objectifsStage;
    }

    public function setObjectifsStage(?string $objectifsStage): self
    {
        $this->objectifsStage = $objectifsStage;

        return $this;
    }

    public function getTechnologiesUtilisees(): ?string
    {
        return $this->technologiesUtilisees;
    }

    public function setTechnologiesUtilisees(?string $technologiesUtilisees): self
    {
        $this->technologiesUtilisees = $technologiesUtilisees;

        return $this;
    }

    public function getDateDebutStage(): \DateTimeImmutable
    {
        return $this->dateDebutStage;
    }

    public function setDateDebutStage(\DateTimeImmutable $dateDebutStage): self
    {
        $this->dateDebutStage = $dateDebutStage;

        return $this;
    }

    public function getDateFinStage(): \DateTimeImmutable
    {
        return $this->dateFinStage;
    }

    public function setDateFinStage(\DateTimeImmutable $dateFinStage): self
    {
        $this->dateFinStage = $dateFinStage;

        return $this;
    }

    public function getDureeStageJours(): ?int
    {
        return $this->dureeStageJours;
    }

    public function setDureeStageJours(?int $dureeStageJours): self
    {
        $this->dureeStageJours = $dureeStageJours;

        return $this;
    }

    public function getNomEncadrant(): string
    {
        return $this->nomEncadrant;
    }

    public function setNomEncadrant(string $nomEncadrant): self
    {
        $this->nomEncadrant = $nomEncadrant;

        return $this;
    }

    public function getPrenomEncadrant(): string
    {
        return $this->prenomEncadrant;
    }

    public function setPrenomEncadrant(string $prenomEncadrant): self
    {
        $this->prenomEncadrant = $prenomEncadrant;

        return $this;
    }

    public function getFonctionEncadrant(): ?string
    {
        return $this->fonctionEncadrant;
    }

    public function setFonctionEncadrant(?string $fonctionEncadrant): self
    {
        $this->fonctionEncadrant = $fonctionEncadrant;

        return $this;
    }

    public function getEmailEncadrant(): string
    {
        return $this->emailEncadrant;
    }

    public function setEmailEncadrant(string $emailEncadrant): self
    {
        $this->emailEncadrant = $emailEncadrant;

        return $this;
    }

    public function getTelephoneEncadrant(): string
    {
        return $this->telephoneEncadrant;
    }

    public function setTelephoneEncadrant(string $telephoneEncadrant): self
    {
        $this->telephoneEncadrant = $telephoneEncadrant;

        return $this;
    }

    public function getAdresseStage(): ?string
    {
        return $this->adresseStage;
    }

    public function setAdresseStage(?string $adresseStage): self
    {
        $this->adresseStage = $adresseStage;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeImmutable $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): ?\DateTimeImmutable
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeImmutable $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }
}
