<?php
/**
 * Subscription Pricing Page
 * Similar to https://windsurf.com/pricing
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订阅计划 - UseePay Demo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/pricing.css">
    <!-- Pricing Page Internationalization (must load before inline scripts) -->
    <script src="/assets/js/i18n/subscription/pricing-i18n.js"></script>
    <!-- UseePay SDK -->
    <script src="https://checkout-sdk.useepay.com/1.0.1/useepay.min.js"></script>
    <!-- Payment Methods Configuration -->
    <script src="/assets/js/payment/payment-methods-config.js"></script>
    <!-- UseePay Elements Initializer (must be loaded first) -->
    <script src="/assets/js/useepay-elements-initializer.js"></script>
    <!-- Payment Response Handler -->
    <script src="/assets/js/payment-response-handler.js"></script>
</head>
<body>
    <div class="container">
        <a href="/" class="back-button">
            <i class="fas fa-arrow-left"></i>
            <span data-i18n="backHome">返回首页</span>
        </a>

        <button class="register-button" onclick="openAuthModal()">
            <i class="fas fa-user"></i>
            <span id="authButtonText" data-i18n="register">注册</span>
        </button>

        <header>
            <div class="logo">💳 UseePay Demo</div>
            <h1 data-i18n="selectPlan">选择适合您的订阅计划</h1>
            <p data-i18n="flexiblePricing">灵活的定价选项，满足各种业务需求</p>

            <div class="toggle-billing">
                <span class="billing-label" data-i18n="monthlyBilling">按月计费</span>
                <div class="toggle-switch">
                    <input type="radio" id="monthly" name="billing" value="monthly" checked>
                    <label for="monthly" data-i18n="monthly">月度</label>
                    <input type="radio" id="annual" name="billing" value="annual">
                    <label for="annual" data-i18n="annual">年度</label>
                </div>
                <span class="billing-label" data-i18n="annualBilling">按年计费</span> <span class="save-badge" data-i18n="saveBadge">节省 20%</span>
            </div>
        </header>

        <div class="pricing-grid">
            <!-- Starter Plan -->
            <div class="pricing-card">
                <h3 class="plan-name" data-i18n="starter">入门版</h3>
                <p class="plan-description" data-i18n="starterDesc">适合个人和小型项目</p>
                <div class="price">
                    <span class="price-currency">$</span><span id="starter-price">9.9</span>
                </div>
                <p class="price-period" id="starter-period"><span data-i18n="perMonth">/月</span></p>
                <button class="cta-button secondary" onclick="selectPlan('starter')" data-i18n="selectThisPlan">选择此计划</button>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="starterTransactions">最多 1,000 笔交易/月</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="basicPaymentMethods">基础支付方式支持</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="standardSupport">标准技术支持</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="basicAnalytics">基础分析报告</span>
                    </div>
                    <div class="feature-item disabled">
                        <i class="fas fa-times feature-icon"></i>
                        <span data-i18n="apiAccess">API 访问</span>
                    </div>
                    <div class="feature-item disabled">
                        <i class="fas fa-times feature-icon"></i>
                        <span data-i18n="prioritySupport2">优先支持</span>
                    </div>
                </div>
            </div>

            <!-- Professional Plan -->
            <div class="pricing-card featured">
                <div class="featured-badge" data-i18n="mostPopular">最受欢迎</div>
                <h3 class="plan-name" data-i18n="professional">专业版</h3>
                <p class="plan-description" data-i18n="professionalDesc">适合成长中的企业</p>
                <div class="price">
                    <span class="price-currency">$</span><span id="professional-price">29.9</span>
                </div>
                <p class="price-period" id="professional-period"><span data-i18n="perMonth">/月</span></p>
                <button class="cta-button" onclick="selectPlan('professional')" data-i18n="selectThisPlan">选择此计划</button>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="professionalTransactions">最多 50,000 笔交易/月</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="allPaymentMethods">所有支付方式支持</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="prioritySupport">优先技术支持</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="advancedAnalytics">高级分析报告</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="fullApiAccess">完整 API 访问</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="prioritySupport2">优先支持</span>
                    </div>
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div class="pricing-card">
                <h3 class="plan-name" data-i18n="enterprise">企业版</h3>
                <p class="plan-description" data-i18n="enterpriseDesc">适合大型企业</p>
                <div class="price">
                    <span class="price-currency">$</span><span id="enterprise-price">99.9</span>
                </div>
                <p class="price-period" id="enterprise-period"><span data-i18n="perMonth">/月</span></p>
                <button class="cta-button secondary" onclick="selectPlan('enterprise')" data-i18n="selectThisPlan">选择此计划</button>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="unlimited">无限交易</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="allPaymentMethods">所有支付方式支持</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="dedicatedSupport">24/7 专属支持</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="customAnalytics">自定义分析报告</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="fullApiAccess">完整 API 访问</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check feature-icon"></i>
                        <span data-i18n="accountManager">专属账户经理</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison Section -->
        <div class="comparison-section">
            <h2 data-i18n="featureComparison">功能对比</h2>
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th data-i18n="feature">功能</th>
                        <th style="text-align: center;" data-i18n="starter">入门版</th>
                        <th style="text-align: center;" data-i18n="professional">专业版</th>
                        <th style="text-align: center;" data-i18n="enterprise">企业版</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="feature-name" data-i18n="monthly">月度交易数</td>
                        <td style="text-align: center;">1,000</td>
                        <td style="text-align: center;">50,000</td>
                        <td style="text-align: center;" data-i18n="unlimited">无限</td>
                    </tr>
                    <tr>
                        <td class="feature-name" data-i18n="paymentMethods">支付方式</td>
                        <td style="text-align: center;">
                            <span class="check">✓</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="check">✓</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="check">✓</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="feature-name" data-i18n="apiAccess">API 访问</td>
                        <td style="text-align: center;">
                            <span class="cross">✗</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="check">✓</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="check">✓</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="feature-name" data-i18n="support">技术支持</td>
                        <td style="text-align: center;" data-i18n="standard">标准</td>
                        <td style="text-align: center;" data-i18n="priority">优先</td>
                        <td style="text-align: center;">24/7</td>
                    </tr>
                    <tr>
                        <td class="feature-name" data-i18n="analytics">分析报告</td>
                        <td style="text-align: center;" data-i18n="basic">基础</td>
                        <td style="text-align: center;" data-i18n="advanced">高级</td>
                        <td style="text-align: center;" data-i18n="custom">自定义</td>
                    </tr>
                    <tr>
                        <td class="feature-name" data-i18n="accountManagement">专属账户经理</td>
                        <td style="text-align: center;">
                            <span class="cross">✗</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="cross">✗</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="check">✓</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <h2 data-i18n="faq">常见问题</h2>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span class="icon"><i class="fas fa-chevron-down"></i></span>
                    <span data-i18n="canUpgrade">我可以随时升级或降级我的计划吗？</span>
                </div>
                <div class="faq-answer" data-i18n="canUpgradeAnswer">
                    是的，您可以随时升级或降级您的订阅计划。升级会立即生效，降级将在下一个计费周期生效。我们会按比例计算费用。
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span class="icon"><i class="fas fa-chevron-down"></i></span>
                    <span data-i18n="exceedLimit">如果我超过了我的交易限额会怎样？</span>
                </div>
                <div class="faq-answer" data-i18n="exceedLimitAnswer">
                    如果您接近交易限额，我们会向您发送通知。您可以选择升级计划或联系我们讨论自定义解决方案。我们不会在没有通知的情况下阻止交易。
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span class="icon"><i class="fas fa-chevron-down"></i></span>
                    <span data-i18n="freeTrial">你们提供免费试用吗？</span>
                </div>
                <div class="faq-answer" data-i18n="freeTrialAnswer">
                    是的，我们为所有新用户提供 14 天的免费试用。您可以在试用期间体验所有功能，无需提供信用卡信息。
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span class="icon"><i class="fas fa-chevron-down"></i></span>
                    <span data-i18n="paymentMethods2">你们接受哪些付款方式？</span>
                </div>
                <div class="faq-answer" data-i18n="paymentMethodsAnswer">
                    我们接受所有主要的信用卡（Visa、MasterCard、American Express）、支付宝、微信支付和银行转账。所有交易都是安全加密的。
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <span class="icon"><i class="fas fa-chevron-down"></i></span>
                    <span data-i18n="canCancel">我可以取消我的订阅吗？</span>
                </div>
                <div class="faq-answer" data-i18n="canCancelAnswer">
                    是的，您可以随时取消订阅。取消后，您将能够访问您的账户直到当前计费周期结束。我们不收取任何取消费用。
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Modal -->
    <div id="processingModal" class="processing-modal">
        <div class="processing-modal-content">
            <div class="processing-spinner"></div>
            <div class="processing-title" id="processingTitle" data-i18n="processing">处理中...</div>
            <div class="processing-message" id="processingMessage" data-i18n="processingMessage">正在创建您的订阅，请稍候...</div>
            <div class="processing-status" id="processingStatus"></div>
        </div>
    </div>

    <!-- Payment Methods Modal -->
    <div id="paymentMethodsModal" class="payment-methods-modal">
        <div class="payment-methods-modal-content">
            <div class="payment-methods-header">
                <h2 class="payment-methods-title" data-i18n="selectPaymentMethod">选择支付方式</h2>
                <button class="payment-methods-close" onclick="closePaymentMethodsModal()">×</button>
            </div>
            <div id="paymentMethodsContainer"></div>
            <div id="payment-element" style="margin: 20px 0;"></div>
            <div class="payment-methods-footer">
                <button class="payment-methods-btn cancel" onclick="closePaymentMethodsModal()" data-i18n="cancel">取消</button>
                <button class="payment-methods-btn confirm" onclick="confirmPaymentMethod()" data-i18n="confirm">确认</button>
            </div>
        </div>
    </div>

    <script>
        // ===== 国际化配置 =====
        // 使用外部翻译文件（从 pricing-i18n.js 加载）
        const translations = window.pricingTranslations;

        // 检查翻译对象是否加载成功
        if (!translations) {
            console.error('❌ Translations not loaded! pricing-i18n.js may have failed to load.');
            console.error('Please check if /assets/js/pricing-i18n.js is accessible.');
            throw new Error('Translations not loaded');
        }

        // 从 localStorage 读取语言设置，默认为中文
        let currentLang = localStorage.getItem('language') || 'zh';
        
        // Initialize global payment response handler
        let paymentHandler = null;
        
        console.log('=== Pricing Page Language Initialization ===');
        console.log('Stored language:', localStorage.getItem('language'));
        console.log('Current language:', currentLang);
        console.log('Translations loaded:', !!translations);
        console.log('Available languages:', Object.keys(translations));

        // Initialize language on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== DOMContentLoaded Event Fired ===');
            console.log('Initializing language to:', currentLang);
            setLanguage(currentLang);
            updateLanguageButtons();
            console.log('=== Language Initialization Complete ===');
        });

        // Language switcher function
        function switchLanguage(lang) {
            console.log('Switching language to:', lang);
            setLanguage(lang);
            updateLanguageButtons();
        }

        // Update language button states
        function updateLanguageButtons() {
            const zhBtn = document.getElementById('lang-zh');
            const enBtn = document.getElementById('lang-en');
            
            if (zhBtn && enBtn) {
                zhBtn.classList.toggle('active', currentLang === 'zh');
                enBtn.classList.toggle('active', currentLang === 'en');
            }
        }

        function setLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('language', lang);
            
            console.log('Setting language to:', lang);
            
            // Update all elements with data-i18n attribute
            document.querySelectorAll('[data-i18n]').forEach(element => {
                const key = element.getAttribute('data-i18n');
                if (translations[lang] && translations[lang][key]) {
                    element.textContent = translations[lang][key];
                }
            });
            
            // Update price period labels based on current billing type
            const billingRadio = document.querySelector('input[name="billing"]:checked');
            if (billingRadio) {
                const currentBillingType = billingRadio.value;
                const periodText = currentBillingType === 'annual' ? translations[lang].perYear : translations[lang].perMonth;
                const starterPeriod = document.getElementById('starter-period');
                const professionalPeriod = document.getElementById('professional-period');
                const enterprisePeriod = document.getElementById('enterprise-period');
                
                if (starterPeriod) {
                    starterPeriod.innerHTML = `<span data-i18n="${currentBillingType === 'annual' ? 'perYear' : 'perMonth'}">${periodText}</span>`;
                }
                if (professionalPeriod) {
                    professionalPeriod.innerHTML = `<span data-i18n="${currentBillingType === 'annual' ? 'perYear' : 'perMonth'}">${periodText}</span>`;
                }
                if (enterprisePeriod) {
                    enterprisePeriod.innerHTML = `<span data-i18n="${currentBillingType === 'annual' ? 'perYear' : 'perMonth'}">${periodText}</span>`;
                }
            }
            
            // Update modal form labels and placeholders
            try {
                updateModalLabels(lang);
            } catch (e) {
                console.warn('Error updating modal labels:', e);
            }
            
            // Update auth button text (must be after updateModalLabels to ensure proper update)
            // 从缓存中获取 customer 对象
            const customerData = localStorage.getItem('customer');
            const customer = customerData ? JSON.parse(customerData) : null;
            
            console.log('Customer data in setLanguage:', {
                hasCustomer: !!customer,
                customer: customer
            });
            
            const authButtonText = document.getElementById('authButtonText');
            if (authButtonText) {
                // 如果有 customer 对象，显示个人中心，否则显示注册
                const buttonText = customer ? translations[lang].personalCenter : translations[lang].register;
                authButtonText.textContent = buttonText;
                
                console.log('Updated auth button text:', {
                    lang: lang,
                    hasCustomer: !!customer,
                    buttonText: buttonText,
                    personalCenter: translations[lang].personalCenter,
                    register: translations[lang].register
                });
            }
            
            // Update HTML lang attribute
            document.documentElement.lang = lang === 'zh' ? 'zh-CN' : 'en';
        }

        // Handle billing toggle
        document.querySelectorAll('input[name="billing"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updatePrices(this.value);
            });
        });

        function updatePrices(billingType) {
            const multiplier = billingType === 'annual' ? 10 : 1;
            const discount = billingType === 'annual' ? 0.8 : 1;

            document.getElementById('starter-price').textContent = (9.9 * multiplier * discount).toFixed(1);
            document.getElementById('professional-price').textContent = (29.9 * multiplier * discount).toFixed(1);
            document.getElementById('enterprise-price').textContent = (99.9 * multiplier * discount).toFixed(1);

            // Update period labels with current language
            const periodText = billingType === 'annual' ? translations[currentLang].perYear : translations[currentLang].perMonth;
            document.getElementById('starter-period').innerHTML = `<span data-i18n="${billingType === 'annual' ? 'perYear' : 'perMonth'}">${periodText}</span>`;
            document.getElementById('professional-period').innerHTML = `<span data-i18n="${billingType === 'annual' ? 'perYear' : 'perMonth'}">${periodText}</span>`;
            document.getElementById('enterprise-period').innerHTML = `<span data-i18n="${billingType === 'annual' ? 'perYear' : 'perMonth'}">${periodText}</span>`;
        }
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

        function selectPlan(plan) {
            // 检查浏览器缓存中是否有消费者对象
            const customerData = localStorage.getItem('customer');
            const customer = customerData ? JSON.parse(customerData) : null;
            
            if (!customer) {
                // 没有消费者缓存，弹出注册界面
                console.log('No consumer found in cache, showing registration modal');
                openAuthModal();
                return;
            }
            
            // 构建 Subscription 对象
            const billingType = document.querySelector('input[name="billing"]:checked').value;
            const planName = getPlanName(plan);
            
            // 获取价格信息
            const planCard = event.target.closest('.pricing-card');
            const priceElement = planCard.querySelector('[id$="-price"]');
            const price = priceElement ? priceElement.textContent.replace(/[^\d.]/g, '') : '0';
            
            // 获取币种
            const currency = 'USD';

            const interval = billingType === 'annual' ? 'year' : 'month';
            
            // 从浏览器缓存中获取支付方式

            const paymentMethods = getPaymentMethods();
            
            console.log('Payment methods from cache:', paymentMethods);
            
            // 构建 Subscription 对象用于后端
            const subscriptionData = {
                customer_id: customer.id,
                recurring: {
                    interval: interval,
                    interval_count: 1,
                    unit_amount: parseFloat(price),
                    totalBillingCycles: 10
                },
                currency: currency,
                description: planName,
                paymentMethods: paymentMethods,
                order: {
                    products: [
                        {
                            name: planName,
                            quantity: 1,
                            price: parseFloat(price)
                        }
                    ]
                }
            };
            
            console.log('Sending subscription data to backend:', subscriptionData);
            
            // Show processing modal
            showProcessingModal();
            
            // Initialize payment response handler for subscription (if not already initialized)
            if (!paymentHandler) {
                paymentHandler = new PaymentResponseHandler({
                    translations: translations,
                    currentLang: currentLang,
                    submitButton: null,
                    totals: {}
                });
            }

            // 通过 AJAX 调用后台 createSubscription 方法
            fetch('/api/subscription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(subscriptionData)
            })
            .then(response => paymentHandler.handleResponse(response))
                .then(result => {
                    // Update processing modal with success status
                    updateProcessingStatus('success', translations[currentLang].processingSuccess);
                    
                    // Prepare order data for success page
                    const orderData = {
                        orderId: result.data.merchant_order_id,
                        paymentIntentId: result.data.id,
                        date: new Date().toISOString(),
                        status: result.data.status,
                        amount: result.data.amount
                    };

                    // Cache the response result to browser localStorage
                    localStorage.setItem('subscriptionResponseCache', JSON.stringify(result));

                    // Close modal after 1.5 seconds and process payment result
                    setTimeout(() => {
                        closeProcessingModal();
                        
                        // Get integration mode from cache
                        const integrationMode = localStorage.getItem('paymentIntegrationMode') || 'redirect';
                        console.log('Integration mode:', integrationMode);
                        
                        // Execute different handler based on integration mode
                        if (integrationMode === 'redirect') {
                            // 跳转收银台模式
                            paymentHandler.processPaymentResultForRedirect(result, orderData);
                        } else if (integrationMode === 'embedded') {
                            // 内嵌收银台模式
                            paymentHandler.processPaymentResultForEmbedded(result, orderData);
                        } else {
                            // 渲染支付方式并显示弹窗
                            renderPaymentMethodSection();
                            // 显示支付方式弹出窗口
                            showPaymentMethodsModal();

                        }
                    }, 1500);
                })
                .catch(error => {
                    // Update processing modal with error status
                    updateProcessingStatus('error', translations[currentLang].processingError);
                    
                    // Close modal after 2 seconds
                    setTimeout(() => {
                        closeProcessingModal();
                        paymentHandler.handleFetchError(error);
                    }, 2000);
                });
        }

        function getPlanName(plan) {
            if (currentLang === 'zh') {
                const names = {
                    'starter': '入门版',
                    'professional': '专业版',
                    'enterprise': '企业版'
                };
                return names[plan] || plan;
            } else {
                const names = {
                    'starter': 'Starter',
                    'professional': 'Professional',
                    'enterprise': 'Enterprise'
                };
                return names[plan] || plan;
            }
        }

        /**
         * Processing Modal Functions
         */
        function showProcessingModal() {
            const modal = document.getElementById('processingModal');
            const spinner = modal.querySelector('.processing-spinner');
            const status = modal.querySelector('#processingStatus');
            
            // Reset modal state
            spinner.style.display = 'block';
            status.textContent = '';
            status.className = 'processing-status';
            
            // Show modal
            modal.classList.add('show');
        }

        function closeProcessingModal() {
            const modal = document.getElementById('processingModal');
            modal.classList.remove('show');
        }

        function updateProcessingStatus(type, message) {
            const modal = document.getElementById('processingModal');
            const spinner = modal.querySelector('.processing-spinner');
            const status = modal.querySelector('#processingStatus');
            
            // Hide spinner
            spinner.style.display = 'none';
            
            // Update status
            status.className = `processing-status ${type}`;
            
            if (type === 'success') {
                status.innerHTML = `<span class="status-icon"><i class="fas fa-check-circle"></i></span>${message}`;
            } else if (type === 'error') {
                status.innerHTML = `<span class="status-icon"><i class="fas fa-exclamation-circle"></i></span>${message}`;
            }
        }

        /**
         * Load payment methods from cache based on action type
         */
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

        /**
         * Generate payment methods HTML - 生成支付方式 HTML
         */
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
                
                return html;
            }).join('');
        }

        /**
         * Handle payment method change
         */
        function handlePaymentMethodChange(method) {
            // 隐藏所有卡信息部分
            document.querySelectorAll('.card-info-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // 如果选择信用卡，显示对应的卡信息部分
            if (method === 'card') {
                const cardSection = document.getElementById('cardInfoSection_card');
                if (cardSection) {
                    cardSection.classList.add('active');
                }
            }
        }

        /**
         * Update card preview
         */
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

        /**
         * Render payment method section - 支付方式界面渲染
         */
        function renderPaymentMethodSection() {
            const container = document.getElementById('paymentMethodsContainer');
            if (!container) {
                console.error('Payment methods container not found');
                return;
            }
            
            const t = translations[currentLang];
            container.innerHTML = `
                <div class="form-section">
                    <h3>
                        <div class="payment-method-title">${t.paymentMethod || '支付方式'}</div>
                    </h3>
                    <div class="payment-methods" id="paymentMethodsList">
                        ${generatePaymentMethods()}
                    </div>
                </div>
            `;
        }

        /**
         * Payment Methods Modal Functions
         */
        function showPaymentMethodsModal(paymentMethods) {
            const modal = document.getElementById('paymentMethodsModal');
            // 渲染支付方式
            renderPaymentMethodSection();
            modal.classList.add('show');
        }

        function closePaymentMethodsModal() {
            const modal = document.getElementById('paymentMethodsModal');
            modal.classList.remove('show');
            setTimeout(() => { window.location.reload(); }, 500); // 延迟500毫秒后刷新
        }

        async function confirmPaymentMethod() {
            // Close payment methods modal
            const paymentMethodsModal = document.getElementById('paymentMethodsModal');
            paymentMethodsModal.classList.remove('show');
            
            // Show processing modal
            const processingModal = document.getElementById('processingModal');
            const processingTitle = document.getElementById('processingTitle');
            const processingMessage = document.getElementById('processingMessage');
            
            processingTitle.textContent = translations[currentLang].paymentProcessing;
            processingMessage.textContent = translations[currentLang].paymentProcessingMessage;
            processingModal.classList.add('show');
            
            // Initialize payment handler if not already initialized
            if (!paymentHandler) {
                paymentHandler = new PaymentResponseHandler({
                    translations: translations,
                    currentLang: currentLang,
                    submitButton: null,
                    totals: {}
                });
            }
            
            // Get integration mode from cache
            const integrationMode = localStorage.getItem('paymentIntegrationMode') || 'redirect';
            console.log('Confirm payment with integration mode:', integrationMode);
            
            try {
                let result;
                
                if (integrationMode === 'embedded') {
                    // Embedded mode: Use UseePay SDK to confirm payment
                    console.log('Using embedded mode - confirmPaymentIntent()');
                    result = await confirmPaymentIntent();
                } else if (integrationMode === 'api') {
                    // API mode: Call backend PaymentController.confirmPayment() using encapsulated method
                    console.log('Using API mode - calling confirmPaymentViaAPI()');
                    
                    // Get payment intent ID from localStorage or current subscription
                    const currentSubscription = localStorage.getItem('subscriptionResponseCache');
                    const subscriptionData = currentSubscription ? JSON.parse(currentSubscription) : null;
                    const paymentIntentId = subscriptionData.data.id;

                    // Get selected payment method from rendered UI (radio button)
                    const selectedPaymentMethodRadio = document.querySelector('input[name="paymentMethod"]:checked');
                    const selectedPaymentMethod = selectedPaymentMethodRadio ? selectedPaymentMethodRadio.value : 'card';
                    console.log('Selected payment method from UI:', selectedPaymentMethod);

                    // Prepare payment method data based on selected payment method
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
                            throw new Error(translations[currentLang].pleaseEnterCardInfo || 'Please enter complete card information');
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
                        
                        console.log('Card data collected:', {
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
                        console.log('Payment method data:', payment_method_data);
                    }

                    // Call encapsulated API method
                    result = await paymentHandler.confirmPaymentViaAPI(paymentIntentId, {
                        payment_method_data: payment_method_data
                    });
                } else {
                    // Unknown mode, default to embedded
                    console.warn('Unknown integration mode, defaulting to embedded');
                }
                
                // Handle result
                if (result.success) {
                    // Payment succeeded
                    updateProcessingStatus('success', translations[currentLang].paymentSuccess);
                    
                    // Close modal after 2 seconds and redirect or reload
                    setTimeout(() => {
                        closeProcessingModal();
                        // Optionally redirect to success page
                        //window.location.href = '/subscription/confirm?subscription_id=' + result.subscriptionId;
                        window.location.href = '/payment/callback?id=' + result.paymentIntent.id +'&merchant_order_id='
                            +result.paymentIntent.merchant_order_id+'&status=succeeded';
                    }, 500);
                } else {
                    // Payment failed
                    const errorMsg = result.error || translations[currentLang].paymentError;
                    updateProcessingStatus('error', errorMsg);
                    
                    // Close modal after 3 seconds
                    setTimeout(() => {
                        closeProcessingModal();
                        // Reopen payment methods modal to allow retry
                        paymentMethodsModal.classList.add('show');
                    }, 3000);
                }
            } catch (error) {
                console.error('Payment confirmation error:', error);
                updateProcessingStatus('error', translations[currentLang].paymentError + ': ' + error.message);
                
                // Close modal after 3 seconds
                setTimeout(() => {
                    closeProcessingModal();
                    // Reopen payment methods modal to allow retry
                    paymentMethodsModal.classList.add('show');
                }, 3000);
            }
        }

        function toggleFAQ(element) {
            const question = element;
            const answer = question.nextElementSibling;

            // Close other FAQs
            document.querySelectorAll('.faq-answer').forEach(a => {
                if (a !== answer) {
                    a.classList.remove('show');
                    a.previousElementSibling.classList.remove('active');
                }
            });

            // Toggle current FAQ
            question.classList.toggle('active');
            answer.classList.toggle('show');
        }

        // Update modal form labels and placeholders
        function updateModalLabels(lang) {
            // Update register form - with safe checks
            const emailLabel = document.querySelector('label[for="register-email"]');
            const passwordLabel = document.querySelector('label[for="register-password"]');
            const confirmPasswordLabel = document.querySelector('label[for="register-confirm-password"]');
            const emailInput = document.getElementById('register-email');
            const passwordInput = document.getElementById('register-password');
            const confirmPasswordInput = document.getElementById('register-confirm-password');
            const submitButton = document.querySelector('#register-form .submit-button');
            
            if (emailLabel) emailLabel.textContent = translations[lang].email;
            if (passwordLabel) passwordLabel.textContent = translations[lang].password;
            if (confirmPasswordLabel) confirmPasswordLabel.textContent = translations[lang].confirmPassword;
            if (emailInput) emailInput.placeholder = translations[lang].emailPlaceholder;
            if (passwordInput) passwordInput.placeholder = translations[lang].passwordPlaceholder;
            if (confirmPasswordInput) confirmPasswordInput.placeholder = translations[lang].confirmPasswordPlaceholder;
            if (submitButton) submitButton.textContent = translations[lang].registerButton;
        }

        // Auth Modal Functions
        function openAuthModal() {
            // 从浏览器内存中获取 customer 对象
            const customerData = localStorage.getItem('customer');
            const customer = customerData ? JSON.parse(customerData) : null;
            
            console.log('openAuthModal - Retrieved customer from localStorage:', customer);
            
            // 将 customer 对象赋值给全局变量，方便在模态框中使用
            window.currentCustomer = customer;
            
            const authModal = document.getElementById('authModal');
            authModal.classList.add('show');
            
            // 如果有 customer 对象，将邮箱地址赋值给注册页面的邮箱输入框，并隐藏注册按钮
            if (customer && customer.email) {
                const emailInput = document.getElementById('register-email');
                if (emailInput) {
                    emailInput.value = customer.email;
                    emailInput.disabled = true; // 禁用邮箱输入框
                    console.log('Populated email field with customer email:', customer.email);
                }
                
                // 隐藏注册按钮
                const registerButton = document.querySelector('#register-form .submit-button');
                if (registerButton) {
                    registerButton.style.display = 'none';
                    console.log('Hidden register button');
                }
            } else {
                console.log('No customer found or customer has no email');
                
                // 显示注册按钮
                const registerButton = document.querySelector('#register-form .submit-button');
                if (registerButton) {
                    registerButton.style.display = 'block';
                }
                
                // 启用邮箱输入框
                const emailInput = document.getElementById('register-email');
                if (emailInput) {
                    emailInput.disabled = false;
                }
            }
        }

        function closeAuthModal() {
            const authModal = document.getElementById('authModal');
            authModal.classList.remove('show');
        }

        function handleRegister(event) {
            event.preventDefault();
            
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;
            
            if (!email || !password || !confirmPassword) {
                alert(currentLang === 'zh' ? '请填写所有字段' : 'Please fill in all fields');
                return;
            }
            
            if (password !== confirmPassword) {
                alert(currentLang === 'zh' ? '两次输入的密码不一致' : 'Passwords do not match');
                return;
            }
            
            // 通过 Ajax 调用 CustomerController 的 createCustomer 接口
            // 生成一个默认的 merchantCustomerId (使用时间戳 + 随机数)
            const merchantCustomerId = 'CUST_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9).toUpperCase();
            
            const customerData = {
                email: email,
                name: email.split('@')[0], // 使用邮箱前缀作为名字
                merchantCustomerId: merchantCustomerId
            };
            
            console.log('Creating customer with data:', customerData);
            
            fetch('/api/customers/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(customerData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Error response text:', text);
                        throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Customer created successfully:', data);
                
                // 提取 customer 对象（可能在 data.data 或直接在 data 中）
                const customerObject = data.data || data;
                
                console.log('Storing customer object:', customerObject);

                // 将整个 customer 对象存储到浏览器内存中
                localStorage.setItem('customer', JSON.stringify(customerObject));

                const successMsg = currentLang === 'zh' ? '注册成功！' : 'Registration successful!';
                alert(successMsg);
                
                // Update button text after registration
                const authButtonText = document.getElementById('authButtonText');
                if (authButtonText) {
                    authButtonText.textContent = translations[currentLang].personalCenter;
                }
                closeAuthModal();
            })
            .catch(error => {
                console.error('Error creating customer:', error);
                const errorMsg = currentLang === 'zh' 
                    ? `注册失败: ${error.message}` 
                    : `Registration failed: ${error.message}`;
                alert(errorMsg);
            });
        }

        // 页面加载时检查用户状态并更新按钮文本
        window.addEventListener('DOMContentLoaded', function() {
            // 从缓存中获取 customer 对象
            const customerData = localStorage.getItem('customer');
            const customer = customerData ? JSON.parse(customerData) : null;
            
            const authButtonText = document.getElementById('authButtonText');
            if (customer) {
                // 如果有 customer 对象，显示个人中心，否则显示注册
                authButtonText.textContent = customer ? translations[currentLang].personalCenter : translations[currentLang].register;
                
                console.log('DOMContentLoaded - Customer status:', {
                    hasCustomer: !!customer,
                    customer: customer,
                    buttonText: authButtonText.textContent
                });
            }
        });

        // 点击模态框外部关闭
        window.addEventListener('click', function(event) {
            const authModal = document.getElementById('authModal');
            if (event.target === authModal) {
                closeAuthModal();
            }
        });
    </script>
    <!-- UseePay Public Key Configuration -->
    <script>
        <?php
            global $config;
            $publicKey = $config['usee_pay']['api_public_key'];
        ?>
        window.USEEPAY_PUBLIC_KEY = '<?php echo $publicKey; ?>';
        console.log('UseePay Public Key configured:', window.USEEPAY_PUBLIC_KEY ? '✓' : '✗');
    </script>


    <!-- Auth Modal -->
    <div id="authModal" class="auth-modal">
        <div class="modal-content">
            <!-- Register Form -->
            <form id="register-form" class="form-content active" onsubmit="handleRegister(event)">
                <div class="modal-header">
                    <h2 data-i18n="register">注册</h2>
                    <button type="button" class="modal-close" onclick="closeAuthModal()">×</button>
                </div>
                <div class="form-group">
                    <label for="register-email" data-i18n="email">邮箱地址</label>
                    <input type="email" id="register-email" placeholder="请输入邮箱地址" required>
                </div>
                <div class="form-group">
                    <label for="register-password" data-i18n="password">密码</label>
                    <input type="password" id="register-password" placeholder="请输入密码" required>
                </div>
                <div class="form-group">
                    <label for="register-confirm-password" data-i18n="confirmPassword">确认密码</label>
                    <input type="password" id="register-confirm-password" placeholder="请再次输入密码" required>
                </div>
                <button type="submit" class="submit-button" data-i18n="registerButton">注册</button>
            </form>
        </div>
    </div>
</body>
</html>
