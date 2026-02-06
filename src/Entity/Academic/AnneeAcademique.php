<?php

declare(strict_types=1);

namespace App\Entity\Academic;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'annee_academique')]
class AnneeAcademique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_annee_academique', type: 'integer')]
    private ?int $idAnneeAcademique = null;

    #[ORM\Column(name: 'libelle_annee', type: 'string', length: 20, unique: true)]
    private string $libelleAnnee;

    #[ORM\Column(name: 'date_debut', type: 'date')]
    private DateTimeInterface $dateDebut;

    #[ORM\Column(name: 'date_fin', type: 'date')]
    private DateTimeInterface $dateFin;

    #[ORM\Column(name: 'est_active', type: 'boolean')]
    private bool $estActive = false;

    #[ORM\Column(name: 'est_ouverte_inscription', type: 'boolean')]
    private bool $estOuverteInscription = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime')]
    private DateTimeInterface $dateModification;

    #[ORM\OneToMany(mappedBy: 'anneeAcademique', targetEntity: UniteEnseignement::class)]
    private Collection $unitesEnseignement;

    #[ORM\OneToMany(mappedBy: 'anneeAcademique', targetEntity: Note::class)]
    private Collection $notes;

    public function __construct()
    {
        $this->unitesEnseignement = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function getIdAnneeAcademique(): ?int
    {
        return $this->idAnneeAcademique;
    }

    public function getLibelleAnnee(): string
    {
        return $this->libelleAnnee;
    }

    public function setLibelleAnnee(string $libelleAnnee): self
    {
        $this->libelleAnnee = $libelleAnnee;

        return $this;
    }

    public function getDateDebut(): DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getEstActive(): bool
    {
        return $this->estActive;
    }

    public function setEstActive(bool $estActive): self
    {
        $this->estActive = $estActive;

        return $this;
    }

    public function getEstOuverteInscription(): bool
    {
        return $this->estOuverteInscription;
    }

    public function setEstOuverteInscription(bool $estOuverteInscription): self
    {
        $this->estOuverteInscription = $estOuverteInscription;

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

    public function getUnitesEnseignement(): Collection
    {
        return $this->unitesEnseignement;
    }

    public function addUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if (!$this->unitesEnseignement->contains($uniteEnseignement)) {
            $this->unitesEnseignement->add($uniteEnseignement);
            $uniteEnseignement->setAnneeAcademique($this);
        }

        return $this;
    }

    public function removeUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if ($this->unitesEnseignement->removeElement($uniteEnseignement)) {
            if ($uniteEnseignement->getAnneeAcademique() === $this) {
                $uniteEnseignement->setAnneeAcademique(null);
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
            $note->setAnneeAcademique($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            if ($note->getAnneeAcademique() === $this) {
                $note->setAnneeAcademique(null);
            }
        }

        return $this;
    }
}
