# 快速开始 / Quick Start Guide

## 🚀 快速测试支付功能

### 1. 启动服务器

```bash
# Windows
cd D:\03Projects\cpp\sdk\useepay-php\examples
php -S localhost:8000

# Linux/Mac
cd /path/to/useepay-php/examples
php -S localhost:8000
```

### 2. 访问购物页面

打开浏览器访问：
```
http://localhost:8000/clothing_shop.html
```

### 3. 测试流程

#### 步骤 1：添加商品
- 浏览 8 个服装商品
- 点击"加入购物车"按钮
- 查看右上角购物车数量变化

#### 步骤 2：查看购物车
- 点击右上角"购物车"按钮
- 查看已添加的商品
- 可以调整数量或删除商品

#### 步骤 3：进入结算
- 点击"结算"按钮
- 自动跳转到结算页面

#### 步骤 4：检查信息
结算页面已预填以下信息（可修改）：

**客户信息：**
- First Name: John
- Last Name: Smith
- Email: john.smith@example.com

**收货地址：**
- Address: 1234 Elm Street, Apt 5B
- City: Los Angeles
- State: California
- ZIP Code: 90001
- Country: United States
- Phone: +1 (323) 555-0123

#### 步骤 5：提交支付
- 选择支付方式（信用卡或 PayPal）
- 点击"确认并支付"按钮
- 观察加载状态

#### 步骤 6：查看结果
- 自动跳转到订单成功页面
- 查看订单详情：
  - ✅ 订单号
  - ✅ 支付意图 ID
  - ✅ 支付状态
  - ✅ 完整订单信息

## 🔍 验证支付集成

### 检查后端响应

打开浏览器开发者工具（F12），查看 Network 标签：

1. 找到 `checkout_handler.php` 请求
2. 查看 Request Payload（发送的数据）
3. 查看 Response（返回的数据）

**成功响应示例：**
```json
{
    "success": true,
    "message": "Payment intent created successfully",
    "data": {
        "order_id": "ORD_1729138265_1234",
        "payment_intent_id": "pi_xxxxxxxxxxxxx",
        "amount": 5101,
        "currency": "USD",
        "status": "pending",
        "customer_name": "John Smith",
        "customer_email": "john.smith@example.com",
        "created_at": "2025-10-17 11:11:05"
    }
}
```

### 检查 Console 日志

在开发者工具的 Console 标签中，不应该看到任何错误。

## 🌐 测试语言切换

### 中英文切换
1. 点击右上角的 **EN** 按钮
2. 页面内容切换为英文
3. 再次点击 **中文** 按钮
4. 页面内容切换回中文

### 验证同步
1. 在购物页面切换语言
2. 进入结算页面
3. 语言设置应该保持一致

## 🛠️ 故障排除

### 问题 1：页面无法访问
**原因：** PHP 服务器未启动
**解决：** 
```bash
php -S localhost:8000
```

### 问题 2：提交支付时出错
**原因：** UseePay SDK 未安装或配置错误
**解决：**
```bash
cd D:\03Projects\cpp\sdk\useepay-php
composer install
```

### 问题 3：支付返回错误
**可能原因：**
- API 密钥错误
- 网络连接问题
- UseePay 服务不可用

**检查步骤：**
1. 查看 `checkout_handler.php` 中的认证信息
2. 检查网络连接
3. 查看 PHP 错误日志

### 问题 4：购物车数据丢失
**原因：** 浏览器 LocalStorage 被清除
**解决：** 重新添加商品到购物车

## 📊 测试数据

### 默认商品列表
1. 经典白色T恤 - $18.99
2. 修身牛仔裤 - $42.99
3. 连帽卫衣 - $36.99
4. 运动休闲裤 - $26.99
5. 针织开衫 - $56.99
6. 时尚风衣 - $99.99
7. 短袖衬衫 - $25.99
8. 休闲短裤 - $21.99

### 费用计算
- **商品小计：** 商品价格 × 数量
- **运费：** $9.99（固定）
- **税费：** 商品小计 × 8%
- **订单总计：** 商品小计 + 运费 + 税费

### 示例订单
假设购买：
- 经典白色T恤 × 2 = $37.98
- 修身牛仔裤 × 1 = $42.99

计算：
- 商品小计：$80.97
- 运费：$9.99
- 税费：$6.48
- **订单总计：$97.44**

## 🎯 下一步

### 查看详细文档
- **README_SHOP.md** - 完整功能说明
- **PAYMENT_INTEGRATION.md** - 支付集成详解
- **PaymentExample.php** - SDK 使用示例

### 扩展功能
- 添加更多商品
- 实现用户登录
- 添加订单历史
- 实现退款功能
- 集成邮件通知
- 添加订单追踪

## ✅ 测试清单

完成以下测试以验证系统功能：

- [ ] 添加商品到购物车
- [ ] 调整购物车商品数量
- [ ] 删除购物车商品
- [ ] 切换中英文语言
- [ ] 提交结算表单
- [ ] 验证表单验证（空字段、无效邮箱）
- [ ] 成功创建支付意图
- [ ] 查看订单成功页面
- [ ] 验证支付 ID 显示
- [ ] 打印订单功能
- [ ] 继续购物功能

## 📞 需要帮助？

如遇到问题，请检查：
1. PHP 版本（需要 5.3+）
2. Composer 依赖是否安装
3. UseePay SDK 是否正确配置
4. 浏览器控制台错误信息
5. PHP 错误日志

---

**祝测试顺利！🎉**
