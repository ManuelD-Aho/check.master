# Définition des Menus, Sous-Menus et Écrans

## 1. Structure Générale des Menus

### 1.1 Organisation par type d'utilisateur

| Type Utilisateur | Accès | Menus principaux |
|------------------|-------|------------------|
| Étudiant | Espace Étudiant | Dashboard, Candidature, Rapport, Suivi |
| Enseignant (Commission) | Espace Commission | Évaluations, Votes |
| Enseignant (Encadreur) | Espace Encadreur | Mes Étudiants, Aptitudes |
| Personnel Admin | Back-office | Selon permissions |
| Administrateur | Tout | Tous les menus |

---

## 2. Menus Espace Étudiant

### 2.1 Structure

```
📊 Tableau de bord
   └── /etudiant/dashboard

📝 Ma Candidature
   ├── Formulaire          → /etudiant/candidature/formulaire
   └── Récapitulatif       → /etudiant/candidature

📄 Mon Rapport
   ├── Choisir modèle      → /etudiant/rapport/nouveau     (si pas de rapport)
   ├── Éditeur             → /etudiant/rapport/editeur     (si brouillon/retourné)
   ├── Informations        → /etudiant/rapport/informations
   └── Visualiser          → /etudiant/rapport/voir        (si soumis+)

📈 Mon Suivi
   └── Avancement          → /etudiant/suivi
```

### 2.2 Détail des écrans

| Écran | URL | Description | Conditions d'affichage |
|-------|-----|-------------|------------------------|
| Dashboard | `/etudiant/dashboard` | Vue d'ensemble, statuts, notifications | Toujours |
| Formulaire candidature | `/etudiant/candidature/formulaire` | Saisie infos stage | Candidature brouillon/rejetée |
| Récapitulatif candidature | `/etudiant/candidature` | Vue lecture seule | Candidature soumise+ |
| Choix modèle | `/etudiant/rapport/nouveau` | Sélection template | Pas de rapport existant |
| Éditeur | `/etudiant/rapport/editeur` | WYSIWYG rédaction | Rapport brouillon/retourné |
| Informations rapport | `/etudiant/rapport/informations` | Titre, thème | Rapport existant |
| Visualiser rapport | `/etudiant/rapport/voir` | Lecture seule + PDF | Rapport soumis+ |
| Suivi | `/etudiant/suivi` | Timeline du parcours | Toujours |

---

## 3. Menus Espace Encadreur Pédagogique

### 3.1 Structure

```
👥 Mes Étudiants
   └── Liste               → /encadreur/etudiants

✅ Aptitude à Soutenir
   └── Valider             → /encadreur/etudiants/{id}/aptitude
```

### 3.2 Détail des écrans

| Écran | URL | Description |
|-------|-----|-------------|
| Mes étudiants | `/encadreur/etudiants` | Liste des étudiants dont je suis encadreur |
| Valider aptitude | `/encadreur/etudiants/{id}/aptitude` | Formulaire validation aptitude |

---

## 4. Menus Espace Commission

### 4.1 Structure

```
📋 Rapports à Évaluer
   ├── Mes évaluations     → /commission/rapports
   └── État des votes      → /commission/rapports/{id}/votes

📊 Délibération
   └── Votes non unanimes  → /commission/rapports/{id}/deliberation
```

### 4.2 Détail des écrans

| Écran | URL | Description |
|-------|-----|-------------|
| Mes évaluations | `/commission/rapports` | Rapports à évaluer (4 onglets) |
| Évaluer rapport | `/commission/rapports/{id}/evaluer` | Formulaire d'évaluation |
| État des votes | `/commission/rapports/{id}/votes` | Progression du vote |
| Délibération | `/commission/rapports/{id}/deliberation` | Gestion votes non unanimes |

---

## 5. Menus Back-Office Administration

### 5.1 Structure complète

