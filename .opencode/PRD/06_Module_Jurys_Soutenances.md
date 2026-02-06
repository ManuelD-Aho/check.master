# PRD Module 6 : Gestion des Jurys et Soutenances

## 1. Vue d'ensemble

### 1.1 Objectif du module
Ce module gère la validation de l'aptitude à soutenir par l'encadreur pédagogique, la composition des jurys de soutenance, la programmation des soutenances (date, heure, salle), la notation finale et le calcul des moyennes pour l'obtention du diplôme.

### 1.2 Position dans le workflow global
```
Encadrants Assignés → Aptitude Encadreur → JURY & SOUTENANCE (ce module) → PV Finaux → Diplôme
                              ↓
          [Composition jury → Programmation → Notation → Calcul moyenne]
```

### 1.3 Principe clé
> **RÈGLE FONDAMENTALE** : L'encadreur pédagogique (obligatoirement membre de la commission) doit valider l'aptitude de l'étudiant à soutenir avant la programmation de sa soutenance.

### 1.4 Bibliothèques utilisées
| Bibliothèque | Rôle dans ce module |
|--------------|---------------------|
| `symfony/workflow` | Machine à états de la soutenance |
| `doctrine/orm` | Gestion des entités jury, soutenance, notes |
| `brick/math` | Calcul précis des moyennes pondérées |
| `nesbot/carbon` | Gestion des dates/heures de soutenance |
| `symfony/event-dispatcher` | Événements de validation, programmation |
| `phpmailer/phpmailer` | Notifications email |
| `tecnickcom/tcpdf` | Génération du planning et PV finaux |
| `monolog/monolog` | Journalisation |
| `white-october/pagerfanta` | Pagination |
| `symfony/expression-language` | Règles de calcul de mentions |

---

## 2. Machine à états (Workflow)

### 2.1 États de l'étudiant vers la soutenance

```
[encadrants_assignes] ──attente_aptitude──> [aptitude_validee] ──composer_jury──> [jury_compose]
                                                                                        │
                                                                                        ▼
                                                                               [soutenance_programmee]
                                                                                        │
                                                                                        ▼
                                                                               [soutenance_effectuee]
                                                                                        │
                                                                                        ▼
                                                                               [notes_saisies]
                                                                                        │
                                                                                        ▼
                                                                               [delibere]
```

| État | Code | Description |
|------|------|-------------|
| **Encadrants assignés** | `encadrants_assignes` | Directeur et encadreur assignés |
| **Aptitude validée** | `aptitude_validee` | Encadreur a validé l'aptitude |
| **Jury composé** | `jury_compose` | 5 membres du jury assignés |
| **Programmée** | `soutenance_programmee` | Date, heure, salle définis |
| **Effectuée** | `soutenance_effectuee` | Soutenance passée |
| **Notes saisies** | `notes_saisies` | Notation complète saisie |
| **Délibéré** | `delibere` | Résultat final calculé |

---

## 3. Entités et Modèle de données

### 3.1 Schéma relationnel

```
etudiants (1) ────< (1) soutenance
                         │
                         ├──────< (N) composition_jury
                         │              │
                         │              └──> enseignants
                         │              └──> roles_jury
                         │
                         ├──────< (N) notes_soutenance
                         │              │
                         │              └──> criteres_evaluation
                         │
                         └──────> salles
```

### 3.2 Tables impliquées

#### `aptitude_soutenance`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_aptitude` | INT PK AUTO | NOT NULL | Identifiant unique |
| `matricule_etudiant` | VARCHAR(20) FK | NOT NULL | Référence étudiant |
| `id_annee_academique` | INT FK | NOT NULL | Année académique |
| `id_encadreur` | INT FK | NOT NULL | Encadreur pédagogique |
| `est_apte` | BOOLEAN | NULL | Décision (NULL = en attente) |
| `commentaire` | TEXT | NULL | Justification |
| `date_validation` | DATETIME | NULL | Date de la décision |
| `date_creation` | DATETIME | NOT NULL | Date de création |

