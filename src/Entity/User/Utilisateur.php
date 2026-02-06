<?php
declare(strict_types=1);

namespace App\Entity\User;

use App\Entity\Student\Etudiant;
use App\Entity\System\AppSetting;
use App\Entity\System\AuditLog;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'utilisateur', indexes: [new ORM\Index(name: 'idx_utilisateur_statut', columns: ['statut_utilisateur']), new ORM\Index(name: 'idx_utilisateur_groupe', columns: ['id_groupe_utilisateur'])])]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_utilisateur', type: 'integer')]
    private ?int $idUtilisateur = null;

    #[ORM\Column(name: 'login_utilisateur', type: 'string', length: 100, unique: true)]
    private string $loginUtilisateur;

    #[ORM\Column(name: 'mot_de_passe_hash', type: 'string', length: 255)]
    private string $motDePasseHash;

    #[ORM\Column(name: 'email_utilisateur', type: 'string', length: 255)]
    private string $emailUtilisateur;

    #[ORM\Column(name: 'nom_complet', type: 'string', length: 200)]
    private string $nomComplet;

    #[ORM\ManyToOne(targetEntity: TypeUtilisateur::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'id_type_utilisateur', referencedColumnName: 'id_type_utilisateur')]
    private TypeUtilisateur $typeUtilisateur;

    #[ORM\ManyToOne(targetEntity: GroupeUtilisateur::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'id_groupe_utilisateur', referencedColumnName: 'id_groupe_utilisateur')]
    private GroupeUtilisateur $groupeUtilisateur;

    #[ORM\ManyToOne(targetEntity: NiveauAccesDonnees::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'id_niveau_acces', referencedColumnName: 'id_niveau_acces')]
    private NiveauAccesDonnees $niveauAcces;

    #[ORM\ManyToOne(targetEntity: 'App\\Entity\\Student\\Etudiant')]
    #[ORM\JoinColumn(name: 'matricule_etudiant', referencedColumnName: 'matricule_etudiant', nullable: true, unique: true)]
    private ?object $etudiant = null;

    #[ORM\ManyToOne(targetEntity: 'App\\Entity\\Staff\\Enseignant')]
    #[ORM\JoinColumn(name: 'matricule_enseignant', referencedColumnName: 'matricule_enseignant', nullable: true, unique: true)]
    private ?object $enseignant = null;

    #[ORM\ManyToOne(targetEntity: 'App\\Entity\\Staff\\PersonnelAdministratif')]
    #[ORM\JoinColumn(name: 'matricule_personnel', referencedColumnName: 'matricule_personnel', nullable: true, unique: true)]
    private ?object $personnelAdministratif = null;

    #[ORM\Column(name: 'statut_utilisateur', enumType: UtilisateurStatut::class)]
    private UtilisateurStatut $statutUtilisateur;

    #[ORM\Column(name: 'secret_2fa', type: 'string', length: 255, nullable: true)]
    private ?string $secret2fa = null;

    #[ORM\Column(name: 'is_2fa_enabled', type: 'boolean')]
    private bool $is2faEnabled = false;

    #[ORM\Column(name: 'codes_recuperation_2fa', type: 'text', nullable: true)]
    private ?string $codesRecuperation2fa = null;

    #[ORM\Column(name: 'premiere_connexion', type: 'boolean')]
    private bool $premiereConnexion = true;

    #[ORM\Column(name: 'derniere_connexion', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $derniereConnexion = null;

    #[ORM\Column(name: 'tentatives_connexion', type: 'integer')]
    private int $tentativesConnexion = 0;

    #[ORM\Column(name: 'date_blocage', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateBlocage = null;

    #[ORM\Column(name: 'token_reinitialisation', type: 'string', length: 255, nullable: true)]
    private ?string $tokenReinitialisation = null;

    #[ORM\Column(name: 'expiration_token', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $expirationToken = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(name: 'date_modification', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateModification;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: AuditLog::class)]
    private Collection $auditLogs;

    #[ORM\OneToMany(mappedBy: 'updatedBy', targetEntity: AppSetting::class)]
    private Collection $appSettingsUpdated;

    public function __construct()
    {
        $this->auditLogs = new ArrayCollection();
        $this->appSettingsUpdated = new ArrayCollection();
    }

    public function getIdUtilisateur(): ?int
    {
        return $this->idUtilisateur;
    }

    public function getLoginUtilisateur(): string
    {
        return $this->loginUtilisateur;
    }

    public function setLoginUtilisateur(string $loginUtilisateur): self
    {
        $this->loginUtilisateur = $loginUtilisateur;

        return $this;
    }

    public function getMotDePasseHash(): string
    {
        return $this->motDePasseHash;
    }

    public function setMotDePasseHash(string $motDePasseHash): self
    {
        $this->motDePasseHash = $motDePasseHash;

        return $this;
    }

    public function getEmailUtilisateur(): string
    {
        return $this->emailUtilisateur;
    }

    public function setEmailUtilisateur(string $emailUtilisateur): self
    {
        $this->emailUtilisateur = $emailUtilisateur;

        return $this;
    }

    public function getNomComplet(): string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    public function getTypeUtilisateur(): TypeUtilisateur
    {
        return $this->typeUtilisateur;
    }

    public function setTypeUtilisateur(TypeUtilisateur $typeUtilisateur): self
    {
        $this->typeUtilisateur = $typeUtilisateur;

        return $this;
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

    public function getNiveauAcces(): NiveauAccesDonnees
    {
        return $this->niveauAcces;
    }

    public function setNiveauAcces(NiveauAccesDonnees $niveauAcces): self
    {
        $this->niveauAcces = $niveauAcces;

        return $this;
    }

    public function getEtudiant(): ?object
    {
        return $this->etudiant;
    }

    public function getMatriculeEtudiant(): ?string
    {
        if (!$this->etudiant instanceof Etudiant) {
            return null;
        }

        return $this->etudiant->getMatriculeEtudiant();
    }

    public function setEtudiant(?object $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getEnseignant(): ?object
    {
        return $this->enseignant;
    }

    public function setEnseignant(?object $enseignant): self
    {
        $this->enseignant = $enseignant;

        return $this;
    }

    public function getMatriculeEnseignant(): ?string
    {
        if ($this->enseignant === null) {
            return null;
        }

        if (method_exists($this->enseignant, 'getMatriculeEnseignant')) {
            return $this->enseignant->getMatriculeEnseignant();
        }

        return null;
    }

    public function getIdGroupeUtilisateur(): ?int
    {
        return $this->groupeUtilisateur->getIdGroupeUtilisateur();
    }

    public function getCodeGroupe(): ?string
    {
        return $this->groupeUtilisateur->getCodeGroupe();
    }

    public function getPersonnelAdministratif(): ?object
    {
        return $this->personnelAdministratif;
    }

    public function setPersonnelAdministratif(?object $personnelAdministratif): self
    {
        $this->personnelAdministratif = $personnelAdministratif;

        return $this;
    }

    public function getStatutUtilisateur(): UtilisateurStatut
    {
        return $this->statutUtilisateur;
    }

    public function setStatutUtilisateur(UtilisateurStatut $statutUtilisateur): self
    {
        $this->statutUtilisateur = $statutUtilisateur;

        return $this;
    }

    public function getSecret2fa(): ?string
    {
        return $this->secret2fa;
    }

    public function setSecret2fa(?string $secret2fa): self
    {
        $this->secret2fa = $secret2fa;

        return $this;
    }

    public function is2faEnabled(): bool
    {
        return $this->is2faEnabled;
    }

    public function setIs2faEnabled(bool $is2faEnabled): self
    {
        $this->is2faEnabled = $is2faEnabled;

        return $this;
    }

    public function getCodesRecuperation2fa(): ?string
    {
        return $this->codesRecuperation2fa;
    }

    public function setCodesRecuperation2fa(?string $codesRecuperation2fa): self
    {
        $this->codesRecuperation2fa = $codesRecuperation2fa;

        return $this;
    }

    public function isPremiereConnexion(): bool
    {
        return $this->premiereConnexion;
    }

    public function setPremiereConnexion(bool $premiereConnexion): self
    {
        $this->premiereConnexion = $premiereConnexion;

        return $this;
    }

    public function getDerniereConnexion(): ?\DateTimeImmutable
    {
        return $this->derniereConnexion;
    }

    public function setDerniereConnexion(?\DateTimeImmutable $derniereConnexion): self
    {
        $this->derniereConnexion = $derniereConnexion;

        return $this;
    }

    public function getTentativesConnexion(): int
    {
        return $this->tentativesConnexion;
    }

    public function setTentativesConnexion(int $tentativesConnexion): self
    {
        $this->tentativesConnexion = $tentativesConnexion;

        return $this;
    }

    public function getDateBlocage(): ?\DateTimeImmutable
    {
        return $this->dateBlocage;
    }

    public function setDateBlocage(?\DateTimeImmutable $dateBlocage): self
    {
        $this->dateBlocage = $dateBlocage;

        return $this;
    }

    public function getTokenReinitialisation(): ?string
    {
        return $this->tokenReinitialisation;
    }

    public function setTokenReinitialisation(?string $tokenReinitialisation): self
    {
        $this->tokenReinitialisation = $tokenReinitialisation;

        return $this;
    }

    public function getExpirationToken(): ?\DateTimeImmutable
    {
        return $this->expirationToken;
    }

    public function setExpirationToken(?\DateTimeImmutable $expirationToken): self
    {
        $this->expirationToken = $expirationToken;

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

    public function getAuditLogs(): Collection
    {
        return $this->auditLogs;
    }

    public function addAuditLog(AuditLog $auditLog): self
    {
        if (!$this->auditLogs->contains($auditLog)) {
            $this->auditLogs->add($auditLog);
            $auditLog->setUtilisateur($this);
        }

        return $this;
    }

    public function removeAuditLog(AuditLog $auditLog): self
    {
        if ($this->auditLogs->removeElement($auditLog)) {
            if ($auditLog->getUtilisateur() === $this) {
                $auditLog->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getAppSettingsUpdated(): Collection
    {
        return $this->appSettingsUpdated;
    }

    public function addAppSettingUpdated(AppSetting $appSetting): self
    {
        if (!$this->appSettingsUpdated->contains($appSetting)) {
            $this->appSettingsUpdated->add($appSetting);
            $appSetting->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeAppSettingUpdated(AppSetting $appSetting): self
    {
        if ($this->appSettingsUpdated->removeElement($appSetting)) {
            if ($appSetting->getUpdatedBy() === $this) {
                $appSetting->setUpdatedBy(null);
            }
        }

        return $this;
    }
}
