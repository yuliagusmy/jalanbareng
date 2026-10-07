#!/bin/bash
# install-stack.sh - Automated LEMP Stack Installation
# For Ubuntu 22.04 LTS - Jalan Bareng Backend

set -e

echo "==================================="
echo "  LEMP Stack Installation Script"
echo "  For Jalan Bareng Backend"
echo "==================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Error: Please run as root (use sudo)${NC}"
    exit 1
fi

# Update system
echo -e "${GREEN}[1/7] Updating system...${NC}"
apt update && apt upgrade -y

# Install basic tools
echo -e "${GREEN}[2/7] Installing basic tools...${NC}"
apt install -y curl wget git unzip ufw fail2ban software-properties-common

# Setup firewall
echo -e "${GREEN}[2.1/7] Configuring firewall...${NC}"
ufw --force reset
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp comment 'SSH'
ufw allow 80/tcp comment 'HTTP'
ufw allow 443/tcp comment 'HTTPS'
echo "y" | ufw enable
ufw status

# Setup Fail2Ban
echo -e "${GREEN}[2.2/7] Configuring Fail2Ban...${NC}"
systemctl enable fail2ban
systemctl start fail2ban

# Install Nginx
echo -e "${GREEN}[3/7] Installing Nginx...${NC}"
apt install -y nginx
systemctl enable nginx
systemctl start nginx

# Install PHP 8.2
echo -e "${GREEN}[4/7] Installing PHP 8.2...${NC}"
add-apt-repository -y ppa:ondrej/php
apt update
apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath \
               php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-cli php8.2-redis \
               php8.2-imagick php8.2-soap

# Optimize PHP-FPM for 1GB RAM
echo -e "${GREEN}[4.1/7] Optimizing PHP-FPM configuration...${NC}"
sed -i 's/pm.max_children = 5/pm.max_children = 20/' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/pm.start_servers = 2/pm.start_servers = 4/' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/pm.min_spare_servers = 1/pm.min_spare_servers = 2/' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/pm.max_spare_servers = 3/pm.max_spare_servers = 8/' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/;pm.max_requests = 500/pm.max_requests = 500/' /etc/php/8.2/fpm/pool.d/www.conf

# Increase upload limit
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 20M/' /etc/php/8.2/fpm/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 20M/' /etc/php/8.2/fpm/php.ini

systemctl restart php8.2-fpm

# Install MySQL 8
echo -e "${GREEN}[5/7] Installing MySQL 8...${NC}"
apt install -y mysql-server
systemctl enable mysql
systemctl start mysql

# Generate random MySQL root password
MYSQL_ROOT_PASSWORD=$(openssl rand -base64 32)

# Secure MySQL installation
echo -e "${YELLOW}[5.1/7] Securing MySQL...${NC}"
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${MYSQL_ROOT_PASSWORD}';"
mysql -e "DELETE FROM mysql.user WHERE User='';"
mysql -e "DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');"
mysql -e "DROP DATABASE IF EXISTS test;"
mysql -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';"
mysql -e "FLUSH PRIVILEGES;"

# Save MySQL password to file
echo "${MYSQL_ROOT_PASSWORD}" > /root/.mysql_root_password
chmod 600 /root/.mysql_root_password

echo -e "${RED}IMPORTANT: MySQL root password saved to /root/.mysql_root_password${NC}"
echo -e "${RED}Password: ${MYSQL_ROOT_PASSWORD}${NC}"

# Install Composer
echo -e "${GREEN}[6/7] Installing Composer...${NC}"
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
chmod +x /usr/local/bin/composer

# Install Node.js 20 LTS
echo -e "${GREEN}[7/7] Installing Node.js 20 LTS...${NC}"
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Install AWS CLI (untuk backup ke R2)
echo -e "${GREEN}[7.1/7] Installing AWS CLI...${NC}"
apt install -y awscli

# Create deploy user
echo -e "${GREEN}[7.2/7] Creating deploy user...${NC}"
if id "deploy" &>/dev/null; then
    echo -e "${YELLOW}User 'deploy' already exists${NC}"
else
    adduser --disabled-password --gecos "" deploy
    usermod -aG sudo deploy
    echo -e "${YELLOW}User 'deploy' created. Set password with: passwd deploy${NC}"
fi

# Verify installations
echo ""
echo -e "${GREEN}==================================="
echo "  Installation Complete!"
echo "===================================${NC}"
echo ""
echo -e "${BLUE}Versions installed:${NC}"
echo "- Nginx: $(nginx -v 2>&1 | cut -d'/' -f2)"
echo "- PHP: $(php -v | head -n 1 | cut -d' ' -f2)"
echo "- MySQL: $(mysql --version | awk '{print $3}')"
echo "- Composer: $(composer --version | cut -d' ' -f3)"
echo "- Node.js: $(node -v)"
echo "- npm: $(npm -v)"
echo "- AWS CLI: $(aws --version | cut -d' ' -f1 | cut -d'/' -f2)"
echo ""
echo -e "${BLUE}Services status:${NC}"
systemctl is-active --quiet nginx && echo "- Nginx: $(systemctl is-active nginx)" || echo "- Nginx: inactive"
systemctl is-active --quiet php8.2-fpm && echo "- PHP-FPM: $(systemctl is-active php8.2-fpm)" || echo "- PHP-FPM: inactive"
systemctl is-active --quiet mysql && echo "- MySQL: $(systemctl is-active mysql)" || echo "- MySQL: inactive"
systemctl is-active --quiet fail2ban && echo "- Fail2Ban: $(systemctl is-active fail2ban)" || echo "- Fail2Ban: inactive"
echo ""
echo -e "${BLUE}Firewall status:${NC}"
ufw status | grep -E "Status|22|80|443"
echo ""
echo -e "${YELLOW}Important files:${NC}"
echo "- MySQL root password: /root/.mysql_root_password"
echo "- Nginx config: /etc/nginx/sites-available/"
echo "- PHP-FPM config: /etc/php/8.2/fpm/pool.d/www.conf"
echo "- MySQL config: /etc/mysql/mysql.conf.d/mysqld.cnf"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Set password for 'deploy' user: passwd deploy"
echo "2. Clone Laravel repository to /home/deploy/app"
echo "3. Create database and user for Laravel"
echo "4. Configure Nginx for your domain"
echo "5. Install SSL certificate with Certbot"
echo ""
echo -e "${GREEN}Stack installation completed successfully!${NC}"
