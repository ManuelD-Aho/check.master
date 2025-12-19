<?php

declare(strict_types=1);

namespace App\Models;

use App\Orm\Model;

/**
 * Modèle Utilisateur
 * 
 * Représente un compte utilisateur du système CheckMaster.
 * Table: utilisateurs
 */
class Utilisateur extends Model
{
    protected string $table = 'utilisateurs';
    protected string $primaryKey = 'id_utilisateur';
    protected array $fillable = [
        'nom_utilisateur',
        'login_utilisateur',
        'mdp_utilisateur',
        'id_type_utilisateur',
        'id_GU',
        'id_niv_acces_donnee',
        'statut_utilisateur',
        'doit_changer_mdp',
        'derniere_connexion',
        'tentatives_echec',
        'verrouille_jusqu_a',
    ];

    /**
     * Statuts possibles
     */
    public const STATUT_ACTIF = 'Actif';
    public const STATUT_INACTIF = 'Inactif';
    public const STATUT_SUSPENDU = 'Suspendu';

    /**
     * Constantes de sécurité
     */
    public const MAX_TENTATIVES_ECHEC = 5;
    public const DUREE_VERROUILLAGE_MINUTES = 15;

    // ===== RELATIONS =====

    /**
     * Retourne le type d'utilisateur
     */
    public function typeUtilisateur(): ?TypeUtilisateur
    {
        return $this->belongsTo(TypeUtilisateur::class, 'id_type_utilisateur', 'id_type_utilisateur');
    }

    /**
     * Retourne le groupe utilisateur principal
     */
    public function groupeUtilisateur(): ?GroupeUtilisateur
    {
        return $this->belongsTo(GroupeUtilisateur::class, 'id_GU', 'id_GU');
    }

    /**
     * Retourne le niveau d'accès aux données
     */
    public function niveauAccesDonnees(): ?NiveauAccesDonnees
    {
        return $this->belongsTo(NiveauAccesDonnees::class, 'id_niv_acces_donnee', 'id_niv_acces_donnee');
    }

    /**
     * Retourne les sessions actives de l'utilisateur
     * @return SessionActive[]
     */
    public function sessionsActives(): array
    {
        return $this->hasMany(SessionActive::class, 'utilisateur_id', 'id_utilisateur');
    }

    /**
     * Retourne les groupes de l'utilisateur
     * @return UtilisateurGroupe[]
     */
    public function utilisateursGroupes(): array
    {
        return $this->hasMany(UtilisateurGroupe::class, 'utilisateur_id', 'id_utilisateur');
    }

    /**
     * Retourne les rôles temporaires actifs
     * @return RoleTemporaire[]
     */
    public function rolesTemporaires(): array
    {
        return $this->hasMany(RoleTemporaire::class, 'utilisateur_id', 'id_utilisateur');
    }

    /**
     * Retourne les codes temporaires
     * @return CodeTemporaire[]
     */
    public function codesTemporaires(): array
    {
        return $this->hasMany(CodeTemporaire::class, 'utilisateur_id', 'id_utilisateur');
    }

    /**
     * Retourne les messages internes envoyés
     * @return MessageInterne[]
     */
    public function messagesEnvoyes(): array
    {
        return $this->hasMany(MessageInterne::class, 'expediteur_id', 'id_utilisateur');
    }

    /**
     * Retourne les messages internes reçus
     * @return MessageInterne[]
     */
    public function messagesRecus(): array
    {
        return $this->hasMany(MessageInterne::class, 'destinataire_id', 'id_utilisateur');
    }

    // ===== MÉTHODES DE RECHERCHE =====

    /**
     * Trouve un utilisateur par son login (email)
     */
    public static function findByLogin(string $login): ?self
    {
        return self::firstWhere(['login_utilisateur' => $login]);
    }

    /**
     * Retourne tous les utilisateurs actifs
     * @return self[]
     */
    public static function actifs(): array
    {
        return self::where(['statut_utilisateur' => self::STATUT_ACTIF]);
    }

    /**
     * Retourne les utilisateurs par groupe
     * @return self[]
     */
    public static function parGroupe(int $groupeId): array
    {
        return self::where(['id_GU' => $groupeId, 'statut_utilisateur' => self::STATUT_ACTIF]);
    }

