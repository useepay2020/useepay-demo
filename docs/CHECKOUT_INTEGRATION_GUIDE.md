# UseePay 支付对接指南（商户端）

## 文档概述

本文档面向**电商网站开发者**，详细说明如何使用 UseePay PHP SDK 对接支付功能。文档涵盖**一次性支付**和**分期支付（订阅）**两种场景，重点说明商户端需要实现的功能，包括订单管理、支付接口调用、回调处理等。

---

## 目录

1. [前置准备](#前置准备)
2. [订单管理流程](#订单管理流程)
3. [一次性支付对接](#一次性支付对接)
4. [分期支付（订阅）对接](#分期支付订阅对接)
5. [支付回调处理](#支付回调处理)
6. [Webhook 通知处理](#webhook-通知处理)
7. [订单状态同步](#订单状态同步)
8. [错误处理与日志](#错误处理与日志)
9. [安全建议](#安全建议)

---

## 前置准备

### 1. 安装 UseePay PHP SDK

在项目的 `composer.json` 中添加依赖：

```json
{
    "require": {
        "useepay/useepay-php": "^1.0"
    }
}
```

执行安装命令：

```bash
composer install
```

### 2. 配置 UseePay 参数

在配置文件中添加 UseePay 相关配置：

```php
// config/useepay.php
return [
    'api_key' => 'your_secret_key',           // UseePay 提供的密钥
    'public_key' => 'your_public_key',        // UseePay 提供的公钥
    'api_base' => 'https://api.useepay.com',  // API 地址
    'callback_url' => 'https://yourdomain.com/payment/callback',  // 支付完成回调地址
    'webhook_url' => 'https://yourdomain.com/webhook/useepay'     // Webhook 通知地址
];
```

### 3. 初始化 SDK 客户端

```php
use UseePay\UseePayClient;

$client = new UseePayClient([
    'api_key' => $config['api_key'],
    'api_base' => $config['api_base']
]);
```

---

## 订单管理流程

### 核心原则：先落库，后支付

**重要：** 商户必须先将订单信息保存到自己的数据库中，然后再调用 UseePay 支付接口。这样可以确保：
- 订单数据完整性
- 支付失败时订单仍然存在
- 可以追溯订单历史
- 便于对账和数据分析

### 订单表结构设计建议

```sql
CREATE TABLE `orders` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_no` VARCHAR(64) UNIQUE NOT NULL COMMENT '商户订单号',
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
    `total_amount` DECIMAL(10,2) NOT NULL COMMENT '订单总金额',
    `currency` VARCHAR(3) DEFAULT 'USD' COMMENT '货币类型',
    `status` ENUM('pending', 'paid', 'failed', 'cancelled', 'refunded') DEFAULT 'pending' COMMENT '订单状态',
    `payment_intent_id` VARCHAR(128) NULL COMMENT 'UseePay 支付意图ID',
    `payment_method` VARCHAR(32) NULL COMMENT '支付方式',
    `paid_at` TIMESTAMP NULL COMMENT '支付完成时间',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_order_no` (`order_no`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_payment_intent_id` (`payment_intent_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

CREATE TABLE `order_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `quantity` INT UNSIGNED NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单明细表';
```

### 订单状态流转

```
pending (待支付)
    ↓
    ├─→ paid (已支付)
    ├─→ failed (支付失败)
    ├─→ cancelled (已取消)
    └─→ refunded (已退款)
```

---

## 一次性支付对接

### 业务流程图

```
用户选择商品并提交订单
    ↓
【商户】生成订单号
    ↓
【商户】订单数据落库（status = pending）
    ↓
【商户】调用 UseePay SDK 创建支付意图
    ↓
【UseePay】返回支付意图和收银台 URL
    ↓
【商户】更新订单表（保存 payment_intent_id）
    ↓
【商户】前端跳转到 UseePay 收银台
    ↓
【用户】在 UseePay 页面完成支付
    ↓
【UseePay】跳转回商户回调页面
    ↓
【商户】展示支付结果
    ↓
【UseePay】发送 Webhook 通知
    ↓
【商户】接收 Webhook，更新订单状态
```

### 商户端实现步骤

#### 步骤 1：生成订单并落库

**商户需要做的事情：**

1. 接收前端提交的订单数据
2. 验证订单数据（商品库存、价格等）
3. 生成唯一订单号
4. 将订单信息保存到数据库

```php
// 示例：OrderController.php
public function createOrder()
{
    // 1. 获取并验证订单数据
    $data = $this->getRequestData();
    $this->validateOrderData($data);
    
    // 2. 生成唯一订单号
    $orderNo = 'ORD_' . date('YmdHis') . '_' . rand(1000, 9999);
    
    // 3. 计算订单金额
    $totalAmount = $this->calculateTotalAmount($data['items']);
    
    // 4. 订单数据落库
    $orderId = DB::table('orders')->insertGetId([
        'order_no' => $orderNo,
        'user_id' => $this->getCurrentUserId(),
        'total_amount' => $totalAmount,
        'currency' => $data['currency'] ?? 'USD',
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // 5. 保存订单明细
    foreach ($data['items'] as $item) {
        DB::table('order_items')->insert([
            'order_id' => $orderId,
            'product_id' => $item['id'],
            'product_name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'subtotal' => $item['price'] * $item['quantity']
        ]);
    }
    
    // 6. 返回订单信息，准备调用支付接口
    return [
        'order_id' => $orderId,
        'order_no' => $orderNo,
        'total_amount' => $totalAmount
    ];
}
```

#### 步骤 2：调用 UseePay SDK 创建支付意图

**商户需要做的事情：**

1. 使用 UseePay SDK 创建支付意图
2. 将返回的 `payment_intent_id` 保存到订单表
3. 返回支付意图数据给前端

```php
// 示例：PaymentController.php
public function createPayment()
{
    // 1. 先创建订单并落库（调用上一步的方法）
    $orderInfo = $this->createOrder();
    
    // 2. 获取订单数据
    $data = $this->getRequestData();
    $config = $this->getConfig();
    
    // 3. 初始化 UseePay 客户端
    $client = new \UseePay\UseePayClient([
        'api_key' => $config['usee_pay']['api_key'],
        'api_base' => $config['usee_pay']['api_base']
    ]);
    
    // 4. 准备支付参数
    $paymentParams = [
        'amount' => $orderInfo['total_amount'],
        'currency' => $data['currency'] ?? 'USD',
        'description' => '订单号：' . $orderInfo['order_no'],
        'merchant_order_id' => $orderInfo['order_no'],  // 使用商户订单号
        'return_url' => $config['usee_pay']['callback_url'],
        'order' => [
            'products' => $this->formatOrderItems($data['items']),
            'shipping' => [
                'address' => [
                    'line1' => $data['shippingAddress']['address'],
                    'city' => $data['shippingAddress']['city'],
                    'state' => $data['shippingAddress']['state'],
                    'postcode' => $data['shippingAddress']['zipCode'],
                    'country' => $data['shippingAddress']['country']
                ],
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'email' => $data['email'],
                'phone' => $data['phone']
            ]
        ],
        'payment_method_data' => [
            'billing' => [
                'address' => [
                    'line1' => $data['billingAddress']['address'],
                    'city' => $data['billingAddress']['city'],
                    'state' => $data['billingAddress']['state'],
                    'postcode' => $data['billingAddress']['zipCode'],
                    'country' => $data['billingAddress']['country']
                ],
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'email' => $data['email'],
                'phone' => $data['phone']
            ]
        ]
    ];
    
    // 5. 可选：限制支付方式
    if (!empty($data['paymentMethods'])) {
        $paymentParams['payment_method_types'] = $data['paymentMethods'];
    }
    
    try {
        // 6. 调用 UseePay SDK 创建支付意图
        $paymentIntent = $client->paymentIntents()->create($paymentParams);
        
        // 7. 更新订单表，保存 payment_intent_id
        DB::table('orders')
            ->where('id', $orderInfo['order_id'])
            ->update([
                'payment_intent_id' => $paymentIntent['id'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        
        // 8. 记录日志
        $this->log('Payment Intent Created', 'info', [
            'order_no' => $orderInfo['order_no'],
            'payment_intent_id' => $paymentIntent['id'],
            'amount' => $orderInfo['total_amount']
        ]);
        
        // 9. 返回支付意图数据给前端
        return $this->jsonResponse([
            'success' => true,
            'data' => $paymentIntent
        ]);
        
    } catch (\Exception $e) {
        // 10. 异常处理：记录错误日志
        $this->log('Payment Intent Creation Failed', 'error', [
            'order_no' => $orderInfo['order_no'],
            'error' => $e->getMessage()
        ]);
        
        // 11. 更新订单状态为失败
        DB::table('orders')
            ->where('id', $orderInfo['order_id'])
            ->update(['status' => 'failed']);
        
        return $this->jsonResponse([
            'success' => false,
            'message' => '支付创建失败：' . $e->getMessage()
        ], 500);
    }
}

// 格式化订单商品数据
private function formatOrderItems($items)
{
    return array_map(function($item) {
        return [
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'description' => $item['description'] ?? ''
        ];
    }, $items);
}
```

**关键参数说明：**

| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| `amount` | float | 是 | 支付金额（从订单表获取） |
| `currency` | string | 是 | 货币类型（USD, CNY, EUR 等） |
| `merchant_order_id` | string | 是 | **商户订单号**（用于关联订单） |
| `return_url` | string | 是 | 支付完成后的回调 URL |
| `order.products` | array | 是 | 商品列表 |
| `order.shipping` | object | 否 | 收货地址信息 |
| `payment_method_types` | array | 否 | 限制可用的支付方式 |

**UseePay SDK 返回的数据结构：**

```json
{
    "id": "pi_1234567890",
    "object": "payment_intent",
    "amount": 114.99,
    "currency": "USD",
    "status": "requires_payment_method",
    "merchant_order_id": "ORD_20260128_5678",
    "client_secret": "pi_1234567890_secret_abcdefg",
    "next_action": {
        "type": "redirect",
        "redirect": {
            "method": "GET",
            "url": "https://checkout.useepay.com/pay?pi=pi_1234567890"
        }
    },
    "return_url": "https://yourdomain.com/payment/callback"
}
```

#### 步骤 3：前端跳转到 UseePay 收银台

**商户需要做的事情：**

前端接收到支付意图数据后，检查 `next_action.redirect.url`，然后跳转到 UseePay 收银台。

```javascript
// 前端代码示例
fetch('/api/payment', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(checkoutData)
})
.then(response => response.json())
.then(result => {
    if (result.success && result.data.next_action) {
        const redirectUrl = result.data.next_action.redirect.url;
        // 跳转到 UseePay 收银台
        window.location.href = redirectUrl;
    }
});
```

---

## 支付回调处理

### 用户支付完成后的处理

用户在 UseePay 收银台完成支付后，UseePay 会将用户重定向回商户配置的 `return_url`，并携带支付结果参数。

**商户需要做的事情：**

1. 接收回调参数
2. 根据 `merchant_order_id` 查询订单
3. 展示支付结果页面
4. **注意：不要在此处更新订单状态，应等待 Webhook 通知**

```php
// 示例：PaymentCallbackController.php
public function handleCallback()
{
    // 1. 获取回调参数
    $paymentIntentId = $_GET['id'] ?? '';
    $merchantOrderId = $_GET['merchant_order_id'] ?? '';
    $status = $_GET['status'] ?? '';
    
    // 2. 根据商户订单号查询订单
    $order = DB::table('orders')
        ->where('order_no', $merchantOrderId)
        ->first();
    
    if (!$order) {
        return $this->showError('订单不存在');
    }
    
    // 3. 记录回调日志
    $this->log('Payment Callback Received', 'info', [
        'payment_intent_id' => $paymentIntentId,
        'merchant_order_id' => $merchantOrderId,
        'status' => $status,
        'order_status' => $order->status
    ]);
    
    // 4. 展示支付结果页面
    if ($status === 'succeeded') {
        return $this->showSuccessPage($order);
    } elseif ($status === 'failed') {
        return $this->showFailedPage($order);
    } else {
        return $this->showPendingPage($order);
    }
}

private function showSuccessPage($order)
{
    // 展示支付成功页面
    // 注意：此时订单状态可能还是 pending，等待 Webhook 更新
    return view('payment/success', [
        'order' => $order,
        'message' => '支付处理中，请稍候...'
    ]);
}
```

**重要提示：**

- ⚠️ **不要依赖回调 URL 的参数来更新订单状态**
- ⚠️ **回调 URL 仅用于展示支付结果给用户**
- ✅ **订单状态的最终更新必须通过 Webhook 通知完成**

---

## Webhook 通知处理

### Webhook 的作用

Webhook 是 UseePay 服务器主动发送给商户服务器的异步通知，用于告知支付状态的变化。这是**更新订单状态的唯一可靠方式**。

### Webhook 事件类型

| 事件类型 | 说明 | 需要处理 |
|---------|------|---------|
| `payment_intent.succeeded` | 支付成功 | ✅ 是 |
| `payment_intent.payment_failed` | 支付失败 | ✅ 是 |
| `payment_intent.canceled` | 支付取消 | ✅ 是 |
| `subscription.created` | 订阅创建 | ✅ 是 |
| `subscription.updated` | 订阅更新 | 可选 |
| `invoice.payment_succeeded` | 订阅扣款成功 | ✅ 是 |

### 商户端实现 Webhook 处理

**商户需要做的事情：**

1. 创建 Webhook 接收接口
2. 验证 Webhook 签名（安全性）
3. 根据事件类型更新订单状态
4. 返回 200 状态码确认接收

```php
// 示例：WebhookController.php
public function handleWebhook()
{
    // 1. 获取原始请求体
    $payload = file_get_contents('php://input');
    $signature = $_SERVER['HTTP_USEEPAY_SIGNATURE'] ?? '';
    
    // 2. 验证签名（重要！）
    if (!$this->verifySignature($payload, $signature)) {
        $this->log('Webhook Signature Verification Failed', 'error', [
            'signature' => $signature
        ]);
        http_response_code(401);
        exit('Invalid signature');
    }
    
    // 3. 解析 Webhook 数据
    $event = json_decode($payload, true);
    
    // 4. 记录 Webhook 日志
    $this->log('Webhook Received', 'info', $event);
    
    // 5. 根据事件类型处理
    try {
        switch ($event['type']) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event['data']);
                break;
                
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event['data']);
                break;
                
            case 'payment_intent.canceled':
                $this->handlePaymentCanceled($event['data']);
                break;
                
            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event['data']);
                break;
                
            default:
                $this->log('Unknown Webhook Event', 'warning', [
                    'type' => $event['type']
                ]);
        }
        
        // 6. 返回 200 确认接收
        http_response_code(200);
        echo json_encode(['received' => true]);
        
    } catch (\Exception $e) {
        $this->log('Webhook Processing Error', 'error', [
            'error' => $e->getMessage(),
            'event' => $event
        ]);
        http_response_code(500);
    }
}

// 验证 Webhook 签名
private function verifySignature($payload, $signature)
{
    $config = $this->getConfig();
    $secret = $config['usee_pay']['webhook_secret'];
    
    $expectedSignature = hash_hmac('sha256', $payload, $secret);
    
    return hash_equals($expectedSignature, $signature);
}

// 处理支付成功
private function handlePaymentSucceeded($paymentIntent)
{
    $merchantOrderId = $paymentIntent['merchant_order_id'];
    
    // 查询订单
    $order = DB::table('orders')
        ->where('order_no', $merchantOrderId)
        ->first();
    
    if (!$order) {
        $this->log('Order Not Found', 'error', [
            'merchant_order_id' => $merchantOrderId
        ]);
        return;
    }
    
    // 防止重复处理
    if ($order->status === 'paid') {
        $this->log('Order Already Paid', 'warning', [
            'order_no' => $merchantOrderId
        ]);
        return;
    }
    
    // 更新订单状态
    DB::table('orders')
        ->where('id', $order->id)
        ->update([
            'status' => 'paid',
            'payment_method' => $paymentIntent['payment_method']['type'] ?? null,
            'paid_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    
    // 记录日志
    $this->log('Order Status Updated to Paid', 'info', [
        'order_no' => $merchantOrderId,
        'payment_intent_id' => $paymentIntent['id']
    ]);
    
    // 触发后续业务逻辑（如发货、发送邮件等）
    $this->triggerOrderPaidEvent($order);
}

// 处理支付失败
private function handlePaymentFailed($paymentIntent)
{
    $merchantOrderId = $paymentIntent['merchant_order_id'];
    
    DB::table('orders')
        ->where('order_no', $merchantOrderId)
        ->update([
            'status' => 'failed',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    
    $this->log('Order Status Updated to Failed', 'info', [
        'order_no' => $merchantOrderId
    ]);
}

// 处理支付取消
private function handlePaymentCanceled($paymentIntent)
{
    $merchantOrderId = $paymentIntent['merchant_order_id'];
    
    DB::table('orders')
        ->where('order_no', $merchantOrderId)
        ->update([
            'status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
}
```

### Webhook 配置

在 UseePay 后台配置 Webhook URL：

```
https://yourdomain.com/webhook/useepay
```

**Webhook 安全要点：**

- ✅ 必须验证签名，防止伪造请求
- ✅ 使用 `hash_equals()` 防止时序攻击
- ✅ 记录所有 Webhook 日志便于排查问题
- ✅ 返回 200 状态码告知 UseePay 已接收
- ✅ 实现幂等性，防止重复处理

---

## 分期支付（订阅）对接

### 订阅表结构设计

```sql
CREATE TABLE `subscriptions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `subscription_id` VARCHAR(128) UNIQUE NOT NULL COMMENT 'UseePay 订阅ID',
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
    `plan_name` VARCHAR(100) NOT NULL COMMENT '套餐名称',
    `amount` DECIMAL(10,2) NOT NULL COMMENT '每期金额',
    `currency` VARCHAR(3) DEFAULT 'USD',
    `interval` ENUM('day', 'week', 'month', 'year') NOT NULL COMMENT '计费周期',
    `interval_count` INT DEFAULT 1 COMMENT '周期数量',
    `status` ENUM('active', 'canceled', 'past_due', 'unpaid') DEFAULT 'active',
    `current_period_start` TIMESTAMP NULL,
    `current_period_end` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `canceled_at` TIMESTAMP NULL,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_subscription_id` (`subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订阅表';
```

### 商户端实现步骤

#### 步骤 1：创建订阅

**商户需要做的事情：**

1. 接收用户选择的套餐信息
2. 将订阅信息保存到数据库
3. 调用 UseePay SDK 创建订阅
4. 处理首次支付

```php
// 示例：SubscriptionController.php
public function createSubscription()
{
    $data = $this->getRequestData();
    
    // 1. 验证套餐信息
    $plan = $this->validatePlan($data['plan_name']);
    
    // 2. 生成订阅记录
    $subscriptionNo = 'SUB_' . date('YmdHis') . '_' . rand(1000, 9999);
    
    // 3. 订阅数据落库
    $subscriptionId = DB::table('subscriptions')->insertGetId([
        'user_id' => $this->getCurrentUserId(),
        'plan_name' => $plan['name'],
        'amount' => $plan['amount'],
        'currency' => $data['currency'] ?? 'USD',
        'interval' => $data['billing_type'] === 'monthly' ? 'month' : 'year',
        'interval_count' => 1,
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // 4. 初始化 UseePay 客户端
    $client = new \UseePay\UseePayClient([
        'api_key' => $config['usee_pay']['api_key']
    ]);
    
    // 5. 准备订阅参数
    $subscriptionParams = [
        'customer_id' => 'CUST_' . $this->getCurrentUserId(),
        'currency' => $data['currency'] ?? 'USD',
        'description' => $plan['name'] . ' - ' . $data['billing_type'],
        'recurring' => [
            'interval' => $data['billing_type'] === 'monthly' ? 'month' : 'year',
            'interval_count' => 1,
            'unit_amount' => $plan['amount'],
            'total_billing_cycles' => null  // null = 无限期
        ],
        'order' => [
            'products' => [[
                'name' => $plan['name'],
                'quantity' => 1,
                'price' => $plan['amount'],
                'currency' => $data['currency'] ?? 'USD'
            ]]
        ],
        'trial_period_days' => $plan['trial_days'] ?? null,
        'metadata' => [
            'subscription_no' => $subscriptionNo,
            'plan_name' => $plan['name']
        ]
    ]);
    
    try {
        // 6. 调用 UseePay SDK 创建订阅
        $subscription = $client->subscriptions()->create($subscriptionParams);
        
        // 7. 更新订阅表，保存 UseePay 返回的 subscription_id
        DB::table('subscriptions')
            ->where('id', $subscriptionId)
            ->update([
                'subscription_id' => $subscription['id'],
                'current_period_start' => date('Y-m-d H:i:s', $subscription['current_period_start']),
                'current_period_end' => date('Y-m-d H:i:s', $subscription['current_period_end'])
            ]);
        
        // 8. 处理首次支付
        // UseePay 会自动创建首次支付意图
        $paymentIntent = $subscription['latest_invoice']['payment_intent'] ?? null;
        
        if ($paymentIntent) {
            // 返回支付意图，前端跳转到收银台
            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'subscription' => $subscription,
                    'paymentIntent' => $paymentIntent
                ]
            ]);
        }
        
        // 如果有试用期，可能不需要立即支付
        return $this->jsonResponse([
            'success' => true,
            'data' => ['subscription' => $subscription]
        ]);
        
    } catch (\Exception $e) {
        $this->log('Subscription Creation Failed', 'error', [
            'error' => $e->getMessage()
        ]);
        
        return $this->jsonResponse([
            'success' => false,
            'message' => '订阅创建失败：' . $e->getMessage()
        ], 500);
    }
}
```

#### 步骤 2：处理订阅扣款 Webhook

**商户需要做的事情：**

订阅创建后，UseePay 会在每个计费周期自动扣款，并通过 Webhook 通知商户。

```php
// 在 WebhookController 中添加
private function handleInvoicePaymentSucceeded($invoice)
{
    $subscriptionId = $invoice['subscription_id'];
    
    // 查询订阅
    $subscription = DB::table('subscriptions')
        ->where('subscription_id', $subscriptionId)
        ->first();
    
    if (!$subscription) {
        $this->log('Subscription Not Found', 'error', [
            'subscription_id' => $subscriptionId
        ]);
        return;
    }
    
    // 更新订阅周期
    DB::table('subscriptions')
        ->where('id', $subscription->id)
        ->update([
            'current_period_start' => date('Y-m-d H:i:s', $invoice['period_start']),
            'current_period_end' => date('Y-m-d H:i:s', $invoice['period_end']),
            'status' => 'active',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    
    // 记录扣款日志
    $this->log('Subscription Payment Succeeded', 'info', [
        'subscription_id' => $subscriptionId,
        'invoice_id' => $invoice['id'],
        'amount' => $invoice['amount']
    ]);
    
    // 触发业务逻辑（如延长会员期限）
    $this->extendMembershipPeriod($subscription);
}
```

---

## 订单状态同步

### 主动查询订单状态

除了 Webhook 通知，商户也可以主动查询支付状态：

```php
public function queryPaymentStatus($paymentIntentId)
{
    $client = new \UseePay\UseePayClient([
        'api_key' => $config['usee_pay']['api_key']
    ]);
    
    try {
        // 查询支付意图状态
        $paymentIntent = $client->paymentIntents()->retrieve($paymentIntentId);
        
        // 根据状态更新订单
        $this->syncOrderStatus($paymentIntent);
        
        return $paymentIntent;
        
    } catch (\Exception $e) {
        $this->log('Query Payment Status Failed', 'error', [
            'payment_intent_id' => $paymentIntentId,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

private function syncOrderStatus($paymentIntent)
{
    $merchantOrderId = $paymentIntent['merchant_order_id'];
    
    $order = DB::table('orders')
        ->where('order_no', $merchantOrderId)
        ->first();
    
    if (!$order) {
        return;
    }
    
    // 根据支付状态更新订单
    $statusMap = [
        'succeeded' => 'paid',
        'payment_failed' => 'failed',
        'canceled' => 'cancelled'
    ];
    
    if (isset($statusMap[$paymentIntent['status']])) {
        DB::table('orders')
            ->where('id', $order->id)
            ->update([
                'status' => $statusMap[$paymentIntent['status']],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }
}
```

---

## 错误处理与日志

### 日志记录建议

```php
// 日志记录方法
private function log($message, $level, $context = [], $category = 'payment')
{
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'level' => $level,
        'category' => $category,
        'message' => $message,
        'context' => $context,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    // 写入日志文件
    $logFile = __DIR__ . '/../../logs/' . $category . '_' . date('Y-m-d') . '.log';
    file_put_contents($logFile, json_encode($logData) . PHP_EOL, FILE_APPEND);
    
    // 同时写入数据库（可选）
    DB::table('payment_logs')->insert($logData);
}
```

### 常见错误处理

| 错误类型 | 处理方式 |
|---------|---------|
| SDK 初始化失败 | 检查 API Key 配置 |
| 支付意图创建失败 | 记录日志，返回友好错误信息 |
| Webhook 签名验证失败 | 拒绝请求，记录安全日志 |
| 订单不存在 | 记录错误，通知技术团队 |
| 重复支付 | 幂等性处理，避免重复更新 |

---

## 安全建议

### 1. API Key 安全

- ✅ 不要将 API Key 硬编码在代码中
- ✅ 使用环境变量或配置文件存储
- ✅ 不要将 API Key 提交到版本控制系统
- ✅ 定期轮换 API Key

### 2. Webhook 安全

- ✅ 必须验证 Webhook 签名
- ✅ 使用 HTTPS 接收 Webhook
- ✅ 限制 Webhook 来源 IP（如果 UseePay 提供）
- ✅ 实现幂等性，防止重复处理

### 3. 订单安全

- ✅ 验证订单金额，防止篡改
- ✅ 使用事务处理订单状态更新
- ✅ 记录所有订单状态变更日志
- ✅ 实现订单超时自动取消机制

### 4. 数据安全

- ✅ 敏感信息加密存储
- ✅ 不要在日志中记录完整卡号
- ✅ 定期备份订单数据
- ✅ 遵守 PCI DSS 规范

---

## 总结

### 商户端核心工作清单

#### 一次性支付

1. ✅ 安装 UseePay PHP SDK (`useepay/useepay-php: ^1.0`)
2. ✅ 设计订单表结构
3. ✅ 实现订单落库逻辑（先落库）
4. ✅ 调用 SDK 创建支付意图（后支付）
5. ✅ 保存 `payment_intent_id` 到订单表
6. ✅ 前端跳转到 UseePay 收银台
7. ✅ 实现支付回调页面（展示结果）
8. ✅ 实现 Webhook 接口（更新订单状态）
9. ✅ 验证 Webhook 签名
10. ✅ 记录完整日志

#### 分期支付（订阅）

1. ✅ 设计订阅表结构
2. ✅ 实现订阅创建逻辑
3. ✅ 调用 SDK 创建订阅
4. ✅ 处理首次支付
5. ✅ 实现订阅扣款 Webhook 处理
6. ✅ 更新订阅周期信息
7. ✅ 实现订阅取消功能

### 关键注意事项

| 注意事项 | 说明 |
|---------|------|
| **先落库，后支付** | 订单必须先保存到数据库，再调用支付接口 |
| **使用 merchant_order_id** | 用商户订单号关联 UseePay 支付意图 |
| **Webhook 是唯一可靠来源** | 订单状态更新必须通过 Webhook |
| **验证 Webhook 签名** | 防止伪造请求，确保安全性 |
| **实现幂等性** | 防止 Webhook 重复处理 |
| **记录完整日志** | 便于排查问题和对账 |

### 相关文档

- UseePay PHP SDK: `https://github.com/useepay/useepay-php`
- UseePay API 文档: `https://docs.useepay.com`
- Webhook 事件参考: `https://docs.useepay.com/webhooks`

---

**文档版本：** 2.0（商户端版本）  
**最后更新：** 2026-01-28  
**适用 SDK：** `useepay/useepay-php: ^1.0`  
**目标读者：** 电商网站开发者