**Contrainte unique** : (matricule_etudiant, id_annee_academique)

#### `jurys`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_jury` | INT PK AUTO | NOT NULL | Identifiant unique |
| `matricule_etudiant` | VARCHAR(20) FK | NOT NULL | Référence étudiant |
| `id_annee_academique` | INT FK | NOT NULL | Année académique |
| `statut_jury` | ENUM | NOT NULL | 'en_composition', 'complet', 'valide' |
| `date_creation` | DATETIME | NOT NULL | Date de création |
| `date_validation` | DATETIME | NULL | Date de validation |
| `id_createur` | INT FK | NOT NULL | Qui a créé le jury |

**Contrainte unique** : (matricule_etudiant, id_annee_academique)

#### `roles_jury`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_role_jury` | INT PK AUTO | NOT NULL | Identifiant unique |
| `code_role` | VARCHAR(50) | NOT NULL, UNIQUE | 'president', 'directeur_memoire', etc. |
| `libelle_role` | VARCHAR(100) | NOT NULL | Nom affiché |
| `description` | TEXT | NULL | Description du rôle |
| `ordre_affichage` | INT | NOT NULL | Ordre dans la liste |
| `est_obligatoire` | BOOLEAN | DEFAULT TRUE | Rôle obligatoire |
| `actif` | BOOLEAN | DEFAULT TRUE | Rôle actif |

**Valeurs par défaut** :
| code_role | libelle_role | ordre |
|-----------|--------------|-------|
| `president` | Président du Jury | 1 |
| `directeur_memoire` | Directeur de Mémoire | 2 |
| `encadreur_pedagogique` | Encadreur Pédagogique | 3 |
| `maitre_stage` | Maître de Stage | 4 |
| `examinateur` | Examinateur | 5 |

#### `composition_jury`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_composition` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_jury` | INT FK | NOT NULL | Référence jury |
| `id_enseignant` | INT FK | NOT NULL | Enseignant membre |
| `id_role_jury` | INT FK | NOT NULL | Rôle dans le jury |
| `est_present` | BOOLEAN | NULL | Présence effective (NULL = prévu) |
| `commentaire` | TEXT | NULL | Note |
| `date_affectation` | DATETIME | NOT NULL | Date d'affectation |
| `id_affecteur` | INT FK | NOT NULL | Qui a fait l'affectation |

**Contraintes uniques** :
- (id_jury, id_role_jury) : Un seul membre par rôle
- (id_jury, id_enseignant) : Un enseignant ne peut avoir qu'un rôle

#### `soutenances`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_soutenance` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_jury` | INT FK | NOT NULL, UNIQUE | Référence jury |
| `matricule_etudiant` | VARCHAR(20) FK | NOT NULL | Référence étudiant |
| `id_salle` | INT FK | NOT NULL | Salle de soutenance |
| `date_soutenance` | DATE | NOT NULL | Date de la soutenance |
| `heure_debut` | TIME | NOT NULL | Heure de début |
| `heure_fin` | TIME | NULL | Heure de fin prévue |
| `duree_minutes` | INT | DEFAULT 60 | Durée prévue |
| `theme_soutenance` | VARCHAR(255) | NOT NULL | Thème présenté |
| `statut_soutenance` | ENUM | NOT NULL | 'programmee', 'en_cours', 'terminee', 'reportee', 'annulee' |
| `observations` | TEXT | NULL | Observations générales |
| `date_creation` | DATETIME | NOT NULL | Date de création |
| `date_modification` | DATETIME | NOT NULL | Dernière modification |
| `id_programmeur` | INT FK | NOT NULL | Qui a programmé |

**Contraintes** :
- Contrainte unique sur (id_salle, date_soutenance, heure_debut) pour éviter les conflits

