#!/bin/bash

# ==========================================
# AtlasTech Solutions - Minimal User Setup
# Creates 1 Representative User per Department
# Matches Main Server Permissions
# ==========================================

echo "Starting Minimal User & Group Setup..."

# 1. Create Groups (Same as Main Server)
echo "Creating Groups..."
sudo groupadd -f grp_ceo        # Direction
sudo groupadd -f grp_it         # Informatique
sudo groupadd -f grp_dev        # Développement
sudo groupadd -f grp_rh         # Ressources Humaines
sudo groupadd -f grp_finance    # Finance
sudo groupadd -f grp_com        # Commercial

# 2. Function to Create a User
create_user() {
    local username=$1
    local group=$2
    
    # Check if user exists
    if id "$username" &>/dev/null; then
        echo "User $username already exists"
    else
        # Create user with home directory and bash shell
        sudo useradd -m -G "$group" -s /bin/bash "$username"
        # Set default password
        echo "$username:Password123!" | sudo chpasswd
        echo "[OK] Created user: $username (Group: $group)"
    fi
}

# 3. Create One Representative User per Department
echo "Creating Representative Users..."

# Direction (CEO)
create_user "ceo_user" "grp_ceo"

# Informatique (IT Admin)
create_user "charlie_it" "grp_it"
# Give IT Admin sudo rights
sudo usermod -aG sudo charlie_it

# Développement
create_user "alice_dev" "grp_dev"

# Ressources Humaines (RH)
create_user "bob_rh" "grp_rh"

# Finance
create_user "finance_user1" "grp_finance"

# Commercial / Marketing
create_user "com_user1" "grp_com"

# 4. Configure Folder Permissions
echo "Configuring Folder Permissions..."

# Ensure directories exist (in case Apache isn't installed yet)
sudo mkdir -p /var/www/html/commercial
sudo mkdir -p /var/www/html/rh

# --- Commercial Site (Public/Marketing/Dev) ---
# Group: grp_dev (Developers/Marketing access)
# Permissions: 775 (Owner/Group RWX, Others RX)
sudo chown -R www-data:grp_dev /var/www/html/commercial
sudo chmod -R 775 /var/www/html/commercial
echo "[OK] Permissions set for /var/www/html/commercial"

# --- RH Site (Private/Internal) ---
# Group: grp_rh (HR access only)
# Permissions: 770 (Owner/Group RWX, Others None)
sudo chown -R www-data:grp_rh /var/www/html/rh
sudo chmod -R 770 /var/www/html/rh
echo "[OK] Permissions set for /var/www/html/rh"

echo "=========================================="
echo "Minimal Setup Complete!"
echo "Users Created:"
echo "- ceo_user (Direction)"
echo "- charlie_it (IT - Sudo)"
echo "- alice_dev (Dev)"
echo "- bob_rh (RH)"
echo "- finance_user1 (Finance)"
echo "- com_user1 (Commercial)"
echo "=========================================="
