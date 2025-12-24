# CheckMaster - Système de Gestion des Mémoires UFHB

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-Proprietary-red.svg)]()

## Description

CheckMaster est un système de gestion académique complet pour la supervision des mémoires de Master à l'UFHB (Université Félix Houphouët-Boigny). Il offre un workflow structuré en 14 états pour accompagner les étudiants de l'inscription jusqu'à la délivrance du diplôme.

## Fonctionnalités Principales

### 🔄 Workflow 14 États
- **INSCRIT** → **DIPLOME_DELIVRE**
- Transitions contrôlées avec gates (prérequis)
- Alertes SLA (Service Level Agreement)
- Historique complet avec snapshots JSON

### 👥 Gestion des Utilisateurs
- **13 groupes** avec permissions granulaires
- Authentification sécurisée (Argon2id)
- Protection brute-force (verrouillage progressif)
- Sessions avec expiration automatique

### 📋 Commission de Validation
- Système de votes à 3 tours
- Escalade automatique au Doyen
- Quorum et majorité configurables

### 📄 Documents
- Génération de **13 types de PDF** (TCPDF/mPDF)
- Reçus, PV, bulletins, attestations
- Archivage avec intégrité SHA256
- Signatures électroniques (optionnel)

### 📧 Communication
- **71 templates** de notifications email
- Messagerie interne
- Calendrier académique

### 📊 Audit & Traçabilité
- Double logging (DB + fichiers)
- Audit complet de toutes les actions
- Snapshots JSON pour historisation

## Prérequis

### Serveur
- PHP 8.0+ avec extensions : `pdo_mysql`, `mbstring`, `openssl`, `intl`, `gd`, `zip`, `fileinfo`, `json`
- MySQL 8.0+ ou MariaDB 10.5+
- Apache 2.4+ avec `mod_rewrite` ou Nginx 1.18+
- Composer 2.0+

### Ressources Recommandées
- 512MB RAM minimum (1GB recommandé)
- 5GB espace disque minimum
- HTTPS obligatoire en production

## Installation

```bash
# 1. Cloner le projet
git clone https://github.com/ManuelD-Aho/check.master.git
cd check.master

# 2. Installer les dépendances
composer install

# 3. Configurer la base de données
cp app/config/database.php.example app/config/database.php
# Éditer database.php avec vos identifiants MySQL

# 4. Créer la base de données et exécuter les migrations
mysql -u root -p -e "CREATE DATABASE checkmaster CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php bin/console migrate

# 5. Charger les données initiales (optionnel)
php bin/console seed

# 6. Configurer les permissions des dossiers
chmod -R 755 storage/
chmod -R 755 public/

# 7. Lancer le serveur de développement
php -S localhost:8000 -t public/
```

## Configuration

### Fichiers Principaux

| Fichier | Description |
|---------|-------------|
| `app/config/app.php` | Configuration générale |
| `app/config/database.php` | Connexion MySQL |
| `app/config/routes.php` | Définition des routes |
| `app/config/bootstrap.php` | Initialisation application |

### Variables d'Environnement

```bash
APP_ENV=production           # production, development, testing
APP_DEBUG=false              # true uniquement en développement
APP_URL=https://checkmaster.ufhb.edu.ci
```

## Structure du Projet

