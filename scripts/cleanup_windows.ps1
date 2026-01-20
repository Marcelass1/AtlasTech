# ==========================================
# AtlasTech Solutions - Windows Cleanup Script
# FORCE DELETES all Project Users, Groups, and Folders
# RUN AS ADMINISTRATOR
# ==========================================

$ErrorActionPreference = "SilentlyContinue"

Write-Host "==========================================" -ForegroundColor Red
Write-Host "    AtlasTech Windows Cleanup Tool" -ForegroundColor Red
Write-Host "==========================================" -ForegroundColor Red
Write-Host "WARNING: This will delete users (ceo_user, it_admin, etc.) and 'C:\Entreprise'."

$confirm = Read-Host "Are you sure you want to delete everything? (type 'yes')"
if ($confirm -ne "yes") {
    Write-Host "Aborted."
    Exit
}

# --- 1. List of Users to Delete ---
# Includes names from my scripts AND your screenshots (e.g., it_admin)
$UsersToDelete = @(
    "ceo_user",
    "charlie_it", "it_admin", "dave_it",
    "alice_dev", "dev_user",
    "bob_rh", "rh_user",
    "finance_user", "finance_user1",
    "com_user", "com_user1"
)

# --- 2. List of Groups to Delete ---
$GroupsToDelete = @(
    "grp_ceo",
    "grp_it",
    "grp_dev",
    "grp_rh",
    "grp_finance",
    "grp_com"
)

# --- 3. Delete Users ---
Write-Host "`nDeleting Users..." -ForegroundColor Yellow
foreach ($user in $UsersToDelete) {
    if (Get-LocalUser -Name $user) {
        Remove-LocalUser -Name $user
        Write-Host "[DELETED] User: $user" -ForegroundColor Green
    }
    else {
        Write-Host "[SKIP] User $user not found" -ForegroundColor DarkGray
    }
}

# --- 4. Delete Groups ---
Write-Host "`nDeleting Groups..." -ForegroundColor Yellow
foreach ($group in $GroupsToDelete) {
    if (Get-LocalGroup -Name $group) {
        Remove-LocalGroup -Name $group
        Write-Host "[DELETED] Group: $group" -ForegroundColor Green
    }
    else {
        Write-Host "[SKIP] Group $group not found" -ForegroundColor DarkGray
    }
}

# --- 5. Delete Directories (from Screenshot) ---
Write-Host "`nDeleting Directories..." -ForegroundColor Yellow
$FolderPath = "C:\Entreprise"
if (Test-Path $FolderPath) {
    Remove-Item -Path $FolderPath -Recurse -Force
    Write-Host "[DELETED] Folder: $FolderPath" -ForegroundColor Green
}
else {
    Write-Host "[SKIP] Folder $FolderPath not found" -ForegroundColor DarkGray
}

Write-Host "`n==========================================" -ForegroundColor Cyan
Write-Host "Cleanup Complete. System is clean." -ForegroundColor Cyan
Write-Host "=========================================="
