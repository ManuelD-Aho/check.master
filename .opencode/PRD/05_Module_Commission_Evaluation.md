# PRD Module 5 : Commission d'Évaluation

## 1. Vue d'ensemble

### 1.1 Objectif du module
Ce module gère l'évaluation des rapports de stage par la commission composée de 4 membres. Le vote doit être unanime pour qu'un rapport soit accepté. En cas de validation, un directeur de mémoire et un encadreur pédagogique sont assignés à l'étudiant.

### 1.2 Position dans le workflow global
```
Rapport Approuvé → COMMISSION (ce module) → Assignation Encadrants → Compte-Rendu (PV) → Soutenance
                        ↓
              [Vote unanime 4 membres]
```

### 1.3 Principe clé
> **RÈGLE FONDAMENTALE** : Les 4 membres de la commission doivent être unanimes pour valider un rapport. Un seul vote négatif entraîne un nouveau cycle de vote.

### 1.4 Bibliothèques utilisées
| Bibliothèque | Rôle dans ce module |
|--------------|---------------------|
| `symfony/workflow` | Machine à états du rapport en commission |
| `symfony/expression-language` | Règles de vote configurables |
| `doctrine/orm` | Gestion des entités évaluation, vote |
| `symfony/event-dispatcher` | Événements de vote, validation |
| `phpmailer/phpmailer` | Notifications email |
| `monolog/monolog` | Journalisation des votes |
| `white-october/pagerfanta` | Pagination des listes |
| `tecnickcom/tcpdf` | Génération du compte-rendu (PV) |
| `nesbot/carbon` | Gestion des sessions par mois/année |

---

## 2. Machine à états (Workflow)

### 2.1 États du rapport en commission

```
[en_attente_evaluation] ──evaluer──> [en_cours_evaluation] ──voter──> [vote_complet]
                                                                            │
                                   ┌────────────────────────────────────────┤
                                   │                                        │
                                   ▼                                        ▼
                           [vote_unanime_non]                       [vote_unanime_oui]
                                   │                                        │
                                   │                                        ▼
                                   │                              [assigner_encadrants]
                                   │                                        │
                                   │                                        ▼
                                   │                              [pret_pour_pv]
                                   │
                                   ▼
                           [retourne_etudiant]
```

| État | Code | Description |
|------|------|-------------|
| **En attente** | `en_attente_evaluation` | Rapport transféré, en attente d'évaluation |
| **En cours** | `en_cours_evaluation` | Au moins un membre a évalué |
| **Vote complet** | `vote_complet` | Les 4 membres ont voté |
| **Unanime OUI** | `vote_unanime_oui` | 4 votes positifs |
| **Unanime NON** | `vote_unanime_non` | 4 votes négatifs → retour étudiant |
| **Pas unanime** | `vote_non_unanime` | Votes mixtes → nouveau cycle |
| **Assignation** | `assigner_encadrants` | Assignation en cours |
| **Prêt PV** | `pret_pour_pv` | Encadrants assignés, prêt pour compte-rendu |
| **Retourné** | `retourne_etudiant` | Renvoyé pour correction |

### 2.2 Transitions

| Transition | De | Vers | Conditions |
|------------|-----|------|------------|
| `evaluer` | en_attente_evaluation | en_cours_evaluation | Premier membre évalue |
| `voter` | en_cours_evaluation | vote_complet | 4 évaluations reçues |
| `declarer_unanime_oui` | vote_complet | vote_unanime_oui | 4 votes = 'oui' |
| `declarer_unanime_non` | vote_complet | vote_unanime_non | 4 votes = 'non' |
| `declarer_non_unanime` | vote_complet | vote_non_unanime | Votes mixtes |
| `relancer_vote` | vote_non_unanime | en_attente_evaluation | Reset des votes |
| `assigner` | vote_unanime_oui | assigner_encadrants | Permission assignation |
| `finaliser_assignation` | assigner_encadrants | pret_pour_pv | Encadrants assignés |
| `retourner` | vote_unanime_non | retourne_etudiant | Notification étudiant |

---

## 3. Entités et Modèle de données

### 3.1 Schéma relationnel

```
rapport_etudiants (1) ──────< (N) evaluations_rapports
                                      │
                                      └──> utilisateur (evaluateur)

                     ──────< (N) affectation_encadrants
                                      │
                                      └──> enseignants (role)
```

