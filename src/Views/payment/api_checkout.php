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
    <link rel="stylesheet" href="/assets/css/payment/checkout.css?v=<?php echo time(); ?>">
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
<script src="/assets/js/i18n/payment/checkout-i18n.js?v=<?php echo time(); ?>"></script>
<!-- Payment Methods Configuration -->
<script src="/assets/js/payment/payment-methods-config.js?v=<?php echo time(); ?>"></script>
<!-- Checkout Renderer -->
<script src="/assets/js/payment/checkout-renderer.js?v=<?php echo time(); ?>"></script>
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

    // ==================== Apple Pay & Google Pay ====================
    // 使用独立的 JS 模块: /assets/js/payment/apple-pay.js 和 /assets/js/payment/google-pay.js
    
    // Apple Pay 可用性检查 (调用模块方法)
    function checkApplePayAvailability() {
        ApplePay.updateConfig({ currentLang, translations, cart });
        ApplePay.checkAvailability();
    }
    
    // Google Pay 可用性检查 (调用模块方法)
    function checkGooglePayAvailability() {
        GooglePay.updateConfig({ currentLang, translations, cart });
        GooglePay.checkAvailability();
    }
    
    // ==================== 初始化 ====================

    document.addEventListener('DOMContentLoaded', function() {
        loadCart();
        updateLanguage(currentLang);
        renderCheckout();
        
        // 初始化 Apple Pay 和 Google Pay 模块
        ApplePay.init({ currentLang, translations, cart });
        GooglePay.init({ currentLang, translations, cart });
    });
</script>
<!-- Apple Pay & Google Pay 独立模块 -->
<script src="/assets/js/payment/apple-pay.js?v=<?php echo time(); ?>"></script>
<script src="/assets/js/payment/google-pay.js?v=<?php echo time(); ?>"></script>
<script src="/assets/js/payment-response-handler.js?v=<?php echo time(); ?>"></script>
</body>
</html>
