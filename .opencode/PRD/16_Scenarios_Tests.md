# PRD 16 : Scenarios de Tests

## Vue d'ensemble

Ce document presente les scenarios de tests detailles pour chaque module de la plateforme. Chaque scenario couvre :
- **Happy Path** : Parcours nominal sans erreur
- **Edge Cases** : Cas limites et situations exceptionnelles
- **Tests de Permissions** : Verification des acces autorises/refuses
- **Cas d'Erreur** : Gestion des erreurs et messages

---

# Module 1 : Authentification et Permissions

## SC-AUTH-01 : Connexion Standard (Happy Path)

### Preconditions
- Utilisateur avec compte actif existe en base
- Credentials valides

### Etapes
1. Acceder a `/login`
2. Saisir login valide
3. Saisir mot de passe valide
4. Cliquer sur "Connexion"

### Resultat Attendu
- Redirection vers dashboard correspondant au type utilisateur
- Session creee avec token JWT valide (8h)
- Log audit "connexion_reussie" cree
- Derniere connexion mise a jour

---

## SC-AUTH-02 : Connexion avec 2FA (Happy Path)

### Preconditions
- Utilisateur admin avec 2FA active
- Application authenticator configuree

### Etapes
1. Connexion login/password reussie
2. Redirection vers `/login/2fa`
3. Saisir code TOTP valide (6 chiffres)
4. Valider

### Resultat Attendu
- Acces au dashboard admin
- Session complete creee

---

## SC-AUTH-03 : Tentatives de Connexion Excessives (Edge Case)

### Preconditions
- Utilisateur existant
- Compteur tentatives a 0

### Etapes
1. Saisir login correct, mot de passe incorrect x5

### Resultat Attendu
- Apres tentative 5 : Message "Compte temporairement bloque. Reessayez dans 15 minutes"
- IP bloquee pour 15 minutes
- Entree `auth_rate_limits` creee

---

## SC-AUTH-04 : Blocage Compte apres 10 Tentatives (Edge Case)

### Preconditions
- Utilisateur avec 9 tentatives echouees

### Etapes
1. 10eme tentative echouee

### Resultat Attendu
- statut_utilisateur passe a "bloque"
- Message "Compte desactive. Contactez l'administration"
- Email notification a l'admin (optionnel)

---

## SC-AUTH-05 : Acces sans Permission (Test Permission)

### Preconditions
- Utilisateur connecte (type Etudiant)
- Pas de permission ADMIN_*

### Etapes
1. Tenter d'acceder a `/admin/utilisateurs`

### Resultat Attendu
- HTTP 403 Forbidden
- Redirection vers dashboard etudiant
- Log audit "acces_refuse" avec route tentee

---

## SC-AUTH-06 : Reinitialisation Mot de Passe (Happy Path)

### Preconditions
- Utilisateur avec email valide

### Etapes
1. Acceder a `/mot-de-passe/oublie`
2. Saisir email
3. Soumettre
4. Recevoir email avec lien
5. Cliquer sur lien (valide 1h)
6. Saisir nouveau mot de passe conforme
7. Confirmer

### Resultat Attendu
- Nouveau mot de passe hashé en base
- Token invalidé
- Email confirmation envoyé
- Connexion possible avec nouveau mot de passe

---

## SC-AUTH-07 : Token Reinitialisation Expire (Edge Case)

### Etapes
1. Demander reinitialisation
2. Attendre > 1 heure
3. Cliquer sur lien

### Resultat Attendu
- Message "Ce lien a expire. Veuillez refaire une demande."
- Redirection vers `/mot-de-passe/oublie`

---

# Module 2 : Etudiants et Inscriptions

## SC-ETU-01 : Creation Etudiant (Happy Path)

### Preconditions
- Utilisateur avec permission ETU_CREER

### Etapes
1. Acceder a `/admin/etudiants/nouveau`
2. Remplir tous champs obligatoires
3. Uploader photo (optionnel)
4. Soumettre

### Resultat Attendu
- Etudiant cree avec matricule auto-genere (ETU + annee + sequence)
- Log audit creation
- Redirection vers fiche etudiant

---

## SC-ETU-02 : Email Deja Utilise (Edge Case)

### Preconditions
- Email "dupont@email.com" existe deja

### Etapes
1. Creer etudiant avec email "dupont@email.com"

### Resultat Attendu
- Erreur "Cet email est deja utilise par un autre etudiant"
- Formulaire conserve les autres donnees
- Aucune creation en base

---

## SC-ETU-03 : Age Non Conforme (Edge Case)

