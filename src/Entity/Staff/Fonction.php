<?php

declare(strict_types=1);

namespace App\Entity\Staff;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'fonction')]
class Fonction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_fonction', type: 'integer')]
    private ?int $idFonction = null;

    #[ORM\Column(name: 'code_fonction', type: 'string', length: 20, unique: true)]
    private string $codeFonction;

    #[ORM\Column(name: 'libelle_fonction', type: 'string', length: 100)]
    private string $libelleFonction;

    #[ORM\Column(name: 'type_fonction', type: 'string', enumType: TypeFonction::class, length: 20)]
    private TypeFonction $typeFonction;

    #[ORM\Column(name: 'actif', type: 'boolean', options: ['default' => true])]
    private bool $actif = true;

    public function getIdFonction(): ?int
    {
        return $this->idFonction;
    }

    public function getCodeFonction(): string
    {
        return $this->codeFonction;
    }

    public function setCodeFonction(string $codeFonction): self
    {
        $this->codeFonction = $codeFonction;

        return $this;
    }

    public function getLibelleFonction(): string
    {
        return $this->libelleFonction;
    }

    public function setLibelleFonction(string $libelleFonction): self
    {
        $this->libelleFonction = $libelleFonction;

        return $this;
    }

    public function getTypeFonction(): TypeFonction
    {
        return $this->typeFonction;
    }

    public function setTypeFonction(TypeFonction $typeFonction): self
    {
        $this->typeFonction = $typeFonction;

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
