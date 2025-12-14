# CheckMaster - Workflow Documentation

**Version**: 1.0.0

## Core Workflow States

CheckMaster implements a 14-state thesis supervision workflow:

```
INSCRIT 
  ↓ Student submits candidature
CANDIDATURE_SOUMISE 
  ↓ Scolarité validates (payment + documents)
VERIFICATION_SCOLARITE 
  ↓ Communication validates format
FILTRE_COMMUNICATION 
  ↓ Passes to commission
EN_ATTENTE_COMMISSION 
  ↓ Commission session scheduled
EN_EVALUATION_COMMISSION 
  ↓ Votes collected (unanimity or escalation)
RAPPORT_VALIDE 
  ↓ Directeur + encadreur assigned
ATTENTE_AVIS_ENCADREUR 
  ↓ Favorable opinion given
PRET_POUR_JURY 
  ↓ 5 jury members accept
JURY_EN_CONSTITUTION 
  ↓ Date/time/room scheduled
SOUTENANCE_PLANIFIEE 
  ↓ Defense day, notes entered
SOUTENANCE_EN_COURS 
  ↓ Président jury validates
SOUTENANCE_TERMINEE 
  ↓ Final version approved
DIPLOME_DELIVRE (Terminal)
```

## Critical Gate

**Report Writing Blocked** until `candidature_validee` state.

Implemented in `WorkflowGateMiddleware.php`:
- Checks dossier état_actuel
- Returns 403 if not candidature_validee
- Applied to route `/etudiant/rapport/*`

## Commission Voting Process

**3-Round Maximum with Escalation**:

1. **Round 1** (48h): All members vote (Valider/À revoir/Rejeter)
   - Unanimity → Decision final
   - Divergence → Round 2

2. **Round 2** (48h): Revote with previous results visible
   - Unanimity → Decision final
   - Divergence → Round 3

3. **Round 3** (24h - FINAL): Last chance
   - Unanimity → Decision final
   - Divergence → **ESCALATE to Dean**

4. **Dean Mediation** (5 days): Arbitral binding decision

## User Group Permissions

**13 Groups**:
1. Administrateur (5): Full control
2. Secrétaire (6): Documents
3. Communication (7): Format verification
4. Scolarité (8): Payments, candidature
5. Resp. Filière (9): MIAGE oversight
6. Resp. Niveau (10): Master 2 management
7. Commission (11): Evaluation, voting
8. Enseignant (12): Supervision, jury
9. Étudiant (13): Report writing
10. Président Commission: Jury constitution
11. Président Jury (Temp): Defense day notes
12. Directeur Mémoire: Thesis supervision
13. Encadreur Pédagogique: Guidance

## Notification Triggers

**71 Email Templates** triggered at workflow transitions:
- Candidature submitted → Student + Scolarité
- Scolarité validated → Student (unlock) + Communication
- Commission vote rounds → All members
- Escalation → Dean + Members
- Rapport validated → Student + Encadreurs
- Jury constituted → 5 members + Maître stage
- Defense day → Code OTP to Président (SMS + Email)
- Results → Student with mention

## SLA & Escalations

**Automatic Alerting**:
- 50% of délai → Reminder
- 80% of délai → Warning
- 100% of délai → Escalation

**Examples**:
- Scolarité verification: 5 days
- Communication check: 3 days
- Commission session: Monthly
- Avis favorable: 15 days
- Final corrections: 10 days

## Document Generation

**13 PDF Types**:
- Reçus (payment/pénalité) - TCPDF
- Bulletins (notes) - TCPDF
- PV (commission/soutenance) - mPDF
- Convocations - TCPDF
- Attestation diplôme - mPDF

All documents:
- Calculate SHA256 hash
- Archive with integrity check
- Send download notification

---

**See Also**: constitution.md, canvas.md, workbench.md
