@echo off
REM UseePay PHP SDK Publishing Script for Windows

setlocal enabledelayedexpansion

echo ==========================================
echo UseePay PHP SDK Publishing Script
echo ==========================================
echo.

REM Check if version argument is provided
if "%1"=="" (
    echo Usage: publish.bat ^<version^>
    echo Example: publish.bat 1.0.0
    exit /b 1
)

set VERSION=%1
set TAG=v%VERSION%

echo Publishing version: %VERSION%
echo.

REM Confirm CHANGELOG update
set /p CHANGELOG="Have you updated CHANGELOG.md? (y/n): "
if /i not "%CHANGELOG%"=="y" (
    echo Please update CHANGELOG.md first
    exit /b 1
)

REM Check if git is initialized
if not exist .git (
    echo Git repository not initialized. Initializing...
    git init
    git add .
    git commit -m "Initial commit: UseePay PHP SDK v%VERSION%"
)

REM Check if remote exists
git remote | findstr /C:"origin" >nul
if errorlevel 1 (
    echo.
    echo No remote 'origin' found.
    echo Please add your GitHub repository:
    echo   git remote add origin https://github.com/useepay2020/useepay-php.git
    exit /b 1
)

REM Check for pending changes
git status --short | findstr /R "." >nul
if not errorlevel 1 (
    echo Committing pending changes...
    git add .
    git commit -m "Release version %VERSION%"
)

REM Push to main branch
echo Pushing to main branch...
git push origin main
if errorlevel 1 (
    echo Error pushing to main branch
    exit /b 1
)

REM Create and push tag
echo Creating tag %TAG%...
git tag -a "%TAG%" -m "Release version %VERSION%"

echo Pushing tag %TAG%...
git push origin "%TAG%"
if errorlevel 1 (
    echo Error pushing tag
    exit /b 1
)

echo.
echo ==========================================
echo ✅ Successfully published version %VERSION%
echo ==========================================
echo.
echo Next steps:
echo 1. Go to https://packagist.org
echo 2. Sign in with your GitHub account
echo 3. Submit your package: https://github.com/useepay2020/useepay-php
echo 4. Set up auto-update webhook (see PUBLISHING_GUIDE.md)
echo.
echo Your package will be available at:
echo https://packagist.org/packages/useepay/useepay-php
echo.

endlocal
