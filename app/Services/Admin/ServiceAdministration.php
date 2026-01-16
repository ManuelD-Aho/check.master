<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\ConfigurationSysteme;
use App\Models\Reclamation;
use App\Models\Utilisateur;
use App\Models\AnneeAcademique;
use App\Services\Security\ServiceAudit;
use Src\Exceptions\ValidationException;
use Src\Exceptions\NotFoundException;
use App\Orm\Model;

/**
 * Service Administration
 * 
 * Gestion centralisée de l'administration du système:
 * - Configuration système
 * - Réclamations
 * - Maintenance
 * - Import/Export
 * 
 * @see PRD 08 - Administration
 */
class ServiceAdministration
{
    // =========================================================================
    // CONFIGURATION SYSTÈME
    // =========================================================================

    /**
     * Récupère une valeur de configuration
     */
    public function getConfig(string $cle, mixed $default = null): mixed
    {
        return ConfigurationSysteme::get($cle, $default);
    }

    /**
     * Définit une valeur de configuration
     */
    public function setConfig(string $cle, mixed $valeur, int $modifiePar): void
    {
        $ancienneValeur = ConfigurationSysteme::get($cle);
        ConfigurationSysteme::set($cle, $valeur);

        ServiceAudit::log('modification_config', 'configuration', null, [
            'cle' => $cle,
            'ancienne_valeur' => $ancienneValeur,
            'nouvelle_valeur' => $valeur,
        ]);
    }

    /**
     * Récupère toutes les configurations
     */
    public function getAllConfigs(): array
    {
        return ConfigurationSysteme::toutes();
    }

    /**
     * Récupère les configurations par groupe
     */
    public function getConfigsByGroupe(string $groupe): array
    {
        $configs = ConfigurationSysteme::parGroupe($groupe);
        $result = [];
        
        foreach ($configs as $config) {
            $result[$config->cle_config] = [
                'valeur' => $config->getValeurTypee(),
                'type' => $config->type_valeur,
                'description' => $config->description,
                'modifiable_ui' => (bool) $config->modifiable_ui,
            ];
        }
        
        return $result;
    }

    /**
     * Récupère les groupes de configuration
     */
    public function getGroupesConfig(): array
    {
        return ConfigurationSysteme::getGroupes();
    }

    /**
     * Récupère les configurations modifiables via l'UI
     */
    public function getConfigsModifiablesUI(): array
    {
        $configs = ConfigurationSysteme::modifiablesUI();
        $result = [];
        
        foreach ($configs as $config) {
            $groupe = $config->groupe_config ?? 'general';
            if (!isset($result[$groupe])) {
                $result[$groupe] = [];
            }
            
            $result[$groupe][] = [
                'cle' => $config->cle_config,
                'valeur' => $config->getValeurTypee(),
                'type' => $config->type_valeur,
                'description' => $config->description,
            ];
        }
        
        return $result;
    }

    // =========================================================================
    // RÉCLAMATIONS
    // =========================================================================

    /**
     * Crée une nouvelle réclamation
     */
    public function creerReclamation(array $donnees, int $etudiantId): Reclamation
    {
        if (empty($donnees['sujet']) || empty($donnees['description'])) {
            throw new ValidationException('Le sujet et la description sont obligatoires');
        }

        $reclamation = new Reclamation([
            'etudiant_id' => $etudiantId,
            'type_reclamation' => $donnees['type_reclamation'] ?? 'Autre',
            'sujet' => $donnees['sujet'],
            'description' => $donnees['description'],
            'entite_concernee_id' => $donnees['entite_concernee_id'] ?? null,
            'statut' => 'En_attente',
        ]);
        $reclamation->save();

        ServiceAudit::logCreation('reclamation', $reclamation->getId(), $donnees);

        return $reclamation;
    }

    /**
     * Prend en charge une réclamation
     */
    public function prendreEnChargeReclamation(int $reclamationId, int $utilisateurId): Reclamation
    {
        $reclamation = Reclamation::find($reclamationId);
        if ($reclamation === null) {
            throw new NotFoundException('Réclamation non trouvée');
        }

        if ($reclamation->statut !== 'En_attente') {
            throw new ValidationException('Cette réclamation est déjà prise en charge ou traitée');
        }

        $reclamation->statut = 'En_cours';
        $reclamation->prise_en_charge_par = $utilisateurId;
        $reclamation->prise_en_charge_le = date('Y-m-d H:i:s');
        $reclamation->save();

        ServiceAudit::log('prise_en_charge_reclamation', 'reclamation', $reclamationId);

        return $reclamation;
    }

