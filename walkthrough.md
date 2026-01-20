# AtlasTech Project Documentation

This repository contains the scripts and documentation for the JobInTech Cybersecurity Project.

## 1. User Management

### Linux Server (Ubuntu)
*   **`scripts/user_management.sh`**: **(Production)** Creates all 25 users and groups as per the PDF requirements. Use this on the Main Server.
*   **`scripts/setup_minimal_users.sh`**: **(Testing)** Creates 1 representative user per group. Use this on Backup or Test servers.
*   **`scripts/cleanup_users.sh`**: **(Cleanup)** Deletes all project users and groups from the Linux system.

### Windows Client (Simulated)
*   **`scripts/setup_windows_users.ps1`**: **(Setup)** Creates local Windows users/groups to mirror the project structure. Run as **Administrator**.
*   **`scripts/cleanup_windows.ps1`**: **(Cleanup)** Force deletes project users, groups, and folders (`C:\Entreprise`) from Windows. Run as **Administrator**.

## 2. Connectivity Testing
*   **`scripts/test_connectivity.ps1`**: automation script for Windows Clients.
    *   Pings the server.
    *   Checks HTTP access to websites.
    *   Initiates SSH connections.
*   **`connectivity_test_guide.md`**: Detailed manual guide for setting up Bridged Networking.

## 3. Permissions Architecture
The scripts enforce the following security model:
*   **Commercial Site** (`/commercial`): `775` (Public/Shared).
*   **RH Site** (`/rh`): `770` (Private/Internal).

## 4. Users List
| Role | Group | Username (Example) |
| :--- | :--- | :--- |
| **Direction** | `grp_ceo` | `ceo_user` |
| **IT Admin** | `grp_it` | `charlie_it` |
| **Dev** | `grp_dev` | `alice_dev` |
| **RH** | `grp_rh` | `bob_rh` |
| **Finance** | `grp_finance` | `finance_user1` |
| **Com/Mkt** | `grp_com` | `com_user1` |
