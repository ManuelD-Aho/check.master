# PRD 01 - Authentification & Utilisateurs

**Module**: Sécurité et Gestion des Accès  
**Version**: 2.0.0  
**Date**: 2025-12-24  
**Dépendances**: PRD 00 (Master)

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

Ce module gère l'authentification sécurisée des utilisateurs, la gestion des sessions multi-appareils, le système de permissions granulaires (RBAC), et les rôles temporaires contextuels. Il constitue le socle de sécurité de l'ensemble du système CheckMaster.

### Principes de Sécurité

1. **DENY ALL par défaut** : Toute permission non explicitement accordée est refusée
2. **Hashage Argon2id** : Mots de passe stockés de manière irréversible
3. **Tokens cryptographiques** : Sessions avec tokens 128 caractères
4. **Audit complet** : Toute action d'authentification est journalisée
5. **Protection brute-force** : Verrouillage progressif automatique

---

## Acteurs

| Acteur | Responsabilités |
|--------|-----------------|
| **Utilisateur** | Se connecte, gère ses sessions, change son mot de passe |
| **Administrateur** | Crée/modifie utilisateurs, gère groupes, force déconnexion |
| **Président Jury** | Reçoit et utilise code temporaire jour J |
| **Système** | Génère codes, invalide sessions, applique verrouillages |

---

## Scénarios Utilisateurs

### Scénario 1 : Connexion Standard
1. L'utilisateur accède à la page de connexion
2. Saisit son identifiant (email) et mot de passe
3. Le système vérifie les identifiants
4. En cas de succès, une session est créée
5. L'utilisateur est redirigé vers son tableau de bord
6. Le menu affiché correspond à ses permissions

**Critères d'Acceptation :**
- [ ] Connexion réussie en moins de 2 secondes
- [ ] Session stockée avec IP et User-Agent
- [ ] Dernière connexion mise à jour
- [ ] Redirection selon le rôle principal

### Scénario 2 : Protection Brute-Force
1. Un attaquant tente des connexions avec mots de passe incorrects
2. Après 3 échecs : délai d'attente de 1 minute
3. Après 5 échecs : délai de 15 minutes
4. Après 10 échecs : compte verrouillé, admin alerté

**Critères d'Acceptation :**
- [ ] Compteur d'échecs incrémenté à chaque tentative
- [ ] Compte verrouillé après 10 tentatives
- [ ] Notification admin par email et messagerie
- [ ] Déblocage possible par admin uniquement

### Scénario 3 : Code Temporaire Président Jury
1. Une soutenance est planifiée pour le jour J
2. Le matin du jour J, le système génère un code à 8 caractères
3. Le code est envoyé au Président Jury par SMS et email
4. Le Président utilise ce code pour accéder au menu temporaire
5. À 23h59, le code et les droits temporaires sont révoqués

**Critères d'Acceptation :**
- [ ] Code généré automatiquement à 06h00 le jour J
- [ ] Format : 8 caractères alphanumériques (sans 0/O, 1/I)
- [ ] Validité : de 06h00 à 23h59 du jour uniquement
- [ ] Menu temporaire visible uniquement avec code actif

### Scénario 4 : Déconnexion Forcée
1. L'admin détecte une session suspecte
2. Accède à la liste des sessions actives d'un utilisateur
3. Sélectionne la session à terminer
4. Confirme la déconnexion forcée
5. L'utilisateur concerné est déconnecté immédiatement

**Critères d'Acceptation :**
- [ ] Liste des sessions avec IP, appareil, dernière activité
- [ ] Déconnexion effective en moins de 30 secondes
- [ ] Notification à l'utilisateur par messagerie interne
- [ ] Action auditée avec identité admin

---

## Requirements Fonctionnels

### RF-001 : Connexion Sécurisée
**Description** : Le système permet aux utilisateurs de s'authentifier avec email et mot de passe.  
**Acteur** : Utilisateur  
**Conditions** : 
- Compte existant et actif
- Email vérifié
- Compte non verrouillé  
**Résultat** : Session créée, utilisateur redirigé vers tableau de bord approprié

### RF-002 : Sessions Multi-Appareils
**Description** : Un utilisateur peut avoir plusieurs sessions simultanées sur différents appareils.  
**Acteur** : Utilisateur  
**Conditions** : Authentification réussie  
**Résultat** : 
- Nouvelle session créée sans invalider les existantes
- Chaque session identifiée par token unique
- Métadonnées conservées (IP, User-Agent, dernière activité)

