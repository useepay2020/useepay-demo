# Publishing Guide - UseePay PHP SDK

This guide explains how to publish the UseePay PHP SDK to Packagist so users can install it via Composer.

## Prerequisites

1. **GitHub Account**: You need a GitHub account
2. **Packagist Account**: Create an account at https://packagist.org
3. **Git Repository**: The code must be in a public GitHub repository

## Step-by-Step Publishing Process

### Step 1: Prepare the GitHub Repository

1. **Create a GitHub repository** (if not already created):
   - Go to https://github.com/useepay2020
   - Create a new repository named `useepay-php`
   - Make it **public** (required for free Packagist hosting)

2. **Initialize Git in your local project** (if not already done):
   ```bash
   cd D:\03Projects\cpp\sdk\useepay-php
   git init
   git add .
   git commit -m "Initial commit: UseePay PHP SDK v1.0.0"
   ```

3. **Connect to GitHub and push**:
   ```bash
   git remote add origin https://github.com/useepay2020/useepay-php.git
   git branch -M main
   git push -u origin main
   ```

### Step 2: Create a Git Tag for Version 1.0.0

Tags are important for Composer versioning:

```bash
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

### Step 3: Register on Packagist

1. **Go to Packagist**: https://packagist.org
2. **Sign in** with your GitHub account
3. **Submit your package**:
   - Click "Submit" in the top menu
   - Enter your repository URL: `https://github.com/useepay2020/useepay-php`
   - Click "Check"
   - If validation passes, click "Submit"

### Step 4: Set Up Auto-Update (Recommended)

To automatically update Packagist when you push to GitHub:

1. **In Packagist**:
   - Go to your package page
   - Click "Settings" or "Edit"
   - Copy the webhook URL

2. **In GitHub**:
   - Go to your repository settings
   - Click "Webhooks" → "Add webhook"
   - Paste the Packagist webhook URL
   - Content type: `application/json`
   - Select "Just the push event"
   - Click "Add webhook"

### Step 5: Verify Installation

Test that users can install your package:

```bash
composer require useepay/useepay-php
```

## Publishing Updates

When you want to release a new version:

1. **Update version in code** (if needed)
2. **Update CHANGELOG.md** with new changes
3. **Commit changes**:
   ```bash
   git add .
   git commit -m "Release version 1.1.0"
   git push origin main
   ```

4. **Create a new tag**:
   ```bash
   git tag -a v1.1.0 -m "Release version 1.1.0"
   git push origin v1.1.0
   ```

5. **Packagist will auto-update** (if webhook is configured)

## Version Numbering (Semantic Versioning)

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR** (1.x.x): Breaking changes
- **MINOR** (x.1.x): New features, backward compatible
- **PATCH** (x.x.1): Bug fixes, backward compatible

Examples:
- `v1.0.0` - Initial release
- `v1.0.1` - Bug fix
- `v1.1.0` - New feature
- `v2.0.0` - Breaking change

## Composer.json Best Practices

✅ **Already configured in your composer.json**:
- Package name: `useepay/useepay-php`
- Description with keywords
- License: Apache-2.0
- PHP version requirement: >=5.4.0
- PSR-4 autoloading
- Support links

## Pre-Publishing Checklist

- [x] `composer.json` is properly configured
- [x] `README.md` has installation and usage instructions
- [x] `LICENSE` file exists
- [x] `CHANGELOG.md` documents version history
- [x] `.gitignore` excludes vendor and logs
- [x] Code follows PSR-4 autoloading standard
- [x] All required PHP extensions are listed
- [ ] GitHub repository is public
- [ ] Git tag v1.0.0 is created
- [ ] Package is submitted to Packagist

## Testing Before Publishing

Test your package locally before publishing:

```bash
# In another project directory
mkdir test-useepay
cd test-useepay
composer init

# Add your local package for testing
composer config repositories.useepay path ../useepay-php
composer require useepay/useepay-php:@dev
```

## Common Issues and Solutions

### Issue: "Package not found"
**Solution**: Make sure the GitHub repository is public and the package name in `composer.json` matches what you're requiring.

### Issue: "Could not find package"
**Solution**: Wait a few minutes after submitting to Packagist. The indexing might take time.

### Issue: "Version constraint mismatch"
**Solution**: Make sure you've created and pushed a git tag (e.g., `v1.0.0`).

### Issue: Packagist not updating
**Solution**: 
1. Check if the webhook is configured correctly
2. Manually trigger update on Packagist package page
3. Verify the tag was pushed: `git push origin --tags`

## Support

If you encounter issues:
- **Packagist Support**: https://packagist.org/about
- **Composer Documentation**: https://getcomposer.org/doc/
- **GitHub Issues**: https://github.com/useepay2020/useepay-php/issues

## Quick Command Reference

```bash
# Initialize repository
git init
git add .
git commit -m "Initial commit"

# Connect to GitHub
git remote add origin https://github.com/useepay2020/useepay-php.git
git push -u origin main

# Create and push tag
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0

# View all tags
git tag -l

# Delete a tag (if needed)
git tag -d v1.0.0
git push origin :refs/tags/v1.0.0
```

## After Publishing

Once published, users can install your SDK with:

```bash
composer require useepay/useepay-php
```

Your package will be available at:
- **Packagist**: https://packagist.org/packages/useepay/useepay-php
- **GitHub**: https://github.com/useepay2020/useepay-php

---

**Ready to publish?** Follow the steps above and your SDK will be available to the PHP community! 🚀
