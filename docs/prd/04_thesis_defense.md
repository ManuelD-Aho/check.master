# PRD 04 - Mémoire & Soutenance

**Module**: Gestion des Rapports de Stage et Soutenances  
**Version**: 1.0.0  
**Date**: 2025-12-14

---

## Vue d'Ensemble

Ce module gère l'intégralité du processus de mémoire de stage : soumission de candidature, rédaction et validation du rapport, constitution du jury, planification et déroulement de la soutenance, et suivi des corrections finales. Il couvre le cœur métier de CheckMaster.

---

## Acteurs

| Acteur | Responsabilités |
|--------|-----------------|
| **Étudiant** | Soumet candidature, rédige rapport, dépose corrections |
| **Scolarité** | Vérifie paiement et documents |
| **Communication** | Vérifie format du rapport |
| **Directeur Mémoire** | Supervise le travail scientifique |
| **Encadreur Pédagogique** | Accompagne l'étudiant, donne avis favorable |
| **Président Commission** | Constitue le jury |
| **Membres Jury** | Évaluent la soutenance |
| **Président Jury** | Saisit les notes le jour J |
| **Maître de Stage** | Membre externe du jury |

---

## Scénarios Utilisateurs

### Scénario 1 : Soumission de Candidature
1. L'étudiant accède au formulaire de candidature
2. Renseigne le thème du mémoire
3. Indique l'entreprise et le maître de stage
4. Précise les dates de stage
5. Soumet la candidature
6. Le dossier passe à "Candidature soumise"
7. La Scolarité est notifiée

**Critères d'Acceptation :**
- [ ] Thème de 10 à 500 caractères
- [ ] Email maître de stage valide
- [ ] Dates cohérentes (début < fin)
- [ ] Accusé de réception avec numéro de référence

### Scénario 2 : Rédaction du Rapport
1. L'étudiant dont la candidature est validée accède à "Rédaction"
2. Saisit le contenu structuré (titre, résumé, corps)
3. Le système sauvegarde automatiquement (brouillon)
4. L'étudiant finalise et soumet le rapport
5. Un PDF est généré avec page de garde
6. Le dossier passe à "Rapport soumis"

**Critères d'Acceptation :**
- [ ] Accès uniquement si état = "candidature_validée"
- [ ] Sauvegarde automatique toutes les 30 secondes
- [ ] Génération PDF en moins de 5 secondes
- [ ] Page de garde avec logo et informations étudiant

### Scénario 3 : Constitution du Jury
1. Le Président Commission identifie les candidats validés
2. Recherche des enseignants par spécialité
3. Compose le jury (5 membres + Maître Stage)
4. Envoie les invitations
5. Suit les réponses (acceptation/refus)
6. Remplace les refus par des suppléants
7. Le jury est complet → soutenance planifiable

**Critères d'Acceptation :**
- [ ] 5 membres internes obligatoires
- [ ] Maître de stage en 6ème membre
- [ ] Délai de réponse : 7 jours
- [ ] Suggestion automatique basée sur spécialité

### Scénario 4 : Jour de Soutenance
1. Le matin, le Président Jury reçoit son code d'accès
2. Active son menu temporaire avec le code
3. L'étudiant présente son travail
4. Le Président saisit les notes critère par critère
5. Peut sauvegarder en brouillon pendant la délibération
6. Valide les notes définitivement
7. Le calcul des résultats est automatique

**Critères d'Acceptation :**
- [ ] Code valide uniquement le jour J
- [ ] Grille de notation avec tous les critères
- [ ] Sauvegarde brouillon possible
- [ ] Calcul mention automatique selon barème

### Scénario 5 : Corrections Post-Soutenance
1. Le jury demande des corrections mineures
2. L'étudiant reçoit la liste des corrections
3. Dépose sa version corrigée
4. L'encadreur valide les corrections
5. Le dossier passe à "Diplôme délivré"

**Critères d'Acceptation :**
- [ ] Liste des corrections en texte structuré
- [ ] Délai de correction : 10 jours max
- [ ] Comparaison version initiale/corrigée possible
- [ ] Validation encadreur obligatoire

---

## Requirements Fonctionnels

### RF-030 : Soumission Candidature
**Description** : L'étudiant soumet sa candidature de stage.  
**Acteur** : Étudiant  
**Conditions** : Inscrit pour l'année en cours  
**Résultat** :
- Candidature enregistrée
- Thème, entreprise, maître de stage, dates
- Numéro de référence attribué
- Workflow initié

### RF-031 : Vérification Scolarité
**Description** : La scolarité valide les aspects administratifs.  
**Acteur** : Scolarité  
**Conditions** : Candidature soumise  
**Résultat** :
- Vérification paiement scolarité
- Vérification documents requis
- Validation ou demande de compléments
- Passage à l'étape suivante si OK

### RF-032 : Filtre Communication
**Description** : La Communication vérifie le format du rapport.  
**Acteur** : Communication  
**Conditions** : Scolarité validé, rapport déposé  
**Résultat** :
- Vérification structure (parties obligatoires)
- Vérification mise en forme
- Validation ou retour avec corrections

### RF-033 : Rédaction Rapport (Gate)
**Description** : L'étudiant rédige son rapport dans l'application.  
**Acteur** : Étudiant  
**Conditions** : État = candidature_validée  
**Résultat** :
- Accès à l'éditeur de rapport
- Sauvegarde automatique (brouillon)
- Soumission avec génération PDF
- Versioning des modifications

