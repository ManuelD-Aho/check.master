# CheckMaster - Audit des Problèmes et Erreurs

Date: 2026-01-15
Version: 1.0

## Résumé

Ce document centralise les erreurs et problèmes identifiés dans l'application CheckMaster lors de l'audit de configuration Apache.

---

## 1. Problèmes CRITIQUES (Erreurs Fatales)

### 1.1 Classe `SessionCommission` inexistante

**Problème:** Plusieurs fichiers utilisent `App\Models\SessionCommission` alors que la vraie classe est `App\Models\CommissionSession`.

| Fichier | Ligne | Impact |
|---------|-------|--------|
| `/app/Controllers/Commission/PvController.php` | 9, 34, 50 | Erreur fatale |
| `/app/Controllers/Commission/SessionsController.php` | 9, 35, 45 | Erreur fatale |

**Solution:** Remplacer `SessionCommission` par `CommissionSession` dans les imports et appels.

**Statut:** ✅ CORRIGÉ dans cette PR

---

### 1.2 Méthodes manquantes dans les modèles

| Méthode Appelée | Modèle | Alternative | Fichier |
|-----------------|--------|-------------|---------|
| `findByEtudiant()` | DossierEtudiant | Ajouter méthode ou utiliser `firstWhere` | Contrôleurs Etudiant |
| `findByDossier()` | RapportEtudiant | `actuelPourDossier()` existe | RapportController.php |
| `enAttente()` | Candidature | `attenteValidationScolarite()` existe | CandidaturesController.php |
| `findByDossier()` | Soutenance | À implémenter | NotesController.php |
| `findByDossier()` | Candidature | `pourDossier()` existe | CandidatureController.php |

**Solution:** Ajouter les méthodes alias manquantes ou corriger les appels.

**Statut:** ✅ CORRIGÉ dans cette PR (ajout de `findByEtudiant()`, `findByDossier()` alias)

---

## 2. Problèmes de Configuration Apache

### 2.1 Fichiers .htaccess

**Problème:** Aucun fichier .htaccess n'existait pour gérer la réécriture d'URL Apache.

**Solution:** 
- `.htaccess` racine : Redirige vers `public/`, protège les dossiers sensibles
- `public/.htaccess` : Front controller pattern, routes vers `index.php`
- `public/index.php` : Point d'entrée créé pour Apache

**Statut:** ✅ CORRIGÉ dans cette PR

---

### 2.2 Chemins codés en dur

**Attention:** Les chemins suivants utilisent des préfixes absolus qui pourraient ne pas fonctionner correctement:

| Fichier | Chemin | Recommandation |
|---------|--------|----------------|
| `ConvocationsController.php:47` | `/storage/convocations/` | Utiliser `STORAGE_PATH` |
| `PvController.php:39,54` | `/storage/pv/` | Utiliser `STORAGE_PATH` |
| `ServiceAudit.php:45` | `/storage/` | Utiliser `STORAGE_PATH` |

**Note:** Ces chemins fonctionnent car `STORAGE_PATH` est défini dans `bootstrap.php`, mais les services utilisent parfois des chemins relatifs inconsistants.

---

## 3. Problèmes de Sécurité (Moyens)

### 3.1 Fichiers de test exposés

| Fichier | Risque | Action |
|---------|--------|--------|
| `/test_ui.php` | Accès non autorisé | Protégé par .htaccess |
| `/public/test_ui.php` | Accès local uniquement | Configuré dans .htaccess |
| `/phpinfo.php` | Exposition info serveur | À supprimer en production |

### 3.2 Rate Limiting

**Fichier:** `/app/Middleware/RateLimitMiddleware.php`

**Problème:** Utilise `file_get_contents/file_put_contents` sans verrouillage atomique. Condition de concurrence possible.

**Recommandation:** Utiliser `flock()` ou une solution Redis/Memcached.

---

## 4. Incohérences de Code

### 4.1 Accès direct aux superglobales

**Problème:** `$_SESSION` et `$_GET` utilisés directement dans environ 50 fichiers alors qu'un `ServiceSession` existe.

**Recommandation:** Migrer progressivement vers l'utilisation du service.

### 4.2 ApiController vide

**Fichier:** `/app/Controllers/ApiController.php`

**Problème:** Le contrôleur existe mais n'a aucune méthode.

**Action:** Soit l'implémenter, soit le supprimer.

---

## 5. Routes et Navigation

### 5.1 Route `/parametres`

**Problème initial signalé:** NotFoundException sur `/parametres`

**Analyse:** Le lien dans le header pointe vers `/admin/parametres` (ligne 112 de header.php), ce qui est correct. La route existe bien dans routes.php (ligne 42).

**Statut:** ✅ Pas de problème - le lien est correct

---

## 6. Méthodes Alias Ajoutées

Pour résoudre les erreurs de méthodes manquantes, les alias suivants ont été ajoutés:

### Dans `RoleTemporaire.php`:
```php
public static function getRolesActifsUtilisateur(int $utilisateurId): array
```

### Dans `Permission.php`:
```php
public static function getPermissionsGroupeRessource(int $groupeId, int $ressourceId): ?self
```
*(Déjà présente)*

### Dans `DossierEtudiant.php`:
```php
public static function findByEtudiant(int $etudiantId): ?self
```

### Dans `Candidature.php`:
```php
public static function findByDossier(int $dossierId): ?self
public static function enAttente(): array
```

### Dans `RapportEtudiant.php`:
```php
public static function findByDossier(int $dossierId): ?self
public static function enAttente(): array
```

### Dans `Soutenance.php`:
```php
public static function findByDossier(int $dossierId): ?self
```

---

## 7. Checklist de Déploiement Apache

### Avant le déploiement:
- [ ] Supprimer `/phpinfo.php`
- [ ] Vérifier que `mod_rewrite` est activé
- [ ] Configurer `APP_ENV=production` dans l'environnement
- [ ] Vérifier les permissions du dossier `storage/`

### Fichiers créés:
- [x] `/.htaccess` - Protection racine + redirection vers public/
- [x] `/public/.htaccess` - Réécriture d'URL front controller
- [x] `/public/index.php` - Point d'entrée Apache

---

## 8. Prochaines Actions Recommandées

1. **Haute priorité:**
   - Corriger les noms de classe `SessionCommission` → `CommissionSession`
   - Tester toutes les routes après déploiement Apache

2. **Moyenne priorité:**
   - Standardiser l'utilisation de `STORAGE_PATH`
   - Améliorer le rate limiting avec verrouillage

3. **Basse priorité:**
   - Migrer vers `ServiceSession` pour tous les accès session
   - Supprimer ou implémenter `ApiController`

---

*Ce document sera mis à jour au fur et à mesure des corrections.*
