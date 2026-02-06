<?php

declare(strict_types=1);

namespace App\Entity\Stage;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'motif_rejet_candidature', uniqueConstraints: [new ORM\UniqueConstraint(name: 'code_motif', columns: ['code_motif'])])]
class MotifRejetCandidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_motif', type: 'integer')]
    private ?int $idMotif = null;

    #[ORM\Column(name: 'code_motif', type: 'string', length: 50)]
    private string $codeMotif;

    #[ORM\Column(name: 'libelle_motif', type: 'string', length: 200)]
    private string $libelleMotif;

    #[ORM\Column(name: 'actif', type: 'boolean', options: ['default' => true])]
    private bool $actif = true;

    public function getIdMotif(): ?int
    {
        return $this->idMotif;
    }

    public function setIdMotif(int $idMotif): self
    {
        $this->idMotif = $idMotif;

        return $this;
    }

    public function getCodeMotif(): string
    {
        return $this->codeMotif;
    }

    public function setCodeMotif(string $codeMotif): self
    {
        $this->codeMotif = $codeMotif;

        return $this;
    }

    public function getLibelleMotif(): string
    {
        return $this->libelleMotif;
    }

    public function setLibelleMotif(string $libelleMotif): self
    {
        $this->libelleMotif = $libelleMotif;

        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }
}
