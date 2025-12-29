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
    <link rel="stylesheet" href="/assets/css/short-dramas.css">
    <!-- Short Dramas Page Internationalization -->
    <script src="/assets/js/i18n/subscription/short-dramas-i18n.js"></script>
    <!-- UseePay SDK -->
    <script src="https://checkout-sdk1.uat.useepay.com/2.0.0/useepay.min.js"></script>
    <!-- Payment Methods Configuration -->
    <script src="/assets/js/payment/payment-methods-config.js"></script>
    <!-- UseePay Elements Initializer -->
    <script src="/assets/js/useepay-elements-initializer.js"></script>
    <!-- Payment Response Handler -->
    <script src="/assets/js/payment-response-handler.js"></script>
</head>
<body>
    <div class="container">
<!--        <a href="/" class="back-button">-->
<!--            <i class="fas fa-arrow-left"></i>-->
<!--            <span data-i18n="backHome">返回首页</span>-->
<!--        </a>-->
<!---->
<!--        <button class="register-button" onclick="openAuthModal()">-->
<!--            <i class="fas fa-user"></i>-->
<!--            <span id="authButtonText" data-i18n="register">注册</span>-->
<!--        </button>-->
<!---->
<!--        <header>-->
<!--            <div class="logo">🎬 UseePay Demo</div>-->
<!--            <h1 data-i18n="selectDramas">选择您喜欢的短剧</h1>-->
<!--            <p data-i18n="dramasDescription">订阅您喜爱的短剧，每部仅需 $0.99/月</p>-->
<!--        </header>-->

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

    <!-- Payment Methods Modal -->
    <div id="paymentMethodsModal" class="payment-methods-modal">
        <div class="payment-methods-modal-content">
            <div class="payment-methods-header">
                <h2 class="payment-methods-title" data-i18n="selectPaymentMethod">选择支付方式</h2>
                <button class="payment-methods-close" onclick="closePaymentMethodsModal()">×</button>
            </div>
            <div id="payment-element" style="margin: 20px 0;"></div>
            <div class="payment-methods-footer">
                <button class="payment-methods-btn cancel" onclick="closePaymentMethodsModal()" data-i18n="cancel">取消</button>
                <button class="payment-methods-btn confirm" onclick="confirmPaymentMethod()" data-i18n="confirm">确认</button>
            </div>
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
        let paymentHandler = null;
        let selectedDramas = new Set();
        const DRAMA_PRICE = 0.99;
        
        console.log('Short Dramas Page - Current language:', currentLang);
        console.log('Short Dramas Page - Translations loaded:', !!dramaTranslations.zh, !!dramaTranslations.en);

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
                subscribeButton.disabled = true;
            } else {
                const selectedDramasList = shortDramas.filter(d => selectedDramas.has(d.id));
                listContainer.innerHTML = selectedDramasList.map(drama => `
                    <div class="selected-drama-item">
                        <span>${currentLang === 'zh' ? drama.name_zh : drama.name_en}</span>
                        <span class="drama-item-price">$${DRAMA_PRICE.toFixed(2)}</span>
                    </div>
                `).join('');
                
                const totalPrice = selectedDramas.size * DRAMA_PRICE;
                totalPriceElement.textContent = '$' + totalPrice.toFixed(2);
                subscribeButton.disabled = false;
            }
            
            // Update billing cycle text
            if (billingCycleElement) {
                billingCycleElement.textContent = perMonthText;
            }
        }

        function handleSubscribe() {
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

            const selectedDramasList = shortDramas.filter(d => selectedDramas.has(d.id));
            const totalPrice = selectedDramas.size * DRAMA_PRICE;
            const currency = 'USD';

            const subscriptionData = {
                customer_id: customer.id,
                recurring: {
                    interval: 'month',
                    interval_count: 1,
                    unit_amount: totalPrice,
                    totalBillingCycles: null
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

            console.log('Sending subscription data:', subscriptionData);

            showProcessingModal();

            if (!paymentHandler) {
                paymentHandler = new PaymentResponseHandler({
                    translations: dramaTranslations,
                    currentLang: currentLang,
                    submitButton: null,
                    totals: {}
                });
            }

            fetch('/api/subscription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(subscriptionData)
            })
            .then(response => paymentHandler.handleResponse(response))
            .then(result => {
                updateProcessingStatus('success', dramaTranslations[currentLang].processingSuccess);

                const orderData = {
                    orderId: result.data.merchant_order_id,
                    paymentIntentId: result.data.id,
                    date: new Date().toISOString(),
                    status: result.data.status,
                    amount: result.data.amount,
                    currency: currency
                };

                localStorage.setItem('subscriptionResponseCache', JSON.stringify(result));

                setTimeout(() => {
                    closeProcessingModal();

                    const integrationMode = localStorage.getItem('paymentIntegrationMode') || 'redirect';
                    console.log('Integration mode:', integrationMode);

                    if (integrationMode === 'redirect') {
                        paymentHandler.processPaymentResultForRedirect(result, orderData);
                    } else if (integrationMode === 'embedded') {
                        paymentHandler.processPaymentResultForEmbedded(result, orderData);
                    } else {
                        renderPaymentMethodSection();
                        showPaymentMethodsModal();
                    }
                }, 1500);
            })
            .catch(error => {
                updateProcessingStatus('error', dramaTranslations[currentLang].processingError);

                setTimeout(() => {
                    closeProcessingModal();
                    paymentHandler.handleFetchError(error);
                }, 2000);
            });
        }

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

        function renderPaymentMethodSection() {
            const container = document.getElementById('payment-element');
            if (!container) {
                console.error('Payment methods container not found');
                return;
            }

            const t = dramaTranslations[currentLang];
            const cachedMethods = getPaymentMethods();
            let methodsToDisplay = cachedMethods.length > 0 ? cachedMethods : ['card', 'apple_pay'];

            const methodsHTML = methodsToDisplay.map((method, index) => {
                const methodInfo = paymentMethodsMap[method];
                if (!methodInfo) return '';

                const methodName = currentLang === 'zh' ? methodInfo.name_zh : methodInfo.name_en;
                const methodDesc = currentLang === 'zh' ? methodInfo.desc_zh : methodInfo.desc_en;
                const isFirst = index === 0;

                return `
                    <div class="payment-option">
                        <input type="radio" id="method_${method}" name="paymentMethod" value="${method}" ${isFirst ? 'checked' : ''}>
                        <label for="method_${method}">
                            <div class="payment-icon">${methodInfo.icon}</div>
                            <div class="payment-info">
                                <div class="payment-name">${methodName}</div>
                                <div class="payment-desc">${methodDesc}</div>
                            </div>
                        </label>
                    </div>
                `;
            }).join('');

            container.innerHTML = `
                <div class="form-section">
                    <h3>
                        <div class="payment-method-title">${t.paymentMethod || '支付方式'}</div>
                    </h3>
                    <div class="payment-methods" id="paymentMethodsList">
                        ${methodsHTML}
                    </div>
                </div>
            `;
        }

        function showPaymentMethodsModal() {
            const modal = document.getElementById('paymentMethodsModal');
            modal.classList.add('show');
        }

        function closePaymentMethodsModal() {
            const modal = document.getElementById('paymentMethodsModal');
            modal.classList.remove('show');
            setTimeout(() => { window.location.reload(); }, 500);
        }

        async function confirmPaymentMethod() {
            const paymentMethodsModal = document.getElementById('paymentMethodsModal');
            paymentMethodsModal.classList.remove('show');

            const processingModal = document.getElementById('processingModal');
            const processingTitle = document.getElementById('processingTitle');
            const processingMessage = document.getElementById('processingMessage');

            processingTitle.textContent = dramaTranslations[currentLang].paymentProcessing;
            processingMessage.textContent = dramaTranslations[currentLang].paymentProcessingMessage;
            processingModal.classList.add('show');

            if (!paymentHandler) {
                paymentHandler = new PaymentResponseHandler({
                    translations: dramaTranslations,
                    currentLang: currentLang,
                    submitButton: null,
                    totals: {}
                });
            }

            const integrationMode = localStorage.getItem('paymentIntegrationMode') || 'redirect';
            console.log('Confirm payment with integration mode:', integrationMode);

            try {
                let result;

                if (integrationMode === 'embedded') {
                    console.log('Using embedded mode - confirmPaymentIntent()');
                    result = await confirmPaymentIntent();
                } else if (integrationMode === 'api') {
                    console.log('Using API mode - calling confirmPaymentViaAPI()');

                    const currentSubscription = localStorage.getItem('subscriptionResponseCache');
                    const subscriptionData = currentSubscription ? JSON.parse(currentSubscription) : null;
                    const paymentIntentId = subscriptionData.data.id;

                    const selectedPaymentMethodRadio = document.querySelector('input[name="paymentMethod"]:checked');
                    const selectedPaymentMethod = selectedPaymentMethodRadio ? selectedPaymentMethodRadio.value : 'card';

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
                    } else {
                        payment_method_data = {
                            type: selectedPaymentMethod
                        };
                    }

                    result = await paymentHandler.confirmPaymentViaAPI(paymentIntentId, {
                        payment_method_data: payment_method_data
                    });
                } else {
                    console.warn('Unknown integration mode, defaulting to embedded');
                }

                if (result.success) {
                    updateProcessingStatus('success', dramaTranslations[currentLang].paymentSuccess);

                    setTimeout(() => {
                        closeProcessingModal();
                        window.location.href = '/payment/callback?id=' + result.paymentIntent.id + '&merchant_order_id=' + result.paymentIntent.merchant_order_id + '&status=succeeded';
                    }, 500);
                } else {
                    const errorMsg = result.error || dramaTranslations[currentLang].paymentError;
                    updateProcessingStatus('error', errorMsg);

                    setTimeout(() => {
                        closeProcessingModal();
                        paymentMethodsModal.classList.add('show');
                    }, 3000);
                }
            } catch (error) {
                console.error('Payment confirmation error:', error);
                updateProcessingStatus('error', dramaTranslations[currentLang].paymentError + ': ' + error.message);

                setTimeout(() => {
                    closeProcessingModal();
                    paymentMethodsModal.classList.add('show');
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

</body>
</html>
