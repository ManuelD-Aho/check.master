# PRD Module 2 : Gestion des Étudiants et Inscriptions

## 1. Vue d'ensemble

### 1.1 Objectif du module
Ce module gère l'intégralité du cycle de vie de l'étudiant dans le système : création du profil, inscription à l'année académique, suivi des paiements de scolarité, saisie des notes (Master 1 et Semestre 1 Master 2).

### 1.2 Workflow principal
```
Création Étudiant → Inscription Année Académique → Paiements Scolarité → Saisie Notes → Génération Compte Utilisateur → Envoi Identifiants
```

### 1.3 Bibliothèques utilisées
| Bibliothèque | Rôle dans ce module |
|--------------|---------------------|
| `doctrine/orm` | Gestion des entités Étudiant, Inscription, Notes |
| `doctrine/dbal` | Requêtes complexes (jointures, agrégations) |
| `respect/validation` | Validation des données saisies |
| `egulias/email-validator` | Validation stricte des emails |
| `nesbot/carbon` | Calcul d'âge, gestion dates de naissance |
| `brick/math` | Calcul précis des moyennes et montants |
| `symfony/string` | Nettoyage des noms (accents, majuscules) |
| `white-october/pagerfanta` | Pagination des listes |
| `league/csv` | Import/Export massif des données |
| `phpmailer/phpmailer` | Envoi des identifiants par email |
| `tecnickcom/tcpdf` | Génération des reçus de paiement |
| `monolog/monolog` | Journalisation des opérations |

---

## 2. Entités et Modèle de données

### 2.1 Schéma relationnel

```
etudiants (1) ──────< (N) inscriptions
     │                       │
     │                       ├──< (N) versements
     │                       │
     │                       └──< (N) echeances
     │
     └──────< (N) notes
                  │
                  ├── ue
                  └── ecue
```

### 2.2 Tables impliquées

#### `etudiants`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `matricule_etudiant` | VARCHAR(20) PK | NOT NULL, UNIQUE | Matricule unique (ex: "ETU2024001") |
| `nom_etudiant` | VARCHAR(100) | NOT NULL | Nom de famille |
| `prenom_etudiant` | VARCHAR(100) | NOT NULL | Prénom(s) |
| `email_etudiant` | VARCHAR(255) | NOT NULL, UNIQUE | Email personnel |
| `telephone_etudiant` | VARCHAR(20) | NULL | Numéro de téléphone |
| `date_naissance` | DATE | NOT NULL | Date de naissance |
| `lieu_naissance` | VARCHAR(100) | NOT NULL | Lieu de naissance |
| `genre` | ENUM('M', 'F') | NOT NULL | Genre |
| `nationalite` | VARCHAR(50) | DEFAULT 'Ivoirienne' | Nationalité |
| `adresse` | TEXT | NULL | Adresse postale |
| `promotion` | VARCHAR(20) | NOT NULL | Promotion (ex: "2024-2025") |
| `photo_profil` | VARCHAR(255) | NULL | Chemin vers photo |
| `id_filiere` | INT FK | NOT NULL | Filière d'appartenance |
| `actif` | BOOLEAN | DEFAULT TRUE | Étudiant actif |
| `date_creation` | DATETIME | NOT NULL | Date de création |
| `date_modification` | DATETIME | NOT NULL | Dernière modification |

**Contraintes** :
- Le matricule suit le format : `ETU` + Année + Numéro séquentiel (5 chiffres)
- L'email doit être validé via `egulias/email-validator`

