# 🚀 Migrasi Jalan Bareng ke VPS Biznet Gio

> **Dokumentasi lengkap migrasi dari Railway ke VPS dengan script otomatis**
> 
> **Estimasi waktu:** 4 jam (termasuk testing)
> 
> **Biaya:** Rp 50.000/bulan (VPS) + Rp 0 (frontend & backup storage gratis)

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Phase 1: Provisioning VPS](#phase-1-provisioning-vps)
3. [Phase 2: Install Stack (Automated)](#phase-2-install-stack-automated)
4. [Phase 3: Deploy Backend](#phase-3-deploy-backend)
5. [Phase 4: Migrate Database](#phase-4-migrate-database)
6. [Phase 5: Setup SSL & Domain](#phase-5-setup-ssl--domain)
7. [Phase 6: Auto-Backup System](#phase-6-auto-backup-system)
8. [Phase 7: Update Frontend](#phase-7-update-frontend)
9. [Phase 8: Monitoring & Maintenance](#phase-8-monitoring--maintenance)
10. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Yang Sudah Disiapkan:
- ✅ Domain sudah ada (contoh: `api.jalanbareng.id`)
- ✅ Source code di GitHub
- ✅ Data di Railway (akan di-export)

### Yang Perlu Dibeli:
- [ ] VPS Biznet Gio NEO Lite (Rp 50.000/bulan)
  - Link: https://portal.biznetgio.com/
  - Paket: **NEO Lite** (1 vCPU, 1GB RAM, 25GB SSD)
  - Lokasi: **Jakarta**
  - OS: **Ubuntu 22.04 LTS**

### Yang Perlu Disiapkan (Gratis):
- [ ] Akun Cloudflare (untuk R2 storage backup)
- [ ] Akun UptimeRobot (untuk monitoring)

---

## Phase 1: Provisioning VPS

### 1.1. Order VPS Biznet Gio

1. Daftar di https://portal.biznetgio.com/
2. Pilih **NEO Lite**:
   - CPU: 1 vCPU
   - RAM: 1 GB
   - Storage: 25 GB SSD
   - Bandwidth: IIX Unlimited
3. Pilih lokasi: **Jakarta**
4. OS: **Ubuntu 22.04 LTS**
5. Selesaikan pembayaran

**Output yang akan didapat:**
```
IP Address: xxx.xxx.xxx.xxx
Username: root
Password: (dikirim via email)
```

### 1.2. First Login & Security Setup

**Login pertama kali:**
```bash
ssh root@xxx.xxx.xxx.xxx
# Masukkan password dari email
```

**Ganti password root:**
```bash
passwd
# Masukkan password baru yang kuat
```

**Update sistem:**
```bash
apt update && apt upgrade -y
```

**Install tools dasar:**
```bash
apt install -y curl wget git unzip ufw fail2ban
```

**Setup firewall:**
```bash
# Allow SSH, HTTP, HTTPS
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
ufw status
```

**Setup Fail2Ban (proteksi brute force):**
```bash
systemctl enable fail2ban
systemctl start fail2ban
```

---

## Phase 2: Install Stack (Automated)

### 2.1. Download & Run Installation Script

**Buat script otomatis untuk install Nginx, PHP 8.2, MySQL 8, Composer:**

```bash
cd /root
curl -o install-stack.sh https://raw.githubusercontent.com/api-jalanbareng-id/scripts/main/install-stack.sh
chmod +x install-stack.sh
./install-stack.sh
```

**Atau copy-paste script ini dan simpan sebagai `install-stack.sh`:**

```bash
#!/bin/bash
# install-stack.sh - Automated LEMP Stack Installation
# For Ubuntu 22.04 LTS

set -e

echo "==================================="
echo "  LEMP Stack Installation Script"
echo "  For Jalan Bareng Backend"
echo "==================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Update system
echo -e "${GREEN}[1/6] Updating system...${NC}"
apt update && apt upgrade -y

# Install Nginx
echo -e "${GREEN}[2/6] Installing Nginx...${NC}"
apt install -y nginx
systemctl enable nginx
systemctl start nginx

# Install PHP 8.2
echo -e "${GREEN}[3/6] Installing PHP 8.2...${NC}"
apt install -y software-properties-common
add-apt-repository -y ppa:ondrej/php
apt update
apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath \
               php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-cli php8.2-redis

# Install MySQL 8
echo -e "${GREEN}[4/6] Installing MySQL 8...${NC}"
apt install -y mysql-server
systemctl enable mysql
systemctl start mysql

# Secure MySQL installation
echo -e "${YELLOW}[4.1/6] Securing MySQL...${NC}"
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'CHANGE_THIS_PASSWORD';"
mysql -e "DELETE FROM mysql.user WHERE User='';"
mysql -e "DROP DATABASE IF EXISTS test;"
mysql -e "FLUSH PRIVILEGES;"

echo -e "${RED}IMPORTANT: MySQL root password set to 'CHANGE_THIS_PASSWORD'${NC}"
echo -e "${RED}Please change it immediately!${NC}"

# Install Composer
echo -e "${GREEN}[5/6] Installing Composer...${NC}"
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js (untuk build assets jika perlu)
echo -e "${GREEN}[6/6] Installing Node.js...${NC}"
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Verify installations
echo ""
echo -e "${GREEN}==================================="
echo "  Installation Complete!"
echo "===================================${NC}"
echo ""
echo "Versions installed:"
echo "- Nginx: $(nginx -v 2>&1 | cut -d'/' -f2)"
echo "- PHP: $(php -v | head -n 1 | cut -d' ' -f2)"
echo "- MySQL: $(mysql --version | cut -d' ' -f3)"
echo "- Composer: $(composer --version | cut -d' ' -f3)"
echo "- Node.js: $(node -v)"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Change MySQL root password"
echo "2. Create database for Laravel"
echo "3. Deploy Laravel application"
echo ""
```

**Jalankan script:**
```bash
chmod +x install-stack.sh
./install-stack.sh
```

**Catat output MySQL password!** (akan diubah nanti)

---

## Phase 3: Deploy Backend

### 3.1. Setup Database

**Login ke MySQL:**
```bash
mysql -u root -p
# Masukkan password MySQL dari install-stack.sh
```

**Buat database & user:**
```sql
-- Ganti password dengan password yang kuat
CREATE DATABASE jalan_bareng CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'jalan_bareng'@'localhost' IDENTIFIED BY 'PASSWORD_DB_YANG_KUAT';
GRANT ALL PRIVILEGES ON jalan_bareng.* TO 'jalan_bareng'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Catat credentials:**
```
DB_HOST=localhost
DB_DATABASE=jalan_bareng
DB_USERNAME=jalan_bareng
DB_PASSWORD=PASSWORD_DB_YANG_KUAT
```

### 3.2. Clone Repository

**Buat user deploy (non-root):**
```bash
adduser deploy
usermod -aG sudo deploy
su - deploy
```

**Clone repo:**
```bash
cd /home/deploy
git clone https://github.com/USERNAME/jalan-bareng.git app
cd app/backend
```

### 3.3. Setup Laravel

**Install dependencies:**
```bash
composer install --optimize-autoloader --no-dev
```

**Setup environment:**
```bash
cp .env.example .env
nano .env
```

**Edit `.env` production:**
```env
APP_NAME="Jalan Bareng"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://api.jalanbareng.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=jalan_bareng
DB_USERNAME=jalan_bareng
DB_PASSWORD=PASSWORD_DB_YANG_KUAT

FRONTEND_URL=https://jalanbareng.id

SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Sanctum
SANCTUM_STATEFUL_DOMAINS=jalanbareng.id,www.jalanbareng.id

# Mail (opsional, setup nanti)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@jalanbareng.id
MAIL_FROM_NAME="${APP_NAME}"
```

**Generate app key:**
```bash
php artisan key:generate
```

**Set permissions:**
```bash
sudo chown -R deploy:www-data /home/deploy/app/backend
sudo chmod -R 775 /home/deploy/app/backend/storage
sudo chmod -R 775 /home/deploy/app/backend/bootstrap/cache
```

**Link storage:**
```bash
php artisan storage:link
```

---

## Phase 4: Migrate Database

### 4.1. Export dari Railway

**Option A: Via Railway CLI (Recommended)**
```bash
# Di komputer lokal
railway login
railway link
railway run mysql -u root -p > railway_backup.sql
```

**Option B: Via Adminer/phpMyAdmin**
1. Buka Railway dashboard → MySQL → Connect
2. Login ke Adminer
3. Export → SQL → Save as file

**Upload backup ke VPS:**
```bash
# Di komputer lokal
scp railway_backup.sql deploy@xxx.xxx.xxx.xxx:/home/deploy/
```

### 4.2. Import ke VPS

**Di VPS:**
```bash
cd /home/deploy
mysql -u jalan_bareng -p jalan_bareng < railway_backup.sql
```

**Verifikasi:**
```bash
mysql -u jalan_bareng -p jalan_bareng -e "SHOW TABLES;"
```

### 4.3. Run Migrations (Jika Ada Update)

```bash
cd /home/deploy/app/backend
php artisan migrate --force
```

**Seed categories & activation codes (one-time):**
```bash
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=ActivationSeeder --force
```

---

## Phase 5: Setup SSL & Domain

### 5.1. Configure Nginx

**Buat config Nginx:**
```bash
sudo nano /etc/nginx/sites-available/jalanbareng-api
```

**Paste config ini:**
```nginx
server {
    listen 80;
    server_name api.jalanbareng.id;
    root /home/deploy/app/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Max upload size (untuk foto event/destination)
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Logs
    access_log /var/log/nginx/jalanbareng-api-access.log;
    error_log /var/log/nginx/jalanbareng-api-error.log;
}
```

**Enable site:**
```bash
sudo ln -s /etc/nginx/sites-available/jalanbareng-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 5.2. Point Domain ke VPS

**Di DNS provider (Cloudflare/Niagahoster/dll):**

Tambah A Record:
```
Type: A
Name: api
Value: xxx.xxx.xxx.xxx (IP VPS)
TTL: Auto / 300
Proxy: Off (jika pakai Cloudflare)
```

**Tunggu propagasi DNS (5-30 menit).**

**Cek DNS sudah propagate:**
```bash
ping api.jalanbareng.id
# Harus resolve ke IP VPS
```

### 5.3. Install SSL Certificate (Let's Encrypt)

**Install Certbot:**
```bash
sudo apt install -y certbot python3-certbot-nginx
```

**Generate SSL:**
```bash
sudo certbot --nginx -d api.jalanbareng.id
```

**Ikuti prompt:**
- Email: (masukkan email kamu)
- Terms: Yes
- Share email: No (optional)
- Redirect HTTP to HTTPS: Yes (pilih 2)

**Certbot akan otomatis:**
- Generate SSL certificate
- Update config Nginx
- Setup auto-renewal

**Test renewal:**
```bash
sudo certbot renew --dry-run
```

**Sekarang API bisa diakses via HTTPS:**
```
https://api.jalanbareng.id/api/health
```

---

## Phase 6: Auto-Backup System

### 6.1. Setup Cloudflare R2 (Free Storage)

**Daftar Cloudflare R2:**
1. Login ke https://dash.cloudflare.com/
2. Sidebar → R2 Object Storage
3. Create Bucket → nama: `jalanbareng-backups`
4. Create API Token:
   - Permissions: Read & Write
   - Catat: **Access Key ID** & **Secret Access Key**

### 6.2. Install AWS CLI (untuk R2)

```bash
sudo apt install -y awscli
```

**Configure AWS CLI untuk R2:**
```bash
aws configure
# AWS Access Key ID: (dari R2)
# AWS Secret Access Key: (dari R2)
# Default region name: auto
# Default output format: json
```

**Test connection:**
```bash
aws s3 ls --endpoint-url=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com
```

### 6.3. Create Backup Script

**Buat script backup:**
```bash
sudo nano /home/deploy/backup-mysql.sh
```

**Paste script ini:**
```bash
#!/bin/bash
# backup-mysql.sh - Automated MySQL Backup to Cloudflare R2

set -e

# Configuration
DB_USER="jalan_bareng"
DB_PASS="PASSWORD_DB_YANG_KUAT"
DB_NAME="jalan_bareng"
BACKUP_DIR="/home/deploy/backups"
DATE=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_FILE="$BACKUP_DIR/jalan_bareng_$DATE.sql.gz"
R2_ENDPOINT="https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com"
R2_BUCKET="jalanbareng-backups"
RETENTION_DAYS=30

# Create backup directory
mkdir -p $BACKUP_DIR

# Dump database
echo "[$(date)] Starting backup..."
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_FILE

# Upload to R2
echo "[$(date)] Uploading to Cloudflare R2..."
aws s3 cp $BACKUP_FILE s3://$R2_BUCKET/ --endpoint-url=$R2_ENDPOINT

# Delete local backup (keep space free)
rm -f $BACKUP_FILE

# Delete old backups from R2 (older than RETENTION_DAYS)
echo "[$(date)] Cleaning old backups..."
CUTOFF_DATE=$(date -d "$RETENTION_DAYS days ago" +%Y-%m-%d)
aws s3 ls s3://$R2_BUCKET/ --endpoint-url=$R2_ENDPOINT | while read -r line; do
    FILE_DATE=$(echo $line | awk '{print $1}')
    FILE_NAME=$(echo $line | awk '{print $4}')
    if [[ "$FILE_DATE" < "$CUTOFF_DATE" ]]; then
        echo "Deleting old backup: $FILE_NAME"
        aws s3 rm s3://$R2_BUCKET/$FILE_NAME --endpoint-url=$R2_ENDPOINT
    fi
done

echo "[$(date)] Backup complete: $BACKUP_FILE uploaded to R2"
```

**Set permissions:**
```bash
sudo chmod +x /home/deploy/backup-mysql.sh
sudo chown deploy:deploy /home/deploy/backup-mysql.sh
```

**Edit credentials di script:**
```bash
nano /home/deploy/backup-mysql.sh
# Ubah:
# - DB_PASS
# - R2_ENDPOINT (ganti YOUR_ACCOUNT_ID)
```

**Test manual backup:**
```bash
/home/deploy/backup-mysql.sh
```

**Cek di Cloudflare R2 dashboard → Bucket → harus ada file backup.**

### 6.4. Setup Cron Job (Auto Backup Harian)

**Edit crontab:**
```bash
crontab -e
```

**Tambahkan di akhir file:**
```cron
# Backup database setiap hari jam 2 pagi
0 2 * * * /home/deploy/backup-mysql.sh >> /home/deploy/backup.log 2>&1

# Cleanup log file setiap minggu
0 3 * * 0 echo "" > /home/deploy/backup.log
```

**Save & exit.**

**Verifikasi cron:**
```bash
crontab -l
```

**Sekarang backup otomatis jalan setiap hari jam 2 pagi!**

### 6.5. Restore dari Backup (Emergency)

**Download backup dari R2:**
```bash
aws s3 ls s3://jalanbareng-backups/ --endpoint-url=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com
# Pilih file backup yang mau di-restore

aws s3 cp s3://jalanbareng-backups/jalan_bareng_2026-10-10_02-00-00.sql.gz /home/deploy/restore.sql.gz --endpoint-url=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com
```

**Restore ke database:**
```bash
cd /home/deploy
gunzip restore.sql.gz
mysql -u jalan_bareng -p jalan_bareng < restore.sql
```

**Verifikasi:**
```bash
mysql -u jalan_bareng -p jalan_bareng -e "SELECT COUNT(*) FROM events;"
```

---

## Phase 7: Update Frontend

### 7.1. Update Nuxt Config

**Di komputer lokal, edit `web/.env`:**
```env
# Ganti dari Railway URL ke VPS URL
NUXT_PUBLIC_API_BASE_URL=https://api.jalanbareng.id/api
```

**Commit & push:**
```bash
cd web
git add .env
git commit -m "chore: update API URL to VPS"
git push origin main
```

**Vercel akan auto-deploy (~2 menit).**

### 7.2. Test End-to-End

**Buka browser:**
```
https://jalanbareng.id
```

**Test fitur:**
- [ ] Login/Register
- [ ] Browse events
- [ ] Browse destinations
- [ ] Browse cerita
- [ ] Like & comment
- [ ] Upload foto
- [ ] GPS tracking (jika sudah ada)

**Cek Network tab:**
- Semua API call ke `https://api.jalanbareng.id`
- Response time < 500ms (untuk user Indonesia)

---

## Phase 8: Monitoring & Maintenance

### 8.1. Setup UptimeRobot (Free Monitoring)

**Daftar di https://uptimerobot.com/ (gratis):**

**Add New Monitor:**
- Monitor Type: HTTP(s)
- Friendly Name: Jalan Bareng API
- URL: `https://api.jalanbareng.id/api/health`
- Monitoring Interval: 5 minutes (free tier)
- Alert Contacts: (email kamu)

**Add Monitor untuk Frontend:**
- URL: `https://jalanbareng.id`

**Sekarang kamu akan dapat email jika site down!**

### 8.2. Setup Log Rotation

**Nginx logs bisa besar, setup rotation:**
```bash
sudo nano /etc/logrotate.d/nginx
```

**Edit:**
```
/var/log/nginx/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data adm
    sharedscripts
    prerotate
        if [ -d /etc/logrotate.d/httpd-prerotate ]; then \
            run-parts /etc/logrotate.d/httpd-prerotate; \
        fi
    endscript
    postrotate
        invoke-rc.d nginx rotate >/dev/null 2>&1
    endscript
}
```

**Laravel logs:**
```bash
sudo nano /etc/logrotate.d/laravel
```

**Paste:**
```
/home/deploy/app/backend/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 deploy deploy
    su deploy deploy
}
```

### 8.3. Performance Monitoring

**Install htop:**
```bash
sudo apt install -y htop
```

**Monitor resource usage:**
```bash
htop
# CPU, RAM, processes
```

**Monitor disk space:**
```bash
df -h
```

**Monitor MySQL performance:**
```bash
mysql -u root -p -e "SHOW PROCESSLIST;"
mysql -u root -p -e "SHOW STATUS LIKE 'Threads%';"
```

### 8.4. Maintenance Commands

**Update Laravel:**
```bash
cd /home/deploy/app/backend
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
sudo systemctl reload php8.2-fpm
```

**Clear cache:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Check disk space:**
```bash
cd /home/deploy/app/backend
du -sh storage/logs/
du -sh storage/app/public/
```

**Manual backup (before major update):**
```bash
/home/deploy/backup-mysql.sh
```

---

## Troubleshooting

### Issue: 502 Bad Gateway

**Penyebab:** PHP-FPM mati atau config salah.

**Solusi:**
```bash
sudo systemctl status php8.2-fpm
sudo systemctl restart php8.2-fpm
sudo nginx -t
sudo systemctl reload nginx
```

### Issue: 500 Internal Server Error

**Cek Laravel logs:**
```bash
tail -n 50 /home/deploy/app/backend/storage/logs/laravel.log
```

**Cek permissions:**
```bash
sudo chown -R deploy:www-data /home/deploy/app/backend
sudo chmod -R 775 /home/deploy/app/backend/storage
```

### Issue: Database Connection Error

**Test koneksi:**
```bash
mysql -u jalan_bareng -p jalan_bareng -e "SELECT 1;"
```

**Cek `.env` Laravel:**
```bash
cat /home/deploy/app/backend/.env | grep DB_
```

**Restart MySQL:**
```bash
sudo systemctl restart mysql
```

### Issue: SSL Certificate Expired

**Renew manual:**
```bash
sudo certbot renew
sudo systemctl reload nginx
```

### Issue: Backup Script Gagal

**Cek log:**
```bash
cat /home/deploy/backup.log
```

**Test manual:**
```bash
/home/deploy/backup-mysql.sh
```

**Cek AWS CLI config:**
```bash
aws configure list
aws s3 ls --endpoint-url=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com
```

### Issue: Out of Disk Space

**Cek space:**
```bash
df -h
```

**Cleanup old logs:**
```bash
sudo truncate -s 0 /var/log/nginx/*.log
cd /home/deploy/app/backend
rm -rf storage/logs/*.log
```

**Cleanup old backups (local):**
```bash
rm -rf /home/deploy/backups/*
```

### Issue: High CPU Usage

**Cek process:**
```bash
htop
# Lihat process mana yang makan CPU
```

**Optimize Laravel:**
```bash
cd /home/deploy/app/backend
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**Restart PHP-FPM:**
```bash
sudo systemctl restart php8.2-fpm
```

---

## Checklist Selesai

Setelah semua phase selesai, cek checklist ini:

### Infrastructure
- [ ] VPS Biznet Gio running (uptime > 99%)
- [ ] Firewall aktif (SSH, HTTP, HTTPS only)
- [ ] Fail2Ban aktif (proteksi brute force)
- [ ] Domain pointing ke VPS (DNS resolved)
- [ ] SSL certificate installed (HTTPS working)

### Backend
- [ ] Laravel deployed di `/home/deploy/app/backend`
- [ ] Database MySQL running
- [ ] All API endpoints working
- [ ] Storage linked (`php artisan storage:link`)
- [ ] Cron job setup (untuk queue, scheduler, dll)

### Backup System
- [ ] Cloudflare R2 bucket created
- [ ] Backup script tested (`/home/deploy/backup-mysql.sh`)
- [ ] Cron job running (setiap hari jam 2 pagi)
- [ ] Restore tested (download + import berhasil)
- [ ] Retention policy aktif (30 hari)

### Frontend
- [ ] Nuxt config updated (API URL ke VPS)
- [ ] Deployed di Vercel (auto-deploy aktif)
- [ ] CORS working (frontend bisa call API)
- [ ] All features tested

### Monitoring
- [ ] UptimeRobot monitoring aktif
- [ ] Email alerts configured
- [ ] Log rotation setup
- [ ] Performance monitoring checked (htop, df -h)

---

## Summary

**Arsitektur Final:**
```
Frontend (Nuxt)      → Vercel (Gratis)
Backend API (Laravel) → VPS Biznet Gio (Rp 50k/bulan)
Database Backup      → Cloudflare R2 (Gratis)
Monitoring           → UptimeRobot (Gratis)
SSL Certificate      → Let's Encrypt (Gratis)
```

**Total Biaya: Rp 50.000/bulan**

**Benefits:**
- ✅ Auto-backup harian (30 hari retention)
- ✅ Full control & dedicated resources
- ✅ HTTPS/SSL gratis
- ✅ Uptime monitoring
- ✅ Performance stabil
- ✅ Tidak ada data loss lagi!

**Next Steps (Opsional):**
- Setup Redis untuk caching (performance boost)
- Setup Queue worker untuk background jobs
- Setup CI/CD auto-deployment (GitHub Actions → VPS)
- Optimize images (ImageKit/Cloudinary)
- Setup CDN (Cloudflare proxy)

---

## Support

**Dokumentasi ini dibuat untuk:**
- User yang melakukan migrasi manual
- Agent lain yang mengambil alih project
- Future maintenance & troubleshooting

**Kontak:**
- GitHub Issues: https://github.com/USERNAME/jalan-bareng/issues
- Email: (masukkan email support)

**Last Updated:** Oktober 2026
