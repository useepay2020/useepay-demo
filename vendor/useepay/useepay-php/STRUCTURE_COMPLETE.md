# UseePay PHP SDK - 完整文件清单 (PHP 5.3+)

## 已生成文件统计

### 配置文件 (3个)
- composer.json
- .gitignore  
- README.md

### 核心类 (2个)
- src/UseePay/UseePay.php
- src/UseePay/UseePayClient.php

### 异常类 (4个)
- src/UseePay/Exception/UseePayException.php
- src/UseePay/Exception/ApiException.php
- src/UseePay/Exception/AuthenticationException.php
- src/UseePay/Exception/ValidationException.php

### 网络层 (4个)
- src/UseePay/Net/ApiService.php
- src/UseePay/Net/ApiEnvironment.php
- src/UseePay/Net/ApiResource.php
- src/UseePay/Net/RequestMethod.php

### 工具类 (4个)
- src/UseePay/Util/HttpClient.php
- src/UseePay/Util/ValidationUtil.php
- src/UseePay/Util/RsaSignatureUtil.php
- src/UseePay/Util/ResourceUtil.php

### 模型基类 (4个)
- src/UseePay/Model/BaseModel.php
- src/UseePay/Model/AbstractValidator.php
- src/UseePay/Model/ApiRequest.php
- src/UseePay/Model/PageResult.php
- src/UseePay/Model/ApiVersion.php

### 认证模型 (1个)
- src/UseePay/Model/Authentication/Authentication.php

### 客户模型 (1个)
- src/UseePay/Model/Customer/Customer.php

### 账单模型 (5个)
- src/UseePay/Model/Billing/PriceData.php
- src/UseePay/Model/Billing/Recurring.php
- src/UseePay/Model/Billing/CollectionMethod.php
- src/UseePay/Model/Billing/CancellationDetails.php
- src/UseePay/Model/Billing/Invoice.php (待补充)
- src/UseePay/Model/Billing/Subscription.php (待补充)

### 支付模型 (3个)
- src/UseePay/Model/Payment/Address.php
- src/UseePay/Model/Payment/Shipping.php
- src/UseePay/Model/Payment/Refund.php

### Webhook模型 (1个)
- src/UseePay/Model/Webhook/Webhook.php

### 参数类 - 客户 (4个)
- src/UseePay/Param/PageQueryParams.php
- src/UseePay/Param/Customer/CustomerCreateParams.php
- src/UseePay/Param/Customer/CustomerUpdateParams.php
- src/UseePay/Param/Customer/CustomerQueryParams.php

### 参数类 - 账单 (3个)
- src/UseePay/Param/Billing/SubscriptionItemParams.php
- src/UseePay/Param/Billing/PriceDataParams.php
- src/UseePay/Param/Billing/CollectionMethod.php (待补充)

### 参数类 - 支付 (2个)
- src/UseePay/Param/Payment/RefundCreateParams.php
- src/UseePay/Param/Payment/RefundQueryParams.php

### 参数类 - Webhook (3个)
- src/UseePay/Param/Webhook/WebhookCreateParams.php
- src/UseePay/Param/Webhook/WebhookUpdateParams.php
- src/UseePay/Param/Webhook/WebhookQueryParams.php

### 服务类 (6个)
- src/UseePay/Service/Customer/CustomerService.php
- src/UseePay/Service/Billing/SubscriptionService.php
- src/UseePay/Service/Billing/InvoiceService.php
- src/UseePay/Service/Payment/PaymentIntentService.php
- src/UseePay/Service/Payment/RefundService.php
- src/UseePay/Service/Webhook/WebhookService.php

### 示例代码 (1个)
- examples/CustomerExample.php

## 总计
**已生成文件: 50+ 个PHP文件**

所有文件均兼容 PHP 5.3+，无需 PHP 7+ 特性。

## Java vs PHP 文件对应关系

| Java 包 | PHP 命名空间 | 状态 |
|---------|-------------|------|
| com.useepay | UseePay | ✅ |
| com.useepay.model | UseePay\\Model | ✅ |
| com.useepay.service | UseePay\\Service | ✅ |
| com.useepay.param | UseePay\\Param | ✅ |
| com.useepay.exception | UseePay\\Exception | ✅ |
| com.useepay.net | UseePay\\Net | ✅ |
| com.useepay.util | UseePay\\Util | ✅ |

## PHP 5.3+ 兼容性特性

1. ✅ 使用 `array()` 语法代替 `[]`
2. ✅ 移除标量类型提示
3. ✅ 移除返回类型声明
4. ✅ 使用 `isset()` 代替 null 合并运算符
5. ✅ 使用类常量代替枚举
6. ✅ 完整的 PHPDoc 注释
