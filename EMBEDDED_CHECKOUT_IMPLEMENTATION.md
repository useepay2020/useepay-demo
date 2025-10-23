# UseePay 内嵌收银台实现文档

## 概述
本文档说明了如何使用 UseePay SDK 的 Elements 方式实现内嵌收银台（Embedded Checkout），替换原来的手动信用卡表单。

## 实现步骤

### 1. 在 embedded_checkout.php 中引入 UseePay SDK

```html
<!-- UseePay SDK -->
<script src="https://checkout-sdk.useepay.com/1.0.1/useepay.min.js"></script>
```

### 2. 更新支付方式生成逻辑

原来的信用卡表单：
```javascript
// 旧方式：手动表单
<input type="text" id="cardNumber" placeholder="1234 5678 9012 3456">
<input type="text" id="cardHolder" placeholder="As shown on card">
<input type="text" id="expiryDate" placeholder="MM/YY">
<input type="text" id="cvv" placeholder="123">
```

新方式：使用 UseePay Elements
```javascript
// 新方式：UseePay Payment Element
<div id="payment-element" style="margin: 20px 0;"></div>
<div id="payment-message" style="color: #d32f2f; margin-top: 10px; display: none;"></div>
```

### 3. 初始化 UseePay SDK

在 `embedded_checkout.js` 中添加全局变量：
```javascript
let useepayInstance = null;
let useepayElements = null;
let useepayPaymentElement = null;
```

### 4. 支付流程

#### 步骤 1: 创建支付意图 (createPaymentIntent)
- 验证用户输入的客户信息和收货地址
- 调用后端 `/api/payment` 接口创建支付意图
- 获取 `client_secret` 和 `paymentIntentId`
- 缓存支付意图到 sessionStorage

#### 步骤 2: 初始化 UseePay Elements (initializeUseepayElements)
- 使用公钥初始化 UseePay 实例
- 创建 Elements 对象，传入 clientSecret 和 paymentIntentId
- 创建 payment element
- 将 payment element 挂载到 DOM

#### 步骤 3: 确认支付 (confirmPaymentIntent)
- 调用 `useepayInstance.confirmPayment()` 方法
- 处理支付结果（成功、需要额外验证、失败）
- 重定向到结果页面

### 5. 核心函数说明

#### initializeUseepayElements(clientSecret, paymentIntentId)
初始化 UseePay Elements，用于信用卡支付。

**参数：**
- `clientSecret`: 支付意图的客户端密钥
- `paymentIntentId`: 支付意图 ID

**流程：**
1. 检查 UseePay SDK 是否加载
2. 初始化 UseePay 实例
3. 创建 Elements 对象
4. 创建 payment element
5. 挂载到 DOM

#### confirmPaymentIntent(paymentIntent)
确认支付意图。

**参数：**
- `paymentIntent`: 支付意图对象

**流程（信用卡）：**
1. 检查 Elements 是否已初始化
2. 调用 `useepayInstance.confirmPayment()`
3. 处理返回结果
4. 根据状态重定向或显示错误

#### handlePaymentSubmit()
支付提交处理函数，协调整个支付流程。

**流程：**
1. 检查是否已存在支付意图
2. 如果存在，直接确认支付
3. 如果不存在，创建新的支付意图

### 6. 支付状态处理

支付可能的状态：
- `succeeded`: 支付成功
- `requires_action`: 需要额外验证（如 3D Secure）
- `processing`: 处理中
- `failed`: 支付失败

### 7. 错误处理

- SDK 加载失败：显示错误提示
- 表单验证失败：显示相应的验证错误
- 支付确认失败：显示错误信息在 `payment-message` 元素中

### 8. 缓存管理

支付意图缓存在 sessionStorage 中，键为 `currentPaymentIntent`。

**相关函数：**
- `getPaymentIntentFromCache()`: 获取缓存的支付意图
- `clearPaymentIntentCache()`: 清除缓存
- `getPaymentIntentId()`: 获取支付意图 ID
- `getClientSecret()`: 获取客户端密钥

## 配置

### 公钥配置

在 HTML 中设置公钥（可选）：
```javascript
window.USEEPAY_PUBLIC_KEY = 'UseePay_PK_TEST_1234';
```

或在 JavaScript 中使用默认测试公钥：
```javascript
const publicKey = window.USEEPAY_PUBLIC_KEY || 'UseePay_PK_TEST_1234';
```

**注意：** UseePay SDK 会根据公钥前缀自动检测环境：
- `PK_TEST_****` → 沙箱环境
- `PK_****` → 生产环境

## 支持的支付方式

- 信用卡/借记卡（使用 UseePay Elements）
- Apple Pay
- Google Pay
- 微信支付
- 支付宝
- Afterpay
- Klarna
- OXXO

## 后端 API 要求

### POST /api/payment
创建支付意图

**请求体：**
```json
{
  "firstName": "John",
  "lastName": "Doe",
  "email": "john@example.com",
  "address": "123 Main St",
  "city": "New York",
  "state": "NY",
  "zipCode": "10001",
  "country": "US",
  "phone": "+1234567890",
  "paymentMethod": "card",
  "items": [...],
  "totals": {...}
}
```

**响应：**
```json
{
  "success": true,
  "data": {
    "id": "pi_1234567890",
    "client_secret": "pi_1234567890_secret_abcdef",
    "status": "requires_payment_method",
    ...
  }
}
```

### POST /api/payment/{id}/confirm
确认支付

**请求体：**
```json
{
  "payment_method": "card",
  "return_url": "https://example.com/payment/result"
}
```

## 测试

1. 访问 `/payment/embedded-checkout`
2. 填写客户信息和收货地址
3. 选择信用卡支付方式
4. 在 UseePay Payment Element 中输入测试卡号
5. 点击"确认并支付"按钮
6. 查看支付结果

### 测试卡号

使用 UseePay 提供的测试卡号进行测试。

## 浏览器控制台调试

所有关键步骤都有 console.log 输出，可在浏览器开发者工具中查看：

```
✓ Payment intent created and cached: pi_1234567890
✓ UseePay instance initialized
✓ UseePay Elements initialized
✓ Payment element created
✓ Payment element mounted
✓ Payment confirmed: {...}
✓ Payment succeeded
```

## 常见问题

### Q: 为什么 Payment Element 没有显示？
A: 检查以下几点：
1. UseePay SDK 是否正确加载
2. 是否选择了信用卡支付方式
3. 是否成功创建了支付意图
4. 浏览器控制台是否有错误信息

### Q: 支付确认失败怎么办？
A: 检查以下几点：
1. 支付意图是否有效
2. 卡号是否正确
3. 后端 `/api/payment/{id}/confirm` 接口是否正常
4. 查看浏览器控制台的错误信息

### Q: 如何处理 3D Secure 验证？
A: 当支付状态为 `requires_action` 时，SDK 会自动处理 3D Secure 验证。用户需要完成验证流程。

## 相关文件

- `embedded_checkout.php`: 内嵌收银台页面
- `embedded_checkout.js`: 内嵌收银台逻辑
- `PaymentController.php`: 后端支付处理
- `EMBEDDED_CHECKOUT.md`: UseePay SDK 集成指南

## 参考资源

- UseePay SDK 文档: https://checkout-sdk.useepay.com/
- UseePay API 文档: https://app.apifox.com/project/5337387
