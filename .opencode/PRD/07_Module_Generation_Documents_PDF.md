# PRD Module 7 : Génération de Documents (PDF, PV)

## 1. Vue d'ensemble

### 1.1 Objectif du module
Ce module centralise la génération de tous les documents PDF officiels de la plateforme : reçus de paiement, bulletins, rapports, PV de commission, plannings de soutenances, et les trois annexes finales (grille d'évaluation, PV standard, PV simplifié).

### 1.2 Documents générés par la plateforme
| Document | Module source | Déclencheur |
|----------|---------------|-------------|
| Reçu de paiement | Module 2 (Inscriptions) | Après enregistrement d'un versement |
| Bulletin de notes provisoire | Module 2 (Étudiants) | Sur demande admin/étudiant |
| Rapport de stage (PDF) | Module 4 (Rapports) | Après soumission du rapport |
| PV Commission | Module 5 (Commission) | Après finalisation du compte-rendu |
| Tableau des soutenances | Module 6 (Soutenances) | Sur demande admin |
| Annexe 1 : Grille d'évaluation | Module 6 (Soutenances) | Après notation soutenance |
| Annexe 2 : PV Jury Standard | Module 6 (Soutenances) | Après délibération (type standard) |
| Annexe 3 : PV Jury Simplifié | Module 6 (Soutenances) | Après délibération (type simplifié) |
| PV Finaux (3 en 1) | Module 6 (Soutenances) | Compilation des 3 annexes |

### 1.3 Bibliothèques utilisées
| Bibliothèque | Rôle |
|--------------|------|
| `tecnickcom/tcpdf` | Génération PDF principale (PV, grilles, tableaux) |
| `phpoffice/phpword` | Conversion documents Word vers PDF |
| `league/csv` | Export données tabulaires en CSV |
| `monolog/monolog` | Journalisation des générations |
| `defuse/php-encryption` | Protection des documents sensibles |
| `symfony/string` | Manipulation des textes pour PDF |

---

## 2. Architecture technique

### 2.1 Service centralisé de génération

```php
namespace App\Service\Document;

class DocumentGenerator
{
    public function generate(string $type, array $data): GeneratedDocument;
    public function preview(string $type, array $data): string; // HTML preview
    public function store(GeneratedDocument $doc): string; // Retourne le chemin
    public function getByReference(string $reference): ?GeneratedDocument;
}
```

### 2.2 Structure des documents générés

```php
class GeneratedDocument
{
    private string $reference;      // REF unique (ex: "REC-2025-00001")
    private string $type;           // Type de document
    private string $filename;       // Nom du fichier
    private string $filePath;       // Chemin complet
    private int $fileSize;          // Taille en octets
    private string $mimeType;       // application/pdf
    private array $metadata;        // Données contextuelles
    private DateTime $generatedAt;  // Date de génération
    private int $generatedBy;       // ID utilisateur
}
```

### 2.3 Stockage des fichiers

```
/storage/
├── documents/
│   ├── recus/
│   │   └── 2025/
│   │       └── REC-2025-00001.pdf
│   ├── bulletins/
│   │   └── 2025/
│   │       └── BUL-2025-00001.pdf
│   ├── rapports/
│   │   └── 2025/
│   │       └── RAP-2025-00001.pdf
│   ├── pv_commission/
│   │   └── 2025/
│   │       └── PVC-2025-00001.pdf
│   ├── planning/
│   │   └── 2025/
│   │       └── PLN-2025-001.pdf
│   └── pv_finaux/
│       └── 2025/
│           ├── ANX1-2025-00001.pdf
│           ├── ANX2-2025-00001.pdf
│           ├── ANX3-2025-00001.pdf
│           └── PVF-2025-00001.pdf  (3 en 1)
```

### 2.4 Référencement des documents

**Format de référence** : `[TYPE]-[ANNÉE]-[SÉQUENCE]`

| Type | Préfixe | Exemple |
|------|---------|---------|
| Reçu de paiement | REC | REC-2025-00001 |
| Bulletin | BUL | BUL-2025-00001 |
| Rapport | RAP | RAP-2025-00001 |
| PV Commission | PVC | PVC-2025-00001 |
| Planning | PLN | PLN-2025-001 |
| Annexe 1 | ANX1 | ANX1-2025-00001 |
| Annexe 2 | ANX2 | ANX2-2025-00001 |
| Annexe 3 | ANX3 | ANX3-2025-00001 |
| PV Final compilé | PVF | PVF-2025-00001 |

---

## 3. Documents détaillés

### 3.1 Reçu de paiement

#### 3.1.1 Déclencheur
Automatique après enregistrement d'un versement (Module 2)

#### 3.1.2 Format
- Taille : A5 (148mm × 210mm)
- Orientation : Portrait
- Marges : 15mm

#### 3.1.3 Structure du document

```
┌─────────────────────────────────────────────────────────────────┐
│  [LOGO UFHB]              [LOGO UFR MI]                         │
│                                                                 │
│                    REÇU DE PAIEMENT                             │
│                    N° REC-2025-00001                            │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  ÉTUDIANT                                                       │
│  Matricule : ETU202400001                                       │
│  Nom : DUPONT Jean                                              │
│  Promotion : 2024-2025                                          │
│  Niveau : Master 2                                              │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  DÉTAILS DU PAIEMENT                                            │
│                                                                 │
│  Type        : Scolarité                                        │
│  Montant     : 150 000 FCFA                                     │
│  En lettres  : Cent cinquante mille francs CFA                  │
│  Date        : 15 janvier 2025                                  │
│  Méthode     : Virement bancaire                                │
│  Référence   : VIR-2025-0123                                    │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  SITUATION APRÈS CE VERSEMENT                                   │
│                                                                 │
│  Montant total dû   : 500 000 FCFA                              │
│  Total payé         : 350 000 FCFA                              │
│  Reste à payer      : 150 000 FCFA                              │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  Généré le 15/01/2025 à 14:30                                   │
│  Par : [Nom agent]                                              │
│                                                                 │
│  [CACHET ÉLECTRONIQUE / QR CODE VÉRIFICATION]                   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

#### 3.1.4 Données requises
```php
[
    'reference' => 'REC-2025-00001',
    'etudiant' => [
        'matricule' => 'ETU202400001',
        'nom' => 'DUPONT',
        'prenom' => 'Jean',
        'promotion' => '2024-2025',
        'niveau' => 'Master 2',
    ],
    'versement' => [
        'type' => 'scolarite',
        'montant' => 150000,
        'montant_lettres' => 'Cent cinquante mille',
        'date' => '2025-01-15',
        'methode' => 'Virement bancaire',
        'reference_externe' => 'VIR-2025-0123',
    ],
    'situation' => [
        'montant_total' => 500000,
        'total_paye' => 350000,
        'reste' => 150000,
    ],
    'generateur' => [
        'nom' => 'MARTIN Marie',
        'date' => '2025-01-15 14:30:00',
    ],
]
```

### 3.2 Bulletin de notes provisoire

#### 3.2.1 Déclencheur
Sur demande (bouton "Générer bulletin" dans fiche étudiant)

#### 3.2.2 Format
- Taille : A4
- Orientation : Portrait
- Marges : 25mm

#### 3.2.3 Structure du document

```
┌─────────────────────────────────────────────────────────────────┐
│  [LOGO UFHB]    UNIVERSITÉ FÉLIX HOUPHOUËT-BOIGNY    [LOGO]    │
│                 UFR MATHÉMATIQUES ET INFORMATIQUE               │
│                      DÉPARTEMENT MIAGE-GI                       │
│                                                                 │
│               BULLETIN DE NOTES PROVISOIRE                      │
│                   Année académique 2024-2025                    │
│                                                                 │
│═════════════════════════════════════════════════════════════════│
│                                                                 │
│  Étudiant : DUPONT Jean                                         │
│  Matricule : ETU202400001                                       │
│  Niveau : Master 2 - MIAGE                                      │
│  Promotion : 2024-2025                                          │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│                     SEMESTRE 1 - MASTER 2                       │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  ┌───────────────────────────────────────┬────────┬────────┐   │
│  │ Unité d'Enseignement                  │ Crédit │ Note   │   │
│  ├───────────────────────────────────────┼────────┼────────┤   │
│  │ UE1 - Systèmes d'Information          │   6    │ 14.50  │   │
│  │ UE2 - Génie Logiciel                  │   6    │ 15.00  │   │
│  │ UE3 - Base de Données Avancées        │   6    │ 13.50  │   │
│  │ UE4 - Management de Projet            │   6    │ 16.00  │   │
│  │ UE5 - Anglais Professionnel           │   3    │ 12.00  │   │
│  │ UE6 - Communication                   │   3    │ 14.00  │   │
│  ├───────────────────────────────────────┼────────┼────────┤   │
│  │ MOYENNE SEMESTRE 1                    │   30   │ 14.33  │   │
│  └───────────────────────────────────────┴────────┴────────┘   │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│             RÉCAPITULATIF GÉNÉRAL                               │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  Moyenne générale Master 1 : 12.50                              │
│  Moyenne Semestre 1 M2 : 14.33                                  │
│                                                                 │
│  ⚠️ BULLETIN PROVISOIRE - DOCUMENT NON OFFICIEL                │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│  Généré le 15/01/2025                                           │
│  Référence : BUL-2025-00001                                     │
└─────────────────────────────────────────────────────────────────┘
```

### 3.3 Rapport de stage (PDF)

#### 3.3.1 Déclencheur
Automatique après soumission du rapport (Module 4)

#### 3.3.2 Format
- Taille : A4
- Orientation : Portrait
- Marges : 25mm (haut/bas), 30mm (gauche), 25mm (droite)

#### 3.3.3 Structure
1. **Page de garde** (générée automatiquement)
2. **Table des matières** (générée depuis H1/H2/H3)
3. **Contenu** (conversion HTML → PDF)
4. **Numérotation** (en pied de page)

#### 3.3.4 Page de garde

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│                         [LOGO UFHB]                             │
│                                                                 │
│             UNIVERSITÉ FÉLIX HOUPHOUËT-BOIGNY                   │
│                                                                 │
│             UFR MATHÉMATIQUES ET INFORMATIQUE                   │
│                                                                 │
│                   DÉPARTEMENT MIAGE-GI                          │
│                                                                 │
│                                                                 │
│                                                                 │
│                                                                 │
│             RAPPORT DE STAGE DE FIN D'ÉTUDES                    │
│                                                                 │
│                   ─────────────────────                         │
│                                                                 │
│         [TITRE DU RAPPORT - EN MAJUSCULES - GRAS]              │
│                                                                 │
│                   ─────────────────────                         │
│                                                                 │
│                                                                 │
│                                                                 │
│  Présenté par :                                                 │
│  DUPONT Jean                                                    │
│  Matricule : ETU202400001                                       │
│                                                                 │
│  Promotion : 2024-2025                                          │
│                                                                 │
│                                                                 │
│  Entreprise d'accueil :                                         │
│  [NOM ENTREPRISE]                                               │
│                                                                 │
│  Maître de stage :                                              │
│  [Nom maître de stage]                                          │
│                                                                 │
│                                                                 │
│                                                                 │
│              Année académique 2024-2025                         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 3.4 PV de Commission

#### 3.4.1 Déclencheur
Finalisation du compte-rendu (Module 5)

#### 3.4.2 Format
- Taille : A4
- Orientation : Portrait
- Marges : 25mm

#### 3.4.3 Structure

```
┌─────────────────────────────────────────────────────────────────┐
│  [EN-TÊTE OFFICIEL UFHB / UFR MI / MIAGE-GI]                   │
│                                                                 │
│              PROCÈS-VERBAL DE LA COMMISSION                     │
│                D'ÉVALUATION DES RAPPORTS                        │
│                                                                 │
│                   Session : Janvier 2025                        │
│                   N° PVC-2025-00001                             │
│                                                                 │
│═════════════════════════════════════════════════════════════════│
│                                                                 │
│  La commission d'évaluation des rapports de stage s'est        │
│  réunie le [DATE] pour examiner les rapports suivants.         │
│                                                                 │
│  MEMBRES PRÉSENTS :                                             │
│  • Prof. MARTIN Jean - Président                                │
│  • Dr. DUPONT Marie - Membre                                    │
│  • Dr. BERNARD Paul - Membre                                    │
│  • Dr. PETIT Sophie - Membre                                    │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│                     RAPPORTS ÉVALUÉS                            │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  ┌────┬─────────────┬──────────────────────┬──────────────────┐ │
│  │ N° │ Étudiant    │ Thème                │ Décision         │ │
│  ├────┼─────────────┼──────────────────────┼──────────────────┤ │
│  │ 1  │ DUPONT J.   │ Développement d'un...│ ✓ Validé         │ │
│  │    │             │                      │ Dir: Prof. X     │ │
│  │    │             │                      │ Enc: Dr. Y       │ │
│  ├────┼─────────────┼──────────────────────┼──────────────────┤ │
│  │ 2  │ MARTIN M.   │ Mise en place d'une..│ ✓ Validé         │ │
│  │    │             │                      │ Dir: Prof. Z     │ │
│  │    │             │                      │ Enc: Dr. W       │ │
│  └────┴─────────────┴──────────────────────┴──────────────────┘ │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│                     REMARQUES GÉNÉRALES                         │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  [Contenu édité par l'administrateur]                          │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  Fait à Abidjan, le [DATE]                                     │
│                                                                 │
│  Le Président de la Commission                                  │
│                                                                 │
│                                                                 │
│  _________________________                                      │
│  Prof. MARTIN Jean                                              │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 3.5 Tableau des soutenances

#### 3.5.1 Déclencheur
Sur demande admin (génération du planning)

#### 3.5.2 Format
- Taille : A4
- Orientation : Paysage
- Marges : 15mm

#### 3.5.3 Structure

```
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│  [EN-TÊTE OFFICIEL]           PLANNING DES SOUTENANCES - Janvier 2025                       │
│                                              N° PLN-2025-001                                │
├──────────┬───────┬─────────┬───────────────┬────────────────────────────┬───────────────────┤
│   Date   │ Heure │  Salle  │   Étudiant    │          Thème             │      Jury         │
├──────────┼───────┼─────────┼───────────────┼────────────────────────────┼───────────────────┤
│ 15/01/25 │ 09:00 │ Amphi A │ DUPONT Jean   │ Développement d'une appli- │ Pdt: MARTIN J.    │
│          │       │         │ ETU202400001  │ cation de gestion...       │ Dir: PETIT P.     │
│          │       │         │               │                            │ Enc: DURAND M.    │
│          │       │         │               │                            │ MS: BLANC L.      │
│          │       │         │               │                            │ Exa: NOIR F.      │
├──────────┼───────┼─────────┼───────────────┼────────────────────────────┼───────────────────┤
│ 15/01/25 │ 10:30 │ Salle B │ BERNARD Marie │ Mise en place d'un système │ Pdt: GIRARD A.    │
│          │       │         │ ETU202400002  │ de monitoring...           │ Dir: LEROY B.     │
│          │       │         │               │                            │ ...               │
├──────────┼───────┼─────────┼───────────────┼────────────────────────────┼───────────────────┤
│   ...    │  ...  │   ...   │      ...      │            ...             │       ...         │
└──────────┴───────┴─────────┴───────────────┴────────────────────────────┴───────────────────┘
│  Généré le 10/01/2025 - Page 1/2                                                            │
└─────────────────────────────────────────────────────────────────────────────────────────────┘
```

### 3.6 Annexe 1 : Grille d'évaluation technique

#### 3.6.1 Déclencheur
Après saisie des notes de soutenance (Module 6)

#### 3.6.2 Format
- Taille : A4
- Orientation : Portrait
- Marges : 20mm

#### 3.6.3 Structure

```
┌─────────────────────────────────────────────────────────────────┐
│  [LOGO UFHB]                                      [LOGO UFR MI] │
│              ─────────────────────────────────────              │
│                         MIAGE - GI                              │
│              ─────────────────────────────────────              │
│                                                                 │
│                         ANNEXE 1                                │
│               SOUTENANCE DE MÉMOIRE DE FIN D'ÉTUDES             │
│                   GRILLE D'ÉVALUATION TECHNIQUE                 │
│                                                                 │
│═════════════════════════════════════════════════════════════════│
│                                                                 │
│  IMPÉTRANT                                                      │
│  Nom et Prénoms : DUPONT Jean                                   │
│  Matricule : ETU202400001                                       │
│  Niveau : Master 2       Classe : Promotion 2024-2025           │
│                                                                 │
│  Date de soutenance : 15 janvier 2025                           │
│  Thème : Développement d'une application de gestion...          │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│                     GRILLE DE NOTATION                          │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  ┌───────────────────────────────────────────┬────────┬───────┐ │
│  │ Critère                                   │ Barème │ Note  │ │
│  ├───────────────────────────────────────────┼────────┼───────┤ │
│  │ Qualité du document écrit                 │   /5   │ 4.5   │ │
│  │ Maîtrise du sujet                         │   /5   │ 4.0   │ │
│  │ Qualité de la présentation orale          │   /5   │ 4.5   │ │
│  │ Pertinence des réponses aux questions     │   /3   │ 2.5   │ │
│  │ Respect du temps imparti                  │   /2   │ 2.0   │ │
│  ├───────────────────────────────────────────┼────────┼───────┤ │
│  │ NOTE FINALE                               │   /20  │ 17.5  │ │
│  └───────────────────────────────────────────┴────────┴───────┘ │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│                     MEMBRES DU JURY                             │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  Président du Jury    : Prof. MARTIN Jean        ____________  │
│  Examinateur          : Dr. PETIT Paul           ____________  │
│  Directeur de mémoire : Dr. DURAND Marie         ____________  │
│  Examinateur          : Dr. BERNARD Sophie       ____________  │
│  Maître de stage      : M. BLANC Louis           ____________  │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│  Référence : ANX1-2025-00001                                    │
└─────────────────────────────────────────────────────────────────┘
```

### 3.7 Annexe 2 : PV Jury Standard

#### 3.7.1 Déclencheur
Après délibération avec type_pv = 'standard'

#### 3.7.2 Formule
```
Moyenne Finale = ((Moyenne_M1 × 2) + (Moyenne_S1_M2 × 3) + (Note_Memoire × 3)) / 8
```

#### 3.7.3 Structure

```
┌─────────────────────────────────────────────────────────────────┐
│  [LOGO UFHB]                                      [LOGO UFR MI] │
│              ─────────────────────────────────────              │
│                         MIAGE - GI                              │
│              ─────────────────────────────────────              │
│                                                                 │
│                         ANNEXE 2                                │
│           PROCÈS-VERBAL DU JURY DE SOUTENANCE                   │
│                   (PARCOURS STANDARD)                           │
│                                                                 │
│═════════════════════════════════════════════════════════════════│
│                                                                 │
│  IMPÉTRANT                                                      │
│  Nom et Prénoms : DUPONT Jean                                   │
│  Matricule : ETU202400001                                       │
│  Date de soutenance : 15 janvier 2025                           │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│                  RÉCAPITULATIF DES MOYENNES                     │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  ┌───────────────────────────────────────┬──────────┬─────────┐ │
│  │ Composante                            │   Note   │  Coef.  │ │
│  ├───────────────────────────────────────┼──────────┼─────────┤ │
│  │ Moyenne générale Master 1             │  12.50   │   × 2   │ │
│  │ Moyenne Semestre 1 Master 2           │  14.33   │   × 3   │ │
│  │ Note du Mémoire (cf. Annexe 1)        │  17.50   │   × 3   │ │
│  ├───────────────────────────────────────┼──────────┼─────────┤ │
│  │ MOYENNE GÉNÉRALE /20                  │  14.87   │   / 8   │ │
│  └───────────────────────────────────────┴──────────┴─────────┘ │
│                                                                 │
│  MENTION : BIEN                                                 │
│                                                                 │
│  DÉCISION DU JURY : ADMIS                                       │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│                     SIGNATURES DU JURY                          │
│─────────────────────────────────────────────────────────────────│
│                                                                 │
│  [Même bloc signatures que Annexe 1]                            │
│                                                                 │
│─────────────────────────────────────────────────────────────────│
│  Fait à Abidjan, le 15 janvier 2025                             │
│  Référence : ANX2-2025-00001                                    │
└─────────────────────────────────────────────────────────────────┘
```

### 3.8 Annexe 3 : PV Jury Simplifié

#### 3.8.1 Déclencheur
Après délibération avec type_pv = 'simplifie'

#### 3.8.2 Formule
```
Moyenne Finale = ((Moyenne_M1 × 1) + (Note_Memoire × 2)) / 3
```

#### 3.8.3 Structure
Similaire à l'Annexe 2, avec le tableau de calcul adapté :

```
│  ┌───────────────────────────────────────┬──────────┬─────────┐ │
│  │ Composante                            │   Note   │  Coef.  │ │
│  ├───────────────────────────────────────┼──────────┼─────────┤ │
│  │ Moyenne générale Master 1             │  12.50   │   × 1   │ │
│  │ Note du Mémoire (cf. Annexe 1)        │  17.50   │   × 2   │ │
│  ├───────────────────────────────────────┼──────────┼─────────┤ │
│  │ MOYENNE GÉNÉRALE /20                  │  15.83   │   / 3   │ │
│  └───────────────────────────────────────┴──────────┴─────────┘ │
```

### 3.9 PV Finaux (3 en 1)

#### 3.9.1 Description
Document unique contenant les trois annexes compilées :
- Page 1-2 : Annexe 1 (Grille d'évaluation)
- Page 3-4 : Annexe 2 ou 3 (selon type_pv)
- Toutes les pages numérotées

#### 3.9.2 Génération
```php
function genererPVFinal(Soutenance $soutenance): string
{
    $pdf = new TCPDF();
    
    // Annexe 1
    $this->genererAnnexe1($pdf, $soutenance);
    
    // Annexe 2 ou 3 selon le type
    if ($soutenance->getTypePv() === 'standard') {
        $this->genererAnnexe2($pdf, $soutenance);
    } else {
        $this->genererAnnexe3($pdf, $soutenance);
    }
    
    return $pdf->Output('PVF-' . $reference . '.pdf', 'S');
}
```

---

## 4. Fonctionnalités transverses

### 4.1 Prévisualisation
- Tous les documents peuvent être prévisualisés avant génération définitive
- Affichage HTML stylisé dans une fenêtre modale ou nouvel onglet

### 4.2 Téléchargement
- Téléchargement direct du PDF
- Ouverture dans un nouvel onglet
- Envoi par email avec pièce jointe

### 4.3 Archivage
- Tous les documents générés sont archivés
- Conservation selon durée légale (5 ans minimum)
- Index de recherche par référence, étudiant, date

### 4.4 Audit
- Chaque génération est journalisée
- Traçabilité : qui, quand, quel document

### 4.5 Vérification d'authenticité
- QR code optionnel avec hash du document
- Lien de vérification en ligne

---

## 5. Règles de gestion

| Code | Règle |
|------|-------|
| RG-DOC-001 | Chaque document a une référence unique |
| RG-DOC-002 | Les documents officiels sont non modifiables après génération |
| RG-DOC-003 | Les PV finaux ne peuvent être générés qu'après délibération validée |
| RG-DOC-004 | Les documents sont conservés 5 ans minimum |
| RG-DOC-005 | Un reçu de paiement est automatiquement généré après versement |

---

## 6. Configuration des en-têtes

### 6.1 Logos
- `logo_ufhb.png` : 40mm × 40mm
- `logo_ufr_mi.png` : 40mm × 40mm
- Stockés dans `/public/assets/images/logos/`

### 6.2 Polices
- Police principale : Helvetica
- Taille titre principal : 18pt bold
- Taille sous-titre : 14pt bold
- Taille corps : 11pt
- Taille tableau : 10pt

### 6.3 Couleurs
- Noir principal : #000000
- Gris bordures : #CCCCCC
- Bleu en-tête (optionnel) : #1E3A5F
