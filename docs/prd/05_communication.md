# PRD 05 - Communication

**Module**: Notifications, Messagerie et Calendrier  
**Version**: 1.0.0  
**Date**: 2025-12-14

---

## Vue d'Ensemble

Ce module gère l'ensemble des communications du système : notifications multi-canal (Email, SMS, Messagerie interne), gestion des templates, file d'attente asynchrone, messagerie contextuelle par dossier, et calendrier avec détection de conflits. Il garantit qu'aucune information critique n'est perdue.

---

## Acteurs

| Acteur | Responsabilités |
|--------|-----------------|
| **Utilisateur** | Reçoit et consulte notifications/messages |
| **Administrateur** | Gère templates, surveille bounces |
| **Système** | Envoie notifications, détecte conflits |

---

## Scénarios Utilisateurs

### Scénario 1 : Notification Automatique
1. Un événement se produit (ex: rapport validé)
2. Le système identifie le template correspondant
3. Charge les données dynamiques (nom, date, référence)
4. Envoie par email en priorité
5. Crée une copie en messagerie interne
6. Archive dans l'historique

**Critères d'Acceptation :**
- [ ] Envoi email en moins de 2 minutes
- [ ] Copie messagerie simultanée
- [ ] Variables remplacées correctement
- [ ] Historique consultable

### Scénario 2 : Gestion des Bounces
1. Un email est envoyé à une adresse invalide
2. Le système reçoit le bounce
3. Identifie le type (hard/soft)
4. Hard bounce : bloque l'adresse immédiatement
5. Soft bounce : compte les occurrences (blocage après 5)
6. Active le fallback messagerie interne
7. Alerte l'admin si taux anormal

**Critères d'Acceptation :**
- [ ] Hard bounce → blocage immédiat
- [ ] Soft bounce → blocage après 5 occurrences
- [ ] Fallback messagerie automatique
- [ ] Alerte admin si > 10 bounces/heure

### Scénario 3 : Conversation par Dossier
1. Un encadreur ouvre la conversation du dossier X
2. Voit l'historique des échanges avec l'étudiant et le directeur
3. Rédige un nouveau message
4. Peut joindre un fichier si nécessaire
5. Les participants sont notifiés

**Critères d'Acceptation :**
- [ ] Participants automatiques : étudiant + encadreurs
- [ ] Historique complet visible
- [ ] Notification aux participants
- [ ] Pièces jointes max 10 MB

### Scénario 4 : Détection Conflit Calendrier
1. Le Président planifie une soutenance le 15/01 à 14h
2. Le système vérifie les disponibilités des 5 jurés
3. Détecte que le Juré X a une autre soutenance à 14h30
4. Affiche le conflit avec détails
5. Propose des créneaux alternatifs

**Critères d'Acceptation :**
- [ ] Vérification tous les membres du jury
- [ ] Vérification disponibilité salle
- [ ] Marge de 30 minutes entre soutenances
- [ ] Proposition alternatives automatique

### Scénario 5 : Rappels Automatiques
1. Une soutenance est planifiée pour le 20/01
2. J-7 : rappel à l'étudiant et au jury
3. J-1 : rappel urgent avec détails (lieu, heure)
4. J : code temporaire au Président

**Critères d'Acceptation :**
- [ ] Rappel J-7 par email uniquement
- [ ] Rappel J-1 par email + messagerie
- [ ] Code J par SMS + email
- [ ] Annulation si soutenance reportée

---

## Requirements Fonctionnels

### RF-050 : Templates de Notifications
**Description** : Les notifications utilisent des templates configurables.  
**Acteur** : Administrateur, Système  
**Conditions** : Template existe et est actif  
**Résultat** :
- 71 templates prédéfinis
- Variables dynamiques ({{nom}}, {{date}}, etc.)
- Multi-canal (Email, SMS, Messagerie)
- Activation/désactivation par template

### RF-051 : File d'Attente Asynchrone
**Description** : Les notifications sont traitées en file d'attente.  
**Acteur** : Système  
**Conditions** : Notification à envoyer  
**Résultat** :
- Insertion en file avec priorité
- Traitement par batch (50/minute)
- Retry automatique (3 tentatives max)
- Délai exponentiel entre tentatives

### RF-052 : Multi-Canal
**Description** : Les notifications peuvent utiliser plusieurs canaux.  
**Acteur** : Système  
**Conditions** : Template défini par canal  
**Résultat** :
- Email : canal principal
- Messagerie interne : backup systématique
- SMS : urgences uniquement (codes, J-1)

### RF-053 : Gestion des Bounces
**Description** : Les emails non délivrés sont gérés.  
**Acteur** : Système  
**Conditions** : Bounce reçu  
**Résultat** :
- Classification hard/soft
- Blocage adaptatif
- Fallback messagerie
- Notification admin si anomalie

