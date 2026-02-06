# PRD 15 : Regles de Gestion Exhaustives

## Vue d'ensemble

Ce document consolide TOUTES les regles de gestion de l'ensemble des modules de la plateforme de gestion des stages et soutenances MIAGE-GI. Chaque regle est identifiee par un code unique et organisee par categorie.

---

## 1. Authentification et Securite (RG-AUTH-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-AUTH-001 | Maximum 5 tentatives de connexion par IP sur 15 minutes | Module 1 |
| RG-AUTH-002 | Maximum 10 tentatives echouees par compte avant blocage automatique | Module 1 |
| RG-AUTH-003 | Un compte bloque necessite l'intervention d'un administrateur pour deblocage | Module 1 |
| RG-AUTH-004 | Le mot de passe doit avoir minimum 8 caracteres, 1 majuscule, 1 chiffre, 1 caractere special | Module 1 |
| RG-AUTH-005 | La session expire apres 8 heures d'inactivite | Module 1 |
| RG-AUTH-006 | L'option "Se souvenir de moi" cree un cookie valide 30 jours | Module 1 |
| RG-AUTH-007 | Le token de reinitialisation de mot de passe est valide 1 heure | Module 1 |
| RG-AUTH-008 | Maximum 3 demandes de reinitialisation par heure par email | Module 1 |

---

## 2. Authentification a Deux Facteurs (RG-2FA-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-2FA-001 | Le 2FA est obligatoire pour le groupe "Administrateur" | Module 1 |
| RG-2FA-002 | Le 2FA est optionnel mais recommande pour les enseignants | Module 1 |
| RG-2FA-003 | Le 2FA n'est pas disponible pour les etudiants | Module 1 |
| RG-2FA-004 | Les codes de recuperation 2FA sont 10 codes a usage unique | Module 1 |
| RG-2FA-005 | Maximum 3 tentatives de code 2FA avant blocage temporaire | Module 1 |
| RG-2FA-006 | Tolerance TOTP : +/-1 periode de 30 secondes | Module 1 |

---

## 3. Groupes Utilisateurs et Permissions (RG-GRP-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-GRP-001 | Un groupe contenant des utilisateurs actifs ne peut pas etre supprime | Module 1 |
| RG-GRP-002 | Le groupe "Administrateur" est immuable (non modifiable) | Module 1 |
| RG-GRP-003 | Un nouveau groupe est cree sans aucune permission par defaut | Module 1 |
| RG-GRP-004 | Un utilisateur appartient a exactement un groupe a la fois | Module 1 |
| RG-GRP-005 | Le changement de groupe d'un utilisateur est effectif immediatement | Module 1 |

---

## 4. Utilisateurs (RG-USR-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-USR-001 | Un utilisateur est lie a une seule entite source (Etudiant OU Enseignant OU Personnel) | Module 1 |
| RG-USR-002 | Le premier mot de passe genere doit etre change a la premiere connexion | Module 1 |
| RG-USR-003 | Le login est unique dans tout le systeme | Module 1 |
| RG-USR-004 | La suppression est logique uniquement (passage au statut 'inactif') | Module 1 |
| RG-USR-005 | La creation d'un utilisateur declenche l'envoi automatique d'un email avec les identifiants | Module 1 |

---

## 5. Audit et Journalisation (RG-AUD-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-AUD-001 | Toute action sensible est journalisee (connexion, modification permissions, etc.) | Module 1 |
| RG-AUD-002 | Les logs d'audit sont non modifiables et non supprimables | Module 1 |
| RG-AUD-003 | La retention des logs est de 5 ans minimum | Module 1 |
| RG-AUD-004 | L'export des logs est autorise uniquement pour les administrateurs | Module 1 |

---

## 6. Annees Academiques (RG-AA-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-AA-001 | Une seule annee academique peut etre active a la fois | Module 2 |
| RG-AA-002 | L'activation d'une annee desactive automatiquement l'annee precedente | Module 2 |
| RG-AA-003 | L'annee active est utilisee par defaut pour toutes les operations | Module 2 |
| RG-AA-004 | Les utilisateurs non-admin voient uniquement l'annee active | Module 2 |
| RG-AA-005 | L'administrateur peut consulter les donnees de toutes les annees | Module 2 |

