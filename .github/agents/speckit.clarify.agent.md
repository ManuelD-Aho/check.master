---
description: Identifier les zones sous-spécifiées dans la spec fonctionnalité CheckMaster en posant jusqu'à 5 questions de clarification ciblées et en encodant les réponses dans la spec.
handoffs: 
  - label: Créer Plan Technique
    agent: speckit.plan
    prompt: Créer un plan pour la spec. Je construis avec CheckMaster (PHP 8.0+ MVC++, MySQL, DB-Driven)
---

## Entrée Utilisateur

```text
$ARGUMENTS
```

Vous **DEVEZ** prendre en compte l'entrée utilisateur avant de procéder (si non vide).

## Contexte Domaine CheckMaster pour Clarifications

Lors de la génération de questions clarification pour fonctionnalités CheckMaster, prioriser ces aspects spécifiques au domaine :

### 1. Questions Intégration Workflow

Si la fonctionnalité touche le cycle de vie candidature/rapport/soutenance :

**Questions Critiques** :
- « Dans quel état workflow cette fonctionnalité opère-t-elle ? »
  - Options : INSCRIT, CANDIDATURE_SOUMISE, VERIFICATION_SCOLARITE, FILTRE_COMMUNICATION, EN_ATTENTE_COMMISSION, EN_EVALUATION_COMMISSION, RAPPORT_VALIDE, ATTENTE_AVIS_ENCADREUR, PRET_POUR_JURY, SOUTENANCE_PLANIFIEE, SOUTENANCE_EN_COURS, DIPLOME_DELIVRE
- « Quelle transition d'état cette action déclenche-t-elle ? »
  - Exemple : « Valider candidature » → CANDIDATURE_SOUMISE vers VERIFICATION_SCOLARITE
- « Cette fonctionnalité a-t-elle une condition gate (bloquante) ? »
  - Exemple : « Rédaction rapport bloquée jusqu'à candidature_validée »

### 2. Questions Permissions & Accès

Si la fonctionnalité implique des actions restreintes :

**Questions Critiques** :
- « Quels groupes utilisateurs peuvent effectuer cette action ? »
  - Options : Administrateur (5), Secrétaire (6), Communication (7), Scolarité (8), Resp. Filière (9), Resp. Niveau (10), Commission (11), Enseignant (12), Étudiant (13)
- « Est-ce un rôle permanent ou temporaire ? »
  - Exemple : Président Jury obtient accès temporaire uniquement le jour de la soutenance
- « Quel niveau de permission est requis ? »
  - Options : Consulter (7), Créer (2), Modifier (4), Supprimer (3), Exporter (5), Valider (6)

### 3. Questions Génération Documents

Si la fonctionnalité produit des documents PDF :

**Questions Critiques** :
- « Quel type de document est généré ? »
  - Options : Reçu paiement, Reçu pénalité, Bulletin notes, PV commission, PV soutenance, Convocation, Attestation diplôme, Rapport évaluation, Bulletin provisoire, Certificat scolarité, etc.
- « Le document nécessite-t-il CSS/layout complexe (mPDF) ou formatage simple (TCPDF) ? »
  - Simple : Reçus, bulletins simples → TCPDF
  - Complexe : PV sessions, rapports multi-sections → mPDF
- « Qui reçoit la notification de téléchargement ? »
  - Options : Étudiant, Enseignant, Membres jury, Admin, Rôles multiples

### 4. Questions Stratégie Notification

Si la fonctionnalité déclenche des communications :

**Questions Critiques** :
- « À quel(s) événement(s) les notifications doivent-elles être envoyées ? »
  - Exemple : Sur validation, rejet, rappel, échéance approchant
- « Quels canaux de notification sont requis ? »
  - Options : Email uniquement, Email + Messagerie interne, Email + SMS (urgent)
- « Qui sont les destinataires ? »
  - Mapper aux rôles utilisateur : Étudiant, Encadreur, Membres commission, Président, Admin

