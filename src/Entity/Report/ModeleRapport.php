<?php

declare(strict_types=1);

namespace App\Entity\Report;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'modele_rapport')]
class ModeleRapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_modele', type: 'integer')]
    private ?int $idModele = null;

    #[ORM\Column(name: 'nom_modele', type: 'string', length: 100)]
    private string $nomModele;

    #[ORM\Column(name: 'description_modele', type: 'text', nullable: true)]
    private ?string $descriptionModele = null;

    #[ORM\Column(name: 'contenu_html', type: 'text', columnDefinition: 'LONGTEXT')]
    private string $contenuHtml;

    #[ORM\Column(name: 'miniature', type: 'string', length: 255, nullable: true)]
    private ?string $miniature = null;

    #[ORM\Column(name: 'ordre_affichage', type: 'integer')]
    private int $ordreAffichage = 0;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    #[ORM\OneToMany(mappedBy: 'modeleRapport', targetEntity: Rapport::class)]
    private Collection $rapports;

    public function __construct()
    {
        $this->rapports = new ArrayCollection();
    }

    public function getIdModele(): ?int
    {
        return $this->idModele;
    }

    public function getNomModele(): string
    {
        return $this->nomModele;
    }

    public function setNomModele(string $nomModele): self
    {
        $this->nomModele = $nomModele;

        return $this;
    }

    public function getDescriptionModele(): ?string
    {
        return $this->descriptionModele;
    }

    public function setDescriptionModele(?string $descriptionModele): self
    {
        $this->descriptionModele = $descriptionModele;

        return $this;
    }

    public function getContenuHtml(): string
    {
        return $this->contenuHtml;
    }

    public function setContenuHtml(string $contenuHtml): self
    {
        $this->contenuHtml = $contenuHtml;

        return $this;
    }

    public function getMiniature(): ?string
    {
        return $this->miniature;
    }

    public function setMiniature(?string $miniature): self
    {
        $this->miniature = $miniature;

        return $this;
    }

    public function getOrdreAffichage(): int
    {
        return $this->ordreAffichage;
    }

    public function setOrdreAffichage(int $ordreAffichage): self
    {
        $this->ordreAffichage = $ordreAffichage;

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

    public function getRapports(): Collection
    {
        return $this->rapports;
    }

    public function addRapport(Rapport $rapport): self
    {
        if (!$this->rapports->contains($rapport)) {
            $this->rapports->add($rapport);
            $rapport->setModeleRapport($this);
        }

        return $this;
    }

    public function removeRapport(Rapport $rapport): self
    {
        if ($this->rapports->removeElement($rapport)) {
            if ($rapport->getModeleRapport() === $this) {
                $rapport->setModeleRapport(null);
            }
        }

        return $this;
    }
}
