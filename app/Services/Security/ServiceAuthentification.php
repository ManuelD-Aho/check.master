<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\Utilisateur;
use App\Models\SessionActive;
use App\Models\CodeTemporaire;
use App\Orm\Model;
use Src\Http\Request;

/**
 * Service d'Authentification
 * 
 * Gère la connexion, les sessions, la protection brute-force,
 * et les codes temporaires.
 * 
 * @see PRD RF-001 à RF-005
 * @see Constitution III - Sécurité Par Défaut
 */
class ServiceAuthentification
{
    /**
     * Seuils de protection brute-force
     */
    private const SEUIL_DELAI_1 = 3;   // Après 3 échecs: 1 minute
    private const SEUIL_DELAI_2 = 5;   // Après 5 échecs: 15 minutes
    private const SEUIL_VERROUILLAGE = 10; // Après 10 échecs: verrouillage total

    private const DELAI_NIVEAU_1 = 1;   // 1 minute
    private const DELAI_NIVEAU_2 = 15;  // 15 minutes

    /**
     * Durée de session par défaut (heures)
     */
    private const DUREE_SESSION_HEURES = 8;

    /**
     * Authentifie un utilisateur
     *
     * @return array{success: bool, user?: Utilisateur, token?: string, error?: string}
     */
    public function authentifier(string $email, string $password): array
    {
        // Rechercher l'utilisateur
        $user = Utilisateur::findByLogin($email);

        if ($user === null) {
            ServiceAudit::logLoginEchec($email, 'Utilisateur non trouvé');
            return [
                'success' => false,
                'error' => 'Identifiants incorrects',
            ];
        }

        // Vérifier si le compte est verrouillé
        if ($user->estVerrouille()) {
            $tempsRestant = strtotime($user->verrouille_jusqu_a) - time();
            $minutes = ceil($tempsRestant / 60);

            ServiceAudit::logLoginEchec($email, 'Compte verrouillé');
            return [
                'success' => false,
                'error' => "Compte temporairement bloqué. Réessayez dans {$minutes} minute(s).",
            ];
        }

        // Vérifier si le compte est actif
        if (!$user->estActif()) {
            ServiceAudit::logLoginEchec($email, 'Compte inactif');
            return [
                'success' => false,
                'error' => 'Ce compte est désactivé. Contactez l\'administrateur.',
            ];
        }

        // Vérifier le mot de passe (Argon2id)
        if (!$this->verifierMotDePasse($password, $user->mdp_utilisateur)) {
            return $this->gererEchecConnexion($user, $email);
        }

        // Connexion réussie
        $user->reinitialiserEchecs();
        $user->majDerniereConnexion();
        $user->save();

        // Créer la session
        $token = $this->creerSession($user->getId());

        ServiceAudit::logLogin($user->getId());

        return [
            'success' => true,
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Vérifie un mot de passe contre son hash Argon2id
     */
    public function verifierMotDePasse(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Hash un mot de passe avec Argon2id
     */
    public function hasherMotDePasse(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
        ]);
    }

    /**
     * Gère un échec de connexion (protection brute-force)
     */
    private function gererEchecConnexion(Utilisateur $user, string $email): array
    {
        $user->incrementerEchecs();
        $echecs = (int) $user->tentatives_echec;

        // Appliquer les délais progressifs
        if ($echecs >= self::SEUIL_VERROUILLAGE) {
            // Verrouillage total - requires admin intervention
            $user->verrouiller(60 * 24); // 24 heures
            $user->save();

            ServiceAudit::logLoginEchec($email, "Compte verrouillé après {$echecs} échecs");

            // TODO: Notifier l'admin

            return [
                'success' => false,
                'error' => 'Compte verrouillé suite à trop de tentatives. Contactez l\'administrateur.',
            ];
        } elseif ($echecs >= self::SEUIL_DELAI_2) {
            $user->verrouiller(self::DELAI_NIVEAU_2);
            $user->save();

            ServiceAudit::logLoginEchec($email, "Délai 15 min après {$echecs} échecs");

            return [
                'success' => false,
                'error' => 'Identifiants incorrects. Compte temporairement bloqué - 15 minutes.',
            ];
        } elseif ($echecs >= self::SEUIL_DELAI_1) {
            $user->verrouiller(self::DELAI_NIVEAU_1);
            $user->save();

            ServiceAudit::logLoginEchec($email, "Délai 1 min après {$echecs} échecs");

            return [
                'success' => false,
                'error' => 'Identifiants incorrects. Réessayez dans 1 minute.',
            ];
        }

        $user->save();
        ServiceAudit::logLoginEchec($email, "Mot de passe incorrect ({$echecs} échecs)");

        return [
            'success' => false,
            'error' => 'Identifiants incorrects',
        ];
    }

    /**
     * Crée une nouvelle session pour l'utilisateur
     */
    public function creerSession(int $userId): string
    {
        // Générer un token unique de 128 caractères
        $token = bin2hex(random_bytes(64));

        // Calculer la date d'expiration
        $expireA = date('Y-m-d H:i:s', time() + (self::DUREE_SESSION_HEURES * 3600));

        // Créer la session
        $session = new SessionActive([
            'utilisateur_id' => $userId,
            'token_session' => $token,
            'ip_adresse' => Request::ip(),
            'user_agent' => substr(Request::userAgent(), 0, 500), // Limiter la longueur
            'derniere_activite' => date('Y-m-d H:i:s'),
            'expire_a' => $expireA,
        ]);
        $session->save();

        return $token;
    }

    /**
     * Valide une session et retourne l'utilisateur associé
     */
    public function validerSession(string $token): ?Utilisateur
    {
        $session = SessionActive::findByToken($token);

        if ($session === null || !$session->estValide()) {
            return null;
        }

        // Mettre à jour la dernière activité
        $session->majDerniereActivite();
        $session->save();

        return $session->getUtilisateur();
    }

    /**
     * Supprime une session (déconnexion)
     */
    public function supprimerSession(string $token): bool
    {
        $session = SessionActive::findByToken($token);

        if ($session === null) {
            return false;
        }

        $userId = (int) $session->utilisateur_id;
        $result = $session->delete();

        if ($result) {
            ServiceAudit::logLogout($userId);
        }

        return $result;
    }

    /**
     * Force la déconnexion d'une session spécifique (admin)
     */
    public function forcerDeconnexion(int $sessionId, int $adminId): bool
    {
        $session = SessionActive::find($sessionId);

        if ($session === null) {
            return false;
        }

        $userId = (int) $session->utilisateur_id;
        $result = $session->delete();

        if ($result) {
            ServiceAudit::logDeconnexionForcee($userId, $sessionId, $adminId);
        }

        return $result;
    }

    /**
     * Supprime toutes les sessions d'un utilisateur
     */
    public function supprimerToutesSessions(int $userId): int
    {
        return SessionActive::supprimerPourUtilisateur($userId);
    }

    /**
     * Génère un code temporaire pour le Président Jury
     */
    public function genererCodePresidentJury(int $userId, int $soutenanceId): string
    {
        // Révoquer les anciens codes non utilisés
        $anciensCodesRévoques = $this->revoquerAnciensCodesUtilisateur($userId, CodeTemporaire::TYPE_PRESIDENT_JURY);

        // Générer le nouveau code
        $code = CodeTemporaire::genererCode();
        $hash = CodeTemporaire::hasherCode($code);

        // Validité: de 06h00 à 23h59 du jour courant
        $aujourdhui = date('Y-m-d');
        $valideDe = $aujourdhui . ' 06:00:00';
        $valideJusqua = $aujourdhui . ' 23:59:59';

        $codeTemp = new CodeTemporaire([
            'utilisateur_id' => $userId,
            'soutenance_id' => $soutenanceId,
            'code_hash' => $hash,
            'type' => CodeTemporaire::TYPE_PRESIDENT_JURY,
            'valide_de' => $valideDe,
            'valide_jusqu_a' => $valideJusqua,
            'utilise' => false,
        ]);
        $codeTemp->save();

        return $code;
    }

    /**
     * Valide un code temporaire
     */
    public function validerCodeTemporaire(string $code, string $type, int $userId): ?CodeTemporaire
    {
        // Récupérer les codes valides de l'utilisateur pour ce type
        $codes = CodeTemporaire::where([
            'utilisateur_id' => $userId,
            'type' => $type,
            'utilise' => 0,
        ]);

        foreach ($codes as $codeTemp) {
            if ($codeTemp->estValide() && CodeTemporaire::verifierCode($code, $codeTemp->code_hash)) {
                return $codeTemp;
            }
        }

        return null;
    }

    /**
     * Révoque les anciens codes d'un utilisateur pour un type donné
     */
    private function revoquerAnciensCodesUtilisateur(int $userId, string $type): int
    {
        $sql = "UPDATE codes_temporaires SET utilise = 1 
                WHERE utilisateur_id = :user_id AND type = :type AND utilise = 0";
        $stmt = Model::raw($sql, ['user_id' => $userId, 'type' => $type]);
        return $stmt->rowCount();
    }

    /**
     * Nettoie les sessions expirées (à appeler via cron)
     */
    public function nettoyerSessionsExpirees(): int
    {
        return SessionActive::nettoyerExpirees();
    }

    /**
     * Génère un mot de passe temporaire sécurisé
     */
    public function genererMotDePasseTemporaire(int $longueur = 12): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%';
        $password = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $longueur; $i++) {
            $password .= $chars[random_int(0, $max)];
        }

        return $password;
    }
}