#### `inscriptions`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_inscription` | INT PK AUTO | NOT NULL | Identifiant unique |
| `matricule_etudiant` | VARCHAR(20) FK | NOT NULL | Référence étudiant |
| `id_niveau_etude` | INT FK | NOT NULL | Niveau (M1, M2) |
| `id_annee_academique` | INT FK | NOT NULL | Année académique |
| `date_inscription` | DATE | NOT NULL | Date d'inscription |
| `statut_inscription` | ENUM | NOT NULL | Statut (voir ci-dessous) |
| `montant_inscription` | DECIMAL(10,2) | NOT NULL | Frais d'inscription |
| `montant_scolarite` | DECIMAL(10,2) | NOT NULL | Montant total scolarité |
| `nombre_tranches` | INT | NOT NULL | Nombre d'échéances prévues |
| `montant_paye` | DECIMAL(10,2) | DEFAULT 0 | Somme des versements |
| `reste_a_payer` | DECIMAL(10,2) | COMPUTED | Calculé automatiquement |
| `date_creation` | DATETIME | NOT NULL | Date de création |
| `date_modification` | DATETIME | NOT NULL | Dernière modification |

**Statuts d'inscription** :
- `en_attente` : Inscription créée, paiement en cours
- `partiel` : Paiement partiel reçu
- `solde` : Scolarité entièrement payée
- `annulee` : Inscription annulée
- `suspendue` : Inscription suspendue

**Contrainte unique** : (matricule_etudiant, id_annee_academique) - Un étudiant ne peut avoir qu'une inscription par année

#### `versements`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_versement` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_inscription` | INT FK | NOT NULL | Référence inscription |
| `montant_versement` | DECIMAL(10,2) | NOT NULL | Montant versé |
| `date_versement` | DATE | NOT NULL | Date du versement |
| `type_versement` | ENUM | NOT NULL | 'inscription', 'scolarite' |
| `methode_paiement` | ENUM | NOT NULL | 'especes', 'cheque', 'virement', 'mobile_money' |
| `reference_paiement` | VARCHAR(100) | NULL | Numéro chèque/transaction |
| `recu_genere` | BOOLEAN | DEFAULT FALSE | Reçu PDF généré |
| `chemin_recu` | VARCHAR(255) | NULL | Chemin fichier reçu |
| `id_utilisateur_saisie` | INT FK | NOT NULL | Qui a saisi le versement |
| `commentaire` | TEXT | NULL | Commentaire libre |
| `date_creation` | DATETIME | NOT NULL | Date de création |

#### `echeances`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_echeance` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_inscription` | INT FK | NOT NULL | Référence inscription |
| `numero_echeance` | INT | NOT NULL | Numéro de la tranche (1, 2, 3...) |
| `montant_echeance` | DECIMAL(10,2) | NOT NULL | Montant attendu |
| `date_echeance` | DATE | NOT NULL | Date limite paiement |
| `statut_echeance` | ENUM | NOT NULL | 'en_attente', 'payee', 'en_retard', 'partielle' |
| `montant_paye` | DECIMAL(10,2) | DEFAULT 0 | Montant effectivement payé |
| `date_paiement` | DATE | NULL | Date de paiement effectif |

#### `notes`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_note` | INT PK AUTO | NOT NULL | Identifiant unique |
| `matricule_etudiant` | VARCHAR(20) FK | NOT NULL | Référence étudiant |
| `id_ue` | INT FK | NULL | UE concernée |
| `id_ecue` | INT FK | NULL | ECUE concernée (optionnel) |
| `id_annee_academique` | INT FK | NOT NULL | Année académique |
| `id_semestre` | INT FK | NOT NULL | Semestre concerné |
| `note` | DECIMAL(4,2) | NULL | Note obtenue (0-20) |
| `type_note` | ENUM | NOT NULL | 'ue', 'ecue', 'moyenne_generale' |
| `commentaire` | TEXT | NULL | Observation |
| `id_utilisateur_saisie` | INT FK | NOT NULL | Qui a saisi |
| `date_creation` | DATETIME | NOT NULL | Date de création |
| `date_modification` | DATETIME | NOT NULL | Dernière modification |

**Contraintes** :
- Note entre 0.00 et 20.00
- Contrainte unique sur (matricule_etudiant, id_ue, id_annee_academique, id_semestre)

