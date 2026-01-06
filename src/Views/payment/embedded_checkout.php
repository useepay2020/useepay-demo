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
    <link rel="stylesheet" href="/assets/css/payment/checkout.css?v=<?php echo @filemtime(__DIR__ . '/../../public/assets/css/payment/checkout.css') ?: time(); ?>">
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

<!-- Internationalization -->
<script src="/assets/js/i18n/payment/checkout-i18n.js?v=<?php echo @filemtime(__DIR__ . '/../../public/assets/js/i18n/payment/checkout-i18n.js') ?: time(); ?>"></script>
<!-- Payment Methods Configuration -->
<script src="/assets/js/payment/payment-methods-config.js?v=<?php echo @filemtime(__DIR__ . '/../../public/assets/js/payment/payment-methods-config.js') ?: time(); ?>"></script>
<!-- Payment Handler -->
<script src="/assets/js/payment-handler.js?v=<?php echo @filemtime(__DIR__ . '/../../public/assets/js/payment-handler.js') ?: time(); ?>"></script>
<!-- Checkout Renderer -->
<script src="/assets/js/payment/checkout-renderer.js?v=<?php echo @filemtime(__DIR__ . '/../../public/assets/js/payment/checkout-renderer.js') ?: time(); ?>"></script>

<script>
    // Use translations from i18n file
    const translations = checkoutTranslations;
    let currentLang = getCurrentLanguage();

    // Initialize PaymentHandler globally
    let paymentHandler = new PaymentHandler({
        translations: translations,
        currentLang: currentLang,
        submitButton: null,
        totals: {}
    });
    
    console.log('PaymentHandler initialized:', !!paymentHandler);
    
    // Initialize Express Checkout if in embedded mode
    let expressCheckoutElement;
    
    async function initializeExpressCheckout() {
        console.log('=== Initializing Express Checkout ===');

        const integrationMode = localStorage.getItem('paymentIntegrationMode');
        const actionType = localStorage.getItem('paymentActionType');
        const isExpress = actionType === 'express' || actionType === 'express_checkout' || actionType === '快捷支付';

        if (integrationMode !== 'embedded' || !isExpress) {
            console.log('❌ Express Checkout skipped - not in embedded mode');
            return ;
        }
        try {
            console.log('🔑 UseePay Public Key:', USEEPAY_PUBLIC_KEY ? 'Available' : 'Missing');
            
            // Check if UseePay SDK is loaded
            if (typeof UseePay === 'undefined') {
                throw new Error('UseePay SDK not loaded');
            }
            console.log('✓ UseePay SDK loaded');
            
            // Initialize UseePay with public key
            const useepay = UseePay(USEEPAY_PUBLIC_KEY);
            console.log('✓ UseePay instance initialized');
            
            // Calculate totals and log details
            const totals = CheckoutRenderer.calculateTotals(cart);
            console.log('💰 Cart totals:', totals);
            
            // Validate and ensure amount is a valid number
            let amount = Number(totals.totalAmount);
            console.log('💳 Payment amount:', totals.totalAmount, '-> converted to:', amount);
            console.log('🔍 Amount type:', typeof amount);
            
            if (typeof amount !== 'number' || isNaN(amount) || amount <= 0) {
                console.error('⚠️ Invalid amount detected:', amount);
                console.error('🛒 Cart totals:', totals);
                return ;
            }
            console.log('✅ Final amount to use:', amount);
            
            // Create elements instance
            const elementsConfig = {
                mode: 'payment',
                amount: amount,
                currency: 'USD', // Update with your currency
                paymentMethodTypes: ['googlepay','applepay']
            };
            console.log('⚙️ Elements config:', elementsConfig);
            
            const elements = useepay.elements(elementsConfig);
            console.log('✓ UseePay Elements created');
            
            // Create and mount Express Checkout element
            console.log('🚀 Creating Express Checkout element...');
            expressCheckoutElement = elements.create('expressCheckout', {
                // Add any additional options here
            });
            console.log('✓ Express Checkout element created');
            
            // Check if container exists
            const expressCheckoutContainer = document.getElementById('express-checkout-element');
            console.log('📦 Express Checkout container:', expressCheckoutContainer ? 'Found' : 'Not found');
            
            if (expressCheckoutContainer) {
                console.log('🔧 Mounting Express Checkout element...');
                // Try mounting with selector string instead of DOM element
                expressCheckoutElement.mount('express-checkout-element');
                
                // Handle ready event
                expressCheckoutElement.on('ready', function(event) {
                    console.log('✅ Express Checkout is ready');
                    console.log('📋 Ready event details:', event);
                });
                
                // Handle click event
                expressCheckoutElement.on('click', function(event) {
                    console.log('🖱️ Express Checkout clicked');
                    console.log('📋 Click event details:', event);
                    console.log('🛒 Current cart state:', cart);
                    // You can update line items or other data here if needed
                    const { resolve } = event;
                    resolve();
                });
                
                // Handle shipping address change
                expressCheckoutElement.on('shippingAddressChange', function(event) {
                    console.log('📍 Shipping address changed');
                    console.log('📋 Address change event:', event);
                    console.log('🏠 New address:', event.shippingAddress);
                    
                    // Update shipping rates based on address
                    const shippingRates = [
                        {
                            id: 'free-shipping',
                            label: 'Free Shipping',
                            detail: '3-5 business days',
                            amount: 0
                        }
                    ];
                    console.log('🚚 Resolving with shipping rates:', shippingRates);
                    
                    event.resolve({
                        shippingRates: shippingRates
                    });
                });
                
                // Handle shipping rate change
                expressCheckoutElement.on('shippingRateChange', function(event) {
                    console.log('🚚 Shipping rate changed');
                    console.log('📋 Rate change event:', event);
                    console.log('💰 Selected rate:', event.shippingRate);
                    
                    const lineItems = getLineItemsForExpressCheckout();
                    console.log('📦 Resolving with line items:', lineItems);
                    
                    // Update order total based on selected shipping rate
                    event.resolve({
                        lineItems: lineItems
                    });
                });
                
                // Handle payment confirmation
                expressCheckoutElement.on('confirm', async function(event) {
                    console.log('💳 Payment confirmation started');
                    console.log('📋 Confirm event details:', event);
                    console.log('👤 Payment method:', event.paymentMethod);
                    console.log('🏠 Billing address:', event.billingAddress);
                    console.log('📦 Shipping address:', event.shippingAddress);
                    
                    try {
                        // Show processing state
                        console.log('⏳ Showing payment progress...');
                        showPaymentProgress('processing', translations[currentLang]?.processingPayment || 'Processing payment...');
                        
                        // Prepare payment data
                        const paymentData = {
                            amount: CheckoutRenderer.calculateTotals(cart).total,
                            currency: 'USD',
                            paymentMethodType: 'card',
                            // Include any additional data needed by your backend
                        };
                        console.log('💰 Payment data to send:', paymentData);
                        
                        // Call your backend to create a payment intent
                        console.log('🌐 Calling backend to create payment intent...');
                        const response = await fetch('/api/create-payment-intent', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(paymentData)
                        });
                        
                        console.log('📡 Backend response status:', response.status);
                        console.log('📡 Backend response ok:', response.ok);
                        
                        if (!response.ok) {
                            throw new Error(`Backend request failed: ${response.status} ${response.statusText}`);
                        }
                        
                        const responseData = await response.json();
                        console.log('📋 Backend response data:', responseData);
                        
                        const { paymentIntentId, clientSecret } = responseData;
                        console.log('🔑 Payment Intent ID:', paymentIntentId);
                        console.log('🔐 Client Secret:', clientSecret ? 'Available' : 'Missing');
                        
                        // Confirm the payment
                        console.log('✅ Confirming payment with UseePay...');
                        const confirmParams = {
                            elements,
                            paymentIntentId,
                            clientSecret,
                            confirmParams: {
                                return_url: window.location.origin + '/payment/success',
                                // Include any additional parameters
                            }
                        };
                        console.log('⚙️ Confirm params:', confirmParams);
                        
                        const { error, paymentIntent } = await useepay.confirmPayment(confirmParams);
                        
                        console.log('📋 Payment confirmation result:', { error, paymentIntent });
                        
                        if (error) {
                            console.error('❌ Payment confirmation error:', error);
                            throw error;
                        }
                        
                        // Payment succeeded
                        console.log('💳 Payment Intent status:', paymentIntent?.status);
                        if (paymentIntent.status === 'succeeded') {
                            console.log('🎉 Payment succeeded!');
                            showPaymentProgress('success', translations[currentLang]?.paymentSuccess || 'Payment successful!');
                            // Redirect to success page or update UI
                            console.log('🔄 Redirecting to success page...');
                            window.location.href = '/payment/success';
                        } else {
                            console.warn('⚠️ Payment status not succeeded:', paymentIntent.status);
                        }
                        
                    } catch (error) {
                        console.error('❌ Payment confirmation error:', error);
                        console.error('📋 Error details:', {
                            message: error.message,
                            code: error.code,
                            type: error.type,
                            stack: error.stack
                        });
                        showPaymentProgress('error', error.message || 'Payment failed. Please try again.');
                    }
                });
                
                console.log('✅ Express Checkout element mounted successfully');
            } else {
                console.error('❌ Express Checkout container not found');
                console.error('📋 Available containers on page:', {
                    'express-checkout-element': !!document.getElementById('express-checkout-element'),
                    'payment-element': !!document.getElementById('payment-element'),
                    'checkout-form': !!document.querySelector('.checkout-form')
                });
            }
            
        } catch (error) {
            console.error('❌ Failed to initialize Express Checkout');
            console.error('📋 Error details:', {
                message: error.message,
                code: error.code,
                type: error.type,
                stack: error.stack
            });
            console.error('🔍 Debug info:', {
                'UseePay SDK loaded': typeof UseePay !== 'undefined',
                'Public key available': !!window.USEEPAY_PUBLIC_KEY,
                'Cart available': !!cart,
                'CheckoutRenderer available': typeof CheckoutRenderer !== 'undefined'
            });
        }
        
        console.log('=== Express Checkout initialization complete ===');
    }
    
    // Helper function to get line items for Express Checkout
    function getLineItemsForExpressCheckout() {
        const totals = CheckoutRenderer.calculateTotals(cart);
        
        // Map cart items to line items format expected by Express Checkout
        const lineItems = cart.map(item => ({
            name: item.name,
            description: item.description || '',
            quantity: item.quantity,
            amount: Math.round(item.price * 100), // Amount in cents
            currency: 'USD' // Update with your currency
        }));
        
        // Add shipping as a line item if applicable
        if (totals.shipping > 0) {
            lineItems.push({
                name: 'Shipping',
                description: 'Standard Shipping',
                quantity: 1,
                amount: Math.round(totals.shipping * 100),
                currency: 'USD' // Update with your currency
            });
        }
        
        // Add tax as a line item if applicable
        if (totals.tax > 0) {
            lineItems.push({
                name: 'Tax',
                description: 'Sales Tax',
                quantity: 1,
                amount: Math.round(totals.tax * 100),
                currency: 'USD' // Update with your currency
            });
        }
        
        return lineItems;
    }

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
        
        // Show processing modal
        showPaymentProgress('processing', translations[currentLang].processing || 'Processing payment...');
        
        try {
            // Prepare checkout data
            const form = document.getElementById('checkoutForm');
            if (!form) {
                throw new Error('Checkout form not found');
            }

            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            const checkoutData = CheckoutRenderer.prepareCheckoutData(
                data,
                cart,
                getPaymentMethods,
                () => CheckoutRenderer.calculateTotals(cart)
            );

            // Process payment using PaymentHandler
            await paymentHandler.processPaymentEmbeddedCheckout(
                checkoutData,
                (status, message) => {
                    // Update progress modal status
                    showPaymentProgress(status, message);
                }
            );

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

    /**
     * Initialize UseePay payment element with amount and currency
     * @param {string} elementId - Element ID for payment element container (default: 'payment-element')
     */
    function initializePaymentElement(elementId = 'payment-element') {
        console.log('=== Initializing/Updating Payment Element ===');
        console.log('Element ID:', elementId);
        
        // Calculate totals from current cart
        const totals = CheckoutRenderer.calculateTotals(cart);
        console.log('Cart totals:', totals);
        
        if (!totals || !totals.totalAmount) {
            console.error('Cannot initialize payment element: invalid totals');
            return false;
        }
        
        // Check if amount > 0
        if (totals.totalAmount <= 0) {
            console.log('Amount is 0, skipping payment element initialization');
            return false;
        }
        
        const currency = totals.currency || 'USD';
        
        console.log('Payment element amount:');
        console.log('  Amount:', totals.totalAmount);
        console.log('  Currency:', currency);
        
        let success = false;
        
        // Check if element is already bound
        const isAlreadyBound = paymentHandler.isElementAlreadyBound(elementId);
        
        if (isAlreadyBound) {
            // Element already bound, just update the amount
            console.log('Payment element already bound, updating amount...');
            success = paymentHandler.updatePaymentElement(totals.totalAmount, currency);
            
            if (success) {
                console.log('✓ Payment element amount updated successfully');
            } else {
                console.error('Failed to update payment element amount');
            }
        } else {
            // Element not bound, initialize it
            console.log('Payment element not bound, initializing...');
            success = paymentHandler.initializeElementsForPayment(totals.totalAmount, currency, {}, elementId);
            
            if (success) {
                console.log('✓ Payment element initialized successfully');
            } else {
                console.error('Failed to initialize payment element');
            }
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
        
        // Use PaymentHandler if available, otherwise fall back to global function
        let success = initializePaymentElement('payment-element');

        
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
            initializeExpressCheckout();
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
