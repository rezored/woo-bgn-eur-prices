# Version Update Script for WooCommerce BGN/EUR Plugin (PowerShell)
# Usage: .\update-version.ps1 1.4.8

param(
    [Parameter(Mandatory = $true)]
    [string]$NewVersion
)

# Validate version format
if ($NewVersion -notmatch '^\d+\.\d+\.\d+$') {
    Write-Host "Error: Version must be in format X.Y.Z (e.g., 1.4.8)" -ForegroundColor Red
    exit 1
}

$CurrentDate = Get-Date -Format "yyyy-MM-dd"

Write-Host "Updating version to: $NewVersion" -ForegroundColor Green

# Files to update
$files = @(
    'prices-in-bgn-and-eur.php',
    'README.md',
    'readme.txt'
)

foreach ($file in $files) {
    if (-not (Test-Path $file)) {
        Write-Host "Warning: File $file not found, skipping..." -ForegroundColor Yellow
        continue
    }
    
    Write-Host "Updating $file..." -ForegroundColor Cyan
    Update-File $file $NewVersion $CurrentDate
}

Write-Host "`nVersion update completed!" -ForegroundColor Green
Write-Host "Don't forget to:" -ForegroundColor Yellow
Write-Host "1. Test the plugin" -ForegroundColor White
Write-Host "2. Update changelog with specific changes" -ForegroundColor White
Write-Host "3. Commit changes to git" -ForegroundColor White

function Update-File {
    param($filename, $newVersion, $date)
    
    $content = Get-Content $filename -Raw
    
    if ($filename -eq 'prices-in-bgn-and-eur.php') {
        # Update plugin header version
        $content = $content -replace '\* Version: \d+\.\d+\.\d+', "* Version: $newVersion"
        
        # Update CSS/JS version numbers
        $content = $content -replace "'1\.\d+\.\d+'", "'$newVersion'"
        
        # Update admin page version
        $content = $content -replace 'Version \d+\.\d+\.\d+\:', "Version $newVersion`:"
        
    }
    elseif ($filename -eq 'README.md') {
        # Update version badges
        $content = $content -replace 'version-\d+\.\d+\.\d+', "version-$newVersion"
        
        # Add new changelog entry
        $changelogEntry = "`n### Version $newVersion`n- ✅ **Bug fixes and improvements** - Updated on $date`n"
        
        # Find the first version entry and add new one before it
        $content = $content -replace '(### Version \d+\.\d+\.\d+)', "$changelogEntry`$1"
        
    }
    elseif ($filename -eq 'readme.txt') {
        # Update WordPress readme version
        $content = $content -replace 'Stable tag: \d+\.\d+\.\d+', "Stable tag: $newVersion"
        
        # Update changelog
        $changelogEntry = "`n= $newVersion =`n* Bug fixes and improvements`n`n"
        $content = $content -replace '(=\d+\.\d+\.\d+=)', "$changelogEntry`$1"
    }
    
    Set-Content $filename $content -NoNewline
    Write-Host "  ✓ Updated $filename" -ForegroundColor Green
} 