---

## 7. Etudiants (RG-ETU-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-ETU-001 | Le matricule est genere automatiquement (format ETU+Annee+Sequence) et immuable | Module 2 |
| RG-ETU-002 | L'email doit etre unique dans le systeme | Module 2 |
| RG-ETU-003 | Un etudiant ne peut pas etre supprime, seulement desactive | Module 2 |
| RG-ETU-004 | L'age minimum est 18 ans, maximum 60 ans | Module 2 |
| RG-ETU-005 | Le nom et prenom sont normalises (majuscule premiere lettre) | Module 2 |
| RG-ETU-006 | La promotion suit le format AAAA-AAAA | Module 2 |

---

## 8. Inscriptions (RG-INS-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-INS-001 | Un etudiant ne peut avoir qu'une seule inscription par annee academique | Module 2 |
| RG-INS-002 | L'inscription necessite une annee academique ouverte aux inscriptions | Module 2 |
| RG-INS-003 | Les montants sont recuperes du parametrage niveau_etude | Module 2 |
| RG-INS-004 | Le nombre de tranches est compris entre 1 et 4 | Module 2 |
| RG-INS-005 | Le statut passe a "solde" quand reste_a_payer = 0 | Module 2 |
| RG-INS-006 | Une inscription annulee ne peut pas recevoir de versements | Module 2 |

---

## 9. Paiements et Versements (RG-PAY-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-PAY-001 | Le montant d'un versement ne peut pas exceder le reste a payer | Module 2 |
| RG-PAY-002 | La date de versement ne peut pas etre dans le futur | Module 2 |
| RG-PAY-003 | Un recu PDF est automatiquement genere pour chaque versement | Module 2 |
| RG-PAY-004 | Les versements sont repartis sur les echeances par ordre chronologique (FIFO) | Module 2 |
| RG-PAY-005 | Un versement ne peut pas etre modifie apres 24h | Module 2 |

---

## 10. Notes (RG-NOTE-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-NOTE-001 | Une note est comprise entre 0.00 et 20.00 | Module 2 |
| RG-NOTE-002 | La precision est de 2 decimales | Module 2 |
| RG-NOTE-003 | La moyenne S1 M2 est ponderee par les credits des UE | Module 2 |
| RG-NOTE-004 | La modification d'une note apres deliberation necessite un motif | Module 2 |
| RG-NOTE-005 | Chaque modification de note est journalisee avec l'auteur | Module 2 |

---

## 11. Import de Donnees (RG-IMP-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-IMP-001 | L'import ne modifie pas les etudiants existants (creation uniquement) | Module 2 |
| RG-IMP-002 | Un email existant rejette la ligne d'import | Module 2 |
| RG-IMP-003 | Maximum 500 lignes par import CSV | Module 2 |
| RG-IMP-004 | Le fichier CSV doit utiliser le separateur point-virgule | Module 2 |

---

## 12. Candidatures de Stage (RG-CAND-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-CAND-001 | Un etudiant ne peut avoir qu'une seule candidature par annee academique | Module 3 |
| RG-CAND-002 | La candidature doit etre validee pour debloquer l'acces au rapport de stage | Module 3 |
| RG-CAND-003 | Une candidature validee ne peut plus etre modifiee | Module 3 |
| RG-CAND-004 | Le rejet d'une candidature necessite obligatoirement un commentaire explicatif | Module 3 |
| RG-CAND-005 | La re-soumission n'est possible qu'apres modification effective | Module 3 |
| RG-CAND-006 | Chaque soumission/rejet est historise en JSON (snapshot) | Module 3 |
| RG-CAND-007 | Le validateur ne peut pas traiter sa propre candidature | Module 3 |

---

## 13. Informations de Stage (RG-STG-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-STG-001 | La duree minimale du stage est de 3 mois (90 jours) | Module 3 |
| RG-STG-002 | La date de debut ne peut pas etre dans le passe (a la creation) | Module 3 |
| RG-STG-003 | La date de fin doit etre posterieure a la date de debut | Module 3 |
| RG-STG-004 | Le sujet doit faire au minimum 10 caracteres | Module 3 |
| RG-STG-005 | La description doit faire au minimum 100 caracteres | Module 3 |
| RG-STG-006 | L'email de l'encadrant entreprise doit etre valide et fonctionnel | Module 3 |

