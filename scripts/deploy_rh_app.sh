#!/bin/bash
REPO_DIR=$(pwd)/../src/rh
TARGET_DIR="/var/www/html/rh"
DB_USER="root"
DB_PASS="StrongRootPassword123!"
if [ -f "$REPO_DIR/init.sql" ]; then
    sudo mysql -u$DB_USER -p$DB_PASS < "$REPO_DIR/init.sql"
else
    if [ -f "../src/rh/init.sql" ]; then
       sudo mysql -u$DB_USER -p$DB_PASS < "../src/rh/init.sql"
       REPO_DIR="../src/rh"
    else 
       echo "Could not find init.sql"
    fi
fi
sudo cp $REPO_DIR/*.php $TARGET_DIR/
sudo chown -R www-data:grp_rh $TARGET_DIR
sudo chmod -R 770 $TARGET_DIR
