# CheckMaster - Deployment Guide

**Version**: 1.0.0

## Server Requirements

**PHP 8.0+ with extensions**:
- pdo_mysql, mbstring, openssl, intl, gd, zip, fileinfo, json

**Database**: MySQL 8.0+ or MariaDB 10.5+

**Web Server**: Apache 2.4+ with mod_rewrite or Nginx 1.18+

**Resources** (LWS Mutualisé compatible):
- 512MB RAM minimum (1GB recommended)
- 5GB disk space minimum
- HTTPS (Let's Encrypt)

## Initial Setup

```bash
# 1. Clone and install
git clone https://github.com/ManuelD-Aho/check.master.git
cd check.master
composer install --no-dev --optimize-autoloader

# 2. Configure
cp config/database.php.example config/database.php
# Edit with production credentials

cp config/app.php.example config/app.php
# Set DEBUG=false, APP_ENV=production

# 3. Database
php bin/console migrate
php bin/console seed

# 4. Permissions
chmod -R 755 storage/
chmod -R 755 public/

# 5. CRON jobs
crontab -e
# Add:
# * * * * * php /path/to/checkmaster/bin/console queue:process
# 0 3 * * * php /path/to/checkmaster/bin/console backup:database
# 0 4 * * * php /path/to/checkmaster/bin/console archive:verify
```

## Apache Configuration

```apache
<VirtualHost *:80>
    ServerName checkmaster.example.com
    DocumentRoot /var/www/checkmaster/public
    
    <Directory /var/www/checkmaster/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName checkmaster.example.com
    DocumentRoot /var/www/checkmaster/public
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
    
    <Directory /var/www/checkmaster/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## PHP Configuration

```ini
; php.ini
memory_limit = 512M
upload_max_filesize = 20M
post_max_size = 25M
max_execution_time = 300
opcache.enable = 1
opcache.memory_consumption = 128
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log
```

## Updates

```bash
# 1. Backup
php bin/console backup:database
tar -czf backup-$(date +%Y%m%d).tar.gz storage/ config/

# 2. Pull changes
git pull origin main

# 3. Update dependencies
composer install --no-dev --optimize-autoloader

# 4. Run migrations
php bin/console migrate

# 5. Clear cache
php bin/console cache:clear

# 6. Restart PHP-FPM
sudo systemctl restart php8.0-fpm
```

## Backup Strategy

**Automated Daily Backups** (3AM):
```bash
#!/bin/bash
# /opt/checkmaster/backup.sh
DATE=$(date +%Y%m%d)
BACKUP_DIR=/var/backups/checkmaster

# Database
mysqldump -u user -p password checkmaster | gzip > $BACKUP_DIR/db-$DATE.sql.gz

# Files
tar -czf $BACKUP_DIR/files-$DATE.tar.gz /var/www/checkmaster/storage

# Retention: 30 daily, 12 monthly
find $BACKUP_DIR -name "db-*.sql.gz" -mtime +30 -delete
```

**Restore**:
```bash
php bin/console restore --backup=/var/backups/checkmaster/db-20250114.sql.gz
```

## Monitoring

**Check Application Health**:
```bash
curl https://checkmaster.example.com/health
# Expected: {"status":"ok","database":"connected","cache":"working"}
```

**Monitor Logs**:
```bash
tail -f storage/logs/app-$(date +%Y-%m-%d).log
tail -f /var/log/apache2/error.log
```

**Email Alerts** (configured in ServiceNotification):
- Database connection failures
- Disk space < 10%
- Failed backups
- Archive integrity mismatches

## Security Hardening

```bash
# File permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 700 storage/
chmod -R 755 public/

# Disable directory listing
echo "Options -Indexes" > public/.htaccess

# Hide sensitive files
echo "deny from all" > config/.htaccess
echo "deny from all" > storage/.htaccess

# Enable HTTPS only
# Add to .htaccess:
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers in Apache
Header always set Strict-Transport-Security "max-age=31536000"
Header always set X-Frame-Options "DENY"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"
```

## Troubleshooting

**500 Internal Server Error**:
- Check PHP error log
- Verify file permissions
- Check .htaccess syntax

**Database Connection Failed**:
- Verify credentials in config/database.php
- Check MySQL service: `systemctl status mysql`
- Test connection: `mysql -u user -p -h localhost checkmaster`

**Email Not Sending**:
- Check SMTP config in configuration_systeme table
- Verify notifications_queue table for errors
- Check email_bounces table

**Performance Issues**:
- Enable OPcache
- Increase memory_limit
- Add database indexes
- Use caching (Symfony Cache)

## Production Checklist

- [ ] DEBUG mode OFF
- [ ] HTTPS enabled with valid certificate
- [ ] Database credentials secured
- [ ] File permissions set correctly
- [ ] CRON jobs configured
- [ ] Backups tested and automated
- [ ] Email notifications working
- [ ] PDF generation tested
- [ ] All migrations run
- [ ] Seeds populated
- [ ] Error logging to file only
- [ ] Security headers configured
- [ ] Rate limiting enabled
- [ ] Session timeout configured
- [ ] Archive integrity checks scheduled

---

**Version**: 1.0.0  
**Last Updated**: 2025-12-14