```
📊 Tableau de bord
   └── /admin/dashboard

👥 GESTION UTILISATEURS
   ├── Utilisateurs
   │   ├── Liste           → /admin/utilisateurs
   │   ├── Créer           → /admin/utilisateurs/nouveau
   │   ├── Voir            → /admin/utilisateurs/{id}
   │   └── Modifier        → /admin/utilisateurs/{id}/modifier
   │
   ├── Groupes utilisateurs
   │   ├── Liste           → /admin/groupes
   │   ├── Créer           → /admin/groupes/nouveau
   │   └── Modifier        → /admin/groupes/{id}/modifier
   │
   └── Permissions
       └── Matrice         → /admin/permissions

🎓 GESTION ÉTUDIANTS
   ├── Étudiants
   │   ├── Liste           → /admin/etudiants
   │   ├── Créer           → /admin/etudiants/nouveau
   │   ├── Voir            → /admin/etudiants/{matricule}
   │   ├── Modifier        → /admin/etudiants/{matricule}/modifier
   │   └── Import CSV      → /admin/etudiants/import
   │
   ├── Inscriptions
   │   ├── Liste           → /admin/inscriptions
   │   └── Inscrire        → /admin/etudiants/{matricule}/inscrire
   │
   ├── Paiements
   │   ├── Versements      → /admin/inscriptions/{id}/versement
   │   └── Échéances       → /admin/echeances
   │
   └── Notes
       ├── Saisie M1       → /admin/etudiants/{matricule}/notes/m1
       └── Tableau S1 M2   → /admin/notes/s1-m2

📋 GESTION STAGES
   ├── Candidatures
   │   ├── À traiter       → /admin/candidatures
   │   ├── Voir            → /admin/candidatures/{id}
   │   ├── Valider         → /admin/candidatures/{id}/valider
   │   └── Rejeter         → /admin/candidatures/{id}/rejeter
   │
   └── Entreprises
       ├── Liste           → /admin/entreprises
       ├── Créer           → /admin/entreprises/nouveau
       └── Modifier        → /admin/entreprises/{id}/modifier

📄 GESTION RAPPORTS
   ├── Vérification
   │   ├── À vérifier      → /admin/rapports/verification
   │   ├── Voir            → /admin/rapports/{id}/voir
   │   └── Approuvés       → /admin/rapports/approuves
   │
   └── Modèles
       └── Gestion         → /admin/modeles-rapport

🏛️ COMMISSION
   ├── Membres
   │   ├── Liste           → /admin/commission/membres
   │   └── Ajouter         → /admin/commission/membres/ajouter
   │
   ├── Assignation
   │   ├── À assigner      → /admin/commission/assignation
   │   └── Assigner        → /admin/commission/assignation/{id}
   │
   └── Comptes-rendus (PV)
       ├── Liste           → /admin/commission/pv
       ├── Créer           → /admin/commission/pv/nouveau
       └── Voir            → /admin/commission/pv/{id}

🎤 SOUTENANCES
   ├── Jurys
   │   ├── Liste           → /admin/jurys
   │   └── Composer        → /admin/jurys/{id}/composer
   │
   ├── Planning
   │   ├── Vue calendrier  → /admin/soutenances/planning
   │   ├── Programmer      → /admin/soutenances/programmer
   │   └── Tableau PDF     → /admin/soutenances/tableau
   │
   ├── Notation
   │   └── Saisir notes    → /admin/soutenances/{id}/notation
   │
   └── Délibération
       └── Valider         → /admin/soutenances/{id}/deliberation

📄 DOCUMENTS
   ├── Reçus               → /admin/documents/recus
   ├── Bulletins           → /admin/documents/bulletins
   └── PV Finaux           → /admin/documents/pv-finaux

⚙️ PARAMÉTRAGE
   ├── Application
   │   ├── Général         → /admin/parametres/application
   │   ├── Email           → /admin/parametres/email
   │   └── Sécurité        → /admin/parametres/securite
   │
   ├── Académique
   │   ├── Années          → /admin/parametres/annees-academiques
   │   ├── Niveaux         → /admin/parametres/niveaux
   │   ├── Semestres       → /admin/parametres/semestres
   │   ├── Filières        → /admin/parametres/filieres
   │   ├── UE              → /admin/parametres/ue
   │   └── ECUE            → /admin/parametres/ecue
   │
   ├── RH
   │   ├── Grades          → /admin/parametres/grades
   │   ├── Fonctions       → /admin/parametres/fonctions
   │   ├── Rôles jury      → /admin/parametres/roles-jury
   │   └── Critères éval   → /admin/parametres/criteres
   │
   ├── Référentiels
   │   ├── Salles          → /admin/parametres/salles
   │   └── Entreprises     → /admin/parametres/entreprises
   │
   ├── Menus
   │   ├── Catégories      → /admin/parametres/menus/categories
   │   ├── Fonctionnalités → /admin/parametres/menus/fonctionnalites
   │   └── Permissions     → /admin/parametres/permissions
   │
   └── Messages
       ├── Libellés        → /admin/parametres/messages/libelles
       └── Templates email → /admin/parametres/messages/emails

🔧 MAINTENANCE
   ├── Audit               → /admin/maintenance/audit
   ├── Statistiques        → /admin/maintenance/statistiques
   ├── Cache               → /admin/maintenance/cache
   └── Mode maintenance    → /admin/maintenance/mode
```

---

## 6. Correspondance Fonctionnalités / Permissions

### 6.1 Table des fonctionnalités (à insérer en base)

