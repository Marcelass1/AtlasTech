#!/bin/bash

# ==========================================
# AtlasTech Solutions - Cleanup / Reset Script
# DELETES all project users and groups!
# USE WITH CAUTION!
# ==========================================

echo "WARNING: This script will DELETE all AtlasTech users and groups."
echo "Home directories for these users will also be removed."
read -p "Are you sure you want to continue? (y/N) " confirm
if [[ $confirm != [yY] && $confirm != [yY][eE][sS] ]]; then
    echo "Aborted."
    exit 1
fi

echo "Starting Cleanup..."

# 1. List of Users to Delete (From both Old and New scripts)
USERS=(
    "ceo_user"
    "charlie_it" "dave_it" "eve_it" "frank_it"
    "alice_dev" "dev_user2" "dev_user3" "dev_user4" "dev_user5" "dev_user6"
    "bob_rh" "rh_user2" "rh_user3"
    "finance_user1" "finance_user2" "finance_user3"
    "com_user1" "com_user2" "com_user3" "com_user4" "com_user5" "com_user6" "com_user7" "com_user8"
)

# 2. List of Groups to Delete
GROUPS=(
    "grp_ceo"
    "grp_it"
    "grp_dev"
    "grp_rh"
    "grp_finance"
    "grp_com"
)

# 3. Delete Users
for user in "${USERS[@]}"; do
    if id "$user" &>/dev/null; then
        sudo userdel -r "$user"
        echo "[DELETED] User: $user"
    else
        echo "[SKIP] User $user not found"
    fi
done

# 4. Delete Groups
for group in "${GROUPS[@]}"; do
    if getent group "$group" >/dev/null; then
        sudo groupdel "$group"
        echo "[DELETED] Group: $group"
    else
        echo "[SKIP] Group $group not found"
    fi
done

# 5. Reset Directory Ownership (Optional safe default)
# We reset them to root:root to ensure no lingering permission issues until re-run
if [ -d "/var/www/html/commercial" ]; then
    sudo chown -R root:root /var/www/html/commercial
    sudo chmod -R 755 /var/www/html/commercial
    echo "[RESET] /var/www/html/commercial ownership reset to root:root"
fi

if [ -d "/var/www/html/rh" ]; then
    sudo chown -R root:root /var/www/html/rh
    sudo chmod -R 700 /var/www/html/rh
    echo "[RESET] /var/www/html/rh ownership reset to root:root"
fi

echo "=========================================="
echo "Cleanup Complete. You can now run the setup script again."
echo "=========================================="
