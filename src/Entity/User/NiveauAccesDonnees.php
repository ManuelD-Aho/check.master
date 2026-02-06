<?php
declare(strict_types=1);

namespace App\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'niveau_acces_donnees', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_niveau_acces_code', columns: ['code_niveau'])])]
class NiveauAccesDonnees
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_niveau_acces', type: 'integer')]
    private ?int $idNiveauAcces = null;

    #[ORM\Column(name: 'code_niveau', type: 'string', length: 20)]
    private string $codeNiveau;

    #[ORM\Column(name: 'libelle_niveau', type: 'string', length: 100)]
    private string $libelleNiveau;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'niveauAcces', targetEntity: Utilisateur::class)]
    private Collection $utilisateurs;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
    }

    public function getIdNiveauAcces(): ?int
    {
        return $this->idNiveauAcces;
    }

    public function getCodeNiveau(): string
    {
        return $this->codeNiveau;
    }

    public function setCodeNiveau(string $codeNiveau): self
    {
        $this->codeNiveau = $codeNiveau;

        return $this;
    }

    public function getLibelleNiveau(): string
    {
        return $this->libelleNiveau;
    }

    public function setLibelleNiveau(string $libelleNiveau): self
    {
        $this->libelleNiveau = $libelleNiveau;

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

    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setNiveauAcces($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        $this->utilisateurs->removeElement($utilisateur);

        return $this;
    }
}
