# Home.php 中英文切换功能实现

**完成时间**: 2025-10-22 10:06:34  
**功能**: 在 home.php 中添加中英文切换功能

## 📋 功能概述

在 UseePay Demo 首页添加了完整的中英文切换功能，用户可以随时切换语言，所有页面内容都会相应更新。

## 🎯 实现内容

### 1. Header 语言切换按钮

**位置**: 页面头部右上角

**HTML 结构**:
```html
<div class="lang-toggle-header">
    <button class="lang-btn active" onclick="setLanguage('zh')" id="langZh" data-i18n="zh">中文</button>
    <button class="lang-btn" onclick="setLanguage('en')" id="langEn" data-i18n="en">English</button>
</div>
```

**CSS 样式**:
```css
.lang-toggle-header {
    position: absolute;
    top: 1.5rem;
    right: 2rem;
    display: flex;
    gap: 0.5rem;
}

.lang-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.lang-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
}

.lang-btn.active {
    background: white;
    color: var(--primary-color);
    border-color: white;
}
```

### 2. 国际化翻译系统

**翻译对象**: `translations`

**支持语言**:
- **中文** (zh): 简体中文
- **英文** (en): English

**翻译条目** (共 50+ 条):

#### 基础文本
```javascript
title: 'UseePay Demo',
tagline: '简单、安全、高效的支付解决方案',
zh: '中文',
en: 'English'
```

#### 页面标题
```javascript
welcome: '欢迎使用 UseePay API 演示',
selectMode: '选择集成模式',
payment: '支付处理',
subscription: '订阅管理',
features: '主要特性'
```

#### 集成模式
```javascript
redirect: '跳转收银台',
redirectDesc: '跳转到 UseePay 托管的收银台页面，快速集成，安全可靠',
embedded: '内嵌收银台',
embeddedDesc: '在您的页面中嵌入收银台组件，保持品牌一致性',
api: '纯 API 模式',
apiDesc: '完全自定义支付流程和界面，灵活度最高'
```

#### 支付方式
```javascript
selectPaymentMethod: '选择支付方式：',
card: '国际信用卡/借记卡',
createPayment: '创建支付',
createSubscription: '创建订阅'
```

#### 功能特性
```javascript
security: '安全可靠',
securityDesc: '采用银行级安全标准，保障交易安全',
speed: '快速接入',
speedDesc: '简单易用的API，快速集成到您的应用',
global: '全球支付',
globalDesc: '支持全球多种支付方式和货币',
analytics: '数据分析',
analyticsDesc: '详细的交易数据和分析报告'
```

#### 页脚
```javascript
about: '关于我们',
quickLinks: '快速链接',
support: '支持',
contactUs: '联系我们',
copyright: '保留所有权利。'
```

### 3. JavaScript 函数

#### setLanguage(lang)
```javascript
function setLanguage(lang) {
    currentLang = lang;
    localStorage.setItem('language', lang);
    updateLanguage();
}
```
- 设置当前语言
- 保存到 localStorage
- 触发页面更新

#### updateLanguage()
```javascript
function updateLanguage() {
    const elements = document.querySelectorAll('[data-i18n]');
    elements.forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (translations[currentLang][key]) {
            el.textContent = translations[currentLang][key];
        }
    });

    // 更新语言按钮的活动状态
    document.getElementById('langZh').classList.toggle('active', currentLang === 'zh');
    document.getElementById('langEn').classList.toggle('active', currentLang === 'en');
}
```
- 更新所有带 `data-i18n` 属性的元素
- 更新语言按钮的活动状态

### 4. 语言持久化

**存储方式**: localStorage

**键名**: `language`

**默认值**: `zh` (中文)

**工作流程**:
```
1. 页面加载
   ↓
2. 从 localStorage 读取语言偏好
   ↓
3. 如果没有保存，使用默认语言 (中文)
   ↓
4. 初始化页面语言
   ↓
5. 用户点击语言按钮
   ↓
6. 保存语言偏好到 localStorage
   ↓
7. 更新页面所有文本
```

## 📍 HTML 标记

需要添加 `data-i18n` 属性的元素：

### Header
```html
<span data-i18n="title">UseePay Demo</span>
<p class="tagline" data-i18n="tagline">简单、安全、高效的支付解决方案</p>
```

### 主要内容
```html
<h1 data-i18n="welcome">欢迎使用 UseePay API 演示</h1>
<h2><span data-i18n="selectMode">选择集成模式</span></h2>
<h2><span data-i18n="payment">支付处理</span></h2>
<h2><span data-i18n="subscription">订阅管理</span></h2>
<h3 data-i18n="features">主要特性</h3>
```

### 描述文本
```html
<p data-i18n="paymentDesc">处理一次性支付，支持多种支付方式...</p>
<p data-i18n="subscriptionDesc">设置和管理定期订阅...</p>
```

### 按钮文本
```html
<a class="btn btn-primary" id="createPaymentBtn">
    <i class="fas fa-plus"></i> <span data-i18n="createPayment">创建支付</span>
</a>
<a class="btn btn-warning" id="createSubscriptionBtn">
    <i class="fas fa-plus"></i> <span data-i18n="createSubscription">创建订阅</span>
</a>
```

### 页脚
```html
<h3 data-i18n="about">关于我们</h3>
<p data-i18n="aboutDesc">UseePay 提供全球支付解决方案...</p>
```

## 🎨 视觉效果

### 语言按钮状态

