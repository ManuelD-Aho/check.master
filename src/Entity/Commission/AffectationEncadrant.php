<?php

declare(strict_types=1);

namespace App\Entity\Commission;

use App\Entity\Report\Rapport;
use App\Entity\Staff\Enseignant;
use App\Entity\User\Utilisateur;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'affectation_encadrant', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uk_affectation_rapport_role', columns: ['id_rapport', 'role_encadrement'])])]
class AffectationEncadrant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_affectation', type: 'integer')]
    private ?int $idAffectation = null;

    #[ORM\ManyToOne(targetEntity: Rapport::class)]
    #[ORM\JoinColumn(name: 'id_rapport', referencedColumnName: 'id_rapport', nullable: false)]
    private Rapport $rapport;

    #[ORM\ManyToOne(targetEntity: Enseignant::class)]
    #[ORM\JoinColumn(name: 'matricule_enseignant', referencedColumnName: 'matricule_enseignant', nullable: false)]
    private Enseignant $enseignant;

    #[ORM\Column(name: 'role_encadrement', type: 'string', enumType: RoleEncadrement::class, length: 30)]
    private RoleEncadrement $roleEncadrement;

    #[ORM\Column(name: 'date_affectation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeInterface $dateAffectation;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id_affecteur', referencedColumnName: 'id_utilisateur', nullable: false)]
    private Utilisateur $affecteur;

    #[ORM\Column(name: 'commentaire', type: 'text', nullable: true)]
    private ?string $commentaire = null;

    public function getIdAffectation(): ?int
    {
        return $this->idAffectation;
    }

    public function setIdAffectation(int $idAffectation): self
    {
        $this->idAffectation = $idAffectation;

        return $this;
    }

    public function getRapport(): Rapport
    {
        return $this->rapport;
    }

    public function setRapport(Rapport $rapport): self
    {
        $this->rapport = $rapport;

        return $this;
    }

    public function getEnseignant(): Enseignant
    {
        return $this->enseignant;
    }

    public function setEnseignant(Enseignant $enseignant): self
    {
        $this->enseignant = $enseignant;

        return $this;
    }

    public function getRoleEncadrement(): RoleEncadrement
    {
        return $this->roleEncadrement;
    }

    public function setRoleEncadrement(RoleEncadrement $roleEncadrement): self
    {
        $this->roleEncadrement = $roleEncadrement;

        return $this;
    }

    public function getDateAffectation(): DateTimeInterface
    {
        return $this->dateAffectation;
    }

    public function setDateAffectation(DateTimeInterface $dateAffectation): self
    {
        $this->dateAffectation = $dateAffectation;

        return $this;
    }

    public function getAffecteur(): Utilisateur
    {
        return $this->affecteur;
    }

    public function setAffecteur(Utilisateur $affecteur): self
    {
        $this->affecteur = $affecteur;

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
}
