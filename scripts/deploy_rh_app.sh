#!/bin/bash

# ==========================================
# AtlasTech Solutions - Deploy RH App
# Authors: Ismail & Yassin
# ==========================================

REPO_DIR=$(pwd)/../src/rh
TARGET_DIR="/var/www/html/rh"
DB_USER="root"
DB_PASS="StrongRootPassword123!"

echo "[INFO] Deploying RH Application..."

# 1. Setup Database
echo "[INFO] Initializing Database..."
if [ -f "$REPO_DIR/init.sql" ]; then
    sudo mysql -u$DB_USER -p$DB_PASS < "$REPO_DIR/init.sql"
    echo "[SUCCESS] Database 'rh_db' created/updated."
else
    echo "[ERROR] init.sql not found!"
    # Fallback if running from scripts dir implies ../src/rh might be different
    # Adjusting for likely path if cloned: ~/AtlasTech/src/rh
    if [ -f "../src/rh/init.sql" ]; then
       sudo mysql -u$DB_USER -p$DB_PASS < "../src/rh/init.sql"
       REPO_DIR="../src/rh" # Update Repo Dir if found here
       echo "[SUCCESS] Database 'rh_db' created from alternate path."
    else 
       echo "Could not find init.sql. Please ensure you cloned the full repo."
    fi
fi

# 2. Copy Files
echo "[INFO] Copying Application Files..."
sudo cp $REPO_DIR/*.php $TARGET_DIR/

# 3. Set Permissions
echo "[INFO] Setting Permissions..."
sudo chown -R www-data:grp_rh $TARGET_DIR
sudo chmod -R 770 $TARGET_DIR

echo "[SUCCESS] RH App Deployed to $TARGET_DIR"
echo "URL: https://<YOUR-IP>/rh"
