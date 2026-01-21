#!/bin/bash

# 1. Update & Upgrade
sudo apt-get update && sudo apt-get upgrade -y

# 2. Firewall (UFW) - Strict Rules for Bridged Mode
sudo ufw --force reset
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Allow Web (Public)
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Allow SSH only from Local Subnet (Adjust 192.168.1.0/24 to your actual subnet if different)
sudo ufw allow from 192.168.1.0/24 to any port 22

# Allow MySQL only from Local Subnet (or specific Backup Server IP)
sudo ufw allow from 192.168.1.0/24 to any port 3306

# Enable UFW
echo "y" | sudo ufw enable

# 3. Fail2Ban Configuration
sudo apt-get install -y fail2ban
cat <<EOF | sudo tee /etc/fail2ban/jail.local
[sshd]
enabled = true
port = ssh
filter = sshd
logpath = /var/log/auth.log
maxretry = 3
bantime = 3600
findtime = 600
ignoreip = 127.0.0.1/8 192.168.1.0/24
EOF
sudo systemctl restart fail2ban

# 4. SSH Hardening
# Backup original config
sudo cp /etc/ssh/sshd_config /etc/ssh/sshd_config.bak

# Disable Root Login & Password Auth (Optional but recommended if keys are set up)
# Note: Be careful with PasswordAuthentication no if you haven't set up keys yet!
# We will set RootLogin to no, but keep PasswordAuth yes for now to avoid locking you out during setup.
sudo sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin no/' /etc/ssh/sshd_config
# sudo sed -i 's/PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config

sudo systemctl restart ssh

# 5. Automatic Updates
sudo apt-get install -y unattended-upgrades
sudo dpkg-reconfigure -plow unattended-upgrades

# 6. Password Policy (Requires libpam-pwquality)
sudo apt-get install -y libpam-pwquality
# Enforce quality: minlen=12, require at least 1 upper, 1 lower, 1 digit, 1 symbol
# This modifies /etc/pam.d/common-password
sudo sed -i 's/retry=3/retry=3 minlen=12 difok=3 ucredit=-1 lcredit=-1 dcredit=-1 ocredit=-1/' /etc/pam.d/common-password

echo "Hardening Complete. Firewall is active and restrictive."
