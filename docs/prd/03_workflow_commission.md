# PRD 03 - Workflow & Commission

**Module**: Gestion des États et Processus de Validation  
**Version**: 1.0.0  
**Date**: 2025-12-14

---

## Vue d'Ensemble

Ce module implémente la machine à états centrale de CheckMaster, gérant les 14 états du dossier étudiant, les transitions autorisées, le processus de vote en commission avec règle d'unanimité, et le système d'escalade en cas de blocage. Il garantit la traçabilité complète de chaque transition.

---

## Acteurs

| Acteur | Responsabilités |
|--------|-----------------|
| **Scolarité** | Validation paiement et documents |
| **Communication** | Validation format du rapport |
| **Commission** | Évaluation et vote sur les rapports |
| **Président Commission** | Gestion sessions, arbitrage |
| **Doyen** | Médiation en cas d'escalade |
| **Système** | Transitions automatiques, alertes SLA |

---

## Scénarios Utilisateurs

### Scénario 1 : Transition Manuelle
1. L'utilisateur habilité (ex: Scolarité) traite un dossier
2. Vérifie les conditions de transition (paiement OK, documents OK)
3. Clique sur "Valider et passer à l'étape suivante"
4. Le système vérifie les permissions et conditions
5. La transition est effectuée avec snapshot des données
6. Les notifications sont envoyées aux parties concernées

**Critères d'Acceptation :**
- [ ] Transition uniquement si toutes conditions remplies
- [ ] Snapshot JSON des données avant/après
- [ ] Historique avec utilisateur, date, commentaire
- [ ] Notifications envoyées en moins de 2 minutes

### Scénario 2 : Session de Commission
1. Le Président Commission programme une session
2. Les rapports éligibles sont assignés aux membres
3. Chaque membre vote (Valider/À revoir/Rejeter)
4. Le système calcule si unanimité atteinte
5. Si non unanime : nouveau tour (max 3)
6. Si blocage tour 3 : escalade au Doyen

**Critères d'Acceptation :**
- [ ] Délai de vote : 48h pour tours 1-2, 24h pour tour 3
- [ ] Unanimité = 100% des votes identiques
- [ ] Escalade automatique après échec tour 3
- [ ] PV de session généré automatiquement

### Scénario 3 : Escalade Automatique
1. Une étape dépasse son délai maximum
2. Le système génère une alerte à 50%, 80% du délai
3. À 100%, l'escalade est déclenchée
4. Le responsable supérieur est notifié
5. L'escalade est tracée avec le contexte complet

**Critères d'Acceptation :**
- [ ] Alertes programmées dès l'entrée dans l'état
- [ ] Notifications multi-canal (Email + Messagerie)
- [ ] Escalade avec contexte JSON complet
- [ ] Résolution obligatoire avant poursuite

### Scénario 4 : Médiation par le Doyen
1. Une escalade commission (blocage vote) arrive au Doyen
2. Le Doyen consulte l'historique des votes et commentaires
3. Prend une décision arbitrale (Valider/Rejeter)
4. La décision est enregistrée avec justification
5. Le dossier reprend son cours normal

**Critères d'Acceptation :**
- [ ] Délai de médiation : 5 jours ouvrés max
- [ ] Décision motivée obligatoire
- [ ] Notification à toutes les parties
- [ ] Décision incontestable (terminale)

---

## Requirements Fonctionnels

### RF-020 : Machine à États
**Description** : Le système gère 14 états possibles pour chaque dossier étudiant.  
**Acteur** : Système  
**Conditions** : Dossier existant  
**Résultat** :
- État actuel toujours défini et valide
- État précédent conservé
- Date d'entrée dans l'état
- Date limite si délai défini

### RF-021 : Transitions Conditionnelles
**Description** : Les transitions entre états sont définies avec conditions.  
**Acteur** : Système, Utilisateur habilité  
**Conditions** : État source, rôle autorisé, conditions métier  
**Résultat** :
- Vérification permissions utilisateur
- Vérification conditions JSON (paiement, documents, etc.)
- Transition atomique (transaction)
- Rollback en cas d'erreur

### RF-022 : Historique des Transitions
**Description** : Chaque transition est historisée avec contexte complet.  
**Acteur** : Système  
**Conditions** : Transition effectuée  
**Résultat** :
- État source et cible enregistrés
- Utilisateur responsable
- Horodatage précis
- Commentaire optionnel
- Snapshot JSON des données du dossier

### RF-023 : Sessions de Commission
**Description** : Les sessions de commission sont planifiées et gérées.  
**Acteur** : Président Commission  
**Conditions** : Rapports à évaluer disponibles  
**Résultat** :
- Date, lieu, statut de la session
- Liste des rapports à l'ordre du jour
- Attribution aux membres évaluateurs
- Génération du PV en fin de session

