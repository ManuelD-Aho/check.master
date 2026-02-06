# PRD Module 1 : Gestion des Utilisateurs et Permissions (RBAC)

## 1. Vue d'ensemble

### 1.1 Objectif du module
Ce module constitue le socle fondamental de l'application. Il gère l'authentification, l'autorisation et le contrôle d'accès basé sur les rôles (RBAC - Role-Based Access Control). Tout le système repose sur les permissions attribuées aux groupes utilisateurs.

### 1.2 Principe fondamental
> **RÈGLE ABSOLUE** : Tout dépend des permissions du groupe utilisateur. Aucune action n'est possible sans la permission correspondante.

### 1.3 Bibliothèques utilisées
| Bibliothèque | Rôle dans ce module |
|--------------|---------------------|
| `symfony/security-core` | Briques fondamentales : voters, authentication, authorization |
| `symfony/security-http` | Pare-feu, formulaire de connexion, guards |
| `symfony/password-hasher` | Hachage sécurisé des mots de passe (Argon2id) |
| `symfony/security-csrf` | Protection CSRF sur tous les formulaires |
| `lcobucci/jwt` | Génération et validation des tokens JWT pour API/sessions |
| `spomky-labs/otphp` | Authentification à deux facteurs (2FA) pour admin/enseignants |
| `symfony/rate-limiter` | Protection contre le brute-force |
| `defuse/php-encryption` | Chiffrement des données sensibles en base |
| `symfony/event-dispatcher` | Événements de connexion/déconnexion pour audit |
| `monolog/monolog` | Journalisation des actions (audit trail) |

---

## 2. Entités et Modèle de données

### 2.1 Schéma des entités

```
type_utilisateur (1) ──────< (N) groupe_utilisateur
        │                              │
        │                              │
        ▼                              ▼
utilisateur (N) >──────────────< (1) groupe_utilisateur
        │
        │
        ▼
niveau_acces_donnees (1) ──────< (N) utilisateur
```

### 2.2 Tables impliquées

#### `type_utilisateur`
| Champ | Type | Description |
|-------|------|-------------|
| `id_type_utilisateur` | INT PK AUTO | Identifiant unique |
| `libelle_type_utilisateur` | VARCHAR(100) | Ex: "Étudiant", "Enseignant", "Personnel Administratif" |

**Valeurs fixes (non modifiables)** :
- 1 = Étudiant
- 2 = Enseignant  
- 3 = Personnel Administratif

#### `groupe_utilisateur`
| Champ | Type | Description |
|-------|------|-------------|
| `id_groupe_utilisateur` | INT PK AUTO | Identifiant unique |
| `libelle_groupe_utilisateur` | VARCHAR(100) | Ex: "Administrateur", "Membre Commission", "Secrétariat" |
| `id_type_utilisateur` | INT FK | Lien vers le type d'utilisateur |
| `description` | TEXT | Description du groupe |
| `actif` | BOOLEAN | Groupe actif ou non |
| `date_creation` | DATETIME | Date de création |

#### `niveau_acces_donnees`
| Champ | Type | Description |
|-------|------|-------------|
| `id_niveau_acces` | INT PK AUTO | Identifiant unique |
| `libelle_niveau_acces` | VARCHAR(100) | Ex: "Toutes données", "Département", "Personnel" |
| `code_niveau` | VARCHAR(20) | Code technique (ALL, DEPT, PERSONAL) |

