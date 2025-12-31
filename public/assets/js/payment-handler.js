/**
 * Payment Handler
 * 处理支付 API 响应的统一模块
 * 
 * 功能：
 * - 解析响应数据
 * - 处理支付成功/失败
 * - 管理重定向逻辑
 * - 错误处理
 */

class PaymentHandler {
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
     * @param {Object} paymentIntent 支付结果对象
     * @param {Object} orderData 订单数据
     * @returns {boolean} 是否需要继续处理
     */
    handlePaymentResult(paymentIntent, orderData) {
        this.logger.log('Payment success:', paymentIntent);

        // 检查支付状态
        const paymentStatus = paymentIntent.status;
        this.logger.log('Payment status:', paymentStatus);
        // 保存订单数据
        this.saveOrderData(orderData);

        if (paymentStatus === 'requires_payment_method') {
            return this.handlePaymentRedirect(paymentIntent);
        }else if (paymentStatus === 'requires_customer_action') {
            //3DS 验证
            return this.handlePaymentRedirect(paymentIntent);
        } else if (paymentStatus === 'succeeded' || paymentStatus === 'failed') {
            const returnUrl = paymentIntent.return_url+'?id=' + paymentIntent.id +'&merchant_order_id='
                +paymentIntent.merchant_order_id+'&status='+paymentStatus;
            
            // 检测是否在 iframe 中
            if (window.self !== window.top) {
                this.logger.log('Detected iframe context, redirecting parent window');
                window.top.location.href = returnUrl;
            } else {
                window.location.href = returnUrl;
            }
        } else if (paymentStatus === 'payment_intent_created') {
            const errorMessage = this.currentLang === 'zh' 
                ? '支付失败，请联系您的客户经理。' 
                : 'Payment failed. Please contact your account manager.';
            this.showError(errorMessage);
        }
        return true;
    }

    /**
     * 处理支付重定向
     * @param {Object} paymentIntent 支付结果对象
     * @returns {boolean} 是否成功处理重定向
     */
    handlePaymentRedirect(paymentIntent) {
        const nextAction = paymentIntent.next_action;

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
        if (paymentIntent.client_secret) {
            this.logger.log('Payment intent created with client_secret');
            localStorage.setItem('currentPaymentIntent', JSON.stringify(paymentIntent));
            this.showInfo('Payment intent created. Please complete payment.');
            return false;
        }

        this.logger.error('No redirect URL found in response');
        this.showError('Payment URL not found. Please contact support.');
        return false;
    }

    /**
     * 处理 GET 重定向 - 使用 iframe 内嵌 3DS 验证
     * @param {string} url 重定向 URL
     * @returns {boolean} 成功标志
     */
    handleGetRedirect(url) {
        this.logger.log('Performing GET redirect to:', url);
        
        // 检测是否在 iframe 中
        if (window.self !== window.top) {
            this.logger.log('Detected iframe context, redirecting parent window');
            window.top.location.href = url;
        } else {
            window.location.href = url;
        }
        return true;
    }

