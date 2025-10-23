# UseePay 配置集成说明

## 概述
本文档说明了如何将 UseePay 配置（特别是 API 公钥）从 PHP 配置文件集成到前端 JavaScript 中。

## 配置流程

### 1. PHP 配置文件 (config/config.php)

配置文件中存储了 UseePay 的所有凭证：

```php
'usee_pay' => [
    'api_public_key' => getenv('USEEPAY_PUBLIC_API_KEY') ?: 'UseePay_PK_TEST_yDY0wXseGnP8wHbA5z1LYfNclwyVeaCHhOOwleI1XcqR0PFXgDbV57EL3yK01Spm0lXHwwFarrJRLzTJWV21NtSHDX6lMENXAEFA',
    'api_private_key' => getenv('USEEPAY_PRIVATE_API_KEY') ?: '...',
    'merchant_no' => getenv('USEEPAY_MERCHANT_NO') ?: '500000000011023',
    'app_id' => getenv('USEEPAY_APP_ID') ?: 'www.pay.com',
    'environment' => getenv('USEEPAY_ENV') ?: 'sandbox',
    ...
]
```

**关键点：**
- `api_public_key`: 用于前端 SDK 初始化
- `api_private_key`: 用于后端 API 调用（不应暴露给前端）
- 所有配置都支持环境变量覆盖

### 2. PHP 视图文件 (embedded_checkout.php)

在页面加载时，将公钥从 PHP 配置传递给 JavaScript：

```php
<script>
    // Get UseePay public key from PHP config
    <?php
        global $config;
        $publicKey = $config['usee_pay']['api_public_key'] ?? 'UseePay_PK_TEST_1234';
    ?>
    window.USEEPAY_PUBLIC_KEY = '<?php echo $publicKey; ?>';
</script>
```

**流程：**
1. 访问全局 `$config` 变量
2. 从配置中获取 `api_public_key`
3. 将其设置为全局 JavaScript 变量 `window.USEEPAY_PUBLIC_KEY`
4. 提供默认值以防配置缺失

### 3. JavaScript 文件 (embedded_checkout.js)

在 JavaScript 中使用从 PHP 传递的公钥：

```javascript
function initializeUseepayElements(clientSecret, paymentIntentId) {
    // Get public key from window config (set in PHP)
    const publicKey = window.USEEPAY_PUBLIC_KEY;
    if (!publicKey) {
        console.error('UseePay public key not configured');
        alert('Payment configuration error. Please contact support.');
        return;
    }
    
    // Initialize UseePay instance with public key
    useepayInstance = window.UseePay(publicKey);
    console.log('✓ UseePay instance initialized');
    
    // ... rest of initialization
}
```

**流程：**
1. 读取 `window.USEEPAY_PUBLIC_KEY`
2. 验证公钥是否存在
3. 使用公钥初始化 UseePay SDK
4. 创建 Elements 和 Payment Element

## 环境配置

### 开发环境 (.env)

```
USEEPAY_PUBLIC_API_KEY=UseePay_PK_TEST_yDY0wXseGnP8wHbA5z1LYfNclwyVeaCHhOOwleI1XcqR0PFXgDbV57EL3yK01Spm0lXHwwFarrJRLzTJWV21NtSHDX6lMENXAEFA
USEEPAY_PRIVATE_API_KEY=UseePay_SK_ZPhc8g0q5RQwcV1FLUdyupD2jMqjtGNMFnZOKTRyr7q5lnz4xkGWWpx0TV1mxc4XM33MEba2WKcE1sHKeMDwwBgWgXUP9O1hpVoe
USEEPAY_MERCHANT_NO=500000000011023
USEEPAY_APP_ID=www.pay.com
USEEPAY_ENV=sandbox
USEEPAY_CALLBACK_URL=http://localhost:8000/payment/callback
```

### 生产环境

