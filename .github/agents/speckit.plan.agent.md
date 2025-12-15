---
description: Exécuter le workflow de planification implémentation utilisant le template plan pour générer des artefacts de conception pour CheckMaster.
handoffs: 
  - label: Créer Tâches
    agent: speckit.tasks
    prompt: Décomposer le plan en tâches pour CheckMaster (PHP 8.0+ MVC++ natif, MySQL, DB-Driven)
    send: true
  - label: Créer Checklist
    agent: speckit.checklist
    prompt: Créer une checklist CheckMaster pour le domaine suivant...
---

## Entrée Utilisateur

```text
$ARGUMENTS
```

Vous **DEVEZ** prendre en compte l'entrée utilisateur avant de procéder (si non vide).

## Contraintes Architecture CheckMaster (NON-NÉGOCIABLES)

### Stack Technique Imposée
- **PHP** : 8.0+ types stricts (`declare(strict_types=1);` obligatoire)
- **Base de données** : MySQL 8.0+ / MariaDB 10.5+
- **Framework** : AUCUN - Architecture MVC++ native
- **Dépendances** : Whitelist uniquement (~12MB total) :
  - hashids/hashids
  - symfony/validator
  - symfony/http-foundation
  - symfony/cache
  - mpdf/mpdf
  - tecnickcom/tcpdf
  - phpoffice/phpspreadsheet
  - phpmailer/phpmailer
  - monolog/monolog
- **Environnement** : Windows (dev) + Linux mutualisé LWS (prod)
- **PAS de Node.js, PAS de Redis requis, PAS de frameworks lourds**

### Couches Architecture (Séparation Stricte)
```
Requête → Routeur (Hashids) → Pipeline Middleware → Contrôleur → Service → Modèle → Base de Données
```

**Règles Contrôleur** :
- Max 50 lignes par méthode
- Validation + Appel Service + Réponse UNIQUEMENT
- ZÉRO logique métier
- Utiliser wrapper Request (jamais $_POST/$_GET)
- Retourner JsonResponse ou View

**Règles Service** :
- Stateless, logique métier pure
- Injection dépendances par constructeur
- Doit appeler ServiceAudit pour opérations écriture
- Transactionnel pour opérations multi-tables
- Testable (dépendances mockables)

**Règles Modèle** :
- Étendre App\Orm\Model
- Types propriétés déclarés
- Relations définies
- Pas de logique métier

### Principes Base de Données
1. **Tout DB-Driven** : Configuration, permissions, workflows, menus, templates TOUS en base de données
2. **Schéma 67 Tables** : Utiliser structure existante, ajouter migrations pour nouvelles tables
3. **Nommage** : Tables/colonnes `snake_case`, Clé Primaire toujours `id_nomtable`
4. **Clés Étrangères** : Explicites avec ON DELETE RESTRICT
5. **Index** : FK + colonnes recherche + fulltext où nécessaire
6. **Migrations** : Fichiers numérotés séquentiellement `database/migrations/0XX_description.sql`
7. **Ne jamais modifier migrations existantes** - toujours en créer de nouvelles

### Mandats Sécurité
- **Mots de passe** : Argon2id uniquement
- **SQL** : Requêtes préparées obligatoires
- **URLs** : Hashids pour tous les IDs entités (`/{module}/{hash}`)
- **Sortie** : Helper échappement `e()` pour toutes les vues
- **CSRF** : Tokens sur tous les formulaires
- **Limitation Débit** : Sur endpoints auth/sensibles
- **Audit** : Double logging (fichier Monolog + table pister)

### Patterns Spécifiques CheckMaster

**Intégration Workflow** :
- Chaque fonctionnalité touchant candidature/rapport doit considérer états workflow
- Utiliser ServiceWorkflow::effectuerTransition() pour changements d'état
- Définir transitions autorisées dans table workflow_transitions
- Mettre à jour workflow_historique avec snapshots

**Vérifications Permissions** :
- Vérifier via ServicePermission::verifier($userId, $ressource, $action)
- Cache permissions (5 min TTL, invalider sur changements)
- Respecter mappings groupe_utilisateur → traitement → action
- Supporter rôles temporaires (accès président jury jour-J)

**Pattern Notification** :
- Utiliser ServiceNotification::envoyer($template, $destinataires, $variables)
- Multi-canal : Email primaire + Messagerie interne backup
- Traquer bounces → logique retry
- Templates stockés dans table notification_templates

