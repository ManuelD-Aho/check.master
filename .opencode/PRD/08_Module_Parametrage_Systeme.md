# PRD Module 8 : Paramétrage Système (Administration)

## 1. Vue d'ensemble

### 1.1 Objectif du module
Ce module centralise toute la configuration de l'application : paramètres généraux, gestion des référentiels (niveaux, semestres, UE, grades, etc.), personnalisation des menus, messages système et surveillance de l'application.

### 1.2 Principe clé
> **RÈGLE FONDAMENTALE** : Tout doit être configurable depuis le back-office. L'administrateur doit pouvoir modifier au maximum sans toucher au code.

### 1.3 Bibliothèques utilisées
| Bibliothèque | Rôle |
|--------------|------|
| `symfony/options-resolver` | Validation des configurations |
| `symfony/expression-language` | Règles métier configurables |
| `doctrine/orm` | Gestion des entités de paramétrage |
| `defuse/php-encryption` | Chiffrement des paramètres sensibles |
| `monolog/monolog` | Journalisation des modifications |
| `psr/simple-cache` | Cache des configurations |
| `white-october/pagerfanta` | Pagination |

---

## 2. Catégories de paramétrage

### 2.1 Vue d'ensemble des sections

```
Paramétrage
├── 1. Paramètres Généraux
│   ├── Application
│   ├── Email
│   └── Sécurité
│
├── 2. Paramètres Académiques
│   ├── Années académiques
│   ├── Niveaux d'étude
│   ├── Semestres
│   ├── Filières/Spécialités
│   └── UE / ECUE
│
├── 3. Paramètres RH
│   ├── Grades enseignants
│   ├── Fonctions personnel
│   ├── Rôles jury
│   └── Critères d'évaluation
│
├── 4. Gestion des Menus
│   ├── Catégories
│   ├── Fonctionnalités
│   └── Permissions
│
├── 5. Messages Système
│   ├── Libellés
│   ├── Notifications
│   └── Emails templates
│
└── 6. Maintenance
    ├── Logs d'audit
    ├── Statistiques
    └── Cache
```

---

## 3. Paramètres Généraux

### 3.1 Configuration Application
**Écran** : `/admin/parametres/application`

**Permission requise** : `PARAM_APPLICATION`

| Paramètre | Type | Défaut | Description |
|-----------|------|--------|-------------|
| `app_name` | String | "Plateforme MIAGE" | Nom de l'application |
| `app_logo` | File | logo.png | Logo principal |
| `app_favicon` | File | favicon.ico | Favicon |
| `app_timezone` | Select | "Africa/Abidjan" | Fuseau horaire |
| `app_locale` | Select | "fr_FR" | Langue par défaut |
| `app_maintenance_mode` | Boolean | false | Mode maintenance |
| `app_maintenance_message` | Text | - | Message de maintenance |
| `pagination_default` | Number | 25 | Éléments par page |
| `session_timeout` | Number | 480 | Timeout session (minutes) |

### 3.2 Configuration Email
**Écran** : `/admin/parametres/email`

**Permission requise** : `PARAM_EMAIL`

| Paramètre | Type | Description | Sensible |
|-----------|------|-------------|----------|
| `smtp_host` | String | Serveur SMTP | Non |
| `smtp_port` | Number | Port SMTP | Non |
| `smtp_username` | String | Utilisateur SMTP | Oui |
| `smtp_password` | Password | Mot de passe SMTP | Oui (chiffré) |
| `smtp_encryption` | Select | TLS/SSL/None | Non |
| `email_from_address` | Email | Adresse expéditeur | Non |
| `email_from_name` | String | Nom expéditeur | Non |
| `email_reply_to` | Email | Adresse de réponse | Non |
| `email_bcc_admin` | Email | Copie cachée admin | Non |
| `email_enabled` | Boolean | Activer l'envoi | Non |

**Action** : Bouton "Tester la configuration" → Envoi email de test

### 3.3 Configuration Sécurité
**Écran** : `/admin/parametres/securite`

**Permission requise** : `PARAM_SECURITE`

