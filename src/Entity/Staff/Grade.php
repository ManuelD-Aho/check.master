<?php

declare(strict_types=1);

namespace App\Entity\Staff;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'grade')]
class Grade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_grade', type: 'integer')]
    private ?int $idGrade = null;

    #[ORM\Column(name: 'code_grade', type: 'string', length: 20, unique: true)]
    private string $codeGrade;

    #[ORM\Column(name: 'libelle_grade', type: 'string', length: 100)]
    private string $libelleGrade;

    #[ORM\Column(name: 'abreviation', type: 'string', length: 20)]
    private string $abreviation;

    #[ORM\Column(name: 'ordre_hierarchique', type: 'integer')]
    private int $ordreHierarchique;

    #[ORM\Column(name: 'peut_presider_jury', type: 'boolean', options: ['default' => false])]
    private bool $peutPresiderJury = false;

    #[ORM\Column(name: 'actif', type: 'boolean', options: ['default' => true])]
    private bool $actif = true;

    public function getIdGrade(): ?int
    {
        return $this->idGrade;
    }

    public function getCodeGrade(): string
    {
        return $this->codeGrade;
    }

    public function setCodeGrade(string $codeGrade): self
    {
        $this->codeGrade = $codeGrade;

        return $this;
    }

    public function getLibelleGrade(): string
    {
        return $this->libelleGrade;
    }

    public function setLibelleGrade(string $libelleGrade): self
    {
        $this->libelleGrade = $libelleGrade;

        return $this;
    }

    public function getAbreviation(): string
    {
        return $this->abreviation;
    }

    public function setAbreviation(string $abreviation): self
    {
        $this->abreviation = $abreviation;

        return $this;
    }

    public function getOrdreHierarchique(): int
    {
        return $this->ordreHierarchique;
    }

    public function setOrdreHierarchique(int $ordreHierarchique): self
    {
        $this->ordreHierarchique = $ordreHierarchique;

        return $this;
    }

    public function getPeutPresiderJury(): bool
    {
        return $this->peutPresiderJury;
    }

    public function setPeutPresiderJury(bool $peutPresiderJury): self
    {
        $this->peutPresiderJury = $peutPresiderJury;

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
}