**Génération Documents** :
- Documents simples : TCPDF
- Documents complexes (CSS3) : mPDF
- Calculer hash SHA256 à la génération
- Stocker dans archives avec hash pour intégrité
- Templates dans `ressources/templates/pdf/`

**Accès Configuration** :
- ServiceParametres::get('cle.config', $default)
- Cache configuration (invalider sur changements admin)
- ~170 paramètres organisés par préfixe (workflow.*, notify.*, etc.)

## Aperçu

1. **Setup** : Exécuter `.specify/scripts/powershell/setup-plan.ps1 -Json` depuis racine repo et parser JSON pour FEATURE_SPEC, IMPL_PLAN, SPECS_DIR, BRANCH. Pour apostrophes dans args comme "J'organise", utiliser syntaxe échappement : ex 'J'\''organise' (ou guillemets si possible : "J'organise").

2. **Charger contexte** : Lire FEATURE_SPEC et `.specify/memory/constitution.md`. Charger template IMPL_PLAN (déjà copié).

3. **Exécuter workflow plan** : Suivre la structure dans template IMPL_PLAN pour :
   - Remplir Contexte Technique (marquer inconnus comme "NÉCESSITE CLARIFICATION")
   - Remplir section Vérification Constitution depuis constitution
   - Évaluer gates (ERREUR si violations non justifiées)
   - Phase 0 : Générer research.md (résoudre tous NÉCESSITE CLARIFICATION)
   - Phase 1 : Générer data-model.md, contracts/, quickstart.md
   - Phase 1 : Mettre à jour contexte agent en exécutant le script agent
   - Ré-évaluer Vérification Constitution post-conception

4. **Arrêter et rapporter** : Commande termine après planification Phase 2. Rapporter branche, chemin IMPL_PLAN, et artefacts générés.

## Phases

### Phase 0 : Esquisse & Recherche

1. **Extraire inconnus depuis Contexte Technique** ci-dessus :
   - Pour chaque NÉCESSITE CLARIFICATION → tâche recherche
   - Pour chaque dépendance → tâche meilleures pratiques
   - Pour chaque intégration → tâche patterns

2. **Générer et dispatcher agents recherche** :

   ```text
   Pour chaque inconnu dans Contexte Technique :
     Tâche : "Rechercher {inconnu} pour {contexte fonctionnalité}"
   Pour chaque choix technologie :
     Tâche : "Trouver meilleures pratiques pour {tech} dans {domaine}"
   ```