### RF-034 : Annotations et Corrections
**Description** : Les évaluateurs peuvent annoter le rapport.  
**Acteur** : Membre Commission, Encadreur  
**Conditions** : Rapport soumis  
**Résultat** :
- Annotation par page/position
- Types : Commentaire, Correction, Suggestion
- Visible par l'étudiant après retour

### RF-035 : Attribution Encadreurs
**Description** : Directeur et Encadreur sont attribués au dossier.  
**Acteur** : Commission  
**Conditions** : Rapport validé par commission  
**Résultat** :
- Directeur Mémoire désigné
- Encadreur Pédagogique désigné
- Notification aux intéressés
- Conversation dossier créée

### RF-036 : Avis Favorable Encadreur
**Description** : L'encadreur donne son avis pour la soutenance.  
**Acteur** : Encadreur Pédagogique  
**Conditions** : Travail jugé prêt  
**Résultat** :
- Avis favorable ou défavorable
- Si défavorable : retour corrections
- Si favorable : passage à constitution jury

### RF-037 : Constitution Jury
**Description** : Le jury de soutenance est constitué.  
**Acteur** : Président Commission  
**Conditions** : Avis favorable obtenu  
**Résultat** :
- 5 membres internes + Maître Stage externe
- Rôles : Président, Rapporteur, Examinateurs
- Invitations envoyées
- Suivi des acceptations

### RF-038 : Gestion Absences Jury
**Description** : Les indisponibilités des jurés sont gérées.  
**Acteur** : Membre Jury, Système  
**Conditions** : Jury en constitution  
**Résultat** :
- Déclaration d'indisponibilité
- Pool de suppléants par spécialité
- Remplacement automatique suggéré
- Notification au Président Commission

### RF-039 : Planification Soutenance
**Description** : La date et le lieu de soutenance sont fixés.  
**Acteur** : Président Commission  
**Conditions** : Jury complet (5 acceptations)  
**Résultat** :
- Date, heure, salle
- Vérification conflits automatique
- Réservation salle
- Convocations générées

### RF-040 : Saisie Notes
**Description** : Le Président Jury saisit les notes le jour J.  
**Acteur** : Président Jury  
**Conditions** : Code temporaire activé  
**Résultat** :
- Accès menu temporaire
- Grille par critère (Fond, Forme, Soutenance)
- Mode brouillon disponible
- Validation définitive

### RF-041 : Calcul Résultats
**Description** : Les résultats finaux sont calculés.  
**Acteur** : Système  
**Conditions** : Notes validées  
**Résultat** :
- Moyenne pondérée calculée
- Mention déterminée selon barème
- Notification étudiant avec résultats
- PV de soutenance généré

### RF-042 : Corrections Finales
**Description** : L'étudiant dépose les corrections post-soutenance.  
**Acteur** : Étudiant, Encadreur  
**Conditions** : Corrections demandées par jury  
**Résultat** :
- Dépôt nouvelle version
- Validation par encadreur
- Passage à "Diplôme délivré"

---

## Critères de Succès

| Métrique | Objectif |
|----------|----------|
| Délai candidature → soutenance | < 6 mois |
| Temps génération PDF | < 5 secondes |
| Taux de jury complet au 1er essai | > 80% |
| Saisie notes jour J | 100% réussie |
| Corrections finales | < 10 jours |

---

## Entités Métier

### Dossier Étudiant
- Étudiant associé
- Année académique
- État workflow actuel
- Historique des états

### Candidature
- Dossier parent
- Thème du mémoire
- Entreprise
- Maître de stage (nom, email, téléphone)
- Dates de stage
- Validations scolarité/communication

### Rapport
- Dossier parent
- Titre, Contenu
- Version courante
- Statut (Brouillon, Soumis, En évaluation, Validé, Rejeté)
- Chemin fichier PDF
- Hash intégrité

### Annotation
- Rapport
- Auteur
- Page et position
- Contenu
- Type (Commentaire, Correction, Suggestion)

### Membre Jury
- Dossier/Soutenance
- Enseignant (interne) ou Externe
- Rôle (Président, Rapporteur, Examinateur, Maître Stage)
- Statut invitation (Invité, Accepté, Refusé)
- Présent le jour J

### Soutenance
- Dossier
- Date/Heure
- Salle
- Durée
- Statut (Planifiée, En cours, Terminée, Reportée)
- PV généré

### Notes Soutenance
- Soutenance
- Membre Jury
- Note Fond, Forme, Soutenance
- Note Finale
- Mention
- Commentaire

---

## Cas Limites et Erreurs

| Cas | Comportement |
|-----|--------------|
| Rapport trop volumineux | Limite affichée, compression suggérée |
| Jury incomplet à la date | Report automatique + notification |
| Code président invalide | Refus d'accès, contact admin suggéré |
| Absence membre jour J | Soutenance maintenue si quorum (3/5) |
| Corrections non déposées dans délai | Rappel puis escalade |

---

## Dépendances

- **Module Workflow** : Gestion des états
- **Module Académique** : Étudiants, Enseignants
- **Module Communication** : Notifications, Calendrier
- **Module Documents** : Génération PDF, Archivage

---

## Hors Périmètre

- Rédaction collaborative (multi-auteurs)
- Détection de plagiat
- Visioconférence intégrée
- Signature électronique des membres jury