---

## 14. Entreprises (RG-ENT-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-ENT-001 | Une entreprise ne peut pas etre supprimee si elle est utilisee dans des stages | Module 3 |
| RG-ENT-002 | La raison sociale doit etre unique | Module 3 |
| RG-ENT-003 | Une entreprise desactivee n'apparait plus dans les recherches/autocompletions | Module 3 |
| RG-ENT-004 | L'etudiant peut creer une nouvelle entreprise si non existante | Module 3 |

---

## 15. Notifications (RG-NOTIF-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-NOTIF-001 | Une notification email est envoyee a chaque changement d'etat majeur | Module 3 |
| RG-NOTIF-002 | Les validateurs sont notifies des nouvelles soumissions | Module 3 |
| RG-NOTIF-003 | L'etudiant recoit toujours le motif de rejet avec le detail | Module 3 |

---

## 16. Rapports de Stage (RG-RAP-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-RAP-001 | Un etudiant ne peut avoir qu'un seul rapport par annee academique | Module 4 |
| RG-RAP-002 | Le rapport necessite une candidature validee pour etre accessible | Module 4 |
| RG-RAP-003 | Le contenu minimum pour soumettre est de 5000 mots | Module 4 |
| RG-RAP-004 | L'editeur se verrouille apres soumission du rapport | Module 4 |
| RG-RAP-005 | Le retour pour correction deverrouille l'editeur | Module 4 |
| RG-RAP-006 | Chaque soumission cree une nouvelle version archivee | Module 4 |
| RG-RAP-007 | Le contenu HTML est systematiquement nettoye (HTMLPurifier) | Module 4 |
| RG-RAP-008 | Les images uploadees sont limitees a 2Mo (JPG/PNG uniquement) | Module 4 |

---

## 17. Validation des Rapports (RG-VAL-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-VAL-001 | Seuls les utilisateurs avec permission RAPPORT_VERIFIER peuvent verifier | Module 4 |
| RG-VAL-002 | Le retour pour correction necessite un commentaire d'au moins 50 caracteres | Module 4 |
| RG-VAL-003 | L'approbation est irreversible (sauf intervention admin) | Module 4 |
| RG-VAL-004 | Le transfert vers la commission peut grouper plusieurs rapports | Module 4 |

---

## 18. Versions des Rapports (RG-VER-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-VER-001 | Sauvegarde automatique du rapport toutes les 60 secondes si modifications | Module 4 |
| RG-VER-002 | Conservation des 10 dernieres auto-saves uniquement | Module 4 |
| RG-VER-003 | Conservation illimitee des versions de soumission | Module 4 |
| RG-VER-004 | Comparaison possible entre differentes versions | Module 4 |

---

## 19. Generation PDF (RG-PDF-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-PDF-001 | Le PDF du rapport est genere a chaque soumission | Module 4 |
| RG-PDF-002 | Format A4, marges 25mm pour tous les documents officiels | Module 4 |
| RG-PDF-003 | Table des matieres generee automatiquement depuis les titres H1/H2/H3 | Module 4 |
| RG-PDF-004 | Numerotation des pages obligatoire sur tous les documents | Module 4 |

---

## 20. Commission d'Evaluation (RG-COM-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-COM-001 | La commission doit avoir exactement 4 membres actifs pour voter | Module 5 |
| RG-COM-002 | Un seul president de commission par annee academique | Module 5 |
| RG-COM-003 | Seuls les membres de la commission peuvent evaluer les rapports | Module 5 |
| RG-COM-004 | Un membre ne peut pas evaluer deux fois le meme rapport dans le meme cycle | Module 5 |

---

## 21. Votes de la Commission (RG-VOT-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-VOT-001 | L'unanimite requiert 4 votes identiques (tous OUI ou tous NON) | Module 5 |
| RG-VOT-002 | Un vote ne peut pas etre modifie apres soumission | Module 5 |
| RG-VOT-003 | Les decisions individuelles sont masquees jusqu'au 4eme vote | Module 5 |
| RG-VOT-004 | Un vote NON necessite un commentaire obligatoire | Module 5 |
| RG-VOT-005 | En cas de non-unanimite (votes mixtes), un nouveau cycle de vote est lance | Module 5 |

