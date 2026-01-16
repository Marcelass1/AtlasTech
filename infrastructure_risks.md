# Infrastructure Risk Analysis - AtlasTech Solutions
**Authors:** Ismail & Yassin

## 1. Introduction
This document outlines the potential security risks associated with the implemented infrastructure for AtlasTech Solutions. It highlights vulnerabilities introduced by specific configuration choices (e.g., Bridged Networking, Self-Signed Certificates) and proposes mitigation strategies.

## 2. Identified Risks

### 2.1 Network Exposure (Bridged Mode)
*   **Risk Description:** Using **Bridged Networking** places the Virtual Machines (VMs) on the same Layer 2 network as the host machine and other devices on the local LAN (e.g., smartphones, IoT devices).
*   **Impact:** If a device on the local network is compromised, an attacker could directly attack the Main Server or Backup Server without passing through a perimeter firewall.
*   **Mitigation (Implemented):** 
    *   **UFW Firewall:** Configured on the servers to block incoming traffic on non-essential ports.
    *   **Fail2Ban:** Protects against brute-force attacks on SSH.
*   **Ideal Mitigation (Production):** Use a dedicated **DMZ VLAN** isolated from the internal LAN by a physical router/firewall.

### 2.2 Transport Security (Self-Signed Certificates)
*   **Risk Description:** The web server uses **Self-Signed SSL Certificates** generated via `openssl`.
*   **Impact:** 
    *   Users receive browser security warnings ("Your connection is not private"), leading to "Certificate Warning Fatigue".
    *   No guarantee of server identity (Man-in-the-Middle attacks are possible if the user ignores warnings without verifying fingerprints).
*   **Mitigation:** Acceptable for internal/lab testing.
*   **Ideal Mitigation (Production):** Purchase a certificate from a trusted CA or use Let's Encrypt for a public domain.

### 2.3 Database Connectivity
*   **Risk Description:** The database allows connections from the Backup Server for replication.
*   **Impact:** If the Backup Server is compromised, it could be used as a pivot point to attack the Main Database.
*   **Mitigation (Implemented):** Firewall rules restrict 3306 access specifically to the Backup Server's IP (or subnet in Bridged mode).

### 2.4 Backup Integrity
*   **Risk Description:** Backups are stored on a secondary VM (`SRV-BACKUP`).
*   **Impact:** 
    *   If the underlying physical host (your PC) fails, **both** Main and Backup VMs are lost.
    *   Ransomware on the host could encrypt both VM disk files.
*   **Mitigation:** Regularly copy backup files (`.sql`) from the VM to external cloud storage (e.g., AWS S3, Google Drive).

### 2.5 Hardcoded Credentials
*   **Risk Description:** Database passwords and API keys are hardcoded in the deployment scripts (`setup_main_server.sh`) and PHP config (`db.php`).
*   **Impact:** Anyone with read access to the codebase knows the production passwords.
*   **Ideal Mitigation:** Use Environment Variables (`.env` files) or a Secret Management tool (HashiCorp Vault) to inject credentials at runtime.
