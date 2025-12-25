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
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/payment/checkout.css">
<style>
/* Payment Progress Modal */
.payment-progress-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    backdrop-filter: blur(4px);
}

.payment-progress-content {
    background: white;
    padding: 40px;
    border-radius: 16px;
    text-align: center;
    max-width: 400px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.payment-progress-spinner {
    width: 60px;
    height: 60px;
    margin: 0 auto 20px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.payment-progress-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin: 0 0 10px 0;
}

.payment-progress-message {
    font-size: 16px;
    color: #666;
    margin: 0;
    line-height: 1.5;
}

.payment-progress-success {
    color: #10b981;
}

.payment-progress-error {
    color: #ef4444;
}
</style>
</head>
<body>
<div class="container">
    <header>
        <div class="logo" data-i18n="logo">🛍️ Fashion Store</div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="/" class="back-button" data-i18n="backToHome">← Back to Home</a>
            <a href="/payment/clothing-shop" class="back-button" data-i18n="backToShop">← Back to Shop</a>
        </div>
    </header>

    <div class="checkout-content" id="checkoutContent">
        <!-- Content will be loaded by JavaScript -->
    </div>

    <!-- Payment Progress Modal -->
    <div id="paymentProgressModal" class="payment-progress-modal" style="display: none;">
        <div class="payment-progress-content">
            <div class="payment-progress-spinner"></div>
            <h3 id="paymentProgressTitle" class="payment-progress-title"></h3>
            <p id="paymentProgressMessage" class="payment-progress-message"></p>
        </div>
    </div>
</div>
<!-- UseePay SDK -->
<script src="https://checkout-sdk1.uat.useepay.com/2.0.0/useepay.min.js"></script>

<!-- UseePay Public Key Configuration -->
<script>
    // Get UseePay public key from PHP config
    <?php
    global $config;
    $publicKey = $config['usee_pay']['api_public_key'];
    ?>
    window.USEEPAY_PUBLIC_KEY = '<?php echo $publicKey; ?>';
    console.log('UseePay Public Key configured:', window.USEEPAY_PUBLIC_KEY ? '✓' : '✗');
</script>

<!-- UseePay Elements Initializer (must be loaded before inline scripts) -->
<script src="/assets/js/useepay-elements-initializer.js"></script>

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
        const actionType = localStorage.getItem('paymentActionType');
        console.log('Current action type:', actionType);

        let cacheKey = 'paymentMethods';
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

    // Render payment method section
    function renderPaymentMethodSection(t, generatePaymentMethods) {
        return `
            <div class="form-section">
                <h3 data-i18n="paymentMethod">💳 Payment Method</h3>
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
        
        // Show payment progress modal
        //showPaymentProgress('processing');
        
        try {
            // Step 1: Create payment intent on server
            console.log('Step 1: Creating payment intent on server...');
            const form = document.getElementById('checkoutForm');
            if (!form) {
                throw new Error('Checkout form not found');
            }

            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            // Prepare checkout data
            const checkoutData = CheckoutRenderer.prepareCheckoutData(
                data,
                cart,
                getPaymentMethods,
                () => CheckoutRenderer.calculateTotals(cart)
            );

            // Submit to backend to create payment intent
            const paymentIntentResponse = await fetch('/api/payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(checkoutData)
            });

            const paymentIntentText = await paymentIntentResponse.text();
            let paymentIntentResult;
            try {
                paymentIntentResult = JSON.parse(paymentIntentText);
            } catch (e) {
                console.error('JSON parse error:', e);
                throw new Error('Invalid JSON response from server');
            }

            if (!paymentIntentResult.success || !paymentIntentResult.data) {
                throw new Error(paymentIntentResult.data?.error?.message || 'Failed to create payment intent');
            }

            const { client_secret: clientSecret, id: paymentIntentId } = paymentIntentResult.data;
            console.log('✓ Payment intent created:', paymentIntentId);
            console.log('Client secret:', clientSecret ? '✓' : '✗');

            // Step 2: Get UseePay elements
            console.log('Step 2: Submitting payment form...');
            const useepayElements = getUseepayElements();
            if (!useepayElements) {
                throw new Error('UseePay Elements not initialized');
            }

            // Step 3: Submit elements to validate and get selected payment method
            const submitResult = await useepayElements.submit();
            const { selectedPaymentMethod, error: submitError } = submitResult;

            if (submitError) {
                console.error('Form submission error:', submitError);
                throw new Error(submitError.message || 'Form validation failed');
            }

            if (!selectedPaymentMethod) {
                throw new Error('No payment method selected');
            }

            console.log('✓ Payment method selected:', selectedPaymentMethod);

            // Step 4: Confirm payment with UseePay
            console.log('Step 3: Confirming payment with UseePay...');
            const useepayInstance = getUseepayInstance();
            if (!useepayInstance) {
                throw new Error('UseePay instance not initialized');
            }

            const confirmResult = await useepayInstance.confirmPayment({
                elements: useepayElements,
                paymentIntentId: paymentIntentId,
                clientSecret: clientSecret
            });

            const { paymentIntent, error: confirmError } = confirmResult;

            if (confirmError) {
                console.error('Payment confirmation error:', confirmError);
                throw new Error(confirmError.message || 'Payment confirmation failed');
            }

            if (!paymentIntent) {
                throw new Error('No payment intent returned');
            }

            console.log('✓ Payment confirmed:', paymentIntent);

            // Step 5: Handle payment success
            if (paymentIntent.status === 'succeeded') {
                console.log('✓ Payment succeeded');
                
                // Update modal to success state
                showPaymentProgress('success');
                
                // Redirect to callback page
                setTimeout(() => {
                    window.location.href = '/payment/callback?id=' + paymentIntent.id + 
                        '&merchant_order_id=' + paymentIntent.merchant_order_id + 
                        '&status=succeeded';
                }, 1500);
            } else {
                throw new Error('Payment status: ' + paymentIntent.status);
            }

        } catch (error) {
            console.error('Payment confirmation error:', error);
            const errorMsg = error.message || translations[currentLang].paymentError;
            
            // Update modal to error state
            showPaymentProgress('error', errorMsg);
            
            // Hide modal after 3 seconds
            setTimeout(() => {
                hidePaymentProgress();
            }, 3000);
        }
    }

    /**
     * Show payment progress modal
     * @param {string} status - 'processing', 'success', or 'error'
     * @param {string} message - Optional custom message
     */
    function showPaymentProgress(status, message) {
        const modal = document.getElementById('paymentProgressModal');
        const title = document.getElementById('paymentProgressTitle');
        const messageEl = document.getElementById('paymentProgressMessage');
        const spinner = modal.querySelector('.payment-progress-spinner');
        
        if (!modal) return;
        
        // Reset classes
        title.className = 'payment-progress-title';
        messageEl.className = 'payment-progress-message';
        
        // Update content based on status
        switch(status) {
            case 'processing':
                spinner.style.display = 'block';
                title.textContent = translations[currentLang].processing || '处理中...';
                messageEl.textContent = translations[currentLang].processingPayment || '正在处理您的支付，请稍候';
                break;
                
            case 'success':
                spinner.style.display = 'none';
                title.textContent = translations[currentLang].paymentSuccess || '✓ 支付成功';
                title.className += ' payment-progress-success';
                messageEl.textContent = translations[currentLang].redirecting || '正在跳转到确认页面...';
                break;
                
            case 'error':
                spinner.style.display = 'none';
                title.textContent = translations[currentLang].paymentFailed || '✗ 支付失败';
                title.className += ' payment-progress-error';
                messageEl.textContent = message || translations[currentLang].paymentError;
                break;
        }
        
        modal.style.display = 'flex';
    }

    /**
     * Hide payment progress modal
     */
    function hidePaymentProgress() {
        const modal = document.getElementById('paymentProgressModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // function createPaymentIntent() {
    //     // Get form element
    //     const form = document.getElementById('checkoutForm');
    //     if (!form) {
    //         console.error('Checkout form not found');
    //         return;
    //     }
    //
    //     const formData = new FormData(form);
    //     const data = Object.fromEntries(formData);
    //
    //     // Get payment methods from local cache
    //     const paymentMethods = getPaymentMethods();
    //     console.log('Payment methods from cache:', paymentMethods);
    //
    //     // Prepare checkout data using CheckoutRenderer
    //     const checkoutData = CheckoutRenderer.prepareCheckoutData(
    //         data,
    //         cart,
    //         getPaymentMethods,
    //         () => CheckoutRenderer.calculateTotals(cart)
    //     );
    //
    //
    //     // Submit to backend - Call PaymentController::createPayment()
    //     fetch('/api/payment', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //         },
    //         body: JSON.stringify(checkoutData)
    //     })
    //         .then(response => {
    //             console.log('Response status:', response.status);
    //             console.log('Response headers:', response.headers);
    //
    //             // Try to parse JSON
    //             return response.text().then(text => {
    //                 console.log('Response text:', text);
    //                 try {
    //                     return JSON.parse(text);
    //                 } catch (e) {
    //                     console.error('JSON parse error:', e);
    //                     console.error('Response was:', text);
    //                     throw new Error('Invalid JSON response from server');
    //                 }
    //             });
    //         })
    //         .then(result => {
    //             console.log('Parsed result:', result);
    //
    //             // Check if payment creation was successful
    //             if (result.success && result.data) {
    //                 // Cache payment intent data to browser memory
    //                 console.log('Caching payment intent data:', result.data);
    //
    //                 // Store in sessionStorage for current session
    //                 sessionStorage.setItem('currentPaymentIntent', JSON.stringify(result.data));
    //                 console.log('✓ Payment intent created and cached:', result.data.id);
    //
    //             } else {
    //                 console.error('Payment failed:', result.data.error.message);
    //                 // Show error message
    //                 const errorMsg = result.error?.message || result.data.error.message || translations[currentLang].paymentError || 'Payment failed. Please try again.';
    //                 alert(errorMsg);
    //             }
    //         })
    //         .catch(error => {
    //             console.error('Payment creation error:', error);
    //             alert(translations[currentLang].paymentError + ': ' + error.message);
    //         })
    //         .finally(() => {
    //             // Restore button state
    //             if (submitButton) {
    //                 submitButton.disabled = false;
    //                 const totals = CheckoutRenderer.calculateTotals(cart);
    //                 submitButton.textContent = `${translations[currentLang].confirmPay} $${totals.totalAmount}`;
    //             }
    //         });
    // }

    /**
     * Initialize UseePay payment element with amount and currency
     */
    function initializePaymentElement() {
        console.log('=== Initializing Payment Element ===');
        
        // Calculate totals from current cart
        const totals = CheckoutRenderer.calculateTotals(cart);
        console.log('Cart totals:', totals);
        
        if (!totals || !totals.totalAmount) {
            console.error('Cannot initialize payment element: invalid totals');
            return false;
        }
        
        // Convert total amount to cents (smallest unit)
        const currency = totals.currency || 'USD';
        
        console.log('Initializing payment element with:');
        console.log('  Amount:', totals.totalAmount, 'cents');
        console.log('  Currency:', currency);
        
        // Call initializeElementsForPayment with amount and currency
        const success = initializeElementsForPayment(totals.totalAmount, currency);
        
        if (success) {
            console.log('✓ Payment element initialized successfully');
        } else {
            console.error('Failed to initialize payment element');
        }
        
        return success;
    }

    /**
     * Update payment element when cart changes (e.g., quantity adjustment)
     */
    function updatePaymentElementOnCartChange() {
        console.log('=== Updating Payment Element on Cart Change ===');
        
        // Calculate new totals
        const totals = CheckoutRenderer.calculateTotals(cart);
        console.log('Updated cart totals:', totals);
        
        if (!totals || !totals.totalAmount) {
            console.error('Cannot update payment element: invalid totals');
            return false;
        }

        const currency = totals.currency || 'USD';
        
        console.log('Updating payment element with:');
        console.log('  Amount:', totals.totalAmount, 'cents');
        console.log('  Currency:', currency);
        
        // Call updatePaymentElementAmount to update the element
        const success = updatePaymentElementAmount(totals.totalAmount, currency);
        
        if (success) {
            console.log('✓ Payment element updated successfully');
        } else {
            console.error('Failed to update payment element');
        }
        
        return success;
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadCart();
        
        // Update language first to apply translations to header
        currentLang = getCurrentLanguage();
        updateLanguage(currentLang);
        
        renderCheckout();
        
        // Update language again after checkout is rendered
        updateLanguage(currentLang);
        
        // Initialize payment element with cart amount and currency
        setTimeout(() => {
            initializePaymentElement();
        }, 500);
        
        // Check if card should be shown by default
        // const firstMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
        // handlePaymentMethodChange(firstMethod);
        //createPaymentIntent();

    });
</script>
</body>
</html>
