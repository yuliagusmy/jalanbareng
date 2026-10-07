# 💾 Local Backup Guide — Jalan Bareng

> **Tujuan:** Backup database Railway ke komputer lokal kamu secara otomatis
> 
> **Benefit:** Punya backup offline, tidak bergantung cloud, gratis!

---

## 🎯 Quick Start (Windows)

### Step 1: Install Railway CLI (One-time)

**Opsi A: Via npm (Recommended)**
```cmd
npm install -g @railway/cli
```

**Opsi B: Download Installer**
1. Download dari https://railway.app/cli
2. Install seperti software biasa

**Verify installation:**
```cmd
railway --version
```

### Step 2: Login ke Railway (One-time)

```cmd
railway login
```

Browser akan terbuka, login dengan akun Railway kamu, lalu kembali ke terminal.

**Verify login:**
```cmd
railway whoami
```

Harus menampilkan email Railway kamu.

### Step 3: Link ke Project (One-time)

```cmd
cd "d:\Project\jalan-bareng-with db"
railway link
```

Pilih project "jalan-bareng" dari list.

### Step 4: Test Manual Backup

```cmd
cd scripts
backup-railway-local.bat
```

**Output:**
```
================================================
  Jalan Bareng - Railway Database Backup
  Backup to Local Computer
================================================

Exporting database from Railway...
This may take a few minutes...

================================================
  Backup Completed Successfully!
================================================

Backup file: C:\Users\YourName\backups\jalan-bareng\railway_jalan_bareng_20261007_140530.sql
File size: 245760 bytes

Done!
```

**Backup location:**
```
C:\Users\[YourUsername]\backups\jalan-bareng\
```

### Step 5: Setup Automatic Backup (Optional)

**Run PowerShell as Administrator:**
```powershell
cd "d:\Project\jalan-bareng-with db\scripts"
.\setup-windows-backup-schedule.ps1
```

**Pilih schedule:**
```
Choose backup schedule:
1. Daily at 2:00 AM (Recommended)  ← Pilih ini
2. Daily at specific time
3. Weekly (Sunday at 2:00 AM)
4. Custom (you configure manually later)

Enter your choice (1-4): 1
```

**Done!** Sekarang backup otomatis jalan setiap hari jam 2 pagi.

---

## 🐧 Quick Start (Linux/Mac/WSL)

### Step 1: Install Railway CLI

```bash
# Install via npm
npm install -g @railway/cli

# Or via script
bash <(curl -fsSL https://railway.app/install.sh)
```

### Step 2: Login & Link

```bash
railway login
railway link
```

### Step 3: Test Manual Backup

```bash
cd scripts
chmod +x backup-railway-local.sh
./backup-railway-local.sh
```

**Backup location:**
```
~/backups/jalan-bareng/
```

### Step 4: Setup Automatic Backup

```bash
# Edit crontab
crontab -e

# Add this line (daily at 2 AM):
0 2 * * * /path/to/scripts/backup-railway-local.sh >> ~/backups/jalan-bareng/backup.log 2>&1

# Save and exit
```

**Verify cron:**
```bash
crontab -l
```

---

## 📋 Features

### ✅ Auto-cleanup Old Backups
Script otomatis hapus backup lama, **keep last 7 backups** saja.

**Storage usage estimation:**
```
Database size: ~10 MB (sekarang)
Backup size (compressed): ~2 MB per file
7 backups × 2 MB = ~14 MB total

→ Very small! No storage problem.
```

### ✅ Timestamped Filenames
Format: `railway_jalan_bareng_YYYYMMDD_HHMMSS.sql`

Example:
```
railway_jalan_bareng_20261007_020015.sql  ← 7 Oct 2026, 02:00:15
railway_jalan_bareng_20261008_020012.sql  ← 8 Oct 2026, 02:00:12
railway_jalan_bareng_20261009_020018.sql  ← 9 Oct 2026, 02:00:18
...
```

### ✅ Safe & Non-Destructive
- Read-only operation (tidak ubah data di Railway)
- No credentials stored (pakai Railway CLI authentication)
- Works even if Railway down (backup dari server terakhir)

---

## 🔄 Restore dari Local Backup

### Scenario 1: Restore ke Railway (Emergency)

**If Railway database corrupted:**

```bash
# Method 1: Via Railway CLI
railway run 'mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD -P $MYSQLPORT $MYSQLDATABASE' < backup_file.sql

# Method 2: Via Adminer/phpMyAdmin
1. Open Railway → MySQL → Connect
2. Open Adminer
3. Import → Select SQL file → Execute
```

### Scenario 2: Restore ke VPS (Migration)

```bash
# 1. Upload backup to VPS
scp railway_jalan_bareng_20261007_020015.sql deploy@vps-ip:/home/deploy/

# 2. SSH to VPS
ssh deploy@vps-ip

# 3. Import to MySQL
cd /home/deploy
mysql -u jalan_bareng -p jalan_bareng < railway_jalan_bareng_20261007_020015.sql

# 4. Verify
mysql -u jalan_bareng -p jalan_bareng -e "SELECT COUNT(*) FROM events;"
```

### Scenario 3: Inspect Backup Locally (Development)