### Etapes
1. Saisir date naissance = aujourd'hui - 17 ans

### Resultat Attendu
- Erreur "La date de naissance indique un age non conforme (minimum 18 ans)"

---

## SC-ETU-04 : Inscription Annee Academique (Happy Path)

### Preconditions
- Etudiant existe sans inscription annee active
- Annee academique ouverte

### Etapes
1. Acceder a `/admin/etudiants/{matricule}/inscrire`
2. Selectionner niveau M2
3. Choisir 3 tranches
4. Valider

### Resultat Attendu
- Inscription creee (statut "en_attente")
- 3 echeances generees automatiquement
- Montants recuperes du parametrage niveau

---

## SC-ETU-05 : Inscription Double Annee (Edge Case)

### Preconditions
- Etudiant deja inscrit annee 2024-2025

### Etapes
1. Tenter de creer nouvelle inscription 2024-2025

### Resultat Attendu
- Erreur "Cet etudiant est deja inscrit pour cette annee academique"

---

## SC-ETU-06 : Versement Scolarite (Happy Path)

### Preconditions
- Inscription avec reste_a_payer = 300000

### Etapes
1. Acceder a `/admin/inscriptions/{id}/versement`
2. Saisir montant 100000
3. Selectionner methode "Especes"
4. Valider

### Resultat Attendu
- Versement cree
- montant_paye += 100000
- reste_a_payer = 200000
- Echeances mises a jour (FIFO)
- PDF recu genere automatiquement

---

## SC-ETU-07 : Versement Depassant Reste (Edge Case)

### Preconditions
- reste_a_payer = 50000

### Etapes
1. Saisir montant 100000

### Resultat Attendu
- Erreur "Le montant ne peut pas depasser le reste a payer (50000 FCFA)"

---

## SC-ETU-08 : Generation Compte Utilisateur (Happy Path)

### Preconditions
- Etudiant sans compte utilisateur
- Moyenne M1 saisie

### Etapes
1. Declenchement automatique ou manuel

### Resultat Attendu
- Utilisateur cree (type Etudiant, groupe Etudiants)
- Login genere (prenom.nom normalise)
- Mot de passe aleatoire 16 caracteres
- Email envoye avec credentials
- premiere_connexion = true

---

# Module 3 : Candidatures de Stage

## SC-CAND-01 : Soumission Candidature (Happy Path)

### Preconditions
- Etudiant connecte
- Pas de candidature existante

### Etapes
1. Acceder a `/etudiant/candidature/formulaire`
2. Selectionner ou creer entreprise
3. Remplir informations stage (sujet, dates, description)
4. Remplir coordonnees encadrant
5. Cliquer "Soumettre"

### Resultat Attendu
- Candidature statut "soumise"
- Snapshot JSON cree (historique)
- Email notification au(x) validateur(s)
- Section rapport reste verrouillee

---

## SC-CAND-02 : Duree Stage Insuffisante (Edge Case)

### Etapes
1. Saisir date_debut = 01/02/2025
2. Saisir date_fin = 01/04/2025 (2 mois)
3. Soumettre

### Resultat Attendu
- Erreur "La duree du stage doit etre d'au moins 3 mois"

---

## SC-CAND-03 : Validation Candidature (Happy Path)

### Preconditions
- Validateur avec permission CANDIDATURE_VALIDER
- Candidature en statut "soumise"

### Etapes
1. Acceder a `/admin/candidatures/{id}`
2. Verifier les informations
3. Cliquer "Valider"
4. Confirmer

### Resultat Attendu
- Candidature statut "validee"
- Section rapport DEBLOQUEE pour l'etudiant
- Email confirmation envoye a l'etudiant
- Snapshot JSON cree

---

## SC-CAND-04 : Rejet sans Commentaire (Edge Case)

### Etapes
1. Cliquer "Rejeter"
2. Laisser commentaire vide
3. Valider

### Resultat Attendu
- Erreur "Un commentaire est obligatoire pour rejeter une candidature"

---

## SC-CAND-05 : Re-soumission apres Rejet (Happy Path)

### Preconditions
- Candidature rejetee
- Etudiant a modifie les champs

### Etapes
1. Modifier les informations demandees
2. Cliquer "Re-soumettre"

### Resultat Attendu
- Candidature statut "soumise"
- nombre_soumissions incremente
- Nouveau snapshot JSON
- Email notification validateur

---

## SC-CAND-06 : Acces Rapport sans Candidature Validee (Test Permission)

### Preconditions
- Candidature en statut "brouillon" ou "soumise"

### Etapes
1. Tenter d'acceder a `/etudiant/rapport`

