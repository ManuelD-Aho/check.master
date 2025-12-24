# PRD 02 - Entités Académiques

**Module**: Gestion des Données Académiques  
**Version**: 2.0.0  
**Date**: 2025-12-24  
**Dépendances**: PRD 00 (Master), PRD 01 (Authentification)

---

## Table des Matières

1. [Vue d'Ensemble](#vue-densemble)
2. [Acteurs](#acteurs)
3. [Scénarios Utilisateurs](#scénarios-utilisateurs)
4. [Requirements Fonctionnels](#requirements-fonctionnels)
5. [Schéma Base de Données](#schéma-base-de-données)
6. [Implémentation Technique](#implémentation-technique)
7. [Critères de Succès](#critères-de-succès)
8. [Tests Requis](#tests-requis)

---

## Vue d'Ensemble

Ce module gère l'ensemble des entités académiques du système : étudiants, enseignants, personnel administratif, entreprises partenaires, ainsi que la structure pédagogique (années académiques, semestres, UE, ECUE). Il constitue le référentiel de données pour tous les autres modules.

### Entités Gérées

| Catégorie | Entités | Tables |
|-----------|---------|--------|
| Acteurs | Étudiants, Enseignants, Personnel | 3 |
| Structure | Années, Semestres, Niveaux | 3 |
| Pédagogie | UE, ECUE | 2 |
| Référentiels | Grades, Fonctions, Spécialités | 3 |
| Partenaires | Entreprises | 1 |

### Règles Métier Clés

1. **Numéro Carte Étudiant** : Format alphanumérique (max 20 chars), unique, non modifiable sauf admin avec justification
2. **Email Unique** : Chaque email est unique dans chaque table (étudiant, enseignant, personnel)
3. **Une Année Active** : Une seule année académique active à un instant T
4. **Entité Avant Compte** : L'entité métier DOIT exister AVANT la création du compte utilisateur

---

## Acteurs

| Acteur | Responsabilités |
|--------|-----------------|
| **Administrateur** | Gestion complète de toutes les entités |
| **Scolarité** | Création/modification étudiants, inscriptions |
| **Resp. Filière** | Gestion des UE/ECUE de sa filière |
| **Secrétaire** | Consultation et export des données |
| **Étudiant** | Consultation de ses propres données |

---

## Scénarios Utilisateurs

### Scénario 1 : Création d'un Étudiant
1. L'agent de scolarité accède au formulaire de création
2. Saisit les informations : numéro carte, nom, prénom, email, téléphone
3. Renseigne la promotion et les informations de naissance
4. Valide le formulaire
5. Le système vérifie l'unicité du numéro carte et de l'email
6. L'étudiant est créé, un compte utilisateur peut être associé

**Critères d'Acceptation :**
- [ ] Numéro carte format validé (ex: CI01552852)
- [ ] Email unique dans le système
- [ ] Données enregistrées en moins de 2 secondes
- [ ] Historique de création tracé

### Scénario 2 : Gestion des Années Académiques
1. L'administrateur crée une nouvelle année académique
2. Définit les dates de début et fin
3. Marque l'année comme active (une seule active à la fois)
4. Crée les semestres associés
5. Les inscriptions peuvent commencer

**Critères d'Acceptation :**
- [ ] Une seule année active à un instant T
- [ ] Dates cohérentes (début < fin)
- [ ] Semestres liés automatiquement disponibles
- [ ] Bascule d'année auditable

### Scénario 3 : Configuration UE/ECUE
1. Le responsable de filière accède à la gestion pédagogique
2. Crée une UE avec code, libellé, crédits
3. Associe l'UE à un niveau et semestre
4. Ajoute les ECUE constituant l'UE
5. Le total des crédits ECUE correspond aux crédits UE

**Critères d'Acceptation :**
- [ ] Code UE unique dans le système
- [ ] Crédits entre 1 et 30
- [ ] Liaison niveau/semestre obligatoire
- [ ] ECUE héritent du semestre de l'UE parent

### Scénario 4 : Recherche d'Étudiant
1. Un utilisateur habilité accède à la recherche
2. Saisit un critère (nom, numéro carte, email)
3. Le système affiche les résultats correspondants
4. L'utilisateur peut accéder au dossier complet

**Critères d'Acceptation :**
- [ ] Recherche par nom partiel (minimum 2 caractères)
- [ ] Résultats en moins de 1 seconde
- [ ] Pagination si plus de 50 résultats
- [ ] Filtrage par promotion possible

---

## Requirements Fonctionnels

### RF-010 : Gestion des Étudiants
**Description** : Le système permet de créer et gérer les fiches étudiants.  
**Acteur** : Scolarité, Administrateur  
**Conditions** : Droits de création sur la ressource "étudiants"  
**Résultat** :
- Fiche étudiant créée avec numéro carte unique
- Format numéro : alphanumérique 20 caractères max
- Données personnelles complètes (nom, prénom, email, téléphone)
- Informations de naissance et promotion

### RF-011 : Gestion des Enseignants
**Description** : Le système permet de gérer le corps enseignant.  
**Acteur** : Administrateur  
**Conditions** : Droits de gestion enseignants  
**Résultat** :
- Fiche enseignant avec coordonnées complètes
- Association à un grade (Professeur, Maître de Conférences, etc.)
- Association à une fonction (Directeur, Responsable, etc.)
- Association à une ou plusieurs spécialités

### RF-012 : Gestion du Personnel Administratif
**Description** : Le système gère les agents administratifs.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Fiche personnel avec coordonnées
- Association à une fonction
- Statut actif/inactif

### RF-013 : Gestion des Entreprises
**Description** : Le système maintient un référentiel d'entreprises partenaires.  
**Acteur** : Administrateur, Scolarité  
**Conditions** : Droits de gestion entreprises  
**Résultat** :
- Fiche entreprise (nom, secteur, adresse, contacts)
- Réutilisable pour plusieurs stages
- Statut actif/inactif

### RF-014 : Gestion des Années Académiques
**Description** : Le système gère le calendrier académique.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Création année avec dates début/fin
- Une seule année active simultanément
- Basculement d'année avec confirmation

### RF-015 : Gestion des Semestres
**Description** : Les années sont divisées en semestres.  
**Acteur** : Administrateur  
**Conditions** : Année académique existante  
**Résultat** :
- Semestre lié à une année
- Dates propres au semestre
- Libellé descriptif

### RF-016 : Gestion des UE
**Description** : Les Unités d'Enseignement structurent le programme.  
**Acteur** : Resp. Filière, Administrateur  
**Conditions** : Niveau et semestre existants  
**Résultat** :
- Code UE unique
- Libellé descriptif
- Nombre de crédits
- Association niveau et semestre

### RF-017 : Gestion des ECUE
**Description** : Les ECUE détaillent le contenu des UE.  
**Acteur** : Resp. Filière, Administrateur  
**Conditions** : UE parent existante  
**Résultat** :
- Code ECUE unique
- Libellé
- Crédits (sous-ensemble de l'UE)
- Lien vers UE parent

### RF-018 : Gestion des Grades
**Description** : Référentiel des grades académiques.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Libellé du grade
- Niveau hiérarchique (pour tri et préséance)

### RF-019 : Gestion des Spécialités
**Description** : Domaines d'expertise des enseignants.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Libellé de la spécialité
- Description optionnelle
- Utilisé pour suggestion de jurys

---

## Critères de Succès

| Métrique | Objectif |
|----------|----------|
| Temps création étudiant | < 3 secondes |
| Temps recherche | < 1 seconde pour 95% des cas |
| Intégrité données | 0 doublons numéro carte |
| Couverture données | 100% des champs obligatoires renseignés |
| Historisation | 100% des modifications tracées |

---

## Tests Requis

### Tests Unitaires

```php
// Tests Modèle Etudiant
class EtudiantTest extends TestCase
{
    /** @test */
    public function testAttributsRequisEtudiant();
    
    /** @test */
    public function testNumeroEtudiantUnique();
    
    /** @test */
    public function testEmailEtudiantValide();
    
    /** @test */
    public function testNomComplet();
    
    /** @test */
    public function testStatutActif();
    
    /** @test */
    public function testFormatTelephoneIvoirien();
    
    /** @test */
    public function testPromotionAnnee();
    
    /** @test */
    public function testGenreValide();
    
    /** @test */
    public function testRechercheParNumero();
    
    /** @test */
    public function testRechercheParEmail();
    
    /** @test */
    public function testCalculAge();
}

// Tests Modèle Enseignant
class EnseignantTest extends TestCase
{
    /** @test */
    public function testAttributsRequisEnseignant();
    
    /** @test */
    public function testEmailEnseignantUnique();
    
    /** @test */
    public function testRelationGrade();
    
    /** @test */
    public function testRelationFonction();
    
    /** @test */
    public function testRelationSpecialite();
    
    /** @test */
    public function testNomFormelAvecGrade();
    
    /** @test */
    public function testDisponibilitePourSoutenance();
}

// Tests Modèle AnneeAcademique
class AnneeAcademiqueTest extends TestCase
{
    /** @test */
    public function testUneSeuleAnneeActive();
    
    /** @test */
    public function testActivationDesactiveAutres();
    
    /** @test */
    public function testDateDebutAvantFin();
    
    /** @test */
    public function testRelationSemestres();
    
    /** @test */
    public function testStatistiquesParEtat();
}

// Tests Validation
class EtudiantValidatorTest extends TestCase
{
    /** @test */
    public function testNumeroFormatValide();
    
    /** @test */
    public function testNumeroFormatInvalide();
    
    /** @test */
    public function testEmailObligatoire();
    
    /** @test */
    public function testEmailFormatInvalide();
    
    /** @test */
    public function testGenreChoixValide();
    
    /** @test */
    public function testPromotionFormatValide();
}
```

### Tests d'Intégration

```php
class EntitesAcademiquesIntegrationTest extends TestCase
{
    /** @test */
    public function testCreationEtudiantComplet();
    
    /** @test */
    public function testCreationCompteApresEtudiant();
    
    /** @test */
    public function testRechercheFulltext();
    
    /** @test */
    public function testImportExcel();
    
    /** @test */
    public function testCreationAnneeAvecSemestres();
    
    /** @test */
    public function testCreationUeAvecEcue();
}
```

### Cas de Test Critiques

| Test | Entrée | Résultat Attendu |
|------|--------|------------------|
| Création étudiant valide | Données complètes | Étudiant créé, ID retourné |
| Numéro carte dupliqué | Même num_etu | Erreur "Numéro déjà existant" |
| Email dupliqué | Même email | Erreur "Email déjà utilisé" |
| Recherche partielle | "Koua" | Tous les noms contenant "Koua" |
| Année active | Activation 2024-2025 | 2023-2024 désactivée |
| Suppression entité référencée | Étudiant avec dossier | Erreur avec dépendances |

---

## Notifications Associées

| Événement | Template | Destinataire | Canal |
|-----------|----------|--------------|-------|
| Étudiant créé | `etudiant_cree` | Scolarité | Messagerie |
| Compte étudiant créé | `compte_cree` | Étudiant | Email |
| Enseignant ajouté | `enseignant_ajoute` | Admin | Messagerie |
| Année académique activée | `annee_activee` | Tous admins | Email |
| Import terminé | `import_termine` | Utilisateur | Messagerie |

---

## Statistiques et Rapports

| Rapport | Description | Exportable |
|---------|-------------|------------|
| Liste étudiants par promotion | Groupement par année | Excel |
| Statistiques par genre | Répartition H/F | PDF |
| Enseignants par grade | Comptage par grade | Excel |
| Entreprises partenaires | Liste active | Excel |
| État des inscriptions | Par année académique | PDF |

---

## Historique des Modifications

| Version | Date | Auteur | Changements |
|---------|------|--------|-------------|
| 1.0.0 | 2025-12-14 | CheckMaster Team | Version initiale |
| 2.0.0 | 2025-12-24 | CheckMaster Team | Ajout schéma BDD, implémentation technique, validators, tests requis |

---

## Entités Métier

### Étudiant
- Numéro carte (unique, format CI01552852)
- Nom, Prénom
- Email (unique)
- Téléphone
- Date et lieu de naissance
- Genre
- Promotion
- Statut actif

### Enseignant
- Nom, Prénom
- Email (unique)
- Téléphone
- Grade associé
- Fonction associée
- Spécialité(s)
- Statut actif

### Personnel Administratif
- Nom, Prénom
- Email (unique)
- Téléphone
- Fonction
- Statut actif

### Entreprise
- Nom
- Secteur d'activité
- Adresse
- Contacts (téléphone, email, site web)
- Statut actif

### Année Académique
- Libellé (ex: 2024-2025)
- Date début
- Date fin
- Indicateur année active

### Semestre
- Libellé
- Année académique parent
- Dates propres

### Niveau d'Étude
- Libellé (Licence 1, Master 2, etc.)
- Description
- Ordre d'affichage

### UE (Unité d'Enseignement)
- Code (unique)
- Libellé
- Crédits
- Niveau associé
- Semestre associé

### ECUE (Élément Constitutif d'UE)
- Code (unique)
- Libellé
- Crédits
- UE parent

### Grade
- Libellé
- Niveau hiérarchique

### Fonction
- Libellé
- Description

### Spécialité
- Libellé
- Description

---

## Cas Limites et Erreurs

| Cas | Comportement |
|-----|--------------|
| Numéro carte déjà existant | Erreur explicite, suggestion de recherche |
| Email déjà utilisé | Erreur avec indication de l'entité existante |
| Suppression entité référencée | Refus avec liste des dépendances |
| Année académique sans semestre | Avertissement à la création |
| UE sans ECUE | Autorisé mais signalé |

---

## Dépendances

- **Module Authentification** : Création compte utilisateur après entité
- **Module Workflow** : Dossier étudiant lié à l'entité
- **Module Financier** : Paiements liés à étudiant et année

---

## Hors Périmètre

- Import automatique depuis SI externe
- Synchronisation temps réel avec autre système
- Gestion des anciens étudiants (alumni)
- Photos d'identité

---

## Schéma Base de Données

### Tables Principales

```sql
-- Table: etudiants
CREATE TABLE etudiants (
    id_etudiant INT PRIMARY KEY AUTO_INCREMENT,
    num_etu VARCHAR(20) UNIQUE NOT NULL,          -- Format: CI01552852
    nom_etu VARCHAR(100) NOT NULL,
    prenom_etu VARCHAR(100) NOT NULL,
    email_etu VARCHAR(255) UNIQUE NOT NULL,
    telephone_etu VARCHAR(20),
    date_naiss_etu DATE,
    lieu_naiss_etu VARCHAR(100),
    genre_etu ENUM('Homme', 'Femme', 'Autre'),
    promotion_etu VARCHAR(20),                     -- Ex: 2024
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_num (num_etu),
    INDEX idx_nom (nom_etu, prenom_etu),
    INDEX idx_email (email_etu),
    FULLTEXT idx_fulltext (nom_etu, prenom_etu, email_etu)
);

-- Table: enseignants
CREATE TABLE enseignants (
    id_enseignant INT PRIMARY KEY AUTO_INCREMENT,
    nom_ens VARCHAR(100) NOT NULL,
    prenom_ens VARCHAR(100) NOT NULL,
    email_ens VARCHAR(255) UNIQUE NOT NULL,
    telephone_ens VARCHAR(20),
    grade_id INT,                                  -- FK vers grades
    fonction_id INT,                               -- FK vers fonctions
    specialite_id INT,                             -- FK vers specialites
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email_ens),
    FULLTEXT idx_fulltext (nom_ens, prenom_ens, email_ens)
);

-- Table: personnel_admin
CREATE TABLE personnel_admin (
    id_pers_admin INT PRIMARY KEY AUTO_INCREMENT,
    nom_pers VARCHAR(100) NOT NULL,
    prenom_pers VARCHAR(100) NOT NULL,
    email_pers VARCHAR(255) UNIQUE NOT NULL,
    telephone_pers VARCHAR(20),
    fonction_id INT,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: entreprises
CREATE TABLE entreprises (
    id_entreprise INT PRIMARY KEY AUTO_INCREMENT,
    nom_entreprise VARCHAR(255) NOT NULL,
    secteur_activite VARCHAR(100),
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(255),
    site_web VARCHAR(255),
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: annee_academique
CREATE TABLE annee_academique (
    id_annee_acad INT PRIMARY KEY AUTO_INCREMENT,
    lib_annee_acad VARCHAR(20) UNIQUE NOT NULL,    -- Ex: 2024-2025
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    est_active BOOLEAN DEFAULT FALSE,
    INDEX idx_active (est_active)
);

-- Table: semestre
CREATE TABLE semestre (
    id_semestre INT PRIMARY KEY AUTO_INCREMENT,
    lib_semestre VARCHAR(50) NOT NULL,             -- Ex: Semestre 1
    annee_acad_id INT NOT NULL,
    date_debut DATE,
    date_fin DATE,
    FOREIGN KEY (annee_acad_id) REFERENCES annee_academique(id_annee_acad)
);

-- Table: niveau_etude
CREATE TABLE niveau_etude (
    id_niveau INT PRIMARY KEY AUTO_INCREMENT,
    lib_niveau VARCHAR(50) UNIQUE NOT NULL,        -- Ex: Master 2
    description TEXT,
    ordre_niveau INT                               -- Pour tri
);

-- Table: ue (Unités d'Enseignement)
CREATE TABLE ue (
    id_ue INT PRIMARY KEY AUTO_INCREMENT,
    code_ue VARCHAR(20) UNIQUE NOT NULL,           -- Ex: UE301
    lib_ue VARCHAR(255) NOT NULL,
    credits INT,                                   -- ECTS
    niveau_id INT,
    semestre_id INT,
    FOREIGN KEY (niveau_id) REFERENCES niveau_etude(id_niveau),
    FOREIGN KEY (semestre_id) REFERENCES semestre(id_semestre)
);

-- Table: ecue (Éléments Constitutifs)
CREATE TABLE ecue (
    id_ecue INT PRIMARY KEY AUTO_INCREMENT,
    code_ecue VARCHAR(20) UNIQUE NOT NULL,
    lib_ecue VARCHAR(255) NOT NULL,
    ue_id INT NOT NULL,
    credits INT,
    FOREIGN KEY (ue_id) REFERENCES ue(id_ue) ON DELETE CASCADE
);

-- Tables référentiels
CREATE TABLE grades (
    id_grade INT PRIMARY KEY AUTO_INCREMENT,
    lib_grade VARCHAR(100) UNIQUE NOT NULL,        -- Ex: Professeur Titulaire
    niveau_hierarchique INT                        -- Pour préséance jury
);

CREATE TABLE fonctions (
    id_fonction INT PRIMARY KEY AUTO_INCREMENT,
    lib_fonction VARCHAR(100) UNIQUE NOT NULL,     -- Ex: Doyen
    description TEXT
);

CREATE TABLE specialites (
    id_specialite INT PRIMARY KEY AUTO_INCREMENT,
    lib_specialite VARCHAR(100) UNIQUE NOT NULL,   -- Ex: Intelligence Artificielle
    description TEXT,
    actif BOOLEAN DEFAULT TRUE
);
```

### Données de Référence

```sql
-- Grades prédéfinis
INSERT INTO grades (lib_grade, niveau_hierarchique) VALUES
('Professeur Titulaire', 100),
('Professeur Agrégé', 90),
('Maître de Conférences HDR', 80),
('Maître de Conférences', 70),
('Maître Assistant', 60),
('Assistant', 50),
('Vacataire', 40);

-- Fonctions prédéfinies
INSERT INTO fonctions (lib_fonction, description) VALUES
('Doyen', 'Responsable UFR'),
('Vice-Doyen', 'Adjoint au Doyen'),
('Directeur de Département', 'Responsable département'),
('Responsable Filière', 'Responsable MIAGE'),
('Responsable Niveau', 'Responsable M1/M2'),
('Membre Commission', 'Membre commission validation'),
('Enseignant', 'Enseignant standard');

-- Niveaux d'étude
INSERT INTO niveau_etude (lib_niveau, ordre_niveau) VALUES
('Licence 1', 1),
('Licence 2', 2),
('Licence 3', 3),
('Master 1', 4),
('Master 2', 5);
```

---

## Implémentation Technique

### Modèle Etudiant

**Fichier**: `app/Models/Etudiant.php`

```php
<?php
declare(strict_types=1);

namespace App\Models;

class Etudiant extends Model
{
    protected string $table = 'etudiants';
    protected string $primaryKey = 'id_etudiant';
    protected array $fillable = [
        'num_etu', 'nom_etu', 'prenom_etu', 'email_etu',
        'telephone_etu', 'date_naiss_etu', 'lieu_naiss_etu',
        'genre_etu', 'promotion_etu', 'actif'
    ];

    // Constantes
    public const GENRE_HOMME = 'Homme';
    public const GENRE_FEMME = 'Femme';
    public const GENRE_AUTRE = 'Autre';

    // Relations
    public function dossiers(): array;
    public function paiements(): array;
    public function penalites(): array;

    // Recherche
    public static function findByNumero(string $numero): ?self;
    public static function findByEmail(string $email): ?self;
    public static function rechercher(string $terme, int $limit = 50): array;

    // Helpers
    public function getNomComplet(): string;
    public function getAge(): ?int;
    public function getDossierActif(): ?DossierEtudiant;
    public function estAJourFinancierement(): bool;
}
```

### Modèle Enseignant

**Fichier**: `app/Models/Enseignant.php`

```php
<?php
declare(strict_types=1);

namespace App\Models;

class Enseignant extends Model
{
    protected string $table = 'enseignants';
    protected string $primaryKey = 'id_enseignant';
    protected array $fillable = [
        'nom_ens', 'prenom_ens', 'email_ens', 'telephone_ens',
        'grade_id', 'fonction_id', 'specialite_id', 'actif'
    ];

    // Relations
    public function grade(): ?Grade;
    public function fonction(): ?Fonction;
    public function specialite(): ?Specialite;
    public function votesCommission(): array;
    public function participationsJury(): array;

    // Méthodes métier
    public function estMembreCommission(): bool;
    public function estDisponible(\DateTime $date): bool;
    public function nombreSoutenances(\DateTime $debut, \DateTime $fin): int;
    public function getNomFormelAvecGrade(): string;
}
```

### Modèle AnneeAcademique

**Fichier**: `app/Models/AnneeAcademique.php`

```php
<?php
declare(strict_types=1);

namespace App\Models;

class AnneeAcademique extends Model
{
    protected string $table = 'annee_academique';
    protected string $primaryKey = 'id_annee_acad';
    protected array $fillable = [
        'lib_annee_acad', 'date_debut', 'date_fin', 'est_active'
    ];

    // Recherche
    public static function active(): ?self;
    public static function ordonnees(): array;

    // État
    public function estActive(): bool;
    public function estEnCours(): bool;

    // Actions
    public function activer(): void;  // Désactive toutes les autres
    
    // Statistiques
    public function nombreDossiers(): int;
    public function statistiquesDossiersParEtat(): array;
    public function totalPaiements(): float;
}
```

### Service ServiceScolarite

**Fichier**: `app/Services/Scolarite/ServiceScolarite.php`

```php
<?php
declare(strict_types=1);

namespace App\Services\Scolarite;

class ServiceScolarite
{
    /**
     * Crée un nouvel étudiant avec validation
     */
    public function creerEtudiant(array $data): Etudiant;

    /**
     * Crée un compte utilisateur pour un étudiant existant
     */
    public function creerCompteEtudiant(Etudiant $etudiant): Utilisateur;

    /**
     * Importe des étudiants depuis un fichier Excel
     */
    public function importerEtudiants(string $fichierPath): array;

    /**
     * Recherche fulltext étudiants
     */
    public function rechercherEtudiants(string $terme, array $filtres = []): array;

    /**
     * Statistiques par promotion
     */
    public function statistiquesParPromotion(): array;
}
```

### Validators

**Fichier**: `app/Validators/EtudiantValidator.php`

```php
<?php
declare(strict_types=1);

namespace App\Validators;

use Symfony\Component\Validator\Constraints as Assert;

class EtudiantValidator
{
    public function rules(): array
    {
        return [
            'num_etu' => [
                new Assert\NotBlank(['message' => 'Le numéro est obligatoire']),
                new Assert\Length(['max' => 20]),
                new Assert\Regex([
                    'pattern' => '/^[A-Z]{2}[0-9]+$/',
                    'message' => 'Format invalide (ex: CI01552852)'
                ])
            ],
            'nom_etu' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 100])
            ],
            'prenom_etu' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 100])
            ],
            'email_etu' => [
                new Assert\NotBlank(),
                new Assert\Email(),
                new Assert\Length(['max' => 255])
            ],
            'telephone_etu' => [
                new Assert\Regex([
                    'pattern' => '/^\+?[0-9\s\-]+$/',
                    'message' => 'Format téléphone invalide'
                ])
            ],
            'date_naiss_etu' => [
                new Assert\Date()
            ],
            'genre_etu' => [
                new Assert\Choice(['choices' => ['Homme', 'Femme', 'Autre']])
            ],
            'promotion_etu' => [
                new Assert\Regex([
                    'pattern' => '/^20[0-9]{2}$/',
                    'message' => 'Format promotion invalide (ex: 2024)'
                ])
            ]
        ];
    }
}
```

---

## Critères de Succès
