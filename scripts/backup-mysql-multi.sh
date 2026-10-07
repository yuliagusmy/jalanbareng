#!/bin/bash
# backup-mysql-multi.sh - Multi-location backup (R2 + Google Drive)
# For Jalan Bareng Production Database

set -e

# ============================================
# CONFIGURATION - EDIT THESE VALUES
# ============================================

# Database credentials
DB_USER="jalan_bareng"
DB_PASS="CHANGE_THIS_PASSWORD"
DB_NAME="jalan_bareng"

# Backup directory (local temporary storage)
BACKUP_DIR="/home/deploy/backups"
DATE=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_FILE="$BACKUP_DIR/jalan_bareng_$DATE.sql.gz"

# Primary: Cloudflare R2
R2_ENDPOINT="https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com"
R2_BUCKET="jalanbareng-backups"

# Secondary: Google Drive (via rclone)
GDRIVE_REMOTE="gdrive:jalan-bareng-backups"

# Retention (how many days to keep backups)
RETENTION_DAYS=30

LOG_FILE="/home/deploy/backup.log"

# ============================================
# DO NOT EDIT BELOW THIS LINE
# ============================================

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" | tee -a "$LOG_FILE"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

# Check if required commands exist
command -v mysqldump >/dev/null 2>&1 || { log_error "mysqldump is not installed. Aborting."; exit 1; }
command -v gzip >/dev/null 2>&1 || { log_error "gzip is not installed. Aborting."; exit 1; }
command -v aws >/dev/null 2>&1 || { log_error "AWS CLI is not installed. Aborting."; exit 1; }

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Check if configuration is updated
if [ "$DB_PASS" = "CHANGE_THIS_PASSWORD" ]; then
    log_error "Database password not configured. Please edit this script."
    exit 1
fi

if [[ "$R2_ENDPOINT" == *"YOUR_ACCOUNT_ID"* ]]; then
    log_error "Cloudflare R2 endpoint not configured. Please edit this script."
    exit 1
fi

# Start backup
log "========================================="
log "Starting multi-location backup..."
log "========================================="

# Check database connection
if ! mysql -u "$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME;" 2>/dev/null; then
    log_error "Cannot connect to database. Check credentials."
    exit 1
fi

# Get database size
DB_SIZE=$(mysql -u "$DB_USER" -p"$DB_PASS" -e "
    SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size_MB'
    FROM information_schema.TABLES
    WHERE table_schema = '$DB_NAME';" -s -N)

log "Database size: ${DB_SIZE} MB"

# Dump database
log "Dumping database to $BACKUP_FILE..."
if mysqldump -u "$DB_USER" -p"$DB_PASS" \
    --single-transaction \
    --quick \
    --lock-tables=false \
    --routines \
    --triggers \
    --events \
    "$DB_NAME" | gzip > "$BACKUP_FILE"; then
    
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    log_success "Database dumped successfully (compressed size: $BACKUP_SIZE)"
else
    log_error "Failed to dump database"
    exit 1
fi

# Upload to Primary (Cloudflare R2)
log "Uploading to Cloudflare R2 (primary storage)..."
R2_SUCCESS=false
if aws s3 cp "$BACKUP_FILE" "s3://$R2_BUCKET/" --endpoint-url="$R2_ENDPOINT" 2>&1 | tee -a "$LOG_FILE"; then
    log_success "✓ Uploaded to Cloudflare R2"
    R2_SUCCESS=true
else
    log_error "✗ Failed to upload to Cloudflare R2 (primary)"
fi

# Upload to Secondary (Google Drive via rclone)
GDRIVE_SUCCESS=false
if command -v rclone >/dev/null 2>&1; then
    log "Uploading to Google Drive (secondary storage)..."
    if rclone copy "$BACKUP_FILE" "$GDRIVE_REMOTE" 2>&1 | tee -a "$LOG_FILE"; then
        log_success "✓ Uploaded to Google Drive"
        GDRIVE_SUCCESS=true
    else
        log_error "✗ Failed to upload to Google Drive (secondary)"
    fi
else
    log_warning "rclone not installed, skipping Google Drive backup"
    log_warning "Install with: curl https://rclone.org/install.sh | sudo bash"
fi

# Check if at least one upload succeeded
if [ "$R2_SUCCESS" = false ] && [ "$GDRIVE_SUCCESS" = false ]; then
    log_error "All backup uploads failed! Keeping local backup."
    log_error "Local backup preserved at: $BACKUP_FILE"
    exit 1
fi

# Delete local backup (save disk space)
log "Removing local backup file..."
rm -f "$BACKUP_FILE"
log "Local backup removed (saved to cloud)"

# Cleanup old backups from R2
if [ "$R2_SUCCESS" = true ]; then
    log "Cleaning old backups from Cloudflare R2..."
    CUTOFF_DATE=$(date -d "$RETENTION_DAYS days ago" +%Y-%m-%d 2>/dev/null || date -v -${RETENTION_DAYS}d +%Y-%m-%d)
    
    DELETED_R2=0
    aws s3 ls "s3://$R2_BUCKET/" --endpoint-url="$R2_ENDPOINT" | while read -r line; do
        FILE_DATE=$(echo "$line" | awk '{print $1}')
        FILE_NAME=$(echo "$line" | awk '{print $4}')
        
        # Skip if not a backup file
        if [[ ! "$FILE_NAME" =~ ^jalan_bareng_.*\.sql\.gz$ ]]; then
            continue
        fi
        
        # Compare dates and delete if older than retention
        if [[ "$FILE_DATE" < "$CUTOFF_DATE" ]]; then
            log "Deleting old backup from R2: $FILE_NAME (date: $FILE_DATE)"
            if aws s3 rm "s3://$R2_BUCKET/$FILE_NAME" --endpoint-url="$R2_ENDPOINT" 2>&1 | tee -a "$LOG_FILE"; then
                DELETED_R2=$((DELETED_R2 + 1))
            fi
        fi
    done
    
    if [ $DELETED_R2 -gt 0 ]; then
        log_success "Deleted $DELETED_R2 old backup(s) from R2"
    fi
fi

# Cleanup old backups from Google Drive
if [ "$GDRIVE_SUCCESS" = true ]; then
    log "Cleaning old backups from Google Drive..."
    if rclone delete "$GDRIVE_REMOTE" --min-age ${RETENTION_DAYS}d 2>&1 | tee -a "$LOG_FILE"; then
        log_success "Old backups cleaned from Google Drive"
    fi
fi

# Final summary
log "========================================="
log_success "Multi-location backup completed!"
log "========================================="
log "Backup locations:"
[ "$R2_SUCCESS" = true ] && log "  ✓ Cloudflare R2: s3://$R2_BUCKET/$(basename $BACKUP_FILE)"
[ "$GDRIVE_SUCCESS" = true ] && log "  ✓ Google Drive: $GDRIVE_REMOTE/$(basename $BACKUP_FILE)"
log "Retention: $RETENTION_DAYS days"
log "========================================="

exit 0
