#!/bin/bash
sudo groupadd grp_it
sudo groupadd grp_dev
sudo groupadd grp_rh
sudo groupadd grp_finance
create_user() {
    local username=$1
    local group=$2
    if id "$username" &>/dev/null; then
        echo "User $username already exists"
    else
        sudo useradd -m -G "$group" -s /bin/bash "$username"
        echo "$username:Password123!" | sudo chpasswd
    fi
}
create_user "alice_dev" "grp_dev"
create_user "bob_rh" "grp_rh"
create_user "charlie_it" "grp_it"
create_user "dave_it" "grp_it"
sudo usermod -aG sudo charlie_it
sudo usermod -aG sudo dave_it
sudo chown -R www-data:grp_dev /var/www/html/commercial
sudo chmod -R 775 /var/www/html/commercial
sudo chown -R www-data:grp_rh /var/www/html/rh
sudo chmod -R 770 /var/www/html/rh
