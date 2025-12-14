# CheckMaster Constitution

## Core Principles

### I. Database-Driven Architecture (NON-NEGOTIABLE)
**Autarcie Totale** :  Le système ne dépend d'aucune configuration externe obligatoire.  Toute configuration, permission, workflow, template de notification, et menu est stocké en base de données et administrable via l'interface sans modification du code source.  Les seuls fichiers de configuration autorisés sont `database. php` (DSN), `app.php` (debug/timezone), et `bootstrap.php` (DI container).

### II. Single Source of Truth
**Principe de Vérité Unique** : Chaque élément du système possède une source de données unique et incontestable : 
- Configuration → `configuration_systeme` (DB)
- Permissions → `rattacher` (DB) + cache
- Workflow → `workflow_etats` + `workflow_transitions` (DB)
- Notifications → `notification_templates` (DB)
- Menu → Construction dynamique depuis `rattacher` (DB)

Aucune duplication de logique métier entre backend et frontend.  Le backend calcule, le frontend affiche.

### III. Sécurité Par Défaut (NON-NEGOTIABLE)
**Principe du Moindre Privilège** : Toutes les permissions sont DENY ALL par défaut, puis autorisées explicitement.  Les règles strictes suivantes s'appliquent :
- Aucun secret en dur dans le code
- Hashage Argon2id obligatoire pour mots de passe
- Prepared statements obligatoires (aucune requête SQL brute)
- Échappement systématique en vue (helper `e()`)
- CSRF tokens sur tous formulaires
- Rate limiting sur routes sensibles
- Transactions explicites pour opérations multi-tables

### IV. Séparation des Responsabilités (MVC++)
**Architecture en Couches** : Le flux de données suit strictement cette hiérarchie :
```
Request → Router (Hashids) → Middleware Pipeline → Controller → Service → Repository → Model → Database
```
- **Controllers** : Validation + appel services (zéro logique métier)
- **Services** : Logique métier pure, stateless, testables
- **Models** : ORM léger (find, all, save, delete)
- Une classe = une responsabilité (Single Responsibility Principle)

### V. Convention Over Configuration
**Standardisation Maximale** : PSR-12 strict appliqué partout.  Nomenclature non négociable : 
- Classes : `PascalCase` (ex: `ServiceAuthentification`)
- Méthodes/Variables : `camelCase` (ex: `validerCandidature()`)
- Constantes : `UPPER_SNAKE_CASE` (ex: `MAX_FILE_SIZE`)
- Tables DB : `snake_case` (ex: `rapport_etudiants`)
- Colonnes DB : `snake_case` (ex: `date_depot`)
- Services :  Préfixe "Service" obligatoire
- Controllers : Suffixe "Controller" obligatoire
- Middleware : Suffixe "Middleware" obligatoire

### VI.  Auditabilité Totale (NON-NEGOTIABLE)
**Traçabilité Complète** : Toute action critique est auditée sans exception via double journalisation (Monolog fichier + table `pister`). Snapshots JSON avant/après obligatoires pour modifications de données.  Les logs sont inaltérables (pas de DELETE). Durée de traitement enregistrée pour monitoring performances.

### VII. Versioning Strict
**Migrations Versionnées** : Aucun `schema.sql` monolithique autorisé. Toute modification DB passe par migrations numérotées séquentielles (`001_`, `002_`, etc.). Table `migrations` trace l'exécution.  Historisation des entités via `historique_entites` pour rollback possible.

## Contraintes Techniques Obligatoires

### Stack Imposée
- **PHP** : 8.0+ minimum (types stricts `declare(strict_types=1)` obligatoire)
- **MySQL/MariaDB** : 8.0+ / 10.5+ minimum
- **Composer** : 2.0+ (autoload PSR-4)
- **Dépendances** : Maximum 12 packages (~12 MB total, pas de framework lourd)
- **Environnement** : Compatible Windows (PowerShell) + hébergement mutualisé LWS

### Extensions PHP Requises
`pdo_mysql`, `mbstring`, `openssl`, `intl`, `gd`, `zip`, `fileinfo`, `json`

### Interdictions Absolues
- ❌ Frameworks lourds (Laravel, Symfony Full Stack)
- ❌ Node.js, Redis, Memcached en dépendance obligatoire
- ❌ Secrets/credentials en dur dans le code
- ❌ SQL brut sans prepared statements
- ❌ Logique métier dans controllers
- ❌ Fichiers de configuration pour permissions/menus/workflows
- ❌ Scripts Bash/Linux (uniquement PowerShell Windows)

## Règles de Développement

### Validation & Erreurs
- **Validation centralisée** : Symfony Validator avec classes `*Validator. php` dédiées
- **Exceptions typées** : Hiérarchie `AppException` (ValidationException, UnauthorizedException, etc.)
- **Handler global** : JSON uniforme `{success, data, message}` ou `{error, code, message, details}`
- **Validation AVANT traitement** : Aucune donnée non validée ne doit toucher la DB

