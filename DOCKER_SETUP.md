# Docker 单容器部署说明

## 架构

本项目使用 **单容器架构**，将 PHP-FPM 和 Nginx 合并到一个容器中：

```
┌─────────────────────────────────┐
│  useepay-demo (单容器)           │
│  ┌───────────────────────────┐  │
│  │  Supervisor               │  │
│  │  ├─ PHP-FPM  (:9000)      │  │
│  │  └─ Nginx    (:9115)      │  │
│  └───────────────────────────┘  │
└─────────────────────────────────┘
         ↓
    Host :9115
```

## 关键配置

- **对外端口**：9115
- **Dockerfile**：`Dockerfile`（已合并单容器配置）
- **Nginx 配置**：`docker/nginx/conf.d/default.conf`
- **Supervisor 配置**：`docker/supervisor/supervisord.conf`

## 快速开始

```bash
# 1. 配置环境变量
cp .env.example .env

# 2. 构建并启动
make init

# 3. 访问应用
# http://localhost:9115
```

## 常用命令

```bash
# 构建镜像
make build

# 启动服务
make up

# 停止服务
make down

# 查看日志
make logs

# 进入容器
make shell

# 查看进程状态
docker compose exec app supervisorctl status
```

## 验证部署

```bash
# 检查容器状态
docker compose ps

# 检查进程
docker compose exec app supervisorctl status
# 应该看到：
# php-fpm    RUNNING
# nginx      RUNNING

# 测试访问
curl http://localhost:9115
```

## 文件结构

```
useepay-demo/
├── Dockerfile                              # 单容器 Dockerfile
├── docker-compose.yml                      # Docker Compose 配置
├── docker/
│   ├── nginx/
│   │   └── conf.d/
│   │       └── default.conf               # Nginx 配置（监听 9115）
│   └── supervisor/
│       └── supervisord.conf               # Supervisor 配置
└── .env                                    # 环境变量
```

## 故障排除

### 问题：容器启动失败

```bash
# 查看日志
docker compose logs -f

# 检查配置
docker compose config
```

### 问题：端口被占用

```bash
# Windows - 查找占用进程
netstat -ano | findstr :9115

# 终止进程
taskkill /PID <进程ID> /F
```

### 问题：PHP-FPM 或 Nginx 未运行

```bash
# 进入容器
docker compose exec app bash

# 查看进程状态
supervisorctl status

# 重启进程
supervisorctl restart php-fpm
supervisorctl restart nginx
```

## 访问地址

- **主页**：http://localhost:9115
- **定价页**：http://localhost:9115/subscription/pricing
- **结算页**：http://localhost:9115/payment/checkout
- **回调地址**：http://localhost:9115/payment/callback

## 优势

✅ **简化部署** - 只需管理一个容器
✅ **性能提升** - 本地通信，减少网络开销
✅ **资源优化** - 降低内存和 CPU 占用
✅ **易于维护** - 统一的日志和配置管理
