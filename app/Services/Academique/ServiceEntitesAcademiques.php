<?php

declare(strict_types=1);

namespace App\Services\Academique;

use App\Models\Etudiant;
use App\Models\Enseignant;
use App\Models\PersonnelAdmin;
use App\Models\Entreprise;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use App\Models\NiveauEtude;
use App\Models\Ue;
use App\Models\Ecue;
use App\Models\Grade;
use App\Models\Fonction;
use App\Models\Specialite;
use App\Services\Security\ServiceAudit;
use App\Validators\EtudiantValidator;
use App\Validators\EnseignantValidator;
use App\Validators\EntrepriseValidator;
use App\Validators\AnneeAcademiqueValidator;
use App\Validators\UeValidator;
use Src\Exceptions\ValidationException;
use Src\Exceptions\NotFoundException;
use App\Orm\Model;

/**
 * Service Entités Académiques
 * 
 * Gestion complète des entités académiques : étudiants, enseignants,
 * personnel administratif, entreprises, structure pédagogique.
 * 
 * @see PRD 02 - Entités Académiques
 */
class ServiceEntitesAcademiques
{
    // =========================================================================
    // ÉTUDIANTS
    // =========================================================================

    /**
     * Crée un nouvel étudiant avec validation complète
     * 
     * @param array $donnees Données de l'étudiant
     * @param int $creePar ID de l'utilisateur créateur
     * @return Etudiant
     * @throws ValidationException Si les données sont invalides
     */
    public function creerEtudiant(array $donnees, int $creePar): Etudiant
    {
        $validator = new EtudiantValidator();
        if (!$validator->validate($donnees, true)) {
            throw new ValidationException($validator->getFirstError() ?? 'Données invalides');
        }

        // Vérifier unicité du numéro étudiant
        if (!empty($donnees['num_etu'])) {
            $existant = Etudiant::findByNumero($donnees['num_etu']);
            if ($existant !== null) {
                throw new ValidationException('Ce numéro étudiant existe déjà');
            }
        }

        // Vérifier unicité email
        if (!empty($donnees['email_etu'])) {
            $existant = Etudiant::findByEmail($donnees['email_etu']);
            if ($existant !== null) {
                throw new ValidationException('Cet email est déjà utilisé par un autre étudiant');
            }
        }

        $etudiant = new Etudiant($donnees);
        $etudiant->actif = true;
        $etudiant->save();

        ServiceAudit::logCreation('etudiant', $etudiant->getId(), $donnees);

        return $etudiant;
    }

    /**
     * Met à jour un étudiant
     */
    public function modifierEtudiant(int $id, array $donnees, int $modifiePar): Etudiant
    {
        $etudiant = Etudiant::find($id);
        if ($etudiant === null) {
            throw new NotFoundException('Étudiant non trouvé');
        }

        $validator = new EtudiantValidator();
        if (!$validator->validate($donnees, false)) {
            throw new ValidationException($validator->getFirstError() ?? 'Données invalides');
        }

        // Vérifier unicité email si modifié
        if (!empty($donnees['email_etu']) && $donnees['email_etu'] !== $etudiant->email_etu) {
            $existant = Etudiant::findByEmail($donnees['email_etu']);
            if ($existant !== null && $existant->getId() !== $id) {
                throw new ValidationException('Cet email est déjà utilisé');
            }
        }

        $anciennesDonnees = $etudiant->toArray();
        $etudiant->fill($donnees);
        $etudiant->save();

        ServiceAudit::logModification('etudiant', $id, $anciennesDonnees, $donnees);

        return $etudiant;
    }

