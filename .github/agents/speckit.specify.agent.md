---
description: Créer ou mettre à jour la spécification fonctionnalité depuis une description en langage naturel pour CheckMaster.
handoffs: 
  - label: Créer Plan Technique
    agent: speckit.plan
    prompt: Créer un plan pour la spec. Je construis avec l'architecture CheckMaster (PHP 8.0+ MVC++ natif, MySQL, configuration DB-Driven).
  - label: Clarifier Exigences Spec
    agent: speckit.clarify
    prompt: Clarifier les exigences de spécification pour CheckMaster
    send: true
---

## Entrée Utilisateur

```text
$ARGUMENTS
```

Vous **DEVEZ** prendre en compte l'entrée utilisateur avant de procéder (si non vide).

## Aperçu

Le texte que l'utilisateur a tapé après `/speckit.specify` dans le message déclencheur **est** la description de la fonctionnalité. Supposez que vous l'avez toujours disponible dans cette conversation même si `$ARGUMENTS` apparaît littéralement ci-dessous. Ne demandez pas à l'utilisateur de la répéter sauf s'il a fourni une commande vide.

Étant donné cette description de fonctionnalité, faire ceci :

1. **Générer un nom court concis** (2-4 mots) pour la branche :
   - Analyser la description de fonctionnalité et extraire les mots-clés les plus significatifs
   - Créer un nom court de 2-4 mots qui capture l'essence de la fonctionnalité
   - Utiliser le format action-nom quand possible (ex : "ajouter-auth-utilisateur", "corriger-bug-paiement")
   - Préserver termes techniques et acronymes (OAuth2, API, JWT, etc.)
   - Garder concis mais suffisamment descriptif pour comprendre la fonctionnalité d'un coup d'œil
   - Exemples :
     - "Je veux ajouter l'authentification utilisateur" → "auth-utilisateur"
     - "Implémenter l'intégration OAuth2 pour l'API" → "integration-oauth2-api"
     - "Créer un tableau de bord pour les analytics" → "tableau-bord-analytics"
     - "Corriger le bug de timeout du traitement paiement" → "corriger-timeout-paiement"

2. **Vérifier les branches existantes avant d'en créer une nouvelle** :

   a. D'abord, récupérer toutes les branches distantes pour s'assurer d'avoir les dernières informations :

      ```bash
      git fetch --all --prune
      ```

   b. Trouver le numéro de fonctionnalité le plus élevé à travers toutes les sources pour le nom-court :
      - Branches distantes : `git ls-remote --heads origin | grep -E 'refs/heads/[0-9]+-<nom-court>$'`
      - Branches locales : `git branch | grep -E '^[* ]*[0-9]+-<nom-court>$'`
      - Répertoires specs : Vérifier les répertoires correspondant à `specs/[0-9]+-<nom-court>`

   c. Déterminer le prochain numéro disponible :
      - Extraire tous les numéros des trois sources
      - Trouver le numéro le plus élevé N
      - Utiliser N+1 pour le nouveau numéro de branche

   d. Exécuter le script `.specify/scripts/powershell/create-new-feature.ps1 -Json "$ARGUMENTS"` avec le numéro calculé et le nom-court :
      - Passer `--number N+1` et `--short-name "votre-nom-court"` avec la description fonctionnalité
      - Exemple Bash : `.specify/scripts/powershell/create-new-feature.ps1 -Json "$ARGUMENTS" --json --number 5 --short-name "auth-utilisateur" "Ajouter authentification utilisateur"`
      - Exemple PowerShell : `.specify/scripts/powershell/create-new-feature.ps1 -Json "$ARGUMENTS" -Json -Number 5 -ShortName "auth-utilisateur" "Ajouter authentification utilisateur"`

   **IMPORTANT** :
   - Vérifier les trois sources (branches distantes, branches locales, répertoires specs) pour trouver le numéro le plus élevé
   - Ne matcher que les branches/répertoires avec le pattern exact nom-court
   - Si aucune branche/répertoire existant trouvé avec ce nom-court, commencer avec numéro 1
   - Vous ne devez exécuter ce script qu'une seule fois par fonctionnalité
   - Le JSON est fourni dans le terminal en sortie - toujours s'y référer pour obtenir le contenu réel recherché
   - La sortie JSON contiendra les chemins BRANCH_NAME et SPEC_FILE
   - Pour apostrophes dans args comme "J'ajoute", utiliser syntaxe échappement : ex 'J'\''ajoute' (ou guillemets si possible : "J'ajoute")

