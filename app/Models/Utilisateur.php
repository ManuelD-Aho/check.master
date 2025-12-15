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
     * Trouve un utilisateur par son login (email)
     */
    public static function findByLogin(string $login): ?self
    {
        return self::firstWhere(['login_utilisateur' => $login]);
    }

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
     * Incrémente le compteur d'échecs de connexion
     */
    public function incrementerEchecs(): void
    {
        $this->tentatives_echec = ($this->tentatives_echec ?? 0) + 1;
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
     * Met à jour la date de dernière connexion
     */
    public function majDerniereConnexion(): void
    {
        $this->derniere_connexion = date('Y-m-d H:i:s');
    }

    /**
     * Vérifie si l'utilisateur doit changer son mot de passe
     */
    public function doitChangerMotDePasse(): bool
    {
        return (bool) $this->doit_changer_mdp;
    }

    /**
     * Retourne l'ID du groupe utilisateur
     */
    public function getGroupeId(): ?int
    {
        return $this->id_GU !== null ? (int) $this->id_GU : null;
    }
}
