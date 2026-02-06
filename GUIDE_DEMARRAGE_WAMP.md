# Guide de démarrage avec WAMP

Ce guide explique comment démarrer l'application **Check Master** (plateforme MIAGE) en utilisant **WampServer** sous Windows.

---

## Prérequis

- **WampServer** 3.3+ installé (inclut Apache, MySQL et PHP)
  - Téléchargement : [https://www.wampserver.com/](https://www.wampserver.com/)
- **PHP 8.3** ou supérieur (inclus avec WampServer 3.3+)
- **MySQL 8.0** ou **MariaDB 10.6+**

---

## Étapes d'installation

### 1. Copier le projet dans WAMP

Copiez le dossier du projet dans le répertoire `www` de WampServer :

```
C:\wamp64\www\check.master\
```

### 2. Configurer l'environnement

Copiez le fichier `.env.example` en `.env` à la racine du projet :

```bash
copy .env.example .env
```

Ouvrez le fichier `.env` avec un éditeur de texte et configurez les paramètres suivants :

```ini
APP_ENV=development
APP_DEBUG=true
APP_SECRET=votre_cle_secrete_aleatoire
APP_URL=http://localhost/check.master/public

DB_HOST=localhost
DB_PORT=3306
DB_NAME=miage_platform
DB_USER=root
DB_PASS=

STORAGE_PATH=storage
LOGS_PATH=storage/logs
CACHE_PATH=storage/cache
SESSIONS_PATH=storage/sessions
DOCUMENTS_PATH=storage/documents
UPLOADS_PATH=storage/uploads
```

### 3. Créer la base de données

1. Démarrez WampServer (icône dans la barre des tâches → clic gauche → **Démarrer tous les services**)
2. Ouvrez **phpMyAdmin** : [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
3. Créez une nouvelle base de données nommée `miage_platform` avec l'encodage `utf8mb4_unicode_ci`

Si un fichier de migration SQL est disponible dans `database/`, importez-le via phpMyAdmin :

- Sélectionnez la base `miage_platform`
- Onglet **Importer** → choisissez le fichier SQL → **Exécuter**

### 4. Créer les dossiers de stockage

Créez les répertoires nécessaires s'ils n'existent pas déjà :

```bash
mkdir storage
mkdir storage\logs
mkdir storage\cache
mkdir storage\sessions
mkdir storage\documents
mkdir storage\uploads
```

Assurez-vous que ces dossiers ont les droits d'écriture (sous Windows, c'est généralement le cas par défaut).

### 5. Vérifier les extensions PHP

Depuis l'icône de WampServer dans la barre des tâches :

1. Clic gauche → **PHP** → **Extensions PHP**
2. Vérifiez que les extensions suivantes sont activées (cochées) :
   - `php_pdo_mysql`
   - `php_mbstring`
   - `php_intl`
   - `php_json` (activé par défaut en PHP 8+)
   - `php_openssl`
   - `php_gd`
   - `php_fileinfo`

### 6. Configurer le Virtual Host (Optionnel mais recommandé)

Pour accéder à l'application via une URL propre (ex : `http://check.local`) :

1. Clic gauche sur l'icône WampServer → **Your VirtualHosts** → **Gestion des VirtualHosts**
2. Remplissez :
   - **Nom du VirtualHost** : `check.local`
   - **Chemin du VirtualHost** : `C:/wamp64/www/check.master/public`
3. Cliquez sur **Démarrer la création du VirtualHost**
4. Redémarrez les services Apache

Ajoutez dans `C:\Windows\System32\drivers\etc\hosts` :
```
127.0.0.1 check.local
```

### 7. Configurer Apache (si pas de VirtualHost)

Si vous n'utilisez pas de VirtualHost, assurez-vous que le `.htaccess` dans le dossier `public/` est pris en compte :

1. Clic gauche sur l'icône WampServer → **Apache** → **Modules Apache**
2. Vérifiez que `rewrite_module` est activé (coché)

---

## Démarrage de l'application

### Avec VirtualHost :
Ouvrez votre navigateur et accédez à :
```
http://check.local
```

### Sans VirtualHost :
Ouvrez votre navigateur et accédez à :
```
http://localhost/check.master/public
```

---

## Résolution des problèmes courants

### Erreur 500 (Internal Server Error)
- Vérifiez les logs dans `storage/logs/app-*.log`
- Vérifiez que le fichier `.env` existe et contient les bonnes valeurs
- Vérifiez que la base de données est bien créée et accessible

### Page blanche
- Activez le mode debug dans `.env` : `APP_DEBUG=true`
- Consultez les logs Apache : `C:\wamp64\logs\apache_error.log`

### Erreur de connexion à la base de données
- Vérifiez que MySQL est démarré (icône WampServer verte)
- Vérifiez les identifiants dans `.env` (DB_USER, DB_PASS)
- Vérifiez que la base `miage_platform` existe

### Les fichiers PDF ne se génèrent pas
- Vérifiez que le dossier `storage/documents` existe et est inscriptible
- Vérifiez que l'extension `php_gd` est activée

---

## Structure des URLs principales

| URL | Description |
|-----|-------------|
| `/login` | Page de connexion |
| `/admin` | Tableau de bord administrateur |
| `/admin/maintenance/statistiques` | Statistiques du système |
| `/admin/maintenance/cache` | Gestion du cache |
| `/admin/maintenance/audit` | Journal d'audit |
| `/admin/maintenance/mode` | Mode maintenance |
| `/admin/documents` | Gestion des documents |
| `/etudiant` | Espace étudiant |
| `/commission` | Espace commission |
| `/encadreur` | Espace encadreur |
