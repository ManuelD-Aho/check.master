# CheckMaster - Résolution des Dépendances et Environnement de Test

**Date**: 2026-01-16  
**Priorité**: 🔴 **CRITIQUE**  
**Statut**: ⚠️ **BLOQUANT** pour exécution des tests

---

## 🚨 Problème Actuel

### Symptômes
```bash
$ composer install
Failed to download symfony/console from dist: Could not authenticate against github.com
```

### Impact
- ❌ Impossible d'installer les dépendances complètes
- ❌ PHPUnit non disponible → tests non exécutables
- ❌ PHPStan non disponible → analyse statique impossible
- ❌ PHP-CS-Fixer non disponible → formatage bloqué
- ✅ Autoloader PSR-4 fonctionne (généré via `composer dump-autoload`)

---

## 🔍 Analyse du Problème

### 1. Incompatibilité Version PHP

**Fichier**: `composer.lock`

Le lock file contient des packages Symfony 8.x qui requièrent PHP 8.4+:
```json
{
  "name": "symfony/console",
  "version": "v8.0.3",
  "require": {
    "php": ">=8.4"
  }
}
```

**Environnement actuel**: PHP 8.3.6

### 2. Authentification GitHub

Composer essaye de télécharger depuis GitHub mais échoue sur l'authentification:
```
Failed to download from dist: Could not authenticate against github.com
Now trying to download from source
```

---

## ✅ Solutions Proposées

### Solution 1: Mettre à Jour composer.json (RECOMMANDÉE)

**Modification**: Ajuster les versions pour PHP 8.3 dans `composer.json`

```json
{
  "require": {
    "php": "^8.0",
    "symfony/validator": "^5.4 || ^6.4 || ^7.2",
    "symfony/http-foundation": "^5.4 || ^6.4 || ^7.2",
    "symfony/cache": "^5.4 || ^6.4 || ^7.2"
  },
  "require-dev": {
    "friendsofphp/php-cs-fixer": "^3.48",
    "phpstan/phpstan": "^1.10",
    "phpunit/phpunit": "^9.6 || ^10.5"
  }
}
```

**Commandes**:
```bash
# Supprimer le lock actuel
rm composer.lock

# Recréer avec versions compatibles
composer update --no-interaction

# Ou forcer les versions spécifiques
composer require --dev phpunit/phpunit:^10.5 --no-interaction
composer require symfony/validator:^7.2 --no-interaction
```

### Solution 2: Configurer Token GitHub

**Si vous avez un compte GitHub**:

```bash
# Créer un Personal Access Token sur GitHub
# Settings → Developer settings → Personal access tokens → Tokens (classic)
# Scopes requis: repo, read:packages

# Configurer globalement
composer config --global github-oauth.github.com YOUR_TOKEN_HERE

# Ou localement pour le projet
composer config github-oauth.github.com YOUR_TOKEN_HERE
```

**Puis réinstaller**:
```bash
composer install --no-interaction
```

### Solution 3: Utiliser Mirror Packagist

**Configurer un miroir alternatif**:

```bash
# Utiliser le miroir officiel Packagist
composer config --global repo.packagist composer https://packagist.org

# Ou miroir plus rapide (si disponible dans votre région)
composer config --global repo.packagist composer https://mirrors.aliyun.com/composer/

# Puis réinstaller
composer install --no-interaction --prefer-dist
```

### Solution 4: Installation Manuelle des Packages Critiques

**Si tout échoue, installer manuellement PHPUnit**:

```bash
# Télécharger PHPUnit PHAR
wget https://phar.phpunit.de/phpunit-10.5.phar -O phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit

# Tester
phpunit --version

# Télécharger PHPStan PHAR
wget https://github.com/phpstan/phpstan/releases/latest/download/phpstan.phar
chmod +x phpstan.phar
sudo mv phpstan.phar /usr/local/bin/phpstan

# Tester
phpstan --version
```

**Puis mettre à jour phpunit.xml**:
```xml
<phpunit bootstrap="vendor/autoload.php">
  <!-- Configuration existante -->
</phpunit>
```

---

## 🔧 Plan de Résolution Étape par Étape

### Étape 1: Diagnostic
```bash
# Vérifier version PHP
php -v
# Attendu: PHP 8.3.6

# Vérifier Composer
composer --version
# Attendu: Composer 2.x

# Vérifier état actuel
composer diagnose
```

### Étape 2: Nettoyage
```bash
# Supprimer le cache Composer
composer clear-cache

# Supprimer le lock et vendor incomplets
rm -rf composer.lock vendor/

# Vérifier .gitignore
cat .gitignore | grep vendor
# Doit contenir: /vendor/
```

### Étape 3: Mise à Jour composer.json

**Appliquer les versions compatibles PHP 8.3**:

```bash
# Éditer composer.json manuellement ou:
composer require --dev phpunit/phpunit:^10.5 --no-update
composer require symfony/validator:~7.2.0 --no-update
composer require symfony/http-foundation:~7.2.0 --no-update
composer require symfony/cache:~7.2.0 --no-update

# Résoudre les dépendances
composer update --no-install
```

### Étape 4: Installation avec Options

**Option A: Ignorer Platform Requirements** (temporaire):
```bash
composer install --ignore-platform-reqs --no-interaction
```

**Option B: Préférer Source** (GitHub auth requise):
```bash
composer config github-oauth.github.com YOUR_TOKEN
composer install --prefer-source --no-interaction
```

**Option C: Préférer Dist** (sans GitHub):
```bash
composer install --prefer-dist --no-interaction
```

