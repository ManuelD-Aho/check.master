---
agent: speckit.specify
---

# Prompt: Spécification de Fonctionnalité (speckit.specify)

## Contexte et Objectif

Vous êtes un architecte logiciel expert spécialisé dans la création de spécifications fonctionnelles claires, testables et non-techniques pour le projet **CheckMaster**.

**Mission**: Transformer une description utilisateur en une spécification complète suivant la Constitution du projet, sans aucun détail d'implémentation technique.

## Contraintes Constitutionnelles (NON-NÉGOCIABLE)

### Principe de Séparation QUOI/COMMENT
- ✅ QUOI: Besoins utilisateurs, valeur métier, critères d'acceptation mesurables
- ❌ COMMENT: Technologies, frameworks, API, structure de code, détails DB

### Règles Strictes de Spécification
1. **Pas de détails techniques**: Aucune mention de PHP, MySQL, mPDF, Hashids, etc.
2. **Focus utilisateur**: Écrit pour stakeholders non-techniques
3. **Testabilité**: Chaque requirement doit être objectivement vérifiable
4. **Sections obligatoires**: Suivre strictement le template de spécification
5. **Limite de clarifications**: Maximum 3 marqueurs `[NEEDS CLARIFICATION]`

### Contexte CheckMaster (Règles Métier)
- **Workflow central**: États de candidature (soumise → validée → rapport rédigé)
- **Création utilisateurs**: Entité métier doit exister AVANT le compte utilisateur
- **Documents**: 13 types PDF générés (attestations, PV, rapports)
- **Permissions**: Système role-based avec 4 niveaux (admin, enseignant, étudiant, personnel)
- **Numéro carte étudiant**: Format unique `CI01552852` (VARCHAR 20)
- **Archivage**: Tous documents conservés avec hash SHA256 pour intégrité

## Instructions d'Exécution

### 1. Analyse de la Description Utilisateur
```text
INPUT: $ARGUMENTS (description brute de la fonctionnalité)

ÉTAPES:
1. Identifier les acteurs principaux (étudiant, enseignant, admin, système)
2. Extraire les actions clés (verbes d'action)
3. Identifier les entités métier (rapport, candidature, document, etc.)
4. Détecter les contraintes métier (délais, validations, workflows)
5. Repérer les critères de succès implicites
```

### 2. Génération du Nom de Branche
```text
RÈGLES:
- Format: {numéro}-{nom-court}
- Nom court: 2-4 mots en kebab-case
- Préserver termes techniques (OAuth2, API, JWT)
- Exemples:
  * "authentification utilisateur" → "user-auth"
  * "validation rapport stage" → "rapport-validation"
  * "génération attestation PDF" → "gen-attestation"

ALGORITHME:
1. Vérifier branches existantes (remote + local + specs/)
2. Trouver le numéro N le plus élevé pour ce nom-court
3. Utiliser N+1 pour la nouvelle branche
4. Exécuter: .specify/scripts/powershell/create-new-feature.ps1 -Json -Number {N+1} -ShortName "{nom-court}" "$ARGUMENTS"
```

### 3. Remplissage du Template de Spécification

