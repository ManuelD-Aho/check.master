<?php

declare(strict_types=1);

namespace App\Controllers\Academique;

use App\Services\Academique\ServiceEntitesAcademiques;
use App\Services\Security\ServicePermissions;
use App\Services\Security\ServiceAudit;
use App\Models\Etudiant;
use App\Models\Enseignant;
use App\Models\PersonnelAdmin;
use App\Models\Entreprise;
use App\Models\AnneeAcademique;
use App\Models\Ue;
use App\Models\Ecue;
use App\Models\Grade;
use App\Models\Fonction;
use App\Models\Specialite;
use App\Models\NiveauEtude;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur Entités Académiques
 * 
 * Gère les opérations CRUD pour les entités académiques:
 * étudiants, enseignants, personnel, entreprises, structure pédagogique.
 * 
 * @see PRD 02 - Entités Académiques
 */
class EntitesAcademiquesController
{
    private ServiceEntitesAcademiques $service;
    private const PER_PAGE = 20;

    public function __construct()
    {
        $this->service = new ServiceEntitesAcademiques();
    }

    // =========================================================================
    // ÉTUDIANTS
    // =========================================================================

    /**
     * Liste des étudiants (vue)
     */
    public function indexEtudiants(): Response
    {
        if (!$this->checkAccess('etudiants', 'lire')) {
            return Response::redirect('/dashboard');
        }

        $page = max(1, (int) Request::query('page', 1));
        $search = trim(Request::query('q', ''));
        $promotion = Request::query('promotion') ?: null;

        $data = $this->service->rechercherEtudiants($search, $promotion, null, true, $page, self::PER_PAGE);
        $promotions = Etudiant::getPromotions();

        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/academique/etudiants.php';
        return Response::html((string) ob_get_clean());
    }

    /**
     * API: Liste des étudiants
     */
    public function listEtudiants(): JsonResponse
    {
        if (!$this->checkAccess('etudiants', 'lire')) {
            return JsonResponse::forbidden();
        }

        $page = max(1, (int) Request::query('page', 1));
        $search = trim(Request::query('q', ''));
        $promotion = Request::query('promotion') ?: null;

        $data = $this->service->rechercherEtudiants($search, $promotion, null, true, $page, self::PER_PAGE);

        return JsonResponse::success([
            'etudiants' => array_map(fn($e) => $e->toArray(), $data['etudiants']),
            'pagination' => $data['pagination'],
        ]);
    }

    /**
     * API: Détails d'un étudiant
     */
    public function showEtudiant(int $id): JsonResponse
    {
        if (!$this->checkAccess('etudiants', 'lire')) {
            return JsonResponse::forbidden();
        }

        $etudiant = Etudiant::find($id);
        if ($etudiant === null) {
            return JsonResponse::notFound('Étudiant non trouvé');
        }

        return JsonResponse::success($etudiant->toArray());
    }