#### `salles`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_salle` | INT PK AUTO | NOT NULL | Identifiant unique |
| `code_salle` | VARCHAR(20) | NOT NULL, UNIQUE | Code (ex: "A101") |
| `libelle_salle` | VARCHAR(100) | NOT NULL | Nom complet |
| `capacite` | INT | NULL | Nombre de places |
| `equipements` | VARCHAR(255) | NULL | Équipements disponibles |
| `batiment` | VARCHAR(100) | NULL | Bâtiment |
| `etage` | VARCHAR(20) | NULL | Étage |
| `actif` | BOOLEAN | DEFAULT TRUE | Salle utilisable |

#### `criteres_evaluation`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_critere` | INT PK AUTO | NOT NULL | Identifiant unique |
| `code_critere` | VARCHAR(50) | NOT NULL, UNIQUE | Code technique |
| `libelle_critere` | VARCHAR(100) | NOT NULL | Nom du critère |
| `description` | TEXT | NULL | Description détaillée |
| `ordre_affichage` | INT | NOT NULL | Ordre dans la grille |
| `actif` | BOOLEAN | DEFAULT TRUE | Critère actif |

**Critères par défaut** :
| code | libelle |
|------|---------|
| `qualite_document` | Qualité du document écrit |
| `maitrise_sujet` | Maîtrise du sujet |
| `presentation_orale` | Qualité de la présentation orale |
| `reponses_questions` | Pertinence des réponses aux questions |
| `respect_temps` | Respect du temps imparti |

#### `baremes_criteres` (correspondre)
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_annee_academique` | INT FK | NOT NULL | Année académique |
| `id_critere` | INT FK | NOT NULL | Critère concerné |
| `bareme` | DECIMAL(4,2) | NOT NULL | Note maximale (ex: 5.00) |
| `coefficient` | DECIMAL(3,2) | DEFAULT 1.00 | Coefficient |

**Contrainte unique** : (id_annee_academique, id_critere)

#### `notes_soutenance` (evaluer)
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_note` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_soutenance` | INT FK | NOT NULL | Référence soutenance |
| `id_critere` | INT FK | NOT NULL | Critère évalué |
| `note` | DECIMAL(4,2) | NOT NULL | Note attribuée |
| `commentaire` | TEXT | NULL | Observation |
| `id_jury_membre` | INT FK | NULL | Qui a noté (si individuel) |
| `date_saisie` | DATETIME | NOT NULL | Date de saisie |

**Contrainte unique** : (id_soutenance, id_critere)

#### `resultats_finaux`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_resultat` | INT PK AUTO | NOT NULL | Identifiant unique |
| `matricule_etudiant` | VARCHAR(20) FK | NOT NULL | Référence étudiant |
| `id_annee_academique` | INT FK | NOT NULL | Année académique |
| `id_soutenance` | INT FK | NOT NULL | Référence soutenance |
| `note_memoire` | DECIMAL(4,2) | NOT NULL | Note de soutenance (Annexe 1) |
| `moyenne_m1` | DECIMAL(4,2) | NOT NULL | Moyenne M1 |
| `moyenne_s1_m2` | DECIMAL(4,2) | NOT NULL | Moyenne S1 M2 |
| `moyenne_finale` | DECIMAL(4,2) | NOT NULL | Moyenne pondérée finale |
| `id_mention` | INT FK | NOT NULL | Mention obtenue |
| `type_pv` | ENUM | NOT NULL | 'standard' (Annexe 2), 'simplifie' (Annexe 3) |
| `decision_jury` | ENUM | NOT NULL | 'admis', 'ajourne', 'refuse' |
| `date_deliberation` | DATETIME | NOT NULL | Date de délibération |
| `valide` | BOOLEAN | DEFAULT FALSE | Validé par l'admin |
| `date_creation` | DATETIME | NOT NULL | Date de création |

**Contrainte unique** : (matricule_etudiant, id_annee_academique)

#### `mentions`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_mention` | INT PK AUTO | NOT NULL | Identifiant unique |
| `code_mention` | VARCHAR(20) | NOT NULL, UNIQUE | 'passable', 'ab', 'bien', 'tb' |
| `libelle_mention` | VARCHAR(50) | NOT NULL | Nom complet |
| `seuil_minimum` | DECIMAL(4,2) | NOT NULL | Note minimale |
| `seuil_maximum` | DECIMAL(4,2) | NOT NULL | Note maximale |
| `ordre` | INT | NOT NULL | Ordre croissant |