#### `utilisateur`
| Champ | Type | Description |
|-------|------|-------------|
| `id_utilisateur` | INT PK AUTO | Identifiant unique |
| `nom_utilisateur` | VARCHAR(100) | Nom complet |
| `id_type_utilisateur` | INT FK | Type d'utilisateur |
| `id_groupe_utilisateur` | INT FK | Groupe d'appartenance |
| `id_niveau_acces` | INT FK | Niveau d'accès aux données |
| `statut_utilisateur` | ENUM | 'actif', 'inactif', 'bloque', 'en_attente' |
| `login_utilisateur` | VARCHAR(100) UNIQUE | Identifiant de connexion |
| `mot_de_passe_hash` | VARCHAR(255) | Mot de passe haché (Argon2id) |
| `email_utilisateur` | VARCHAR(255) | Email pour notifications |
| `secret_2fa` | VARCHAR(255) NULL | Secret TOTP pour 2FA (chiffré) |
| `is_2fa_enabled` | BOOLEAN | 2FA activé ou non |
| `derniere_connexion` | DATETIME NULL | Date/heure dernière connexion |
| `tentatives_connexion` | INT DEFAULT 0 | Compteur tentatives échouées |
| `date_blocage` | DATETIME NULL | Date de blocage automatique |
| `token_reinitialisation` | VARCHAR(255) NULL | Token reset password |
| `expiration_token` | DATETIME NULL | Expiration du token |
| `date_creation` | DATETIME | Date de création du compte |
| `date_modification` | DATETIME | Dernière modification |

#### `permissions`
| Champ | Type | Description |
|-------|------|-------------|
| `id_permission` | INT PK AUTO | Identifiant unique |
| `id_groupe_utilisateur` | INT FK | Groupe concerné |
| `id_fonctionnalite` | INT FK | Fonctionnalité concernée |
| `peut_voir` | BOOLEAN | Permission de lecture |
| `peut_creer` | BOOLEAN | Permission de création |
| `peut_modifier` | BOOLEAN | Permission de modification |
| `peut_supprimer` | BOOLEAN | Permission de suppression |
| `date_attribution` | DATETIME | Date d'attribution |

#### `fonctionnalites`
| Champ | Type | Description |
|-------|------|-------------|
| `id_fonctionnalite` | INT PK AUTO | Identifiant unique |
| `id_categorie` | INT FK | Catégorie parente |
| `code_fonctionnalite` | VARCHAR(50) UNIQUE | Code technique (ex: "ETU_LIST") |
| `libelle_fonctionnalite` | VARCHAR(100) | Nom affiché |
| `label_fonctionnalite` | VARCHAR(100) | Label court |
| `description_fonctionnalite` | TEXT | Description complète |
| `url_fonctionnalite` | VARCHAR(255) | Route associée |
| `icone_fonctionnalite` | VARCHAR(50) | Classe icône (FontAwesome) |
| `ordre_fonctionnalite` | INT | Ordre d'affichage |
| `est_sous_page` | BOOLEAN | Est une sous-page |
| `page_parente` | INT NULL FK | ID de la page parente |
| `actif` | BOOLEAN | Fonctionnalité active |
| `date_creation` | DATETIME | Date de création |

#### `categories_fonctionnalites`
| Champ | Type | Description |
|-------|------|-------------|
| `id_categorie` | INT PK AUTO | Identifiant unique |
| `code_categorie` | VARCHAR(50) UNIQUE | Code technique |
| `libelle_categorie` | VARCHAR(100) | Nom affiché (menu) |
| `description_categorie` | TEXT | Description |
| `icone_categorie` | VARCHAR(50) | Classe icône |
| `ordre_categorie` | INT | Ordre d'affichage |
| `actif` | BOOLEAN | Catégorie active |
| `date_creation` | DATETIME | Date de création |

#### `route_actions`
| Champ | Type | Description |
|-------|------|-------------|
| `id_route_action` | INT PK AUTO | Identifiant unique |
| `route_pattern` | VARCHAR(255) | Pattern de route (regex) |
| `http_method` | ENUM | 'GET', 'POST', 'PUT', 'DELETE' |
| `action_crud` | ENUM | 'voir', 'creer', 'modifier', 'supprimer' |
| `id_fonctionnalite` | INT FK | Fonctionnalité liée |
| `description` | VARCHAR(255) | Description de l'action |
| `actif` | BOOLEAN | Route active |
| `date_creation` | DATETIME | Date de création |
| `date_modification` | DATETIME | Dernière modification |

