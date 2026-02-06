<?php

declare(strict_types=1);

namespace App\Entity\Report;

use App\Entity\Utilisateur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'commentaire_rapport')]
class CommentaireRapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_commentaire', type: 'integer')]
    private ?int $idCommentaire = null;

    #[ORM\ManyToOne(targetEntity: Rapport::class, inversedBy: 'commentairesRapport')]
    #[ORM\JoinColumn(name: 'id_rapport', referencedColumnName: 'id_rapport', nullable: false)]
    private ?Rapport $rapport = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_auteur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $auteur = null;

    #[ORM\Column(name: 'contenu_commentaire', type: 'text')]
    private string $contenuCommentaire;

    #[ORM\Column(name: 'type_commentaire', enumType: TypeCommentaire::class)]
    private TypeCommentaire $typeCommentaire;

    #[ORM\Column(name: 'est_public', type: 'boolean')]
    private bool $estPublic = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    public function getIdCommentaire(): ?int
    {
        return $this->idCommentaire;
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

    public function getAuteur(): ?Utilisateur
    {
        return $this->auteur;
    }

    public function setAuteur(?Utilisateur $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getContenuCommentaire(): string
    {
        return $this->contenuCommentaire;
    }

    public function setContenuCommentaire(string $contenuCommentaire): self
    {
        $this->contenuCommentaire = $contenuCommentaire;

        return $this;
    }

    public function getTypeCommentaire(): TypeCommentaire
    {
        return $this->typeCommentaire;
    }

    public function setTypeCommentaire(TypeCommentaire $typeCommentaire): self
    {
        $this->typeCommentaire = $typeCommentaire;

        return $this;
    }

    public function getEstPublic(): bool
    {
        return $this->estPublic;
    }

    public function setEstPublic(bool $estPublic): self
    {
        $this->estPublic = $estPublic;

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
