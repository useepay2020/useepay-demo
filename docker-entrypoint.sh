#!/bin/bash

set -e

echo "Starting UseePay API Demo application..."

# 安装Composer依赖
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# 设置文件权限
echo "Setting file permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 775 /var/www/html/logs

echo "Application is ready!"

# 启动PHP-FPM
exec php-fpm
