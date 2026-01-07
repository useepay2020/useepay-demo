# Express Checkout 元素文档

## 概述

Express Checkout 元素提供了一种简化的支付体验，集成了 Apple Pay、Google Pay 和其他基于钱包的快捷支付方式。它允许客户使用预填充的配送和支付信息快速完成购买。

### 完整流程图

```
┌─────────────────────────────────────────────────────────────┐
│  1. 页面加载 - 初始化 UseePay SDK                            │
│     UseePay(publicKey) → elements(options)                  │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│  2. 创建并挂载 Express Checkout 元素                         │
│     elements.create('expressCheckout', options)             │
│     expressCheckoutElement.mount(domId)                     │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│  3. ready 事件触发                                           │
│     → 检查可用的支付方式 (Apple Pay / Google Pay)            │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│  4. 客户点击快捷支付按钮                                      │
│     → click 事件触发 (1秒内完成)                             │
│     → 可选：动态更新 lineItems / shippingRates / applePay   │
└────────────────────────┬────────────────────────────────────┘
                         ↓
         ┌───────────────┴───────────────┐
         │  是否需要配送？                 │
         └───────┬───────────────┬───────┘
                 │ 是             │ 否
                 ↓               ↓
    ┌──────────────────────┐    │
    │ 5a. 客户选择/更改地址  │    │
    │ shippingAddressChange│    │
    │ (20秒内完成)          │    │
    │ → 返回配送方式        │    │
    └──────────┬───────────┘    │
               ↓                │
    ┌──────────────────────┐    │
    │ 5b. 客户选择配送方式   │    │
    │ shippingRateChange   │    │
    │ (20秒内完成)          │    │
    └──────────┬───────────┘    │
               └────────┬────────┘
                        ↓
┌─────────────────────────────────────────────────────────────┐
│  6. 客户确认支付 - confirm 事件触发                           │
│     → 获取 billingDetails 和 shippingAddress                │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│  7. 调用后端 API 创建支付意图                                 │
│     POST /api/create-payment-intent                         │
│     → 返回 paymentIntentId 和 clientSecret                  │
└────────────────────────┬────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────────┐
│  8. 确认支付                                                 │
│     useepay.confirmPayment({ elements, paymentIntentId,     │
│                              clientSecret })                │
└────────────────────────┬────────────────────────────────────┘
                         ↓
         ┌───────────────┴───────────────┐
         │  支付结果                      │
         └───────┬───────────────┬───────┘
                 │ 成功           │ 失败
                 ↓               ↓
    ┌──────────────────┐  ┌──────────────────┐
    │ 9a. 重定向到      │  │ 9b. 显示错误信息  │
    │     成功页面      │  │     让客户重试    │
    └──────────────────┘  └──────────────────┘
```

### 关键时间要求

| 事件 | 超时时间 | 说明 |
|------|---------|------|
| `click` | 1 秒 | 必须快速响应，避免耗时操作 |
| `shippingAddressChange` | 20 秒 | 可以调用 API 计算配送费用 |
| `shippingRateChange` | 20 秒 | 可以调用 API 更新订单信息 |
| `confirm` | 无限制 | 需要创建支付意图并确认支付 |

## 快速开始

### 步骤 1：引入 SDK

在您的 HTML 页面中引入 UseePay SDK：

```html
<script src="https://checkout-sdk.useepay.com/2.0.0/useepay.min.js"></script>
```

### 步骤 2：验证 SDK 加载

在使用 SDK 之前，验证 SDK 是否成功加载：

```javascript
window.onload = function() {
  if (window.UseePay) {
    console.log('UseePay SDK 加载成功')
    // 继续初始化支付流程
    initializePayment()
  } else {
    console.error('UseePay SDK 加载失败')
    // 显示错误提示给用户
    alert('支付系统加载失败，请刷新页面重试')
  }
}

function initializePayment() {
  // 初始化 UseePay 实例
  const useepay = UseePay('YOUR_PUBLIC_KEY')
  // ... 后续步骤
}
```

**最佳实践：**
- 在 `window.onload` 或 `DOMContentLoaded` 事件中检查 SDK 是否加载
- 如果 SDK 加载失败，向用户显示友好的错误提示
- 考虑添加重试机制或备用方案

