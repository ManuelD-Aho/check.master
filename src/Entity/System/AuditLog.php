<?php
declare(strict_types=1);

namespace App\Entity\System;

use App\Entity\User\Utilisateur;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'audit_log', indexes: [new ORM\Index(name: 'idx_audit_date', columns: ['date_creation']), new ORM\Index(name: 'idx_audit_utilisateur', columns: ['id_utilisateur']), new ORM\Index(name: 'idx_audit_action', columns: ['action'])])]
class AuditLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_audit', type: 'integer')]
    private ?int $idAudit = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'auditLogs')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_utilisateur', nullable: true)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(name: 'action', type: 'string', length: 100)]
    private string $action;

    #[ORM\Column(name: 'statut_action', enumType: AuditStatutAction::class)]
    private AuditStatutAction $statutAction;

    #[ORM\Column(name: 'table_concernee', type: 'string', length: 100, nullable: true)]
    private ?string $tableConcernee = null;

    #[ORM\Column(name: 'id_enregistrement', type: 'integer', nullable: true)]
    private ?int $idEnregistrement = null;

    #[ORM\Column(name: 'donnees_avant', type: 'json', nullable: true)]
    private ?array $donneesAvant = null;

    #[ORM\Column(name: 'donnees_apres', type: 'json', nullable: true)]
    private ?array $donneesApres = null;

    #[ORM\Column(name: 'adresse_ip', type: 'string', length: 45, nullable: true)]
    private ?string $adresseIp = null;

    #[ORM\Column(name: 'user_agent', type: 'string', length: 255, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(name: 'details', type: 'text', nullable: true)]
    private ?string $details = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    public function getIdAudit(): ?int
    {
        return $this->idAudit;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
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

    public function getStatutAction(): AuditStatutAction
    {
        return $this->statutAction;
    }

    public function setStatutAction(AuditStatutAction $statutAction): self
    {
        $this->statutAction = $statutAction;

        return $this;
    }

    public function getTableConcernee(): ?string
    {
        return $this->tableConcernee;
    }

    public function setTableConcernee(?string $tableConcernee): self
    {
        $this->tableConcernee = $tableConcernee;

        return $this;
    }

    public function getIdEnregistrement(): ?int
    {
        return $this->idEnregistrement;
    }

    public function setIdEnregistrement(?int $idEnregistrement): self
    {
        $this->idEnregistrement = $idEnregistrement;

        return $this;
    }

    public function getDonneesAvant(): ?array
    {
        return $this->donneesAvant;
    }

    public function setDonneesAvant(?array $donneesAvant): self
    {
        $this->donneesAvant = $donneesAvant;

        return $this;
    }

    public function getDonneesApres(): ?array
    {
        return $this->donneesApres;
    }

    public function setDonneesApres(?array $donneesApres): self
    {
        $this->donneesApres = $donneesApres;

        return $this;
    }

    public function getAdresseIp(): ?string
    {
        return $this->adresseIp;
    }

    public function setAdresseIp(?string $adresseIp): self
    {
        $this->adresseIp = $adresseIp;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

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
}