    /**
     * 处理 POST 重定向 - 使用 iframe 内嵌 3DS 验证
     * @param {string} url 重定向 URL
     * @param {Object} data 表单数据
     * @returns {boolean} 成功标志
     */
    handlePostRedirect(url, data = {}) {
        this.logger.log('Performing POST redirect to:', url);

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        // 检测是否在 iframe 中，如果是则设置 target 为 _top
        if (window.self !== window.top) {
            this.logger.log('Detected iframe context, setting form target to _top');
            form.target = '_top';
        }

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
     * 显示 3DS 验证 iframe
     * @param {string} url 3DS 验证 URL
     * @param {string} method 请求方法 (GET/POST)
     * @param {Object} data POST 数据
     * @returns {boolean} 成功标志
     */
    show3DSIframe(url, method = 'GET', data = {}) {
        this.logger.log('Creating 3DS iframe:', { url, method });

        // 创建遮罩层
        const overlay = document.createElement('div');
        overlay.id = 'threeds-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9998;
            display: flex;
            justify-content: center;
            align-items: center;
        `;

        // 创建 iframe 容器
        const container = document.createElement('div');
        container.id = 'threeds-container';
        container.style.cssText = `
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 500px;
            height: 80%;
            max-height: 600px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        `;

        // 创建头部
        const header = document.createElement('div');
        header.style.cssText = `
            padding: 16px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
        `;
        header.innerHTML = `
            <span style="font-weight: 600; color: #333;">
                ${this.currentLang === 'zh' ? '安全验证' : '3D Secure Verification'}
            </span>
            <button id="threeds-close" style="
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #666;
                padding: 0;
                line-height: 1;
            ">&times;</button>
        `;

        // 创建 iframe
        const iframe = document.createElement('iframe');
        iframe.id = 'threeds-iframe';
        iframe.name = 'threeds-iframe';
        iframe.style.cssText = `
            flex: 1;
            width: 100%;
            border: none;
        `;

        // 组装 DOM
        container.appendChild(header);
        container.appendChild(iframe);
        overlay.appendChild(container);
        document.body.appendChild(overlay);

        // 关闭按钮事件
        const closeBtn = document.getElementById('threeds-close');
        closeBtn.addEventListener('click', () => {
            this.close3DSIframe();
        });

        // 点击遮罩关闭
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                this.close3DSIframe();
            }
        });

        // 监听 iframe 消息（用于接收 3DS 完成回调）
        window.addEventListener('message', this.handle3DSMessage.bind(this));

        // 根据方法提交
        if (method.toUpperCase() === 'GET') {
            iframe.src = url;
        } else {
            // POST 方式：创建表单提交到 iframe
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.target = 'threeds-iframe';
            form.style.display = 'none';

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
            document.body.removeChild(form);
        }

        return true;
    }

    /**
     * 处理 3DS iframe 消息
     * @param {MessageEvent} event 消息事件
     */
    handle3DSMessage(event) {
        this.logger.log('Received message from iframe:', event.data);

        // 验证消息来源（可根据实际情况调整）
        if (event.data && (event.data.type === '3ds_complete' || event.data.status)) {
            this.logger.log('3DS verification completed:', event.data);
            this.close3DSIframe();

            // 处理 3DS 结果
            if (event.data.status === 'succeeded' || event.data.success) {
                // 支付成功，跳转到成功页面
                if (event.data.return_url) {
                    // 检测是否在 iframe 中
                    if (window.self !== window.top) {
                        this.logger.log('Detected iframe context, redirecting parent window');
                        window.top.location.href = event.data.return_url;
                    } else {
                        window.location.href = event.data.return_url;
                    }
                } else {
                    if (window.self !== window.top) {
                        window.top.location.reload();
                    } else {
                        window.location.reload();
                    }
                }
            } else {
                // 支付失败
                this.showError(event.data.message || (this.currentLang === 'zh' ? '3DS 验证失败' : '3DS verification failed'));
            }
        }
    }

    /**
     * 关闭 3DS iframe
     */
    close3DSIframe() {
        const overlay = document.getElementById('threeds-overlay');
        if (overlay) {
            overlay.remove();
        }
        window.removeEventListener('message', this.handle3DSMessage.bind(this));
        this.logger.log('3DS iframe closed');
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
        
        // 检测是否在 iframe 中
        if (window.self !== window.top) {
            this.logger.log('Detected iframe context, redirecting parent window');
            window.top.location.href = url;
        } else {
            window.location.href = url;
        }
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
     * 创建支付意图 (Payment Intent)
     * @param {Object} checkoutData 结账数据
     * @returns {Promise<Object>} Payment Intent 数据
     */
    async createPayment(checkoutData) {
        this.logger.log('Creating payment intent with data:', checkoutData);
        
        try {
            // Submit to backend to create payment intent
            const paymentIntentResponse = await fetch('/api/payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(checkoutData)
            });

            const paymentIntentText = await paymentIntentResponse.text();
            this.logger.log('Payment intent response text:', paymentIntentText);
            
            let paymentIntentResult;
            try {
                paymentIntentResult = JSON.parse(paymentIntentText);
            } catch (e) {
                this.logger.error('JSON parse error:', e);
                throw new Error('Invalid JSON response from server');
            }

            if (!paymentIntentResult.success || !paymentIntentResult.data) {
                const errorMsg = paymentIntentResult.data?.error?.message || 'Failed to create payment intent';
                this.logger.error('Payment intent creation failed:', errorMsg);
                throw new Error(errorMsg);
            }
            
            this.logger.log('✓ Payment intent created successfully:', paymentIntentResult.data);
            return paymentIntentResult.data;
            
        } catch (error) {
            this.logger.error('createPayment error:', error);
            throw error;
        }
    }

    /**
     * 处理完整的跳转收银台流程支付流程
     * @param {Object} result 支付结果
     * @param {Object} orderData 订单数据
     */
    processPaymentResultForRedirect(result, orderData) {
        this.logger.log('Processing payment result:', result);

        if (result.success) {
            this.handlePaymentResult(result.data, orderData);
        } else {
            this.handlePaymentError(result);
        }
    }

    /**
     * 调用后端 API 确认支付
     * @param {string} paymentIntentId 支付意图 ID
     * @param {Object} options 可选参数
     * @param {string} options.payment_method 支付方式（默认从 localStorage 获取）
     * @param {string} options.return_url 回调 URL（默认为当前域名 + /payment/callback）
     * @returns {Promise<Object>} PaymentIntent支付确认结果
     */
    async confirmPaymentForApi(paymentIntentId, options = {}) {
        this.logger.log('Calling /api/payment/confirm with ID:', paymentIntentId);
        
        if (!paymentIntentId) {
            throw new Error('Payment Intent ID is required');
        }

        this.logger.log('Request options:', options);
        
        try {
            // 调用后端 API
            const response = await fetch(`/api/payment/confirm/${paymentIntentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(options)
            });
            
            // 解析响应
            const apiResponse = await response.json();
            this.logger.log('API confirm response:', apiResponse);

            return  apiResponse.data;
            
        } catch (error) {
            this.logger.error('API confirm error:', error);
            throw error;
        }
    }

    /**
     * 更新支付元素的金额和币种
     * @param {number} amount 新的支付金额 (最小单位，如分)
     * @param {string} currency 货币代码 (如 'USD', 'CNY', 'EUR')
     * @param {Object} options 可选的额外更新选项
     * @returns {boolean} 更新成功返回 true，失败返回 false
     */
    updatePaymentElement(amount, currency, options = {}) {
        this.logger.log('=== Updating Payment Element Amount ===');
        this.logger.log('Debug: amount =', amount ? '✓' : '✗', `(${amount})`);
        this.logger.log('Debug: currency =', currency ? '✓' : '✗', `(${currency})`);

        // Get useepayElements from global scope
        const useepayElements = window.useepayElements;

        // Check if payment element exists
        if (!useepayElements) {
            this.logger.error('❌ Payment element not initialized');
            this.logger.error('Debug: useepayElements =', useepayElements);
            return false;
        }

        try {
            // Prepare update options
            const updateOptions = {
                amount: amount,
                currency: currency,
                ...options
            };

            this.logger.log('Calling useepayElements.update() with options:', updateOptions);
            
            // Call the update method on payment element
            useepayElements.update(updateOptions);
            
            this.logger.log('✓ Payment element updated successfully');
            this.logger.log('Updated amount:', amount);
            this.logger.log('Updated currency:', currency);
            return true;
        } catch (error) {
            this.logger.error('❌ Error updating payment element:', error);
            this.logger.error('Error details:', {
                message: error.message,
                stack: error.stack,
                name: error.name
            });
            return false;
        }
    }

    /**
     * 检查支付元素是否已经绑定
     * @param {string} elementId 支付元素容器ID
     * @returns {boolean} 如果已绑定返回 true，否则返回 false
     */
    isElementAlreadyBound(elementId) {
        const container = document.getElementById(elementId);
        
        if (!container) {
            return false;
        }
        
        // Check multiple indicators that element is already bound
        const isAlreadyBound = container.hasAttribute('data-useepay-bound') || 
                              container.querySelector('.useepay-payment-element') !== null ||
                              container.querySelector('iframe[name^="__useepay"]') !== null;
        
        return isAlreadyBound;
    }

    /**
     * 初始化订阅支付元素
     * @param {number} amount 支付金额
     * @param {string} currency 货币单位
     * @param {Object} options 可选参数
     * @param {string} elementId 支付元素容器ID (默认: 'payment-element')
     * @returns {boolean} 初始化是否成功
     */
    initializeElementsForSubscription(amount, currency, options = {}, elementId = 'payment-element') {
        return this.initializeElements('subscription', amount, currency, options, elementId);
    }

    /**
     * 初始化订阅支付元素
     * @param {number} amount 支付金额
     * @param {string} currency 货币单位
     * @param {Object} options 可选参数
     * @param {string} elementId 支付元素容器ID (默认: 'payment-element')
     * @returns {boolean} 初始化是否成功
     */
    initializeElementsForPayment(amount, currency, options = {}, elementId = 'payment-element') {
        return this.initializeElements('payment', amount, currency, options, elementId);
    }
    /**
     * 初始化支付元素
     * @param {string} mode 'subscription' | 'payment'
     * @param {number} amount 支付金额 (数字类型)
     * @param {string} currency 货币单位
     * @param {Object} options 可选参数
     * @param {string} elementId 支付元素容器ID (默认: 'payment-element')
     * @returns {boolean} 初始化是否成功
     */
    initializeElements(mode, amount, currency, options = {}, elementId = 'payment-element') {
        this.logger.log('=== Initializing UseePay Elements ===');
        this.logger.log('Debug: mode =', mode ? '✓' : '✗', `(${mode})`);
        
        // Check if element is already bound (early check)
        if (this.isElementAlreadyBound(elementId)) {
            this.logger.log('⚠️ Payment element already bound, skipping initialization');
            this.logger.log('Container already has UseePay payment element');
            return true;
        }
        
        const paymentElementContainer = document.getElementById(elementId);
        
        // Convert amount to number type
        const amountAsNumber = typeof amount === 'number' ? amount : parseFloat(amount);
        this.logger.log('Debug: amount =', !isNaN(amountAsNumber) ? '✓' : '✗', `(${amountAsNumber})`);
        this.logger.log('Debug: currency =', currency ? '✓' : '✗', `(${currency})`);
        
        // Validate amount is a valid number
        if (isNaN(amountAsNumber)) {
            this.logger.error('❌ Invalid amount: must be a number');
            this.logger.error('Debug: amount value =', amount);
            return false;
        }
        
        // Check if UseePay SDK is loaded
        if (!window.UseePay) {
            this.logger.error('❌ UseePay SDK not loaded');
            this.logger.error('Debug: window.UseePay =', window.UseePay);
            alert('Payment SDK failed to load. Please refresh the page.');
            return false;
        }
        this.logger.log('✓ UseePay SDK loaded');

        try {
            // Get public key from window config (set in PHP)
            const publicKey = window.USEEPAY_PUBLIC_KEY;
            this.logger.log('Debug: publicKey =', publicKey ? '✓ configured' : '✗ missing');

            if (!publicKey) {
                this.logger.error('❌ UseePay public key not configured');
                this.logger.error('Debug: window.USEEPAY_PUBLIC_KEY =', window.USEEPAY_PUBLIC_KEY);
                alert('Payment configuration error. Please contact support.');
                return false;
            }

            // Initialize UseePay instance
            this.logger.log('Initializing UseePay instance with public key...');
            const useepayInstance = window.UseePay(publicKey);
            this.logger.log('✓ UseePay instance initialized');

            // Initialize Elements with mode, amount, and currency
            this.logger.log('Creating UseePay Elements...');
            const useepayElements = useepayInstance.elements({
                mode: mode,
                amount: amountAsNumber,
                currency: currency
            });
            this.logger.log('✓ UseePay Elements created');

            // Create payment element
            this.logger.log('Creating payment element...');
            const useepayPaymentElement = useepayElements.create('payment', options);
            this.logger.log('✓ Payment element created');

            // Store instances globally for access by other functions
            window.useepayInstance = useepayInstance;
            window.useepayElements = useepayElements;
            window.useepayPaymentElement = useepayPaymentElement;

            // Mount payment element to DOM (container already checked at the beginning)
            this.logger.log(`Debug: ${elementId} container =`, paymentElementContainer ? '✓ found' : '✗ not found');

            if (paymentElementContainer) {
                // Clear any existing content in the container
                if (paymentElementContainer.hasChildNodes()) {
                    this.logger.log('Clearing existing content from payment-element container...');
                    paymentElementContainer.innerHTML = '';
                }

                this.logger.log('Mounting payment element...');
                useepayPaymentElement.mount(elementId);
                
                // Mark element as bound
                paymentElementContainer.setAttribute('data-useepay-bound', 'true');

                // Detect which page this is running on
                const containerLocation = paymentElementContainer.closest('.payment-methods-modal')
                    ? 'pricing.php (payment methods modal)'
                    : 'embedded_checkout.php (checkout form)';

                this.logger.log('✓ Payment element mounted successfully');
                this.logger.log('Container location:', containerLocation);
                this.logger.log('Container class:', paymentElementContainer.className || 'none');
                return true;
            } else {
                this.logger.error('❌ Payment element container not found');
                this.logger.error('Available containers:', {
                    'embedded_checkout': !!document.querySelector('.checkout-form'),
                    'pricing_modal': !!document.getElementById('paymentMethodsModal')
                });
                alert('Payment form container not found. Please refresh the page.');
                return false;
            }
        } catch (error) {
            this.logger.error('❌ Error initializing UseePay Elements:', error);
            this.logger.error('Error details:', {
                message: error.message,
                stack: error.stack,
                name: error.name
            });
            alert('Failed to initialize payment form: ' + error.message);
            return false;
        }
    }

    /**
     * 创建订阅 - 调用后端 API
     * @param {Object} subscriptionData 订阅数据
     * @returns {Promise<Object>} 支付意图数据 (result.data)
     */
    async createSubscription(subscriptionData) {
        this.logger.log('=== createSubscription called ===');
        this.logger.log('Subscription data:', subscriptionData);
        
        try {
            const response = await fetch('/api/subscription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(subscriptionData)
            });
            
            const result = await this.handleResponse(response);
            this.logger.log('✓ Subscription created:', result);

            // Return payment intent data
            return result.data;
        } catch (error) {
            this.logger.error('createSubscription error:', error);
            throw error;
        }
    }

    /**
     * 处理支付内嵌收银台完整流程
     * Embedded mode: submit form -> createPayment -> confirmPayment -> handle result
     * 
     * @param {Object} checkoutData 结账数据
     * @param {Function} updateStatusFn 更新状态的回调函数 (status, message)
     * @returns {Promise<void>}
     */
    async processPaymentEmbeddedCheckout(checkoutData, updateStatusFn) {
        this.logger.log('=== Processing Payment EMBEDDED mode ===');
        
        try {
            // Step 1: Submit form validation
            this.logger.log('Step 1: Submitting form validation...');
            const useepayElements = window.useepayElements;
            
            if (!useepayElements) {
                throw new Error('UseePay Elements not initialized');
            }
            
            const submitResult = await useepayElements.submit();
            const { selectedPaymentMethod, error: submitError } = submitResult;
            
            if (submitError) {
                this.logger.error('Form validation error:', submitError);
                throw new Error(submitError.message || 'Form validation failed');
            }
            
            if (!selectedPaymentMethod) {
                throw new Error('No payment method selected');
            }
            
            this.logger.log('✓ Form validation passed, payment method:', selectedPaymentMethod);
            
            // Step 2: Create payment intent
            this.logger.log('Step 2: Creating payment intent...');
            const paymentIntent = await this.createPayment(checkoutData);
            
            if (updateStatusFn) {
                const processingMsg = this.getTranslation('processingSuccess') || 'Processing successful';
                updateStatusFn('success', processingMsg);
            }
            
            this.logger.log('✓ Payment intent created:', paymentIntent.id);
            
            const clientSecret = paymentIntent.client_secret;
            const paymentIntentId = paymentIntent.id;
            
            if (!clientSecret || !paymentIntentId) {
                throw new Error('Missing client_secret or payment_intent_id');
            }
            
            // Step 3: Confirm payment with UseePay
            this.logger.log('Step 3: Confirming payment with UseePay...');
            const useepayInstance = window.useepayInstance;
            
            if (!useepayInstance) {
                throw new Error('UseePay instance not initialized');
            }
            
            const confirmResult = await useepayInstance.confirmPayment({
                elements: useepayElements,
                paymentIntentId: paymentIntentId,
                clientSecret: clientSecret
            });
            
            const { paymentIntent: confirmedPaymentIntent, error: confirmError } = confirmResult;
            
            if (confirmError) {
                this.logger.error('Payment confirmation error:', confirmError);
                throw new Error(confirmError.message || 'Payment confirmation failed');
            }
            
            if (!confirmedPaymentIntent) {
                throw new Error('No payment intent returned');
            }
            
            this.logger.log('✓ Payment confirmed:', confirmedPaymentIntent);
            
            // Step 4: Handle payment result
            this.logger.log('Step 4: Handling payment result...');
            this.handlePaymentResult(confirmedPaymentIntent, checkoutData);
            
        } catch (error) {
            this.logger.error('processPaymentEmbeddedCheckout error:', error);
            throw error;
        }
    }

    /**
     * 处理订阅内嵌收银台完整流程
     * Embedded mode: submit form -> createSubscription -> confirmPayment -> handle result
     * 
     * @param {Object} subscriptionData 订阅数据
     * @param {Function} updateStatusFn 更新状态的回调函数 (status, message)
     * @returns {Promise<void>}
     */
    async processSubscriptionEmbeddedCheckout(subscriptionData, updateStatusFn) {
        this.logger.log('=== Processing Subscription EMBEDDED mode ===');
        
        try {
            // Step 1: Submit form validation
            this.logger.log('Step 1: Submitting form validation...');
            const useepayElements = window.useepayElements;
            
            if (!useepayElements) {
                throw new Error('UseePay Elements not initialized');
            }
            
            const submitResult = await useepayElements.submit();
            const { selectedPaymentMethod, error: submitError } = submitResult;
            
            if (submitError) {
                this.logger.error('Form validation error:', submitError);
                throw new Error(submitError.message || 'Form validation failed');
            }
            
            if (!selectedPaymentMethod) {
                throw new Error('No payment method selected');
            }
            
            this.logger.log('✓ Form validation passed, payment method:', selectedPaymentMethod);
            
            // Step 2: Create subscription
            this.logger.log('Step 2: Creating subscription...');
            const paymentIntent = await this.createSubscription(subscriptionData);
            
            if (updateStatusFn) {
                const processingMsg = this.getTranslation('processingSuccess') || 'Processing successful';
                updateStatusFn('success', processingMsg);
            }
            
            const clientSecret = paymentIntent.client_secret;
            const paymentIntentId = paymentIntent.id;
            
            if (!clientSecret || !paymentIntentId) {
                throw new Error('Missing client_secret or payment_intent_id');
            }
            
            // Step 3: Confirm payment with UseePay
            this.logger.log('Step 3: Confirming payment with UseePay...');
            const useepayInstance = window.useepayInstance;
            
            if (!useepayInstance) {
                throw new Error('UseePay instance not initialized');
            }
            
            const confirmResult = await useepayInstance.confirmPayment({
                elements: useepayElements,
                paymentIntentId: paymentIntentId,
                clientSecret: clientSecret
            });
            
            const { paymentIntent: confirmedPaymentIntent, error: confirmError } = confirmResult;
            
            if (confirmError) {
                this.logger.error('Payment confirmation error:', confirmError);
                throw new Error(confirmError.message || 'Payment confirmation failed');
            }
            
            if (!confirmedPaymentIntent) {
                throw new Error('No payment intent returned');
            }
            
            this.logger.log('✓ Payment confirmed:', confirmedPaymentIntent);
            
            // Step 4: Handle payment result
            this.logger.log('Step 4: Handling payment result...');
            this.handlePaymentResult(confirmedPaymentIntent, subscriptionData);
            
        } catch (error) {
            this.logger.error('processSubscriptionEmbeddedCheckout error:', error);
            throw error;
        }
    }

    /**
     * 处理订阅跳转收银台完整流程
     * Redirect mode: createSubscription -> redirect to payment page
     * 
     * @param {Object} subscriptionData 订阅数据
     * @param {Function} updateStatusFn 更新状态的回调函数 (status, message)
     * @returns {Promise<void>}
     */
    async processSubscriptionForRedirect(subscriptionData, updateStatusFn) {
        this.logger.log('=== Processing REDIRECT mode ===');
        
        try {
            // Step 1: Validate subscription data
            if (!subscriptionData) {
                throw new Error('Subscription data not found');
            }
            
            // Step 2: Create subscription
            this.logger.log('Step 2: Creating subscription...');
            const paymentIntent = await this.createSubscription(subscriptionData);
            
            if (updateStatusFn) {
                const processingMsg = this.getTranslation('processingSuccess') || 'Processing successful';
                updateStatusFn('success', processingMsg);
            }
            
            this.logger.log('✓ Subscription created:', paymentIntent);
            
            // Step 3: Handle payment redirect
            this.logger.log('Step 3: Processing payment redirect...');
            this.handlePaymentResult(paymentIntent, subscriptionData);
            
        } catch (error) {
            this.logger.error('processSubscriptionForRedirect error:', error);
            throw error;
        }
    }

    /**
     * 处理订阅 API 对接完整流程
     * API mode: get cached data -> createSubscription -> generatePaymentMethodData -> confirmPaymentForApi -> handle result
     * 
     * @param {Object} subscriptionData 缓存键名
     * @param {Function} updateStatusFn 更新状态的回调函数 (status, message)
     * @returns {Promise<Object>} 支付确认结果
     */
    async processSubscriptionForApi(subscriptionData, updateStatusFn) {
        this.logger.log('=== Processing API mode ===');
        
        try {
            // Step 1: Get subscription data
            if (!subscriptionData) {
                throw new Error('Subscription data not found in cache');
            }
            // Step 2: Create subscription
            this.logger.log('Step 2: Creating subscription...');
            const paymentIntent = await this.createSubscription(subscriptionData);
            
            if (updateStatusFn) {
                const processingMsg = this.getTranslation('processingSuccess') || 'Processing successful';
                updateStatusFn('success', processingMsg);
            }
            
            const paymentIntentId = paymentIntent.id;
            
            if (!paymentIntentId) {
                throw new Error('Missing payment_intent_id');
            }
            
            this.logger.log('✓ Subscription created, payment intent ID:', paymentIntentId);
            
            // Step 3: Generate payment method data
            this.logger.log('Step 3: Generating payment method data...');
            const payment_method_data = this.generatePaymentMethodData();
            
            this.logger.log('✓ Payment method data generated');
            
            // Step 4: Confirm payment via API
            this.logger.log('Step 4: Confirming payment via API...');
            const confirmedPaymentIntent = await this.confirmPaymentForApi(paymentIntentId, {
                payment_method_data: payment_method_data
            });
            
            this.logger.log('✓ Payment confirmation result:', confirmedPaymentIntent);
            
            // Step 5: Handle payment result
            this.logger.log('Step 5: Handling payment result...');
            this.handlePaymentResult(confirmedPaymentIntent, subscriptionData);
        } catch (error) {
            this.logger.error('processSubscriptionForApi error:', error);
            throw error;
        }
    }

    /**
     * Generate payment method data based on selected payment method
     * @returns {Object|null} Payment method data object
     */
    generatePaymentMethodData() {
        this.logger.log('=== Generating Payment Method Data ===');
        // Get selected payment method from rendered UI (radio button)
        const selectedPaymentMethodRadio = document.querySelector('input[name="paymentMethod"]:checked');
        const selectedPaymentMethod = selectedPaymentMethodRadio ? selectedPaymentMethodRadio.value : 'card';
        console.log('Selected payment method from UI:', selectedPaymentMethod);
        
        let payment_method_data = null;
        
        if (selectedPaymentMethod === 'card') {
            // Collect card information from input fields
            const cardNumber = document.getElementById('cardNumber')?.value?.replace(/\s/g, '');
            const expiryDate = document.getElementById('expiryDate')?.value;
            const cvv = document.getElementById('cvv')?.value;
            const cardHolder = document.getElementById('cardHolder')?.value;
            
            // Parse expiry date (MM/YY format)
            const [expMonth, expYear] = expiryDate ? expiryDate.split('/') : ['', ''];
            
            // Validate card information
            if (!cardNumber || !expiryDate || !cvv) {
                const errorMsg = this.getTranslation('pleaseEnterCardInfo') || 'Please enter complete card information';
                this.logger.error('Card validation failed:', errorMsg);
                throw new Error(errorMsg);
            }
            
            payment_method_data = {
                type: 'card',
                card: {
                    number: cardNumber,
                    expiry_month: expMonth,
                    expiry_year: expYear,
                    cvc: cvv,
                    name: cardHolder || ''
                }
            };
            
            this.logger.log('Card data collected:', {
                number: cardNumber ? '****' + cardNumber.slice(-4) : 'N/A',
                exp_month: expMonth,
                exp_year: expYear,
                cvc: cvv ? '***' : 'N/A',
                name: cardHolder
            });
        } else {
            // For other payment methods, use basic structure
            payment_method_data = {
                type: selectedPaymentMethod
            };
            this.logger.log('Payment method data:', payment_method_data);
        }
        
        return payment_method_data;
    }
}

// 导出到全局作用域
if (typeof window !== 'undefined') {
    window.PaymentHandler = PaymentHandler;
    // Keep backward compatibility
    window.PaymentResponseHandler = PaymentHandler;
}
