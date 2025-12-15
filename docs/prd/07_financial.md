# PRD 07 - Financier

**Module**: Gestion des Paiements, Pénalités et Exonérations  
**Version**: 1.0.0  
**Date**: 2025-12-14

---

## Vue d'Ensemble

Ce module gère l'ensemble des aspects financiers du système : enregistrement des paiements de scolarité, calcul et suivi des pénalités de retard, gestion des exonérations, et génération automatique des reçus. Il constitue un prérequis pour la validation des candidatures.

---

## Acteurs

| Acteur | Responsabilités |
|--------|-----------------|
| **Scolarité** | Enregistre paiements, calcule pénalités |
| **Étudiant** | Consulte son solde, télécharge reçus |
| **Administrateur** | Approuve exonérations, configure barèmes |
| **Système** | Calcule pénalités, génère reçus |

---

## Scénarios Utilisateurs

### Scénario 1 : Enregistrement d'un Paiement
1. L'agent de scolarité sélectionne l'étudiant
2. Indique le montant versé
3. Précise le mode de paiement (espèces, carte, virement)
4. Valide l'enregistrement
5. Le système génère un reçu PDF
6. L'étudiant est notifié avec le reçu
7. Le solde est mis à jour

**Critères d'Acceptation :**
- [ ] Numéro de référence unique généré
- [ ] Reçu généré en moins de 3 secondes
- [ ] Email avec pièce jointe envoyé
- [ ] Historique des paiements mis à jour

### Scénario 2 : Calcul de Pénalité
1. Le système détecte un retard de paiement
2. Calcule la pénalité selon le barème configuré
3. Enregistre la pénalité avec motif
4. Notifie l'étudiant avec le montant et délai
5. La pénalité s'ajoute au solde dû

**Critères d'Acceptation :**
- [ ] Calcul automatique selon configuration
- [ ] Notification claire avec échéance
- [ ] Pénalité visible dans le dossier
- [ ] Bloque progression si non payée

### Scénario 3 : Demande d'Exonération
1. L'étudiant ou l'admin initie une demande
2. Précise le motif et le pourcentage demandé
3. L'admin approuve ou refuse
4. Si approuvé, le montant est déduit du solde
5. Un justificatif est archivé

**Critères d'Acceptation :**
- [ ] Motif obligatoire
- [ ] Approbation par admin uniquement
- [ ] Historique des décisions
- [ ] Impact immédiat sur le solde

### Scénario 4 : Consultation Solde Étudiant
1. L'étudiant accède à son espace financier
2. Voit le montant total de la scolarité
3. Voit les versements effectués
4. Voit les pénalités éventuelles
5. Voit les exonérations accordées
6. Voit le solde restant à payer

**Critères d'Acceptation :**
- [ ] Vue synthétique claire
- [ ] Détail accessible pour chaque ligne
- [ ] Téléchargement des reçus
- [ ] Historique complet

---

## Requirements Fonctionnels

### RF-070 : Enregistrement Paiements
**Description** : Les versements sont enregistrés avec traçabilité.  
**Acteur** : Scolarité  
**Conditions** : Étudiant inscrit pour l'année  
**Résultat** :
- Paiement enregistré avec montant, mode, date
- Référence unique attribuée
- Enregistreur identifié
- Reçu généré automatiquement

### RF-071 : Génération Reçus
**Description** : Chaque paiement produit un reçu officiel.  
**Acteur** : Système  
**Conditions** : Paiement enregistré  
**Résultat** :
- PDF généré avec tous les détails
- Numéro séquentiel
- Logo et tampon institutionnel
- Archivage automatique

### RF-072 : Calcul Pénalités
**Description** : Les retards génèrent des pénalités calculées.  
**Acteur** : Système  
**Conditions** : Échéance dépassée sans paiement total  
**Résultat** :
- Pénalité calculée selon barème
- Motif automatique (retard X jours)
- Notification à l'étudiant
- Ajout au solde dû

### RF-073 : Paiement Pénalités
**Description** : Les pénalités peuvent être payées séparément.  
**Acteur** : Scolarité  
**Conditions** : Pénalité existante non payée  
**Résultat** :
- Paiement enregistré sur la pénalité
- Reçu spécifique généré
- Pénalité marquée comme payée
- Déblocage si applicable

### RF-074 : Gestion Exonérations
**Description** : Des réductions peuvent être accordées.  
**Acteur** : Administrateur  
**Conditions** : Demande formulée avec motif  
**Résultat** :
- Exonération créée (montant ou pourcentage)
- Approuvé par admin
- Motif et justificatif archivés
- Impact sur solde calculé

### RF-075 : Calcul Solde
**Description** : Le solde dû est calculé en temps réel.  
**Acteur** : Système  
**Conditions** : Étudiant inscrit  
**Résultat** :
- Formule : Scolarité - Paiements - Exonérations + Pénalités
- Mise à jour à chaque transaction
- Consultation instantanée

### RF-076 : Historique Financier
**Description** : Tout l'historique financier est consultable.  
**Acteur** : Scolarité, Étudiant (ses propres données)  
**Conditions** : Dossier existant  
**Résultat** :
- Liste chronologique de toutes les opérations
- Filtrage par type
- Export possible

---

## Critères de Succès

| Métrique | Objectif |
|----------|----------|
| Temps enregistrement | < 30 secondes |
| Génération reçu | < 3 secondes |
| Exactitude calcul solde | 100% |
| Traçabilité | 100% des opérations tracées |
| Disponibilité reçus | 100% téléchargeables |

---

## Entités Métier

### Paiement
- Étudiant
- Année académique
- Montant
- Mode (Espèces, Carte, Virement, Chèque)
- Référence unique
- Date de paiement
- Reçu généré (oui/non)
- Chemin du reçu
- Enregistré par

### Pénalité
- Étudiant
- Montant
- Motif
- Date d'application
- Payée (oui/non)
- Date de paiement
- Chemin du reçu

### Exonération
- Étudiant
- Année académique
- Montant exonéré
- Pourcentage (alternatif)
- Motif
- Date d'attribution
- Approuvé par

---

## Cas Limites et Erreurs

| Cas | Comportement |
|-----|--------------|
| Paiement > montant dû | Avertissement, trop-perçu affiché |
| Pénalité sur étudiant exonéré | Calcul sur base réduite |
| Double enregistrement | Vérification référence unique |
| Annulation paiement | Trace conservée, solde recalculé |
| Exonération > 100% | Refusé avec message |

---

## Configuration

| Paramètre | Description | Défaut |
|-----------|-------------|--------|
| `finance.penalite.taux_jour` | Taux par jour de retard | 0.5% |
| `finance.penalite.plafond` | Plafond maximum | 50% |
| `finance.penalite.grace_jours` | Jours de grâce | 7 |
| `finance.scolarite.montant` | Montant annuel | (config) |

---

## Dépendances

- **Module Académique** : Étudiants, Années
- **Module Documents** : Génération reçus
- **Module Communication** : Notifications
- **Module Workflow** : Déblocage candidature

---

## Hors Périmètre

- Paiement en ligne (carte bancaire)
- Intégration banque
- Facturation entreprises
- Comptabilité générale