---

## 22. Assignation des Encadrants (RG-ASS-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-ASS-001 | Le directeur de memoire et l'encadreur pedagogique doivent etre des personnes differentes | Module 5 |
| RG-ASS-002 | L'encadreur pedagogique doit obligatoirement etre membre de la commission | Module 5 |
| RG-ASS-003 | Les deux roles (directeur + encadreur) sont obligatoires avant finalisation | Module 5 |
| RG-ASS-004 | L'assignation est irreversible sauf intervention admin | Module 5 |

---

## 23. PV de Commission (RG-PV-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-PV-001 | Un rapport ne peut figurer que dans un seul PV de commission | Module 5 |
| RG-PV-002 | Le PV finalise ne peut plus etre modifie | Module 5 |
| RG-PV-003 | L'envoi du PV notifie tous les acteurs concernes (etudiants, enseignants, admin) | Module 5 |
| RG-PV-004 | Le numero de PV est unique et sequentiel par annee | Module 5 |

---

## 24. Aptitude a Soutenir (RG-APT-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-APT-001 | Seul l'encadreur pedagogique assigne peut valider l'aptitude de l'etudiant | Module 6 |
| RG-APT-002 | La validation negative (non apte) necessite un commentaire explicatif | Module 6 |
| RG-APT-003 | L'aptitude peut etre revalidee plusieurs fois (jusqu'a validation positive) | Module 6 |
| RG-APT-004 | L'aptitude validee est requise avant de pouvoir composer le jury | Module 6 |

---

## 25. Composition des Jurys (RG-JUR-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-JUR-001 | Le jury est compose de exactement 5 membres | Module 6 |
| RG-JUR-002 | Chaque role est occupe par une personne differente (aucun doublon) | Module 6 |
| RG-JUR-003 | Le Directeur de Memoire et l'Encadreur Pedagogique sont pre-remplis et non modifiables | Module 6 |
| RG-JUR-004 | Le maitre de stage peut etre externe (saisie libre, non obligatoirement enseignant) | Module 6 |
| RG-JUR-005 | Le president doit avoir un grade suffisant (configurable en parametrage) | Module 6 |

---

## 26. Programmation des Soutenances (RG-PROG-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-PROG-001 | Une salle ne peut avoir qu'une seule soutenance par creneau horaire | Module 6 |
| RG-PROG-002 | Un membre de jury ne peut pas etre sur deux soutenances simultanees | Module 6 |
| RG-PROG-003 | La soutenance doit etre programmee au moins 7 jours a l'avance | Module 6 |
| RG-PROG-004 | Les creneaux de soutenance sont entre 08:00 et 18:00 | Module 6 |
| RG-PROG-005 | Une convocation est envoyee par email a tous les acteurs (etudiant + 5 membres jury) | Module 6 |

---

## 27. Notation des Soutenances (RG-NOT-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-NOT-001 | Chaque note de critere doit etre inferieure ou egale au bareme defini | Module 6 |
| RG-NOT-002 | Le total de la note de soutenance est la somme arithmetique des notes par critere | Module 6 |
| RG-NOT-003 | Le total ne peut pas depasser 20 | Module 6 |
| RG-NOT-004 | Les notes sont saisies apres que la soutenance ait eu lieu | Module 6 |

---

## 28. Deliberation (RG-DEL-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-DEL-001 | La moyenne finale utilise la formule correspondant au type de PV choisi | Module 6 |
| RG-DEL-002 | La mention est attribuee automatiquement selon les seuils definis | Module 6 |
| RG-DEL-003 | Moyenne >= 10 = Admis, sinon Ajourne | Module 6 |
| RG-DEL-004 | La deliberation validee declenche la generation automatique des PV finaux | Module 6 |

---

## 29. Formules de Calcul

### 29.1 Moyenne Finale - Annexe 2 (PV Standard)
```
Coefficient total : 8

Moyenne Finale = ((Moyenne_M1 x 2) + (Moyenne_S1_M2 x 3) + (Note_Memoire x 3)) / 8
```

### 29.2 Moyenne Finale - Annexe 3 (PV Simplifie)
```
Coefficient total : 3

Moyenne Finale = ((Moyenne_M1 x 1) + (Note_Memoire x 2)) / 3
```

