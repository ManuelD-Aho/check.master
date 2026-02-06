# PRD Module 4 : Rédaction et Validation des Rapports de Stage

## 1. Vue d'ensemble

### 1.1 Objectif du module
Ce module permet aux étudiants de rédiger leur rapport de stage directement dans l'application via un éditeur de texte riche intégré. Le rapport passe ensuite par un cycle de validation avant d'être soumis à la commission d'évaluation.

### 1.2 Position dans le workflow global
```
Candidature Validée → RÉDACTION RAPPORT (ce module) → Vérification → Commission → Soutenance
                              ↓
                    [Éditeur intégré + Modèles]
```

### 1.3 Principe clé
> **RÈGLE FONDAMENTALE** : L'étudiant rédige son rapport directement dans l'application. Une fois soumis, l'éditeur se verrouille et le rapport passe en mode lecture seule.

### 1.4 Bibliothèques utilisées
| Bibliothèque | Rôle dans ce module |
|--------------|---------------------|
| `symfony/workflow` | Machine à états du rapport |
| `ezyang/htmlpurifier` | Nettoyage rigoureux du HTML de l'éditeur |
| `phpoffice/phpword` | Conversion du rapport en format Word/PDF |
| `tecnickcom/tcpdf` | Génération PDF du rapport |
| `doctrine/orm` | Gestion des entités rapport, versions |
| `symfony/event-dispatcher` | Événements de changement d'état |
| `phpmailer/phpmailer` | Notifications email |
| `monolog/monolog` | Journalisation des opérations |
| `symfony/string` | Manipulation des contenus texte |
| `white-october/pagerfanta` | Pagination des listes |

---

## 2. Machine à états (Workflow)

### 2.1 États du rapport

```
[brouillon] ──soumettre──> [soumis] ──approuver──> [approuve] ──transferer──> [en_commission]
                              │
                              └──retourner──> [retourne] ──re_soumettre──> [soumis]
```

| État | Code | Description | Éditeur | Actions possibles |
|------|------|-------------|---------|-------------------|
| **Brouillon** | `brouillon` | Rédaction en cours | Éditable | Modifier, Soumettre |
| **Soumis** | `soumis` | En attente de vérification | Verrouillé | Approuver, Retourner |
| **Retourné** | `retourne` | Renvoyé pour correction | Éditable | Modifier, Re-soumettre |
| **Approuvé** | `approuve` | Validé, prêt pour commission | Verrouillé | Transférer |
| **En Commission** | `en_commission` | Transféré pour évaluation | Verrouillé | - (Suite Module 5) |

### 2.2 Transitions

| Transition | De | Vers | Conditions | Actions déclenchées |
|------------|-----|------|------------|---------------------|
| `soumettre` | brouillon | soumis | Contenu minimum atteint | Email vérificateur |
| `approuver` | soumis | approuve | Permission vérificateur | Email étudiant |
| `retourner` | soumis | retourne | Commentaire obligatoire | Email étudiant, déblocage éditeur |
| `re_soumettre` | retourne | soumis | Modifications effectuées | Email vérificateur, nouvelle version |
| `transferer` | approuve | en_commission | Permission commission | Email commission |

### 2.3 Configuration Symfony Workflow

```yaml
# config/workflow/rapport.yaml
framework:
    workflows:
        rapport:
            type: state_machine
            marking_store:
                type: method
                property: statut
            supports:
                - App\Entity\Rapport
            initial_marking: brouillon
            places:
                - brouillon
                - soumis
                - retourne
                - approuve
                - en_commission
            transitions:
                soumettre:
                    from: brouillon
                    to: soumis
                    guard: "subject.hasMinimumContent()"
                approuver:
                    from: soumis
                    to: approuve
                retourner:
                    from: soumis
                    to: retourne
                    guard: "subject.hasCommentaireRetour()"
                re_soumettre:
                    from: retourne
                    to: soumis
                    guard: "subject.hasBeenModified()"
                transferer:
                    from: approuve
                    to: en_commission
```

---

## 3. Entités et Modèle de données

### 3.1 Schéma relationnel

```
etudiants (1) ──────< (1) rapport_etudiants
                              │
                              ├──────< (N) versions_rapport
                              │
                              ├──────< (N) commentaires_rapport
                              │
                              └──────< (N) deposer (historique)
```

### 3.2 Tables impliquées