#### `auth_rate_limits`
| Champ | Type | Description |
|-------|------|-------------|
| `id` | INT PK AUTO | Identifiant unique |
| `action` | VARCHAR(50) | Type d'action (login, reset_password) |
| `ip_address` | VARCHAR(45) | Adresse IP |
| `identifier` | VARCHAR(255) | Identifiant utilisateur/email |
| `tentatives` | INT | Nombre de tentatives |
| `debut_fenetre` | DATETIME | Début de la fenêtre de rate limit |
| `derniere_tentative` | DATETIME | Dernière tentative |
| `bloque_jusqu` | DATETIME NULL | Date de fin de blocage |
| `date_creation` | DATETIME | Date de création |
| `date_modification` | DATETIME | Dernière modification |

#### `pister` (Audit Trail)
| Champ | Type | Description |
|-------|------|-------------|
| `id_piste` | INT PK AUTO | Identifiant unique |
| `id_utilisateur` | INT FK | Utilisateur concerné |
| `action` | VARCHAR(100) | Action effectuée |
| `statut_action` | ENUM | 'succes', 'echec', 'tentative' |
| `table_concernee` | VARCHAR(100) | Table modifiée |
| `id_enregistrement` | INT NULL | ID de l'enregistrement concerné |
| `donnees_avant` | JSON NULL | État avant modification |
| `donnees_apres` | JSON NULL | État après modification |
| `ip_address` | VARCHAR(45) | Adresse IP |
| `user_agent` | VARCHAR(255) | Navigateur utilisateur |
| `date_creation` | DATETIME | Date de l'action |

---

## 3. Fonctionnalités détaillées

### 3.1 Authentification

#### 3.1.1 Connexion standard
**Écran** : `/login`

**Champs** :
- Login (email ou matricule)
- Mot de passe
- Case "Se souvenir de moi" (optionnel, cookie 30 jours)

**Processus** :
1. Validation CSRF (symfony/security-csrf)
2. Vérification rate limiting (symfony/rate-limiter) : max 5 tentatives / 15 min
3. Recherche utilisateur par login
4. Vérification statut utilisateur (doit être 'actif')
5. Vérification mot de passe (symfony/password-hasher avec Argon2id)
6. Si 2FA activé → redirection vers écran 2FA
7. Génération token JWT (lcobucci/jwt) stocké en session
8. Journalisation connexion réussie (monolog)
9. Redirection vers dashboard selon type utilisateur

**Règles de gestion** :
- RG-AUTH-001 : Après 5 tentatives échouées, blocage IP pour 15 minutes
- RG-AUTH-002 : Après 10 tentatives échouées sur un compte, compte bloqué
- RG-AUTH-003 : Un utilisateur bloqué doit être débloqué par un admin
- RG-AUTH-004 : Le mot de passe doit avoir minimum 8 caractères, 1 majuscule, 1 chiffre

#### 3.1.2 Authentification à deux facteurs (2FA)
**Écran** : `/login/2fa`

**Bibliothèque** : `spomky-labs/otphp`

**Champs** :
- Code TOTP à 6 chiffres

**Processus** :
1. Affichage formulaire de saisie du code
2. Validation du code TOTP (fenêtre de 30 secondes, tolérance ±1)
3. Si valide → finalisation connexion
4. Si invalide → compteur tentatives 2FA, max 3 essais

**Configuration 2FA (première activation)** :
1. Génération secret TOTP aléatoire
2. Chiffrement du secret avec defuse/php-encryption avant stockage
3. Génération QR code pour application authenticator
4. Affichage codes de récupération (10 codes usage unique)
5. Validation par saisie d'un code pour confirmer activation

**Règles de gestion** :
- RG-2FA-001 : Le 2FA est obligatoire pour les administrateurs
- RG-2FA-002 : Le 2FA est optionnel mais recommandé pour les enseignants
- RG-2FA-003 : Les étudiants n'ont pas accès au 2FA
- RG-2FA-004 : Les codes de récupération sont à usage unique

