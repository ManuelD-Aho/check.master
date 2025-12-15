---
description: Créer ou mettre à jour la constitution projet CheckMaster depuis des entrées de principes interactives ou fournies, en s'assurant que tous les templates dépendants restent synchronisés.
handoffs: 
  - label: Créer Spécification
    agent: speckit.specify
    prompt: Implémenter la spécification fonctionnalité basée sur la constitution CheckMaster (PHP 8.0+ MVC++, MySQL, DB-Driven). Je veux construire...
---

## Entrée Utilisateur

```text
$ARGUMENTS
```

Vous **DEVEZ** prendre en compte l'entrée utilisateur avant de procéder (si non vide).

## Contexte Constitution CheckMaster

### Piliers Constitution Existants (NON-NÉGOCIABLES)

La constitution CheckMaster à `.specify/memory/constitution.md` établit ces principes fondamentaux qui **NE DOIVENT PAS** être violés :

1. **Architecture Database-Driven** : Toute configuration, permissions, workflows et menus en base de données (pas fichiers PHP)
2. **Source Unique de Vérité** : Chaque élément système a exactement une source de données autoritaire
3. **Sécurité Par Défaut** : Permissions deny-all, mots de passe Argon2id, requêtes préparées, routage Hashids
4. **Séparation des Responsabilités** : MVC++ strict avec Contrôleurs (≤50 lignes), Services (logique métier), Modèles (ORM)
5. **Convention Plutôt que Configuration** : PSR-12, nommage strict (PascalCase classes, camelCase méthodes, snake_case DB)
6. **Auditabilité Totale** : Double logging (Monolog + table pister) avec snapshots avant/après
7. **Versioning Strict** : Migrations séquentielles, historique_entites pour rollback

### Contraintes Stack (IMMUABLES)

**Autorisé** :
- PHP 8.0+ (types stricts obligatoires)
- MySQL 8.0+ / MariaDB 10.5+
- 12 dépendances approuvées (~12MB total) :
  - hashids, symfony/validator, symfony/http-foundation, symfony/cache
  - mpdf, tcpdf, phpoffice/phpspreadsheet, phpmailer, monolog

**Interdit** :
- Laravel/Symfony Full Stack
- Node.js, Redis, Memcached comme dépendances requises
- Tout framework dépassant 50MB
- Requêtes SQL brutes (doit utiliser requêtes préparées)
- Logique dans Contrôleurs (doit être dans Services)
- Permissions/config codées en dur (doit être DB-driven)

### Règles Spécifiques CheckMaster

**Gestion Workflow** :
- Tous les états dans table workflow_etats
- Transitions dans table workflow_transitions
- ServiceWorkflow::effectuerTransition pour tous les changements d'état
- Gates workflow bloquent progression jusqu'à conditions remplies

**Système Permissions** :
- 13 groupes utilisateurs (Administrateur, Scolarité, Commission, Étudiant, etc.)
- Mappings traitement → action → rattacher
- ServicePermission::verifier avant opérations restreintes
- Rôles temporaires (accès président jury jour-J)

**Génération Documents** :
- 13 types PDF (reçus, PV, bulletins, attestations, etc.)
- TCPDF pour simple, mPDF pour layouts CSS complexes
- Hachage intégrité SHA256 obligatoire
- Archiver avec vérification périodique

**Système Notifications** :
- 71 templates email pour transitions workflow
- Multi-canal : Email (primaire) + Messagerie interne (backup)
- ServiceNotification::envoyer avec code template
- Suivi bounces et logique retry

**Configuration** :
- ~170 paramètres dans table configuration_systeme
- Organisés par préfixe (workflow.*, notify.*, finance.*, etc.)
- ServiceParametres::get/set pour accès
- 27 fonctionnalités désactivables via flags config

**Opérations Financières** :
- Tables paiements, pénalités, exonérations
- Génération reçu avec TCPDF
- Gates financières dans workflow (bloquer si impayé)
- Montants et règles pilotés par configuration

### Directives Amendement pour CheckMaster

Lors de la mise à jour de la constitution, respecter ces directives :

**Incrémentation Version** :
- **MAJEUR** : Changer architecture fondamentale (DB-driven → basé fichiers) - INTERDIT pour CheckMaster
- **MINEUR** : Ajouter nouveau service obligatoire (ex : ServiceReclamation)
- **PATCH** : Clarifier principes existants, corriger typos

**Critères Ajout Principe** :
- Doit adresser préoccupation transversale récurrente
- Doit être testable/vérifiable en revue code
- Ne doit pas contredire piliers existants
- Doit s'appliquer largement (pas spécifique à une fonctionnalité)

**Propagation Cohérence Requise** :
Après mises à jour constitution, vérifier :
- `.specify/templates/plan-template.md` (section Vérification Constitution)
- `.specify/templates/spec-template.md` (exigences périmètre)
- `.specify/templates/tasks-template.md` (types tâches)
- `.github/prompts/*.md` (instructions agents)
- `.github/agents/*.md` (comportements agents)

## Aperçu

Vous mettez à jour la constitution projet à `.specify/memory/constitution.md`. Ce fichier est un TEMPLATE contenant des tokens placeholder entre crochets (ex : `[NOM_PROJET]`, `[NOM_PRINCIPE_1]`). Votre travail est de (a) collecter/dériver des valeurs concrètes, (b) remplir le template précisément, et (c) propager tout amendement à travers les artefacts dépendants.

