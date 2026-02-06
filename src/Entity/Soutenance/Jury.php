<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\Student\Etudiant;
use App\Entity\User\Utilisateur;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'jury')]
#[ORM\UniqueConstraint(name: 'uk_jury_etudiant_annee', columns: ['matricule_etudiant', 'id_annee_academique'])]
class Jury
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_jury', type: 'integer')]
    private ?int $idJury = null;

    #[ORM\ManyToOne(targetEntity: Etudiant::class)]
    #[ORM\JoinColumn(name: 'matricule_etudiant', referencedColumnName: 'matricule_etudiant', nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(targetEntity: AnneeAcademique::class)]
    #[ORM\JoinColumn(name: 'id_annee_academique', referencedColumnName: 'id_annee_academique', nullable: false)]
    private ?AnneeAcademique $anneeAcademique = null;

    #[ORM\Column(name: 'statut_jury', enumType: StatutJury::class)]
    private StatutJury $statutJury = StatutJury::EN_COMPOSITION;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_createur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $createur = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    #[ORM\Column(name: 'date_validation', type: 'datetime', nullable: true)]
    private ?DateTimeInterface $dateValidation = null;

    #[ORM\OneToMany(mappedBy: 'jury', targetEntity: CompositionJury::class)]
    private Collection $compositions;

    #[ORM\OneToOne(mappedBy: 'jury', targetEntity: Soutenance::class)]
    private ?Soutenance $soutenance = null;

    public function __construct()
    {
        $this->compositions = new ArrayCollection();
    }

    public function getIdJury(): ?int
    {
        return $this->idJury;
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

    public function getStatutJury(): StatutJury
    {
        return $this->statutJury;
    }

    public function setStatutJury(StatutJury $statutJury): self
    {
        $this->statutJury = $statutJury;

        return $this;
    }

    public function getCreateur(): ?Utilisateur
    {
        return $this->createur;
    }

    public function setCreateur(?Utilisateur $createur): self
    {
        $this->createur = $createur;

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

    public function getDateValidation(): ?DateTimeInterface
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?DateTimeInterface $dateValidation): self
    {
        $this->dateValidation = $dateValidation;

        return $this;
    }

    public function getCompositions(): Collection
    {
        return $this->compositions;
    }

    public function addComposition(CompositionJury $compositionJury): self
    {
        if (!$this->compositions->contains($compositionJury)) {
            $this->compositions->add($compositionJury);
            $compositionJury->setJury($this);
        }

        return $this;
    }

    public function removeComposition(CompositionJury $compositionJury): self
    {
        if ($this->compositions->removeElement($compositionJury)) {
            if ($compositionJury->getJury() === $this) {
                $compositionJury->setJury(null);
            }
        }

        return $this;
    }

    public function getSoutenance(): ?Soutenance
    {
        return $this->soutenance;
    }

    public function setSoutenance(?Soutenance $soutenance): self
    {
        $this->soutenance = $soutenance;

        return $this;
    }
}
