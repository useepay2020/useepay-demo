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
<!-- UseePay SDK -->
<script src="https://checkout-sdk.useepay.com/1.0.1/useepay.min.js"></script>
<!-- Internationalization -->
<script src="/assets/js/i18n/payment/checkout-i18n.js"></script>
<!-- Payment Methods Configuration -->
<script src="/assets/js/payment/payment-methods-config.js"></script>
<!-- Checkout Renderer -->
<script src="/assets/js/payment/checkout-renderer.js"></script>
<script>
    // Get UseePay public key from PHP config
    <?php
    global $config;
    $publicKey = $config['usee_pay']['api_public_key'];
    ?>
    window.USEEPAY_PUBLIC_KEY = '<?php echo $publicKey; ?>';
    console.log('UseePay Public Key configured:', window.USEEPAY_PUBLIC_KEY ? '✓' : '✗');
</script>

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
                <h3 data-i18n="paymentMethod">💳 支付方式</h3>
                <div id="payment-element" style="margin: 20px 0;"></div>
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
        confirmPaymentMethod();
    }

    async function confirmPaymentMethod() {
        console.log('=== Starting payment confirmation ===');
        
        try {
            // Call the payment confirmation
            const result = await confirmPaymentIntent();
            console.log('Payment confirmation result:', result);
            
            if (result.success) {
                // Payment succeeded
                console.log('✓ Payment succeeded');
                
                // Redirect to callback page
                setTimeout(() => {
                    window.location.href = '/payment/callback?id=' + result.paymentIntent.id + 
                        '&merchant_order_id=' + result.paymentIntent.merchant_order_id + 
                        '&status=succeeded';
                }, 500);
            } else {
                // Payment failed
                const errorMsg = result.error || translations[currentLang].paymentError;
                console.error('Payment failed:', errorMsg);
                showAlertModal(errorMsg, 'error');
            }
        } catch (error) {
            console.error('Payment confirmation error:', error);
            showAlertModal(translations[currentLang].paymentError + ': ' + error.message, 'error');
        }
    }

    function createPaymentIntent() {
        // Get form element
        const form = document.getElementById('checkoutForm');
        if (!form) {
            console.error('Checkout form not found');
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        // Get payment methods from local cache
        const paymentMethods = getPaymentMethods();
        console.log('Payment methods from cache:', paymentMethods);

        // Prepare checkout data using CheckoutRenderer
        const checkoutData = CheckoutRenderer.prepareCheckoutData(
            data,
            cart,
            getPaymentMethods,
            () => CheckoutRenderer.calculateTotals(cart)
        );


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
                    const totals = CheckoutRenderer.calculateTotals(cart);
                    submitButton.textContent = `${translations[currentLang].confirmPay} $${totals.totalAmount}`;
                }
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadCart();
        renderCheckout();
        updateLanguage();
        renderCheckout();
        // Check if card should be shown by default
        // const firstMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
        // handlePaymentMethodChange(firstMethod);
        createPaymentIntent();

    });
</script>
<script src="/assets/js/payment-response-handler.js"></script>
<!-- UseePay Elements Initializer (must be loaded first) -->
<script src="/assets/js/useepay-elements-initializer.js"></script>
</body>
</html>