    /**
     * Recherche d'utilisateurs
     */
    public static function rechercher(string $terme, int $limit = 50): array
    {
        $sql = "SELECT * FROM utilisateurs 
                WHERE statut_utilisateur = 'Actif' AND (
                    nom_utilisateur LIKE :terme OR 
                    login_utilisateur LIKE :terme
                )
                ORDER BY nom_utilisateur
                LIMIT :limit";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindValue('terme', "%{$terme}%", \PDO::PARAM_STR);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function (array $row) {
            $model = new self($row);
            $model->exists = true;
            return $model;
        }, $rows);
    }

    // ===== MÉTHODES D'ÉTAT =====

    /**
     * Vérifie si le compte est actif
     */
    public function estActif(): bool
    {
        return $this->statut_utilisateur === self::STATUT_ACTIF;
    }

    /**
     * Vérifie si le compte est verrouillé
     */
    public function estVerrouille(): bool
    {
        if ($this->verrouille_jusqu_a === null) {
            return false;
        }
        return strtotime($this->verrouille_jusqu_a) > time();
    }

    /**
     * Vérifie si l'utilisateur doit changer son mot de passe
     */
    public function doitChangerMotDePasse(): bool
    {
        return (bool) $this->doit_changer_mdp;
    }

    // ===== MÉTHODES D'AUTHENTIFICATION =====

    /**
     * Incrémente le compteur d'échecs de connexion
     */
    public function incrementerEchecs(): void
    {
        $this->tentatives_echec = ($this->tentatives_echec ?? 0) + 1;

        // Verrouiller automatiquement après MAX_TENTATIVES_ECHEC
        if ($this->tentatives_echec >= self::MAX_TENTATIVES_ECHEC) {
            $this->verrouiller(self::DUREE_VERROUILLAGE_MINUTES);
        }
    }

    /**
     * Réinitialise le compteur d'échecs
     */
    public function reinitialiserEchecs(): void
    {
        $this->tentatives_echec = 0;
        $this->verrouille_jusqu_a = null;
    }

    /**
     * Verrouille le compte pour une durée donnée
     */
    public function verrouiller(int $minutes): void
    {
        $this->verrouille_jusqu_a = date('Y-m-d H:i:s', time() + ($minutes * 60));
    }

    /**
     * Déverrouille le compte
     */
    public function deverrouiller(): void
    {
        $this->verrouille_jusqu_a = null;
        $this->tentatives_echec = 0;
    }

    /**
     * Met à jour la date de dernière connexion
     */
    public function majDerniereConnexion(): void
    {
        $this->derniere_connexion = date('Y-m-d H:i:s');
    }

    /**
     * Change le mot de passe
     */
    public function changerMotDePasse(string $nouveauMdpHash): void
    {
        $this->mdp_utilisateur = $nouveauMdpHash;
        $this->doit_changer_mdp = false;
    }

    // ===== MÉTHODES HELPER =====

    /**
     * Retourne l'ID du groupe utilisateur
     */
    public function getGroupeId(): ?int
    {
        return $this->id_GU !== null ? (int) $this->id_GU : null;
    }

    /**
     * Retourne le nom d'affichage
     */
    public function getNomAffichage(): string
    {
        return $this->nom_utilisateur ?? $this->login_utilisateur ?? 'Utilisateur';
    }

    /**
     * Vérifie si l'utilisateur a un rôle temporaire actif
     */
    public function aRoleTemporaireActif(string $roleCode): bool
    {
        $sql = "SELECT COUNT(*) FROM roles_temporaires 
                WHERE utilisateur_id = :id 
                AND role_code = :role 
                AND actif = 1 
                AND valide_de <= NOW() 
                AND valide_jusqu_a >= NOW()";

        $stmt = self::raw($sql, [
            'id' => $this->getId(),
            'role' => $roleCode,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Retourne les messages non lus
     */
    public function nombreMessagesNonLus(): int
    {
        $sql = "SELECT COUNT(*) FROM messages_internes 
                WHERE destinataire_id = :id AND lu = 0";

        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Déconnecte toutes les sessions actives
     */
    public function deconnecterToutesSessions(): int
    {
        $sql = "DELETE FROM sessions_actives WHERE utilisateur_id = :id";
        $stmt = self::raw($sql, ['id' => $this->getId()]);
        return $stmt->rowCount();
    }

    /**
     * Suspend le compte utilisateur
     */
    public function suspendre(): void
    {
        $this->statut_utilisateur = self::STATUT_SUSPENDU;
        $this->deconnecterToutesSessions();
    }

    /**
     * Active le compte utilisateur
     */
    public function activer(): void
    {
        $this->statut_utilisateur = self::STATUT_ACTIF;
        $this->reinitialiserEchecs();
    }

    /**
     * Désactive le compte utilisateur
     */
    public function desactiver(): void
    {
        $this->statut_utilisateur = self::STATUT_INACTIF;
        $this->deconnecterToutesSessions();
    }
}
