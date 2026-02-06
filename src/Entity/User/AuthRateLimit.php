<?php
declare(strict_types=1);

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'auth_rate_limit', indexes: [new ORM\Index(name: 'idx_rate_limit_ip_action', columns: ['adresse_ip', 'action'])])]
class AuthRateLimit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'action', type: 'string', length: 50)]
    private string $action;

    #[ORM\Column(name: 'adresse_ip', type: 'string', length: 45)]
    private string $adresseIp;

    #[ORM\Column(name: 'identifiant', type: 'string', length: 255, nullable: true)]
    private ?string $identifiant = null;

    #[ORM\Column(name: 'tentatives', type: 'integer')]
    private int $tentatives = 0;

    #[ORM\Column(name: 'debut_fenetre', type: 'datetime_immutable')]
    private \DateTimeImmutable $debutFenetre;

    #[ORM\Column(name: 'derniere_tentative', type: 'datetime_immutable')]
    private \DateTimeImmutable $derniereTentative;

    #[ORM\Column(name: 'bloque_jusqu', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $bloqueJusqu = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateModification;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getAdresseIp(): string
    {
        return $this->adresseIp;
    }

    public function setAdresseIp(string $adresseIp): self
    {
        $this->adresseIp = $adresseIp;

        return $this;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(?string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getTentatives(): int
    {
        return $this->tentatives;
    }

    public function setTentatives(int $tentatives): self
    {
        $this->tentatives = $tentatives;

        return $this;
    }

    public function getDebutFenetre(): \DateTimeImmutable
    {
        return $this->debutFenetre;
    }

    public function setDebutFenetre(\DateTimeImmutable $debutFenetre): self
    {
        $this->debutFenetre = $debutFenetre;

        return $this;
    }

    public function getDerniereTentative(): \DateTimeImmutable
    {
        return $this->derniereTentative;
    }

    public function setDerniereTentative(\DateTimeImmutable $derniereTentative): self
    {
        $this->derniereTentative = $derniereTentative;

        return $this;
    }

    public function getBloqueJusqu(): ?\DateTimeImmutable
    {
        return $this->bloqueJusqu;
    }

    public function setBloqueJusqu(?\DateTimeImmutable $bloqueJusqu): self
    {
        $this->bloqueJusqu = $bloqueJusqu;

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

    public function getDateModification(): \DateTimeImmutable
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTimeImmutable $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }
}
