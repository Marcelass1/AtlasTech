# ==========================================
# AtlasTech Solutions - Windows Local User Setup
# Creates Representative Users using 'net user'
# RUN AS ADMINISTRATOR
# ==========================================

# 1. Check Administrator Privileges
$currentPrincipal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
if (-not $currentPrincipal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    Write-Host "ERROR: You must run this script as Administrator!" -ForegroundColor Red
    Write-Host "Right-click the script and select 'Run with PowerShell', then accept the Admin prompt." -ForegroundColor Yellow
    Read-Host "Press Enter to exit..."
    Exit
}

Write-Host "Starting Windows User Setup..." -ForegroundColor Cyan

# Function to run net user command
function Create-User-Net ($Username, $Group, $Password) {
    # Check if user exists
    $userCheck = Get-LocalUser -Name $Username -ErrorAction SilentlyContinue
    if ($userCheck) {
        Write-Host "User $Username already exists." -ForegroundColor Yellow
    }
    else {
        # Create User using net user
        Write-Host "Creating $Username..." -ForegroundColor Gray
        $proc = Start-Process "net" -ArgumentList "user $Username $Password /add /passwordchg:no /expires:never" -NoNewWindow -PassThru -Wait
        if ($proc.ExitCode -eq 0) {
            Write-Host "[OK] Created User: $Username" -ForegroundColor Green
        }
        else {
            Write-Host "[FAIL] Failed to create $Username" -ForegroundColor Red
        }
    }

    # Add to Group (Create group if missing)
    $groupCheck = Get-LocalGroup -Name $Group -ErrorAction SilentlyContinue
    if (-not $groupCheck) {
        Start-Process "net" -ArgumentList "localgroup $Group /add" -NoNewWindow -Wait
        Write-Host "[OK] Created Group: $Group" -ForegroundColor Green
    }
    
    # Add Member
    Start-Process "net" -ArgumentList "localgroup $Group $Username /add" -NoNewWindow -Wait
}

# --- Create Users ---
# Direction
Create-User-Net "ceo_user" "grp_ceo" "Password123!"

# IT
Create-User-Net "charlie_it" "grp_it" "Password123!"
# Add IT to Admins
Start-Process "net" -ArgumentList "localgroup Administrators charlie_it /add" -NoNewWindow -Wait
Write-Host "[INFO] Added charlie_it to Administrators" -ForegroundColor Magenta

# Dev
Create-User-Net "alice_dev" "grp_dev" "Password123!"

# RH
Create-User-Net "bob_rh" "grp_rh" "Password123!"

# Finance
Create-User-Net "finance_user1" "grp_finance" "Password123!"

# Commercial
Create-User-Net "com_user1" "grp_com" "Password123!"

# --- VERIFICATION ---
Write-Host "`n==========================================" -ForegroundColor Cyan
Write-Host "VERIFICATION LIST:" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
gwmi Win32_UserAccount -Filter "LocalAccount=True" | Select-Object Name, Description, Disabled | Format-Table -AutoSize

Write-Host "`nSetup Complete!" -ForegroundColor Green
Read-Host "Press Enter to close..."
