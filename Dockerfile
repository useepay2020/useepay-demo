# 使用官方PHP 8.1 FPM镜像作为基础镜像
FROM php:8.1-fpm

# 设置工作目录
WORKDIR /var/www/html

# 安装系统依赖和 Nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    wget \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    nginx \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# 安装PHP扩展
RUN docker-php-ext-install \
    zip \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    curl

# 安装Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 复制项目文件到容器
COPY . .

# 安装PHP依赖
RUN composer install --no-dev --optimize-autoloader

# 复制 Nginx 配置
COPY docker/nginx/conf.d/default.conf /etc/nginx/sites-available/default

# 创建 Nginx 配置软链接
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# 复制 Supervisor 配置
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 设置文件权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/logs \
    && chmod -R 775 /var/www/html/logs \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R www-data:www-data /var/lib/nginx

# 暴露端口
EXPOSE 9115

# 使用 Supervisor 启动 PHP-FPM 和 Nginx
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
