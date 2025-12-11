#!/bin/bash
# UseePay PHP SDK Publishing Script

set -e

echo "=========================================="
echo "UseePay PHP SDK Publishing Script"
echo "=========================================="
echo ""

# Check if version argument is provided
if [ -z "$1" ]; then
    echo "Usage: ./publish.sh <version>"
    echo "Example: ./publish.sh 1.0.0"
    exit 1
fi

VERSION=$1
TAG="v${VERSION}"

echo "Publishing version: ${VERSION}"
echo ""

# Confirm
read -p "Have you updated CHANGELOG.md? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Please update CHANGELOG.md first"
    exit 1
fi

# Check if git is initialized
if [ ! -d .git ]; then
    echo "Git repository not initialized. Initializing..."
    git init
    git add .
    git commit -m "Initial commit: UseePay PHP SDK v${VERSION}"
fi

# Check if remote exists
if ! git remote | grep -q origin; then
    echo ""
    echo "No remote 'origin' found."
    echo "Please add your GitHub repository:"
    echo "  git remote add origin https://github.com/useepay2020/useepay-php.git"
    exit 1
fi

# Commit any pending changes
if [[ -n $(git status -s) ]]; then
    echo "Committing pending changes..."
    git add .
    git commit -m "Release version ${VERSION}"
fi

# Push to main branch
echo "Pushing to main branch..."
git push origin main

# Create and push tag
echo "Creating tag ${TAG}..."
git tag -a "${TAG}" -m "Release version ${VERSION}"

echo "Pushing tag ${TAG}..."
git push origin "${TAG}"

echo ""
echo "=========================================="
echo "✅ Successfully published version ${VERSION}"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Go to https://packagist.org"
echo "2. Sign in with your GitHub account"
echo "3. Submit your package: https://github.com/useepay2020/useepay-php"
echo "4. Set up auto-update webhook (see PUBLISHING_GUIDE.md)"
echo ""
echo "Your package will be available at:"
echo "https://packagist.org/packages/useepay/useepay-php"
echo ""