#### `annee_academique`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_annee_academique` | INT PK AUTO | NOT NULL | Identifiant unique |
| `libelle_annee` | VARCHAR(20) | NOT NULL, UNIQUE | Ex: "2024-2025" |
| `date_debut` | DATE | NOT NULL | Date début (ex: 01/09/2024) |
| `date_fin` | DATE | NOT NULL | Date fin (ex: 31/08/2025) |
| `est_active` | BOOLEAN | DEFAULT FALSE | Année en cours |
| `est_ouverte` | BOOLEAN | DEFAULT TRUE | Inscriptions ouvertes |
| `date_creation` | DATETIME | NOT NULL | Date de création |

**Règle** : Une seule année académique peut avoir `est_active = TRUE`

#### `niveau_etude`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_niveau_etude` | INT PK AUTO | NOT NULL | Identifiant unique |
| `libelle_niveau` | VARCHAR(50) | NOT NULL | Ex: "Master 1", "Master 2" |
| `code_niveau` | VARCHAR(10) | NOT NULL, UNIQUE | Ex: "M1", "M2" |
| `ordre` | INT | NOT NULL | Ordre de progression |
| `montant_scolarite` | DECIMAL(10,2) | NOT NULL | Montant scolarité par défaut |
| `montant_inscription` | DECIMAL(10,2) | NOT NULL | Frais d'inscription |
| `id_responsable` | INT FK | NULL | Enseignant responsable |

#### `filiere`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_filiere` | INT PK AUTO | NOT NULL | Identifiant unique |
| `libelle_filiere` | VARCHAR(100) | NOT NULL | Ex: "MIAGE", "Génie Logiciel" |
| `code_filiere` | VARCHAR(20) | NOT NULL, UNIQUE | Code court |
| `description` | TEXT | NULL | Description |
| `actif` | BOOLEAN | DEFAULT TRUE | Filière active |

---

## 3. Fonctionnalités détaillées

### 3.1 Gestion de l'année académique

#### 3.1.1 Paramétrage de l'année académique
**Écran** : `/admin/annees-academiques`

**Permission requise** : `ANNEE_ACAD_GESTION`

**Fonctionnalités** :
- Liste des années académiques
- Création d'une nouvelle année
- Définition des dates (début, fin)
- Activation d'une année (désactive automatiquement les autres)
- Ouverture/Fermeture des inscriptions

**Règles de gestion** :
| Code | Règle |
|------|-------|
| RG-AA-001 | Une seule année académique peut être active à la fois |
| RG-AA-002 | L'activation d'une année désactive automatiquement l'année précédente |
| RG-AA-003 | L'année active est utilisée par défaut pour toutes les opérations |
| RG-AA-004 | Les utilisateurs non-admin voient uniquement l'année active |
| RG-AA-005 | L'admin peut consulter les données de toutes les années |

#### 3.1.2 Comportement selon le type d'utilisateur
| Type utilisateur | Accès année académique |
|------------------|------------------------|
| Administrateur | Peut sélectionner n'importe quelle année (menu déroulant) |
| Personnel Admin | Travaille sur l'année active uniquement |
| Enseignant | Travaille sur l'année active uniquement |
| Étudiant | Voit uniquement ses données de l'année active |

### 3.2 Création d'un étudiant

#### 3.2.1 Formulaire de création
**Écran** : `/admin/etudiants/nouveau`

**Permission requise** : `ETU_CREER`

**Champs du formulaire** :
| Champ | Type | Obligatoire | Validation |
|-------|------|-------------|------------|
| Nom | Text | Oui | 2-100 caractères, lettres uniquement |
| Prénom | Text | Oui | 2-100 caractères |
| Email | Email | Oui | Format email valide, unique |
| Téléphone | Tel | Non | Format international |
| Date de naissance | Date | Oui | > 18 ans, < 60 ans |
| Lieu de naissance | Text | Oui | 2-100 caractères |
| Genre | Select | Oui | M ou F |
| Nationalité | Select | Non | Liste prédéfinie |
| Adresse | Textarea | Non | Max 500 caractères |
| Promotion | Text | Oui | Format AAAA-AAAA |
| Filière | Select | Oui | Filières actives uniquement |
| Photo | File | Non | JPG/PNG, max 2Mo, 300x300 min |

