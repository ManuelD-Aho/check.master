-- ============================================================================
-- SCHEMA SQL COMPLET - PLATEFORME GESTION STAGES ET SOUTENANCES MIAGE-GI
-- ============================================================================
-- Version: 1.0
-- Date: 2025-02-04
-- Charset: UTF8MB4
-- Collation: utf8mb4_unicode_ci
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- 1. TABLES DE REFERENCE (PARAMETRAGE)
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: type_utilisateur
-- Description: Types d'utilisateurs du systeme (Etudiant, Enseignant, Personnel)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS type_utilisateur (
    id_type_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    code_type_utilisateur VARCHAR(20) NOT NULL UNIQUE,
    libelle_type_utilisateur VARCHAR(100) NOT NULL,
    description TEXT,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: groupe_utilisateur
-- Description: Groupes de permissions RBAC
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS groupe_utilisateur (
    id_groupe_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    code_groupe VARCHAR(50) NOT NULL UNIQUE,
    libelle_groupe VARCHAR(100) NOT NULL,
    id_type_utilisateur INT NOT NULL,
    description TEXT,
    est_modifiable BOOLEAN DEFAULT TRUE,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_type_utilisateur) REFERENCES type_utilisateur(id_type_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: niveau_acces_donnees
-- Description: Niveaux d'acces aux donnees (ALL, DEPT, PERSONAL)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS niveau_acces_donnees (
    id_niveau_acces INT AUTO_INCREMENT PRIMARY KEY,
    code_niveau VARCHAR(20) NOT NULL UNIQUE,
    libelle_niveau VARCHAR(100) NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: annee_academique
-- Description: Annees academiques du systeme
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS annee_academique (
    id_annee_academique INT AUTO_INCREMENT PRIMARY KEY,
    libelle_annee VARCHAR(20) NOT NULL UNIQUE,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    est_active BOOLEAN DEFAULT FALSE,
    est_ouverte_inscription BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CHECK (date_fin > date_debut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: filiere
-- Description: Filieres/Specialites (MIAGE, Genie Logiciel, etc.)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS filiere (
    id_filiere INT AUTO_INCREMENT PRIMARY KEY,
    code_filiere VARCHAR(20) NOT NULL UNIQUE,
    libelle_filiere VARCHAR(100) NOT NULL,
    description TEXT,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: niveau_etude
-- Description: Niveaux d'etude (M1, M2) avec montants associes
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS niveau_etude (
    id_niveau_etude INT AUTO_INCREMENT PRIMARY KEY,
    code_niveau VARCHAR(10) NOT NULL UNIQUE,
    libelle_niveau VARCHAR(50) NOT NULL,
    ordre_progression INT NOT NULL,
    montant_scolarite DECIMAL(10,2) NOT NULL,
    montant_inscription DECIMAL(10,2) NOT NULL,
    id_responsable INT NULL,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: semestre
-- Description: Semestres lies aux niveaux d'etude
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS semestre (
    id_semestre INT AUTO_INCREMENT PRIMARY KEY,
    code_semestre VARCHAR(10) NOT NULL UNIQUE,
    libelle_semestre VARCHAR(50) NOT NULL,
    id_niveau_etude INT NOT NULL,
    ordre INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_niveau_etude) REFERENCES niveau_etude(id_niveau_etude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: specialite
-- Description: Specialites des enseignants
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS specialite (
    id_specialite INT AUTO_INCREMENT PRIMARY KEY,
    code_specialite VARCHAR(20) NOT NULL UNIQUE,
    libelle_specialite VARCHAR(100) NOT NULL,
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: grade
-- Description: Grades des enseignants (Prof, MCF, etc.)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS grade (
    id_grade INT AUTO_INCREMENT PRIMARY KEY,
    code_grade VARCHAR(20) NOT NULL UNIQUE,
    libelle_grade VARCHAR(100) NOT NULL,
    abreviation VARCHAR(20) NOT NULL,
    ordre_hierarchique INT NOT NULL,
    peut_presider_jury BOOLEAN DEFAULT FALSE,
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: fonction
-- Description: Fonctions du personnel administratif et enseignant
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS fonction (
    id_fonction INT AUTO_INCREMENT PRIMARY KEY,
    code_fonction VARCHAR(20) NOT NULL UNIQUE,
    libelle_fonction VARCHAR(100) NOT NULL,
    type_fonction ENUM('enseignant', 'administratif') NOT NULL,
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. TABLES ENTITES PRINCIPALES (NON MODIFIABLES)
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: etudiant
-- Description: Etudiants de la plateforme (matricule VARCHAR)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS etudiant (
    matricule_etudiant VARCHAR(20) PRIMARY KEY,
    nom_etudiant VARCHAR(100) NOT NULL,
    prenom_etudiant VARCHAR(100) NOT NULL,
    email_etudiant VARCHAR(255) NOT NULL UNIQUE,
    telephone_etudiant VARCHAR(20),
    date_naissance DATE NOT NULL,
    lieu_naissance VARCHAR(100) NOT NULL,
    genre ENUM('M', 'F') NOT NULL,
    nationalite VARCHAR(50) DEFAULT 'Ivoirienne',
    adresse TEXT,
    promotion VARCHAR(20) NOT NULL,
    photo_profil VARCHAR(255),
    id_filiere INT NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_filiere) REFERENCES filiere(id_filiere),
    INDEX idx_etudiant_promotion (promotion),
    INDEX idx_etudiant_filiere (id_filiere)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: enseignant
-- Description: Enseignants de la plateforme (matricule VARCHAR)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS enseignant (
    matricule_enseignant VARCHAR(20) PRIMARY KEY,
    nom_enseignant VARCHAR(100) NOT NULL,
    prenom_enseignant VARCHAR(100) NOT NULL,
    email_enseignant VARCHAR(255) NOT NULL UNIQUE,
    telephone_enseignant VARCHAR(20),
    id_specialite INT,
    type_enseignant ENUM('permanent', 'vacataire') NOT NULL DEFAULT 'permanent',
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_specialite) REFERENCES specialite(id_specialite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: personnel_administratif
-- Description: Personnel administratif (matricule VARCHAR)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS personnel_administratif (
    matricule_personnel VARCHAR(20) PRIMARY KEY,
    nom_personnel VARCHAR(100) NOT NULL,
    prenom_personnel VARCHAR(100) NOT NULL,
    email_personnel VARCHAR(255) NOT NULL UNIQUE,
    telephone_personnel VARCHAR(20),
    poste VARCHAR(100),
    date_embauche DATE,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. TABLES UTILISATEURS ET PERMISSIONS
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: utilisateur
-- Description: Comptes utilisateurs du systeme
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    login_utilisateur VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    email_utilisateur VARCHAR(255) NOT NULL,
    nom_complet VARCHAR(200) NOT NULL,
    id_type_utilisateur INT NOT NULL,
    id_groupe_utilisateur INT NOT NULL,
    id_niveau_acces INT NOT NULL,
    -- Lien vers entite source (un seul rempli)
    matricule_etudiant VARCHAR(20) NULL UNIQUE,
    matricule_enseignant VARCHAR(20) NULL UNIQUE,
    matricule_personnel VARCHAR(20) NULL UNIQUE,
    -- Securite
    statut_utilisateur ENUM('actif', 'inactif', 'bloque', 'en_attente') DEFAULT 'en_attente',
    secret_2fa VARCHAR(255) NULL,
    is_2fa_enabled BOOLEAN DEFAULT FALSE,
    codes_recuperation_2fa TEXT NULL,
    premiere_connexion BOOLEAN DEFAULT TRUE,
    -- Tracking connexion
    derniere_connexion DATETIME NULL,
    tentatives_connexion INT DEFAULT 0,
    date_blocage DATETIME NULL,
    -- Reset password
    token_reinitialisation VARCHAR(255) NULL,
    expiration_token DATETIME NULL,
    -- Timestamps
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Foreign keys
    FOREIGN KEY (id_type_utilisateur) REFERENCES type_utilisateur(id_type_utilisateur),
    FOREIGN KEY (id_groupe_utilisateur) REFERENCES groupe_utilisateur(id_groupe_utilisateur),
    FOREIGN KEY (id_niveau_acces) REFERENCES niveau_acces_donnees(id_niveau_acces),
    FOREIGN KEY (matricule_etudiant) REFERENCES etudiant(matricule_etudiant),
    FOREIGN KEY (matricule_enseignant) REFERENCES enseignant(matricule_enseignant),
    FOREIGN KEY (matricule_personnel) REFERENCES personnel_administratif(matricule_personnel),
    -- Indexes
    INDEX idx_utilisateur_statut (statut_utilisateur),
    INDEX idx_utilisateur_groupe (id_groupe_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: categorie_fonctionnalite
-- Description: Categories de menus/fonctionnalites
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categorie_fonctionnalite (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    code_categorie VARCHAR(50) NOT NULL UNIQUE,
    libelle_categorie VARCHAR(100) NOT NULL,
    description_categorie TEXT,
    icone_categorie VARCHAR(50),
    ordre_affichage INT NOT NULL DEFAULT 0,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: fonctionnalite
-- Description: Pages/Fonctionnalites du systeme
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS fonctionnalite (
    id_fonctionnalite INT AUTO_INCREMENT PRIMARY KEY,
    id_categorie INT NOT NULL,
    code_fonctionnalite VARCHAR(50) NOT NULL UNIQUE,
    libelle_fonctionnalite VARCHAR(100) NOT NULL,
    label_court VARCHAR(50),
    description_fonctionnalite TEXT,
    url_fonctionnalite VARCHAR(255) NOT NULL,
    icone_fonctionnalite VARCHAR(50),
    ordre_affichage INT NOT NULL DEFAULT 0,
    est_sous_page BOOLEAN DEFAULT FALSE,
    id_page_parente INT NULL,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categorie) REFERENCES categorie_fonctionnalite(id_categorie),
    FOREIGN KEY (id_page_parente) REFERENCES fonctionnalite(id_fonctionnalite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: permission
-- Description: Permissions CRUD par groupe/fonctionnalite
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS permission (
    id_permission INT AUTO_INCREMENT PRIMARY KEY,
    id_groupe_utilisateur INT NOT NULL,
    id_fonctionnalite INT NOT NULL,
    peut_voir BOOLEAN DEFAULT FALSE,
    peut_creer BOOLEAN DEFAULT FALSE,
    peut_modifier BOOLEAN DEFAULT FALSE,
    peut_supprimer BOOLEAN DEFAULT FALSE,
    date_attribution DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_groupe_utilisateur) REFERENCES groupe_utilisateur(id_groupe_utilisateur),
    FOREIGN KEY (id_fonctionnalite) REFERENCES fonctionnalite(id_fonctionnalite),
    UNIQUE KEY uk_permission_groupe_fonc (id_groupe_utilisateur, id_fonctionnalite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: route_action
-- Description: Mapping routes HTTP vers permissions
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS route_action (
    id_route_action INT AUTO_INCREMENT PRIMARY KEY,
    route_pattern VARCHAR(255) NOT NULL,
    http_method ENUM('GET', 'POST', 'PUT', 'PATCH', 'DELETE') NOT NULL,
    action_crud ENUM('voir', 'creer', 'modifier', 'supprimer') NOT NULL,
    id_fonctionnalite INT NOT NULL,
    description VARCHAR(255),
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_fonctionnalite) REFERENCES fonctionnalite(id_fonctionnalite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: auth_rate_limit
-- Description: Rate limiting pour securite authentification
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS auth_rate_limit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(50) NOT NULL,
    adresse_ip VARCHAR(45) NOT NULL,
    identifiant VARCHAR(255),
    tentatives INT DEFAULT 0,
    debut_fenetre DATETIME NOT NULL,
    derniere_tentative DATETIME NOT NULL,
    bloque_jusqu DATETIME NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_rate_limit_ip_action (adresse_ip, action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. TABLES INSCRIPTIONS ET PAIEMENTS
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: inscription
-- Description: Inscriptions des etudiants par annee academique
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS inscription (
    id_inscription INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(20) NOT NULL,
    id_niveau_etude INT NOT NULL,
    id_annee_academique INT NOT NULL,
    date_inscription DATE NOT NULL,
    statut_inscription ENUM('en_attente', 'partiel', 'solde', 'annulee', 'suspendue') DEFAULT 'en_attente',
    montant_inscription DECIMAL(10,2) NOT NULL,
    montant_scolarite DECIMAL(10,2) NOT NULL,
    nombre_tranches INT NOT NULL DEFAULT 1,
    montant_paye DECIMAL(10,2) DEFAULT 0.00,
    reste_a_payer DECIMAL(10,2) AS (montant_scolarite + montant_inscription - montant_paye) STORED,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (matricule_etudiant) REFERENCES etudiant(matricule_etudiant),
    FOREIGN KEY (id_niveau_etude) REFERENCES niveau_etude(id_niveau_etude),
    FOREIGN KEY (id_annee_academique) REFERENCES annee_academique(id_annee_academique),
    UNIQUE KEY uk_inscription_etudiant_annee (matricule_etudiant, id_annee_academique),
    CHECK (nombre_tranches BETWEEN 1 AND 4)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: versement
-- Description: Versements/Paiements des etudiants
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS versement (
    id_versement INT AUTO_INCREMENT PRIMARY KEY,
    id_inscription INT NOT NULL,
    montant_versement DECIMAL(10,2) NOT NULL,
    date_versement DATE NOT NULL,
    type_versement ENUM('inscription', 'scolarite') NOT NULL,
    methode_paiement ENUM('especes', 'cheque', 'virement', 'mobile_money') NOT NULL,
    reference_paiement VARCHAR(100),
    recu_genere BOOLEAN DEFAULT FALSE,
    chemin_recu VARCHAR(255),
    reference_recu VARCHAR(50),
    id_utilisateur_saisie INT NOT NULL,
    commentaire TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_inscription) REFERENCES inscription(id_inscription),
    FOREIGN KEY (id_utilisateur_saisie) REFERENCES utilisateur(id_utilisateur),
    CHECK (montant_versement > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: echeance
-- Description: Echeancier de paiement
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS echeance (
    id_echeance INT AUTO_INCREMENT PRIMARY KEY,
    id_inscription INT NOT NULL,
    numero_echeance INT NOT NULL,
    montant_echeance DECIMAL(10,2) NOT NULL,
    date_echeance DATE NOT NULL,
    statut_echeance ENUM('en_attente', 'payee', 'en_retard', 'partielle') DEFAULT 'en_attente',
    montant_paye DECIMAL(10,2) DEFAULT 0.00,
    date_paiement DATE NULL,
    FOREIGN KEY (id_inscription) REFERENCES inscription(id_inscription),
    UNIQUE KEY uk_echeance_inscription_numero (id_inscription, numero_echeance)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. TABLES UE/ECUE ET NOTES
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: unite_enseignement
-- Description: Unites d'enseignement (UE)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS unite_enseignement (
    id_ue INT AUTO_INCREMENT PRIMARY KEY,
    code_ue VARCHAR(20) NOT NULL,
    libelle_ue VARCHAR(100) NOT NULL,
    id_niveau_etude INT NOT NULL,
    id_semestre INT NOT NULL,
    id_annee_academique INT NOT NULL,
    credits INT NOT NULL,
    matricule_responsable VARCHAR(20),
    description TEXT,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_niveau_etude) REFERENCES niveau_etude(id_niveau_etude),
    FOREIGN KEY (id_semestre) REFERENCES semestre(id_semestre),
    FOREIGN KEY (id_annee_academique) REFERENCES annee_academique(id_annee_academique),
    FOREIGN KEY (matricule_responsable) REFERENCES enseignant(matricule_enseignant),
    UNIQUE KEY uk_ue_code_annee (code_ue, id_annee_academique)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: element_constitutif
-- Description: Elements constitutifs (ECUE) des UE
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS element_constitutif (
    id_ecue INT AUTO_INCREMENT PRIMARY KEY,
    code_ecue VARCHAR(20) NOT NULL,
    libelle_ecue VARCHAR(100) NOT NULL,
    id_ue INT NOT NULL,
    credits INT NOT NULL,
    matricule_enseignant VARCHAR(20),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ue) REFERENCES unite_enseignement(id_ue),
    FOREIGN KEY (matricule_enseignant) REFERENCES enseignant(matricule_enseignant)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: note
-- Description: Notes des etudiants
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS note (
    id_note INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(20) NOT NULL,
    id_annee_academique INT NOT NULL,
    id_semestre INT NOT NULL,
    id_ue INT NULL,
    id_ecue INT NULL,
    type_note ENUM('ue', 'ecue', 'moyenne_generale', 'moyenne_m1', 'moyenne_s1_m2') NOT NULL,
    note DECIMAL(4,2),
    commentaire TEXT,
    id_utilisateur_saisie INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (matricule_etudiant) REFERENCES etudiant(matricule_etudiant),
    FOREIGN KEY (id_annee_academique) REFERENCES annee_academique(id_annee_academique),
    FOREIGN KEY (id_semestre) REFERENCES semestre(id_semestre),
    FOREIGN KEY (id_ue) REFERENCES unite_enseignement(id_ue),
    FOREIGN KEY (id_ecue) REFERENCES element_constitutif(id_ecue),
    FOREIGN KEY (id_utilisateur_saisie) REFERENCES utilisateur(id_utilisateur),
    CHECK (note IS NULL OR (note >= 0 AND note <= 20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. TABLES CANDIDATURES ET STAGES
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: entreprise
-- Description: Referentiel des entreprises partenaires
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS entreprise (
    id_entreprise INT AUTO_INCREMENT PRIMARY KEY,
    raison_sociale VARCHAR(200) NOT NULL,
    sigle VARCHAR(50),
    secteur_activite VARCHAR(100),
    adresse TEXT,
    ville VARCHAR(100),
    pays VARCHAR(100) DEFAULT 'Cote d''Ivoire',
    telephone VARCHAR(20),
    email VARCHAR(255),
    site_web VARCHAR(255),
    description TEXT,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_entreprise_raison_sociale (raison_sociale)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: candidature
-- Description: Candidatures de stage des etudiants
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS candidature (
    id_candidature INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(20) NOT NULL,
    id_annee_academique INT NOT NULL,
    statut_candidature ENUM('brouillon', 'soumise', 'validee', 'rejetee') DEFAULT 'brouillon',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_soumission DATETIME NULL,
    date_traitement DATETIME NULL,
    id_validateur INT NULL,
    commentaire_validation TEXT,
    nombre_soumissions INT DEFAULT 1,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (matricule_etudiant) REFERENCES etudiant(matricule_etudiant),
    FOREIGN KEY (id_annee_academique) REFERENCES annee_academique(id_annee_academique),
    FOREIGN KEY (id_validateur) REFERENCES utilisateur(id_utilisateur),
    UNIQUE KEY uk_candidature_etudiant_annee (matricule_etudiant, id_annee_academique)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: information_stage
-- Description: Informations detaillees du stage
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS information_stage (
    id_info_stage INT AUTO_INCREMENT PRIMARY KEY,
    id_candidature INT NOT NULL UNIQUE,
    id_entreprise INT NOT NULL,
    sujet_stage VARCHAR(255) NOT NULL,
    description_stage TEXT NOT NULL,
    objectifs_stage TEXT,
    technologies_utilisees VARCHAR(500),
    date_debut_stage DATE NOT NULL,
    date_fin_stage DATE NOT NULL,
    duree_stage_jours INT AS (DATEDIFF(date_fin_stage, date_debut_stage)) STORED,
    nom_encadrant VARCHAR(100) NOT NULL,
    prenom_encadrant VARCHAR(100) NOT NULL,
    fonction_encadrant VARCHAR(100),
    email_encadrant VARCHAR(255) NOT NULL,
    telephone_encadrant VARCHAR(20) NOT NULL,
    adresse_stage TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_candidature) REFERENCES candidature(id_candidature),
    FOREIGN KEY (id_entreprise) REFERENCES entreprise(id_entreprise),
    CHECK (date_fin_stage > date_debut_stage)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: historique_candidature
-- Description: Historique JSON des actions sur candidatures
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS historique_candidature (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_candidature INT NOT NULL,
    action ENUM('creation', 'soumission', 'validation', 'rejet', 'modification') NOT NULL,
    snapshot_json JSON NOT NULL,
    id_auteur INT NOT NULL,
    commentaire TEXT,
    date_action DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_candidature) REFERENCES candidature(id_candidature),
    FOREIGN KEY (id_auteur) REFERENCES utilisateur(id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: motif_rejet_candidature
-- Description: Motifs de rejet predefinis (parametrable)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS motif_rejet_candidature (
    id_motif INT AUTO_INCREMENT PRIMARY KEY,
    code_motif VARCHAR(50) NOT NULL UNIQUE,
    libelle_motif VARCHAR(200) NOT NULL,
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. TABLES RAPPORTS DE STAGE
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: modele_rapport
-- Description: Modeles de rapport predefinis
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS modele_rapport (
    id_modele INT AUTO_INCREMENT PRIMARY KEY,
    nom_modele VARCHAR(100) NOT NULL,
    description_modele TEXT,
    contenu_html LONGTEXT NOT NULL,
    miniature VARCHAR(255),
    ordre_affichage INT DEFAULT 0,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: rapport
-- Description: Rapports de stage des etudiants
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS rapport (
    id_rapport INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(20) NOT NULL,
    id_annee_academique INT NOT NULL,
    id_modele INT NULL,
    titre_rapport VARCHAR(255) NOT NULL,
    theme_rapport VARCHAR(255) NOT NULL,
    contenu_html LONGTEXT NOT NULL,
    contenu_texte LONGTEXT,
    statut_rapport ENUM('brouillon', 'soumis', 'retourne', 'approuve', 'en_commission') DEFAULT 'brouillon',
    nombre_mots INT DEFAULT 0,
    nombre_pages_estime INT DEFAULT 0,
    version_courante INT DEFAULT 1,
    chemin_fichier_pdf VARCHAR(255),
    reference_document VARCHAR(50),
    taille_fichier INT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    date_soumission DATETIME NULL,
    date_approbation DATETIME NULL,
    FOREIGN KEY (matricule_etudiant) REFERENCES etudiant(matricule_etudiant),
    FOREIGN KEY (id_annee_academique) REFERENCES annee_academique(id_annee_academique),
    FOREIGN KEY (id_modele) REFERENCES modele_rapport(id_modele),
    UNIQUE KEY uk_rapport_etudiant_annee (matricule_etudiant, id_annee_academique)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: version_rapport
-- Description: Historique des versions du rapport
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS version_rapport (
    id_version INT AUTO_INCREMENT PRIMARY KEY,
    id_rapport INT NOT NULL,
    numero_version INT NOT NULL,
    contenu_html LONGTEXT NOT NULL,
    type_version ENUM('auto_save', 'soumission', 'modification') NOT NULL,
    id_auteur INT NOT NULL,
    commentaire TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rapport) REFERENCES rapport(id_rapport),
    FOREIGN KEY (id_auteur) REFERENCES utilisateur(id_utilisateur),
    UNIQUE KEY uk_version_rapport_numero (id_rapport, numero_version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: commentaire_rapport
-- Description: Commentaires sur les rapports
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS commentaire_rapport (
    id_commentaire INT AUTO_INCREMENT PRIMARY KEY,
    id_rapport INT NOT NULL,
    id_auteur INT NOT NULL,
    contenu_commentaire TEXT NOT NULL,
    type_commentaire ENUM('verification', 'commission', 'retour') NOT NULL,
    est_public BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rapport) REFERENCES rapport(id_rapport),
    FOREIGN KEY (id_auteur) REFERENCES utilisateur(id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: validation_rapport
-- Description: Actions de validation des rapports
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS validation_rapport (
    id_validation INT AUTO_INCREMENT PRIMARY KEY,
    id_rapport INT NOT NULL,
    id_validateur INT NOT NULL,
    action_validation ENUM('approuve', 'retourne') NOT NULL,
    motif_retour VARCHAR(100),
    commentaire_validation TEXT,
    date_validation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rapport) REFERENCES rapport(id_rapport),
    FOREIGN KEY (id_validateur) REFERENCES utilisateur(id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. TABLES COMMISSION
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: membre_commission
-- Description: Membres de la commission d'evaluation
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS membre_commission (
    id_membre INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    id_annee_academique INT NOT NULL,
    role_commission ENUM('president', 'membre') NOT NULL DEFAULT 'membre',
    actif BOOLEAN DEFAULT TRUE,
    date_nomination DATE NOT NULL,
    date_fin DATE NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (id_annee_academique) REFERENCES annee_academique(id_annee_academique),
    UNIQUE KEY uk_membre_commission_annee (id_utilisateur, id_annee_academique)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: evaluation_rapport
-- Description: Evaluations des rapports par la commission
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS evaluation_rapport (
    id_evaluation INT AUTO_INCREMENT PRIMARY KEY,
    id_rapport INT NOT NULL,
    id_evaluateur INT NOT NULL,
    numero_cycle INT DEFAULT 1,
    decision_evaluation ENUM('oui', 'non') NULL,
    note_qualite INT NULL,
    points_forts TEXT,
    points_ameliorer TEXT,
    commentaire TEXT,
    date_evaluation DATETIME NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rapport) REFERENCES rapport(id_rapport),
    FOREIGN KEY (id_evaluateur) REFERENCES utilisateur(id_utilisateur),
    UNIQUE KEY uk_evaluation_rapport_cycle (id_rapport, id_evaluateur, numero_cycle),
    CHECK (note_qualite IS NULL OR (note_qualite >= 1 AND note_qualite <= 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: affectation_encadrant
-- Description: Assignation des encadrants apres validation commission
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS affectation_encadrant (
    id_affectation INT AUTO_INCREMENT PRIMARY KEY,
    id_rapport INT NOT NULL,
    matricule_enseignant VARCHAR(20) NOT NULL,
    role_encadrement ENUM('directeur_memoire', 'encadreur_pedagogique') NOT NULL,
    date_affectation DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_affecteur INT NOT NULL,
    commentaire TEXT,
    FOREIGN KEY (id_rapport) REFERENCES rapport(id_rapport),
    FOREIGN KEY (matricule_enseignant) REFERENCES enseignant(matricule_enseignant),
    FOREIGN KEY (id_affecteur) REFERENCES utilisateur(id_utilisateur),
    UNIQUE KEY uk_affectation_rapport_role (id_rapport, role_encadrement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: session_commission
-- Description: Sessions de la commission (par mois/annee)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS session_commission (
    id_session INT AUTO_INCREMENT PRIMARY KEY,
    id_annee_academique INT NOT NULL,
    mois_session INT NOT NULL,
    annee_session INT NOT NULL,
    libelle_session VARCHAR(100) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    statut_session ENUM('ouverte', 'fermee', 'archivee') DEFAULT 'ouverte',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_annee_academique) REFERENCES annee_academique(id_annee_academique),
    UNIQUE KEY uk_session_mois_annee (mois_session, annee_session)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: compte_rendu_commission
-- Description: PV de la commission
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS compte_rendu_commission (
    id_compte_rendu INT AUTO_INCREMENT PRIMARY KEY,
    id_session INT NOT NULL,
    numero_pv VARCHAR(50) NOT NULL UNIQUE,
    titre_pv VARCHAR(255) NOT NULL,
    contenu_html LONGTEXT NOT NULL,
    chemin_fichier_pdf VARCHAR(255),
    statut_pv ENUM('brouillon', 'finalise', 'envoye') DEFAULT 'brouillon',
    id_createur INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_finalisation DATETIME NULL,
    FOREIGN KEY (id_session) REFERENCES session_commission(id_session),
    FOREIGN KEY (id_createur) REFERENCES utilisateur(id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: compte_rendu_rapport
-- Description: Association rapports/PV commission
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS compte_rendu_rapport (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_compte_rendu INT NOT NULL,
    id_rapport INT NOT NULL,
    ordre INT NOT NULL,
    remarque_specifique TEXT,
    FOREIGN KEY (id_compte_rendu) REFERENCES compte_rendu_commission(id_compte_rendu),
    FOREIGN KEY (id_rapport) REFERENCES rapport(id_rapport),
    UNIQUE KEY uk_cr_rapport (id_compte_rendu, id_rapport)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 9. TABLES JURYS ET SOUTENANCES
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: aptitude_soutenance
-- Description: Validation aptitude par l'encadreur pedagogique
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS aptitude_soutenance (
    id_aptitude INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(20) NOT NULL,
    id_annee_academique INT NOT NULL,
    matricule_encadreur VARCHAR(20) NOT NULL,
    est_apte BOOLEAN NULL,
    commentaire TEXT,
    date_validation DATETIME NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (matricule_etudiant) REFERENCES etudiant(matricule_etudiant),
    FOREIGN KEY (id_annee_academique) REFERENCES annee_academique(id_annee_academique),
    FOREIGN KEY (matricule_encadreur) REFERENCES enseignant(matricule_enseignant),
    UNIQUE KEY uk_aptitude_etudiant_annee (matricule_etudiant, id_annee_academique)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: role_jury
-- Description: Roles au sein d'un jury (President, Directeur, etc.)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS role_jury (
    id_role_jury INT AUTO_INCREMENT PRIMARY KEY,
    code_role VARCHAR(50) NOT NULL UNIQUE,
    libelle_role VARCHAR(100) NOT NULL,
    description TEXT,
    ordre_affichage INT NOT NULL,
    est_obligatoire BOOLEAN DEFAULT TRUE,
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: jury
-- Description: Jurys de soutenance
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS jury (
    id_jury INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(20) NOT NULL,
    id_annee_academique INT NOT NULL,
    statut_jury ENUM('en_composition', 'complet', 'valide') DEFAULT 'en_composition',
    id_createur INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_validation DATETIME NULL,
    FOREIGN KEY (matricule_etudiant) REFERENCES etudiant(matricule_etudiant),
    FOREIGN KEY (id_annee_academique) REFERENCES annee_academique(id_annee_academique),
    FOREIGN KEY (id_createur) REFERENCES utilisateur(id_utilisateur),
    UNIQUE KEY uk_jury_etudiant_annee (matricule_etudiant, id_annee_academique)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: composition_jury
-- Description: Membres composant un jury
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS composition_jury (
    id_composition INT AUTO_INCREMENT PRIMARY KEY,
    id_jury INT NOT NULL,
    matricule_enseignant VARCHAR(20) NULL,
    -- Pour maitre de stage externe (non enseignant)
    nom_externe VARCHAR(100) NULL,
    prenom_externe VARCHAR(100) NULL,
    fonction_externe VARCHAR(100) NULL,
    email_externe VARCHAR(255) NULL,
    telephone_externe VARCHAR(20) NULL,
    entreprise_externe VARCHAR(200) NULL,
    --
    id_role_jury INT NOT NULL,
    est_present BOOLEAN NULL,
    commentaire TEXT,
    date_affectation DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_affecteur INT NOT NULL,
    FOREIGN KEY (id_jury) REFERENCES jury(id_jury),
    FOREIGN KEY (matricule_enseignant) REFERENCES enseignant(matricule_enseignant),
    FOREIGN KEY (id_role_jury) REFERENCES role_jury(id_role_jury),
    FOREIGN KEY (id_affecteur) REFERENCES utilisateur(id_utilisateur),
    UNIQUE KEY uk_composition_jury_role (id_jury, id_role_jury)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: salle
-- Description: Salles de soutenance
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS salle (
    id_salle INT AUTO_INCREMENT PRIMARY KEY,
    code_salle VARCHAR(20) NOT NULL UNIQUE,
    libelle_salle VARCHAR(100) NOT NULL,
    capacite INT,
    equipements VARCHAR(255),
    batiment VARCHAR(100),
    etage VARCHAR(20),
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: soutenance
-- Description: Soutenances programmees
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS soutenance (
    id_soutenance INT AUTO_INCREMENT PRIMARY KEY,
    id_jury INT NOT NULL UNIQUE,
    matricule_etudiant VARCHAR(20) NOT NULL,
    id_salle INT NOT NULL,
    date_soutenance DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NULL,
    duree_minutes INT DEFAULT 60,
    theme_soutenance VARCHAR(255) NOT NULL,
    statut_soutenance ENUM('programmee', 'en_cours', 'terminee', 'reportee', 'annulee') DEFAULT 'programmee',
    observations TEXT,
    id_programmeur INT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_jury) REFERENCES jury(id_jury),
    FOREIGN KEY (matricule_etudiant) REFERENCES etudiant(matricule_etudiant),
    FOREIGN KEY (id_salle) REFERENCES salle(id_salle),
    FOREIGN KEY (id_programmeur) REFERENCES utilisateur(id_utilisateur),
    UNIQUE KEY uk_soutenance_salle_creneau (id_salle, date_soutenance, heure_debut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: critere_evaluation
-- Description: Criteres de notation des soutenances
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS critere_evaluation (
    id_critere INT AUTO_INCREMENT PRIMARY KEY,
    code_critere VARCHAR(50) NOT NULL UNIQUE,
    libelle_critere VARCHAR(100) NOT NULL,
    description TEXT,
    ordre_affichage INT NOT NULL,
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: bareme_critere
-- Description: Baremes par critere et annee academique
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bareme_critere (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_annee_academique INT NOT NULL,
    id_critere INT NOT NULL,
    bareme DECIMAL(4,2) NOT NULL,
    coefficient DECIMAL(3,2) DEFAULT 1.00,
    FOREIGN KEY (id_annee_academique) REFERENCES annee_academique(id_annee_academique),
    FOREIGN KEY (id_critere) REFERENCES critere_evaluation(id_critere),
    UNIQUE KEY uk_bareme_annee_critere (id_annee_academique, id_critere)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: note_soutenance
-- Description: Notes par critere pour chaque soutenance
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS note_soutenance (
    id_note INT AUTO_INCREMENT PRIMARY KEY,
    id_soutenance INT NOT NULL,
    id_critere INT NOT NULL,
    note DECIMAL(4,2) NOT NULL,
    commentaire TEXT,
    id_utilisateur_saisie INT NOT NULL,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_soutenance) REFERENCES soutenance(id_soutenance),
    FOREIGN KEY (id_critere) REFERENCES critere_evaluation(id_critere),
    FOREIGN KEY (id_utilisateur_saisie) REFERENCES utilisateur(id_utilisateur),
    UNIQUE KEY uk_note_soutenance_critere (id_soutenance, id_critere),
    CHECK (note >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: mention
-- Description: Mentions selon la moyenne finale
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS mention (
    id_mention INT AUTO_INCREMENT PRIMARY KEY,
    code_mention VARCHAR(20) NOT NULL UNIQUE,
    libelle_mention VARCHAR(50) NOT NULL,
    seuil_minimum DECIMAL(4,2) NOT NULL,
    seuil_maximum DECIMAL(4,2) NOT NULL,
    ordre INT NOT NULL,
    CHECK (seuil_maximum >= seuil_minimum)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: resultat_final
-- Description: Resultats finaux apres deliberation
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS resultat_final (
    id_resultat INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(20) NOT NULL,
    id_annee_academique INT NOT NULL,
    id_soutenance INT NOT NULL,
    note_memoire DECIMAL(4,2) NOT NULL,
    moyenne_m1 DECIMAL(4,2) NOT NULL,
    moyenne_s1_m2 DECIMAL(4,2),
    moyenne_finale DECIMAL(4,2) NOT NULL,
    id_mention INT,
    type_pv ENUM('standard', 'simplifie') NOT NULL,
    decision_jury ENUM('admis', 'ajourne', 'refuse') NOT NULL,
    date_deliberation DATETIME NOT NULL,
    valide BOOLEAN DEFAULT FALSE,
    id_validateur INT,
    -- References documents generes
    reference_annexe1 VARCHAR(50),
    reference_annexe2 VARCHAR(50),
    reference_annexe3 VARCHAR(50),
    reference_pv_final VARCHAR(50),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (matricule_etudiant) REFERENCES etudiant(matricule_etudiant),
    FOREIGN KEY (id_annee_academique) REFERENCES annee_academique(id_annee_academique),
    FOREIGN KEY (id_soutenance) REFERENCES soutenance(id_soutenance),
    FOREIGN KEY (id_mention) REFERENCES mention(id_mention),
    FOREIGN KEY (id_validateur) REFERENCES utilisateur(id_utilisateur),
    UNIQUE KEY uk_resultat_etudiant_annee (matricule_etudiant, id_annee_academique)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. TABLES AUDIT ET PARAMETRAGE
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: audit_log (pister)
-- Description: Journal d'audit de toutes les actions
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS audit_log (
    id_audit INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NULL,
    action VARCHAR(100) NOT NULL,
    statut_action ENUM('succes', 'echec', 'tentative') NOT NULL,
    table_concernee VARCHAR(100),
    id_enregistrement INT NULL,
    donnees_avant JSON NULL,
    donnees_apres JSON NULL,
    adresse_ip VARCHAR(45),
    user_agent VARCHAR(255),
    details TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur),
    INDEX idx_audit_date (date_creation),
    INDEX idx_audit_utilisateur (id_utilisateur),
    INDEX idx_audit_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: password_reset
-- Description: Tokens de reinitialisation de mot de passe
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS password_reset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reset_email (email),
    INDEX idx_reset_token (token_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: app_setting
-- Description: Parametres applicatifs configurables
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS app_setting (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json', 'encrypted') DEFAULT 'string',
    category VARCHAR(50),
    description VARCHAR(255),
    is_sensitive BOOLEAN DEFAULT FALSE,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT NULL,
    FOREIGN KEY (updated_by) REFERENCES utilisateur(id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: message_systeme
-- Description: Messages/libelles configurables
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS message_systeme (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    code_message VARCHAR(100) NOT NULL UNIQUE,
    categorie VARCHAR(50) NOT NULL,
    contenu_message TEXT NOT NULL,
    type_message ENUM('info', 'erreur', 'succes', 'warning') DEFAULT 'info',
    actif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: template_email
-- Description: Templates d'emails configurables
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS template_email (
    id_template INT AUTO_INCREMENT PRIMARY KEY,
    code_template VARCHAR(50) NOT NULL UNIQUE,
    sujet_email VARCHAR(255) NOT NULL,
    corps_html LONGTEXT NOT NULL,
    variables_disponibles TEXT,
    actif BOOLEAN DEFAULT TRUE,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: document_genere
-- Description: Registre des documents PDF generes
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS document_genere (
    id_document INT AUTO_INCREMENT PRIMARY KEY,
    reference_document VARCHAR(50) NOT NULL UNIQUE,
    type_document ENUM('recu', 'bulletin', 'rapport', 'pv_commission', 'planning', 'annexe1', 'annexe2', 'annexe3', 'pv_final') NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(500) NOT NULL,
    taille_fichier INT,
    mime_type VARCHAR(100) DEFAULT 'application/pdf',
    metadata JSON,
    id_utilisateur_generation INT NOT NULL,
    date_generation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur_generation) REFERENCES utilisateur(id_utilisateur),
    INDEX idx_document_type (type_document),
    INDEX idx_document_date (date_generation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. TABLES ASSOCIATIONS (LIENS ENSEIGNANTS)
-- ============================================================================

-- -----------------------------------------------------------------------------
-- Table: enseignant_grade (avoir)
-- Description: Historique des grades des enseignants
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS enseignant_grade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule_enseignant VARCHAR(20) NOT NULL,
    id_grade INT NOT NULL,
    date_attribution DATE NOT NULL,
    date_fin DATE NULL,
    actuel BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (matricule_enseignant) REFERENCES enseignant(matricule_enseignant),
    FOREIGN KEY (id_grade) REFERENCES grade(id_grade)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: enseignant_fonction (occuper)
-- Description: Fonctions occupees par les enseignants
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS enseignant_fonction (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule_enseignant VARCHAR(20) NOT NULL,
    id_fonction INT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NULL,
    actuel BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (matricule_enseignant) REFERENCES enseignant(matricule_enseignant),
    FOREIGN KEY (id_fonction) REFERENCES fonction(id_fonction)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 12. DONNEES INITIALES (SEEDS)
-- ============================================================================

-- Types d'utilisateurs
INSERT INTO type_utilisateur (code_type_utilisateur, libelle_type_utilisateur, description) VALUES
('ETU', 'Etudiant', 'Etudiants inscrits'),
('ENS', 'Enseignant', 'Corps enseignant'),
('ADMIN', 'Personnel Administratif', 'Personnel administratif');

-- Niveaux d'acces
INSERT INTO niveau_acces_donnees (code_niveau, libelle_niveau, description) VALUES
('ALL', 'Toutes les donnees', 'Acces a toutes les donnees du systeme'),
('DEPT', 'Departement', 'Acces limite au departement'),
('PERSONAL', 'Personnel', 'Acces limite aux donnees personnelles');

-- Groupes utilisateurs
INSERT INTO groupe_utilisateur (code_groupe, libelle_groupe, id_type_utilisateur, description, est_modifiable) VALUES
('ADMIN', 'Administrateur', 3, 'Administrateurs systeme avec tous les droits', FALSE),
('SECRETARIAT', 'Secretariat', 3, 'Personnel du secretariat', TRUE),
('COMMISSION', 'Membre Commission', 2, 'Membres de la commission d\'evaluation', TRUE),
('ENSEIGNANT', 'Enseignant Standard', 2, 'Enseignants sans role particulier', TRUE),
('ETUDIANT', 'Etudiant', 1, 'Etudiants standards', TRUE);

-- Filieres
INSERT INTO filiere (code_filiere, libelle_filiere, description) VALUES
('MIAGE', 'MIAGE', 'Methodes Informatiques Appliquees a la Gestion des Entreprises'),
('GL', 'Genie Logiciel', 'Specialite Genie Logiciel');

-- Niveaux d'etude
INSERT INTO niveau_etude (code_niveau, libelle_niveau, ordre_progression, montant_scolarite, montant_inscription) VALUES
('M1', 'Master 1', 1, 500000.00, 50000.00),
('M2', 'Master 2', 2, 600000.00, 50000.00);

-- Semestres
INSERT INTO semestre (code_semestre, libelle_semestre, id_niveau_etude, ordre) VALUES
('S1M1', 'Semestre 1 Master 1', 1, 1),
('S2M1', 'Semestre 2 Master 1', 1, 2),
('S1M2', 'Semestre 1 Master 2', 2, 3),
('S2M2', 'Semestre 2 Master 2 (Memoire)', 2, 4);

-- Grades
INSERT INTO grade (code_grade, libelle_grade, abreviation, ordre_hierarchique, peut_presider_jury) VALUES
('PT', 'Professeur Titulaire', 'Prof.', 1, TRUE),
('MC', 'Maitre de Conferences', 'Dr.', 2, TRUE),
('MA', 'Maitre Assistant', 'M.', 3, FALSE),
('AT', 'Assistant', 'M.', 4, FALSE);

-- Roles jury
INSERT INTO role_jury (code_role, libelle_role, description, ordre_affichage, est_obligatoire) VALUES
('president', 'President du Jury', 'Preside la soutenance', 1, TRUE),
('directeur_memoire', 'Directeur de Memoire', 'Encadre la redaction du memoire', 2, TRUE),
('encadreur_pedagogique', 'Encadreur Pedagogique', 'Membre de la commission, valide aptitude', 3, TRUE),
('maitre_stage', 'Maitre de Stage', 'Encadrant en entreprise', 4, TRUE),
('examinateur', 'Examinateur', 'Membre supplementaire du jury', 5, TRUE);

-- Criteres d'evaluation
INSERT INTO critere_evaluation (code_critere, libelle_critere, description, ordre_affichage) VALUES
('qualite_document', 'Qualite du document ecrit', 'Clarte, structure, redaction', 1),
('maitrise_sujet', 'Maitrise du sujet', 'Comprehension et expertise technique', 2),
('presentation_orale', 'Qualite de la presentation orale', 'Expression, supports, dynamisme', 3),
('reponses_questions', 'Pertinence des reponses aux questions', 'Precision et justesse des reponses', 4),
('respect_temps', 'Respect du temps imparti', 'Gestion du temps de presentation', 5);

-- Mentions
INSERT INTO mention (code_mention, libelle_mention, seuil_minimum, seuil_maximum, ordre) VALUES
('passable', 'Passable', 10.00, 11.99, 1),
('ab', 'Assez Bien', 12.00, 13.99, 2),
('bien', 'Bien', 14.00, 15.99, 3),
('tb', 'Tres Bien', 16.00, 20.00, 4);

-- Motifs de rejet candidature
INSERT INTO motif_rejet_candidature (code_motif, libelle_motif) VALUES
('SUJET_NON_CONFORME', 'Sujet non conforme au niveau Master'),
('DUREE_INSUFFISANTE', 'Duree de stage insuffisante'),
('INFO_ENTREPRISE_INCOMPLETE', 'Informations entreprise incompletes'),
('CONTACT_ENCADRANT_INVALIDE', 'Coordonnees encadrant invalides'),
('DESCRIPTION_INSUFFISANTE', 'Description du stage trop succincte'),
('AUTRE', 'Autre motif (preciser en commentaire)');

-- Modeles de rapport
INSERT INTO modele_rapport (nom_modele, description_modele, contenu_html, ordre_affichage) VALUES
('Standard MIAGE', 'Modele complet avec tous les chapitres recommandes', '<h1>Titre du Rapport</h1><h2>Remerciements</h2><p>[Vos remerciements]</p><h2>Resume</h2><p>[Resume en francais]</p><h2>Abstract</h2><p>[Resume en anglais]</p><h2>Introduction</h2><p>[Introduction generale]</p><h2>Chapitre 1 : Presentation de l''entreprise</h2><p>[Contenu]</p><h2>Chapitre 2 : Etude de l''existant</h2><p>[Contenu]</p><h2>Chapitre 3 : Conception</h2><p>[Contenu]</p><h2>Chapitre 4 : Realisation</h2><p>[Contenu]</p><h2>Conclusion</h2><p>[Conclusion]</p><h2>Bibliographie</h2><p>[References]</p><h2>Annexes</h2><p>[Annexes]</p>', 1),
('Simplifie', 'Structure allegee pour stages courts', '<h1>Titre du Rapport</h1><h2>Introduction</h2><p>[Contenu]</p><h2>Presentation du stage</h2><p>[Contenu]</p><h2>Travaux realises</h2><p>[Contenu]</p><h2>Conclusion</h2><p>[Contenu]</p>', 2),
('Personnalise', 'Page blanche avec structure minimale', '<h1>Titre du Rapport</h1><p>Commencez votre redaction...</p>', 3);

-- Parametres applicatifs
INSERT INTO app_setting (setting_key, setting_value, setting_type, category, description, is_sensitive) VALUES
('app_name', 'Plateforme MIAGE-GI', 'string', 'application', 'Nom de l''application', FALSE),
('app_timezone', 'Africa/Abidjan', 'string', 'application', 'Fuseau horaire', FALSE),
('app_locale', 'fr_FR', 'string', 'application', 'Langue par defaut', FALSE),
('pagination_default', '25', 'number', 'application', 'Elements par page', FALSE),
('session_timeout', '480', 'number', 'security', 'Timeout session (minutes)', FALSE),
('password_min_length', '8', 'number', 'security', 'Longueur minimale mot de passe', FALSE),
('login_max_attempts', '5', 'number', 'security', 'Tentatives max avant blocage', FALSE),
('login_lockout_duration', '15', 'number', 'security', 'Duree blocage (minutes)', FALSE),
('rapport_min_mots', '5000', 'number', 'application', 'Nombre minimum de mots pour soumettre un rapport', FALSE),
('soutenance_delai_min_jours', '7', 'number', 'application', 'Delai minimum pour programmer une soutenance (jours)', FALSE),
('email_enabled', 'true', 'boolean', 'email', 'Activer envoi emails', FALSE),
('maintenance_mode', 'false', 'boolean', 'maintenance', 'Mode maintenance actif', FALSE);

-- ============================================================================
-- FIN DU SCRIPT
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 1;
