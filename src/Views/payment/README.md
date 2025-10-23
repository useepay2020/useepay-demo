# Payment Views - 支付页面

## 概述 (Overview)

本目录包含与支付相关的视图文件。

## 文件说明

### clothing_shop.php
时尚服装商城 - 一个完整的电商购物页面示例

**功能特性 (Features):**
- 🛍️ 产品展示网格布局
- 🛒 购物车管理系统
- 🌐 双语言支持 (中文/英文)
- 💾 本地存储购物车数据
- 📱 响应式设计
- 🎨 现代化UI设计

**访问路由 (Access Route):**
```
/payment/clothing-shop
```

**页面功能 (Page Features):**

1. **产品展示 (Product Display)**
   - 8种不同的服装产品
   - 产品分类、名称、描述和价格
   - 产品卡片悬停效果

2. **购物车 (Shopping Cart)**
   - 添加/删除商品
   - 修改商品数量
   - 购物车总数显示
   - 购物车摘要（总数、总价）

3. **语言切换 (Language Toggle)**
   - 支持中文和英文
   - 语言偏好本地存储
   - 实时更新所有文本

4. **通知系统 (Toast Notifications)**
   - 添加商品时显示提示
   - 自动消失提示

## 产品列表 (Product List)

| ID | 产品名称 | 英文名称 | 分类 | 价格 |
|----|---------|---------|------|------|
| 1 | 经典白色T恤 | Classic White T-Shirt | 上装/Tops | $18.99 |
| 2 | 修身牛仔裤 | Slim Fit Jeans | 裤装/Bottoms | $42.99 |
| 3 | 连帽卫衣 | Hooded Sweatshirt | 上装/Tops | $36.99 |
| 4 | 运动休闲裤 | Athletic Casual Pants | 裤装/Bottoms | $26.99 |
| 5 | 针织开衫 | Knit Cardigan | 上装/Tops | $56.99 |
| 6 | 时尚风衣 | Fashion Trench Coat | 外套/Outerwear | $99.99 |
| 7 | 短袖衬衫 | Short Sleeve Shirt | 上装/Tops | $25.99 |
| 8 | 休闲短裤 | Casual Shorts | 裤装/Bottoms | $21.99 |

## 技术栈 (Technology Stack)

- **前端**: HTML5, CSS3, JavaScript (Vanilla)
- **存储**: LocalStorage API
- **国际化**: 自定义i18n系统
- **响应式**: CSS Media Queries

## 使用说明 (Usage)

### 基本使用
1. 访问 `/payment/clothing-shop` 页面
2. 浏览产品列表
3. 点击"加入购物车"按钮添加商品
4. 点击购物车按钮查看购物车
5. 修改商品数量或删除商品
6. 点击"结算"按钮进行支付

### 语言切换
- 点击右上角的"EN"或"中文"按钮切换语言
- 语言偏好会自动保存到浏览器本地存储

### 购物车数据
- 购物车数据存储在浏览器的 LocalStorage 中
- 键名: `fashionCart`
- 数据格式: JSON 数组

## 集成支付 (Payment Integration)

结算按钮当前重定向到 `/payment/checkout`。要集成真实支付功能：

1. 创建 `checkout.php` 页面
2. 在 PaymentController 中添加 `checkout()` 方法
3. 调用 UseePay API 创建支付意图
4. 处理支付结果和回调

## 自定义 (Customization)

### 修改产品
编辑 `clothing_shop.php` 中的 JavaScript 部分：

```javascript
// 修改 products 数组
const products = [
    { id: 1, price: 18.99, image: '👕' },
    // ...
];

// 修改 translations 对象中的产品信息
const translations = {
    zh: {
        products: {
            1: { name: '产品名称', category: '分类', description: '描述' },
            // ...
        }
    }
};
```

### 修改样式
编辑 `<style>` 部分的 CSS 规则

### 修改颜色主题
主要颜色变量:
- 主色: `#667eea` 和 `#764ba2` (渐变)
- 强调色: `#ff4757` (红色)
- 成功色: `#2ecc71` (绿色)

## 浏览器兼容性 (Browser Compatibility)

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- 移动浏览器 (iOS Safari, Chrome Mobile)

## 注意事项 (Notes)

- 页面使用 LocalStorage，需要启用 JavaScript
- 购物车数据仅存储在本地，不会发送到服务器
- 结算功能需要后端支持
- 图标使用 Unicode 表情符号

## 迁移来源 (Migration Source)

原始文件: `D:\03Projects\cpp\sdk\useepay-php\examples\clothing_shop.html`

迁移时间: 2025-10-22

迁移说明:
- 将 HTML 转换为 PHP 模板
- 保持所有功能和样式不变
- 集成到项目路由系统
