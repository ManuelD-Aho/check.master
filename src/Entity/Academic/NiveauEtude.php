<?php

declare(strict_types=1);

namespace App\Entity\Academic;

use App\Entity\Enseignant;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'niveau_etude')]
class NiveauEtude
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_niveau_etude', type: 'integer')]
    private ?int $idNiveauEtude = null;

    #[ORM\Column(name: 'code_niveau', type: 'string', length: 10, unique: true)]
    private string $codeNiveau;

    #[ORM\Column(name: 'libelle_niveau', type: 'string', length: 50)]
    private string $libelleNiveau;

    #[ORM\Column(name: 'ordre_progression', type: 'integer')]
    private int $ordreProgression;

    #[ORM\Column(name: 'montant_scolarite', type: 'decimal', precision: 10, scale: 2)]
    private string $montantScolarite;

    #[ORM\Column(name: 'montant_inscription', type: 'decimal', precision: 10, scale: 2)]
    private string $montantInscription;

    #[ORM\ManyToOne(targetEntity: Enseignant::class)]
    #[ORM\JoinColumn(name: 'id_responsable', referencedColumnName: 'matricule_enseignant', nullable: true)]
    private ?Enseignant $responsable = null;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    #[ORM\OneToMany(mappedBy: 'niveauEtude', targetEntity: Semestre::class)]
    private Collection $semestres;

    #[ORM\OneToMany(mappedBy: 'niveauEtude', targetEntity: UniteEnseignement::class)]
    private Collection $unitesEnseignement;

    public function __construct()
    {
        $this->semestres = new ArrayCollection();
        $this->unitesEnseignement = new ArrayCollection();
    }

    public function getIdNiveauEtude(): ?int
    {
        return $this->idNiveauEtude;
    }

    public function getCodeNiveau(): string
    {
        return $this->codeNiveau;
    }

    public function setCodeNiveau(string $codeNiveau): self
    {
        $this->codeNiveau = $codeNiveau;

        return $this;
    }

    public function getLibelleNiveau(): string
    {
        return $this->libelleNiveau;
    }

    public function setLibelleNiveau(string $libelleNiveau): self
    {
        $this->libelleNiveau = $libelleNiveau;

        return $this;
    }

    public function getOrdreProgression(): int
    {
        return $this->ordreProgression;
    }

    public function setOrdreProgression(int $ordreProgression): self
    {
        $this->ordreProgression = $ordreProgression;

        return $this;
    }

    public function getMontantScolarite(): string
    {
        return $this->montantScolarite;
    }

    public function setMontantScolarite(string $montantScolarite): self
    {
        $this->montantScolarite = $montantScolarite;

        return $this;
    }

    public function getMontantInscription(): string
    {
        return $this->montantInscription;
    }

    public function setMontantInscription(string $montantInscription): self
    {
        $this->montantInscription = $montantInscription;

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

    public function getSemestres(): Collection
    {
        return $this->semestres;
    }

    public function addSemestre(Semestre $semestre): self
    {
        if (!$this->semestres->contains($semestre)) {
            $this->semestres->add($semestre);
            $semestre->setNiveauEtude($this);
        }

        return $this;
    }

    public function removeSemestre(Semestre $semestre): self
    {
        if ($this->semestres->removeElement($semestre)) {
            if ($semestre->getNiveauEtude() === $this) {
                $semestre->setNiveauEtude(null);
            }
        }

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
            $uniteEnseignement->setNiveauEtude($this);
        }

        return $this;
    }

    public function removeUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if ($this->unitesEnseignement->removeElement($uniteEnseignement)) {
            if ($uniteEnseignement->getNiveauEtude() === $this) {
                $uniteEnseignement->setNiveauEtude(null);
            }
        }

        return $this;
    }
}
