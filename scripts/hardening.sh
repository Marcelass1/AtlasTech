#!/bin/bash

# ==========================================
# AtlasTech Solutions - Hardening & Security
# Authors: Ismail & Yassin
# ==========================================

# 1. Reset Firewall
echo "[INFO] Configuring Firewall..."
sudo ufw --force reset
sudo ufw default deny incoming
sudo ufw default allow outgoing

# 2. Allow Critical Services
sudo ufw allow 80/tcp  # HTTP
sudo ufw allow 443/tcp # HTTPS

# 3. Allow SSH (Modified for Bridge: Open to all local, or risk lockout)
# WARNING: Since IP is dynamic, we allow SSH from ANY for now.
# In production, restrict this to your specific Admin IP/Subnet.
echo "[INFO] Allowing SSH..."
sudo ufw allow 22/tcp

# 4. Allow Database Port
sudo ufw allow 3306/tcp

# 5. Enable Firewall
echo "y" | sudo ufw enable

# 6. Configure Fail2Ban
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

echo "[SUCCESS] Server Hardened (Relaxed rules for Bridged Mode)."
