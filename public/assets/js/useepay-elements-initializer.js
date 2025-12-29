/**
 * UseePay Elements Initializer
 * 公用模块 - 为 pricing.php 和 embedded_checkout.php 提供统一的支付元素初始化
 * 
 * 功能：
 * - 初始化 UseePay SDK 实例
 * - 创建支付元素
 * - 挂载支付元素到 DOM
 * - 完善的错误处理和日志记录
 */

// UseePay SDK global variables
let useepayInstance = null;
let useepayElements = null;
let useepayPaymentElement = null;

/**
 * Initialize UseePay Elements for card payment
 * 支持在 pricing.php 和 embedded_checkout.php 中使用
 * 
 * @param {string} clientSecret - Client secret from payment intent
 * @param {string} paymentIntentId - Payment intent ID
 * @returns {boolean} 初始化是否成功
 */
function initializeUseepayElements(clientSecret, paymentIntentId) {
    console.log('=== Initializing UseePay Elements ===');
    console.log('Debug: clientSecret =', clientSecret ? '✓' : '✗');
    console.log('Debug: paymentIntentId =', paymentIntentId ? '✓' : '✗');

    // Check if UseePay SDK is loaded
    if (!window.UseePay) {
        console.error('❌ UseePay SDK not loaded');
        console.error('Debug: window.UseePay =', window.UseePay);
        alert('Payment SDK failed to load. Please refresh the page.');
        return false;
    }
    console.log('✓ UseePay SDK loaded');

    try {
        // Get public key from window config (set in PHP)
        const publicKey = window.USEEPAY_PUBLIC_KEY;
        console.log('Debug: publicKey =', publicKey ? '✓ configured' : '✗ missing');

        if (!publicKey) {
            console.error('❌ UseePay public key not configured');
            console.error('Debug: window.USEEPAY_PUBLIC_KEY =', window.USEEPAY_PUBLIC_KEY);
            alert('Payment configuration error. Please contact support.');
            return false;
        }

        // Initialize UseePay instance
        console.log('Initializing UseePay instance with public key...');
        useepayInstance = window.UseePay(publicKey);
        console.log('✓ UseePay instance initialized');

        // Initialize Elements with clientSecret and paymentIntentId
        console.log('Creating UseePay Elements...');
        useepayElements = useepayInstance.elements({
            clientSecret: clientSecret,
            paymentIntentId: paymentIntentId
        });
        console.log('✓ UseePay Elements created');

        // Create payment element
        console.log('Creating payment element...');
        useepayPaymentElement = useepayElements.create('payment');
        console.log('✓ Payment element created');

        // Mount payment element to DOM
        const paymentElementContainer = document.getElementById('payment-element');
        console.log('Debug: payment-element container =', paymentElementContainer ? '✓ found' : '✗ not found');

        if (paymentElementContainer) {
            // Clear any existing content in the container
            if (paymentElementContainer.hasChildNodes()) {
                console.log('Clearing existing content from payment-element container...');
                paymentElementContainer.innerHTML = '';
            }
            
            console.log('Mounting payment element...');
            useepayPaymentElement.mount('payment-element');
            
            // Detect which page this is running on
            const containerLocation = paymentElementContainer.closest('.payment-methods-modal') 
                ? 'pricing.php (payment methods modal)' 
                : 'embedded_checkout.php (checkout form)';
            
            console.log('✓ Payment element mounted successfully');
            console.log('Container location:', containerLocation);
            console.log('Container class:', paymentElementContainer.className || 'none');
            return true;
        } else {
            console.error('❌ Payment element container not found');
            console.error('Available containers:', {
                'embedded_checkout': !!document.querySelector('.checkout-form'),
                'pricing_modal': !!document.getElementById('paymentMethodsModal')
            });
            alert('Payment form container not found. Please refresh the page.');
            return false;
        }
    } catch (error) {
        console.error('❌ Error initializing UseePay Elements:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack,
            name: error.name
        });
        alert('Failed to initialize payment form: ' + error.message);
        return false;
    }
}

/**
 * Initialize UseePay Elements for card payment
 * 支持在 pricing.php 和 embedded_checkout.php 中使用
 *
 * @param {number} amount - 支付金额
 * @param {string} currency - 货币单位
 * @param {Object} options - 可选参数
 * @returns {boolean} 初始化是否成功
 */
