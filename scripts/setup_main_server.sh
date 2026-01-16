#!/bin/bash

# ==========================================
# AtlasTech Solutions - Main Server Setup
# Authors: Ismail & Yassin
# ==========================================

# 1. Update & Upgrade
echo "[INFO] Updating system..."
sudo apt-get update && sudo apt-get upgrade -y

# 2. Install LAMP Stack
echo "[INFO] Installing LAMP stack..."
sudo apt-get install -y apache2 mariadb-server php libapache2-mod-php php-mysql fail2ban ufw

# 3. Secure MariaDB
echo "[INFO] Securing Database..."
sudo mysql -e "UPDATE mysql.user SET Password = PASSWORD('StrongRootPassword123!') WHERE User = 'root'"
sudo mysql -e "DELETE FROM mysql.user WHERE User = ''"
sudo mysql -e "DELETE FROM mysql.user WHERE User = 'root' AND Host NOT IN ('localhost', '127.0.0.1', '::1')"
sudo mysql -e "DROP DATABASE IF EXISTS test"
sudo mysql -e "FLUSH PRIVILEGES"

# 4. Create Web Directories
echo "[INFO] Creating Web Directories..."
sudo mkdir -p /var/www/html/commercial
sudo mkdir -p /var/www/html/rh

# 5. Create Dummy Index Files
echo "<h1>Site Commercial (Public)</h1><p>Welcome to AtlasTech Commercial</p>" | sudo tee /var/www/html/commercial/index.html
echo "<h1>Intranet RH (Private)</h1><p>Restricted Access - RH Dept Only</p>" | sudo tee /var/www/html/rh/index.html

# 6. Configure Apache Virtual Hosts
echo "[INFO] Configuring Apache..."

# Commercial Site - Open to All
cat <<EOF | sudo tee /etc/apache2/sites-available/atlas-commercial.conf
<VirtualHost *:80>
    ServerAdmin admin@atlastech.com
    DocumentRoot /var/www/html/commercial
    ErrorLog \${APACHE_LOG_DIR}/commercial_error.log
    CustomLog \${APACHE_LOG_DIR}/commercial_access.log combined
</VirtualHost>
EOF

# RH Site - Restricted (Modified for Bridged Mode)
# Note: In a real bridged network, 192.168.x.x might be anyone.
# We are allowing all local traffic for simplicity now.
cat <<EOF | sudo tee /etc/apache2/sites-available/atlas-rh.conf
<VirtualHost *:80>
    ServerAdmin admin@atlastech.com
    DocumentRoot /var/www/html/rh
    ServerName rh.atlastech.internal
    
    <Directory /var/www/html/rh>
        Require all granted
        # To restrict, uncomment and add your subnet:
        # Require ip 192.168.1.0/24
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/rh_error.log
    CustomLog \${APACHE_LOG_DIR}/rh_access.log combined
</VirtualHost>
EOF

# Enable Sites
sudo a2ensite atlas-commercial.conf
sudo a2ensite atlas-rh.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2

echo "[SUCCESS] Main Server Setup Complete!"