```bash
# 1. Install MySQL locally (if not installed)
# Windows: Download from https://dev.mysql.com/downloads/installer/
# Mac: brew install mysql
# Linux: sudo apt install mysql-server

# 2. Create local database
mysql -u root -p
CREATE DATABASE jalan_bareng_local;
EXIT;

# 3. Import backup
mysql -u root -p jalan_bareng_local < railway_jalan_bareng_20261007_020015.sql

# 4. Connect Laravel to local DB
# Edit backend/.env:
DB_HOST=localhost
DB_DATABASE=jalan_bareng_local
DB_USERNAME=root
DB_PASSWORD=your_local_password
```

---

## 📊 Monitoring & Maintenance

### Check Backup History

**Windows:**
```cmd
dir /O-D "C:\Users\YourName\backups\jalan-bareng"
```

**Linux/Mac:**
```bash
ls -lth ~/backups/jalan-bareng/
```

### Check Disk Space

**Windows:**
```cmd
dir /s "C:\Users\YourName\backups\jalan-bareng"
```

**Linux/Mac:**
```bash
du -sh ~/backups/jalan-bareng/
```

### Verify Last Backup

**Windows:**
```cmd
# Check if backup ran today
dir /O-D "C:\Users\YourName\backups\jalan-bareng\railway_*.sql" | findstr /C:"%date:~-4,4%%date:~-10,2%%date:~-7,2%"
```

**Linux/Mac:**
```bash
# Check if backup ran today
ls -l ~/backups/jalan-bareng/railway_*.sql | grep $(date +%Y%m%d)
```

### Manual Cleanup (if needed)

```bash
# Delete backups older than 30 days
find ~/backups/jalan-bareng/ -name "railway_*.sql" -mtime +30 -delete
```

---

## 🐛 Troubleshooting

### Error: "Railway CLI not found"

**Solution:**
```bash
# Check if installed
railway --version

# If not installed, install it
npm install -g @railway/cli
```

### Error: "Not logged in to Railway"

**Solution:**
```bash
railway login
```

### Error: "Project not linked"

**Solution:**
```bash
cd "d:\Project\jalan-bareng-with db"
railway link
```

### Error: "mysqldump: command not found"

**Railway runs mysqldump on their server, bukan lokal.**

Check if:
1. Railway CLI authenticated: `railway whoami`
2. Project linked: `railway status`
3. Database service exists: `railway service`

### Error: "Access denied for user"

**Check Railway database credentials:**
```bash
railway variables
```

Should show: `MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLPORT`, `MYSQLDATABASE`

### Backup file is empty or 0 bytes

**Possible causes:**
1. Database is actually empty (check via Railway dashboard)
2. Network timeout (try again)
3. Railway service down (check Railway status page)

**Solution:**
```bash
# Test connection
railway run 'mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD -P $MYSQLPORT -e "SHOW TABLES;"'
```

---

## 🔐 Security Best Practices

### ✅ DO's:
- ✅ Keep backup files in secure location (not shared folder)
- ✅ Encrypt backup files jika berisi data sensitif
- ✅ Test restore monthly
- ✅ Keep Railway CLI updated: `npm update -g @railway/cli`

### ❌ DON'Ts:
- ❌ Jangan commit backup files ke Git (.sql files in .gitignore)
- ❌ Jangan share backup files via public link
- ❌ Jangan simpan di cloud storage publik tanpa encryption

### Encryption (Optional)

**Encrypt backup with password:**

**Windows (7-Zip):**
```cmd
# Encrypt with password
"C:\Program Files\7-Zip\7z.exe" a -p -mhe=on backup.sql.7z backup.sql

# Decrypt
"C:\Program Files\7-Zip\7z.exe" x backup.sql.7z
```

**Linux/Mac:**
```bash
# Encrypt with gpg
gpg -c backup.sql  # Creates backup.sql.gpg

# Decrypt
gpg backup.sql.gpg
```

---

## 📈 Backup Strategy: 3-2-1 Rule

**Sekarang kamu punya:**

1. **Primary:** Railway MySQL (production)
2. **Secondary (cloud):** Cloudflare R2 backup (dari VPS nanti)
3. **Tertiary (local):** Local computer backup ← **YANG INI BARU!**

**3-2-1 Backup Rule:**
- **3** copies of data (Railway + R2 + Local)
- **2** different media types (cloud + local disk)
- **1** off-site (R2 atau Local, tergantung perspektif)

**Data loss risk:** Virtually 0% 🎉

---

## 🎉 Summary

**Setup time:** 10 minutes
**Storage required:** ~14 MB (7 backups)
**Cost:** FREE!

**What you get:**
- ✅ Daily automatic backup to local computer
- ✅ Keep last 7 backups (1 week history)
- ✅ Can restore anytime (even if Railway down)
- ✅ No cloud dependency
- ✅ No cost

**Next steps:**
1. ✅ Setup backup (done after following guide)
2. ⏰ Wait for first automatic backup (tonight at 2 AM)
3. 📧 Check backup folder tomorrow morning
4. 🧪 Test restore once a month

---

## 📞 Support

**If backup fails:**
1. Check Railway CLI: `railway whoami`
2. Check project link: `railway status`
3. Check database: `railway variables | grep MYSQL`
4. Check logs: `C:\Users\YourName\backups\jalan-bareng\backup.log`

**Questions?**
- Railway CLI docs: https://docs.railway.app/cli/quick-start
- GitHub issues: (link to your repo)

---

**Last Updated:** Oktober 2026  
**Status:** Ready to use  
**Tested on:** Windows 11, macOS, Ubuntu 22.04
