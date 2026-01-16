#!/bin/bash

# ==========================================
# AtlasTech Solutions - User Management
# Authors: Ismail & Yassin
# ==========================================

# 1. Create Groups
echo "[INFO] Creating Groups..."
sudo groupadd grp_it
sudo groupadd grp_dev
sudo groupadd grp_rh
sudo groupadd grp_finance

# 2. Create Users (with dummy passwords)
# Password for all is: Password123!
create_user() {
    local username=$1
    local group=$2
    if id "$username" &>/dev/null; then
        echo "User $username already exists"
    else
        sudo useradd -m -G "$group" -s /bin/bash "$username"
        echo "$username:Password123!" | sudo chpasswd
        echo "User $username created in group $group"
    fi
}

create_user "alice_dev" "grp_dev"
create_user "bob_rh" "grp_rh"
create_user "charlie_it" "grp_it"
create_user "dave_it" "grp_it"

# 3. Add IT to sudoers
echo "[INFO] Granting sudo to IT..."
sudo usermod -aG sudo charlie_it
sudo usermod -aG sudo dave_it

# 4. Set Directory Permissions
echo "[INFO] Setting Permissions..."

# Commercial Site: Owned by www-data, Writable by Devs
sudo chown -R www-data:grp_dev /var/www/html/commercial
sudo chmod -R 775 /var/www/html/commercial

# RH Site: Owned by www-data, Writable by RH
sudo chown -R www-data:grp_rh /var/www/html/rh
sudo chmod -R 770 /var/www/html/rh  # Stricter: Others cannot read

echo "[SUCCESS] Users and Permissions configured."
