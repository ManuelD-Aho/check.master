<?php

declare(strict_types=1);

namespace App\Entity\Report;

use App\Entity\Utilisateur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'version_rapport')]
#[ORM\UniqueConstraint(name: 'uk_version_rapport_numero', columns: ['id_rapport', 'numero_version'])]
class VersionRapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_version', type: 'integer')]
    private ?int $idVersion = null;

    #[ORM\ManyToOne(targetEntity: Rapport::class, inversedBy: 'versionsRapport')]
    #[ORM\JoinColumn(name: 'id_rapport', referencedColumnName: 'id_rapport', nullable: false)]
    private ?Rapport $rapport = null;

    #[ORM\Column(name: 'numero_version', type: 'integer')]
    private int $numeroVersion;

    #[ORM\Column(name: 'contenu_html', type: 'text', columnDefinition: 'LONGTEXT')]
    private string $contenuHtml;

    #[ORM\Column(name: 'type_version', enumType: TypeVersion::class)]
    private TypeVersion $typeVersion;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_auteur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private ?Utilisateur $auteur = null;

    #[ORM\Column(name: 'commentaire', type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime')]
    private DateTimeInterface $dateCreation;

    public function getIdVersion(): ?int
    {
        return $this->idVersion;
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

    public function getNumeroVersion(): int
    {
        return $this->numeroVersion;
    }

    public function setNumeroVersion(int $numeroVersion): self
    {
        $this->numeroVersion = $numeroVersion;

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

    public function getTypeVersion(): TypeVersion
    {
        return $this->typeVersion;
    }

    public function setTypeVersion(TypeVersion $typeVersion): self
    {
        $this->typeVersion = $typeVersion;

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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

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