**Processus de création** :
1. Validation de tous les champs (respect/validation)
2. Validation email stricte (egulias/email-validator)
3. Nettoyage du nom/prénom (symfony/string) :
   - Suppression espaces superflus
   - Première lettre majuscule
   - Gestion accents
4. Génération matricule automatique
5. Création enregistrement en base
6. Journalisation création (monolog)

**Génération du matricule** :
```
Format : ETU + Année(4) + Séquence(5)
Exemple : ETU202400001

Algorithme :
1. Récupérer l'année en cours
2. Trouver le dernier matricule de l'année
3. Incrémenter la séquence
4. Formater avec padding de zéros
```

#### 3.2.2 Liste des étudiants
**Écran** : `/admin/etudiants`

**Permission requise** : `ETU_VOIR`

**Colonnes** :
- Photo (miniature)
- Matricule
- Nom complet
- Promotion
- Filière
- Statut inscription (année en cours)
- Actions

**Filtres** :
- Par année académique (admin uniquement)
- Par promotion
- Par filière
- Par statut inscription
- Recherche textuelle (matricule, nom, prénom, email)

**Pagination** : 25 étudiants par page (white-october/pagerfanta)

**Export** :
- Bouton "Exporter CSV" → Téléchargement fichier CSV (league/csv)
- Colonnes exportées : Matricule, Nom, Prénom, Email, Téléphone, Promotion, Filière

#### 3.2.3 Fiche étudiant
**Écran** : `/admin/etudiants/{matricule}`

**Permission requise** : `ETU_VOIR`

**Sections** :
1. **Informations personnelles** : Identité, contact, photo
2. **Scolarité** : Inscriptions par année, statuts de paiement
3. **Notes** : Moyennes par semestre, notes par UE
4. **Documents** : Candidature, rapport (liens vers autres modules)
5. **Historique** : Logs des actions sur cette fiche

### 3.3 Inscription à l'année académique

#### 3.3.1 Processus d'inscription
**Écran** : `/admin/etudiants/{matricule}/inscrire`

**Permission requise** : `INSCRIPTION_CREER`

**Prérequis** :
- Étudiant existant et actif
- Année académique ouverte aux inscriptions
- Pas d'inscription existante pour cette année

**Formulaire** :
| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| Année académique | Select (disabled) | Oui | Année active |
| Niveau d'étude | Select | Oui | M1 ou M2 |
| Nombre de tranches | Select | Oui | 1 à 4 |
| Montant inscription | Number (readonly) | Oui | Récupéré du niveau |
| Montant scolarité | Number (readonly) | Oui | Récupéré du niveau |

**Processus** :
1. Vérification des prérequis
2. Création de l'inscription (statut `en_attente`)
3. Génération automatique des échéances
4. Mise à jour du reste à payer
5. Journalisation

**Génération des échéances** :
```
Entrée : montant_scolarite, nombre_tranches, date_inscription
Sortie : Liste des échéances

Algorithme :
1. Calculer montant par tranche = montant_scolarite / nombre_tranches (brick/math pour précision)
2. Pour chaque tranche :
   - date_echeance = date_inscription + (i * 30 jours)
   - montant_echeance = montant par tranche
   - Ajustement dernière tranche pour arrondi
3. Créer les enregistrements echeances
```

#### 3.3.2 Liste des inscriptions
**Écran** : `/admin/inscriptions`

**Permission requise** : `INSCRIPTION_VOIR`

**Colonnes** :
- Matricule étudiant
- Nom complet
- Niveau
- Statut inscription
- Montant total
- Montant payé
- Reste à payer
- Progression (barre visuelle)
- Actions

**Filtres** :
- Par année académique
- Par niveau
- Par statut
- Par statut de paiement (soldé, partiel, aucun)

### 3.4 Gestion des paiements

#### 3.4.1 Enregistrement d'un versement
**Écran** : `/admin/inscriptions/{id}/versement`

**Permission requise** : `VERSEMENT_CREER`