    /**
     * Résout une réclamation
     */
    public function resoudreReclamation(
        int $reclamationId,
        string $resolution,
        int $utilisateurId
    ): Reclamation {
        $reclamation = Reclamation::find($reclamationId);
        if ($reclamation === null) {
            throw new NotFoundException('Réclamation non trouvée');
        }

        if ($reclamation->statut === 'Resolue' || $reclamation->statut === 'Rejetee') {
            throw new ValidationException('Cette réclamation est déjà traitée');
        }

        $reclamation->statut = 'Resolue';
        $reclamation->resolution = $resolution;
        $reclamation->resolue_par = $utilisateurId;
        $reclamation->resolue_le = date('Y-m-d H:i:s');
        $reclamation->save();

        ServiceAudit::log('resolution_reclamation', 'reclamation', $reclamationId, [
            'resolution' => $resolution,
        ]);

        return $reclamation;
    }

    /**
     * Rejette une réclamation
     */
    public function rejeterReclamation(
        int $reclamationId,
        string $motif,
        int $utilisateurId
    ): Reclamation {
        $reclamation = Reclamation::find($reclamationId);
        if ($reclamation === null) {
            throw new NotFoundException('Réclamation non trouvée');
        }

        if ($reclamation->statut === 'Resolue' || $reclamation->statut === 'Rejetee') {
            throw new ValidationException('Cette réclamation est déjà traitée');
        }

        $reclamation->statut = 'Rejetee';
        $reclamation->motif_rejet = $motif;
        $reclamation->resolue_par = $utilisateurId;
        $reclamation->resolue_le = date('Y-m-d H:i:s');
        $reclamation->save();

        ServiceAudit::log('rejet_reclamation', 'reclamation', $reclamationId, [
            'motif' => $motif,
        ]);

        return $reclamation;
    }

