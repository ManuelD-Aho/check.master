# PRD 18 : Bloquants et Donnees Manquantes

## Vue d'ensemble

Ce document identifie les elements bloquants, les informations manquantes ou non specifiees, et les points qui necessitent une clarification ou une decision avant l'implementation. Conformement au contexte initial, rien n'a ete invente et les lacunes sont explicitement listees.

---

## 1. Informations Fournies et Completes

Les elements suivants ont ete clairement definis et ne presentent pas de bloquants :

| Element | Source | Statut |
|---------|--------|--------|
| Stack technique (PHP 8.4, HTML, CSS, JS, AJAX) | Contexte | OK |
| Contrainte hebergement (mutualiee, pas de SSH) | Contexte | OK |
| Architecture MVC | Contexte | OK |
| Principe RBAC (permissions par groupe) | Workflow.txt | OK |
| Workflow complet (candidature -> soutenance) | Workflow.txt | OK |
| Composition du jury (5 membres) | Workflow.txt | OK |
| Vote commission (4 membres, unanimite) | Workflow.txt | OK |
| Formules de calcul des moyennes (Annexe 2 et 3) | Workflow.txt | OK |
| Liste complete des bibliotheques PHP | Framework.txt | OK |
| Schema SQL initial | Workflow.txt | OK |
| Documents a generer (9 types) | Workflow.txt | OK |
| Contrainte UI (aucune modal) | Contexte | OK |
| Principe Data-Driven | Contexte | OK |

---

## 2. Elements Manquants - Niveau BLOQUANT

### 2.1 Donnees Visuelles et Assets

| Element | Description | Impact | Action Requise |
|---------|-------------|--------|----------------|
| **Logo UFHB** | Fichier image officiel de l'universite | Bloque generation PDF | Fournir fichier PNG/SVG |
| **Logo UFR MI** | Fichier image de l'UFR | Bloque generation PDF | Fournir fichier PNG/SVG |
| **Bandeau MIAGE-GI** | Image ou texte officiel | Bloque en-tetes documents | Clarifier format attendu |

### 2.2 Configuration SMTP Email

| Element | Description | Impact | Action Requise |
|---------|-------------|--------|----------------|
| **Serveur SMTP** | Host du serveur mail | Aucun email ne sera envoye | Fournir configuration |
| **Port SMTP** | Port (25, 465, 587) | Bloque envoi mails | Fournir valeur |
| **Credentials SMTP** | Login/Password | Bloque envoi mails | Fournir credentials |
| **Adresse expediteur** | Email "From" | Bloque envoi mails | Definir adresse officielle |

### 2.3 Configuration Base de Donnees

| Element | Description | Impact | Action Requise |
|---------|-------------|--------|----------------|
| **Host BDD** | Serveur MySQL/MariaDB | Bloque deploiement | Fournir par hebergeur |
| **Nom BDD** | Nom de la base | Bloque deploiement | Fournir par hebergeur |
| **User BDD** | Utilisateur MySQL | Bloque deploiement | Fournir par hebergeur |
| **Password BDD** | Mot de passe | Bloque deploiement | Fournir par hebergeur |

### 2.4 URL et Domaine

| Element | Description | Impact | Action Requise |
|---------|-------------|--------|----------------|
| **URL Production** | Domaine de l'application | Bloque liens dans emails | Definir domaine final |
| **Chemin de base** | Si sous-repertoire | Impact sur routage | Clarifier si racine ou sous-dossier |

---

## 3. Elements Manquants - Niveau IMPORTANT

### 3.1 Donnees Administratives

| Element | Description | Valeur par Defaut Proposee | Decision Requise |
|---------|-------------|---------------------------|------------------|
| **Annee academique initiale** | Premiere annee a creer | 2024-2025 | Confirmer ou modifier |
| **Date debut annee** | Format academique | 01/09/2024 | Confirmer dates |
| **Date fin annee** | Format academique | 31/08/2025 | Confirmer dates |
| **Montant scolarite M1** | Montant en FCFA | 500 000 FCFA | Confirmer montant |
| **Montant scolarite M2** | Montant en FCFA | 600 000 FCFA | Confirmer montant |
| **Montant inscription** | Frais fixes | 50 000 FCFA | Confirmer montant |
| **Ville par defaut** | Lieu des documents | "Abidjan" | Confirmer |

### 3.2 Utilisateur Initial (Super Admin)

| Element | Description | Action Requise |
|---------|-------------|----------------|
| **Nom admin** | Premier administrateur | Fournir nom complet |
| **Email admin** | Email de connexion | Fournir email valide |
| **Login admin** | Identifiant de connexion | Definir ou generer |

### 3.3 Parametrage Pedagogique

| Element | Description | Valeur par Defaut | Decision Requise |
|---------|-------------|-------------------|------------------|
| **Liste des UE M2 S1** | Matieres du semestre | Non fournie | Fournir liste complete |
| **Credits par UE** | Ponderation | 6 ECTS suggere | Confirmer par UE |
| **Enseignants** | Corps enseignant initial | Non fourni | Fournir liste avec grades |
| **Baremes evaluation** | Note par critere | Suggere (/5, /5, /5, /3, /2 = 20) | Confirmer |

