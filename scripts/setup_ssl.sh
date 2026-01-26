#!/bin/bash
sudo a2enmod ssl
sudo a2enmod rewrite
sudo mkdir -p /etc/apache2/ssl
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/apache2/ssl/atlastech.key \
    -out /etc/apache2/ssl/atlastech.crt \
    -subj "/C=MA/ST=Meknes/L=Meknes/O=AtlasTech/OU=IT/CN=www.atlastech.com"
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
cat <<EOF | sudo tee /etc/apache2/sites-available/atlas-commercial.conf
<VirtualHost *:80>
    ServerAdmin admin@atlastech.com
    DocumentRoot /var/www/html/commercial
    
    # Generic Redirection (Handles Domains AND IPs)
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^/?(.*) https://%{HTTP_HOST}/\$1 [R=301,L]
    
    ErrorLog \${APACHE_LOG_DIR}/commercial_error.log
    CustomLog \${APACHE_LOG_DIR}/commercial_access.log combined
</VirtualHost>
EOF
sudo a2ensite atlas-ssl.conf
sudo systemctl reload apache2
