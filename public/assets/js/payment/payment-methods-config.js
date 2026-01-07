/**
 * Payment Methods Configuration - 支付方式配置
 * 定义所有支付方式的图标、名称和描述
 * 与 home.php 中的支付方式保持一致
 */

const paymentMethodsMap = {
    'card': {
        icon: '<i class="fas fa-credit-card" style="color: #1a73e8; font-size: 1.1rem;"></i>',
        name_zh: '国际信用卡/借记卡',
        name_en: 'Credit/Debit Card',
        i18nKey: 'card'
    },
    'apple_pay': {
        icon: '<i class="fab fa-apple" style="color: #000000; font-size: 1.2rem;"></i>',
        name_zh: 'Apple Pay',
        name_en: 'Apple Pay',
        i18nKey: null
    },
    'google_pay': {
        icon: '<i class="fab fa-google" style="color: #4285F4; font-size: 1.1rem;"></i>',
        name_zh: 'Google Pay',
        name_en: 'Google Pay',
        i18nKey: null
    },
    'wechat': {
        icon: '<i class="fab fa-weixin" style="color: #09B83E; font-size: 1.2rem;"></i>',
        name_zh: '微信支付',
        name_en: 'WeChat Pay',
        i18nKey: 'wechat'
    },
    'alipay': {
        icon: '<i class="fab fa-alipay" style="color: #1677FF; font-size: 1.2rem;"></i>',
        name_zh: '支付宝',
        name_en: 'Alipay',
        i18nKey: 'alipay'
    },
    'afterpay': {
        icon: '<i class="fas fa-calendar-check" style="color: #B2FCE4; font-size: 1.1rem;"></i>',
        name_zh: 'Afterpay',
        name_en: 'Afterpay',
        i18nKey: 'afterpay'
    },
    'klarna': {
        icon: '<i class="fas fa-shopping-bag" style="color: #FFB3C7; font-size: 1.1rem;"></i>',
        name_zh: 'Klarna',
        name_en: 'Klarna',
        i18nKey: 'klarna'
    },
    'affirm': {
        icon: '<i class="fas fa-check-circle" style="color: #0FA0EA; font-size: 1.1rem;"></i>',
        name_zh: 'Affirm',
        name_en: 'Affirm',
        i18nKey: 'affirm'
    },
    'oxxo': {
        icon: '<i class="fas fa-store" style="color: #EC0000; font-size: 1.1rem;"></i>',
        name_zh: 'OXXO',
        name_en: 'OXXO',
        i18nKey: 'oxxo'
    },
    'kakao_pay': {
        icon: '<svg style="margin-right: 0.5rem; width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="11" fill="#FFE812"/><path d="M12 5C8.13 5 5 7.68 5 11c0 2.04 1.23 3.82 3.04 4.63L7.84 18l3.05-1.61c.5.08 1.02.12 1.54.12 3.87 0 7-2.68 7-6s-3.13-6-7-6z" fill="#000000"/></svg>',
        name_zh: 'Kakao Pay',
        name_en: 'Kakao Pay',
        i18nKey: 'kakaoPay'
    },
    'naver_pay': {
        icon: '<svg style="margin-right: 0.5rem; width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" fill="#00C73C"/><path d="M6 8h3v8H6V8zm5 0h3v8h-3V8zm5 0h3v8h-3V8z" fill="white"/></svg>',
        name_zh: 'Naver Pay',
        name_en: 'Naver Pay',
        i18nKey: 'naverPay'
    },
    'payco': {
        icon: '<svg style="margin-right: 0.5rem; width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" fill="#0066FF"/><text x="12" y="16" font-size="14" font-weight="bold" fill="white" text-anchor="middle">P</text></svg>',
        name_zh: 'Payco',
        name_en: 'Payco',
        i18nKey: 'payco'
    },
    'toss_pay': {
        icon: '<svg style="margin-right: 0.5rem; width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" fill="#0066FF" rx="4"/><path d="M12 6L8 14h3v4h2v-4h3L12 6z" fill="white"/></svg>',
        name_zh: 'Toss',
        name_en: 'Toss',
        i18nKey: 'toss'
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

/**
 * Render payment methods as HTML checkboxes
 * @param {Object} config - Configuration object
 * @param {Array<string>} config.methods - Array of payment method keys to render
 * @param {string} config.inputName - Name attribute for checkboxes (e.g., 'paymentMethod')
 * @param {Array<string>} config.defaultChecked - Array of method keys that should be checked by default
 * @param {string} config.containerId - ID of the container to render into (optional)
 * @returns {string} HTML string of payment method checkboxes
 */
function renderPaymentMethods(config) {
    const { methods, inputName, defaultChecked = [], containerId } = config;
    
    if (!methods || !Array.isArray(methods) || methods.length === 0) {
        console.warn('renderPaymentMethods: No methods provided');
        return '';
    }
    
    let html = '';
    
    methods.forEach(methodKey => {
        const methodInfo = getPaymentMethodInfo(methodKey);
        
        if (!methodInfo) {
            console.warn(`renderPaymentMethods: Unknown payment method '${methodKey}'`);
            return;
        }
        
        const isChecked = defaultChecked.includes(methodKey) ? 'checked' : '';
        const i18nAttr = methodInfo.i18nKey ? `data-i18n="${methodInfo.i18nKey}"` : '';
        
        html += `
            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                <input type="checkbox" name="${inputName}" value="${methodKey}" ${isChecked} style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                ${methodInfo.icon}
                <span style="font-size: 0.95rem; margin-left: 0.5rem;" ${i18nAttr}>${methodInfo.name_zh}</span>
            </label>
        `;
    });
    
    // If containerId is provided, render directly into the container
    if (containerId) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = html;
        } else {
            console.warn(`renderPaymentMethods: Container '${containerId}' not found`);
        }
    }
    
    return html;
}

/**
 * Render payment methods section with title and grid layout
 * @param {Object} config - Configuration object
 * @param {Array<string>} config.methods - Array of payment method keys to render
 * @param {string} config.inputName - Name attribute for checkboxes
 * @param {Array<string>} config.defaultChecked - Array of method keys that should be checked by default
 * @param {string} config.titleIcon - Icon HTML for the title (optional)
 * @param {string} config.titleText - Title text (optional)
 * @param {string} config.titleI18nKey - i18n key for title (optional)
 * @returns {string} Complete HTML section with title and payment methods grid
 */
function renderPaymentMethodsSection(config) {
    const {
        methods,
        inputName,
        defaultChecked = [],
        titleIcon = '<i class="fas fa-wallet"></i>',
        titleText = '选择支付方式：',
        titleI18nKey = 'selectPaymentMethod'
    } = config;
    
    const methodsHtml = renderPaymentMethods({ methods, inputName, defaultChecked });
    
    return `
        <div style="margin: 1.5rem 0; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
            <label style="display: block; margin-bottom: 1rem; font-weight: 600; color: var(--text-color);">
                ${titleIcon} <span data-i18n="${titleI18nKey}">${titleText}</span>
            </label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.8rem;">
                ${methodsHtml}
            </div>
        </div>
    `;
}
