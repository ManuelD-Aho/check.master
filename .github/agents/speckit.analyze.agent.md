---
description: Effectuer une analyse non-destructive de cohérence et qualité inter-artefacts pour CheckMaster (spec.md, plan.md, tasks.md) après génération des tâches.
---

## Entrée Utilisateur

```text
$ARGUMENTS
```

Vous **DEVEZ** prendre en compte l'entrée utilisateur avant de procéder (si non vide).

## Règles d'Analyse Spécifiques CheckMaster

### Conformité Constitution (Vérifications CRITIQUES)

**Chaque analyse DOIT vérifier ces mandats CheckMaster** :

1. **Architecture DB-Driven** :
   - [ ] Configuration NON dans fichiers PHP (doit utiliser table configuration_systeme)
   - [ ] Permissions NON codées en dur (doit utiliser tables rattacher + permissions)
   - [ ] Menus construits depuis tables traitement + rattacher
   - [ ] États workflow dans tables workflow_etats/transitions

2. **Standards de Sécurité** :
   - [ ] Tous les IDs entités utilisent Hashids dans URLs (pas d'entiers bruts)
   - [ ] Mots de passe utilisent Argon2id (pas md5, sha1, bcrypt)
   - [ ] SQL utilise requêtes préparées (pas de concaténation)
   - [ ] Vues utilisent échappement `e()` (pas d'echo brut)
   - [ ] Tous les formulaires ont tokens CSRF
   - [ ] ServiceAudit appelé pour opérations d'écriture

3. **Couches Architecture** :
   - [ ] Contrôleurs max 50 lignes (validation + service + réponse uniquement)
   - [ ] Logique métier dans Services (pas Contrôleurs ou Vues)
   - [ ] Modèles étendent App\Orm\Model
   - [ ] Injection de dépendances via constructeur
   - [ ] Transactions pour opérations multi-tables

4. **Intégration CheckMaster** :
   - [ ] Changements workflow utilisent ServiceWorkflow::effectuerTransition()
   - [ ] Permissions vérifiées via ServicePermission::verifier()
   - [ ] Notifications via ServiceNotification::envoyer()
   - [ ] Configuration via ServiceParametres::get()
   - [ ] Génération PDF avec archivage SHA256
   - [ ] Types documents correspondent aux 13 types définis

5. **Standards Base de Données** :
   - [ ] Noms tables `snake_case`
   - [ ] Clé primaire toujours `id_nomtable`
   - [ ] Clés étrangères avec ON DELETE RESTRICT
   - [ ] Migrations séquentielles (0XX_description.sql)
   - [ ] Ne jamais modifier migrations existantes
   - [ ] Index sur colonnes FK + recherche

### Validation Domaine CheckMaster

**Vérifier ces patterns de domaine** :

**Cohérence Workflow** :
```markdown
- [ ] Fonctionnalité touche candidature/rapport/soutenance ?
  - [ ] Spec définit quels états workflow impliqués
  - [ ] Plan mappe états aux transitions
  - [ ] Tâches incluent MAJ workflow_etats/transitions
  - [ ] Tâches appellent ServiceWorkflow pour changements état
  - [ ] Notifications définies pour chaque transition
```

**Mapping Permissions** :
```markdown
- [ ] Fonctionnalité requiert contrôle d'accès ?
  - [ ] Spec définit quels groupes utilisateurs ont accès
  - [ ] Plan identifie entrées traitement + action
  - [ ] Tâches incluent données seed pour table rattacher
  - [ ] Contrôleurs vérifient ServicePermission
  - [ ] Middleware applique PermissionMiddleware
```

**Génération Documents** :
```markdown
- [ ] Fonctionnalité génère documents PDF ?
  - [ ] Spec définit type document (simple/complexe)
  - [ ] Plan choisit TCPDF ou mPDF approprié
  - [ ] Tâches créent template dans ressources/templates/pdf/
  - [ ] Tâches calculent hash SHA256
  - [ ] Tâches archivent avec vérification intégrité
  - [ ] Tâches déclenchent notification téléchargement
```

**Opérations Financières** :
```markdown
- [ ] Fonctionnalité implique paiements/pénalités ?
  - [ ] Spec définit montants, règles de calcul
  - [ ] Plan mappe aux tables paiements/penalites
  - [ ] Tâches génèrent PDFs reçus
  - [ ] Tâches archivent documents financiers
  - [ ] Tâches mettent à jour statut financier étudiant
  - [ ] Gate vérifie statut paiement avant avancement workflow
```

**Commission/Vote** :
```markdown
- [ ] Fonctionnalité implique décisions commission ?
  - [ ] Spec définit logique vote (unanimité/majorité)
  - [ ] Plan gère 3 tours max avec escalade
  - [ ] Tâches tracent votes dans sessions_commission
  - [ ] Tâches déclenchent escalade Doyen après tour 3
  - [ ] Tâches génèrent documents PV
  - [ ] Tâches envoient notifications par tour
```

### Constatations Spécifiques CheckMaster

**Rechercher ces problèmes courants** :

**Problèmes CRITIQUES** :
- Utilisation Laravel/Symfony Full Stack (CheckMaster est MVC++ natif)
- Dépendances Node.js (CheckMaster est PHP uniquement)
- Redis/Memcached comme dépendances requises (uniquement Symfony Cache)
- Requêtes SQL brutes (doit utiliser requêtes préparées)
- Contrôleurs avec logique métier (>50 lignes, logique complexe)
- Permissions codées en dur (doit être DB-driven)
- IDs entiers bruts dans URLs (doit utiliser Hashids)
- Appels ServiceAudit manquants pour écritures données

**Problèmes ÉLEVÉS** :
- Changements état workflow sans ServiceWorkflow
- Vérifications permissions manquant ServicePermission
- Notifications n'utilisant pas ServiceNotification
- Configuration dans fichiers PHP (doit utiliser DB)
- PDF sans archivage SHA256
- Modifications migrations existantes
- Wrappers transaction manquants pour ops multi-tables
- Propriétés/paramètres/retours non typés

**Problèmes MOYENS** :
- Nommage tables incohérent (pas snake_case)
- Index manquants sur colonnes recherche/FK
- Usage direct $_POST/$_GET (doit utiliser wrapper Request)
- Vues avec echo brut (doit utiliser helper e())
- Services non stateless (stockant état)
- PHPDoc manquant sur méthodes publiques
- Validateur n'utilisant pas contraintes Symfony

**Problèmes FAIBLES** :
- Nommage variables incohérent
- Commentaires manquants sur logique complexe
- Formatage non conforme PSR-12
- Patterns requête sous-optimaux

## Objectif

Identifier incohérences, duplications, ambiguïtés et éléments sous-spécifiés à travers les trois artefacts principaux (`spec.md`, `plan.md`, `tasks.md`) avant implémentation. Cette commande DOIT s'exécuter uniquement après que `/speckit.tasks` a produit un `tasks.md` complet.

## Contraintes Opératoires

**STRICTEMENT LECTURE SEULE** : Ne **pas** modifier de fichiers. Produire un rapport d'analyse structuré. Proposer un plan de remédiation optionnel (l'utilisateur doit approuver explicitement avant toute commande d'édition de suivi).

**Autorité Constitution** : La constitution projet (`.specify/memory/constitution.md`) est **non-négociable** dans ce périmètre d'analyse. Les conflits constitution sont automatiquement CRITIQUES et nécessitent ajustement de spec, plan ou tâches—pas dilution, réinterprétation ou ignorance silencieuse du principe. Si un principe lui-même doit changer, cela doit se faire dans une mise à jour constitution séparée, explicite, hors `/speckit.analyze`.

## Étapes d'Exécution

### 1. Initialiser Contexte Analyse

Exécuter `.specify/scripts/powershell/check-prerequisites.ps1 -Json -RequireTasks -IncludeTasks` une fois depuis racine repo et parser JSON pour FEATURE_DIR et AVAILABLE_DOCS. Dériver chemins absolus :

- SPEC = FEATURE_DIR/spec.md
- PLAN = FEATURE_DIR/plan.md
- TASKS = FEATURE_DIR/tasks.md

Abandonner avec message erreur si fichier requis manquant (instruire utilisateur à exécuter commande prérequis manquante).
Pour apostrophes dans args comme "J'analyse", utiliser syntaxe échappement : ex 'J'\''analyse' (ou guillemets si possible : "J'analyse").

### 2. Charger Artefacts (Divulgation Progressive)

Charger uniquement contexte minimal nécessaire de chaque artefact :

**Depuis spec.md :**
- Vue d'ensemble/Contexte
- Exigences Fonctionnelles
- Exigences Non-Fonctionnelles
- User Stories
- Cas Limites (si présent)

**Depuis plan.md :**
- Choix Architecture/stack
- Références Modèle Données
- Phases
- Contraintes techniques

**Depuis tasks.md :**
- IDs Tâches
- Descriptions
- Groupement par phase
- Marqueurs parallèles [P]
- Chemins fichiers référencés

**Depuis constitution :**
- Charger `.specify/memory/constitution.md` pour validation principes

### 3. Construire Modèles Sémantiques

Créer représentations internes (ne pas inclure artefacts bruts en sortie) :

- **Inventaire exigences** : Chaque exigence fonctionnelle + non-fonctionnelle avec clé stable (dériver slug basé sur phrase impérative)
- **Inventaire user story/action** : Actions utilisateur discrètes avec critères acceptation
- **Mapping couverture tâches** : Mapper chaque tâche à une ou plusieurs exigences ou stories (inférence par mot-clé / patterns référence explicite)
- **Ensemble règles constitution** : Extraire noms principes et déclarations normatives DOIT/DEVRAIT

### 4. Passes Détection (Analyse Token-Efficiente)

Se concentrer sur constatations à signal élevé. Limiter à 50 constatations total ; agréger reste en résumé débordement.

#### A. Détection Duplication

- Identifier exigences quasi-dupliquées
- Marquer formulation qualité inférieure pour consolidation

#### B. Détection Ambiguïté

- Signaler adjectifs vagues (rapide, scalable, sécurisé, intuitif, robuste) manquant critères mesurables
- Signaler placeholders non résolus (TODO, TKTK, ???, `<placeholder>`, etc.)

#### C. Sous-spécification

- Exigences avec verbes mais objet ou résultat mesurable manquant
- User stories manquant alignement critères acceptation
- Tâches référençant fichiers ou composants non définis dans spec/plan

#### D. Alignement Constitution

- Toute exigence ou élément plan en conflit avec principe DOIT
- Sections mandatées ou gates qualité manquants de constitution

#### E. Lacunes Couverture

- Exigences avec zéro tâche associée
- Tâches sans exigence/story mappée
- Exigences non-fonctionnelles non reflétées dans tâches (ex : performance, sécurité)

#### F. Incohérence

- Dérive terminologique (même concept nommé différemment entre fichiers)
- Entités données référencées dans plan mais absentes de spec (ou vice versa)
- Contradictions ordre tâches (ex : tâches intégration avant tâches setup fondamentales sans note dépendance)
- Exigences conflictuelles (ex : une requiert Next.js tandis qu'autre spécifie Vue)

### 5. Attribution Sévérité

Utiliser cette heuristique pour prioriser constatations :

- **CRITIQUE** : Viole constitution DOIT, artefact spec principal manquant, ou exigence avec zéro couverture bloquant fonctionnalité de base
- **ÉLEVÉ** : Exigence dupliquée ou conflictuelle, attribut sécurité/performance ambigu, critère acceptation non testable
- **MOYEN** : Dérive terminologique, couverture tâche non-fonctionnelle manquante, cas limite sous-spécifié
- **FAIBLE** : Améliorations style/formulation, redondance mineure n'affectant pas ordre exécution

### 6. Produire Rapport Analyse Compact

Produire rapport Markdown (pas d'écriture fichier) avec structure suivante :

## Rapport Analyse Spécification

| ID | Catégorie | Sévérité | Emplacement(s) | Résumé | Recommandation |
|----|-----------|----------|----------------|--------|----------------|
| A1 | Duplication | ÉLEVÉ | spec.md:L120-134 | Deux exigences similaires ... | Fusionner formulation ; garder version plus claire |

(Ajouter une ligne par constatation ; générer IDs stables préfixés par initiale catégorie.)

**Table Résumé Couverture :**

| Clé Exigence | A Tâche ? | IDs Tâches | Notes |
|--------------|-----------|------------|-------|

**Problèmes Alignement Constitution :** (si présent)

**Tâches Non Mappées :** (si présent)

**Métriques :**

- Total Exigences
- Total Tâches
- % Couverture (exigences avec >=1 tâche)
- Nombre Ambiguïtés
- Nombre Duplications
- Nombre Problèmes Critiques

### 7. Fournir Actions Suivantes

En fin de rapport, produire bloc Actions Suivantes concis :

- Si problèmes CRITIQUES existent : Recommander résolution avant `/speckit.implement`
- Si seulement FAIBLE/MOYEN : Utilisateur peut procéder, mais fournir suggestions amélioration
- Fournir suggestions commandes explicites : ex « Exécuter /speckit.specify avec raffinement », « Exécuter /speckit.plan pour ajuster architecture », « Éditer manuellement tasks.md pour ajouter couverture 'performance-metrics' »

### 8. Proposer Remédiation

Demander utilisateur : « Souhaitez-vous que je suggère des éditions de remédiation concrètes pour les N principaux problèmes ? » (NE PAS les appliquer automatiquement.)

## Principes Opératoires

### Efficience Contexte

- **Tokens minimum signal élevé** : Se concentrer sur constatations actionnables, pas documentation exhaustive
- **Divulgation progressive** : Charger artefacts incrémentalement ; ne pas déverser tout contenu en analyse
- **Sortie token-efficiente** : Limiter table constatations à 50 lignes ; résumer débordement
- **Résultats déterministes** : Ré-exécution sans changements doit produire IDs et comptages cohérents

### Directives Analyse

- **NE JAMAIS modifier fichiers** (analyse lecture seule)
- **NE JAMAIS halluciner sections manquantes** (si absent, rapporter avec précision)
- **Prioriser violations constitution** (toujours CRITIQUES)
- **Utiliser exemples plutôt que règles exhaustives** (citer instances spécifiques, pas patterns génériques)
- **Rapporter zéro problème gracieusement** (émettre rapport succès avec statistiques couverture)

## Contexte

$ARGUMENTS
