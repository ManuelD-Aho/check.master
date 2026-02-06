# PRD Diagrammes - Spécifications UML

## 1. Vue d'ensemble

Ce document définit les diagrammes à produire pour documenter l'architecture et le comportement du système.

---

## 2. Diagrammes de Classes (UML)

### 2.1 Package User

```
┌─────────────────────────┐
│     TypeUtilisateur     │
├─────────────────────────┤
│ - id: int               │
│ - libelle: string       │
├─────────────────────────┤
│ + getUtilisateurs(): [] │
└───────────┬─────────────┘
            │ 1
            │
            │ *
┌───────────▼─────────────┐       ┌─────────────────────────┐
│   GroupeUtilisateur     │       │    NiveauAccesDonnees   │
├─────────────────────────┤       ├─────────────────────────┤
│ - id: int               │       │ - id: int               │
│ - libelle: string       │       │ - libelle: string       │
│ - actif: bool           │       │ - code: string          │
├─────────────────────────┤       └───────────┬─────────────┘
│ + getPermissions(): []  │                   │
└───────────┬─────────────┘                   │
            │ 1                               │ 1
            │                                 │
            │ *                               │ *
┌───────────▼─────────────────────────────────▼─────────────┐
│                       Utilisateur                          │
├────────────────────────────────────────────────────────────┤
│ - id: int                                                  │
│ - login: string                                            │
│ - motDePasseHash: string                                   │
│ - email: string                                            │
│ - statut: enum                                             │
│ - secret2fa: string?                                       │
│ - is2faEnabled: bool                                       │
│ - derniereConnexion: datetime?                             │
├────────────────────────────────────────────────────────────┤
│ + verifyPassword(password: string): bool                   │
│ + hasPermission(fonctionnalite: string, action: string)    │
│ + isActive(): bool                                         │
│ + getSourceEntity(): Etudiant|Enseignant|PersonnelAdmin    │
└────────────────────────────────────────────────────────────┘
            ▲ 1
            │
            │ 1
┌───────────┴─────────────┐  ┌─────────────────┐  ┌─────────────────┐
│       Etudiant          │  │   Enseignant    │  │ PersonnelAdmin  │
├─────────────────────────┤  ├─────────────────┤  ├─────────────────┤
│ - matricule: string(PK) │  │ - id: int       │  │ - id: int       │
│ - nom: string           │  │ - nom: string   │  │ - nom: string   │
│ - prenom: string        │  │ - prenom: string│  │ - prenom: string│
│ - email: string         │  │ - email: string │  │ - email: string │
│ ...                     │  │ ...             │  │ ...             │
└─────────────────────────┘  └─────────────────┘  └─────────────────┘
```

### 2.2 Package Academic

```
┌─────────────────────────┐
│    AnneeAcademique      │
├─────────────────────────┤
│ - id: int               │
│ - libelle: string       │
│ - dateDebut: date       │
│ - dateFin: date         │
│ - estActive: bool       │
│ - estOuverte: bool      │
└───────────┬─────────────┘
            │ 1
            ├─────────────────────────┐
            │ *                       │ *
┌───────────▼─────────────┐ ┌─────────▼─────────────┐
│      NiveauEtude        │ │     Inscription       │
├─────────────────────────┤ ├───────────────────────┤
│ - id: int               │ │ - id: int             │
│ - code: string          │ │ - dateInscription     │
│ - libelle: string       │ │ - statut: enum        │
│ - montantScolarite      │ │ - montantPaye         │
│ - montantInscription    │ │ - resteAPayer         │
└───────────┬─────────────┘ └───────────────────────┘
            │ 1
            │ *
┌───────────▼─────────────┐
│        Semestre         │
├─────────────────────────┤
│ - id: int               │
│ - code: string          │
│ - libelle: string       │
└───────────┬─────────────┘
            │ 1
            │ *
┌───────────▼─────────────┐
│  UniteEnseignement (UE) │
├─────────────────────────┤
│ - id: int               │
│ - code: string          │
│ - libelle: string       │
│ - credit: int           │
└───────────┬─────────────┘
            │ 1
            │ *
┌───────────▼─────────────┐
│ ElementConstitutif(ECUE)│
├─────────────────────────┤
│ - id: int               │
│ - code: string          │
│ - libelle: string       │
│ - credit: int           │
└─────────────────────────┘
```

