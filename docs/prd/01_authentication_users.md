# PRD 01 - Authentification & Utilisateurs

**Module**: Sécurité et Gestion des Accès  
**Version**: 1.0.0  
**Date**: 2025-12-14

---

## Vue d'Ensemble

Ce module gère l'authentification sécurisée des utilisateurs, la gestion des sessions multi-appareils, le système de permissions granulaires (RBAC), et les rôles temporaires contextuels. Il constitue le socle de sécurité de l'ensemble du système CheckMaster.

---

## Acteurs

| Acteur | Responsabilités |
|--------|-----------------|
| **Utilisateur** | Se connecte, gère ses sessions, change son mot de passe |
| **Administrateur** | Crée/modifie utilisateurs, gère groupes, force déconnexion |
| **Président Jury** | Reçoit et utilise code temporaire jour J |
| **Système** | Génère codes, invalide sessions, applique verrouillages |

---

## Scénarios Utilisateurs

### Scénario 1 : Connexion Standard
1. L'utilisateur accède à la page de connexion
2. Saisit son identifiant (email) et mot de passe
3. Le système vérifie les identifiants
4. En cas de succès, une session est créée
5. L'utilisateur est redirigé vers son tableau de bord
6. Le menu affiché correspond à ses permissions

**Critères d'Acceptation :**
- [ ] Connexion réussie en moins de 2 secondes
- [ ] Session stockée avec IP et User-Agent
- [ ] Dernière connexion mise à jour
- [ ] Redirection selon le rôle principal

### Scénario 2 : Protection Brute-Force
1. Un attaquant tente des connexions avec mots de passe incorrects
2. Après 3 échecs : délai d'attente de 1 minute
3. Après 5 échecs : délai de 15 minutes
4. Après 10 échecs : compte verrouillé, admin alerté

**Critères d'Acceptation :**
- [ ] Compteur d'échecs incrémenté à chaque tentative
- [ ] Compte verrouillé après 10 tentatives
- [ ] Notification admin par email et messagerie
- [ ] Déblocage possible par admin uniquement

### Scénario 3 : Code Temporaire Président Jury
1. Une soutenance est planifiée pour le jour J
2. Le matin du jour J, le système génère un code à 8 caractères
3. Le code est envoyé au Président Jury par SMS et email
4. Le Président utilise ce code pour accéder au menu temporaire
5. À 23h59, le code et les droits temporaires sont révoqués

**Critères d'Acceptation :**
- [ ] Code généré automatiquement à 06h00 le jour J
- [ ] Format : 8 caractères alphanumériques (sans 0/O, 1/I)
- [ ] Validité : de 06h00 à 23h59 du jour uniquement
- [ ] Menu temporaire visible uniquement avec code actif

### Scénario 4 : Déconnexion Forcée
1. L'admin détecte une session suspecte
2. Accède à la liste des sessions actives d'un utilisateur
3. Sélectionne la session à terminer
4. Confirme la déconnexion forcée
5. L'utilisateur concerné est déconnecté immédiatement

**Critères d'Acceptation :**
- [ ] Liste des sessions avec IP, appareil, dernière activité
- [ ] Déconnexion effective en moins de 30 secondes
- [ ] Notification à l'utilisateur par messagerie interne
- [ ] Action auditée avec identité admin

---

## Requirements Fonctionnels

### RF-001 : Connexion Sécurisée
**Description** : Le système permet aux utilisateurs de s'authentifier avec email et mot de passe.  
**Acteur** : Utilisateur  
**Conditions** : 
- Compte existant et actif
- Email vérifié
- Compte non verrouillé  
**Résultat** : Session créée, utilisateur redirigé vers tableau de bord approprié

### RF-002 : Sessions Multi-Appareils
**Description** : Un utilisateur peut avoir plusieurs sessions simultanées sur différents appareils.  
**Acteur** : Utilisateur  
**Conditions** : Authentification réussie  
**Résultat** : 
- Nouvelle session créée sans invalider les existantes
- Chaque session identifiée par token unique
- Métadonnées conservées (IP, User-Agent, dernière activité)

### RF-003 : Protection Brute-Force
**Description** : Le système protège contre les tentatives de connexion répétées.  
**Acteur** : Système  
**Conditions** : Tentatives de connexion échouées  
**Résultat** :
- 3 échecs → délai 1 minute
- 5 échecs → délai 15 minutes
- 10 échecs → verrouillage + alerte admin

