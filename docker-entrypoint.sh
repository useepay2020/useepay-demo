#!/bin/bash

set -e

echo "Starting UseePay API Demo application..."

# 安装Composer依赖（如果使用volume挂载时需要）
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# 设置文件权限
echo "Setting file permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 644 /var/www/html/config/*.php 2>/dev/null || true
chmod 755 /var/www/html/config 2>/dev/null || true
chmod -R 775 /var/www/html/logs

# 确保日志目录存在
mkdir -p /var/log/supervisor
mkdir -p /var/www/html/logs

echo "Application is ready!"

# 启动 Supervisor（管理 PHP-FPM 和 Nginx）
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