### Étape 5: Vérification
```bash
# Vérifier que l'autoloader fonctionne
php -r "require 'vendor/autoload.php'; echo 'OK';"

# Vérifier PHPUnit
php vendor/bin/phpunit --version

# Lancer les tests
php vendor/bin/phpunit

# Vérifier PHPStan
php vendor/bin/phpstan --version
```

---

## 📦 Versions Recommandées (PHP 8.3 Compatible)

```json
{
  "require": {
    "php": "^8.0",
    "hashids/hashids": "^4.0 || ^5.0",
    "symfony/validator": "^7.2",
    "symfony/http-foundation": "^7.2",
    "symfony/cache": "^7.2",
    "mpdf/mpdf": "^8.2",
    "tecnickcom/tcpdf": "^6.7",
    "phpoffice/phpspreadsheet": "^1.29 || ^2.0",
    "phpmailer/phpmailer": "^6.9",
    "monolog/monolog": "^3.5",
    "ext-pdo": "*",
    "ext-mbstring": "*",
    "ext-openssl": "*",
    "ext-intl": "*"
  },
  "require-dev": {
    "friendsofphp/php-cs-fixer": "^3.48",
    "phpstan/phpstan": "^1.10",
    "phpunit/phpunit": "^10.5"
  }
}
```

---

## 🐳 Alternative: Environnement Docker

**Si les problèmes persistent, utiliser Docker**:

### Dockerfile
```dockerfile
FROM php:8.3-cli

# Extensions PHP requises
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring gd intl zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

# Installation dépendances
RUN composer install --no-interaction --prefer-dist

CMD ["php", "vendor/bin/phpunit"]
```

### docker-compose.yml
```yaml
version: '3.8'
services:
  app:
    build: .
    volumes:
      - .:/app
    environment:
      - APP_ENV=testing
      - DB_CONNECTION=mysql
      - DB_HOST=db
      - DB_DATABASE=checkmaster_test
      - DB_USERNAME=root
      - DB_PASSWORD=secret

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: checkmaster_test
    ports:
      - "3306:3306"
```

### Utilisation
```bash
# Build
docker-compose build

# Installer dépendances
docker-compose run app composer install

# Lancer tests
docker-compose run app php vendor/bin/phpunit

# Bash interactif
docker-compose run app bash
```

---

## 🔍 Vérification Post-Installation

### Checklist
```bash
# ✅ Autoloader fonctionne
php -r "require 'vendor/autoload.php'; echo 'OK\n';"

# ✅ PHPUnit installé
php vendor/bin/phpunit --version

# ✅ PHPStan installé
php vendor/bin/phpstan --version

# ✅ PHP-CS-Fixer installé
php vendor/bin/php-cs-fixer --version

# ✅ Classes App disponibles
php -r "require 'vendor/autoload.php'; var_dump(class_exists('App\Models\Utilisateur'));"

# ✅ Classes Src disponibles
php -r "require 'vendor/autoload.php'; var_dump(class_exists('Src\Http\Request'));"

# ✅ Helpers chargés
php -r "require 'vendor/autoload.php'; var_dump(function_exists('e'));"

# ✅ Lancer 1 test simple
php vendor/bin/phpunit tests/Unit/Support/HelpersTest.php
```

---

## 🚀 Actions Immédiates Recommandées

### Pour l'Équipe CheckMaster

**1. Configurer GitHub Token** (10 min)
```bash
# Sur GitHub.com
# Settings → Developer settings → Personal access tokens → Generate new token
# Scopes: repo, read:packages

# Sur votre machine
composer config --global github-oauth.github.com ghp_VOTRE_TOKEN_ICI
```

**2. Mettre à Jour composer.json** (5 min)
```bash
# Supprimer lock
rm composer.lock

# Mettre à jour les contraintes
# Éditer composer.json avec les versions ci-dessus

# Réinstaller
composer update --no-interaction
composer install --no-interaction
```

**3. Vérifier Installation** (5 min)
```bash
# Tests
composer test

# Analyse
composer stan

# Formatage
composer fix
```

**4. Committer** (2 min)
```bash
git add composer.json composer.lock
git commit -m "fix(deps): Update dependencies for PHP 8.3 compatibility"
git push
```

### Pour les CI/CD (GitHub Actions)

**Ajouter dans `.github/workflows/tests.yml`**:
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: mbstring, pdo, pdo_mysql, intl, gd, zip
          coverage: xdebug
      
      - name: Validate composer.json
        run: composer validate --strict
      
      - name: Cache Composer packages
        uses: actions/cache@v3
        with:
          path: vendor
          key: ${{ runner.os }}-php-${{ hashFiles('**/composer.lock') }}
          restore-keys: |
            ${{ runner.os }}-php-
      
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress
      
      - name: Run test suite
        run: composer test
      
      - name: Run PHPStan
        run: composer stan
```

---

## 📞 Support

### Si Problèmes Persistent

1. **Vérifier logs Composer**:
   ```bash
   composer install -vvv 2>&1 | tee composer-install.log
   ```

2. **Partager le log**:
   - Créer une issue GitHub avec le fichier `composer-install.log`
   - Inclure: `php -v`, `composer --version`, `cat composer.json`

3. **Solutions de Contournement**:
   - Utiliser Docker (voir ci-dessus)
   - Installer PHPUnit PHAR manuellement
   - Demander à un membre de l'équipe de partager le dossier `vendor/` zippé

---

**Dernière mise à jour**: 2026-01-16  
**Auteur**: Équipe CheckMaster  
**Statut**: 🔴 Critique - À résoudre en priorité
