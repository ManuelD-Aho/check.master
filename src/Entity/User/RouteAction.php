<?php
declare(strict_types=1);

namespace App\Entity\User;

use App\Entity\System\Fonctionnalite;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'route_action')]
class RouteAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_route_action', type: 'integer')]
    private ?int $idRouteAction = null;

    #[ORM\Column(name: 'route_pattern', type: 'string', length: 255)]
    private string $routePattern;

    #[ORM\Column(name: 'http_method', enumType: RouteHttpMethod::class)]
    private RouteHttpMethod $httpMethod;

    #[ORM\Column(name: 'action_crud', enumType: RouteActionCrud::class)]
    private RouteActionCrud $actionCrud;

    #[ORM\ManyToOne(targetEntity: Fonctionnalite::class, inversedBy: 'routeActions')]
    #[ORM\JoinColumn(name: 'id_fonctionnalite', referencedColumnName: 'id_fonctionnalite')]
    private Fonctionnalite $fonctionnalite;

    #[ORM\Column(name: 'description', type: 'string', length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateModification;

    public function getIdRouteAction(): ?int
    {
        return $this->idRouteAction;
    }

    public function getRoutePattern(): string
    {
        return $this->routePattern;
    }

    public function setRoutePattern(string $routePattern): self
    {
        $this->routePattern = $routePattern;

        return $this;
    }

    public function getHttpMethod(): RouteHttpMethod
    {
        return $this->httpMethod;
    }

    public function setHttpMethod(RouteHttpMethod $httpMethod): self
    {
        $this->httpMethod = $httpMethod;

        return $this;
    }

    public function getActionCrud(): RouteActionCrud
    {
        return $this->actionCrud;
    }

    public function setActionCrud(RouteActionCrud $actionCrud): self
    {
        $this->actionCrud = $actionCrud;

        return $this;
    }

    public function getFonctionnalite(): Fonctionnalite
    {
        return $this->fonctionnalite;
    }

    public function setFonctionnalite(Fonctionnalite $fonctionnalite): self
    {
        $this->fonctionnalite = $fonctionnalite;

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

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

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