### 3.2 Tables impliquées

#### `evaluations_rapports`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_evaluation` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_rapport` | INT FK | NOT NULL | Référence rapport |
| `id_evaluateur` | INT FK | NOT NULL | Membre de la commission |
| `numero_cycle` | INT | DEFAULT 1 | Cycle de vote (si reprise) |
| `decision_evaluation` | ENUM | NULL | 'oui', 'non', NULL (pas encore voté) |
| `commentaire` | TEXT | NULL | Commentaire/remarque |
| `note_qualite` | INT | NULL | Note indicative 1-5 (optionnel) |
| `points_forts` | TEXT | NULL | Points positifs identifiés |
| `points_ameliorer` | TEXT | NULL | Points à améliorer |
| `date_evaluation` | DATETIME | NULL | Date du vote |
| `date_creation` | DATETIME | NOT NULL | Date d'affectation |
| `date_modification` | DATETIME | NOT NULL | Dernière modification |

**Contrainte unique** : (id_rapport, id_evaluateur, numero_cycle)

#### `membres_commission`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_membre` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_utilisateur` | INT FK | NOT NULL | Référence utilisateur |
| `id_annee_academique` | INT FK | NOT NULL | Année académique |
| `role_commission` | ENUM | NOT NULL | 'president', 'membre' |
| `actif` | BOOLEAN | DEFAULT TRUE | Membre actif |
| `date_nomination` | DATE | NOT NULL | Date de nomination |
| `date_fin` | DATE | NULL | Date de fin (si applicable) |

**Contrainte unique** : (id_utilisateur, id_annee_academique)

#### `affectation_encadrants`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_affectation` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_rapport` | INT FK | NOT NULL | Référence rapport |
| `id_enseignant` | INT FK | NOT NULL | Enseignant affecté |
| `role_encadrement` | ENUM | NOT NULL | 'directeur_memoire', 'encadreur_pedagogique' |
| `date_affectation` | DATETIME | NOT NULL | Date d'affectation |
| `id_affecteur` | INT FK | NOT NULL | Qui a fait l'affectation |
| `commentaire` | TEXT | NULL | Note interne |

**Contrainte unique** : (id_rapport, role_encadrement)

#### `sessions_commission`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_session` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_annee_academique` | INT FK | NOT NULL | Année académique |
| `mois_session` | INT | NOT NULL | Mois (1-12) |
| `annee_session` | INT | NOT NULL | Année |
| `libelle_session` | VARCHAR(100) | NOT NULL | Ex: "Session Janvier 2025" |
| `date_debut` | DATE | NOT NULL | Début de la session |
| `date_fin` | DATE | NOT NULL | Fin de la session |
| `statut_session` | ENUM | NOT NULL | 'ouverte', 'fermee', 'archivee' |
| `date_creation` | DATETIME | NOT NULL | Date de création |

#### `compte_rendu` (PV Commission)
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_compte_rendu` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_session` | INT FK | NOT NULL | Session de la commission |
| `numero_pv` | VARCHAR(50) | NOT NULL, UNIQUE | Numéro du PV |
| `titre_pv` | VARCHAR(255) | NOT NULL | Titre du document |
| `contenu_html` | LONGTEXT | NOT NULL | Contenu édité |
| `chemin_fichier_pdf` | VARCHAR(255) | NULL | PDF généré |
| `statut_pv` | ENUM | NOT NULL | 'brouillon', 'finalise', 'envoye' |
| `date_creation` | DATETIME | NOT NULL | Date de création |
| `date_finalisation` | DATETIME | NULL | Date de finalisation |
| `id_createur` | INT FK | NOT NULL | Créateur du PV |