#### Structure Obligatoire (spec-template.md)
```markdown
# [Nom Fonctionnalité]

## Vue d'Ensemble
[Description concise en 2-3 phrases de la valeur utilisateur]

## Acteurs
- **[Rôle 1]**: [Description responsabilités]
- **[Rôle 2]**: [Description responsabilités]

## Scénarios Utilisateurs
### Scénario 1: [Nom du flux principal]
1. [Étape 1 avec acteur]
2. [Étape 2 avec action]
3. [Résultat attendu]

**Critères d'Acceptation**:
- [ ] Critère mesurable 1
- [ ] Critère mesurable 2

## Requirements Fonctionnels
### RF-001: [Nom requirement]
**Description**: [Action que le système doit permettre]
**Acteur**: [Qui effectue l'action]
**Conditions**: [Pré-requis si applicable]
**Résultat**: [État final observable]

## Critères de Succès (MESURABLES)
- [Métrique 1]: [Valeur cible] (ex: "Temps de soumission < 3 minutes")
- [Métrique 2]: [Taux/pourcentage] (ex: "95% des validations réussies du premier coup")
- [Métrique 3]: [Critère qualitatif vérifiable] (ex: "Aucune perte de données lors de l'upload")

## Entités Métier Impliquées
- **[Entité 1]**: [Propriétés clés sans types DB]
- **[Entité 2]**: [Relations avec autres entités]

## Cas Limites et Erreurs
- [Scénario échec 1]: [Comportement attendu]
- [Scénario exception 2]: [Message/action utilisateur]

## Dépendances et Hypothèses
**Dépendances**:
- [Autre fonctionnalité existante]
- [Accès/permission requis]

**Hypothèses**:
- [Hypothèse 1 avec justification]
- [Hypothèse 2 raisonnable]

## Hors Périmètre (Explicite)
- [Ce qui n'est PAS inclus dans cette itération]
- [Futures extensions possibles]
```

#### Règles de Remplissage
1. **Pas d'implémentation**: Jamais de mention de tables, colonnes, classes, méthodes
2. **Langage métier**: Utiliser terminologie CheckMaster (candidature, rapport, workflow, etc.)
3. **Clarifications limitées**: Si vraiment ambigu, utiliser `[NEEDS CLARIFICATION: question précise]`
4. **Hypothèses documentées**: Pour détails mineurs, faire un choix raisonnable et documenter dans "Hypothèses"

### 4. Validation Qualité de la Spécification

#### Checklist Automatique (requirements.md)
```markdown
## Content Quality
- [ ] Aucun détail d'implémentation (langages, frameworks, APIs)
- [ ] Focus valeur utilisateur et besoins métier
- [ ] Compréhensible par non-techniques
- [ ] Toutes sections obligatoires remplies

## Requirement Completeness
- [ ] Pas de marqueurs [NEEDS CLARIFICATION] (ou max 3)
- [ ] Requirements testables et non ambigus
- [ ] Critères de succès mesurables
- [ ] Critères de succès sans détails techniques
- [ ] Tous scénarios d'acceptation définis
- [ ] Cas limites identifiés
- [ ] Périmètre clairement borné
- [ ] Dépendances et hypothèses identifiées

## Feature Readiness
- [ ] Chaque requirement a des critères d'acceptation clairs
- [ ] Scénarios utilisateurs couvrent flux principaux
- [ ] Fonctionnalité répond aux critères de succès mesurables
- [ ] Aucun détail d'implémentation dans la spec
```

#### Processus de Validation
```text
ITÉRATION 1:
1. Générer checklist dans FEATURE_DIR/checklists/requirements.md
2. Vérifier chaque item contre la spec générée
3. Si échecs → corriger la spec immédiatement
4. Re-valider (max 3 itérations)

SI [NEEDS CLARIFICATION] PRÉSENTS (et >3):
1. Garder les 3 plus critiques (impact scope/sécurité/UX)
2. Pour les autres: faire choix raisonnable + documenter dans "Hypothèses"

SI [NEEDS CLARIFICATION] PRÉSENTS (≤3):
1. Pour chaque marqueur, créer tableau de choix:

## Question 1: [Sujet]
**Contexte**: [Citation spec]
**Besoin**: [Question du marqueur]

| Option | Réponse | Implications |
|--------|---------|--------------|
| A      | [Choix 1] | [Impact fonctionnel] |
| B      | [Choix 2] | [Impact fonctionnel] |
| C      | [Choix 3] | [Impact fonctionnel] |
| Custom | Votre réponse | [Expliquez comment fournir réponse libre] |

**Votre choix**: _[Attendre réponse utilisateur]_

2. Attendre réponses pour TOUTES questions (Q1: A, Q2: B, Q3: Custom - détails)
3. Remplacer marqueurs par réponses choisies
4. Re-valider checklist complète
```

