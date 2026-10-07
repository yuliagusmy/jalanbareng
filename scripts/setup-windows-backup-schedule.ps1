# setup-windows-backup-schedule.ps1
# Setup Windows Task Scheduler untuk auto-backup Railway database

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "  Setup Automatic Railway Backup (Windows)" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Get script directory
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$BackupScript = Join-Path $ScriptDir "backup-railway-local.bat"

# Check if backup script exists
if (-not (Test-Path $BackupScript)) {
    Write-Host "[ERROR] Backup script not found: $BackupScript" -ForegroundColor Red
    Write-Host "Please make sure backup-railway-local.bat exists in the scripts folder." -ForegroundColor Red
    pause
    exit 1
}

Write-Host "Backup script found: $BackupScript" -ForegroundColor Green
Write-Host ""

# Task configuration
$TaskName = "JalanBareng-Railway-Backup"
$TaskDescription = "Automatic backup Railway database for Jalan Bareng project"

# Check if task already exists
$ExistingTask = Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue

if ($ExistingTask) {
    Write-Host "Task '$TaskName' already exists!" -ForegroundColor Yellow
    Write-Host ""
    $Response = Read-Host "Do you want to replace it? (y/n)"
    
    if ($Response -ne "y" -and $Response -ne "Y") {
        Write-Host "Cancelled." -ForegroundColor Yellow
        pause
        exit 0
    }
    
    Write-Host "Removing existing task..." -ForegroundColor Yellow
    Unregister-ScheduledTask -TaskName $TaskName -Confirm:$false
}

# Ask for schedule
Write-Host ""
Write-Host "Choose backup schedule:" -ForegroundColor Cyan
Write-Host "1. Daily at 2:00 AM (Recommended)"
Write-Host "2. Daily at specific time"
Write-Host "3. Weekly (Sunday at 2:00 AM)"
Write-Host "4. Custom (you configure manually later)"
Write-Host ""
$Choice = Read-Host "Enter your choice (1-4)"

$Trigger = $null

switch ($Choice) {
    "1" {
        # Daily at 2:00 AM
        $Trigger = New-ScheduledTaskTrigger -Daily -At 2:00AM
        Write-Host "Scheduled: Daily at 2:00 AM" -ForegroundColor Green
    }
    "2" {
        # Daily at custom time
        $Time = Read-Host "Enter time (format: HH:MM, e.g., 14:30)"
        try {
            $Trigger = New-ScheduledTaskTrigger -Daily -At $Time
            Write-Host "Scheduled: Daily at $Time" -ForegroundColor Green
        } catch {
            Write-Host "[ERROR] Invalid time format!" -ForegroundColor Red
            pause
            exit 1
        }
    }
    "3" {
        # Weekly on Sunday at 2:00 AM
        $Trigger = New-ScheduledTaskTrigger -Weekly -DaysOfWeek Sunday -At 2:00AM
        Write-Host "Scheduled: Weekly on Sunday at 2:00 AM" -ForegroundColor Green
    }
    "4" {
        # Create task without trigger (manual configuration)
        Write-Host "Task will be created without schedule. Configure manually in Task Scheduler." -ForegroundColor Yellow
    }
    default {
        Write-Host "[ERROR] Invalid choice!" -ForegroundColor Red
        pause
        exit 1
    }
}

# Create action
$Action = New-ScheduledTaskAction -Execute $BackupScript -WorkingDirectory $ScriptDir

# Create task settings
$Settings = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -StartWhenAvailable `
    -RunOnlyIfNetworkAvailable `
    -ExecutionTimeLimit (New-TimeSpan -Hours 2)

# Register task
Write-Host ""
Write-Host "Registering scheduled task..." -ForegroundColor Cyan

try {
    if ($Trigger) {
        Register-ScheduledTask `
            -TaskName $TaskName `
            -Description $TaskDescription `
            -Action $Action `
            -Trigger $Trigger `
            -Settings $Settings `
            -RunLevel Highest `
            -Force | Out-Null
    } else {
        Register-ScheduledTask `
            -TaskName $TaskName `
            -Description $TaskDescription `
            -Action $Action `
            -Settings $Settings `
            -RunLevel Highest `
            -Force | Out-Null
    }
    
    Write-Host ""
    Write-Host "================================================" -ForegroundColor Green
    Write-Host "  Setup Completed Successfully!" -ForegroundColor Green
    Write-Host "================================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Task Name: $TaskName" -ForegroundColor Cyan
    Write-Host "Backup Script: $BackupScript" -ForegroundColor Cyan
    Write-Host "Backup Location: $env:USERPROFILE\backups\jalan-bareng\" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "To manage this task:" -ForegroundColor Yellow
    Write-Host "1. Open Task Scheduler (search 'Task Scheduler' in Start Menu)"
    Write-Host "2. Find '$TaskName' in Task Scheduler Library"
    Write-Host "3. Right-click to Run, Disable, or Edit"
    Write-Host ""
    Write-Host "To test backup now, run:" -ForegroundColor Yellow
    Write-Host "  .\backup-railway-local.bat" -ForegroundColor Cyan
    Write-Host ""
    
} catch {
    Write-Host ""
    Write-Host "[ERROR] Failed to register scheduled task!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host ""
    Write-Host "Try running PowerShell as Administrator." -ForegroundColor Yellow
    pause
    exit 1
}

# Ask if user wants to test backup now
Write-Host ""
$TestNow = Read-Host "Do you want to test backup now? (y/n)"

if ($TestNow -eq "y" -or $TestNow -eq "Y") {
    Write-Host ""
    Write-Host "Running backup test..." -ForegroundColor Cyan
    & $BackupScript
}

Write-Host ""
Write-Host "Done! Press any key to exit..." -ForegroundColor Green
pause