### 2.3 Package Stage & Rapport

```
┌─────────────────────────┐         ┌─────────────────────────┐
│       Entreprise        │         │       Etudiant          │
├─────────────────────────┤         ├─────────────────────────┤
│ - id: int               │         │ - matricule: string     │
│ - raisonSociale: string │         │ ...                     │
│ - secteur: string       │         └───────────┬─────────────┘
│ - adresse: text         │                     │ 1
└───────────┬─────────────┘                     │
            │ 1                                 │ 1
            │                                   │
            │ *                                 │ *
┌───────────▼─────────────┐         ┌───────────▼─────────────┐
│    InformationStage     │◄────────│      Candidature        │
├─────────────────────────┤    1  1 ├─────────────────────────┤
│ - id: int               │         │ - id: int               │
│ - sujet: string         │         │ - statut: enum          │
│ - description: text     │         │ - dateCreation          │
│ - dateDebut: date       │         │ - dateSoumission        │
│ - dateFin: date         │         │ - dateTraitement        │
│ - nomEncadrant: string  │         │ - commentaire: text     │
│ - emailEncadrant: string│         └───────────┬─────────────┘
└─────────────────────────┘                     │
                                                │ Workflow:
                                    [brouillon] → [soumise] → [validee]
                                                         ↘ [rejetee]

┌─────────────────────────────────────────────────────────────────────┐
│                             Rapport                                  │
├─────────────────────────────────────────────────────────────────────┤
│ - id: int                                                           │
│ - titre: string                                                     │
│ - theme: string                                                     │
│ - contenuHtml: longtext                                             │
│ - statut: enum                                                      │
│ - nombreMots: int                                                   │
│ - versionCourante: int                                              │
│ - cheminPdf: string                                                 │
├─────────────────────────────────────────────────────────────────────┤
│ + getVersions(): VersionRapport[]                                   │
│ + submit(): void                                                    │
│ + isEditable(): bool                                                │
│ + getWordCount(): int                                               │
└─────────────────────────────────────────────────────────────────────┘
            │ 1
            │ *
┌───────────▼─────────────┐
│     VersionRapport      │
├─────────────────────────┤
│ - id: int               │
│ - numero: int           │
│ - contenuHtml: longtext │
│ - type: enum            │
│ - dateCreation          │
└─────────────────────────┘
```

### 2.4 Package Commission & Soutenance

```
┌──────────────────────┐    ┌──────────────────────┐
│  MembreCommission    │    │     Rapport          │
├──────────────────────┤    ├──────────────────────┤
│ - id: int            │    │ - id: int            │
│ - utilisateur        │    │ - statut: enum       │
│ - role: enum         │    └──────────┬───────────┘
│ - actif: bool        │               │ 1
└──────────┬───────────┘               │
           │ 1                         │ *
           │                 ┌─────────▼───────────┐
           │ *               │  EvaluationRapport  │
┌──────────▼───────────┐     ├─────────────────────┤
│ - decision: enum     │────▶│ - id: int           │
│ - commentaire        │ 1 * │ - numeroCycle: int  │
│ - dateEvaluation     │     │ - decision: enum    │
└──────────────────────┘     │ - commentaire: text │
                             └─────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│                            Jury                                   │
├──────────────────────────────────────────────────────────────────┤
│ - id: int                                                        │
│ - etudiant: Etudiant                                             │
│ - statut: enum                                                   │
├──────────────────────────────────────────────────────────────────┤
│ + getComposition(): CompositionJury[]                            │
│ + isComplete(): bool                                             │
└────────────────────────────────┬─────────────────────────────────┘
                                 │ 1
                                 │ 5
                      ┌──────────▼───────────┐
                      │   CompositionJury    │
                      ├──────────────────────┤
                      │ - enseignant         │
                      │ - roleJury           │
                      │ - estPresent: bool?  │
                      └──────────────────────┘

┌──────────────────────────────────────────────────────────────────┐
│                         Soutenance                                │
├──────────────────────────────────────────────────────────────────┤
│ - id: int                                                        │
│ - jury: Jury                                                     │
│ - salle: Salle                                                   │
│ - dateSoutenance: date                                           │
│ - heureDebut: time                                               │
│ - dureeMinutes: int                                              │
│ - theme: string                                                  │
│ - statut: enum                                                   │
├──────────────────────────────────────────────────────────────────┤
│ + getNotes(): NoteSoutenance[]                                   │
│ + calculateTotal(): BigDecimal                                   │
└──────────────────────────────────────────────────────────────────┘
```

