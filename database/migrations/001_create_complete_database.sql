-- =====================================================
-- Migration: 001_create_complete_database.sql
-- Date: 2025-12-14
-- Purpose: Complete CheckMaster database schema (67 tables)
-- Author: CheckMaster Team
-- =====================================================

-- Set character set and collation
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- =====================================================
-- SECTION 1: AUTHENTICATION & USERS (10 tables)
-- =====================================================

-- Table: utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    nom_utilisateur VARCHAR(255) NOT NULL,
    login_utilisateur VARCHAR(255) UNIQUE NOT NULL,
    mdp_utilisateur VARCHAR(255) NOT NULL,
    id_type_utilisateur INT NOT NULL,
    id_GU INT NOT NULL,
    id_niv_acces_donnee INT,
    statut_utilisateur ENUM('Actif', 'Inactif', 'Suspendu') DEFAULT 'Actif',
    doit_changer_mdp BOOLEAN DEFAULT TRUE,
    derniere_connexion DATETIME,
    tentatives_echec INT DEFAULT 0,
    verrouille_jusqu_a DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_login (login_utilisateur),
    INDEX idx_statut (statut_utilisateur),
    INDEX idx_type (id_type_utilisateur),
    INDEX idx_groupe (id_GU)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sessions_actives
CREATE TABLE IF NOT EXISTS sessions_actives (
    id_session INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    token_session VARCHAR(128) UNIQUE NOT NULL,
    ip_adresse VARCHAR(45),
    user_agent TEXT,
    derniere_activite DATETIME DEFAULT CURRENT_TIMESTAMP,
    expire_a DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_token (token_session),
    INDEX idx_expire (expire_a),
    INDEX idx_utilisateur (utilisateur_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: codes_temporaires
CREATE TABLE IF NOT EXISTS codes_temporaires (
    id_code INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    soutenance_id INT,
    code_hash VARCHAR(255) NOT NULL,
    type ENUM('president_jury', 'reset_password', 'verification') NOT NULL,
    valide_de DATETIME NOT NULL,
    valide_jusqu_a DATETIME NOT NULL,
    utilise BOOLEAN DEFAULT FALSE,
    utilise_a DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_type (type),
    INDEX idx_validite (valide_de, valide_jusqu_a)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: groupes
CREATE TABLE IF NOT EXISTS groupes (
    id_groupe INT PRIMARY KEY AUTO_INCREMENT,
    nom_groupe VARCHAR(100) NOT NULL,
    description TEXT,
    niveau_hierarchique INT DEFAULT 0,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: utilisateurs_groupes
CREATE TABLE IF NOT EXISTS utilisateurs_groupes (
    utilisateur_id INT NOT NULL,
    groupe_id INT NOT NULL,
    attribue_par INT,
    attribue_le DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (utilisateur_id, groupe_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (groupe_id) REFERENCES groupes(id_groupe) ON DELETE CASCADE,
    FOREIGN KEY (attribue_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_groupe (groupe_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: roles_temporaires
CREATE TABLE IF NOT EXISTS roles_temporaires (
    id_role_temp INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    role_code VARCHAR(50) NOT NULL,
    contexte_type VARCHAR(50),
    contexte_id INT,
    permissions_json JSON NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    valide_de DATETIME NOT NULL,
    valide_jusqu_a DATETIME NOT NULL,
    cree_par INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (cree_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_validite (valide_de, valide_jusqu_a),
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ressources
CREATE TABLE IF NOT EXISTS ressources (
    id_ressource INT PRIMARY KEY AUTO_INCREMENT,
    code_ressource VARCHAR(50) UNIQUE NOT NULL,
    nom_ressource VARCHAR(100) NOT NULL,
    description TEXT,
    module VARCHAR(50),
    INDEX idx_code (code_ressource)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: permissions
CREATE TABLE IF NOT EXISTS permissions (
    id_permission INT PRIMARY KEY AUTO_INCREMENT,
    groupe_id INT NOT NULL,
    ressource_id INT NOT NULL,
    peut_lire BOOLEAN DEFAULT FALSE,
    peut_creer BOOLEAN DEFAULT FALSE,
    peut_modifier BOOLEAN DEFAULT FALSE,
    peut_supprimer BOOLEAN DEFAULT FALSE,
    peut_exporter BOOLEAN DEFAULT FALSE,
    peut_valider BOOLEAN DEFAULT FALSE,
    conditions_json JSON,
    UNIQUE KEY unique_groupe_ressource (groupe_id, ressource_id),
    FOREIGN KEY (groupe_id) REFERENCES groupes(id_groupe) ON DELETE CASCADE,
    FOREIGN KEY (ressource_id) REFERENCES ressources(id_ressource) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: permissions_cache
CREATE TABLE IF NOT EXISTS permissions_cache (
    utilisateur_id INT NOT NULL,
    ressource_code VARCHAR(50) NOT NULL,
    permissions_json JSON NOT NULL,
    genere_le DATETIME DEFAULT CURRENT_TIMESTAMP,
    expire_le DATETIME,
    PRIMARY KEY (utilisateur_id, ressource_code),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_expire (expire_le)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pister (audit trail)
CREATE TABLE IF NOT EXISTS pister (
    id_pister INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT,
    action VARCHAR(100) NOT NULL,
    entite_type VARCHAR(50),
    entite_id INT,
    donnees_snapshot JSON,
    ip_adresse VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_entite (entite_type, entite_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECTION 2: ACADEMIC ENTITIES (12 tables)
-- =====================================================

-- Table: etudiants
CREATE TABLE IF NOT EXISTS etudiants (
    id_etudiant INT PRIMARY KEY AUTO_INCREMENT,
    num_etu VARCHAR(20) UNIQUE NOT NULL,
    nom_etu VARCHAR(100) NOT NULL,
    prenom_etu VARCHAR(100) NOT NULL,
    email_etu VARCHAR(255) UNIQUE NOT NULL,
    telephone_etu VARCHAR(20),
    date_naiss_etu DATE,
    lieu_naiss_etu VARCHAR(100),
    genre_etu ENUM('Homme', 'Femme', 'Autre'),
    promotion_etu VARCHAR(20),
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_num (num_etu),
    INDEX idx_nom (nom_etu, prenom_etu),
    INDEX idx_email (email_etu),
    INDEX idx_actif (actif),
    FULLTEXT idx_fulltext (nom_etu, prenom_etu, email_etu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: enseignants
CREATE TABLE IF NOT EXISTS enseignants (
    id_enseignant INT PRIMARY KEY AUTO_INCREMENT,
    nom_ens VARCHAR(100) NOT NULL,
    prenom_ens VARCHAR(100) NOT NULL,
    email_ens VARCHAR(255) UNIQUE NOT NULL,
    telephone_ens VARCHAR(20),
    grade_id INT,
    fonction_id INT,
    specialite_id INT,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nom (nom_ens, prenom_ens),
    INDEX idx_email (email_ens),
    INDEX idx_grade (grade_id),
    INDEX idx_specialite (specialite_id),
    FULLTEXT idx_fulltext (nom_ens, prenom_ens, email_ens)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: personnel_admin
CREATE TABLE IF NOT EXISTS personnel_admin (
    id_pers_admin INT PRIMARY KEY AUTO_INCREMENT,
    nom_pers VARCHAR(100) NOT NULL,
    prenom_pers VARCHAR(100) NOT NULL,
    email_pers VARCHAR(255) UNIQUE NOT NULL,
    telephone_pers VARCHAR(20),
    fonction_id INT,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nom (nom_pers, prenom_pers),
    INDEX idx_email (email_pers)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: entreprises
CREATE TABLE IF NOT EXISTS entreprises (
    id_entreprise INT PRIMARY KEY AUTO_INCREMENT,
    nom_entreprise VARCHAR(255) NOT NULL,
    secteur_activite VARCHAR(100),
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(255),
    site_web VARCHAR(255),
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nom (nom_entreprise),
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: specialites
CREATE TABLE IF NOT EXISTS specialites (
    id_specialite INT PRIMARY KEY AUTO_INCREMENT,
    lib_specialite VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: grades
CREATE TABLE IF NOT EXISTS grades (
    id_grade INT PRIMARY KEY AUTO_INCREMENT,
    lib_grade VARCHAR(100) UNIQUE NOT NULL,
    niveau_hierarchique INT,
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_niveau (niveau_hierarchique)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: fonctions
CREATE TABLE IF NOT EXISTS fonctions (
    id_fonction INT PRIMARY KEY AUTO_INCREMENT,
    lib_fonction VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: annee_academique
CREATE TABLE IF NOT EXISTS annee_academique (
    id_annee_acad INT PRIMARY KEY AUTO_INCREMENT,
    lib_annee_acad VARCHAR(20) UNIQUE NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    est_active BOOLEAN DEFAULT FALSE,
    INDEX idx_active (est_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: semestre
CREATE TABLE IF NOT EXISTS semestre (
    id_semestre INT PRIMARY KEY AUTO_INCREMENT,
    lib_semestre VARCHAR(50) NOT NULL,
    annee_acad_id INT NOT NULL,
    date_debut DATE,
    date_fin DATE,
    FOREIGN KEY (annee_acad_id) REFERENCES annee_academique(id_annee_acad) ON DELETE CASCADE,
    INDEX idx_annee (annee_acad_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: niveau_etude
CREATE TABLE IF NOT EXISTS niveau_etude (
    id_niveau INT PRIMARY KEY AUTO_INCREMENT,
    lib_niveau VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    ordre_niveau INT,
    INDEX idx_ordre (ordre_niveau)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ue (Unités d'Enseignement)
CREATE TABLE IF NOT EXISTS ue (
    id_ue INT PRIMARY KEY AUTO_INCREMENT,
    code_ue VARCHAR(20) UNIQUE NOT NULL,
    lib_ue VARCHAR(255) NOT NULL,
    credits INT,
    niveau_id INT,
    semestre_id INT,
    FOREIGN KEY (niveau_id) REFERENCES niveau_etude(id_niveau) ON DELETE SET NULL,
    FOREIGN KEY (semestre_id) REFERENCES semestre(id_semestre) ON DELETE SET NULL,
    INDEX idx_code (code_ue),
    INDEX idx_niveau (niveau_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ecue (Éléments Constitutifs d'UE)
CREATE TABLE IF NOT EXISTS ecue (
    id_ecue INT PRIMARY KEY AUTO_INCREMENT,
    code_ecue VARCHAR(20) UNIQUE NOT NULL,
    lib_ecue VARCHAR(255) NOT NULL,
    ue_id INT NOT NULL,
    credits INT,
    FOREIGN KEY (ue_id) REFERENCES ue(id_ue) ON DELETE CASCADE,
    INDEX idx_code (code_ecue),
    INDEX idx_ue (ue_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: workflow_etats
CREATE TABLE IF NOT EXISTS workflow_etats (
    id_etat INT PRIMARY KEY AUTO_INCREMENT,
    code_etat VARCHAR(50) UNIQUE NOT NULL,
    nom_etat VARCHAR(100) NOT NULL,
    phase VARCHAR(50),
    delai_max_jours INT,
    ordre_affichage INT,
    couleur_hex VARCHAR(7),
    description TEXT,
    INDEX idx_code (code_etat),
    INDEX idx_phase (phase),
    INDEX idx_ordre (ordre_affichage)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: workflow_transitions
CREATE TABLE IF NOT EXISTS workflow_transitions (
    id_transition INT PRIMARY KEY AUTO_INCREMENT,
    etat_source_id INT NOT NULL,
    etat_cible_id INT NOT NULL,
    code_transition VARCHAR(50) UNIQUE NOT NULL,
    nom_transition VARCHAR(100) NOT NULL,
    roles_autorises JSON,
    conditions_json JSON,
    notifier BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (etat_source_id) REFERENCES workflow_etats(id_etat) ON DELETE RESTRICT,
    FOREIGN KEY (etat_cible_id) REFERENCES workflow_etats(id_etat) ON DELETE RESTRICT,
    INDEX idx_source (etat_source_id),
    INDEX idx_cible (etat_cible_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: workflow_historique
CREATE TABLE IF NOT EXISTS workflow_historique (
    id_historique INT PRIMARY KEY AUTO_INCREMENT,
    dossier_id INT NOT NULL,
    etat_source_id INT,
    etat_cible_id INT NOT NULL,
    transition_id INT,
    utilisateur_id INT,
    commentaire TEXT,
    snapshot_json JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etat_source_id) REFERENCES workflow_etats(id_etat) ON DELETE SET NULL,
    FOREIGN KEY (etat_cible_id) REFERENCES workflow_etats(id_etat) ON DELETE RESTRICT,
    FOREIGN KEY (transition_id) REFERENCES workflow_transitions(id_transition) ON DELETE SET NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_dossier (dossier_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: workflow_alertes
CREATE TABLE IF NOT EXISTS workflow_alertes (
    id_alerte INT PRIMARY KEY AUTO_INCREMENT,
    dossier_id INT NOT NULL,
    etat_id INT NOT NULL,
    type_alerte ENUM('50_pourcent', '80_pourcent', '100_pourcent') NOT NULL,
    envoyee BOOLEAN DEFAULT FALSE,
    envoyee_le DATETIME,
    INDEX idx_dossier (dossier_id),
    INDEX idx_envoyee (envoyee)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: dossiers_etudiants
CREATE TABLE IF NOT EXISTS dossiers_etudiants (
    id_dossier INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    annee_acad_id INT NOT NULL,
    etat_actuel_id INT NOT NULL,
    date_entree_etat DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_limite_etat DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id_etudiant) ON DELETE CASCADE,
    FOREIGN KEY (annee_acad_id) REFERENCES annee_academique(id_annee_acad) ON DELETE RESTRICT,
    FOREIGN KEY (etat_actuel_id) REFERENCES workflow_etats(id_etat) ON DELETE RESTRICT,
    UNIQUE KEY unique_etudiant_annee (etudiant_id, annee_acad_id),
    INDEX idx_etat (etat_actuel_id),
    INDEX idx_date_limite (date_limite_etat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: candidatures
CREATE TABLE IF NOT EXISTS candidatures (
    id_candidature INT PRIMARY KEY AUTO_INCREMENT,
    dossier_id INT NOT NULL,
    theme VARCHAR(500) NOT NULL,
    entreprise_id INT,
    maitre_stage_nom VARCHAR(255),
    maitre_stage_email VARCHAR(255),
    maitre_stage_tel VARCHAR(20),
    date_debut_stage DATE,
    date_fin_stage DATE,
    date_soumission DATETIME DEFAULT CURRENT_TIMESTAMP,
    validee_scolarite BOOLEAN DEFAULT FALSE,
    date_valid_scolarite DATETIME,
    validee_communication BOOLEAN DEFAULT FALSE,
    date_valid_communication DATETIME,
    FOREIGN KEY (dossier_id) REFERENCES dossiers_etudiants(id_dossier) ON DELETE CASCADE,
    FOREIGN KEY (entreprise_id) REFERENCES entreprises(id_entreprise) ON DELETE SET NULL,
    INDEX idx_dossier (dossier_id),
    INDEX idx_entreprise (entreprise_id),
    FULLTEXT idx_theme (theme)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: rapports_etudiants
CREATE TABLE IF NOT EXISTS rapports_etudiants (
    id_rapport INT PRIMARY KEY AUTO_INCREMENT,
    dossier_id INT NOT NULL,
    titre VARCHAR(500),
    contenu_html LONGTEXT,
    version INT DEFAULT 1,
    statut ENUM('Brouillon', 'Soumis', 'En_evaluation', 'Valide', 'Rejete') DEFAULT 'Brouillon',
    date_depot DATETIME,
    chemin_fichier VARCHAR(500),
    hash_fichier VARCHAR(64),
    FOREIGN KEY (dossier_id) REFERENCES dossiers_etudiants(id_dossier) ON DELETE CASCADE,
    INDEX idx_dossier (dossier_id),
    INDEX idx_statut (statut),
    FULLTEXT idx_titre (titre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sessions_commission
CREATE TABLE IF NOT EXISTS sessions_commission (
    id_session INT PRIMARY KEY AUTO_INCREMENT,
    date_session DATETIME NOT NULL,
    lieu VARCHAR(255),
    statut ENUM('Planifiee', 'En_cours', 'Terminee', 'Annulee') DEFAULT 'Planifiee',
    tour_vote INT DEFAULT 1,
    pv_genere BOOLEAN DEFAULT FALSE,
    pv_chemin VARCHAR(500),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_date (date_session),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: votes_commission
CREATE TABLE IF NOT EXISTS votes_commission (
    id_vote INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT NOT NULL,
    rapport_id INT NOT NULL,
    membre_id INT NOT NULL,
    tour INT NOT NULL,
    decision ENUM('Valider', 'A_revoir', 'Rejeter') NOT NULL,
    commentaire TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions_commission(id_session) ON DELETE CASCADE,
    FOREIGN KEY (rapport_id) REFERENCES rapports_etudiants(id_rapport) ON DELETE CASCADE,
    FOREIGN KEY (membre_id) REFERENCES enseignants(id_enseignant) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (session_id, rapport_id, membre_id, tour),
    INDEX idx_session (session_id),
    INDEX idx_rapport (rapport_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: annotations_rapport
CREATE TABLE IF NOT EXISTS annotations_rapport (
    id_annotation INT PRIMARY KEY AUTO_INCREMENT,
    rapport_id INT NOT NULL,
    auteur_id INT NOT NULL,
    page_numero INT,
    position_json JSON,
    contenu TEXT NOT NULL,
    type_annotation ENUM('Commentaire', 'Correction', 'Suggestion') DEFAULT 'Commentaire',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rapport_id) REFERENCES rapports_etudiants(id_rapport) ON DELETE CASCADE,
    FOREIGN KEY (auteur_id) REFERENCES enseignants(id_enseignant) ON DELETE CASCADE,
    INDEX idx_rapport (rapport_id),
    INDEX idx_auteur (auteur_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: jury_membres
CREATE TABLE IF NOT EXISTS jury_membres (
    id_membre_jury INT PRIMARY KEY AUTO_INCREMENT,
    dossier_id INT NOT NULL,
    enseignant_id INT NOT NULL,
    role_jury VARCHAR(50) NOT NULL,
    statut_acceptation ENUM('Invite', 'Accepte', 'Refuse') DEFAULT 'Invite',
    date_invitation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_reponse DATETIME,
    FOREIGN KEY (dossier_id) REFERENCES dossiers_etudiants(id_dossier) ON DELETE CASCADE,
    FOREIGN KEY (enseignant_id) REFERENCES enseignants(id_enseignant) ON DELETE CASCADE,
    UNIQUE KEY unique_jury_membre (dossier_id, enseignant_id),
    INDEX idx_dossier (dossier_id),
    INDEX idx_statut (statut_acceptation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: soutenances
CREATE TABLE IF NOT EXISTS soutenances (
    id_soutenance INT PRIMARY KEY AUTO_INCREMENT,
    dossier_id INT NOT NULL,
    date_soutenance DATETIME NOT NULL,
    lieu VARCHAR(255),
    salle_id INT,
    duree_minutes INT DEFAULT 60,
    statut ENUM('Planifiee', 'En_cours', 'Terminee', 'Annulee', 'Reportee') DEFAULT 'Planifiee',
    pv_genere BOOLEAN DEFAULT FALSE,
    pv_chemin VARCHAR(500),
    FOREIGN KEY (dossier_id) REFERENCES dossiers_etudiants(id_dossier) ON DELETE CASCADE,
    INDEX idx_dossier (dossier_id),
    INDEX idx_date (date_soutenance),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: notes_soutenance
CREATE TABLE IF NOT EXISTS notes_soutenance (
    id_note INT PRIMARY KEY AUTO_INCREMENT,
    soutenance_id INT NOT NULL,
    membre_jury_id INT NOT NULL,
    note_fond DECIMAL(5,2),
    note_forme DECIMAL(5,2),
    note_soutenance DECIMAL(5,2),
    note_finale DECIMAL(5,2),
    mention VARCHAR(50),
    commentaire TEXT,
    FOREIGN KEY (soutenance_id) REFERENCES soutenances(id_soutenance) ON DELETE CASCADE,
    FOREIGN KEY (membre_jury_id) REFERENCES jury_membres(id_membre_jury) ON DELETE CASCADE,
    INDEX idx_soutenance (soutenance_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: escalades
CREATE TABLE IF NOT EXISTS escalades (
    id_escalade INT PRIMARY KEY AUTO_INCREMENT,
    dossier_id INT,
    type_escalade VARCHAR(50) NOT NULL,
    niveau_escalade INT DEFAULT 1,
    description TEXT,
    statut ENUM('Ouverte', 'En_cours', 'Resolue', 'Fermee') DEFAULT 'Ouverte',
    cree_par INT,
    assignee_a INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (dossier_id) REFERENCES dossiers_etudiants(id_dossier) ON DELETE CASCADE,
    FOREIGN KEY (cree_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    FOREIGN KEY (assignee_a) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_dossier (dossier_id),
    INDEX idx_statut (statut),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: escalades_actions
CREATE TABLE IF NOT EXISTS escalades_actions (
    id_action INT PRIMARY KEY AUTO_INCREMENT,
    escalade_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    type_action VARCHAR(50) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (escalade_id) REFERENCES escalades(id_escalade) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_escalade (escalade_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECTION 4: FINANCIAL (3 tables)
-- =====================================================

-- Table: paiements
CREATE TABLE IF NOT EXISTS paiements (
    id_paiement INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    annee_acad_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    mode_paiement ENUM('Especes', 'Carte', 'Virement', 'Cheque') NOT NULL,
    reference VARCHAR(100) UNIQUE,
    date_paiement DATE NOT NULL,
    recu_genere BOOLEAN DEFAULT FALSE,
    recu_chemin VARCHAR(500),
    enregistre_par INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id_etudiant) ON DELETE CASCADE,
    FOREIGN KEY (annee_acad_id) REFERENCES annee_academique(id_annee_acad) ON DELETE RESTRICT,
    FOREIGN KEY (enregistre_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_etudiant (etudiant_id),
    INDEX idx_annee (annee_acad_id),
    INDEX idx_date (date_paiement),
    INDEX idx_reference (reference)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: penalites
CREATE TABLE IF NOT EXISTS penalites (
    id_penalite INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    motif TEXT NOT NULL,
    date_application DATE NOT NULL,
    payee BOOLEAN DEFAULT FALSE,
    date_paiement DATE,
    recu_chemin VARCHAR(500),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id_etudiant) ON DELETE CASCADE,
    INDEX idx_etudiant (etudiant_id),
    INDEX idx_payee (payee)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: exonerations
CREATE TABLE IF NOT EXISTS exonerations (
    id_exoneration INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    annee_acad_id INT NOT NULL,
    montant_exonere DECIMAL(10,2) NOT NULL,
    pourcentage_exonere DECIMAL(5,2),
    motif TEXT NOT NULL,
    date_attribution DATE NOT NULL,
    approuve_par INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id_etudiant) ON DELETE CASCADE,
    FOREIGN KEY (annee_acad_id) REFERENCES annee_academique(id_annee_acad) ON DELETE RESTRICT,
    FOREIGN KEY (approuve_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_etudiant (etudiant_id),
    INDEX idx_annee (annee_acad_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- SECTION 5: COMMUNICATIONS (5 tables)
-- =====================================================

-- Table: notification_templates
CREATE TABLE IF NOT EXISTS notification_templates (
    id_template INT PRIMARY KEY AUTO_INCREMENT,
    code_template VARCHAR(50) UNIQUE NOT NULL,
    canal ENUM('Email', 'SMS', 'Messagerie') NOT NULL,
    sujet VARCHAR(255),
    corps LONGTEXT NOT NULL,
    variables_json JSON,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code_template),
    INDEX idx_canal (canal),
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: notifications_queue
CREATE TABLE IF NOT EXISTS notifications_queue (
    id_queue INT PRIMARY KEY AUTO_INCREMENT,
    template_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    canal ENUM('Email', 'SMS', 'Messagerie') NOT NULL,
    variables_json JSON,
    priorite INT DEFAULT 5,
    statut ENUM('En_attente', 'En_cours', 'Envoye', 'Echec') DEFAULT 'En_attente',
    tentatives INT DEFAULT 0,
    erreur_message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    envoye_le DATETIME,
    FOREIGN KEY (template_id) REFERENCES notification_templates(id_template) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_statut (statut),
    INDEX idx_priorite (priorite),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: notifications_historique
CREATE TABLE IF NOT EXISTS notifications_historique (
    id_historique INT PRIMARY KEY AUTO_INCREMENT,
    template_code VARCHAR(50),
    destinataire_id INT,
    canal ENUM('Email', 'SMS', 'Messagerie'),
    sujet VARCHAR(255),
    statut ENUM('Envoye', 'Echec', 'Bounce'),
    erreur_message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_destinataire (destinataire_id),
    INDEX idx_statut (statut),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: email_bounces
CREATE TABLE IF NOT EXISTS email_bounces (
    id_bounce INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    type_bounce ENUM('Hard', 'Soft') NOT NULL,
    raison TEXT,
    compteur INT DEFAULT 1,
    bloque BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_bloque (bloque)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: messages_internes
CREATE TABLE IF NOT EXISTS messages_internes (
    id_message INT PRIMARY KEY AUTO_INCREMENT,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    lu BOOLEAN DEFAULT FALSE,
    date_lecture DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_destinataire (destinataire_id),
    INDEX idx_lu (lu),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECTION 6: DOCUMENTS & ARCHIVES (8 tables)
-- =====================================================

-- Table: documents_generes
CREATE TABLE IF NOT EXISTS documents_generes (
    id_document INT PRIMARY KEY AUTO_INCREMENT,
    type_document VARCHAR(50) NOT NULL,
    entite_type VARCHAR(50),
    entite_id INT,
    chemin_fichier VARCHAR(500) NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    taille_octets BIGINT,
    hash_sha256 VARCHAR(64) NOT NULL,
    genere_par INT,
    genere_le DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (genere_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_type (type_document),
    INDEX idx_entite (entite_type, entite_id),
    INDEX idx_hash (hash_sha256)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: archives
CREATE TABLE IF NOT EXISTS archives (
    id_archive INT PRIMARY KEY AUTO_INCREMENT,
    document_id INT NOT NULL,
    hash_sha256 VARCHAR(64) NOT NULL,
    verifie BOOLEAN DEFAULT TRUE,
    derniere_verification DATETIME DEFAULT CURRENT_TIMESTAMP,
    verrouille BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (document_id) REFERENCES documents_generes(id_document) ON DELETE CASCADE,
    INDEX idx_verifie (verifie),
    INDEX idx_verification (derniere_verification)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: historique_entites
CREATE TABLE IF NOT EXISTS historique_entites (
    id_historique INT PRIMARY KEY AUTO_INCREMENT,
    entite_type VARCHAR(50) NOT NULL,
    entite_id INT NOT NULL,
    version INT NOT NULL,
    snapshot_json JSON NOT NULL,
    modifie_par INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (modifie_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_entite (entite_type, entite_id),
    INDEX idx_version (version),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: critere_evaluation
CREATE TABLE IF NOT EXISTS critere_evaluation (
    id_critere INT PRIMARY KEY AUTO_INCREMENT,
    code_critere VARCHAR(50) UNIQUE NOT NULL,
    libelle VARCHAR(255) NOT NULL,
    description TEXT,
    ponderation DECIMAL(5,2),
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_code (code_critere),
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mentions
CREATE TABLE IF NOT EXISTS mentions (
    id_mention INT PRIMARY KEY AUTO_INCREMENT,
    code_mention VARCHAR(50) UNIQUE NOT NULL,
    libelle_mention VARCHAR(100) NOT NULL,
    note_min DECIMAL(5,2) NOT NULL,
    note_max DECIMAL(5,2) NOT NULL,
    ordre_affichage INT,
    INDEX idx_code (code_mention)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: decisions_jury
CREATE TABLE IF NOT EXISTS decisions_jury (
    id_decision INT PRIMARY KEY AUTO_INCREMENT,
    soutenance_id INT NOT NULL,
    decision ENUM('Admis', 'Ajourné', 'Corrections_mineures', 'Corrections_majeures') NOT NULL,
    delai_corrections INT,
    commentaires TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (soutenance_id) REFERENCES soutenances(id_soutenance) ON DELETE CASCADE,
    INDEX idx_soutenance (soutenance_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: roles_jury
CREATE TABLE IF NOT EXISTS roles_jury (
    id_role INT PRIMARY KEY AUTO_INCREMENT,
    code_role VARCHAR(50) UNIQUE NOT NULL,
    libelle_role VARCHAR(100) NOT NULL,
    ordre_affichage INT,
    INDEX idx_code (code_role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: salles
CREATE TABLE IF NOT EXISTS salles (
    id_salle INT PRIMARY KEY AUTO_INCREMENT,
    nom_salle VARCHAR(100) UNIQUE NOT NULL,
    batiment VARCHAR(100),
    capacite INT,
    equipement_json JSON,
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_nom (nom_salle),
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECTION 7: CONFIGURATION & RÉFÉRENTIELS (14 tables)
-- =====================================================

-- Table: configuration_systeme
CREATE TABLE IF NOT EXISTS configuration_systeme (
    id_config INT PRIMARY KEY AUTO_INCREMENT,
    cle_config VARCHAR(100) UNIQUE NOT NULL,
    valeur_config TEXT,
    type_valeur ENUM('string', 'int', 'float', 'boolean', 'json') DEFAULT 'string',
    groupe_config VARCHAR(50),
    description TEXT,
    modifiable_ui BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_cle (cle_config),
    INDEX idx_groupe (groupe_config)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: traitement
CREATE TABLE IF NOT EXISTS traitement (
    id_traitement INT PRIMARY KEY AUTO_INCREMENT,
    lib_traitement VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    ordre_traitement INT,
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_ordre (ordre_traitement),
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: action
CREATE TABLE IF NOT EXISTS action (
    id_action INT PRIMARY KEY AUTO_INCREMENT,
    lib_action VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    INDEX idx_lib (lib_action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: rattacher
CREATE TABLE IF NOT EXISTS rattacher (
    id_rattacher INT PRIMARY KEY AUTO_INCREMENT,
    id_GU INT NOT NULL,
    id_traitement INT NOT NULL,
    id_action INT NOT NULL,
    UNIQUE KEY unique_permission (id_GU, id_traitement, id_action),
    INDEX idx_groupe (id_GU),
    INDEX idx_traitement (id_traitement),
    INDEX idx_action (id_action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: type_utilisateur
CREATE TABLE IF NOT EXISTS type_utilisateur (
    id_type_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    lib_type_utilisateur VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: groupe_utilisateur
CREATE TABLE IF NOT EXISTS groupe_utilisateur (
    id_GU INT PRIMARY KEY AUTO_INCREMENT,
    lib_GU VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    niveau_hierarchique INT,
    INDEX idx_niveau (niveau_hierarchique)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: niveau_acces_donnees
CREATE TABLE IF NOT EXISTS niveau_acces_donnees (
    id_niv_acces_donnee INT PRIMARY KEY AUTO_INCREMENT,
    lib_niveau_acces VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: niveau_approbation
CREATE TABLE IF NOT EXISTS niveau_approbation (
    id_niveau_approbation INT PRIMARY KEY AUTO_INCREMENT,
    lib_niveau VARCHAR(50) UNIQUE NOT NULL,
    ordre_niveau INT,
    INDEX idx_ordre (ordre_niveau)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: statut_jury
CREATE TABLE IF NOT EXISTS statut_jury (
    id_statut INT PRIMARY KEY AUTO_INCREMENT,
    lib_statut VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: escalade_niveaux
CREATE TABLE IF NOT EXISTS escalade_niveaux (
    id_niveau INT PRIMARY KEY AUTO_INCREMENT,
    niveau INT UNIQUE NOT NULL,
    nom_niveau VARCHAR(100) NOT NULL,
    delai_reponse_jours INT,
    INDEX idx_niveau (niveau)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: imports_historiques
CREATE TABLE IF NOT EXISTS imports_historiques (
    id_import INT PRIMARY KEY AUTO_INCREMENT,
    type_import VARCHAR(50) NOT NULL,
    fichier_nom VARCHAR(255) NOT NULL,
    nb_lignes_totales INT,
    nb_lignes_reussies INT,
    nb_lignes_erreurs INT,
    erreurs_json JSON,
    importe_par INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (importe_par) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL,
    INDEX idx_type (type_import),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: stats_cache
CREATE TABLE IF NOT EXISTS stats_cache (
    id_stat INT PRIMARY KEY AUTO_INCREMENT,
    cle_stat VARCHAR(100) UNIQUE NOT NULL,
    valeur_json JSON NOT NULL,
    expire_le DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cle (cle_stat),
    INDEX idx_expire (expire_le)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: maintenance_mode
CREATE TABLE IF NOT EXISTS maintenance_mode (
    id INT PRIMARY KEY AUTO_INCREMENT,
    actif BOOLEAN DEFAULT FALSE,
    message TEXT,
    debut_maintenance DATETIME,
    fin_maintenance DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: migrations
CREATE TABLE IF NOT EXISTS migrations (
    id_migration INT PRIMARY KEY AUTO_INCREMENT,
    migration_name VARCHAR(255) UNIQUE NOT NULL,
    executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