| Paramètre | Type | Défaut | Description |
|-----------|------|--------|-------------|
| `password_min_length` | Number | 8 | Longueur minimale |
| `password_require_uppercase` | Boolean | true | Majuscule requise |
| `password_require_number` | Boolean | true | Chiffre requis |
| `password_require_special` | Boolean | true | Caractère spécial |
| `login_max_attempts` | Number | 5 | Tentatives avant blocage |
| `login_lockout_duration` | Number | 15 | Durée blocage (minutes) |
| `session_concurrent` | Boolean | false | Sessions simultanées |
| `2fa_mandatory_admin` | Boolean | true | 2FA obligatoire admins |
| `2fa_enabled_teachers` | Boolean | true | 2FA dispo enseignants |
| `csrf_token_lifetime` | Number | 3600 | Durée token CSRF (sec) |

---

## 4. Paramètres Académiques

### 4.1 Années Académiques
**Écran** : `/admin/parametres/annees-academiques`

**Permission requise** : `ANNEE_ACAD_GESTION`

**Colonnes** :
- Libellé (ex: "2024-2025")
- Date début
- Date fin
- Active (badge)
- Ouverte aux inscriptions (badge)
- Actions

**Formulaire** :
| Champ | Type | Obligatoire | Validation |
|-------|------|-------------|------------|
| Libellé | Text | Oui | Format AAAA-AAAA |
| Date début | Date | Oui | - |
| Date fin | Date | Oui | > date début |
| Est active | Toggle | Oui | Une seule active |
| Inscriptions ouvertes | Toggle | Oui | - |

**Règles** :
- Une seule année peut être active
- L'activation désactive automatiquement les autres
- La suppression est impossible si données associées

### 4.2 Niveaux d'étude
**Écran** : `/admin/parametres/niveaux`

**Permission requise** : `NIVEAU_GESTION`

**Colonnes** :
- Code (M1, M2)
- Libellé
- Ordre
- Montant scolarité
- Montant inscription
- Responsable
- Actions

**Formulaire** :
| Champ | Type | Obligatoire |
|-------|------|-------------|
| Code | Text | Oui |
| Libellé | Text | Oui |
| Ordre | Number | Oui |
| Montant scolarité | Number | Oui |
| Montant inscription | Number | Oui |
| Responsable | Autocomplete | Non |

### 4.3 Semestres
**Écran** : `/admin/parametres/semestres`

**Permission requise** : `SEMESTRE_GESTION`

**Colonnes** :
- Code (S1, S2)
- Libellé
- Niveau associé
- Actions

**Formulaire** :
| Champ | Type | Obligatoire |
|-------|------|-------------|
| Code | Text | Oui |
| Libellé | Text | Oui |
| Niveau | Select | Oui |

### 4.4 Filières / Spécialités
**Écran** : `/admin/parametres/filieres`

**Permission requise** : `FILIERE_GESTION`

**Colonnes** :
- Code
- Libellé
- Description
- Actif
- Nb étudiants
- Actions

### 4.5 Unités d'Enseignement (UE)
**Écran** : `/admin/parametres/ue`

**Permission requise** : `UE_GESTION`

**Filtres** :
- Par niveau
- Par semestre
- Par année académique

**Colonnes** :
- Code UE
- Libellé
- Niveau
- Semestre
- Crédits
- Responsable
- Actif
- Actions

**Formulaire** :
| Champ | Type | Obligatoire |
|-------|------|-------------|
| Code | Text | Oui |
| Libellé | Text | Oui |
| Niveau | Select | Oui |
| Semestre | Select | Oui |
| Année académique | Select | Oui |
| Crédits | Number | Oui |
| Enseignant responsable | Autocomplete | Non |
| Description | Textarea | Non |

### 4.6 Éléments Constitutifs (ECUE)
**Écran** : `/admin/parametres/ecue`

**Permission requise** : `ECUE_GESTION`

**Colonnes** :
- Code ECUE
- Libellé
- UE parente
- Crédits
- Enseignant
- Actions

---

## 5. Paramètres RH

### 5.1 Grades Enseignants
**Écran** : `/admin/parametres/grades`

**Permission requise** : `GRADE_GESTION`

**Données** :
| Code | Libellé | Abréviation |
|------|---------|-------------|
| PT | Professeur Titulaire | Prof. |
| MC | Maître de Conférences | Dr. |
| MA | Maître Assistant | M. |
| AT | Assistant | M. |

**Formulaire** :
| Champ | Type | Obligatoire |
|-------|------|-------------|
| Code | Text | Oui |
| Libellé | Text | Oui |
| Abréviation | Text | Oui |
| Ordre hiérarchique | Number | Oui |
| Peut présider jury | Boolean | Oui |

