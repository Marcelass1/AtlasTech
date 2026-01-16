# Infrastructure Design - AtlasTech Solutions

## 1. Project Overview & Role Clarification
**Who Does What?**
*   **AI (Me):** I act as the **System Architect & DevOps Engineer**. I define the architecture, write the configuration scripts (Bash), create the design documents, and provide step-by-step guides.
*   **User (You):** You act as the **Data Center Operator**. You are responsible for creating the Virtual Machines in your hypervisor (VirtualBox/VMware), configuring the network adapters, and running the scripts I provide inside the VMs.

## 2. Target Network Topology (VM Environment)
We will simulate a segmented network using **Internal Networks** in your Hypervisor.

### Virtual Machines
| VM Name | OS | Role | Specs (Min) |
| :--- | :--- | :--- | :--- |
| **SRV-MAIN** | Ubuntu Server 20.04/22.04 | Main Web Server (App Commerciale, App RH), Master DB | 2GB RAM, 20GB Disk |
| **SRV-BACKUP** | Ubuntu Server 20.04/22.04 | Backup Server (DB Slave, File Backup) | 1GB RAM, 20GB Disk |
| **CLIENT-ADMIN** | Windows 10 or Linux Mint | Admin/RH Client (Testing internal access) | 4GB RAM, 40GB Disk |

### Network Zones (Simulated VLANs)
In VirtualBox/VMware, we will use **Network Adapters** to separate traffic.

*   **WAN (Internet):** Bridged Adapter or NAT (Allows VM to reach the internet to install packages).
*   **LAN (Internal):** Internal Network named `intnet_lan`.
*   **DMZ (Optional):** We will keep it simple and use Firewall Rules (UFW) on the MAIN server to simulate a DMZ behavior.

### IP Addressing Plan
Subnet: `192.168.50.0/24` (Internal LAN)

| Device | Interface | IP Address | Gateway | Description |
| :--- | :--- | :--- | :--- | :--- |
| **SRV-MAIN** | `eth0` (NAT) | DHCP | ISP | Internet Access |
| **SRV-MAIN** | `eth1` (Internal) | `192.168.50.10` | - | Serves Apps |
| **SRV-BACKUP** | `eth0` (NAT) | DHCP | ISP | Internet Access |
| **SRV-BACKUP** | `eth1` (Internal) | `192.168.50.20` | - | Database Replication |
| **CLIENT-ADMIN** | `eth0` (Internal) | `192.168.50.100` | - | Simulates Internal User |

---

## 3. Security Strategy

### 3.1 Firewall Rules (UFW on SRV-MAIN)
We will implement a **Wait-list (Default Deny)** policy.

| Direction | Port | Protocol | Source | Action | Justification |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **IN** | - | - | Any | **DENY** | Default Policy |
| **IN** | 80 | TCP | Any | **ALLOW** | Public Web Traffic (HTTP) |
| **IN** | 443 | TCP | Any | **ALLOW** | Public Web Traffic (HTTPS) |
| **IN** | 22 | TCP | `192.168.50.100` | **ALLOW** | SSH Management (IT Admin only) |
| **IN** | 3306 | TCP | `192.168.50.20` | **ALLOW** | DB Replication from Backup Server |
| **OUT** | - | - | Any | **ALLOW** | Allow updates/installations |

### 3.2 User Access Control (Linux Groups)
We will create these groups on **SRV-MAIN** to map the company structure:
*   `grp_it`: Full sudo access, SSH allowed.
*   `grp_dev`: Access to `/var/www/html/commercial` (Write). No access to RH data.
*   `grp_rh`: Access to `/var/www/html/rh` (Read/Write via App). No shell access preferable.
*   `grp_finance`: (Data only, managed via App roles, not Linux users usually).

---

## 4. Next Steps
1.  **Create your 3 VMs** following the specs above.
2.  **Configure Networks**: Ensure they can ping each other on the `192.168.50.x` network.
3.  **Run Scripts**: I will provide the script `setup_main_server.sh` next.
