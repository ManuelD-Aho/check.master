# PRD Module 3 : Gestion des Candidatures de Stage

## 1. Vue d'ensemble

### 1.1 Objectif du module
Ce module permet aux étudiants de soumettre leur candidature de stage contenant toutes les informations relatives à leur stage (entreprise, dates, sujet, encadrant). La validation de cette candidature est le prérequis indispensable pour accéder à la rédaction du rapport.

### 1.2 Position dans le workflow global
```
Compte Étudiant Créé → CANDIDATURE (ce module) → Rapport de Stage → Commission → Soutenance
                              ↓
                      [Verrouille/Déverrouille accès au rapport]
```

### 1.3 Principe clé
> **RÈGLE FONDAMENTALE** : Tant que la candidature n'est pas validée, la section "Rapport de Stage" reste verrouillée pour l'étudiant.

### 1.4 Bibliothèques utilisées
| Bibliothèque | Rôle dans ce module |
|--------------|---------------------|
| `symfony/workflow` | Machine à états de la candidature |
| `doctrine/orm` | Gestion des entités candidature, entreprise, stage |
| `respect/validation` | Validation des données saisies |
| `egulias/email-validator` | Validation email encadrant entreprise |
| `nesbot/carbon` | Calcul durée de stage, validation dates |
| `symfony/event-dispatcher` | Événements de changement d'état |
| `phpmailer/phpmailer` | Notifications email |
| `ezyang/htmlpurifier` | Nettoyage des descriptions de stage |
| `monolog/monolog` | Journalisation des opérations |
| `white-october/pagerfanta` | Pagination des listes |

---

## 2. Machine à états (Workflow)

### 2.1 États de la candidature

```
[brouillon] ──soumettre──> [soumise] ──valider──> [validee]
                              │
                              └──rejeter──> [rejetee] ──re_soumettre──> [soumise]
```

| État | Code | Description | Actions possibles |
|------|------|-------------|-------------------|
| **Brouillon** | `brouillon` | L'étudiant prépare sa candidature | Modifier, Soumettre |
| **Soumise** | `soumise` | Candidature envoyée pour validation | Valider, Rejeter |
| **Validée** | `validee` | Candidature acceptée, rapport débloqué | Aucune (état final) |
| **Rejetée** | `rejetee` | Candidature refusée | Modifier, Re-soumettre |

### 2.2 Transitions

| Transition | De | Vers | Conditions | Actions déclenchées |
|------------|-----|------|------------|---------------------|
| `soumettre` | brouillon | soumise | Tous champs obligatoires remplis | Email notification admin |
| `valider` | soumise | validee | Permission validateur | Déblocage rapport, Email étudiant |
| `rejeter` | soumise | rejetee | Permission validateur, Commentaire obligatoire | Email étudiant avec motif |
| `re_soumettre` | rejetee | soumise | Modifications effectuées | Email notification admin |

### 2.3 Configuration Symfony Workflow

```yaml
# config/workflow/candidature.yaml
framework:
    workflows:
        candidature:
            type: state_machine
            marking_store:
                type: method
                property: statut
            supports:
                - App\Entity\Candidature
            initial_marking: brouillon
            places:
                - brouillon
                - soumise
                - validee
                - rejetee
            transitions:
                soumettre:
                    from: brouillon
                    to: soumise
                    guard: "subject.isComplete()"
                valider:
                    from: soumise
                    to: validee
                rejeter:
                    from: soumise
                    to: rejetee
                    guard: "subject.hasCommentaireRejet()"
                re_soumettre:
                    from: rejetee
                    to: soumise
                    guard: "subject.hasBeenModified()"
```

---

## 3. Entités et Modèle de données

### 3.1 Schéma relationnel

```
etudiants (1) ──────< (1) candidature_soutenance
                              │
                              ├──────< (N) resume_candidature
                              │
                              └────────> entreprises (N-1)
                                              │
                                              └──────< (N) informations_stage
```

### 3.2 Tables impliquées