### 5.2 Fonctions Personnel
**Écran** : `/admin/parametres/fonctions`

**Permission requise** : `FONCTION_GESTION`

**Données** :
- Directeur de département
- Secrétaire
- Comptable
- Agent administratif
- ...

### 5.3 Rôles Jury
**Écran** : `/admin/parametres/roles-jury`

**Permission requise** : `ROLE_JURY_GESTION`

**Données fixes** :
| Code | Libellé | Obligatoire |
|------|---------|-------------|
| president | Président du Jury | Oui |
| directeur_memoire | Directeur de Mémoire | Oui |
| encadreur_pedagogique | Encadreur Pédagogique | Oui |
| maitre_stage | Maître de Stage | Oui |
| examinateur | Examinateur | Oui |

### 5.4 Critères d'Évaluation
**Écran** : `/admin/parametres/criteres`

**Permission requise** : `CRITERE_GESTION`

**Colonnes** :
- Code
- Libellé
- Ordre
- Actif
- Actions

**Configuration des barèmes par année** :

**Écran** : `/admin/parametres/criteres/baremes`

Interface tableau croisé :

| Critère | Barème 2024-2025 | Barème 2023-2024 |
|---------|------------------|------------------|
| Qualité du document | /5 | /5 |
| Maîtrise du sujet | /5 | /5 |
| Présentation orale | /5 | /5 |
| Réponses questions | /3 | /3 |
| Respect du temps | /2 | /2 |
| **TOTAL** | **/20** | **/20** |

---

## 6. Gestion des Menus

### 6.1 Catégories de fonctionnalités
**Écran** : `/admin/parametres/menus/categories`

**Permission requise** : `MENU_GESTION`

**Structure arborescente** :
```
📁 Gestion Étudiants
├── 📄 Liste des étudiants
├── 📄 Inscriptions
└── 📄 Notes

📁 Gestion Stages
├── 📄 Candidatures
├── 📄 Rapports
└── 📄 Entreprises

📁 Commission
├── 📄 Évaluations
├── 📄 Assignations
└── 📄 PV Commission

📁 Soutenances
├── 📄 Jurys
├── 📄 Planning
└── 📄 Notation

📁 Administration
├── 📄 Utilisateurs
├── 📄 Permissions
└── 📁 Paramétrage
    ├── 📄 Application
    ├── 📄 Académique
    └── ...
```

**Formulaire catégorie** :
| Champ | Type | Obligatoire |
|-------|------|-------------|
| Code | Text | Oui |
| Libellé | Text | Oui |
| Icône | IconPicker | Non |
| Ordre | Number | Oui |
| Actif | Toggle | Oui |

### 6.2 Fonctionnalités (Pages)
**Écran** : `/admin/parametres/menus/fonctionnalites`

**Colonnes** :
- Code
- Libellé
- Catégorie
- URL
- Icône
- Ordre
- Actif
- Actions

**Formulaire** :
| Champ | Type | Obligatoire | Description |
|-------|------|-------------|-------------|
| Code | Text | Oui | Identifiant unique |
| Libellé | Text | Oui | Texte affiché dans le menu |
| Label court | Text | Non | Version courte |
| Description | Textarea | Non | Info-bulle |
| Catégorie | Select | Oui | Catégorie parente |
| URL | Text | Oui | Route de la page |
| Icône | IconPicker | Non | Icône FontAwesome |
| Ordre | Number | Oui | Position dans le menu |
| Est sous-page | Toggle | Non | N'apparaît pas dans le menu |
| Page parente | Select | Conditionnel | Si sous-page |
| Actif | Toggle | Oui | Visible ou non |

### 6.3 Matrice des Permissions
**Écran** : `/admin/parametres/permissions`

**Permission requise** : `PERMISSION_GESTION`

**Interface** : Tableau croisé dynamique