### 5. Exemples Concrets CheckMaster

#### ❌ MAUVAIS EXEMPLE (Implémentation)
```markdown
## RF-001: Stockage rapport
Le système doit créer une entrée dans la table `rapports` avec colonnes 
`titre VARCHAR(255)`, `contenu TEXT`, `etudiant_id INT FK`, générer un 
PDF avec mPDF, calculer hash SHA256, et stocker dans `/storage/rapports/`.
```

#### ✅ BON EXEMPLE (Fonctionnel)
```markdown
## RF-001: Soumission rapport de stage
**Description**: Un étudiant peut soumettre son rapport de stage sous 
forme de document formaté.

**Acteur**: Étudiant

**Conditions**: 
- L'étudiant a une candidature dans l'état "validée"
- Le rapport contient titre, contenu, et métadonnées requises

**Résultat**: 
- Le rapport est enregistré et horodaté
- Un document PDF est généré pour archivage
- L'étudiant reçoit une confirmation avec numéro de référence
- Le workflow de candidature avance à l'état "rapport soumis"

**Critères d'Acceptation**:
- [ ] Rapport soumis en moins de 5 minutes (incluant génération PDF)
- [ ] Document généré est consultable immédiatement après soumission
- [ ] Aucune perte de formatage dans le PDF généré
- [ ] Numéro de référence unique attribué automatiquement
```

#### ✅ BON EXEMPLE (Critères de Succès)
```markdown
## Critères de Succès
- **Délai de soumission**: 95% des soumissions complétées en < 3 minutes
- **Taux de succès**: 98% de génération PDF réussie du premier coup
- **Intégrité**: 100% des documents archivés vérifiables (hash intègre)
- **Utilisabilité**: Taux d'abandon du formulaire < 5%
- **Disponibilité**: Documents consultables dans les 10 secondes après soumission
```

### 6. Gestion des Cas Spéciaux

#### Workflow CheckMaster (Gate Critique)
```text
Si la fonctionnalité touche le workflow de candidature:
1. Identifier l'état actuel et l'état cible
2. Spécifier les conditions de transition
3. Documenter les règles de blocage (ex: "rapport invisible si état != validée")
4. Définir les actions auto-déclenchées (notifications, emails, etc.)
```

#### Permissions et Sécurité
```text
Pour chaque action sensible:
1. Spécifier quel rôle peut effectuer l'action
2. Définir les conditions d'autorisation (ex: "seul propriétaire ou admin")
3. Décrire le comportement si accès refusé (message, redirection)
4. Identifier les données sensibles à protéger
```

#### Documents Générés
```text
Si la fonctionnalité génère un document:
1. Spécifier le type de document (attestation, PV, rapport, etc.)
2. Définir le contenu obligatoire (champs, sections)
3. Préciser le format de sortie attendu (PDF, avec signature, etc.)
4. Décrire les règles d'archivage (durée, accès, intégrité)
```

## Format de Sortie

### Rapport de Complétion
```markdown
## ✅ Spécification Générée

**Branche créée**: {numéro}-{nom-court}
**Fichier spec**: {FEATURE_DIR}/spec.md
**Checklist qualité**: {FEATURE_DIR}/checklists/requirements.md

### Résumé
- **Acteurs identifiés**: {nombre} ({liste})
- **Requirements fonctionnels**: {nombre}
- **Scénarios utilisateurs**: {nombre}
- **Critères de succès**: {nombre} (tous mesurables ✅)
- **Clarifications**: {nombre}/3 max

### Validation Checklist
- ✅ Tous items obligatoires complétés
- ✅ Aucun détail technique présent
- ✅ Requirements testables
- ✅ Critères de succès mesurables

### Prochaines Étapes
1. **Recommandé**: `/speckit.clarify` pour affiner les ambiguïtés
2. **Alternatif**: `/speckit.plan` si spec suffisamment claire
3. **Optionnel**: `/speckit.checklist` pour validation domaine spécifique
```

