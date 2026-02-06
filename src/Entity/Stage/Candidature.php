<?php

declare(strict_types=1);

namespace App\Entity\Stage;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Student\Etudiant;
use App\Entity\User\Utilisateur;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'candidature', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uk_candidature_etudiant_annee', columns: ['matricule_etudiant', 'id_annee_academique'])])]
class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_candidature', type: 'integer')]
    private ?int $idCandidature = null;

    #[ORM\ManyToOne(targetEntity: Etudiant::class)]
    #[ORM\JoinColumn(name: 'matricule_etudiant', referencedColumnName: 'matricule_etudiant', nullable: false)]
    private Etudiant $etudiant;

    #[ORM\ManyToOne(targetEntity: AnneeAcademique::class)]
    #[ORM\JoinColumn(name: 'id_annee_academique', referencedColumnName: 'id_annee_academique', nullable: false)]
    private AnneeAcademique $anneeAcademique;

    #[ORM\Column(name: 'statut_candidature', type: 'string', enumType: StatutCandidature::class, length: 20, options: ['default' => 'brouillon'])]
    private StatutCandidature $statutCandidature = StatutCandidature::Brouillon;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\Column(name: 'date_soumission', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateSoumission = null;

    #[ORM\Column(name: 'date_traitement', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateTraitement = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_validateur', referencedColumnName: 'id_utilisateur', nullable: true)]
    private ?Utilisateur $validateur = null;

    #[ORM\Column(name: 'commentaire_validation', type: 'text', nullable: true)]
    private ?string $commentaireValidation = null;

    #[ORM\Column(name: 'nombre_soumissions', type: 'integer', options: ['default' => 1])]
    private int $nombreSoumissions = 1;

    #[ORM\Column(name: 'date_modification', type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $dateModification = null;

    public function getIdCandidature(): ?int
    {
        return $this->idCandidature;
    }

    public function setIdCandidature(int $idCandidature): self
    {
        $this->idCandidature = $idCandidature;

        return $this;
    }

    public function getEtudiant(): Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(Etudiant $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getAnneeAcademique(): AnneeAcademique
    {
        return $this->anneeAcademique;
    }

    public function setAnneeAcademique(AnneeAcademique $anneeAcademique): self
    {
        $this->anneeAcademique = $anneeAcademique;

        return $this;
    }

    public function getStatutCandidature(): StatutCandidature
    {
        return $this->statutCandidature;
    }

    public function setStatutCandidature(StatutCandidature $statutCandidature): self
    {
        $this->statutCandidature = $statutCandidature;

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

    public function getDateSoumission(): ?\DateTimeImmutable
    {
        return $this->dateSoumission;
    }

    public function setDateSoumission(?\DateTimeImmutable $dateSoumission): self
    {
        $this->dateSoumission = $dateSoumission;

        return $this;
    }

    public function getDateTraitement(): ?\DateTimeImmutable
    {
        return $this->dateTraitement;
    }

    public function setDateTraitement(?\DateTimeImmutable $dateTraitement): self
    {
        $this->dateTraitement = $dateTraitement;

        return $this;
    }

    public function getValidateur(): ?Utilisateur
    {
        return $this->validateur;
    }

    public function setValidateur(?Utilisateur $validateur): self
    {
        $this->validateur = $validateur;

        return $this;
    }

    public function getCommentaireValidation(): ?string
    {
        return $this->commentaireValidation;
    }

    public function setCommentaireValidation(?string $commentaireValidation): self
    {
        $this->commentaireValidation = $commentaireValidation;

        return $this;
    }

    public function getNombreSoumissions(): int
    {
        return $this->nombreSoumissions;
    }

    public function setNombreSoumissions(int $nombreSoumissions): self
    {
        $this->nombreSoumissions = $nombreSoumissions;

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
