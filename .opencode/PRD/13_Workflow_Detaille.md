# Workflow Détaillé - États et Transitions

## 1. Workflow Global du Système

### 1.1 Vue d'ensemble du parcours étudiant

```
┌──────────────────────────────────────────────────────────────────────────────────────────────┐
│                                 WORKFLOW GLOBAL - PARCOURS ÉTUDIANT                           │
├──────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                              │
│  PHASE 1: INSCRIPTION               PHASE 2: STAGE                PHASE 3: SOUTENANCE       │
│  ──────────────────                 ─────────────                 ───────────────────        │
│                                                                                              │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌───────────┐  │
│  │  Création   │───▶│ Inscription │───▶│ Candidature │───▶│   Rapport   │───▶│Commission │  │
│  │  Étudiant   │    │   + Paie    │    │    Stage    │    │    Stage    │    │ Évaluation│  │
│  └─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘    └─────┬─────┘  │
│        │                  │                  │                  │                   │       │
│        │                  │                  │                  │                   │       │
│        ▼                  ▼                  ▼                  ▼                   ▼       │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌───────────┐  │
│  │ Création    │    │ Génération  │    │ Débloque    │    │ Transfert   │    │ Assignation│  │
│  │ Compte User │    │   Reçu      │    │  Rapport    │    │ Commission  │    │ Encadrants │  │
│  └─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘    └─────┬─────┘  │
│                                                                                     │       │
│                                                                                     │       │
│  PHASE 4: DIPLÔME                                                                   │       │
│  ────────────────                                                                   │       │
│                                                                                     │       │
│    ┌───────────┐    ┌───────────┐    ┌───────────┐    ┌───────────┐    ┌─────────┐ │       │
│    │ Aptitude  │◀───│    PV     │◀───│   Notes   │◀───│Soutenance │◀───│  Jury   │◀┘       │
│    │ Encadreur │    │Commission │    │ Saisie    │    │   Passée  │    │ Composé │         │
│    └─────┬─────┘    └───────────┘    └───────────┘    └───────────┘    └─────────┘         │
│          │                                                                                   │
│          ▼                                                                                   │
│    ┌───────────┐    ┌───────────┐    ┌───────────┐                                          │
│    │ Planning  │───▶│Délibération│───▶│  PV Final │                                          │
│    │Soutenance │    │  + Calcul  │    │ (Annexes) │                                          │
│    └───────────┘    └───────────┘    └───────────┘                                          │
│                                                                                              │
└──────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Workflow Candidature (Module 3)

### 2.1 Description complète

| État | Description | Durée max | Acteur responsable |
|------|-------------|-----------|-------------------|
| `brouillon` | L'étudiant saisit les informations de stage | Illimitée | Étudiant |
| `soumise` | En attente de validation par admin | 7 jours ouvr. | Validateur |
| `validee` | Candidature acceptée, rapport débloqué | - | - |
| `rejetee` | Candidature refusée, correction nécessaire | Jusqu'à re-soumission | Étudiant |

### 2.2 Transitions détaillées

#### Transition: `soumettre`
```
De: brouillon → Vers: soumise

Conditions (Guard):
- Tous les champs obligatoires remplis
- Email encadrant valide
- Durée stage >= 3 mois
- Entreprise sélectionnée/créée

Actions:
1. Valider les données (respect/validation)
2. Nettoyer le HTML des descriptions (htmlpurifier)
3. Créer snapshot JSON (resume_candidature)
4. Mettre à jour statut et date_soumission
5. Déclencher événement CandidatureSubmittedEvent
6. Envoyer email aux validateurs
7. Logger l'action (audit)

Erreurs possibles:
- CAND_002: "Champs obligatoires manquants"
- CAND_003: "Durée de stage insuffisante"
- CAND_004: "Email encadrant invalide"
```

#### Transition: `valider`
```
De: soumise → Vers: validee

Conditions (Guard):
- Utilisateur a la permission CANDIDATURE_VALIDER
- Utilisateur différent de l'étudiant

Actions:
1. Enregistrer id_validateur et date_traitement
2. Créer snapshot JSON
3. Déclencher événement CandidatureValidatedEvent
4. Envoyer email de confirmation à l'étudiant
5. CRITIQUE: Débloquer l'accès au module Rapport
6. Logger l'action

Post-condition:
- L'étudiant peut accéder à /etudiant/rapport
```

#### Transition: `rejeter`
```
De: soumise → Vers: rejetee

