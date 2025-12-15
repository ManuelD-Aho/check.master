---
description: Convertir les tâches CheckMaster existantes en issues GitHub actionnables et ordonnées par dépendances basées sur les artefacts de conception disponibles.
tools: ['github/github-mcp-server/issue_write']
---

## Entrée Utilisateur

```text
$ARGUMENTS
```

Vous **DEVEZ** prendre en compte l'entrée utilisateur avant de procéder (si non vide).

## Directives Templates Issues CheckMaster

Lors de la création d'issues GitHub depuis les tâches CheckMaster, utiliser ces templates :

### Template Issue Tâche Standard

```markdown
## Tâche : [Description Tâche]

**ID Tâche** : T0XX  
**Phase** : [Setup/Core/Interface/Integration/Quality]  
**User Story** : [USN] (si applicable)  
**Parallèle** : [Oui/Non]  
**Priorité** : [P1/P2/P3]

### Description
[Brève description de ce qui doit être implémenté]

### Critères d'Acceptation
- [ ] [Livrable spécifique 1]
- [ ] [Livrable spécifique 2]
- [ ] [Livrable spécifique 3]

### Détails Techniques
**Fichiers à Créer/Modifier** :
- `[chemin fichier 1]`
- `[chemin fichier 2]`

**Dépendances** :
- [ ] Tâche T0XX doit être complétée d'abord
- [ ] Dépend de Service/Modèle/Table

**Standards CheckMaster** :
- [ ] Types stricts déclarés (`declare(strict_types=1);`)
- [ ] 100% type hints (paramètres, retours, propriétés)
- [ ] Journalisation ServiceAudit (si opération écriture)
- [ ] Vérification ServicePermission (si restreint)
- [ ] Requêtes préparées (pas de SQL brut)
- [ ] Échappement e() dans vues
- [ ] Hashids dans URLs
- [ ] PHPStan niveau 6+ passe
- [ ] PHP-CS-Fixer (PSR-12) passe

### Lié
- Spec : [lien vers section spec.md]
- Plan : [lien vers section plan.md]
- Issue Parent : #XX (si partie d'un epic)

### Labels
`tache`, `phase-[phase]`, `[module]`, `[priorite]`
```

### Template Issue Migration Base de Données

```markdown
## Migration : [Nom Table/Fonctionnalité]

**ID Tâche** : T0XX  
**Numéro Migration** : 0XX  
**Type** : [Créer Table/Altérer Table/Ajouter Seed Data]

### Description
[Quels changements base de données sont effectués]

### Détails Migration
**Fichier** : `database/migrations/0XX_description.sql`

**Tables Affectées** :
- [nom_table] : [CREATE/ALTER/SEED]

**Changements** :
- Ajouter table `[nom]` avec colonnes [liste]
- Ajouter FK vers `[table]`([colonne])
- Ajouter index sur [colonnes]

### Critères d'Acceptation
- [ ] Fichier migration créé avec numéro séquentiel
- [ ] Nommage table suit convention snake_case
- [ ] Clé primaire nommée `id_nomtable`
- [ ] Clés étrangères incluent ON DELETE RESTRICT
- [ ] Index ajoutés pour colonnes FK et recherche
- [ ] Entrée migration ajoutée dans table migrations
- [ ] Migration s'exécute avec succès sur DB vierge
- [ ] Migration est idempotente (peut s'exécuter plusieurs fois)

### Plan Rollback
[Décrire comment annuler si nécessaire]

### Labels
`database`, `migration`, `phase-setup`
```

### Template Issue Implémentation Service

```markdown
## Service : Service[Nom]

**ID Tâche** : T0XX  
**Service** : `App\Services\[Module]\Service[Nom]`  
**User Story** : [USN]

### Description
Implémenter logique métier pour [description fonctionnalité]

### Critères d'Acceptation
- [ ] Classe service créée à `app/Services/[Module]/Service[Nom].php`
- [ ] DI constructeur pour dépendances
- [ ] Méthodes publiques avec type hints complets
- [ ] PHPDoc sur toutes les méthodes publiques
- [ ] Journalisation ServiceAudit pour écritures
- [ ] Transactions pour opérations multi-tables
- [ ] Gestion exceptions (exceptions typées)
- [ ] Implémentation stateless (pas de propriétés stockant état)

### Méthodes à Implémenter
```php
public function nomMethode(Type $param): TypeRetour;
```

**Règles Métier** :
- [Règle 1]
- [Règle 2]

**Intégrations** :
- ServiceWorkflow (si changements workflow)
- ServiceNotification (si notifications)
- ServicePermission (si vérifications accès)
- ServiceAudit (si écritures données)

### Tests
- [ ] Test unitaire créé à `tests/Unit/Services/Service[Nom]Test.php`
- [ ] Mock dépendances
- [ ] Tester chemin nominal
- [ ] Tester scénarios erreur
- [ ] Tester rollback transaction

### Labels
`service`, `logique-metier`, `user-story-[N]`, `[priorite]`
```

### Template Issue Implémentation Contrôleur

