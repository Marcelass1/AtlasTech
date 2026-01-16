# User Guide: Setting Up AtlasTech Infrastructure
**Authors:** Ismail & Yassin

## 1. Prerequisites
You need **VirtualBox** or **VMware Workstation** installed on your PC.

## 2. Step 1: Create Virtual Machines
Create 2 VMs with **Ubuntu Server** (20.04 or 22.04).

### VM 1: Main Server (SRV-MAIN)
*   **RAM:** 2048 MB
*   **Network Adapter 1:** NAT (For Internet access)
*   **Network Adapter 2:** Internal Network (Name: `intnet_lan`) -> Set IP manually to `192.168.50.10` inside the VM later.

### VM 2: Backup Server (SRV-BACKUP)
*   **RAM:** 1024 MB
*   **Network Adapter 1:** NAT
*   **Network Adapter 2:** Internal Network (Name: `intnet_lan`) -> Set IP manually to `192.168.50.20` later.

## 3. Step 2: Configure IP Addresses
On **both servers**, edit Netplan configuration to set static IPs.
`sudo nano /etc/netplan/00-installer-config.yaml`
Example for **Main Server**:
```yaml
network:
  version: 2
  ethernets:
    enp0s8: # Check your interface name with 'ip a'
      addresses: [192.168.50.10/24]
```
Run `sudo netplan apply`.

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
./hardening.sh
```

### On Backup Server:
```bash
sudo apt install git -y
git clone https://github.com/Marcelass1/AtlasTech.git
cd AtlasTech/scripts
chmod +x *.sh
./setup_backup_server.sh
```

## 5. Verification
*   Open a browser on your Client VM (connected to `intnet_lan`).
*   Go to `http://192.168.50.10/commercial` -> Should work.
*   Go to `http://192.168.50.10/rh` -> Should work (simulating internal access).
