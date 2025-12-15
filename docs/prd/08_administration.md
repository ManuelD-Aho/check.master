# PRD 08 - Administration

**Module**: Configuration Système, Audit et Réclamations  
**Version**: 1.0.0  
**Date**: 2025-12-14

---

## Vue d'Ensemble

Ce module gère les aspects administratifs du système : configuration centralisée en base de données, activation des modules optionnels, import/export de données, gestion des réclamations étudiantes, et audit trail complet. Il garantit la gouvernance et la conformité du système.

---

## Acteurs

| Acteur | Responsabilités |
|--------|-----------------|
| **Administrateur** | Configuration, utilisateurs, maintenance |
| **Auditeur** | Consultation logs, vérifications |
| **Commission Réclamation** | Traitement des réclamations |
| **Étudiant** | Dépose réclamations |
| **Système** | Journalisation, alertes |

---

## Scénarios Utilisateurs

### Scénario 1 : Modification Configuration
1. L'admin accède aux paramètres système
2. Modifie une valeur (ex: délai SLA)
3. Le système valide le format
4. Enregistre avec historisation
5. Le changement est effectif immédiatement

**Critères d'Acceptation :**
- [ ] Validation format selon type de paramètre
- [ ] Historique des modifications conservé
- [ ] Pas de redémarrage requis
- [ ] Audit de qui a modifié quoi

### Scénario 2 : Consultation Logs d'Audit
1. L'auditeur accède au journal
2. Filtre par utilisateur, date, action, entité
3. Consulte le détail d'une entrée
4. Voit les données avant/après modification
5. Exporte le rapport si nécessaire

**Critères d'Acceptation :**
- [ ] Filtres multiples combinables
- [ ] Détail complet avec diff
- [ ] Export PDF/Excel
- [ ] Logs non modifiables

### Scénario 3 : Dépôt Réclamation
1. L'étudiant remplit le formulaire de réclamation
2. Choisit le type (note, décision, procédure)
3. Décrit le problème et joint des preuves
4. Soumet la réclamation
5. Reçoit un accusé de réception avec numéro

**Critères d'Acceptation :**
- [ ] Numéro de suivi unique
- [ ] Accusé de réception en 24h
- [ ] Pièces jointes possibles
- [ ] Workflow dédié déclenché

### Scénario 4 : Traitement Réclamation
1. La commission reçoit la réclamation
2. Examine les éléments fournis
3. Peut demander des compléments
4. Rend une décision motivée
5. L'étudiant est notifié avec possibilité d'appel

**Critères d'Acceptation :**
- [ ] Délai traitement : 15 jours ouvrés
- [ ] Décision motivée obligatoire
- [ ] Appel possible sous 15 jours
- [ ] Historique complet conservé

### Scénario 5 : Import Données Historiques
1. L'admin prépare un fichier Excel standardisé
2. Upload le fichier dans l'interface
3. Le système valide le format et les données
4. Affiche les erreurs/avertissements
5. L'admin confirme l'import
6. Les données sont intégrées avec traçabilité

**Critères d'Acceptation :**
- [ ] Validation format avant import
- [ ] Rapport d'erreurs détaillé
- [ ] Rollback possible si échec
- [ ] Historique de l'import conservé

---

## Requirements Fonctionnels

### RF-080 : Configuration Système
**Description** : Tous les paramètres sont stockés en base de données.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Clé/valeur avec type (texte, nombre, booléen, JSON)
- Catégorisation par module
- Valeur par défaut définie
- Historique des changements

### RF-081 : Modules Optionnels
**Description** : Certaines fonctionnalités sont activables.  
**Acteur** : Administrateur  
**Conditions** : Configuration existante  
**Résultat** :
- Escalade : activable/désactivable
- Limite tours vote : configurable (1-5)
- Signatures électroniques : activable
- Effet immédiat sans redémarrage

### RF-082 : Import Données
**Description** : Des données historiques peuvent être importées.  
**Acteur** : Administrateur  
**Conditions** : Fichier au format requis  
**Résultat** :
- Validation structure et données
- Rapport d'erreurs avant import
- Transaction atomique
- Historique de l'import

### RF-083 : Export Données
**Description** : Les données peuvent être exportées.  
**Acteur** : Utilisateur habilité  
**Conditions** : Permission d'export sur la ressource  
**Résultat** :
- Formats : Excel, CSV, PDF
- Filtres applicables
- Données anonymisables si requis
- Audit de l'export

### RF-084 : Gestion Réclamations
**Description** : Les réclamations suivent un workflow dédié.  
**Acteur** : Étudiant, Commission  
**Conditions** : N/A  
**Résultat** :
- Dépôt avec type et description
- Workflow : Déposée → En examen → Décision
- Appel possible
- Délais légaux respectés

### RF-085 : Audit Trail
**Description** : Toutes les actions critiques sont journalisées.  
**Acteur** : Système  
**Conditions** : Action effectuée  
**Résultat** :
- Double journalisation (fichier + base)
- Données avant/après (snapshots JSON)
- Utilisateur, IP, horodatage
- Durée de traitement
- Non supprimable

