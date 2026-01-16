#!/bin/bash

# ==========================================
# AtlasTech Solutions - Hardening & Security
# Authors: Ismail & Yassin
# ==========================================

# 1. Configure UFW Firewall (Reset to Default)
echo "[INFO] Configuring Firewall..."
sudo ufw --force reset
sudo ufw default deny incoming
sudo ufw default allow outgoing

# 2. Allow Critical Services
sudo ufw allow 80/tcp  # HTTP
sudo ufw allow 443/tcp # HTTPS

# 3. Restrict SSH to Admin IP (Simulated Client)
# REPLACE 192.168.50.100 WITH YOUR ACTUAL ADMIN CP IP
echo "[INFO] Restricting SSH..."
sudo ufw allow from 192.168.50.100 to any port 22

# 4. Allow Database Replication from Backup Server
sudo ufw allow from 192.168.50.20 to any port 3306

# 5. Enable Firewall
echo "y" | sudo ufw enable

# 6. Configure Fail2Ban (Protect SSH)
echo "[INFO] Configuring Fail2Ban..."
cat <<EOF | sudo tee /etc/fail2ban/jail.local
[sshd]
enabled = true
port = ssh
filter = sshd
logpath = /var/log/auth.log
maxretry = 3
bantime = 3600
EOF

sudo systemctl restart fail2ban

# 7. Secure SSH Config
echo "[INFO] Hardening SSH..."
# Disable Root Login
sudo sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin no/' /etc/ssh/sshd_config
# Disable Empty Passwords
sudo sed -i 's/#PermitEmptyPasswords no/PermitEmptyPasswords no/' /etc/ssh/sshd_config

sudo systemctl restart sshd

echo "[SUCCESS] Server Hardened."