**Formulaire** :
| Champ | Type | Obligatoire | Validation |
|-------|------|-------------|------------|
| Montant | Number | Oui | > 0, <= reste à payer |
| Date versement | Date | Oui | <= aujourd'hui |
| Type | Select | Oui | inscription, scolarite |
| Méthode paiement | Select | Oui | Liste prédéfinie |
| Référence | Text | Conditionnel | Obligatoire si chèque/virement |
| Commentaire | Textarea | Non | Max 500 caractères |

**Processus** :
1. Validation du montant (brick/math)
2. Création du versement
3. Mise à jour inscription :
   - Incrémenter montant_paye
   - Recalculer reste_a_payer
   - Mettre à jour statut_inscription
4. Mise à jour des échéances :
   - Distribuer le versement sur les échéances en cours (FIFO)
   - Marquer échéances comme payées si couvertes
5. Génération reçu PDF (tcpdf)
6. Journalisation

**Règles de gestion paiements** :
| Code | Règle |
|------|-------|
| RG-PAY-001 | Un versement ne peut pas dépasser le reste à payer |
| RG-PAY-002 | Le versement s'impute sur les échéances les plus anciennes d'abord |
| RG-PAY-003 | Un reçu est automatiquement généré pour chaque versement |
| RG-PAY-004 | Les versements ne peuvent pas être supprimés, uniquement annulés |
| RG-PAY-005 | L'annulation génère une écriture inverse |

#### 3.4.2 Génération du reçu de paiement
**Format** : PDF A5

**Contenu** :
- En-tête : Logos université, titre "REÇU DE PAIEMENT"
- Référence : Numéro auto-généré (REC-AAAA-XXXXX)
- Informations étudiant : Matricule, nom, promotion
- Détails paiement :
  - Montant (en chiffres et en lettres)
  - Type (inscription/scolarité)
  - Date
  - Méthode
  - Référence transaction
- Situation après paiement :
  - Total dû
  - Total payé
  - Reste à payer
- Pied : Date génération, signature électronique

**Bibliothèque** : tecnickcom/tcpdf

#### 3.4.3 Suivi des échéances
**Écran** : `/admin/echeances`

**Permission requise** : `ECHEANCE_VOIR`

**Vues** :
1. **Échéances en retard** : date_echeance < aujourd'hui ET statut != 'payee'
2. **Échéances à venir** : dans les 30 prochains jours
3. **Toutes les échéances** : filtrable

**Alertes automatiques** :
- Cron job quotidien pour mettre à jour statut `en_retard`
- Notification email aux étudiants concernés (optionnel, configurable)

### 3.5 Gestion des notes

#### 3.5.1 Contexte des notes dans le workflow
Le système gère 3 types de notes :
1. **Moyenne générale Master 1** : Saisie administrative
2. **Notes UE Semestre 1 Master 2** : Saisie par UE
3. **Note de soutenance** : Calculée automatiquement (Module 6)

#### 3.5.2 Saisie de la moyenne Master 1
**Écran** : `/admin/etudiants/{matricule}/notes/m1`

**Permission requise** : `NOTE_SAISIR`

**Champ** :
- Moyenne générale M1 : Number (0.00 - 20.00, 2 décimales)

**Règles** :
| Code | Règle |
|------|-------|
| RG-NOTE-001 | La moyenne M1 doit être saisie avant le début du M2 |
| RG-NOTE-002 | Seule la moyenne générale est saisie (pas de détail par UE) |
| RG-NOTE-003 | Modification possible tant que le calcul final n'est pas effectué |

#### 3.5.3 Saisie des notes Semestre 1 Master 2
**Écran** : `/admin/notes/s1-m2`

**Permission requise** : `NOTE_SAISIR`

**Interface** : Tableau croisé dynamique

| Étudiant | UE1 | UE2 | UE3 | ... | Moyenne S1 |
|----------|-----|-----|-----|-----|------------|
| DUPONT Jean | 14.5 | 12.0 | 15.5 | ... | 14.00 |
| MARTIN Marie | 16.0 | 14.5 | 13.0 | ... | 14.50 |

**Fonctionnalités** :
- Saisie directe dans le tableau (éditable)
- Calcul automatique de la moyenne pondérée (crédits UE)
- Validation au blur (sauvegarde AJAX)
- Indicateurs visuels : rouge si < 10, vert si >= 10

