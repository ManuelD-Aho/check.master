<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Staff\Enseignant;
use App\Entity\Student\Etudiant;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'aptitude_soutenance')]
#[ORM\UniqueConstraint(name: 'uk_aptitude_etudiant_annee', columns: ['matricule_etudiant', 'id_annee_academique'])]
class AptitudeSoutenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_aptitude', type: 'integer')]
    private ?int $idAptitude = null;

    #[ORM\ManyToOne(targetEntity: Etudiant::class)]
    #[ORM\JoinColumn(name: 'matricule_etudiant', referencedColumnName: 'matricule_etudiant', nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(targetEntity: AnneeAcademique::class)]
    #[ORM\JoinColumn(name: 'id_annee_academique', referencedColumnName: 'id_annee_academique', nullable: false)]
    private ?AnneeAcademique $anneeAcademique = null;

    #[ORM\ManyToOne(targetEntity: Enseignant::class)]
    #[ORM\JoinColumn(name: 'matricule_encadreur', referencedColumnName: 'matricule_enseignant', nullable: false)]
    private ?Enseignant $encadreur = null;

    #[ORM\Column(name: 'est_apte', type: 'boolean', nullable: true)]
    private ?bool $estApte = null;

    #[ORM\Column(name: 'commentaire', type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(name: 'date_validation', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $dateValidation = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    public function getIdAptitude(): ?int
    {
        return $this->idAptitude;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getAnneeAcademique(): ?AnneeAcademique
    {
        return $this->anneeAcademique;
    }

    public function setAnneeAcademique(?AnneeAcademique $anneeAcademique): self
    {
        $this->anneeAcademique = $anneeAcademique;

        return $this;
    }

    public function getEncadreur(): ?Enseignant
    {
        return $this->encadreur;
    }

    public function setEncadreur(?Enseignant $encadreur): self
    {
        $this->encadreur = $encadreur;

        return $this;
    }

    public function getEstApte(): ?bool
    {
        return $this->estApte;
    }

    public function setEstApte(?bool $estApte): self
    {
        $this->estApte = $estApte;

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

    public function getDateValidation(): ?DateTimeInterface
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?DateTimeInterface $dateValidation): self
    {
        $this->dateValidation = $dateValidation;

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
}
