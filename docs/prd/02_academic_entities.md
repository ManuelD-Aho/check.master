# PRD 02 - Entités Académiques

**Module**: Gestion des Données Académiques  
**Version**: 1.0.0  
**Date**: 2025-12-14

---

## Vue d'Ensemble

Ce module gère l'ensemble des entités académiques du système : étudiants, enseignants, personnel administratif, entreprises partenaires, ainsi que la structure pédagogique (années académiques, semestres, UE, ECUE). Il constitue le référentiel de données pour tous les autres modules.

---

## Acteurs

| Acteur | Responsabilités |
|--------|-----------------|
| **Administrateur** | Gestion complète de toutes les entités |
| **Scolarité** | Création/modification étudiants, inscriptions |
| **Resp. Filière** | Gestion des UE/ECUE de sa filière |
| **Secrétaire** | Consultation et export des données |
| **Étudiant** | Consultation de ses propres données |

---

## Scénarios Utilisateurs

### Scénario 1 : Création d'un Étudiant
1. L'agent de scolarité accède au formulaire de création
2. Saisit les informations : numéro carte, nom, prénom, email, téléphone
3. Renseigne la promotion et les informations de naissance
4. Valide le formulaire
5. Le système vérifie l'unicité du numéro carte et de l'email
6. L'étudiant est créé, un compte utilisateur peut être associé

**Critères d'Acceptation :**
- [ ] Numéro carte format validé (ex: CI01552852)
- [ ] Email unique dans le système
- [ ] Données enregistrées en moins de 2 secondes
- [ ] Historique de création tracé

### Scénario 2 : Gestion des Années Académiques
1. L'administrateur crée une nouvelle année académique
2. Définit les dates de début et fin
3. Marque l'année comme active (une seule active à la fois)
4. Crée les semestres associés
5. Les inscriptions peuvent commencer

**Critères d'Acceptation :**
- [ ] Une seule année active à un instant T
- [ ] Dates cohérentes (début < fin)
- [ ] Semestres liés automatiquement disponibles
- [ ] Bascule d'année auditable

### Scénario 3 : Configuration UE/ECUE
1. Le responsable de filière accède à la gestion pédagogique
2. Crée une UE avec code, libellé, crédits
3. Associe l'UE à un niveau et semestre
4. Ajoute les ECUE constituant l'UE
5. Le total des crédits ECUE correspond aux crédits UE

**Critères d'Acceptation :**
- [ ] Code UE unique dans le système
- [ ] Crédits entre 1 et 30
- [ ] Liaison niveau/semestre obligatoire
- [ ] ECUE héritent du semestre de l'UE parent

### Scénario 4 : Recherche d'Étudiant
1. Un utilisateur habilité accède à la recherche
2. Saisit un critère (nom, numéro carte, email)
3. Le système affiche les résultats correspondants
4. L'utilisateur peut accéder au dossier complet

**Critères d'Acceptation :**
- [ ] Recherche par nom partiel (minimum 2 caractères)
- [ ] Résultats en moins de 1 seconde
- [ ] Pagination si plus de 50 résultats
- [ ] Filtrage par promotion possible

---

## Requirements Fonctionnels

### RF-010 : Gestion des Étudiants
**Description** : Le système permet de créer et gérer les fiches étudiants.  
**Acteur** : Scolarité, Administrateur  
**Conditions** : Droits de création sur la ressource "étudiants"  
**Résultat** :
- Fiche étudiant créée avec numéro carte unique
- Format numéro : alphanumérique 20 caractères max
- Données personnelles complètes (nom, prénom, email, téléphone)
- Informations de naissance et promotion

### RF-011 : Gestion des Enseignants
**Description** : Le système permet de gérer le corps enseignant.  
**Acteur** : Administrateur  
**Conditions** : Droits de gestion enseignants  
**Résultat** :
- Fiche enseignant avec coordonnées complètes
- Association à un grade (Professeur, Maître de Conférences, etc.)
- Association à une fonction (Directeur, Responsable, etc.)
- Association à une ou plusieurs spécialités

### RF-012 : Gestion du Personnel Administratif
**Description** : Le système gère les agents administratifs.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Fiche personnel avec coordonnées
- Association à une fonction
- Statut actif/inactif

