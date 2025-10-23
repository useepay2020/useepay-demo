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
        cardInfo: '💳 卡信息',
        cardNumber: '卡号',
        cardNumberPlaceholder: '1234 5678 9012 3456',
        cardHolder: '持卡人姓名',
        cardHolderPlaceholder: '如卡上所示',
        expiryDate: '有效期',
        expiryPlaceholder: 'MM/YY',
        cvv: 'CVV',
        cvvPlaceholder: '123',
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
        apiResponse: 'API 响应:',
        paymentSuccess: '支付成功！',
        paymentProcessing: '支付处理中...',
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
        backToHome: '← Back to Home',
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
        cardInfo: '💳 Card Information',
        cardNumber: 'Card Number',
        cardNumberPlaceholder: '1234 5678 9012 3456',
        cardHolder: 'Cardholder Name',
        cardHolderPlaceholder: 'As shown on card',
        expiryDate: 'Expiry Date',
        expiryPlaceholder: 'MM/YY',
        cvv: 'CVV',
        cvvPlaceholder: '123',
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
        apiResponse: 'API Response:',
        paymentSuccess: 'Payment successful!',
        paymentProcessing: 'Processing payment...',
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

// Payment method mapping
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
        desc_zh: '使用微信支付',
        desc_en: 'Pay with WeChat'
    },
    'alipay': {
        icon: '<i class="fab fa-alipay" style="color: #1677FF;"></i>',
        name_zh: '支付宝',
        name_en: 'Alipay',
        desc_zh: '使用支付宝支付',
        desc_en: 'Pay with Alipay'
    },
    'afterpay': {
        icon: '<i class="fas fa-calendar-check" style="color: #B2FCE4;"></i>',
        name_zh: 'Afterpay',
        name_en: 'Afterpay',
        desc_zh: '分期支付',
        desc_en: 'Pay in installments'
    },
    'klarna': {
        icon: '<i class="fas fa-shopping-bag" style="color: #FFB3C7;"></i>',
        name_zh: 'Klarna',
        name_en: 'Klarna',
        desc_zh: '分期支付',
        desc_en: 'Pay in installments'
    },
    'oxxo': {
        icon: '<i class="fas fa-store" style="color: #EC0000;"></i>',
        name_zh: 'OXXO',
        name_en: 'OXXO',
        desc_zh: '便利店支付',
        desc_en: 'Pay at convenience store'
    }
};

// Calculate totals
function calculateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const shipping = 10;
    const tax = subtotal * 0.08;
    const totalAmount = subtotal + shipping + tax;
    return { subtotal, shipping, tax, totalAmount, currency: 'USD' };
}

// Generate payment methods HTML
function generatePaymentMethods() {
    const cachedMethods = getPaymentMethods();
    console.log('Cached payment methods:', cachedMethods);
    
    let methodsToDisplay = [];
    if (cachedMethods && cachedMethods.length > 0) {
        methodsToDisplay = [...cachedMethods];
        console.log('Using cached methods:', methodsToDisplay);
    } else {
        methodsToDisplay = ['card', 'apple_pay'];
        console.log('No cached methods, using default methods:', methodsToDisplay);
    }
    
    return methodsToDisplay.map((method, index) => {
        const methodInfo = paymentMethodsMap[method];
        if (!methodInfo) {
            console.warn('Unknown payment method:', method);
            return '';
        }
        
        const methodName = currentLang === 'zh' ? methodInfo.name_zh : methodInfo.name_en;
        const methodDesc = currentLang === 'zh' ? methodInfo.desc_zh : methodInfo.desc_en;
        const isFirst = index === 0;
        
        let html = `
            <div class="payment-option">
                <input type="radio" id="method_${method}" name="paymentMethod" value="${method}" ${isFirst ? 'checked' : ''} onchange="handlePaymentMethodChange('${method}')">
                <label for="method_${method}">
                    <div class="payment-icon" style="font-size: 1.2rem;">${methodInfo.icon}</div>
                    <div class="payment-info">
                        <div class="payment-name">${methodName}</div>
                        <div class="payment-desc">${methodDesc}</div>
                    </div>
                </label>
            </div>
        `;
        
        // 如果是信用卡，添加卡信息表单
        if (method === 'card') {
            html += `
            <div class="card-info-section ${isFirst ? 'active' : ''}" id="cardInfoSection_${method}">
                <div class="card-row">
                    <div class="form-group full-width">
                        <label><span data-i18n="cardNumber">卡号</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" value="4532 1234 5678 9010" oninput="updateCardPreview()">
                    </div>
                </div>

                <div class="card-row">
                    <div class="form-group">
                        <label><span data-i18n="cardHolder">持卡人姓名</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="cardHolder" placeholder="As shown on card" value="JOHN DOE" oninput="updateCardPreview()">
                    </div>
                    <div class="form-group">
                        <label><span data-i18n="expiryDate">有效期</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="expiryDate" placeholder="MM/YY" maxlength="5" value="12/25" oninput="updateCardPreview()">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><span data-i18n="cvv">CVV</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="cvv" placeholder="123" maxlength="4" value="123">
                    </div>
                </div>
            </div>
            `;
        }
        
        return html;
    }).join('');
}