Conditions (Guard):
- Utilisateur a la permission CANDIDATURE_REJETER
- Commentaire non vide (minimum 50 caractères)
- Motif de rejet sélectionné

Actions:
1. Enregistrer motif, commentaire, validateur, date
2. Créer snapshot JSON
3. Déclencher événement CandidatureRejectedEvent
4. Envoyer email à l'étudiant avec le motif
5. Logger l'action

Post-condition:
- L'étudiant peut modifier et re-soumettre
```

#### Transition: `re_soumettre`
```
De: rejetee → Vers: soumise

Conditions (Guard):
- Des modifications ont été effectuées depuis le rejet
- Mêmes conditions que 'soumettre'

Actions:
1. Incrémenter nombre_soumissions
2. Créer snapshot JSON
3. Déclencher événement CandidatureResubmittedEvent
4. Envoyer notification aux validateurs
5. Logger l'action
```

---

## 3. Workflow Rapport (Module 4)

### 3.1 Description complète

| État | Description | Éditeur | Acteur responsable |
|------|-------------|---------|-------------------|
| `brouillon` | Rédaction en cours | Éditable | Étudiant |
| `soumis` | En attente de vérification | Verrouillé | Vérificateur |
| `retourne` | Renvoyé pour corrections | Éditable | Étudiant |
| `approuve` | Validé, prêt pour commission | Verrouillé | - |
| `en_commission` | Transféré à la commission | Verrouillé | Commission |

### 3.2 Transitions détaillées

#### Transition: `soumettre`
```
De: brouillon → Vers: soumis

Conditions (Guard):
- Titre et thème renseignés
- Contenu >= 5000 mots (configurable)
- Structure minimale (3 titres H2)
- Candidature de l'étudiant validée

Actions:
1. Nettoyer le HTML (htmlpurifier)
2. Calculer nombre de mots et pages estimées
3. Générer le PDF du rapport (tcpdf)
4. Créer version avec type 'soumission'
5. Mettre à jour statut et date_soumission
6. Déclencher événement RapportSubmittedEvent
7. Envoyer notification aux vérificateurs
8. Logger l'action

Erreurs possibles:
- RAP_002: "Contenu insuffisant"
- RAP_003: "Titre et thème obligatoires"
```

#### Transition: `approuver`
```
De: soumis → Vers: approuve

Conditions (Guard):
- Utilisateur a la permission RAPPORT_APPROUVER

Actions:
1. Créer entrée dans table 'valider'
2. Mettre à jour date_approbation
3. Déclencher événement RapportApprovedEvent
4. Envoyer email de confirmation à l'étudiant
5. Logger l'action
```

#### Transition: `retourner`
```
De: soumis → Vers: retourne

Conditions (Guard):
- Utilisateur a la permission RAPPORT_RETOURNER
- Commentaire minimum 50 caractères
- Motif sélectionné

Actions:
1. Créer entrée dans table 'valider'
2. Créer commentaire_rapport (type: retour)
3. Déclencher événement RapportReturnedEvent
4. Envoyer email à l'étudiant avec commentaires
5. DÉVERROUILLER l'éditeur
6. Logger l'action
```

#### Transition: `re_soumettre`
```
De: retourne → Vers: soumis

(Similaire à 'soumettre' mais incrémente version)
```

#### Transition: `transferer`
```
De: approuve → Vers: en_commission

Conditions (Guard):
- Utilisateur a la permission RAPPORT_TRANSFERER
- Peut être fait en lot (plusieurs rapports)

Actions:
1. Pour chaque rapport:
   - Mettre à jour statut
   - Créer les 4 évaluations vides (pour chaque membre)
2. Déclencher événement RapportTransferredEvent
3. Envoyer notification aux membres de la commission
4. Logger l'action
```

---

## 4. Workflow Commission (Module 5)

### 4.1 Description complète

| État | Description | Acteur responsable |
|------|-------------|-------------------|
| `en_attente_evaluation` | Transféré, aucun vote | Membres commission |
| `en_cours_evaluation` | 1-3 votes reçus | Membres commission |
| `vote_complet` | 4 votes reçus | Système (auto) |
| `vote_unanime_oui` | 4 × OUI | - |
| `vote_unanime_non` | 4 × NON | - |
| `vote_non_unanime` | Votes mixtes | Président (relance) |
| `assigner_encadrants` | Attribution en cours | Admin |
| `pret_pour_pv` | Prêt pour compte-rendu | Admin |
| `retourne_etudiant` | Renvoyé pour correction | → Module 4 |

### 4.2 Transitions détaillées

#### Transition: `evaluer` (premier vote)
```
De: en_attente_evaluation → Vers: en_cours_evaluation

