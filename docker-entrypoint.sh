#!/bin/bash

set -e

echo "Starting UseePay API Demo application..."

# 获取宿主机的 UID 和 GID（从环境变量）
HOST_UID=${HOST_UID:-1000}
HOST_GID=${HOST_GID:-1000}

echo "Host UID: $HOST_UID"
echo "Host GID: $HOST_GID"

# 检查 stack 用户是否存在，如果存在则修改 UID/GID
if id "stack" &>/dev/null; then
    echo "Updating stack user UID/GID to match host..."
    
    # 获取当前 stack 用户的 UID 和 GID
    CURRENT_UID=$(id -u stack)
    CURRENT_GID=$(id -g stack)
    
    echo "Current stack UID: $CURRENT_UID, GID: $CURRENT_GID"
    
    # 如果 UID 或 GID 不匹配，则修改
    if [ "$CURRENT_UID" != "$HOST_UID" ] || [ "$CURRENT_GID" != "$HOST_GID" ]; then
        echo "Modifying stack user to UID=$HOST_UID, GID=$HOST_GID..."
        
        # 修改组 GID
        groupmod -g $HOST_GID stack 2>/dev/null || true
        
        # 修改用户 UID
        usermod -u $HOST_UID stack 2>/dev/null || true
        
        echo "✓ Stack user updated"
    else
        echo "✓ Stack user UID/GID already matches host"
    fi
else
    echo "Creating stack user with UID=$HOST_UID, GID=$HOST_GID..."
    groupadd -g $HOST_GID stack
    useradd -u $HOST_UID -g stack -m -s /bin/bash stack
    usermod -aG www-data stack
    echo "✓ Stack user created"
fi

# 安装Composer依赖（如果使用volume挂载时需要）
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# 设置文件权限（使用 stack 用户）
echo "Setting file permissions for stack user..."
chown -R stack:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 664 /var/www/html/config/*.php 2>/dev/null || true
chmod 755 /var/www/html/config 2>/dev/null || true
chmod -R 775 /var/www/html/logs
chown -R stack:www-data /var/www/html/logs

# 确保日志目录存在
mkdir -p /var/log/supervisor
mkdir -p /var/www/html/logs

# 设置 src 和 public 目录权限，允许 stack 用户修改
echo "Setting permissions for src and public directories..."
chown -R stack:www-data /var/www/html/src
chown -R stack:www-data /var/www/html/public
chmod -R 775 /var/www/html/src
chmod -R 775 /var/www/html/public

echo "Application is ready!"
echo "File owner: stack:www-data"
echo "Permissions: 755 (directories), 664 (files)"

# 启动 Supervisor（管理 PHP-FPM 和 Nginx）
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
