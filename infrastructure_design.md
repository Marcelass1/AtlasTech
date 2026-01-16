# Infrastructure Design - AtlasTech Solutions
## 1. Project Overview & Team Roles
**Team Members: Ismail & Yassin**

We act as the **System Architects & DevOps Engineers**.
*   **Responsibilities:** Architecture definition, infrastructure implementation, security configuration, and documentation.
*   **Goal:** Upgrade the company's infrastructure using a **Bridged Network** topology for simpler connectivity.

## 2. Target Network Topology (Bridged Mode)
All VMs will be connected via **Bridged Adapter**. They will receive IP addresses from your local home router (DHCP).

### Virtual Machines
| VM Name | OS | Adapter | IP Address |
| :--- | :--- | :--- | :--- |
| **SRV-MAIN** | Ubuntu Server | Bridged | **DHCP** (Check with `ip a`) |
| **SRV-BACKUP** | Ubuntu Server | Bridged | **DHCP** (Check with `ip a`) |
| **CLIENT-WIN** | Windows 10 | Bridged | **DHCP** (Check with `ipconfig`) |

### Connectivity
*   All machines (including your Host PC) are on the same network.
*   You can access the Web Server directly from your Host PC or the Client VM.

---

## 3. Security Strategy (Updated for Bridge)
Since IPs are dynamic, we must be careful with fixed rules.

### 3.1 Firewall Rules (UFW)
We will allow access from the **Local Subnet** instead of a single IP.
*   **SSH (22):** Allow from Local Network (e.g., `192.168.1.0/24`).
*   **Web (80/443):** Allow from Anywhere.
*   **DB (3306):** Allow from Local Network (for Backup Server).

### 3.2 User Access Control
*   **Groups:** `grp_it`, `grp_dev`, `grp_rh`, `grp_finance`.
*   **Permissions:** Strict directory permissions remain unchanged.