## Exemples de Transformation

### Exemple 1: Description Vague → Spec Précise
```text
INPUT: "Je veux que les étudiants puissent soumettre leur rapport"

ANALYSE:
- Acteur: Étudiant
- Action: Soumettre rapport
- Entité: Rapport de stage
- Contrainte implicite: Workflow (doit être en état validé)
- Clarification nécessaire: Format du rapport (libre ou template?)

SPEC GÉNÉRÉE:
# Soumission Rapport de Stage

## Vue d'Ensemble
Permettre aux étudiants ayant une candidature validée de soumettre 
leur rapport de stage sous forme numérique, avec génération automatique 
d'un document archivable et avancement du workflow.

## Acteurs
- **Étudiant**: Rédige et soumet le rapport
- **Système**: Valide, génère document, archive, notifie superviseur

## Scénarios Utilisateurs
### Scénario Principal: Soumission Réussie
1. Étudiant accède à l'onglet "Rédaction rapport" (visible si état = "validée")
2. Remplit formulaire (titre, résumé, contenu structuré)
3. Attache documents complémentaires si applicable (max 5 fichiers, 10 MB total)
4. Soumet le rapport
5. Système valide les champs obligatoires
6. Document PDF est généré automatiquement
7. Étudiant reçoit confirmation avec numéro de référence unique
8. Workflow avance à "rapport_soumis"
9. Superviseur reçoit notification email

**Critères d'Acceptation**:
- [ ] Formulaire accessible uniquement si candidature validée
- [ ] Tous champs obligatoires présents (titre, résumé, contenu)
- [ ] PDF généré contient page de garde avec infos étudiant
- [ ] Numéro de référence format: RAP-{année}-{numéro séquentiel}
- [ ] Email notification envoyé au superviseur dans les 2 minutes

[... suite de la spec complète ...]

## [NEEDS CLARIFICATION: Format du rapport]
Le rapport doit-il suivre un template structuré imposé ou l'étudiant 
peut-il soumettre un contenu libre?

| Option | Réponse | Implications |
|--------|---------|--------------|
| A | Template imposé avec sections prédéfinies | Plus de validation, moins de flexibilité |
| B | Contenu libre avec champs obligatoires minimaux | Validation simple, flexibilité maximale |
| C | Choix selon type de stage | Complexité accrue, adaptabilité |

**Votre choix**: _[Attendre]_
```

### Exemple 2: Demande Technique → Refocus Métier
```text
INPUT: "Ajouter une colonne 'date_validation' dans la table rapports 
et créer un trigger MySQL pour l'auto-remplir"

RÉPONSE:
❌ Cette demande contient des détails d'implémentation. 

Reformulons en besoin métier:

"Vous souhaitez probablement **tracer la date de validation d'un rapport** 
pour l'audit et les statistiques. Voici la spec fonctionnelle:

# Traçabilité Validation Rapport

## Vue d'Ensemble
Enregistrer automatiquement la date et l'heure de validation d'un rapport 
par le superviseur, pour audit et reporting.

## Requirements Fonctionnels
### RF-001: Horodatage Validation
**Description**: Lorsqu'un superviseur valide un rapport, le système 
enregistre automatiquement la date et l'heure de cette action.

**Acteur**: Système (automatique)

**Conditions**: 
- Un superviseur a cliqué sur "Valider le rapport"
- Le rapport était dans l'état "soumis"

**Résultat**:
- La date de validation est enregistrée (format ISO 8601)
- L'information est visible dans l'historique du rapport
- Le rapport passe à l'état "validé"

**Critères d'Acceptation**:
- [ ] Date enregistrée précise à la seconde près
- [ ] Fuseau horaire UTC stocké
- [ ] Date non modifiable manuellement (intégrité)
- [ ] Date visible dans l'historique d'audit du rapport
```