Suivre ce flux d'exécution :

1. Charger le template constitution existant à `.specify/memory/constitution.md`.
   - Identifier chaque token placeholder de la forme `[IDENTIFIANT_MAJUSCULES]`.
   **IMPORTANT** : L'utilisateur peut nécessiter moins ou plus de principes que ceux utilisés dans le template. Si un nombre est spécifié, le respecter - suivre le template général. Vous mettrez à jour le doc en conséquence.

2. Collecter/dériver valeurs pour placeholders :
   - Si entrée utilisateur (conversation) fournit une valeur, l'utiliser.
   - Sinon inférer depuis contexte repo existant (README, docs, versions constitution antérieures si intégrées).
   - Pour dates gouvernance : `DATE_RATIFICATION` est la date d'adoption originale (si inconnue demander ou marquer TODO), `DATE_DERNIER_AMENDEMENT` est aujourd'hui si changements effectués, sinon garder précédente.
   - `VERSION_CONSTITUTION` doit incrémenter selon règles versioning sémantique :
     - MAJEUR : Suppressions ou redéfinitions gouvernance/principes rétro-incompatibles.
     - MINEUR : Nouveau principe/section ajouté ou guidance matériellement étendue.
     - PATCH : Clarifications, formulation, corrections typos, raffinements non-sémantiques.
   - Si type incrémentation version ambigu, proposer raisonnement avant finalisation.

3. Rédiger le contenu constitution mis à jour :
   - Remplacer chaque placeholder par texte concret (aucun token crocheté laissé sauf slots template intentionnellement retenus que le projet a choisi de ne pas définir encore—justifier explicitement tout laissé).
   - Préserver hiérarchie titres et les commentaires peuvent être supprimés une fois remplacés sauf s'ils ajoutent encore guidance clarifiante.
   - S'assurer que chaque section Principe : ligne nom succincte, paragraphe (ou liste puces) capturant règles non-négociables, rationale explicite si non évident.
   - S'assurer que section Gouvernance liste procédure amendement, politique versioning, et attentes revue conformité.

4. Checklist propagation cohérence (convertir checklist antérieure en validations actives) :
   - Lire `.specify/templates/plan-template.md` et s'assurer que toute « Vérification Constitution » ou règles s'alignent avec principes mis à jour.
   - Lire `.specify/templates/spec-template.md` pour alignement périmètre/exigences—mettre à jour si constitution ajoute/supprime sections ou contraintes obligatoires.
   - Lire `.specify/templates/tasks-template.md` et s'assurer que la catégorisation tâches reflète les types tâches pilotés par nouveaux ou supprimés principes (ex : observabilité, versioning, discipline test).
   - Lire chaque fichier commande dans `.specify/templates/commands/*.md` (incluant celui-ci) pour vérifier qu'aucune référence obsolète (noms spécifiques agents comme CLAUDE uniquement) ne reste quand guidance générique est requise.
   - Lire tout doc guidance runtime (ex : `README.md`, `docs/quickstart.md`, ou fichiers guidance spécifiques agents si présents). Mettre à jour références aux principes changés.

5. Produire un Rapport Impact Sync (préfixer comme commentaire HTML en haut du fichier constitution après mise à jour) :
   - Changement version : ancienne → nouvelle
   - Liste principes modifiés (ancien titre → nouveau titre si renommé)
   - Sections ajoutées
   - Sections supprimées
   - Templates nécessitant mises à jour (✅ mis à jour / ⚠ en attente) avec chemins fichiers
   - TODOs suivi si placeholders intentionnellement différés.

6. Validation avant sortie finale :
   - Aucun token crochet inexpliqué restant.
   - Ligne version correspond au rapport.
   - Dates format ISO AAAA-MM-JJ.
   - Principes sont déclaratifs, testables, et exempts de langage vague ("devrait" → remplacer par rationale DOIT/DEVRAIT où approprié).

7. Écrire la constitution complétée dans `.specify/memory/constitution.md` (écraser).

8. Produire résumé final à l'utilisateur avec :
   - Nouvelle version et rationale incrémentation.
   - Tout fichier signalé pour suivi manuel.
   - Message commit suggéré (ex : `docs: amender constitution vers vX.Y.Z (ajouts principes + mise à jour gouvernance)`).

Exigences Formatage & Style :

- Utiliser titres Markdown exactement comme dans le template (ne pas rétrograder/promouvoir niveaux).
- Wrapper longues lignes rationale pour garder lisibilité (<100 caractères idéalement) mais ne pas forcer avec coupures maladroites.
- Garder une seule ligne vide entre sections.
- Éviter espaces blancs trailing.

Si l'utilisateur fournit mises à jour partielles (ex : révision un seul principe), effectuer quand même validation et étapes décision version.

Si info critique manquante (ex : date ratification vraiment inconnue), insérer `TODO(<NOM_CHAMP>) : explication` et inclure dans Rapport Impact Sync sous éléments différés.

Ne pas créer nouveau template ; toujours opérer sur le fichier `.specify/memory/constitution.md` existant.