### RF-013 : Gestion des Entreprises
**Description** : Le système maintient un référentiel d'entreprises partenaires.  
**Acteur** : Administrateur, Scolarité  
**Conditions** : Droits de gestion entreprises  
**Résultat** :
- Fiche entreprise (nom, secteur, adresse, contacts)
- Réutilisable pour plusieurs stages
- Statut actif/inactif

### RF-014 : Gestion des Années Académiques
**Description** : Le système gère le calendrier académique.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Création année avec dates début/fin
- Une seule année active simultanément
- Basculement d'année avec confirmation

### RF-015 : Gestion des Semestres
**Description** : Les années sont divisées en semestres.  
**Acteur** : Administrateur  
**Conditions** : Année académique existante  
**Résultat** :
- Semestre lié à une année
- Dates propres au semestre
- Libellé descriptif

### RF-016 : Gestion des UE
**Description** : Les Unités d'Enseignement structurent le programme.  
**Acteur** : Resp. Filière, Administrateur  
**Conditions** : Niveau et semestre existants  
**Résultat** :
- Code UE unique
- Libellé descriptif
- Nombre de crédits
- Association niveau et semestre

### RF-017 : Gestion des ECUE
**Description** : Les ECUE détaillent le contenu des UE.  
**Acteur** : Resp. Filière, Administrateur  
**Conditions** : UE parent existante  
**Résultat** :
- Code ECUE unique
- Libellé
- Crédits (sous-ensemble de l'UE)
- Lien vers UE parent

### RF-018 : Gestion des Grades
**Description** : Référentiel des grades académiques.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Libellé du grade
- Niveau hiérarchique (pour tri et préséance)

### RF-019 : Gestion des Spécialités
**Description** : Domaines d'expertise des enseignants.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Libellé de la spécialité
- Description optionnelle
- Utilisé pour suggestion de jurys

---

## Critères de Succès

| Métrique | Objectif |
|----------|----------|
| Temps création étudiant | < 3 secondes |
| Temps recherche | < 1 seconde pour 95% des cas |
| Intégrité données | 0 doublons numéro carte |
| Couverture données | 100% des champs obligatoires renseignés |
| Historisation | 100% des modifications tracées |

---

## Entités Métier

### Étudiant
- Numéro carte (unique, format CI01552852)
- Nom, Prénom
- Email (unique)
- Téléphone
- Date et lieu de naissance
- Genre
- Promotion
- Statut actif

### Enseignant
- Nom, Prénom
- Email (unique)
- Téléphone
- Grade associé
- Fonction associée
- Spécialité(s)
- Statut actif

### Personnel Administratif
- Nom, Prénom
- Email (unique)
- Téléphone
- Fonction
- Statut actif

### Entreprise
- Nom
- Secteur d'activité
- Adresse
- Contacts (téléphone, email, site web)
- Statut actif

### Année Académique
- Libellé (ex: 2024-2025)
- Date début
- Date fin
- Indicateur année active

### Semestre
- Libellé
- Année académique parent
- Dates propres

### Niveau d'Étude
- Libellé (Licence 1, Master 2, etc.)
- Description
- Ordre d'affichage

### UE (Unité d'Enseignement)
- Code (unique)
- Libellé
- Crédits
- Niveau associé
- Semestre associé

### ECUE (Élément Constitutif d'UE)
- Code (unique)
- Libellé
- Crédits
- UE parent

### Grade
- Libellé
- Niveau hiérarchique

### Fonction
- Libellé
- Description

### Spécialité
- Libellé
- Description

---

## Cas Limites et Erreurs

| Cas | Comportement |
|-----|--------------|
| Numéro carte déjà existant | Erreur explicite, suggestion de recherche |
| Email déjà utilisé | Erreur avec indication de l'entité existante |
| Suppression entité référencée | Refus avec liste des dépendances |
| Année académique sans semestre | Avertissement à la création |
| UE sans ECUE | Autorisé mais signalé |

---

## Dépendances

- **Module Authentification** : Création compte utilisateur après entité
- **Module Workflow** : Dossier étudiant lié à l'entité
- **Module Financier** : Paiements liés à étudiant et année

---

## Hors Périmètre

- Import automatique depuis SI externe
- Synchronisation temps réel avec autre système
- Gestion des anciens étudiants (alumni)
- Photos d'identité
