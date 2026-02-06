<?php

declare(strict_types=1);

namespace App\Entity\Academic;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'filiere')]
class Filiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_filiere', type: 'integer')]
    private ?int $idFiliere = null;

    #[ORM\Column(name: 'code_filiere', type: 'string', length: 20, unique: true)]
    private string $codeFiliere;

    #[ORM\Column(name: 'libelle_filiere', type: 'string', length: 100)]
    private string $libelleFiliere;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    public function getIdFiliere(): ?int
    {
        return $this->idFiliere;
    }

    public function getCodeFiliere(): string
    {
        return $this->codeFiliere;
    }

    public function setCodeFiliere(string $codeFiliere): self
    {
        $this->codeFiliere = $codeFiliere;

        return $this;
    }

    public function getLibelleFiliere(): string
    {
        return $this->libelleFiliere;
    }

    public function setLibelleFiliere(string $libelleFiliere): self
    {
        $this->libelleFiliere = $libelleFiliere;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
}
