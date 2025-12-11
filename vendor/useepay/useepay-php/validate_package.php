<?php
/**
 * Package Validation Script
 * Validates that the package is ready for publishing to Packagist
 */

echo "========================================\n";
echo "UseePay PHP SDK - Package Validation\n";
echo "========================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// Check composer.json
echo "Checking composer.json...\n";
if (!file_exists('composer.json')) {
    $errors[] = "composer.json not found";
} else {
    $composer = json_decode(file_get_contents('composer.json'), true);
    
    if (!$composer) {
        $errors[] = "composer.json is not valid JSON";
    } else {
        // Check required fields
        $requiredFields = ['name', 'description', 'type', 'license', 'authors', 'require', 'autoload'];
        foreach ($requiredFields as $field) {
            if (!isset($composer[$field])) {
                $errors[] = "composer.json missing required field: $field";
            } else {
                $success[] = "composer.json has field: $field";
            }
        }
        
        // Check package name format
        if (isset($composer['name']) && !preg_match('/^[a-z0-9-]+\/[a-z0-9-]+$/', $composer['name'])) {
            $errors[] = "Package name must be in format: vendor/package";
        } else if (isset($composer['name'])) {
            $success[] = "Package name format is correct: " . $composer['name'];
        }
        
        // Check license
        if (isset($composer['license'])) {
            $success[] = "License: " . $composer['license'];
        }
        
        // Check PHP version
        if (isset($composer['require']['php'])) {
            $success[] = "PHP requirement: " . $composer['require']['php'];
        }
        
        // Check autoload
        if (isset($composer['autoload']['psr-4'])) {
            $success[] = "PSR-4 autoloading configured";
        }
    }
}

// Check README.md
echo "\nChecking README.md...\n";
if (!file_exists('README.md')) {
    $errors[] = "README.md not found";
} else {
    $readme = file_get_contents('README.md');
    if (strlen($readme) < 100) {
        $warnings[] = "README.md seems too short";
    } else {
        $success[] = "README.md exists and has content";
    }
    
    if (strpos($readme, 'composer require') !== false) {
        $success[] = "README.md includes installation instructions";
    } else {
        $warnings[] = "README.md should include 'composer require' instructions";
    }
}

// Check LICENSE
echo "\nChecking LICENSE...\n";
if (!file_exists('LICENSE')) {
    $errors[] = "LICENSE file not found";
} else {
    $success[] = "LICENSE file exists";
}

// Check CHANGELOG.md
echo "\nChecking CHANGELOG.md...\n";
if (!file_exists('CHANGELOG.md')) {
    $warnings[] = "CHANGELOG.md not found (recommended)";
} else {
    $success[] = "CHANGELOG.md exists";
}

// Check .gitignore
echo "\nChecking .gitignore...\n";
if (!file_exists('.gitignore')) {
    $warnings[] = ".gitignore not found";
} else {
    $gitignore = file_get_contents('.gitignore');
    if (strpos($gitignore, 'vendor') !== false) {
        $success[] = ".gitignore excludes vendor directory";
    } else {
        $warnings[] = ".gitignore should exclude vendor directory";
    }
}

// Check source directory
echo "\nChecking source directory...\n";
if (!is_dir('src')) {
    $errors[] = "src directory not found";
} else {
    $success[] = "src directory exists";
    
    // Count PHP files
    $phpFiles = glob('src/**/*.php');
    if (count($phpFiles) > 0) {
        $success[] = "Found " . count($phpFiles) . " PHP files in src/";
    } else {
        $errors[] = "No PHP files found in src/";
    }
}

// Check if git is initialized
echo "\nChecking Git repository...\n";
if (!is_dir('.git')) {
    $errors[] = "Git repository not initialized. Run: git init";
} else {
    $success[] = "Git repository initialized";
    
    // Check for git remote
    exec('git remote -v 2>&1', $remotes, $returnCode);
    if ($returnCode === 0 && !empty($remotes)) {
        $success[] = "Git remote configured";
        foreach ($remotes as $remote) {
            if (strpos($remote, 'github.com') !== false) {
                $success[] = "GitHub remote found: " . trim($remote);
            }
        }
    } else {
        $warnings[] = "No git remote configured. Add with: git remote add origin <url>";
    }
    
    // Check for git tags
    exec('git tag 2>&1', $tags, $returnCode);
    if ($returnCode === 0 && !empty($tags)) {
        $success[] = "Git tags found: " . implode(', ', $tags);
    } else {
        $warnings[] = "No git tags found. Create with: git tag -a v1.0.0 -m 'Release v1.0.0'";
    }
}

// Check PHP syntax
echo "\nChecking PHP syntax...\n";
$syntaxErrors = 0;
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('src', RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->getExtension() === 'php') {
        exec('php -l ' . escapeshellarg($file->getPathname()) . ' 2>&1', $output, $returnCode);
        if ($returnCode !== 0) {
            $errors[] = "Syntax error in: " . $file->getPathname();
            $syntaxErrors++;
        }
    }
}

if ($syntaxErrors === 0) {
    $success[] = "All PHP files have valid syntax";
}

// Print results
echo "\n========================================\n";
echo "VALIDATION RESULTS\n";
echo "========================================\n\n";

if (!empty($success)) {
    echo "✅ SUCCESS (" . count($success) . "):\n";
    foreach ($success as $msg) {
        echo "  ✓ $msg\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $msg) {
        echo "  ! $msg\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $msg) {
        echo "  ✗ $msg\n";
    }
    echo "\n";
}

// Final verdict
echo "========================================\n";
if (empty($errors)) {
    echo "✅ READY TO PUBLISH!\n";
    echo "========================================\n\n";
    echo "Next steps:\n";
    echo "1. Run: git add .\n";
    echo "2. Run: git commit -m 'Prepare for v1.0.0 release'\n";
    echo "3. Run: git tag -a v1.0.0 -m 'Release v1.0.0'\n";
    echo "4. Run: git push origin main --tags\n";
    echo "5. Submit to Packagist: https://packagist.org\n";
    exit(0);
} else {
    echo "❌ NOT READY - Please fix errors above\n";
    echo "========================================\n";
    exit(1);
}
