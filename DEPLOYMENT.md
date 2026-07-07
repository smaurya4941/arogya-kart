# ArogyaKart — VPS + Nginx + SSL Deployment Guide

> **Target stack:** Ubuntu 22.04 LTS · PHP 8.3-FPM · MySQL 8 · Nginx · Let's Encrypt (Certbot) · Supervisor

---

## 1. VPS Provisioning

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.3 and required extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-xml \
    php8.3-mbstring php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath \
    php8.3-intl php8.3-tokenizer php8.3-fileinfo

# Install MySQL 8
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (for asset compilation)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Supervisor (queue worker manager)
sudo apt install -y supervisor

# Install Certbot for SSL
sudo apt install -y certbot python3-certbot-nginx
```

---

## 2. MySQL — Create Database & User

```sql
sudo mysql -u root -p

CREATE DATABASE `arogya_kart` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'arogya'@'localhost' IDENTIFIED BY 'CHANGE_THIS_PASSWORD';
GRANT ALL PRIVILEGES ON `arogya_kart`.* TO 'arogya'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 3. Deploy the Application

```bash
# Clone the repo (or upload via SFTP)
cd /var/www
sudo git clone https://github.com/YOUR_ORG/arogya-kart.git arogya-kart
sudo chown -R www-data:www-data arogya-kart
cd arogya-kart/arogya-kart

# Install PHP dependencies (no dev)
composer install --no-dev --optimize-autoloader

# Install & build JS/CSS assets
npm ci
npm run build

# Environment configuration
cp .env.example .env
nano .env          # fill in DB, mail, APP_URL, APP_KEY
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Cache for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache   # if using blade-icons

# Create storage symlink
php artisan storage:link

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 4. Production `.env` Values

```dotenv
APP_NAME=ArogyaKart
APP_ENV=production
APP_DEBUG=false                     # ← CRITICAL: never true in production
APP_URL=https://yourdomain.com

LOG_CHANNEL=daily
LOG_LEVEL=error                     # only log errors in production

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arogya_kart
DB_USERNAME=arogya
DB_PASSWORD=CHANGE_THIS_PASSWORD

SESSION_DRIVER=database
CACHE_STORE=file                    # switch to redis if available
QUEUE_CONNECTION=database           # switch to redis for better performance

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_app_password
MAIL_FROM_ADDRESS=your@email.com
MAIL_FROM_NAME="ArogyaKart"

# Pharmacy-specific
PHARMACY_CURRENCY_SYMBOL=₹
PHARMACY_CURRENCY_CODE=INR
PHARMACY_EXPIRY_ALERT_DAYS=30
PHARMACY_BACKUP_RETENTION_DAYS=30
```

---

## 5. Nginx Server Block

```nginx
# /etc/nginx/sites-available/arogya-kart

server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/arogya-kart/arogya-kart/public;
    index index.php;

    # SSL — managed by Certbot
    ssl_certificate     /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    include             /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam         /etc/letsencrypt/ssl-dhparams.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Hide Nginx version
    server_tokens off;

    # Deny access to hidden files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Laravel front controller
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass   unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include        fastcgi_params;
        fastcgi_read_timeout 300;   # allow for slow PDF generation in non-queued fallbacks
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2?)$ {
        expires 1y;
        access_log off;
        add_header Cache-Control "public, immutable";
    }

    # Uploads — no PHP execution in storage
    location ^~ /storage/ {
        try_files $uri =404;
    }
}
```

```bash
# Enable the site
sudo ln -s /etc/nginx/sites-available/arogya-kart /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

## 6. SSL via Let's Encrypt

```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
# Certbot auto-updates the server block with SSL directives.
# Auto-renewal is already set up via the certbot systemd timer.
sudo systemctl status certbot.timer
```

---

## 7. Supervisor — Queue Worker

```ini
# /etc/supervisor/conf.d/arogya-kart-worker.conf

[program:arogya-kart-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/arogya-kart/arogya-kart/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
directory=/var/www/arogya-kart/arogya-kart
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/arogya-kart-worker.log
stdout_logfile_maxbytes=10MB
stdout_logfile_backups=5
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start arogya-kart-worker:*
sudo supervisorctl status
```

---

## 8. Cron — Laravel Scheduler

```bash
sudo crontab -u www-data -e
```

Add this single line:

```cron
* * * * * cd /var/www/arogya-kart/arogya-kart && php artisan schedule:run >> /dev/null 2>&1
```

This fires every minute; Laravel's scheduler internally checks which commands are due:
- **08:00 daily** → `pharmacy:stock-alerts` (low-stock & expiry notifications)
- **02:00 daily** → `pharmacy:backup` (gzip DB dump → `storage/app/backups/`)

---

## 9. Post-Deploy Checklist

| Step | Command |
|------|---------|
| Clear all caches | `php artisan optimize:clear` |
| Re-cache for production | `php artisan optimize` |
| Run pending migrations | `php artisan migrate --force` |
| Check scheduler | `php artisan schedule:list` |
| Test backup command | `php artisan pharmacy:backup --no-prune` |
| Test stock alerts (dry run) | `php artisan pharmacy:stock-alerts --dry-run` |
| Process one queued job | `php artisan queue:work --once` |
| Check logs | `tail -f storage/logs/laravel-$(date +%Y-%m-%d).log` |

---

## 10. Zero-Downtime Deploys (Optional)

For future zero-downtime releases use a deployer like [Deployer.org](https://deployer.org) or a simple `deploy.sh`:

```bash
#!/bin/bash
set -e
cd /var/www/arogya-kart/arogya-kart

git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build

php artisan down --retry=60         # maintenance mode
php artisan migrate --force
php artisan optimize
php artisan up                       # back online

# Restart queue workers gracefully (finish current job, then restart)
sudo supervisorctl signal SIGUSR2 arogya-kart-worker:*
```

---

## 11. Firewall (UFW)

```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
sudo ufw status
```

---

## 12. Monitoring (Minimal)

```bash
# Laravel Telescope (local/staging only — do NOT enable in production by default)
# composer require laravel/telescope --dev

# Check failed jobs
php artisan queue:failed

# Retry all failed jobs
php artisan queue:retry all

# Flush failed jobs table
php artisan queue:flush
```
