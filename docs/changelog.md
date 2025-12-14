# Changelog

All notable changes to CheckMaster will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Complete agent configuration with CheckMaster domain context
- Comprehensive documentation (constitution, canvas, workbench, workflows, deployment)
- Database migration framework with 13 sequential migrations
- Seed data for reference tables

## [1.0.0] - 2025-01-15 (Planned)

### Added
- **Core Workflow**: 14-state thesis supervision workflow (INSCRIT → DIPLOME_DELIVRE)
- **User Management**: 13 user groups with granular permissions
- **Commission System**: 3-round voting with Dean escalation
- **Document Generation**: 13 PDF types (reçus, bulletins, PV, attestations)
- **Notification System**: 71 email templates for automated communications
- **Financial Management**: Payment tracking, pénalités, exonérations
- **Audit Trail**: Double logging (Monolog + database) with snapshots
- **Security**: Argon2id passwords, Hashids routing, CSRF protection
- **Configuration**: ~170 DB-driven parameters with 27 feature flags

### Architecture
- Native PHP 8.0+ MVC++ architecture (no heavy framework)
- MySQL 8.0+ database with 67 tables
- 12MB dependency footprint (LWS mutualisé compatible)
- PSR-12 compliant codebase
- PHPStan level 6+ static analysis
- 80%+ test coverage

### Security
- All entity IDs use Hashids in URLs
- Prepared statements only (no raw SQL)
- Output escaping with e() helper
- ServiceAudit logging on all writes
- Permission checks via ServicePermission
- Rate limiting on authentication endpoints

## [0.5.0] - 2024-12-01 (Development)

### Added
- Initial project structure
- Database schema design
- Core service layer (Workflow, Permission, Notification, Audit, Pdf, Parametres)
- Authentication system with session management
- Basic CRUD operations for entities

### Changed
- Migrated from Laravel to native MVC++ architecture
- Switched to database-driven configuration

## [0.1.0] - 2024-09-15 (Prototype)

### Added
- Initial prototype with basic student management
- Simple candidature submission
- Basic commission workflow

---

## Version Numbering

**Format**: MAJOR.MINOR.PATCH

- **MAJOR**: Incompatible architecture changes
- **MINOR**: New features, backwards-compatible
- **PATCH**: Bug fixes, backwards-compatible

## Change Categories

- **Added**: New features
- **Changed**: Changes in existing functionality
- **Deprecated**: Soon-to-be removed features
- **Removed**: Removed features
- **Fixed**: Bug fixes
- **Security**: Security vulnerabilities fixed
