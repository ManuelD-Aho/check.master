# PRD 06 - Documents & Archives

**Module**: Génération PDF, Archivage et Historisation  
**Version**: 1.0.0  
**Date**: 2025-12-14

---

## Vue d'Ensemble

Ce module gère la génération des 13 types de documents PDF, leur archivage avec vérification d'intégrité (hash SHA256), l'historisation des modifications d'entités, et le système de brouillons automatiques. Il garantit la pérennité et l'intégrité des documents sur le long terme.

---

## Acteurs

| Acteur | Responsabilités |
|--------|-----------------|
| **Système** | Génère et archive automatiquement |
| **Utilisateur** | Consulte et télécharge documents |
| **Administrateur** | Vérifie intégrité, régénère si nécessaire |
| **Secrétaire** | Gère les archives physiques |

---

## Scénarios Utilisateurs

### Scénario 1 : Génération Automatique de Document
1. Un événement déclenche la génération (ex: paiement validé)
2. Le système charge le template approprié
3. Remplit les données dynamiques (nom, montant, date)
4. Ajoute la page de garde si applicable
5. Génère le PDF
6. Calcule le hash SHA256
7. Stocke le fichier et les métadonnées
8. Notifie le bénéficiaire

**Critères d'Acceptation :**
- [ ] Génération en moins de 5 secondes
- [ ] Logo et nom de l'institution dynamiques
- [ ] Hash calculé et stocké
- [ ] Notification de disponibilité envoyée

### Scénario 2 : Vérification d'Intégrité
1. L'admin lance une vérification d'intégrité
2. Le système parcourt les archives
3. Recalcule le hash de chaque fichier
4. Compare avec le hash stocké
5. Signale les anomalies détectées

**Critères d'Acceptation :**
- [ ] Vérification automatique hebdomadaire
- [ ] Alerte immédiate si anomalie
- [ ] Rapport de vérification généré
- [ ] Fichiers corrompus signalés (non modifiés)

### Scénario 3 : Consultation Historique
1. Un admin recherche l'historique d'un dossier
2. Le système affiche toutes les versions
3. Permet de comparer deux versions
4. Montre les différences (diff)
5. Permet de restaurer une version si autorisé

**Critères d'Acceptation :**
- [ ] Historique complet depuis création
- [ ] Comparaison côte à côte
- [ ] Auteur et date de chaque modification
- [ ] Export de l'historique possible

### Scénario 4 : Mode Brouillon
1. L'utilisateur commence à remplir un formulaire
2. Le système sauvegarde automatiquement toutes les 30s
3. En cas de déconnexion, le brouillon est conservé
4. À la reconnexion, l'utilisateur retrouve ses données
5. La soumission finalise et supprime le brouillon