#### `compte_rendu_rapport` (Rapports inclus dans un PV)
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_compte_rendu` | INT FK | NOT NULL | Référence compte-rendu |
| `id_rapport` | INT FK | NOT NULL | Rapport inclus |
| `ordre` | INT | NOT NULL | Ordre dans le PV |
| `remarque_specifique` | TEXT | NULL | Remarque pour ce rapport |

---

## 4. Fonctionnalités détaillées

### 4.1 Gestion des membres de la commission

#### 4.1.1 Liste des membres
**Écran** : `/admin/commission/membres`

**Permission requise** : `COMMISSION_GERER`

**Colonnes** :
- Nom complet
- Fonction (enseignant/grade)
- Rôle commission (Président/Membre)
- Statut (Actif/Inactif)
- Date nomination
- Actions

**Actions** :
- Ajouter un membre
- Modifier le rôle
- Désactiver un membre

#### 4.1.2 Ajout d'un membre
**Écran** : `/admin/commission/membres/ajouter`

**Champs** :
| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| Utilisateur | Autocomplete | Oui | Recherche parmi enseignants |
| Rôle | Select | Oui | 'président', 'membre' |
| Date nomination | Date | Oui | Date d'effet |

**Règles** :
- Un seul président par année académique
- Un membre doit être de type "Enseignant"
- Minimum 4 membres actifs requis pour voter

### 4.2 Espace Commission - Évaluation des rapports

#### 4.2.1 Liste des rapports à évaluer
**Écran** : `/commission/rapports`

**Permission requise** : `COMMISSION_EVALUER` (membres uniquement)

**Onglets** :
1. **Mes évaluations en attente** : Rapports non encore évalués par moi
2. **En cours de vote** : Au moins 1 vote mais pas 4
3. **Vote complet** : 4 votes reçus
4. **Historique** : Rapports traités

**Colonnes (onglet "Mes évaluations")** :
| Colonne | Description |
|---------|-------------|
| Matricule | Matricule étudiant |
| Étudiant | Nom complet |
| Titre | Titre du rapport |
| Entreprise | Entreprise de stage |
| Transféré le | Date de transfert |
| Votes | X/4 (indicateur visuel) |
| Actions | Évaluer, Voir |

**Indicateurs visuels** :
- Badge rouge : Non évalué par moi
- Badge vert : Déjà évalué par moi
- Barre de progression : X/4 votes reçus

#### 4.2.2 Évaluation d'un rapport
**Écran** : `/commission/rapports/{id}/evaluer`

**Permission requise** : `COMMISSION_EVALUER`

**Interface** :
- Zone gauche : Visualisation du rapport (PDF intégré ou HTML)
- Zone droite : Formulaire d'évaluation

**Formulaire d'évaluation** :
| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| Décision | Radio | Oui | ○ Favorable (OUI) / ○ Défavorable (NON) |
| Note qualité | Slider | Non | 1 à 5 étoiles (indicatif) |
| Points forts | Textarea | Non | Éléments positifs |
| Points à améliorer | Textarea | Non | Éléments à revoir |
| Commentaire général | Textarea | Conditionnel | Obligatoire si NON |

**Actions** :
- "Soumettre mon évaluation" : Enregistre et verrouille
- "Télécharger le rapport" : PDF
- "Voir l'historique" : Si re-évaluation

**Processus** :
1. Vérification que l'évaluateur n'a pas déjà voté (cycle courant)
2. Enregistrement de l'évaluation
3. Mise à jour du compteur de votes
4. Si 4 votes atteints → déclenchement calcul unanimité
5. Notification des autres membres (progression)
6. Journalisation

#### 4.2.3 Tableau de bord du vote
**Écran** : `/commission/rapports/{id}/votes`

**Permission requise** : `COMMISSION_VOIR`

**Affichage** (visible par tous les membres après leur vote) :
```
╔═══════════════════════════════════════════════════════════════╗
║  📊 État du vote - [Titre du rapport]                        ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  Cycle de vote : #[N]                                        ║
║  Votes reçus : [X] / 4                                       ║
║                                                               ║
║  ┌─────────────────────────────────────────────────────────┐ ║
║  │ Membre 1 : ✅ Voté (OUI/NON visible après 4 votes)     │ ║
║  │ Membre 2 : ✅ Voté                                      │ ║
║  │ Membre 3 : ⏳ En attente                                │ ║
║  │ Membre 4 : ⏳ En attente                                │ ║
║  └─────────────────────────────────────────────────────────┘ ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
```

**Règle de confidentialité** :
- Avant 4 votes : On voit qui a voté mais pas la décision
- Après 4 votes : Décisions visibles + résultat global

### 4.3 Traitement des résultats de vote

#### 4.3.1 Calcul de l'unanimité
**Événement** : Déclenché quand le 4ème vote est enregistré

**Algorithme** :
```php
function determinerResultat(array $votes): string
{
    $countOui = 0;
    $countNon = 0;
    
    foreach ($votes as $vote) {
        if ($vote->getDecision() === 'oui') $countOui++;
        else $countNon++;
    }
    
    if ($countOui === 4) return 'unanime_oui';
    if ($countNon === 4) return 'unanime_non';
    return 'non_unanime';
}
```

#### 4.3.2 Cas : Vote unanime OUI (4 × OUI)
**Transition** : `vote_complet → vote_unanime_oui`

**Actions déclenchées** :
1. Notification email à l'étudiant (félicitations)
2. Notification au gestionnaire pour assignation encadrants
3. Le rapport passe à l'étape d'assignation
4. Journalisation

#### 4.3.3 Cas : Vote unanime NON (4 × NON)
**Transition** : `vote_complet → vote_unanime_non → retourne_etudiant`

**Actions déclenchées** :
1. Compilation des commentaires des 4 membres
2. Email à l'étudiant avec :
   - Décision : Rapport non accepté
   - Commentaires consolidés
   - Instructions pour correction
3. Déblocage de l'éditeur du rapport (Module 4)
4. Le rapport repasse en état "retourné"
5. Journalisation

**Email étudiant** :
```
Sujet : [Commission] Votre rapport nécessite des corrections

