<?php

declare(strict_types=1);

namespace App\Entity\Academic;

use App\Entity\Enseignant;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'element_constitutif')]
class ElementConstitutif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_ecue', type: 'integer')]
    private ?int $idEcue = null;

    #[ORM\Column(name: 'code_ecue', type: 'string', length: 20)]
    private string $codeEcue;

    #[ORM\Column(name: 'libelle_ecue', type: 'string', length: 100)]
    private string $libelleEcue;

    #[ORM\ManyToOne(targetEntity: UniteEnseignement::class, inversedBy: 'elementsConstitutifs')]
    #[ORM\JoinColumn(name: 'id_ue', referencedColumnName: 'id_ue', nullable: false)]
    private ?UniteEnseignement $uniteEnseignement = null;

    #[ORM\Column(name: 'credits', type: 'integer')]
    private int $credits;

    #[ORM\ManyToOne(targetEntity: Enseignant::class)]
    #[ORM\JoinColumn(name: 'matricule_enseignant', referencedColumnName: 'matricule_enseignant', nullable: true)]
    private ?Enseignant $enseignant = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    #[ORM\OneToMany(mappedBy: 'elementConstitutif', targetEntity: Note::class)]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getIdEcue(): ?int
    {
        return $this->idEcue;
    }

    public function getCodeEcue(): string
    {
        return $this->codeEcue;
    }

    public function setCodeEcue(string $codeEcue): self
    {
        $this->codeEcue = $codeEcue;

        return $this;
    }

    public function getLibelleEcue(): string
    {
        return $this->libelleEcue;
    }

    public function setLibelleEcue(string $libelleEcue): self
    {
        $this->libelleEcue = $libelleEcue;

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

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): self
    {
        $this->credits = $credits;

        return $this;
    }

    public function getEnseignant(): ?Enseignant
    {
        return $this->enseignant;
    }

    public function setEnseignant(?Enseignant $enseignant): self
    {
        $this->enseignant = $enseignant;

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

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setElementConstitutif($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            if ($note->getElementConstitutif() === $this) {
                $note->setElementConstitutif(null);
            }
        }

        return $this;
    }
}
