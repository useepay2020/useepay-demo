.PHONY: help build up down logs restart shell clean test

# 默认目标
.DEFAULT_GOAL := help

# 颜色定义
BLUE := \033[0;34m
GREEN := \033[0;32m
RED := \033[0;31m
NC := \033[0m # No Color

# 自动检测 docker-compose 命令
# 优先使用 docker compose (新版)，回退到 docker-compose (旧版)
DOCKER_COMPOSE := $(shell docker compose version > /dev/null 2>&1 && echo "docker compose" || echo "docker-compose")

help: ## 显示帮助信息
	@echo "$(BLUE)UseePay API Demo - Docker 命令$(NC)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "$(GREEN)%-20s$(NC) %s\n", $$1, $$2}'

build: ## 构建Docker镜像
	@echo "$(BLUE)构建Docker镜像...$(NC)"
	$(DOCKER_COMPOSE) build

up: ## 启动所有容器
	@echo "$(BLUE)启动容器...$(NC)"
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✓ 容器已启动$(NC)"
	@echo "应用地址: http://localhost:8000"

down: ## 停止并删除所有容器
	@echo "$(BLUE)停止容器...$(NC)"
	$(DOCKER_COMPOSE) down
	@echo "$(GREEN)✓ 容器已停止$(NC)"

restart: ## 重启所有容器
	@echo "$(BLUE)重启容器...$(NC)"
	$(DOCKER_COMPOSE) restart
	@echo "$(GREEN)✓ 容器已重启$(NC)"

logs: ## 查看应用日志
	$(DOCKER_COMPOSE) logs -f app

logs-nginx: ## 查看Nginx日志
	$(DOCKER_COMPOSE) logs -f nginx

ps: ## 查看容器状态
	$(DOCKER_COMPOSE) ps

shell: ## 进入PHP容器shell
	$(DOCKER_COMPOSE) exec app bash

shell-nginx: ## 进入Nginx容器shell
	$(DOCKER_COMPOSE) exec nginx sh

composer-install: ## 安装Composer依赖
	$(DOCKER_COMPOSE) exec app composer install

composer-update: ## 更新Composer依赖
	$(DOCKER_COMPOSE) exec app composer update

composer-require: ## 添加Composer包 (使用: make composer-require PKG=package/name)
	$(DOCKER_COMPOSE) exec app composer require $(PKG)

composer-remove: ## 移除Composer包 (使用: make composer-remove PKG=package/name)
	$(DOCKER_COMPOSE) exec app composer remove $(PKG)

test: ## 运行测试
	$(DOCKER_COMPOSE) exec app ./vendor/bin/phpunit

lint: ## 检查PHP代码风格
	$(DOCKER_COMPOSE) exec app ./vendor/bin/phpcs

fix: ## 修复PHP代码风格
	$(DOCKER_COMPOSE) exec app ./vendor/bin/phpcbf

clean: ## 清理Docker资源
	@echo "$(BLUE)清理Docker资源...$(NC)"
	$(DOCKER_COMPOSE) down -v
	docker system prune -f
	@echo "$(GREEN)✓ 清理完成$(NC)"

stats: ## 查看容器资源使用情况
	docker stats

prune: ## 清理未使用的Docker资源
	docker system prune -a

rebuild: ## 重新构建并启动容器
	@echo "$(BLUE)重新构建容器...$(NC)"
	$(DOCKER_COMPOSE) down
	$(DOCKER_COMPOSE) build --no-cache
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✓ 容器已重新构建并启动$(NC)"

health-check: ## 检查服务健康状态
	@echo "$(BLUE)检查服务健康状态...$(NC)"
	@echo "检查Nginx..	@curl -s http://localhost:9115 > /dev/null && echo "$(GREEN)✓ Nginx 正常$(NC)" || echo "$(RED)✗ Nginx 异常$(NC)"
	@echo "检查PHP-FPM..."
	@docker exec useepay-demo php -v > /dev/null 2>&1 && echo "$(GREEN)✓ PHP-FPM 正常$(NC)" || echo "$(RED)✗ PHP-FPM 异常$(NC)"

diagnose: ## 运行完整诊断（Windows使用PowerShell）
	@echo "$(BLUE)运行诊断脚本...$(NC)"
	@if command -v bash > /dev/null 2>&1; then \
		bash docker-diagnose.sh; \
	else \
		powershell -ExecutionPolicy Bypass -File docker-diagnose.ps1; \
	fi

logs-nginx-error: ## 查看Nginx错误日志
	@docker exec useepay-demo tail -50 /var/log/nginx/error.log

logs-nginx-access: ## 查看Nginx访问日志
	@docker exec useepay-demo tail -50 /var/log/nginx/access.log

test-routes: ## 测试主要路由
	@echo "$(BLUE)测试路由访问...$(NC)"
	@echo "测试首页:"
	@curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" http://localhost:9115/
	@echo "测试 clothing-shop:"
	@curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" http://localhost:9115/payment/clothing-shop
	@echo "测试 pricing:"
	@curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" http://localhost:9115/subscription/pricing

nginx-test: ## 测试Nginx配置
	@docker exec useepay-demo nginx -t

supervisor-status: ## 查看Supervisor服务状态
	@docker exec useepay-demo supervisorctl status

supervisor-restart: ## 重启Supervisor所有服务
	@docker exec useepay-demo supervisorctl restart all

info: ## 显示项目信息
	@echo "$(BLUE)UseePay API Demo - 项目信息$(NC)"
	@echo ""
	@echo "$(GREEN)服务信息:$(NC)"
	@$(DOCKER_COMPOSE) ps
	@echo ""
	@echo "$(GREEN)网络信息:$(NC)"
	@docker network ls | grep useepay
	@echo ""
	@echo "$(GREEN)卷信息:$(NC)"
	@docker volume ls | grep useepay

version: ## 显示版本信息
	@echo "$(BLUE)版本信息:$(NC)"
	@echo "Docker: $$(docker --version)"
	@echo "Docker Compose: $$($(DOCKER_COMPOSE) version)"
	@echo "PHP: $$($(DOCKER_COMPOSE) exec -T app php --version | head -1)"
	@echo "Nginx: $$($(DOCKER_COMPOSE) exec -T nginx nginx -v 2>&1)"

init: ## 初始化项目（首次运行）
	@echo "$(BLUE)初始化项目...$(NC)"
	@if [ ! -f .env ]; then cp .env.example .env; echo "$(GREEN)✓ 已创建 .env 文件$(NC)"; fi
	@make build
	@make up
	@make composer-install
	@echo "$(GREEN)✓ 项目初始化完成$(NC)"
	@echo "应用地址: http://localhost:9115"