function initializeElementsForPayment(amount, currency,options) {
    initializeElements('payment',amount,currency,options);
}
/**
 * Initialize UseePay Elements for card payment
 * 支持在 pricing.php 和 embedded_checkout.php 中使用
 *
 * @param {number} amount - 支付金额
 * @param {string} currency - 货币单位
 * @param {Object} options - 可选参数
 * @returns {boolean} 初始化是否成功
 */
function initializeElementsForSubscription(amount, currency,options) {
    initializeElements('subscription',amount,currency,options);
}

/**
 * Initialize UseePay Elements for card payment
 * 支持在 pricing.php 和 embedded_checkout.php 中使用
 *
 * @param {string} mode - 'subscription' | 'payment',
 * @param {number} amount - 支付金额 (数字类型)
 * @param {string} currency - 货币单位
 * @param {Object} options - 可选参数
 * @returns {boolean} 初始化是否成功
 */
function initializeElements(mode, amount, currency,options) {
    console.log('=== Initializing UseePay Elements ===');
    console.log('Debug: mode =', mode ? '✓' : '✗', `(${mode})`);
    
    // Convert amount to number type
    const amountAsNumber = typeof amount === 'number' ? amount : parseFloat(amount);
    console.log('Debug: amount =', !isNaN(amountAsNumber) ? '✓' : '✗', `(${amountAsNumber})`);
    console.log('Debug: currency =', currency ? '✓' : '✗', `(${currency})`);
    
    // Validate amount is a valid number
    if (isNaN(amountAsNumber)) {
        console.error('❌ Invalid amount: must be a number');
        console.error('Debug: amount value =', amount);
        return false;
    }
    
    // Check if UseePay SDK is loaded
    if (!window.UseePay) {
        console.error('❌ UseePay SDK not loaded');
        console.error('Debug: window.UseePay =', window.UseePay);
        alert('Payment SDK failed to load. Please refresh the page.');
        return false;
    }
    console.log('✓ UseePay SDK loaded');

    try {
        // Get public key from window config (set in PHP)
        const publicKey = window.USEEPAY_PUBLIC_KEY;
        console.log('Debug: publicKey =', publicKey ? '✓ configured' : '✗ missing');

        if (!publicKey) {
            console.error('❌ UseePay public key not configured');
            console.error('Debug: window.USEEPAY_PUBLIC_KEY =', window.USEEPAY_PUBLIC_KEY);
            alert('Payment configuration error. Please contact support.');
            return false;
        }

        // Initialize UseePay instance
        console.log('Initializing UseePay instance with public key...');
        useepayInstance = window.UseePay(publicKey);
        console.log('✓ UseePay instance initialized');

        // Initialize Elements with mode, amount, and currency
        console.log('Creating UseePay Elements...');
        useepayElements = useepayInstance.elements({
            mode: mode,
            amount: amountAsNumber,
            currency: currency
        });
        console.log('✓ UseePay Elements created');

        // Create payment element
        console.log('Creating payment element...');
        useepayPaymentElement = useepayElements.create('payment',options);
        console.log('✓ Payment element created');

        // Mount payment element to DOM
        const paymentElementContainer = document.getElementById('payment-element');
        console.log('Debug: payment-element container =', paymentElementContainer ? '✓ found' : '✗ not found');

        if (paymentElementContainer) {
            // Clear any existing content in the container
            if (paymentElementContainer.hasChildNodes()) {
                console.log('Clearing existing content from payment-element container...');
                paymentElementContainer.innerHTML = '';
            }

            console.log('Mounting payment element...');
            useepayPaymentElement.mount('payment-element');

            // Detect which page this is running on
            const containerLocation = paymentElementContainer.closest('.payment-methods-modal')
                ? 'pricing.php (payment methods modal)'
                : 'embedded_checkout.php (checkout form)';

            console.log('✓ Payment element mounted successfully');
            console.log('Container location:', containerLocation);
            console.log('Container class:', paymentElementContainer.className || 'none');
            return true;
        } else {
            console.error('❌ Payment element container not found');
            console.error('Available containers:', {
                'embedded_checkout': !!document.querySelector('.checkout-form'),
                'pricing_modal': !!document.getElementById('paymentMethodsModal')
            });
            alert('Payment form container not found. Please refresh the page.');
            return false;
        }
    } catch (error) {
        console.error('❌ Error initializing UseePay Elements:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack,
            name: error.name
        });
        alert('Failed to initialize payment form: ' + error.message);
        return false;
    }
}

