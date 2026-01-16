#!/bin/bash
sudo apt-get update
sudo apt-get install -y mariadb-server rsync
BACKUP_DIR="/var/backups/atlastech"
sudo mkdir -p $BACKUP_DIR
sudo chown -R root:root $BACKUP_DIR
sudo chmod 700 $BACKUP_DIR
cat <<EOF | sudo tee /usr/local/bin/daily_backup.sh
#!/bin/bash
DATE=\$(date +%F)
mysqldump -u root --all-databases > $BACKUP_DIR/db_backup_\$DATE.sql
echo "Backup completed for \$DATE"
EOF
sudo chmod +x /usr/local/bin/daily_backup.sh
(crontab -l 2>/dev/null; echo "0 2 * * * /usr/local/bin/daily_backup.sh") | crontab -
