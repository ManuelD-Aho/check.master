<?php

declare(strict_types=1);

namespace App\Entity\Academic;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'semestre')]
class Semestre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_semestre', type: 'integer')]
    private ?int $idSemestre = null;

    #[ORM\Column(name: 'code_semestre', type: 'string', length: 10, unique: true)]
    private string $codeSemestre;

    #[ORM\Column(name: 'libelle_semestre', type: 'string', length: 50)]
    private string $libelleSemestre;

    #[ORM\ManyToOne(targetEntity: NiveauEtude::class, inversedBy: 'semestres')]
    #[ORM\JoinColumn(name: 'id_niveau_etude', referencedColumnName: 'id_niveau_etude', nullable: false)]
    private ?NiveauEtude $niveauEtude = null;

    #[ORM\Column(name: 'ordre', type: 'integer')]
    private int $ordre;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    #[ORM\OneToMany(mappedBy: 'semestre', targetEntity: UniteEnseignement::class)]
    private Collection $unitesEnseignement;

    #[ORM\OneToMany(mappedBy: 'semestre', targetEntity: Note::class)]
    private Collection $notes;

    public function __construct()
    {
        $this->unitesEnseignement = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function getIdSemestre(): ?int
    {
        return $this->idSemestre;
    }

    public function getCodeSemestre(): string
    {
        return $this->codeSemestre;
    }

    public function setCodeSemestre(string $codeSemestre): self
    {
        $this->codeSemestre = $codeSemestre;

        return $this;
    }

    public function getLibelleSemestre(): string
    {
        return $this->libelleSemestre;
    }

    public function setLibelleSemestre(string $libelleSemestre): self
    {
        $this->libelleSemestre = $libelleSemestre;

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

    public function getOrdre(): int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

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

    public function getUnitesEnseignement(): Collection
    {
        return $this->unitesEnseignement;
    }

    public function addUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if (!$this->unitesEnseignement->contains($uniteEnseignement)) {
            $this->unitesEnseignement->add($uniteEnseignement);
            $uniteEnseignement->setSemestre($this);
        }

        return $this;
    }

    public function removeUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if ($this->unitesEnseignement->removeElement($uniteEnseignement)) {
            if ($uniteEnseignement->getSemestre() === $this) {
                $uniteEnseignement->setSemestre(null);
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
            $note->setSemestre($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            if ($note->getSemestre() === $this) {
                $note->setSemestre(null);
            }
        }

        return $this;
    }
}