### 5. Questions Opérations Financières

Si la fonctionnalité implique des paiements :

**Questions Critiques** :
- « Qu'est-ce qui déclenche la transaction financière ? »
  - Options : Validation inscription, Enregistrement paiement, Calcul pénalité, Application exonération
- « Quel calcul de montant s'applique ? »
  - Exemple : « Montant fixe depuis configuration », « Pourcentage du montant de base », « Échelonné selon délai »
- « La génération de reçu est-elle automatique ? »
  - Options : Génération immédiate, Déclenchement manuel par admin, Batch en fin de journée

### 6. Questions Commission/Vote

Si la fonctionnalité implique une évaluation commission :

**Questions Critiques** :
- « Quel mécanisme de vote s'applique ? »
  - Options : Unanimité requise (3 tours max), Majorité (simple >50%), Majorité qualifiée (>66%)
- « Que se passe-t-il après le tour 3 sans consensus ? »
  - Options : Escalade au Doyen, Escalade au Directeur, Rejet automatique, Intervention manuelle
- « Qui vote ? »
  - Options : Tous les membres commission, Évaluateurs assignés uniquement, Président décide

### 7. Questions Entités Données

Si la fonctionnalité implique de nouvelles entités :

**Questions Critiques** :
- « Quel est l'identifiant primaire ? »
  - Exemple : « num_etu pour étudiants (VARCHAR 20, unique, non-autogénéré) »
- « Quelles sont les relations ? »
  - Exemple : « Une candidature par étudiant par an », « Plusieurs annotations par rapport »
- « Quel niveau d'audit est requis ? »
  - Options : Snapshots complets (données critiques), Journalisation minimale (données référence), Pas d'audit (données temporaires)

### 8. Questions Configuration

Si la fonctionnalité ajoute de nouveaux paramètres :

**Questions Critiques** :
- « Quel préfixe de configuration s'applique ? »
  - Options : workflow.*, notify.*, finance.*, security.*, jury.*, commission.*, etc.
- « Cette fonctionnalité est-elle désactivable ? »
  - Exemple : « L'admin peut-il désactiver le mécanisme d'escalade via configuration ? »
- « Quelles sont les valeurs par défaut ? »
  - Doit spécifier pour chaque paramètre config

### 9. Questions Intégration

Si la fonctionnalité se connecte à des systèmes existants :

**Questions Critiques** :
- « De quel(s) Service(s) existant(s) cela dépend-il ? »
  - Options : ServiceWorkflow, ServiceNotification, ServicePermission, ServiceAudit, ServicePdf, ServiceParametres
- « Cela nécessite-t-il la création d'un nouveau Service ? »
  - Quand : Logique domaine complexe, réutilisable entre fonctionnalités, implique plusieurs modèles
- « Que se passe-t-il en cas d'échec service ? »
  - Exemple : « Si ServiceNotification échoue, retry 3 fois puis journaliser erreur »

### 10. Questions Tests & Validation

Si les critères d'acceptation sont vagues :

**Questions Critiques** :
- « Quel est le critère de succès mesurable ? »
  - Exemple : « Rapport soumis » → « Rapport visible dans file Communication en moins de 5 secondes »
- « Quels sont les scénarios d'erreur ? »
  - Exemple : « Candidature invalide rejetée avec messages erreur spécifiques pour chaque règle validation »
- « Quel est le volume de données attendu ? »
  - Exemple : « Support 500 étudiants par promotion, 100 utilisateurs simultanés »

## Aperçu

Objectif : Détecter et réduire l'ambiguïté ou les points de décision manquants dans la spécification fonctionnalité active et enregistrer les clarifications directement dans le fichier spec.

Note : Ce workflow de clarification est prévu pour s'exécuter (et être complété) AVANT d'invoquer `/speckit.plan`. Si l'utilisateur déclare explicitement qu'il saute la clarification (ex : spike exploratoire), vous pouvez procéder, mais devez avertir que le risque de retravail en aval augmente.