### 29.3 Seuils de Mentions
| Mention | Note Minimum | Note Maximum |
|---------|--------------|--------------|
| Passable | 10.00 | 11.99 |
| Assez Bien | 12.00 | 13.99 |
| Bien | 14.00 | 15.99 |
| Tres Bien | 16.00 | 20.00 |

---

## 30. Documents (RG-DOC-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-DOC-001 | Chaque document genere a une reference unique (format TYPE-ANNEE-SEQUENCE) | Module 7 |
| RG-DOC-002 | Les documents officiels sont non modifiables apres generation | Module 7 |
| RG-DOC-003 | Les PV finaux ne peuvent etre generes qu'apres deliberation validee | Module 7 |
| RG-DOC-004 | Les documents sont conserves 5 ans minimum (retention legale) | Module 7 |
| RG-DOC-005 | Un recu de paiement est automatiquement genere apres chaque versement | Module 7 |

---

## 31. Parametrage Systeme (RG-PARAM-*)

| Code | Regle | Module |
|------|-------|--------|
| RG-PARAM-001 | Les parametres sensibles (mot de passe SMTP, etc.) sont chiffres en base | Module 8 |
| RG-PARAM-002 | Toute modification de parametre est journalisee avec auteur et date | Module 8 |
| RG-PARAM-003 | Le mode maintenance bloque l'acces sauf pour les IPs autorisees | Module 8 |
| RG-PARAM-004 | Une seule annee academique peut etre active a la fois | Module 8 |
| RG-PARAM-005 | Les referentiels utilises dans des donnees ne peuvent pas etre supprimes | Module 8 |
| RG-PARAM-006 | Le cache applicatif est vide apres modification de parametres | Module 8 |

---

## 32. Regles Transversales (RG-TRANS-*)

| Code | Regle | Description |
|------|-------|-------------|
| RG-TRANS-001 | Tout fonctionne en fonction des permissions par groupe utilisateur | Principe RBAC |
| RG-TRANS-002 | L'annee academique est un champ en entree pour l'admin, en sortie (filtre auto) pour les autres | Contexte annee |
| RG-TRANS-003 | Aucune modal dans l'UI - navigation par ecrans dedies uniquement | Contrainte UI |
| RG-TRANS-004 | Zero duplication dans les definitions (DRY, single source of truth) | Architecture |
| RG-TRANS-005 | Tout contenu, parametre, libelle configurable doit etre en base de donnees | Data-driven |
| RG-TRANS-006 | L'admin doit pouvoir modifier au maximum sans toucher au code | Configurabilite |

---

## Resume Statistique

| Categorie | Nombre de Regles |
|-----------|------------------|
| Authentification (RG-AUTH) | 8 |
| 2FA (RG-2FA) | 6 |
| Groupes (RG-GRP) | 5 |
| Utilisateurs (RG-USR) | 5 |
| Audit (RG-AUD) | 4 |
| Annees Academiques (RG-AA) | 5 |
| Etudiants (RG-ETU) | 6 |
| Inscriptions (RG-INS) | 6 |
| Paiements (RG-PAY) | 5 |
| Notes (RG-NOTE) | 5 |
| Import (RG-IMP) | 4 |
| Candidatures (RG-CAND) | 7 |
| Stage (RG-STG) | 6 |
| Entreprises (RG-ENT) | 4 |
| Notifications (RG-NOTIF) | 3 |
| Rapports (RG-RAP) | 8 |
| Validation (RG-VAL) | 4 |
| Versions (RG-VER) | 4 |
| PDF (RG-PDF) | 4 |
| Commission (RG-COM) | 4 |
| Votes (RG-VOT) | 5 |
| Assignation (RG-ASS) | 4 |
| PV (RG-PV) | 4 |
| Aptitude (RG-APT) | 4 |
| Jury (RG-JUR) | 5 |
| Programmation (RG-PROG) | 5 |
| Notation (RG-NOT) | 4 |
| Deliberation (RG-DEL) | 4 |
| Documents (RG-DOC) | 5 |
| Parametrage (RG-PARAM) | 6 |
| Transversales (RG-TRANS) | 6 |
| **TOTAL** | **146 regles** |