Bonjour [Prénom],

La commission d'évaluation a examiné votre rapport de stage.

Décision : Le rapport n'a pas été accepté en l'état.

Remarques de la commission :
[Commentaires consolidés]

Veuillez apporter les corrections nécessaires et soumettre à nouveau votre rapport.

Lien : [URL vers éditeur]
```

#### 4.3.4 Cas : Vote non unanime (mixte)
**Transition** : `vote_complet → vote_non_unanime`

**Écran** : `/commission/rapports/{id}/deliberation`

**Affichage** :
```
╔═══════════════════════════════════════════════════════════════╗
║  ⚠️ Vote non unanime - Délibération requise                  ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  Résultat : [X] OUI / [Y] NON                                ║
║                                                               ║
║  Détail des votes :                                          ║
║  ┌─────────────────────────────────────────────────────────┐ ║
║  │ [Membre 1] : OUI - "[Commentaire]"                      │ ║
║  │ [Membre 2] : NON - "[Commentaire]"                      │ ║
║  │ [Membre 3] : OUI - "[Commentaire]"                      │ ║
║  │ [Membre 4] : OUI - "[Commentaire]"                      │ ║
║  └─────────────────────────────────────────────────────────┘ ║
║                                                               ║
║  Action requise :                                            ║
║  Les membres doivent délibérer et soumettre un nouveau vote. ║
║                                                               ║
║  [Relancer le vote] (Président uniquement)                   ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
```

**Action "Relancer le vote"** (Président) :
1. Incrémentation du numéro de cycle
2. Reset des évaluations (nouveau cycle)
3. Notification aux 4 membres
4. Retour à l'état `en_attente_evaluation`

### 4.4 Assignation des encadrants

#### 4.4.1 Liste des rapports à assigner
**Écran** : `/admin/commission/assignation`

**Permission requise** : `ENCADRANT_ASSIGNER`

**Filtres** :
- Statut : "En attente d'assignation" par défaut
- Session de commission
- Promotion

**Colonnes** :
| Colonne | Description |
|---------|-------------|
| Matricule | Matricule étudiant |
| Étudiant | Nom complet |
| Titre rapport | Titre |
| Validé le | Date validation commission |
| Directeur | Assigné ou "Non assigné" |
| Encadreur | Assigné ou "Non assigné" |
| Actions | Assigner |

#### 4.4.2 Formulaire d'assignation
**Écran** : `/admin/commission/assignation/{id}`

**Champs** :
| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| Directeur de mémoire | Autocomplete | Oui | Recherche parmi enseignants |
| Encadreur pédagogique | Autocomplete | Oui | Recherche parmi membres commission |
| Commentaire | Textarea | Non | Note interne |

**Règles** :
| Code | Règle |
|------|-------|
| RG-ASS-001 | Le directeur et l'encadreur doivent être différents |
| RG-ASS-002 | L'encadreur pédagogique doit être membre de la commission |
| RG-ASS-003 | Les deux rôles doivent être assignés avant finalisation |

**Processus** :
1. Validation des règles
2. Création des entrées `affectation_encadrants`
3. Transition : `assigner_encadrants → pret_pour_pv`
4. Email aux enseignants assignés
5. Journalisation

**Email enseignant assigné** :
```
Sujet : [Assignation] Encadrement mémoire - [NOM Prénom étudiant]