3. **Consolider résultats** dans `research.md` utilisant format :
   - Décision : [ce qui a été choisi]
   - Rationale : [pourquoi choisi]
   - Alternatives considérées : [quoi d'autre évalué]

**Sortie** : research.md avec tous NÉCESSITE CLARIFICATION résolus

### Phase 1 : Conception & Contrats

**Prérequis :** `research.md` complet

1. **Extraire entités depuis spec fonctionnalité** → `data-model.md` :
   - Nom entité, champs, relations
   - Règles validation depuis exigences
   - Transitions d'état si applicable

2. **Générer contrats API** depuis exigences fonctionnelles :
   - Pour chaque action utilisateur → endpoint
   - Utiliser patterns REST/GraphQL standard
   - Produire schéma OpenAPI/GraphQL vers `/contracts/`

3. **Mise à jour contexte agent** :
   - Exécuter `.specify/scripts/powershell/update-agent-context.ps1 -AgentType copilot`
   - Ces scripts détectent quel agent AI est utilisé
   - Mettre à jour le fichier contexte spécifique agent approprié
   - Ajouter uniquement nouvelle technologie depuis plan actuel
   - Préserver ajouts manuels entre marqueurs

**Sortie** : data-model.md, /contracts/*, quickstart.md, fichier spécifique agent

## Règles Clés

- Utiliser chemins absolus
- ERREUR sur échecs gate ou clarifications non résolues
- **Vérification Constitution CheckMaster** : Chaque plan DOIT vérifier :
  - [ ] Pas de dépendances Node.js/NPM
  - [ ] Pas de logique dans Contrôleurs (max 50 lignes)
  - [ ] Hashids utilisés pour tous les IDs publics
  - [ ] ServiceAudit appelé pour opérations écriture
  - [ ] ServicePermission vérifié avant actions
  - [ ] Workflows utilisent transitions ServiceWorkflow
  - [ ] Documents calculent hashes SHA256
  - [ ] Configuration via ServiceParametres (DB)
  - [ ] Notifications via ServiceNotification
  - [ ] Tout SQL utilise requêtes préparées
  - [ ] Mots de passe utilisent Argon2id
  - [ ] Vues utilisent échappement `e()`
  - [ ] Nouvelles tables ont migrations séquentielles
  - [ ] Services sont stateless et testables

### Référence Tables CheckMaster (67 tables)

**Authentification & Utilisateurs Core** :
- utilisateurs, sessions_actives, codes_temporaires
- groupes, utilisateurs_groupes, roles_temporaires
- ressources, permissions, permissions_cache

**Entités Académiques** :
- etudiants, enseignants, personnel_admin
- entreprises, specialites, grades, fonctions
- annee_academique, semestre, niveau_etude, ue, ecue

**Workflow & Processus** :
- workflow_etats, workflow_transitions, workflow_historique, workflow_alertes
- dossiers_etudiants, candidatures, rapports_etudiants
- sessions_commission, votes_commission, annotations_rapport
- jury_membres, soutenances, notes_soutenance
- escalades, escalades_actions, escalade_niveaux

**Financier** :
- paiements, penalites, exonerations

**Communications** :
- notification_templates, notifications_queue, notifications_historique
- email_bounces, messages_internes

**Documents & Archives** :
- documents_generes, archives, historique_entites
- critere_evaluation, mentions, decisions_jury

**Configuration & Audit** :
- configuration_systeme, traitement, action, rattacher
- pister (piste audit)

**Référentiels** :
- roles_jury, statut_jury, salles
- type_utilisateur, groupe_utilisateur, niveau_acces_donnees
- niveau_approbation

### Template Modèle Données (data-model.md)

```markdown
# Modèle Données : [Nom Fonctionnalité]

## Tables Impactées

### Tables Existantes Modifiées
- **Table** : `nom_table`
  - **Changements** : Ajouter colonne `nouvelle_colonne VARCHAR(255)`
  - **Migration** : `database/migrations/0XX_ajout_nouvelle_colonne.sql`
  - **Raison** : [Pourquoi nécessaire pour fonctionnalité]

### Nouvelles Tables Créées
#### `nom_nouvelle_table`
```sql
CREATE TABLE nom_nouvelle_table (
    id_nouvelle_table INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(100) NOT NULL,
    description TEXT,
    actif BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Objectif** : [Objectif table]
**Relations** : 
- FK vers `autre_table(id_autre)`
**Contraintes** : [Règles métier]

## Relations Entités

```
[Dessiner relations en ASCII ou décrire]
```

## États Workflow (si applicable)

- **État Actuel** : `nom_etat`
- **Nouveaux États** : `nouvel_etat_1`, `nouvel_etat_2`
- **Transitions** :
  - `etat_1` → `etat_2` (Déclencheur : [événement], Condition : [règle])

## Règles Validation Données

- Champ `x` : Requis, max 100 caractères, alphanumérique
- Champ `y` : Optionnel, doit correspondre à `/regex/`
- Champ `z` : Clé étrangère doit exister dans table_ref
```

### Template Contrats (contracts/)

Pour chaque endpoint Contrôleur :

```php
/**
 * @route POST /module/{hash}/action
 * @middleware AuthMiddleware, PermissionMiddleware
 * @permission traitement_id=XX, action_id=YY
 * 
 * @param array $data Corps requête
 * @return JsonResponse
 * 
 * @throws ValidationException Entrée invalide
 * @throws NotFoundException Entité non trouvée
 * @throws ForbiddenException Permission refusée
 */
public function nomAction(array $data): JsonResponse;
```

### Artefacts Recherche (research.md)

Documenter décisions techniques :

```markdown
# Recherche : [Nom Fonctionnalité]

## Décision 1 : [Sujet]
**Choisi** : [Solution]
**Rationale** : [Pourquoi choisi par rapport aux alternatives]
**Alternatives Considérées** : 
- Option A : [Avantages/Inconvénients]
- Option B : [Avantages/Inconvénients]
**Impact** : [Ce que cela affecte]

## Points Intégration
- Service X : [Comment fonctionnalité s'intègre]
- Table Y : [Dépendances données]

## Inconnus Résolus
- Question : [Incertitude originale]
- Résolution : [Comment résolu]
```