    /**
     * Recherche des étudiants avec pagination
     */
    public function rechercherEtudiants(
        string $terme = '',
        ?string $promotion = null,
        ?string $genre = null,
        bool $actifsUniquement = true,
        int $page = 1,
        int $parPage = 20
    ): array {
        $offset = ($page - 1) * $parPage;
        $conditions = [];
        $params = [];

        if ($actifsUniquement) {
            $conditions[] = 'actif = 1';
        }

        if (!empty($terme)) {
            $conditions[] = '(nom_etu LIKE :terme OR prenom_etu LIKE :terme OR num_etu LIKE :terme OR email_etu LIKE :terme)';
            $params['terme'] = "%{$terme}%";
        }

        if ($promotion !== null) {
            $conditions[] = 'promotion_etu = :promotion';
            $params['promotion'] = $promotion;
        }

        if ($genre !== null) {
            $conditions[] = 'genre_etu = :genre';
            $params['genre'] = $genre;
        }

        $whereClause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

        // Compter le total
        $countSql = "SELECT COUNT(*) FROM etudiants {$whereClause}";
        $stmt = Model::raw($countSql, $params);
        $total = (int) $stmt->fetchColumn();

        // Récupérer les données
        $sql = "SELECT * FROM etudiants {$whereClause} 
                ORDER BY nom_etu, prenom_etu 
                LIMIT {$parPage} OFFSET {$offset}";
        $stmt = Model::raw($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $etudiants = array_map(function (array $row) {
            $model = new Etudiant($row);
            $model->exists = true;
            return $model;
        }, $rows);

        return [
            'etudiants' => $etudiants,
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil($total / $parPage),
                'perPage' => $parPage,
                'totalItems' => $total,
            ],
        ];
    }

    /**
     * Importe des étudiants depuis un fichier Excel
     */
    public function importerEtudiants(array $lignes, int $importePar): array
    {
        $resultats = [
            'total' => count($lignes),
            'reussis' => 0,
            'erreurs' => [],
            'etudiantsCrees' => [],
        ];

        Model::beginTransaction();
        try {
            foreach ($lignes as $index => $ligne) {
                $numeroLigne = $index + 2; // +2 car ligne 1 = en-têtes

                try {
                    $donnees = $this->mapperLigneImportEtudiant($ligne);
                    $etudiant = $this->creerEtudiant($donnees, $importePar);
                    $resultats['reussis']++;
                    $resultats['etudiantsCrees'][] = $etudiant->getId();
                } catch (\Exception $e) {
                    $resultats['erreurs'][] = [
                        'ligne' => $numeroLigne,
                        'message' => $e->getMessage(),
                        'donnees' => $ligne,
                    ];
                }
            }

            if ($resultats['reussis'] === 0 && !empty($resultats['erreurs'])) {
                Model::rollBack();
                throw new ValidationException('Aucun étudiant importé. Vérifiez le fichier.');
            }

            Model::commit();
        } catch (\Exception $e) {
            Model::rollBack();
            throw $e;
        }

        return $resultats;
    }

    /**
     * Mappe une ligne d'import vers les données étudiant
     */
    private function mapperLigneImportEtudiant(array $ligne): array
    {
        return [
            'num_etu' => trim((string) ($ligne['num_etu'] ?? $ligne['numero'] ?? $ligne[0] ?? '')),
            'nom_etu' => trim((string) ($ligne['nom_etu'] ?? $ligne['nom'] ?? $ligne[1] ?? '')),
            'prenom_etu' => trim((string) ($ligne['prenom_etu'] ?? $ligne['prenom'] ?? $ligne[2] ?? '')),
            'email_etu' => trim((string) ($ligne['email_etu'] ?? $ligne['email'] ?? $ligne[3] ?? '')),
            'telephone_etu' => trim((string) ($ligne['telephone_etu'] ?? $ligne['telephone'] ?? $ligne[4] ?? '')),
            'date_naiss_etu' => $ligne['date_naiss_etu'] ?? $ligne['date_naissance'] ?? $ligne[5] ?? null,
            'lieu_naiss_etu' => trim((string) ($ligne['lieu_naiss_etu'] ?? $ligne['lieu_naissance'] ?? $ligne[6] ?? '')),
            'genre_etu' => $ligne['genre_etu'] ?? $ligne['genre'] ?? $ligne[7] ?? null,
            'promotion_etu' => trim((string) ($ligne['promotion_etu'] ?? $ligne['promotion'] ?? $ligne[8] ?? '')),
        ];
    }

