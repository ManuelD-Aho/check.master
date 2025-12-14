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

-- Insert into migrations tracking
INSERT INTO migrations (migration_name, executed_at) 
VALUES ('001_create_complete_database', NOW())
ON DUPLICATE KEY UPDATE executed_at = NOW();
