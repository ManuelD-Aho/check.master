# CheckMaster UFHB 2.0 - PRD Master

**Version**: 1.0.0  
**Date**: 2025-12-14  
**Statut**: Approuvé

---

## Vue d'Ensemble

CheckMaster est un système de gestion académique complet pour l'UFR Mathématiques et Informatique de l'Université Félix Houphouët-Boigny (UFHB). Il gère l'intégralité du cycle de vie étudiant, de l'inscription jusqu'à la délivrance du diplôme, en passant par la validation des rapports de stage et l'organisation des soutenances.

Le système est conçu pour être **100% Database-Driven** (autarcie totale), permettant toute configuration via l'interface sans modification du code source.

---

## Acteurs du Système

### Acteurs Principaux (13 Groupes)

| # | Groupe | Niveau | Responsabilités |
|---|--------|--------|-----------------|
| 1 | **Administrateur** | 5 | Contrôle total du système, configuration, utilisateurs |
| 2 | **Secrétaire** | 6 | Gestion documentaire, archivage |
| 3 | **Communication** | 7 | Vérification format des rapports |
| 4 | **Scolarité** | 8 | Paiements, candidatures, inscriptions |
| 5 | **Resp. Filière** | 9 | Supervision filière MIAGE |
| 6 | **Resp. Niveau** | 10 | Gestion Master 2 |
| 7 | **Commission** | 11 | Évaluation rapports, votes |
| 8 | **Enseignant** | 12 | Supervision, participation jury |
| 9 | **Étudiant** | 13 | Rédaction rapport, soumissions |
| 10 | **Président Commission** | - | Constitution des jurys |
| 11 | **Président Jury** | Temp. | Saisie notes jour J |
| 12 | **Directeur Mémoire** | - | Direction scientifique |
| 13 | **Encadreur Pédagogique** | - | Accompagnement étudiant |

### Acteurs Système
- **Système** : Actions automatiques (notifications, calculs, alertes)
- **Cron Jobs** : Traitements planifiés (rappels, escalades)

---

## Workflow Principal

### Machine à États (14 États)

```
┌─────────────┐
│   INSCRIT   │
└──────┬──────┘
       │ Soumission candidature
       ▼
┌─────────────────────┐
│ CANDIDATURE_SOUMISE │
└──────────┬──────────┘
           │ Validation Scolarité (paiement + docs)
           ▼
┌────────────────────────┐
│ VERIFICATION_SCOLARITE │
└──────────┬─────────────┘
           │ Validation Communication (format)
           ▼
┌─────────────────────┐
│ FILTRE_COMMUNICATION │
└──────────┬──────────┘
           │ Passage en commission
           ▼
┌────────────────────────┐
│ EN_ATTENTE_COMMISSION  │
└──────────┬─────────────┘
           │ Session programmée
           ▼
┌──────────────────────────┐
│ EN_EVALUATION_COMMISSION │◄──┐
└──────────┬───────────────┘   │ Corrections demandées
           │ Unanimité obtenue │
           ├───────────────────┘
           ▼
┌─────────────────┐
│ RAPPORT_VALIDE  │
└───────┬─────────┘
        │ Attribution encadreurs
        ▼
┌──────────────────────────┐
│ ATTENTE_AVIS_ENCADREUR   │
└──────────┬───────────────┘
           │ Avis favorable
           ▼
┌─────────────────┐
│ PRET_POUR_JURY  │
└───────┬─────────┘
        │ Constitution jury
        ▼
┌──────────────────────┐
│ JURY_EN_CONSTITUTION │
└──────────┬───────────┘
           │ 5 membres acceptent
           ▼
┌──────────────────────┐
│ SOUTENANCE_PLANIFIEE │
└──────────┬───────────┘
           │ Jour J
           ▼
┌─────────────────────┐
│ SOUTENANCE_EN_COURS │
└──────────┬──────────┘
           │ Notes validées
           ▼
┌──────────────────────┐
│ SOUTENANCE_TERMINEE  │
└──────────┬───────────┘
           │ Corrections finales validées
           ▼
┌─────────────────────┐
│ DIPLOME_DELIVRE (T) │
└─────────────────────┘
```

### États Spéciaux
- **ABANDON** : État terminal déclaratif
- **ESCALADE_DOYEN** : Blocage commission après 3 tours
- **CORRECTIONS_DEMANDEES** : Boucle de correction rapport

---

## Modules du Système

### Structure Modulaire

| Module | PRD | Description |
|--------|-----|-------------|
| Authentification & Utilisateurs | `01_authentication_users.md` | Sessions, permissions, rôles |
| Entités Académiques | `02_academic_entities.md` | Étudiants, enseignants, UE |
| Workflow & Commission | `03_workflow_commission.md` | États, transitions, votes |
| Mémoire & Soutenance | `04_thesis_defense.md` | Rapports, jury, notes |
| Communication | `05_communication.md` | Notifications, messagerie |
| Documents & Archives | `06_documents_archives.md` | PDF, archivage, historisation |
| Financier | `07_financial.md` | Paiements, pénalités |
| Administration | `08_administration.md` | Configuration, audit |

