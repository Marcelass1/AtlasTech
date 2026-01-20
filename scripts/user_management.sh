#!/bin/bash

# Create Groups based on PDF Roles
sudo groupadd grp_ceo        # Direction
sudo groupadd grp_it         # Informatique (Admins IT)
sudo groupadd grp_dev        # Développement
sudo groupadd grp_rh         # Ressources Humaines
sudo groupadd grp_finance    # Finance / Comptabilité
sudo groupadd grp_com        # Commercial / Marketing

create_user() {
    local username=$1
    local group=$2
    if id "$username" &>/dev/null; then
        echo "User $username already exists"
    else
        # Create user with primary group
        sudo useradd -m -G "$group" -s /bin/bash "$username"
        # Set default password
        echo "$username:Password123!" | sudo chpasswd
        # Force password change on first login (optional, good practice)
        # sudo chage -d 0 "$username"
        echo "Created user $username in group $group"
    fi
}

# 1. Direction (CEO) - 1 User
create_user "ceo_user" "grp_ceo"

# 2. Informatique (IT) - 4 Users
create_user "charlie_it" "grp_it"
create_user "dave_it" "grp_it"
create_user "eve_it" "grp_it"
create_user "frank_it" "grp_it"
# Grant sudo access to IT admins
for user in charlie_it dave_it eve_it frank_it; do
    sudo usermod -aG sudo "$user"
done

# 3. Développement - 6 Users
create_user "alice_dev" "grp_dev"
create_user "dev_user2" "grp_dev"
create_user "dev_user3" "grp_dev"
create_user "dev_user4" "grp_dev"
create_user "dev_user5" "grp_dev"
create_user "dev_user6" "grp_dev"

# 4. Ressources Humaines (RH) - 3 Users
create_user "bob_rh" "grp_rh"
create_user "rh_user2" "grp_rh"
create_user "rh_user3" "grp_rh"

# 5. Finance / Comptabilité - 3 Users
create_user "finance_user1" "grp_finance"
create_user "finance_user2" "grp_finance"
create_user "finance_user3" "grp_finance"

# 6. Commercial / Marketing - 8 Users
create_user "com_user1" "grp_com"
create_user "com_user2" "grp_com"
create_user "com_user3" "grp_com"
create_user "com_user4" "grp_com"
create_user "com_user5" "grp_com"
create_user "com_user6" "grp_com"
create_user "com_user7" "grp_com"
create_user "com_user8" "grp_com"

# --- Folder Permissions ---

# Commercial Site (Public/Marketing)
# Developers need to write code here? Or maybe Commercial team needs to upload content?
# Assuming Developers manage the code for now, as standard. 
# Giving Write access to grp_dev and Read access to others via 'others' (775).
sudo chown -R www-data:grp_dev /var/www/html/commercial
sudo chmod -R 775 /var/www/html/commercial

# RH Site (Private)
# Strictly for RH usage. 
sudo chown -R www-data:grp_rh /var/www/html/rh
sudo chmod -R 770 /var/www/html/rh

echo "User creation and permissions setup complete."
