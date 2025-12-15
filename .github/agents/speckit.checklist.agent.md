---
description: Générer une checklist personnalisée pour la fonctionnalité CheckMaster actuelle basée sur les exigences utilisateur - valide la qualité des exigences, pas l'implémentation.
---

## Objectif Checklist : « Tests Unitaires pour le Français »

**CONCEPT CRITIQUE** : Les checklists sont des **TESTS UNITAIRES POUR LA RÉDACTION D'EXIGENCES** - elles valident la qualité, clarté et complétude des exigences dans un domaine donné.

**PAS pour vérification/test** :

- ❌ PAS « Vérifier que le bouton clique correctement »
- ❌ PAS « Tester que la gestion d'erreurs fonctionne »
- ❌ PAS « Confirmer que l'API retourne 200 »
- ❌ PAS vérifier si code/implémentation correspond à la spec

**POUR validation qualité exigences** :

- ✅ « Les transitions d'état workflow sont-elles définies pour toutes les opérations candidature ? » (complétude)
- ✅ « 'Validé par scolarité' est-il quantifié avec critères spécifiques ? » (clarté)
- ✅ « Les exigences permissions sont-elles cohérentes pour toutes les actions admin ? » (cohérence)
- ✅ « Les exigences routage Hashids sont-elles définies pour toutes les URLs entités ? » (couverture)
- ✅ « La spec définit-elle ce qui se passe quand une transition ServiceWorkflow échoue ? » (cas limites)

**Métaphore** : Si votre spec est du code écrit en français, la checklist est sa suite de tests unitaires. Vous testez si les exigences sont bien écrites, complètes, non ambiguës et prêtes pour l'implémentation - PAS si l'implémentation fonctionne.

## Catégories Checklist Spécifiques au Domaine CheckMaster

Lors de la génération de checklists pour fonctionnalités CheckMaster, considérer ces catégories standard :

### 1. Checklist Conformité Architecture (architecture.md)

Valide l'adhérence spec à la Constitution CheckMaster :

```markdown
- [ ] CHK001 Toutes les exigences configuration sont-elles définies comme paramètres DB (pas constantes PHP) ?
- [ ] CHK002 Toutes les vérifications permissions sont-elles spécifiées via mappings groupe_utilisateur → traitement → action ?
- [ ] CHK003 Tous les IDs entités sont-ils spécifiés pour utiliser Hashids dans URLs ?
- [ ] CHK004 Argon2id est-il spécifié pour toute gestion de mot de passe ?
- [ ] CHK005 Toutes les opérations SQL sont-elles spécifiées pour utiliser requêtes préparées ?
- [ ] CHK006 La journalisation ServiceAudit est-elle spécifiée pour toutes les opérations d'écriture ?
- [ ] CHK007 Les responsabilités Contrôleur sont-elles limitées à validation + service + réponse ?
- [ ] CHK008 La logique métier est-elle spécifiée pour résider dans Services (pas Contrôleurs) ?
- [ ] CHK009 Les transactions sont-elles spécifiées pour opérations multi-tables ?
- [ ] CHK010 L'injection de dépendances est-elle spécifiée via constructeur ?
```

### 2. Checklist Workflow (workflow.md)

Valide les exigences liées au workflow :

```markdown
- [ ] CHK001 Tous les états workflow affectés sont-ils explicitement listés ?
- [ ] CHK002 L'état source est-il défini pour chaque transition ?
- [ ] CHK003 L'état cible est-il défini pour chaque transition ?
- [ ] CHK004 Les conditions de transition sont-elles spécifiées (qui peut déclencher, quand) ?
- [ ] CHK005 Les gates workflow sont-elles clairement définies (ce qui bloque transition) ?
- [ ] CHK006 ServiceWorkflow::effectuerTransition est-il spécifié pour changements d'état ?
- [ ] CHK007 Les exigences notification sont-elles définies pour chaque transition ?
- [ ] CHK008 Le snapshot workflow_historique est-il spécifié pour audit ?
- [ ] CHK009 Les scénarios escalade sont-ils définis pour délais dépassés ?
- [ ] CHK010 Le comportement rollback est-il spécifié si transition échoue ?
```

