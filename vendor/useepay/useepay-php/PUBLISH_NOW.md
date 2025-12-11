# 🚀 立即发布 UseePay PHP SDK 到 Packagist

## ✅ 准备工作已完成

您的 SDK 已经准备好发布！以下文件已配置完成：

- ✅ `composer.json` - 包配置文件
- ✅ `README.md` - 详细的文档和使用说明
- ✅ `LICENSE` - Apache 2.0 许可证
- ✅ `CHANGELOG.md` - 版本更新日志
- ✅ `.gitignore` - Git 忽略配置
- ✅ 源代码结构符合 PSR-4 标准

## 📋 发布步骤（5分钟完成）

### 第 1 步：初始化 Git 仓库

在项目目录下打开命令行，执行：

```bash
cd D:\03Projects\cpp\sdk\useepay-php

# 初始化 Git
git init

# 添加所有文件
git add .

# 提交
git commit -m "Initial commit: UseePay PHP SDK v1.0.0"
```

### 第 2 步：创建 GitHub 仓库

1. 访问 https://github.com/useepay2020
2. 点击 "New repository"
3. 仓库名称：`useepay-php`
4. 描述：`Official UseePay PHP SDK for payment processing`
5. 选择 **Public**（公开仓库，Packagist 免费托管要求）
6. **不要**勾选 "Initialize this repository with a README"
7. 点击 "Create repository"

### 第 3 步：推送代码到 GitHub

```bash
# 添加远程仓库
git remote add origin https://github.com/useepay2020/useepay-php.git

# 推送到 main 分支
git branch -M main
git push -u origin main

# 创建版本标签
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

### 第 4 步：提交到 Packagist

1. 访问 https://packagist.org
2. 点击右上角 "Sign in with GitHub"
3. 授权 Packagist 访问您的 GitHub
4. 点击顶部菜单的 "Submit"
5. 输入仓库 URL：`https://github.com/useepay2020/useepay-php`
6. 点击 "Check" 按钮验证
7. 如果验证通过，点击 "Submit" 提交

### 第 5 步：配置自动更新（可选但推荐）

**在 Packagist：**
1. 进入您的包页面：https://packagist.org/packages/useepay/useepay-php
2. 点击右侧的 "Settings" 或 "Edit"
3. 找到并复制 "Webhook URL"（类似：`https://packagist.org/api/github?username=xxx`）

**在 GitHub：**
1. 进入仓库：https://github.com/useepay2020/useepay-php
2. 点击 "Settings" → "Webhooks" → "Add webhook"
3. 粘贴 Packagist 的 Webhook URL
4. Content type 选择：`application/json`
5. 选择 "Just the push event"
6. 勾选 "Active"
7. 点击 "Add webhook"

## ✅ 完成！

发布完成后，用户可以通过以下命令安装：

```bash
composer require useepay/useepay-php
```

您的包将在以下位置可见：
- **Packagist**: https://packagist.org/packages/useepay/useepay-php
- **GitHub**: https://github.com/useepay2020/useepay-php

## 🔄 发布新版本

当需要发布更新时：

```bash
# 1. 更新代码和 CHANGELOG.md

# 2. 提交更改
git add .
git commit -m "Release version 1.1.0"
git push origin main

# 3. 创建新标签
git tag -a v1.1.0 -m "Release version 1.1.0"
git push origin v1.1.0

# 4. Packagist 会自动更新（如果配置了 webhook）
```

或者使用提供的脚本：

**Windows:**
```bash
publish.bat 1.1.0
```

**Linux/Mac:**
```bash
chmod +x publish.sh
./publish.sh 1.1.0
```

## 📊 验证发布状态

运行验证脚本检查包状态：

```bash
php validate_package.php
```

## 🆘 遇到问题？

### 问题 1：Git 推送失败
**解决方案：** 确保您有 GitHub 仓库的写权限，可能需要配置 SSH 密钥或使用个人访问令牌。

### 问题 2：Packagist 提交失败
**解决方案：** 
- 确保 GitHub 仓库是公开的
- 确保 `composer.json` 格式正确
- 等待几分钟后重试

### 问题 3：包无法安装
**解决方案：**
- 确保至少有一个 git tag（如 v1.0.0）
- 等待 Packagist 索引完成（可能需要几分钟）
- 手动触发 Packagist 更新

## 📚 相关文档

- [PUBLISHING_GUIDE.md](PUBLISHING_GUIDE.md) - 详细发布指南
- [README.md](README.md) - SDK 使用文档
- [CHANGELOG.md](CHANGELOG.md) - 版本更新历史

## 🎉 恭喜！

完成以上步骤后，您的 UseePay PHP SDK 将对全球 PHP 开发者开放！

---

**需要帮助？** 联系 technology@useepay.com