### RF-024 : Votes Commission
**Description** : Les membres votent sur chaque rapport.  
**Acteur** : Membre Commission  
**Conditions** : Session en cours, rapport assigné  
**Résultat** :
- Vote : Valider, À revoir, ou Rejeter
- Commentaire obligatoire si "À revoir" ou "Rejeter"
- Un vote par membre par rapport par tour
- Historique des votes conservé

### RF-025 : Règle d'Unanimité
**Description** : La décision finale requiert l'unanimité.  
**Acteur** : Système  
**Conditions** : Tous les votes reçus  
**Résultat** :
- Si unanimité "Valider" → Rapport validé
- Si unanimité "Rejeter" → Rapport rejeté
- Si mixte → Nouveau tour (max 3)
- Tour 3 échoué → Escalade

### RF-026 : Système d'Alertes SLA
**Description** : Le système alerte en cas de dépassement de délai.  
**Acteur** : Système  
**Conditions** : Délai défini pour l'état  
**Résultat** :
- Alerte à 50% du délai (rappel)
- Alerte à 80% du délai (avertissement)
- Alerte à 100% du délai (escalade)
- Destinataires différenciés par niveau

### RF-027 : Escalade Automatique
**Description** : Les blocages déclenchent une escalade hiérarchique.  
**Acteur** : Système  
**Conditions** : Dépassement délai ou blocage vote  
**Résultat** :
- Escalade créée avec type et niveau
- Contexte complet en JSON
- Attribution à un responsable
- Suivi jusqu'à résolution

### RF-028 : Génération PV Commission
**Description** : Un PV est généré après chaque session.  
**Acteur** : Système  
**Conditions** : Session terminée  
**Résultat** :
- Document PDF avec liste des décisions
- Détail des votes par rapport
- Signatures des membres (si activé)
- Archivage automatique

---

## Critères de Succès

| Métrique | Objectif |
|----------|----------|
| Transition atomique | 100% sans corruption |
| Couverture historique | 100% des transitions tracées |
| Délai moyen par étape | Conforme au SLA défini |
| Taux d'escalade | < 5% des dossiers |
| Résolution escalade | < 5 jours ouvrés |

---

## Entités Métier

### État Workflow
- Code unique
- Nom lisible
- Phase (inscription, candidature, commission, etc.)
- Délai maximum en jours
- Ordre d'affichage
- Couleur (pour affichage)

### Transition
- État source
- État cible
- Code transition
- Rôles autorisés (JSON)
- Conditions (JSON)
- Déclenchement notifications

### Dossier Étudiant
- Étudiant associé
- Année académique
- État actuel
- Date d'entrée dans l'état
- Date limite de l'étape

### Historique Transition
- Dossier concerné
- États source/cible
- Transition utilisée
- Utilisateur
- Horodatage
- Commentaire
- Snapshot données

### Session Commission
- Date et lieu
- Statut (planifiée, en cours, terminée)
- Tour de vote actuel
- PV généré (oui/non)

### Vote Commission
- Session
- Rapport
- Membre votant
- Tour
- Décision
- Commentaire

### Alerte Workflow
- Dossier
- Type (50%, 80%, 100%)
- Planifiée pour
- Envoyée (oui/non)

### Escalade
- Dossier
- Type (blocage, délai, absence)
- Niveau d'escalade
- Statut (ouverte, en cours, résolue)
- Assignée à
- Contexte JSON

---

## Cas Limites et Erreurs

| Cas | Comportement |
|-----|--------------|
| Transition interdite | Erreur explicite avec raison |
| Conditions non remplies | Liste des conditions manquantes |
| Membre absent pour vote | Rappel automatique + escalade si persistant |
| Session sans quorum | Report automatique avec notification |
| Escalade non traitée | Escalade niveau supérieur après délai |

---

## Dépendances

- **Module Authentification** : Vérification permissions
- **Module Communication** : Notifications multi-canal
- **Module Documents** : Génération PV
- **Module Audit** : Historisation complète

---

## Configuration Optionnelle

Les fonctionnalités suivantes sont activables/désactivables :

| Fonctionnalité | Clé de configuration | Par défaut |
|----------------|---------------------|------------|
| Escalade automatique | `workflow.escalade.enabled` | Activé |
| Limite tours de vote | `commission.max_tours` | 3 |
| Médiation Doyen | `escalade.mediation.enabled` | Activé |

---

## Hors Périmètre

- Workflow configurable par interface
- Branches parallèles
- Rollback d'état (retour arrière)
- Workflow conditionnel par filière