    /**
     * Recherche des réclamations avec filtres
     */
    public function rechercherReclamations(
        ?string $statut = null,
        ?string $type = null,
        ?int $etudiantId = null,
        int $page = 1,
        int $parPage = 20
    ): array {
        $offset = ($page - 1) * $parPage;
        $conditions = [];
        $params = [];

        if ($statut !== null) {
            $conditions[] = 'statut = :statut';
            $params['statut'] = $statut;
        }

        if ($type !== null) {
            $conditions[] = 'type_reclamation = :type';
            $params['type'] = $type;
        }

        if ($etudiantId !== null) {
            $conditions[] = 'etudiant_id = :etudiant';
            $params['etudiant'] = $etudiantId;
        }

        $whereClause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

        // Compter
        $countSql = "SELECT COUNT(*) FROM reclamations {$whereClause}";
        $stmt = Model::raw($countSql, $params);
        $total = (int) $stmt->fetchColumn();

        // Récupérer avec jointure étudiant
        $sql = "SELECT r.*, e.nom_etu, e.prenom_etu, e.num_etu
                FROM reclamations r
                INNER JOIN etudiants e ON e.id_etudiant = r.etudiant_id
                {$whereClause}
                ORDER BY r.created_at DESC
                LIMIT {$parPage} OFFSET {$offset}";
        $stmt = Model::raw($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'reclamations' => $rows,
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil($total / $parPage),
                'perPage' => $parPage,
                'totalItems' => $total,
            ],
        ];
    }

    /**
     * Statistiques des réclamations
     */
    public function statistiquesReclamations(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'En_attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN statut = 'En_cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN statut = 'Resolue' THEN 1 ELSE 0 END) as resolues,
                    SUM(CASE WHEN statut = 'Rejetee' THEN 1 ELSE 0 END) as rejetees
                FROM reclamations";

        $stmt = Model::raw($sql, []);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Par type
        $sqlType = "SELECT type_reclamation, COUNT(*) as total
                    FROM reclamations 
                    GROUP BY type_reclamation";
        $stmtType = Model::raw($sqlType, []);
        $parType = $stmtType->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'general' => $stats,
            'par_type' => $parType,
        ];
    }

    // =========================================================================
    // MAINTENANCE
    // =========================================================================

    /**
     * Active le mode maintenance
     */
    public function activerMaintenance(string $message, int $activePar): void
    {
        $this->setConfig('app.maintenance.enabled', true, $activePar);
        $this->setConfig('app.maintenance.message', $message, $activePar);

        ServiceAudit::log('activation_maintenance', 'systeme', null, [
            'message' => $message,
        ]);
    }

    /**
     * Désactive le mode maintenance
     */
    public function desactiverMaintenance(int $desactivePar): void
    {
        $this->setConfig('app.maintenance.enabled', false, $desactivePar);
        $this->setConfig('app.maintenance.message', '', $desactivePar);

        ServiceAudit::log('desactivation_maintenance', 'systeme', null);
    }

    /**
     * Vérifie si le mode maintenance est actif
     */
    public function estEnMaintenance(): bool
    {
        return (bool) $this->getConfig('app.maintenance.enabled', false);
    }

    /**
     * Récupère le message de maintenance
     */
    public function getMessageMaintenance(): string
    {
        return (string) $this->getConfig('app.maintenance.message', '');
    }

    // =========================================================================
    // IMPORT/EXPORT
    // =========================================================================

    /**
     * Enregistre un import
     */
    public function enregistrerImport(
        string $type,
        string $fichier,
        int $lignesTotal,
        int $lignesReussies,
        array $erreurs,
        int $importePar
    ): int {
        $sql = "INSERT INTO imports_historiques 
                (type_import, fichier_nom, nb_lignes_totales, nb_lignes_reussies, nb_lignes_erreurs, erreurs_json, importe_par)
                VALUES (:type, :fichier, :total, :reussies, :erreurs_nb, :erreurs, :par)";

        Model::raw($sql, [
            'type' => $type,
            'fichier' => $fichier,
            'total' => $lignesTotal,
            'reussies' => $lignesReussies,
            'erreurs_nb' => count($erreurs),
            'erreurs' => json_encode($erreurs),
            'par' => $importePar,
        ]);

        ServiceAudit::log('import', 'import', null, [
            'type' => $type,
            'fichier' => $fichier,
            'reussies' => $lignesReussies,
            'erreurs' => count($erreurs),
        ]);

        // Récupérer l'ID inséré
        $stmt = Model::raw("SELECT LAST_INSERT_ID()", []);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Récupère l'historique des imports
     */
    public function getHistoriqueImports(int $page = 1, int $parPage = 20): array
    {
        $offset = ($page - 1) * $parPage;

        $countSql = "SELECT COUNT(*) FROM imports_historiques";
        $stmt = Model::raw($countSql, []);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT ih.*, u.nom_utilisateur
                FROM imports_historiques ih
                LEFT JOIN utilisateurs u ON u.id_utilisateur = ih.importe_par
                ORDER BY ih.created_at DESC
                LIMIT {$parPage} OFFSET {$offset}";
        $stmt = Model::raw($sql, []);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'imports' => $rows,
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil($total / $parPage),
                'perPage' => $parPage,
                'totalItems' => $total,
            ],
        ];
    }

    // =========================================================================
    // AUDIT
    // =========================================================================

    /**
     * Récupère les logs d'audit avec filtres
     */
    public function getLogsAudit(
        ?string $action = null,
        ?string $entite = null,
        ?int $utilisateurId = null,
        ?\DateTime $dateDebut = null,
        ?\DateTime $dateFin = null,
        int $page = 1,
        int $parPage = 50
    ): array {
        $offset = ($page - 1) * $parPage;
        $conditions = [];
        $params = [];

        if ($action !== null) {
            $conditions[] = 'action = :action';
            $params['action'] = $action;
        }

        if ($entite !== null) {
            $conditions[] = 'entite_type = :entite';
            $params['entite'] = $entite;
        }

        if ($utilisateurId !== null) {
            $conditions[] = 'utilisateur_id = :user';
            $params['user'] = $utilisateurId;
        }

        if ($dateDebut !== null) {
            $conditions[] = 'created_at >= :debut';
            $params['debut'] = $dateDebut->format('Y-m-d H:i:s');
        }

        if ($dateFin !== null) {
            $conditions[] = 'created_at <= :fin';
            $params['fin'] = $dateFin->format('Y-m-d H:i:s');
        }

        $whereClause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

        $countSql = "SELECT COUNT(*) FROM pister {$whereClause}";
        $stmt = Model::raw($countSql, $params);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT p.*, u.nom_utilisateur, u.login_utilisateur
                FROM pister p
                LEFT JOIN utilisateurs u ON u.id_utilisateur = p.utilisateur_id
                {$whereClause}
                ORDER BY p.created_at DESC
                LIMIT {$parPage} OFFSET {$offset}";
        $stmt = Model::raw($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'logs' => $rows,
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil($total / $parPage),
                'perPage' => $parPage,
                'totalItems' => $total,
            ],
        ];
    }

    /**
     * Statistiques d'audit
     */
    public function statistiquesAudit(): array
    {
        // Par action
        $sqlAction = "SELECT action, COUNT(*) as total
                      FROM pister 
                      WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                      GROUP BY action
                      ORDER BY total DESC
                      LIMIT 10";
        $stmtAction = Model::raw($sqlAction, []);
        $parAction = $stmtAction->fetchAll(\PDO::FETCH_ASSOC);

        // Par entité
        $sqlEntite = "SELECT entite_type, COUNT(*) as total
                      FROM pister 
                      WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                      GROUP BY entite_type
                      ORDER BY total DESC
                      LIMIT 10";
        $stmtEntite = Model::raw($sqlEntite, []);
        $parEntite = $stmtEntite->fetchAll(\PDO::FETCH_ASSOC);

        // Par jour (30 derniers jours)
        $sqlJour = "SELECT DATE(created_at) as date, COUNT(*) as total
                    FROM pister 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY date";
        $stmtJour = Model::raw($sqlJour, []);
        $parJour = $stmtJour->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'par_action' => $parAction,
            'par_entite' => $parEntite,
            'par_jour' => $parJour,
        ];
    }

    // =========================================================================
    // UTILISATEURS
    // =========================================================================

    /**
     * Récupère les statistiques utilisateurs
     */
    public function statistiquesUtilisateurs(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut_utilisateur = 'Actif' THEN 1 ELSE 0 END) as actifs,
                    SUM(CASE WHEN statut_utilisateur = 'Inactif' THEN 1 ELSE 0 END) as inactifs,
                    SUM(CASE WHEN statut_utilisateur = 'Suspendu' THEN 1 ELSE 0 END) as suspendus,
                    SUM(CASE WHEN derniere_connexion >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as actifs_7j
                FROM utilisateurs";

        $stmt = Model::raw($sql, []);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Par groupe
        $sqlGroupe = "SELECT g.nom_groupe, COUNT(ug.utilisateur_id) as total
                      FROM groupes g
                      LEFT JOIN utilisateurs_groupes ug ON ug.groupe_id = g.id_groupe
                      GROUP BY g.id_groupe, g.nom_groupe
                      ORDER BY total DESC";
        $stmtGroupe = Model::raw($sqlGroupe, []);
        $parGroupe = $stmtGroupe->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'general' => $stats,
            'par_groupe' => $parGroupe,
        ];
    }

    // =========================================================================
    // TABLEAU DE BORD ADMIN
    // =========================================================================

    /**
     * Récupère les données du tableau de bord admin
     */
    public function getDashboardData(): array
    {
        // Statistiques rapides
        $statsUtilisateurs = $this->statistiquesUtilisateurs();
        $statsReclamations = $this->statistiquesReclamations();
        
        // Compteurs basiques
        $sqlCounts = "SELECT 
            (SELECT COUNT(*) FROM etudiants WHERE actif = 1) as etudiants,
            (SELECT COUNT(*) FROM enseignants WHERE actif = 1) as enseignants,
            (SELECT COUNT(*) FROM dossiers_etudiants) as dossiers,
            (SELECT COUNT(*) FROM sessions_commission WHERE statut = 'Planifiee') as sessions_planifiees,
            (SELECT COUNT(*) FROM soutenances WHERE statut = 'Planifiee') as soutenances_planifiees,
            (SELECT COUNT(*) FROM escalades WHERE statut IN ('Ouverte', 'En_cours')) as escalades_actives";
        
        $stmt = Model::raw($sqlCounts, []);
        $counts = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Dernières activités
        $sqlActivites = "SELECT action, entite_type, created_at, 
                                (SELECT nom_utilisateur FROM utilisateurs WHERE id_utilisateur = p.utilisateur_id) as utilisateur
                         FROM pister p
                         ORDER BY created_at DESC
                         LIMIT 10";
        $stmtActivites = Model::raw($sqlActivites, []);
        $activites = $stmtActivites->fetchAll(\PDO::FETCH_ASSOC);

        // Année académique active
        $anneeActive = AnneeAcademique::active();

        return [
            'counts' => $counts,
            'utilisateurs' => $statsUtilisateurs['general'],
            'reclamations' => $statsReclamations['general'],
            'activites' => $activites,
            'annee_active' => $anneeActive ? [
                'id' => $anneeActive->getId(),
                'libelle' => $anneeActive->lib_annee_acad,
            ] : null,
            'maintenance' => [
                'actif' => $this->estEnMaintenance(),
                'message' => $this->getMessageMaintenance(),
            ],
        ];
    }
}