#### `candidature_soutenance`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_candidature` | INT PK AUTO | NOT NULL | Identifiant unique |
| `matricule_etudiant` | VARCHAR(20) FK | NOT NULL, UNIQUE per année | Référence étudiant |
| `id_annee_academique` | INT FK | NOT NULL | Année académique |
| `statut_candidature` | ENUM | NOT NULL | 'brouillon', 'soumise', 'validee', 'rejetee' |
| `date_creation` | DATETIME | NOT NULL | Date de création |
| `date_soumission` | DATETIME | NULL | Date de première soumission |
| `date_traitement` | DATETIME | NULL | Date de validation/rejet |
| `id_validateur` | INT FK | NULL | Qui a traité la candidature |
| `commentaire_validation` | TEXT | NULL | Commentaire du validateur |
| `nombre_soumissions` | INT | DEFAULT 1 | Compteur de soumissions |
| `date_modification` | DATETIME | NOT NULL | Dernière modification |

**Contrainte unique** : (matricule_etudiant, id_annee_academique)

#### `informations_stage`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_info_stage` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_candidature` | INT FK | NOT NULL, UNIQUE | Lien vers candidature |
| `id_entreprise` | INT FK | NOT NULL | Entreprise d'accueil |
| `sujet_stage` | VARCHAR(255) | NOT NULL | Intitulé du sujet |
| `description_stage` | TEXT | NOT NULL | Description détaillée |
| `objectifs_stage` | TEXT | NULL | Objectifs du stage |
| `technologies_utilisees` | VARCHAR(500) | NULL | Technologies/outils |
| `date_debut_stage` | DATE | NOT NULL | Date de début |
| `date_fin_stage` | DATE | NOT NULL | Date de fin |
| `duree_stage_mois` | INT | COMPUTED | Durée en mois |
| `nom_encadrant` | VARCHAR(100) | NOT NULL | Nom du maître de stage |
| `prenom_encadrant` | VARCHAR(100) | NOT NULL | Prénom du maître de stage |
| `fonction_encadrant` | VARCHAR(100) | NULL | Poste de l'encadrant |
| `email_encadrant` | VARCHAR(255) | NOT NULL | Email de l'encadrant |
| `telephone_encadrant` | VARCHAR(20) | NOT NULL | Téléphone de l'encadrant |
| `adresse_stage` | TEXT | NULL | Lieu du stage si différent du siège |
| `date_creation` | DATETIME | NOT NULL | Date de création |
| `date_modification` | DATETIME | NOT NULL | Dernière modification |

#### `entreprises`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_entreprise` | INT PK AUTO | NOT NULL | Identifiant unique |
| `raison_sociale` | VARCHAR(200) | NOT NULL | Nom de l'entreprise |
| `sigle` | VARCHAR(50) | NULL | Sigle/acronyme |
| `secteur_activite` | VARCHAR(100) | NULL | Secteur d'activité |
| `adresse` | TEXT | NULL | Adresse complète |
| `ville` | VARCHAR(100) | NULL | Ville |
| `pays` | VARCHAR(100) | DEFAULT 'Côte d\'Ivoire' | Pays |
| `telephone` | VARCHAR(20) | NULL | Téléphone principal |
| `email` | VARCHAR(255) | NULL | Email général |
| `site_web` | VARCHAR(255) | NULL | URL site web |
| `description` | TEXT | NULL | Description de l'entreprise |
| `actif` | BOOLEAN | DEFAULT TRUE | Entreprise active |
| `date_creation` | DATETIME | NOT NULL | Date de création |
| `date_modification` | DATETIME | NOT NULL | Dernière modification |