Bonjour [Prénom enseignant],

Vous avez été désigné(e) comme [Directeur de mémoire / Encadreur pédagogique] 
pour l'étudiant(e) suivant(e) :

Étudiant : [NOM Prénom] - [Matricule]
Thème : [Titre du rapport]
Entreprise : [Raison sociale]

[Si encadreur pédagogique]
En tant qu'encadreur pédagogique, vous pourrez valider l'aptitude de l'étudiant
à soutenir lorsque son mémoire sera finalisé.

Cordialement,
La Direction
```

### 4.5 Génération du Compte-Rendu (PV)

#### 4.5.1 Création d'un compte-rendu
**Écran** : `/admin/commission/pv/nouveau`

**Permission requise** : `PV_CREER`

**Étape 1 : Sélection de la session**
| Champ | Type | Description |
|-------|------|-------------|
| Session | Select | Sessions avec rapports prêts |
| Titre | Text | Généré automatiquement, modifiable |

**Étape 2 : Sélection des rapports**
Liste des rapports en état `pret_pour_pv` pour la session :
- Cases à cocher pour sélection
- Ordre modifiable (drag & drop)

**Étape 3 : Édition du contenu**
Éditeur de texte pour le corps du PV avec sections pré-remplies :

```html
<h1>PROCÈS-VERBAL DE LA COMMISSION</h1>
<h2>Session [Mois] [Année]</h2>

<p>La commission d'évaluation des rapports de stage s'est réunie...</p>

<h3>Membres présents :</h3>
<ul>
  <li>[Président] - Président de la commission</li>
  <li>[Membre 2] - Membre</li>
  <li>[Membre 3] - Membre</li>
  <li>[Membre 4] - Membre</li>
</ul>

<h3>Rapports évalués :</h3>

<table>
  <thead>
    <tr>
      <th>N°</th>
      <th>Étudiant</th>
      <th>Thème</th>
      <th>Entreprise</th>
      <th>Décision</th>
      <th>Directeur</th>
      <th>Encadreur</th>
    </tr>
  </thead>
  <tbody>
    <!-- Généré automatiquement -->
  </tbody>
</table>

<h3>Remarques générales :</h3>
<p>[Zone éditable]</p>

<h3>Signatures</h3>
<p>Fait à [Ville], le [Date]</p>
```

#### 4.5.2 Finalisation et envoi
**Actions** :
1. **Prévisualiser** : Génération PDF temporaire
2. **Finaliser** : Verrouille le contenu, génère le PDF définitif
3. **Envoyer** : Email aux destinataires

**Destinataires de l'envoi** :
- Tous les étudiants dont le rapport figure dans le PV
- Les membres de la commission
- Les directeurs de mémoire assignés
- Les encadreurs pédagogiques assignés
- Administration (configurable)

**Email envoi PV** :
```
Sujet : [PV Commission] Compte-rendu [Session]

Bonjour,

Veuillez trouver ci-joint le compte-rendu de la commission d'évaluation 
pour la session [Session].

[Si étudiant]
Votre rapport a été évalué favorablement. 
Directeur de mémoire : [Nom]
Encadreur pédagogique : [Nom]

Cordialement,
La Commission