**Critères d'Acceptation :**
- [ ] Sauvegarde silencieuse (pas d'interruption)
- [ ] Conservation 7 jours par défaut
- [ ] Un brouillon par formulaire par utilisateur
- [ ] Restauration automatique proposée

### Scénario 5 : Signature Électronique (Optionnel)
1. Un PV de commission est généré
2. Si les signatures sont activées, une demande est créée
3. Chaque signataire reçoit un code OTP
4. Signe en saisissant le code
5. Le document est marqué comme signé avec horodatage

**Critères d'Acceptation :**
- [ ] Affichage signature uniquement si configuré
- [ ] Code OTP valide 15 minutes
- [ ] Horodatage et IP conservés
- [ ] Intégrité vérifiée avant chaque signature

---

## Requirements Fonctionnels

### RF-060 : Génération PDF
**Description** : Le système génère 13 types de documents PDF.  
**Acteur** : Système  
**Conditions** : Événement déclencheur  
**Résultat** :
- Document généré selon template
- Données dynamiques insérées
- Format A4, qualité impression
- Stockage sécurisé

### RF-061 : Templates Dynamiques
**Description** : Les templates utilisent le logo et nom de l'institution.  
**Acteur** : Administrateur  
**Conditions** : Configuration définie  
**Résultat** :
- Logo chargé depuis configuration
- Nom de l'institution dynamique
- Mise à jour sans modification code

### RF-062 : Calcul Hash Intégrité
**Description** : Chaque document est signé numériquement.  
**Acteur** : Système  
**Conditions** : Document généré  
**Résultat** :
- Hash SHA256 calculé
- Stocké avec métadonnées
- Non modifiable après création

### RF-063 : Archivage Structuré
**Description** : Les documents sont archivés de manière pérenne.  
**Acteur** : Système  
**Conditions** : Document généré  
**Résultat** :
- Stockage dans arborescence organisée
- Métadonnées : type, entité, date, taille
- Verrouillage par défaut (inaltérable)
- Conservation 30 ans minimum

### RF-064 : Vérification Intégrité
**Description** : L'intégrité des archives est vérifiée périodiquement.  
**Acteur** : Système, Admin  
**Conditions** : Archives existantes  
**Résultat** :
- Recalcul hash
- Comparaison avec hash stocké
- Alerte si différence
- Rapport de vérification

### RF-065 : Régénération Document
**Description** : Un document peut être régénéré depuis les données.  
**Acteur** : Administrateur  
**Conditions** : Snapshot JSON disponible  
**Résultat** :
- Reconstruction depuis données historiques
- Nouveau hash calculé
- Mention "Régénéré le..."
- Document original conservé

### RF-066 : Historisation Entités
**Description** : Les modifications d'entités sont historisées.  
**Acteur** : Système  
**Conditions** : Modification d'une entité  
**Résultat** :
- Version incrémentée
- Snapshot JSON complet
- Auteur et horodatage
- Comparaison entre versions possible

### RF-067 : Mode Brouillon
**Description** : Les saisies partielles sont conservées.  
**Acteur** : Système  
**Conditions** : Formulaire en cours  
**Résultat** :
- Sauvegarde automatique (30s)
- Un brouillon par contexte
- Expiration après 7 jours
- Finalisation supprime le brouillon

### RF-068 : Signatures Électroniques (Optionnel)
**Description** : Les documents critiques peuvent être signés.  
**Acteur** : Signataire, Système  
**Conditions** : Fonctionnalité activée  
**Résultat** :
- Demande de signature créée
- Code OTP envoyé (15 min validité)
- Signature avec horodatage
- Vérification intégrité avant signature

---

## Types de Documents (13)

| Type | Générateur | Déclencheur | Destinataire |
|------|------------|-------------|--------------|
| Reçu de paiement | TCPDF | Versement enregistré | Étudiant |
| Reçu de pénalité | TCPDF | Pénalité payée | Étudiant |
| Bulletin de notes | TCPDF | Fin semestre | Étudiant |
| Attestation inscription | TCPDF | Sur demande | Étudiant |
| PV Commission | mPDF | Fin session | Commission |
| PV Soutenance | mPDF | Délibération terminée | Jury |
| Convocation Commission | TCPDF | Session planifiée | Membres |
| Convocation Jury | TCPDF | Soutenance planifiée | Jury |
| Fiche notation | TCPDF | Jour soutenance | Président Jury |
| Attestation réussite | mPDF | Soutenance réussie | Étudiant |
| Attestation diplôme | mPDF | Processus terminé | Étudiant |
| Relevé de notes | TCPDF | Sur demande | Étudiant |
| Page de garde rapport | mPDF | Soumission rapport | Étudiant |

---

## Critères de Succès

| Métrique | Objectif |
|----------|----------|
| Temps génération PDF | < 5 secondes |
| Intégrité archives | 100% vérifiable |
| Taux corruption | 0% |
| Durée conservation | 30 ans |
| Récupération brouillon | 100% si < 7 jours |

---

## Entités Métier

### Document Généré
- Type de document
- Entité associée (type + ID)
- Chemin fichier
- Nom fichier
- Taille en octets
- Hash SHA256
- Générateur
- Date génération

### Archive
- Document associé
- Hash vérification
- Dernière vérification
- Statut vérifié
- Verrouillé (oui/non)

### Historique Entité
- Type d'entité
- ID entité
- Numéro de version
- Snapshot JSON complet
- Modifié par
- Date modification
- Commentaire optionnel

### Brouillon
- Utilisateur
- Type de contexte
- ID contexte
- Code formulaire
- Données JSON
- Date création
- Date modification
- Date expiration
- Finalisé (oui/non)

### Document Signature (Optionnel)
- Type document
- Référence document
- Fichier original (chemin + hash)
- Fichier signé (chemin)
- Statut (en attente, partiel, complet)
- Date expiration

### Signataire (Optionnel)
- Document
- Utilisateur
- Ordre de signature
- Obligatoire (oui/non)
- Statut (en attente, signé, refusé)
- Code OTP hash
- Date signature
- IP et User-Agent

### Critère Évaluation
- Code unique
- Libellé
- Description
- Pondération
- Actif (oui/non)

### Mention
- Libellé
- Note minimum
- Note maximum

---

## Cas Limites et Erreurs

| Cas | Comportement |
|-----|--------------|
| Génération échouée | Retry 3x puis alerte admin |
| Fichier corrompu détecté | Alerte critique, tentative régénération |
| Brouillon expiré | Suppression automatique, log |
| Signature refusée | Notification créateur, motif conservé |
| Template manquant | Erreur bloquante, pas de document vide |

---

## Dépendances

- **Module Authentification** : Auteur des modifications
- **Module Workflow** : Déclencheurs de génération
- **Module Communication** : Notifications de disponibilité
- **Module Audit** : Journalisation des accès

---

## Hors Périmètre

- OCR (reconnaissance caractères)
- Édition PDF en ligne
- Stockage cloud externe
- Signature cryptographique avancée (PKI)
