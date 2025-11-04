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

// Load payment methods from cache based on action type
function getPaymentMethods() {
    // 获取操作类型
    const actionType = localStorage.getItem('paymentActionType');
    console.log('Current action type:', actionType);
    
    // 根据操作类型选择对应的缓存键
    let cacheKey = 'paymentMethods'; // 默认为支付方式
    if (actionType === 'subscription') {
        cacheKey = 'subscriptionMethods';
    } else if (actionType === 'installment') {
        cacheKey = 'installmentMethods';
    }
    
    const cached = localStorage.getItem(cacheKey);
    console.log(`Loading ${cacheKey} from cache:`, cached);
    
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


// Calculate totals
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
        // Execute createPaymentIntent when card payment method is selected
    }
}

// Note: initializeUseepayElements is now provided by useepay-elements-initializer.js
// This file uses the global function from that module


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
                        <input type="text" id="firstName" placeholder="John">
                    </div>
                    <div class="form-group">
                        <label><span data-i18n="lastName">姓氏</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="lastName" placeholder="Doe">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><span data-i18n="email">电子邮箱</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="email" id="email" placeholder="john@example.com">
                    </div>
                    <div class="form-group">
                        <label><span data-i18n="phone">联系电话</span></label>
                        <input type="tel" id="phone" placeholder="+1 (555) 000-0000">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 data-i18n="shippingAddress">📍 收货地址</h3>
                <div class="form-row">
                    <div class="form-group full-width">
                        <label><span data-i18n="address">详细地址</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="address" placeholder="Street Address">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><span data-i18n="city">城市</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="city" placeholder="City">
                    </div>
                    <div class="form-group">
                        <label><span data-i18n="state">州/省</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="state" placeholder="State/Province">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><span data-i18n="zipCode">邮政编码</span> <span class="required" data-i18n="required">*</span></label>
                        <input type="text" id="zipCode" placeholder="12345">
                    </div>
                    <div class="form-group">
                        <label><span data-i18n="country">国家</span> <span class="required" data-i18n="required">*</span></label>
                        <select id="country">
                            <option value="" selected>Select Country</option>
                            <option value="US">United States</option>
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
                <div id="payment-element" style="margin: 20px 0;"></div>
            </div>

            <button onclick="handlePaymentSubmit()" data-i18n="confirmPay">确认并支付 $${totalAmount}</button>
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
                <span>$${subtotal}</span>
            </div>
            <div class="summary-row">
                <span data-i18n="shipping">运费:</span>
                <span>$${shipping}</span>
            </div>
            <div class="summary-row">
                <span data-i18n="tax">税费 (8%):</span>
                <span>$${tax}</span>
            </div>
            <div class="summary-row total">
                <span data-i18n="orderTotal">订单总计:</span>
                <span class="amount">$${totalAmount}</span>
            </div>
        </div>
    `;

    updateLanguage();
}

/**
 * Handle payment submission - orchestrates the payment flow
 */
function handlePaymentSubmit() {
    const existingIntent = sessionStorage.getItem('currentPaymentIntent');
    if (!existingIntent) {
        // Create new payment intent
        createPaymentIntent();

    }
    // Payment intent already exists, proceed to confirmation
    try {
        const paymentIntent = JSON.parse(existingIntent);
        confirmPaymentIntent(paymentIntent);
    } catch (e) {
        console.error('Error parsing payment intent:', e);
        alert(translations[currentLang].paymentError);
    }
}

// Create payment intent - Reference checkout.php handleSubmit()
function createPaymentIntent() {
    clearPaymentIntentCache();

    const firstName = document.getElementById('firstName')?.value;
    const lastName = document.getElementById('lastName')?.value;
    const email = document.getElementById('email')?.value;
    const phone = document.getElementById('phone')?.value;
    const address = document.getElementById('address')?.value;
    const city = document.getElementById('city')?.value;
    const state = document.getElementById('state')?.value;
    const zipCode = document.getElementById('zipCode')?.value;
    const country = document.getElementById('country')?.value;

    // Prepare data to send to backend - Reference checkout.php
    const totals = calculateTotals();
    
    // Get payment methods from local cache
    const paymentMethods = getPaymentMethods();
    console.log('Payment methods from cache:', paymentMethods);
    
    const checkoutData = {
        firstName: firstName,
        lastName: lastName,
        email: email,
        address: address,
        city: city,
        state: state,
        zipCode: zipCode,
        country: country,
        phone: phone,
        items: cart,
        totals: totals,
        paymentMethods: paymentMethods
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
        if (result.success && result.data) {
            // Cache payment intent data to browser memory
            console.log('Caching payment intent data:', result.data);
            
            // Store in sessionStorage for current session
            sessionStorage.setItem('currentPaymentIntent', JSON.stringify(result.data));
            console.log('✓ Payment intent created and cached:', result.data.id);
            
            // For card payment method, initialize UseePay Elements
            initializeUseepayElements(result.data.client_secret, result.data.id);

        } else {
            console.error('Payment failed:', result.data.error.message);
            // Show error message
            const errorMsg = result.error?.message || result.data.error.message || translations[currentLang].paymentError || 'Payment failed. Please try again.';
            alert(errorMsg);
        }
    })
    .catch(error => {
        console.error('Payment creation error:', error);
        alert(translations[currentLang].paymentError + ': ' + error.message);
    })
    .finally(() => {
        // Restore button state
        if (submitButton) {
            submitButton.disabled = false;
            const totals = calculateTotals();
            submitButton.textContent = `${translations[currentLang].confirmPay} $${totals.totalAmount}`;
        }
    });
}

/**
 * Confirm payment intent - Step 2 of embedded checkout
 * @param {object} paymentIntent - Payment intent data from backend
 */
// async function confirmPaymentIntent(paymentIntent) {
//     console.log('Confirming payment intent:', paymentIntent.id);
//
//     if (!paymentIntent || !paymentIntent.id) {
//         console.error('Invalid payment intent');
//         alert(translations[currentLang].paymentError);
//         return;
//     }
//
//     const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
//
//     try {
//         // For card payments, use UseePay SDK to confirm
//         if (paymentMethod === 'card') {
//             if (!useepayInstance || !useepayElements) {
//                 console.error('UseePay Elements not initialized');
//                 alert('Payment form not ready. Please refresh the page.');
//                 return;
//             }
//
//             console.log('Confirming payment with UseePay SDK...');
//             const { paymentIntent: confirmedIntent, error } = await useepayInstance.confirmPayment({
//                 elements: useepayElements,
//                 redirect: 'if_required'
//             });
//
//             if (error) {
//                 console.error('Payment confirmation error:', error);
//                 const messageElement = document.getElementById('payment-message');
//                 if (messageElement) {
//                     messageElement.textContent = error.message;
//                     messageElement.style.display = 'block';
//                 }
//                 alert(translations[currentLang].paymentError + ': ' + error.message);
//             } else if (confirmedIntent) {
//                 console.log('✓ Payment confirmed:', confirmedIntent);
//
//                 // Update cached payment intent with final status
//                 sessionStorage.setItem('currentPaymentIntent', JSON.stringify(confirmedIntent));
//
//                 // Check payment status
//                 if (confirmedIntent.status === 'succeeded') {
//                     console.log('✓ Payment succeeded');
//                     window.location.href = '/payment/result?status=success&payment_id=' + paymentIntent.id;
//                 } else if (confirmedIntent.status === 'requires_action') {
//                     console.log('Payment requires additional action');
//                     alert('Payment requires additional action. Please complete the verification.');
//                 } else {
//                     console.log('Payment status:', confirmedIntent.status);
//                     alert('Payment status: ' + confirmedIntent.status);
//                 }
//             }
//         } else {
//             // For other payment methods, use backend confirmation
//             const confirmData = {
//                 payment_method: paymentMethod,
//                 return_url: window.location.origin + '/payment/result'
//             };
//
//             const response = await fetch(`/api/payment/${paymentIntent.id}/confirm`, {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/json',
//                 },
//                 body: JSON.stringify(confirmData)
//             });
//
//             const result = await response.json();
//             console.log('Payment confirmation result:', result);
//
//             if (result.status === 'succeeded' || result.payment?.status === 'succeeded') {
//                 console.log('✓ Payment succeeded');
//                 sessionStorage.setItem('currentPaymentIntent', JSON.stringify(result.payment || result));
//                 window.location.href = '/payment/result?status=success&payment_id=' + paymentIntent.id;
//             } else if (result.status === 'requires_action' || result.payment?.status === 'requires_action') {
//                 console.log('Payment requires additional action');
//                 alert('Payment requires additional action. Please complete the verification.');
//             } else {
//                 throw new Error(result.message || 'Payment confirmation failed');
//             }
//         }
//     } catch (error) {
//         console.error('Payment confirmation error:', error);
//         alert(translations[currentLang].paymentError + ': ' + error.message);
//     }
// }

// ============================================
// Payment Intent Cache Management Functions
// ============================================

/**
 * Get payment intent from cache
 * @param {string} source - 'window', 'session', or 'local' (default: 'window')
 * @returns {object|null} Payment intent data or null if not found
 */
function getPaymentIntentFromCache(source = 'session') {
    try {
        switch(source.toLowerCase()) {
            case 'session':
                const sessionData = sessionStorage.getItem('currentPaymentIntent');
                return sessionData ? JSON.parse(sessionData) : null;
        }
    } catch (e) {
        console.error('Error retrieving payment intent from cache:', e);
        return null;
    }
}

/**
 * Clear payment intent cache
 * @param {string} source - 'all', 'window', 'session', or 'local' (default: 'all')
 */
function clearPaymentIntentCache(source = 'session') {
    try {
        switch(source.toLowerCase()) {
            case 'session':
                sessionStorage.removeItem('currentPaymentIntent');
                console.log('✓ Cleared sessionStorage cache');
                break;
        }
    } catch (e) {
        console.error('Error clearing payment intent cache:', e);
    }
}

/**
 * Get payment intent ID from cache
 * @returns {string|null} Payment intent ID or null if not found
 */
function getPaymentIntentId() {
    const paymentIntent = getPaymentIntentFromCache('session');
    return paymentIntent ? paymentIntent.id : null;
}

/**
 * Get client secret from cache
 * @returns {string|null} Client secret or null if not found
 */
function getClientSecret() {
    const paymentIntent = getPaymentIntentFromCache('session');
    return paymentIntent ? paymentIntent.client_secret : null;
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    renderCheckout();
    updateLanguage();

    // Check if card should be shown by default
    // const firstMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
    // handlePaymentMethodChange(firstMethod);
    createPaymentIntent();
});