### RF-003 : Protection Brute-Force
**Description** : Le système protège contre les tentatives de connexion répétées.  
**Acteur** : Système  
**Conditions** : Tentatives de connexion échouées  
**Résultat** :
- 3 échecs → délai 1 minute
- 5 échecs → délai 15 minutes
- 10 échecs → verrouillage + alerte admin

### RF-004 : Gestion Mots de Passe
**Description** : Le système impose une politique de mots de passe robuste.  
**Acteur** : Utilisateur, Admin  
**Conditions** : Création ou changement de mot de passe  
**Résultat** :
- Minimum 8 caractères, 1 majuscule, 1 chiffre, 1 spécial
- Stockage sécurisé irréversible
- Forçage du changement à première connexion

### RF-005 : Codes Temporaires
**Description** : Le système génère des codes d'accès temporaires pour le Président Jury.  
**Acteur** : Système, Président Jury  
**Conditions** : Soutenance planifiée pour le jour courant  
**Résultat** :
- Code généré automatiquement à 06h00
- Envoi par SMS (prioritaire) et email (backup)
- Validité limitée au jour J
- Révocation automatique à minuit

### RF-006 : Gestion des Groupes
**Description** : Les utilisateurs sont organisés en groupes avec niveaux hiérarchiques.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Création/modification/désactivation de groupes
- Attribution d'utilisateurs à un ou plusieurs groupes
- Niveau hiérarchique définit la préséance

### RF-007 : Permissions Granulaires
**Description** : Chaque groupe dispose de permissions CRUD par ressource.  
**Acteur** : Administrateur  
**Conditions** : Groupe existant  
**Résultat** :
- Définition : Lire, Créer, Modifier, Supprimer, Exporter, Valider
- Conditions JSON optionnelles (filtres contextuels)
- Cache des permissions (5 minutes)