#### `resume_candidature` (Historique JSON)
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_candidature` | INT FK | NOT NULL | Référence candidature |
| `resume_json` | JSON | NOT NULL | Snapshot de la candidature |
| `action` | ENUM | NOT NULL | 'soumission', 'validation', 'rejet', 'modification' |
| `id_auteur` | INT FK | NOT NULL | Qui a effectué l'action |
| `commentaire` | TEXT | NULL | Commentaire associé |
| `date_enregistrement` | DATETIME | NOT NULL | Date de l'action |

---

## 4. Fonctionnalités détaillées

### 4.1 Espace Étudiant - Saisie de la candidature

#### 4.1.1 Accès à la section Candidature
**Écran** : `/etudiant/candidature`

**Conditions d'accès** :
- Utilisateur connecté avec type "Étudiant"
- Inscription active pour l'année académique en cours
- Compte utilisateur actif

**Affichage conditionnel** :
| État candidature | Affichage |
|-----------------|-----------|
| Aucune candidature | Formulaire vide |
| Brouillon | Formulaire éditable |
| Soumise | Résumé lecture seule + message "En attente de validation" |
| Validée | Résumé lecture seule + accès rapport débloqué |
| Rejetée | Formulaire éditable + commentaire de rejet affiché |

#### 4.1.2 Formulaire de candidature
**Écran** : `/etudiant/candidature/formulaire`

**Sections du formulaire** :

**Section 1 : Entreprise d'accueil**
| Champ | Type | Obligatoire | Validation |
|-------|------|-------------|------------|
| Recherche entreprise | Autocomplete | - | Recherche dans entreprises existantes |
| OU Nouvelle entreprise | Button | - | Ouvre formulaire création |
| Raison sociale | Text | Oui | 2-200 caractères |
| Sigle | Text | Non | Max 50 caractères |
| Secteur d'activité | Select | Non | Liste prédéfinie |
| Adresse | Textarea | Non | Max 500 caractères |
| Ville | Text | Non | Max 100 caractères |
| Pays | Select | Non | Liste pays |
| Téléphone | Tel | Non | Format valide |
| Site web | URL | Non | Format URL valide |

**Section 2 : Informations du stage**
| Champ | Type | Obligatoire | Validation |
|-------|------|-------------|------------|
| Sujet du stage | Text | Oui | 10-255 caractères |
| Description | Textarea | Oui | 100-5000 caractères |
| Objectifs | Textarea | Non | Max 2000 caractères |
| Technologies | Tags | Non | Max 10 tags |
| Date de début | Date | Oui | >= date du jour |
| Date de fin | Date | Oui | > date début, >= 3 mois après début |
| Lieu (si différent) | Textarea | Non | Max 500 caractères |

**Section 3 : Maître de stage (Encadrant entreprise)**
| Champ | Type | Obligatoire | Validation |
|-------|------|-------------|------------|
| Nom | Text | Oui | 2-100 caractères |
| Prénom | Text | Oui | 2-100 caractères |
| Fonction | Text | Non | Max 100 caractères |
| Email | Email | Oui | Format email valide |
| Téléphone | Tel | Oui | Format valide |

**Comportement** :
- Sauvegarde automatique en brouillon (AJAX toutes les 30 secondes)
- Indicateur de progression (% de champs remplis)
- Validation côté client et serveur
- Nettoyage HTML des champs texte longs (htmlpurifier)

#### 4.1.3 Soumission de la candidature
**Action** : Bouton "Soumettre ma candidature"

**Pré-vérifications** :
1. Tous les champs obligatoires remplis
2. Dates de stage valides (durée >= 3 mois)
3. Email encadrant valide
4. Entreprise sélectionnée ou créée

**Processus** :
1. Validation complète des données
2. Nettoyage des contenus HTML
3. Transition workflow : `brouillon → soumise`
4. Sauvegarde date_soumission
5. Création snapshot JSON dans resume_candidature
6. Envoi notification email au(x) validateur(s)
7. Affichage message de confirmation
8. Basculement vers vue lecture seule

**Email notification validateur** :
```
Sujet : [Candidature] Nouvelle soumission - [NOM Prénom]

Une nouvelle candidature a été soumise et attend votre validation.

Étudiant : [Matricule] - [NOM Prénom]
Sujet de stage : [Sujet]
Entreprise : [Raison sociale]
Période : du [date_debut] au [date_fin]

