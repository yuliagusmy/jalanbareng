#!/bin/bash
# backup-mysql.sh - Automated MySQL Backup to Cloudflare R2
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

# Cloudflare R2 configuration
R2_ENDPOINT="https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com"
R2_BUCKET="jalanbareng-backups"

# Retention (how many days to keep backups)
RETENTION_DAYS=30

# ============================================
# DO NOT EDIT BELOW THIS LINE
# ============================================

# Timestamp for backup file
DATE=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_FILE="$BACKUP_DIR/jalan_bareng_$DATE.sql.gz"
LOG_FILE="/home/deploy/backup.log"

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

# Create backup directory if not exists
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
log "Starting backup process..."

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

# Upload to Cloudflare R2
log "Uploading backup to Cloudflare R2..."
if aws s3 cp "$BACKUP_FILE" "s3://$R2_BUCKET/" --endpoint-url="$R2_ENDPOINT" 2>&1 | tee -a "$LOG_FILE"; then
    log_success "Backup uploaded to R2: s3://$R2_BUCKET/$(basename $BACKUP_FILE)"
else
    log_error "Failed to upload backup to R2"
    # Keep local backup if upload fails
    log_warning "Local backup preserved at: $BACKUP_FILE"
    exit 1
fi

# Delete local backup (save disk space)
log "Removing local backup file..."
rm -f "$BACKUP_FILE"
log_success "Local backup removed"

# Cleanup old backups from R2
log "Cleaning up old backups (retention: $RETENTION_DAYS days)..."

# Calculate cutoff date
CUTOFF_DATE=$(date -d "$RETENTION_DAYS days ago" +%Y-%m-%d 2>/dev/null || date -v -${RETENTION_DAYS}d +%Y-%m-%d)

# List and delete old backups
DELETED_COUNT=0
aws s3 ls "s3://$R2_BUCKET/" --endpoint-url="$R2_ENDPOINT" | while read -r line; do
    # Parse S3 ls output: date time size filename
    FILE_DATE=$(echo "$line" | awk '{print $1}')
    FILE_NAME=$(echo "$line" | awk '{print $4}')
    
    # Skip if not a backup file
    if [[ ! "$FILE_NAME" =~ ^jalan_bareng_.*\.sql\.gz$ ]]; then
        continue
    fi
    
    # Compare dates and delete if older than retention
    if [[ "$FILE_DATE" < "$CUTOFF_DATE" ]]; then
        log "Deleting old backup: $FILE_NAME (date: $FILE_DATE)"
        if aws s3 rm "s3://$R2_BUCKET/$FILE_NAME" --endpoint-url="$R2_ENDPOINT" 2>&1 | tee -a "$LOG_FILE"; then
            DELETED_COUNT=$((DELETED_COUNT + 1))
        else
            log_warning "Failed to delete: $FILE_NAME"
        fi
    fi
done

if [ $DELETED_COUNT -gt 0 ]; then
    log_success "Deleted $DELETED_COUNT old backup(s)"
else
    log "No old backups to delete"
fi

# List remaining backups
log "Current backups in R2:"
aws s3 ls "s3://$R2_BUCKET/" --endpoint-url="$R2_ENDPOINT" | grep "jalan_bareng_" | tee -a "$LOG_FILE"

# Final summary
log_success "Backup process completed successfully!"
log "----------------------------------------"

# Send notification (optional - uncomment if you want email notifications)
# echo "Backup completed: $(basename $BACKUP_FILE)" | mail -s "Jalan Bareng - Backup Success" your-email@example.com

exit 0
