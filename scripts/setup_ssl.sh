#!/bin/bash

# ==========================================
# AtlasTech Solutions - SSL/TLS Setup
# Authors: Ismail & Yassin
# Description: Generates Self-Signed Certs & Enables HTTPS
# ==========================================

# 1. Enable SSL Module
echo "[INFO] Enabling Apache SSL Module..."
sudo a2enmod ssl
sudo a2enmod rewrite

# 2. Generate Self-Signed Certificate
echo "[INFO] Generating Self-Signed Certificate..."
sudo mkdir -p /etc/apache2/ssl
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/apache2/ssl/atlastech.key \
    -out /etc/apache2/ssl/atlastech.crt \
    -subj "/C=MA/ST=Meknes/L=Meknes/O=AtlasTech/OU=IT/CN=www.atlastech.com"

# 3. Configure Apache for HTTPS
echo "[INFO] Configuring Virtual Hosts for SSL..."

cat <<EOF | sudo tee /etc/apache2/sites-available/atlas-ssl.conf
<VirtualHost *:443>
    ServerName www.atlastech.com
    DocumentRoot /var/www/html/commercial
    
    SSLEngine on
    SSLCertificateFile /etc/apache2/ssl/atlastech.crt
    SSLCertificateKeyFile /etc/apache2/ssl/atlastech.key

    ErrorLog \${APACHE_LOG_DIR}/commercial_ssl_error.log
    CustomLog \${APACHE_LOG_DIR}/commercial_ssl_access.log combined
</VirtualHost>

<VirtualHost *:443>
    ServerName rh.atlastech.internal
    DocumentRoot /var/www/html/rh
    
    SSLEngine on
    SSLCertificateFile /etc/apache2/ssl/atlastech.crt
    SSLCertificateKeyFile /etc/apache2/ssl/atlastech.key

    <Directory /var/www/html/rh>
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/rh_ssl_error.log
    CustomLog \${APACHE_LOG_DIR}/rh_ssl_access.log combined
</VirtualHost>
EOF

# 4. Enforce HTTPS (Redirect HTTP -> HTTPS)
# We modify the existing standard config to redirect
echo "[INFO] Adding Redirect Rules..."
cat <<EOF | sudo tee /etc/apache2/sites-available/atlas-commercial.conf
<VirtualHost *:80>
    ServerAdmin admin@atlastech.com
    DocumentRoot /var/www/html/commercial
    ServerName www.atlastech.com
    
    # Redirect all traffic to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^/?(.*) https://%{SERVER_NAME}/\$1 [R=301,L]
    
    ErrorLog \${APACHE_LOG_DIR}/commercial_error.log
    CustomLog \${APACHE_LOG_DIR}/commercial_access.log combined
</VirtualHost>
EOF

# 5. Enable SSL Config
sudo a2ensite atlas-ssl.conf
sudo systemctl reload apache2

echo "[SUCCESS] HTTPS Enabled and Enforced!"
echo "Note: Your browser will warn about 'Unsafe Identity' because this is a self-signed cert. This is normal for lab environments."