---

## 3. Diagrammes d'États (State Machine)

### 3.1 États de la Candidature

```
                          ┌─────────────┐
                          │  BROUILLON  │ (Initial)
                          └──────┬──────┘
                                 │
                                 │ soumettre()
                                 │ [tous champs remplis]
                                 ▼
                          ┌─────────────┐
              ┌──────────▶│   SOUMISE   │◀──────────┐
              │           └──────┬──────┘           │
              │                  │                  │
              │     ┌────────────┼────────────┐     │
              │     │            │            │     │
              │     │ valider()  │ rejeter()  │     │
              │     │            │ [commentaire]    │
              │     ▼            ▼            │     │
              │ ┌─────────┐  ┌─────────┐      │     │
              │ │ VALIDEE │  │ REJETEE │──────┘     │
              │ └─────────┘  └────┬────┘            │
              │    (Final)       │                  │
              │                  │ reSoumettre()    │
              │                  │ [modifié]        │
              └──────────────────┴──────────────────┘
```

### 3.2 États du Rapport

```
                          ┌─────────────┐
                          │  BROUILLON  │ (Initial)
                          └──────┬──────┘
                                 │
                                 │ soumettre()
                                 │ [minWords >= 5000]
                                 ▼
                          ┌─────────────┐
              ┌──────────▶│   SOUMIS    │◀──────────┐
              │           └──────┬──────┘           │
              │                  │                  │
              │     ┌────────────┼────────────┐     │
              │     │            │            │     │
              │     │ approuver()│ retourner()│     │
              │     │            │            │     │
              │     ▼            ▼            │     │
              │ ┌─────────┐  ┌──────────┐    │     │
              │ │APPROUVE │  │ RETOURNE │────┘     │
              │ └────┬────┘  └──────────┘          │
              │      │                             │
              │      │ transferer()                │
              │      ▼                             │
              │ ┌────────────────┐                 │
              │ │ EN_COMMISSION  │                 │
              │ └────────────────┘                 │
              │                                    │
              │    ... (suite workflow commission) │
              └────────────────────────────────────┘
```

### 3.3 États du Rapport en Commission

```
┌────────────────────────┐
│ EN_ATTENTE_EVALUATION  │ (Initial - venant de Module 4)
└──────────┬─────────────┘
           │
           │ premierVote()
           ▼
┌────────────────────────┐
│  EN_COURS_EVALUATION   │◀──────────────────────────┐
└──────────┬─────────────┘                           │
           │                                         │
           │ [4 votes reçus]                         │
           ▼                                         │
┌────────────────────────┐                           │
│     VOTE_COMPLET       │                           │
└──────────┬─────────────┘                           │
           │                                         │
           ├───────────────────┬─────────────────────┤
           │                   │                     │
           │ [4 OUI]           │ [4 NON]             │ [Mixte]
           ▼                   ▼                     ▼
┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│ VOTE_UNANIME_OUI │  │ VOTE_UNANIME_NON │  │  VOTE_NON_UNANIME│
└────────┬─────────┘  └────────┬─────────┘  └────────┬─────────┘
         │                     │                     │
         │                     │                     │ relancer()
         │                     │                     └─────────────┘
         │                     │
         │ assigner()          │ retourner()
         ▼                     ▼
┌────────────────────┐  ┌────────────────────┐
│ ASSIGNER_ENCADRANTS│  │ RETOURNE_ETUDIANT  │
└────────┬───────────┘  └────────────────────┘
         │                    (→ Module 4)
         │ finaliser()
         ▼
┌────────────────────┐
│    PRET_POUR_PV    │ → Module 6 (Soutenance)
└────────────────────┘
```