### Performance Obligatoire
- **Cache multi-niveaux** : OPcache PHP + Symfony Cache (permissions, config, menus)
- **Eager Loading** : Éviter N+1 queries (relations chargées explicitement)
- **Pagination systématique** : Listes > 50 items doivent être paginées
- **Index DB** : FK, colonnes de recherche, fulltext indexés obligatoirement

### Tests & Qualité
- **Coverage minimum** : 80% (PHPUnit)
- **Analyse statique** : PHPStan niveau 6 minimum sans erreurs
- **Formatage automatique** : PHP-CS-Fixer (PSR-12) exécuté avant commit
- **Tests unitaires** : Services isolés, mockables, testables
- **Tests d'intégration** : Endpoints API critiques (candidature, workflow, paiements)

### Documentation Obligatoire
- **PHPDoc** : Toutes méthodes publiques documentées (`@param`, `@return`, `@throws`)
- **README** : Par module (Services, Controllers, etc.)
- **OpenAPI/Swagger** : Endpoints API documentés dans `docs/api.yaml`
- **Changelog** : Versioning sémantique (`MAJOR.MINOR. PATCH`)

## Workflow de Développement

### Git Flow Strict
```
main (production, protégée)
  ├── develop (intégration)
  │   ├── feature/nom-fonctionnalite
  │   ├── bugfix/correction-bug
  │   └── hotfix/securite-critique
```

### Commits Conventionnels Obligatoires
Format :  `type(scope): message`
- `feat` : Nouvelle fonctionnalité
- `fix` : Correction bug
- `docs` : Documentation
- `test` : Ajout/modification tests
- `refactor` : Refactoring (pas de changement fonctionnel)
- `perf` : Amélioration performances
- `chore` : Maintenance (dépendances, config)

### Pre-Commit Gates
Avant chaque commit, exécution automatique : 
1. PHP-CS-Fixer (formatage)
2. PHPUnit (tests unitaires)
3. PHPStan (analyse statique)
4. Si échec → commit rejeté

### Code Review Obligatoire
- Aucun merge direct sur `main` ou `develop`
- Pull Request requise avec : 
  - Description claire du changement
  - Tests ajoutés/modifiés
  - Vérification compliance Constitution
  - Approbation d'au moins 1 reviewer

## Spécificités CheckMaster

### Workflow Central (Gate Critique)
Le **déblocage rédaction rapport** est NON-NÉGOCIABLE :  L'onglet "Rédaction du rapport" reste invisible tant que `workflow_etat != 'candidature_validee'`. Middleware `WorkflowGateMiddleware` bloque les routes `/etudiant/rapport/*` si condition non remplie.

### Création Utilisateurs
Règle stricte : Un compte utilisateur ne peut être créé que si l'entité (`etudiants`, `enseignants`, `personnel_admin`) existe déjà. Email de l'entité devient `login_utilisateur`. Mot de passe temporaire généré et envoyé par email.  Changement mot de passe obligatoire à première connexion.

### Numéro Carte Étudiant
Type **VARCHAR(20)** (format : `CI01552852`). Unique, non autogénéré, non modifiable sauf admin avec justification auditée.

### Routage Hashids
Format URL : `/{module}/{hash}` où `hash = encode(traitementId, actionId, entiteId)`. Modules whitelist stricte.  Aucune énumération possible.

### Documents Générés
13 types de documents PDF (mPDF pour avancés, TCPDF pour simples). Templates PHP dans `ressources/templates/pdf/`. Page de garde auto-générée.  Archivage avec hash SHA256 pour intégrité.

### Archivage Pérenne
Tous documents/dossiers archivés avec : 
- Hash SHA256 calculé à la création
- Vérification intégrité périodique (PowerShell Task Scheduler)
- Verrouillage par défaut (inaltérable)
- Régénération possible depuis snapshots JSON

## Gouvernance

### Hiérarchie des Règles
1. **Cette Constitution** : Supersède toutes autres pratiques
2. **Workbench** (`docs/workbench.md`) : Guide opérationnel d'implémentation
3. **Dossier Technique** (`docs/canvas.md`) : Spécifications fonctionnelles complètes

### Processus d'Amendement
Toute modification de cette Constitution requiert :
1. Documentation écrite de la justification
2. Impact analysé sur codebase existante
3. Approbation Lead Dev + Admin Système
4. Plan de migration si rétroactif
5. Mise à jour version + date

### Vérification Compliance
- Chaque Pull Request vérifie conformité Constitution
- Code review rejette toute violation
- Complexité doit être justifiée (YAGNI appliqué)
- Aucune exception sans documentation explicite

### Responsabilités
- **Lead Dev** : Garant application Constitution, arbitre conflits techniques
- **Reviewers** : Vérification compliance avant approbation PR
- **Équipe** : Signalement violations, proposition améliorations

**Version**: 1.0.0 | **Ratified**: 2025-12-14 | **Last Amended**: 2025-12-14

---

**Artefacts Associés** :
- Workbench : `docs/workbench.md` (Guide implémentation complet)
- Dossier Technique : `docs/canvas.md` (Spécifications détaillées)
- Base de Données : `database/migrations/` (67 tables versionnées)
- Scripts Setup : `bin/*. ps1` (Installation, migrations, maintenance)