### RF-008 : Rôles Temporaires
**Description** : Attribution de droits temporaires liés à un contexte.  
**Acteur** : Système, Admin  
**Conditions** : Événement déclencheur (ex: planification soutenance)  
**Résultat** :
- Droits additifs (n'enlèvent jamais de permissions)
- Validité bornée (date début/fin)
- Contexte spécifique (ex: soutenance_id)
- Révocation automatique à expiration

### RF-009 : Déconnexion Forcée
**Description** : L'administrateur peut forcer la déconnexion d'un utilisateur.  
**Acteur** : Administrateur  
**Conditions** : Droits admin, session active existante  
**Résultat** :
- Session invalidée immédiatement
- Utilisateur informé par messagerie
- Action auditée

---

## Critères de Succès

| Métrique | Objectif |
|----------|----------|
| Temps de connexion | < 2 secondes pour 95% des cas |
| Taux d'échec auth légitime | < 1% |
| Détection brute-force | 100% des attaques > 5 tentatives |
| Latence vérification permission | < 50ms (cache actif) |
| Couverture audit | 100% des actions sensibles |

---

## Tests Requis

### Tests Unitaires

```php
// Tests ServiceAuthentification
class ServiceAuthentificationTest extends TestCase
{
    /** @test */
    public function testHashageMotDePasseUtiliseArgon2id();
    
    /** @test */
    public function testVerificationMotDePasseCorrecte();
    
    /** @test */
    public function testVerificationMotDePasseIncorrecte();
    
    /** @test */
    public function testGenerationMotDePasseTemporaire();
    
    /** @test */
    public function testHashDifferentPourMemeMotDePasse();
    
    /** @test */
    public function testBruteForceDelaiApres3Echecs();
    
    /** @test */
    public function testBruteForceDelaiApres5Echecs();
    
    /** @test */
    public function testVerrouillageApres10Echecs();
    
    /** @test */
    public function testCreationSession();
    
    /** @test */
    public function testValidationSessionValide();
    
    /** @test */
    public function testValidationSessionExpiree();
    
    /** @test */
    public function testGenerationCodePresidentJury();
    
    /** @test */
    public function testCodePresidentJuryFormatValide();
    
    /** @test */
    public function testDeconnexionForcee();
}

// Tests ServicePermissions
class ServicePermissionsTest extends TestCase
{
    /** @test */
    public function testPermissionAccordee();
    
    /** @test */
    public function testPermissionRefusee();
    
    /** @test */
    public function testCachePermissions();
    
    /** @test */
    public function testInvalidationCache();
    
    /** @test */
    public function testRoleTemporaireAdditif();
    
    /** @test */
    public function testRoleTemporaireExpire();
    
    /** @test */
    public function testAdministrateurTousLesDroits();
}
```

### Tests d'Intégration

```php
class AuthenticationIntegrationTest extends TestCase
{
    /** @test */
    public function testConnexionCompleteAvecRedirection();
    
    /** @test */
    public function testDeconnexionSupprimeSession();
    
    /** @test */
    public function testChangementMotDePassePremiereConnexion();
    
    /** @test */
    public function testSessionsMultiplesParUtilisateur();
    
    /** @test */
    public function testMenuSelonGroupeUtilisateur();
}
```

### Cas de Test Critiques

| Test | Entrée | Résultat Attendu |
|------|--------|------------------|
| Login valide | email + mdp correct | Token session, redirection dashboard |
| Login invalide | email + mdp incorrect | Message générique, compteur++ |
| Compte verrouillé | login après 10 échecs | Message avec temps restant |
| Session expirée | token > 8h | Redirection login |
| Code Président | jour soutenance | Code 8 chars envoyé |
| Code invalide | code erroné | Refus + log tentative |
| Permission refusée | action non autorisée | 403 Forbidden |

---

## Notifications Associées

| Événement | Template | Destinataire | Canal |
|-----------|----------|--------------|-------|
| Compte créé | `compte_cree` | Utilisateur | Email |
| Connexion suspecte | `connexion_suspecte` | Admin | Email + Messagerie |
| Compte verrouillé | `compte_verrouille` | Admin | Email |
| Code Président | `code_president_jour_j` | Président | SMS + Email |
| Déconnexion forcée | `deconnexion_forcee` | Utilisateur | Messagerie |
| Mot de passe changé | `mdp_modifie` | Utilisateur | Email |

---

## Historique des Modifications

| Version | Date | Auteur | Changements |
|---------|------|--------|-------------|
| 1.0.0 | 2025-12-14 | CheckMaster Team | Version initiale |
| 2.0.0 | 2025-12-24 | CheckMaster Team | Ajout schéma BDD, implémentation technique, tests requis |

---

## Entités Métier

### Utilisateur
- Identifiant unique
- Email (login)
- Mot de passe (sécurisé)
- Statut (Actif, Inactif, Suspendu)
- Dernière connexion
- Compteur d'échecs
- Indicateur changement mot de passe requis

### Session Active
- Token unique
- Utilisateur associé
- Adresse IP
- Appareil (User-Agent)
- Dernière activité
- Date d'expiration

### Groupe
- Nom
- Description
- Niveau hiérarchique
- Statut actif

### Permission
- Groupe associé
- Ressource associée
- Droits (CRUD + Export + Valider)
- Conditions optionnelles

### Rôle Temporaire
- Utilisateur
- Code du rôle
- Type de contexte
- ID du contexte
- Permissions JSON
- Période de validité

---

## Cas Limites et Erreurs

| Cas | Comportement |
|-----|--------------|
| Email non trouvé | Message générique "Identifiants incorrects" |
| Compte verrouillé | Message avec heure de déverrouillage |
| Session expirée | Redirection vers connexion avec message |
| Code temporaire invalide | Refus d'accès, log de tentative |
| Double authentification même session | Régénération token |

---

## Dépendances

- **Module Audit** : Journalisation de toutes les actions
- **Module Communication** : Envoi SMS/Email pour codes
- **Module Administration** : Configuration politique sécurité

---

## Hors Périmètre

- Authentification OAuth2 / SSO
- Authentification biométrique
- Double facteur (2FA) généralisé
- Connexion par certificat client

---

## Schéma Base de Données

### Tables Impliquées

```sql
-- Table: utilisateurs
CREATE TABLE utilisateurs (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    nom_utilisateur VARCHAR(255) NOT NULL,
    login_utilisateur VARCHAR(255) UNIQUE NOT NULL,
    mdp_utilisateur VARCHAR(255) NOT NULL,          -- Argon2id hash
    id_type_utilisateur INT NOT NULL,
    id_GU INT NOT NULL,                              -- Groupe principal
    id_niv_acces_donnee INT,
    statut_utilisateur ENUM('Actif', 'Inactif', 'Suspendu') DEFAULT 'Actif',
    doit_changer_mdp BOOLEAN DEFAULT TRUE,
    derniere_connexion DATETIME,
    tentatives_echec INT DEFAULT 0,
    verrouille_jusqu_a DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: sessions_actives
CREATE TABLE sessions_actives (
    id_session INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    token_session VARCHAR(128) UNIQUE NOT NULL,     -- 64 bytes hex
    ip_adresse VARCHAR(45),                          -- IPv6 compatible
    user_agent TEXT,
    derniere_activite DATETIME DEFAULT CURRENT_TIMESTAMP,
    expire_a DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
);

-- Table: codes_temporaires
CREATE TABLE codes_temporaires (
    id_code INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    soutenance_id INT,                               -- Contexte
    code_hash VARCHAR(255) NOT NULL,                 -- Argon2id hash
    type ENUM('president_jury', 'reset_password', 'verification') NOT NULL,
    valide_de DATETIME NOT NULL,
    valide_jusqu_a DATETIME NOT NULL,
    utilise BOOLEAN DEFAULT FALSE,
    utilise_a DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
);

-- Table: groupes
CREATE TABLE groupes (
    id_groupe INT PRIMARY KEY AUTO_INCREMENT,
    nom_groupe VARCHAR(100) NOT NULL,
    description TEXT,
    niveau_hierarchique INT DEFAULT 0,               -- Plus haut = plus de droits
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: permissions
CREATE TABLE permissions (
    id_permission INT PRIMARY KEY AUTO_INCREMENT,
    groupe_id INT NOT NULL,
    ressource_id INT NOT NULL,
    peut_lire BOOLEAN DEFAULT FALSE,
    peut_creer BOOLEAN DEFAULT FALSE,
    peut_modifier BOOLEAN DEFAULT FALSE,
    peut_supprimer BOOLEAN DEFAULT FALSE,
    peut_exporter BOOLEAN DEFAULT FALSE,
    peut_valider BOOLEAN DEFAULT FALSE,
    conditions_json JSON,                            -- Conditions contextuelles
    UNIQUE KEY unique_groupe_ressource (groupe_id, ressource_id),
    FOREIGN KEY (groupe_id) REFERENCES groupes(id_groupe) ON DELETE CASCADE
);

-- Table: roles_temporaires
CREATE TABLE roles_temporaires (
    id_role_temp INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    role_code VARCHAR(50) NOT NULL,                  -- ex: 'president_jury'
    contexte_type VARCHAR(50),                       -- ex: 'soutenance'
    contexte_id INT,                                 -- ex: soutenance_id
    permissions_json JSON NOT NULL,                  -- Permissions accordées
    actif BOOLEAN DEFAULT TRUE,
    valide_de DATETIME NOT NULL,
    valide_jusqu_a DATETIME NOT NULL,
    cree_par INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE
);

-- Table: pister (Audit)
CREATE TABLE pister (
    id_pister INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT,
    action VARCHAR(100) NOT NULL,                    -- ex: 'login', 'logout', 'login_echec'
    entite_type VARCHAR(50),
    entite_id INT,
    donnees_snapshot JSON,                           -- Données contextuelles
    ip_adresse VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id_utilisateur) ON DELETE SET NULL
);
```

### Groupes Utilisateurs Prédéfinis

| ID | Nom | Niveau | Description |
|----|-----|--------|-------------|
| 5 | Administrateur | 100 | Contrôle total système |
| 6 | Secrétaire | 60 | Gestion documentaire |
| 7 | Communication | 70 | Vérification rapports |
| 8 | Scolarité | 80 | Finances et inscriptions |
| 9 | Resp. Filière | 50 | Supervision MIAGE |
| 10 | Resp. Niveau | 45 | Gestion niveau |
| 11 | Commission | 55 | Évaluation rapports |
| 12 | Enseignant | 40 | Supervision étudiants |
| 13 | Étudiant | 10 | Accès limité |

---

## Implémentation Technique

### ServiceAuthentification

**Fichier**: `app/Services/Security/ServiceAuthentification.php`

```php
<?php
declare(strict_types=1);

namespace App\Services\Security;

class ServiceAuthentification
{
    // Seuils brute-force
    private const SEUIL_DELAI_1 = 3;        // → 1 minute
    private const SEUIL_DELAI_2 = 5;        // → 15 minutes
    private const SEUIL_VERROUILLAGE = 10;  // → verrouillage total

    // Durée session
    private const DUREE_SESSION_HEURES = 8;

    /**
     * Authentifie un utilisateur
     * @return array{success: bool, user?: Utilisateur, token?: string, error?: string}
     */
    public function authentifier(string $email, string $password): array;

    /**
     * Vérifie un mot de passe contre son hash Argon2id
     */
    public function verifierMotDePasse(string $password, string $hash): bool;

    /**
     * Hash un mot de passe avec Argon2id
     */
    public function hasherMotDePasse(string $password): string;

    /**
     * Crée une nouvelle session
     */
    public function creerSession(int $userId): string;

    /**
     * Valide une session et retourne l'utilisateur
     */
    public function validerSession(string $token): ?Utilisateur;

    /**
     * Génère un code temporaire pour Président Jury
     */
    public function genererCodePresidentJury(int $userId, int $soutenanceId): string;

    /**
     * Force la déconnexion d'une session
     */
    public function forcerDeconnexion(int $sessionId, int $adminId): bool;
}
```

### ServicePermissions

**Fichier**: `app/Services/Security/ServicePermissions.php`

```php
<?php
declare(strict_types=1);

namespace App\Services\Security;

class ServicePermissions
{
    // Cache TTL
    private const CACHE_DUREE_SECONDES = 300;  // 5 minutes

    /**
     * Vérifie si un utilisateur a une permission sur une ressource
     */
    public static function verifier(int $userId, string $ressourceCode, string $action): bool;

    /**
     * Invalide le cache pour un utilisateur
     */
    public static function invaliderCache(int $userId): void;

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    public static function estAdministrateur(int $userId): bool;

    /**
     * Retourne toutes les permissions effectives
     */
    public static function getToutesPermissions(int $userId): array;
}
```

### Middleware AuthMiddleware

**Fichier**: `app/Middleware/AuthMiddleware.php`

```php
<?php
declare(strict_types=1);

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Vérifie l'authentification pour chaque requête
     * 
     * 1. Extrait le token du cookie ou header Authorization
     * 2. Valide le token via ServiceAuthentification
     * 3. Charge l'utilisateur dans Auth::user()
     * 4. Continue ou redirige vers login
     */
    public function handle(Request $request, callable $next): Response;
}
```

### Algorithme de Vérification des Permissions

```
FONCTION verifierPermission(utilisateur_id, ressource, action):
    
    // 1. Vérifier cache (performance)
    SI cache_existe(utilisateur_id, ressource) ET cache_valide():
        RETOURNER cache.permissions[action]
    
    // 2. Vérifier rôles temporaires actifs (additifs)
    roles_temp = obtenir_roles_temporaires_actifs(utilisateur_id)
    POUR CHAQUE role DANS roles_temp:
        SI role.permissions[ressource][action] == VRAI:
            RETOURNER VRAI
    
    // 3. Vérifier permissions groupe principal
    groupe_id = utilisateur.id_GU
    permission = obtenir_permission(groupe_id, ressource)
    SI permission[action] == VRAI:
        resultat = VRAI
    SINON:
        resultat = FAUX
    
    // 4. Mettre en cache (5 minutes)
    mettre_en_cache(utilisateur_id, ressource, permissions, TTL=300s)
    
    RETOURNER resultat
```

### Processus de Connexion

```
1. Utilisateur saisit email + mot de passe
2. Recherche utilisateur par login_utilisateur
3. SI non trouvé → Erreur générique + log
4. SI verrouillé → Erreur avec temps restant
5. SI inactif → Erreur compte désactivé
6. Vérification password_verify(mdp, hash)
7. SI incorrect → Incrémenter echecs + appliquer délai
8. SI correct:
   a. Réinitialiser compteur échecs
   b. MAJ derniere_connexion
   c. Créer session (token 128 chars)
   d. Log succès dans pister
   e. Retourner token
```

---

## Critères de Succès