### 3.4 États de la Soutenance

```
┌────────────────────────┐
│  ENCADRANTS_ASSIGNES   │ (Initial - venant de Module 5)
└──────────┬─────────────┘
           │
           │ validerAptitude()
           │ [par encadreur pédagogique]
           ▼
┌────────────────────────┐
│   APTITUDE_VALIDEE     │
└──────────┬─────────────┘
           │
           │ composerJury()
           │ [5 membres]
           ▼
┌────────────────────────┐
│     JURY_COMPOSE       │
└──────────┬─────────────┘
           │
           │ programmer()
           │ [date, heure, salle]
           ▼
┌────────────────────────┐
│ SOUTENANCE_PROGRAMMEE  │
└──────────┬─────────────┘
           │
           │ [date soutenance atteinte]
           ▼
┌────────────────────────┐
│ SOUTENANCE_EFFECTUEE   │
└──────────┬─────────────┘
           │
           │ saisirNotes()
           │ [tous critères]
           ▼
┌────────────────────────┐
│     NOTES_SAISIES      │
└──────────┬─────────────┘
           │
           │ deliberer()
           │ [calcul moyenne, mention]
           ▼
┌────────────────────────┐
│       DELIBERE         │ (Final)
└────────────────────────┘
           │
           │ genererPV()
           ▼
    [Génération Annexes 1, 2 ou 3]
```

---

## 4. Diagrammes de Séquence

### 4.1 Connexion utilisateur

```
┌────────┐     ┌────────────────┐     ┌──────────────┐     ┌─────────────┐     ┌──────────┐
│ Client │     │LoginController │     │AuthService   │     │RateLimiter  │     │UserRepo  │
└───┬────┘     └───────┬────────┘     └──────┬───────┘     └──────┬──────┘     └────┬─────┘
    │                  │                     │                    │                 │
    │ POST /login      │                     │                    │                 │
    │ {login,password} │                     │                    │                 │
    │─────────────────▶│                     │                    │                 │
    │                  │                     │                    │                 │
    │                  │ checkRateLimit(ip)  │                    │                 │
    │                  │────────────────────────────────────────▶ │                 │
    │                  │                     │                    │                 │
    │                  │                     │     isAllowed()    │                 │
    │                  │◀───────────────────────────────────────  │                 │
    │                  │                     │                    │                 │
    │                  │ authenticate(login, password)           │                 │
    │                  │────────────────────▶│                    │                 │
    │                  │                     │                    │                 │
    │                  │                     │ findByLogin(login) │                 │
    │                  │                     │───────────────────────────────────▶  │
    │                  │                     │                    │                 │
    │                  │                     │◀──────────────────────── user        │
    │                  │                     │                    │                 │
    │                  │                     │ verifyPassword()   │                 │
    │                  │                     │ ─────────┐         │                 │
    │                  │                     │          │         │                 │
    │                  │                     │ ◀────────┘         │                 │
    │                  │                     │                    │                 │
    │                  │◀─── AuthResult(user, needsTwoFactor)     │                 │
    │                  │                     │                    │                 │
    │                  │ [if 2FA enabled]    │                    │                 │
    │ redirect /2fa    │                     │                    │                 │
    │◀─────────────────│                     │                    │                 │
    │                  │                     │                    │                 │
    │                  │ [if success]        │                    │                 │
    │ redirect /dashboard                    │                    │                 │
    │◀─────────────────│                     │                    │                 │
```

### 4.2 Soumission de candidature

