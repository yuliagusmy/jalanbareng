# Data Loss Prevention — Jalan Bareng

> **Tujuan:** Mencegah kehilangan data saat develop fitur baru atau production incident.
> **Diperbarui:** 7 Oktober 2026

---

## 🔍 Root Cause Analysis: Insiden Railway (Oktober 2026)

### Apa yang Terjadi?
- **Event data hilang** — semua record di tabel `events` terhapus
- **Story tetap ada** — data teks utuh, hanya foto yang hilang (404)
- **Destination tetap ada** — tidak terpengaruh

### Kenapa Event Hilang Tapi Story Tidak?

**Root Cause: Foreign Key CASCADE DELETE**

```php
// ❌ Events Table (SEBELUM FIX)
$table->foreignId('user_id')
      ->constrained('users')
      ->onDelete('cascade');  // DELETE user → AUTO DELETE semua event

// ✅ Stories Table (AMAN)
$table->foreignId('user_id')
      ->nullable()
      ->constrained('users')
      ->nullOnDelete();  // DELETE user → story tetap ada, user_id = NULL
```

**Skenario yang Mungkin Terjadi:**
1. Ada user account di Railway yang dihapus (manual/bug/migration issue)
2. Karena `events.user_id` pakai `onDelete('cascade')`, semua event user tersebut **otomatis ikut terhapus**
3. Stories pakai `nullOnDelete()`, jadi data tetap ada

### Kenapa Foto Story Hilang?

**Root Cause: Storage Path vs File System**

```
Database (stories table):
  └─ cover_image: "stories/foto1.jpg" ✅ PATH ADA

Railway Storage (file system):
  └─ /storage/stories/foto1.jpg ❌ FILE HILANG
      └─> Kemungkinan: ephemeral storage, volume unmount, restart

Result: Database punya path, tapi file fisik hilang → 404 Not Found
```

---

## 🛡️ Solusi: 3-Layer Protection

### **Layer 1: Foreign Key Constraint Fix** ✅ IMPLEMENTED

**Migration:** `2026_10_07_change_cascade_to_restrict.php`

**Perubahan:**

| Tabel | Kolom | SEBELUM | SESUDAH | Dampak |
|-------|-------|---------|---------|--------|
| `events` | `user_id` | `cascade` | `restrict` | ❌ Tidak bisa hapus user jika ada event |
| `destinations` | `user_id` | `cascade` | `restrict` | ❌ Tidak bisa hapus user jika ada destination |
| `destinations` | `category_id` | `cascade` | `set null` | ✅ Hapus kategori, destination tetap ada |
| `comments` | `user_id` | `cascade` | `set null` | ✅ Hapus user, comment tetap ada (anonymous) |

**Keuntungan:**
- Data **tidak akan hilang** karena cascade delete
- Harus manual cleanup/archive dulu sebelum hapus user
- Error message jelas kalau ada data yang masih berelasi

**Cara Aktifkan:**

```bash
# Development (SQLite) - migration auto-skip
php artisan migrate

# Production (MySQL/PostgreSQL) - WAJIB BACKUP DULU
# 1. Backup database
mysqldump -u user -p database > backup_before_fix.sql

# 2. Run migration
php artisan migrate

# 3. Test
# Coba hapus user yang ada event → harusnya error
```

---

### **Layer 2: Soft Deletes** ✅ IMPLEMENTED

**Migration:** `2026_10_07_add_soft_deletes_to_tables.php`

**Tabel yang Punya Soft Delete:**
- `events`
- `destinations`
- `stories`

**Cara Kerja:**
```php
// Soft delete - data tidak benar-benar dihapus
$event->delete();  // Set deleted_at = now()

// Data masih ada di database, cuma "disembunyikan"
Event::withTrashed()->get();  // Lihat semua termasuk yang deleted

// Restore kalau salah hapus
$event->restore();

// Hard delete (permanent) - hanya admin yang bisa
$event->forceDelete();
```

---

### **Layer 3: Audit Trail** ✅ IMPLEMENTED

**Tabel:** `audit_logs`
**Trait:** `Auditable`

**Models yang Diaudit:**
- Event
- Destination
- Story

**Apa yang Dicatat:**
```php
[
  'action' => 'created|updated|deleted',
  'user_id' => 123,
  'model_type' => 'App\Models\Event',
  'model_id' => 456,
  'changes' => ['name' => ['Old Name', 'New Name']],
  'ip_address' => '192.168.1.1',
  'user_agent' => 'Mozilla/5.0...'
]
```