```
check.master/
├── app/                    # Code applicatif MVC++
│   ├── Controllers/        # Contrôleurs (≤50 lignes)
│   ├── Middleware/         # Pipeline HTTP
│   ├── Models/             # ORM léger
│   ├── Services/           # Logique métier
│   ├── Validators/         # Validation Symfony
│   └── config/             # Configuration
├── bin/                    # Scripts CLI
├── database/               # Migrations et seeds
│   ├── migrations/         # SQL séquentiels
│   └── seeds/              # Données initiales
├── docs/                   # Documentation
│   ├── prd/                # Spécifications fonctionnelles
│   ├── constitution.md     # Principes non-négociables
│   ├── workflows.md        # États et transitions
│   └── deployment.md       # Guide déploiement
├── public/                 # Assets publics
│   └── index.php           # Point d'entrée
├── ressources/             # Ressources
│   ├── views/              # Templates PHP
│   └── templates/          # Templates PDF
├── src/                    # Framework core
│   ├── Container.php       # Injection de dépendances
│   ├── Kernel.php          # Noyau applicatif
│   └── Router.php          # Routeur HTTP
├── storage/                # Fichiers générés
│   ├── cache/              # Cache
│   ├── logs/               # Logs applicatifs
│   └── sessions/           # Sessions PHP
└── tests/                  # Tests PHPUnit
    ├── Unit/               # Tests unitaires
    ├── Feature/            # Tests fonctionnels
    └── Integration/        # Tests intégration
```

## Documentation

| Document | Description |
|----------|-------------|
| [Constitution](docs/constitution.md) | Principes architecturaux non-négociables |
| [Workflows](docs/workflows.md) | Diagramme des 14 états et transitions |
| [Roadmap](docs/roadmap.md) | Plan d'implémentation complet |
| [Déploiement](docs/deployment.md) | Guide de mise en production |
| [Guide Utilisation](docs/guide-utilisation.md) | Manuel utilisateur IA |
| [Audit](docs/AUDIT.md) | Rapport d'audit du projet |
| [Corrections](docs/CORRECTIONS.md) | Corrections détaillées |

### Spécifications (PRD)

- [00 - Vision Globale](docs/prd/00_master_prd.md)
- [01 - Authentification](docs/prd/01_authentication_users.md)
- [02 - Entités Académiques](docs/prd/02_academic_entities.md)
- [03 - Workflow & Commission](docs/prd/03_workflow_commission.md)
- [04 - Soutenance](docs/prd/04_thesis_defense.md)
- [05 - Communication](docs/prd/05_communication.md)
- [06 - Documents](docs/prd/06_documents_archives.md)
- [07 - Financier](docs/prd/07_financial.md)
- [08 - Administration](docs/prd/08_administration.md)

## Qualité du Code

```bash
# Linting PSR-12
composer run fix

# Analyse statique PHPStan (niveau 6)
composer run stan

# Tests unitaires
composer test

# Tous les checks
composer run check
```

### Standards Appliqués
- PSR-12 (Style)
- PSR-4 (Autoloading)
- Type hints stricts (`declare(strict_types=1)`)
- PHPDoc sur méthodes publiques

## Sécurité

### Mesures Implémentées
- ✅ Mots de passe : Argon2id
- ✅ Sessions : Tokens 128 caractères + expiration
- ✅ CSRF : Tokens sur tous les formulaires
- ✅ SQL : Prepared statements uniquement
- ✅ XSS : Échappement `e()` dans les vues
- ✅ Brute-force : Délais progressifs + verrouillage
- ✅ Headers : Security headers (HSTS, X-Frame-Options, etc.)

### Audit
Toutes les actions sont loguées dans :
- Table `pister` (base de données)
- Fichiers `storage/logs/audit-*.log`

## Contribution

1. Fork le projet
2. Créer une branche (`git checkout -b feature/ma-fonctionnalite`)
3. Commiter (`git commit -m 'feat: description'`)
4. Push (`git push origin feature/ma-fonctionnalite`)
5. Ouvrir une Pull Request

### Conventions de Commit
- `feat:` Nouvelle fonctionnalité
- `fix:` Correction de bug
- `docs:` Documentation
- `refactor:` Refactoring
- `test:` Tests
- `chore:` Maintenance

## Support

Pour toute question ou problème :
- 📧 Email : support@checkmaster.ufhb.edu.ci
- 📖 Documentation : [docs/](docs/)
- 🐛 Issues : [GitHub Issues](https://github.com/ManuelD-Aho/check.master/issues)

## Licence

Propriétaire - CheckMaster Team © 2025

Tous droits réservés. Ce logiciel est la propriété de l'UFHB.
