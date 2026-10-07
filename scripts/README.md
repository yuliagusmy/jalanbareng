# 🛠️ Scripts untuk Migrasi & Deployment VPS

Script otomatis untuk setup dan maintenance Jalan Bareng di VPS.

---

## 📁 Daftar Script

### 1. `install-stack.sh` - Install LEMP Stack

**Fungsi:** Install Nginx, PHP 8.2, MySQL 8, Composer, Node.js di Ubuntu 22.04

**Cara pakai:**
```bash
# Upload script ke VPS
scp install-stack.sh root@xxx.xxx.xxx.xxx:/root/

# SSH ke VPS
ssh root@xxx.xxx.xxx.xxx

# Jalankan script
chmod +x install-stack.sh
sudo ./install-stack.sh
```

**Output:**
- ✅ Nginx installed & running
- ✅ PHP 8.2-FPM installed & optimized
- ✅ MySQL 8 installed & secured
- ✅ Composer installed
- ✅ Node.js 20 LTS installed
- ✅ Firewall configured (SSH, HTTP, HTTPS)
- ✅ Fail2Ban enabled
- ✅ MySQL root password saved to `/root/.mysql_root_password`

**Waktu: ~10 menit**

---

### 2. `backup-mysql.sh` - Auto Backup Database

**Fungsi:** Backup MySQL database ke Cloudflare R2, dengan auto-cleanup backup lama

**Setup:**

1. Edit konfigurasi di script:
```bash
nano backup-mysql.sh
```

2. Ubah nilai ini:
```bash
DB_USER="jalan_bareng"
DB_PASS="PASSWORD_DATABASE_ANDA"
DB_NAME="jalan_bareng"
R2_ENDPOINT="https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com"
R2_BUCKET="jalanbareng-backups"
```

3. Setup AWS CLI untuk R2:
```bash
aws configure
# Access Key: (dari Cloudflare R2)
# Secret Key: (dari Cloudflare R2)
# Region: auto
# Output: json
```

4. Test manual:
```bash
chmod +x backup-mysql.sh
./backup-mysql.sh
```

5. Setup cron job (auto backup harian):
```bash
crontab -e
# Tambahkan:
0 2 * * * /home/deploy/backup-mysql.sh >> /home/deploy/backup.log 2>&1
```

**Features:**
- ✅ Dump database dengan gzip compression
- ✅ Upload ke Cloudflare R2
- ✅ Auto-delete backup lokal (save disk space)
- ✅ Auto-cleanup backup lama (> 30 hari)
- ✅ Logging lengkap

**Waktu: ~2-5 menit** (tergantung ukuran database)

---

### 3. `restore-backup.sh` - Restore dari Backup

**Fungsi:** Restore database dari backup di Cloudflare R2 (interactive)

**Setup:**

1. Edit konfigurasi di script:
```bash
nano restore-backup.sh
```

2. Ubah nilai ini:
```bash
DB_USER="jalan_bareng"
DB_PASS="PASSWORD_DATABASE_ANDA"
DB_NAME="jalan_bareng"
R2_ENDPOINT="https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com"
R2_BUCKET="jalanbareng-backups"
```

3. Jalankan:
```bash
chmod +x restore-backup.sh
./restore-backup.sh
```

4. Pilih backup yang mau di-restore
5. Confirm restore
6. Script akan otomatis:
   - Backup database saat ini (sebelum restore)
   - Download backup dari R2
   - Decompress & import ke MySQL
   - Verifikasi data

**Features:**
- ✅ List semua backup yang available
- ✅ Auto-backup database sebelum restore
- ✅ Interactive (pilih backup mana yang mau di-restore)
- ✅ Verifikasi data setelah restore
- ✅ Safety checks

**Waktu: ~5-10 menit** (tergantung ukuran database)

---

### 4. `deploy-laravel.sh` - Deploy/Update Laravel

**Fungsi:** Deploy atau update Laravel application di VPS

**Setup:**

1. Edit konfigurasi di script:
```bash
nano deploy-laravel.sh
```

2. Ubah nilai ini:
```bash
GIT_REPO="https://github.com/USERNAME/jalan-bareng.git"
GIT_BRANCH="main"
```

3. Jalankan (sebagai user `deploy`):
```bash
su - deploy
chmod +x deploy-laravel.sh
./deploy-laravel.sh
```

**Features:**
- ✅ Clone repository (first time) atau pull latest changes
- ✅ Install/update Composer dependencies
- ✅ Run migrations (optional)
- ✅ Link storage
- ✅ Set permissions
- ✅ Clear & cache config/routes/views
- ✅ Restart PHP-FPM

**Cara pakai untuk update:**
```bash
# Push changes ke GitHub
git push origin main

# SSH ke VPS
ssh deploy@xxx.xxx.xxx.xxx

# Run deployment
./deploy-laravel.sh
```

**Waktu: ~3-5 menit**

---

## 🔄 Workflow Lengkap

### First Time Setup (Fresh VPS)