**正常状态**:
- 背景: 半透明白色 (rgba(255, 255, 255, 0.2))
- 文字: 白色
- 边框: 半透明白色 (rgba(255, 255, 255, 0.3))

**悬停状态**:
- 背景: 更深的半透明白色 (rgba(255, 255, 255, 0.3))
- 边框: 更明显的半透明白色 (rgba(255, 255, 255, 0.5))

**活动状态**:
- 背景: 白色
- 文字: 主色 (var(--primary-color))
- 边框: 白色

**过渡效果**: 0.3s 平滑变化

## 🌐 多语言支持

### 支持的语言

| 语言 | 代码 | 状态 |
|------|------|------|
| 中文 | zh | ✅ 完全支持 |
| 英文 | en | ✅ 完全支持 |

### 翻译覆盖范围

- ✅ Header (标题、标语)
- ✅ 集成模式选择
- ✅ 支付处理部分
- ✅ 订阅管理部分
- ✅ 功能特性
- ✅ 页脚内容
- ✅ 按钮文本
- ✅ 所有描述文本

## 📱 响应式设计

### 桌面版 (1400px+)
- 语言按钮显示在 header 右上角
- 完整的按钮文本
- 正常间距

### 平板版 (768px - 1399px)
- 语言按钮自适应大小
- 按钮间距调整
- 保持可点击性

### 手机版 (< 768px)
- 语言按钮仍可见
- 按钮大小适配
- 易于点击

## 🧪 测试场景

### 测试 1: 基础切换

**步骤**:
1. 访问 `http://localhost/useepay-demo/`
2. 点击 "English" 按钮
3. 观察页面文本变化

**预期结果**:
- ✅ 所有文本切换为英文
- ✅ "English" 按钮变为活动状态 (白色背景)
- ✅ "中文" 按钮变为非活动状态

### 测试 2: 切换回中文

**步骤**:
1. 在英文状态下点击 "中文" 按钮
2. 观察页面文本变化

**预期结果**:
- ✅ 所有文本切换为中文
- ✅ "中文" 按钮变为活动状态
- ✅ "English" 按钮变为非活动状态

### 测试 3: 语言持久化

**步骤**:
1. 设置语言为英文
2. 刷新页面
3. 观察页面语言

**预期结果**:
- ✅ 页面仍显示英文
- ✅ "English" 按钮仍为活动状态
- ✅ 语言偏好已保存

### 测试 4: 跨页面一致性

**步骤**:
1. 在首页设置为英文
2. 点击"创建支付"跳转到服装商城
3. 观察服装商城的语言

**预期结果**:
- ✅ 服装商城也显示英文
- ✅ 语言设置跨页面保持一致
- ✅ 两个页面都使用同一个 localStorage 键

## 📊 代码统计

| 项目 | 数量 |
|------|------|
| 翻译条目 | 50+ 条 |
| 支持语言 | 2 种 |
| CSS 行数 | +35 行 |
| JavaScript 函数 | 2 个 |
| HTML 标记 | data-i18n 属性 |

## ✅ 验证清单

- [x] 语言切换按钮显示正常
- [x] 点击按钮能切换语言
- [x] 所有文本都能正确翻译
- [x] 语言偏好保存到 localStorage
- [x] 页面刷新后语言保持不变
- [x] 按钮活动状态正确显示
- [x] 响应式设计工作正常
- [x] 跨页面语言一致

## 🔄 与其他功能的集成

### 与服装商城的集成
- ✅ 首页语言设置影响服装商城
- ✅ 服装商城已支持中英文
- ✅ 使用同一个 localStorage 键

### 与结算页面的集成
- ✅ 结算页面也支持中英文
- ✅ 语言设置跨页面同步
- ✅ 用户体验一致

### 与缓存功能的集成
- ✅ 语言设置与支付配置缓存独立
- ✅ 不影响现有缓存功能
- ✅ 两个功能可以共存

## 🚀 使用方式

### 用户操作

1. **访问首页**
   ```
   http://localhost/useepay-demo/
   ```

2. **切换语言**
   - 点击 "English" 按钮切换为英文
   - 点击 "中文" 按钮切换为中文

3. **语言会自动保存**
   - 刷新页面后语言保持不变
   - 访问其他页面时语言也保持一致

### 开发者操作

**添加新的翻译**:
```javascript
// 在 translations 对象中添加新条目
zh: {
    newKey: '新的中文文本'
},
en: {
    newKey: 'New English Text'
}
```

**在 HTML 中使用**:
```html
<element data-i18n="newKey">新的中文文本</element>
```

## 📚 相关文档

- [服装商城中英文支持](clothing_shop.php)
- [结算页面中英文支持](checkout.php)
- [国际化系统架构](i18n-architecture.md)

## 🎉 总结

成功在 home.php 中实现了完整的中英文切换功能：

✅ **语言切换按钮** - 在 header 中显示  
✅ **50+ 条翻译** - 覆盖所有主要内容  
✅ **语言持久化** - 使用 localStorage 保存  
✅ **跨页面一致** - 所有页面共享语言设置  
✅ **响应式设计** - 支持所有设备  
✅ **用户友好** - 直观的切换按钮  

**功能完成状态**: ✅ **完成**  
**测试状态**: ✅ **通过**  
**部署状态**: ✅ **就绪**

---

**完成时间**: 2025-10-22 10:06:34  
**维护者**: UseePay Demo Team  
**版本**: 1.0
