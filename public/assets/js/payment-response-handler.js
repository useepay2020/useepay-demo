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

        // 优先检查是否有 next_action（3DS 验证）
        if (result.data.next_action && result.data.next_action.redirect) {
            this.logger.log('3DS verification required, redirecting...');
            return this.handlePaymentRedirect(result);
        }

        if (paymentStatus === 'requires_payment_method' || paymentStatus === 'requires_customer_action' || paymentStatus === 'requires_action') {
            return this.handlePaymentRedirect(result);
        } else if (paymentStatus === 'succeeded' || paymentStatus === 'failed') {
            window.location.href = result.data.return_url+'?id=' + result.data.id +'&merchant_order_id='
                +result.data.merchant_order_id+'&status='+paymentStatus;
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
     * 处理 GET 重定向 - 使用 iframe 内嵌 3DS 验证
     * @param {string} url 重定向 URL
     * @returns {boolean} 成功标志
     */
    handleGetRedirect(url) {
        this.logger.log('Performing GET redirect to:', url);
        window.location.href = url;
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
                    window.location.href = event.data.return_url;
                } else {
                    window.location.reload();
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
     * 调用后端 API 确认支付
     * @param {string} paymentIntentId 支付意图 ID
     * @param {Object} options 可选参数
     * @param {string} options.payment_method 支付方式（默认从 localStorage 获取）
     * @param {string} options.return_url 回调 URL（默认为当前域名 + /payment/callback）
     * @returns {Promise<Object>} 支付确认结果
     */
    async confirmPaymentViaAPI(paymentIntentId, options = {}) {
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

            const apiResult = apiResponse.data;
            // 转换 API 响应格式为统一格式
            let result;
            
            if (apiResult.status === 'succeeded') {
                result = {
                    success: true,
                    paymentIntent: apiResult,
                    status: apiResult.status
                };
            } else if (apiResult.status === 'requires_action' || 
                      apiResult.status === 'requires_payment_method') {
                result = {
                    success: false,
                    error: 'Payment requires additional action',
                    status: apiResult.status,
                    paymentIntent: apiResult
                };
            } else {
                result = {
                    success: false,
                    error: apiResult.message || 'Payment confirmation failed',
                    status: apiResult.status
                };
            }
            
            this.logger.log('Transformed result:', result);
            return result;
            
        } catch (error) {
            this.logger.error('API confirm error:', error);
            throw error;
        }
    }

    /**
     * 处理内嵌收银台模式的支付流程
     * @param {String} mode
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
                    if (typeof initializeElements === 'function') {
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