Déclencheur: Premier membre vote

Actions:
1. Créer enregistrement EvaluationRapport
2. Mettre à jour compteur votes
3. Notifier les autres membres de la progression
4. Logger l'action
```

#### Transition: `voter` (votes suivants)
```
De: en_cours_evaluation → Vers: en_cours_evaluation (si < 4)
                       → Vers: vote_complet (si = 4)

Conditions (Guard):
- Membre n'a pas déjà voté pour ce cycle
- Si décision = 'non', commentaire obligatoire

Actions:
1. Créer/mettre à jour EvaluationRapport
2. Incrémenter compteur votes
3. Si 4 votes: déclencher calcul résultat
4. Notifier progression
```

#### Transition: `declarer_resultat` (automatique)
```
De: vote_complet → Vers: vote_unanime_oui | vote_unanime_non | vote_non_unanime

Déclencheur: Automatique après 4ème vote

Logique:
if (count(oui) == 4) → vote_unanime_oui
else if (count(non) == 4) → vote_unanime_non  
else → vote_non_unanime

Actions selon résultat:
- unanime_oui: Notifier pour assignation
- unanime_non: Notifier étudiant, retourner rapport
- non_unanime: Notifier pour délibération
```

#### Transition: `relancer_vote`
```
De: vote_non_unanime → Vers: en_attente_evaluation

Conditions (Guard):
- Utilisateur est Président de la commission

Actions:
1. Incrémenter numero_cycle
2. Réinitialiser les votes du cycle précédent
3. Notifier tous les membres
4. Logger l'action
```

#### Transition: `assigner`
```
De: vote_unanime_oui → Vers: assigner_encadrants

Déclencheur: Admin initie l'assignation

Actions:
1. Afficher formulaire d'assignation
```

#### Transition: `finaliser_assignation`
```
De: assigner_encadrants → Vers: pret_pour_pv

Conditions (Guard):
- Directeur de mémoire assigné
- Encadreur pédagogique assigné
- Encadreur pédagogique est membre commission
- Directeur ≠ Encadreur

Actions:
1. Créer enregistrements AffectationEncadrant
2. Déclencher événement EncadrantsAssignedEvent
3. Envoyer emails aux enseignants assignés
4. Envoyer email à l'étudiant
5. Logger l'action
```

#### Transition: `retourner` (après unanime non)
```
De: vote_unanime_non → Vers: retourne_etudiant

Actions:
1. Compiler les commentaires des 4 membres
2. Déclencher retour du rapport (→ Module 4)
3. Envoyer email consolidé à l'étudiant
4. Logger l'action
```

---

## 5. Workflow Soutenance (Module 6)

### 5.1 Description complète

| État | Description | Acteur responsable |
|------|-------------|-------------------|
| `encadrants_assignes` | Venant de commission | Encadreur péda |
| `aptitude_validee` | Encadreur a validé | Admin |
| `jury_compose` | 5 membres assignés | Admin |
| `soutenance_programmee` | Date/heure/salle définis | - |
| `soutenance_effectuee` | Soutenance passée | Admin |
| `notes_saisies` | Notation complète | Admin |
| `delibere` | Résultat final calculé | - |

### 5.2 Transitions détaillées

#### Transition: `valider_aptitude`
```
De: encadrants_assignes → Vers: aptitude_validee

Conditions (Guard):
- Utilisateur est l'encadreur pédagogique de l'étudiant
- Décision prise (apte ou non apte)
- Si non apte: commentaire obligatoire

Actions (si apte):
1. Créer enregistrement AptitudeSoutenance
2. Déclencher événement AptitudeValidatedEvent
3. Notifier admin pour composition jury
4. Logger l'action

Si non apte:
- Enregistrer avec est_apte = false
- Notifier l'étudiant
- Peut être revalidé plus tard
```

#### Transition: `composer_jury`
```
De: aptitude_validee → Vers: jury_compose

Conditions (Guard):
- 5 membres distincts
- Président assigné
- Directeur mémoire = celui assigné (non modifiable)
- Encadreur péda = celui assigné (non modifiable)
- Maître de stage
- Examinateur