#### `rapport_etudiants`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_rapport` | INT PK AUTO | NOT NULL | Identifiant unique |
| `matricule_etudiant` | VARCHAR(20) FK | NOT NULL | Référence étudiant |
| `id_annee_academique` | INT FK | NOT NULL | Année académique |
| `titre_rapport` | VARCHAR(255) | NOT NULL | Titre du mémoire |
| `theme_rapport` | VARCHAR(255) | NOT NULL | Thème/sujet |
| `contenu_html` | LONGTEXT | NOT NULL | Contenu HTML de l'éditeur |
| `contenu_texte` | LONGTEXT | COMPUTED | Version texte brut (pour recherche) |
| `statut_rapport` | ENUM | NOT NULL | État du workflow |
| `etape_validation` | INT | DEFAULT 0 | Étape dans le processus |
| `nombre_mots` | INT | COMPUTED | Compteur de mots |
| `nombre_pages_estime` | INT | COMPUTED | Estimation pages |
| `version_courante` | INT | DEFAULT 1 | Numéro de version |
| `chemin_fichier_pdf` | VARCHAR(255) | NULL | PDF généré |
| `taille_fichier` | INT | NULL | Taille en octets |
| `id_modele` | INT FK | NULL | Modèle utilisé |
| `date_creation` | DATETIME | NOT NULL | Date de création |
| `date_modification` | DATETIME | NOT NULL | Dernière modification |
| `date_soumission` | DATETIME | NULL | Date première soumission |
| `date_approbation` | DATETIME | NULL | Date d'approbation |

**Contrainte unique** : (matricule_etudiant, id_annee_academique)

#### `versions_rapport`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_version` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_rapport` | INT FK | NOT NULL | Référence rapport |
| `numero_version` | INT | NOT NULL | Numéro séquentiel |
| `contenu_html` | LONGTEXT | NOT NULL | Snapshot du contenu |
| `type_version` | ENUM | NOT NULL | 'auto_save', 'soumission', 'modification' |
| `id_auteur` | INT FK | NOT NULL | Utilisateur auteur |
| `commentaire` | TEXT | NULL | Note sur la version |
| `date_creation` | DATETIME | NOT NULL | Date de création |

**Contrainte unique** : (id_rapport, numero_version)

#### `modeles_rapport`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_modele` | INT PK AUTO | NOT NULL | Identifiant unique |
| `nom_modele` | VARCHAR(100) | NOT NULL | Nom affiché |
| `description_modele` | TEXT | NULL | Description |
| `contenu_html` | LONGTEXT | NOT NULL | Structure HTML du modèle |
| `miniature` | VARCHAR(255) | NULL | Image preview |
| `ordre_affichage` | INT | DEFAULT 0 | Ordre dans la liste |
| `actif` | BOOLEAN | DEFAULT TRUE | Modèle actif |
| `date_creation` | DATETIME | NOT NULL | Date de création |

#### `commentaires_rapport`
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_commentaire` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_rapport` | INT FK | NOT NULL | Référence rapport |
| `id_auteur` | INT FK | NOT NULL | Utilisateur auteur |
| `contenu_commentaire` | TEXT | NOT NULL | Texte du commentaire |
| `type_commentaire` | ENUM | NOT NULL | 'verification', 'commission', 'retour' |
| `est_public` | BOOLEAN | DEFAULT TRUE | Visible par l'étudiant |
| `date_creation` | DATETIME | NOT NULL | Date de création |

#### `valider` (Actions de validation)
| Champ | Type | Contraintes | Description |
|-------|------|-------------|-------------|
| `id_validation` | INT PK AUTO | NOT NULL | Identifiant unique |
| `id_rapport` | INT FK | NOT NULL | Référence rapport |
| `id_validateur` | INT FK | NOT NULL | Enseignant/Admin validateur |
| `action_validation` | ENUM | NOT NULL | 'approuve', 'retourne' |
| `commentaire_validation` | TEXT | NULL | Commentaire |
| `date_validation` | DATETIME | NOT NULL | Date de l'action |

---

## 4. Fonctionnalités détaillées

### 4.1 Espace Étudiant - Rédaction du rapport

#### 4.1.1 Accès à la section Rapport
**Écran** : `/etudiant/rapport`

**Prérequis** :
- Candidature validée (vérifié par middleware)
- Connexion active

