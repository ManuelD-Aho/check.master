<?php

declare(strict_types=1);

namespace App\Entity\Academic;

use App\Entity\Etudiant;
use App\Entity\Utilisateur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'note')]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_note', type: 'integer')]
    private ?int $idNote = null;

    #[ORM\ManyToOne(targetEntity: Etudiant::class)]
    #[ORM\JoinColumn(name: 'matricule_etudiant', referencedColumnName: 'matricule_etudiant', nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(targetEntity: AnneeAcademique::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'id_annee_academique', referencedColumnName: 'id_annee_academique', nullable: false)]
    private ?AnneeAcademique $anneeAcademique = null;

    #[ORM\ManyToOne(targetEntity: Semestre::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'id_semestre', referencedColumnName: 'id_semestre', nullable: false)]
    private ?Semestre $semestre = null;

    #[ORM\ManyToOne(targetEntity: UniteEnseignement::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'id_ue', referencedColumnName: 'id_ue', nullable: true)]
    private ?UniteEnseignement $uniteEnseignement = null;

    #[ORM\ManyToOne(targetEntity: ElementConstitutif::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'id_ecue', referencedColumnName: 'id_ecue', nullable: true)]
    private ?ElementConstitutif $elementConstitutif = null;

    #[ORM\Column(name: 'type_note', enumType: TypeNote::class)]
    private TypeNote $typeNote;

    #[ORM\Column(name: 'note', type: 'decimal', precision: 4, scale: 2, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(name: 'commentaire', type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_utilisateur_saisie', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $utilisateurSaisie = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime')]
    private DateTimeInterface $dateModification;

    public function getIdNote(): ?int
    {
        return $this->idNote;
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

    public function getSemestre(): ?Semestre
    {
        return $this->semestre;
    }

    public function setSemestre(?Semestre $semestre): self
    {
        $this->semestre = $semestre;

        return $this;
    }

    public function getUniteEnseignement(): ?UniteEnseignement
    {
        return $this->uniteEnseignement;
    }

    public function setUniteEnseignement(?UniteEnseignement $uniteEnseignement): self
    {
        $this->uniteEnseignement = $uniteEnseignement;

        return $this;
    }

    public function getElementConstitutif(): ?ElementConstitutif
    {
        return $this->elementConstitutif;
    }

    public function setElementConstitutif(?ElementConstitutif $elementConstitutif): self
    {
        $this->elementConstitutif = $elementConstitutif;

        return $this;
    }

    public function getTypeNote(): TypeNote
    {
        return $this->typeNote;
    }

    public function setTypeNote(TypeNote $typeNote): self
    {
        $this->typeNote = $typeNote;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

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

    public function getUtilisateurSaisie(): ?Utilisateur
    {
        return $this->utilisateurSaisie;
    }

    public function setUtilisateurSaisie(?Utilisateur $utilisateurSaisie): self
    {
        $this->utilisateurSaisie = $utilisateurSaisie;

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
