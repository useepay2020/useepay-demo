/**
 * Payment Methods Configuration - 支付方式配置
 * 定义所有支付方式的图标、名称和描述
 * 与 home.php 中的支付方式保持一致
 */

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
    },
    'kakao_pay': {
        icon: '<i class="fas fa-comment" style="color: #FFE812;"></i>',
        name_zh: 'Kakao Pay',
        name_en: 'Kakao Pay',
        desc_zh: '使用 Kakao Pay 快速支付',
        desc_en: 'Pay quickly with Kakao Pay'
    },
    'naver_pay': {
        icon: '<i class="fas fa-shopping-bag" style="color: #00C73C;"></i>',
        name_zh: 'Naver Pay',
        name_en: 'Naver Pay',
        desc_zh: '使用 Naver Pay 快速支付',
        desc_en: 'Pay quickly with Naver Pay'
    },
    'payco': {
        icon: '<i class="fas fa-wallet" style="color: #0066FF;"></i>',
        name_zh: 'Payco',
        name_en: 'Payco',
        desc_zh: '使用 Payco 快速支付',
        desc_en: 'Pay quickly with Payco'
    },
    'toss_pay': {
        icon: '<i class="fas fa-mobile-alt" style="color: #0066FF;"></i>',
        name_zh: 'Toss',
        name_en: 'Toss',
        desc_zh: '使用 Toss 快速支付',
        desc_en: 'Pay quickly with Toss'
    },
    'samsung_pay': {
        icon: '<svg style="width: 1.2rem; height: 1.2rem; vertical-align: middle;" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="512" height="512" rx="80" fill="#1428A0"/><path d="M256 140c-50 0-90 25-90 60 0 20 15 35 40 45-30 8-50 25-50 50 0 40 45 70 100 70s100-30 100-70c0-25-20-42-50-50 25-10 40-25 40-45 0-35-40-60-90-60zm0 35c25 0 45 12 45 30s-20 30-45 30-45-12-45-30 20-30 45-30zm0 110c30 0 55 15 55 35s-25 35-55 35-55-15-55-35 25-35 55-35z" fill="white"/></svg>',
        name_zh: 'Samsung Pay',
        name_en: 'Samsung Pay',
        desc_zh: '使用 Samsung Pay 快速支付',
        desc_en: 'Pay quickly with Samsung Pay'
    },
    'tmoney': {
        icon: '<svg style="width: 1.2rem; height: 1.2rem; vertical-align: middle;" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="tmoneyGradientConfig" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#FF6B00;stop-opacity:1" /><stop offset="100%" style="stop-color:#FF9500;stop-opacity:1" /></linearGradient></defs><rect width="512" height="512" rx="80" fill="url(#tmoneyGradientConfig)"/><path d="M150 180h212v40H150zm106 80c-60 0-106 35-106 85 0 45 46 80 106 80s106-35 106-80c0-50-46-85-106-85zm0 125c-35 0-60-20-60-40s25-40 60-40 60 20 60 40-25 40-60 40z" fill="white"/><circle cx="256" cy="200" r="25" fill="white"/></svg>',
        name_zh: 'T-money',
        name_en: 'T-money',
        desc_zh: '使用 T-money 快速支付',
        desc_en: 'Pay quickly with T-money'
    }
};

/**
 * Get payment method info by key
 * @param {string} methodKey - Payment method key
 * @returns {Object|null} Payment method info or null if not found
 */
function getPaymentMethodInfo(methodKey) {
    return paymentMethodsMap[methodKey] || null;
}

/**
 * Get all payment method keys
 * @returns {Array<string>} Array of payment method keys
 */
function getAllPaymentMethodKeys() {
    return Object.keys(paymentMethodsMap);
}

/**
 * Check if payment method exists
 * @param {string} methodKey - Payment method key
 * @returns {boolean} True if method exists
 */
function hasPaymentMethod(methodKey) {
    return methodKey in paymentMethodsMap;
}
