<?php

declare(strict_types=1);

namespace App\Entity\Student;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'echeance', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uk_echeance_inscription_numero', columns: ['id_inscription', 'numero_echeance']),
])]
class Echeance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_echeance', type: 'integer')]
    private ?int $idEcheance = null;

    #[ORM\ManyToOne(targetEntity: Inscription::class, inversedBy: 'echeances')]
    #[ORM\JoinColumn(name: 'id_inscription', referencedColumnName: 'id_inscription', nullable: false)]
    private ?Inscription $inscription = null;

    #[ORM\Column(name: 'numero_echeance', type: 'integer')]
    private int $numeroEcheance;

    #[ORM\Column(name: 'montant_echeance', type: 'decimal', precision: 10, scale: 2)]
    private string $montantEcheance;

    #[ORM\Column(name: 'date_echeance', type: 'date')]
    private \DateTimeInterface $dateEcheance;

    #[ORM\Column(name: 'statut_echeance', enumType: StatutEcheance::class, options: ['default' => 'en_attente'])]
    private StatutEcheance $statutEcheance;

    #[ORM\Column(name: 'montant_paye', type: 'decimal', precision: 10, scale: 2, options: ['default' => '0.00'])]
    private string $montantPaye = '0.00';

    #[ORM\Column(name: 'date_paiement', type: 'date', nullable: true)]
    private ?\DateTimeInterface $datePaiement = null;

    public function getIdEcheance(): ?int
    {
        return $this->idEcheance;
    }

    public function getInscription(): ?Inscription
    {
        return $this->inscription;
    }

    public function setInscription(?Inscription $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getNumeroEcheance(): int
    {
        return $this->numeroEcheance;
    }

    public function setNumeroEcheance(int $numeroEcheance): self
    {
        $this->numeroEcheance = $numeroEcheance;

        return $this;
    }

    public function getMontantEcheance(): string
    {
        return $this->montantEcheance;
    }

    public function setMontantEcheance(string $montantEcheance): self
    {
        $this->montantEcheance = $montantEcheance;

        return $this;
    }

    public function getDateEcheance(): \DateTimeInterface
    {
        return $this->dateEcheance;
    }

    public function setDateEcheance(\DateTimeInterface $dateEcheance): self
    {
        $this->dateEcheance = $dateEcheance;

        return $this;
    }

    public function getStatutEcheance(): StatutEcheance
    {
        return $this->statutEcheance;
    }

    public function setStatutEcheance(StatutEcheance $statutEcheance): self
    {
        $this->statutEcheance = $statutEcheance;

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

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(?\DateTimeInterface $datePaiement): self
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }
}