**Keuntungan:**
- Tahu siapa yang hapus/ubah data
- Bisa track history perubahan
- Forensik kalau ada incident

---

## 📋 Development Best Practices

### ✅ DO (Lakukan)

```bash
# 1. Selalu backup sebelum migrate production
mysqldump -u user -p database > backup_$(date +%Y%m%d).sql

# 2. Test migration di lokal dulu (SQLite)
php artisan migrate

# 3. Buat migration baru, JANGAN edit yang lama
php artisan make:migration add_status_to_events_table

# 4. Soft delete, bukan hard delete
$event->delete();  # ✅
$event->forceDelete();  # ❌ Hanya jika benar-benar yakin

# 5. Archive user, jangan delete
$user->is_active = false;
$user->save();
```

### ❌ DON'T (Jangan)

```bash
# 1. JANGAN migrate:fresh di database yang ada data
php artisan migrate:fresh  # ❌ BAHAYA - DROP semua tabel

# 2. JANGAN truncate tabel production
DB::table('events')->truncate();  # ❌ Data hilang semua

# 3. JANGAN edit migration yang sudah di-commit
# Buat migration baru sebagai gantinya

# 4. JANGAN hard delete user yang punya relasi
$user->forceDelete();  # ❌ Bisa cause data loss

# 5. JANGAN bypass foreign key constraint
DB::statement('SET FOREIGN_KEY_CHECKS=0');  # ❌ Berbahaya
```

---

## 🚨 Emergency Recovery Procedure

### Kalau Data Hilang di Production

**Step 1: Stop Deployment (Immediate)**
```bash
# Freeze production - jangan deploy apapun
git tag production-freeze-$(date +%Y%m%d)
```

**Step 2: Assess Damage**
```bash
# Cek tabel mana yang affected
mysql -u user -p database -e "SELECT COUNT(*) FROM events;"
mysql -u user -p database -e "SELECT COUNT(*) FROM stories;"

# Cek audit log untuk forensik
mysql -u user -p database -e "SELECT * FROM audit_logs WHERE action='deleted' ORDER BY created_at DESC LIMIT 50;"
```

**Step 3: Restore dari Backup**
```bash
# Restore dari backup terakhir
mysql -u user -p database < backup_latest.sql

# Atau restore specific table
mysql -u user -p database --one-database events < backup_latest.sql
```

**Step 4: Verify Integrity**
```bash
# Check foreign key constraints
php artisan tinker
>>> Event::with('user')->count();
>>> Destination::with('category')->count();
```

**Step 5: Post-Mortem**
- Tulis laporan: apa yang terjadi, kenapa, solusi
- Update runbook ini kalau ada lesson learned
- Improve monitoring/alerting

---

## 🔧 Monitoring & Alerting

### Metrics yang Harus Dimonitor

```php
// 1. Row count trend - alert kalau drop tiba-tiba
- events: normal ~100-500 records
- destinations: normal ~50-200 records
- stories: normal ~20-100 records

// 2. Soft delete ratio - alert kalau >10% deleted
- deleted records / total records

// 3. Audit log anomaly - alert kalau >50 deletes/hour
- DELETE actions per hour

// 4. Storage usage - alert kalau drop >20% tiba-tiba
- File count di /storage/app/public
```

### Setup Alert (Future - Railway Monitoring)

```javascript
// Webhook alert ke Discord/Slack
if (deletedCount > 50 per hour) {
  notify("⚠️ High delete activity detected!");
}

if (rowCount drop > 20%) {
  notify("🚨 Possible data loss! Check database.");
}
```

---

## 📚 Related Documentation

- **AGENTS.md** — Protocol keamanan database (wajib baca sebelum migrate)
- **DATA-SAFETY-STRATEGY.md** — 6-layer protection system
- **LOCAL-BACKUP-GUIDE.md** — Setup auto-backup lokal
- **MIGRATION-VPS.md** — Setup VPS dengan backup automation

---

## ✅ Checklist Sebelum Deploy Production

```
[ ] Backup database sudah running (auto daily)
[ ] Migration sudah di-test di lokal
[ ] Foreign key constraint fix sudah aktif
[ ] Soft delete sudah enabled di semua model kritis
[ ] Audit trail sudah recording changes
[ ] Monitoring/alerting sudah setup
[ ] Emergency recovery procedure sudah dipahami tim
```

---

## 🤝 Contributions

Dokumen ini akan diupdate setiap ada:
- Incident baru
- Lesson learned
- Best practice baru

**Last Updated:** 7 Oktober 2026 by Kiro AI  
**Next Review:** Setelah VPS migration selesai