**Affichage conditionnel** :
| État | Affichage |
|------|-----------|
| Pas de rapport | Écran de choix de modèle |
| Brouillon | Éditeur éditable |
| Soumis | Vue lecture seule + statut "En attente" |
| Retourné | Éditeur éditable + commentaires de retour |
| Approuvé | Vue lecture seule + téléchargement PDF |
| En commission | Vue lecture seule + suivi commission |

#### 4.1.2 Choix du modèle (première fois)
**Écran** : `/etudiant/rapport/nouveau`

**Affichage** :
- Grille de modèles disponibles
- Chaque modèle avec :
  - Miniature/Aperçu
  - Nom
  - Description
  - Bouton "Utiliser ce modèle"
- Option "Commencer de zéro" (modèle vide)

**Modèles prédéfinis** (exemples) :
| Modèle | Description |
|--------|-------------|
| Standard MIAGE | Structure complète avec tous les chapitres |
| Simplifié | Structure allégée pour stages courts |
| Recherche | Adapté aux stages R&D |
| Personnalisé | Page blanche avec en-têtes minimum |

**Structure d'un modèle** :
```html
<h1>Titre du Rapport</h1>
<h2>Remerciements</h2>
<p>[Vos remerciements]</p>
<h2>Résumé</h2>
<p>[Résumé en français]</p>
<h2>Abstract</h2>
<p>[Résumé en anglais]</p>
<h2>Introduction</h2>
<p>[Introduction générale]</p>
<h2>Chapitre 1 : Présentation de l'entreprise</h2>
...
```

#### 4.1.3 Éditeur de texte riche
**Écran** : `/etudiant/rapport/editeur`

**Composant** : Éditeur WYSIWYG (TinyMCE ou CKEditor, JS côté client)

**Fonctionnalités de l'éditeur** :
| Catégorie | Fonctionnalités |
|-----------|-----------------|
| **Formatage texte** | Gras, Italique, Souligné, Barré |
| **Titres** | H1, H2, H3, H4 (hiérarchie imposée) |
| **Listes** | Numérotées, À puces |
| **Alignement** | Gauche, Centre, Droite, Justifié |
| **Tableaux** | Insertion, édition cellules |
| **Images** | Upload (limite 2Mo, JPG/PNG) |
| **Liens** | Insertion de liens hypertexte |
| **Citations** | Bloc de citation |
| **Code** | Bloc de code (monospace) |
| **Caractères** | Caractères spéciaux |

**Fonctionnalités interdites** (pour cohérence PDF) :
- Couleurs personnalisées (seulement noir/gris)
- Polices personnalisées (police unique imposée)
- Tailles de police arbitraires

**Barre d'outils** :
```
[Défaire] [Refaire] | [Gras] [Italique] [Souligné] | [H1] [H2] [H3] | 
[Liste num] [Liste puces] | [Aligner gauche] [Centrer] [Justifier] |
[Image] [Tableau] [Lien] | [Rechercher] [Remplacer]
```

**Panneau latéral** :
- Compteur de mots en temps réel
- Estimation du nombre de pages
- Structure du document (sommaire cliquable)
- Dernière sauvegarde

#### 4.1.4 Sauvegarde automatique
**Mécanisme** :
- Sauvegarde AJAX toutes les 60 secondes si modifications
- Sauvegarde au changement de focus (blur)
- Indicateur visuel : "Sauvegardé" / "Sauvegarde en cours..."

**Versioning automatique** :
- Chaque sauvegarde crée une entrée `versions_rapport` (type: auto_save)
- Conservation des 10 dernières auto-saves uniquement
- Les versions de soumission sont conservées indéfiniment

#### 4.1.5 Nettoyage du contenu HTML
**Processus** (à chaque sauvegarde) :

```php
// Configuration HTMLPurifier stricte
$config = HTMLPurifier_Config::createDefault();
$config->set('HTML.Allowed', 
    'h1,h2,h3,h4,p,br,strong,em,u,s,ul,ol,li,table,thead,tbody,tr,th,td,
     blockquote,pre,code,img[src|alt],a[href],figure,figcaption');
$config->set('CSS.AllowedProperties', 
    'text-align,margin-left,margin-right');
$config->set('AutoFormat.RemoveEmpty', true);
$config->set('HTML.TidyLevel', 'heavy');

$purifier = new HTMLPurifier($config);
$cleanHtml = $purifier->purify($dirtyHtml);
```

**Transformations appliquées** :
- Suppression des balises non autorisées
- Suppression des styles inline (sauf alignement)
- Suppression des scripts et événements
- Normalisation des espaces

