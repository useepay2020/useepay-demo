/**
 * Apple Pay 支付模块
 * 依赖: CheckoutRenderer, PaymentResponseHandler
 */

const ApplePay = (function() {
    'use strict';

    // Apple Pay 配置 (从服务端获取)
    let config = {
        merchantIdentifier: null,
        merchantName: 'Fashion Store',
        domain: null,
        supportedNetworks: ['visa', 'masterCard', 'discover', 'amex'],
        merchantCapabilities: ['supports3DS', 'supportsDebit', 'supportsCredit']
    };

    // 当前语言
    let currentLang = 'en';

    // 翻译文本
    let translations = {};

    // 购物车数据
    let cart = [];

    /**
     * 初始化 Apple Pay
     * @param {Object} options - 配置选项
     */
    function init(options = {}) {
        currentLang = options.currentLang || 'en';
        translations = options.translations || {};
        cart = options.cart || [];
        
        loadSDK();
    }

    /**
     * 动态加载 Apple Pay SDK
     */
    function loadSDK() {
        if (document.querySelector('script[src*="apple-pay-sdk"]')) return;
        
        const script = document.createElement('script');
        script.src = 'https://applepay.cdn-apple.com/jsapi/v1/apple-pay-sdk.js';
        script.crossOrigin = 'anonymous';
        script.onload = () => {
            console.log('Apple Pay SDK loaded');
            const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked');
            if (selectedMethod && selectedMethod.value === 'apple_pay') {
                checkAvailability();
            }
        };
        script.onerror = (err) => console.error('Failed to load Apple Pay SDK:', err);
        document.head.appendChild(script);
    }

    /**
     * 获取 Apple Pay 配置
     */
    async function fetchConfiguration() {
        const totals = CheckoutRenderer.calculateTotals(cart);
        const amount = parseFloat(totals.totalAmount) || 0;
        try {
            const response = await fetch('/api/payment/apple-pay/configuration', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    currency: totals.currency || 'USD',
                    host: window.location.hostname,
                    merchant_name: 'Fashion Store',
                    os_type: 'WEB',
                    amount: amount
                })
            });
            const result = await response.json();
            console.log('Apple Pay Configuration:', result);
            if (result.success && result.data) {
                config.merchantIdentifier = result.data.acquire_merchant_id;
                config.merchantName = result.data.merchant_name || 'Fashion Store';
                config.supportedNetworks = result.data.allowed_card_networks || config.supportedNetworks;
                config.merchantCapabilities = result.data.allowed_card_auth_methods || config.merchantCapabilities;
                config.domain = result.data.domain || window.location.hostname;
            }
            return config;
        } catch (err) {
            console.error('Failed to fetch Apple Pay config:', err);
            return config;
        }
    }

    /**
     * 检测 Apple Pay 可用性
     */
    async function checkAvailability() {
        const statusEl = document.getElementById('applePayStatus');
        const buttonContainer = document.getElementById('applePayButtonContainer');
        const errorEl = document.getElementById('applePayError');
        
        if (!statusEl || !buttonContainer) return;
        
        // 检查是否支持 Apple Pay
        if (!window.ApplePaySession) {
            statusEl.textContent = currentLang === 'zh' 
                ? 'Apple Pay 在此浏览器不可用，请使用 Safari 浏览器。'
                : 'Apple Pay is not available on this browser. Please use Safari.';
            buttonContainer.style.display = 'none';
            return;
        }
        
        statusEl.textContent = currentLang === 'zh' ? '正在检查 Apple Pay...' : 'Checking Apple Pay...';
        
        // 获取配置
        await fetchConfiguration();
        
        if (!config.merchantIdentifier) {
            statusEl.textContent = currentLang === 'zh' 
                ? 'Apple Pay 配置获取失败'
                : 'Failed to get Apple Pay configuration';
            buttonContainer.style.display = 'none';
            return;
        }
        
        try {
            const canMakePayments = await ApplePaySession.canMakePaymentsWithActiveCard(config.merchantIdentifier);
            if (canMakePayments) {
                statusEl.textContent = currentLang === 'zh' 
                    ? '点击下方按钮使用 Apple Pay 支付'
                    : 'Click the button below to pay with Apple Pay';
                buttonContainer.style.display = 'flex';
            } else {
                statusEl.textContent = currentLang === 'zh' 
                    ? 'Apple Pay 可用，但当前未激活。请在钱包中添加卡片。'
                    : 'Apple Pay is available but not currently activated. Please add a card in Wallet.';
                buttonContainer.style.display = 'flex';
            }
        } catch (err) {
            console.error('Apple Pay check error:', err);
            statusEl.textContent = currentLang === 'zh' 
                ? '检查 Apple Pay 状态时出错'
                : 'Error checking Apple Pay status';
            buttonContainer.style.display = 'flex';
        }
    }

    /**
     * 构建 ApplePayPaymentRequest
     */
    function getPaymentRequest() {
        const totals = CheckoutRenderer.calculateTotals(cart);
        return {
            countryCode: 'US',
            currencyCode: totals.currency || 'USD',
            merchantCapabilities: config.merchantCapabilities,
            supportedNetworks: config.supportedNetworks,
            total: {
                label: config.merchantName,
                amount: totals.totalAmount,
                type: 'final'
            }
        };
    }

    /**
     * 发起 Apple Pay 支付
     */
    async function initiate() {
        console.log('Initiating Apple Pay...');
        
        hideError();
        
        if (!window.ApplePaySession) {
            showError(currentLang === 'zh' ? 'Apple Pay 不可用' : 'Apple Pay is not available');
            return;
        }
        
        // 验证表单数据
        const form = document.getElementById('checkoutForm');
        if (form) {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            if (!CheckoutRenderer.validateForm(data, translations, currentLang)) {
                return;
            }
        }
        
        try {
            const applePayRequest = getPaymentRequest();
            console.log('Apple Pay Request:', applePayRequest);
            const session = new ApplePaySession(14, applePayRequest);
            
            // 商户验证回调
            session.onvalidatemerchant = async (event) => {
                console.log('onvalidatemerchant - validationURL:', event.validationURL);
                
                try {
                    const response = await fetch('/api/payment/apple-pay/validate', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            displayName: config.merchantName,
                            domainName: window.location.hostname,
                            merchantIdentifier: config.merchantIdentifier,
                            validationURL: event.validationURL
                        })
                    });
                    
                    const result = await response.json();
                    console.log('Merchant session response:', result);
                    
                    if (result.success && result.data.applePaySession) {
                        session.completeMerchantValidation(result.data.applePaySession.merchantSession);
                    } else {
                        throw new Error(result.error?.message || 'Failed to validate merchant');
                    }
                } catch (err) {
                    console.error('Merchant validation error:', err);
                    session.abort();
                    showError(currentLang === 'zh' ? '商户验证失败: ' + err.message : 'Merchant validation failed: ' + err.message);
                }
            };
            
            // 支付方式选择回调
            session.onpaymentmethodselected = (event) => {
                console.log('onpaymentmethodselected:', event);
                const totals = CheckoutRenderer.calculateTotals(cart);
                session.completePaymentMethodSelection({
                    newTotal: {
                        label: config.merchantName,
                        amount: totals.totalAmount,
                        type: 'final'
                    },
                    newLineItems: []
                });
            };
            
            // 配送方式选择回调
            session.onshippingmethodselected = (event) => {
                console.log('onshippingmethodselected:', event);
                session.completeShippingMethodSelection({});
            };
            
            // 配送联系人选择回调
            session.onshippingcontactselected = (event) => {
                console.log('onshippingcontactselected:', event);
                session.completeShippingContactSelection({});
            };
            
            // 支付授权回调
            // 直接在 createPayment 时传递 Apple Pay 数据，不需要单独 confirm
            session.onpaymentauthorized = async (event) => {
                console.log('onpaymentauthorized - payment:', event.payment);
                
                try {
                    const form = document.getElementById('checkoutForm');
                    const formData = new FormData(form);
                    const data = Object.fromEntries(formData);
                    
                    const checkoutData = CheckoutRenderer.prepareCheckoutData(
                        data, cart, window.getPaymentMethods,
                        () => CheckoutRenderer.calculateTotals(cart)
                    );
                    
                    // 添加 Apple Pay 支付数据到请求中
                    checkoutData.payment_method_data = {
                        type: 'apple_pay',
                        apple_pay: {
                            merchant_identifier: config.merchantIdentifier,
                            payment: event.payment
                        }
                    };
                    
                    // 创建并确认支付（一步完成）
                    console.log('Creating and confirming payment with Apple Pay...');
                    const response = await fetch('/api/payment', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(checkoutData)
                    });
                    const result = await response.json();
                    console.log('Payment response:', result);
                    
                    const paymentStatus = result.success && result.data.status === 'succeeded';
                    session.completePayment({
                        status: paymentStatus ? ApplePaySession.STATUS_SUCCESS : ApplePaySession.STATUS_FAILURE
                    });
                    
                    if (paymentStatus) {
                        const orderData = {
                            orderId: result.data.merchant_order_id,
                            paymentIntentId: result.data.id,
                            customer: data,
                            items: cart,
                            totals: checkoutData.totals,
                            date: new Date().toISOString(),
                            status: result.data.status,
                            amount: result.data.amount
                        };
                        
                        const paymentHandler = new PaymentResponseHandler({
                            translations: translations,
                            currentLang: currentLang,
                            submitButton: document.getElementById('submitButton'),
                            totals: checkoutData.totals
                        });
                        paymentHandler.handlePaymentResult(result, orderData);
                    } else {
                        showError(result.error?.message || (currentLang === 'zh' ? '支付失败' : 'Payment failed'));
                    }
                    
                } catch (err) {
                    console.error('Payment error:', err);
                    session.completePayment({ status: ApplePaySession.STATUS_FAILURE });
                    showError(currentLang === 'zh' ? '支付处理失败: ' + err.message : 'Payment failed: ' + err.message);
                }
            };
            
            // 取消回调
            session.oncancel = (event) => {
                console.log('Apple Pay cancelled');
            };
            
            session.begin();
            
        } catch (err) {
            console.error('Apple Pay session error:', err);
            showError(currentLang === 'zh' ? '启动 Apple Pay 失败: ' + err.message : 'Failed to start Apple Pay: ' + err.message);
        }
    }

    /**
     * 显示错误信息
     */
    function showError(message) {
        const errorEl = document.getElementById('applePayError');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }

    /**
     * 隐藏错误信息
     */
    function hideError() {
        const errorEl = document.getElementById('applePayError');
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }
    }

    /**
     * 更新配置
     */
    function updateConfig(options) {
        if (options.currentLang) currentLang = options.currentLang;
        if (options.translations) translations = options.translations;
        if (options.cart) cart = options.cart;
    }

    // 公开 API
    return {
        init,
        loadSDK,
        checkAvailability,
        initiate,
        updateConfig,
        showError,
        hideError
    };
})();

// 全局函数，供 HTML onclick 调用
function initiateApplePay() {
    ApplePay.initiate();
}
