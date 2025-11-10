/**
 * Payment Response Handler
 * 处理支付 API 响应的统一模块
 * 
 * 功能：
 * - 解析响应数据
 * - 处理支付成功/失败
 * - 管理重定向逻辑
 * - 错误处理
 */

class PaymentResponseHandler {
    /**
     * 构造函数
     * @param {Object} config 配置对象
     * @param {Object} config.translations 多语言翻译对象
     * @param {string} config.currentLang 当前语言
     * @param {HTMLElement} config.submitButton 提交按钮元素
     * @param {Object} config.totals 订单总额信息
     */
    constructor(config = {}) {
        this.translations = config.translations || {};
        this.currentLang = config.currentLang || 'en';
        this.submitButton = config.submitButton;
        this.totals = config.totals || {};
        this.logger = this.createLogger();
    }

    /**
     * 创建日志记录器
     * @returns {Object} 日志对象
     */
    createLogger() {
        return {
            log: (message, data = null) => {
                console.log(`[PaymentHandler] ${message}`, data || '');
            },
            error: (message, data = null) => {
                console.error(`[PaymentHandler] ${message}`, data || '');
            },
            warn: (message, data = null) => {
                console.warn(`[PaymentHandler] ${message}`, data || '');
            }
        };
    }

    /**
     * 处理支付响应
     * @param {Response} response Fetch 响应对象
     * @returns {Promise<Object>} 解析后的响应数据
     */
    async handleResponse(response) {
        this.logger.log('Response status:', response.status);
        this.logger.log('Response headers:', response.headers);

        // 尝试解析 JSON
        return response.text().then(text => {
            this.logger.log('Response text:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                this.logger.error('JSON parse error:', e);
                this.logger.error('Response was:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    }

    /**
     * 处理支付结果
     * @param {Object} result 支付结果对象
     * @param {Object} orderData 订单数据
     * @returns {boolean} 是否需要继续处理
     */
    handlePaymentResult(result, orderData) {
        this.logger.log('Payment success:', result);

        // 检查支付状态
        const paymentStatus = result.data.status;
        this.logger.log('Payment status:', paymentStatus);
        // 保存订单数据
        this.saveOrderData(orderData);

        if (paymentStatus === 'requires_payment_method' || paymentStatus === 'requires_customer_action' || paymentStatus === 'requires_payment_method') {
            return this.handlePaymentRedirect(result);
        }else if (paymentStatus === 'succeeded' || paymentStatus === 'failed' ) {
            window.location.href = result.data.return_url+'?id=' + result.data.id +'&merchant_order_id='
                +result.data.merchant_order_id+'&status='+paymentStatus;
        }else if(paymentStatus === 'payment_intent_created'){
            const errorMessage = this.currentLang === 'zh' 
                ? '支付失败，请联系您的客户经理。' 
                : 'Payment failed. Please contact your account manager.';
            this.showError(errorMessage);
        }
        return true;
    }

    /**
     * 处理支付重定向
     * @param {Object} result 支付结果对象
     * @returns {boolean} 是否成功处理重定向
     */
    handlePaymentRedirect(result) {
        const nextAction = result.data.next_action;

        // 检查是否有重定向 URL
        if (nextAction && nextAction.redirect && nextAction.redirect.url) {
            const redirectUrl = nextAction.redirect.url;
            const redirectMethod = nextAction.redirect.method || 'GET';

            this.logger.log('Redirect method:', redirectMethod);
            this.logger.log('Redirect URL:', redirectUrl);

            if (redirectMethod.toUpperCase() === 'GET') {
                return this.handleGetRedirect(redirectUrl);
            } else if (redirectMethod.toUpperCase() === 'POST') {
                return this.handlePostRedirect(redirectUrl, nextAction.redirect.data);
            } else {
                this.logger.error('Unsupported redirect method:', redirectMethod);
                this.showError('Unsupported redirect method. Please contact support.');
                return false;
            }
        }

        // 检查是否有 client_secret
        if (result.data.client_secret) {
            this.logger.log('Payment intent created with client_secret');
            localStorage.setItem('currentPaymentIntent', JSON.stringify(result.data));
            this.showInfo('Payment intent created. Please complete payment.');
            return false;
        }

        this.logger.error('No redirect URL found in response');
        this.showError('Payment URL not found. Please contact support.');
        return false;
    }

    /**
     * 处理 GET 重定向
     * @param {string} url 重定向 URL
     * @returns {boolean} 成功标志
     */
    handleGetRedirect(url) {
        this.logger.log('Performing GET redirect to:', url);
        window.location.href = url;
        return true;
    }

    /**
     * 处理 POST 重定向
     * @param {string} url 重定向 URL
     * @param {Object} data 表单数据
     * @returns {boolean} 成功标志
     */
    handlePostRedirect(url, data = {}) {
        this.logger.log('Performing POST redirect to:', url);

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        // 添加表单数据
        if (data && typeof data === 'object') {
            for (const [key, value] of Object.entries(data)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = typeof value === 'string' ? value : JSON.stringify(value);
                form.appendChild(input);
            }
        }

        document.body.appendChild(form);
        form.submit();
        return true;
    }

    /**
     * 处理支付失败
     * @param {Object} result 支付结果对象
     */
    handlePaymentError(result) {
        this.logger.error('Payment failed:', result);

        const errorMessage = result.error?.message ||
            result.data?.error?.message ||
            this.getTranslation('paymentError') ||
            'Payment failed. Please try again.';

        this.showError(errorMessage);
    }

    /**
     * 处理 Fetch 错误
     * @param {Error} error 错误对象
     */
    handleFetchError(error) {
        this.logger.error('Fetch error:', error);
        this.showError(`Error: ${error.message}\n\nPlease check the console for details.`);
    }

    /**
     * 保存订单数据到本地存储
     * @param {Object} orderData 订单数据
     */
    saveOrderData(orderData) {
        this.logger.log('Saving order data:', orderData);
        localStorage.setItem('lastOrder', JSON.stringify(orderData));
    }

    /**
     * 重定向到指定页面
     * @param {string} url 目标 URL
     */
    redirect(url) {
        this.logger.log('Redirecting to:', url);
        window.location.href = url;
    }

    /**
     * 显示错误消息
     * @param {string} message 错误消息
     */
    showError(message) {
        this.logger.error('Showing error:', message);
        this.resetSubmitButton();
        alert(message);
    }

    /**
     * 显示信息消息
     * @param {string} message 信息消息
     */
    showInfo(message) {
        this.logger.log('Showing info:', message);
        this.resetSubmitButton();
        alert(message);
    }

    /**
     * 重置提交按钮状态
     */
    resetSubmitButton() {
        if (this.submitButton) {
            this.submitButton.disabled = false;
            const buttonText = this.getTranslation('confirmPay') || 'Confirm Payment';
            const amount = this.totals.totalAmount || this.totals.total || '0';
            this.submitButton.innerHTML = `${buttonText} $${amount}`;
        }
    }

    /**
     * 获取翻译文本
     * @param {string} key 翻译键
     * @returns {string} 翻译文本
     */
    getTranslation(key) {
        return this.translations[this.currentLang]?.[key] || '';
    }

    /**
     * 处理完整的跳转收银台流程支付流程
     * @param {Object} result 支付结果
     * @param {Object} orderData 订单数据
     */
    processPaymentResultForRedirect(result, orderData) {
        this.logger.log('Processing payment result:', result);

        if (result.success) {
            this.handlePaymentResult(result, orderData);
        } else {
            this.handlePaymentError(result);
        }
    }

    /**
     * 处理内嵌收银台模式的支付流程
     * @param {Object} result 支付结果
     * @param {Object} orderData 订单数据
     */
    processPaymentResultForEmbedded(result, orderData) {
        this.logger.log('Processing embedded payment result:', result);

        if (result.success) {
            // Get payment methods from result or orderData
            const paymentMethods = result.data?.paymentMethods || 
                                  result.paymentMethods || 
                                  orderData?.paymentMethods || [];
            
            this.logger.log('Payment methods for embedded checkout:', paymentMethods);
            
            // Show payment methods modal
            if (typeof showPaymentMethodsModal === 'function') {
                showPaymentMethodsModal(paymentMethods);
                
                // Initialize UseePay Elements for payment rendering
                const clientSecret = result.data?.client_secret || result.client_secret;
                const paymentIntentId = result.data?.id || result.id;
                
                if (clientSecret && paymentIntentId) {
                    this.logger.log('Initializing UseePay Elements with clientSecret and paymentIntentId');
                    this.logger.log('Debug info:', {
                        clientSecret: clientSecret ? '✓ present' : '✗ missing',
                        paymentIntentId: paymentIntentId ? '✓ present' : '✗ missing',
                        useepaySDK: window.UseePay ? '✓ loaded' : '✗ not loaded',
                        publicKey: window.USEEPAY_PUBLIC_KEY ? '✓ configured' : '✗ not configured'
                    });
                    
                    // Call the public initializeUseepayElements function
                    if (typeof initializeUseepayElements === 'function') {
                        try {
                            const success = initializeUseepayElements(clientSecret, paymentIntentId);
                            if (success) {
                                this.logger.log('✓ UseePay Elements initialized successfully');
                            } else {
                                this.logger.error('UseePay Elements initialization failed');
                                this.showError('Payment element initialization failed. Please refresh the page.');
                            }
                        } catch (error) {
                            this.logger.error('Error calling initializeUseepayElements:', error);
                            this.showError('Payment element initialization failed: ' + error.message);
                        }
                    } else {
                        this.logger.error('initializeUseepayElements function not found');
                        this.logger.error('Make sure useepay-elements-initializer.js is loaded');
                        this.showError('Payment element initialization failed. Please refresh the page.');
                    }

                } else {
                    this.logger.warn('Missing clientSecret or paymentIntentId for UseePay initialization');
                    this.logger.log('Available data:', { clientSecret, paymentIntentId, result });
                    this.showError('Payment configuration incomplete. Please try again.');
                }
            } else {
                this.logger.warn('showPaymentMethodsModal function not found');
                // Fallback: show error message
                this.showError(this.getTranslation('paymentMethodsNotAvailable') || 'Payment methods not available');
            }
        } else {
            this.handlePaymentError(result);
        }
    }
}

// 导出到全局作用域
if (typeof window !== 'undefined') {
    window.PaymentResponseHandler = PaymentResponseHandler;
}
