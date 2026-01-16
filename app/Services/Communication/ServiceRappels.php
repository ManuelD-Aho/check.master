<?php

declare(strict_types=1);

namespace App\Services\Communication;

use App\Models\Soutenance;
use App\Models\DossierEtudiant;
use App\Models\CommissionSession;
use App\Models\CodeTemporaire;
use App\Services\Security\ServiceAudit;
use App\Orm\Model;

/**
 * Service Rappels
 * 
 * Gestion des rappels automatiques (J-7, J-1, J).
 * Envoi des codes temporaires le jour J.
 * 
 * @see PRD 05 - Communication
 */
class ServiceRappels
{
    /**
     * Types de rappels
     */
    public const RAPPEL_J7 = 'J-7';
    public const RAPPEL_J1 = 'J-1';
    public const RAPPEL_JOUR_J = 'J';

    /**
     * Exécute tous les rappels (à appeler quotidiennement)
     */
    public static function executerTousRappels(): array
    {
        $resultats = [
            'rappels_j7' => 0,
            'rappels_j1' => 0,
            'rappels_jour_j' => 0,
            'codes_envoyes' => 0,
            'erreurs' => 0,
        ];

        // Rappels J-7
        try {
            $resultats['rappels_j7'] = self::envoyerRappelsJ7();
        } catch (\Exception $e) {
            $resultats['erreurs']++;
            error_log('Erreur rappels J-7: ' . $e->getMessage());
        }

        // Rappels J-1
        try {
            $resultats['rappels_j1'] = self::envoyerRappelsJ1();
        } catch (\Exception $e) {
            $resultats['erreurs']++;
            error_log('Erreur rappels J-1: ' . $e->getMessage());
        }

        // Rappels Jour J + codes
        try {
            $rapportsJourJ = self::envoyerRappelsJourJ();
            $resultats['rappels_jour_j'] = $rapportsJourJ['rappels'];
            $resultats['codes_envoyes'] = $rapportsJourJ['codes'];
        } catch (\Exception $e) {
            $resultats['erreurs']++;
            error_log('Erreur rappels jour J: ' . $e->getMessage());
        }

        return $resultats;
    }

    /**
     * Envoie les rappels J-7 pour les soutenances
     */
    public static function envoyerRappelsJ7(): int
    {
        $dateJ7 = date('Y-m-d', strtotime('+7 days'));

        $sql = "SELECT s.*, de.etudiant_id, e.utilisateur_id as etudiant_user_id,
                       e.nom_etu, e.prenom_etu, sa.nom_salle
                FROM soutenances s
                INNER JOIN dossiers_etudiants de ON de.id_dossier = s.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                LEFT JOIN salles sa ON sa.id_salle = s.salle_id
                WHERE s.date_soutenance = :date
                AND s.statut = 'Planifiee'
                AND s.rappel_j7_envoye = 0";

        $stmt = Model::raw($sql, ['date' => $dateJ7]);
        $soutenances = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $count = 0;
        foreach ($soutenances as $soutenance) {
            try {
                // Notifier l'étudiant
                if (!empty($soutenance['etudiant_user_id'])) {
                    ServiceNotification::envoyerParCode(
                        'rappel_soutenance_j7',
                        [(int) $soutenance['etudiant_user_id']],
                        [
                            'etudiant_nom' => $soutenance['nom_etu'] . ' ' . $soutenance['prenom_etu'],
                            'date' => date('d/m/Y', strtotime($soutenance['date_soutenance'])),
                            'heure' => $soutenance['heure_debut'],
                            'salle' => $soutenance['nom_salle'] ?? 'À déterminer',
                        ]
                    );
                }

                // Notifier le jury
                self::notifierJury((int) $soutenance['dossier_id'], 'rappel_soutenance_j7', [
                    'date' => date('d/m/Y', strtotime($soutenance['date_soutenance'])),
                    'heure' => $soutenance['heure_debut'],
                    'etudiant' => $soutenance['nom_etu'] . ' ' . $soutenance['prenom_etu'],
                ]);

                // Marquer le rappel comme envoyé
                self::marquerRappelEnvoye((int) $soutenance['id_soutenance'], 'j7');

                $count++;
            } catch (\Exception $e) {
                error_log('Erreur rappel J-7 soutenance #' . $soutenance['id_soutenance'] . ': ' . $e->getMessage());
            }
        }

        return $count;
    }