**Formule moyenne S1** :
```
Moyenne S1 = Σ(Note UE × Crédit UE) / Σ(Crédits)
```

#### 3.5.4 Bulletin de notes provisoire
**Écran** : `/admin/etudiants/{matricule}/bulletin`

**Permission requise** : `BULLETIN_VOIR`

**Génération PDF** :
- En-tête : Logos, année académique, infos étudiant
- Tableau des notes S1 M2 (UE, crédits, notes)
- Moyenne générale S1
- Mention "BULLETIN PROVISOIRE - NON OFFICIEL"
- Date génération

### 3.6 Création automatique du compte utilisateur

#### 3.6.1 Déclenchement
**Événement** : Après saisie de la moyenne M1 ou au moment défini par l'admin

**Processus** :
1. Vérification que l'étudiant n'a pas déjà de compte utilisateur
2. Génération du login : `prenom.nom` (normalisé)
3. Génération du mot de passe : 16 caractères aléatoires
4. Création utilisateur avec :
   - Type = "Étudiant"
   - Groupe = "Étudiants" (groupe par défaut)
   - Statut = "actif"
5. Envoi email avec identifiants (phpmailer)
6. Flag `premiere_connexion = true`
7. Journalisation

#### 3.6.2 Email d'envoi des identifiants
**Sujet** : "[Plateforme MIAGE] Vos identifiants de connexion"

**Contenu** :
```
Bonjour [Prénom] [Nom],

Votre compte sur la plateforme de gestion des stages et soutenances a été créé.

Vos identifiants de connexion :
- Login : [login]
- Mot de passe temporaire : [mot_de_passe]

Lors de votre première connexion, vous serez invité(e) à changer ce mot de passe.

Lien de connexion : [URL]

Cordialement,
L'équipe administrative
```

### 3.7 Import/Export de données

#### 3.7.1 Import CSV des étudiants
**Écran** : `/admin/etudiants/import`

**Permission requise** : `ETU_IMPORT`

**Format CSV attendu** :
```csv
nom;prenom;email;date_naissance;lieu_naissance;genre;promotion;filiere
DUPONT;Jean;jean.dupont@email.com;1995-05-15;Abidjan;M;2024-2025;MIAGE
```

**Processus** :
1. Upload du fichier CSV
2. Parsing avec league/csv
3. Validation de chaque ligne
4. Rapport d'import :
   - Lignes valides → créées
   - Lignes invalides → affichées avec erreur
5. Téléchargement du rapport d'erreurs

**Règles d'import** :
| Code | Règle |
|------|-------|
| RG-IMP-001 | L'import ne modifie pas les étudiants existants |
| RG-IMP-002 | Un email existant rejette la ligne |
| RG-IMP-003 | Maximum 500 lignes par import |
| RG-IMP-004 | Le fichier doit utiliser le séparateur point-virgule |

#### 3.7.2 Export CSV
**Formats disponibles** :
- Liste complète des étudiants
- Liste des inscriptions avec statuts de paiement
- Liste des échéances (toutes ou en retard)
- Notes par promotion

---

## 4. Règles de gestion complètes

### 4.1 Étudiants
| Code | Règle |
|------|-------|
| RG-ETU-001 | Le matricule est généré automatiquement et immuable |
| RG-ETU-002 | L'email doit être unique dans le système |
| RG-ETU-003 | Un étudiant ne peut pas être supprimé, seulement désactivé |
| RG-ETU-004 | L'âge minimum est 18 ans, maximum 60 ans |
| RG-ETU-005 | Le nom et prénom sont normalisés (majuscule première lettre) |
| RG-ETU-006 | La promotion suit le format AAAA-AAAA |