```bash
# 1. Install stack (as root)
ssh root@xxx.xxx.xxx.xxx
./install-stack.sh

# 2. Setup database
mysql -u root -p
CREATE DATABASE jalan_bareng CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'jalan_bareng'@'localhost' IDENTIFIED BY 'PASSWORD';
GRANT ALL PRIVILEGES ON jalan_bareng.* TO 'jalan_bareng'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# 3. Deploy Laravel (as deploy user)
su - deploy
./deploy-laravel.sh

# 4. Setup backup script
nano backup-mysql.sh  # Edit configuration
chmod +x backup-mysql.sh
./backup-mysql.sh  # Test manual backup

# 5. Setup cron for auto-backup
crontab -e
# Add: 0 2 * * * /home/deploy/backup-mysql.sh >> /home/deploy/backup.log 2>&1
```

### Update Application

```bash
# Local
git push origin main

# VPS
ssh deploy@xxx.xxx.xxx.xxx
./deploy-laravel.sh
```

### Restore from Backup (Emergency)

```bash
ssh deploy@xxx.xxx.xxx.xxx
./restore-backup.sh
# Follow interactive prompts
```

---

## 📝 Checklist

### Setelah menjalankan `install-stack.sh`:
- [ ] MySQL root password tersimpan di `/root/.mysql_root_password`
- [ ] Nginx running: `systemctl status nginx`
- [ ] PHP-FPM running: `systemctl status php8.2-fpm`
- [ ] MySQL running: `systemctl status mysql`
- [ ] Firewall active: `ufw status`
- [ ] User `deploy` created: `id deploy`

### Setelah menjalankan `deploy-laravel.sh`:
- [ ] `.env` configured dengan database credentials
- [ ] `APP_KEY` generated
- [ ] Migrations run (jika ada)
- [ ] Storage linked
- [ ] Permissions set (775 storage, 775 bootstrap/cache)
- [ ] Config cached
- [ ] Health check OK: `curl https://api.jalanbareng.id/api/health`

### Setelah setup `backup-mysql.sh`:
- [ ] AWS CLI configured untuk Cloudflare R2
- [ ] Manual backup tested (file muncul di R2)
- [ ] Cron job setup (`crontab -l`)
- [ ] Log file exists: `tail /home/deploy/backup.log`

### Setelah setup `restore-backup.sh`:
- [ ] Configuration edited (DB credentials, R2 endpoint)
- [ ] Test restore (di non-production dulu!)
- [ ] Pre-restore backup berfungsi

---

## 🐛 Troubleshooting

### `install-stack.sh` gagal

**Problem:** Package not found
```bash
apt update
apt upgrade -y
```

**Problem:** MySQL tidak start
```bash
systemctl status mysql
journalctl -xe
```

### `backup-mysql.sh` gagal

**Problem:** Cannot connect to database
```bash
# Test koneksi
mysql -u jalan_bareng -p jalan_bareng -e "SELECT 1;"
```

**Problem:** AWS CLI cannot upload to R2
```bash
# Test AWS CLI config
aws configure list
aws s3 ls --endpoint-url=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com

# Re-configure if needed
aws configure
```

### `restore-backup.sh` gagal

**Problem:** No backups found
```bash
# Check R2 bucket
aws s3 ls s3://jalanbareng-backups/ --endpoint-url=https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com
```

**Problem:** Import failed
```bash
# Check MySQL connection
mysql -u jalan_bareng -p jalan_bareng -e "SHOW TABLES;"

# Check disk space
df -h
```

### `deploy-laravel.sh` gagal

**Problem:** Git pull failed
```bash
cd /home/deploy/app/backend
git status
git stash
git pull origin main
```

**Problem:** Composer install failed
```bash
# Check PHP version
php -v

# Check Composer
composer --version

# Manually install
cd /home/deploy/app/backend
composer install --optimize-autoloader --no-dev
```

**Problem:** Permissions issue
```bash
sudo chown -R deploy:www-data /home/deploy/app/backend
sudo chmod -R 775 /home/deploy/app/backend/storage
sudo chmod -R 775 /home/deploy/app/backend/bootstrap/cache
```

---

## 📚 Resources

- **Full Migration Guide:** `../MIGRATION-VPS.md`
- **Laravel Deployment Docs:** https://laravel.com/docs/11.x/deployment
- **Nginx Config:** https://laravel.com/docs/11.x/deployment#nginx
- **Cloudflare R2 Docs:** https://developers.cloudflare.com/r2/
- **Certbot (SSL):** https://certbot.eff.org/

---

## 🆘 Support

Jika ada masalah:
1. Cek log file: `tail -n 50 /home/deploy/backup.log`
2. Cek Laravel log: `tail -n 50 /home/deploy/app/backend/storage/logs/laravel.log`
3. Cek Nginx error: `tail -n 50 /var/log/nginx/jalanbareng-api-error.log`
4. Cek systemctl: `systemctl status nginx php8.2-fpm mysql`

Dokumentasi dibuat untuk agent lain atau developer yang mengambil alih project ini.