### 3. Checklist Permissions & Accès (permissions.md)

Valide les exigences contrôle d'accès :

```markdown
- [ ] CHK001 Tous les groupes utilisateurs nécessitant accès sont-ils explicitement listés ?
- [ ] CHK002 Chaque groupe est-il mappé à une entrée traitement ?
- [ ] CHK003 Les actions requises (Consulter/Créer/Modifier/etc.) sont-elles spécifiées par groupe ?
- [ ] CHK004 ServicePermission::verifier est-il spécifié avant actions restreintes ?
- [ ] CHK005 Les rôles temporaires sont-ils définis avec périodes validité (si applicable) ?
- [ ] CHK006 Le comportement fallback est-il spécifié pour refus permission ?
- [ ] CHK007 Les règles invalidation cache permissions sont-elles définies ?
- [ ] CHK008 Le code ressource (traitement) est-il clairement identifié ?
- [ ] CHK009 Les capacités override admin sont-elles spécifiées (si présentes) ?
- [ ] CHK010 La journalisation audit est-elle spécifiée pour octrois/révocations permissions ?
```

### 4. Checklist Notifications (notifications.md)

Valide les exigences communication :

```markdown
- [ ] CHK001 Tous les déclencheurs notification sont-ils clairement définis ?
- [ ] CHK002 Le code template notification est-il spécifié ?
- [ ] CHK003 Les rôles/groupes destinataires sont-ils explicitement listés ?
- [ ] CHK004 Le sujet et les placeholders corps email sont-ils définis ?
- [ ] CHK005 Le backup messagerie interne est-il spécifié ?
- [ ] CHK006 L'usage ServiceNotification::envoyer est-il spécifié ?
- [ ] CHK007 Les variables notification (données dynamiques) sont-elles listées ?
- [ ] CHK008 Le comportement gestion bounces est-il spécifié ?
- [ ] CHK009 Les règles retry sont-elles définies pour envois échoués ?
- [ ] CHK010 L'archivage notification_historique est-il spécifié ?
```

### 5. Checklist Génération Documents (documents.md)

Valide les exigences génération PDF :

```markdown
- [ ] CHK001 Le type document est-il clairement identifié (reçu, PV, bulletin, etc.) ?
- [ ] CHK002 Le moteur PDF (TCPDF vs mPDF) est-il spécifié selon complexité ?
- [ ] CHK003 Toutes les exigences données document (variables) sont-elles listées ?
- [ ] CHK004 L'emplacement template PDF est-il spécifié (ressources/templates/pdf/) ?
- [ ] CHK005 Le calcul hash SHA256 est-il spécifié pour archivage ?
- [ ] CHK006 Le stockage table archives est-il spécifié ?
- [ ] CHK007 Les permissions téléchargement (qui peut accéder) sont-elles définies ?
- [ ] CHK008 La capacité régénération est-elle spécifiée (depuis snapshots) ?
- [ ] CHK009 Les règles vérification intégrité document sont-elles définies ?
- [ ] CHK010 La notification disponibilité document est-elle spécifiée ?
```

### 6. Checklist Opérations Financières (financial.md)

Valide les exigences paiement/pénalité :

```markdown
- [ ] CHK001 Les règles de calcul sont-elles explicitement définies (formules, montants) ?
- [ ] CHK002 La source configuration est-elle spécifiée (paramètres finance.*) ?
- [ ] CHK003 Les déclencheurs enregistrement paiement sont-ils définis ?
- [ ] CHK004 La logique calcul pénalité est-elle spécifiée (délais, taux) ?
- [ ] CHK005 La génération reçu est-elle spécifiée (TCPDF + archivage) ?
- [ ] CHK006 Les vérifications gate financières sont-elles définies (bloqueurs workflow) ?
- [ ] CHK007 Le suivi statut paiement est-il spécifié ?
- [ ] CHK008 La mise à jour tableau de bord financier étudiant est-elle spécifiée ?
- [ ] CHK009 Les règles exonération sont-elles définies (si applicable) ?
- [ ] CHK010 La journalisation audit financière est-elle spécifiée ?
```