```
┌────────┐  ┌───────────────────┐  ┌──────────────────┐  ┌────────────┐  ┌────────────┐
│Etudiant│  │CandidatureCtrl    │  │CandidatureService│  │Workflow    │  │EmailService│
└───┬────┘  └─────────┬─────────┘  └────────┬─────────┘  └──────┬─────┘  └──────┬─────┘
    │                 │                     │                   │               │
    │ POST /soumettre │                     │                   │               │
    │────────────────▶│                     │                   │               │
    │                 │                     │                   │               │
    │                 │ submit(candidature) │                   │               │
    │                 │────────────────────▶│                   │               │
    │                 │                     │                   │               │
    │                 │                     │ can('soumettre')  │               │
    │                 │                     │──────────────────▶│               │
    │                 │                     │                   │               │
    │                 │                     │◀─────── true ─────│               │
    │                 │                     │                   │               │
    │                 │                     │ apply('soumettre')│               │
    │                 │                     │──────────────────▶│               │
    │                 │                     │                   │               │
    │                 │                     │◀──────────────────│               │
    │                 │                     │                   │               │
    │                 │                     │ notifyValidateurs()               │
    │                 │                     │──────────────────────────────────▶│
    │                 │                     │                   │               │
    │                 │                     │◀──────────────────────────────────│
    │                 │                     │                   │               │
    │                 │◀──── success ───────│                   │               │
    │                 │                     │                   │               │
    │◀── redirect ────│                     │                   │               │
```

### 4.3 Vote commission (cycle complet)

```
┌────────┐  ┌─────────────┐  ┌────────────┐  ┌─────────────┐  ┌────────────┐
│Membre  │  │EvalCtrl     │  │VoteService │  │EmailService │  │EventDispatcher│
└───┬────┘  └──────┬──────┘  └──────┬─────┘  └──────┬──────┘  └──────┬─────┘
    │              │                │               │                │
    │ POST /vote   │                │               │                │
    │─────────────▶│                │               │                │
    │              │                │               │                │
    │              │submitVote(rapport,membre,decision)             │
    │              │───────────────▶│               │                │
    │              │                │               │                │
    │              │                │ save vote     │                │
    │              │                │ ──────┐       │                │
    │              │                │ ◀─────┘       │                │
    │              │                │               │                │
    │              │                │ isComplete()? │                │
    │              │                │ ──────┐       │                │
    │              │                │ ◀─────┘ [3/4] │                │
    │              │                │               │                │
    │              │                │ notifyProgress()              │
    │              │                │──────────────▶│                │
    │              │                │               │                │
    │              │◀─── success ───│               │                │
    │              │                │               │                │
    │◀─────────────│                │               │                │
    │              │                │               │                │
    ═══════════════════════════════════════════════════════════════════
    │ [4ème vote] │                 │               │                │
    ═══════════════════════════════════════════════════════════════════
    │              │                │               │                │
    │ POST /vote   │                │               │                │
    │─────────────▶│                │               │                │
    │              │                │               │                │
    │              │submitVote(...)│                │                │
    │              │───────────────▶│               │                │
    │              │                │               │                │
    │              │                │ isComplete()? │                │
    │              │                │ ──────┐       │                │
    │              │                │ ◀─────┘ [4/4 = YES]            │
    │              │                │               │                │
    │              │                │ calculateResult()             │
    │              │                │ ──────┐       │                │
    │              │                │ ◀─────┘ UNANIME_OUI           │
    │              │                │               │                │
    │              │                │ dispatch(VoteCompleteEvent)   │
    │              │                │───────────────────────────────▶│
    │              │                │               │                │
    │              │                │◀──────────────────────────────│
    │              │                │               │                │
    │              │◀─── result ────│               │                │
    │              │                │               │                │
    │◀── redirect ─│                │               │                │
```

---

## 5. Diagramme de Déploiement