#### 4.1.6 Métadonnées du rapport
**Écran** : `/etudiant/rapport/informations`

**Champs** :
| Champ | Type | Obligatoire | Validation |
|-------|------|-------------|------------|
| Titre du rapport | Text | Oui | 10-255 caractères |
| Thème | Text | Oui | 10-255 caractères |

Ces informations sont modifiables tant que le rapport est en brouillon ou retourné.

#### 4.1.7 Soumission du rapport
**Action** : Bouton "Soumettre mon rapport"

**Pré-vérifications** :
1. Titre et thème renseignés
2. Contenu minimum : 5000 mots (configurable)
3. Structure minimale : au moins 3 titres H2

**Écran de confirmation** :
```
╔═══════════════════════════════════════════════════════════════╗
║  📄 Confirmer la soumission                                   ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  Vous êtes sur le point de soumettre votre rapport.          ║
║                                                               ║
║  Titre : [Titre du rapport]                                  ║
║  Nombre de mots : [X] mots                                   ║
║  Pages estimées : [Y] pages                                  ║
║                                                               ║
║  ⚠️ Une fois soumis, vous ne pourrez plus modifier votre     ║
║  rapport jusqu'à ce qu'il soit traité.                       ║
║                                                               ║
║  [Annuler]                    [Confirmer la soumission]       ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
```

**Processus** :
1. Nettoyage final du HTML
2. Génération du PDF (tcpdf)
3. Création version (type: soumission)
4. Transition workflow : `brouillon → soumis`
5. Verrouillage de l'éditeur
6. Email notification au vérificateur
7. Affichage confirmation

#### 4.1.8 Vue lecture seule (après soumission)
**Écran** : `/etudiant/rapport/voir`

**Affichage** :
- Rendu HTML du rapport (non éditable)
- Bandeau de statut en haut
- Boutons :
  - "Télécharger PDF" (toujours disponible)
  - "Voir les commentaires" (si présents)

**Bandeaux de statut** :
| Statut | Couleur | Message |
|--------|---------|---------|
| Soumis | Jaune | "Votre rapport est en cours de vérification" |
| Approuvé | Vert | "Votre rapport a été approuvé et transmis à la commission" |
| En commission | Bleu | "Votre rapport est en cours d'évaluation par la commission" |

#### 4.1.9 Retour pour correction
Lorsque le rapport est retourné, l'étudiant :

1. Reçoit un email avec le motif
2. Voit un bandeau rouge sur son espace
3. Accède à nouveau à l'éditeur
4. Voit les commentaires du vérificateur

**Affichage des commentaires** :
```
╔═══════════════════════════════════════════════════════════════╗
║  ⚠️ Rapport retourné pour correction                         ║
╠═══════════════════════════════════════════════════════════════╣
║  Date : [date_retour]                                        ║
║  Par : [nom_verificateur]                                    ║
║                                                               ║
║  Commentaire :                                                ║
║  "[Commentaire détaillé du vérificateur]"                    ║
║                                                               ║
║  [Accéder à l'éditeur pour corriger]                         ║
╚═══════════════════════════════════════════════════════════════╝
```

### 4.2 Espace Vérificateur - Contrôle des rapports

#### 4.2.1 Liste des rapports à vérifier
**Écran** : `/admin/rapports/verification`

**Permission requise** : `RAPPORT_VERIFIER`

**Onglets** :
1. **À vérifier** : statut = 'soumis' (défaut)
2. **Approuvés** : statut = 'approuve'
3. **Retournés** : statut = 'retourne'
4. **Tous**

**Colonnes** :
| Colonne | Description |
|---------|-------------|
| Matricule | Matricule étudiant |
| Étudiant | Nom complet |
| Titre | Titre du rapport (tronqué) |
| Mots | Nombre de mots |
| Soumis le | Date de soumission |
| Version | Numéro de version |
| Actions | Voir, Approuver, Retourner |

**Filtres** :
- Par promotion
- Par période de soumission
- Par nombre de soumissions (première, re-soumission)
- Recherche textuelle

#### 4.2.2 Visualisation d'un rapport
**Écran** : `/admin/rapports/{id}/voir`

**Permission requise** : `RAPPORT_VOIR`

**Interface** :
- Zone principale : Rendu HTML du rapport (scrollable)
- Panneau latéral :
  - Informations rapport (titre, thème, mots, pages)
  - Informations étudiant
  - Historique des versions
  - Commentaires existants
  - Zone d'ajout de commentaire