---

## 4. Points d'Ambiguite a Clarifier

### 4.1 Workflow et Regles Metier

| Question | Contexte | Options | Impact |
|----------|----------|---------|--------|
| **Delai minimum stage** | Regle 3 mois specifiee | 90 jours calendaires ou 3 mois pleins? | Validation dates |
| **Resoumission illimitee** | Apres rejet candidature | Limite nombre de tentatives? | Workflow |
| **Archivage rapports** | Conservation des versions | Duree retention auto-saves? | Stockage |
| **Validation aptitude negative** | Encadreur dit "non apte" | Combien de fois peut-il revalider? | Workflow |
| **Annulation soutenance** | Report ou annulation | Quelles conditions autorisent l'annulation? | Workflow |

### 4.2 Commission

| Question | Contexte | Options | Impact |
|----------|----------|---------|--------|
| **Cycle de vote max** | Non-unanimite relance vote | Nombre max de cycles? | Blocage potentiel |
| **Composition commission** | 4 membres requis | Toujours les memes 4 ou variable par rapport? | Affectation |
| **President fixe** | Un seul par annee | Peut-il changer en cours d'annee? | Parametrage |

### 4.3 Jury et Soutenance

| Question | Contexte | Options | Impact |
|----------|----------|---------|--------|
| **Grade minimum president** | "Grade suffisant" mentionne | Quel grade minimum? (Professeur Titulaire?) | Validation |
| **Horaires soutenances** | 08:00-18:00 mentionne | Pause dejeuner a exclure (12:00-14:00)? | Planning |
| **Duree soutenance** | 60 minutes par defaut | Configurable? 45, 60, 90 min? | Programmation |
| **Membre jury externe** | Maitre de stage non enseignant | Comment le notifier sans email systeme? | Email |

### 4.4 Documents

| Question | Contexte | Options | Impact |
|----------|----------|---------|--------|
| **Signature electronique** | Mentionnee dans recus | QR code verification ou simple texte? | Generation PDF |
| **Numerotation PV** | Format sequentiel | Par annee? Par type? Global? | Reference docs |
| **Watermark PROVISOIRE** | Sur bulletins provisoires | Texte simple ou filigrane diagonal? | Generation PDF |

---

## 5. Dependances Externes Non Resolues

### 5.1 Editeur WYSIWYG (Frontend)

| Element | Description | Decision Requise |
|---------|-------------|------------------|
| **Choix editeur** | TinyMCE ou CKEditor mentionne | Choisir l'un des deux |
| **Licence** | Gratuit ou payant? | Verifier conditions usage |
| **Hebergement CDN** | Charger depuis CDN ou local? | Impact performance/disponibilite |

### 5.2 Integration Externe (Optionnel)

| Element | Description | Priorite |
|---------|-------------|----------|
| **Anti-plagiat** | Verification plagiat rapports | Non mentionne - a discuter |
| **Stockage cloud** | Backup documents | Inclus hebergeur ou externe? |

---

## 6. Risques Identifies

### 6.1 Risques Techniques

| Risque | Probabilite | Impact | Mitigation |
|--------|-------------|--------|------------|
| **Hebergeur limite** | Moyenne | Haute | Verifier limites upload, execution, memoire |
| **Volume fichiers PDF** | Moyenne | Moyenne | Prevoir nettoyage periodique |
| **Performance editeur** | Faible | Moyenne | Tester avec longs documents |
| **Deliverabilite emails** | Moyenne | Haute | Configurer SPF/DKIM |

### 6.2 Risques Fonctionnels

| Risque | Probabilite | Impact | Mitigation |
|--------|-------------|--------|------------|
| **Vote commission bloque** | Faible | Haute | Definir timeout ou escalade |
| **Conflits planning** | Moyenne | Moyenne | Interface detection conflits |
| **Perte travail etudiant** | Faible | Haute | Sauvegarde auto + versions |

---

## 7. Hypotheses Retenues (a Valider)

Les hypotheses suivantes ont ete prises pour avancer. Elles doivent etre validees :

| Hypothese | Justification | Validation |
|-----------|---------------|------------|
| Devise = FCFA | Cote d'Ivoire | A confirmer |
| Pays defaut = Cote d'Ivoire | Contexte UFHB | A confirmer |
| Langue = Francais uniquement | Pas de mention multilingue | A confirmer |
| 1 session commission/mois max | Organisation suggere | A confirmer |
| Etudiant ne voit que son annee | Logique metier | A confirmer |
| Enseignant peut etre dans plusieurs jurys | Realiste | A confirmer |
| Rapport max 50 Mo | Limite raisonnable | A confirmer |
| Image rapport max 2 Mo | Mentionne | Valide |

---

## 8. Actions Prioritaires Avant Implementation

### Phase 1 : Bloquants Critiques
1. [ ] Obtenir les logos officiels (UFHB, UFR MI)
2. [ ] Configurer acces SMTP
3. [ ] Obtenir acces base de donnees production
4. [ ] Definir domaine/URL finale