    /**
     * Envoie les rappels J-1 pour les soutenances
     */
    public static function envoyerRappelsJ1(): int
    {
        $dateJ1 = date('Y-m-d', strtotime('+1 day'));

        $sql = "SELECT s.*, de.etudiant_id, e.utilisateur_id as etudiant_user_id,
                       e.nom_etu, e.prenom_etu, sa.nom_salle, sa.localisation
                FROM soutenances s
                INNER JOIN dossiers_etudiants de ON de.id_dossier = s.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                LEFT JOIN salles sa ON sa.id_salle = s.salle_id
                WHERE s.date_soutenance = :date
                AND s.statut = 'Planifiee'
                AND s.rappel_j1_envoye = 0";

        $stmt = Model::raw($sql, ['date' => $dateJ1]);
        $soutenances = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $count = 0;
        foreach ($soutenances as $soutenance) {
            try {
                // Notifier l'étudiant (email + messagerie urgente)
                if (!empty($soutenance['etudiant_user_id'])) {
                    ServiceNotification::envoyerUrgent(
                        'rappel_soutenance_j1',
                        [(int) $soutenance['etudiant_user_id']],
                        [
                            'etudiant_nom' => $soutenance['nom_etu'] . ' ' . $soutenance['prenom_etu'],
                            'date' => 'Demain',
                            'heure' => $soutenance['heure_debut'],
                            'salle' => $soutenance['nom_salle'] ?? 'À déterminer',
                            'localisation' => $soutenance['localisation'] ?? '',
                        ]
                    );
                }

                // Notifier le jury avec urgence
                self::notifierJury((int) $soutenance['dossier_id'], 'rappel_soutenance_j1', [
                    'date' => 'Demain',
                    'heure' => $soutenance['heure_debut'],
                    'etudiant' => $soutenance['nom_etu'] . ' ' . $soutenance['prenom_etu'],
                    'salle' => $soutenance['nom_salle'] ?? 'À déterminer',
                ]);

                // Marquer le rappel comme envoyé
                self::marquerRappelEnvoye((int) $soutenance['id_soutenance'], 'j1');

                $count++;
            } catch (\Exception $e) {
                error_log('Erreur rappel J-1 soutenance #' . $soutenance['id_soutenance'] . ': ' . $e->getMessage());
            }
        }

        return $count;
    }

    /**
     * Envoie les rappels du jour J et les codes temporaires
     */
    public static function envoyerRappelsJourJ(): array
    {
        $dateJourJ = date('Y-m-d');

        $sql = "SELECT s.*, de.etudiant_id, de.id_dossier,
                       e.utilisateur_id as etudiant_user_id,
                       e.nom_etu, e.prenom_etu, 
                       sa.nom_salle, sa.localisation,
                       jm.enseignant_id as president_enseignant_id,
                       ens.utilisateur_id as president_user_id,
                       ens.nom_ens as president_nom, ens.prenom_ens as president_prenom
                FROM soutenances s
                INNER JOIN dossiers_etudiants de ON de.id_dossier = s.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                LEFT JOIN salles sa ON sa.id_salle = s.salle_id
                LEFT JOIN jury_membres jm ON jm.dossier_id = de.id_dossier AND jm.role = 'President'
                LEFT JOIN enseignants ens ON ens.id_enseignant = jm.enseignant_id
                WHERE s.date_soutenance = :date
                AND s.statut = 'Planifiee'
                AND s.rappel_jour_j_envoye = 0";

        $stmt = Model::raw($sql, ['date' => $dateJourJ]);
        $soutenances = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $rappels = 0;
        $codes = 0;

        foreach ($soutenances as $soutenance) {
            try {
                // Notifier l'étudiant
                if (!empty($soutenance['etudiant_user_id'])) {
                    ServiceNotification::envoyerUrgent(
                        'rappel_soutenance_jour_j',
                        [(int) $soutenance['etudiant_user_id']],
                        [
                            'etudiant_nom' => $soutenance['nom_etu'] . ' ' . $soutenance['prenom_etu'],
                            'heure' => $soutenance['heure_debut'],
                            'salle' => $soutenance['nom_salle'] ?? 'À déterminer',
                        ]
                    );
                    $rappels++;
                }

                // Envoyer le code temporaire au président du jury
                if (!empty($soutenance['president_user_id'])) {
                    $code = self::genererCodePresident((int) $soutenance['id_soutenance'], (int) $soutenance['president_user_id']);

                    ServiceNotification::envoyerUrgent(
                        'code_president_jury',
                        [(int) $soutenance['president_user_id']],
                        [
                            'president_nom' => $soutenance['president_nom'] . ' ' . $soutenance['president_prenom'],
                            'etudiant' => $soutenance['nom_etu'] . ' ' . $soutenance['prenom_etu'],
                            'code' => $code,
                            'heure' => $soutenance['heure_debut'],
                            'validite' => 'aujourd\'hui uniquement',
                        ]
                    );
                    $codes++;
                }

                // Marquer le rappel comme envoyé
                self::marquerRappelEnvoye((int) $soutenance['id_soutenance'], 'jour_j');
            } catch (\Exception $e) {
                error_log('Erreur rappel jour J soutenance #' . $soutenance['id_soutenance'] . ': ' . $e->getMessage());
            }
        }

        return ['rappels' => $rappels, 'codes' => $codes];
    }

