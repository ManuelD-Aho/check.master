<?php

declare(strict_types=1);

namespace App\Entity\Report;

use App\Entity\Utilisateur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'validation_rapport')]
class ValidationRapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_validation', type: 'integer')]
    private ?int $idValidation = null;

    #[ORM\ManyToOne(targetEntity: Rapport::class, inversedBy: 'validationsRapport')]
    #[ORM\JoinColumn(name: 'id_rapport', referencedColumnName: 'id_rapport', nullable: false)]
    private ?Rapport $rapport = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_validateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $validateur = null;

    #[ORM\Column(name: 'action_validation', enumType: ActionValidation::class)]
    private ActionValidation $actionValidation;

    #[ORM\Column(name: 'motif_retour', type: 'string', length: 100, nullable: true)]
    private ?string $motifRetour = null;

    #[ORM\Column(name: 'commentaire_validation', type: 'text', nullable: true)]
    private ?string $commentaireValidation = null;

    #[ORM\Column(name: 'date_validation', type: 'datetime')]
    private DateTimeInterface $dateValidation;

    public function getIdValidation(): ?int
    {
        return $this->idValidation;
    }

    public function getRapport(): ?Rapport
    {
        return $this->rapport;
    }

    public function setRapport(?Rapport $rapport): self
    {
        $this->rapport = $rapport;

        return $this;
    }

    public function getValidateur(): ?Utilisateur
    {
        return $this->validateur;
    }

    public function setValidateur(?Utilisateur $validateur): self
    {
        $this->validateur = $validateur;

        return $this;
    }

    public function getActionValidation(): ActionValidation
    {
        return $this->actionValidation;
    }

    public function setActionValidation(ActionValidation $actionValidation): self
    {
        $this->actionValidation = $actionValidation;

        return $this;
    }

    public function getMotifRetour(): ?string
    {
        return $this->motifRetour;
    }

    public function setMotifRetour(?string $motifRetour): self
    {
        $this->motifRetour = $motifRetour;

        return $this;
    }

    public function getCommentaireValidation(): ?string
    {
        return $this->commentaireValidation;
    }

    public function setCommentaireValidation(?string $commentaireValidation): self
    {
        $this->commentaireValidation = $commentaireValidation;

        return $this;
    }

    public function getDateValidation(): DateTimeInterface
    {
        return $this->dateValidation;
    }

    public function setDateValidation(DateTimeInterface $dateValidation): self
    {
        $this->dateValidation = $dateValidation;

        return $this;
    }
}
