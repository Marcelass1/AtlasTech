# Project Final JobInTech Cybersecurity - Implementation Plan (VM Approach)

## Goal Description
Implement the secure infrastructure for "AtlasTech Solutions" using **Virtual Machines**. This involves deploying two Ubuntu servers (Main & Backup) to ensure redundancy and strict role-based access control as described in the project PDF.

## User Review Required
> [!IMPORTANT]
> **Hardware Requirements**: You will need to manually create 2 Virtual Machines (VMs) for the servers (Ubuntu Server 20.04/22.04 recommended) and 1 Client VM (Windows or Linux). Ensure your host machine has enough RAM (at least 8GB recommended).

> [!NOTE]
> **Automation**: I will provide **Shell Scripts (.sh)**. You will copy these into your VMs and run them to automatically configure the software, users, and security rules.

## Proposed Changes

### Documentation & Design
#### [NEW] [infrastructure_design.md](file:///C:/Users/dell/.gemini/antigravity/brain/a017d6e3-1fd0-4ac3-90ca-9a9e8b8a27c9/infrastructure_design.md)
- **Topology**: Diagram showing Main Server, Backup Server, and Clients (Simulated VLANs).
- **IP Plan**: Static IP assignments for the VMs.
- **Security Policy**: Detailed UFW firewall rules.

### VM Configuration Scripts
#### [NEW] [scripts/main_server_setup.sh](file:///C:/Users/dell/.gemini/antigravity/brain/a017d6e3-1fd0-4ac3-90ca-9a9e8b8a27c9/scripts/main_server_setup.sh)
- Installs Apache, MariaDB (Master), PHP.
- Configures Virtual Hosts for Commercial App & RH App.
- Runs `user_management.sh`.

#### [NEW] [scripts/backup_server_setup.sh](file:///C:/Users/dell/.gemini/antigravity/brain/a017d6e3-1fd0-4ac3-90ca-9a9e8b8a27c9/scripts/backup_server_setup.sh)
- Installs MariaDB (Slave) and Backup utilities (rsync/automysqlbackup).
- Configures redundancy.

#### [NEW] [scripts/user_management.sh](file:///C:/Users/dell/.gemini/antigravity/brain/a017d6e3-1fd0-4ac3-90ca-9a9e8b8a27c9/scripts/user_management.sh)
- Creates Linux Groups: `grp_it`, `grp_dev`, `grp_rh`, `grp_fin`.
- Creates Users with specific directory permissions (e.g., Devs access `/var/www/html`, RH access only their app folder).

### Security
#### [NEW] [scripts/hardening.sh](file:///C:/Users/dell/.gemini/antigravity/brain/a017d6e3-1fd0-4ac3-90ca-9a9e8b8a27c9/scripts/hardening.sh)
- SSH Hardening (Disable Root Login, Key-only).
- Fail2Ban setup.
- AppArmor profiles (app-level).

## Verification Plan

### Manual Verification
1.  **Connectivity**: Ping tests between VMs.
2.  **Web Access**: Access `https://atlas-solutions.local` from the Client VM.
3.  **Permissions**: SSH into Main Server as "Dev User" and try to read "RH Data" (Should fail).
4.  **Backup**: Verify data replication to the Backup Server.