#### 3.1.3 Réinitialisation de mot de passe
**Écran** : `/mot-de-passe/oublie`

**Champs** :
- Email

**Processus** :
1. Validation CSRF
2. Vérification rate limiting : max 3 demandes / heure / email
3. Vérification existence email en base
4. Génération token unique (32 bytes, bin2hex)
5. Stockage token hashé avec expiration (1 heure)
6. Envoi email avec lien de réinitialisation (phpmailer)
7. Journalisation demande

**Écran** : `/mot-de-passe/reinitialiser/{token}`

**Champs** :
- Nouveau mot de passe
- Confirmation mot de passe

**Processus** :
1. Vérification validité token (non expiré, non utilisé)
2. Validation complexité mot de passe
3. Hachage nouveau mot de passe (Argon2id)
4. Mise à jour en base, invalidation token
5. Envoi email de confirmation
6. Journalisation changement

### 3.2 Autorisation (RBAC)

#### 3.2.1 Middleware de vérification des permissions
Chaque requête passe par le middleware d'autorisation :

```
Requête → Middleware Auth → Vérification Permission → Action
                ↓
           Refus si non autorisé (HTTP 403)
```

**Processus** :
1. Extraction route et méthode HTTP de la requête
2. Correspondance route → fonctionnalité via `route_actions`
3. Récupération groupe utilisateur de la session
4. Vérification permission dans table `permissions`
5. Mapping méthode HTTP → action CRUD :
   - GET → peut_voir
   - POST → peut_creer
   - PUT/PATCH → peut_modifier
   - DELETE → peut_supprimer
6. Si permission accordée → continuer
7. Si permission refusée → HTTP 403 + journalisation

#### 3.2.2 Gestion des groupes utilisateurs
**Écran** : `/admin/groupes-utilisateurs`

**Permissions requises** : `GRP_USR_LIST` (voir), `GRP_USR_CREATE` (créer), etc.

**Fonctionnalités** :
- Liste paginée des groupes (white-october/pagerfanta)
- Création d'un nouveau groupe
- Modification d'un groupe existant
- Désactivation d'un groupe (jamais suppression physique)
- Attribution des permissions (matrice CRUD)

**Règles de gestion** :
- RG-GRP-001 : Un groupe ne peut pas être supprimé s'il contient des utilisateurs actifs
- RG-GRP-002 : Le groupe "Administrateur" ne peut pas être modifié
- RG-GRP-003 : Tout nouveau groupe hérite d'aucune permission par défaut

#### 3.2.3 Matrice des permissions
**Écran** : `/admin/permissions`

**Interface** : Tableau croisé dynamique

| Fonctionnalité | Voir | Créer | Modifier | Supprimer |
|----------------|------|-------|----------|-----------|
| Étudiants      | [x]  | [x]   | [x]      | [ ]       |
| Inscriptions   | [x]  | [x]   | [ ]      | [ ]       |
| Rapports       | [x]  | [ ]   | [ ]      | [ ]       |

**Sauvegarde** : AJAX avec validation côté serveur

### 3.3 Gestion des utilisateurs

#### 3.3.1 Création automatique d'utilisateur
Lorsqu'un étudiant, enseignant ou personnel est créé, un utilisateur est automatiquement généré :

**Processus** :
1. Création de l'entité source (Étudiant/Enseignant/Personnel)
2. Déclenchement événement `EntityCreatedEvent`
3. Listener `UserCreatorListener` :
   - Génération login unique (basé sur matricule ou email)
   - Génération mot de passe aléatoire sécurisé (16 caractères)
   - Création utilisateur avec groupe par défaut
   - Hachage mot de passe
4. Envoi email avec identifiants (phpmailer)
5. Journalisation création

**Règle** : 
- RG-USR-001 : Un utilisateur est toujours lié à exactement une entité source (Étudiant OU Enseignant OU Personnel)
- RG-USR-002 : Le mot de passe généré doit être changé à la première connexion

