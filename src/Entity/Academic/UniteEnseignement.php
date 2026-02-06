<?php

declare(strict_types=1);

namespace App\Entity\Academic;

use App\Entity\Enseignant;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'unite_enseignement')]
#[ORM\UniqueConstraint(name: 'uk_ue_code_annee', columns: ['code_ue', 'id_annee_academique'])]
class UniteEnseignement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_ue', type: 'integer')]
    private ?int $idUe = null;

    #[ORM\Column(name: 'code_ue', type: 'string', length: 20)]
    private string $codeUe;

    #[ORM\Column(name: 'libelle_ue', type: 'string', length: 100)]
    private string $libelleUe;

    #[ORM\ManyToOne(targetEntity: NiveauEtude::class, inversedBy: 'unitesEnseignement')]
    #[ORM\JoinColumn(name: 'id_niveau_etude', referencedColumnName: 'id_niveau_etude', nullable: false)]
    private ?NiveauEtude $niveauEtude = null;

    #[ORM\ManyToOne(targetEntity: Semestre::class, inversedBy: 'unitesEnseignement')]
    #[ORM\JoinColumn(name: 'id_semestre', referencedColumnName: 'id_semestre', nullable: false)]
    private ?Semestre $semestre = null;

    #[ORM\ManyToOne(targetEntity: AnneeAcademique::class, inversedBy: 'unitesEnseignement')]
    #[ORM\JoinColumn(name: 'id_annee_academique', referencedColumnName: 'id_annee_academique', nullable: false)]
    private ?AnneeAcademique $anneeAcademique = null;

    #[ORM\Column(name: 'credits', type: 'integer')]
    private int $credits;

    #[ORM\ManyToOne(targetEntity: Enseignant::class)]
    #[ORM\JoinColumn(name: 'matricule_responsable', referencedColumnName: 'matricule_enseignant', nullable: true)]
    private ?Enseignant $responsable = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    #[ORM\OneToMany(mappedBy: 'uniteEnseignement', targetEntity: ElementConstitutif::class)]
    private Collection $elementsConstitutifs;

    #[ORM\OneToMany(mappedBy: 'uniteEnseignement', targetEntity: Note::class)]
    private Collection $notes;

    public function __construct()
    {
        $this->elementsConstitutifs = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function getIdUe(): ?int
    {
        return $this->idUe;
    }

    public function getCodeUe(): string
    {
        return $this->codeUe;
    }

    public function setCodeUe(string $codeUe): self
    {
        $this->codeUe = $codeUe;

        return $this;
    }

    public function getLibelleUe(): string
    {
        return $this->libelleUe;
    }

    public function setLibelleUe(string $libelleUe): self
    {
        $this->libelleUe = $libelleUe;

        return $this;
    }

    public function getNiveauEtude(): ?NiveauEtude
    {
        return $this->niveauEtude;
    }

    public function setNiveauEtude(?NiveauEtude $niveauEtude): self
    {
        $this->niveauEtude = $niveauEtude;

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

    public function getAnneeAcademique(): ?AnneeAcademique
    {
        return $this->anneeAcademique;
    }

    public function setAnneeAcademique(?AnneeAcademique $anneeAcademique): self
    {
        $this->anneeAcademique = $anneeAcademique;

        return $this;
    }

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): self
    {
        $this->credits = $credits;

        return $this;
    }

    public function getResponsable(): ?Enseignant
    {
        return $this->responsable;
    }

    public function setResponsable(?Enseignant $responsable): self
    {
        $this->responsable = $responsable;

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

    public function getElementsConstitutifs(): Collection
    {
        return $this->elementsConstitutifs;
    }

    public function addElementConstitutif(ElementConstitutif $elementConstitutif): self
    {
        if (!$this->elementsConstitutifs->contains($elementConstitutif)) {
            $this->elementsConstitutifs->add($elementConstitutif);
            $elementConstitutif->setUniteEnseignement($this);
        }

        return $this;
    }

    public function removeElementConstitutif(ElementConstitutif $elementConstitutif): self
    {
        if ($this->elementsConstitutifs->removeElement($elementConstitutif)) {
            if ($elementConstitutif->getUniteEnseignement() === $this) {
                $elementConstitutif->setUniteEnseignement(null);
            }
        }

        return $this;
    }

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setUniteEnseignement($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            if ($note->getUniteEnseignement() === $this) {
                $note->setUniteEnseignement(null);
            }
        }

        return $this;
    }
}
