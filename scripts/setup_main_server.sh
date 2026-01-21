#!/bin/bash
sudo apt-get update && sudo apt-get upgrade -y
sudo apt-get install -y apache2 mariadb-server php libapache2-mod-php php-mysql fail2ban ufw
sudo mysql -e "UPDATE mysql.user SET Password = PASSWORD('StrongRootPassword123!') WHERE User = 'root'"
sudo mysql -e "DELETE FROM mysql.user WHERE User = ''"
sudo mysql -e "DELETE FROM mysql.user WHERE User = 'root' AND Host NOT IN ('localhost', '127.0.0.1', '::1')"
sudo mysql -e "DROP DATABASE IF EXISTS test"
sudo mysql -e "FLUSH PRIVILEGES"
sudo mkdir -p /var/www/html/commercial
sudo mkdir -p /var/www/html/rh
if [ -d "../src/commercial" ]; then
    sudo cp -r ../src/commercial/* /var/www/html/commercial/
else
    echo "<h1>Site Commercial (Public)</h1><p>Welcome to AtlasTech Commercial</p>" | sudo tee /var/www/html/commercial/index.html
fi
echo "<h1>Intranet RH (Private)</h1><p>Restricted Access - RH Dept Only</p>" | sudo tee /var/www/html/rh/index.html
cat <<EOF | sudo tee /etc/apache2/sites-available/atlas-commercial.conf
<VirtualHost *:80>
    ServerAdmin admin@atlastech.com
    DocumentRoot /var/www/html/commercial
    
    # Allow access to RH via subfolder for easier testing (http://ip/rh)
    Alias /rh /var/www/html/rh
    <Directory /var/www/html/rh>
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/commercial_error.log
    CustomLog \${APACHE_LOG_DIR}/commercial_access.log combined
</VirtualHost>
EOF
cat <<EOF | sudo tee /etc/apache2/sites-available/atlas-rh.conf
<VirtualHost *:80>
    ServerAdmin admin@atlastech.com
    DocumentRoot /var/www/html/rh
    ServerName rh.atlastech.internal
    <Directory /var/www/html/rh>
        Require all granted
    </Directory>
    ErrorLog \${APACHE_LOG_DIR}/rh_error.log
    CustomLog \${APACHE_LOG_DIR}/rh_access.log combined
</VirtualHost>
EOF
sudo a2ensite atlas-commercial.conf
sudo a2ensite atlas-rh.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
