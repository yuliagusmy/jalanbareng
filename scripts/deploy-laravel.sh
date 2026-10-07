#!/bin/bash
# deploy-laravel.sh - Automated Laravel deployment script
# For Jalan Bareng Backend on VPS

set -e

# ============================================
# CONFIGURATION
# ============================================

APP_DIR="/home/deploy/app/backend"
GIT_REPO="https://github.com/USERNAME/jalan-bareng.git"
GIT_BRANCH="main"
PHP_VERSION="8.2"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}======================================${NC}"
echo -e "${BLUE}  Jalan Bareng - Deployment Script${NC}"
echo -e "${BLUE}======================================${NC}"
echo ""

# Check if running as deploy user
if [ "$(whoami)" != "deploy" ]; then
    echo -e "${RED}Error: This script must be run as 'deploy' user${NC}"
    echo "Run: su - deploy"
    exit 1
fi

# Check if app directory exists
if [ -d "$APP_DIR" ]; then
    echo -e "${YELLOW}Application directory exists. Updating...${NC}"
    
    cd "$APP_DIR"
    
    # Stash any local changes
    git stash
    
    # Pull latest changes
    echo -e "${GREEN}Pulling latest changes from $GIT_BRANCH...${NC}"
    git pull origin "$GIT_BRANCH"
    
else
    echo -e "${GREEN}Cloning repository...${NC}"
    
    # Clone repository
    mkdir -p /home/deploy
    cd /home/deploy
    git clone "$GIT_REPO" app
    cd app/backend
fi

# Install/update Composer dependencies
echo ""
echo -e "${GREEN}Installing Composer dependencies...${NC}"
composer install --optimize-autoloader --no-dev --no-interaction

# Check if .env exists
if [ ! -f ".env" ]; then
    echo ""
    echo -e "${YELLOW}.env file not found. Creating from .env.example...${NC}"
    cp .env.example .env
    
    echo -e "${RED}IMPORTANT: Please edit .env file with your database credentials${NC}"
    echo "nano $APP_DIR/.env"
    echo ""
    echo "Then run this script again."
    exit 1
fi

# Generate app key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo ""
    echo -e "${GREEN}Generating application key...${NC}"
    php artisan key:generate --force
fi

# Run migrations
echo ""
echo -e "${YELLOW}Do you want to run database migrations? (y/n)${NC}"
read -r RUN_MIGRATIONS

if [ "$RUN_MIGRATIONS" = "y" ] || [ "$RUN_MIGRATIONS" = "Y" ]; then
    echo -e "${GREEN}Running migrations...${NC}"
    php artisan migrate --force
fi

# Link storage
echo ""
echo -e "${GREEN}Linking storage...${NC}"
php artisan storage:link

# Set permissions
echo ""
echo -e "${GREEN}Setting permissions...${NC}"
sudo chown -R deploy:www-data "$APP_DIR"
sudo chmod -R 775 "$APP_DIR/storage"
sudo chmod -R 775 "$APP_DIR/bootstrap/cache"

# Clear and cache config
echo ""
echo -e "${GREEN}Optimizing application...${NC}"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Restart PHP-FPM
echo ""
echo -e "${GREEN}Restarting PHP-FPM...${NC}"
sudo systemctl restart php${PHP_VERSION}-fpm

# Test application
echo ""
echo -e "${GREEN}Testing application...${NC}"
php artisan --version

echo ""
echo -e "${GREEN}======================================${NC}"
echo -e "${GREEN}  Deployment completed!${NC}"
echo -e "${GREEN}======================================${NC}"
echo ""
echo -e "${BLUE}Application URL:${NC} https://api.jalanbareng.id"
echo -e "${BLUE}Health check:${NC} https://api.jalanbareng.id/api/health"
echo ""

exit 0
