<?php

declare(strict_types=1);

namespace App\Entity\Soutenance;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'mention')]
class Mention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_mention', type: 'integer')]
    private ?int $idMention = null;

    #[ORM\Column(name: 'code_mention', type: 'string', length: 20, unique: true)]
    private string $codeMention;

    #[ORM\Column(name: 'libelle_mention', type: 'string', length: 50)]
    private string $libelleMention;

    #[ORM\Column(name: 'seuil_minimum', type: 'decimal', precision: 4, scale: 2)]
    private string $seuilMinimum;

    #[ORM\Column(name: 'seuil_maximum', type: 'decimal', precision: 4, scale: 2)]
    private string $seuilMaximum;

    #[ORM\Column(name: 'ordre', type: 'integer')]
    private int $ordre;

    #[ORM\OneToMany(mappedBy: 'mention', targetEntity: ResultatFinal::class)]
    private Collection $resultats;

    public function __construct()
    {
        $this->resultats = new ArrayCollection();
    }

    public function getIdMention(): ?int
    {
        return $this->idMention;
    }

    public function getCodeMention(): string
    {
        return $this->codeMention;
    }

    public function setCodeMention(string $codeMention): self
    {
        $this->codeMention = $codeMention;

        return $this;
    }

    public function getLibelleMention(): string
    {
        return $this->libelleMention;
    }

    public function setLibelleMention(string $libelleMention): self
    {
        $this->libelleMention = $libelleMention;

        return $this;
    }

    public function getSeuilMinimum(): string
    {
        return $this->seuilMinimum;
    }

    public function setSeuilMinimum(string $seuilMinimum): self
    {
        $this->seuilMinimum = $seuilMinimum;

        return $this;
    }

    public function getSeuilMaximum(): string
    {
        return $this->seuilMaximum;
    }

    public function setSeuilMaximum(string $seuilMaximum): self
    {
        $this->seuilMaximum = $seuilMaximum;

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

    public function getResultats(): Collection
    {
        return $this->resultats;
    }

    public function addResultat(ResultatFinal $resultatFinal): self
    {
        if (!$this->resultats->contains($resultatFinal)) {
            $this->resultats->add($resultatFinal);
            $resultatFinal->setMention($this);
        }

        return $this;
    }

    public function removeResultat(ResultatFinal $resultatFinal): self
    {
        if ($this->resultats->removeElement($resultatFinal)) {
            if ($resultatFinal->getMention() === $this) {
                $resultatFinal->setMention(null);
            }
        }

        return $this;
    }
}
