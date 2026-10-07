#!/bin/bash
# restore-backup.sh - Restore MySQL database from Cloudflare R2 backup
# For Jalan Bareng Production Database

set -e

# ============================================
# CONFIGURATION - EDIT THESE VALUES
# ============================================

# Database credentials
DB_USER="jalan_bareng"
DB_PASS="CHANGE_THIS_PASSWORD"
DB_NAME="jalan_bareng"

# Cloudflare R2 configuration
R2_ENDPOINT="https://YOUR_ACCOUNT_ID.r2.cloudflarestorage.com"
R2_BUCKET="jalanbareng-backups"

# Temporary directory for restore
RESTORE_DIR="/home/deploy/restore"

# ============================================
# DO NOT EDIT BELOW THIS LINE
# ============================================

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}======================================${NC}"
echo -e "${BLUE}  Jalan Bareng - Database Restore${NC}"
echo -e "${BLUE}======================================${NC}"
echo ""

# Check if required commands exist
command -v mysql >/dev/null 2>&1 || { echo -e "${RED}Error: mysql is not installed.${NC}"; exit 1; }
command -v gunzip >/dev/null 2>&1 || { echo -e "${RED}Error: gunzip is not installed.${NC}"; exit 1; }
command -v aws >/dev/null 2>&1 || { echo -e "${RED}Error: AWS CLI is not installed.${NC}"; exit 1; }

# Check if configuration is updated
if [ "$DB_PASS" = "CHANGE_THIS_PASSWORD" ]; then
    echo -e "${RED}Error: Database password not configured. Please edit this script.${NC}"
    exit 1
fi

if [[ "$R2_ENDPOINT" == *"YOUR_ACCOUNT_ID"* ]]; then
    echo -e "${RED}Error: Cloudflare R2 endpoint not configured. Please edit this script.${NC}"
    exit 1
fi

# Create restore directory
mkdir -p "$RESTORE_DIR"

# List available backups
echo -e "${YELLOW}Fetching available backups from R2...${NC}"
echo ""

BACKUPS=$(aws s3 ls "s3://$R2_BUCKET/" --endpoint-url="$R2_ENDPOINT" | grep "jalan_bareng_" | awk '{print $4}')

if [ -z "$BACKUPS" ]; then
    echo -e "${RED}Error: No backups found in R2 bucket.${NC}"
    exit 1
fi

# Display backups with numbers
echo -e "${GREEN}Available backups:${NC}"
echo ""
i=1
declare -a BACKUP_ARRAY
while IFS= read -r backup; do
    BACKUP_ARRAY[$i]=$backup
    FILE_SIZE=$(aws s3 ls "s3://$R2_BUCKET/$backup" --endpoint-url="$R2_ENDPOINT" | awk '{print $3}')
    FILE_SIZE_MB=$(echo "scale=2; $FILE_SIZE / 1024 / 1024" | bc)
    
    # Extract date from filename: jalan_bareng_2026-10-07_02-00-00.sql.gz
    BACKUP_DATE=$(echo "$backup" | sed 's/jalan_bareng_//' | sed 's/.sql.gz//' | tr '_' ' ')
    
    echo -e "${BLUE}[$i]${NC} $backup"
    echo "    Date: $BACKUP_DATE"
    echo "    Size: ${FILE_SIZE_MB} MB"
    echo ""
    i=$((i + 1))
done <<< "$BACKUPS"

# Prompt user to select backup
echo -e "${YELLOW}Enter the number of the backup to restore (or 'q' to quit):${NC}"
read -r SELECTION

if [ "$SELECTION" = "q" ] || [ "$SELECTION" = "Q" ]; then
    echo "Restore cancelled."
    exit 0
fi

# Validate selection
if ! [[ "$SELECTION" =~ ^[0-9]+$ ]] || [ "$SELECTION" -lt 1 ] || [ "$SELECTION" -ge "$i" ]; then
    echo -e "${RED}Error: Invalid selection.${NC}"
    exit 1
fi

SELECTED_BACKUP=${BACKUP_ARRAY[$SELECTION]}

echo ""
echo -e "${YELLOW}You selected: $SELECTED_BACKUP${NC}"
echo ""
echo -e "${RED}WARNING: This will REPLACE all data in database '$DB_NAME'${NC}"
echo -e "${RED}         Current data will be LOST!${NC}"
echo ""
echo -e "${YELLOW}Do you want to create a backup of current database before restore? (y/n)${NC}"
read -r CREATE_BACKUP