3. Charger `.specify/templates/spec-template.md` pour comprendre les sections requises.

4. Suivre ce flux d'exécution :

    1. Parser description utilisateur depuis Entrée
       Si vide : ERREUR "Aucune description de fonctionnalité fournie"
    2. Extraire concepts clés de la description
       Identifier : acteurs, actions, données, contraintes
    3. Pour aspects flous :
       - Faire des suppositions informées basées sur contexte et standards industrie
       - Ne marquer avec [NÉCESSITE CLARIFICATION : question spécifique] que si :
         - Le choix impacte significativement le périmètre fonctionnalité ou l'expérience utilisateur
         - Plusieurs interprétations raisonnables existent avec implications différentes
         - Aucun défaut raisonnable n'existe
       - **LIMITE : Maximum 3 marqueurs [NÉCESSITE CLARIFICATION] au total**
       - Prioriser clarifications par impact : périmètre > sécurité/confidentialité > expérience utilisateur > détails techniques
    4. Remplir section Scénarios Utilisateurs & Tests
       Si pas de flux utilisateur clair : ERREUR "Impossible de déterminer les scénarios utilisateurs"
    5. Générer Exigences Fonctionnelles
       Chaque exigence doit être testable
       Utiliser des défauts raisonnables pour détails non spécifiés (documenter hypothèses dans section Hypothèses)
    6. Définir Critères de Succès
       Créer résultats mesurables, agnostiques technologie
       Inclure métriques quantitatives (temps, performance, volume) et mesures qualitatives (satisfaction utilisateur, complétion tâche)
       Chaque critère doit être vérifiable sans détails implémentation
    7. Identifier Entités Clés (si données impliquées)
    8. Retourner : SUCCÈS (spec prête pour planification)

5. Écrire la spécification dans SPEC_FILE utilisant la structure template, remplaçant placeholders par détails concrets dérivés de la description fonctionnalité (arguments) tout en préservant ordre et titres des sections.

