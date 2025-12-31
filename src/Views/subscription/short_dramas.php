<?php
/**
 * Short Dramas Subscription Page
 * Allows users to select and subscribe to multiple short dramas
 * Each short drama costs $0.99/month
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>短剧订阅 - UseePay Demo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/short-dramas.css?v=<?php echo @filemtime(__DIR__ . '/../../public/assets/css/short-dramas.css') ?: time(); ?>">
    <!-- Short Dramas Page Internationalization -->
    <script src="/assets/js/i18n/subscription/short-dramas-i18n.js?v=<?php echo @filemtime(__DIR__ . '/../../public/assets/js/i18n/subscription/short-dramas-i18n.js') ?: time(); ?>"></script>
    <!-- UseePay SDK -->
    <script src="https://checkout-sdk1.uat.useepay.com/2.0.0/useepay.min.js"></script>
    <!-- UseePay Public Key Configuration -->
    <script>
        <?php
            global $config;
            $publicKey = $config['usee_pay']['api_public_key'];
        ?>
        window.USEEPAY_PUBLIC_KEY = '<?php echo $publicKey; ?>';
        console.log('UseePay Public Key configured:', window.USEEPAY_PUBLIC_KEY ? '✓' : '✗');
    </script>
    <!-- Payment Methods Configuration -->
    <script src="/assets/js/payment/payment-methods-config.js?v=<?php echo @filemtime(__DIR__ . '/../../public/assets/js/payment/payment-methods-config.js') ?: time(); ?>"></script>
    <!-- Payment Handler -->
    <script src="/assets/js/payment-handler.js?v=<?php echo @filemtime(__DIR__ . '/../../public/assets/js/payment-handler.js') ?: time(); ?>"></script>
</head>
<body>
    <div class="container">
        <div class="dramas-container">
            <div class="dramas-grid" id="dramasGrid">
                <!-- Drama cards will be generated here -->
            </div>

            <div class="subscription-summary">
                <div class="summary-header">
                    <h3 data-i18n="subscriptionSummary">订阅摘要</h3>
                </div>
                <div class="selected-dramas" id="selectedDramasList">
                    <p data-i18n="noDramasSelected">未选择任何短剧</p>
                </div>
                <div class="summary-total">
                    <div class="total-row">
                        <span data-i18n="totalPrice">总价格</span>
                        <span id="totalPrice">$0.00</span>
                    </div>
                    <div class="total-row">
                        <span data-i18n="billingCycle">计费周期</span>
                        <span id="billingCycle">每月</span>
                    </div>
                </div>
                
                <!-- Embedded Payment Element (shown when integration mode is embedded and amount > 0) -->
                <div id="embeddedPaymentContainer" style="display: none; margin: 20px 0;">
                    <h3 data-i18n="selectPaymentMethod" style="margin-bottom: 15px;">选择支付方式</h3>
                    <div id="payment-element" style="margin: 20px 0;"></div>
                </div>
                
                <!-- Subscribe Button (shown when not in embedded mode or amount is 0) -->
                <button class="subscribe-button" id="subscribeButton" onclick="handleSubscribe()" data-i18n="subscribeNow">立即订阅</button>
            </div>
        </div>

        <!-- FAQ Section -->
        <section class="faq-section">
            <h2 data-i18n="frequentlyAsked">常见问题</h2>
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)" data-i18n="faqQuestion1">如何取消订阅？</div>
                    <div class="faq-answer" data-i18n="faqAnswer1">您可以随时在个人中心取消订阅，取消后将不再扣费。</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)" data-i18n="faqQuestion2">支持哪些支付方式？</div>
                    <div class="faq-answer" data-i18n="faqAnswer2">我们支持信用卡、Apple Pay、Google Pay 等多种支付方式。</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)" data-i18n="faqQuestion3">订阅后可以立即观看吗？</div>
                    <div class="faq-answer" data-i18n="faqAnswer3">是的，订阅成功后可以立即观看所有已订阅的短剧。</div>
                </div>
            </div>
        </section>
    </div>

    <!-- Processing Modal -->
    <div id="processingModal" class="modal">
        <div class="modal-content processing-modal">
            <div class="processing-spinner">
                <div class="spinner"></div>
                <p id="processingTitle" data-i18n="processing">处理中...</p>
                <p id="processingMessage" data-i18n="pleaseWait">请稍候</p>
            </div>
            <div id="processingStatus" class="processing-status"></div>
        </div>
    </div>
    
    <!-- Auth Modal -->
    <div id="authModal" class="auth-modal">
        <div class="modal-content">
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

    <script>
        // Global variables
        let currentLang = localStorage.getItem('language') || 'zh';
        // short-dramas-i18n.js exports as window.translations
        let dramaTranslations = window.translations || {};
        let selectedDramas = new Set();
        const DRAMA_PRICE = 0.99;
        
        // Initialize PaymentHandler globally
        let paymentHandler = new PaymentHandler({
            translations: dramaTranslations,
            currentLang: currentLang,
            submitButton: null,
            totals: {}
        });
        
        console.log('Short Dramas Page - Current language:', currentLang);
        console.log('Short Dramas Page - Translations loaded:', !!dramaTranslations.zh, !!dramaTranslations.en);
        console.log('PaymentHandler initialized:', !!paymentHandler);

        // Sample short dramas data
        const shortDramas = [
            {
                id: 'drama_001',
                name_zh: '爱情公寓',
                name_en: 'Love Apartment',
                desc_zh: '年轻人的爱情故事',
                desc_en: 'Love story of young people',
                cover: '/assets/images/dramas/drama_001.jpg',
                episodes: 12
            },
            {
                id: 'drama_002',
                name_zh: '甜蜜蜜',
                name_en: 'Sweet Love',
                desc_zh: '甜蜜的爱情冒险',
                desc_en: 'Sweet love adventure',
                cover: '/assets/images/dramas/drama_002.jpg',
                episodes: 15
            },
            {
                id: 'drama_003',
                name_zh: '校园青春',
                name_en: 'Campus Youth',
                desc_zh: '青春校园故事',
                desc_en: 'Youth campus stories',
                cover: '/assets/images/dramas/drama_003.jpg',
                episodes: 20
            },
            {
                id: 'drama_004',
                name_zh: '悬疑密室',
                name_en: 'Mystery Room',
                desc_zh: '烧脑悬疑剧',
                desc_en: 'Brain-burning mystery drama',
                cover: '/assets/images/dramas/drama_004.jpg',
                episodes: 10
            },
            {
                id: 'drama_005',
                name_zh: '古装传奇',
                name_en: 'Ancient Legend',
                desc_zh: '古装传奇故事',
                desc_en: 'Ancient costume legend',
                cover: '/assets/images/dramas/drama_005.jpg',
                episodes: 25
            },
            {
                id: 'drama_006',
                name_zh: '科幻未来',
                name_en: 'Sci-Fi Future',
                desc_zh: '科幻冒险故事',
                desc_en: 'Sci-fi adventure story',
                cover: '/assets/images/dramas/drama_006.jpg',
                episodes: 18
            }
        ];

        // Initialize page
        function initializePage() {
            console.log('=== Short Dramas Page Initializing ===');
            loadTranslations();
            
            // Apply translations to all static elements
            document.querySelectorAll('[data-i18n]').forEach(element => {
                const key = element.getAttribute('data-i18n');
                if (dramaTranslations[currentLang] && dramaTranslations[currentLang][key]) {
                    element.textContent = dramaTranslations[currentLang][key];
                }
            });
            
            // Initialize billing cycle text
            const billingCycleElement = document.getElementById('billingCycle');
            if (billingCycleElement) {
                const perMonthText = dramaTranslations[currentLang]?.perMonth || '每月';
                billingCycleElement.textContent = perMonthText;
            }
            
            renderDramas();
            updateAuthButton();
            
            // Notify parent to resize after initialization
            setTimeout(() => {
                notifyParentResize();
            }, 500);
        }
        
        // Helper function to notify parent window to resize iframe
        function notifyParentResize() {
            if (window.parent && window.parent !== window) {
                try {
                    const iframe = window.parent.document.getElementById('dramas-iframe');
                    if (iframe && window.parent.resizeIframe) {
                        window.parent.resizeIframe(iframe);
                        console.log('✓ Notified parent to resize iframe');
                    }
                } catch (e) {
                    console.log('Cannot access parent window:', e.message);
                }
            }
        }

        // Try multiple initialization methods
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializePage);
        } else {
            // DOM already loaded
            initializePage();
        }

        // Also try on window load as fallback
        window.addEventListener('load', function() {
            const grid = document.getElementById('dramasGrid');
            if (!grid || grid.children.length === 0) {
                console.log('Retrying drama rendering on window load...');
                renderDramas();
            }
        });

        function loadTranslations() {
            if (typeof dramaTranslations === 'object' && Object.keys(dramaTranslations).length > 0) {
                console.log('✓ Translations already loaded');
                return;
            }
            
            // Use translations from i18n script
            if (window.shortDramasTranslations) {
                dramaTranslations = window.shortDramasTranslations;
                console.log('✓ Loaded short dramas translations');
            } else {
                console.warn('⚠ Translations not available');
            }
        }

        function renderDramas() {
            console.log('renderDramas called');
            const grid = document.getElementById('dramasGrid');
            
            if (!grid) {
                console.error('✗ dramasGrid element not found!');
                return;
            }
            
            console.log('✓ dramasGrid found, rendering', shortDramas.length, 'dramas');
            
            const episodesText = dramaTranslations[currentLang]?.episodes || '集';
            const perMonthText = dramaTranslations[currentLang]?.perMonth || '/月';
            const selectText = dramaTranslations[currentLang]?.select || '选择';
            
            grid.innerHTML = shortDramas.map(drama => `
                <div class="drama-card" data-drama-id="${drama.id}">
                    <div class="drama-cover">
                        <img src="${drama.cover}" alt="${currentLang === 'zh' ? drama.name_zh : drama.name_en}" loading="lazy">
                    </div>
                    <div class="drama-info">
                        <h3 class="drama-name">${currentLang === 'zh' ? drama.name_zh : drama.name_en}</h3>
                        <p class="drama-desc">${currentLang === 'zh' ? drama.desc_zh : drama.desc_en}</p>
                        <p class="drama-episodes">
                            <i class="fas fa-film"></i>
                            <span>${drama.episodes}</span>
                            <span>${episodesText}</span>
                        </p>
                    </div>
                    <div class="drama-price">
                        <span class="price-amount">$${DRAMA_PRICE.toFixed(2)}</span>
                        <span class="price-period">${perMonthText}</span>
                    </div>
                    <button class="drama-select-btn" onclick="toggleDramaSelection('${drama.id}', this)">
                        <i class="fas fa-plus"></i>
                        <span>${selectText}</span>
                    </button>
                </div>
            `).join('');
            
            console.log('✓ Dramas rendered successfully. Grid HTML length:', grid.innerHTML.length);
            console.log('Grid children count:', grid.children.length);
            
            // Notify parent window to resize iframe after content is rendered
            setTimeout(() => {
                notifyParentResize();
            }, 100);
        }

        function toggleDramaSelection(dramaId, button) {
            if (selectedDramas.has(dramaId)) {
                selectedDramas.delete(dramaId);
                button.classList.remove('selected');
            } else {
                selectedDramas.add(dramaId);
                button.classList.add('selected');
            }
            updateSubscriptionSummary();
        }

        function updateSubscriptionSummary() {
            const listContainer = document.getElementById('selectedDramasList');
            const totalPriceElement = document.getElementById('totalPrice');
            const subscribeButton = document.getElementById('subscribeButton');
            const billingCycleElement = document.getElementById('billingCycle');

            const noDramasText = dramaTranslations[currentLang]?.noDramasSelected || '未选择任何短剧';
            const perMonthText = dramaTranslations[currentLang]?.perMonth || '每月';

            if (selectedDramas.size === 0) {
                listContainer.innerHTML = `<p>${noDramasText}</p>`;
                totalPriceElement.textContent = '$0.00';
            } else {
                const selectedDramasList = shortDramas.filter(d => selectedDramas.has(d.id));
                listContainer.innerHTML = selectedDramasList.map(drama => `
                    <div class="selected-drama-item">
                        <span>${currentLang === 'zh' ? drama.name_zh : drama.name_en}</span>
                        <span class="drama-item-price">$${DRAMA_PRICE.toFixed(2)}</span>
                    </div>
                `).join('');
                
                // Fix floating point precision issue
                const totalPrice = parseFloat((selectedDramas.size * DRAMA_PRICE).toFixed(2));
                totalPriceElement.textContent = '$' + totalPrice.toFixed(2);
            }
            
            // Update billing cycle text
            if (billingCycleElement) {
                billingCycleElement.textContent = perMonthText;
            }
            
            // Handle payment element based on integration mode
            const integrationMode = localStorage.getItem('paymentIntegrationMode') || 'redirect';
            const embeddedPaymentContainer = document.getElementById('embeddedPaymentContainer');
            
            if (selectedDramas.size > 0) {
                if (integrationMode === 'embedded') {
                    // Embedded mode: show container and initialize UseePay elements
                    if (embeddedPaymentContainer) embeddedPaymentContainer.style.display = 'block';
                    
                    setTimeout(() => {
                        initializePaymentElement('payment-element');
                    }, 100);
                } else if (integrationMode === 'api') {
                    // API mode: render payment methods in container
                    console.log('API mode detected, rendering payment methods');
                    renderPaymentMethodInContainer();
                } else {
                    // Redirect mode: hide container
                    if (embeddedPaymentContainer) embeddedPaymentContainer.style.display = 'none';
                }
            } else {
                // No dramas selected: hide container
                if (embeddedPaymentContainer) embeddedPaymentContainer.style.display = 'none';
                
                // Hide confirm payment button if exists
                const confirmButton = document.getElementById('confirmPaymentButton');
                if (confirmButton) confirmButton.style.display = 'none';
                
                // Show subscribe button
                const subscribeButton = document.getElementById('subscribeButton');
                if (subscribeButton) subscribeButton.style.display = 'block';
            }
        }

        async function handleSubscribe() {
            const customerData = localStorage.getItem('customer');
            const customer = customerData ? JSON.parse(customerData) : null;
            
            if (!customer) {
                console.log('No customer found, showing registration modal');
                openAuthModal();
                return;
            }

            if (selectedDramas.size === 0) {
                alert(currentLang === 'zh' ? '请选择至少一部短剧' : 'Please select at least one drama');
                return;
            }

            // Step 1: Assemble subscriptionData object
            const selectedDramasList = shortDramas.filter(d => selectedDramas.has(d.id));
            const totalPrice = parseFloat((selectedDramas.size * DRAMA_PRICE).toFixed(2));
            const currency = 'USD';

            const subscriptionData = {
                customer_id: customer.id,
                recurring: {
                    interval: 'month',
                    interval_count: 1,
                    unit_amount: totalPrice,
                    totalBillingCycles: 10
                },
                currency: currency,
                description: currentLang === 'zh' ? '短剧订阅' : 'Short Dramas Subscription',
                paymentMethods: getPaymentMethods(),
                order: {
                    products: selectedDramasList.map(drama => ({
                        name: currentLang === 'zh' ? drama.name_zh : drama.name_en,
                        quantity: 1,
                        price: DRAMA_PRICE
                    }))
                }
            };

            console.log('=== Step 1: Subscription data assembled ===');
            console.log('Subscription data:', subscriptionData);

            showProcessingModal();

            try {
                // Step 2: Determine integration mode
                const integrationMode = localStorage.getItem('paymentIntegrationMode') || 'redirect';
                console.log('=== Step 2: Integration mode ===', integrationMode);

                if (integrationMode === 'redirect') {
                    // Redirect mode: createSubscription -> processPaymentResultForRedirect
                    console.log('=== Processing REDIRECT mode ===');
                    showProcessingModal();

                    await paymentHandler.processSubscriptionForRedirect(
                        subscriptionData,
                        updateProcessingStatus
                    );

                    setTimeout(() => {
                        closeProcessingModal();
                    }, 1500);
                    
                } else if (integrationMode === 'embedded') {
                    // Embedded mode: submit form -> createSubscription -> confirmPayment -> handle result
                    await paymentHandler.processSubscriptionEmbeddedCheckout(
                        subscriptionData,
                        updateProcessingStatus
                    );
                    
                } else if (integrationMode === 'api') {
                    // API mode: createSubscription -> confirmPaymentMethod
                    console.log('=== Processing API mode ===');

                    // Process subscription with API mode
                    await paymentHandler.processSubscriptionForApi(
                        subscriptionData,
                        updateProcessingStatus
                    );
                    
                } else {
                    throw new Error('Unknown integration mode: ' + integrationMode);
                }
                
            } catch (error) {
                console.error('handleSubscribe error:', error);
                updateProcessingStatus('error', dramaTranslations[currentLang].processingError);
                
                setTimeout(() => {
                    closeProcessingModal();
                    paymentHandler.handleFetchError(error);
                }, 2000);
            }
        }


        /**
         * Load payment methods from cache based on action type
         */
        function getPaymentMethods() {
            const actionType = localStorage.getItem('paymentActionType');
            let cacheKey = 'paymentMethods';
            if (actionType === 'subscription') {
                cacheKey = 'subscriptionMethods';
            } else if (actionType === 'installment') {
                cacheKey = 'installmentMethods';
            }

            const cached = localStorage.getItem(cacheKey);
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
                        <input type="radio" id="method_${method}" name="paymentMethod" value="${method}" ${isFirst ? 'checked' : ''} onchange="handlePaymentMethodChange('${method}')" style="display: none;">
                        <label for="method_${method}" style="display: flex; align-items: center; padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px; cursor: pointer; margin-bottom: 10px;">
                            <div class="payment-icon" style="font-size: 1.5rem; margin-right: 15px;">${methodInfo.icon}</div>
                            <div class="payment-info">
                                <div class="payment-name" style="font-weight: 600; font-size: 14px;">${methodName}</div>
                                <div class="payment-desc" style="font-size: 12px; color: #666;">${methodDesc}</div>
                            </div>
                        </label>
                    </div>
                `;
                
                // 如果是信用卡，添加卡信息表单
                if (method === 'card') {
                    const t = dramaTranslations[currentLang];
                    html += `
                    <div class="card-info-section ${isFirst ? 'active' : ''}" id="cardInfoSection_${method}" style="display: ${isFirst ? 'block' : 'none'}; margin-top: 15px; padding: 15px; background: #f9f9f9; border-radius: 8px;">
                        <div class="card-row" style="margin-bottom: 15px;">
                            <div class="form-group full-width">
                                <label><span data-i18n="cardNumber">${t.cardNumber}</span> <span class="required" data-i18n="required">*</span></label>
                                <input type="text" id="cardNumber" placeholder="${t.cardNumberPlaceholder}" maxlength="19" value="4111 1111 1111 1111" oninput="updateCardPreview()">
                            </div>
                        </div>

                        <div class="card-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
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
            console.log('Payment method changed to:', method);
            
            // Hide all card info sections
            document.querySelectorAll('.card-info-section').forEach(section => {
                section.classList.remove('active');
                section.style.display = 'none';
            });
            
            // Show card info section only if card is selected
            if (method === 'card') {
                const cardSection = document.getElementById('cardInfoSection_card');
                if (cardSection) {
                    cardSection.classList.add('active');
                    cardSection.style.display = 'block';
                    console.log('✓ Card info section shown');
                } else {
                    console.warn('Card info section not found');
                }
            } else {
                console.log('✓ Card info section hidden (non-card method selected)');
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
         * Render payment method in embeddedPaymentContainer - 在容器中渲染支付方式
         * @param {boolean} isAfterSubscription - 是否在订阅创建后调用（影响按钮文本）
         */
        function renderPaymentMethodInContainer(isAfterSubscription = false) {
            console.log('=== renderPaymentMethodInContainer called ===');
            console.log('isAfterSubscription:', isAfterSubscription);
            
            const container = document.getElementById('embeddedPaymentContainer');
            if (!container) {
                console.error('embeddedPaymentContainer not found');
                return;
            }
            console.log('✓ embeddedPaymentContainer found');

            const t = dramaTranslations[currentLang];
            const paymentElement = document.getElementById('payment-element');
            
            if (!paymentElement) {
                console.error('payment-element not found');
                return;
            }
            console.log('✓ payment-element found');
            
            // 生成支付方式 HTML
            const paymentMethodsHTML = generatePaymentMethods();
            console.log('Generated payment methods HTML length:', paymentMethodsHTML.length);
            
            // 渲染支付方式到 payment-element
            paymentElement.innerHTML = `
                <div class="form-section">
                    <div class="payment-methods" id="paymentMethodsList">
                        ${paymentMethodsHTML}
                    </div>
                </div>
            `;
            console.log('✓ Payment methods rendered to payment-element');
            
            // 显示容器
            container.style.display = 'block';
            console.log('✓ embeddedPaymentContainer displayed');
            
            const subscribeButton = document.getElementById('subscribeButton');
            let confirmButton = document.getElementById('confirmPaymentButton');
            
            if (isAfterSubscription) {
                // 订阅后：隐藏订阅按钮，显示确认支付按钮
                if (subscribeButton) {
                    subscribeButton.style.display = 'none';
                    console.log('✓ Subscribe button hidden');
                }
                
                // 添加确认支付按钮（如果不存在）
                if (!confirmButton) {
                    confirmButton = document.createElement('button');
                    confirmButton.id = 'confirmPaymentButton';
                    confirmButton.className = 'subscribe-button';
                    confirmButton.onclick = confirmPaymentMethod;
                    confirmButton.setAttribute('data-i18n', 'confirm');
                    confirmButton.textContent = t.confirm || '确认支付';
                    container.parentElement.appendChild(confirmButton);
                    console.log('✓ Confirm payment button created');
                } else {
                    confirmButton.style.display = 'block';
                    confirmButton.textContent = t.confirm || '确认支付';
                    console.log('✓ Confirm payment button shown');
                }
            } else {
                // 页面加载时：保持订阅按钮，隐藏确认支付按钮
                if (subscribeButton) {
                    subscribeButton.style.display = 'block';
                    console.log('✓ Subscribe button shown');
                }
                
                if (confirmButton) {
                    confirmButton.style.display = 'none';
                    console.log('✓ Confirm payment button hidden');
                }
            }
            
            console.log('=== renderPaymentMethodInContainer completed ===');
        }

        async function confirmPaymentMethod() {
            // Show processing modal
            showProcessingModal();
            const processingTitle = document.getElementById('processingTitle');
            const processingMessage = document.getElementById('processingMessage');
            
            processingTitle.textContent = dramaTranslations[currentLang].paymentProcessing;
            processingMessage.textContent = dramaTranslations[currentLang].paymentProcessingMessage;
            
            const integrationMode = localStorage.getItem('paymentIntegrationMode') || 'redirect';
            console.log('Confirm payment with integration mode:', integrationMode);
            
            try {
                let result;
                
                if (integrationMode === 'embedded') {
                    console.log('Using embedded mode - confirmPaymentIntent()');
                    result = await confirmPaymentIntent();
                } else if (integrationMode === 'api') {
                    console.log('Using API mode - calling confirmPaymentForApi()');
                    
                    const currentSubscription = localStorage.getItem('subscriptionResponseCache');
                    const subscriptionData = currentSubscription ? JSON.parse(currentSubscription) : null;
                    const paymentIntentId = subscriptionData.data.id;

                    const selectedPaymentMethodRadio = document.querySelector('input[name="paymentMethod"]:checked');
                    const selectedPaymentMethod = selectedPaymentMethodRadio ? selectedPaymentMethodRadio.value : 'card';
                    console.log('Selected payment method from UI:', selectedPaymentMethod);

                    let payment_method_data = null;
                    
                    if (selectedPaymentMethod === 'card') {
                        const cardNumber = document.getElementById('cardNumber')?.value?.replace(/\s/g, '');
                        const expiryDate = document.getElementById('expiryDate')?.value;
                        const cvv = document.getElementById('cvv')?.value;
                        const cardHolder = document.getElementById('cardHolder')?.value;
                        
                        const [expMonth, expYear] = expiryDate ? expiryDate.split('/') : ['', ''];
                        
                        if (!cardNumber || !expiryDate || !cvv) {
                            throw new Error(dramaTranslations[currentLang].pleaseEnterCardInfo || 'Please enter complete card information');
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
                        payment_method_data = {
                            type: selectedPaymentMethod
                        };
                        console.log('Payment method data:', payment_method_data);
                    }

                    result = await paymentHandler.confirmPaymentForApi(paymentIntentId, {
                        payment_method_data: payment_method_data
                    });
                } else {
                    console.warn('Unknown integration mode, defaulting to embedded');
                }
                
                if (result.success) {
                    updateProcessingStatus('success', dramaTranslations[currentLang].paymentSuccess);
                    
                    setTimeout(() => {
                        closeProcessingModal();
                        const returnUrl = '/payment/callback?id=' + result.paymentIntent.id +'&merchant_order_id='
                            +result.paymentIntent.merchant_order_id+'&status=succeeded';
                        
                        if (window.self !== window.top) {
                            console.log('Detected iframe context, redirecting parent window');
                            window.top.location.href = returnUrl;
                        } else {
                            window.location.href = returnUrl;
                        }
                    }, 500);
                } else {
                    const errorMsg = result.error || dramaTranslations[currentLang].paymentError;
                    updateProcessingStatus('error', errorMsg);
                    
                    setTimeout(() => {
                        closeProcessingModal();
                    }, 3000);
                }
            } catch (error) {
                console.error('Payment confirmation error:', error);
                updateProcessingStatus('error', dramaTranslations[currentLang].paymentError + ': ' + error.message);
                
                setTimeout(() => {
                    closeProcessingModal();
                }, 3000);
            }
        }

        function showProcessingModal() {
            const modal = document.getElementById('processingModal');
            const spinner = modal.querySelector('.processing-spinner');
            const status = modal.querySelector('#processingStatus');

            spinner.style.display = 'block';
            status.textContent = '';
            status.className = 'processing-status';

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

            spinner.style.display = 'none';

            status.className = `processing-status ${type}`;

            if (type === 'success') {
                status.innerHTML = `<span class="status-icon"><i class="fas fa-check-circle"></i></span>${message}`;
            } else if (type === 'error') {
                status.innerHTML = `<span class="status-icon"><i class="fas fa-exclamation-circle"></i></span>${message}`;
            }
        }

        function toggleFAQ(element) {
            const question = element;
            const answer = question.nextElementSibling;

            document.querySelectorAll('.faq-answer').forEach(a => {
                if (a !== answer) {
                    a.classList.remove('show');
                    a.previousElementSibling.classList.remove('active');
                }
            });

            question.classList.toggle('active');
            answer.classList.toggle('show');
        }

        function setLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('language', lang);
            
            // Update all static elements with data-i18n
            document.querySelectorAll('[data-i18n]').forEach(element => {
                const key = element.getAttribute('data-i18n');
                if (dramaTranslations[lang] && dramaTranslations[lang][key]) {
                    element.textContent = dramaTranslations[lang][key];
                }
            });
            
            // Re-render dynamic content
            renderDramas();
            updateSubscriptionSummary();
            updateAuthButton();
        }

        function updateAuthButton() {
            const customerData = localStorage.getItem('customer');
            const customer = customerData ? JSON.parse(customerData) : null;

            const authButtonText = document.getElementById('authButtonText');
            if (authButtonText) {
                if (customer) {
                    authButtonText.textContent = dramaTranslations[currentLang]?.personalCenter || '个人中心';
                } else {
                    authButtonText.textContent = dramaTranslations[currentLang]?.register || '注册';
                }
            }
        }

        function openAuthModal() {
            const customerData = localStorage.getItem('customer');
            const customer = customerData ? JSON.parse(customerData) : null;

            window.currentCustomer = customer;

            const authModal = document.getElementById('authModal');
            authModal.classList.add('show');

            if (customer && customer.email) {
                const emailInput = document.getElementById('register-email');
                if (emailInput) {
                    emailInput.value = customer.email;
                    emailInput.disabled = true;
                }

                const registerButton = document.querySelector('#register-form .submit-button');
                if (registerButton) {
                    registerButton.style.display = 'none';
                }
            } else {
                const registerButton = document.querySelector('#register-form .submit-button');
                if (registerButton) {
                    registerButton.style.display = 'block';
                }

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

            const merchantCustomerId = 'CUST_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9).toUpperCase();

            const customerData = {
                email: email,
                name: email.split('@')[0],
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
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Customer created successfully:', data);

                const customerObject = data.data || data;
                localStorage.setItem('customer', JSON.stringify(customerObject));

                const successMsg = currentLang === 'zh' ? '注册成功！' : 'Registration successful!';
                alert(successMsg);

                const authButtonText = document.getElementById('authButtonText');
                if (authButtonText) {
                    authButtonText.textContent = dramaTranslations[currentLang].personalCenter;
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

        /**
         * Initialize or update UseePay payment element with amount and currency
         * Reference: embedded_checkout.php updatePaymentElementOnCartChange()
         * @param {string} elementId 支付元素容器ID (默认: 'payment-element')
         */
        function initializePaymentElement(elementId = 'payment-element') {
            console.log('=== Initializing/Updating Payment Element for Short Dramas ===');
            console.log('Element ID:', elementId);
            
            // Check integration mode
            const integrationMode = localStorage.getItem('paymentIntegrationMode') || 'redirect';
            if (integrationMode !== 'embedded') {
                console.log('Not in embedded mode, skipping payment element initialization');
                return false;
            }
            
            // Calculate total amount (fix floating point precision)
            const totalPrice = parseFloat((selectedDramas.size * DRAMA_PRICE).toFixed(2));
            console.log('Selected dramas count:', selectedDramas.size);
            console.log('Total price (USD):', totalPrice);
            
            // Check if amount > 0
            if (totalPrice <= 0) {
                console.log('Amount is 0, skipping payment element initialization');
                return false;
            }

            const currency = 'USD';
            
            console.log('Payment element amount:');
            console.log('  Currency:', currency);
            
            // Prepare Apple Pay recurring payment request
            const applePayOptions = {
                applePay: {
                    recurringPaymentRequest: {
                        paymentDescription: currentLang === 'zh' ? '短剧订阅' : 'Short Dramas Subscription',
                        managementURL: window.location.origin + '/subscription/manage',
                        regularBilling: {
                            amount: totalPrice,
                            label: currentLang === 'zh' ? '短剧订阅' : 'Short Dramas Subscription',
                            recurringPaymentStartDate: new Date(),
                            recurringPaymentEndDate: new Date(new Date().setMonth(new Date().getMonth() + 10)), // 10 months from now
                            recurringPaymentIntervalUnit: 'month',
                            recurringPaymentIntervalCount: 1
                        },
                        billingAgreement: currentLang === 'zh'
                            ? '订阅后将每月自动扣费，您可以随时取消订阅'
                            : 'You will be charged monthly after subscription. You can cancel anytime.'
                    }
                }
            };
            
            let success = false;
            
            // Check if element is already bound
            const isAlreadyBound = paymentHandler.isElementAlreadyBound(elementId);
            
            if (isAlreadyBound) {
                // Element already bound, just update the amount
                console.log('Payment element already bound, updating amount...');
                success = paymentHandler.updatePaymentElement(totalPrice, currency, applePayOptions);
                
                if (success) {
                    console.log('✓ Payment element amount updated successfully');
                } else {
                    console.error('Failed to update payment element amount');
                }
            } else {
                // Element not bound, initialize it
                console.log('Payment element not bound, initializing...');

                success = paymentHandler.initializeElementsForSubscription(totalPrice, currency, applePayOptions, elementId);
                
                if (success) {
                    console.log('✓ Payment element initialized successfully');
                } else {
                    console.error('Failed to initialize payment element');
                }
            }
            
            return success;
        }

        window.addEventListener('click', function(event) {
            const authModal = document.getElementById('authModal');
            if (event.target === authModal) {
                closeAuthModal();
            }
        });
    </script>


</body>
</html>