### RF-004 : Gestion Mots de Passe
**Description** : Le système impose une politique de mots de passe robuste.  
**Acteur** : Utilisateur, Admin  
**Conditions** : Création ou changement de mot de passe  
**Résultat** :
- Minimum 8 caractères, 1 majuscule, 1 chiffre, 1 spécial
- Stockage sécurisé irréversible
- Forçage du changement à première connexion

### RF-005 : Codes Temporaires
**Description** : Le système génère des codes d'accès temporaires pour le Président Jury.  
**Acteur** : Système, Président Jury  
**Conditions** : Soutenance planifiée pour le jour courant  
**Résultat** :
- Code généré automatiquement à 06h00
- Envoi par SMS (prioritaire) et email (backup)
- Validité limitée au jour J
- Révocation automatique à minuit

### RF-006 : Gestion des Groupes
**Description** : Les utilisateurs sont organisés en groupes avec niveaux hiérarchiques.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Création/modification/désactivation de groupes
- Attribution d'utilisateurs à un ou plusieurs groupes
- Niveau hiérarchique définit la préséance

### RF-007 : Permissions Granulaires
**Description** : Chaque groupe dispose de permissions CRUD par ressource.  
**Acteur** : Administrateur  
**Conditions** : Groupe existant  
**Résultat** :
- Définition : Lire, Créer, Modifier, Supprimer, Exporter, Valider
- Conditions JSON optionnelles (filtres contextuels)
- Cache des permissions (5 minutes)

### RF-008 : Rôles Temporaires
**Description** : Attribution de droits temporaires liés à un contexte.  
**Acteur** : Système, Admin  
**Conditions** : Événement déclencheur (ex: planification soutenance)  
**Résultat** :
- Droits additifs (n'enlèvent jamais de permissions)
- Validité bornée (date début/fin)
- Contexte spécifique (ex: soutenance_id)
- Révocation automatique à expiration

### RF-009 : Déconnexion Forcée
**Description** : L'administrateur peut forcer la déconnexion d'un utilisateur.  
**Acteur** : Administrateur  
**Conditions** : Droits admin, session active existante  
**Résultat** :
- Session invalidée immédiatement
- Utilisateur informé par messagerie
- Action auditée

---

## Critères de Succès

| Métrique | Objectif |
|----------|----------|
| Temps de connexion | < 2 secondes pour 95% des cas |
| Taux d'échec auth légitime | < 1% |
| Détection brute-force | 100% des attaques > 5 tentatives |
| Latence vérification permission | < 50ms (cache actif) |
| Couverture audit | 100% des actions sensibles |

---

## Entités Métier

### Utilisateur
- Identifiant unique
- Email (login)
- Mot de passe (sécurisé)
- Statut (Actif, Inactif, Suspendu)
- Dernière connexion
- Compteur d'échecs
- Indicateur changement mot de passe requis

### Session Active
- Token unique
- Utilisateur associé
- Adresse IP
- Appareil (User-Agent)
- Dernière activité
- Date d'expiration

### Groupe
- Nom
- Description
- Niveau hiérarchique
- Statut actif

### Permission
- Groupe associé
- Ressource associée
- Droits (CRUD + Export + Valider)
- Conditions optionnelles

### Rôle Temporaire
- Utilisateur
- Code du rôle
- Type de contexte
- ID du contexte
- Permissions JSON
- Période de validité

---

## Cas Limites et Erreurs

| Cas | Comportement |
|-----|--------------|
| Email non trouvé | Message générique "Identifiants incorrects" |
| Compte verrouillé | Message avec heure de déverrouillage |
| Session expirée | Redirection vers connexion avec message |
| Code temporaire invalide | Refus d'accès, log de tentative |
| Double authentification même session | Régénération token |

---

## Dépendances

- **Module Audit** : Journalisation de toutes les actions
- **Module Communication** : Envoi SMS/Email pour codes
- **Module Administration** : Configuration politique sécurité

---

## Hors Périmètre

- Authentification OAuth2 / SSO
- Authentification biométrique
- Double facteur (2FA) généralisé
- Connexion par certificat client