// Handle payment method change
function handlePaymentMethodChange(method) {
    // 隐藏所有卡信息部分
    document.querySelectorAll('.card-info-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // 如果选择信用卡，显示对应的卡信息部分
    if (method === 'card') {
        const cardSection = document.getElementById('cardInfoSection_card');
        if (cardSection) {
            cardSection.classList.add('active');
        }
    }
}

// Update card preview
function updateCardPreview() {
    const cardNumber = document.getElementById('cardNumber')?.value || '•••• •••• •••• ••••';
    const cardHolder = document.getElementById('cardHolder')?.value || 'CARDHOLDER NAME';
    const expiryDate = document.getElementById('expiryDate')?.value || 'MM/YY';
    
    const previewNumber = document.getElementById('previewCardNumber');
    const previewHolder = document.getElementById('previewCardHolder');
    const previewExpiry = document.getElementById('previewExpiryDate');
    
    if (previewNumber) previewNumber.textContent = cardNumber;
    if (previewHolder) previewHolder.textContent = cardHolder.toUpperCase();
    if (previewExpiry) previewExpiry.textContent = expiryDate;
}

// Show API response
function showApiResponse(data, isSuccess = true) {
    const responseDiv = document.getElementById('apiResponse');
    if (responseDiv) {
        responseDiv.textContent = JSON.stringify(data, null, 2);
        responseDiv.classList.add('show');
        responseDiv.classList.toggle('success', isSuccess);
        responseDiv.classList.toggle('error', !isSuccess);
    }
}

// Show loading state
function showLoading(show = true) {
    const loading = document.getElementById('loadingIndicator');
    const button = document.querySelector('button[onclick="submitCheckout()"]');
    if (loading) {
        loading.classList.toggle('show', show);
    }
    if (button) {
        button.disabled = show;
        button.textContent = show ? translations[currentLang].processing : translations[currentLang].confirmPay;
    }
}

// Render checkout page
function renderCheckout() {
    const content = document.getElementById('checkoutContent');

    if (cart.length === 0) {
        content.innerHTML = `
            <div class="checkout-form" style="grid-column: 1 / -1; text-align: center; padding: 60px 30px;">
                <div style="font-size: 48px; margin-bottom: 20px;">🛒</div>
                <h2 data-i18n="cartEmpty">您的购物车是空的</h2>
                <a href="/payment/clothing-shop" class="btn-primary" style="display: inline-block; margin-top: 20px; text-decoration: none; padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px;">
                    <span data-i18n="startShopping">开始购物</span>
                </a>
            </div>
        `;
        updateLanguage();
        return;
    }

    const { subtotal, shipping, tax, totalAmount, currency } = calculateTotals();

    content.innerHTML = `
        <div class="checkout-form">
            <h2 class="section-title" data-i18n="checkoutInfo">结算信息</h2>
            
            <div class="form-section">
                <h3 data-i18n="customerInfo">👤 客户信息</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label><span data-i18n="firstName">名字</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="firstName" placeholder="John" value="John">
                    </div>
                    <div class="form-group">
                        <label><span data-i18n="lastName">姓氏</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="lastName" placeholder="Doe" value="Doe">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><span data-i18n="email">电子邮箱</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="email" id="email" placeholder="john@example.com" value="john@example.com">
                    </div>
                    <div class="form-group">
                        <label><span data-i18n="phone">联系电话</span></label>
                        <input type="tel" id="phone" placeholder="+1 (555) 000-0000" value="+1 (555) 000-0000">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 data-i18n="shippingAddress">📍 收货地址</h3>
                <div class="form-row">
                    <div class="form-group full-width">
                        <label><span data-i18n="address">详细地址</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="address" placeholder="Street Address" value="123 Main Street">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><span data-i18n="city">城市</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="city" placeholder="City" value="New York">
                    </div>
                    <div class="form-group">
                        <label><span data-i18n="state">州/省</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="state" placeholder="State/Province" value="NY">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><span data-i18n="zipCode">邮政编码</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="zipCode" placeholder="12345" value="10001">
                    </div>
                    <div class="form-group">
                        <label><span data-i18n="country">国家</span> <span class="required" data-i18n="required">*</span></label>
                        <select id="country">
                            <option value="">Select Country</option>
                            <option value="US" selected>United States</option>
                            <option value="CN">China</option>
                            <option value="GB">United Kingdom</option>
                            <option value="CA">Canada</option>
                            <option value="AU">Australia</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 data-i18n="paymentMethod">💳 支付方式</h3>
                <div class="payment-methods">
                    ${generatePaymentMethods()}
                </div>
            </div>

            <div id="loadingIndicator" class="loading">
                <div class="spinner"></div>
                <p style="margin-top: 10px; color: #667eea;" data-i18n="paymentProcessing">支付处理中...</p>
            </div>

            <div id="apiResponse" class="api-response"></div>

            <button onclick="submitCheckout()" data-i18n="confirmPay">确认并支付 $${totalAmount.toFixed(2)}</button>
        </div>

        <div class="order-summary">
            <h3 style="font-size: 20px; font-weight: bold; color: #2d3436; margin-bottom: 20px;" data-i18n="orderSummary">订单摘要</h3>
            <div>
                ${cart.map(item => `
                    <div class="order-item">
                        <div class="order-item-image">👕</div>
                        <div class="order-item-details">
                            <div class="order-item-name">${getProductName(item.id)}</div>
                            <div class="order-item-price">$${item.price.toFixed(2)} x <span data-i18n="quantity">数量</span> ${item.quantity}</div>
                        </div>
                    </div>
                `).join('')}
            </div>
            <div class="order-summary-divider"></div>
            <div class="summary-row">
                <span data-i18n="subtotal">商品小计:</span>
                <span>$${subtotal.toFixed(2)}</span>
            </div>
            <div class="summary-row">
                <span data-i18n="shipping">运费:</span>
                <span>$${shipping.toFixed(2)}</span>
            </div>
            <div class="summary-row">
                <span data-i18n="tax">税费 (8%):</span>
                <span>$${tax.toFixed(2)}</span>
            </div>
            <div class="summary-row total">
                <span data-i18n="orderTotal">订单总计:</span>
                <span class="amount">$${totalAmount.toFixed(2)}</span>
            </div>
        </div>
    `;

    updateLanguage();
}

// Submit checkout via API
async function submitCheckout() {
    const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
    
    if (!selectedMethod) {
        alert(translations[currentLang].paymentError);
        return;
    }

    // Validate customer info
    const firstName = document.getElementById('firstName')?.value;
    const lastName = document.getElementById('lastName')?.value;
    const email = document.getElementById('email')?.value;
    const phone = document.getElementById('phone')?.value;
    const address = document.getElementById('address')?.value;
    const city = document.getElementById('city')?.value;
    const state = document.getElementById('state')?.value;
    const zipCode = document.getElementById('zipCode')?.value;
    const country = document.getElementById('country')?.value;

    if (!firstName || !lastName || !email || !address || !city || !state || !zipCode || !country) {
        alert(translations[currentLang].fillCustomerInfo);
        return;
    }

    if (!email.includes('@')) {
        alert(translations[currentLang].invalidEmail);
        return;
    }

    // Validate card info if credit card selected
    let cardData = null;
    if (selectedMethod === 'card') {
        const cardNumber = document.getElementById('cardNumber')?.value;
        const cardHolder = document.getElementById('cardHolder')?.value;
        const expiryDate = document.getElementById('expiryDate')?.value;
        const cvv = document.getElementById('cvv')?.value;

        if (!cardNumber || !cardHolder || !expiryDate || !cvv) {
            alert(translations[currentLang].fillCustomerInfo);
            return;
        }

        cardData = { cardNumber, cardHolder, expiryDate, cvv };
    }

    // Prepare payment data
    const paymentData = {
        method: selectedMethod,
        customer: { firstName, lastName, email, phone },
        shipping: { address, city, state, zipCode, country },
        cart: cart,
        card: cardData
    };

    console.log('Submitting payment via API:', paymentData);

    // Show loading state
    showLoading(true);

    try {
        // Send to API handler
        const response = await fetch('/api/payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'process_payment',
                data: paymentData
            })
        });

        const result = await response.json();
        console.log('API Response:', result);

        if (response.ok && result.success) {
            showApiResponse(result, true);
            alert(translations[currentLang].paymentSuccess);
            // Clear cart
            localStorage.removeItem('fashionCart');
            setTimeout(() => {
                window.location.href = '/payment/clothing-shop';
            }, 2000);
        } else {
            showApiResponse(result, false);
            alert(result.message || translations[currentLang].paymentError);
        }
    } catch (error) {
        console.error('Payment error:', error);
        showApiResponse({ error: error.message }, false);
        alert(translations[currentLang].paymentError);
    } finally {
        showLoading(false);
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    renderCheckout();
    updateLanguage();
    
    // Check if card should be shown by default
    const firstMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
    if (firstMethod === 'card') {
        handlePaymentMethodChange('card');
    }
});