### 7. Checklist Commission/Vote (commission.md)

Valide les exigences évaluation commission :

```markdown
- [ ] CHK001 Le mécanisme vote est-il spécifié (unanimité/majorité) ?
- [ ] CHK002 Le maximum 3 tours est-il appliqué ?
- [ ] CHK003 L'escalade au Doyen est-elle spécifiée après tour 3 ?
- [ ] CHK004 Les exigences suivi votes sont-elles définies (sessions_commission) ?
- [ ] CHK005 Les exigences notification sont-elles définies par tour de vote ?
- [ ] CHK006 La génération PV est-elle spécifiée (template, signatures) ?
- [ ] CHK007 Les règles attribution membres sont-elles définies ?
- [ ] CHK008 L'exigence quorum est-elle spécifiée ?
- [ ] CHK009 Les procédures résolution conflits sont-elles définies ?
- [ ] CHK010 L'anonymat/visibilité votes est-il spécifié ?
```

### 8. Checklist Modèle Données (data.md)

Valide les exigences entité/table :

```markdown
- [ ] CHK001 Tous les noms entités sont-ils définis en snake_case ?
- [ ] CHK002 Le format clé primaire est-il spécifié (id_nomtable) ?
- [ ] CHK003 Toutes les clés étrangères sont-elles définies avec comportement ON DELETE ?
- [ ] CHK004 Les index requis sont-ils spécifiés (FK, colonnes recherche) ?
- [ ] CHK005 Les contraintes unicité sont-elles définies où nécessaire ?
- [ ] CHK006 Les schémas colonnes JSON sont-ils définis (si utilisant type JSON) ?
- [ ] CHK007 Les noms fichiers migration sont-ils spécifiés (0XX_description) ?
- [ ] CHK008 Les relations tables sont-elles clairement documentées ?
- [ ] CHK009 La validation données est-elle spécifiée (NOT NULL, plages, formats) ?
- [ ] CHK010 Les colonnes audit sont-elles spécifiées (created_at, updated_at) ?
```

### 9. Checklist Sécurité (security.md)

Valide les exigences sécurité :

```markdown
- [ ] CHK001 Argon2id est-il spécifié pour hachage mot de passe ?
- [ ] CHK002 Les requêtes préparées sont-elles spécifiées pour tout SQL ?
- [ ] CHK003 L'échappement e() est-il spécifié pour toute sortie vue ?
- [ ] CHK004 Les tokens CSRF sont-ils spécifiés pour tous les formulaires ?
- [ ] CHK005 La limitation de débit est-elle spécifiée pour endpoints sensibles ?
- [ ] CHK006 La validation entrée est-elle spécifiée (Symfony Validator) ?
- [ ] CHK007 ServiceAudit est-il spécifié pour actions pertinentes sécurité ?
- [ ] CHK008 Les vérifications permissions sont-elles spécifiées avant accès données ?
- [ ] CHK009 La gestion session est-elle spécifiée (timeout, invalidation) ?
- [ ] CHK010 Les règles manipulation données sensibles sont-elles définies (PII, credentials) ?
```

### 10. Checklist Intégration (integration.md)

Valide les exigences intégration Services :

```markdown
- [ ] CHK001 Tous les Services dépendants sont-ils explicitement listés ?
- [ ] CHK002 L'intégration ServiceWorkflow est-elle spécifiée (si changements workflow) ?
- [ ] CHK003 L'intégration ServiceNotification est-elle spécifiée (si notifications) ?
- [ ] CHK004 L'intégration ServicePermission est-elle spécifiée (si contrôle accès) ?
- [ ] CHK005 L'intégration ServiceAudit est-elle spécifiée (si écritures données) ?
- [ ] CHK006 L'intégration ServiceParametres est-elle spécifiée (si configuration) ?
- [ ] CHK007 L'intégration ServicePdf est-elle spécifiée (si documents) ?
- [ ] CHK008 Les comportements gestion erreurs sont-ils définis pour échecs Service ?
- [ ] CHK009 Les stratégies retry/fallback sont-elles spécifiées ?
- [ ] CHK010 La coordination transaction est-elle spécifiée entre Services ?
```