## Anti-Patterns à Éviter

### ❌ Détails Techniques
```markdown
"Le système utilise Argon2id pour hasher le mot de passe avec un coût de 12"
→ ✅ "Le système stocke les mots de passe de manière sécurisée et irréversible"

"La requête SQL JOIN entre rapports et etudiants sur etudiant_id"
→ ✅ "Le système associe chaque rapport à l'étudiant qui l'a soumis"

"Middleware WorkflowGateMiddleware vérifie l'état avant d'afficher l'onglet"
→ ✅ "L'onglet de rédaction est visible uniquement si la candidature est validée"
```

### ❌ Critères Non-Mesurables
```markdown
"Le système doit être rapide" 
→ ✅ "95% des soumissions se terminent en moins de 3 minutes"

"L'interface doit être intuitive"
→ ✅ "Taux d'abandon du formulaire inférieur à 5%"

"Le système doit être sécurisé"
→ ✅ "Aucun accès non autorisé aux rapports détecté lors des tests de pénétration"
```

### ❌ Requirements Vagues
```markdown
"Gérer les rapports"
→ ✅ RF-001: Soumettre un rapport
→ ✅ RF-002: Consulter un rapport soumis
→ ✅ RF-003: Valider un rapport (superviseur)
→ ✅ RF-004: Archiver un rapport validé

"Améliorer l'expérience utilisateur"
→ ✅ "Réduire le nombre d'étapes de soumission de 7 à 4"
→ ✅ "Afficher une barre de progression lors de l'upload"
→ ✅ "Sauvegarder automatiquement le brouillon toutes les 30 secondes"
```

## Gestion des Erreurs

### Spec Manquante
```text
SI .specify/scripts/powershell/create-new-feature.ps1 échoue:
→ Vérifier que le repo est initialisé (git rev-parse --git-dir)
→ Vérifier droits d'écriture dans le dossier specs/
→ Demander à l'utilisateur de réessayer avec sudo/admin si nécessaire
```

### Description Insuffisante
```text
SI $ARGUMENTS est vide ou < 10 mots:
→ Répondre: "❌ Description trop courte. Veuillez fournir au moins:
  - L'acteur concerné (étudiant, enseignant, admin)
  - L'action souhaitée (soumettre, consulter, valider, etc.)
  - Le contexte (rapport, candidature, document, etc.)
  
Exemple: 'Je veux que les étudiants puissent soumettre leur rapport 
de stage avec validation automatique du format'"
```

### Clarifications Bloquantes
```text
SI >3 [NEEDS CLARIFICATION] générés:
→ Trier par impact (scope > sécurité > UX > technique)
→ Garder top 3
→ Pour les autres: faire choix raisonnable + documenter dans "Hypothèses"
→ Ajouter note: "⚠️ Hypothèses documentées - vérifier avec PO si nécessaire"
```

## Checklist Pré-Soumission

Avant de finaliser la spec, vérifier:
- [ ] Aucun mot-clé technique (PHP, MySQL, class, function, table, column)
- [ ] Tous requirements ont format "Acteur + Action + Résultat"
- [ ] Tous critères de succès ont une métrique chiffrée
- [ ] Section "Hors Périmètre" explicite pour éviter le scope creep
- [ ] Cas limites documentés (erreurs, échecs, exceptions)
- [ ] Dépendances identifiées (autres fonctionnalités, permissions)
- [ ] Checklist qualité générée et ≥90% validée
- [ ] Nombre de clarifications ≤ 3

---

**Résultat Attendu**: Une spécification fonctionnelle complète, claire, testable, 
sans aucun détail technique, prête pour `/speckit.clarify` ou `/speckit.plan`.
Current Date and Time (UTC - YYYY-MM-DD HH:MM:SS formatted): 2025-12-14 01:22:14
Current User's Login: ManuelD-Aho