### Resultat Attendu
- Page verrouillee affichee
- Message explicatif avec lien vers candidature

---

# Module 4 : Rapports de Stage

## SC-RAP-01 : Selection Modele et Debut Redaction (Happy Path)

### Preconditions
- Candidature validee
- Pas de rapport existant

### Etapes
1. Acceder a `/etudiant/rapport/nouveau`
2. Selectionner modele "Standard MIAGE"
3. Confirmer

### Resultat Attendu
- Rapport cree (statut "brouillon")
- Editeur affiche avec structure du modele
- Sauvegarde auto demarre

---

## SC-RAP-02 : Sauvegarde Automatique (Happy Path)

### Preconditions
- Rapport en edition

### Etapes
1. Taper du texte
2. Attendre 60 secondes

### Resultat Attendu
- Indicateur "Sauvegarde..." puis "Sauvegarde"
- Version auto_save creee
- date_modification mise a jour

---

## SC-RAP-03 : Soumission Rapport (Happy Path)

### Preconditions
- Contenu >= 5000 mots
- Titre et theme renseignes

### Etapes
1. Cliquer "Soumettre mon rapport"
2. Confirmer

### Resultat Attendu
- Rapport statut "soumis"
- Editeur verrouille
- PDF genere
- Version "soumission" creee
- Email notification verificateur

---

## SC-RAP-04 : Contenu Insuffisant (Edge Case)

### Preconditions
- Contenu = 3000 mots

### Etapes
1. Tenter de soumettre

### Resultat Attendu
- Erreur "Le contenu doit contenir au moins 5000 mots"
- Compteur de mots affiche 3000/5000

---

## SC-RAP-05 : Approbation Rapport (Happy Path)

### Preconditions
- Verificateur avec permission RAPPORT_APPROUVER
- Rapport en statut "soumis"

### Etapes
1. Acceder a `/admin/rapports/{id}/voir`
2. Cocher "J'ai verifie le rapport"
3. Cliquer "Approuver"

### Resultat Attendu
- Rapport statut "approuve"
- Email etudiant envoye
- Eligible pour transfert commission

---

## SC-RAP-06 : Retour pour Correction (Happy Path)

### Preconditions
- Rapport en statut "soumis"

### Etapes
1. Selectionner motif "Fautes d'orthographe"
2. Saisir commentaire >= 50 caracteres
3. Cliquer "Retourner"

### Resultat Attendu
- Rapport statut "retourne"
- Editeur DEVERROUILLE pour etudiant
- Email avec commentaires envoye
- Commentaire type "retour" cree

---

## SC-RAP-07 : Transfert vers Commission (Happy Path)

### Preconditions
- Plusieurs rapports approuves

### Etapes
1. Selectionner rapports
2. Cliquer "Transferer a la commission"

### Resultat Attendu
- Rapports statut "en_commission"
- Email notification membres commission
- Rapports visibles dans espace commission

---

# Module 5 : Commission d'Evaluation

## SC-COM-01 : Evaluation Rapport par Membre (Happy Path)

### Preconditions
- Membre commission connecte
- Rapport en attente d'evaluation

### Etapes
1. Acceder a `/commission/rapports/{id}/evaluer`
2. Visualiser le rapport PDF
3. Selectionner "Favorable"
4. Optionnel: noter 1-5, points forts/ameliorer
5. Soumettre

### Resultat Attendu
- Evaluation enregistree
- Compteur votes incremente (ex: 1/4)
- Autres membres voient progression

---

## SC-COM-02 : Vote Unanime OUI (Happy Path)

### Preconditions
- 3 evaluations OUI deja enregistrees

### Etapes
1. 4eme membre vote OUI

### Resultat Attendu
- Rapport statut "vote_unanime_oui"
- Email felicitations a l'etudiant
- Notification pour assignation encadrants

---

## SC-COM-03 : Vote Unanime NON (Happy Path)

### Preconditions
- 3 evaluations NON

### Etapes
1. 4eme membre vote NON

### Resultat Attendu
- Rapport statut "vote_unanime_non" puis "retourne_etudiant"
- Commentaires compiles
- Email etudiant avec commentaires consolides
- Editeur rapport deverrouille

---

## SC-COM-04 : Vote Non Unanime (Edge Case)

### Preconditions
- Votes: OUI, OUI, NON, OUI (3-1)

### Etapes
1. 4eme vote enregistre

### Resultat Attendu
- Rapport statut "vote_non_unanime"
- Ecran deliberation affiche resultats
- President peut "Relancer le vote"
- Nouveau cycle demarre si relance

---