    /**
     * Exporte les étudiants au format tableau
     */
    public function exporterEtudiants(?string $promotion = null, bool $actifsUniquement = true): array
    {
        $conditions = [];
        $params = [];

        if ($actifsUniquement) {
            $conditions[] = 'actif = 1';
        }

        if ($promotion !== null) {
            $conditions[] = 'promotion_etu = :promotion';
            $params['promotion'] = $promotion;
        }

        $whereClause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

        $sql = "SELECT num_etu, nom_etu, prenom_etu, email_etu, telephone_etu, 
                       date_naiss_etu, lieu_naiss_etu, genre_etu, promotion_etu
                FROM etudiants {$whereClause}
                ORDER BY nom_etu, prenom_etu";

        $stmt = Model::raw($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Statistiques des étudiants
     */
    public function statistiquesEtudiants(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN actif = 1 THEN 1 ELSE 0 END) as actifs,
                    SUM(CASE WHEN genre_etu = 'Homme' THEN 1 ELSE 0 END) as hommes,
                    SUM(CASE WHEN genre_etu = 'Femme' THEN 1 ELSE 0 END) as femmes
                FROM etudiants";

        $stmt = Model::raw($sql, []);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

        return [
            'general' => $stats,
            'par_promotion' => Etudiant::statistiquesParPromotion(),
        ];
    }

    // =========================================================================
    // ENSEIGNANTS
    // =========================================================================

    /**
     * Crée un nouvel enseignant
     */
    public function creerEnseignant(array $donnees, int $creePar): Enseignant
    {
        $validator = new EnseignantValidator();
        if (!$validator->validate($donnees, true)) {
            throw new ValidationException($validator->getFirstError() ?? 'Données invalides');
        }

        // Vérifier unicité email
        if (!empty($donnees['email_ens'])) {
            $existant = Enseignant::findByEmail($donnees['email_ens']);
            if ($existant !== null) {
                throw new ValidationException('Cet email est déjà utilisé par un autre enseignant');
            }
        }

        $enseignant = new Enseignant($donnees);
        $enseignant->actif = true;
        $enseignant->save();

        ServiceAudit::logCreation('enseignant', $enseignant->getId(), $donnees);

        return $enseignant;
    }

    /**
     * Met à jour un enseignant
     */
    public function modifierEnseignant(int $id, array $donnees, int $modifiePar): Enseignant
    {
        $enseignant = Enseignant::find($id);
        if ($enseignant === null) {
            throw new NotFoundException('Enseignant non trouvé');
        }

        $validator = new EnseignantValidator();
        if (!$validator->validate($donnees, false)) {
            throw new ValidationException($validator->getFirstError() ?? 'Données invalides');
        }

        // Vérifier unicité email si modifié
        if (!empty($donnees['email_ens']) && $donnees['email_ens'] !== $enseignant->email_ens) {
            $existant = Enseignant::findByEmail($donnees['email_ens']);
            if ($existant !== null && $existant->getId() !== $id) {
                throw new ValidationException('Cet email est déjà utilisé');
            }
        }

        $anciennesDonnees = $enseignant->toArray();
        $enseignant->fill($donnees);
        $enseignant->save();

        ServiceAudit::logModification('enseignant', $id, $anciennesDonnees, $donnees);

        return $enseignant;
    }

    /**
     * Recherche des enseignants avec pagination
     */
    public function rechercherEnseignants(
        string $terme = '',
        ?int $gradeId = null,
        ?int $specialiteId = null,
        bool $actifsUniquement = true,
        int $page = 1,
        int $parPage = 20
    ): array {
        $offset = ($page - 1) * $parPage;
        $conditions = [];
        $params = [];

        if ($actifsUniquement) {
            $conditions[] = 'actif = 1';
        }

        if (!empty($terme)) {
            $conditions[] = '(nom_ens LIKE :terme OR prenom_ens LIKE :terme OR email_ens LIKE :terme)';
            $params['terme'] = "%{$terme}%";
        }

        if ($gradeId !== null) {
            $conditions[] = 'grade_id = :grade';
            $params['grade'] = $gradeId;
        }

        if ($specialiteId !== null) {
            $conditions[] = 'specialite_id = :specialite';
            $params['specialite'] = $specialiteId;
        }

        $whereClause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

        // Compter
        $countSql = "SELECT COUNT(*) FROM enseignants {$whereClause}";
        $stmt = Model::raw($countSql, $params);
        $total = (int) $stmt->fetchColumn();

        // Récupérer
        $sql = "SELECT * FROM enseignants {$whereClause} 
                ORDER BY nom_ens, prenom_ens 
                LIMIT {$parPage} OFFSET {$offset}";
        $stmt = Model::raw($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $enseignants = array_map(function (array $row) {
            $model = new Enseignant($row);
            $model->exists = true;
            return $model;
        }, $rows);

        return [
            'enseignants' => $enseignants,
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil($total / $parPage),
                'perPage' => $parPage,
                'totalItems' => $total,
            ],
        ];
    }

    /**
     * Statistiques des enseignants
     */
    public function statistiquesEnseignants(): array
    {
        return [
            'total' => Enseignant::count(['actif' => true]),
            'par_grade' => Enseignant::statistiquesParGrade(),
            'par_specialite' => Enseignant::statistiquesParSpecialite(),
        ];
    }

    // =========================================================================
    // PERSONNEL ADMINISTRATIF
    // =========================================================================

    /**
     * Crée un membre du personnel administratif
     */
    public function creerPersonnel(array $donnees, int $creePar): PersonnelAdmin
    {
        // Validation basique
        if (empty($donnees['nom_pers']) || empty($donnees['prenom_pers'])) {
            throw new ValidationException('Le nom et prénom sont obligatoires');
        }

        // Vérifier unicité email
        if (!empty($donnees['email_pers'])) {
            $existant = PersonnelAdmin::findByEmail($donnees['email_pers']);
            if ($existant !== null) {
                throw new ValidationException('Cet email est déjà utilisé');
            }
        }

        $personnel = new PersonnelAdmin($donnees);
        $personnel->actif = true;
        $personnel->save();

        ServiceAudit::logCreation('personnel_admin', $personnel->getId(), $donnees);

        return $personnel;
    }

    /**
     * Met à jour un membre du personnel
     */
    public function modifierPersonnel(int $id, array $donnees, int $modifiePar): PersonnelAdmin
    {
        $personnel = PersonnelAdmin::find($id);
        if ($personnel === null) {
            throw new NotFoundException('Personnel non trouvé');
        }

        $anciennesDonnees = $personnel->toArray();
        $personnel->fill($donnees);
        $personnel->save();

        ServiceAudit::logModification('personnel_admin', $id, $anciennesDonnees, $donnees);

        return $personnel;
    }

    /**
     * Recherche du personnel
     */
    public function rechercherPersonnel(
        string $terme = '',
        ?int $fonctionId = null,
        int $page = 1,
        int $parPage = 20
    ): array {
        $offset = ($page - 1) * $parPage;
        $conditions = ['actif = 1'];
        $params = [];

        if (!empty($terme)) {
            $conditions[] = '(nom_pers LIKE :terme OR prenom_pers LIKE :terme OR email_pers LIKE :terme)';
            $params['terme'] = "%{$terme}%";
        }

        if ($fonctionId !== null) {
            $conditions[] = 'fonction_id = :fonction';
            $params['fonction'] = $fonctionId;
        }

        $whereClause = 'WHERE ' . implode(' AND ', $conditions);

        $countSql = "SELECT COUNT(*) FROM personnel_admin {$whereClause}";
        $stmt = Model::raw($countSql, $params);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT * FROM personnel_admin {$whereClause} 
                ORDER BY nom_pers, prenom_pers 
                LIMIT {$parPage} OFFSET {$offset}";
        $stmt = Model::raw($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $personnel = array_map(function (array $row) {
            $model = new PersonnelAdmin($row);
            $model->exists = true;
            return $model;
        }, $rows);

        return [
            'personnel' => $personnel,
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil($total / $parPage),
                'perPage' => $parPage,
                'totalItems' => $total,
            ],
        ];
    }

    // =========================================================================
    // ENTREPRISES
    // =========================================================================

    /**
     * Crée une entreprise
     */
    public function creerEntreprise(array $donnees, int $creePar): Entreprise
    {
        $validator = new EntrepriseValidator();
        if (!$validator->validate($donnees)) {
            throw new ValidationException($validator->getFirstError() ?? 'Données invalides');
        }

        $entreprise = new Entreprise($donnees);
        $entreprise->actif = true;
        $entreprise->save();

        ServiceAudit::logCreation('entreprise', $entreprise->getId(), $donnees);

        return $entreprise;
    }

    /**
     * Met à jour une entreprise
     */
    public function modifierEntreprise(int $id, array $donnees, int $modifiePar): Entreprise
    {
        $entreprise = Entreprise::find($id);
        if ($entreprise === null) {
            throw new NotFoundException('Entreprise non trouvée');
        }

        $anciennesDonnees = $entreprise->toArray();
        $entreprise->fill($donnees);
        $entreprise->save();

        ServiceAudit::logModification('entreprise', $id, $anciennesDonnees, $donnees);

        return $entreprise;
    }

    /**
     * Recherche des entreprises
     */
    public function rechercherEntreprises(
        string $terme = '',
        ?string $secteur = null,
        int $page = 1,
        int $parPage = 20
    ): array {
        $offset = ($page - 1) * $parPage;
        $conditions = ['actif = 1'];
        $params = [];

        if (!empty($terme)) {
            $conditions[] = '(nom_entreprise LIKE :terme OR secteur_activite LIKE :terme OR adresse LIKE :terme)';
            $params['terme'] = "%{$terme}%";
        }

        if ($secteur !== null) {
            $conditions[] = 'secteur_activite = :secteur';
            $params['secteur'] = $secteur;
        }

        $whereClause = 'WHERE ' . implode(' AND ', $conditions);

        $countSql = "SELECT COUNT(*) FROM entreprises {$whereClause}";
        $stmt = Model::raw($countSql, $params);
        $total = (int) $stmt->fetchColumn();

        $sql = "SELECT * FROM entreprises {$whereClause} ORDER BY nom_entreprise LIMIT {$parPage} OFFSET {$offset}";
        $stmt = Model::raw($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $entreprises = array_map(function (array $row) {
            $model = new Entreprise($row);
            $model->exists = true;
            return $model;
        }, $rows);

        return [
            'entreprises' => $entreprises,
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil($total / $parPage),
                'perPage' => $parPage,
                'totalItems' => $total,
            ],
        ];
    }

    // =========================================================================
    // ANNÉES ACADÉMIQUES
    // =========================================================================

    /**
     * Crée une année académique
     */
    public function creerAnneeAcademique(array $donnees, int $creePar): AnneeAcademique
    {
        $validator = new AnneeAcademiqueValidator();
        if (!$validator->validate($donnees)) {
            throw new ValidationException($validator->getFirstError() ?? 'Données invalides');
        }

        // Vérifier unicité du libellé
        if (!empty($donnees['lib_annee_acad'])) {
            $existant = AnneeAcademique::findByLibelle($donnees['lib_annee_acad']);
            if ($existant !== null) {
                throw new ValidationException('Cette année académique existe déjà');
            }
        }

        $annee = new AnneeAcademique($donnees);
        $annee->est_active = false;
        $annee->save();

        ServiceAudit::logCreation('annee_academique', $annee->getId(), $donnees);

        return $annee;
    }

    /**
     * Active une année académique (désactive les autres)
     */
    public function activerAnneeAcademique(int $id, int $activePar): AnneeAcademique
    {
        $annee = AnneeAcademique::find($id);
        if ($annee === null) {
            throw new NotFoundException('Année académique non trouvée');
        }

        $annee->activer();

        ServiceAudit::log('activation_annee_academique', 'annee_academique', $id, [
            'libelle' => $annee->lib_annee_acad,
        ]);

        return $annee;
    }

    /**
     * Crée les semestres pour une année académique
     */
    public function creerSemestres(int $anneeAcadId, array $semestres, int $creePar): array
    {
        $annee = AnneeAcademique::find($anneeAcadId);
        if ($annee === null) {
            throw new NotFoundException('Année académique non trouvée');
        }

        $semestresCrees = [];

        foreach ($semestres as $donnees) {
            $semestre = new Semestre([
                'lib_semestre' => $donnees['lib_semestre'] ?? 'Semestre',
                'annee_acad_id' => $anneeAcadId,
                'date_debut' => $donnees['date_debut'] ?? null,
                'date_fin' => $donnees['date_fin'] ?? null,
            ]);
            $semestre->save();
            $semestresCrees[] = $semestre;
        }

        ServiceAudit::log('creation_semestres', 'annee_academique', $anneeAcadId, [
            'nombre' => count($semestresCrees),
        ]);

        return $semestresCrees;
    }

    // =========================================================================
    // STRUCTURE PÉDAGOGIQUE (UE/ECUE)
    // =========================================================================

    /**
     * Crée une Unité d'Enseignement
     */
    public function creerUe(array $donnees, int $creePar): Ue
    {
        $validator = new UeValidator();
        if (!$validator->validate($donnees)) {
            throw new ValidationException($validator->getFirstError() ?? 'Données invalides');
        }

        // Vérifier unicité du code
        if (!empty($donnees['code_ue'])) {
            $existant = Ue::findByCode($donnees['code_ue']);
            if ($existant !== null) {
                throw new ValidationException('Ce code UE existe déjà');
            }
        }

        $ue = new Ue($donnees);
        $ue->save();

        ServiceAudit::logCreation('ue', $ue->getId(), $donnees);

        return $ue;
    }

    /**
     * Met à jour une UE
     */
    public function modifierUe(int $id, array $donnees, int $modifiePar): Ue
    {
        $ue = Ue::find($id);
        if ($ue === null) {
            throw new NotFoundException('UE non trouvée');
        }

        // Vérifier unicité du code si modifié
        if (!empty($donnees['code_ue']) && $donnees['code_ue'] !== $ue->code_ue) {
            $existant = Ue::findByCode($donnees['code_ue']);
            if ($existant !== null && $existant->getId() !== $id) {
                throw new ValidationException('Ce code UE existe déjà');
            }
        }

        $anciennesDonnees = $ue->toArray();
        $ue->fill($donnees);
        $ue->save();

        ServiceAudit::logModification('ue', $id, $anciennesDonnees, $donnees);

        return $ue;
    }

    /**
     * Crée un ECUE
     */
    public function creerEcue(array $donnees, int $creePar): Ecue
    {
        if (empty($donnees['code_ecue']) || empty($donnees['lib_ecue']) || empty($donnees['ue_id'])) {
            throw new ValidationException('Code, libellé et UE parent sont obligatoires');
        }

        // Vérifier que l'UE existe
        $ue = Ue::find((int) $donnees['ue_id']);
        if ($ue === null) {
            throw new ValidationException('UE parent non trouvée');
        }

        // Vérifier unicité du code
        $existant = Ecue::findByCode($donnees['code_ecue']);
        if ($existant !== null) {
            throw new ValidationException('Ce code ECUE existe déjà');
        }

        $ecue = new Ecue($donnees);
        $ecue->save();

        ServiceAudit::logCreation('ecue', $ecue->getId(), $donnees);

        return $ecue;
    }

    /**
     * Liste les UE avec leurs ECUE
     */
    public function listerUeAvecEcue(?int $niveauId = null, ?int $semestreId = null): array
    {
        $conditions = [];
        $params = [];

        if ($niveauId !== null) {
            $conditions[] = 'niveau_id = :niveau';
            $params['niveau'] = $niveauId;
        }

        if ($semestreId !== null) {
            $conditions[] = 'semestre_id = :semestre';
            $params['semestre'] = $semestreId;
        }

        $whereClause = empty($conditions) ? '' : 'WHERE ' . implode(' AND ', $conditions);

        $sql = "SELECT * FROM ue {$whereClause} ORDER BY code_ue";
        $stmt = Model::raw($sql, $params);
        $ues = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];
        foreach ($ues as $ueData) {
            $ecuesSql = "SELECT * FROM ecue WHERE ue_id = :ue_id ORDER BY code_ecue";
            $ecuesStmt = Model::raw($ecuesSql, ['ue_id' => $ueData['id_ue']]);
            $ecues = $ecuesStmt->fetchAll(\PDO::FETCH_ASSOC);

            $result[] = [
                'ue' => $ueData,
                'ecues' => $ecues,
            ];
        }

        return $result;
    }

