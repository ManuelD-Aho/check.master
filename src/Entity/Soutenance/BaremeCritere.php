<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

use App\Entity\Academic\AnneeAcademique;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'bareme_critere')]
#[ORM\UniqueConstraint(name: 'uk_bareme_annee_critere', columns: ['id_annee_academique', 'id_critere'])]
class BaremeCritere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: AnneeAcademique::class)]
    #[ORM\JoinColumn(name: 'id_annee_academique', referencedColumnName: 'id_annee_academique', nullable: false)]
    private ?AnneeAcademique $anneeAcademique = null;

    #[ORM\ManyToOne(targetEntity: CritereEvaluation::class, inversedBy: 'baremes')]
    #[ORM\JoinColumn(name: 'id_critere', referencedColumnName: 'id_critere', nullable: false)]
    private ?CritereEvaluation $critere = null;

    #[ORM\Column(name: 'bareme', type: 'decimal', precision: 4, scale: 2)]
    private string $bareme;

    #[ORM\Column(name: 'coefficient', type: 'decimal', precision: 3, scale: 2)]
    private string $coefficient = '1.00';

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCritere(): ?CritereEvaluation
    {
        return $this->critere;
    }

    public function setCritere(?CritereEvaluation $critere): self
    {
        $this->critere = $critere;

        return $this;
    }

    public function getBareme(): string
    {
        return $this->bareme;
    }

    public function setBareme(string $bareme): self
    {
        $this->bareme = $bareme;

        return $this;
    }

    public function getCoefficient(): string
    {
        return $this->coefficient;
    }

    public function setCoefficient(string $coefficient): self
    {
        $this->coefficient = $coefficient;

        return $this;
    }
}
