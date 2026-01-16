# Problèmes de l'Application CheckMaster

**Date de mise à jour** : 2025-12-24  
**Statut** : En cours de correction

---

## Vue d'ensemble

Ce document répertorie les problèmes identifiés dans l'application CheckMaster, classés par priorité et statut de correction.

### Légende
- 🔴 **Critique** - Bloque le fonctionnement de l'application
- 🟠 **Majeur** - Viole les principes ou impacte la maintenance
- 🟡 **Mineur** - Amélioration recommandée
- ✅ **Corrigé** - Problème résolu
- ⏳ **En cours** - Correction en cours
- ❌ **Non corrigé** - À faire

---

## Problèmes Critiques (P0) 🔴

| # | Problème | Fichier | Statut |
|---|----------|---------|--------|
| 1 | ~~Router.php vide~~ | `src/Router.php` | ✅ Corrigé |
| 2 | ~~Constantes WorkflowGateMiddleware sans préfixe ETAT_~~ | `app/Middleware/WorkflowGateMiddleware.php` | ✅ Corrigé |
| 3 | **Deux migrations numéro 002** | `database/migrations/002_*.sql` | ❌ Non corrigé |

### Détails pour #3 - Numérotation migrations
**Fichiers concernés** :
- `database/migrations/002_add_rapport_annotations.sql`
- `database/migrations/002_create_notifications_table.sql`

**Impact** : Conflits lors de l'exécution des migrations

**Correction recommandée** :
```bash
# Renommer le fichier notifications en 014
mv database/migrations/002_create_notifications_table.sql database/migrations/014_create_notifications_table.sql
```

---

## Problèmes Majeurs (P1) 🟠

| # | Problème | Fichier | Statut |
|---|----------|---------|--------|
| 4 | **AuthController dépasse 50 lignes** | `app/Controllers/AuthController.php` | ⏳ Partiellement corrigé (175 lignes, constitution exige ≤50) |
| 5 | **README.md absent/vide** | `README.md` à la racine | ❌ Non corrigé |
| 6 | Logique de session/flash dans contrôleur | `app/Controllers/AuthController.php` | ❌ Non corrigé |
| 7 | Duplication DossierEtudiant::transitionner() avec ServiceWorkflow | `app/Models/DossierEtudiant.php` | ❌ À vérifier |
| 8 | Référence à GroupeUtilisateur inexistante | `app/Models/Utilisateur.php` | ❌ À vérifier |

### Détails pour #4 - AuthController trop long
**Situation actuelle** : 175 lignes (réduit depuis 298)

**Constitution** : Les contrôleurs doivent avoir ≤50 lignes

**Éléments à extraire** :
1. `setFlashError()` / `setFlashSuccess()` → `ServiceSession` ou `ServiceFlash`
2. Logique de `forgotPassword()` → `ServicePassword`
3. Logique de `changePassword()` → `ServicePassword`

### Détails pour #5 - README.md manquant
Le fichier `README.md` n'existe pas à la racine du projet. Un README est essentiel pour :
- La documentation du projet
- Les instructions d'installation
- Le guide de démarrage rapide

---

## Problèmes Mineurs (P2) 🟡

| # | Problème | Fichier | Statut |
|---|----------|---------|--------|
| 9 | Casse Helpers.php vs helpers.php | `src/Support/helpers.php` vs `composer.json` | ❌ À vérifier |
| 10 | Chemin ServiceAudit incorrect dans docs | `docs/workbench.md` | ❌ Non corrigé |
| 11 | Tests unitaires insuffisants (<80%) | `tests/` | ❌ Non corrigé |
| 12 | Dossier `Policies/` non utilisé | `app/Policies/` | ❌ Non corrigé |

---

## Manques Fonctionnels

| # | Fonctionnalité | Description | Priorité |
|---|---------------|-------------|----------|
| F1 | **Authentification non implémentée** | `processLogin()` dans AuthController ne fait que rediriger avec un message d'erreur | 🔴 Critique |
| F2 | **Envoi email de reset password** | TODO dans `forgotPassword()` | 🟠 Majeur |
| F3 | **Changement de mot de passe** | TODO dans `changePassword()` | 🟠 Majeur |

---

## Incohérences Documentation vs Code

| Document | Référence | Réalité dans le code |
|----------|-----------|---------------------|
| `workflows.md` | `RAPPORT_VALIDE` | `ETAT_RAPPORT_VALIDE` |
| `workbench.md` | `App\Services\Core\ServiceAudit` | `App\Services\Security\ServiceAudit` |
| Constitution | Gate "candidature_validée" | Pas de constante correspondante exacte |

---

## Violations de la Constitution

### Principe IV : Séparation des Responsabilités ⚠️

| Règle | Situation | Impact |
|-------|-----------|--------|
| Controllers ≤50 lignes | `AuthController` = 175 lignes | Difficile à maintenir |
| Controllers ≤50 lignes | HTML inline dans `forgotPassword()` | Mélange présentation/logique |
| Logique métier dans Services uniquement | Gestion flash/session dans AuthController | Responsabilité mal placée |

### Principe VII : Versioning Strict ⚠️

| Règle | Situation | Impact |
|-------|-----------|--------|
| Migrations numérotées uniques | Deux migrations 002_*.sql | Conflits de migration |

---

## Recommandations de Correction

### Actions Immédiates (P0)

1. **Renuméroter les migrations**
   ```bash
   mv database/migrations/002_create_notifications_table.sql database/migrations/014_create_notifications_table.sql
   ```

### Actions à Court Terme (P1)

2. **Créer README.md** avec :
   - Description du projet
   - Prérequis
   - Instructions d'installation
   - Commandes de développement

3. **Implémenter l'authentification réelle** :
   - Connecter `processLogin()` à `ServiceAuthentification`
   - Gérer les cookies de session
   - Implémenter la protection brute-force

4. **Refactoriser AuthController** :
   - Extraire `setFlashError/Success` vers un service
   - Extraire la logique de templates vers des vues séparées
   - Déléguer la logique à des services dédiés

### Actions à Moyen Terme (P2)

5. **Harmoniser la documentation**
   - Mettre à jour `workbench.md` avec les bons chemins
   - Standardiser les références aux constantes workflow

6. **Augmenter la couverture de tests** à 80%

---

## Historique des Corrections

| Date | Problème | Commit/Action |
|------|----------|---------------|
| 2025-12-24 | Router.php implémenté | ✅ |
| 2025-12-24 | WorkflowGateMiddleware constantes corrigées | ✅ |
| 2025-12-24 | AuthController réduit de 298 à 175 lignes | ⏳ (encore >50) |

---

## Voir aussi

- [AUDIT.md](./AUDIT.md) - Audit complet du projet
- [CORRECTIONS.md](./CORRECTIONS.md) - Détails des corrections à appliquer
- [constitution.md](./constitution.md) - Principes et règles du projet
