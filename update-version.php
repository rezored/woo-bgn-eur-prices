<?php
/**
 * Version Update Script for WooCommerce BGN/EUR Plugin
 * 
 * Usage: php update-version.php [new_version]
 * Example: php update-version.php 1.4.8
 */

if ($argc < 2) {
    echo "Usage: php update-version.php [new_version]\n";
    echo "Example: php update-version.php 1.4.8\n";
    exit(1);
}

$newVersion = $argv[1];
$currentDate = date('Y-m-d');

// Validate version format
if (!preg_match('/^\d+\.\d+\.\d+$/', $newVersion)) {
    echo "Error: Version must be in format X.Y.Z (e.g., 1.4.8)\n";
    exit(1);
}

echo "Updating version to: $newVersion\n";

// Files to update
$files = [
    'prices-in-bgn-and-eur.php',
    'README.md',
    'readme.txt'
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "Warning: File $file not found, skipping...\n";
        continue;
    }
    
    echo "Updating $file...\n";
    updateFile($file, $newVersion, $currentDate);
}

echo "\nVersion update completed!\n";
echo "Don't forget to:\n";
echo "1. Test the plugin\n";
echo "2. Update changelog with specific changes\n";
echo "3. Commit changes to git\n";

function updateFile($filename, $newVersion, $date) {
    $content = file_get_contents($filename);
    
    if ($filename === 'prices-in-bgn-and-eur.php') {
        // Update plugin header version
        $content = preg_replace(
            '/\* Version: \d+\.\d+\.\d+/',
            "* Version: $newVersion",
            $content
        );
        
        // Update CSS/JS version numbers
        $content = preg_replace(
            '/\'1\.\d+\.\d+\'/',
            "'$newVersion'",
            $content
        );
        
        // Update admin page version
        $content = preg_replace(
            '/Version \d+\.\d+\.\d+:/',
            "Version $newVersion:",
            $content
        );
        
    } elseif ($filename === 'README.md') {
        // Update version badges
        $content = preg_replace(
            '/version-\d+\.\d+\.\d+/',
            "version-$newVersion",
            $content
        );
        
        // Add new changelog entry (you'll need to manually add details)
        $changelogEntry = "\n### Version $newVersion\n- ✅ **Bug fixes and improvements** - Updated on $date\n";
        
        // Find the first version entry and add new one before it
        $content = preg_replace(
            '/(### Version \d+\.\d+\.\d+)/',
            "$changelogEntry$1",
            $content,
            1
        );
        
    } elseif ($filename === 'readme.txt') {
        // Update WordPress readme version
        $content = preg_replace(
            '/Stable tag: \d+\.\d+\.\d+/',
            "Stable tag: $newVersion",
            $content
        );
        
        // Update changelog
        $changelogEntry = "\n= $newVersion =\n* Bug fixes and improvements\n\n";
        $content = preg_replace(
            '/(=\d+\.\d+\.\d+=)/',
            "$changelogEntry$1",
            $content,
            1
        );
    }
    
    file_put_contents($filename, $content);
    echo "  ✓ Updated $filename\n";
}
?> 