## Entrée Utilisateur

```text
$ARGUMENTS
```

Vous **DEVEZ** prendre en compte l'entrée utilisateur avant de procéder (si non vide).

## Étapes d'Exécution

1. **Setup** : Exécuter `.specify/scripts/powershell/check-prerequisites.ps1 -Json` depuis racine repo et parser JSON pour FEATURE_DIR et liste AVAILABLE_DOCS.
   - Tous les chemins fichiers doivent être absolus.
   - Pour apostrophes dans args comme "J'évalue", utiliser syntaxe échappement : ex 'J'\''évalue' (ou guillemets si possible : "J'évalue").

2. **Clarifier intention (dynamique)** : Dériver jusqu'à TROIS questions clarification contextuelles initiales (pas de catalogue pré-fabriqué). Elles DOIVENT :
   - Être générées depuis la formulation utilisateur + signaux extraits de spec/plan/tasks
   - Ne demander que les informations qui changent matériellement le contenu checklist
   - Être sautées individuellement si déjà non ambiguës dans `$ARGUMENTS`
   - Préférer précision sur étendue

   Algorithme génération :
   1. Extraire signaux : mots-clés domaine fonctionnalité (ex: auth, latence, UX, API), indicateurs risque ("critique", "doit", "conformité"), indices partie prenante ("QA", "revue", "équipe sécurité"), et livrables explicites ("a11y", "rollback", "contrats").
   2. Regrouper signaux en domaines focus candidats (max 4) classés par pertinence.
   3. Identifier audience probable & timing (auteur, relecteur, QA, release) si non explicite.
   4. Détecter dimensions manquantes : étendue périmètre, profondeur/rigueur, emphase risque, limites exclusion, critères acceptation mesurables.
   5. Formuler questions choisies parmi ces archétypes :
      - Raffinement périmètre (ex: « Ceci doit-il inclure points contact intégration avec X et Y ou rester limité à correction module local ? »)
      - Priorisation risque (ex: « Lesquels de ces domaines risque potentiels doivent recevoir vérifications gate obligatoires ? »)
      - Calibration profondeur (ex: « Est-ce une liste légère sanity pré-commit ou gate release formelle ? »)
      - Cadrage audience (ex: « Sera utilisé par l'auteur seul ou pairs pendant revue PR ? »)
      - Exclusion limite (ex: « Devons-nous explicitement exclure éléments tuning performance ce tour ? »)
      - Lacune classe scénario (ex: « Pas de flows recovery détectés—les chemins rollback / échec partiel sont-ils en scope ? »)

   Règles formatage questions :
   - Si présentant options, générer table compacte avec colonnes : Option | Candidat | Pourquoi Ça Compte
   - Limiter à options A–E maximum ; omettre table si réponse libre plus claire
   - Ne jamais demander à l'utilisateur de reformuler ce qu'il a déjà dit
   - Éviter catégories spéculatives (pas d'hallucination). Si incertain, demander explicitement : « Confirmer si X appartient au scope. »

   Défauts quand interaction impossible :
   - Profondeur : Standard
   - Audience : Relecteur (PR) si lié code ; Auteur sinon
   - Focus : Top 2 clusters pertinence

   Produire les questions (libeller Q1/Q2/Q3). Après réponses : si ≥2 classes scénario (Alternatif / Exception / Recovery / domaine Non-Fonctionnel) restent floues, vous POUVEZ poser jusqu'à DEUX suivis ciblés supplémentaires (Q4/Q5) avec justification une ligne chacun (ex: « Risque chemin recovery non résolu »). Ne pas dépasser cinq questions total. Sauter escalade si utilisateur décline explicitement plus.

3. **Comprendre requête utilisateur** : Combiner `$ARGUMENTS` + réponses clarification :
   - Dériver thème checklist (ex: sécurité, revue, déploiement, ux)
   - Consolider éléments must-have explicites mentionnés par utilisateur
   - Mapper sélections focus au scaffolding catégorie
   - Inférer contexte manquant depuis spec/plan/tasks (NE PAS halluciner)

4. **Charger contexte fonctionnalité** : Lire depuis FEATURE_DIR :
   - spec.md : Exigences et périmètre fonctionnalité
   - plan.md (si existe) : Détails techniques, dépendances
   - tasks.md (si existe) : Tâches implémentation

   **Stratégie Chargement Contexte** :
   - Charger uniquement portions nécessaires pertinentes aux domaines focus actifs (éviter déversement fichier complet)
   - Préférer résumer longues sections en bullets scénario/exigence concis
   - Utiliser divulgation progressive : ajouter récupération suivi uniquement si lacunes détectées
   - Si docs source volumineux, générer éléments résumé intermédiaires au lieu d'incorporer texte brut

5. **Générer checklist** - Créer « Tests Unitaires pour Exigences » :
   - Créer répertoire `FEATURE_DIR/checklists/` s'il n'existe pas
   - Générer nom fichier checklist unique :
     - Utiliser nom court, descriptif basé sur domaine (ex: `ux.md`, `api.md`, `security.md`)
     - Format : `[domaine].md`
     - Si fichier existe, ajouter au fichier existant
   - Numéroter éléments séquentiellement depuis CHK001
   - Chaque exécution `/speckit.checklist` crée un NOUVEAU fichier (n'écrase jamais checklists existantes)

   **PRINCIPE CENTRAL - Tester les Exigences, Pas l'Implémentation** :
   Chaque élément checklist DOIT évaluer les EXIGENCES ELLES-MÊMES pour :
   - **Complétude** : Toutes les exigences nécessaires sont-elles présentes ?
   - **Clarté** : Les exigences sont-elles non ambiguës et spécifiques ?
   - **Cohérence** : Les exigences s'alignent-elles entre elles ?
   - **Mesurabilité** : Les exigences peuvent-elles être objectivement vérifiées ?
   - **Couverture** : Tous les scénarios/cas limites sont-ils traités ?

   **Structure Catégorie** - Grouper éléments par dimensions qualité exigences :
   - **Complétude Exigences** (Toutes les exigences nécessaires sont-elles documentées ?)
   - **Clarté Exigences** (Les exigences sont-elles spécifiques et non ambiguës ?)
   - **Cohérence Exigences** (Les exigences s'alignent-elles sans conflits ?)
   - **Qualité Critères Acceptation** (Les critères succès sont-ils mesurables ?)
   - **Couverture Scénarios** (Tous les flux/cas sont-ils traités ?)
   - **Couverture Cas Limites** (Les conditions frontière sont-elles définies ?)
   - **Exigences Non-Fonctionnelles** (Performance, Sécurité, Accessibilité, etc. - sont-elles spécifiées ?)
   - **Dépendances & Hypothèses** (Sont-elles documentées et validées ?)
   - **Ambiguïtés & Conflits** (Que faut-il clarifier ?)

   **COMMENT RÉDIGER ÉLÉMENTS CHECKLIST - « Tests Unitaires pour le Français »** :

   ❌ **FAUX** (Tester implémentation) :
   - « Vérifier que la page d'accueil affiche 3 cartes épisodes »
   - « Tester que les états hover fonctionnent sur desktop »
   - « Confirmer que le clic logo navigue vers accueil »

   ✅ **CORRECT** (Tester qualité exigences) :
   - « Le nombre exact et la disposition des épisodes vedettes sont-ils spécifiés ? » [Complétude]
   - « 'Affichage proéminent' est-il quantifié avec dimensionnement/positionnement spécifique ? » [Clarté]
   - « Les exigences état hover sont-elles cohérentes pour tous les éléments interactifs ? » [Cohérence]
   - « Les exigences navigation clavier sont-elles définies pour toute UI interactive ? » [Couverture]
   - « Le comportement fallback est-il spécifié quand l'image logo échoue à charger ? » [Cas Limites]
   - « Les états chargement sont-ils définis pour données épisode asynchrones ? » [Complétude]
   - « La spec définit-elle la hiérarchie visuelle pour éléments UI concurrents ? » [Clarté]

   **STRUCTURE ÉLÉMENT** :
   Chaque élément doit suivre ce pattern :
   - Format question demandant sur qualité exigence
   - Focus sur ce qui est ÉCRIT (ou non écrit) dans spec/plan
   - Inclure dimension qualité entre crochets [Complétude/Clarté/Cohérence/etc.]
   - Référencer section spec `[Spec §X.Y]` quand vérifiant exigences existantes
   - Utiliser marqueur `[Lacune]` quand vérifiant exigences manquantes

   **EXEMPLES PAR DIMENSION QUALITÉ** :

   Complétude :
   - « Les exigences gestion erreurs sont-elles définies pour tous les modes échec API ? [Lacune] »
   - « Les exigences accessibilité sont-elles spécifiées pour tous les éléments interactifs ? [Complétude] »
   - « Les exigences breakpoint mobile sont-elles définies pour layouts responsifs ? [Lacune] »

   Clarté :
   - « 'Chargement rapide' est-il quantifié avec seuils timing spécifiques ? [Clarté, Spec §ENF-2] »
   - « Les critères sélection 'épisodes liés' sont-ils explicitement définis ? [Clarté, Spec §EF-5] »
   - « 'Proéminent' est-il défini avec propriétés visuelles mesurables ? [Ambiguïté, Spec §EF-4] »

   Cohérence :
   - « Les exigences navigation s'alignent-elles sur toutes les pages ? [Cohérence, Spec §EF-10] »
   - « Les exigences composant carte sont-elles cohérentes entre pages accueil et détail ? [Cohérence] »

   Couverture :
   - « Les exigences sont-elles définies pour scénarios état zéro (pas d'épisodes) ? [Couverture, Cas Limite] »
   - « Les scénarios interaction utilisateur concurrent sont-ils traités ? [Couverture, Lacune] »
   - « Les exigences sont-elles spécifiées pour échecs chargement données partiel ? [Couverture, Flux Exception] »

   Mesurabilité :
   - « Les exigences hiérarchie visuelle sont-elles mesurables/testables ? [Critères Acceptation, Spec §EF-1] »
   - « 'Poids visuel équilibré' peut-il être objectivement vérifié ? [Mesurabilité, Spec §EF-2] »

   **Classification & Couverture Scénarios** (Focus Qualité Exigences) :
   - Vérifier si exigences existent pour : scénarios Primaire, Alternatif, Exception/Erreur, Recovery, Non-Fonctionnel
   - Pour chaque classe scénario, demander : « Les exigences [type scénario] sont-elles complètes, claires et cohérentes ? »
   - Si classe scénario manquante : « Les exigences [type scénario] sont-elles intentionnellement exclues ou manquantes ? [Lacune] »
   - Inclure résilience/rollback quand mutation état se produit : « Les exigences rollback sont-elles définies pour échecs migration ? [Lacune] »

   **Exigences Traçabilité** :
   - MINIMUM : ≥80% des éléments DOIVENT inclure au moins une référence traçabilité
   - Chaque élément doit référencer : section spec `[Spec §X.Y]`, ou utiliser marqueurs : `[Lacune]`, `[Ambiguïté]`, `[Conflit]`, `[Hypothèse]`
   - Si pas de système ID existe : « Un schéma ID exigence & critères acceptation est-il établi ? [Traçabilité] »

   **Faire Remonter & Résoudre Problèmes** (Problèmes Qualité Exigences) :
   Poser questions sur les exigences elles-mêmes :
   - Ambiguïtés : « Le terme 'rapide' est-il quantifié avec métriques spécifiques ? [Ambiguïté, Spec §ENF-1] »
   - Conflits : « Les exigences navigation conflictent-elles entre §EF-10 et §EF-10a ? [Conflit] »
   - Hypothèses : « L'hypothèse 'API podcast toujours disponible' est-elle validée ? [Hypothèse] »
   - Dépendances : « Les exigences API podcast externe sont-elles documentées ? [Dépendance, Lacune] »
   - Définitions manquantes : « 'Hiérarchie visuelle' est-elle définie avec critères mesurables ? [Lacune] »

   **Consolidation Contenu** :
   - Plafond souple : Si éléments candidats bruts > 40, prioriser par risque/impact
   - Fusionner quasi-doublons vérifiant même aspect exigence
   - Si >5 cas limites faible impact, créer un élément : « Les cas limites X, Y, Z sont-ils traités dans les exigences ? [Couverture] »

   **🚫 ABSOLUMENT INTERDIT** - Ceux-ci en font un test implémentation, pas un test exigences :
   - ❌ Tout élément commençant par « Vérifier », « Tester », « Confirmer », « Contrôler » + comportement implémentation
   - ❌ Références à exécution code, actions utilisateur, comportement système
   - ❌ « S'affiche correctement », « fonctionne correctement », « fonctionne comme attendu »
   - ❌ « Cliquer », « naviguer », « rendre », « charger », « exécuter »
   - ❌ Cas de test, plans de test, procédures QA
   - ❌ Détails implémentation (frameworks, APIs, algorithmes)

   **✅ PATTERNS REQUIS** - Ceux-ci testent qualité exigences :
   - ✅ « Les [type exigence] sont-elles définies/spécifiées/documentées pour [scénario] ? »
   - ✅ « [Terme vague] est-il quantifié/clarifié avec critères spécifiques ? »
   - ✅ « Les exigences sont-elles cohérentes entre [section A] et [section B] ? »
   - ✅ « [Exigence] peut-elle être objectivement mesurée/vérifiée ? »
   - ✅ « Les [cas limites/scénarios] sont-ils traités dans les exigences ? »
   - ✅ « La spec définit-elle [aspect manquant] ? »

6. **Référence Structure** : Générer la checklist suivant le template canonique dans `.specify/templates/checklist-template.md` pour titre, section méta, en-têtes catégorie, et formatage ID. Si template indisponible, utiliser : titre H1, lignes méta objectif/créé, sections catégorie `##` contenant lignes `- [ ] CHK### <élément exigence>` avec IDs incrémentant globalement depuis CHK001.

7. **Rapport** : Produire chemin complet vers checklist créée, nombre éléments, et rappeler utilisateur que chaque exécution crée nouveau fichier. Résumer :
   - Domaines focus sélectionnés
   - Niveau profondeur
   - Acteur/timing
   - Tous éléments must-have spécifiés par utilisateur incorporés

**Important** : Chaque invocation commande `/speckit.checklist` crée fichier checklist utilisant noms courts, descriptifs sauf si fichier existe déjà. Ceci permet :

- Checklists multiples de types différents (ex: `ux.md`, `test.md`, `security.md`)
- Noms fichiers simples, mémorables indiquant objectif checklist
- Identification et navigation faciles dans dossier `checklists/`

Pour éviter encombrement, utiliser types descriptifs et nettoyer checklists obsolètes une fois terminé.

## Types Exemples Checklist & Éléments Exemples

**Qualité Exigences UX :** `ux.md`

Éléments exemples (testant les exigences, PAS l'implémentation) :

- « Les exigences hiérarchie visuelle sont-elles définies avec critères mesurables ? [Clarté, Spec §EF-1] »
- « Le nombre et positionnement des éléments UI sont-ils explicitement spécifiés ? [Complétude, Spec §EF-1] »
- « Les exigences état interaction (hover, focus, active) sont-elles définies de manière cohérente ? [Cohérence] »
- « Les exigences accessibilité sont-elles spécifiées pour tous les éléments interactifs ? [Couverture, Lacune] »
- « Le comportement fallback est-il défini quand les images échouent à charger ? [Cas Limite, Lacune] »
- « 'Affichage proéminent' peut-il être objectivement mesuré ? [Mesurabilité, Spec §EF-4] »

**Qualité Exigences API :** `api.md`

Éléments exemples :

- « Les formats réponse erreur sont-ils spécifiés pour tous les scénarios échec ? [Complétude] »
- « Les exigences limitation débit sont-elles quantifiées avec seuils spécifiques ? [Clarté] »
- « Les exigences authentification sont-elles cohérentes sur tous les endpoints ? [Cohérence] »
- « Les exigences retry/timeout sont-elles définies pour dépendances externes ? [Couverture, Lacune] »
- « La stratégie versioning est-elle documentée dans les exigences ? [Lacune] »

**Qualité Exigences Performance :** `performance.md`

Éléments exemples :

- « Les exigences performance sont-elles quantifiées avec métriques spécifiques ? [Clarté] »
- « Les cibles performance sont-elles définies pour tous les parcours utilisateur critiques ? [Couverture] »
- « Les exigences performance sous différentes conditions charge sont-elles spécifiées ? [Complétude] »
- « Les exigences performance peuvent-elles être objectivement mesurées ? [Mesurabilité] »
- « Les exigences dégradation sont-elles définies pour scénarios forte charge ? [Cas Limite, Lacune] »

**Qualité Exigences Sécurité :** `security.md`

Éléments exemples :

- « Les exigences authentification sont-elles spécifiées pour toutes les ressources protégées ? [Couverture] »
- « Les exigences protection données sont-elles définies pour informations sensibles ? [Complétude] »
- « Le modèle menace est-il documenté et les exigences alignées dessus ? [Traçabilité] »
- « Les exigences sécurité sont-elles cohérentes avec obligations conformité ? [Cohérence] »
- « Les exigences réponse échec/violation sécurité sont-elles définies ? [Lacune, Flux Exception] »

## Anti-Exemples : Ce Qu'il Ne Faut PAS Faire

**❌ FAUX - Ceux-ci testent implémentation, pas exigences :**

```markdown
- [ ] CHK001 - Vérifier que la page d'accueil affiche 3 cartes épisodes [Spec §EF-001]
- [ ] CHK002 - Tester que les états hover fonctionnent correctement sur desktop [Spec §EF-003]
- [ ] CHK003 - Confirmer que le clic logo navigue vers la page d'accueil [Spec §EF-010]
- [ ] CHK004 - Contrôler que la section épisodes liés montre 3-5 éléments [Spec §EF-005]
```

**✅ CORRECT - Ceux-ci testent qualité exigences :**

```markdown
- [ ] CHK001 - Le nombre et la disposition des épisodes vedettes sont-ils explicitement spécifiés ? [Complétude, Spec §EF-001]
- [ ] CHK002 - Les exigences état hover sont-elles définies de manière cohérente pour tous les éléments interactifs ? [Cohérence, Spec §EF-003]
- [ ] CHK003 - Les exigences navigation sont-elles claires pour tous les éléments marque cliquables ? [Clarté, Spec §EF-010]
- [ ] CHK004 - Les critères de sélection pour épisodes liés sont-ils documentés ? [Lacune, Spec §EF-005]
- [ ] CHK005 - Les exigences état chargement sont-elles définies pour données épisode asynchrones ? [Lacune]
- [ ] CHK006 - Les exigences « hiérarchie visuelle » peuvent-elles être objectivement mesurées ? [Mesurabilité, Spec §EF-001]
```

**Différences Clés :**

- Faux : Teste si le système fonctionne correctement
- Correct : Teste si les exigences sont écrites correctement
- Faux : Vérification du comportement
- Correct : Validation de la qualité des exigences
- Faux : « Est-ce que ça fait X ? »
- Correct : « X est-il clairement spécifié ? »
