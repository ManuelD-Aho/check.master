<?php
declare(strict_types=1);

namespace App\Entity\System;

use App\Entity\User\Permission;
use App\Entity\User\RouteAction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'fonctionnalite', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uniq_fonctionnalite_code', columns: ['code_fonctionnalite'])])]
class Fonctionnalite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_fonctionnalite', type: 'integer')]
    private ?int $idFonctionnalite = null;

    #[ORM\ManyToOne(targetEntity: CategorieFonctionnalite::class, inversedBy: 'fonctionnalites')]
    #[ORM\JoinColumn(name: 'id_categorie', referencedColumnName: 'id_categorie')]
    private CategorieFonctionnalite $categorie;

    #[ORM\Column(name: 'code_fonctionnalite', type: 'string', length: 50)]
    private string $codeFonctionnalite;

    #[ORM\Column(name: 'libelle_fonctionnalite', type: 'string', length: 100)]
    private string $libelleFonctionnalite;

    #[ORM\Column(name: 'label_court', type: 'string', length: 50, nullable: true)]
    private ?string $labelCourt = null;

    #[ORM\Column(name: 'description_fonctionnalite', type: 'text', nullable: true)]
    private ?string $descriptionFonctionnalite = null;

    #[ORM\Column(name: 'url_fonctionnalite', type: 'string', length: 255)]
    private string $urlFonctionnalite;

    #[ORM\Column(name: 'icone_fonctionnalite', type: 'string', length: 50, nullable: true)]
    private ?string $iconeFonctionnalite = null;

    #[ORM\Column(name: 'ordre_affichage', type: 'integer')]
    private int $ordreAffichage = 0;

    #[ORM\Column(name: 'est_sous_page', type: 'boolean')]
    private bool $estSousPage = false;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'enfants')]
    #[ORM\JoinColumn(name: 'id_page_parente', referencedColumnName: 'id_fonctionnalite', nullable: true)]
    private ?self $pageParente = null;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\OneToMany(mappedBy: 'pageParente', targetEntity: self::class)]
    private Collection $enfants;

    #[ORM\OneToMany(mappedBy: 'fonctionnalite', targetEntity: Permission::class)]
    private Collection $permissions;

    #[ORM\OneToMany(mappedBy: 'fonctionnalite', targetEntity: RouteAction::class)]
    private Collection $routeActions;

    public function __construct()
    {
        $this->enfants = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->routeActions = new ArrayCollection();
    }

    public function getIdFonctionnalite(): ?int
    {
        return $this->idFonctionnalite;
    }

    public function getCategorie(): CategorieFonctionnalite
    {
        return $this->categorie;
    }

    public function setCategorie(CategorieFonctionnalite $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getCodeFonctionnalite(): string
    {
        return $this->codeFonctionnalite;
    }

    public function setCodeFonctionnalite(string $codeFonctionnalite): self
    {
        $this->codeFonctionnalite = $codeFonctionnalite;

        return $this;
    }

    public function getLibelleFonctionnalite(): string
    {
        return $this->libelleFonctionnalite;
    }

    public function setLibelleFonctionnalite(string $libelleFonctionnalite): self
    {
        $this->libelleFonctionnalite = $libelleFonctionnalite;

        return $this;
    }

    public function getLabelCourt(): ?string
    {
        return $this->labelCourt;
    }

    public function setLabelCourt(?string $labelCourt): self
    {
        $this->labelCourt = $labelCourt;

        return $this;
    }

    public function getDescriptionFonctionnalite(): ?string
    {
        return $this->descriptionFonctionnalite;
    }

    public function setDescriptionFonctionnalite(?string $descriptionFonctionnalite): self
    {
        $this->descriptionFonctionnalite = $descriptionFonctionnalite;

        return $this;
    }

    public function getUrlFonctionnalite(): string
    {
        return $this->urlFonctionnalite;
    }

    public function setUrlFonctionnalite(string $urlFonctionnalite): self
    {
        $this->urlFonctionnalite = $urlFonctionnalite;

        return $this;
    }

    public function getIconeFonctionnalite(): ?string
    {
        return $this->iconeFonctionnalite;
    }

    public function setIconeFonctionnalite(?string $iconeFonctionnalite): self
    {
        $this->iconeFonctionnalite = $iconeFonctionnalite;

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

    public function isEstSousPage(): bool
    {
        return $this->estSousPage;
    }

    public function setEstSousPage(bool $estSousPage): self
    {
        $this->estSousPage = $estSousPage;

        return $this;
    }

    public function getPageParente(): ?self
    {
        return $this->pageParente;
    }

    public function setPageParente(?self $pageParente): self
    {
        $this->pageParente = $pageParente;

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

    public function getEnfants(): Collection
    {
        return $this->enfants;
    }

    public function addEnfant(self $enfant): self
    {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants->add($enfant);
            $enfant->setPageParente($this);
        }

        return $this;
    }

    public function removeEnfant(self $enfant): self
    {
        if ($this->enfants->removeElement($enfant)) {
            if ($enfant->getPageParente() === $this) {
                $enfant->setPageParente(null);
            }
        }

        return $this;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
            $permission->setFonctionnalite($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        $this->permissions->removeElement($permission);

        return $this;
    }

    public function getRouteActions(): Collection
    {
        return $this->routeActions;
    }

    public function addRouteAction(RouteAction $routeAction): self
    {
        if (!$this->routeActions->contains($routeAction)) {
            $this->routeActions->add($routeAction);
            $routeAction->setFonctionnalite($this);
        }

        return $this;
    }

    public function removeRouteAction(RouteAction $routeAction): self
    {
        $this->routeActions->removeElement($routeAction);

        return $this;
    }
}