Actions:
1. Créer enregistrement Jury
2. Créer 5 enregistrements CompositionJury
3. Déclencher événement JuryComposedEvent
4. Logger l'action
```

#### Transition: `programmer`
```
De: jury_compose → Vers: soutenance_programmee

Conditions (Guard):
- Date >= aujourd'hui + 7 jours
- Heure entre 08:00 et 18:00
- Salle disponible au créneau
- Aucun membre du jury en conflit horaire

Actions:
1. Créer enregistrement Soutenance
2. Déclencher événement SoutenanceScheduledEvent
3. Envoyer convocations à:
   - L'étudiant
   - Les 5 membres du jury
4. Logger l'action

Vérifications de conflits:
- SELECT * FROM soutenance 
  WHERE id_salle = :salle 
  AND date_soutenance = :date
  AND (heure_debut BETWEEN :debut AND :fin 
       OR heure_fin BETWEEN :debut AND :fin)
  
- Pour chaque membre du jury:
  SELECT * FROM composition_jury cj
  JOIN soutenance s ON s.id_jury = cj.id_jury
  WHERE cj.id_enseignant = :membre
  AND s.date_soutenance = :date
  AND ... (chevauchement horaire)
```

#### Transition: `effectuer`
```
De: soutenance_programmee → Vers: soutenance_effectuee

Déclencheur: Manuel ou automatique (date passée)

Actions:
1. Mettre à jour statut soutenance
2. Permettre la saisie des notes
```

#### Transition: `saisir_notes`
```
De: soutenance_effectuee → Vers: notes_saisies

Conditions (Guard):
- Toutes les notes de critères saisies
- Chaque note <= barème du critère
- Total <= 20

Actions:
1. Calculer note finale (somme des critères)
2. Créer enregistrements NoteSoutenance
3. Déclencher calcul moyenne finale
4. Logger l'action
```

#### Transition: `deliberer`
```
De: notes_saisies → Vers: delibere

Conditions (Guard):
- Type de PV sélectionné (standard ou simplifié)
- Notes M1 et S1 M2 disponibles

Actions:
1. Récupérer toutes les notes nécessaires:
   - Note mémoire (Annexe 1)
   - Moyenne M1
   - Moyenne S1 M2 (si standard)
   
2. Calculer moyenne finale selon type:
   Standard: ((M1×2) + (S1M2×3) + (Memoire×3)) / 8
   Simplifié: ((M1×1) + (Memoire×2)) / 3
   
3. Déterminer mention:
   >= 16: Très Bien
   >= 14: Bien
   >= 12: Assez Bien
   >= 10: Passable
   < 10: Ajourné (pas de mention)
   
4. Créer enregistrement ResultatFinal
5. Déclencher événement DeliberationCompletedEvent
6. Générer les PV (Annexes 1, 2 ou 3)
7. Logger l'action
```

---

## 6. Résumé des événements par workflow

### 6.1 Événements Candidature
| Événement | Transition | Actions déclenchées |
|-----------|------------|---------------------|
| CandidatureSubmittedEvent | soumettre | Email validateurs, Audit |
| CandidatureValidatedEvent | valider | Email étudiant, Déblocage rapport |
| CandidatureRejectedEvent | rejeter | Email étudiant avec motif |
| CandidatureResubmittedEvent | re_soumettre | Email validateurs, Audit |

### 6.2 Événements Rapport
| Événement | Transition | Actions déclenchées |
|-----------|------------|---------------------|
| RapportSubmittedEvent | soumettre | Email vérificateurs, Génération PDF |
| RapportApprovedEvent | approuver | Email étudiant |
| RapportReturnedEvent | retourner | Email étudiant, Déblocage éditeur |
| RapportTransferredEvent | transferer | Email commission |

### 6.3 Événements Commission
| Événement | Transition | Actions déclenchées |
|-----------|------------|---------------------|
| VoteSubmittedEvent | evaluer/voter | Notification progression |
| VoteCompleteEvent | (automatique) | Calcul résultat, Routage |
| EncadrantsAssignedEvent | finaliser_assignation | Email enseignants et étudiant |
| PvCommissionCreatedEvent | (création PV) | Email à tous les acteurs |

### 6.4 Événements Soutenance
| Événement | Transition | Actions déclenchées |
|-----------|------------|---------------------|
| AptitudeValidatedEvent | valider_aptitude | Notification admin |
| JuryComposedEvent | composer_jury | Audit |
| SoutenanceScheduledEvent | programmer | Convocations email |
| DeliberationCompletedEvent | deliberer | Génération PV finaux |
