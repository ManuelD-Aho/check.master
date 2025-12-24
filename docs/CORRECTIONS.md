# Corrections Détaillées du Projet CheckMaster

**Date** : 2025-12-24  
**Basé sur** : [AUDIT.md](./AUDIT.md)  
**Objectif** : Corrections pixel-perfect pour chaque erreur identifiée

---

## Table des Matières

1. [Corrections Critiques (P0)](#1-corrections-critiques-p0)
2. [Corrections Majeures (P1)](#2-corrections-majeures-p1)
3. [Corrections Mineures (P2)](#3-corrections-mineures-p2)
4. [Script de Validation](#4-script-de-validation)

---

## 1. Corrections Critiques (P0)

### 1.1 Router.php - Fichier Vide

**Fichier** : `src/Router.php`  
**Problème** : Le fichier est vide (1 ligne)  
**Impact** : Application non fonctionnelle

**Solution A : Implémenter un routeur simple**

```php
<?php

declare(strict_types=1);

namespace Src;

use Src\Http\Request;
use Src\Http\Response;
use Src\Exceptions\NotFoundException;

/**
 * Routeur HTTP pour CheckMaster
 * 
 * Gère le mapping des URLs vers les contrôleurs.
 */
class Router
{
    /**
     * Routes enregistrées
     * @var array<string, array<string, array{controller: string, action: string, middleware: array}>>
     */
    private array $routes = [];

    /**
     * Préfixe de namespace des contrôleurs
     */
    private string $controllerNamespace = 'App\\Controllers\\';

    /**
     * Instance du kernel pour les middleware
     */
    private ?Kernel $kernel = null;

    /**
     * Définit le kernel
     */
    public function setKernel(Kernel $kernel): self
    {
        $this->kernel = $kernel;
        return $this;
    }

    /**
     * Enregistre une route GET
     */
    public function get(string $uri, string $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $uri, $handler, $middleware);
    }

    /**
     * Enregistre une route POST
     */
    public function post(string $uri, string $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $uri, $handler, $middleware);
    }

    /**
     * Enregistre une route pour plusieurs méthodes
     */
    public function match(array $methods, string $uri, string $handler, array $middleware = []): self
    {
        foreach ($methods as $method) {
            $this->addRoute(strtoupper($method), $uri, $handler, $middleware);
        }
        return $this;
    }

    /**
     * Ajoute une route
     */
    private function addRoute(string $method, string $uri, string $handler, array $middleware): self
    {
        [$controller, $action] = explode('@', $handler);
        
        $this->routes[$method][$uri] = [
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware,
        ];
        
        return $this;
    }

    /**
     * Dispatch la requête vers le contrôleur approprié
     */
    public function dispatch(Request $request): Response
    {
        $method = $request::method();
        $uri = $request::uri();
        
        // Chercher une route exacte
        if (isset($this->routes[$method][$uri])) {
            return $this->executeRoute($this->routes[$method][$uri], $request);
        }
        
        // Chercher une route avec paramètres
        foreach ($this->routes[$method] ?? [] as $pattern => $route) {
            if ($params = $this->matchRoute($pattern, $uri)) {
                return $this->executeRoute($route, $request, $params);
            }
        }
        
        throw new NotFoundException('Route', $uri);
    }

    /**
     * Match une route avec paramètres
     */
    private function matchRoute(string $pattern, string $uri): ?array
    {
        // Convertir [i:id] en regex (?P<id>\d+)
        $regex = preg_replace('/\[i:(\w+)\]/', '(?P<$1>\d+)', $pattern);
        $regex = preg_replace('/\[a:(\w+)\]/', '(?P<$1>[a-zA-Z0-9_-]+)', $regex);
        $regex = '#^' . $regex . '$#';
        
        if (preg_match($regex, $uri, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }
        
        return null;
    }

    /**
     * Exécute une route
     */
    private function executeRoute(array $route, Request $request, array $params = []): Response
    {
        $controllerClass = $this->controllerNamespace . $route['controller'];
        $action = $route['action'];
        
        if (!class_exists($controllerClass)) {
            throw new NotFoundException('Controller', $controllerClass);
        }
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $action)) {
            throw new NotFoundException('Action', $action);
        }
        
        // Exécuter avec middleware si kernel disponible
        if ($this->kernel && !empty($route['middleware'])) {
            return $this->kernel->runRouteMiddleware(
                $route['middleware'],
                $request,
                fn() => $controller->$action(...array_values($params))
            );
        }
        
        return $controller->$action(...array_values($params));
    }

    /**
     * Charge les routes depuis un fichier
     */
    public function loadRoutes(string $path): self
    {
        $router = $this;
        require $path;
        return $this;
    }
}
```

**Solution B : Ajouter AltoRouter aux dépendances**

```bash
composer require altorouter/altorouter
```

Puis adapter le fichier routes.php pour utiliser notre Router natif ou garder AltoRouter.

---

### 1.2 WorkflowGateMiddleware - Constantes Inexistantes

**Fichier** : `app/Middleware/WorkflowGateMiddleware.php`  
**Problème** : Utilise des constantes sans le préfixe `ETAT_`  
**Impact** : Erreurs PHP Fatal

**Correction** :

```php
// AVANT (lignes 117-147)
public static function redaction(): self
{
    return new self('Rédaction du rapport', [
        WorkflowEtat::RAPPORT_VALIDE,
        WorkflowEtat::ATTENTE_AVIS_ENCADREUR,
        WorkflowEtat::PRET_POUR_JURY,
        WorkflowEtat::JURY_EN_CONSTITUTION,
    ]);
}

public static function soutenance(): self
{
    return new self('Soutenance', [
        WorkflowEtat::SOUTENANCE_PLANIFIEE,
        WorkflowEtat::SOUTENANCE_EN_COURS,
        WorkflowEtat::SOUTENANCE_TERMINEE,
    ]);
}

public static function commission(): self
{
    return new self('Commission', [
        WorkflowEtat::EN_ATTENTE_COMMISSION,
        WorkflowEtat::EN_EVALUATION_COMMISSION,
    ]);
}

// APRÈS (corrigé)
public static function redaction(): self
{
    return new self('Rédaction du rapport', [
        WorkflowEtat::ETAT_RAPPORT_VALIDE,
        WorkflowEtat::ETAT_ATTENTE_AVIS_ENCADREUR,
        WorkflowEtat::ETAT_PRET_POUR_JURY,
        WorkflowEtat::ETAT_JURY_EN_CONSTITUTION,
    ]);
}

public static function soutenance(): self
{
    return new self('Soutenance', [
        WorkflowEtat::ETAT_SOUTENANCE_PLANIFIEE,
        WorkflowEtat::ETAT_SOUTENANCE_EN_COURS,
        WorkflowEtat::ETAT_SOUTENANCE_TERMINEE,
    ]);
}

public static function commission(): self
{
    return new self('Commission', [
        WorkflowEtat::ETAT_EN_ATTENTE_COMMISSION,
        WorkflowEtat::ETAT_EN_EVALUATION_COMMISSION,
    ]);
}
```

---

### 1.3 Migrations - Numérotation en Double

**Problème** : Deux fichiers `002_*.sql`  
**Fichiers** :
- `database/migrations/002_add_rapport_annotations.sql`
- `database/migrations/002_create_notifications_table.sql`

**Correction** : Renommer le second fichier

```bash
# Renommer dans l'ordre chronologique
mv database/migrations/002_create_notifications_table.sql database/migrations/014_create_notifications_table.sql
```

---

### 1.4 AuthController - Méthode Inexistante

**Fichier** : `app/Controllers/AuthController.php`  
**Ligne** : 119  
**Problème** : `SessionActive::getSessionsUtilisateur()` n'existe pas

**Correction** :

```php
// AVANT (ligne 119)
$sessions = SessionActive::getSessionsUtilisateur($targetUserId);

// APRÈS
$sessions = SessionActive::pourUtilisateur($targetUserId);
```

---

### 1.5 ServiceAuthentification - Méthode Inexistante

**Fichier** : `app/Services/Security/ServiceAuthentification.php`  
**Ligne** : 263  
**Problème** : `SessionActive::supprimerToutesSessionsUtilisateur()` n'existe pas

**Correction** :

```php
// AVANT (ligne 263)
return SessionActive::supprimerToutesSessionsUtilisateur($userId);

// APRÈS
return SessionActive::supprimerPourUtilisateur($userId);
```

---

## 2. Corrections Majeures (P1)

### 2.1 AuthController - Trop Long

**Fichier** : `app/Controllers/AuthController.php`  
**Problème** : 298 lignes vs 50 lignes max (Constitution)

**Correction** : Extraire la logique vers des services

#### 2.1.1 Créer ServiceSession

```php
<?php
// app/Services/Core/ServiceSession.php

declare(strict_types=1);

namespace App\Services\Core;

/**
 * Service de gestion des sessions et cookies
 */
class ServiceSession
{
    private const DUREE_SESSION_HEURES = 8;

    /**
     * Définit le cookie de session
     */
    public function setSessionCookie(string $token): void
    {
        $expire = time() + (self::DUREE_SESSION_HEURES * 3600);
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        setcookie('session_token', $token, [
            'expires' => $expire,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Supprime le cookie de session
     */
    public function clearSessionCookie(): void
    {
        setcookie('session_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
        ]);
    }

    /**
     * Récupère l'URL de redirection après login
     */
    public function getRedirectAfterLogin(): string
    {
        $this->ensureSessionStarted();
        $url = $_SESSION['redirect_after_login'] ?? '/dashboard';
        unset($_SESSION['redirect_after_login']);
        return $url;
    }

    /**
     * Définit un message flash d'erreur
     */
    public function setFlashError(string $message): void
    {
        $this->ensureSessionStarted();
        $_SESSION['flash_error'] = $message;
    }

    /**
     * Définit un message flash de succès
     */
    public function setFlashSuccess(string $message): void
    {
        $this->ensureSessionStarted();
        $_SESSION['flash_success'] = $message;
    }

    /**
     * S'assure que la session est démarrée
     */
    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
```

#### 2.1.2 Refactoriser AuthController

```php
<?php
// app/Controllers/AuthController.php (version refactorisée)

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Security\ServiceAuthentification;
use App\Services\Core\ServiceSession;
use Src\Http\Request;
use Src\Http\Response;
use Src\Http\JsonResponse;
use Src\Support\Auth;

/**
 * Contrôleur d'Authentification (Refactorisé ≤50 lignes)
 */
class AuthController
{
    private ServiceAuthentification $authService;
    private ServiceSession $sessionService;

    public function __construct()
    {
        $this->authService = new ServiceAuthentification();
        $this->sessionService = new ServiceSession();
    }

    public function login(): Response
    {
        if (Auth::check()) {
            return Response::redirect('/dashboard');
        }
        if (Request::method() === 'POST') {
            return $this->processLogin();
        }
        return $this->renderLoginForm();
    }

    private function processLogin(): Response
    {
        $result = $this->authService->authentifier(
            trim(Request::post('email', '')),
            Request::post('password', '')
        );
        if (!$result['success']) {
            $this->sessionService->setFlashError($result['error'] ?? 'Erreur de connexion');
            return Response::redirect('/connexion');
        }
        $this->sessionService->setSessionCookie($result['token']);
        return Response::redirect($this->sessionService->getRedirectAfterLogin());
    }

    public function logout(): Response
    {
        $token = Request::cookie('session_token');
        if ($token !== null) {
            $this->authService->supprimerSession($token);
        }
        $this->sessionService->clearSessionCookie();
        Auth::logout();
        return Response::redirect('/connexion');
    }

    private function renderLoginForm(): Response
    {
        ob_start();
        include dirname(__DIR__, 2) . '/ressources/views/connexion.php';
        return Response::html(ob_get_clean());
    }
}
```

> **Note** : Les méthodes `changePassword`, `listSessions`, `forceLogout` doivent être déplacées vers des contrôleurs séparés (`PasswordController`, `Admin/SessionsController`).

---

### 2.2 DossierEtudiant - Duplication de Logique

**Fichier** : `app/Models/DossierEtudiant.php`  
**Problème** : Méthode `transitionner()` duplique `ServiceWorkflow::effectuerTransition()`

**Correction** : Supprimer la méthode du modèle et déléguer au service

```php
// SUPPRIMER les lignes 66-122 de DossierEtudiant.php

// À LA PLACE, ajouter une méthode délégatrice simple :

/**
 * Effectue une transition (délègue au ServiceWorkflow)
 * @deprecated Utiliser ServiceWorkflow::effectuerTransition() directement
 */
public function transitionner(
    string $codeEtatCible,
    ?int $utilisateurId = null,
    ?string $commentaire = null
): void {
    $workflow = new \App\Services\Workflow\ServiceWorkflow();
    $workflow->effectuerTransition(
        $this->getId(),
        $codeEtatCible,
        $utilisateurId ?? \Src\Support\Auth::id() ?? 0,
        $commentaire
    );
    // Recharger les données
    $updated = self::find($this->getId());
    if ($updated) {
        $this->fill($updated->toArray());
    }
}
```

---

### 2.3 Utilisateur - Référence à Classe Inexistante

**Fichier** : `app/Models/Utilisateur.php`  
**Lignes** : 59-62  
**Problème** : `GroupeUtilisateur` n'existe pas

**Correction** :

```php
// AVANT (lignes 59-62)
public function groupeUtilisateur(): ?GroupeUtilisateur
{
    return $this->belongsTo(GroupeUtilisateur::class, 'id_GU', 'id_GU');
}

// APRÈS
public function groupe(): ?Groupe
{
    return $this->belongsTo(Groupe::class, 'id_GU', 'id_groupe');
}
```

---

### 2.4 README.md - Fichier Vide

**Fichier** : `README.md`  
**Problème** : Aucun contenu

**Correction** : Ajouter le contenu suivant

```markdown
# CheckMaster - Système de Gestion des Mémoires UFHB

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-Proprietary-red.svg)]()

## Description

CheckMaster est un système de gestion académique pour la supervision des mémoires de Master à l'UFHB (Université Félix Houphouët-Boigny).

## Fonctionnalités Principales

- **Workflow 14 états** : INSCRIT → DIPLOME_DELIVRE
- **13 groupes utilisateurs** avec permissions granulaires
- **Commission de validation** avec votes à 3 tours
- **Génération de 13 types de PDF** (reçus, PV, attestations)
- **71 templates de notifications** email
- **Audit complet** de toutes les actions

## Prérequis

- PHP 8.0+ avec extensions : pdo_mysql, mbstring, openssl, intl, gd, zip, fileinfo
- MySQL 8.0+ ou MariaDB 10.5+
- Composer 2.0+

## Installation

```bash
# 1. Cloner le projet
git clone https://github.com/ManuelD-Aho/check.master.git
cd check.master

# 2. Installer les dépendances
composer install

# 3. Configurer la base de données
cp app/config/database.example.php app/config/database.php
# Éditer database.php avec vos identifiants

# 4. Exécuter les migrations
php bin/migrate.ps1

# 5. Lancer le serveur de développement
php -S localhost:8000 -t public/
```

## Documentation

- [Constitution](docs/constitution.md) - Principes non-négociables
- [Workflows](docs/workflows.md) - États et transitions
- [Roadmap](docs/roadmap.md) - Plan d'implémentation
- [Déploiement](docs/deployment.md) - Guide de mise en production

## Qualité du Code

```bash
# Linting PSR-12
composer run fix

# Analyse statique PHPStan
composer run stan

# Tests unitaires
composer test
```

## Architecture

```
app/
├── Controllers/    # Contrôleurs MVC (≤50 lignes)
├── Services/       # Logique métier
├── Models/         # ORM léger
├── Middleware/     # Pipeline HTTP
└── Validators/     # Validation Symfony

src/
├── Kernel.php      # Noyau applicatif
├── Container.php   # Injection de dépendances
└── Http/           # Request/Response
```

## Licence

Propriétaire - CheckMaster Team © 2025
```

---

## 3. Corrections Mineures (P2)

### 3.1 Helpers.php - Casse du Fichier

**Fichier** : `src/Support/Helpers.php` (majuscule) vs `helpers.php` (minuscule)  
**Problème** : Incohérence avec le composer.json qui référence `Helpers.php`

**Vérification** :
```json
// composer.json ligne 37
"files": [
    "src/Support/Helpers.php"
]
```

Le fichier est `helpers.php` (minuscule). Sur Linux, cela pose problème.

**Correction** :

```bash
# Renommer le fichier
mv src/Support/helpers.php src/Support/Helpers.php
```

---

### 3.2 workbench.md - Chemins Incorrects

**Fichier** : `docs/workbench.md`  
**Problème** : Référence `App\Services\Core\ServiceAudit` mais le fichier est dans `Security/`

**Correction** :

```markdown
// AVANT (ligne 48)
use App\Services\Core\{ServiceAudit, ServiceWorkflow};

// APRÈS
use App\Services\Security\ServiceAudit;
use App\Services\Workflow\ServiceWorkflow;
```

---

### 3.3 SessionActive - Méthode getUtilisateur() Manquante

**Fichier** : `app/Models/SessionActive.php`  
**Problème** : La méthode `getUtilisateur()` est appelée mais non définie

**Correction** : Ajouter la méthode

```php
// Ajouter après la méthode utilisateur() (ligne 44)

/**
 * Retourne l'utilisateur associé à la session
 */
public function getUtilisateur(): ?Utilisateur
{
    return $this->utilisateur();
}
```

---

### 3.4 SessionActive - Méthode majDerniereActivite() Manquante

**Fichier** : `app/Models/SessionActive.php`  
**Problème** : Utilisé dans `Auth.php` mais non définie

**Correction** : Ajouter la méthode

```php
// Ajouter après la méthode rafraichir() (ligne 138)

/**
 * Met à jour la dernière activité
 */
public function majDerniereActivite(): void
{
    $this->derniere_activite = date('Y-m-d H:i:s');
}
```

---

### 3.5 routes.php - Commentaire AltoRouter

**Fichier** : `app/config/routes.php`  
**Problème** : Référence `@var \AltoRouter $router`

**Correction** : Adapter pour notre Router ou supprimer

```php
// AVANT (lignes 1-8)
<?php

/**
 * Définition des routes de l'application
 * Utilise AltoRouter
 * @var \AltoRouter $router
 */

// APRÈS
<?php

declare(strict_types=1);

/**
 * Définition des routes de l'application
 * @var \Src\Router $router
 */
```

---

## 4. Script de Validation

Créer un script pour valider les corrections :

```php
<?php
// bin/validate-corrections.php

declare(strict_types=1);

echo "=== Validation des Corrections CheckMaster ===\n\n";

$errors = [];
$warnings = [];

// 1. Vérifier Router.php
if (file_get_contents(__DIR__ . '/../src/Router.php') === '' || 
    strlen(file_get_contents(__DIR__ . '/../src/Router.php')) < 100) {
    $errors[] = "[P0] src/Router.php est vide ou trop court";
}

// 2. Vérifier constantes WorkflowGateMiddleware
$middleware = file_get_contents(__DIR__ . '/../app/Middleware/WorkflowGateMiddleware.php');
if (strpos($middleware, 'WorkflowEtat::RAPPORT_VALIDE') !== false) {
    $errors[] = "[P0] WorkflowGateMiddleware utilise encore RAPPORT_VALIDE sans préfixe ETAT_";
}

// 3. Vérifier migrations
$migrations = glob(__DIR__ . '/../database/migrations/002_*.sql');
if (count($migrations) > 1) {
    $warnings[] = "[P0] Plusieurs fichiers 002_*.sql trouvés";
}

// 4. Vérifier méthodes inexistantes
$authController = file_get_contents(__DIR__ . '/../app/Controllers/AuthController.php');
if (strpos($authController, 'getSessionsUtilisateur') !== false) {
    $errors[] = "[P0] AuthController utilise getSessionsUtilisateur() inexistante";
}

$authService = file_get_contents(__DIR__ . '/../app/Services/Security/ServiceAuthentification.php');
if (strpos($authService, 'supprimerToutesSessionsUtilisateur') !== false) {
    $errors[] = "[P0] ServiceAuthentification utilise supprimerToutesSessionsUtilisateur() inexistante";
}

// 5. Vérifier AuthController longueur
$authLines = count(explode("\n", $authController));
if ($authLines > 100) {
    $warnings[] = "[P1] AuthController a {$authLines} lignes (max recommandé: 50)";
}

// 6. Vérifier README.md
$readme = file_get_contents(__DIR__ . '/../README.md');
if (strlen(trim($readme)) < 100) {
    $warnings[] = "[P1] README.md est vide ou incomplet";
}

// 7. Vérifier référence GroupeUtilisateur
$utilisateur = file_get_contents(__DIR__ . '/../app/Models/Utilisateur.php');
if (strpos($utilisateur, 'GroupeUtilisateur') !== false) {
    $warnings[] = "[P1] Utilisateur.php référence GroupeUtilisateur inexistante";
}

// Résultats
echo "Erreurs critiques: " . count($errors) . "\n";
foreach ($errors as $e) {
    echo "  ❌ $e\n";
}

echo "\nAvertissements: " . count($warnings) . "\n";
foreach ($warnings as $w) {
    echo "  ⚠️  $w\n";
}

echo "\n";
if (count($errors) === 0) {
    echo "✅ Toutes les corrections critiques sont appliquées!\n";
    exit(0);
} else {
    echo "❌ Des corrections critiques sont encore nécessaires.\n";
    exit(1);
}
```

---

## Résumé des Fichiers à Modifier

| Fichier | Action | Priorité |
|---------|--------|----------|
| `src/Router.php` | Implémenter ou utiliser AltoRouter | P0 |
| `app/Middleware/WorkflowGateMiddleware.php` | Corriger constantes | P0 |
| `database/migrations/002_create_notifications_table.sql` | Renommer en 014_* | P0 |
| `app/Controllers/AuthController.php` | Corriger appel méthode | P0 |
| `app/Services/Security/ServiceAuthentification.php` | Corriger appel méthode | P0 |
| `app/Controllers/AuthController.php` | Refactoriser (extraire logique) | P1 |
| `app/Models/DossierEtudiant.php` | Supprimer duplication | P1 |
| `app/Models/Utilisateur.php` | Corriger référence | P1 |
| `README.md` | Compléter | P1 |
| `app/config/routes.php` | Adapter commentaire | P2 |
| `docs/workbench.md` | Corriger chemins | P2 |
| `app/Models/SessionActive.php` | Ajouter méthodes | P2 |

---

**Fin du document de corrections**

*Généré le 2025-12-24*
