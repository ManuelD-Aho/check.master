<?php

declare(strict_types=1);

namespace App\Services\Soutenance;

use App\Models\Candidature;
use App\Models\DossierEtudiant;
use App\Models\Etudiant;
use App\Models\Entreprise;
use App\Services\Security\ServiceAudit;
use App\Services\Workflow\ServiceWorkflow;
use App\Services\Communication\ServiceNotification;
use Src\Exceptions\NotFoundException;
use Src\Exceptions\ValidationException;
use App\Orm\Model;

/**
 * Service Candidature
 * 
 * Gestion des candidatures de stage/mémoire.
 * Soumission, validation, rejet.
 * 
 * @see PRD 04 - Mémoire & Soutenance
 */
class ServiceCandidature
{
    /**
     * Statuts de candidature
     */
    public const STATUT_BROUILLON = 'Brouillon';
    public const STATUT_SOUMISE = 'Soumise';
    public const STATUT_EN_VERIFICATION = 'En_verification';
    public const STATUT_VALIDEE = 'Validee';
    public const STATUT_REJETEE = 'Rejetee';
    public const STATUT_A_COMPLETER = 'A_completer';

    /**
     * Crée une candidature
     */
    public function creer(int $dossierId, array $donnees, int $creePar): Candidature
    {
        $dossier = DossierEtudiant::find($dossierId);
        if ($dossier === null) {
            throw new NotFoundException('Dossier non trouvé');
        }

        // Vérifier qu'une candidature n'existe pas déjà
        $existante = Candidature::firstWhere(['dossier_id' => $dossierId]);
        if ($existante !== null) {
            throw new ValidationException('Une candidature existe déjà pour ce dossier');
        }

        // Valider les données
        $this->validerDonnees($donnees);

        // Générer la référence
        $reference = $this->genererReference();

        $candidature = new Candidature([
            'dossier_id' => $dossierId,
            'reference' => $reference,
            'theme' => $donnees['theme'],
            'entreprise_nom' => $donnees['entreprise_nom'] ?? null,
            'entreprise_adresse' => $donnees['entreprise_adresse'] ?? null,
            'maitre_stage_nom' => $donnees['maitre_stage_nom'] ?? null,
            'maitre_stage_email' => $donnees['maitre_stage_email'] ?? null,
            'maitre_stage_telephone' => $donnees['maitre_stage_telephone'] ?? null,
            'date_debut_stage' => $donnees['date_debut_stage'] ?? null,
            'date_fin_stage' => $donnees['date_fin_stage'] ?? null,
            'statut' => self::STATUT_BROUILLON,
            'creee_par' => $creePar,
        ]);
        $candidature->save();

        ServiceAudit::logCreation('candidature', $candidature->getId(), [
            'dossier_id' => $dossierId,
            'reference' => $reference,
        ]);

        return $candidature;
    }

