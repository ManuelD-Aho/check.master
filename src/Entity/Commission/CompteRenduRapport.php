<?php

declare(strict_types=1);

namespace App\Entity\Commission;

use App\Entity\Report\Rapport;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'compte_rendu_rapport', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uk_cr_rapport', columns: ['id_compte_rendu', 'id_rapport'])])]
class CompteRenduRapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CompteRenduCommission::class)]
    #[ORM\JoinColumn(name: 'id_compte_rendu', referencedColumnName: 'id_compte_rendu', nullable: false)]
    private CompteRenduCommission $compteRendu;

    #[ORM\ManyToOne(targetEntity: Rapport::class)]
    #[ORM\JoinColumn(name: 'id_rapport', referencedColumnName: 'id_rapport', nullable: false)]
    private Rapport $rapport;

    #[ORM\Column(name: 'ordre', type: 'integer')]
    private int $ordre;

    #[ORM\Column(name: 'remarque_specifique', type: 'text', nullable: true)]
    private ?string $remarqueSpecifique = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCompteRendu(): CompteRenduCommission
    {
        return $this->compteRendu;
    }

    public function setCompteRendu(CompteRenduCommission $compteRendu): self
    {
        $this->compteRendu = $compteRendu;

        return $this;
    }

    public function getRapport(): Rapport
    {
        return $this->rapport;
    }

    public function setRapport(Rapport $rapport): self
    {
        $this->rapport = $rapport;

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

    public function getRemarqueSpecifique(): ?string
    {
        return $this->remarqueSpecifique;
    }

    public function setRemarqueSpecifique(?string $remarqueSpecifique): self
    {
        $this->remarqueSpecifique = $remarqueSpecifique;

        return $this;
    }
}
