/**
 * Google Pay 支付模块
 * 依赖: CheckoutRenderer, PaymentResponseHandler
 */

const GooglePay = (function() {
    'use strict';

    // Google Pay 配置
    // allowedCardNetworks 和 allowedCardAuthMethods 从 payment configuration 读取
    // 其他参数写死
    let config = {
        // 从服务端读取
        allowedCardNetworks: [],
        allowedCardAuthMethods: [],
        // 固定参数
        baseRequest: {
            apiVersion: 2,
            apiVersionMinor: 0
        },
        tokenizationSpecification: {
            type: 'PAYMENT_GATEWAY',
            parameters: {
                gateway: 'useepay',
                gatewayMerchantId: 'BCR2DN4T7LTNVTBU'
            }
        },
        merchantName: '',
        merchantId: ''
    };

    // Google Pay 客户端
    let paymentsClient = null;

    // 当前语言
    let currentLang = 'en';

    // 翻译文本
    let translations = {};

    // 购物车数据
    let cart = [];

    /**
     * 初始化 Google Pay
     * @param {Object} options - 配置选项
     */
    function init(options = {}) {
        currentLang = options.currentLang || 'en';
        translations = options.translations || {};
        cart = options.cart || [];
        
        loadSDK();
    }

    /**
     * 动态加载 Google Pay SDK
     */
    function loadSDK() {
        if (document.querySelector('script[src*="pay.google.com"]')) return;
        
        const script = document.createElement('script');
        script.src = 'https://pay.google.com/gp/p/js/pay.js';
        script.async = true;
        script.onload = () => {
            console.log('Google Pay SDK loaded');
            const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked');
            if (selectedMethod && selectedMethod.value === 'google_pay') {
                checkAvailability();
            }
        };
        script.onerror = (err) => console.error('Failed to load Google Pay SDK:', err);
        document.head.appendChild(script);
    }

    /**
     * 获取 Google Pay 配置
     * 只读取 allowedCardNetworks 和 allowedCardAuthMethods
     */
    async function fetchConfiguration() {
        const totals = CheckoutRenderer.calculateTotals(cart);
        const amount = parseFloat(totals.totalAmount) || 0;
        try {
            const response = await fetch('/api/payment/google-pay/configuration', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    currency: totals.currency || 'USD',
                    host: window.location.hostname,
                    // merchant_name: 'Fashion Store',
                    os_type: 'WEB',
                    amount: amount
                })
            });
            const result = await response.json();
            console.log('Google Pay Configuration:', result);
            if (result.success && result.data) {
                if (result.data.allowed_card_networks) {
                    config.allowedCardNetworks = result.data.allowed_card_networks;
                }
                if (result.data.allowed_card_auth_methods) {
                    config.allowedCardAuthMethods = result.data.allowed_card_auth_methods;
                }
            }
            return config;
        } catch (err) {
            console.error('Failed to fetch Google Pay config:', err);
            return config;
        }
    }

    /**
     * 获取基础卡支付方式配置
     */
    function getBaseCardPaymentMethod() {
        return {
            type: 'CARD',
            parameters: {
                allowedAuthMethods: config.allowedCardAuthMethods,
                allowedCardNetworks: config.allowedCardNetworks
            }
        };
    }

    /**
     * 获取完整卡支付方式配置（包含 tokenization）
     */
    function getCardPaymentMethod() {
        return {
            ...getBaseCardPaymentMethod(),
            tokenizationSpecification: config.tokenizationSpecification
        };
    }

    /**
     * 获取 Google Pay 客户端
     */
    function getPaymentsClient() {
        if (!paymentsClient && window.google?.payments?.api) {
            const environment = window.location.hostname.includes('localhost') || 
                               window.location.hostname.includes('dev') || 
                               window.location.hostname.includes('uat') ? 'TEST' : 'PRODUCTION';
            console.log('Google Pay environment:', environment);
            paymentsClient = new google.payments.api.PaymentsClient({ environment });
        }
        return paymentsClient;
    }

    /**
     * 检测 Google Pay 可用性
     */
    async function checkAvailability() {
        const statusEl = document.getElementById('googlePayStatus');
        const buttonContainer = document.getElementById('googlePayButtonContainer');
        if (!statusEl || !buttonContainer) return;
        
        if (!window.google?.payments?.api) {
            statusEl.textContent = currentLang === 'zh' 
                ? 'Google Pay SDK 加载中...'
                : 'Loading Google Pay SDK...';
            return;
        }
        
        statusEl.textContent = currentLang === 'zh' ? '正在检查 Google Pay...' : 'Checking Google Pay...';
        
        await fetchConfiguration();
        
        const client = getPaymentsClient();
        if (!client) {
            statusEl.textContent = currentLang === 'zh' 
                ? 'Google Pay 初始化失败'
                : 'Failed to initialize Google Pay';
            return;
        }
        
        try {
            const isReadyToPayRequest = {
                ...config.baseRequest,
                allowedPaymentMethods: [getBaseCardPaymentMethod()]
            };
            
            const response = await client.isReadyToPay(isReadyToPayRequest);
            console.log('Google Pay isReadyToPay:', response);
            
            if (response.result) {
                statusEl.textContent = currentLang === 'zh' 
                    ? '点击下方按钮使用 Google Pay 支付'
                    : 'Click the button below to pay with Google Pay';
                addButton();
            } else {
                statusEl.textContent = currentLang === 'zh' 
                    ? 'Google Pay 在此设备不可用'
                    : 'Google Pay is not available on this device';
            }
        } catch (err) {
            console.error('Google Pay check error:', err);
            statusEl.textContent = currentLang === 'zh' 
                ? '检查 Google Pay 状态时出错'
                : 'Error checking Google Pay status';
        }
    }

    /**
     * 添加 Google Pay 按钮
     */
    function addButton() {
        const container = document.getElementById('googlePayButtonContainer');
        if (!container) return;
        
        container.innerHTML = '';
        
        const client = getPaymentsClient();
        if (!client) return;
        
        const button = client.createButton({
            buttonColor: 'black',
            buttonType: 'plain',
            buttonSizeMode: 'fill',
            onClick: onButtonClicked,
            allowedPaymentMethods: [getBaseCardPaymentMethod()]
        });
        
        container.appendChild(button);
        console.log('Google Pay button added');
    }

    /**
     * Google Pay 按钮点击处理
     * 注意：必须是同步函数，loadPaymentData 必须在用户点击的同步上下文中调用
     */
    function onButtonClicked() {
        console.log('Google Pay button clicked');
        
        hideError();
        
        // 验证表单数据（同步操作）
        const form = document.getElementById('checkoutForm');
        if (form) {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            if (!CheckoutRenderer.validateForm(data, translations, currentLang)) {
                return;
            }
        }
        
        const client = getPaymentsClient();
        if (!client) {
            showError(currentLang === 'zh' ? 'Google Pay 未初始化' : 'Google Pay not initialized');
            return;
        }
        
        const totals = CheckoutRenderer.calculateTotals(cart);
        
        const paymentDataRequest = {
            ...config.baseRequest,
            allowedPaymentMethods: [getCardPaymentMethod()],
            transactionInfo: {
                countryCode: 'US',
                currencyCode: totals.currency || 'USD',
                totalPriceStatus: 'FINAL',
                totalPrice: totals.totalAmount
            },
            merchantInfo: {
                merchantId: config.merchantId,
                merchantName: config.merchantName
            }
        };
        
        console.log('Google Pay payment request:', paymentDataRequest);
        
        // 必须同步调用 loadPaymentData，使用 .then().catch() 而不是 await
        client.loadPaymentData(paymentDataRequest)
            .then(function(paymentData) {
                console.log('Google Pay payment data:', paymentData);
                return processPayment(paymentData);
            })
            .catch(function(err) {
                console.error('Google Pay error:', err);
                if (err.statusCode !== 'CANCELED') {
                    showError(currentLang === 'zh' ? 'Google Pay 支付失败: ' + (err.statusMessage || err.message) : 'Google Pay failed: ' + (err.statusMessage || err.message));
                }
            });
    }

    /**
     * 处理 Google Pay 支付
     * 直接在 createPayment 时传递 Google Pay 数据，不需要单独 confirm
     */
    async function processPayment(paymentData) {
        try {
            const form = document.getElementById('checkoutForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            const checkoutData = CheckoutRenderer.prepareCheckoutData(
                data, cart, window.getPaymentMethods,
                () => CheckoutRenderer.calculateTotals(cart)
            );
            
            // 添加 Google Pay 支付数据到请求中
            checkoutData.payment_method_data = {
                type: 'google_pay',
                google_pay: {
                    encrypt_payment_data: paymentData.paymentMethodData.tokenizationData.token
                }
            };
            
            // 创建并确认支付（一步完成）
            console.log('Creating and confirming payment with Google Pay...');
            const response = await fetch('/api/payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(checkoutData)
            });
            const result = await response.json();
            console.log('Payment response:', result);
            
            if (!result.success) {
                throw new Error(result.error?.message || 'Payment failed');
            }
            
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
            
            // 检查是否需要 3DS 验证
            if (result.data.next_action && result.data.next_action.redirect) {
                console.log('3DS verification required:', result.data.next_action);
                paymentHandler.handlePaymentResult(result, orderData);
            } else if (result.data.status === 'succeeded') {
                // 支付成功
                paymentHandler.handlePaymentResult(result, orderData);
            } else {
                showError(result.error?.message || (currentLang === 'zh' ? '支付失败' : 'Payment failed'));
            }
            
        } catch (err) {
            console.error('Google Pay payment error:', err);
            showError(currentLang === 'zh' ? '支付处理失败: ' + err.message : 'Payment failed: ' + err.message);
        }
    }

    /**
     * 显示错误信息
     */
    function showError(message) {
        const errorEl = document.getElementById('googlePayError');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }

    /**
     * 隐藏错误信息
     */
    function hideError() {
        const errorEl = document.getElementById('googlePayError');
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
        addButton,
        updateConfig,
        showError,
        hideError
    };
})();