| Code | Libellé | Catégorie | URL |
|------|---------|-----------|-----|
| `DASHBOARD` | Tableau de bord | Administration | /admin/dashboard |
| `ETU_LIST` | Liste étudiants | Étudiants | /admin/etudiants |
| `ETU_CREATE` | Créer étudiant | Étudiants | /admin/etudiants/nouveau |
| `ETU_VIEW` | Voir étudiant | Étudiants | /admin/etudiants/{matricule} |
| `ETU_EDIT` | Modifier étudiant | Étudiants | /admin/etudiants/{matricule}/modifier |
| `ETU_IMPORT` | Import étudiants | Étudiants | /admin/etudiants/import |
| `INSCR_LIST` | Liste inscriptions | Inscriptions | /admin/inscriptions |
| `INSCR_CREATE` | Créer inscription | Inscriptions | /admin/etudiants/{}/inscrire |
| `VERS_CREATE` | Créer versement | Paiements | /admin/inscriptions/{}/versement |
| `ECH_LIST` | Liste échéances | Paiements | /admin/echeances |
| `NOTE_EDIT` | Saisir notes | Notes | /admin/notes/* |
| `CAND_LIST` | Liste candidatures | Candidatures | /admin/candidatures |
| `CAND_VIEW` | Voir candidature | Candidatures | /admin/candidatures/{id} |
| `CAND_VALIDATE` | Valider candidature | Candidatures | /admin/candidatures/{}/valider |
| `CAND_REJECT` | Rejeter candidature | Candidatures | /admin/candidatures/{}/rejeter |
| `RAP_VERIF` | Vérifier rapports | Rapports | /admin/rapports/verification |
| `RAP_VIEW` | Voir rapport | Rapports | /admin/rapports/{id}/voir |
| `RAP_APPROVE` | Approuver rapport | Rapports | - |
| `RAP_RETURN` | Retourner rapport | Rapports | - |
| `RAP_TRANSFER` | Transférer rapport | Rapports | /admin/rapports/approuves |
| `COM_MEMBERS` | Gérer membres | Commission | /admin/commission/membres |
| `COM_ASSIGN` | Assigner encadrants | Commission | /admin/commission/assignation |
| `PV_LIST` | Liste PV | Commission | /admin/commission/pv |
| `PV_CREATE` | Créer PV | Commission | /admin/commission/pv/nouveau |
| `JURY_LIST` | Liste jurys | Soutenances | /admin/jurys |
| `JURY_COMPOSE` | Composer jury | Soutenances | /admin/jurys/{}/composer |
| `SOUT_PLANNING` | Planning | Soutenances | /admin/soutenances/planning |
| `SOUT_PROGRAM` | Programmer | Soutenances | /admin/soutenances/programmer |
| `SOUT_NOTE` | Noter soutenance | Soutenances | /admin/soutenances/{}/notation |
| `SOUT_DELIB` | Délibérer | Soutenances | /admin/soutenances/{}/deliberation |
| `DOC_*` | Documents | Documents | /admin/documents/* |
| `PARAM_*` | Paramétrage | Paramétrage | /admin/parametres/* |
| `MAINT_*` | Maintenance | Maintenance | /admin/maintenance/* |

### 6.2 Groupes et permissions par défaut

#### Groupe: Administrateur
- Toutes les permissions (peut_voir, peut_creer, peut_modifier, peut_supprimer = TRUE pour tout)

#### Groupe: Secrétariat
| Fonctionnalité | Voir | Créer | Modifier | Supprimer |
|----------------|------|-------|----------|-----------|
| ETU_* | ✓ | ✓ | ✓ | ✗ |
| INSCR_* | ✓ | ✓ | ✓ | ✗ |
| VERS_* | ✓ | ✓ | ✗ | ✗ |
| CAND_LIST/VIEW | ✓ | ✗ | ✗ | ✗ |

#### Groupe: Responsable Pédagogique
| Fonctionnalité | Voir | Créer | Modifier | Supprimer |
|----------------|------|-------|----------|-----------|
| ETU_* | ✓ | ✗ | ✗ | ✗ |
| NOTE_* | ✓ | ✓ | ✓ | ✗ |
| CAND_* | ✓ | ✗ | ✓ | ✗ |
| RAP_* | ✓ | ✗ | ✓ | ✗ |
| JURY_* | ✓ | ✓ | ✓ | ✗ |
| SOUT_* | ✓ | ✓ | ✓ | ✗ |

#### Groupe: Membre Commission
| Fonctionnalité | Voir | Créer | Modifier | Supprimer |
|----------------|------|-------|----------|-----------|
| COMMISSION_EVAL | ✓ | ✓ | ✗ | ✗ |
| RAP_VIEW | ✓ | ✗ | ✗ | ✗ |

---

## 7. Icônes FontAwesome recommandées

| Catégorie | Icône |
|-----------|-------|
| Tableau de bord | `fa-tachometer-alt` |
| Utilisateurs | `fa-users` |
| Étudiants | `fa-user-graduate` |
| Inscriptions | `fa-clipboard-list` |
| Paiements | `fa-money-bill` |
| Notes | `fa-star` |
| Candidatures | `fa-file-alt` |
| Entreprises | `fa-building` |
| Rapports | `fa-book` |
| Commission | `fa-balance-scale` |
| Soutenances | `fa-microphone` |
| Jurys | `fa-gavel` |
| Planning | `fa-calendar-alt` |
| Documents | `fa-file-pdf` |
| Paramétrage | `fa-cog` |
| Maintenance | `fa-tools` |