    /**
     * Génère un code temporaire pour le président du jury
     */
    private static function genererCodePresident(int $soutenanceId, int $utilisateurId): string
    {
        // Générer un code à 6 chiffres
        $code = sprintf('%06d', random_int(0, 999999));

        // Calculer l'expiration (fin de la journée)
        $expiration = date('Y-m-d 23:59:59');

        // Hasher le code pour stockage
        $codeHash = password_hash($code, PASSWORD_DEFAULT);

        // Stocker le code
        $codeTemp = new CodeTemporaire([
            'utilisateur_id' => $utilisateurId,
            'type' => 'president_jury',
            'code_hash' => $codeHash,
            'contexte_type' => 'soutenance',
            'contexte_id' => $soutenanceId,
            'expire_le' => $expiration,
            'utilise' => false,
        ]);
        $codeTemp->save();

        ServiceAudit::log('generation_code_president', 'code_temporaire', $codeTemp->getId(), [
            'soutenance_id' => $soutenanceId,
        ]);

        return $code;
    }

    /**
     * Vérifie un code président
     */
    public static function verifierCodePresident(int $soutenanceId, int $utilisateurId, string $code): bool
    {
        $sql = "SELECT * FROM codes_temporaires 
                WHERE utilisateur_id = :user
                AND contexte_type = 'soutenance'
                AND contexte_id = :soutenance
                AND type = 'president_jury'
                AND expire_le >= NOW()
                AND utilise = 0
                ORDER BY created_at DESC
                LIMIT 1";

        $stmt = Model::raw($sql, [
            'user' => $utilisateurId,
            'soutenance' => $soutenanceId,
        ]);

        $codeTemp = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$codeTemp) {
            return false;
        }

        // Vérifier le code
        if (!password_verify($code, $codeTemp['code_hash'])) {
            return false;
        }

        // Marquer le code comme utilisé
        $sql2 = "UPDATE codes_temporaires SET utilise = 1, utilise_le = NOW() WHERE id_code = :id";
        Model::raw($sql2, ['id' => $codeTemp['id_code']]);

        ServiceAudit::log('utilisation_code_president', 'code_temporaire', (int) $codeTemp['id_code']);

        return true;
    }

    /**
     * Notifie les membres du jury
     */
    private static function notifierJury(int $dossierId, string $templateCode, array $variables): void
    {
        $sql = "SELECT ens.utilisateur_id
                FROM jury_membres jm
                INNER JOIN enseignants ens ON ens.id_enseignant = jm.enseignant_id
                WHERE jm.dossier_id = :dossier
                AND jm.statut = 'Accepte'
                AND ens.utilisateur_id IS NOT NULL";

        $stmt = Model::raw($sql, ['dossier' => $dossierId]);
        $juryUsers = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        if (!empty($juryUsers)) {
            ServiceNotification::envoyerParCode($templateCode, $juryUsers, $variables);
        }
    }

    /**
     * Marque un rappel comme envoyé pour une soutenance
     */
    private static function marquerRappelEnvoye(int $soutenanceId, string $typeRappel): void
    {
        $column = match ($typeRappel) {
            'j7' => 'rappel_j7_envoye',
            'j1' => 'rappel_j1_envoye',
            'jour_j' => 'rappel_jour_j_envoye',
            default => throw new \InvalidArgumentException("Type de rappel invalide: {$typeRappel}"),
        };

        $sql = "UPDATE soutenances SET {$column} = 1 WHERE id_soutenance = :id";
        Model::raw($sql, ['id' => $soutenanceId]);
    }

    /**
     * Envoie les rappels pour les sessions de commission
     */
    public static function envoyerRappelsCommission(): int
    {
        $dateJ1 = date('Y-m-d', strtotime('+1 day'));

        $sql = "SELECT cs.*, 
                       GROUP_CONCAT(ens.utilisateur_id) as membres_user_ids
                FROM commission_sessions cs
                LEFT JOIN commission_membres cm ON cm.session_id = cs.id_session
                LEFT JOIN enseignants ens ON ens.id_enseignant = cm.enseignant_id
                WHERE DATE(cs.date_session) = :date
                AND cs.statut = 'Planifiee'
                AND cs.rappel_envoye = 0
                GROUP BY cs.id_session";

        $stmt = Model::raw($sql, ['date' => $dateJ1]);
        $sessions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $count = 0;
        foreach ($sessions as $session) {
            if (empty($session['membres_user_ids'])) {
                continue;
            }

            $membresIds = array_map('intval', explode(',', $session['membres_user_ids']));

            try {
                ServiceNotification::envoyerParCode(
                    'rappel_commission_j1',
                    $membresIds,
                    [
                        'date' => 'Demain',
                        'heure' => $session['heure_debut'] ?? '08:00',
                        'lieu' => $session['lieu'] ?? 'À déterminer',
                    ]
                );

                $sql2 = "UPDATE commission_sessions SET rappel_envoye = 1 WHERE id_session = :id";
                Model::raw($sql2, ['id' => $session['id_session']]);

                $count++;
            } catch (\Exception $e) {
                error_log('Erreur rappel commission #' . $session['id_session'] . ': ' . $e->getMessage());
            }
        }

        return $count;
    }

    /**
     * Nettoie les codes temporaires expirés
     */
    public static function nettoyerCodesExpires(): int
    {
        $sql = "DELETE FROM codes_temporaires WHERE expire_le < NOW()";
        $stmt = Model::raw($sql);
        return $stmt->rowCount();
    }
}
