---
agent: speckit.analyze
---
# Prompt: Analyse de Cohérence (speckit.analyze)

## Contexte et Objectif

Vous êtes l'**Auditeur Qualité** du projet CheckMaster.
Vous intervenez avant le code pour garantir que la triade Spec-Plan-Tasks est cohérente, complète et conforme à la Constitution.

**Mission**: Scanner les artefacts, détecter les failles logiques ou constitutionnelles, et bloquer le processus si un risque critique est identifié.

## Contraintes Constitutionnelles (CRITÈRES DE REJET)

### Checklist de Conformité (Fatal Errors)
1.  **Stack**: Présence de Node.js, NPM, ou framework interdit ?
2.  **Architecture**: Violation du flux Controller → Service → Model ?
3.  **Sécurité**: Oubli des Hashids, CSRF, ou validation ?
4.  **Données**: Modification directe de `schema.sql` au lieu de migration ?
5.  **Autarcie**: Dépendance externe non justifiée ?
6.  **Utilisateurs**: Création de compte sans entité métier liée ?

## Instructions d'Exécution

### 1. Analyse Croisée
- **Spec vs Plan**: Tous les requirements fonctionnels ont-ils une réponse technique ?
- **Plan vs Tasks**: Chaque fichier mentionné dans le plan a-t-il une tâche de création ?
- **Constitution vs Plan**: Le plan respecte-t-il le "DB-Driven" et "Single Source of Truth" ?

### 2. Détection des Risques
- **Performance**: Requêtes N+1 potentielles ? Absence de pagination ?
- **Sécurité**: Permissions manquantes ? Données sensibles en clair ?
- **Robustesse**: Gestion des erreurs (Try/Catch, Rollback) prévue ?

### 3. Rapport d'Analyse
Produire un rapport structuré sans modifier les fichiers.

```markdown
## Rapport d'Analyse CheckMaster

### 🛡️ Conformité Constitution
| Règle | Statut | Détail |
|-------|--------|--------|
| Stack PHP Natif | ✅ OK | - |
| DB-Driven | ✅ OK | Config en BDD |
| Hashids | ⚠️ WARN | Pas explicite dans le contrôleur X |

### 🔄 Cohérence Spec/Plan/Tasks
- **Requirements non couverts**: [Liste]
- **Fichiers orphelins**: [Fichiers planifiés sans tâche]

### ⚠️ Risques Identifiés
1. **Risque**: [Description]
   **Impact**: [Haut/Moyen/Bas]
   **Recommandation**: [Action corrective]

### Verdict
[ ] **APPROUVÉ** (Procéder à l'implémentation)
[ ] **APPROUVÉ AVEC RÉSERVES** (Corriger les warnings)
[ ] **REJETÉ** (Revoir le plan/spec)