if [ "$CREATE_BACKUP" = "y" ] || [ "$CREATE_BACKUP" = "Y" ]; then
    echo ""
    echo -e "${GREEN}Creating backup of current database...${NC}"
    CURRENT_BACKUP="$RESTORE_DIR/pre_restore_backup_$(date +%Y%m%d_%H%M%S).sql.gz"
    if mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$CURRENT_BACKUP"; then
        echo -e "${GREEN}Current database backed up to: $CURRENT_BACKUP${NC}"
    else
        echo -e "${RED}Failed to backup current database. Aborting restore.${NC}"
        exit 1
    fi
fi

echo ""
echo -e "${YELLOW}Type 'YES' (all caps) to confirm restore:${NC}"
read -r CONFIRM

if [ "$CONFIRM" != "YES" ]; then
    echo "Restore cancelled."
    exit 0
fi

# Download backup from R2
echo ""
echo -e "${GREEN}Downloading backup from R2...${NC}"
DOWNLOAD_FILE="$RESTORE_DIR/$SELECTED_BACKUP"

if aws s3 cp "s3://$R2_BUCKET/$SELECTED_BACKUP" "$DOWNLOAD_FILE" --endpoint-url="$R2_ENDPOINT"; then
    echo -e "${GREEN}Backup downloaded successfully.${NC}"
else
    echo -e "${RED}Failed to download backup from R2.${NC}"
    exit 1
fi

# Decompress backup
echo ""
echo -e "${GREEN}Decompressing backup...${NC}"
DECOMPRESSED_FILE="${DOWNLOAD_FILE%.gz}"

if gunzip -c "$DOWNLOAD_FILE" > "$DECOMPRESSED_FILE"; then
    echo -e "${GREEN}Backup decompressed successfully.${NC}"
else
    echo -e "${RED}Failed to decompress backup.${NC}"
    exit 1
fi

# Check database connection
echo ""
echo -e "${GREEN}Testing database connection...${NC}"
if ! mysql -u "$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME;" 2>/dev/null; then
    echo -e "${RED}Cannot connect to database. Check credentials.${NC}"
    exit 1
fi
echo -e "${GREEN}Database connection OK.${NC}"

# Get current table count (for comparison)
TABLES_BEFORE=$(mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT COUNT(*) FROM information_schema.TABLES WHERE table_schema = '$DB_NAME';" -s -N)

# Restore database
echo ""
echo -e "${GREEN}Restoring database...${NC}"
echo -e "${YELLOW}This may take a few minutes depending on database size...${NC}"

if mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$DECOMPRESSED_FILE"; then
    echo -e "${GREEN}Database restored successfully!${NC}"
else
    echo -e "${RED}Failed to restore database.${NC}"
    echo -e "${YELLOW}Your pre-restore backup is at: $CURRENT_BACKUP${NC}"
    exit 1
fi

# Get table count after restore
TABLES_AFTER=$(mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT COUNT(*) FROM information_schema.TABLES WHERE table_schema = '$DB_NAME';" -s -N)

# Get record counts from main tables
echo ""
echo -e "${GREEN}Restore verification:${NC}"
echo "Tables before: $TABLES_BEFORE"
echo "Tables after: $TABLES_AFTER"
echo ""

mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
SELECT 
    'users' as table_name, COUNT(*) as record_count FROM users
UNION ALL
SELECT 
    'events', COUNT(*) FROM events
UNION ALL
SELECT 
    'destinations', COUNT(*) FROM destinations
UNION ALL
SELECT 
    'stories', COUNT(*) FROM stories
UNION ALL
SELECT 
    'activations', COUNT(*) FROM activations
UNION ALL
SELECT 
    'categories', COUNT(*) FROM categories;
" | column -t

# Cleanup temporary files
echo ""
echo -e "${YELLOW}Cleaning up temporary files...${NC}"
rm -f "$DOWNLOAD_FILE"
rm -f "$DECOMPRESSED_FILE"

echo ""
echo -e "${GREEN}======================================${NC}"
echo -e "${GREEN}  Restore completed successfully!${NC}"
echo -e "${GREEN}======================================${NC}"
echo ""

if [ -n "$CURRENT_BACKUP" ]; then
    echo -e "${YELLOW}Pre-restore backup saved at:${NC}"
    echo "$CURRENT_BACKUP"
    echo ""
    echo -e "${YELLOW}You can delete it after verifying the restore:${NC}"
    echo "rm $CURRENT_BACKUP"
    echo ""
fi

echo -e "${BLUE}Next steps:${NC}"
echo "1. Test your application to verify the restore"
echo "2. Clear Laravel cache: php artisan cache:clear"
echo "3. Clear Laravel config: php artisan config:clear"
echo ""

exit 0
