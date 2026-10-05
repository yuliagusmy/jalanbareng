# 📘 Panduan Lengkap Migrasi Backend ke VPS / Hosting Berbayar
## Platform Jalan Bareng (Production-Grade Architecture)

> Dokumen ini adalah panduan teknis *step-by-step* untuk memindahkan backend Laravel dari Railway (free-tier / ephemeral SQLite) ke VPS Linux berbayar (misal: **Niagahoster Cloud VPS, Biznet GIO, IDCloudHost, DigitalOcean, Hetzner, atau AWS Lightsail**).

---

## 1. Rekomendasi Spesifikasi Server & Provider

Untuk menangani ribuan pengguna, upload gambar, antrean notifikasi, dan rute peta interaktif dengan lancar:

| Komponen | Spesifikasi Minimum | Rekomendasi Ideal |
|---|---|---|
| **OS** | Ubuntu 24.04 LTS (x86_64) | Ubuntu 24.04 LTS (x86_64) |
| **vCPU** | 2 Core | 2 - 4 Core |
| **RAM** | 2 GB (+ 2GB Swap) | 4 GB |
| **Storage** | 30 GB SSD / NVMe | 50 GB NVMe |
| **Lokasi Server** | Jakarta, Indonesia (untuk latency rendah < 20ms) | Jakarta, Indonesia / Singapura |
| **Pilihan Provider** | Niagahoster VPS, IDCloudHost, Biznet GIO | DigitalOcean (SGP1), Hetzner, AWS Lightsail |

---

## 2. Instalasi Paket di VPS (One-Time Setup)

Jalankan perintah berikut via SSH sebagai user `root` atau `sudo`:

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install dependensi utama
sudo apt install -y software-properties-common curl git unzip ufw fail2ban supervisor nginx

# Tambahkan repository PHP Ondrej
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update

# Install PHP 8.2 dan ekstensi yang dibutuhkan Laravel
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mbstring php8.2-xml php8.2-bcmath \
    php8.2-curl php8.2-gd php8.2-zip php8.2-sqlite3 php8.2-mysql php8.2-redis \
    php8.2-intl php8.2-opcache

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Install MySQL 8 & Redis
sudo apt install -y mysql-server redis-server
sudo systemctl enable mysql redis-server
sudo systemctl start mysql redis-server
```

---

## 3. Setup Database MySQL Production

```bash
# Masuk ke MySQL shell
sudo mysql

# Jalankan query berikut:
CREATE DATABASE jalanbareng CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'jalanbareng_user'@'localhost' IDENTIFIED BY 'PASSWORD_KUAT_ANDA_DISINI!';
GRANT ALL PRIVILEGES ON jalanbareng.* TO 'jalanbareng_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 4. Setup Storage Gambar Persisten (Cloudflare R2 / AWS S3)

Penyimpanan disk lokal pada VPS rentan penuh saat ribuan foto diunggah. Disarankan menggunakan **Cloudflare R2** (Gratis 10 GB pertama, **$0 egress/bandwidth fee**):

1. Buat Bucket di Cloudflare Dashboard: **R2 Object Storage** → Buat bucket: `jalanbareng-media`.
2. Buat API Token R2 dengan izin *Object Read & Write*.
3. Pada backend Laravel, install driver S3:
   ```bash
   composer require league/flysystem-aws-s3-v3
   ```
4. Tambahkan konfigurasi di `.env` VPS:
   ```env
   FILESYSTEM_DISK=r2

   CLOUDFLARE_R2_ACCESS_KEY_ID=your_r2_key_id
   CLOUDFLARE_R2_SECRET_ACCESS_KEY=your_r2_secret_key
   CLOUDFLARE_R2_BUCKET=jalanbareng-media
   CLOUDFLARE_R2_URL=https://media.jalanbareng.web.id
   CLOUDFLARE_R2_ENDPOINT=https://<account-id>.r2.cloudflarestorage.com
   ```

---

## 5. Deployment Source Code ke VPS

```bash
# Buat direktori aplikasi
sudo mkdir -p /var/www/jalanbareng
sudo chown -R $USER:www-data /var/www/jalanbareng

# Clone repository
git clone https://github.com/yuliagusmy/jalanbareng.git /var/www/jalanbareng
cd /var/www/jalanbareng/backend

# Install dependensi PHP tanpa dev packages
composer install --no-dev --optimize-autoloader

# Salin konfigurasi environment
cp .env.example .env

# Generate APP_KEY
php artisan key:generate

# Konfigurasi hak akses folder storage & bootstrap/cache
sudo chown -R www-data:www-data /var/www/jalanbareng/backend/storage /var/www/jalanbareng/backend/bootstrap/cache
sudo chmod -R 775 /var/www/jalanbareng/backend/storage /var/www/jalanbareng/backend/bootstrap/cache

# Jalankan migrasi dan seeder awal
php artisan migrate --force
php artisan db:seed --force

# Optimasi caching config dan route Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 6. Konfigurasi Nginx & SSL HTTPS

Buat file `/etc/nginx/sites-available/api.jalanbareng.web.id`:

```nginx
server {
    listen 80;
    server_name api.jalanbareng.web.id;
    root /var/www/jalanbareng/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    client_max_body_size 25M;

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
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan site dan pasang SSL gratis Let's Encrypt:
```bash
sudo ln -s /etc/nginx/sites-available/api.jalanbareng.web.id /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# Pasang Certbot & generate SSL
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d api.jalanbareng.web.id
```

---

## 7. Supervisor Worker (Background Job & Antrean)

Buat file `/etc/supervisor/conf.d/jalanbareng-worker.conf`:

```ini
[program:jalanbareng-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/jalanbareng/backend/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/jalanbareng/backend/storage/logs/worker.log
stopwaitsecs=3600
```

Aktifkan Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

---

## 8. Cron Scheduler & Auto-Backup Database

Buka crontab:
```bash
sudo crontab -e
```

Tambahkan baris berikut di baris paling bawah:
```cron
# Laravel Task Scheduler (berjalan tiap menit)
* * * * * cd /var/www/jalanbareng/backend && php artisan schedule:run >> /dev/null 2>&1

# Auto-Backup Database MySQL setiap hari pukul 02:00 pagi
0 2 * * * mysqldump -u jalanbareng_user -p'PASSWORD_KUAT' jalanbareng | gzip > /var/backups/db_jalanbareng_$(date +\%F).sql.gz
```

---

## 9. Konfigurasi DNS di IDwebhost / Cloudflare

1. Buat **A Record**:
   - **Host / Name**: `api`
   - **Type**: `A`
   - **Target / Value**: `<IP_PUBLIK_VPS_ANDA>`
   - **TTL**: Auto / 3600
2. Di **Google Cloud Console**:
   - Tambahkan URI: `https://api.jalanbareng.web.id/api/auth/google/callback`
3. Di **Vercel Dashboard (Frontend)**:
   - Ubah `NUXT_PUBLIC_API_BASE` = `https://api.jalanbareng.web.id`
   - Ubah `NUXT_PUBLIC_API_URL` = `https://api.jalanbareng.web.id/api`
   - Redeploy frontend di Vercel.

---

*Dengan panduan ini, saat Anda siap beralih ke hosting berbayar, proses migrasi dapat diselesaikan secara rapi, minim downtime, dan langsung berstatus production-ready!*
