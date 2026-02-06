<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

use App\Entity\Student\Etudiant;
use App\Entity\User\Utilisateur;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'soutenance')]
#[ORM\UniqueConstraint(name: 'uk_soutenance_salle_creneau', columns: ['id_salle', 'date_soutenance', 'heure_debut'])]
class Soutenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_soutenance', type: 'integer')]
    private ?int $idSoutenance = null;

    #[ORM\OneToOne(targetEntity: Jury::class, inversedBy: 'soutenance')]
    #[ORM\JoinColumn(name: 'id_jury', referencedColumnName: 'id_jury', nullable: false, unique: true)]
    private ?Jury $jury = null;

    #[ORM\ManyToOne(targetEntity: Etudiant::class)]
    #[ORM\JoinColumn(name: 'matricule_etudiant', referencedColumnName: 'matricule_etudiant', nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(targetEntity: Salle::class, inversedBy: 'soutenances')]
    #[ORM\JoinColumn(name: 'id_salle', referencedColumnName: 'id_salle', nullable: false)]
    private ?Salle $salle = null;

    #[ORM\Column(name: 'date_soutenance', type: 'date')]
    private DateTimeInterface $dateSoutenance;

    #[ORM\Column(name: 'heure_debut', type: 'time')]
    private DateTimeInterface $heureDebut;

    #[ORM\Column(name: 'heure_fin', type: 'time', nullable: true)]
    private ?DateTimeInterface $heureFin = null;

    #[ORM\Column(name: 'duree_minutes', type: 'integer')]
    private int $dureeMinutes = 60;

    #[ORM\Column(name: 'theme_soutenance', type: 'string', length: 255)]
    private string $themeSoutenance;

    #[ORM\Column(name: 'statut_soutenance', enumType: StatutSoutenance::class)]
    private StatutSoutenance $statutSoutenance = StatutSoutenance::PROGRAMMEE;

    #[ORM\Column(name: 'observations', type: 'text', nullable: true)]
    private ?string $observations = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_programmeur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $programmeur = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime')]
    private DateTimeInterface $dateModification;

    #[ORM\OneToMany(mappedBy: 'soutenance', targetEntity: NoteSoutenance::class)]
    private Collection $notesSoutenance;

    public function __construct()
    {
        $this->notesSoutenance = new ArrayCollection();
    }

    public function getIdSoutenance(): ?int
    {
        return $this->idSoutenance;
    }

    public function getJury(): ?Jury
    {
        return $this->jury;
    }

    public function setJury(?Jury $jury): self
    {
        $this->jury = $jury;

        return $this;
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

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): self
    {
        $this->salle = $salle;

        return $this;
    }

    public function getDateSoutenance(): DateTimeInterface
    {
        return $this->dateSoutenance;
    }

    public function setDateSoutenance(DateTimeInterface $dateSoutenance): self
    {
        $this->dateSoutenance = $dateSoutenance;

        return $this;
    }

    public function getHeureDebut(): DateTimeInterface
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(DateTimeInterface $heureDebut): self
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?DateTimeInterface
    {
        return $this->heureFin;
    }

    public function setHeureFin(?DateTimeInterface $heureFin): self
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    public function getDureeMinutes(): int
    {
        return $this->dureeMinutes;
    }

    public function setDureeMinutes(int $dureeMinutes): self
    {
        $this->dureeMinutes = $dureeMinutes;

        return $this;
    }

    public function getThemeSoutenance(): string
    {
        return $this->themeSoutenance;
    }

    public function setThemeSoutenance(string $themeSoutenance): self
    {
        $this->themeSoutenance = $themeSoutenance;

        return $this;
    }

    public function getStatutSoutenance(): StatutSoutenance
    {
        return $this->statutSoutenance;
    }

    public function setStatutSoutenance(StatutSoutenance $statutSoutenance): self
    {
        $this->statutSoutenance = $statutSoutenance;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): self
    {
        $this->observations = $observations;

        return $this;
    }

    public function getProgrammeur(): ?Utilisateur
    {
        return $this->programmeur;
    }

    public function setProgrammeur(?Utilisateur $programmeur): self
    {
        $this->programmeur = $programmeur;

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

    public function getNotesSoutenance(): Collection
    {
        return $this->notesSoutenance;
    }

    public function addNoteSoutenance(NoteSoutenance $noteSoutenance): self
    {
        if (!$this->notesSoutenance->contains($noteSoutenance)) {
            $this->notesSoutenance->add($noteSoutenance);
            $noteSoutenance->setSoutenance($this);
        }

        return $this;
    }

    public function removeNoteSoutenance(NoteSoutenance $noteSoutenance): self
    {
        if ($this->notesSoutenance->removeElement($noteSoutenance)) {
            if ($noteSoutenance->getSoutenance() === $this) {
                $noteSoutenance->setSoutenance(null);
            }
        }

        return $this;
    }
}