**Valeurs** :
| code | libelle | min | max |
|------|---------|-----|-----|
| passable | Passable | 10.00 | 11.99 |
| ab | Assez Bien | 12.00 | 13.99 |
| bien | Bien | 14.00 | 15.99 |
| tb | Très Bien | 16.00 | 20.00 |

---

## 4. Fonctionnalités détaillées

### 4.1 Validation de l'aptitude à soutenir

#### 4.1.1 Espace Encadreur Pédagogique
**Écran** : `/encadreur/etudiants`

**Permission requise** : `APTITUDE_VALIDER`

**Condition** : L'utilisateur doit être assigné comme encadreur pédagogique

**Liste affichée** :
- Étudiants dont l'utilisateur est l'encadreur pédagogique
- Colonnes : Matricule, Nom, Thème, Directeur mémoire, Statut aptitude, Actions

**Statuts** :
| Icône | Statut | Description |
|-------|--------|-------------|
| ⏳ | En attente | Décision non prise |
| ✅ | Apte | Peut soutenir |
| ❌ | Non apte | Pas encore prêt |

#### 4.1.2 Validation de l'aptitude
**Écran** : `/encadreur/etudiants/{matricule}/aptitude`

**Affichage** :
- Informations étudiant
- Thème du mémoire
- Lien vers le rapport (lecture seule)
- Historique des échanges (optionnel)

**Formulaire** :
| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| Décision | Radio | Oui | ○ Apte à soutenir / ○ Pas encore apte |
| Commentaire | Textarea | Conditionnel | Obligatoire si "Pas encore apte" |

**Processus** :
1. Enregistrement de la décision
2. Si "Apte" :
   - Transition workflow : `encadrants_assignes → aptitude_validee`
   - Notification à l'administration pour composition jury
   - Email à l'étudiant (information)
3. Si "Non apte" :
   - Email à l'étudiant avec commentaire
   - L'encadreur peut revalider plus tard

### 4.2 Composition du Jury

#### 4.2.1 Liste des jurys à composer
**Écran** : `/admin/jurys`

**Permission requise** : `JURY_COMPOSER`

**Onglets** :
1. **À composer** : Étudiants aptes sans jury complet
2. **Complets** : Jurys de 5 membres
3. **Programmés** : Avec soutenance planifiée
4. **Historique** : Soutenances passées

**Colonnes** :
| Colonne | Description |
|---------|-------------|
| Matricule | Matricule étudiant |
| Étudiant | Nom complet |
| Thème | Titre du mémoire |
| Directeur | Déjà assigné (Module 5) |
| Encadreur | Déjà assigné (Module 5) |
| Jury | X/5 membres |
| Actions | Composer, Voir |

#### 4.2.2 Formulaire de composition
**Écran** : `/admin/jurys/{id}/composer`

**Interface** :
```
╔═══════════════════════════════════════════════════════════════╗
║  📋 Composition du Jury - [NOM Prénom étudiant]              ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  Thème : [Titre du mémoire]                                  ║
║  Entreprise : [Raison sociale]                               ║
║                                                               ║
║  ┌─────────────────────────────────────────────────────────┐ ║
║  │ 1. Président du Jury *                                  │ ║
║  │    [Autocomplete enseignant________________] [Grade]    │ ║
║  │                                                         │ ║
║  │ 2. Directeur de Mémoire (pré-rempli)                   │ ║
║  │    [Prof. DUPONT Jean - Professeur Titulaire] ✓        │ ║
║  │                                                         │ ║
║  │ 3. Encadreur Pédagogique (pré-rempli)                  │ ║
║  │    [Dr. MARTIN Marie - Maître de Conférences] ✓        │ ║
║  │                                                         │ ║
║  │ 4. Maître de Stage *                                    │ ║
║  │    [Autocomplete ou saisie libre__________]             │ ║
║  │    Entreprise: [Nom] Email: [Email] Tél: [Tel]         │ ║
║  │                                                         │ ║
║  │ 5. Examinateur *                                        │ ║
║  │    [Autocomplete enseignant________________]            │ ║
║  └─────────────────────────────────────────────────────────┘ ║
║                                                               ║
║  [Annuler]                              [Valider le jury]    ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
```