### Dépendances Inter-Modules

```
┌───────────────────┐
│ Authentification  │◄───────────────────────────────────┐
└─────────┬─────────┘                                    │
          │                                              │
          ▼                                              │
┌───────────────────┐     ┌───────────────────┐          │
│    Permissions    │◄────│   Administration  │          │
└─────────┬─────────┘     └───────────────────┘          │
          │                                              │
          ├──────────────────┬───────────────────┐       │
          ▼                  ▼                   ▼       │
┌───────────────────┐ ┌───────────────┐ ┌─────────────┐  │
│     Workflow      │ │  Communication │ │  Documents  │  │
└─────────┬─────────┘ └───────┬───────┘ └──────┬──────┘  │
          │                   │                │         │
          ▼                   ▼                ▼         │
┌──────────────────────────────────────────────────────┐ │
│                  Mémoire & Soutenance                │─┘
└──────────────────────────────────────────────────────┘
          │
          ▼
┌───────────────────┐     ┌───────────────────┐
│  Entités Acad.    │─────│    Financier      │
└───────────────────┘     └───────────────────┘
```

---

## Fonctionnalités Transversales

### Services Primordiaux

| Service | Criticité | Fonction |
|---------|-----------|----------|
| ServiceAudit | 🔴 Critique | Traçabilité complète, snapshots JSON |
| ServiceAuthentification | 🔴 Critique | Connexion sécurisée, sessions |
| ServicePermission | 🔴 Critique | RBAC, cache, rôles temporaires |
| ServiceNotification | 🔴 Critique | Multi-canal (Email, SMS, Messagerie) |
| ServiceWorkflow | 🔴 Critique | Machine à états, transitions |
| ServiceEscalade | 🔴 Critique | Médiation, déblocage |
| ServiceCalendrier | 🟠 Élevé | Disponibilités, conflits |
| ServicePdf | 🟠 Élevé | 13 types documents |
| ServiceArchivage | 🟠 Élevé | Intégrité SHA256 |
| ServiceSignature | 🟡 Moyen | Optionnel, OTP |

### Règles Métier Non-Négociables

1. **Gate Critique** : L'onglet "Rédaction rapport" invisible tant que `état != candidature_validée`
2. **Création Utilisateur** : L'entité métier (étudiant/enseignant) DOIT exister AVANT le compte
3. **Numéro Carte** : Format `CI01552852` (VARCHAR 20), unique, non modifiable
4. **Archivage** : Hash SHA256 obligatoire, documents inaltérables
5. **Audit** : Double journalisation (fichier + base), pas de suppression

---

## Critères de Succès Globaux

### Performance
- Temps de réponse < 200ms pour 95% des requêtes
- Support de 500 utilisateurs simultanés
- Génération PDF < 5 secondes

### Fiabilité
- Disponibilité 99.5% (hors maintenance planifiée)
- Zéro perte de données sur 10 ans
- Récupération < 4 heures après incident

### Sécurité
- Aucun accès non autorisé détecté
- 100% des actions critiques auditées
- Conformité RGPD

### Utilisabilité
- Taux d'abandon formulaires < 5%
- Formation utilisateur < 2 heures
- Support mobile (responsive)

---

## Documents Générés (13 Types)

| Type | Générateur | Usage |
|------|------------|-------|
| Reçu de paiement | TCPDF | Après versement |
| Reçu de pénalité | TCPDF | Après paiement pénalité |
| Bulletin de notes | TCPDF | Fin semestre |
| Attestation inscription | TCPDF | Sur demande |
| PV Commission | mPDF | Fin session |
| PV Soutenance | mPDF | Après délibération |
| Convocation Commission | TCPDF | Avant session |
| Convocation Jury | TCPDF | Avant soutenance |
| Fiche notation | TCPDF | Jour soutenance |
| Attestation réussite | mPDF | Post-soutenance |
| Attestation diplôme | mPDF | Fin processus |
| Relevé de notes | TCPDF | Sur demande |
| Page de garde rapport | mPDF | Soumission |

---

## Hors Périmètre (V1)

- Intégration SI universitaire externe (APOGEE)
- Application mobile native
- Détection de plagiat automatique
- Blockchain pour diplômes
- Multi-langue (français uniquement)
- Paiement en ligne

---

## Références

- [Constitution](../constitution.md) - Principes non-négociables
- [Workflows](../workflows.md) - Documentation processus
- [Workbench](../workbench.md) - Guide implémentation
- [API](../api.yaml) - Spécifications API