#### 3.3.2 Liste des utilisateurs
**Écran** : `/admin/utilisateurs`

**Colonnes** :
- Login
- Nom complet
- Type utilisateur
- Groupe utilisateur
- Statut
- Dernière connexion
- Actions

**Filtres** :
- Par type utilisateur
- Par groupe
- Par statut
- Recherche textuelle (login, nom)

**Actions** :
- Voir détails
- Modifier
- Changer groupe
- Réinitialiser mot de passe
- Activer/Désactiver
- Débloquer

#### 3.3.3 Modification d'utilisateur
**Écran** : `/admin/utilisateurs/{id}/modifier`

**Champs modifiables** :
- Groupe utilisateur
- Niveau d'accès aux données
- Statut

**Champs non modifiables** :
- Login
- Type utilisateur (lié à l'entité source)

### 3.4 Audit Trail (Journalisation)

#### 3.4.1 Événements journalisés
| Événement | Données enregistrées |
|-----------|---------------------|
| Connexion réussie | utilisateur, IP, user-agent, date |
| Connexion échouée | login tenté, IP, raison échec |
| Déconnexion | utilisateur, date |
| Modification permission | utilisateur modifiant, groupe, permissions avant/après |
| Création utilisateur | admin créateur, utilisateur créé |
| Blocage/Déblocage | admin, utilisateur concerné, raison |
| Changement mot de passe | utilisateur, méthode (reset/manuel) |
| Activation 2FA | utilisateur |

#### 3.4.2 Consultation des logs
**Écran** : `/admin/audit`

**Permissions requises** : `AUDIT_VIEW`

**Filtres** :
- Période (date début - date fin)
- Utilisateur
- Type d'action
- Statut (succès/échec)
- Table concernée

**Export** : CSV via league/csv

---

## 4. Intégration technique

### 4.1 Configuration Symfony Security

```php
// config/security.php
return [
    'password_hashers' => [
        User::class => [
            'algorithm' => 'argon2id',
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 1,
        ],
    ],
    'providers' => [
        'user_provider' => [
            'entity' => [
                'class' => User::class,
                'property' => 'login',
            ],
        ],
    ],
];
```

### 4.2 Rate Limiter

```php
// Configuration rate limiter
$factory = new RateLimiterFactory([
    'id' => 'login',
    'policy' => 'sliding_window',
    'limit' => 5,
    'interval' => '15 minutes',
]);
```

### 4.3 JWT Configuration

```php
// Configuration JWT
$config = Configuration::forSymmetricSigner(
    new Sha256(),
    InMemory::plainText(getenv('JWT_SECRET'))
);

// Durée de validité : 8 heures
$token = $config->builder()
    ->issuedAt($now)
    ->expiresAt($now->modify('+8 hours'))
    ->withClaim('user_id', $user->getId())
    ->withClaim('group_id', $user->getGroupId())
    ->getToken($config->signer(), $config->signingKey());
```

### 4.4 Encryption des secrets 2FA

```php
// Chiffrement du secret TOTP avant stockage
$key = Key::loadFromAsciiSafeString(getenv('ENCRYPTION_KEY'));
$ciphertext = Crypto::encrypt($totpSecret, $key);
$user->setSecret2fa($ciphertext);

// Déchiffrement pour validation
$decrypted = Crypto::decrypt($user->getSecret2fa(), $key);
```

---

## 5. Règles de gestion complètes

### 5.1 Authentification
| Code | Règle |
|------|-------|
| RG-AUTH-001 | Maximum 5 tentatives de connexion par IP sur 15 minutes |
| RG-AUTH-002 | Maximum 10 tentatives échouées par compte avant blocage |
| RG-AUTH-003 | Un compte bloqué nécessite intervention admin |
| RG-AUTH-004 | Mot de passe : min 8 caractères, 1 majuscule, 1 chiffre, 1 spécial |
| RG-AUTH-005 | Session expire après 8 heures d'inactivité |
| RG-AUTH-006 | "Se souvenir de moi" = cookie valide 30 jours |
| RG-AUTH-007 | Token de réinitialisation valide 1 heure |
| RG-AUTH-008 | Maximum 3 demandes de réinitialisation par heure par email |

### 5.2 Authentification 2FA
| Code | Règle |
|------|-------|
| RG-2FA-001 | 2FA obligatoire pour groupe "Administrateur" |
| RG-2FA-002 | 2FA optionnel pour enseignants |
| RG-2FA-003 | 2FA non disponible pour étudiants |
| RG-2FA-004 | Codes de récupération : 10 codes, usage unique |
| RG-2FA-005 | Maximum 3 tentatives de code 2FA |
| RG-2FA-006 | Tolérance TOTP : ±1 période (30s chaque) |

### 5.3 Groupes et permissions
| Code | Règle |
|------|-------|
| RG-GRP-001 | Groupe avec utilisateurs actifs non supprimable |
| RG-GRP-002 | Groupe "Administrateur" immuable |
| RG-GRP-003 | Nouveau groupe = aucune permission par défaut |
| RG-GRP-004 | Un utilisateur appartient à exactement un groupe |
| RG-GRP-005 | Changement de groupe effectif immédiatement |

### 5.4 Utilisateurs
| Code | Règle |
|------|-------|
| RG-USR-001 | Utilisateur lié à une seule entité source |
| RG-USR-002 | Premier mot de passe doit être changé |
| RG-USR-003 | Login unique dans tout le système |
| RG-USR-004 | Suppression logique uniquement (statut 'inactif') |
| RG-USR-005 | Création utilisateur génère email automatique |

### 5.5 Audit
| Code | Règle |
|------|-------|
| RG-AUD-001 | Toute action sensible est journalisée |
| RG-AUD-002 | Logs non modifiables, non supprimables |
| RG-AUD-003 | Rétention logs : 5 ans minimum |
| RG-AUD-004 | Export logs autorisé pour admin uniquement |

---

## 6. Cas d'erreur et messages

| Code erreur | Message utilisateur | Action système |
|-------------|---------------------|----------------|
| AUTH_001 | "Identifiants incorrects" | Incrémenter compteur tentatives |
| AUTH_002 | "Compte temporairement bloqué. Réessayez dans X minutes" | Afficher temps restant |
| AUTH_003 | "Compte désactivé. Contactez l'administration" | Aucune action |
| AUTH_004 | "Session expirée. Veuillez vous reconnecter" | Redirection login |
| AUTH_005 | "Code 2FA incorrect" | Incrémenter compteur 2FA |
| PERM_001 | "Accès non autorisé" | Log tentative, redirection dashboard |
| PERM_002 | "Votre session a été invalidée" | Forcer déconnexion |

---

## 7. Dépendances inter-modules

| Module dépendant | Dépendance | Description |
|------------------|------------|-------------|
| Tous les modules | Utilisateurs & Permissions | Vérification permissions avant chaque action |
| Étudiants | Création utilisateur | Génération automatique utilisateur étudiant |
| Enseignants | Création utilisateur | Génération automatique utilisateur enseignant |
| Personnel Admin | Création utilisateur | Génération automatique utilisateur personnel |

---

## 8. Points d'attention sécurité

1. **Injection SQL** : Toutes les requêtes via Doctrine ORM (requêtes préparées)
2. **XSS** : Échappement systématique des sorties (htmlpurifier pour contenu riche)
3. **CSRF** : Token sur tous les formulaires POST
4. **Brute force** : Rate limiting sur authentification
5. **Mots de passe** : Jamais en clair, Argon2id uniquement
6. **Sessions** : Régénération ID après connexion
7. **Secrets** : Variables d'environnement, jamais en code
8. **2FA secrets** : Chiffrés en base avec defuse/php-encryption