**Champs** :
| Rôle | Source | Obligatoire | Notes |
|------|--------|-------------|-------|
| Président | Enseignants | Oui | Généralement un professeur |
| Directeur Mémoire | Pré-rempli | Oui | Non modifiable |
| Encadreur Pédagogique | Pré-rempli | Oui | Non modifiable |
| Maître de Stage | Saisie/Base | Oui | Peut être externe |
| Examinateur | Enseignants | Oui | Membre supplémentaire |

**Règles de validation** :
| Code | Règle |
|------|-------|
| RG-JUR-001 | Les 5 rôles doivent être remplis |
| RG-JUR-002 | Aucun doublon (une personne = un rôle) |
| RG-JUR-003 | Le président doit être différent du directeur |
| RG-JUR-004 | Le maître de stage peut être externe (non enseignant) |

### 4.3 Programmation des soutenances

#### 4.3.1 Planning des soutenances
**Écran** : `/admin/soutenances/planning`

**Permission requise** : `SOUTENANCE_PROGRAMMER`

**Interface** : Vue calendrier semaine/mois

**Vue semaine** :
```
         | Lundi 15  | Mardi 16  | Mercredi 17 | ...
─────────┼───────────┼───────────┼─────────────┼────
08:00    |           |           |             |
09:00    | Salle A1  |           | Salle B2    |
         | DUPONT J. |           | MARTIN M.   |
10:00    |           | Salle A1  |             |
         |           | PETIT P.  |             |
11:00    |           |           |             |
...
```

**Fonctionnalités** :
- Glisser-déposer pour déplacer une soutenance
- Clic pour créer une nouvelle soutenance
- Code couleur par statut
- Filtres par salle, par promotion

#### 4.3.2 Programmation d'une soutenance
**Écran** : `/admin/soutenances/programmer`

**Étape 1 : Sélection de l'étudiant**
- Liste des étudiants avec jury complet et non programmés
- Recherche par nom, matricule

**Étape 2 : Choix du créneau**
| Champ | Type | Obligatoire | Validation |
|-------|------|-------------|------------|
| Date | DatePicker | Oui | >= aujourd'hui |
| Heure début | TimePicker | Oui | 08:00 - 18:00 |
| Durée | Select | Oui | 45, 60, 90 minutes |
| Salle | Select | Oui | Salles disponibles au créneau |

**Vérification des conflits** :
- Salle déjà occupée au créneau ?
- Un membre du jury a-t-il une autre soutenance au même moment ?
- L'étudiant a-t-il un autre événement ?

**Affichage des conflits** :
```
⚠️ Conflit détecté :
- Prof. DUPONT (Président) a une soutenance en Salle B2 de 09:00 à 10:00
- La salle A1 est occupée de 08:30 à 09:30

Suggestion : Décaler à 10:00 en Salle A1
```

**Processus de validation** :
1. Vérification absence de conflits
2. Création de l'enregistrement soutenance
3. Transition workflow : `jury_compose → soutenance_programmee`
4. Envoi emails à tous les acteurs :
   - Étudiant
   - 5 membres du jury
   - Administration
5. Journalisation

#### 4.3.3 Email de convocation
**Destinataires** : Étudiant + Membres du jury

**Sujet** : [Convocation] Soutenance de mémoire - [DATE]