### Phase 2 : Donnees Initiales
5. [ ] Confirmer montants scolarite
6. [ ] Fournir liste UE/ECUE avec credits
7. [ ] Creer compte super administrateur
8. [ ] Definir annee academique initiale

### Phase 3 : Clarifications
9. [ ] Valider baremes criteres evaluation
10. [ ] Confirmer grade minimum president jury
11. [ ] Decider format signature electronique
12. [ ] Choisir editeur WYSIWYG

---

## 9. Resume Statistique

| Categorie | Nombre |
|-----------|--------|
| Elements bloquants critiques | 12 |
| Elements importants a fournir | 14 |
| Points d'ambiguite | 13 |
| Hypotheses a valider | 8 |
| Risques identifies | 6 |
| **Total points a traiter** | **53** |

---

## 10. Contact pour Clarifications

Les questions ci-dessus doivent etre adressees au :
- **Responsable pedagogique** : Questions sur workflow, baremes, UE
- **Service informatique** : Configuration technique, hebergement, SMTP
- **Administration** : Montants, logos officiels, utilisateur initial

---

Voici la mise à jour des réponses aux points bloquants du document **18_Bloquants_Donnees_Manquantes.md**, intégrant vos modifications spécifiques :

---

### 2. Éléments Bloquants (Critiques)

*   **2.1 Logos et Assets :**
    *   **Logo UFHB :** Armoiries officielles (Éléphant/Palmes). Format PNG transparent.
    *   **Logo UFR MI :** Logo spécifique UFR Mathématiques et Informatique.
    *   **Bandeau MIAGE-GI :** Texte officiel : *"UNIVERSITÉ FÉLIX HOUPHOUËT-BOIGNY | UFR MATHÉMATIQUES ET INFORMATIQUE | DÉPARTEMENT MIAGE-GI"*.

*   **2.2 Configuration SMTP :**
    *   **Host :** `mail.ufhb.edu.ci` (ou host hébergeur).
    *   **Port :** `587` (TLS).
    *   **Email Expéditeur :** `noreply-miage@ufhb.edu.ci`.

*   **2.3 Configuration BDD :**
    *   **Charset :** `utf8mb4_unicode_ci`.

*   **2.4 URL et Domaine :**
    *   **URL de développement :** `http://localhost` (à configurer via `APP_URL` dans le `.env`).
    *   **Chemin :** Racine du serveur local.

---

### 3. Éléments Importants (Données Administratives)

*   **3.1 Scolarité et Année :**
    *   **Année initiale :** `2024-2025`.
    *   **Dates :** Début `01/10/2024`, Fin `30/09/2025`.
    *   **Montant M1 :** `500 000 FCFA`.
    *   **Montant M2 :** `600 000 FCFA`.
    *   **Inscription :** `50 000 FCFA`.
    *   **Ville :** `Abidjan`.

*   **3.2 Super Admin Initial :**
    *   **Login :** `admin.miage`.
    *   **Email :** `admin.miage@ufhb.edu.ci`.

---

### 4. Clarifications Workflow

*   **4.1 Stages :**
    *   **Délai minimum :** **Aucun**. (La règle des 90 jours est supprimée, la validation repose sur l'appréciation du sujet par le validateur).
    *   **Ré-soumission :** Illimitée.
    *   **Archivage :** Conservation des 10 dernières auto-saves.
    *   **Aptitude négative :** Re-validation illimitée par l'encadreur.

*   **4.2 Commission :**
    *   **Cycles de vote :** Maximum **3 cycles**.
    *   **Composition :** 4 membres fixes par session mensuelle.

*   **4.3 Jury :**
    *   **Grade Président :** Minimum **Maître de Conférences (MC)**.
    *   **Pause déjeuner :** **Non**. (Les créneaux entre 12:30 et 14:00 restent disponibles pour la programmation des soutenances).
    *   **Membre externe :** Invité par email, pas d'accès au système.

*   **4.4 Documents :**
    *   **Signature :** **QR Code de vérification** unique sur chaque document généré.
    *   **Numérotation PV :** Format `PVC-YYYY-MM-SEQ`.

---

### 5. Dépendances Externes

*   **5.1 Éditeur WYSIWYG :**
    *   **Choix :** `TinyMCE` (version Community).
    *   **Hébergement :** Local (dans `/public/assets/vendors/`).

---

### 7. Hypothèses Retenues (Confirmées)

1.  **Devise :** FCFA (XOF).
2.  **Pays :** Côte d'Ivoire.
3.  **Langue :** Français uniquement.
4.  **Sessions :** 1 session de commission par mois.
5.  **Visibilité :** L'étudiant ne voit que son année d'inscription active.
6.  **Taille Rapport :** Limite technique de **20 Mo**.

---

### Actions Techniques Prioritaires :
1.  Régler `APP_URL=http://localhost` dans le fichier `.env`.
2.  Désactiver la validation de durée minimale (90 jours) dans le `CandidatureValidator`.
3.  Maintenir la continuité des créneaux horaires dans le `PlanningService` (pas d'exclusion méridienne).