Lien : [URL vers la candidature]
```

#### 4.1.4 Après rejet - Modification et re-soumission
**Écran** : `/etudiant/candidature/formulaire` (même écran)

**Affichage spécifique** :
- Bandeau d'alerte : "Votre candidature a été refusée"
- Affichage du commentaire de rejet
- Date du rejet
- Formulaire pré-rempli avec les données précédentes
- Champs modifiables

**Processus de re-soumission** :
1. Vérification que des modifications ont été effectuées
2. Incrémentation du compteur nombre_soumissions
3. Transition workflow : `rejetee → soumise`
4. Création nouveau snapshot JSON
5. Email notification

### 4.2 Espace Validateur - Traitement des candidatures

#### 4.2.1 Liste des candidatures à traiter
**Écran** : `/admin/candidatures`

**Permission requise** : `CANDIDATURE_VOIR`

**Onglets** :
1. **À traiter** : statut = 'soumise' (défaut)
2. **Validées** : statut = 'validee'
3. **Rejetées** : statut = 'rejetee'
4. **Toutes** : tous statuts

**Colonnes** :
| Colonne | Description |
|---------|-------------|
| Matricule | Matricule étudiant |
| Étudiant | Nom complet |
| Entreprise | Raison sociale |
| Sujet | Sujet du stage (tronqué) |
| Soumis le | Date de soumission |
| Tentative | N° de soumission |
| Actions | Voir, Valider, Rejeter |

**Filtres** :
- Par promotion
- Par période de soumission
- Par entreprise
- Recherche textuelle

**Tri** : Par date de soumission (plus ancien d'abord par défaut)

#### 4.2.2 Détail d'une candidature
**Écran** : `/admin/candidatures/{id}`

**Permission requise** : `CANDIDATURE_VOIR`

**Sections affichées** :
1. **Informations étudiant** : Matricule, Nom, Promotion, Contact
2. **Entreprise** : Toutes les informations
3. **Stage** : Sujet, description, objectifs, technologies, dates
4. **Encadrant** : Coordonnées complètes
5. **Historique** : Timeline des actions (soumissions, rejets)

**Actions disponibles** (si statut = 'soumise') :
- Bouton "Valider la candidature"
- Bouton "Rejeter la candidature"

#### 4.2.3 Validation de la candidature
**Écran** : Modal ou section dans `/admin/candidatures/{id}`

**Permission requise** : `CANDIDATURE_VALIDER`

**Champs** :
| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| Commentaire | Textarea | Non | Observation (visible par l'étudiant) |
| Confirmer | Checkbox | Oui | "Je confirme avoir vérifié les informations" |

**Processus** :
1. Vérification permission utilisateur
2. Transition workflow : `soumise → validee`
3. Sauvegarde :
   - date_traitement = maintenant
   - id_validateur = utilisateur courant
   - commentaire_validation = commentaire saisi
4. Création snapshot JSON
5. **CRITIQUE** : Déblocage de la section Rapport pour l'étudiant
6. Envoi email de confirmation à l'étudiant
7. Journalisation

**Email confirmation étudiant** :
```
Sujet : [Candidature] Candidature validée

Bonjour [Prénom],

Votre candidature de stage a été validée.

Sujet : [Sujet]
Entreprise : [Raison sociale]

Vous pouvez maintenant accéder à la section "Rapport de Stage" pour commencer la rédaction de votre mémoire.

[Commentaire du validateur si présent]

Lien : [URL vers rapport]
```

#### 4.2.4 Rejet de la candidature
**Écran** : Modal ou section dans `/admin/candidatures/{id}`

**Permission requise** : `CANDIDATURE_REJETER`

**Champs** :
| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| Motif du rejet | Select | Oui | Liste prédéfinie (paramétrable) |
| Commentaire détaillé | Textarea | Oui | Explication pour l'étudiant |

**Motifs prédéfinis** (paramétrables en base) :
- Sujet non conforme au niveau Master
- Durée de stage insuffisante
- Informations entreprise incomplètes
- Coordonnées encadrant invalides
- Autre (préciser)

**Processus** :
1. Vérification permission utilisateur
2. Vérification commentaire non vide
3. Transition workflow : `soumise → rejetee`
4. Sauvegarde :
   - date_traitement = maintenant
   - id_validateur = utilisateur courant
   - commentaire_validation = motif + commentaire
5. Création snapshot JSON
6. Envoi email à l'étudiant avec motif
7. Journalisation

**Email rejet étudiant** :
```
Sujet : [Candidature] Candidature refusée - Action requise

Bonjour [Prénom],

Votre candidature de stage a été refusée.

Motif : [Motif sélectionné]

Commentaire du validateur :
[Commentaire détaillé]

Veuillez corriger les points mentionnés et soumettre à nouveau votre candidature.