**Contenu** :
```
Bonjour [Prénom],

Vous êtes convoqué(e) à la soutenance de mémoire suivante :

Étudiant : [NOM Prénom] - [Matricule]
Thème : [Titre du mémoire]

Date : [Date complète]
Heure : [Heure]
Durée prévue : [Durée] minutes
Lieu : [Salle] - [Bâtiment]

Composition du jury :
- Président : [Nom] ([Grade])
- Directeur de mémoire : [Nom]
- Encadreur pédagogique : [Nom]
- Maître de stage : [Nom] ([Entreprise])
- Examinateur : [Nom]

Cordialement,
L'Administration
```

### 4.4 Génération du tableau des soutenances

#### 4.4.1 Tableau récapitulatif PDF
**Écran** : `/admin/soutenances/tableau`

**Filtres** :
- Période (date début - date fin)
- Par salle
- Par promotion

**Action** : "Générer le tableau PDF"

**Format PDF** :
```
┌─────────────────────────────────────────────────────────────────────────────┐
│           TABLEAU DES SOUTENANCES - [Période]                               │
├───────┬───────┬─────────────────┬─────────────┬─────────────────────────────┤
│ Date  │ Heure │ Salle           │ Étudiant    │ Thème                       │
├───────┼───────┼─────────────────┼─────────────┼─────────────────────────────┤
│ 15/01 │ 09:00 │ Amphi A         │ DUPONT Jean │ Développement d'une...     │
│       │       │                 │             │                             │
│       │       │ Jury : Pdt: MARTIN, Dir: PETIT, Enc: DURAND, MS: BLANC     │
├───────┼───────┼─────────────────┼─────────────┼─────────────────────────────┤
│ 15/01 │ 10:30 │ Salle B2        │ BERNARD M.  │ Mise en place d'un...      │
...
└───────┴───────┴─────────────────┴─────────────┴─────────────────────────────┘
```

### 4.5 Notation de la soutenance

#### 4.5.1 Saisie des notes (Annexe 1)
**Écran** : `/admin/soutenances/{id}/notation`

**Permission requise** : `SOUTENANCE_NOTER`

**Interface** :
```
╔═══════════════════════════════════════════════════════════════╗
║  📝 Grille d'évaluation - [NOM Prénom étudiant]              ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  Date soutenance : [Date]                                    ║
║  Thème : [Titre]                                             ║
║                                                               ║
║  ┌───────────────────────────────────────────────┬─────────┐ ║
║  │ Critère                                       │ Note    │ ║
║  ├───────────────────────────────────────────────┼─────────┤ ║
║  │ Qualité du document écrit                     │ [__]/5  │ ║
║  │ Maîtrise du sujet                            │ [__]/5  │ ║
║  │ Qualité de la présentation orale             │ [__]/5  │ ║
║  │ Pertinence des réponses aux questions        │ [__]/3  │ ║
║  │ Respect du temps imparti                     │ [__]/2  │ ║
║  ├───────────────────────────────────────────────┼─────────┤ ║
║  │ TOTAL                                         │ [XX]/20 │ ║
║  └───────────────────────────────────────────────┴─────────┘ ║
║                                                               ║
║  Observations du jury :                                      ║
║  [_________________________________________________]        ║
║                                                               ║
║  [Annuler]                      [Enregistrer les notes]      ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
```

**Validation** :
- Chaque note <= barème du critère
- Total calculé automatiquement (somme simple)
- Total <= 20

**Processus** :
1. Saisie des notes par critère
2. Calcul du total (Note du mémoire)
3. Enregistrement
4. Transition : `soutenance_effectuee → notes_saisies`
5. Déclenchement du calcul de la moyenne finale

### 4.6 Calcul de la moyenne finale

#### 4.6.1 Formules de calcul

**Annexe 2 (PV Standard)** - Coefficient total : 8
```
Moyenne Finale = ((Moyenne_M1 × 2) + (Moyenne_S1_M2 × 3) + (Note_Memoire × 3)) / 8
```

**Annexe 3 (PV Simplifié)** - Coefficient total : 3
```
Moyenne Finale = ((Moyenne_M1 × 1) + (Note_Memoire × 2)) / 3
```

#### 4.6.2 Processus de calcul
**Déclenché après** : Saisie des notes de soutenance

