# User Guide: Setting Up AtlasTech Infrastructure (Bridged)
**Authors:** Ismail & Yassin

## 1. Prerequisites
You need **VirtualBox** or **VMware Workstation** installed on your PC.

## 2. Step 1: Create Virtual Machines
Create 2 VMs with **Ubuntu Server** (20.04 or 22.04).

### VM 1: Main Server (SRV-MAIN)
*   **RAM:** 2048 MB
*   **Network Adapter:** **Bridged Adapter** (Select your main WiFi/Ethernet card)

### VM 2: Backup Server (SRV-BACKUP)
*   **RAM:** 1024 MB
*   **Network Adapter:** **Bridged Adapter** (Select your main WiFi/Ethernet card)

## 3. Step 2: Check IP Addresses (Important!)
Since you are using **Bridged Mode**, your router assigns the IPs.
1.  Start the **Main Server**.
2.  Login and run: `ip a`
3.  **Write down the IP address** (e.g., `192.168.1.45`). You will need this to access the website.

## 4. Step 3: Run Configuration Scripts
I have pushed all scripts to GitHub. On each server, clone the repo and run the scripts.

### On Main Server:
```bash
sudo apt install git -y
git clone https://github.com/Marcelass1/AtlasTech.git
cd AtlasTech/scripts
chmod +x *.sh
./setup_main_server.sh
./user_management.sh
```
*Note: Run `hardening.sh` ONLY if you know your local subnet range.*

### On Backup Server:
```bash
sudo apt install git -y
git clone https://github.com/Marcelass1/AtlasTech.git
cd AtlasTech/scripts
chmod +x *.sh
./setup_backup_server.sh
```

## 5. Verification
*   Open a browser on your Windows PC (or Client VM).
*   Go to `http://<YOUR-SERVER-IP>/commercial`.
*   Example: `http://192.168.1.45/commercial`