```markdown
## Contrôleur : [Nom]Controller

**ID Tâche** : T0XX  
**Contrôleur** : `App\Controllers\[Module]\[Nom]Controller`  
**User Story** : [USN]

### Description
Gérer requêtes HTTP pour [description fonctionnalité]

### Critères d'Acceptation
- [ ] Contrôleur créé à `app/Controllers/[Module]/[Nom]Controller.php`
- [ ] DI constructeur pour Service
- [ ] Méthodes ≤50 lignes
- [ ] Pattern Validation + Service + Réponse uniquement
- [ ] Retours JsonResponse ou View
- [ ] Wrapper Request (jamais $_POST/$_GET)
- [ ] PermissionMiddleware appliqué
- [ ] Routage Hashids configuré

### Méthodes à Implémenter
```php
public function action(int $id): JsonResponse;
```

**Responsabilités** :
1. Obtenir données de Request
2. Valider via Validator
3. Appeler méthode Service
4. Retourner JsonResponse

**Routes** :
- `POST /[module]/{hash}/[action]`
- `GET /[module]/{hash}/[action]`

### Permissions
- **Traitement** : [ID]
- **Action** : [Consulter/Créer/Modifier/Supprimer]
- **Groupes** : [Liste des IDs groupe_utilisateur]

### Labels
`controleur`, `http`, `user-story-[N]`, `[priorite]`
```

### Template Issue Intégration Workflow

```markdown
## Workflow : [Nom Transition]

**ID Tâche** : T0XX  
**Transition** : [état_source] → [état_cible]  
**User Story** : [USN]

### Description
Implémenter transition état workflow pour [fonctionnalité]

### Critères d'Acceptation
- [ ] État ajouté dans table workflow_etats
- [ ] Transition ajoutée dans table workflow_transitions
- [ ] ServiceWorkflow::effectuerTransition appelé
- [ ] Conditions transition validées
- [ ] Snapshot workflow_historique enregistré
- [ ] Notifications déclenchées
- [ ] Vérification permission appliquée
- [ ] Conditions gate vérifiées

### Détails Workflow
**État Source** : `[état_source]`  
**État Cible** : `[état_cible]`  
**Code Transition** : `[code_transition]`

**Conditions** :
- [Condition 1]
- [Condition 2]

**Déclencheurs** :
- [Quelle action déclenche cette transition]

**Effets de Bord** :
- Mettre à jour [entité liée]
- Notifier [groupes utilisateurs]
- Générer [document]

### Notifications
- Template : `[code_template]`
- Destinataires : [groupes/rôles utilisateurs]
- Canaux : Email, Messagerie interne

### Labels
`workflow`, `machine-etat`, `user-story-[N]`, `[priorite]`
```

### Stratégie Labels Issues

**Labels Standard** :
- `tache` - Tâche implémentation régulière
- `database` - Migration/seed base de données
- `service` - Implémentation couche service
- `controleur` - Implémentation contrôleur
- `workflow` - Workflow/machine état
- `notification` - Notification/communication
- `document` - Génération PDF/archivage
- `securite` - Tâche liée sécurité
- `permission` - Permission/contrôle accès
- `financier` - Fonctionnalités paiement/pénalité

**Labels Phase** :
- `phase-setup` - Setup infrastructure
- `phase-fondamentale` - Prérequis bloquants
- `phase-core` - Logique métier principale
- `phase-interface` - UI/Contrôleurs
- `phase-integration` - Intégration services
- `phase-qualite` - Tests/QA

**Labels Priorité** :
- `P1` - Doit avoir (MVP)
- `P2` - Devrait avoir
- `P3` - Bien d'avoir

**Labels Module** :
- `scolarite` - Module Scolarité
- `commission` - Module Commission
- `communication` - Module Communication
- `soutenance` - Module Soutenance/Jury
- `etudiant` - Fonctionnalités étudiant
- `admin` - Administration

**Labels User Story** :
- `user-story-1` - Tâches US1
- `user-story-2` - Tâches US2
- etc.

## Aperçu

1. Exécuter `.specify/scripts/powershell/check-prerequisites.ps1 -Json -RequireTasks -IncludeTasks` depuis racine repo et parser FEATURE_DIR et liste AVAILABLE_DOCS. Tous les chemins doivent être absolus. Pour apostrophes dans args comme "J'exporte", utiliser syntaxe échappement : ex 'J'\''exporte' (ou guillemets si possible : "J'exporte").
1. Depuis le script exécuté, extraire le chemin vers **tasks**.
1. Obtenir le remote Git en exécutant :

```bash
git config --get remote.origin.url
```

> [!CAUTION]
> PROCÉDER AUX ÉTAPES SUIVANTES UNIQUEMENT SI LE REMOTE EST UNE URL GITHUB

1. Pour chaque tâche dans la liste, utiliser le serveur MCP GitHub pour créer une nouvelle issue dans le repository représentatif du remote Git.

> [!CAUTION]
> NE JAMAIS SOUS AUCUNE CIRCONSTANCE CRÉER DES ISSUES DANS DES REPOSITORIES QUI NE CORRESPONDENT PAS À L'URL REMOTE
