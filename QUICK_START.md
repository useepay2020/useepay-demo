# 快速开始指南

## 5分钟快速部署

### 架构说明 ✨

本项目使用 **单容器架构**，将 PHP-FPM 和 Nginx 合并到一个容器中：
- 🚀 更简单的部署
- 📦 更少的资源占用
- 🔌 统一端口：**9115**

### 1. 准备环境变量

```bash
cp .env.example .env
```

编辑 `.env` 文件，填入你的 UseePay API 凭证：

```env
USEEPAY_PUBLIC_API_KEY=your_public_key
USEEPAY_PRIVATE_API_KEY=your_private_key
USEEPAY_MERCHANT_NO=your_merchant_no
USEEPAY_APP_ID=your_app_id
USEEPAY_CALLBACK_URL=http://localhost:9115/payment/callback
```

### 2. 启动容器

使用 Makefile（推荐）：

```bash
make init
```

或手动执行：

```bash
docker compose build
docker compose up -d
docker compose exec app composer install
```

### 3. 验证部署

```bash
# 查看容器状态
docker compose ps

# 访问应用
curl http://localhost:9115

# 查看日志
docker compose logs -f app
```

### 4. 访问应用

- **主页**：http://localhost:9115
- **定价页**：http://localhost:9115/subscription/pricing
- **结算页**：http://localhost:9115/payment/checkout

## 常用命令

| 命令 | 说明 |
|------|------|
| `make up` | 启动容器 |
| `make down` | 停止容器 |
| `make logs` | 查看应用日志 |
| `make shell` | 进入PHP容器 |
| `make restart` | 重启容器 |
| `make health-check` | 检查服务状态 |
| `make ps` | 查看容器状态 |

## 项目结构

```
useepay-demo/
├── public/              # Web根目录
├── src/                 # 应用源代码
├── config/              # 配置文件
├── docker/              # Docker配置
│   └── nginx/conf.d/    # Nginx配置
├── logs/                # 应用日志
├── Dockerfile           # PHP容器定义
├── docker-compose.yml   # 服务编排
├── Makefile             # 便捷命令
└── .env                 # 环境变量
```

## 访问应用

- **应用地址**: http://localhost:8000
- **API文档**: http://localhost:8000/api/docs

## 修改端口

编辑 `.env` 文件：

```env
NGINX_PORT=9000        # 改为9000
NGINX_SSL_PORT=9443    # 改为9443
```

然后重启：

```bash
make restart
```

## 进入容器

```bash
# 进入PHP容器
make shell

# 进入Nginx容器
make shell-nginx

# 在容器中运行命令
docker-compose exec app php -v
docker-compose exec app composer install
```

## 查看日志

```bash
# 查看应用日志
make logs

# 查看Nginx日志
make logs-nginx

# 实时查看所有日志
docker-compose logs -f
```

## 停止和清理

```bash
# 停止容器
make down

# 清理所有资源
make clean

# 重新构建
make rebuild
```

## 常见问题

### 端口被占用

```bash
# 查看占用8000端口的进程
netstat -an | grep 8000

# 修改 .env 中的 NGINX_PORT
```

### 容器无法启动

```bash
# 查看错误日志
docker-compose logs app

# 重新构建镜像
make rebuild
```

### 权限错误

```bash
# 修复文件权限
docker-compose exec app chown -R www-data:www-data /var/www/html
docker-compose exec app chmod -R 755 /var/www/html
```

## 更多信息

详见 [DOCKER_DEPLOYMENT.md](DOCKER_DEPLOYMENT.md)
