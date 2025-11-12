#!/bin/bash

# Docker 启动脚本 - 自动获取宿主机 UID/GID
# 用于确保容器内的 stack 用户与宿主机用户权限一致

set -e

echo "=== UseePay Demo Docker Startup Script ==="
echo ""

# 检测操作系统
OS="$(uname -s)"
case "${OS}" in
    Linux*)     MACHINE=Linux;;
    Darwin*)    MACHINE=Mac;;
    CYGWIN*)    MACHINE=Cygwin;;
    MINGW*)     MACHINE=MinGw;;
    MSYS*)      MACHINE=Git;;
    *)          MACHINE="UNKNOWN:${OS}"
esac

echo "Detected OS: $MACHINE"

# 获取当前用户的 UID 和 GID
if [ "$MACHINE" = "Linux" ] || [ "$MACHINE" = "Mac" ]; then
    # Linux 或 Mac
    CURRENT_UID=$(id -u)
    CURRENT_GID=$(id -g)
    CURRENT_USER=$(whoami)
    
    echo "Current user: $CURRENT_USER"
    echo "Current UID: $CURRENT_UID"
    echo "Current GID: $CURRENT_GID"
    
    # 导出环境变量
    export HOST_UID=$CURRENT_UID
    export HOST_GID=$CURRENT_GID
    
elif [ "$MACHINE" = "Cygwin" ] || [ "$MACHINE" = "MinGw" ] || [ "$MACHINE" = "Git" ]; then
    # Windows (Git Bash, MinGW, Cygwin)
    echo "Windows detected - using default UID/GID (1000)"
    export HOST_UID=1000
    export HOST_GID=1000
    
else
    # 未知系统，使用默认值
    echo "Unknown OS - using default UID/GID (1000)"
    export HOST_UID=1000
    export HOST_GID=1000
fi

echo ""
echo "Setting HOST_UID=$HOST_UID, HOST_GID=$HOST_GID"
echo ""

# 检查 Docker 是否运行
if ! docker info > /dev/null 2>&1; then
    echo "Error: Docker is not running. Please start Docker first."
    exit 1
fi

# 自动检测 docker-compose 命令
# 优先使用 docker compose (新版)，回退到 docker-compose (旧版)
if docker compose version > /dev/null 2>&1; then
    DOCKER_COMPOSE="docker compose"
    echo "Using: docker compose (Docker Compose V2)"
elif command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE="docker-compose"
    echo "Using: docker-compose (Docker Compose V1)"
else
    echo "Error: Neither 'docker compose' nor 'docker-compose' is available."
    echo "Please install Docker Compose: https://docs.docker.com/compose/install/"
    exit 1
fi

echo "Docker Compose command: $DOCKER_COMPOSE"
echo ""

# 解析命令行参数
ACTION=${1:-up}

case "$ACTION" in
    up)
        echo "Starting containers..."
        $DOCKER_COMPOSE up -d
        ;;
    down)
        echo "Stopping containers..."
        $DOCKER_COMPOSE down
        ;;
    restart)
        echo "Restarting containers..."
        $DOCKER_COMPOSE down
        $DOCKER_COMPOSE up -d
        ;;
    build)
        echo "Building and starting containers..."
        $DOCKER_COMPOSE down
        $DOCKER_COMPOSE build --no-cache
        $DOCKER_COMPOSE up -d
        ;;
    logs)
        echo "Showing logs..."
        $DOCKER_COMPOSE logs -f
        ;;
    shell)
        echo "Opening shell as stack user..."
        docker exec -it -u stack useepay-demo bash
        ;;
    root-shell)
        echo "Opening shell as root user..."
        docker exec -it useepay-demo bash
        ;;
    status)
        echo "Container status:"
        $DOCKER_COMPOSE ps
        echo ""
        echo "Checking file permissions..."
        docker exec -it useepay-demo ls -la /var/www/html | head -n 20
        ;;
    *)
        echo "Usage: $0 {up|down|restart|build|logs|shell|root-shell|status}"
        echo ""
        echo "Commands:"
        echo "  up          - Start containers"
        echo "  down        - Stop containers"
        echo "  restart     - Restart containers"
        echo "  build       - Rebuild and start containers"
        echo "  logs        - Show container logs"
        echo "  shell       - Open shell as stack user"
        echo "  root-shell  - Open shell as root user"
        echo "  status      - Show container status and file permissions"
        exit 1
        ;;
esac

echo ""
echo "Done!"
