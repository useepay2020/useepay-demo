/**
 * Checkout Page Internationalization - 结算页面国际化
 * 支持中文和英文的翻译文本
 */

const checkoutTranslations = {
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
        kakaoPay: 'Kakao Pay',
        kakaoPayDesc: '使用 Kakao Pay 快速支付',
        naverPay: 'Naver Pay',
        naverPayDesc: '使用 Naver Pay 快速支付',
        payco: 'Payco',
        paycoDesc: '使用 Payco 快速支付',
        toss: 'Toss',
        tossDesc: '使用 Toss 快速支付',
        onePageCheckout: '一页支付',
        onePageCheckoutDesc: '启用一页支付模式，简化结算流程',
        billingAddress: '💰 账单地址',
        sameAsShipping: '账单地址同收货地址',
        fillBillingAddress: '请填写完整的账单地址',
        cardNumber: '卡号',
        cardNumberPlaceholder: '1234 5678 9012 3456',
        expiryDate: '有效期',
        expiryPlaceholder: 'MM/YY',
        cvv: 'CVV',
        cvvPlaceholder: '123',
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
        kakaoPay: 'Kakao Pay',
        kakaoPayDesc: 'Pay quickly with Kakao Pay',
        naverPay: 'Naver Pay',
        naverPayDesc: 'Pay quickly with Naver Pay',
        payco: 'Payco',
        paycoDesc: 'Pay quickly with Payco',
        toss: 'Toss',
        tossDesc: 'Pay quickly with Toss',
        onePageCheckout: 'One-Page Checkout',
        onePageCheckoutDesc: 'Enable one-page checkout mode to simplify the checkout process',
        billingAddress: '💰 Billing Address',
        sameAsShipping: 'Same as shipping address',
        fillBillingAddress: 'Please fill in complete billing address',
        cardNumber: 'Card Number',
        cardNumberPlaceholder: '1234 5678 9012 3456',
        expiryDate: 'Expiry Date',
        expiryPlaceholder: 'MM/YY',
        cvv: 'CVV',
        cvvPlaceholder: '123',
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

/**
 * Get current language from localStorage
 * @returns {string} Current language code ('zh' or 'en')
 */
function getCurrentLanguage() {
    return localStorage.getItem('language') || 'zh';
}

/**
 * Update all elements with data-i18n attribute
 * @param {string} lang - Language code
 */
function updateLanguage(lang) {
    const elements = document.querySelectorAll('[data-i18n]');
    elements.forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (checkoutTranslations[lang] && checkoutTranslations[lang][key]) {
            el.textContent = checkoutTranslations[lang][key];
        }
    });
}

/**
 * Get product name by ID
 * @param {number} productId - Product ID
 * @param {string} lang - Language code
 * @returns {string} Product name
 */
function getProductName(productId, lang) {
    return checkoutTranslations[lang]?.products[productId]?.name || 'Product ' + productId;
}

/**
 * Get translation by key
 * @param {string} key - Translation key
 * @param {string} lang - Language code
 * @returns {string} Translation text
 */
function getTranslation(key, lang) {
    return checkoutTranslations[lang]?.[key] || key;
}