    /**
     * Valide les données de candidature
     */
    private function validerDonnees(array $donnees): void
    {
        if (empty($donnees['theme'])) {
            throw new ValidationException('Le thème du mémoire est requis');
        }

        if (strlen($donnees['theme']) < 10) {
            throw new ValidationException('Le thème doit contenir au moins 10 caractères');
        }

        if (strlen($donnees['theme']) > 500) {
            throw new ValidationException('Le thème ne peut pas dépasser 500 caractères');
        }

        // Valider l'email du maître de stage si fourni
        if (!empty($donnees['maitre_stage_email'])) {
            if (!filter_var($donnees['maitre_stage_email'], FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException('L\'email du maître de stage est invalide');
            }
        }

        // Valider les dates de stage si fournies
        if (!empty($donnees['date_debut_stage']) && !empty($donnees['date_fin_stage'])) {
            $debut = strtotime($donnees['date_debut_stage']);
            $fin = strtotime($donnees['date_fin_stage']);

            if ($debut >= $fin) {
                throw new ValidationException('La date de fin doit être postérieure à la date de début');
            }
        }
    }

    /**
     * Génère une référence unique
     */
    private function genererReference(): string
    {
        $annee = date('Y');
        $random = strtoupper(bin2hex(random_bytes(4)));
        return "CAND-{$annee}-{$random}";
    }

    /**
     * Met à jour une candidature
     */
    public function mettreAJour(int $candidatureId, array $donnees, int $modifiePar): Candidature
    {
        $candidature = Candidature::find($candidatureId);
        if ($candidature === null) {
            throw new NotFoundException('Candidature non trouvée');
        }

        if (!in_array($candidature->statut, [self::STATUT_BROUILLON, self::STATUT_A_COMPLETER], true)) {
            throw new ValidationException('Cette candidature ne peut plus être modifiée');
        }

        // Valider les données
        $this->validerDonnees($donnees);

        // Mettre à jour les champs
        if (isset($donnees['theme'])) {
            $candidature->theme = $donnees['theme'];
        }
        if (isset($donnees['entreprise_nom'])) {
            $candidature->entreprise_nom = $donnees['entreprise_nom'];
        }
        if (isset($donnees['entreprise_adresse'])) {
            $candidature->entreprise_adresse = $donnees['entreprise_adresse'];
        }
        if (isset($donnees['maitre_stage_nom'])) {
            $candidature->maitre_stage_nom = $donnees['maitre_stage_nom'];
        }
        if (isset($donnees['maitre_stage_email'])) {
            $candidature->maitre_stage_email = $donnees['maitre_stage_email'];
        }
        if (isset($donnees['maitre_stage_telephone'])) {
            $candidature->maitre_stage_telephone = $donnees['maitre_stage_telephone'];
        }
        if (isset($donnees['date_debut_stage'])) {
            $candidature->date_debut_stage = $donnees['date_debut_stage'];
        }
        if (isset($donnees['date_fin_stage'])) {
            $candidature->date_fin_stage = $donnees['date_fin_stage'];
        }

        $candidature->modifiee_le = date('Y-m-d H:i:s');
        $candidature->save();

        ServiceAudit::log('modification_candidature', 'candidature', $candidatureId);

        return $candidature;
    }

    /**
     * Soumet une candidature
     */
    public function soumettre(int $candidatureId, int $soumisPar): Candidature
    {
        $candidature = Candidature::find($candidatureId);
        if ($candidature === null) {
            throw new NotFoundException('Candidature non trouvée');
        }

        if (!in_array($candidature->statut, [self::STATUT_BROUILLON, self::STATUT_A_COMPLETER], true)) {
            throw new ValidationException('Cette candidature ne peut pas être soumise');
        }

        // Vérifier que tous les champs obligatoires sont remplis
        $this->verifierCompletude($candidature);

        $candidature->statut = self::STATUT_SOUMISE;
        $candidature->soumise_le = date('Y-m-d H:i:s');
        $candidature->soumise_par = $soumisPar;
        $candidature->save();

        // Avancer le workflow du dossier
        if ($candidature->dossier_id !== null) {
            $serviceWorkflow = new ServiceWorkflow();
            try {
                $serviceWorkflow->effectuerTransition(
                    (int) $candidature->dossier_id,
                    'CANDIDATURE_SOUMISE',
                    $soumisPar,
                    'Candidature soumise'
                );
            } catch (\Exception $e) {
                error_log('Erreur transition candidature: ' . $e->getMessage());
            }
        }

        ServiceAudit::log('soumission_candidature', 'candidature', $candidatureId);

        // Notifier la scolarité
        $this->notifierScolarite($candidature);

        return $candidature;
    }

    /**
     * Vérifie que la candidature est complète
     */
    private function verifierCompletude(Candidature $candidature): void
    {
        $champsMandatoires = [
            'theme' => 'Le thème du mémoire',
            'maitre_stage_nom' => 'Le nom du maître de stage',
            'maitre_stage_email' => 'L\'email du maître de stage',
        ];

        foreach ($champsMandatoires as $champ => $libelle) {
            if (empty($candidature->$champ)) {
                throw new ValidationException("{$libelle} est requis");
            }
        }
    }

    /**
     * Notifie la scolarité d'une nouvelle candidature
     */
    private function notifierScolarite(Candidature $candidature): void
    {
        // Récupérer les utilisateurs de la scolarité
        $sql = "SELECT u.id_utilisateur 
                FROM utilisateurs u
                INNER JOIN utilisateurs_groupes ug ON ug.utilisateur_id = u.id_utilisateur
                INNER JOIN groupes g ON g.id_groupe = ug.groupe_id
                WHERE g.code_groupe = 'SCOLARITE' AND u.actif = 1";

        $stmt = Model::raw($sql);
        $scolariteUsers = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        if (!empty($scolariteUsers)) {
            ServiceNotification::envoyerParCode(
                'nouvelle_candidature',
                $scolariteUsers,
                [
                    'reference' => $candidature->reference,
                    'theme' => $candidature->theme,
                ]
            );
        }
    }

    /**
     * Valide une candidature (scolarité)
     */
    public function valider(int $candidatureId, int $validePar, ?string $commentaire = null): Candidature
    {
        $candidature = Candidature::find($candidatureId);
        if ($candidature === null) {
            throw new NotFoundException('Candidature non trouvée');
        }

        if ($candidature->statut !== self::STATUT_EN_VERIFICATION) {
            throw new ValidationException('Cette candidature n\'est pas en vérification');
        }

        $candidature->statut = self::STATUT_VALIDEE;
        $candidature->validee_le = date('Y-m-d H:i:s');
        $candidature->validee_par = $validePar;
        $candidature->commentaire_validation = $commentaire;
        $candidature->save();

        // Avancer le workflow
        if ($candidature->dossier_id !== null) {
            $serviceWorkflow = new ServiceWorkflow();
            try {
                $serviceWorkflow->effectuerTransition(
                    (int) $candidature->dossier_id,
                    'VERIFICATION_SCOLARITE',
                    $validePar,
                    'Candidature validée'
                );
            } catch (\Exception $e) {
                error_log('Erreur transition validation: ' . $e->getMessage());
            }
        }

        ServiceAudit::log('validation_candidature', 'candidature', $candidatureId);

        // Notifier l'étudiant
        $this->notifierEtudiantDecision($candidature, true);

        return $candidature;
    }

    /**
     * Rejette une candidature
     */
    public function rejeter(int $candidatureId, string $motif, int $rejetePar): Candidature
    {
        $candidature = Candidature::find($candidatureId);
        if ($candidature === null) {
            throw new NotFoundException('Candidature non trouvée');
        }

        if (!in_array($candidature->statut, [self::STATUT_SOUMISE, self::STATUT_EN_VERIFICATION], true)) {
            throw new ValidationException('Cette candidature ne peut pas être rejetée');
        }

        $candidature->statut = self::STATUT_REJETEE;
        $candidature->rejetee_le = date('Y-m-d H:i:s');
        $candidature->rejetee_par = $rejetePar;
        $candidature->motif_rejet = $motif;
        $candidature->save();

        ServiceAudit::log('rejet_candidature', 'candidature', $candidatureId, [
            'motif' => $motif,
        ]);

        // Notifier l'étudiant
        $this->notifierEtudiantDecision($candidature, false);

        return $candidature;
    }

    /**
     * Demande des compléments d'information
     */
    public function demanderComplements(int $candidatureId, string $demande, int $demandePar): Candidature
    {
        $candidature = Candidature::find($candidatureId);
        if ($candidature === null) {
            throw new NotFoundException('Candidature non trouvée');
        }

        $candidature->statut = self::STATUT_A_COMPLETER;
        $candidature->demande_complements = $demande;
        $candidature->complements_demandes_le = date('Y-m-d H:i:s');
        $candidature->complements_demandes_par = $demandePar;
        $candidature->save();

        ServiceAudit::log('demande_complements', 'candidature', $candidatureId);

        // Notifier l'étudiant
        $dossier = DossierEtudiant::find((int) $candidature->dossier_id);
        $etudiant = $dossier?->getEtudiant();

        if ($etudiant !== null && $etudiant->utilisateur_id !== null) {
            ServiceNotification::envoyerParCode(
                'complements_requis',
                [(int) $etudiant->utilisateur_id],
                [
                    'reference' => $candidature->reference,
                    'demande' => $demande,
                ]
            );
        }

        return $candidature;
    }

    /**
     * Notifie l'étudiant de la décision
     */
    private function notifierEtudiantDecision(Candidature $candidature, bool $acceptee): void
    {
        $dossier = DossierEtudiant::find((int) $candidature->dossier_id);
        $etudiant = $dossier?->getEtudiant();

        if ($etudiant === null || $etudiant->utilisateur_id === null) {
            return;
        }

        $templateCode = $acceptee ? 'candidature_validee' : 'candidature_rejetee';

        ServiceNotification::envoyerParCode(
            $templateCode,
            [(int) $etudiant->utilisateur_id],
            [
                'reference' => $candidature->reference,
                'theme' => $candidature->theme,
                'motif' => $candidature->motif_rejet ?? '',
            ]
        );
    }

    /**
     * Retourne une candidature avec ses détails
     */
    public function getDetails(int $candidatureId): array
    {
        $candidature = Candidature::find($candidatureId);
        if ($candidature === null) {
            throw new NotFoundException('Candidature non trouvée');
        }

        $dossier = DossierEtudiant::find((int) $candidature->dossier_id);
        $etudiant = $dossier?->getEtudiant();

        return [
            'candidature' => $candidature->toArray(),
            'etudiant' => $etudiant?->toArray(),
            'dossier' => $dossier?->toArray(),
        ];
    }

    /**
     * Liste les candidatures avec filtres
     */
    public function lister(array $filtres = [], int $page = 1, int $perPage = 50): array
    {
        $sql = "SELECT c.*, e.nom_etu, e.prenom_etu, e.numero_carte,
                       de.id_dossier, aa.libelle_annee_acad
                FROM candidatures c
                INNER JOIN dossiers_etudiants de ON de.id_dossier = c.dossier_id
                INNER JOIN etudiants e ON e.id_etudiant = de.etudiant_id
                LEFT JOIN annees_academiques aa ON aa.id_annee_acad = de.annee_acad_id
                WHERE 1=1";

        $params = [];

        if (!empty($filtres['statut'])) {
            $sql .= " AND c.statut = :statut";
            $params['statut'] = $filtres['statut'];
        }

        if (!empty($filtres['annee_id'])) {
            $sql .= " AND de.annee_acad_id = :annee";
            $params['annee'] = $filtres['annee_id'];
        }

        if (!empty($filtres['recherche'])) {
            $sql .= " AND (e.nom_etu LIKE :q1 OR e.prenom_etu LIKE :q2 OR c.theme LIKE :q3 OR c.reference LIKE :q4)";
            $terme = "%{$filtres['recherche']}%";
            $params['q1'] = $terme;
            $params['q2'] = $terme;
            $params['q3'] = $terme;
            $params['q4'] = $terme;
        }

        $sql .= " ORDER BY c.created_at DESC";
        $sql .= " LIMIT " . (($page - 1) * $perPage) . ", " . $perPage;

        $stmt = Model::raw($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les statistiques des candidatures
     */
    public function getStatistiques(?int $anneeId = null): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN c.statut = 'Brouillon' THEN 1 ELSE 0 END) as brouillons,
                    SUM(CASE WHEN c.statut = 'Soumise' THEN 1 ELSE 0 END) as soumises,
                    SUM(CASE WHEN c.statut = 'Validee' THEN 1 ELSE 0 END) as validees,
                    SUM(CASE WHEN c.statut = 'Rejetee' THEN 1 ELSE 0 END) as rejetees,
                    SUM(CASE WHEN c.statut = 'A_completer' THEN 1 ELSE 0 END) as a_completer
                FROM candidatures c
                INNER JOIN dossiers_etudiants de ON de.id_dossier = c.dossier_id
                WHERE 1=1";

        $params = [];

        if ($anneeId !== null) {
            $sql .= " AND de.annee_acad_id = :annee";
            $params['annee'] = $anneeId;
        }

        $stmt = Model::raw($sql, $params);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }
}