**Algorithme** :
```php
function calculerMoyenneFinale(Etudiant $etudiant, string $typePv): array
{
    $noteMemoire = $etudiant->getNoteSoutenance(); // Annexe 1
    $moyenneM1 = $etudiant->getMoyenneM1();
    $moyenneS1M2 = $etudiant->getMoyenneS1M2();
    
    if ($typePv === 'standard') {
        // Annexe 2
        $moyenneFinale = (($moyenneM1 * 2) + ($moyenneS1M2 * 3) + ($noteMemoire * 3)) / 8;
    } else {
        // Annexe 3
        $moyenneFinale = (($moyenneM1 * 1) + ($noteMemoire * 2)) / 3;
    }
    
    // Arrondi à 2 décimales (brick/math)
    $moyenneFinale = BigDecimal::of($moyenneFinale)->toScale(2, RoundingMode::HALF_UP);
    
    // Détermination de la mention
    $mention = $this->determinerMention($moyenneFinale);
    
    // Décision
    $decision = $moyenneFinale >= 10 ? 'admis' : 'ajourne';
    
    return [
        'note_memoire' => $noteMemoire,
        'moyenne_m1' => $moyenneM1,
        'moyenne_s1_m2' => $moyenneS1M2,
        'moyenne_finale' => $moyenneFinale,
        'mention' => $mention,
        'decision' => $decision,
    ];
}
```

#### 4.6.3 Détermination de la mention
```php
function determinerMention(float $moyenne): Mention
{
    return match(true) {
        $moyenne >= 16 => Mention::TRES_BIEN,
        $moyenne >= 14 => Mention::BIEN,
        $moyenne >= 12 => Mention::ASSEZ_BIEN,
        $moyenne >= 10 => Mention::PASSABLE,
        default => null, // Ajourné, pas de mention
    };
}
```

#### 4.6.4 Écran de délibération
**Écran** : `/admin/soutenances/{id}/deliberation`

**Permission requise** : `DELIBERATION_VALIDER`

**Affichage** :
```
╔═══════════════════════════════════════════════════════════════╗
║  📊 Délibération - [NOM Prénom étudiant]                     ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  Type de PV : ○ Standard (Annexe 2)  ○ Simplifié (Annexe 3)  ║
║                                                               ║
║  ┌─────────────────────────────────────────────────────────┐ ║
║  │ Composante                    │ Note      │ Coefficient │ ║
║  ├─────────────────────────────────────────────────────────┤ ║
║  │ Moyenne générale Master 1     │ 12.50     │ × 2         │ ║
║  │ Moyenne S1 Master 2           │ 14.00     │ × 3         │ ║
║  │ Note du Mémoire (Annexe 1)    │ 15.50     │ × 3         │ ║
║  ├─────────────────────────────────────────────────────────┤ ║
║  │ MOYENNE FINALE                │ 14.19 /20 │             │ ║
║  │ MENTION                       │ BIEN      │             │ ║
║  │ DÉCISION                      │ ADMIS     │             │ ║
║  └─────────────────────────────────────────────────────────┘ ║
║                                                               ║
║  [Annuler]            [Valider la délibération]              ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
```

**Actions** :
1. Sélection du type de PV (Standard ou Simplifié)
2. Visualisation du calcul
3. Validation → Création du résultat final
4. Génération des PV (Module 7)

---

## 5. Règles de gestion complètes

### 5.1 Aptitude
| Code | Règle |
|------|-------|
| RG-APT-001 | Seul l'encadreur pédagogique assigné peut valider l'aptitude |
| RG-APT-002 | La validation négative nécessite un commentaire |
| RG-APT-003 | L'aptitude peut être revalidée plusieurs fois |
| RG-APT-004 | L'aptitude validée est requise pour composer le jury |

