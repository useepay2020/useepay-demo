<?php
/**
 * API Checkout Page - 纯 API 对接的结算页面
 * 基于 embedded_checkout.php 的逻辑，使用纯 API 方式处理支付
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>结算 - Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/payment/checkout.css">
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

<!-- Internationalization -->
<script src="/assets/js/i18n/payment/checkout-i18n.js"></script>
<!-- Payment Methods Configuration -->
<script src="/assets/js/payment/payment-methods-config.js"></script>
<!-- Checkout Renderer -->
<script src="/assets/js/payment/checkout-renderer.js"></script>
<script>
    // Use translations from i18n file
    const translations = checkoutTranslations;
    let currentLang = getCurrentLanguage();

    // Load cart from localStorage
    let cart = [];

    function loadCart() {
        const saved = localStorage.getItem('fashionCart');
        if (saved) {
            cart = JSON.parse(saved);
        }
    }

    // Get product name
    function getProductName(productId) {
        return translations[currentLang].products[productId]?.name || 'Product ' + productId;
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

    // Generate payment methods HTML - 生成支付方式 HTML
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
                const t = translations[currentLang];
                html += `
                <div class="card-info-section ${isFirst ? 'active' : ''}" id="cardInfoSection_${method}">
                    <div class="card-row">
                        <div class="form-group full-width">
                            <label><span data-i18n="cardNumber">${t.cardNumber}</span> <span class="required" data-i18n="required">*</span></label>
                            <input type="text" id="cardNumber" placeholder="${t.cardNumberPlaceholder}" maxlength="19" value="4111 1111 1111 1111" oninput="updateCardPreview()">
                        </div>
                    </div>

                    <div class="card-row">
                        <div class="form-group">
                            <label><span data-i18n="expiryDate">${t.expiryDate}</span> <span class="required" data-i18n="required">*</span></label>
                            <input type="text" id="expiryDate" placeholder="${t.expiryPlaceholder}" maxlength="5" value="12/25" oninput="updateCardPreview()">
                        </div>
                        <div class="form-group">
                            <label><span data-i18n="cvv">${t.cvv}</span> <span class="required" data-i18n="required">*</span></label>
                            <input type="text" id="cvv" placeholder="${t.cvvPlaceholder}" maxlength="4" value="123">
                        </div>
                    </div>
                </div>
                `;
            }

            // 如果是 Apple Pay，添加 Apple Pay 按钮区域
            if (method === 'apple_pay') {
                html += `
                <div class="apple-pay-section" id="applePaySection" style="display: none; padding: 15px; margin-top: 10px;">
                    <div id="applePayStatus" style="margin-bottom: 10px; color: #666;"></div>
                    <div id="applePayButtonContainer" style="display: flex; justify-content: center;">
                        <apple-pay-button 
                            buttonstyle="black" 
                            type="plain" 
                            locale="${currentLang === 'zh' ? 'zh-CN' : 'en-US'}"
                            onclick="initiateApplePay()"
                            style="--apple-pay-button-width: 100%; --apple-pay-button-height: 44px; --apple-pay-button-border-radius: 8px; cursor: pointer;">
                        </apple-pay-button>
                    </div>
                    <div id="applePayError" style="margin-top: 10px; color: #dc3545; display: none;"></div>
                </div>
                `;
            }

            // 如果是 Google Pay，添加 Google Pay 按钮区域
            if (method === 'google_pay') {
                html += `
                <div class="google-pay-section" id="googlePaySection" style="display: none; padding: 15px; margin-top: 10px;">
                    <div id="googlePayStatus" style="margin-bottom: 10px; color: #666;"></div>
                    <div id="googlePayButtonContainer" style="display: flex; justify-content: center; width: 100%; height: 44px;"></div>
                    <div id="googlePayError" style="margin-top: 10px; color: #dc3545; display: none;"></div>
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
        
        // 隐藏 Apple Pay 区域
        const applePaySection = document.getElementById('applePaySection');
        if (applePaySection) {
            applePaySection.style.display = 'none';
        }
        
        // 隐藏 Google Pay 区域
        const googlePaySection = document.getElementById('googlePaySection');
        if (googlePaySection) {
            googlePaySection.style.display = 'none';
        }
        
        // 获取原始提交按钮
        const submitButton = document.getElementById('submitButton');
        
        // 如果选择信用卡，显示对应的卡信息部分，显示原始提交按钮
        if (method === 'card') {
            const cardSection = document.getElementById('cardInfoSection_card');
            if (cardSection) {
                cardSection.classList.add('active');
            }
            if (submitButton) {
                submitButton.style.display = 'block';
            }
        }
        
        // 如果选择 Apple Pay，显示 Apple Pay 区域，隐藏原始提交按钮
        if (method === 'apple_pay') {
            if (applePaySection) {
                applePaySection.style.display = 'block';
                checkApplePayAvailability();
            }
            if (submitButton) {
                submitButton.style.display = 'none';
            }
        }
        
        // 如果选择 Google Pay，显示 Google Pay 区域，隐藏原始提交按钮
        if (method === 'google_pay') {
            if (googlePaySection) {
                googlePaySection.style.display = 'block';
                checkGooglePayAvailability();
            }
            if (submitButton) {
                submitButton.style.display = 'none';
            }
        }
        
        // 其他支付方式，显示原始提交按钮
        if (method !== 'card' && method !== 'apple_pay' && method !== 'google_pay') {
            if (submitButton) {
                submitButton.style.display = 'block';
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

    // Render payment method section - 支付方式界面渲染
    function renderPaymentMethodSection(t) {
        return `
                <div class="form-section">
                    <h3>
                        <div class="payment-method-title">${t.paymentMethod}</div>
                        <!-- One-Page Checkout Checkbox -->
                        <div class="one-page-checkout-section">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" id="onePageCheckout" name="onePageCheckout" checked>
                                <div class="checkbox-label">
                                    <div class="checkbox-title">${t.onePageCheckout}</div>
                                </div>
                            </label>
                        </div>
                    </h3>
                    <div class="payment-methods" id="paymentMethodsContainer">
                        ${generatePaymentMethods()}
                    </div>
                </div>
            `;
    }

    // Render checkout page using CheckoutRenderer
    function renderCheckout() {
        const container = document.getElementById('checkoutContent');

        // Create renderer instance
        const renderer = new CheckoutRenderer({
            translations: translations,
            currentLang: currentLang,
            cart: cart,
            paymentMethodsMap: paymentMethodsMap,
            getPaymentMethods: getPaymentMethods,
            calculateTotals: () => CheckoutRenderer.calculateTotals(cart),
            getProductName: getProductName,
            handleSubmit: handleSubmit,
            renderPaymentMethodSection: renderPaymentMethodSection
        });

        // Render the checkout page
        renderer.render(container);
    }

    // Handle form submission
    function handleSubmit(e) {
        e.preventDefault();

        const submitButton = document.getElementById('submitButton');
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        // Validate form using CheckoutRenderer
        if (!CheckoutRenderer.validateForm(data, translations, currentLang)) {
            return;
        }

        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = `<span class="spinner"></span>${translations[currentLang].processing}`;

        // Prepare checkout data using CheckoutRenderer
        const checkoutData = CheckoutRenderer.prepareCheckoutData(
            data,
            cart,
            getPaymentMethods,
            () => CheckoutRenderer.calculateTotals(cart)
        );

        // Initialize payment response handler
        const paymentHandler = new PaymentResponseHandler({
            translations: translations,
            currentLang: currentLang,
            submitButton: submitButton,
            totals: checkoutData.totals
        });

        // Submit to backend - Call PaymentController::createPayment()
        fetch('/api/payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(checkoutData)
        })
            .then(response => paymentHandler.handleResponse(response))
            .then(result => {
                // Prepare order data for success page
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

                // Process payment result
                paymentHandler.handlePaymentResult(result, orderData);
            })
            .catch(error => {
                paymentHandler.handleFetchError(error);
            });
    }

    // ==================== Apple Pay 相关函数 ====================
    
    // Apple Pay 配置 (从服务端获取)
    let applePayConfig = {
        merchantIdentifier: null,
        merchantName: 'Fashion Store',
        domain: null,
        supportedNetworks: ['visa', 'masterCard', 'discover', 'amex'],
        merchantCapabilities: ['supports3DS', 'supportsDebit', 'supportsCredit']
    };
    
    // 获取 Apple Pay 配置
    async function fetchApplePayConfiguration() {
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
                applePayConfig.merchantIdentifier = result.data.acquire_merchant_id;
                applePayConfig.merchantName = result.data.merchant_name || 'Fashion Store';
                applePayConfig.supportedNetworks = result.data.allowed_card_networks || applePayConfig.supportedNetworks;
                applePayConfig.merchantCapabilities = result.data.allowed_card_auth_methods || applePayConfig.merchantCapabilities;
                applePayConfig.domain = result.data.domain || window.location.hostname;
            }
            return applePayConfig;
        } catch (err) {
            console.error('Failed to fetch Apple Pay config:', err);
            return applePayConfig;
        }
    }
    
    // 检测 Apple Pay 可用性
    async function checkApplePayAvailability() {
        const statusEl = document.getElementById('applePayStatus');
        const buttonContainer = document.getElementById('applePayButtonContainer');
        if (!statusEl || !buttonContainer) return;
        
        if (!window.ApplePaySession) {
            statusEl.textContent = currentLang === 'zh' 
                ? 'Apple Pay 在此浏览器不可用，请使用 Safari 浏览器。'
                : 'Apple Pay is not available. Please use Safari.';
            buttonContainer.style.display = 'none';
            return;
        }
        
        statusEl.textContent = currentLang === 'zh' ? '正在加载 Apple Pay...' : 'Loading Apple Pay...';
        await fetchApplePayConfiguration();
        
        if (!applePayConfig.merchantIdentifier) {
            statusEl.textContent = currentLang === 'zh' 
                ? 'Apple Pay 配置获取失败'
                : 'Failed to get Apple Pay configuration';
            return;
        }
        
        try {
            const canMakePayments = await ApplePaySession.canMakePaymentsWithActiveCard(applePayConfig.merchantIdentifier);
            if (canMakePayments) {
                statusEl.textContent = currentLang === 'zh' 
                    ? '点击下方按钮使用 Apple Pay 支付'
                    : 'Click the button below to pay with Apple Pay';
            } else {
                statusEl.textContent = currentLang === 'zh' 
                    ? 'Apple Pay 可用，但当前未激活。请在钱包中添加卡片。'
                    : 'Apple Pay available but not activated. Please add a card.';
            }
            buttonContainer.style.display = 'flex';
        } catch (err) {
            console.error('Apple Pay check error:', err);
            statusEl.textContent = currentLang === 'zh' ? '检查 Apple Pay 状态时出错' : 'Error checking Apple Pay';
            buttonContainer.style.display = 'flex';
        }
    }
    
    // 构建 ApplePayPaymentRequest
    function getApplePayRequest() {
        const totals = CheckoutRenderer.calculateTotals(cart);
        return {
            countryCode: 'US',
            currencyCode: totals.currency || 'USD',
            merchantCapabilities: applePayConfig.merchantCapabilities,
            supportedNetworks: applePayConfig.supportedNetworks,
            total: {
                label: applePayConfig.merchantName,
                amount: totals.totalAmount,
                type: 'final'
            }
        };
    }
    
    // 发起 Apple Pay 支付
    async function initiateApplePay() {
        console.log('Initiating Apple Pay...');
        
        const errorEl = document.getElementById('applePayError');
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }
        
        if (!window.ApplePaySession) {
            showApplePayError(currentLang === 'zh' ? 'Apple Pay 不可用' : 'Apple Pay is not available');
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
            const applePayRequest = getApplePayRequest();
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
                            displayName: applePayConfig.merchantName,
                            domainName: window.location.hostname,
                            merchantIdentifier: applePayConfig.merchantIdentifier,
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
                    showApplePayError(currentLang === 'zh' ? '商户验证失败: ' + err.message : 'Merchant validation failed: ' + err.message);
                }
            };
            
            // 支付方式选择回调
            session.onpaymentmethodselected = (event) => {
                console.log('onpaymentmethodselected:', event);
                const totals = CheckoutRenderer.calculateTotals(cart);
                session.completePaymentMethodSelection({
                    newTotal: {
                        label: applePayConfig.merchantName,
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
            session.onpaymentauthorized = async (event) => {
                console.log('onpaymentauthorized - payment:', event.payment);
                
                try {
                    const form = document.getElementById('checkoutForm');
                    const formData = new FormData(form);
                    const data = Object.fromEntries(formData);
                    
                    const checkoutData = CheckoutRenderer.prepareCheckoutData(
                        data, cart, getPaymentMethods,
                        () => CheckoutRenderer.calculateTotals(cart)
                    );
                    
                    // 创建 PaymentIntent
                    console.log('Creating PaymentIntent...');
                    const createResponse = await fetch('/api/payment', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(checkoutData)
                    });
                    const createResult = await createResponse.json();
                    console.log('PaymentIntent created:', createResult);
                    
                    if (!createResult.success || !createResult.data.id) {
                        throw new Error(createResult.error?.message || 'Failed to create payment');
                    }
                    
                    const paymentIntentId = createResult.data.id;
                    
                    // Confirm 支付
                    console.log('Confirming payment with Apple Pay token...');
                    const confirmResponse = await fetch(`/api/payment/confirm/${paymentIntentId}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            payment_method_data: {
                                type: 'apple_pay',
                                apple_pay: {
                                    merchant_identifier: applePayConfig.merchantIdentifier,
                                    encrypted_payment_token: event.payment
                                }
                            }
                        })
                    });
                    
                    const confirmResult = await confirmResponse.json();
                    console.log('Confirm response:', confirmResult);
                    
                    const paymentStatus = confirmResult.success && confirmResult.data.status === 'succeeded';
                    session.completePayment({
                        status: paymentStatus ? ApplePaySession.STATUS_SUCCESS : ApplePaySession.STATUS_FAILURE
                    });
                    
                    if (paymentStatus) {
                        const orderData = {
                            orderId: confirmResult.data.merchant_order_id,
                            paymentIntentId: confirmResult.data.id,
                            customer: data,
                            items: cart,
                            totals: checkoutData.totals,
                            date: new Date().toISOString(),
                            status: confirmResult.data.status,
                            amount: confirmResult.data.amount
                        };
                        
                        const paymentHandler = new PaymentResponseHandler({
                            translations: translations,
                            currentLang: currentLang,
                            submitButton: document.getElementById('submitButton'),
                            totals: checkoutData.totals
                        });
                        paymentHandler.handlePaymentResult(confirmResult, orderData);
                    } else {
                        showApplePayError(confirmResult.error?.message || (currentLang === 'zh' ? '支付失败' : 'Payment failed'));
                    }
                    
                } catch (err) {
                    console.error('Payment error:', err);
                    session.completePayment({ status: ApplePaySession.STATUS_FAILURE });
                    showApplePayError(currentLang === 'zh' ? '支付处理失败: ' + err.message : 'Payment failed: ' + err.message);
                }
            };
            
            // 取消回调
            session.oncancel = (event) => {
                console.log('Apple Pay cancelled');
            };
            
            session.begin();
            
        } catch (err) {
            console.error('Apple Pay session error:', err);
            showApplePayError(currentLang === 'zh' ? '启动 Apple Pay 失败: ' + err.message : 'Failed to start Apple Pay: ' + err.message);
        }
    }
    
    // 显示 Apple Pay 错误
    function showApplePayError(message) {
        const errorEl = document.getElementById('applePayError');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }

    // ==================== Google Pay 相关函数 ====================
    
    // Google Pay 配置
    let googlePayConfig = {
        allowedCardNetworks: ['DISCOVER', 'MASTERCARD', 'VISA', 'AMEX'],
        allowedCardAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
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
        merchantName: 'Fashion Store',
        merchantId: 'BCR2DN4T7LTNVTBU'
    };
    
    // Google Pay 客户端
    let googlePaymentsClient = null;
    
    // 获取 Google Pay 配置
    async function fetchGooglePayConfiguration() {
        const totals = CheckoutRenderer.calculateTotals(cart);
        const amount = parseFloat(totals.totalAmount) || 0;
        try {
            const response = await fetch('/api/payment/google-pay/configuration', {
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
            console.log('Google Pay Configuration:', result);
            if (result.success && result.data) {
                if (result.data.allowed_card_networks) {
                    googlePayConfig.allowedCardNetworks = result.data.allowed_card_networks;
                }
                if (result.data.allowed_card_auth_methods) {
                    googlePayConfig.allowedCardAuthMethods = result.data.allowed_card_auth_methods;
                }
            }
            return googlePayConfig;
        } catch (err) {
            console.error('Failed to fetch Google Pay config:', err);
            return googlePayConfig;
        }
    }
    
    // 获取基础卡支付方式配置
    function getBaseCardPaymentMethod() {
        return {
            type: 'CARD',
            parameters: {
                allowedAuthMethods: googlePayConfig.allowedCardAuthMethods,
                allowedCardNetworks: googlePayConfig.allowedCardNetworks
            }
        };
    }
    
    // 获取完整卡支付方式配置
    function getCardPaymentMethod() {
        return {
            ...getBaseCardPaymentMethod(),
            tokenizationSpecification: googlePayConfig.tokenizationSpecification
        };
    }
    
    // 获取 Google Pay 客户端
    function getGooglePaymentsClient() {
        if (!googlePaymentsClient && window.google?.payments?.api) {
            const environment = window.location.hostname.includes('localhost') || 
                               window.location.hostname.includes('dev') || 
                               window.location.hostname.includes('uat') ? 'TEST' : 'PRODUCTION';
            console.log('Google Pay environment:', environment);
            googlePaymentsClient = new google.payments.api.PaymentsClient({ environment });
        }
        return googlePaymentsClient;
    }
    
    // 检测 Google Pay 可用性
    async function checkGooglePayAvailability() {
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
        
        await fetchGooglePayConfiguration();
        
        const client = getGooglePaymentsClient();
        if (!client) {
            statusEl.textContent = currentLang === 'zh' 
                ? 'Google Pay 初始化失败'
                : 'Failed to initialize Google Pay';
            return;
        }
        
        try {
            const isReadyToPayRequest = {
                ...googlePayConfig.baseRequest,
                allowedPaymentMethods: [getBaseCardPaymentMethod()]
            };
            
            const response = await client.isReadyToPay(isReadyToPayRequest);
            console.log('Google Pay isReadyToPay:', response);
            
            if (response.result) {
                statusEl.textContent = currentLang === 'zh' 
                    ? '点击下方按钮使用 Google Pay 支付'
                    : 'Click the button below to pay with Google Pay';
                addGooglePayButton();
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
    
    // 添加 Google Pay 按钮
    function addGooglePayButton() {
        const container = document.getElementById('googlePayButtonContainer');
        if (!container) return;
        
        container.innerHTML = '';
        
        const client = getGooglePaymentsClient();
        if (!client) return;
        
        const button = client.createButton({
            buttonColor: 'black',
            buttonType: 'plain',
            buttonSizeMode: 'fill',
            onClick: onGooglePayButtonClicked,
            allowedPaymentMethods: [getBaseCardPaymentMethod()]
        });
        
        container.appendChild(button);
        console.log('Google Pay button added');
    }
    
    // Google Pay 按钮点击处理
    function onGooglePayButtonClicked() {
        console.log('Google Pay button clicked');
        
        const errorEl = document.getElementById('googlePayError');
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
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
        
        const client = getGooglePaymentsClient();
        if (!client) {
            showGooglePayError(currentLang === 'zh' ? 'Google Pay 未初始化' : 'Google Pay not initialized');
            return;
        }
        
        const totals = CheckoutRenderer.calculateTotals(cart);
        
        // 判断环境
        const isTestEnv = window.location.hostname.includes('localhost') || 
                         window.location.hostname.includes('dev') || 
                         window.location.hostname.includes('uat');
        
        const paymentDataRequest = {
            ...googlePayConfig.baseRequest,
            allowedPaymentMethods: [getCardPaymentMethod()],
            transactionInfo: {
                countryCode: 'US',
                currencyCode: totals.currency || 'USD',
                totalPriceStatus: 'FINAL',
                totalPrice: totals.totalAmount
            },
            merchantInfo: {
                ...(isTestEnv ? {} : { merchantId: googlePayConfig.merchantId }),
                merchantName: googlePayConfig.merchantName
            }
        };
        
        console.log('Google Pay payment request:', paymentDataRequest);
        
        // loadPaymentData 必须在同步上下文中调用
        client.loadPaymentData(paymentDataRequest)
            .then(paymentData => {
                console.log('Google Pay payment data:', paymentData);
                return processGooglePayPayment(paymentData);
            })
            .catch(err => {
                console.error('Google Pay error:', err);
                if (err.statusCode !== 'CANCELED') {
                    showGooglePayError(currentLang === 'zh' ? 'Google Pay 支付失败: ' + (err.statusMessage || err.message) : 'Google Pay failed: ' + (err.statusMessage || err.message));
                }
            });
    }
    
    // 处理 Google Pay 支付
    async function processGooglePayPayment(paymentData) {
        try {
            const form = document.getElementById('checkoutForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            const checkoutData = CheckoutRenderer.prepareCheckoutData(
                data, cart, getPaymentMethods,
                () => CheckoutRenderer.calculateTotals(cart)
            );
            
            // 创建 PaymentIntent
            console.log('Creating PaymentIntent for Google Pay...');
            const createResponse = await fetch('/api/payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(checkoutData)
            });
            const createResult = await createResponse.json();
            console.log('PaymentIntent created:', createResult);
            
            if (!createResult.success || !createResult.data.id) {
                throw new Error(createResult.error?.message || 'Failed to create payment');
            }
            
            const paymentIntentId = createResult.data.id;
            
            // Confirm 支付
            console.log('Confirming payment with Google Pay token...');
            const confirmResponse = await fetch(`/api/payment/confirm/${paymentIntentId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    payment_method_data: {
                        type: 'google_pay',
                        google_pay: paymentData
                    }
                })
            });
            
            const confirmResult = await confirmResponse.json();
            console.log('Confirm response:', confirmResult);
            
            if (confirmResult.success && confirmResult.data.status === 'succeeded') {
                const orderData = {
                    orderId: confirmResult.data.merchant_order_id,
                    paymentIntentId: confirmResult.data.id,
                    customer: data,
                    items: cart,
                    totals: checkoutData.totals,
                    date: new Date().toISOString(),
                    status: confirmResult.data.status,
                    amount: confirmResult.data.amount
                };
                
                const paymentHandler = new PaymentResponseHandler({
                    translations: translations,
                    currentLang: currentLang,
                    submitButton: document.getElementById('submitButton'),
                    totals: checkoutData.totals
                });
                paymentHandler.handlePaymentResult(confirmResult, orderData);
            } else {
                showGooglePayError(confirmResult.error?.message || (currentLang === 'zh' ? '支付失败' : 'Payment failed'));
            }
            
        } catch (err) {
            console.error('Google Pay payment error:', err);
            showGooglePayError(currentLang === 'zh' ? '支付处理失败: ' + err.message : 'Payment failed: ' + err.message);
        }
    }
    
    // 显示 Google Pay 错误
    function showGooglePayError(message) {
        const errorEl = document.getElementById('googlePayError');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }
    
    // ==================== 初始化 ====================

    document.addEventListener('DOMContentLoaded', function() {
        loadCart();
        updateLanguage(currentLang);
        renderCheckout();
        loadApplePaySDK();
        loadGooglePaySDK();
    });
    
    // 动态加载 Apple Pay SDK
    function loadApplePaySDK() {
        if (document.querySelector('script[src*="apple-pay-sdk"]')) return;
        
        const script = document.createElement('script');
        script.src = 'https://applepay.cdn-apple.com/jsapi/v1/apple-pay-sdk.js';
        script.crossOrigin = 'anonymous';
        script.onload = () => {
            console.log('Apple Pay SDK loaded');
            const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked');
            if (selectedMethod && selectedMethod.value === 'apple_pay') {
                checkApplePayAvailability();
            }
        };
        script.onerror = (err) => console.error('Failed to load Apple Pay SDK:', err);
        document.head.appendChild(script);
    }
    
    // 动态加载 Google Pay SDK
    function loadGooglePaySDK() {
        if (document.querySelector('script[src*="pay.google.com"]')) return;
        
        const script = document.createElement('script');
        script.src = 'https://pay.google.com/gp/p/js/pay.js';
        script.async = true;
        script.onload = () => {
            console.log('Google Pay SDK loaded');
            const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked');
            if (selectedMethod && selectedMethod.value === 'google_pay') {
                checkGooglePayAvailability();
            }
        };
        script.onerror = (err) => console.error('Failed to load Google Pay SDK:', err);
        document.head.appendChild(script);
    }
</script>
<script src="/assets/js/payment-response-handler.js"></script>
</body>
</html>