### 4.2 Inscriptions
| Code | Règle |
|------|-------|
| RG-INS-001 | Un étudiant ne peut avoir qu'une inscription par année académique |
| RG-INS-002 | L'inscription nécessite une année académique ouverte |
| RG-INS-003 | Les montants sont récupérés du paramétrage niveau_etude |
| RG-INS-004 | Le nombre de tranches est entre 1 et 4 |
| RG-INS-005 | Le statut passe à "soldé" quand reste_a_payer = 0 |
| RG-INS-006 | Une inscription annulée ne peut pas recevoir de versements |

### 4.3 Paiements
| Code | Règle |
|------|-------|
| RG-PAY-001 | Le montant du versement ne peut excéder le reste à payer |
| RG-PAY-002 | La date de versement ne peut pas être dans le futur |
| RG-PAY-003 | Un reçu PDF est automatiquement généré |
| RG-PAY-004 | Les versements sont répartis sur les échéances par ordre chronologique |
| RG-PAY-005 | Un versement ne peut pas être modifié après 24h |

### 4.4 Notes
| Code | Règle |
|------|-------|
| RG-NOTE-001 | Une note est comprise entre 0.00 et 20.00 |
| RG-NOTE-002 | La précision est de 2 décimales |
| RG-NOTE-003 | La moyenne S1 M2 est pondérée par les crédits des UE |
| RG-NOTE-004 | La modification d'une note après délibération nécessite un motif |
| RG-NOTE-005 | Chaque modification de note est journalisée avec l'auteur |

---

## 5. Messages d'erreur

| Code | Message | Contexte |
|------|---------|----------|
| ETU_001 | "Cet email est déjà utilisé par un autre étudiant" | Création/modification étudiant |
| ETU_002 | "La date de naissance indique un âge non conforme" | Création étudiant |
| ETU_003 | "La filière sélectionnée n'est pas active" | Création étudiant |
| INS_001 | "Cet étudiant est déjà inscrit pour cette année académique" | Création inscription |
| INS_002 | "Les inscriptions sont fermées pour cette année" | Création inscription |
| PAY_001 | "Le montant ne peut pas dépasser le reste à payer ([montant] FCFA)" | Versement |
| PAY_002 | "La date de versement ne peut pas être dans le futur" | Versement |
| NOTE_001 | "La note doit être comprise entre 0 et 20" | Saisie note |
| NOTE_002 | "Cette UE n'est pas rattachée au niveau de l'étudiant" | Saisie note |

---

## 6. Dépendances inter-modules

| Module dépendant | Dépendance | Description |
|------------------|------------|-------------|
| Module 1 (Utilisateurs) | Création compte étudiant | Génération automatique du compte utilisateur |
| Module 3 (Candidatures) | Étudiant existant | La candidature est liée à un étudiant |
| Module 6 (Soutenances) | Notes M1 et S1 M2 | Calcul de la moyenne finale |
| Module 7 (Documents) | Données étudiant | Génération des PV avec infos étudiant |

---

## 7. Écrans récapitulatifs

| Écran | URL | Permission |
|-------|-----|------------|
| Liste étudiants | `/admin/etudiants` | ETU_VOIR |
| Créer étudiant | `/admin/etudiants/nouveau` | ETU_CREER |
| Fiche étudiant | `/admin/etudiants/{matricule}` | ETU_VOIR |
| Modifier étudiant | `/admin/etudiants/{matricule}/modifier` | ETU_MODIFIER |
| Inscrire étudiant | `/admin/etudiants/{matricule}/inscrire` | INSCRIPTION_CREER |
| Liste inscriptions | `/admin/inscriptions` | INSCRIPTION_VOIR |
| Versement | `/admin/inscriptions/{id}/versement` | VERSEMENT_CREER |
| Échéances | `/admin/echeances` | ECHEANCE_VOIR |
| Saisie notes M1 | `/admin/etudiants/{matricule}/notes/m1` | NOTE_SAISIR |
| Tableau notes S1 M2 | `/admin/notes/s1-m2` | NOTE_SAISIR |
| Bulletin provisoire | `/admin/etudiants/{matricule}/bulletin` | BULLETIN_VOIR |
| Import étudiants | `/admin/etudiants/import` | ETU_IMPORT |
| Années académiques | `/admin/annees-academiques` | ANNEE_ACAD_GESTION |
