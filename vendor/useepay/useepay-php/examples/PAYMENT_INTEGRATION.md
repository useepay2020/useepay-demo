# 支付集成说明 / Payment Integration Guide

## 概述 / Overview

本项目已集成 UseePay 支付网关，实现完整的电商支付流程。

## 文件说明 / Files

### 前端文件
- **clothing_shop.html** - 购物页面
- **checkout.html** - 结算页面（包含表单提交逻辑）
- **order_success.html** - 订单成功页面（显示支付结果）

### 后端文件
- **checkout_handler.php** - 结算处理器（处理支付请求）

## 支付流程 / Payment Flow

### 1. 用户购物
- 在 `clothing_shop.html` 浏览商品
- 添加商品到购物车
- 点击"结算"按钮

### 2. 填写信息
- 跳转到 `checkout.html`
- 表单已预填默认信息（可修改）：
  - 客户信息：John Smith, john.smith@example.com
  - 收货地址：1234 Elm Street, Los Angeles, CA 90001, USA
  - 电话：+1 (323) 555-0123

### 3. 提交支付
- 点击"确认并支付"按钮
- 前端验证表单数据
- 通过 AJAX 提交到 `checkout_handler.php`

### 4. 后端处理
`checkout_handler.php` 执行以下操作：
1. 接收并验证 JSON 数据
2. 初始化 UseePay 客户端
3. 创建支付意图（Payment Intent）
4. 返回支付结果

### 5. 显示结果
- 成功：跳转到 `order_success.html`
- 失败：显示错误提示，允许重试

## 技术实现 / Technical Implementation

### 前端提交（checkout.html）

```javascript
// 准备数据
const checkoutData = {
    firstName: data.firstName,
    lastName: data.lastName,
    email: data.email,
    address: data.address,
    city: data.city,
    state: data.state,
    zipCode: data.zipCode,
    country: data.country,
    phone: data.phone,
    paymentMethod: data.paymentMethod,
    items: cart,
    totals: totals
};

// 提交到后端
fetch('checkout_handler.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(checkoutData)
})
```

### 后端处理（checkout_handler.php）

```php
// 初始化 UseePay
$authentication = new Authentication(
    '500000000011101',
    'www.demo.com',
    'UseePay_SK_...'
);

$client = UseePayClient::withEnvironment(
    ApiEnvironment::SANDBOX,
    $authentication
);

// 创建支付意图
$paymentParams = array(
    'amount' => $totalAmount,  // 金额（美分）
    'currency' => 'USD',
    'description' => $description,
    'merchant_order_id' => $orderId,
    'metadata' => array(...)
);

$paymentIntent = $client->paymentIntents()->create($paymentParams);
```

## 数据结构 / Data Structure

### 提交到后端的数据
```json
{
    "firstName": "John",
    "lastName": "Smith",
    "email": "john.smith@example.com",
    "address": "1234 Elm Street, Apt 5B",
    "city": "Los Angeles",
    "state": "California",
    "zipCode": "90001",
    "country": "US",
    "phone": "+1 (323) 555-0123",
    "paymentMethod": "credit_card",
    "items": [
        {
            "id": 1,
            "name": "Classic White T-Shirt",
            "price": 18.99,
            "quantity": 2,
            "image": "👕"
        }
    ],
    "totals": {
        "subtotal": "37.98",
        "shipping": "9.99",
        "tax": "3.04",
        "total": "51.01"
    }
}
```

### 后端返回的数据
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

## UseePay 配置 / UseePay Configuration

### 环境 / Environment
- **Sandbox**: 测试环境（当前使用）
- **Production**: 生产环境

### 认证信息 / Authentication
```php
$authentication = new Authentication(
    '500000000011101',           // Merchant ID
    'www.demo.com',              // Domain
    'UseePay_SK_...'             // Secret Key
);
```

### 支付意图参数 / Payment Intent Parameters
- **amount**: 金额（美分，如 $50.00 = 5000）
- **currency**: 货币代码（USD）
- **description**: 订单描述
- **merchant_order_id**: 商户订单号（唯一）
- **metadata**: 元数据（客户信息、地址等）

## 测试说明 / Testing Instructions

### 前提条件
1. 确保 PHP 环境已配置
2. 已安装 Composer 依赖
3. UseePay SDK 已正确安装

### 测试步骤
1. 启动本地 Web 服务器：
   ```bash
   php -S localhost:8000 -t examples
   ```

2. 访问购物页面：
   ```
   http://localhost:8000/clothing_shop.html
   ```

3. 添加商品到购物车

4. 点击"结算"进入结算页面

5. 检查预填信息，点击"确认并支付"

6. 查看控制台和网络请求，确认：
   - 请求成功发送到 `checkout_handler.php`
   - 返回支付意图 ID
   - 跳转到成功页面

7. 在成功页面查看：
   - 订单号
   - 支付 ID
   - 支付状态
   - 完整订单信息

## 错误处理 / Error Handling

### 前端错误
- 表单验证失败：显示中英文提示
- 网络请求失败：显示错误消息，允许重试
- 按钮状态管理：防止重复提交

### 后端错误
- 数据验证失败：返回 400 错误和错误消息
- UseePay API 失败：捕获异常并返回错误信息
- 所有错误以 JSON 格式返回

## 安全注意事项 / Security Notes

### 开发环境
- SSL 验证已禁用（`UseePay::setVerifySslCerts(false)`）
- 仅用于测试

### 生产环境建议
1. **启用 SSL 验证**
   ```php
   UseePay::setVerifySslCerts(true);
   ```

2. **保护 API 密钥**
   - 使用环境变量
   - 不要提交到版本控制

3. **验证请求来源**
   - 添加 CSRF 令牌
   - 验证 HTTP Referer

4. **数据库存储**
   - 保存订单到数据库
   - 记录支付日志

5. **Webhook 处理**
   - 实现 webhook 接收器
   - 验证 webhook 签名
   - 更新订单状态

## 扩展功能 / Extended Features

### 可添加的功能
- [ ] 订单状态查询
- [ ] 支付结果 Webhook
- [ ] 退款功能
- [ ] 订单历史记录
- [ ] 数据库持久化
- [ ] 邮件通知
- [ ] 订单追踪
- [ ] 多种支付方式

## 参考文档 / References

- UseePay PHP SDK 文档
- PaymentExample.php - 支付示例
- CustomerExample.php - 客户管理示例

## 联系支持 / Support

如有问题，请参考：
- UseePay 官方文档
- SDK 源代码注释
- 示例代码