async function confirmPaymentIntent() {
    console.log('Confirming payment with UseePay SDK...');
    
    try {
        const { paymentIntent, error } = await useepayInstance.confirmPayment({
            elements: useepayElements
        });

        if (error) {
            console.error('Payment confirmation error:', error);
            const messageElement = document.getElementById('payment-message');
            if (messageElement) {
                messageElement.textContent = error.message;
                messageElement.style.display = 'block';
            }
            return {
                success: false,
                error: error.message || 'Payment confirmation failed'
            };
        } else if (paymentIntent) {
            console.log('✓ Payment confirmed:', paymentIntent);

            // Check payment status
            if (paymentIntent.status === 'succeeded') {
                console.log('✓ Payment succeeded');
                return {
                    success: true,
                    paymentIntent: paymentIntent,
                    status: 'succeeded'
                };
            } else if (paymentIntent.status === 'requires_action') {
                console.log('Payment requires additional action');
                return {
                    success: false,
                    error: 'Payment requires additional action. Please complete the verification.',
                    status: 'requires_action'
                };
            } else {
                console.log('Payment status:', paymentIntent.status);
                return {
                    success: false,
                    error: 'Payment status: ' + paymentIntent.status,
                    status: paymentIntent.status
                };
            }
        } else {
            return {
                success: false,
                error: 'No payment intent returned'
            };
        }
    } catch (error) {
        console.error('Exception during payment confirmation:', error);
        return {
            success: false,
            error: error.message || 'Payment confirmation failed'
        };
    }
}
/**
 * Get UseePay instance
 * @returns {object|null} UseePay instance or null
 */
function getUseepayInstance() {
    return useepayInstance;
}

/**
 * Get UseePay Elements
 * @returns {object|null} UseePay Elements or null
 */
function getUseepayElements() {
    return useepayElements;
}

/**
 * Get UseePay Payment Element
 * @returns {object|null} UseePay Payment Element or null
 */
function getUseepayPaymentElement() {
    return useepayPaymentElement;
}

/**
 * Reset UseePay instances
 * Useful for cleanup or re-initialization
 */
function resetUseepayInstances() {
    console.log('Resetting UseePay instances...');
    useepayInstance = null;
    useepayElements = null;
    useepayPaymentElement = null;
    console.log('✓ UseePay instances reset');
}

/**
 * Update payment element with new amount and currency
 * 更新支付元素的金额和币种
 * 
 * @param {number} amount - New payment amount (in cents/smallest unit)
 * @param {string} currency - Currency code (e.g., 'USD', 'CNY', 'EUR')
 * @param {Object} options - Optional additional update options
 * @returns {boolean} True if update successful, false otherwise
 */
function updatePaymentElementAmount(amount, currency, options = {}) {
    console.log('=== Updating Payment Element Amount ===');
    console.log('Debug: amount =', amount ? '✓' : '✗', `(${amount})`);
    console.log('Debug: currency =', currency ? '✓' : '✗', `(${currency})`);

    // Check if payment element exists
    if (!useepayElements) {
        console.error('❌ Payment element not initialized');
        console.error('Debug: useepayPaymentElement =', useepayElements);
        return false;
    }

    try {
        // Prepare update options
        const updateOptions = {
            amount: amount,
            currency: currency,
            ...options
        };

        console.log('Calling useepayElements.update() with options:', updateOptions);
        
        // Call the update method on payment element
        useepayElements.update(updateOptions);
        
        console.log('✓ Payment element updated successfully');
        console.log('Updated amount:', amount);
        console.log('Updated currency:', currency);
        return true;
    } catch (error) {
        console.error('❌ Error updating payment element:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack,
            name: error.name
        });
        return false;
    }
}

// Export to global scope for use in other pages
window.initializeUseepayElements = initializeUseepayElements;
window.getUseepayInstance = getUseepayInstance;
window.getUseepayElements = getUseepayElements;
window.getUseepayPaymentElement = getUseepayPaymentElement;
window.resetUseepayInstances = resetUseepayInstances;
window.updatePaymentElementAmount = updatePaymentElementAmount;
window.initializeElementsForPayment = initializeElementsForPayment;
window.initializeElementsForSubscription = initializeElementsForSubscription;

console.log('✓ UseePay Elements Initializer loaded');
