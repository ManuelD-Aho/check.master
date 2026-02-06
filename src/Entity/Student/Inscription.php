<?php

declare(strict_types=1);

namespace App\Entity\Student;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'inscription', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uk_inscription_etudiant_annee', columns: ['matricule_etudiant', 'id_annee_academique']),
])]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_inscription', type: 'integer')]
    private ?int $idInscription = null;

    #[ORM\ManyToOne(targetEntity: Etudiant::class, inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(name: 'matricule_etudiant', referencedColumnName: 'matricule_etudiant', nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(targetEntity: 'App\\Entity\\Academic\\NiveauEtude')]
    #[ORM\JoinColumn(name: 'id_niveau_etude', referencedColumnName: 'id_niveau_etude', nullable: false)]
    private $niveauEtude;

    #[ORM\ManyToOne(targetEntity: 'App\\Entity\\Academic\\AnneeAcademique')]
    #[ORM\JoinColumn(name: 'id_annee_academique', referencedColumnName: 'id_annee_academique', nullable: false)]
    private $anneeAcademique;

    #[ORM\Column(name: 'date_inscription', type: 'date')]
    private \DateTimeInterface $dateInscription;

    #[ORM\Column(name: 'statut_inscription', enumType: StatutInscription::class, options: ['default' => 'en_attente'])]
    private StatutInscription $statutInscription;

    #[ORM\Column(name: 'montant_inscription', type: 'decimal', precision: 10, scale: 2)]
    private string $montantInscription;

    #[ORM\Column(name: 'montant_scolarite', type: 'decimal', precision: 10, scale: 2)]
    private string $montantScolarite;

    #[ORM\Column(name: 'nombre_tranches', type: 'integer', options: ['default' => 1])]
    private int $nombreTranches = 1;

    #[ORM\Column(name: 'montant_paye', type: 'decimal', precision: 10, scale: 2, options: ['default' => '0.00'])]
    private string $montantPaye = '0.00';

    #[ORM\Column(name: 'reste_a_payer', type: 'decimal', precision: 10, scale: 2, insertable: false, updatable: false, columnDefinition: 'DECIMAL(10,2) AS (montant_scolarite + montant_inscription - montant_paye) STORED')]
    private string $resteAPayer;

    #[ORM\Column(name: 'date_creation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeInterface $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeInterface $dateModification;

    #[ORM\OneToMany(mappedBy: 'inscription', targetEntity: Versement::class)]
    private Collection $versements;

    #[ORM\OneToMany(mappedBy: 'inscription', targetEntity: Echeance::class)]
    private Collection $echeances;

    public function __construct()
    {
        $this->versements = new ArrayCollection();
        $this->echeances = new ArrayCollection();
    }

    public function getIdInscription(): ?int
    {
        return $this->idInscription;
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

    public function getNiveauEtude()
    {
        return $this->niveauEtude;
    }

    public function setNiveauEtude($niveauEtude): self
    {
        $this->niveauEtude = $niveauEtude;

        return $this;
    }

    public function getAnneeAcademique()
    {
        return $this->anneeAcademique;
    }

    public function setAnneeAcademique($anneeAcademique): self
    {
        $this->anneeAcademique = $anneeAcademique;

        return $this;
    }

    public function getDateInscription(): \DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getStatutInscription(): StatutInscription
    {
        return $this->statutInscription;
    }

    public function setStatutInscription(StatutInscription $statutInscription): self
    {
        $this->statutInscription = $statutInscription;

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

    public function getMontantScolarite(): string
    {
        return $this->montantScolarite;
    }

    public function setMontantScolarite(string $montantScolarite): self
    {
        $this->montantScolarite = $montantScolarite;

        return $this;
    }

    public function getNombreTranches(): int
    {
        return $this->nombreTranches;
    }

    public function setNombreTranches(int $nombreTranches): self
    {
        $this->nombreTranches = $nombreTranches;

        return $this;
    }

    public function getMontantPaye(): string
    {
        return $this->montantPaye;
    }

    public function setMontantPaye(string $montantPaye): self
    {
        $this->montantPaye = $montantPaye;

        return $this;
    }

    public function getResteAPayer(): string
    {
        return $this->resteAPayer;
    }

    public function setResteAPayer(string $resteAPayer): self
    {
        $this->resteAPayer = $resteAPayer;

        return $this;
    }

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): \DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getVersements(): Collection
    {
        return $this->versements;
    }

    public function addVersement(Versement $versement): self
    {
        if (!$this->versements->contains($versement)) {
            $this->versements->add($versement);
            $versement->setInscription($this);
        }

        return $this;
    }

    public function removeVersement(Versement $versement): self
    {
        if ($this->versements->removeElement($versement)) {
            if ($versement->getInscription() === $this) {
                $versement->setInscription(null);
            }
        }

        return $this;
    }

    public function getEcheances(): Collection
    {
        return $this->echeances;
    }

    public function addEcheance(Echeance $echeance): self
    {
        if (!$this->echeances->contains($echeance)) {
            $this->echeances->add($echeance);
            $echeance->setInscription($this);
        }

        return $this;
    }

    public function removeEcheance(Echeance $echeance): self
    {
        if ($this->echeances->removeElement($echeance)) {
            if ($echeance->getInscription() === $this) {
                $echeance->setInscription(null);
            }
        }

        return $this;
    }
}