6. **Validation Qualité Spécification** : Après écriture de la spec initiale, la valider contre les critères qualité :

   a. **Créer Checklist Qualité Spec** : Générer un fichier checklist à `FEATURE_DIR/checklists/requirements.md` utilisant la structure template checklist avec ces éléments validation :

      ```markdown
      # Checklist Qualité Spécification : [NOM FONCTIONNALITÉ]
      
      **Objectif** : Valider complétude et qualité spécification avant de procéder à la planification
      **Créé** : [DATE]
      **Fonctionnalité** : [Lien vers spec.md]
      
      ## Qualité Contenu
      
      - [ ] Pas de détails implémentation (langages, frameworks, APIs)
      - [ ] Focus sur valeur utilisateur et besoins métier
      - [ ] Écrit pour parties prenantes non-techniques
      - [ ] Toutes les sections obligatoires complétées
      
      ## Complétude Exigences
      
      - [ ] Aucun marqueur [NÉCESSITE CLARIFICATION] ne reste
      - [ ] Exigences sont testables et non ambiguës
      - [ ] Critères succès sont mesurables
      - [ ] Critères succès sont agnostiques technologie (pas de détails implémentation)
      - [ ] Tous les scénarios acceptation sont définis
      - [ ] Cas limites sont identifiés
      - [ ] Périmètre est clairement délimité
      - [ ] Dépendances et hypothèses identifiées
      
      ## Préparation Fonctionnalité
      
      - [ ] Toutes les exigences fonctionnelles ont critères acceptation clairs
      - [ ] Scénarios utilisateur couvrent flux primaires
      - [ ] Fonctionnalité atteint résultats mesurables définis dans Critères Succès
      - [ ] Aucun détail implémentation ne fuite dans spécification
      
      ## Notes
      
      - Éléments marqués incomplets nécessitent mises à jour spec avant `/speckit.clarify` ou `/speckit.plan`
      ```

   b. **Exécuter Vérification Validation** : Revoir la spec contre chaque élément checklist :
      - Pour chaque élément, déterminer s'il passe ou échoue
      - Documenter problèmes spécifiques trouvés (citer sections spec pertinentes)

   c. **Gérer Résultats Validation** :

      - **Si tous les éléments passent** : Marquer checklist complète et procéder à étape 6

      - **Si éléments échouent (excluant [NÉCESSITE CLARIFICATION])** :
        1. Lister les éléments échoués et problèmes spécifiques
        2. Mettre à jour la spec pour adresser chaque problème
        3. Ré-exécuter validation jusqu'à ce que tous les éléments passent (max 3 itérations)
        4. Si toujours en échec après 3 itérations, documenter problèmes restants dans notes checklist et avertir utilisateur

      - **Si marqueurs [NÉCESSITE CLARIFICATION] restent** :
        1. Extraire tous les marqueurs [NÉCESSITE CLARIFICATION : ...] de la spec
        2. **VÉRIFICATION LIMITE** : Si plus de 3 marqueurs existent, garder uniquement les 3 plus critiques (par impact périmètre/sécurité/UX) et faire des suppositions informées pour le reste
        3. Pour chaque clarification nécessaire (max 3), présenter options à l'utilisateur dans ce format :

           ```markdown
           ## Question [N] : [Sujet]
           
           **Contexte** : [Citer section spec pertinente]
           
           **Ce que nous devons savoir** : [Question spécifique du marqueur NÉCESSITE CLARIFICATION]
           
           **Réponses Suggérées** :
           
           | Option | Réponse | Implications |
           |--------|---------|--------------|
           | A      | [Première réponse suggérée] | [Ce que cela signifie pour la fonctionnalité] |
           | B      | [Deuxième réponse suggérée] | [Ce que cela signifie pour la fonctionnalité] |
           | C      | [Troisième réponse suggérée] | [Ce que cela signifie pour la fonctionnalité] |
           | Libre  | Fournir votre propre réponse | [Expliquer comment fournir entrée personnalisée] |
           
           **Votre choix** : _[Attendre réponse utilisateur]_
           ```

        4. **CRITIQUE - Formatage Table** : S'assurer que les tables markdown sont correctement formatées :
           - Utiliser espacement cohérent avec pipes alignés
           - Chaque cellule doit avoir espaces autour du contenu : `| Contenu |` pas `|Contenu|`
           - Séparateur en-tête doit avoir au moins 3 tirets : `|--------|`
           - Tester que la table se rend correctement en aperçu markdown
        5. Numéroter questions séquentiellement (Q1, Q2, Q3 - max 3 total)
        6. Présenter toutes les questions ensemble avant d'attendre les réponses
        7. Attendre que l'utilisateur réponde avec ses choix pour toutes les questions (ex : "Q1 : A, Q2 : Libre - [détails], Q3 : B")
        8. Mettre à jour la spec en remplaçant chaque marqueur [NÉCESSITE CLARIFICATION] par la réponse sélectionnée ou fournie par l'utilisateur
        9. Ré-exécuter validation après résolution de toutes les clarifications

   d. **Mettre à jour Checklist** : Après chaque itération validation, mettre à jour le fichier checklist avec statut pass/fail actuel

7. Rapporter complétion avec nom de branche, chemin fichier spec, résultats checklist, et préparation pour la phase suivante (`/speckit.clarify` ou `/speckit.plan`).

**NOTE :** Le script crée et checkout la nouvelle branche et initialise le fichier spec avant écriture.

## Connaissance Domaine CheckMaster

### Contexte Système
CheckMaster est un système complet de gestion académique pour la supervision des mémoires de Master à l'UFR MI. Comprendre ce contexte est critique pour écrire des spécifications significatives.

**États Workflow Principaux** :
- INSCRIT → CANDIDATURE_SOUMISE → VERIFICATION_SCOLARITE → FILTRE_COMMUNICATION → EN_ATTENTE_COMMISSION → EN_EVALUATION_COMMISSION → RAPPORT_VALIDE → ATTENTE_AVIS_ENCADREUR → PRET_POUR_JURY → JURY_EN_CONSTITUTION → SOUTENANCE_PLANIFIEE → SOUTENANCE_EN_COURS → SOUTENANCE_TERMINEE → DIPLOME_DELIVRE

