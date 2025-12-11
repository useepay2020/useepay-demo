# 时尚服装商城 - 使用说明 / Fashion Store - User Guide

## 页面文件 / Page Files

### 前端页面
1. **clothing_shop.html** - 主购物页面（支持中英双语） / Main shopping page (Bilingual support)
2. **checkout.html** - 结算页面（支持中英双语） / Checkout page (Bilingual support)
3. **order_success.html** - 订单完成页面 / Order completion page

### 后端处理
4. **checkout_handler.php** - 结算处理器，集成 UseePay 支付 / Checkout handler with UseePay integration

## 🌐 语言切换功能 / Language Toggle Feature

- 两个页面都支持中文/英文切换
- 点击右上角的语言切换按钮（EN/中文）
- 语言偏好保存在浏览器 LocalStorage 中
- 切换语言后，所有文本内容自动更新
- 商品名称、分类、描述都会相应翻译

## 功能流程

### 1. 购物页面 (clothing_shop.html)
- 展示 8 个固定的服装商品
- 商品价格以美元显示
- 购物车功能（添加、删除、调整数量）
- 购物车数据保存在浏览器 LocalStorage
- 点击"结算"按钮跳转到结算页面

### 2. 结算页面 (checkout.html)
包含以下表单和信息：

#### 客户信息（预填默认值）
- First Name (名字): **John**
- Last Name (姓氏): **Smith**
- Email (电子邮箱): **john.smith@example.com**

#### 收货地址（预填美国地址）
- 详细地址: **1234 Elm Street, Apt 5B**
- 城市: **Los Angeles**
- 州/省: **California**
- 邮政编码: **90001**
- 国家: **United States** (默认选中)
- 联系电话: **+1 (323) 555-0123**

#### 支付方式
- 💳 信用卡/借记卡
- 🅿️ PayPal

#### 订单摘要（右侧栏）
- 显示购物车中的所有商品
- 商品小计
- 运费：$9.99
- 订单总计（自动计算）

#### 提交按钮
- 显示总金额
- 点击后验证表单
- 提交数据到 `checkout_handler.php`
- 调用 UseePay API 创建支付意图
- 显示加载动画
- 成功后跳转到订单完成页面

### 3. 订单完成页面 (order_success.html)
- ✓ 成功图标动画
- 订单详细信息
  - 订单号
  - **支付意图 ID**（来自 UseePay）
  - **支付状态**（pending/succeeded）
  - 下单时间
  - 客户信息
  - 收货地址
  - 支付方式
  - 订单商品列表
  - 价格明细
  - 订单总计
- 邮件通知提示
- "继续购物"按钮
- "打印订单"按钮

### 4. 后端处理 (checkout_handler.php)
- 接收前端提交的 JSON 数据
- 验证必填字段和数据格式
- 初始化 UseePay 客户端（Sandbox 环境）
- 创建支付意图（Payment Intent）
- 返回支付结果（订单号、支付ID、状态等）
- 错误处理和日志记录

## 数据存储

### LocalStorage 使用
1. **fashionCart** - 存储购物车数据
   - 在 clothing_shop.html 中创建和更新
   - 在 checkout.html 中读取
   - 在订单提交后清空

2. **lastOrder** - 存储最近一次订单
   - 在 checkout.html 收到后端响应后创建
   - 包含 UseePay 返回的支付意图 ID 和状态
   - 在 order_success.html 中读取和显示

3. **language** - 存储用户选择的语言
   - 'zh' 或 'en'
   - 在两个页面间同步

## 技术特点

### 前端技术
- HTML5 + CSS3 + JavaScript (ES6+)
- 响应式设计，支持移动端
- 现代化 UI 设计
- 渐变色和动画效果
- 表单验证
- 本地数据持久化（LocalStorage）
- Fetch API 异步请求

### 后端技术
- PHP 5.3+
- UseePay PHP SDK
- RESTful API 设计
- JSON 数据交互
- 异常处理和错误日志

## 使用方法

### 启动服务器
```bash
# 进入 examples 目录
cd D:\03Projects\cpp\sdk\useepay-php\examples

# 启动 PHP 内置服务器
php -S localhost:8000
```

### 操作流程
1. 访问 `http://localhost:8000/clothing_shop.html`
2. 浏览商品并添加到购物车
3. 点击右上角"购物车"查看已添加商品
4. 点击"结算"按钮进入结算页面
5. 检查预填的客户信息和收货地址（可修改）
6. 选择支付方式（信用卡或 PayPal）
7. 点击"确认并支付"按钮
8. 系统提交数据到后端，调用 UseePay API
9. 等待支付处理完成
10. 自动跳转到订单完成页面
11. 查看订单详情（包含支付 ID 和状态）
12. 可选择继续购物或打印订单

## 注意事项

### 开发环境
- 当前使用 UseePay **Sandbox 环境**（测试环境）
- SSL 证书验证已禁用（仅用于开发）
- 购物车数据存储在浏览器本地
- 需要 PHP 环境和 Composer 依赖

### 生产环境建议
- 切换到 UseePay **Production 环境**
- 启用 SSL 证书验证
- 使用环境变量保护 API 密钥
- 实现数据库持久化存储
- 添加 Webhook 处理订单状态更新
- 实现完整的错误处理和日志系统
- 添加用户认证和授权
- 实施安全措施（CSRF、XSS 防护等）

## 相关文档

- **PAYMENT_INTEGRATION.md** - 详细的支付集成说明
- **PaymentExample.php** - UseePay SDK 使用示例
- **CustomerExample.php** - 客户管理示例