### 5.2 Jury
| Code | Règle |
|------|-------|
| RG-JUR-001 | Le jury est composé de exactement 5 membres |
| RG-JUR-002 | Chaque rôle est occupé par une personne différente |
| RG-JUR-003 | Directeur et Encadreur sont pré-remplis (non modifiables) |
| RG-JUR-004 | Le maître de stage peut être externe (saisie libre) |
| RG-JUR-005 | Le président doit avoir un grade suffisant (paramétrable) |

### 5.3 Programmation
| Code | Règle |
|------|-------|
| RG-PROG-001 | Une salle ne peut avoir qu'une soutenance par créneau |
| RG-PROG-002 | Un membre de jury ne peut pas être sur 2 soutenances simultanées |
| RG-PROG-003 | La soutenance doit être programmée au moins 7 jours à l'avance |
| RG-PROG-004 | Les créneaux sont entre 08:00 et 18:00 |
| RG-PROG-005 | Une convocation est envoyée à tous les acteurs |

### 5.4 Notation
| Code | Règle |
|------|-------|
| RG-NOT-001 | Chaque note de critère <= barème défini |
| RG-NOT-002 | Le total est la somme arithmétique des notes |
| RG-NOT-003 | Le total ne peut pas dépasser 20 |
| RG-NOT-004 | Les notes sont saisies après la soutenance |

### 5.5 Délibération
| Code | Règle |
|------|-------|
| RG-DEL-001 | La moyenne finale utilise la formule correspondant au type de PV |
| RG-DEL-002 | La mention est attribuée automatiquement selon les seuils |
| RG-DEL-003 | Moyenne >= 10 = Admis, sinon Ajourné |
| RG-DEL-004 | La délibération validée déclenche la génération des PV |

---

## 6. Messages d'erreur

| Code | Message |
|------|---------|
| APT_001 | "Vous n'êtes pas l'encadreur pédagogique de cet étudiant" |
| JUR_001 | "Le jury doit comporter 5 membres distincts" |
| JUR_002 | "Cette personne est déjà membre du jury avec un autre rôle" |
| PROG_001 | "La salle est déjà occupée à ce créneau" |
| PROG_002 | "[Nom] a déjà une soutenance à ce créneau" |
| PROG_003 | "La soutenance doit être programmée au moins 7 jours à l'avance" |
| NOT_001 | "La note dépasse le barème du critère ([X]/[Y])" |
| NOT_002 | "Toutes les notes doivent être saisies" |

---

## 7. Dépendances inter-modules

| Module | Type | Description |
|--------|------|-------------|
| Module 5 (Commission) | Prérequis | Encadrants doivent être assignés |
| Module 2 (Étudiants) | Données | Notes M1 et S1 M2 pour calcul moyenne |
| Module 7 (Documents) | Déclenche | Génération Annexes 1, 2, 3 après délibération |
| Module 1 (Permissions) | Prérequis | Permissions JURY_*, SOUTENANCE_*, etc. |

---

## 8. Écrans récapitulatifs

### 8.1 Espace Encadreur Pédagogique
| Écran | URL | Permission |
|-------|-----|------------|
| Mes étudiants | `/encadreur/etudiants` | APTITUDE_VALIDER |
| Valider aptitude | `/encadreur/etudiants/{id}/aptitude` | APTITUDE_VALIDER |

### 8.2 Espace Administration
| Écran | URL | Permission |
|-------|-----|------------|
| Jurys à composer | `/admin/jurys` | JURY_VOIR |
| Composer jury | `/admin/jurys/{id}/composer` | JURY_COMPOSER |
| Planning soutenances | `/admin/soutenances/planning` | SOUTENANCE_VOIR |
| Programmer | `/admin/soutenances/programmer` | SOUTENANCE_PROGRAMMER |
| Notation | `/admin/soutenances/{id}/notation` | SOUTENANCE_NOTER |
| Délibération | `/admin/soutenances/{id}/deliberation` | DELIBERATION_VALIDER |
| Tableau PDF | `/admin/soutenances/tableau` | SOUTENANCE_VOIR |
| Gestion salles | `/admin/salles` | SALLE_GERER |
| Critères évaluation | `/admin/criteres` | CRITERE_GERER |