### 步骤 3：初始化 SDK

使用您在 MC 商户后台生成的公钥初始化 UseePay：

```javascript
const useepay = UseePay('YOUR_PUBLIC_KEY')
```

**公钥格式说明：**
- **生产环境**：`UseePay_PK_` 开头（例如：`UseePay_PK_1234567890abcdef...`）
- **测试环境**：`UseePay_PK_TEST_` 开头（例如：`UseePay_PK_TEST_1234567890abcdef...`）

> ⚠️ **重要提示**：请从 MC 管理后台获取正确的公钥，切勿在生产环境使用测试公钥。

### 步骤 4：创建 Elements 实例

初始化 Elements 来管理支付界面：

```javascript
const elements = useepay.elements({
  mode: 'payment',      // 'payment' 或 'subscription'
  amount: 99,           // 支付金额（实际金额）
  currency: 'USD',      // 货币代码
  paymentMethodTypes: ['googlepay']  // 可选：可用支付方式数组
})
```


## 创建 Express Checkout 元素

### 基础设置

```javascript
const useepay = UseePay('YOUR_PUBLIC_KEY')

const elementsOptions = {
  mode: 'subscription', // 或 'payment'
  amount: 99,
  currency: 'USD',
  paymentMethodTypes: ['googlepay']  // 可选：可用支付方式数组
}

const elements = useepay.elements(elementsOptions)

const expressCheckoutElement = elements.create('expressCheckout', options?)
expressCheckoutElement.mount('express-checkout-element')
```

## 配置选项

### Elements 实例选项

在创建 Elements 实例时，可以配置以下选项：

| 参数 | 类型 | 必填 | 描述 |
|-----------|------|----------|-------------|
| `mode` | String | 是 | 支付模式，可选值：`'payment'`（一次性支付）或 `'subscription'`（订阅支付） |
| `amount` | Number | 是 | 支付金额（实际金额） |
| `currency` | String | 是 | 货币代码（例如：`'USD'`、`'EUR'`、`'CNY'`） |
| `paymentMethodTypes` | Array | 否 | 可用支付方式的字符串数组。可选值包括：`'googlepay'`（Google Pay）、`'applepay'`（Apple Pay）等。如果不提供，将显示所有支持的支付方式 |

### ExpressCheckout 选项

| 参数 | 类型 | 必填 | 描述 |
|-----------|------|----------|-------------|
| `business` | Object | 否 | 结账时显示的商家信息 |
| `business.name` | String | 否 | 向客户显示的商家名称 |
| `shippingAddressRequired` | Boolean | 否 | 是否需要客户提供配送地址。**如果设置为 `true`，则必须在 `create`、`click` 或 `shippingAddressChange` 事件中提供有效的 `shippingRates` 选项** |
| `allowedShippingCountries` | Array | 否 | 允许配送的国家代码数组（例如：`['US', 'CA']`） |
| `shippingRates` | Array | 否 | 可供客户选择的配送选项 |
| `lineItems` | Array | 否 | 要显示的订单明细项 |
| `applePay` | Object | 否 | Apple Pay 特定配置选项 |

### ShippingRate 对象

| 参数 | 类型 | 必填 | 描述 |
|-----------|------|----------|-------------|
| `id` | String | 是 | 配送方式的唯一标识符 |
| `displayName` | String | 是 | 向客户显示的配送方式名称 |
| `amount` | Number | 是 | 配送费用（实际金额） |

### LineItem 对象

| 参数 | 类型 | 必填 | 描述 |
|-----------|------|----------|-------------|
| `name` | String | 是 | 明细项的名称/描述 |
| `amount` | Number | 是 | 价格（实际金额） |

### ApplePay 对象

Apple Pay 特定配置，用于设置订阅支付等高级功能。