```
┌─────────────────────────────────────────────────────────────────────────┐
│                     SERVEUR MUTUALISÉ (HÉBERGEMENT)                      │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌───────────────────────────────────────────────────────────────────┐  │
│  │                         APACHE HTTP SERVER                         │  │
│  │                                                                    │  │
│  │   ┌────────────┐    ┌─────────────────────────────────────────┐   │  │
│  │   │ .htaccess  │───▶│           mod_rewrite                   │   │  │
│  │   │ (réécriture)    │           mod_php                       │   │  │
│  │   └────────────┘    └─────────────────────────────────────────┘   │  │
│  │                                                                    │  │
│  └───────────────────────────────────────────────────────────────────┘  │
│                              │                                           │
│                              ▼                                           │
│  ┌───────────────────────────────────────────────────────────────────┐  │
│  │                          PHP 8.4                                   │  │
│  │                                                                    │  │
│  │   ┌──────────────────────────────────────────────────────────┐    │  │
│  │   │                  APPLICATION MIAGE                        │    │  │
│  │   │                                                           │    │  │
│  │   │   public/         src/           templates/               │    │  │
│  │   │   ├─ index.php    ├─ Controller/ ├─ admin/               │    │  │
│  │   │   └─ assets/      ├─ Service/    ├─ etudiant/            │    │  │
│  │   │                   ├─ Entity/     └─ ...                   │    │  │
│  │   │                   └─ ...                                  │    │  │
│  │   │                                                           │    │  │
│  │   │   vendor/         storage/       config/                  │    │  │
│  │   │   (composer)      ├─ logs/       ├─ routes.php           │    │  │
│  │   │                   ├─ cache/      └─ ...                   │    │  │
│  │   │                   └─ documents/                           │    │  │
│  │   │                                                           │    │  │
│  │   └──────────────────────────────────────────────────────────┘    │  │
│  │                                                                    │  │
│  └───────────────────────────────────────────────────────────────────┘  │
│                              │                                           │
│                              ▼                                           │
│  ┌───────────────────────────────────────────────────────────────────┐  │
│  │                        MySQL 8.0+                                  │  │
│  │                                                                    │  │
│  │   ┌─────────────┐  ┌─────────────┐  ┌─────────────┐               │  │
│  │   │ miage_db    │  │ Tables:     │  │ Indexes     │               │  │
│  │   │             │  │ - utilisateur│  │             │               │  │
│  │   │ charset:    │  │ - etudiant  │  │             │               │  │
│  │   │ utf8mb4     │  │ - rapport   │  │             │               │  │
│  │   │             │  │ - ...       │  │             │               │  │
│  │   └─────────────┘  └─────────────┘  └─────────────┘               │  │
│  │                                                                    │  │
│  └───────────────────────────────────────────────────────────────────┘  │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                           SERVICES EXTERNES                              │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│   ┌─────────────┐                                                       │
│   │ SMTP Server │  ◀──────── PHPMailer (notifications)                 │
│   │             │                                                       │
│   └─────────────┘                                                       │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 6. Diagramme de Composants

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              <<component>>                                   │
│                              APPLICATION                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│   ┌───────────────┐     ┌───────────────┐     ┌───────────────┐            │
│   │   <<component>>│     │   <<component>>│     │   <<component>>│            │
│   │   PRESENTATION │────▶│   BUSINESS    │────▶│   PERSISTENCE │            │
│   │   (Controllers)│     │   (Services)  │     │   (Repositories)           │
│   └───────────────┘     └───────────────┘     └───────────────┘            │
│          │                      │                      │                    │
│          │                      │                      │                    │
│          ▼                      ▼                      ▼                    │
│   ┌───────────────┐     ┌───────────────┐     ┌───────────────┐            │
│   │   <<component>>│     │   <<component>>│     │   <<component>>│            │
│   │   VIEW        │     │   WORKFLOW    │     │   DOCTRINE ORM│            │
│   │   (Templates) │     │   (Symfony)   │     │               │            │
│   └───────────────┘     └───────────────┘     └───────────────┘            │
│                                 │                      │                    │
│                                 │                      │                    │
│   ┌───────────────┐     ┌───────────────┐             │                    │
│   │   <<component>>│     │   <<component>>│             │                    │
│   │   SECURITY    │     │   EVENT       │             │                    │
│   │   (Auth/Perms)│     │   (Dispatcher)│             │                    │
│   └───────────────┘     └───────────────┘             │                    │
│                                                        │                    │
│   ┌───────────────┐     ┌───────────────┐             │                    │
│   │   <<component>>│     │   <<component>>│             │                    │
│   │   DOCUMENT    │     │   EMAIL       │             │                    │
│   │   (PDF Gen)   │     │   (PHPMailer) │             │                    │
│   └───────────────┘     └───────────────┘             │                    │
│                                                        │                    │
│                                                        ▼                    │
│                                                ┌───────────────┐            │
│                                                │   <<database>>│            │
│                                                │   MySQL       │            │
│                                                └───────────────┘            │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```