**Règles Métier Clés** :
1. **Gate Critique** : Rédaction rapport BLOQUÉE jusqu'à état candidature_validée
2. **Unanimité Commission** : 3 tours de vote maximum, puis escalade au Doyen
3. **Piste Audit** : Toutes les actions critiques doivent être traçables avec snapshots avant/après
4. **Prérequis Financiers** : Étudiants doivent régler paiements avant validation candidature
5. **Rôle Temporaire** : Présidents jury obtiennent accès menu temporaire uniquement le jour de la soutenance
6. **Routage Hashids** : Tous les IDs entités doivent être masqués dans URLs pour sécurité

**Acteurs Système** :
- **Administrateur** (Groupe 5) : Contrôle complet système, configuration, gestion utilisateurs
- **Scolarité** (Groupe 8) : Dossiers étudiants, paiements, validation candidature
- **Communication** (Groupe 7) : Vérification format rapport
- **Commission** (Groupe 11) : Évaluation contenu rapport et validation
- **Président Commission** : Gestion commission, constitution jury
- **Enseignant** (Groupe 9/10/12) : Gestion filière, gestion niveau, ou enseignement simple
- **Président Jury** (Rôle temporaire) : Saisie notes jour de soutenance
- **Étudiant** (Groupe 13) : Rédaction rapport, soumission candidature
- **Secrétaire** (Groupe 6) : Gestion documents administratifs

**Entités Clés** :
- etudiants (num_etu VARCHAR(20) unique, non-autogénéré comme CI01552852)
- dossiers_etudiants (suivi workflow_etat)
- rapports_etudiants (versioning, annotations)
- candidatures (info stage, états validation)
- sessions_commission (sessions mensuelles, tours de vote)
- jury_membres (rôles : président, rapporteur, examinateur, directeur, maître stage)
- soutenances (planification, détection conflits)
- paiements (tranches, pénalités, reçus)
- utilisateurs (liés à etudiants/enseignants/personnel_admin)

**Fonctionnalités Critiques** :
- **13 Types Documents** : Reçus, PV, Bulletins, Attestations, Convocations (mPDF/TCPDF)
- **71 Templates Email** : Notifications automatisées à chaque transition workflow
- **27 Fonctionnalités Désactivables** : Escalade, signatures électroniques, messagerie, etc.
- **~170 Paramètres Configuration** : DB-driven (etablissement.*, workflow.*, notify.*, etc.)
- **21 Référentiels** : Grades, fonctions, spécialités, critères évaluation, mentions

**Sécurité & Conformité** :
- Hachage mot de passe Argon2id obligatoire
- Protection CSRF sur tous les formulaires
- Limitation débit sur routes sensibles
- Intégrité SHA256 pour documents archivés
- Table audit avec double logging Monolog
- Requêtes préparées uniquement (pas de SQL brut)

### Contexte Spécification pour CheckMaster

Lors de l'écriture de specs pour fonctionnalités CheckMaster, considérer :

1. **Quels états workflow sont affectés ?** Mapper fonctionnalité au cycle de vie candidature/rapport/soutenance
2. **Quels groupes utilisateurs ont besoin d'accès ?** Vérifier contre 13 permissions groupes
3. **Quelles notifications sont déclenchées ?** Identifier exigences email/messagerie
4. **Des documents sont-ils générés ?** Spécifier type PDF (simple TCPDF vs complexe mPDF)
5. **Cela nécessite-t-il transition workflow ?** Définir conditions état_source → état_cible
6. **Impact financier ?** Considérer paiements, pénalités, génération reçus
7. **Exigences audit ?** Déterminer ce qui est journalisé avec snapshots
8. **Scénarios escalade ?** Gérer cas délais, absence, blocage
9. **Besoins configuration ?** Vérifier si nouveaux paramètres config nécessaires (workflow.*, notify.*, etc.)
10. **Impact référentiel ?** Identifier si nouvelles données référence nécessaires (immuable vs modifiable)

### Patterns Communs CheckMaster

**Flux Candidature** :
```
Étudiant soumet → Scolarité valide (vérification paiements) → Communication approuve (format) → 
Rédaction rapport DÉBLOQUÉE → Étudiant rédige → Soumet → Commission évalue → Valide
```

