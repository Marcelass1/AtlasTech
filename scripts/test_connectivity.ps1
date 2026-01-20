# ==========================================
# AtlasTech Solutions - Windows Connectivity Tester
# Run this script on your Windows Client VM
# ==========================================

$ErrorActionPreference = "SilentlyContinue"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "    AtlasTech Connectivity Tester" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

# 1. Get Server IP
$ServerIP = Read-Host "Enter the Ubuntu Server IP Address (e.g., 192.168.1.45)"
if ([string]::IsNullOrWhiteSpace($ServerIP)) {
    Write-Host "IP Address is required!" -ForegroundColor Red
    Exit
}

# 2. Test Ping
Write-Host "`n[1/3] Testing Connection (Ping)..." -ForegroundColor Yellow
if (Test-Connection -ComputerName $ServerIP -Count 2 -Quiet) {
    Write-Host "[OK] Server $ServerIP is reachable." -ForegroundColor Green
} else {
    Write-Host "[FAIL] Cannot reach $ServerIP. Check Network Bridge!" -ForegroundColor Red
    Write-Host "     Hint: Are both VMs on 'Bridged Adapter'?" -ForegroundColor Gray
    Exit
}

# 3. Test Web Access
Write-Host "`n[2/3] Testing Web Sites..." -ForegroundColor Yellow

# Function to check URL
function Test-Url ($Url, $Name) {
    try {
        $request = Invoke-WebRequest -Uri $Url -UseBasicParsing -TimeoutSec 5
        if ($request.StatusCode -eq 200) {
            Write-Host "[OK] $Name ($Url) is Accessible." -ForegroundColor Green
        } else {
            Write-Host "[WARN] $Name returned status $($request.StatusCode)" -ForegroundColor Yellow
        }
    } catch {
        # Check if it's a 403 Forbidden (which is expected for RH sometimes)
        if ($_.Exception.Response.StatusCode -eq 403) {
            Write-Host "[SECURE] $Name Access Denied (403). This is GOOD for restricted folders!" -ForegroundColor Green
        }
        elseif ($_.Exception.Response.StatusCode -eq 401) {
            Write-Host "[SECURE] $Name asks for Password (401). This is GOOD!" -ForegroundColor Green
        }
        else {
            Write-Host "[FAIL] Could not load $Name. Check Apache service." -ForegroundColor Red
            Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Gray
        }
    }
}

Test-Url "http://$ServerIP/commercial" "Commercial Site"
Test-Url "http://$ServerIP/rh" "RH Intranet"

# 4. Test SSH (Manual Prompt)
Write-Host "`n[3/3] Testing SSH Login..." -ForegroundColor Yellow
Write-Host "We will now try to connect as 'charlie_it' (IT Admin)."
Write-Host "Type 'yes' if asked for fingerprint, then enter password: Password123!" -ForegroundColor Gray
Write-Host "Type 'exit' to close the connection after success." -ForegroundColor Gray
Read-Host "Press Enter to start SSH..."

ssh charlie_it@$ServerIP

Write-Host "`n==========================================" -ForegroundColor Cyan
Write-Host "Test Complete." -ForegroundColor Cyan