```
┌─────────────────────────────────────────────────────────────────┐
│  Groupe : [Select: Administrateur ▼]                            │
├───────────────────────────────┬───────┬────────┬────────┬──────┤
│ Fonctionnalité                │ Voir  │ Créer  │ Modif  │ Supp │
├───────────────────────────────┼───────┼────────┼────────┼──────┤
│ 📁 Gestion Étudiants          │       │        │        │      │
│   ├ Liste étudiants           │  [x]  │  [x]   │  [x]   │ [x]  │
│   ├ Inscriptions              │  [x]  │  [x]   │  [x]   │ [ ]  │
│   └ Notes                     │  [x]  │  [x]   │  [x]   │ [ ]  │
├───────────────────────────────┼───────┼────────┼────────┼──────┤
│ 📁 Gestion Stages             │       │        │        │      │
│   ├ Candidatures              │  [x]  │  [ ]   │  [x]   │ [ ]  │
│   ...                         │       │        │        │      │
└───────────────────────────────┴───────┴────────┴────────┴──────┘

[Tout sélectionner]  [Tout désélectionner]  [Enregistrer]
```

**Sauvegarde** : AJAX avec validation côté serveur

---

## 7. Messages Système

### 7.1 Libellés et traductions
**Écran** : `/admin/parametres/messages/libelles`

**Permission requise** : `MESSAGE_GESTION`

**Colonnes** :
- Code
- Catégorie
- Texte FR
- Type (info, erreur, succès, warning)
- Actions

**Exemple de données** :
| Code | Texte |
|------|-------|
| `auth.login.success` | "Connexion réussie" |
| `auth.login.failed` | "Identifiants incorrects" |
| `etudiant.create.success` | "L'étudiant a été créé avec succès" |
| `rapport.submit.confirm` | "Êtes-vous sûr de vouloir soumettre votre rapport ?" |

### 7.2 Templates d'emails
**Écran** : `/admin/parametres/messages/emails`

**Permission requise** : `EMAIL_TEMPLATE_GESTION`

**Templates disponibles** :
| Code | Sujet par défaut | Variables |
|------|------------------|-----------|
| `user.created` | Vos identifiants de connexion | {prenom}, {login}, {password}, {url} |
| `password.reset` | Réinitialisation de mot de passe | {prenom}, {token}, {url} |
| `candidature.submitted` | Candidature soumise | {prenom}, {sujet} |
| `candidature.validated` | Candidature validée | {prenom}, {sujet} |
| `candidature.rejected` | Candidature refusée | {prenom}, {motif} |
| `rapport.submitted` | Rapport soumis | {prenom}, {titre} |
| `rapport.approved` | Rapport approuvé | {prenom}, {titre} |
| `rapport.returned` | Rapport à corriger | {prenom}, {commentaire} |
| `soutenance.convocation` | Convocation soutenance | {prenom}, {date}, {heure}, {salle}, {jury} |

**Éditeur de template** :
- Éditeur WYSIWYG simplifié
- Insertion de variables par bouton
- Prévisualisation avec données fictives
- Test d'envoi

---

## 8. Gestion des Salles et Entreprises

### 8.1 Salles
**Écran** : `/admin/parametres/salles`

**Permission requise** : `SALLE_GESTION`

**Colonnes** :
- Code
- Libellé
- Bâtiment
- Étage
- Capacité
- Équipements
- Actif
- Actions

### 8.2 Entreprises (Référentiel)
**Écran** : `/admin/parametres/entreprises`

**Permission requise** : `ENTREPRISE_GESTION`

**Fonctionnalités** :
- Liste paginée
- Recherche
- Fusion de doublons
- Import CSV
- Désactivation

---

## 9. Maintenance et Supervision

### 9.1 Logs d'audit
**Écran** : `/admin/maintenance/audit`

**Permission requise** : `AUDIT_VIEW`

**Filtres** :
- Période
- Utilisateur
- Action
- Table concernée
- Statut (succès/échec)

**Colonnes** :
- Date/Heure
- Utilisateur
- Action
- Table
- Détails
- IP
- Statut

**Export** : CSV

### 9.2 Statistiques
**Écran** : `/admin/maintenance/statistiques`

**Permission requise** : `STATS_VIEW`

**Tableaux de bord** :
- Nombre d'étudiants par promotion
- Taux de validation des candidatures
- Progression des soutenances
- Répartition des notes
- Graphiques d'activité

### 9.3 Gestion du cache
**Écran** : `/admin/maintenance/cache`

**Permission requise** : `CACHE_GESTION`

**Actions** :
- Vider le cache de configuration
- Vider le cache des templates
- Vider le cache des permissions
- Tout vider

### 9.4 Mode Maintenance
**Écran** : `/admin/maintenance/mode`