Étapes d'exécution :

1. Exécuter `.specify/scripts/powershell/check-prerequisites.ps1 -Json -PathsOnly` depuis racine repo **une fois** (mode combiné `--json --paths-only` / `-Json -PathsOnly`). Parser payload JSON minimal :
   - `FEATURE_DIR`
   - `FEATURE_SPEC`
   - (Optionnellement capturer `IMPL_PLAN`, `TASKS` pour flux chaînés futurs.)
   - Si parsing JSON échoue, abandonner et instruire utilisateur à ré-exécuter `/speckit.specify` ou vérifier environnement branche fonctionnalité.
   - Pour apostrophes dans args comme "J'écris", utiliser syntaxe échappement : ex 'J'\''écris' (ou guillemets si possible : "J'écris").

2. Charger le fichier spec actuel. Effectuer un scan structuré ambiguïté & couverture utilisant cette taxonomie. Pour chaque catégorie, marquer statut : Clair / Partiel / Manquant. Produire une carte couverture interne utilisée pour priorisation (ne pas produire carte brute sauf si aucune question ne sera posée).

   Périmètre & Comportement Fonctionnel :
   - Objectifs utilisateur principaux & critères succès
   - Déclarations hors-périmètre explicites
   - Différenciation rôles/personas utilisateurs

   Domaine & Modèle Données :
   - Entités, attributs, relations
   - Règles identité & unicité
   - Transitions cycle de vie/état
   - Hypothèses volume/échelle données

   Interaction & Flux UX :
   - Parcours/séquences utilisateur critiques
   - États erreur/vide/chargement
   - Notes accessibilité ou localisation

   Attributs Qualité Non-Fonctionnels :
   - Performance (cibles latence, débit)
   - Scalabilité (horizontale/verticale, limites)
   - Fiabilité & disponibilité (uptime, attentes récupération)
   - Observabilité (signaux logging, métriques, tracing)
   - Sécurité & confidentialité (authN/Z, protection données, hypothèses menace)
   - Contraintes conformité / réglementaires (si présentes)

   Intégration & Dépendances Externes :
   - Services/APIs externes et modes échec
   - Formats import/export données
   - Hypothèses protocole/versioning

   Cas Limites & Gestion Échecs :
   - Scénarios négatifs
   - Limitation de débit / throttling
   - Résolution conflits (ex : éditions concurrentes)

   Contraintes & Compromis :
   - Contraintes techniques (langage, stockage, hébergement)
   - Compromis explicites ou alternatives rejetées

   Terminologie & Cohérence :
   - Termes glossaire canoniques
   - Synonymes évités / termes dépréciés

   Signaux Complétion :
   - Testabilité critères acceptation
   - Indicateurs style Definition of Done mesurables

   Divers / Placeholders :
   - Marqueurs TODO / décisions non résolues
   - Adjectifs ambigus ("robuste", "intuitif") manquant quantification

   Pour chaque catégorie avec statut Partiel ou Manquant, ajouter une opportunité question candidate sauf si :
   - La clarification ne changerait pas matériellement la stratégie implémentation ou validation
   - L'information est mieux différée à la phase planning (noter en interne)

3. Générer (en interne) une file priorisée de questions clarification candidates (maximum 5). NE PAS les produire toutes d'un coup. Appliquer ces contraintes :
    - Maximum 10 questions totales sur toute la session.
    - Chaque question doit être répondable avec SOIT :
       - Une courte sélection choix multiples (2-5 options distinctes, mutuellement exclusives), OU
       - Une réponse un-mot / phrase-courte (contraindre explicitement : "Répondre en <=5 mots").
    - N'inclure que les questions dont les réponses impactent matériellement architecture, modélisation données, décomposition tâches, conception tests, comportement UX, préparation opérationnelle, ou validation conformité.
    - Assurer équilibre couverture catégories : tenter de couvrir d'abord les catégories non résolues à plus fort impact ; éviter de poser deux questions faible impact quand un domaine fort impact (ex : posture sécurité) est non résolu.
    - Exclure questions déjà répondues, préférences stylistiques triviales, ou détails exécution niveau plan (sauf si bloquant pour correction).
    - Favoriser clarifications qui réduisent risque retravail aval ou préviennent tests acceptation mal alignés.
    - Si plus de 5 catégories restent non résolues, sélectionner top 5 par heuristique (Impact * Incertitude).

