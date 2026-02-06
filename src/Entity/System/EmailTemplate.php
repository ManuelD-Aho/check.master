<?php
declare(strict_types=1);

namespace App\Entity\System;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'email_template', indexes: [new ORM\Index(name: 'idx_email_template_code', columns: ['code'])])]
class EmailTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_template', type: 'integer')]
    private ?int $idTemplate = null;

    #[ORM\Column(name: 'code', type: 'string', length: 100, unique: true)]
    private string $code;

    #[ORM\Column(name: 'nom', type: 'string', length: 255)]
    private string $nom;

    #[ORM\Column(name: 'sujet', type: 'string', length: 255)]
    private string $sujet;

    #[ORM\Column(name: 'contenu', type: 'text')]
    private string $contenu;

    #[ORM\Column(name: 'variables', type: 'json', nullable: true)]
    private ?array $variables = null;

    #[ORM\Column(name: 'est_actif', type: 'boolean', options: ['default' => true])]
    private bool $estActif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateModification = null;

    public function getIdTemplate(): ?int
    {
        return $this->idTemplate;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSujet(): string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getVariables(): ?array
    {
        return $this->variables;
    }

    public function setVariables(?array $variables): self
    {
        $this->variables = $variables;

        return $this;
    }

    public function isEstActif(): bool
    {
        return $this->estActif;
    }

    public function setEstActif(bool $estActif): self
    {
        $this->estActif = $estActif;

        return $this;
    }

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateModification(): ?\DateTimeImmutable
    {
        return $this->dateModification;
    }

    public function setDateModification(?\DateTimeImmutable $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }
}