**Permission requise** : `MAINTENANCE_MODE`

**Fonctionnalités** :
- Activer/Désactiver le mode maintenance
- Personnaliser le message
- Liste des IP autorisées (admin)
- Planification (date/heure activation/désactivation)

---

## 10. Table de stockage des paramètres

### 10.1 Structure `app_settings`

```sql
CREATE TABLE app_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json', 'encrypted'),
    category VARCHAR(50),
    description VARCHAR(255),
    is_sensitive BOOLEAN DEFAULT FALSE,
    updated_at DATETIME,
    updated_by INT REFERENCES utilisateur(id_utilisateur)
);
```

### 10.2 Données initiales

```sql
INSERT INTO app_settings VALUES
('app_name', 'Plateforme MIAGE-GI', 'string', 'application', 'Nom de l''application', false, NOW(), 1),
('smtp_password', '[ENCRYPTED]', 'encrypted', 'email', 'Mot de passe SMTP', true, NOW(), 1),
('login_max_attempts', '5', 'number', 'security', 'Tentatives max avant blocage', false, NOW(), 1),
('maintenance_mode', 'false', 'boolean', 'maintenance', 'Mode maintenance actif', false, NOW(), 1)
;
```

### 10.3 Service de paramétrage

```php
class SettingsService
{
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function getByCategory(string $category): array;
    public function isEncrypted(string $key): bool;
}
```

---

## 11. Règles de gestion

| Code | Règle |
|------|-------|
| RG-PARAM-001 | Les paramètres sensibles sont chiffrés en base |
| RG-PARAM-002 | Toute modification de paramètre est journalisée |
| RG-PARAM-003 | Le mode maintenance bloque l'accès sauf IPs autorisées |
| RG-PARAM-004 | Une seule année académique peut être active |
| RG-PARAM-005 | Les référentiels utilisés ne peuvent pas être supprimés |
| RG-PARAM-006 | Le cache est vidé après modification de paramètres |

---

## 12. Écrans récapitulatifs

| Section | Écran | URL | Permission |
|---------|-------|-----|------------|
| Application | Config générale | `/admin/parametres/application` | PARAM_APPLICATION |
| Application | Email | `/admin/parametres/email` | PARAM_EMAIL |
| Application | Sécurité | `/admin/parametres/securite` | PARAM_SECURITE |
| Académique | Années | `/admin/parametres/annees-academiques` | ANNEE_ACAD_GESTION |
| Académique | Niveaux | `/admin/parametres/niveaux` | NIVEAU_GESTION |
| Académique | Semestres | `/admin/parametres/semestres` | SEMESTRE_GESTION |
| Académique | Filières | `/admin/parametres/filieres` | FILIERE_GESTION |
| Académique | UE | `/admin/parametres/ue` | UE_GESTION |
| Académique | ECUE | `/admin/parametres/ecue` | ECUE_GESTION |
| RH | Grades | `/admin/parametres/grades` | GRADE_GESTION |
| RH | Fonctions | `/admin/parametres/fonctions` | FONCTION_GESTION |
| RH | Rôles jury | `/admin/parametres/roles-jury` | ROLE_JURY_GESTION |
| RH | Critères | `/admin/parametres/criteres` | CRITERE_GESTION |
| Menus | Catégories | `/admin/parametres/menus/categories` | MENU_GESTION |
| Menus | Fonctionnalités | `/admin/parametres/menus/fonctionnalites` | MENU_GESTION |
| Menus | Permissions | `/admin/parametres/permissions` | PERMISSION_GESTION |
| Messages | Libellés | `/admin/parametres/messages/libelles` | MESSAGE_GESTION |
| Messages | Emails | `/admin/parametres/messages/emails` | EMAIL_TEMPLATE_GESTION |
| Référentiels | Salles | `/admin/parametres/salles` | SALLE_GESTION |
| Référentiels | Entreprises | `/admin/parametres/entreprises` | ENTREPRISE_GESTION |
| Maintenance | Audit | `/admin/maintenance/audit` | AUDIT_VIEW |
| Maintenance | Stats | `/admin/maintenance/statistiques` | STATS_VIEW |
| Maintenance | Cache | `/admin/maintenance/cache` | CACHE_GESTION |
| Maintenance | Mode | `/admin/maintenance/mode` | MAINTENANCE_MODE |