## SC-COM-05 : Membre Tente Double Vote (Edge Case)

### Preconditions
- Membre a deja vote cycle courant

### Etapes
1. Tenter de soumettre nouvelle evaluation

### Resultat Attendu
- Erreur "Vous avez deja evalue ce rapport pour ce cycle"

---

## SC-COM-06 : Assignation Encadrants (Happy Path)

### Preconditions
- Rapport valide unanimement
- Permission ENCADRANT_ASSIGNER

### Etapes
1. Acceder a `/admin/commission/assignation/{id}`
2. Selectionner directeur memoire
3. Selectionner encadreur pedagogique (membre commission)
4. Valider

### Resultat Attendu
- 2 affectations creees
- Rapport statut "pret_pour_pv"
- Emails envoyes aux 2 enseignants assignes

---

## SC-COM-07 : Meme Personne Directeur et Encadreur (Edge Case)

### Etapes
1. Selectionner Prof. DUPONT comme directeur
2. Selectionner Prof. DUPONT comme encadreur

### Resultat Attendu
- Erreur "Le directeur et l'encadreur ne peuvent pas etre la meme personne"

---

## SC-COM-08 : Encadreur Non Membre Commission (Edge Case)

### Etapes
1. Selectionner enseignant non membre de la commission comme encadreur pedagogique

### Resultat Attendu
- Erreur "L'encadreur pedagogique doit etre membre de la commission"

---

## SC-COM-09 : Generation PV Commission (Happy Path)

### Etapes
1. Acceder a `/admin/commission/pv/nouveau`
2. Selectionner session
3. Selectionner rapports prets
4. Editer contenu
5. Finaliser

### Resultat Attendu
- PV cree avec numero unique
- PDF genere
- Statut "finalise"

---

## SC-COM-10 : Envoi PV (Happy Path)

### Etapes
1. Cliquer "Envoyer"

### Resultat Attendu
- Emails envoyes a:
  - Tous etudiants du PV
  - Membres commission
  - Directeurs memoire
  - Encadreurs pedagogiques
- PV statut "envoye"

---

# Module 6 : Jurys et Soutenances

## SC-JUR-01 : Validation Aptitude (Happy Path)

### Preconditions
- Encadreur pedagogique connecte
- Etudiant avec encadrants assignes

### Etapes
1. Acceder a `/encadreur/etudiants/{id}/aptitude`
2. Selectionner "Apte a soutenir"
3. Valider

### Resultat Attendu
- aptitude validee
- Etudiant eligible pour composition jury
- Notification administration

---

## SC-JUR-02 : Non Aptitude avec Commentaire (Happy Path)

### Etapes
1. Selectionner "Pas encore apte"
2. Saisir commentaire justificatif
3. Valider

### Resultat Attendu
- Email etudiant avec commentaire
- Encadreur peut revalider plus tard

---

## SC-JUR-03 : Composition Jury Complet (Happy Path)

### Preconditions
- Aptitude validee
- Permission JURY_COMPOSER

### Etapes
1. Acceder a `/admin/jurys/{id}/composer`
2. Verifier Directeur (pre-rempli)
3. Verifier Encadreur (pre-rempli)
4. Selectionner President
5. Saisir Maitre de stage
6. Selectionner Examinateur
7. Valider

### Resultat Attendu
- 5 membres assignes
- Jury statut "complet"
- Eligible pour programmation

---

## SC-JUR-04 : Doublon Membre Jury (Edge Case)

### Etapes
1. Selectionner meme personne pour 2 roles

### Resultat Attendu
- Erreur "Cette personne est deja membre du jury avec un autre role"

---

## SC-JUR-05 : Programmation Soutenance (Happy Path)

### Preconditions
- Jury complet
- Permission SOUTENANCE_PROGRAMMER

### Etapes
1. Acceder a `/admin/soutenances/programmer`
2. Selectionner etudiant
3. Choisir date (>= J+7)
4. Choisir heure (09:00)
5. Choisir salle disponible
6. Valider

### Resultat Attendu
- Soutenance creee (statut "programmee")
- 6 emails envoyes (etudiant + 5 jury)
- Apparait dans planning

---

## SC-JUR-06 : Conflit Horaire Salle (Edge Case)

### Preconditions
- Salle A1 occupee 09:00-10:00

### Etapes
1. Tenter programmer 09:30 en Salle A1

### Resultat Attendu
- Erreur "La salle est deja occupee a ce creneau"
- Suggestion alternative affichee

---

## SC-JUR-07 : Conflit Membre Jury (Edge Case)

### Preconditions
- Prof. MARTIN president jury 09:00 Salle A1