```
USEEPAY_PUBLIC_API_KEY=UseePay_PK_XXXX...
USEEPAY_PRIVATE_API_KEY=UseePay_SK_XXXX...
USEEPAY_MERCHANT_NO=your_merchant_no
USEEPAY_APP_ID=your_app_id
USEEPAY_ENV=production
USEEPAY_CALLBACK_URL=https://yourdomain.com/payment/callback
```

## 安全考虑

### ✅ 安全做法

1. **公钥可以暴露给前端**
   - `api_public_key` 用于客户端 SDK 初始化
   - 不包含敏感信息

2. **私钥必须保护**
   - `api_private_key` 仅在后端使用
   - 从不在 JavaScript 中使用
   - 存储在环境变量中

3. **环境变量管理**
   - 使用 `.env` 文件管理凭证
   - `.env` 文件不应提交到版本控制
   - 在生产环境中使用系统环境变量

### ❌ 不安全做法

```javascript
// ❌ 不要硬编码私钥
const privateKey = 'UseePay_SK_XXXX...';

// ❌ 不要在前端使用私钥
fetch('/api/payment', {
    headers: {
        'Authorization': 'Bearer ' + privateKey
    }
});

// ❌ 不要在 JavaScript 中直接使用 .env 文件
const config = require('.env');
```

## 工作流程图

```
用户访问 embedded_checkout.php
          ↓
PHP 读取 config/config.php
          ↓
PHP 从配置中获取 api_public_key
          ↓
PHP 将公钥注入到 JavaScript 全局变量
          ↓
JavaScript 加载 embedded_checkout.js
          ↓
JavaScript 读取 window.USEEPAY_PUBLIC_KEY
          ↓
initializeUseepayElements() 使用公钥初始化 SDK
          ↓
创建 Payment Element 并挂载到 DOM
          ↓
用户输入支付信息并确认
          ↓
SDK 确认支付
```

## 配置验证

### 检查公钥是否正确加载

在浏览器控制台中运行：

```javascript
// 检查公钥是否存在
console.log(window.USEEPAY_PUBLIC_KEY);

// 检查公钥格式
if (window.USEEPAY_PUBLIC_KEY && window.USEEPAY_PUBLIC_KEY.startsWith('UseePay_PK_')) {
    console.log('✓ Public key is valid');
} else {
    console.error('✗ Public key is invalid');
}
```

### 检查 SDK 是否正确初始化

```javascript
// 检查 SDK 是否加载
console.log(window.UseePay);

// 检查 SDK 实例
console.log(useepayInstance);

// 检查 Elements
console.log(useepayElements);

// 检查 Payment Element
console.log(useepayPaymentElement);
```

## 故障排除

### 问题：Payment Element 未显示

**可能原因：**
1. 公钥未正确配置
2. SDK 未加载
3. Elements 初始化失败

**解决方案：**
```javascript
// 在浏览器控制台检查
console.log('Public Key:', window.USEEPAY_PUBLIC_KEY);
console.log('SDK Loaded:', !!window.UseePay);
console.log('Instance:', useepayInstance);
console.log('Elements:', useepayElements);
```

### 问题：支付确认失败

**可能原因：**
1. 公钥与后端私钥不匹配
2. 环境配置不一致（沙箱 vs 生产）
3. 支付意图已过期

**解决方案：**
1. 检查 `.env` 文件中的公钥和私钥
2. 确保公钥前缀与环境一致：
   - `PK_TEST_` → 沙箱环境
   - `PK_` → 生产环境
3. 检查支付意图的创建时间

## 相关文件

- `config/config.php`: 配置文件
- `src/Views/payment/embedded_checkout.php`: 视图文件
- `public/assets/js/embedded_checkout.js`: JavaScript 文件
- `.env`: 环境变量文件（本地开发）
- `EMBEDDED_CHECKOUT_IMPLEMENTATION.md`: 实现文档

## 参考资源

- UseePay SDK 文档: https://checkout-sdk.useepay.com/
- 环境变量管理: https://12factor.net/config
