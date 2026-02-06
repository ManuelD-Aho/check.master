# GUIDE DE DÉMARRAGE RAPIDE
## Plateforme MIAGE-GI - Configuration et Vérification

**Date**: 2026-02-06
**Version**: 1.0

---

## TABLE DES MATIÈRES

1. [Prérequis](#1-prérequis)
2. [Installation](#2-installation)
3. [Configuration](#3-configuration)
4. [Initialisation Base de Données](#4-initialisation-base-de-données)
5. [Premier Démarrage](#5-premier-démarrage)
6. [Vérifications Essentielles](#6-vérifications-essentielles)
7. [Tests Fonctionnels](#7-tests-fonctionnels)
8. [Dépannage](#8-dépannage)

---

## 1. PRÉREQUIS

### Serveur

| Composant | Version Minimum | Recommandé |
|-----------|----------------|------------|
| PHP | 8.4.0 | 8.4.x |
| MySQL | 8.0 | 8.0.x |
| Apache | 2.4 | 2.4.x avec mod_rewrite |
| Composer | 2.x | Latest |

### Extensions PHP Requises

```bash
php -m | grep -E "pdo_mysql|mbstring|json|openssl|xml|curl|gd|zip|intl"
```

Toutes ces extensions doivent être activées:
- ✅ pdo_mysql
- ✅ mbstring
- ✅ json
- ✅ openssl
- ✅ xml
- ✅ curl
- ✅ gd (pour images)
- ✅ zip
- ✅ intl

### Vérification PHP

```bash
php -v
# Doit afficher: PHP 8.4.x

php -i | grep "memory_limit"
# Minimum: 256M (recommandé: 512M)

php -i | grep "upload_max_filesize"
# Minimum: 10M (recommandé: 20M)
```

---

## 2. INSTALLATION

### 2.1 Cloner le Repository

```bash
cd /var/www/
git clone https://github.com/ManuelD-Aho/check.master.git miage-platform
cd miage-platform
```

### 2.2 Installer les Dépendances

```bash
composer install --optimize-autoloader --no-dev
```

**⚠️ Important**: En production, utilisez `--no-dev` pour ne pas installer les dépendances de développement.

En développement:
```bash
composer install
```

### 2.3 Permissions Fichiers

```bash
# Propriétaire Apache (ajuster selon votre système)
sudo chown -R www-data:www-data /var/www/miage-platform

# Permissions répertoires storage
chmod -R 755 storage/
chmod -R 755 storage/cache
chmod -R 755 storage/logs
chmod -R 755 storage/sessions
chmod -R 755 storage/documents
chmod -R 755 storage/uploads

# Créer les répertoires si manquants
mkdir -p storage/{cache,logs,sessions,documents,uploads}
```

---

## 3. CONFIGURATION

### 3.1 Fichier .env

```bash
cp .env.example .env
nano .env
```

### 3.2 Générer les Clés Sécurisées

#### APP_SECRET (32+ caractères)
```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

#### JWT_SECRET (32+ caractères)
```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

#### ENCRYPTION_KEY (defuse/php-encryption)
```bash
php -r "require 'vendor/autoload.php'; echo Defuse\Crypto\Key::createNewRandomKey()->saveToAsciiSafeString() . PHP_EOL;"
```

### 3.3 Configuration .env Complète

```ini
# Application
APP_ENV=production
APP_DEBUG=false
APP_SECRET=<généré ci-dessus>
APP_URL=https://votre-domaine.com

# Base de données
DB_HOST=localhost
DB_PORT=3306
DB_NAME=miage_platform
DB_USER=miage_user
DB_PASS=<mot-de-passe-sécurisé>
DB_CHARSET=utf8mb4

# JWT
JWT_SECRET=<généré ci-dessus>

# Encryption
ENCRYPTION_KEY=<généré ci-dessus>

# SMTP (à configurer avec vos credentials)
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USERNAME=noreply@miage.edu
SMTP_PASSWORD=<votre-mot-de-passe-smtp>
SMTP_ENCRYPTION=tls
EMAIL_FROM=noreply@miage.edu
EMAIL_FROM_NAME=Plateforme MIAGE

# Chemins (laisser par défaut)
STORAGE_PATH=storage
LOGS_PATH=storage/logs
CACHE_PATH=storage/cache
SESSIONS_PATH=storage/sessions
DOCUMENTS_PATH=storage/documents
UPLOADS_PATH=storage/uploads

# Sécurité
SESSION_TIMEOUT=480
PASSWORD_MIN_LENGTH=8
LOGIN_MAX_ATTEMPTS=5
LOGIN_LOCKOUT_DURATION=15
```

### 3.4 Apache VirtualHost

Créer `/etc/apache2/sites-available/miage-platform.conf`:

```apache
<VirtualHost *:80>
    ServerName miage.example.com
    ServerAdmin admin@example.com
    DocumentRoot /var/www/miage-platform/public

    <Directory /var/www/miage-platform/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/miage-error.log
    CustomLog ${APACHE_LOG_DIR}/miage-access.log combined
</VirtualHost>
```

Activer le site:
```bash
sudo a2ensite miage-platform
sudo a2enmod rewrite
sudo systemctl reload apache2
```

---

## 4. INITIALISATION BASE DE DONNÉES

### 4.1 Créer la Base de Données

```bash
mysql -u root -p
```

```sql
CREATE DATABASE miage_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'miage_user'@'localhost' IDENTIFIED BY 'mot-de-passe-sécurisé';

GRANT ALL PRIVILEGES ON miage_platform.* TO 'miage_user'@'localhost';

FLUSH PRIVILEGES;

EXIT;
```

### 4.2 Importer le Schema

```bash
mysql -u miage_user -p miage_platform < database/schema.sql
```

Vérifier l'import:
```bash
mysql -u miage_user -p miage_platform -e "SHOW TABLES;"
```

Vous devriez voir 50+ tables.

### 4.3 Créer le Super Admin Initial

```sql
USE miage_platform;

-- Insérer le type utilisateur Personnel
INSERT INTO type_utilisateur (id_type_utilisateur, code_type_utilisateur, libelle_type_utilisateur, actif)
VALUES (3, 'PERSONNEL', 'Personnel Administratif', TRUE);

-- Insérer le groupe Super Admin
INSERT INTO groupe_utilisateur (code_groupe, libelle_groupe, id_type_utilisateur, est_modifiable, actif)
VALUES ('SUPER_ADMIN', 'Super Administrateur', 3, FALSE, TRUE);

-- Insérer le niveau d'accès ALL
INSERT INTO niveau_acces_donnees (code_niveau, libelle_niveau)
VALUES ('ALL', 'Toutes données');

-- Créer l'utilisateur super admin
-- Mot de passe temporaire: Admin@2025 (à changer immédiatement après connexion)
-- Hash Argon2id de "Admin@2025":
INSERT INTO utilisateur (
    nom_utilisateur,
    id_type_utilisateur,
    id_groupe_utilisateur,
    id_niveau_acces,
    statut_utilisateur,
    login_utilisateur,
    mot_de_passe_hash,
    email_utilisateur,
    is_2fa_enabled,
    date_creation,
    date_modification
) VALUES (
    'Super Administrateur',
    3,
    (SELECT id_groupe_utilisateur FROM groupe_utilisateur WHERE code_groupe = 'SUPER_ADMIN'),
    (SELECT id_niveau_acces FROM niveau_acces_donnees WHERE code_niveau = 'ALL'),
    'actif',
    'admin',
    '$argon2id$v=19$m=65536,t=4,p=1$cG5yL0lXUGFsZkRXczZ5Wg$kKx7HCLJu3CxHvSRfLNdjlqBRm5J3r3sFNzJvWxgqKc',
    'admin@miage.edu',
    FALSE,
    NOW(),
    NOW()
);
```

**⚠️ IMPORTANT**: Changez ce mot de passe immédiatement après la première connexion!

---

## 5. PREMIER DÉMARRAGE

### 5.1 Tester l'Accès

Ouvrir un navigateur et accéder à:
```
http://miage.example.com/login
```

Vous devriez voir la page de connexion.

### 5.2 Première Connexion

**Credentials**:
- Login: `admin`
- Mot de passe: `Admin@2025`

Vous serez redirigé vers la page de changement de mot de passe obligatoire.

### 5.3 Vérifier les Logs

```bash
tail -f storage/logs/app.log
tail -f storage/logs/audit.log
```

Aucune erreur ne doit apparaître lors de la connexion.

---

## 6. VÉRIFICATIONS ESSENTIELLES

### 6.1 Checklist Technique

```bash
# ✅ PHP Version
php -v | grep "PHP 8.4"

# ✅ Extensions PHP
php -m | grep -E "pdo_mysql|mbstring|json|openssl"

# ✅ Permissions storage/
ls -ld storage/*/

# ✅ Fichier .env existe
test -f .env && echo "✅ .env existe" || echo "❌ .env manquant"

# ✅ Vendor installé
test -d vendor && echo "✅ Vendor installé" || echo "❌ Lancer composer install"

# ✅ Base de données accessible
mysql -u miage_user -p miage_platform -e "SELECT COUNT(*) FROM utilisateur;"

# ✅ Logs écrivables
touch storage/logs/test.log && echo "✅ Logs OK" || echo "❌ Permissions logs"

# ✅ Cache écrivable
touch storage/cache/test.cache && echo "✅ Cache OK" || echo "❌ Permissions cache"
```

### 6.2 Checklist Fonctionnelle

| Fonctionnalité | Test | Statut |
|----------------|------|--------|
| **Login** | Se connecter avec admin/Admin@2025 | ☐ |
| **Changement MDP** | Changer le mot de passe | ☐ |
| **Dashboard** | Accéder au tableau de bord | ☐ |
| **Menu** | Vérifier affichage des menus | ☐ |
| **Utilisateurs** | Accéder à /admin/utilisateurs | ☐ |
| **Étudiants** | Accéder à /admin/etudiants | ☐ |
| **Logs** | Vérifier écriture dans audit.log | ☐ |

---

## 7. TESTS FONCTIONNELS

### 7.1 Test Complet Module Utilisateurs (PRD 01)

#### Test 1: Création d'un Utilisateur
1. Aller sur `/admin/utilisateurs`
2. Cliquer "Nouveau utilisateur"
3. Remplir le formulaire:
   - Nom: Test Utilisateur
   - Email: test@miage.edu
   - Type: Personnel Administratif
   - Groupe: Secrétariat
4. Soumettre
5. ✅ Vérifier: Email envoyé avec identifiants

#### Test 2: Rate Limiting
1. Aller sur `/login`
2. Tenter 5 connexions avec mauvais mot de passe
3. ✅ Vérifier: Message "Compte temporairement bloqué"
4. Attendre 15 minutes OU débloquer depuis admin

#### Test 3: Permissions
1. Créer un utilisateur avec groupe "Consultation Seule"
2. Se connecter avec ce compte
3. ✅ Vérifier: Pas d'accès aux boutons "Créer", "Modifier", "Supprimer"

### 7.2 Test Complet Module Étudiants (PRD 02)

#### Test 1: Création Étudiant
1. Aller sur `/admin/etudiants/nouveau`
2. Remplir:
   - Nom: DUPONT
   - Prénom: Jean
   - Email: jean.dupont@example.com
   - Date naissance: 1998-05-15
   - Filière: MIAGE
   - Promotion: 2024-2025
3. Soumettre
4. ✅ Vérifier: Matricule généré (ex: ETU202400001)

#### Test 2: Inscription
1. Sur la fiche de l'étudiant, cliquer "Inscrire"
2. Sélectionner:
   - Année: 2024-2025
   - Niveau: Master 2
   - Tranches: 3
3. ✅ Vérifier: Échéancier créé automatiquement

#### Test 3: Versement
1. Sur l'inscription, cliquer "Nouveau versement"
2. Saisir:
   - Montant: 200000 FCFA
   - Méthode: Espèces
3. Soumettre
4. ✅ Vérifier:
   - Reçu PDF téléchargeable
   - Reste à payer mis à jour
   - Échéance marquée "payée"

#### Test 4: Notes
1. Aller sur "Saisie notes"
2. Saisir moyenne M1: 14.50
3. Saisir notes S1 M2 par UE
4. ✅ Vérifier: Moyenne S1 calculée automatiquement

#### Test 5: Compte Utilisateur
1. Depuis la fiche étudiant, cliquer "Générer compte"
2. ✅ Vérifier:
   - Login créé (jean.dupont)
   - Email envoyé avec identifiants
   - Lien vers utilisateur créé

### 7.3 Test Complet Module Candidatures (PRD 03)

#### Test 1: Accès Rapport Verrouillé
1. Se connecter avec compte étudiant
2. Essayer d'accéder `/etudiant/rapport`
3. ✅ Vérifier: Message "Section verrouillée - Candidature requise"

#### Test 2: Création Candidature
1. Aller sur `/etudiant/candidature`
2. Remplir formulaire:
   - Entreprise: Nouvelle (ou sélectionner existante)
   - Sujet: Développement application mobile
   - Date début: 2025-03-01
   - Date fin: 2025-08-31
   - Encadrant: Nom, email, téléphone
3. ✅ Vérifier: Sauvegarde automatique toutes les 30s

#### Test 3: Soumission Candidature
1. Sur la candidature en brouillon, cliquer "Soumettre"
2. ✅ Vérifier:
   - Statut → "soumise"
   - Formulaire verrouillé
   - Email envoyé au validateur

#### Test 4: Validation Admin
1. Se connecter comme admin
2. Aller sur `/admin/candidatures`
3. Ouvrir la candidature soumise
4. Cliquer "Valider"
5. ✅ Vérifier:
   - Statut → "validee"
   - Email envoyé à l'étudiant
   - Section rapport débloquée

#### Test 5: Accès Rapport Débloqué
1. Se reconnecter comme étudiant
2. Accéder `/etudiant/rapport`
3. ✅ Vérifier: Accès autorisé, choix de modèle affiché

### 7.4 Test Complet Module Rapports (PRD 04)

#### Test 1: Choix Modèle
1. Sur `/etudiant/rapport/nouveau`
2. Sélectionner "Modèle Standard MIAGE"
3. ✅ Vérifier: Éditeur chargé avec structure pré-remplie

#### Test 2: Rédaction
1. Dans l'éditeur:
   - Modifier le titre
   - Rédiger du contenu (min 5000 mots pour test)
   - Insérer une image
   - Créer un tableau
2. ✅ Vérifier:
   - Compteur de mots fonctionne
   - Sauvegarde auto toutes les 60s
   - Message "Sauvegardé" apparaît

#### Test 3: Soumission Rapport
1. Cliquer "Soumettre mon rapport"
2. Confirmer
3. ✅ Vérifier:
   - Statut → "soumis"
   - Éditeur verrouillé
   - PDF généré et téléchargeable
   - Email envoyé au vérificateur

#### Test 4: Vérification Admin
1. Se connecter comme vérificateur
2. Aller sur `/admin/rapports/verification`
3. Ouvrir le rapport
4. Tester:
   - Option A: Cliquer "Approuver" → Statut "approuve"
   - Option B: Cliquer "Retourner" avec commentaire → Statut "retourne"
5. ✅ Vérifier: Email envoyé à l'étudiant

#### Test 5: Re-soumission (si retourné)
1. Se reconnecter comme étudiant
2. Voir le commentaire de retour
3. Modifier le rapport
4. Re-soumettre
5. ✅ Vérifier: Nouvelle version créée, workflow recommence

---

## 8. DÉPANNAGE

### Problème: Page blanche

**Diagnostic**:
```bash
tail -f storage/logs/app.log
```

**Solutions courantes**:
- Vérifier permissions storage/
- Vérifier configuration .env (clés générées)
- Activer le debug: `APP_DEBUG=true` dans .env
- Vérifier logs Apache: `/var/log/apache2/miage-error.log`

### Problème: Erreur 500 - Base de données

**Diagnostic**:
```bash
mysql -u miage_user -p miage_platform -e "SELECT 1;"
```

**Solutions**:
- Vérifier credentials dans .env
- Vérifier que la base existe
- Vérifier que l'utilisateur a les permissions
- Re-importer schema.sql si nécessaire

### Problème: Emails non envoyés

**Diagnostic**:
```bash
tail -f storage/logs/app.log | grep -i "email"
```

**Solutions**:
- Vérifier configuration SMTP dans .env
- Tester connexion SMTP:
```php
php -r "
require 'vendor/autoload.php';
\$mail = new PHPMailer\PHPMailer\PHPMailer();
\$mail->isSMTP();
\$mail->Host = 'smtp.example.com';
\$mail->Port = 587;
\$mail->SMTPAuth = true;
\$mail->Username = 'user@example.com';
\$mail->Password = 'password';
if(\$mail->smtpConnect()) {
    echo 'SMTP OK';
} else {
    echo 'SMTP ERREUR';
}
"
```

### Problème: Sessions perdues

**Solutions**:
- Vérifier permissions `storage/sessions/`
- Vérifier `session.save_path` dans php.ini
- Vérifier cookie_secure si HTTPS

### Problème: Upload d'images échoue

**Solutions**:
- Vérifier `upload_max_filesize` et `post_max_size` dans php.ini
- Vérifier permissions `storage/uploads/`
- Vérifier extension GD installée: `php -m | grep gd`

---

## 9. COMMANDES UTILES

### Vider le Cache
```bash
rm -rf storage/cache/*
```

### Voir les Derniers Logs
```bash
tail -n 100 storage/logs/app.log
tail -n 100 storage/logs/audit.log
```

### Vérifier Taille Storage
```bash
du -sh storage/*
```

### Backup Base de Données
```bash
mysqldump -u miage_user -p miage_platform > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restaurer Backup
```bash
mysql -u miage_user -p miage_platform < backup_20250206_120000.sql
```

### Nettoyer Anciennes Sessions
```bash
find storage/sessions/ -type f -mtime +7 -delete
```

### Nettoyer Anciennes Auto-saves Rapports
```sql
DELETE FROM versions_rapport
WHERE type_version = 'auto_save'
AND date_creation < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

---

## 10. SÉCURITÉ - CHECKLIST FINALE

Avant de mettre en production, vérifier:

```
☐ APP_DEBUG=false dans .env
☐ Toutes les clés générées (APP_SECRET, JWT_SECRET, ENCRYPTION_KEY)
☐ Mot de passe super admin changé
☐ Credentials SMTP configurés
☐ Permissions fichiers correctes (755 pour storage/)
☐ HTTPS activé (Let's Encrypt recommandé)
☐ Firewall configuré (ports 80, 443 ouverts)
☐ Backups automatiques BDD configurés
☐ Logs rotatifs activés
☐ Sessions sécurisées (cookie_secure si HTTPS)
☐ Rate limiting vérifié (5 tentatives / 15 min)
☐ CSRF protection active
☐ Audit trail fonctionnel
```

---

## 11. SUPPORT

### Documentation
- **PRDs complets**: `.opencode/PRD/`
- **Rapport de complétion**: `COMPLETION_REPORT_PRD_01-04.md`
- **Plan de développement**: `PLAN_DEVELOPPEMENT_COMPLET.md`

### Logs
- **Application**: `storage/logs/app.log`
- **Audit**: `storage/logs/audit.log`
- **Apache**: `/var/log/apache2/miage-error.log`

### Contact
Pour toute question technique:
1. Consulter d'abord les PRDs correspondants
2. Vérifier les logs
3. Contacter l'équipe de développement

---

**Bon démarrage avec la Plateforme MIAGE-GI!** 🚀

*Document généré le 2026-02-06*
