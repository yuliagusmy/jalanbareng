#!/bin/bash
# backup-railway-local.sh - Backup Railway MySQL to Local Computer
# For Linux/Mac/WSL

set -e

echo "================================================"
echo "  Jalan Bareng - Railway Database Backup"
echo "  Backup to Local Computer"
echo "================================================"
echo ""

# Configuration
BACKUP_DIR="$HOME/backups/jalan-bareng"
DATE_TIME=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/railway_jalan_bareng_$DATE_TIME.sql"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Check if Railway CLI is installed
if ! command -v railway &> /dev/null; then
    echo -e "${RED}[ERROR] Railway CLI is not installed!${NC}"
    echo ""
    echo "Install Railway CLI:"
    echo "  npm install -g @railway/cli"
    echo ""
    echo "Or download from: https://railway.app/cli"
    exit 1
fi

# Check if logged in to Railway
echo "Checking Railway authentication..."
if ! railway whoami &> /dev/null; then
    echo -e "${RED}[ERROR] Not logged in to Railway!${NC}"
    echo ""
    echo "Please login first:"
    echo "  railway login"
    echo ""
    exit 1
fi

echo -e "${GREEN}✓ Railway CLI authenticated${NC}"

# Link to project (if not already linked)
echo ""
echo "Linking to Railway project..."
railway link

# Export database
echo ""
echo "Exporting database from Railway..."
echo "This may take a few minutes depending on database size..."
echo ""

if railway run 'mysqldump --no-tablespaces -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD -P $MYSQLPORT $MYSQLDATABASE' > "$BACKUP_FILE"; then
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    
    echo ""
    echo -e "${GREEN}================================================${NC}"
    echo -e "${GREEN}  Backup Completed Successfully!${NC}"
    echo -e "${GREEN}================================================${NC}"
    echo ""
    echo "Backup file: $BACKUP_FILE"
    echo "File size: $BACKUP_SIZE"
    echo ""
    echo "To restore this backup:"
    echo "1. Transfer file to VPS: scp $BACKUP_FILE deploy@vps-ip:/home/deploy/"
    echo "2. Import: mysql -u jalan_bareng -p jalan_bareng < railway_backup.sql"
    echo ""
else
    echo ""
    echo -e "${RED}[ERROR] Backup failed!${NC}"
    echo "Check your Railway connection and database credentials."
    exit 1
fi

# List recent backups
echo "Recent backups:"
ls -lth "$BACKUP_DIR"/railway_*.sql 2>/dev/null | head -5

# Cleanup old backups (keep last 7)
echo ""
echo "Cleaning up old backups (keeping last 7)..."
cd "$BACKUP_DIR"
ls -t railway_*.sql 2>/dev/null | tail -n +8 | xargs -r rm -v

echo ""
echo -e "${GREEN}Done!${NC}"
