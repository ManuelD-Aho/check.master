<?php

declare(strict_types=1);

namespace App\Entity\Commission;

use App\Entity\Academic\AnneeAcademique;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'session_commission', uniqueConstraints: [new ORM\UniqueConstraint(name: 'uk_session_mois_annee', columns: ['mois_session', 'annee_session'])])]
class SessionCommission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_session', type: 'integer')]
    private ?int $idSession = null;

    #[ORM\ManyToOne(targetEntity: AnneeAcademique::class)]
    #[ORM\JoinColumn(name: 'id_annee_academique', referencedColumnName: 'id_annee_academique', nullable: false)]
    private AnneeAcademique $anneeAcademique;

    #[ORM\Column(name: 'mois_session', type: 'integer')]
    private int $moisSession;

    #[ORM\Column(name: 'annee_session', type: 'integer')]
    private int $anneeSession;

    #[ORM\Column(name: 'libelle_session', type: 'string', length: 100)]
    private string $libelleSession;

    #[ORM\Column(name: 'date_debut', type: 'date')]
    private DateTimeInterface $dateDebut;

    #[ORM\Column(name: 'date_fin', type: 'date')]
    private DateTimeInterface $dateFin;

    #[ORM\Column(name: 'statut_session', type: 'string', enumType: StatutSession::class, length: 20, options: ['default' => 'ouverte'])]
    private StatutSession $statutSession = StatutSession::Ouverte;

    #[ORM\Column(name: 'date_creation', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTimeInterface $dateCreation;

    public function getIdSession(): ?int
    {
        return $this->idSession;
    }

    public function setIdSession(int $idSession): self
    {
        $this->idSession = $idSession;

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

    public function getMoisSession(): int
    {
        return $this->moisSession;
    }

    public function setMoisSession(int $moisSession): self
    {
        $this->moisSession = $moisSession;

        return $this;
    }

    public function getAnneeSession(): int
    {
        return $this->anneeSession;
    }

    public function setAnneeSession(int $anneeSession): self
    {
        $this->anneeSession = $anneeSession;

        return $this;
    }

    public function getLibelleSession(): string
    {
        return $this->libelleSession;
    }

    public function setLibelleSession(string $libelleSession): self
    {
        $this->libelleSession = $libelleSession;

        return $this;
    }

    public function getDateDebut(): DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getStatutSession(): StatutSession
    {
        return $this->statutSession;
    }

    public function setStatutSession(StatutSession $statutSession): self
    {
        $this->statutSession = $statutSession;

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
