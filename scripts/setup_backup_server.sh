#!/bin/bash

# ==========================================
# AtlasTech Solutions - Backup Server Setup
# Authors: Ismail & Yassin
# ==========================================

# 1. Install Basics
echo "[INFO] Installing Backup Utilities..."
sudo apt-get update
sudo apt-get install -y mariadb-server rsync

# 2. Configure Backup Directory
BACKUP_DIR="/var/backups/atlastech"
sudo mkdir -p $BACKUP_DIR
sudo chown -R root:root $BACKUP_DIR
sudo chmod 700 $BACKUP_DIR

# 3. Create Backup Script (Cron Job)
echo "[INFO] Creating Backup Script..."
cat <<EOF | sudo tee /usr/local/bin/daily_backup.sh
#!/bin/bash
DATE=\$(date +%F)
# 1. Backup Databases
mysqldump -u root --all-databases > $BACKUP_DIR/db_backup_\$DATE.sql

# 2. Sync Files from Main Server (Simulated via rsync over ssh)
# Assumption: SSH Access is set up between servers
# rsync -avz user@192.168.50.10:/var/www/html $BACKUP_DIR/web_content

echo "Backup completed for \$DATE"
EOF

# Make executable
sudo chmod +x /usr/local/bin/daily_backup.sh

# 4. Schedule Cron Job (Every night at 2 AM)
echo "[INFO] Scheduling Cron Job..."
(crontab -l 2>/dev/null; echo "0 2 * * * /usr/local/bin/daily_backup.sh") | crontab -

echo "[SUCCESS] Backup Server Configured."