**Fonctionnalités** :
- Navigation par sommaire (H2/H3)
- Zoom +/-
- Téléchargement PDF
- Comparaison versions (si re-soumission)

#### 4.2.3 Approbation du rapport
**Écran** : Section dans `/admin/rapports/{id}/voir`

**Permission requise** : `RAPPORT_APPROUVER`

**Champs** :
| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| Commentaire | Textarea | Non | Note (visible par l'étudiant) |
| Confirmer | Checkbox | Oui | "J'ai vérifié le rapport" |

**Processus** :
1. Vérification permission
2. Transition workflow : `soumis → approuve`
3. Création entrée dans `valider`
4. Email étudiant (confirmation)
5. Journalisation

#### 4.2.4 Retour pour correction
**Écran** : Section dans `/admin/rapports/{id}/voir`

**Permission requise** : `RAPPORT_RETOURNER`

**Champs** :
| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| Motif | Select | Oui | Liste prédéfinie |
| Commentaire détaillé | Textarea | Oui | Min 50 caractères |

**Motifs prédéfinis** :
- Contenu insuffisant
- Structure inadéquate
- Fautes d'orthographe/grammaire
- Mise en forme incorrecte
- Plagiat détecté
- Autre (préciser)

**Processus** :
1. Vérification commentaire non vide
2. Transition workflow : `soumis → retourne`
3. Création entrée dans `valider`
4. Création commentaire (type: retour)
5. Déblocage éditeur pour l'étudiant
6. Email étudiant avec motif et commentaire
7. Journalisation

### 4.3 Transfert vers la Commission

#### 4.3.1 Liste des rapports approuvés
**Écran** : `/admin/rapports/approuves`

**Permission requise** : `RAPPORT_TRANSFERER`

**Colonnes** :
- Matricule
- Étudiant
- Titre
- Approuvé le
- Par (vérificateur)
- Action : Transférer

**Action groupée** : Sélection multiple + "Transférer les sélectionnés"

#### 4.3.2 Transfert vers commission
**Action** : Bouton "Transférer à la commission"

**Processus** :
1. Vérification permission
2. Pour chaque rapport sélectionné :
   - Transition workflow : `approuve → en_commission`
   - Date de transfert enregistrée
3. Notification email aux membres de la commission
4. Les rapports apparaissent dans l'espace commission (Module 5)

---

## 5. Génération PDF du rapport

### 5.1 Structure du PDF

**Page de garde** :
- Logo université (centré)
- "UNIVERSITÉ FÉLIX HOUPHOUËT-BOIGNY"
- "UFR MATHÉMATIQUES ET INFORMATIQUE"
- "DÉPARTEMENT MIAGE-GI"
- "RAPPORT DE STAGE DE FIN D'ÉTUDES"
- Titre du rapport (centré, gras)
- "Présenté par : [NOM Prénom]"
- "Matricule : [Matricule]"
- "Promotion : [Promotion]"
- "Entreprise d'accueil : [Raison sociale]"
- "Encadrant entreprise : [Nom encadrant]"
- "Année académique : [Année]"

**Contenu** :
- Table des matières (générée automatiquement)
- Contenu du rapport (conversion HTML → PDF)
- Numérotation des pages

### 5.2 Conversion HTML vers PDF

**Bibliothèque** : tecnickcom/tcpdf

**Configuration** :
```php
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetFont('helvetica', '', 12);
$pdf->SetMargins(25, 25, 25);
$pdf->SetAutoPageBreak(true, 25);

// Conversion HTML
$pdf->writeHTML($cleanHtml, true, false, true, false, '');
```

**Mapping des styles** :
| HTML | Rendu PDF |
|------|-----------|
| h1 | Helvetica Bold 18pt |
| h2 | Helvetica Bold 14pt |
| h3 | Helvetica Bold 12pt |
| p | Helvetica 12pt, justifié |
| ul/ol | Indentation 10mm |
| blockquote | Italique, marge gauche |
| table | Bordures fines |
| img | Redimensionnement max 150mm largeur |

---

## 6. Règles de gestion complètes

### 6.1 Rapport
| Code | Règle |
|------|-------|
| RG-RAP-001 | Un étudiant ne peut avoir qu'un seul rapport par année académique |
| RG-RAP-002 | Le rapport nécessite une candidature validée |
| RG-RAP-003 | Le contenu minimum pour soumettre est de 5000 mots |
| RG-RAP-004 | L'éditeur se verrouille après soumission |
| RG-RAP-005 | Le retour pour correction déverrouille l'éditeur |
| RG-RAP-006 | Chaque soumission crée une nouvelle version |
| RG-RAP-007 | Le contenu HTML est systématiquement nettoyé |
| RG-RAP-008 | Les images uploadées sont limitées à 2Mo |

### 6.2 Validation
| Code | Règle |
|------|-------|
| RG-VAL-001 | Seuls les utilisateurs avec permission peuvent vérifier |
| RG-VAL-002 | Le retour nécessite un commentaire d'au moins 50 caractères |
| RG-VAL-003 | L'approbation est irréversible (sauf par admin) |
| RG-VAL-004 | Le transfert groupe les rapports pour la commission |

### 6.3 Versions
| Code | Règle |
|------|-------|
| RG-VER-001 | Sauvegarde automatique toutes les 60 secondes |
| RG-VER-002 | Conservation des 10 dernières auto-saves |
| RG-VER-003 | Conservation illimitée des versions de soumission |
| RG-VER-004 | Comparaison possible entre versions |

### 6.4 PDF
| Code | Règle |
|------|-------|
| RG-PDF-001 | Le PDF est généré à chaque soumission |
| RG-PDF-002 | Format A4, marges 25mm |
| RG-PDF-003 | Table des matières générée automatiquement |
| RG-PDF-004 | Numérotation des pages obligatoire |

---

## 7. Messages d'erreur et de succès

### 7.1 Erreurs
| Code | Message |
|------|---------|
| RAP_001 | "Votre candidature doit être validée pour accéder à cette section" |
| RAP_002 | "Le contenu doit contenir au moins 5000 mots" |
| RAP_003 | "Veuillez renseigner le titre et le thème du rapport" |
| RAP_004 | "L'image dépasse la taille maximale autorisée (2 Mo)" |
| RAP_005 | "Format d'image non supporté (JPG ou PNG uniquement)" |
| RAP_006 | "Vous ne pouvez pas modifier un rapport soumis" |
| RAP_007 | "Un commentaire est obligatoire pour retourner un rapport" |

### 7.2 Succès
| Code | Message |
|------|---------|
| RAP_S01 | "Rapport sauvegardé automatiquement" |
| RAP_S02 | "Votre rapport a été soumis avec succès" |
| RAP_S03 | "Le rapport a été approuvé" |
| RAP_S04 | "Le rapport a été retourné pour correction" |
| RAP_S05 | "Le rapport a été transféré à la commission" |

---

## 8. Événements déclenchés

| Événement | Déclencheur | Actions |
|-----------|-------------|---------|
| `rapport.created` | Création rapport | Log audit |
| `rapport.saved` | Sauvegarde | Mise à jour date_modification |
| `rapport.submitted` | Soumission | Génération PDF, Email, Log |
| `rapport.approved` | Approbation | Email étudiant, Log |
| `rapport.returned` | Retour | Déblocage éditeur, Email, Log |
| `rapport.transferred` | Transfert | Notif commission, Log |

---

## 9. Dépendances inter-modules

| Module | Type | Description |
|--------|------|-------------|
| Module 3 (Candidatures) | Prérequis | Candidature doit être validée |
| Module 1 (Permissions) | Prérequis | Permissions RAPPORT_* requises |
| Module 5 (Commission) | Déclenche | Le transfert envoie à la commission |
| Module 7 (Documents) | Utilise | Génération du rapport PDF |

---

## 10. Écrans récapitulatifs

### 10.1 Espace Étudiant
| Écran | URL | Condition |
|-------|-----|-----------|
| Choix modèle | `/etudiant/rapport/nouveau` | Pas de rapport existant |
| Éditeur | `/etudiant/rapport/editeur` | Brouillon ou Retourné |
| Informations | `/etudiant/rapport/informations` | Rapport existant |
| Vue lecture | `/etudiant/rapport/voir` | Soumis ou Approuvé |

### 10.2 Espace Administration
| Écran | URL | Permission |
|-------|-----|------------|
| Rapports à vérifier | `/admin/rapports/verification` | RAPPORT_VERIFIER |
| Voir rapport | `/admin/rapports/{id}/voir` | RAPPORT_VOIR |
| Rapports approuvés | `/admin/rapports/approuves` | RAPPORT_TRANSFERER |
| Modèles de rapport | `/admin/modeles-rapport` | MODELE_GESTION |