### Etapes
1. Programmer autre soutenance 09:00 avec Prof. MARTIN

### Resultat Attendu
- Erreur "Prof. MARTIN a deja une soutenance a ce creneau"

---

## SC-JUR-08 : Notation Soutenance (Happy Path)

### Preconditions
- Soutenance effectuee
- Permission SOUTENANCE_NOTER

### Etapes
1. Acceder a `/admin/soutenances/{id}/notation`
2. Saisir note chaque critere (respectant bareme)
3. Verifier total calcule
4. Enregistrer

### Resultat Attendu
- Notes enregistrees
- Total = somme des notes par critere
- Soutenance statut "notes_saisies"

---

## SC-JUR-09 : Note Depasse Bareme (Edge Case)

### Preconditions
- Bareme critere "Qualite document" = 5

### Etapes
1. Saisir note 6

### Resultat Attendu
- Erreur "La note depasse le bareme du critere (5/5)"

---

## SC-JUR-10 : Deliberation et Calcul Moyenne (Happy Path)

### Preconditions
- Notes saisies
- Permission DELIBERATION_VALIDER

### Etapes
1. Acceder a `/admin/soutenances/{id}/deliberation`
2. Selectionner type PV "Standard"
3. Verifier calcul affiche
4. Valider

### Resultat Attendu
- Moyenne calculee selon formule Annexe 2
- Mention attribuee automatiquement
- Decision "Admis" si >= 10
- PV finaux generes (Annexe 1 + 2 ou 3)
- Resultat statut "valide"

---

# Module 7 : Generation Documents

## SC-DOC-01 : Generation Recu Paiement Automatique

### Declencheur
- Versement enregistre

### Resultat Attendu
- PDF cree (format A5)
- Reference unique (REC-AAAA-XXXXX)
- Chemin stocke en base
- Telechargeable depuis fiche inscription

---

## SC-DOC-02 : Generation Annexe 1 apres Notation

### Declencheur
- Notes soutenance enregistrees

### Resultat Attendu
- PDF Annexe 1 genere
- Contient grille avec notes par critere
- Noms 5 membres jury
- Reference ANX1-AAAA-XXXXX

---

## SC-DOC-03 : Generation PV Final Compile

### Declencheur
- Deliberation validee

### Resultat Attendu
- PDF unique contenant:
  - Annexe 1 (grille notation)
  - Annexe 2 ou 3 (selon type choisi)
- Reference PVF-AAAA-XXXXX
- Pages numerotees

---

# Module 8 : Parametrage

## SC-PARAM-01 : Modification Parametre Application

### Preconditions
- Permission PARAM_APPLICATION

### Etapes
1. Acceder a `/admin/parametres/application`
2. Modifier "Nom application"
3. Sauvegarder

### Resultat Attendu
- Parametre mis a jour
- Cache vide automatiquement
- Log audit cree

---

## SC-PARAM-02 : Test Configuration Email

### Etapes
1. Cliquer "Tester la configuration"

### Resultat Attendu
- Email test envoye a l'admin
- Message succes ou erreur avec details

---

## SC-PARAM-03 : Modification Matrice Permissions

### Etapes
1. Acceder a `/admin/parametres/permissions`
2. Selectionner groupe "Secretariat"
3. Cocher/Decocher permissions
4. Sauvegarder

### Resultat Attendu
- Permissions mises a jour
- Effet immediat pour utilisateurs du groupe
- Log audit cree

---

## SC-PARAM-04 : Activation Mode Maintenance

### Etapes
1. Acceder a `/admin/maintenance/mode`
2. Activer mode maintenance
3. Saisir message
4. Saisir IPs autorisees

### Resultat Attendu
- Tous utilisateurs (sauf IPs) voient page maintenance
- Admin peut toujours naviguer

---

# Resume des Scenarios

| Module | Happy Path | Edge Cases | Permissions | Total |
|--------|------------|------------|-------------|-------|
| Module 1 - Auth | 3 | 3 | 1 | 7 |
| Module 2 - Etudiants | 4 | 3 | 1 | 8 |
| Module 3 - Candidatures | 3 | 2 | 1 | 6 |
| Module 4 - Rapports | 5 | 2 | 0 | 7 |
| Module 5 - Commission | 6 | 4 | 0 | 10 |
| Module 6 - Soutenances | 5 | 4 | 1 | 10 |
| Module 7 - Documents | 3 | 0 | 0 | 3 |
| Module 8 - Parametrage | 4 | 0 | 0 | 4 |
| **TOTAL** | **33** | **18** | **4** | **55** |