[Pièce jointe : PV_Commission_[Session].pdf]
```

---

## 5. Règles de gestion complètes

### 5.1 Commission
| Code | Règle |
|------|-------|
| RG-COM-001 | La commission doit avoir exactement 4 membres actifs pour voter |
| RG-COM-002 | Un seul président par année académique |
| RG-COM-003 | Seuls les membres peuvent évaluer les rapports |
| RG-COM-004 | Un membre ne peut pas évaluer deux fois le même rapport (même cycle) |

### 5.2 Votes
| Code | Règle |
|------|-------|
| RG-VOT-001 | L'unanimité requiert 4 votes identiques |
| RG-VOT-002 | Un vote ne peut pas être modifié après soumission |
| RG-VOT-003 | Les décisions sont masquées jusqu'au 4ème vote |
| RG-VOT-004 | Un vote NON nécessite un commentaire obligatoire |
| RG-VOT-005 | En cas de non-unanimité, un nouveau cycle est lancé |

### 5.3 Assignation
| Code | Règle |
|------|-------|
| RG-ASS-001 | Le directeur et l'encadreur doivent être différents |
| RG-ASS-002 | L'encadreur pédagogique doit être membre de la commission |
| RG-ASS-003 | Les deux rôles sont obligatoires |
| RG-ASS-004 | L'assignation est irréversible sauf par admin |

### 5.4 PV Commission
| Code | Règle |
|------|-------|
| RG-PV-001 | Un rapport ne peut figurer que dans un seul PV |
| RG-PV-002 | Le PV finalisé ne peut plus être modifié |
| RG-PV-003 | L'envoi notifie tous les acteurs concernés |
| RG-PV-004 | Le numéro de PV est unique et séquentiel |

---

## 6. Messages d'erreur et de succès

### 6.1 Erreurs
| Code | Message |
|------|---------|
| COM_001 | "Vous avez déjà évalué ce rapport pour ce cycle" |
| COM_002 | "Un commentaire est obligatoire pour un vote défavorable" |
| COM_003 | "La commission ne compte pas assez de membres actifs" |
| COM_004 | "Ce rapport a déjà été traité" |
| ASS_001 | "Le directeur et l'encadreur ne peuvent pas être la même personne" |
| ASS_002 | "L'encadreur pédagogique doit être membre de la commission" |
| PV_001 | "Ce rapport figure déjà dans un compte-rendu" |

### 6.2 Succès
| Code | Message |
|------|---------|
| COM_S01 | "Votre évaluation a été enregistrée" |
| COM_S02 | "Vote complet - Le rapport a été validé à l'unanimité" |
| COM_S03 | "Vote complet - Le rapport a été refusé à l'unanimité" |
| COM_S04 | "Vote non unanime - Nouveau cycle lancé" |
| ASS_S01 | "Les encadrants ont été assignés avec succès" |
| PV_S01 | "Le compte-rendu a été finalisé" |
| PV_S02 | "Le compte-rendu a été envoyé aux destinataires" |

---

## 7. Événements déclenchés

| Événement | Déclencheur | Actions |
|-----------|-------------|---------|
| `commission.vote.submitted` | Vote soumis | Mise à jour compteur, notif membres |
| `commission.vote.complete` | 4 votes reçus | Calcul unanimité |
| `commission.rapport.valide` | Unanime OUI | Email étudiant, prêt assignation |
| `commission.rapport.refuse` | Unanime NON | Email étudiant, retour édition |
| `commission.vote.relance` | Vote non unanime | Reset votes, notif membres |
| `encadrants.assigned` | Assignation faite | Email enseignants |
| `pv.finalized` | PV finalisé | Génération PDF |
| `pv.sent` | PV envoyé | Email tous destinataires |

---

## 8. Dépendances inter-modules

| Module | Type | Description |
|--------|------|-------------|
| Module 4 (Rapports) | Prérequis | Rapport doit être transféré (en_commission) |
| Module 1 (Permissions) | Prérequis | Permissions COMMISSION_* requises |
| Module 6 (Soutenances) | Déclenche | Encadrants assignés → éligible soutenance |
| Module 7 (Documents) | Utilise | Génération PDF du PV |

---

## 9. Écrans récapitulatifs

### 9.1 Espace Commission (Membres)
| Écran | URL | Permission |
|-------|-----|------------|
| Mes évaluations | `/commission/rapports` | COMMISSION_EVALUER |
| Évaluer rapport | `/commission/rapports/{id}/evaluer` | COMMISSION_EVALUER |
| État du vote | `/commission/rapports/{id}/votes` | COMMISSION_VOIR |
| Délibération | `/commission/rapports/{id}/deliberation` | COMMISSION_VOIR |

### 9.2 Espace Administration
| Écran | URL | Permission |
|-------|-----|------------|
| Membres commission | `/admin/commission/membres` | COMMISSION_GERER |
| Assignation encadrants | `/admin/commission/assignation` | ENCADRANT_ASSIGNER |
| Créer PV | `/admin/commission/pv/nouveau` | PV_CREER |
| Liste PV | `/admin/commission/pv` | PV_VOIR |
| Voir PV | `/admin/commission/pv/{id}` | PV_VOIR |
