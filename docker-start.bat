@echo off
REM Docker 启动脚本 (Windows) - 自动设置 UID/GID
REM 用于确保容器内的 stack 用户与宿主机用户权限一致

echo === UseePay Demo Docker Startup Script (Windows) ===
echo.

REM Windows 下使用默认 UID/GID (1000)
set HOST_UID=1000
set HOST_GID=1000

echo Setting HOST_UID=%HOST_UID%, HOST_GID=%HOST_GID%
echo.

REM 检查 Docker 是否运行
docker info >nul 2>&1
if errorlevel 1 (
    echo Error: Docker is not running. Please start Docker Desktop first.
    pause
    exit /b 1
)

REM 自动检测 docker-compose 命令
REM 优先使用 docker compose (新版)，回退到 docker-compose (旧版)
docker compose version >nul 2>&1
if errorlevel 0 (
    set "DOCKER_COMPOSE=docker compose"
    echo Using: docker compose (Docker Compose V2)
) else (
    docker-compose --version >nul 2>&1
    if errorlevel 0 (
        set "DOCKER_COMPOSE=docker-compose"
        echo Using: docker-compose (Docker Compose V1)
    ) else (
        echo Error: Neither 'docker compose' nor 'docker-compose' is available.
        echo Please install Docker Compose: https://docs.docker.com/compose/install/
        pause
        exit /b 1
    )
)

echo Docker Compose command: %DOCKER_COMPOSE%
echo.

REM 解析命令行参数
set ACTION=%1
if "%ACTION%"=="" set ACTION=up

if "%ACTION%"=="up" (
    echo Starting containers...
    %DOCKER_COMPOSE% up -d
) else if "%ACTION%"=="down" (
    echo Stopping containers...
    %DOCKER_COMPOSE% down
) else if "%ACTION%"=="restart" (
    echo Restarting containers...
    %DOCKER_COMPOSE% down
    %DOCKER_COMPOSE% up -d
) else if "%ACTION%"=="build" (
    echo Building and starting containers...
    %DOCKER_COMPOSE% down
    %DOCKER_COMPOSE% build --no-cache
    %DOCKER_COMPOSE% up -d
) else if "%ACTION%"=="logs" (
    echo Showing logs...
    %DOCKER_COMPOSE% logs -f
) else if "%ACTION%"=="shell" (
    echo Opening shell as stack user...
    docker exec -it -u stack useepay-demo bash
) else if "%ACTION%"=="root-shell" (
    echo Opening shell as root user...
    docker exec -it useepay-demo bash
) else if "%ACTION%"=="status" (
    echo Container status:
    %DOCKER_COMPOSE% ps
    echo.
    echo Checking file permissions...
    docker exec -it useepay-demo ls -la /var/www/html
) else (
    echo Usage: %0 {up^|down^|restart^|build^|logs^|shell^|root-shell^|status}
    echo.
    echo Commands:
    echo   up          - Start containers
    echo   down        - Stop containers
    echo   restart     - Restart containers
    echo   build       - Rebuild and start containers
    echo   logs        - Show container logs
    echo   shell       - Open shell as stack user
    echo   root-shell  - Open shell as root user
    echo   status      - Show container status and file permissions
    pause
    exit /b 1
)

echo.
echo Done!
pause