Lien : [URL vers formulaire]
```

### 4.3 Gestion des entreprises (référentiel)

#### 4.3.1 Liste des entreprises
**Écran** : `/admin/entreprises`

**Permission requise** : `ENTREPRISE_VOIR`

**Colonnes** :
- Raison sociale
- Sigle
- Secteur
- Ville
- Nombre de stages (compteur)
- Actions

**Fonctionnalités** :
- Recherche textuelle
- Filtre par secteur
- Filtre par ville
- Export CSV

#### 4.3.2 Création/Modification entreprise
**Écran** : `/admin/entreprises/nouveau` ou `/admin/entreprises/{id}/modifier`

**Permission requise** : `ENTREPRISE_CREER` / `ENTREPRISE_MODIFIER`

**Règles** :
- Une entreprise ne peut pas être supprimée si elle a des stages associés
- Désactivation logique uniquement
- Fusion d'entreprises en doublon possible (admin)

### 4.4 Verrouillage/Déverrouillage du rapport

#### 4.4.1 Mécanisme de verrouillage
**Implémentation technique** :

```php
// Dans le middleware de vérification d'accès au rapport
public function canAccessRapport(User $user): bool
{
    if ($user->getType() !== 'Etudiant') {
        return true; // Non-étudiants ont d'autres règles
    }
    
    $candidature = $this->candidatureRepository->findByEtudiantAndAnnee(
        $user->getEtudiant(),
        $this->anneeAcademiqueService->getActive()
    );
    
    return $candidature && $candidature->getStatut() === 'validee';
}
```

**Affichage côté étudiant** :
- Menu "Rapport de Stage" :
  - Si candidature non validée → Icône cadenas, non cliquable, tooltip explicatif
  - Si candidature validée → Icône normale, cliquable

#### 4.4.2 Message de verrouillage
Si l'étudiant tente d'accéder à `/etudiant/rapport` sans candidature validée :

**Affichage** :
```
╔═══════════════════════════════════════════════════════════════╗
║  🔒 Section verrouillée                                       ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  Pour accéder à la rédaction de votre rapport de stage,      ║
║  vous devez d'abord soumettre et faire valider votre         ║
║  candidature.                                                 ║
║                                                               ║
║  État actuel de votre candidature :                          ║
║  [Statut avec explication]                                   ║
║                                                               ║
║  [Bouton : Accéder à ma candidature]                         ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
```

---

## 5. Règles de gestion complètes

### 5.1 Candidature
| Code | Règle |
|------|-------|
| RG-CAND-001 | Un étudiant ne peut avoir qu'une seule candidature par année académique |
| RG-CAND-002 | La candidature doit être validée pour débloquer l'accès au rapport |
| RG-CAND-003 | Une candidature validée ne peut plus être modifiée |
| RG-CAND-004 | Le rejet nécessite obligatoirement un commentaire explicatif |
| RG-CAND-005 | La re-soumission n'est possible qu'après modification |
| RG-CAND-006 | Chaque soumission/rejet est historisé en JSON |
| RG-CAND-007 | Le validateur ne peut pas traiter sa propre candidature |

### 5.2 Stage
| Code | Règle |
|------|-------|
| RG-STG-001 | La durée minimale du stage est de 3 mois (90 jours) |
| RG-STG-002 | La date de début ne peut pas être dans le passé (création) |
| RG-STG-003 | La date de fin doit être postérieure à la date de début |
| RG-STG-004 | Le sujet doit faire au minimum 10 caractères |
| RG-STG-005 | La description doit faire au minimum 100 caractères |
| RG-STG-006 | L'email de l'encadrant doit être valide et fonctionnel |

### 5.3 Entreprise
| Code | Règle |
|------|-------|
| RG-ENT-001 | Une entreprise ne peut pas être supprimée si utilisée |
| RG-ENT-002 | La raison sociale doit être unique |
| RG-ENT-003 | Une entreprise désactivée n'apparaît plus dans les recherches |
| RG-ENT-004 | L'étudiant peut créer une nouvelle entreprise si non existante |

### 5.4 Notifications
| Code | Règle |
|------|-------|
| RG-NOTIF-001 | Une notification email est envoyée à chaque changement d'état |
| RG-NOTIF-002 | Les validateurs sont notifiés des nouvelles soumissions |
| RG-NOTIF-003 | L'étudiant reçoit toujours le motif de rejet |

---

## 6. Messages d'erreur et de succès

### 6.1 Erreurs
| Code | Message | Contexte |
|------|---------|----------|
| CAND_001 | "Vous avez déjà une candidature pour cette année académique" | Tentative création doublon |
| CAND_002 | "Veuillez remplir tous les champs obligatoires" | Soumission incomplète |
| CAND_003 | "La durée du stage doit être d'au moins 3 mois" | Dates invalides |
| CAND_004 | "L'adresse email de l'encadrant n'est pas valide" | Email incorrect |
| CAND_005 | "Vous ne pouvez pas modifier une candidature validée" | Tentative modification |
| CAND_006 | "Veuillez effectuer des modifications avant de re-soumettre" | Re-soumission identique |
| CAND_007 | "Un commentaire est obligatoire pour rejeter une candidature" | Rejet sans motif |

### 6.2 Succès
| Code | Message |
|------|---------|
| CAND_S01 | "Votre candidature a été enregistrée comme brouillon" |
| CAND_S02 | "Votre candidature a été soumise avec succès" |
| CAND_S03 | "La candidature a été validée" |
| CAND_S04 | "La candidature a été rejetée" |
| CAND_S05 | "Votre candidature a été re-soumise" |

---

## 7. Événements déclenchés

| Événement | Déclencheur | Actions |
|-----------|-------------|---------|
| `candidature.created` | Création candidature | Log audit |
| `candidature.submitted` | Soumission | Email validateurs, Log |
| `candidature.validated` | Validation | Déblocage rapport, Email étudiant, Log |
| `candidature.rejected` | Rejet | Email étudiant, Log |
| `candidature.resubmitted` | Re-soumission | Email validateurs, Log |

---

## 8. Dépendances inter-modules

| Module | Type | Description |
|--------|------|-------------|
| Module 2 (Étudiants) | Prérequis | Étudiant doit exister et être inscrit |
| Module 1 (Permissions) | Prérequis | Permissions CANDIDATURE_* requises |
| Module 4 (Rapports) | Déclenche | La validation débloque l'accès au rapport |

---

## 9. Écrans récapitulatifs

### 9.1 Espace Étudiant
| Écran | URL | Permission |
|-------|-----|------------|
| Ma candidature | `/etudiant/candidature` | Type = Étudiant |
| Formulaire candidature | `/etudiant/candidature/formulaire` | Type = Étudiant |

### 9.2 Espace Administration
| Écran | URL | Permission |
|-------|-----|------------|
| Liste candidatures | `/admin/candidatures` | CANDIDATURE_VOIR |
| Détail candidature | `/admin/candidatures/{id}` | CANDIDATURE_VOIR |
| Valider | `/admin/candidatures/{id}/valider` | CANDIDATURE_VALIDER |
| Rejeter | `/admin/candidatures/{id}/rejeter` | CANDIDATURE_REJETER |
| Liste entreprises | `/admin/entreprises` | ENTREPRISE_VOIR |
| Créer entreprise | `/admin/entreprises/nouveau` | ENTREPRISE_CREER |
| Modifier entreprise | `/admin/entreprises/{id}/modifier` | ENTREPRISE_MODIFIER |

---

## 10. Configuration des motifs de rejet

Table `motifs_rejet_candidature` (paramétrable) :

| id | code | libelle | actif |
|----|------|---------|-------|
| 1 | SUJET_NON_CONFORME | Sujet non conforme au niveau Master | true |
| 2 | DUREE_INSUFFISANTE | Durée de stage insuffisante | true |
| 3 | INFO_ENTREPRISE_INCOMPLETE | Informations entreprise incomplètes | true |
| 4 | CONTACT_ENCADRANT_INVALIDE | Coordonnées encadrant invalides | true |
| 5 | DESCRIPTION_INSUFFISANTE | Description du stage trop succincte | true |
| 6 | AUTRE | Autre motif (préciser en commentaire) | true |

Cette table est modifiable par l'administrateur via le paramétrage système.
