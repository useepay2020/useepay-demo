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

        // Render payment method section - 支付方式界面渲染
        function renderPaymentMethodSection(t, generatePaymentMethods) {
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
                paymentHandler.processPaymentResultForRedirect(result, orderData);
            })
            .catch(error => {
                paymentHandler.handleFetchError(error);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize
            loadCart();
            updateLanguage(currentLang);
            renderCheckout();

        });
    </script>
    <script src="/assets/js/payment-response-handler.js"></script>
</body>
</html>