### RF-054 : Messagerie Interne
**Description** : Communication tracée entre utilisateurs.  
**Acteur** : Utilisateur  
**Conditions** : Authentifié  
**Résultat** :
- Envoi/réception de messages
- Pièces jointes (max 10 MB)
- Indicateur lu/non-lu
- Archivage possible

### RF-055 : Conversations Contextuelles
**Description** : Conversations liées à un dossier spécifique.  
**Acteur** : Participants au dossier  
**Conditions** : Dossier existant  
**Résultat** :
- Création automatique à l'attribution encadreurs
- Participants : étudiant, directeur, encadreur
- Historique consolidé
- Ajout de participants possible

### RF-056 : Gestion Calendrier
**Description** : Les disponibilités et événements sont gérés.  
**Acteur** : Enseignant, Admin  
**Conditions** : Compte existant  
**Résultat** :
- Déclaration créneaux disponibles/indisponibles
- Récurrence possible
- Visualisation planning

### RF-057 : Détection Conflits
**Description** : Les conflits de planification sont détectés.  
**Acteur** : Système  
**Conditions** : Planification d'événement  
**Résultat** :
- Vérification participants
- Vérification salles
- Affichage conflits détaillés
- Suggestions alternatives

### RF-058 : Rappels Automatiques
**Description** : Des rappels sont envoyés avant les échéances.  
**Acteur** : Système  
**Conditions** : Événement planifié  
**Résultat** :
- J-7 : rappel standard
- J-1 : rappel urgent
- J : actions spécifiques (code président)
- Annulation si événement supprimé

### RF-059 : Historique Notifications
**Description** : Toutes les notifications sont historisées.  
**Acteur** : Admin, Utilisateur (ses propres)  
**Conditions** : Notification envoyée  
**Résultat** :
- Statut final (délivré, bounce, échec)
- Horodatage
- Contenu aperçu
- Canal utilisé

---

## Critères de Succès

| Métrique | Objectif |
|----------|----------|
| Délai envoi email | < 2 minutes après événement |
| Taux de délivrabilité | > 98% |
| Fallback messagerie | 100% si email échoue |
| Détection conflit | 100% des cas |
| Temps calcul conflit | < 3 secondes |

---

## Entités Métier

### Template Notification
- Code unique
- Canal (Email, SMS, Messagerie)
- Sujet
- Corps (avec variables)
- Variables attendues (JSON)
- Actif (oui/non)

### File Notifications
- Template
- Destinataire
- Canal
- Variables (JSON)
- Priorité (1-10)
- Statut (en attente, en cours, envoyé, échec)
- Tentatives
- Erreur éventuelle

### Historique Notifications
- Template code
- Destinataire
- Canal
- Statut final
- Horodatage

### Email Bounce
- Adresse email
- Type (Hard, Soft)
- Raison
- Compteur
- Bloqué (oui/non)

### Message Interne
- Expéditeur
- Destinataire
- Sujet
- Contenu
- Lu (oui/non)
- Date lecture

### Conversation
- Sujet
- Type (direct, groupe, dossier, système)
- Contexte (type, ID)
- Dernier message

### Participant Conversation
- Conversation
- Utilisateur
- Rôle
- Dernière lecture
- Notifications actives

### Disponibilité
- Utilisateur
- Dates début/fin
- Type (disponible, indisponible, préférence)
- Récurrence (JSON)

### Conflit Calendrier
- Type (salle, jury, étudiant)
- Soutenances concernées
- Description
- Résolu (oui/non)

---

## Templates Email (71 Types par Phase)

### Phase A - Inscription (6)
- A1: Confirmation inscription
- A2: Reçu de paiement
- A3: Notification pénalité
- A4: Reçu pénalité
- A5: Dossier verrouillé
- A6: Dossier déverrouillé

### Phase B - Candidature (10)
- B1-B10: Soumission, vérifications, validations, rejets

### Phase C - Commission (18)
- C1-C18: Sessions, votes, tours, escalades, PV

### Phase D - Pré-Soutenance (17)
- D1-D17: Avis, jury, invitations, planification

### Phase E - Soutenance (13)
- E1-E13: Rappels, code, notes, résultats, corrections

### Phase F - Réclamations (7)
- F1-F7: Dépôt, traitement, décisions, abandon

---

## Cas Limites et Erreurs

| Cas | Comportement |
|-----|--------------|
| Destinataire sans email | Messagerie interne uniquement |
| Template manquant | Erreur loggée, pas d'envoi |
| SMS échoué | Fallback email + messagerie |
| Conflit non résolvable | Blocage planification avec alerte |
| Conversation supprimée | Messages archivés, non supprimés |

---

## Dépendances

- **Module Authentification** : Utilisateurs et contacts
- **Module Workflow** : Événements déclencheurs
- **Module Audit** : Historisation des envois

---

## Hors Périmètre

- Notifications push mobile
- Intégration Slack/Teams
- Chatbot automatique
- Traduction automatique