4. Boucle questionnement séquentielle (interactive) :
    - Présenter EXACTEMENT UNE question à la fois.
    - Pour questions choix multiples :
       - **Analyser toutes les options** et déterminer l'**option la plus adaptée** basée sur :
          - Meilleures pratiques pour le type de projet
          - Patterns courants dans implémentations similaires
          - Réduction risque (sécurité, performance, maintenabilité)
          - Alignement avec objectifs ou contraintes projet explicites visibles dans spec
       - Présenter votre **option recommandée de manière proéminente** en haut avec raisonnement clair (1-2 phrases expliquant pourquoi c'est le meilleur choix).
       - Formater ainsi : `**Recommandé :** Option [X] - <raisonnement>`
       - Puis rendre toutes les options en table Markdown :

       | Option | Description |
       |--------|-------------|
       | A | <Description Option A> |
       | B | <Description Option B> |
       | C | <Description Option C> (ajouter D/E si nécessaire jusqu'à 5) |
       | Libre | Fournir une réponse différente (<=5 mots) (Inclure seulement si alternative libre appropriée) |

       - Après la table, ajouter : `Vous pouvez répondre avec la lettre option (ex : "A"), accepter la recommandation en disant "oui" ou "recommandé", ou fournir votre propre réponse courte.`
    - Pour style réponse-courte (pas d'options discrètes significatives) :
       - Fournir votre **réponse suggérée** basée sur meilleures pratiques et contexte.
       - Formater ainsi : `**Suggéré :** <votre réponse proposée> - <raisonnement bref>`
       - Puis produire : `Format : Réponse courte (<=5 mots). Vous pouvez accepter la suggestion en disant "oui" ou "suggéré", ou fournir votre propre réponse.`
    - Après réponse utilisateur :
       - Si utilisateur répond "oui", "recommandé", ou "suggéré", utiliser votre recommandation/suggestion précédemment énoncée comme réponse.
       - Sinon, valider que la réponse mappe à une option ou respecte la contrainte <=5 mots.
       - Si ambigu, demander clarification rapide (compte appartient toujours à même question ; ne pas avancer).
       - Une fois satisfaisant, enregistrer en mémoire de travail (ne pas encore écrire sur disque) et passer à la question suivante en file.
    - Arrêter de poser d'autres questions quand :
       - Toutes les ambiguïtés critiques résolues tôt (éléments restants en file deviennent inutiles), OU
       - Utilisateur signale complétion ("terminé", "c'est bon", "plus de questions"), OU
       - Vous atteignez 5 questions posées.
    - Ne jamais révéler les questions futures en file à l'avance.
    - Si aucune question valide n'existe au départ, rapporter immédiatement aucune ambiguïté critique.

5. Intégration après CHAQUE réponse acceptée (approche mise à jour incrémentale) :
    - Maintenir représentation en mémoire de la spec (chargée une fois au départ) plus le contenu fichier brut.
    - Pour la première réponse intégrée de cette session :
       - S'assurer qu'une section `## Clarifications` existe (la créer juste après la section contextuelle/aperçu de plus haut niveau selon template spec si manquante).
       - Dessous, créer (si non présent) un sous-titre `### Session AAAA-MM-JJ` pour aujourd'hui.
    - Ajouter une ligne puce immédiatement après acceptation : `- Q : <question> → R : <réponse finale>`.
    - Puis appliquer immédiatement la clarification à la section(s) la plus appropriée :
       - Ambiguïté fonctionnelle → Mettre à jour ou ajouter une puce dans Exigences Fonctionnelles.
       - Interaction utilisateur / distinction acteur → Mettre à jour User Stories ou sous-section Acteurs (si présente) avec rôle, contrainte ou scénario clarifié.
       - Forme données / entités → Mettre à jour Modèle Données (ajouter champs, types, relations) en préservant ordre ; noter contraintes ajoutées succinctement.
       - Contrainte non-fonctionnelle → Ajouter/modifier critères mesurables dans section Non-Fonctionnel / Attributs Qualité (convertir adjectif vague en métrique ou cible explicite).
       - Cas limite / flux négatif → Ajouter nouvelle puce sous Cas Limites / Gestion Erreurs (ou créer telle sous-section si template fournit placeholder).
       - Conflit terminologie → Normaliser terme à travers spec ; conserver original seulement si nécessaire en ajoutant `(anciennement appelé "X")` une fois.
    - Si la clarification invalide une déclaration antérieure ambiguë, remplacer cette déclaration au lieu de dupliquer ; ne laisser aucun texte contradictoire obsolète.
    - Sauvegarder le fichier spec APRÈS chaque intégration pour minimiser risque de perte contexte (écrasement atomique).
    - Préserver formatage : ne pas réordonner sections non liées ; garder hiérarchie titres intacte.
    - Garder chaque clarification insérée minimale et testable (éviter dérive narrative).

6. Validation (effectuée après CHAQUE écriture plus passe finale) :
   - Session clarifications contient exactement une puce par réponse acceptée (pas de doublons).
   - Total questions posées (acceptées) ≤ 5.
   - Sections mises à jour ne contiennent pas de placeholders vagues persistants que la nouvelle réponse devait résoudre.
   - Aucune déclaration antérieure contradictoire ne reste (scanner pour choix alternatifs maintenant invalides retirés).
   - Structure Markdown valide ; seuls nouveaux titres autorisés : `## Clarifications`, `### Session AAAA-MM-JJ`.
   - Cohérence terminologie : même terme canonique utilisé dans toutes les sections mises à jour.

7. Écrire la spec mise à jour dans `FEATURE_SPEC`.

8. Rapport complétion (après fin boucle questionnement ou terminaison anticipée) :
   - Nombre de questions posées & répondues.
   - Chemin vers spec mise à jour.
   - Sections touchées (lister noms).
   - Table résumé couverture listant chaque catégorie taxonomie avec Statut : Résolu (était Partiel/Manquant et adressé), Différé (dépasse quota questions ou mieux adapté au planning), Clair (déjà suffisant), En Attente (toujours Partiel/Manquant mais faible impact).
   - Si En Attente ou Différé restent, recommander si procéder à `/speckit.plan` ou exécuter `/speckit.clarify` à nouveau plus tard post-plan.
   - Commande suivante suggérée.

Règles comportement :

- Si aucune ambiguïté significative trouvée (ou toutes questions potentielles seraient faible impact), répondre : « Aucune ambiguïté critique détectée méritant clarification formelle. » et suggérer de procéder.
- Si fichier spec manquant, instruire utilisateur d'exécuter `/speckit.specify` d'abord (ne pas créer nouvelle spec ici).
- Ne jamais dépasser 5 questions totales posées (retries clarification pour une seule question ne comptent pas comme nouvelles questions).
- Éviter questions tech stack spéculatives sauf si l'absence bloque clarté fonctionnelle.
- Respecter signaux terminaison anticipée utilisateur ("stop", "terminé", "procéder").
- Si aucune question posée due à couverture complète, produire résumé couverture compact (toutes catégories Claires) puis suggérer d'avancer.
- Si quota atteint avec catégories fort impact non résolues restantes, les signaler explicitement sous Différé avec justification.

Contexte pour priorisation : $ARGUMENTS