> **📖 详细参数说明**  
> 关于 Apple Pay 循环支付的详细参数和配置，请参考 Apple 官方文档：  
> [Apple Pay on the Web - Recurring Payments](https://developer.apple.com/documentation/applepayontheweb/applepayrecurringpaymentrequest)

| 参数 | 类型 | 必填 | 描述 |
|-----------|------|----------|-------------|
| `recurringPaymentRequest` | Object | 否 | 循环支付请求配置，用于订阅类型的支付 |

#### recurringPaymentRequest 对象

| 参数 | 类型 | 必填 | 描述 |
|-----------|------|----------|-------------|
| `paymentDescription` | String | 是 | 支付描述，向客户说明此循环支付的用途 |
| `managementURL` | String | 是 | 管理 URL，客户可以在此 URL 管理其订阅 |
| `regularBilling` | Object | 是 | 常规账单配置 |
| `billingAgreement` | String | 否 | 账单协议文本 |

#### regularBilling 对象

| 参数 | 类型 | 必填 | 描述 |
|-----------|------|----------|-------------|
| `amount` | Number | 是 | 循环支付金额（实际金额） |
| `label` | String | 是 | 账单标签，向客户显示的账单项名称 |
| `recurringPaymentStartDate` | Date | 是 | 循环支付开始日期 |
| `recurringPaymentEndDate` | Date | 是 | 循环支付结束日期 |
| `recurringPaymentIntervalUnit` | String | 是 | 循环支付间隔单位，可选值：`'year'`、`'month'`、`'day'`、`'hour'`、`'minute'` |
| `recurringPaymentIntervalCount` | Number | 是 | 循环支付间隔数量，例如：每 2 个月则为 2 |

## 配置示例

### 基础配置示例

```javascript
const expressCheckoutElement = elements.create('expressCheckout', {
  business: {
    name: 'UseePay Test',
  },
  shippingAddressRequired: true,
  allowedShippingCountries: ['US'],
  shippingRates: [
    {
      id: 'free-shipping',
      displayName: '免费配送',
      amount: 0,
    },
    {
      id: 'express-shipping',
      displayName: '快速配送',
      amount: 100.1,
    },
  ],
  lineItems: [
    {
      name: "商品名称",
      amount: 99.9
    },  
  ]
})
```

### Apple Pay 订阅支付配置示例

当使用 `mode: 'subscription'` 时，可以配置 Apple Pay 的循环支付功能：

```javascript
const expressCheckoutElement = elements.create('expressCheckout', {
  business: {
    name: 'UseePay 订阅服务',
  },
  lineItems: [
    {
      name: "高级会员订阅",
      amount: 99.9
    },  
  ],
  applePay: {
    recurringPaymentRequest: {
      paymentDescription: '高级会员月度订阅',
      managementURL: 'https://your-domain.com/subscription/manage',
      regularBilling: {
        amount: 99.9,
        label: '月度会员费',
        recurringPaymentStartDate: new Date('2024-01-01'),
        recurringPaymentEndDate: new Date('2025-01-01'),
        recurringPaymentIntervalUnit: 'month',
        recurringPaymentIntervalCount: 1
      },
      billingAgreement: '订阅将自动续费，您可以随时取消'
    }
  }
})
```
## 方法

### mount(domId)

将快捷结账元素挂载到 DOM 元素。

```javascript
expressCheckoutElement.mount('express-checkout-element')
```

**参数：**
- `domId` (String)：组件应挂载到的 DOM 元素的 ID

### unmount()

从 DOM 中移除快捷结账元素。

```javascript
expressCheckoutElement.unmount()
```

### update(options)

动态更新快捷结账元素的配置选项。

```javascript
expressCheckoutElement.update({
  allowedShippingCountries: ['US', 'CA', 'GB'],
  shippingAddressRequired: true
})
```

**参数：**
- `options` (Object)：更新选项
    - `allowedShippingCountries` (Array，可选)：允许配送的国家代码数组
    - `shippingAddressRequired` (Boolean，可选)：是否需要客户提供配送地址

## Elements 方法和事件

### elements.update(options)

动态更新 Elements 实例的配置。

```javascript
elements.update({
  mode: 'payment',
  amount: 199,
  currency: 'USD',
  paymentMethodTypes: ['googlepay']  // 更新可用支付方式
})
```

**参数：**
- `options` (Object)：更新选项
    - `mode` (String，可选)：支付模式（`'payment'` 或 `'subscription'`）
    - `amount` (Number，可选)：支付金额
    - `currency` (String，可选)：货币代码
    - `paymentMethodTypes` (Array，可选)：可用支付方式的字符串数组，例如：`['googlepay']`

### elements.on('update-end')

当 Elements 更新完成时触发。

```javascript
elements.on('update-end', function () {
  console.log('Elements 更新完成')
  // Elements 配置已更新，所有关联的元素都已同步
})
```

**使用场景：**
- 在动态更新金额或货币后，确认更新已完成
- 在更新完成后执行后续操作
- 调试和日志记录


## Express Checkout 事件

Express Checkout 元素会发出多个事件，您可以监听这些事件来处理客户交互。

### ready

当快捷结账元素完全加载并准备好交互时触发。此事件会返回当前可用的支付方式信息。

```javascript
expressCheckoutElement.on('ready', function (event) {
  console.log("快捷结账已就绪:", event)
  
  // 检查可用的支付方式
  const { availablePaymentMethods } = event
  
  if (availablePaymentMethods.applePay) {
    console.log("Apple Pay 可用")
  }
  
  if (availablePaymentMethods.googlePay) {
    console.log("Google Pay 可用")
  }
  
  // 元素已准备好供客户交互
})
```

**事件参数：**
- `event` (Object)：事件对象
    - `availablePaymentMethods` (Object)：可用的支付方式对象，键为支付方式名称，值为布尔值
        - `applePay` (Boolean)：Apple Pay 是否可用
        - `googlePay` (Boolean)：Google Pay 是否可用
        - 其他支持的快捷支付方式...

### click

当点击快捷结账按钮时触发。**此事件处理必须在 1 秒内完成**，否则可能导致支付流程超时。

```javascript
expressCheckoutElement.on('click', function (event) {
  console.log("快捷结账已点击:", event)
  const { elementType, expressPaymentType, resolve } = event
  
  console.log("元素类型:", elementType) // 'expressCheckout'
  console.log("快捷支付类型:", expressPaymentType) // 'apple_pay' 或 'google_pay'
  
  // 方式 1：简单确认，继续支付流程
  resolve()
  
  // 方式 2：更新配置后继续支付流程
  resolve({
    lineItems: [
      {
        name: "商品",
        amount: 99
      }
    ],
    shippingRates: [
      {
        id: 'standard',
        displayName: '标准配送',
        amount: 10
      }
    ],
    applePay: {
      recurringPaymentRequest: {
        paymentDescription: '订阅服务',
        managementURL: 'https://your-domain.com/subscription/manage',
        regularBilling: {
          amount: 99,
          label: '月度订阅',
          recurringPaymentStartDate: new Date(),
          recurringPaymentEndDate: new Date(new Date().setFullYear(new Date().getFullYear() + 1)),
          recurringPaymentIntervalUnit: 'month',
          recurringPaymentIntervalCount: 1
        }
      }
    }
  })
})
```

**⚠️ 重要提示：**
- 此事件处理器必须在 **1 秒内**调用 `resolve()`，否则支付流程可能超时
- 避免在此事件中执行耗时操作（如网络请求）

**事件参数：**
- `event` (Object)：事件对象
    - `elementType` (String)：元素类型，值为 `'expressCheckout'`
    - `expressPaymentType` (String)：快捷支付类型，可能的值：
        - `'apple_pay'`：Apple Pay
        - `'google_pay'`：Google Pay
    - `resolve(options)`：调用以继续支付流程

**resolve 方法参数：**
- `options` (Boolean | Object)：确认选项
    - 传入 `true`：简单确认，使用当前配置继续
    - 传入对象：更新配置后继续
        - `lineItems` (Array，可选)：更新后的明细项数组
        - `shippingRates` (Array，可选)：更新后的配送方式数组
        - `applePay` (Object，可选)：Apple Pay 特定配置更新
            - `recurringPaymentRequest`：循环支付请求配置（参见 [ApplePay 对象](#applepay-对象)）

### cancel

当客户取消快捷结账流程时触发。

```javascript
expressCheckoutElement.on('cancel', function () {
  console.log("快捷结账已取消")
  // 处理取消操作（例如：显示消息、记录分析数据）
})
```

### confirm

当客户在快捷结账流程中确认支付时触发。**在此事件中，您需要先调用后端 API 创建支付意图（Payment Intent），然后使用返回的 `paymentIntentId` 和 `clientSecret` 调用 `confirmPayment` 完成支付。**

```javascript
expressCheckoutElement.on('confirm', async function (event) {
  console.log("支付已确认:", event)
  const { elementType, expressPaymentType, billingDetails, shippingAddress } = event
  
  console.log("元素类型:", elementType) // 'expressCheckout'
  console.log("快捷支付类型:", expressPaymentType) // 'apple_pay' 或 'google_pay'
  console.log("账单信息:", billingDetails)
  console.log("配送地址:", shippingAddress)
  
  try {
    // 步骤 1：调用您的后端 API 创建支付意图
    const response = await fetch('/api/create-payment-intent', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        amount: 9900, // 金额（以最小单位计）
        currency: 'USD',
        billingDetails,
        shippingAddress
      })
    })
    
    const { paymentIntentId, clientSecret } = await response.json()
    
    // 步骤 2：使用支付意图确认支付
    const result = await useepay.confirmPayment({
      elements,
      paymentIntentId,
      clientSecret
    })
    
    console.log("支付结果:", result)
    
    if (result.error) {
      console.error('支付失败:', result.error)
      // 处理支付失败
    } else {
      console.log('支付成功')
      // 重定向到成功页面或显示成功消息
    }
  } catch (error) {
    console.error('支付处理出错:', error)
  }
})
```

**事件参数：**
- `event` (Object)：事件对象
    - `elementType` (String)：元素类型，值为 `'expressCheckout'`
    - `expressPaymentType` (String)：快捷支付类型，可能的值：
        - `'apple_pay'`：Apple Pay
        - `'google_pay'`：Google Pay
    - `billingDetails` (BillingAddressType，可选)：账单信息
    - `shippingAddress` (ShippingAddressType，可选)：配送地址信息

**BillingAddressType 对象：**
| 属性 | 类型 | 描述 |
|-----------|------|-------------|
| `name` | String | 账单持有人姓名 |
| `email` | String | 电子邮件地址 |
| `phone` | String | 电话号码 |
| `address` | AddressDetail | 账单地址详情 |

**ShippingAddressType 对象：**
| 属性 | 类型 | 描述 |
|-----------|------|-------------|
| `name` | String | 收件人姓名 |
| `address` | AddressDetail | 配送地址详情 |

**AddressDetail 对象：**
| 属性 | 类型 | 描述 |
|-----------|------|-------------|
| `line1` | String | 地址第一行（街道地址） |
| `line2` | String | 地址第二行（公寓、套房等） |
| `city` | String | 城市 |
| `state` | String | 州/省 |
| `postal_code` | String | 邮政编码 |
| `country` | String | 国家代码（例如：'US'） |

### shippingAddressChange

当客户更改配送地址时触发。使用此事件根据新地址更新配送方式和明细项。**此事件处理必须在 20 秒内完成**，否则可能导致支付流程超时。

```javascript
expressCheckoutElement.on('shippingAddressChange', function (event) {
  console.log("配送地址已更改:", event)
  const { elementType, address, resolve, reject } = event
  
  console.log("元素类型:", elementType) // 'expressCheckout'
  console.log("配送地址:", address)
  // address: {
  //   city: '城市',
  //   state: '州/省',
  //   postal_code: '邮编',
  //   country: '国家代码'
  // }
  
  // 根据新地址更新配送方式和明细项
  resolve({
    lineItems: [
      {
        name: "更新后的商品",
        amount: 99
      },  
    ],
    shippingRates: [
      {
        id: 'standard',
        displayName: '标准配送',
        amount: 0,
      },
      {
        id: 'express',
        displayName: '快速配送',
        amount: 200,
      },
    ],
    applePay: {
      recurringPaymentRequest: {
        paymentDescription: '根据地址更新的订阅',
        managementURL: 'https://your-domain.com/subscription/manage',
        regularBilling: {
          amount: 99,
          label: '月度费用',
          recurringPaymentStartDate: new Date(),
          recurringPaymentEndDate: new Date(new Date().setFullYear(new Date().getFullYear() + 1)),
          recurringPaymentIntervalUnit: 'month',
          recurringPaymentIntervalCount: 1
        }
      }
    }
  })
  
  // 或者如果无法配送到该地址，则拒绝
  // reject()
})
```

**⚠️ 重要提示：**
- 此事件处理器必须在 **20 秒内**调用 `resolve()` 或 `reject()`，否则支付流程可能超时
- 如需调用后端 API 计算配送费用，请确保 API 响应时间足够快

**事件参数：**
- `event` (Object)：事件对象
    - `elementType` (String)：元素类型，值为 `'expressCheckout'`
    - `address` (Address)：客户选择的配送地址
    - `resolve(options)`：调用以接受地址更改并更新配送选项
    - `reject()`：调用以拒绝该地址（例如：如果无法配送）

**Address 对象：**
| 属性 | 类型 | 描述 |
|-----------|------|-------------|
| `city` | String | 城市 |
| `state` | String | 州/省 |
| `postal_code` | String | 邮政编码 |
| `country` | String | 国家代码（例如：'US'） |

**resolve 方法参数：**
- `options` (Object)：更新选项
    - `lineItems` (Array)：更新后的明细项数组
    - `shippingRates` (Array)：更新后的配送方式数组
    - `applePay` (Object，可选)：Apple Pay 特定配置更新
        - `recurringPaymentRequest`：循环支付请求配置（参见 [ApplePay 对象](#applepay-对象)）

### shippingRateChange

当客户选择不同的配送方式时触发。**此事件处理必须在 20 秒内完成**，否则可能导致支付流程超时。

```javascript
expressCheckoutElement.on('shippingRateChange', function (event) {
  console.log("配送方式已更改:", event)
  const { elementType, shippingRate, resolve } = event
  
  console.log("元素类型:", elementType) // 'expressCheckout'
  console.log("选择的配送方式:", shippingRate)
  // shippingRate: {
  //   id: 'standard',
  //   amount: 10,
  //   displayName: '标准配送'
  // }
  
  // 根据配送方式更新明细项和 Apple Pay 配置
  resolve({
    lineItems: [
      {
        name: "商品",
        amount: 99
      },
      {
        name: shippingRate.displayName,
        amount: shippingRate.amount
      }
    ],
    applePay: {
      recurringPaymentRequest: {
        paymentDescription: '包含配送的订阅',
        managementURL: 'https://your-domain.com/subscription/manage',
        regularBilling: {
          amount: 99 + shippingRate.amount,
          label: '订阅费用（含配送）',
          recurringPaymentStartDate: new Date(),
          recurringPaymentEndDate: new Date(new Date().setFullYear(new Date().getFullYear() + 1)),
          recurringPaymentIntervalUnit: 'month',
          recurringPaymentIntervalCount: 1
        }
      }
    }
  })
})
```

**⚠️ 重要提示：**
- 此事件处理器必须在 **20 秒内**调用 `resolve()`，否则支付流程可能超时
- 如需调用后端 API 更新订单信息，请确保 API 响应时间足够快

**事件参数：**
- `event` (Object)：事件对象
    - `elementType` (String)：元素类型，值为 `'expressCheckout'`
    - `shippingRate` (ShippingRate)：客户选择的配送方式
    - `resolve(options)`：调用以确认配送方式更改

**ShippingRate 对象：**
| 属性 | 类型 | 描述 |
|-----------|------|-------------|
| `id` | String | 配送方式的唯一标识符 |
| `amount` | Number | 配送费用（实际金额） |
| `displayName` | String | 配送方式显示名称 |

**resolve 方法参数：**
- `options` (Object，可选)：更新选项
    - `lineItems` (Array)：更新后的明细项数组
    - `applePay` (Object)：Apple Pay 特定配置更新
        - `recurringPaymentRequest`：循环支付请求配置（参见 [ApplePay 对象](#applepay-对象)）