    // =========================================================================
    // RÉFÉRENTIELS (Grades, Fonctions, Spécialités)
    // =========================================================================

    /**
     * Liste tous les grades actifs
     */
    public function listerGrades(): array
    {
        return Grade::parNiveauHierarchique();
    }

    /**
     * Crée un grade
     */
    public function creerGrade(array $donnees, int $creePar): Grade
    {
        if (empty($donnees['lib_grade'])) {
            throw new ValidationException('Le libellé du grade est obligatoire');
        }

        $existant = Grade::findByLibelle($donnees['lib_grade']);
        if ($existant !== null) {
            throw new ValidationException('Ce grade existe déjà');
        }

        $grade = new Grade($donnees);
        $grade->actif = true;
        $grade->save();

        ServiceAudit::logCreation('grade', $grade->getId(), $donnees);

        return $grade;
    }

    /**
     * Liste toutes les fonctions actives
     */
    public function listerFonctions(): array
    {
        return Fonction::actives();
    }

    /**
     * Crée une fonction
     */
    public function creerFonction(array $donnees, int $creePar): Fonction
    {
        if (empty($donnees['lib_fonction'])) {
            throw new ValidationException('Le libellé de la fonction est obligatoire');
        }

        $existant = Fonction::findByLibelle($donnees['lib_fonction']);
        if ($existant !== null) {
            throw new ValidationException('Cette fonction existe déjà');
        }

        $fonction = new Fonction($donnees);
        $fonction->actif = true;
        $fonction->save();

        ServiceAudit::logCreation('fonction', $fonction->getId(), $donnees);

        return $fonction;
    }

    /**
     * Liste toutes les spécialités actives
     */
    public function listerSpecialites(): array
    {
        return Specialite::where(['actif' => true]);
    }

    /**
     * Crée une spécialité
     */
    public function creerSpecialite(array $donnees, int $creePar): Specialite
    {
        if (empty($donnees['lib_specialite'])) {
            throw new ValidationException('Le libellé de la spécialité est obligatoire');
        }

        $existant = Specialite::findByLibelle($donnees['lib_specialite']);
        if ($existant !== null) {
            throw new ValidationException('Cette spécialité existe déjà');
        }

        $specialite = new Specialite($donnees);
        $specialite->actif = true;
        $specialite->save();

        ServiceAudit::logCreation('specialite', $specialite->getId(), $donnees);

        return $specialite;
    }

    /**
     * Liste tous les niveaux d'étude
     */
    public function listerNiveauxEtude(): array
    {
        return NiveauEtude::ordonnes();
    }
}
