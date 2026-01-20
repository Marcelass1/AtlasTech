# Connectivity Testing Guide

This guide helps you verify the connection between your **Ubuntu Servers** (Main/Backup) and your friend's **Windows Client VM**.

## 1. Network Setup Check
**CRITICAL:** Both computers (Yours and your Friend's) must be on the **SAME WiFi or Network Cable**.
If you are in different houses, **Bridged Mode will NOT work** directly. You would need a VPN (like ZeroTier).

### Configuration Requirements
*   **All VMs (Ubuntu & Windows)** must be set to **Bridged Adapter** in VirtualBox/VMware settings.
*   They must all receive an IP address from your home router (usually `192.168.1.x` or `192.168.0.x`).

---

## 2. Get IP Addresses

### On Your Main Server (Ubuntu):
1.  Login as a sudo user (e.g., `charlie_it` or root).
2.  Run: `ip a`
3.  Find access `inet` address (e.g., `192.168.1.45`). **Record this.**

### On Friend's Windows VM:
1.  Open **Command Prompt** (cmd).
2.  Run: `ipconfig`
3.  Find **IPv4 Address** (e.g., `192.168.1.50`). **Record this.**

---

## 3. Test 1: Ping (Basic Connection)

### From Windows VM (Friend):
Open `cmd` and ping your server:
```cmd
ping <YOUR_SERVER_IP>
# Example: ping 192.168.1.45
```
*   **Success:** "Reply from 192.168.1.45: bytes=32..."
*   **Failure:** "Request timed out." or "Destination host unreachable."
    *   *Fix:* Check if Firewall (UFW) is blocking. Run `sudo ufw allow from 192.168.1.0/24 to any` on Ubuntu.

---

## 4. Test 2: Web Access (Applications)

### From Windows VM (Friend):
Open a Web Browser (Edge/Chrome) and try:

1.  **Commercial Site:** `http://<YOUR_SERVER_IP>/commercial`
    *   *Result:* Should show "Site Commercial (Public)".
2.  **RH Site:** `http://<YOUR_SERVER_IP>/rh`
    *   *Result:* Should show "Forbidden" or Ask for Login (depending on config).
    *   *Note:* The RH site is restricted. We configured it to assume restricted access.

---

## 5. Test 3: SSH Access (Remote Terminal)

### From Windows VM (Friend):
Open **PowerShell** or **Command Prompt** and try to login as one of the users we created.

1.  **Login as IT Admin:**
    ```powershell
    ssh charlie_it@<YOUR_SERVER_IP>
    ```
    *   **Password:** `charlie_it:Password123!`
    *   *Result:* Successful login.

2.  **Login as Developer:**
    ```powershell
    ssh alice_dev@<YOUR_SERVER_IP>
    ```
    *   *Result:* Successful login.

3.  **Check Permissions:**
    Once logged in as `alice_dev`:
    ```bash
    # Try to write to commercial folder (Should WORK for Developers/IT usually, or check specific permissions)
    # Based on our script, Developers own the folder.
    touch /var/www/html/commercial/test_file.txt
    
    # Try to write to RH folder (Should FAIL)
    touch /var/www/html/rh/test_file.txt
    ```

---

## Troubleshooting
*   **"Connection Refused"**: SSH isn't running (`sudo systemctl status ssh`) or Firewall blocked.
*   **"Host Unreachable"**: VMs are on different networks. Check "Bridged Adapter" setting.
