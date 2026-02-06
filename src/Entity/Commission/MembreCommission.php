<?php

declare(strict_types=1);

namespace App\Entity\Commission;

use App\Entity\Academic\AnneeAcademique;
use App\Entity\User\Utilisateur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'membre_commission', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uk_membre_commission_annee', columns: ['id_utilisateur', 'id_annee_academique'])])]
class MembreCommission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_membre', type: 'integer')]
    private ?int $idMembre = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private Utilisateur $utilisateur;

    #[ORM\ManyToOne(targetEntity: AnneeAcademique::class)]
    #[ORM\JoinColumn(name: 'id_annee_academique', referencedColumnName: 'id_annee_academique', nullable: false)]
    private AnneeAcademique $anneeAcademique;

    #[ORM\Column(name: 'role_commission', type: 'string', enumType: RoleCommission::class, length: 20, options: ['default' => 'membre'])]
    private RoleCommission $roleCommission = RoleCommission::Membre;

    #[ORM\Column(name: 'actif', type: 'boolean', options: ['default' => true])]
    private bool $actif = true;

    #[ORM\Column(name: 'date_nomination', type: 'date')]
    private DateTimeInterface $dateNomination;

    #[ORM\Column(name: 'date_fin', type: 'date', nullable: true)]
    private ?DateTimeInterface $dateFin = null;

    public function getIdMembre(): ?int
    {
        return $this->idMembre;
    }

    public function setIdMembre(int $idMembre): self
    {
        $this->idMembre = $idMembre;

        return $this;
    }

    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getAnneeAcademique(): AnneeAcademique
    {
        return $this->anneeAcademique;
    }

    public function setAnneeAcademique(AnneeAcademique $anneeAcademique): self
    {
        $this->anneeAcademique = $anneeAcademique;

        return $this;
    }

    public function getRoleCommission(): RoleCommission
    {
        return $this->roleCommission;
    }

    public function setRoleCommission(RoleCommission $roleCommission): self
    {
        $this->roleCommission = $roleCommission;

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

    public function getDateNomination(): DateTimeInterface
    {
        return $this->dateNomination;
    }

    public function setDateNomination(DateTimeInterface $dateNomination): self
    {
        $this->dateNomination = $dateNomination;

        return $this;
    }

    public function getDateFin(): ?DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }
}
