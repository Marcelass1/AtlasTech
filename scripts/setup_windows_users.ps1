# ==========================================
# AtlasTech Solutions - Windows Local User Setup
# Creates 1 Representative User per Department on Windows
# RUN AS ADMINISTRATOR
# ==========================================

$ErrorActionPreference = "SilentlyContinue"

Write-Host "Starting Windows User & Group Setup..." -ForegroundColor Cyan

# Function to Create Group
function New-LocalGroupSafe ($Name, $Description) {
    if (-not (Get-LocalGroup -Name $Name)) {
        New-LocalGroup -Name $Name -Description $Description | Out-Null
        Write-Host "[OK] Created Group: $Name" -ForegroundColor Green
    }
    else {
        Write-Host "[SKIP] Group $Name already exists" -ForegroundColor Yellow
    }
}

# Function to Create User
function New-LocalUserSafe ($Username, $Group, $Password) {
    $SecurePassword = ConvertTo-SecureString $Password -AsPlainText -Force
    
    # 1. Create User
    if (-not (Get-LocalUser -Name $Username)) {
        New-LocalUser -Name $Username -Password $SecurePassword -FullName "$Username ($Group)" -PasswordNeverExpires | Out-Null
        Write-Host "[OK] Created User: $Username" -ForegroundColor Green
    }
    else {
        Write-Host "[SKIP] User $Username already exists" -ForegroundColor Yellow
    }

    # 2. Add to Group
    try {
        Add-LocalGroupMember -Group $Group -Member $Username
        Write-Host "     -> Added to Group: $Group" -ForegroundColor Gray
    }
    catch {
        Write-Host "     [ERR] Failed to add to group $Group" -ForegroundColor Red
    }
}

# --- 1. Create Groups ---
Write-Host "`nCreating Groups..." -ForegroundColor Cyan
New-LocalGroupSafe "grp_ceo" "Direction"
New-LocalGroupSafe "grp_it" "Informatique"
New-LocalGroupSafe "grp_dev" "Developpement"
New-LocalGroupSafe "grp_rh" "Ressources Humaines"
New-LocalGroupSafe "grp_finance" "Finance"
New-LocalGroupSafe "grp_com" "Commercial"

# --- 2. Create Users ---
Write-Host "`nCreating Users (Default Password: Password123!)..." -ForegroundColor Cyan

# Direction
New-LocalUserSafe "ceo_user" "grp_ceo" "Password123!"

# IT
New-LocalUserSafe "charlie_it" "grp_it" "Password123!"
# Add IT to Administrators (Optional - mirrors Sudo)
Add-LocalGroupMember -Group "Administrators" -Member "charlie_it"
Write-Host "     -> Added charlie_it to Administrators" -ForegroundColor Magenta

# Dev
New-LocalUserSafe "alice_dev" "grp_dev" "Password123!"

# RH
New-LocalUserSafe "bob_rh" "grp_rh" "Password123!"

# Finance
New-LocalUserSafe "finance_user1" "grp_finance" "Password123!"

# Commercial
New-LocalUserSafe "com_user1" "grp_com" "Password123!"

Write-Host "`n==========================================" -ForegroundColor Cyan
Write-Host "Setup Complete!" -ForegroundColor Cyan
Write-Host "You can now log into Windows with these users."
Write-Host "=========================================="
