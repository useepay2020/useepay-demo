<?php
/**
 * Checkout Page - 结算页面
 * 处理购物车结算和支付流程
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>结算 - Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        header {
            background: white;
            padding: 20px 40px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .back-button {
            background: #f1f3f5;
            color: #2d3436;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .back-button:hover {
            background: #e1e5e8;
        }

        /* Main Content */
        .checkout-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        .checkout-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            color: #2d3436;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f3f5;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            font-size: 18px;
            color: #2d3436;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section h3::before {
            content: '';
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 8px;
        }

        label .required {
            color: #ff4757;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e8;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.2s;
            font-family: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* Payment Method */
        .payment-methods {
            display: grid;
            gap: 15px;
        }

        .payment-option {
            position: relative;
        }

        .payment-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .payment-option label {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 2px solid #e1e5e8;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .payment-option input[type="radio"]:checked + label {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .payment-icon {
            width: 50px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .payment-info {
            flex: 1;
        }

        .payment-name {
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 3px;
        }

        .payment-desc {
            font-size: 12px;
            color: #636e72;
        }

        /* Order Summary */
        .order-summary {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .order-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .order-item:first-child {
            padding-top: 0;
        }

        .order-item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }

        .order-item-info {
            flex: 1;
        }

        .order-item-name {
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .order-item-details {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #636e72;
        }

        .order-item-price {
            font-weight: 600;
            color: #667eea;
        }

        .order-totals {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f1f3f5;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: #636e72;
            font-size: 14px;
        }

        .total-row.grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #2d3436;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            margin-top: 15px;
        }

        /* Submit Button */
        .submit-button {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        .submit-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Empty Cart */
        .empty-cart {
            background: white;
            padding: 60px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            grid-column: 1 / -1;
        }

        .empty-cart-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .empty-cart-text {
            font-size: 18px;
            color: #636e72;
            margin-bottom: 30px;
        }

        .shop-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .shop-button:hover {
            transform: translateY(-2px);
        }

        /* Loading Spinner */
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 968px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }

            .order-summary {
                position: static;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
            }

            .logo {
                font-size: 22px;
            }

            .checkout-form,
            .order-summary {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo" data-i18n="logo">🛍️ 时尚服装商城</div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="/" class="back-button" data-i18n="backToHome">← 返回首页</a>
                <a href="/payment/clothing-shop" class="back-button" data-i18n="backToShop">← 返回购物</a>
            </div>
        </header>

        <div class="checkout-content" id="checkoutContent">
            <!-- Content will be loaded by JavaScript -->
        </div>
    </div>

    <script>
        // Language translations
        const translations = {
            zh: {
                logo: '🛍️ 时尚服装商城',
                backToHome: '← 返回首页',
                backToShop: '← 返回购物',
                checkoutInfo: '结算信息',
                customerInfo: '👤 客户信息',
                firstName: '名字',
                lastName: '姓氏',
                email: '电子邮箱',
                shippingAddress: '📍 收货地址',
                address: '详细地址',
                addressPlaceholder: '街道地址',
                city: '城市',
                state: '州/省',
                zipCode: '邮政编码',
                country: '国家',
                selectCountry: '选择国家',
                phone: '联系电话',
                paymentMethod: '💳 支付方式',
                creditCard: '信用卡/借记卡',
                creditCardDesc: '支持 Visa, MasterCard, American Express',
                paypalDesc: '使用 PayPal 账户安全支付',
                confirmPay: '确认并支付',
                processing: '处理中...',
                orderSummary: '订单摘要',
                quantity: '数量',
                subtotal: '商品小计:',
                shipping: '运费:',
                tax: '税费 (8%):',
                orderTotal: '订单总计:',
                cartEmpty: '您的购物车是空的',
                startShopping: '开始购物',
                required: '*',
                fillCustomerInfo: '请填写完整的客户信息',
                fillShippingAddress: '请填写完整的收货地址',
                invalidEmail: '请输入有效的电子邮箱地址',
                paymentError: '支付失败，请重试',
                products: {
                    1: { name: '经典白色T恤' },
                    2: { name: '修身牛仔裤' },
                    3: { name: '连帽卫衣' },
                    4: { name: '运动休闲裤' },
                    5: { name: '针织开衫' },
                    6: { name: '时尚风衣' },
                    7: { name: '短袖衬衫' },
                    8: { name: '休闲短裤' }
                }
            },
            en: {
                logo: '🛍️ Fashion Store',
                backToShop: '← Back to Shop',
                checkoutInfo: 'Checkout Information',
                customerInfo: '👤 Customer Information',
                firstName: 'First Name',
                lastName: 'Last Name',
                email: 'Email',
                shippingAddress: '📍 Shipping Address',
                address: 'Address',
                addressPlaceholder: 'Street Address',
                city: 'City',
                state: 'State/Province',
                zipCode: 'ZIP Code',
                country: 'Country',
                selectCountry: 'Select Country',
                phone: 'Phone',
                paymentMethod: '💳 Payment Method',
                creditCard: 'Credit/Debit Card',
                creditCardDesc: 'Supports Visa, MasterCard, American Express',
                paypalDesc: 'Pay securely with your PayPal account',
                confirmPay: 'Confirm and Pay',
                processing: 'Processing...',
                orderSummary: 'Order Summary',
                quantity: 'Qty',
                subtotal: 'Subtotal:',
                shipping: 'Shipping:',
                tax: 'Tax (8%):',
                orderTotal: 'Order Total:',
                cartEmpty: 'Your cart is empty',
                startShopping: 'Start Shopping',
                required: '*',
                fillCustomerInfo: 'Please fill in complete customer information',
                fillShippingAddress: 'Please fill in complete shipping address',
                invalidEmail: 'Please enter a valid email address',
                paymentError: 'Payment failed, please try again',
                products: {
                    1: { name: 'Classic White T-Shirt' },
                    2: { name: 'Slim Fit Jeans' },
                    3: { name: 'Hooded Sweatshirt' },
                    4: { name: 'Athletic Casual Pants' },
                    5: { name: 'Knit Cardigan' },
                    6: { name: 'Fashion Trench Coat' },
                    7: { name: 'Short Sleeve Shirt' },
                    8: { name: 'Casual Shorts' }
                }
            }
        };

        // Current language
        let currentLang = localStorage.getItem('language') || 'zh';

        // Update language
        function updateLanguage() {
            const elements = document.querySelectorAll('[data-i18n]');
            elements.forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[currentLang][key]) {
                    el.textContent = translations[currentLang][key];
                }
            });
        }

        // Get product name
        function getProductName(productId) {
            return translations[currentLang].products[productId]?.name || 'Product ' + productId;
        }

        // Load cart from localStorage
        let cart = [];
        
        function loadCart() {
            const saved = localStorage.getItem('fashionCart');
            if (saved) {
                cart = JSON.parse(saved);
            }
        }

        // Load payment methods from cache
        function getPaymentMethods() {
            const cached = localStorage.getItem('paymentMethods');
            if (cached) {
                try {
                    return JSON.parse(cached);
                } catch (e) {
                    console.error('Failed to parse payment methods:', e);
                    return [];
                }
            }
            return [];
        }

        // Payment method mapping - 与 home.php 中的支付方式保持一致
        const paymentMethodsMap = {
            'card': {
                icon: '<i class="fas fa-credit-card" style="color: #1a73e8;"></i>',
                name_zh: '信用卡/借记卡',
                name_en: 'Credit/Debit Card',
                desc_zh: '支持 Visa, MasterCard, American Express',
                desc_en: 'Supports Visa, MasterCard, American Express'
            },
            'apple_pay': {
                icon: '<i class="fab fa-apple" style="color: #000000;"></i>',
                name_zh: 'Apple Pay',
                name_en: 'Apple Pay',
                desc_zh: '使用 Apple Pay 快速支付',
                desc_en: 'Pay quickly with Apple Pay'
            },
            'google_pay': {
                icon: '<i class="fab fa-google" style="color: #4285F4;"></i>',
                name_zh: 'Google Pay',
                name_en: 'Google Pay',
                desc_zh: '使用 Google Pay 快速支付',
                desc_en: 'Pay quickly with Google Pay'
            },
            'wechat': {
                icon: '<i class="fab fa-weixin" style="color: #09B83E;"></i>',
                name_zh: '微信支付',
                name_en: 'WeChat Pay',
                desc_zh: '使用微信账户安全支付',
                desc_en: 'Pay securely with WeChat'
            },
            'alipay': {
                icon: '<i class="fab fa-alipay" style="color: #1677FF;"></i>',
                name_zh: '支付宝',
                name_en: 'Alipay',
                desc_zh: '使用支付宝账户安全支付',
                desc_en: 'Pay securely with Alipay'
            },
            'afterpay': {
                icon: '<i class="fas fa-calendar-check" style="color: #B2FCE4;"></i>',
                name_zh: 'Afterpay',
                name_en: 'Afterpay',
                desc_zh: '分期付款，先买后付',
                desc_en: 'Buy now, pay later'
            },
            'klarna': {
                icon: '<i class="fas fa-shopping-bag" style="color: #FFB3C7;"></i>',
                name_zh: 'Klarna',
                name_en: 'Klarna',
                desc_zh: '灵活的支付计划',
                desc_en: 'Flexible payment plans'
            },
            'oxxo': {
                icon: '<i class="fas fa-store" style="color: #EC0000;"></i>',
                name_zh: 'OXXO',
                name_en: 'OXXO',
                desc_zh: '在 OXXO 便利店支付',
                desc_en: 'Pay at OXXO convenience stores'
            }
        };

        // Calculate totals
        function calculateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const shipping = subtotal > 0 ? 9.99 : 0;
            const tax = subtotal * 0.08; // 8% tax
            const totalAmount = subtotal + shipping + tax;

            return {
                subtotal: subtotal.toFixed(2),
                shipping: shipping.toFixed(2),
                tax: tax.toFixed(2),
                totalAmount: totalAmount.toFixed(2),
                currency: 'USD'
            };
        }

        // Generate payment methods HTML based on cached methods
        function generatePaymentMethods(t) {
            const cachedMethods = getPaymentMethods();
            console.log('Cached payment methods:', cachedMethods);
            
            // 获取所有支付方式
            let methodsToDisplay = [];
            
            // 如果有缓存的支付方式，先显示缓存的方式
            if (cachedMethods && cachedMethods.length > 0) {
                methodsToDisplay = [...cachedMethods];
                console.log('Using cached methods:', methodsToDisplay);
            } else {
                // 如果没有缓存，使用默认的前两个支付方式
                methodsToDisplay = ['card', 'apple_pay'];
                console.log('No cached methods, using default methods:', methodsToDisplay);
            }

            
            // 生成支付选项 HTML
            return methodsToDisplay.map((method, index) => {
                const methodInfo = paymentMethodsMap[method];
                if (!methodInfo) {
                    console.warn('Unknown payment method:', method);
                    return '';
                }
                
                const methodName = currentLang === 'zh' ? methodInfo.name_zh : methodInfo.name_en;
                const methodDesc = currentLang === 'zh' ? methodInfo.desc_zh : methodInfo.desc_en;
                const isFirst = index === 0; // 第一个支付方式默认选中
                
                return `
                    <div class="payment-option">
                        <input type="radio" id="method_${method}" name="paymentMethod" value="${method}" ${isFirst ? 'checked' : ''}>
                        <label for="method_${method}">
                            <div class="payment-icon" style="font-size: 1.2rem;">${methodInfo.icon}</div>
                            <div class="payment-info">
                                <div class="payment-name">${methodName}</div>
                                <div class="payment-desc">${methodDesc}</div>
                            </div>
                        </label>
                    </div>
                `;
            }).join('');
        }

        // Render checkout page
        function renderCheckout() {
            const content = document.getElementById('checkoutContent');

            if (cart.length === 0) {
                content.innerHTML = `
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <div class="empty-cart-text">${translations[currentLang].cartEmpty}</div>
                        <a href="/payment/clothing-shop" class="shop-button">${translations[currentLang].startShopping}</a>
                    </div>
                `;
                return;
            }

            const totals = calculateTotals();
            const t = translations[currentLang];

            content.innerHTML = `
                <div class="checkout-form">
                    <h2 class="section-title">${t.checkoutInfo}</h2>
                    
                    <form id="checkoutForm">
                        <!-- Customer Information -->
                        <div class="form-section">
                            <h3>${t.customerInfo}</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>${t.firstName} <span class="required">${t.required}</span></label>
                                    <input type="text" id="firstName" name="firstName" value="John" required>
                                </div>
                                <div class="form-group">
                                    <label>${t.lastName} <span class="required">${t.required}</span></label>
                                    <input type="text" id="lastName" name="lastName" value="Smith" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>${t.email} <span class="required">${t.required}</span></label>
                                <input type="email" id="email" name="email" value="john.smith@example.com" required>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="form-section">
                            <h3>${t.shippingAddress}</h3>
                            <div class="form-group">
                                <label>${t.address} <span class="required">${t.required}</span></label>
                                <input type="text" id="address" name="address" placeholder="${t.addressPlaceholder}" value="1234 Elm Street, Apt 5B" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>${t.city} <span class="required">${t.required}</span></label>
                                    <input type="text" id="city" name="city" value="Los Angeles" required>
                                </div>
                                <div class="form-group">
                                    <label>${t.state} <span class="required">${t.required}</span></label>
                                    <input type="text" id="state" name="state" value="California" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>${t.zipCode} <span class="required">${t.required}</span></label>
                                    <input type="text" id="zipCode" name="zipCode" value="90001" required>
                                </div>
                                <div class="form-group">
                                    <label>${t.country} <span class="required">${t.required}</span></label>
                                    <select id="country" name="country" required>
                                        <option value="">${t.selectCountry}</option>
                                        <option value="US" selected>美国 (United States)</option>
                                        <option value="CN">中国 (China)</option>
                                        <option value="UK">英国 (United Kingdom)</option>
                                        <option value="CA">加拿大 (Canada)</option>
                                        <option value="AU">澳大利亚 (Australia)</option>
                                        <option value="JP">日本 (Japan)</option>
                                        <option value="KR">韩国 (South Korea)</option>
                                        <option value="DE">德国 (Germany)</option>
                                        <option value="FR">法国 (France)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>${t.phone}</label>
                                <input type="tel" id="phone" name="phone" value="+1 (323) 555-0123">
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="form-section">
                            <h3>${t.paymentMethod}</h3>
                            <div class="payment-methods" id="paymentMethodsContainer">
                                ${generatePaymentMethods(t)}
                            </div>
                        </div>

                        <button type="submit" class="submit-button" id="submitButton">
                            ${t.confirmPay} $${totals.totalAmount}
                        </button>
                    </form>
                </div>

                <div class="order-summary">
                    <h2 class="section-title">${t.orderSummary}</h2>
                    <div id="orderItems">
                        ${cart.map(item => `
                            <div class="order-item">
                                <div class="order-item-image">${item.image}</div>
                                <div class="order-item-info">
                                    <div class="order-item-name">${getProductName(item.id)}</div>
                                    <div class="order-item-details">
                                        <span>${t.quantity}: ${item.quantity}</span>
                                        <span class="order-item-price">$${(item.price * item.quantity).toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="order-totals">
                        <div class="total-row">
                            <span>${t.subtotal}</span>
                            <span>$${totals.subtotal}</span>
                        </div>
                        <div class="total-row">
                            <span>${t.shipping}</span>
                            <span>$${totals.shipping}</span>
                        </div>
                        <div class="total-row">
                            <span>${t.tax}</span>
                            <span>$${totals.tax}</span>
                        </div>
                        <div class="total-row grand-total">
                            <span>${t.orderTotal}</span>
                            <span>$${totals.totalAmount}</span>
                        </div>
                    </div>
                </div>
            `;

            // Add form submit handler
            document.getElementById('checkoutForm').addEventListener('submit', handleSubmit);
        }

        // Handle form submission
        function handleSubmit(e) {
            e.preventDefault();

            const submitButton = document.getElementById('submitButton');
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            // Validate form
            if (!validateForm(data)) {
                return;
            }

            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner"></span>${translations[currentLang].processing}`;

            // Prepare data to send to backend
            const totals = calculateTotals();
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
                paymentMethods: data.paymentMethod ? [data.paymentMethod] : [],
                items: cart,
                totals: totals
            };

            // Submit to backend - Call PaymentController::createPayment()
            fetch('/api/payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(checkoutData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // Check if response is OK
                // if (!response.ok) {
                //     throw new Error(`HTTP error! status: ${response.status}`);
                // }
                
                // Try to parse JSON
                return response.text().then(text => {
                    console.log('Response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.error('Response was:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(result => {
                console.log('Parsed result:', result);
                
                // Check if payment creation was successful
                if (result.success) {
                    // Save order data for success page
                    const orderData = {
                        orderId: result.data.merchant_order_id,
                        paymentIntentId: result.data.id,
                        customer: data,
                        items: cart,
                        totals: totals,
                        date: new Date().toISOString(),
                        status: result.data.status,
                        amount: result.data.amount
                    };

                    // Check payment status
                    if (result.data.status === 'requires_payment_method' || result.data.status === 'requires_action') {
                        console.log('Redirecting to payment page...');
                        
                        // Check if next_action exists with redirect URL
                        if (result.data.next_action && result.data.next_action.redirect && result.data.next_action.redirect.url) {
                            const redirectUrl = result.data.next_action.redirect.url;
                            const redirectMethod = result.data.next_action.redirect.method || 'GET';
                            
                            console.log('Redirect method:', redirectMethod);
                            console.log('Redirect URL:', redirectUrl);
                            
                            if (redirectMethod.toUpperCase() === 'GET') {
                                // Direct redirect for GET method
                                window.location.href = redirectUrl;
                            } else if (redirectMethod.toUpperCase() === 'POST') {
                                // Form submission for POST method
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = redirectUrl;
                                
                                // Add form data if provided
                                if (result.data.next_action.redirect.data) {
                                    for (const [key, value] of Object.entries(result.data.next_action.redirect.data)) {
                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = key;
                                        input.value = typeof value === 'string' ? value : JSON.stringify(value);
                                        form.appendChild(input);
                                    }
                                }
                                
                                document.body.appendChild(form);
                                form.submit();
                            } else {
                                console.error('Unsupported redirect method:', redirectMethod);
                                alert('Unsupported redirect method. Please contact support.');
                                submitButton.disabled = false;
                                submitButton.innerHTML = `${translations[currentLang].confirmPay} $${totals.totalAmount}`;
                            }
                        } else if (result.data.client_secret) {
                            // Store payment intent for later use
                            localStorage.setItem('currentPaymentIntent', JSON.stringify(result.data));
                            alert('Payment intent created. Please complete payment.');
                            submitButton.disabled = false;
                            submitButton.innerHTML = `${translations[currentLang].confirmPay} $${totals.totalAmount}`;
                        } else {
                            console.error('No redirect URL found in response');
                            alert('Payment URL not found. Please contact support.');
                            submitButton.disabled = false;
                            submitButton.innerHTML = `${translations[currentLang].confirmPay} $${totals.totalAmount}`;
                        }
                        return;
                    }
                    
                    localStorage.setItem('lastOrder', JSON.stringify(orderData));
                    
                    // Clear cart
                    localStorage.removeItem('fashionCart');

                    // Redirect to success page
                    window.location.href = 'order_success.html';
                } else {
                    console.error('Payment failed:', result.data.error.message);
                    // Show error message
                    const errorMsg = result.error?.message || result.data.error.message || translations[currentLang].paymentError || 'Payment failed. Please try again.';
                    alert(errorMsg);
                    submitButton.disabled = false;
                    submitButton.innerHTML = `${translations[currentLang].confirmPay} $${totals.total}`;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert(`Error: ${error.message}\n\nPlease check the console for details.`);
                submitButton.disabled = false;
                submitButton.innerHTML = `${translations[currentLang].confirmPay} $${totals.total}`;
            });
        }

        // Validate form
        function validateForm(data) {
            if (!data.firstName || !data.lastName || !data.email) {
                alert(translations[currentLang].fillCustomerInfo);
                return false;
            }

            if (!data.address || !data.city || !data.state || !data.zipCode || !data.country) {
                alert(translations[currentLang].fillShippingAddress);
                return false;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(data.email)) {
                alert(translations[currentLang].invalidEmail);
                return false;
            }

            return true;
        }

        // Initialize
        loadCart();
        updateLanguage();
        renderCheckout();
    </script>
</body>
</html>