### RF-086 : Maintenance
**Description** : Des opérations de maintenance sont disponibles.  
**Acteur** : Administrateur  
**Conditions** : Droits admin  
**Résultat** :
- Vider cache permissions
- Régénérer statistiques
- Vérifier intégrité archives
- Nettoyer sessions expirées
- Mode maintenance activable

### RF-087 : Statistiques et Tableaux de Bord
**Description** : Des indicateurs clés sont disponibles.  
**Acteur** : Administrateur, Resp. Filière  
**Conditions** : Données existantes  
**Résultat** :
- Nombre de dossiers par état
- Taux de validation/rejet
- Délais moyens par étape
- Évolution dans le temps

---

## Workflow Réclamations

### États

```
[DEPOSEE]
    │
    │ Attribution automatique (24h)
    ▼
[EN_EXAMEN]
    │
    ├─── Compléments requis ──► [COMPLEMENT_REQUIS] ─┐
    │                                                 │
    │    └───────────────────────────────────────────┘
    │
    ├─── Acceptée ──► [ACCEPTEE] ──► FIN
    │
    └─── Rejetée ──► [REJETEE]
                          │
                          │ Appel (15 jours)
                          ▼
                     [EN_APPEL]
                          │
                          ├─── Acceptée ──► [ACCEPTEE_APPEL] ──► FIN
                          │
                          └─── Rejetée ──► [REJETEE_DEFINITIF] ──► FIN
```

### Délais Légaux
- Accusé réception : 48h ouvrées
- Examen initial : 15 jours ouvrés
- Demande compléments : 7 jours pour répondre
- Décision finale : 30 jours max
- Délai d'appel : 15 jours après notification
- Examen appel : 30 jours

---

## Critères de Succès

| Métrique | Objectif |
|----------|----------|
| Configuration sans redémarrage | 100% |
| Couverture audit | 100% actions critiques |
| Délai traitement réclamation | < 15 jours ouvrés |
| Intégrité logs | 0 modification possible |
| Import sans erreur | > 95% des fichiers conformes |

---

## Entités Métier

### Configuration Système
- Clé unique
- Valeur
- Type (texte, nombre, booléen, JSON)
- Module associé
- Description
- Valeur par défaut
- Modifié par
- Date modification

### Entrée Audit (Pister)
- Utilisateur
- Action
- Type d'entité
- ID entité
- Données avant/après (JSON)
- IP, User-Agent
- URL requête
- Durée traitement
- Horodatage

### Réclamation
- Demandeur
- Dossier concerné (optionnel)
- Type (note, décision, procédure, autre)
- Objet
- Description
- Pièces jointes
- Statut
- Priorité
- Date limite réponse
- Traité par
- Décision
- Motif décision

### Historique Réclamation
- Réclamation
- Action
- Effectué par
- Commentaire
- Ancien/nouveau statut

### Import Historique
- Utilisateur
- Fichier original
- Type d'entité importée
- Nombre de lignes
- Succès/Erreurs
- Statut
- Horodatage

### Statistique Cache
- Clé
- Valeur JSON
- Calculé le
- Expire le

---

## Types de Réclamations

| Type | Description | Commission |
|------|-------------|------------|
| Note soutenance | Contestation note finale | Commission Réclamation |
| Note UE | Contestation note de cours | Resp. Niveau |
| Décision commission | Contestation validation/rejet | Doyen |
| Procédure | Irrégularité de procédure | Commission Réclamation |
| Autre | Cas non catégorisés | Admin |

---

## Cas Limites et Erreurs

| Cas | Comportement |
|-----|--------------|
| Configuration invalide | Refus avec message explicite |
| Import fichier corrompu | Rejet, rapport d'erreur |
| Réclamation hors délai | Avertissement, acceptation conditionnelle |
| Appel sur décision définitive | Refusé avec explication |
| Log supprimé manuellement | Alerte critique, investigation |

---

## Configuration Modules Optionnels

| Module | Clé | Valeurs | Défaut |
|--------|-----|---------|--------|
| Escalade | `workflow.escalade.enabled` | true/false | true |
| Tours vote | `commission.max_tours` | 1-5 | 3 |
| Médiation Doyen | `escalade.mediation.enabled` | true/false | true |
| Signatures | `documents.signatures.enabled` | true/false | false |
| SMS | `notifications.sms.enabled` | true/false | true |

---

## Dépendances

- **Tous les modules** : L'audit est transversal
- **Module Authentification** : Utilisateurs, permissions
- **Module Communication** : Notifications réclamations
- **Module Workflow** : Statuts réclamations

---

## Hors Périmètre

- Sauvegarde automatique externalisée
- Monitoring temps réel
- Alerting externe (PagerDuty, etc.)
- Multi-tenant (plusieurs institutions)
