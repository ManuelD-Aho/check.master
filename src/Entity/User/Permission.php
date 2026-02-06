<?php
declare(strict_types=1);

namespace App\Entity\User;

use App\Entity\System\Fonctionnalite;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'permission', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uk_permission_groupe_fonc', columns: ['id_groupe_utilisateur', 'id_fonctionnalite'])])]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_permission', type: 'integer')]
    private ?int $idPermission = null;

    #[ORM\ManyToOne(targetEntity: GroupeUtilisateur::class, inversedBy: 'permissions')]
    #[ORM\JoinColumn(name: 'id_groupe_utilisateur', referencedColumnName: 'id_groupe_utilisateur')]
    private GroupeUtilisateur $groupeUtilisateur;

    #[ORM\ManyToOne(targetEntity: Fonctionnalite::class, inversedBy: 'permissions')]
    #[ORM\JoinColumn(name: 'id_fonctionnalite', referencedColumnName: 'id_fonctionnalite')]
    private Fonctionnalite $fonctionnalite;

    #[ORM\Column(name: 'peut_voir', type: 'boolean')]
    private bool $peutVoir = false;

    #[ORM\Column(name: 'peut_creer', type: 'boolean')]
    private bool $peutCreer = false;

    #[ORM\Column(name: 'peut_modifier', type: 'boolean')]
    private bool $peutModifier = false;

    #[ORM\Column(name: 'peut_supprimer', type: 'boolean')]
    private bool $peutSupprimer = false;

    #[ORM\Column(name: 'date_attribution', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateAttribution;

    public function getIdPermission(): ?int
    {
        return $this->idPermission;
    }

    public function getGroupeUtilisateur(): GroupeUtilisateur
    {
        return $this->groupeUtilisateur;
    }

    public function setGroupeUtilisateur(GroupeUtilisateur $groupeUtilisateur): self
    {
        $this->groupeUtilisateur = $groupeUtilisateur;

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

    public function isPeutVoir(): bool
    {
        return $this->peutVoir;
    }

    public function setPeutVoir(bool $peutVoir): self
    {
        $this->peutVoir = $peutVoir;

        return $this;
    }

    public function isPeutCreer(): bool
    {
        return $this->peutCreer;
    }

    public function setPeutCreer(bool $peutCreer): self
    {
        $this->peutCreer = $peutCreer;

        return $this;
    }

    public function isPeutModifier(): bool
    {
        return $this->peutModifier;
    }

    public function setPeutModifier(bool $peutModifier): self
    {
        $this->peutModifier = $peutModifier;

        return $this;
    }

    public function isPeutSupprimer(): bool
    {
        return $this->peutSupprimer;
    }

    public function setPeutSupprimer(bool $peutSupprimer): self
    {
        $this->peutSupprimer = $peutSupprimer;

        return $this;
    }

    public function getDateAttribution(): \DateTimeImmutable
    {
        return $this->dateAttribution;
    }

    public function setDateAttribution(\DateTimeImmutable $dateAttribution): self
    {
        $this->dateAttribution = $dateAttribution;

        return $this;
    }
}