**Vote Commission** :
```
Tour 1 (48h) → Unanimité ? Oui=Valider, Non=Tour 2 (48h) → 
Unanimité ? Oui=Valider, Non=Tour 3 (24h) → Unanimité ? Oui=Valider, Non=Escalade au Doyen
```

**Génération Documents** :
```
Déclencheur action → ServicePdf::generer($type, $data) → PDF créé → 
Calculer SHA256 → Archiver → Notification avec lien téléchargement
```

**Pattern Notification** :
```
Événement → ServiceNotification::send($template, $destinataires, $variables) →
File → Worker envoie (Email primaire, Messagerie backup) → Traquer bounces → Archiver historique
```

## Directives Générales

## Directives Rapides

- Focus sur **QUOI** les utilisateurs ont besoin et **POURQUOI**.
- Éviter COMMENT implémenter (pas de stack technique, APIs, structure code).
- Écrit pour parties prenantes métier, pas développeurs.
- NE PAS créer de checklists intégrées dans la spec. Ce sera une commande séparée.
- **Pour CheckMaster** : Toujours considérer quel(s) état(s) workflow sont impactés et quels groupes utilisateurs ont besoin de permissions.

### Exigences Sections

- **Sections obligatoires** : Doivent être complétées pour chaque fonctionnalité
- **Sections optionnelles** : Inclure uniquement quand pertinent pour la fonctionnalité
- Quand une section ne s'applique pas, la supprimer entièrement (ne pas laisser comme "N/A")

### Pour Génération IA

Lors de la création de cette spec depuis un prompt utilisateur :

1. **Faire des suppositions informées** : Utiliser contexte, standards industrie, et patterns communs pour combler les lacunes
2. **Documenter hypothèses** : Enregistrer défauts raisonnables dans la section Hypothèses
3. **Limiter clarifications** : Maximum 3 marqueurs [NÉCESSITE CLARIFICATION] - utiliser uniquement pour décisions critiques qui :
   - Impactent significativement périmètre fonctionnalité ou expérience utilisateur
   - Ont plusieurs interprétations raisonnables avec implications différentes
   - Manquent de tout défaut raisonnable
4. **Prioriser clarifications** : périmètre > sécurité/confidentialité > expérience utilisateur > détails techniques
5. **Penser comme un testeur** : Chaque exigence vague doit échouer à l'élément checklist "testable et non ambigu"
6. **Domaines communs nécessitant clarification** (uniquement si pas de défaut raisonnable existe) :
   - Périmètre et limites fonctionnalité (inclure/exclure cas d'usage spécifiques)
   - Types utilisateurs et permissions (si plusieurs interprétations conflictuelles possibles)
   - Exigences sécurité/conformité (quand légalement/financièrement significatif)

**Exemples de défauts raisonnables** (ne pas demander à propos de ceux-ci) :

- Rétention données : Pratiques standard industrie pour le domaine
- Cibles performance : Attentes apps web/mobile standard sauf si spécifié
- Gestion erreurs : Messages conviviaux avec fallbacks appropriés
- Méthode authentification : Session standard ou OAuth2 pour apps web
- Patterns intégration : APIs RESTful sauf si spécifié autrement

### Directives Critères Succès

Les critères succès doivent être :

1. **Mesurables** : Inclure métriques spécifiques (temps, pourcentage, compte, taux)
2. **Agnostiques technologie** : Aucune mention de frameworks, langages, bases de données, ou outils
3. **Focus utilisateur** : Décrire résultats depuis perspective utilisateur/métier, pas internes système
4. **Vérifiables** : Peuvent être testés/validés sans connaître détails implémentation

**Bons exemples** :

- "Les utilisateurs peuvent compléter le checkout en moins de 3 minutes"
- "Le système supporte 10 000 utilisateurs simultanés"
- "95% des recherches retournent résultats en moins de 1 seconde"
- "Le taux de complétion de tâche améliore de 40%"

**Mauvais exemples** (focus implémentation) :

- "Le temps de réponse API est sous 200ms" (trop technique, utiliser "Les utilisateurs voient les résultats instantanément")
- "La base de données peut gérer 1000 TPS" (détail implémentation, utiliser métrique côté utilisateur)
- "Les composants React rendent efficacement" (spécifique framework)
- "Le taux de cache Redis au-dessus de 80%" (spécifique technologie)
