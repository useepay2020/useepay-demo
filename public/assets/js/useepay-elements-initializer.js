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
        alert(translations[currentLang].paymentError + ': ' + error.message);
    } else if (paymentIntent) {
        console.log('✓ Payment confirmed:', paymentIntent);
        // Update cached payment intent with final status
        sessionStorage.setItem('currentPaymentIntent', JSON.stringify(paymentIntent));

        // Check payment status
        if (paymentIntent.status === 'succeeded') {
            console.log('✓ Payment succeeded');
            alert('✓ Payment succeeded');
        } else if (paymentIntent.status === 'requires_action') {
            console.log('Payment requires additional action');
            alert('Payment requires additional action. Please complete the verification.');
        } else {
            console.log('Payment status:', paymentIntent.status);
            alert('Payment status: ' + paymentIntent.status);
        }
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

// Export to global scope for use in other pages
window.initializeUseepayElements = initializeUseepayElements;
window.getUseepayInstance = getUseepayInstance;
window.getUseepayElements = getUseepayElements;
window.getUseepayPaymentElement = getUseepayPaymentElement;
window.resetUseepayInstances = resetUseepayInstances;

console.log('✓ UseePay Elements Initializer loaded');