    /**
     * API: Création d'un étudiant
     */
    public function storeEtudiant(): JsonResponse
    {
        if (!$this->checkAccess('etudiants', 'creer')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $etudiant = $this->service->creerEtudiant($data, Auth::id() ?? 0);
            return JsonResponse::success($etudiant->toArray(), 'Étudiant créé avec succès');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Mise à jour d'un étudiant
     */
    public function updateEtudiant(int $id): JsonResponse
    {
        if (!$this->checkAccess('etudiants', 'modifier')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $etudiant = $this->service->modifierEtudiant($id, $data, Auth::id() ?? 0);
            return JsonResponse::success($etudiant->toArray(), 'Étudiant mis à jour');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Désactivation d'un étudiant
     */
    public function desactiverEtudiant(int $id): JsonResponse
    {
        if (!$this->checkAccess('etudiants', 'supprimer')) {
            return JsonResponse::forbidden();
        }

        $etudiant = Etudiant::find($id);
        if ($etudiant === null) {
            return JsonResponse::notFound('Étudiant non trouvé');
        }

        $etudiant->desactiver();
        ServiceAudit::log('desactivation_etudiant', 'etudiant', $id);

        return JsonResponse::success(null, 'Étudiant désactivé');
    }

    /**
     * API: Import des étudiants
     */
    public function importEtudiants(): JsonResponse
    {
        if (!$this->checkAccess('etudiants', 'creer')) {
            return JsonResponse::forbidden();
        }

        $file = $_FILES['file'] ?? null;
        if ($file === null || $file['error'] !== UPLOAD_ERR_OK) {
            return JsonResponse::error('Fichier non reçu ou erreur de téléchargement');
        }

        try {
            // Lire le fichier Excel/CSV
            $lignes = $this->lireFichierImport($file['tmp_name']);
            $resultats = $this->service->importerEtudiants($lignes, Auth::id() ?? 0);

            return JsonResponse::success($resultats, sprintf(
                'Import terminé: %d réussis, %d erreurs',
                $resultats['reussis'],
                count($resultats['erreurs'])
            ));
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Export des étudiants
     */
    public function exportEtudiants(): JsonResponse
    {
        if (!$this->checkAccess('etudiants', 'exporter')) {
            return JsonResponse::forbidden();
        }

        $promotion = Request::query('promotion') ?: null;
        $data = $this->service->exporterEtudiants($promotion);

        return JsonResponse::success($data);
    }

    /**
     * API: Statistiques étudiants
     */
    public function statistiquesEtudiants(): JsonResponse
    {
        if (!$this->checkAccess('etudiants', 'lire')) {
            return JsonResponse::forbidden();
        }

        $stats = $this->service->statistiquesEtudiants();
        return JsonResponse::success($stats);
    }

    // =========================================================================
    // ENSEIGNANTS
    // =========================================================================

    /**
     * Liste des enseignants (vue)
     */
    public function indexEnseignants(): Response
    {
        if (!$this->checkAccess('enseignants', 'lire')) {
            return Response::redirect('/dashboard');
        }

        $page = max(1, (int) Request::query('page', 1));
        $search = trim(Request::query('q', ''));
        $gradeId = Request::query('grade') ? (int) Request::query('grade') : null;
        $specialiteId = Request::query('specialite') ? (int) Request::query('specialite') : null;

        $data = $this->service->rechercherEnseignants($search, $gradeId, $specialiteId, true, $page, self::PER_PAGE);
        $grades = $this->service->listerGrades();
        $specialites = $this->service->listerSpecialites();

        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/academique/enseignants.php';
        return Response::html((string) ob_get_clean());
    }

    /**
     * API: Liste des enseignants
     */
    public function listEnseignants(): JsonResponse
    {
        if (!$this->checkAccess('enseignants', 'lire')) {
            return JsonResponse::forbidden();
        }

        $page = max(1, (int) Request::query('page', 1));
        $search = trim(Request::query('q', ''));
        $gradeId = Request::query('grade') ? (int) Request::query('grade') : null;
        $specialiteId = Request::query('specialite') ? (int) Request::query('specialite') : null;

        $data = $this->service->rechercherEnseignants($search, $gradeId, $specialiteId, true, $page, self::PER_PAGE);

        return JsonResponse::success([
            'enseignants' => array_map(fn($e) => $e->toArray(), $data['enseignants']),
            'pagination' => $data['pagination'],
        ]);
    }

    /**
     * API: Détails d'un enseignant
     */
    public function showEnseignant(int $id): JsonResponse
    {
        if (!$this->checkAccess('enseignants', 'lire')) {
            return JsonResponse::forbidden();
        }

        $enseignant = Enseignant::find($id);
        if ($enseignant === null) {
            return JsonResponse::notFound('Enseignant non trouvé');
        }

        $data = $enseignant->toArray();
        $data['grade'] = $enseignant->grade()?->toArray();
        $data['fonction'] = $enseignant->fonction()?->toArray();
        $data['specialite'] = $enseignant->specialite()?->toArray();

        return JsonResponse::success($data);
    }

    /**
     * API: Création d'un enseignant
     */
    public function storeEnseignant(): JsonResponse
    {
        if (!$this->checkAccess('enseignants', 'creer')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $enseignant = $this->service->creerEnseignant($data, Auth::id() ?? 0);
            return JsonResponse::success($enseignant->toArray(), 'Enseignant créé avec succès');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Mise à jour d'un enseignant
     */
    public function updateEnseignant(int $id): JsonResponse
    {
        if (!$this->checkAccess('enseignants', 'modifier')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $enseignant = $this->service->modifierEnseignant($id, $data, Auth::id() ?? 0);
            return JsonResponse::success($enseignant->toArray(), 'Enseignant mis à jour');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Statistiques enseignants
     */
    public function statistiquesEnseignants(): JsonResponse
    {
        if (!$this->checkAccess('enseignants', 'lire')) {
            return JsonResponse::forbidden();
        }

        $stats = $this->service->statistiquesEnseignants();
        return JsonResponse::success($stats);
    }

    // =========================================================================
    // PERSONNEL ADMINISTRATIF
    // =========================================================================

    /**
     * API: Liste du personnel
     */
    public function listPersonnel(): JsonResponse
    {
        if (!$this->checkAccess('personnel', 'lire')) {
            return JsonResponse::forbidden();
        }

        $page = max(1, (int) Request::query('page', 1));
        $search = trim(Request::query('q', ''));
        $fonctionId = Request::query('fonction') ? (int) Request::query('fonction') : null;

        $data = $this->service->rechercherPersonnel($search, $fonctionId, $page, self::PER_PAGE);

        return JsonResponse::success([
            'personnel' => array_map(fn($p) => $p->toArray(), $data['personnel']),
            'pagination' => $data['pagination'],
        ]);
    }

    /**
     * API: Création d'un membre du personnel
     */
    public function storePersonnel(): JsonResponse
    {
        if (!$this->checkAccess('personnel', 'creer')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $personnel = $this->service->creerPersonnel($data, Auth::id() ?? 0);
            return JsonResponse::success($personnel->toArray(), 'Personnel créé avec succès');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Mise à jour d'un membre du personnel
     */
    public function updatePersonnel(int $id): JsonResponse
    {
        if (!$this->checkAccess('personnel', 'modifier')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $personnel = $this->service->modifierPersonnel($id, $data, Auth::id() ?? 0);
            return JsonResponse::success($personnel->toArray(), 'Personnel mis à jour');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    // =========================================================================
    // ENTREPRISES
    // =========================================================================

    /**
     * Liste des entreprises (vue)
     */
    public function indexEntreprises(): Response
    {
        if (!$this->checkAccess('entreprises', 'lire')) {
            return Response::redirect('/dashboard');
        }

        $page = max(1, (int) Request::query('page', 1));
        $search = trim(Request::query('q', ''));
        $secteur = Request::query('secteur') ?: null;

        $data = $this->service->rechercherEntreprises($search, $secteur, $page, self::PER_PAGE);
        $secteurs = Entreprise::getSecteurs();

        ob_start();
        include dirname(__DIR__, 3) . '/ressources/views/academique/entreprises.php';
        return Response::html((string) ob_get_clean());
    }

    /**
     * API: Liste des entreprises
     */
    public function listEntreprises(): JsonResponse
    {
        if (!$this->checkAccess('entreprises', 'lire')) {
            return JsonResponse::forbidden();
        }

        $page = max(1, (int) Request::query('page', 1));
        $search = trim(Request::query('q', ''));
        $secteur = Request::query('secteur') ?: null;

        $data = $this->service->rechercherEntreprises($search, $secteur, $page, self::PER_PAGE);

        return JsonResponse::success([
            'entreprises' => array_map(fn($e) => $e->toArray(), $data['entreprises']),
            'pagination' => $data['pagination'],
        ]);
    }

    /**
     * API: Détails d'une entreprise
     */
    public function showEntreprise(int $id): JsonResponse
    {
        if (!$this->checkAccess('entreprises', 'lire')) {
            return JsonResponse::forbidden();
        }

        $entreprise = Entreprise::find($id);
        if ($entreprise === null) {
            return JsonResponse::notFound('Entreprise non trouvée');
        }

        return JsonResponse::success($entreprise->toArray());
    }

    /**
     * API: Création d'une entreprise
     */
    public function storeEntreprise(): JsonResponse
    {
        if (!$this->checkAccess('entreprises', 'creer')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $entreprise = $this->service->creerEntreprise($data, Auth::id() ?? 0);
            return JsonResponse::success($entreprise->toArray(), 'Entreprise créée avec succès');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Mise à jour d'une entreprise
     */
    public function updateEntreprise(int $id): JsonResponse
    {
        if (!$this->checkAccess('entreprises', 'modifier')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $entreprise = $this->service->modifierEntreprise($id, $data, Auth::id() ?? 0);
            return JsonResponse::success($entreprise->toArray(), 'Entreprise mise à jour');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    // =========================================================================
    // ANNÉES ACADÉMIQUES
    // =========================================================================

    /**
     * API: Liste des années académiques
     */
    public function listAnnees(): JsonResponse
    {
        if (!$this->checkAccess('annees', 'lire')) {
            return JsonResponse::forbidden();
        }

        $annees = AnneeAcademique::all();
        return JsonResponse::success(array_map(fn($a) => $a->toArray(), $annees));
    }

    /**
     * API: Détails d'une année académique
     */
    public function showAnnee(int $id): JsonResponse
    {
        if (!$this->checkAccess('annees', 'lire')) {
            return JsonResponse::forbidden();
        }

        $annee = AnneeAcademique::find($id);
        if ($annee === null) {
            return JsonResponse::notFound('Année académique non trouvée');
        }

        $data = $annee->toArray();
        $data['semestres'] = array_map(fn($s) => $s->toArray(), $annee->semestres());

        return JsonResponse::success($data);
    }

    /**
     * API: Création d'une année académique
     */
    public function storeAnnee(): JsonResponse
    {
        if (!$this->checkAccess('annees', 'creer')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $annee = $this->service->creerAnneeAcademique($data, Auth::id() ?? 0);
            return JsonResponse::success($annee->toArray(), 'Année académique créée avec succès');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Activation d'une année académique
     */
    public function activerAnnee(int $id): JsonResponse
    {
        if (!$this->checkAccess('annees', 'modifier')) {
            return JsonResponse::forbidden();
        }

        try {
            $annee = $this->service->activerAnneeAcademique($id, Auth::id() ?? 0);
            return JsonResponse::success($annee->toArray(), 'Année académique activée');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    // =========================================================================
    // STRUCTURE PÉDAGOGIQUE (UE/ECUE)
    // =========================================================================

    /**
     * API: Liste des UE
     */
    public function listUe(): JsonResponse
    {
        if (!$this->checkAccess('ue', 'lire')) {
            return JsonResponse::forbidden();
        }

        $niveauId = Request::query('niveau') ? (int) Request::query('niveau') : null;
        $semestreId = Request::query('semestre') ? (int) Request::query('semestre') : null;

        $data = $this->service->listerUeAvecEcue($niveauId, $semestreId);
        return JsonResponse::success($data);
    }

    /**
     * API: Création d'une UE
     */
    public function storeUe(): JsonResponse
    {
        if (!$this->checkAccess('ue', 'creer')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $ue = $this->service->creerUe($data, Auth::id() ?? 0);
            return JsonResponse::success($ue->toArray(), 'UE créée avec succès');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Mise à jour d'une UE
     */
    public function updateUe(int $id): JsonResponse
    {
        if (!$this->checkAccess('ue', 'modifier')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $ue = $this->service->modifierUe($id, $data, Auth::id() ?? 0);
            return JsonResponse::success($ue->toArray(), 'UE mise à jour');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    /**
     * API: Création d'un ECUE
     */
    public function storeEcue(): JsonResponse
    {
        if (!$this->checkAccess('ue', 'creer')) {
            return JsonResponse::forbidden();
        }

        try {
            $data = Request::all();
            $ecue = $this->service->creerEcue($data, Auth::id() ?? 0);
            return JsonResponse::success($ecue->toArray(), 'ECUE créé avec succès');
        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }

    // =========================================================================
    // RÉFÉRENTIELS
    // =========================================================================

    /**
     * API: Liste des grades
     */
    public function listGrades(): JsonResponse
    {
        $grades = $this->service->listerGrades();
        return JsonResponse::success(array_map(fn($g) => $g->toArray(), $grades));
    }

    /**
     * API: Liste des fonctions
     */
    public function listFonctions(): JsonResponse
    {
        $fonctions = $this->service->listerFonctions();
        return JsonResponse::success(array_map(fn($f) => $f->toArray(), $fonctions));
    }

    /**
     * API: Liste des spécialités
     */
    public function listSpecialites(): JsonResponse
    {
        $specialites = $this->service->listerSpecialites();
        return JsonResponse::success(array_map(fn($s) => $s->toArray(), $specialites));
    }

    /**
     * API: Liste des niveaux d'étude
     */
    public function listNiveaux(): JsonResponse
    {
        $niveaux = $this->service->listerNiveauxEtude();
        return JsonResponse::success(array_map(fn($n) => $n->toArray(), $niveaux));
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Vérifie les permissions
     */
    private function checkAccess(string $ressource, string $action): bool
    {
        $userId = Auth::id();
        return $userId !== null && ServicePermissions::verifier($userId, $ressource, $action);
    }

    /**
     * Lit un fichier d'import (CSV/Excel)
     */
    private function lireFichierImport(string $cheminFichier): array
    {
        $extension = strtolower(pathinfo($cheminFichier, PATHINFO_EXTENSION));
        
        if ($extension === 'csv') {
            return $this->lireCsv($cheminFichier);
        }
        
        // Pour Excel, utiliser une bibliothèque comme PhpSpreadsheet
        // Pour simplifier, on suppose ici un CSV
        return $this->lireCsv($cheminFichier);
    }

    /**
     * Lit un fichier CSV
     */
    private function lireCsv(string $cheminFichier): array
    {
        $lignes = [];
        $handle = fopen($cheminFichier, 'r');
        if ($handle === false) {
            return [];
        }

        $headers = fgetcsv($handle, 0, ';'); // Première ligne = en-têtes
        if ($headers === false) {
            fclose($handle);
            return [];
        }

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $ligne = [];
            foreach ($headers as $index => $header) {
                $ligne[trim($header)] = $row[$index] ?? '';
            }
            $lignes[] = $ligne;
        }

        fclose($handle);
        return $lignes;
    }
}
