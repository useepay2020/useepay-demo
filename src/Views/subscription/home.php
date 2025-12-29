<?php
/**
 * Subscription Home Page
 * Provides tabbed interface for pricing and short dramas subscription
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订阅中心 - UseePay Demo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/pricing.css">
    <link rel="stylesheet" href="/assets/css/short-dramas.css">
    <!-- Pricing Page Internationalization -->
    <script src="/assets/js/i18n/subscription/pricing-i18n.js"></script>
    <!-- Short Dramas Internationalization -->
    <script src="/assets/js/i18n/subscription/short-dramas-i18n.js"></script>
    <!-- UseePay SDK -->
    <script src="https://checkout-sdk1.uat.useepay.com/2.0.0/useepay.min.js"></script>
    <!-- Payment Methods Configuration -->
    <script src="/assets/js/payment/payment-methods-config.js"></script>
    <!-- UseePay Elements Initializer -->
    <script src="/assets/js/useepay-elements-initializer.js"></script>
    <!-- Payment Response Handler -->
    <script src="/assets/js/payment-response-handler.js"></script>
    <script>
        // Define switchTab function early so it's available for onclick handlers
        function switchTab(tabName, buttonElement) {
            console.log('=== switchTab called ===');
            console.log('Tab name:', tabName);
            console.log('Button element:', buttonElement);
            
            // Hide all tabs
            const allTabs = document.querySelectorAll('.tab-pane');
            console.log('Found tabs:', allTabs.length);
            allTabs.forEach(tab => {
                console.log('Removing active from:', tab.id);
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            const allButtons = document.querySelectorAll('.tab-button');
            console.log('Found buttons:', allButtons.length);
            allButtons.forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            const tabElement = document.getElementById(tabName + '-tab');
            console.log('Tab element found:', !!tabElement, 'ID:', tabName + '-tab');
            if (tabElement) {
                console.log('Adding active to tab:', tabName + '-tab');
                tabElement.classList.add('active');
                console.log('Tab classes after add:', tabElement.className);
            }

            // Mark button as active
            if (buttonElement) {
                console.log('Adding active to button');
                buttonElement.classList.add('active');
            }
            
            // Resize iframe after tab switch
            setTimeout(() => {
                const iframe = tabElement ? tabElement.querySelector('iframe') : null;
                if (iframe) {
                    console.log('Resizing iframe after tab switch:', iframe.id);
                    resizeIframe(iframe);
                    // Try again after a short delay to ensure content is fully rendered
                    setTimeout(() => resizeIframe(iframe), 300);
                    setTimeout(() => resizeIframe(iframe), 600);
                }
            }, 100);
            
            console.log('=== switchTab complete ===');
        }

        // Define resizeIframe function early so it's available for iframe onload
        let lastHeights = {}; // Track last heights to prevent infinite loops
        let resizeAttempts = {}; // Track resize attempts per iframe
        
        function resizeIframe(iframe) {
            if (!resizeAttempts[iframe.id]) {
                resizeAttempts[iframe.id] = 0;
            }
            
            function attemptResize() {
                try {
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    
                    if (iframeDoc && iframeDoc.body) {
                        const bodyScrollHeight = iframeDoc.body.scrollHeight;
                        const docScrollHeight = iframeDoc.documentElement.scrollHeight;
                        const height = Math.max(bodyScrollHeight, docScrollHeight);
                        
                        // Only resize if height is meaningful
                        if (height > 100) {
                            const lastHeight = lastHeights[iframe.id] || 0;
                            
                            // Only update if height changed significantly and not growing infinitely
                            if (Math.abs(height - lastHeight) > 50 && height < 10000) {
                                const newHeight = height + 50;
                                iframe.style.height = newHeight + 'px';
                                iframe.style.width = '100%';
                                lastHeights[iframe.id] = height;
                                console.log('✓', iframe.id, 'resized to:', newHeight, 'px');
                            }
                        }
                    }
                } catch (e) {
                    console.warn('Cannot resize iframe:', e.message);
                    if (!iframe.style.height) {
                        iframe.style.height = '1200px';
                        iframe.style.width = '100%';
                    }
                }
            }
            
            // Try multiple times with delays, but limit total attempts
            if (resizeAttempts[iframe.id] < 6) {
                attemptResize();
                setTimeout(attemptResize, 300);
                setTimeout(attemptResize, 600);
                setTimeout(attemptResize, 1000);
                setTimeout(attemptResize, 1500);
                setTimeout(attemptResize, 2000);
                resizeAttempts[iframe.id]++;
            }
        }
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7ff;
        }

        .subscription-header {
            background: linear-gradient(135deg, #4a6bdf, #2a4ab3);
            color: white;
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .subscription-header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .subscription-header-left {
            flex: 1;
            text-align: left;
        }

        .subscription-header-left h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .subscription-header-left p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .subscription-header-right {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .back-button {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .register-button {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .register-button:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .tabs-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .tabs-header {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #e0e0e0;
            margin-top: 2rem;
            background: white;
            border-radius: 8px 8px 0 0;
        }

        .tab-button {
            padding: 1.2rem 2rem;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }

        .tab-button:hover {
            color: #4a6bdf;
        }

        .tab-button.active {
            color: #4a6bdf;
            border-bottom-color: #4a6bdf;
        }

        .tabs-content {
            background: white;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .tab-pane {
            display: none !important;
            padding: 0;
            visibility: hidden;
            height: 0;
            overflow: hidden;
        }

        .tab-pane.active {
            display: block !important;
            visibility: visible;
            height: auto;
            overflow: visible;
        }

        .tab-pane iframe {
            width: 100%;
            border: none;
            display: block;
            min-height: 600px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Override pricing.css container styles */
        .tab-pane .container {
            padding: 0;
            max-width: 100%;
        }

        .tab-pane .back-button,
        .tab-pane .register-button {
            display: none;
        }

        .tab-pane header {
            background: transparent;
            color: #333;
            padding: 0 0 2rem 0;
            text-align: center;
            box-shadow: none;
            position: relative;
            overflow: visible;
            display: block;
            justify-content: center;
            align-items: center;
            padding-right: 0;
        }

        .tab-pane header .logo {
            color: #333;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .tab-pane header h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .tab-pane header p {
            color: #666;
        }

        /* Pricing grid adjustments */
        .tab-pane .pricing-grid {
            margin-top: 2rem;
        }

        /* Short dramas adjustments */
        .tab-pane .dramas-container {
            margin-top: 2rem;
        }

        /* FAQ section adjustments */
        .tab-pane .faq-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e0e0e0;
        }

        /* Comparison section adjustments */
        .tab-pane .comparison-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e0e0e0;
        }

        @media (max-width: 768px) {
            .subscription-header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .subscription-header-left {
                text-align: center;
            }

            .subscription-header-left h1 {
                font-size: 1.5rem;
            }

            .subscription-header-right {
                width: 100%;
                justify-content: center;
            }

            .tabs-header {
                flex-wrap: wrap;
            }

            .tab-button {
                flex: 1;
                padding: 1rem;
                font-size: 0.9rem;
            }

            .tab-pane {
                padding: 1rem;
            }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h2 {
            margin: 0;
            color: #333;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .submit-button {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .submit-button:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Header with navigation -->
    <div class="subscription-header">
        <div class="subscription-header-content">
            <div class="subscription-header-left">
                <h1 data-i18n="subscriptionCenter">订阅中心</h1>
                <p data-i18n="chooseYourPlan">选择适合您的订阅方案</p>
            </div>
            <div class="subscription-header-right">
                <button class="register-button" onclick="openAuthModal()">
                    <i class="fas fa-user"></i>
                    <span id="authButtonText" data-i18n="register">注册</span>
                </button>
                <a href="/" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    <span data-i18n="backHome">返回首页</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs-container">
        <div class="tabs-header">
            <button class="tab-button active" onclick="switchTab('pricing', this)" data-i18n="softwareSubscription">软件订阅示例</button>
            <button class="tab-button" onclick="switchTab('dramas', this)" data-i18n="shortDramas">短剧订阅示例</button>
        </div>

        <div class="tabs-content">
            <!-- Pricing Tab -->
            <div id="pricing-tab" class="tab-pane active">
                <iframe id="pricing-iframe" src="/subscription/software" frameborder="0" scrolling="no" onload="resizeIframe(this)"></iframe>
            </div>

            <!-- Short Dramas Tab -->
            <div id="dramas-tab" class="tab-pane">
                <iframe id="dramas-iframe" src="/subscription/short_dramas" frameborder="0" scrolling="no" onload="resizeIframe(this)"></iframe>
            </div>
        </div>
    </div>

    <!-- Auth Modal -->
    <div id="authModal" class="modal">
        <div class="modal-content">
            <form id="register-form" class="form-content active" onsubmit="handleRegister(event)">
                <div class="modal-header">
                    <h2 data-i18n="register">注册</h2>
                    <button type="button" class="modal-close" onclick="closeAuthModal()">×</button>
                </div>
                <div class="form-group">
                    <label for="register-email" data-i18n="email">邮箱地址</label>
                    <input type="email" id="register-email" data-i18n-placeholder="emailPlaceholder" required>
                </div>
                <div class="form-group">
                    <label for="register-password" data-i18n="password">密码</label>
                    <input type="password" id="register-password" data-i18n-placeholder="passwordPlaceholder" required>
                </div>
                <div class="form-group">
                    <label for="register-confirm-password" data-i18n="confirmPassword">确认密码</label>
                    <input type="password" id="register-confirm-password" data-i18n-placeholder="confirmPasswordPlaceholder" required>
                </div>
                <button type="submit" class="submit-button" data-i18n="registerButton">注册</button>
            </form>
        </div>
    </div>

    <script>
        // ===== Global Variables =====
        let currentLang = localStorage.getItem('language') || 'zh';

        // Get translations from i18n files
        // Note: short-dramas-i18n.js exports as window.translations
        // pricing-i18n.js exports as window.pricingTranslations
        const homePricingTranslations = window.pricingTranslations || {};
        const homeDramasTranslations = window.translations || {}; // short-dramas uses window.translations
        
        // Merge translations for each language
        const homeTranslations = {
            zh: {
                ...(homePricingTranslations.zh || {}),
                ...(homeDramasTranslations.zh || {}),
                subscriptionCenter: '订阅中心',
                chooseYourPlan: '选择适合您的订阅方案',
                softwareSubscription: '软件订阅示例',
                shortDramas: '短剧订阅示例',
                register: '注册',
                backHome: '返回首页',
                personalCenter: '个人中心',
                email: '邮箱地址',
                password: '密码',
                confirmPassword: '确认密码',
                registerButton: '注册',
                emailPlaceholder: '请输入邮箱地址',
                passwordPlaceholder: '请输入密码',
                confirmPasswordPlaceholder: '请再次输入密码'
            },
            en: {
                ...(homePricingTranslations.en || {}),
                ...(homeDramasTranslations.en || {}),
                subscriptionCenter: 'Subscription Center',
                chooseYourPlan: 'Choose Your Subscription Plan',
                softwareSubscription: 'Software Subscription Demo',
                shortDramas: 'Short Dramas Demo',
                register: 'Register',
                backHome: 'Back to Home',
                personalCenter: 'Personal Center',
                email: 'Email Address',
                password: 'Password',
                confirmPassword: 'Confirm Password',
                registerButton: 'Register',
                emailPlaceholder: 'Please enter your email address',
                passwordPlaceholder: 'Please enter your password',
                confirmPasswordPlaceholder: 'Please confirm your password'
            }
        };
        
        console.log('Pricing translations:', homePricingTranslations);
        console.log('Dramas translations:', homeDramasTranslations);
        console.log('Merged translations:', homeTranslations);

        // ===== Initialization =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== Subscription Home Page Initialized ===');
            console.log('Current language from localStorage:', currentLang);
            console.log('Available translations:', homeTranslations);
            console.log('Translations for current language:', homeTranslations[currentLang]);
            setLanguage(currentLang);
            updateAuthButtonText();
            initializeIframeResizing();
        });

        // ===== Iframe Auto-Resizing =====
        function initializeIframeResizing() {
            console.log('Iframe resizing initialized');
            // Resizing is handled by onload event on each iframe
            // No continuous monitoring needed to prevent infinite loops
        }

        // ===== Language Management =====
        function setLanguage(lang) {
            console.log('setLanguage called with:', lang);
            currentLang = lang;
            localStorage.setItem('language', lang);

            let updatedCount = 0;
            
            // Update text content for elements with data-i18n
            document.querySelectorAll('[data-i18n]').forEach(element => {
                const key = element.getAttribute('data-i18n');
                if (homeTranslations[lang] && homeTranslations[lang][key]) {
                    element.textContent = homeTranslations[lang][key];
                    updatedCount++;
                }
            });
            
            // Update placeholders for elements with data-i18n-placeholder
            document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
                const key = element.getAttribute('data-i18n-placeholder');
                if (homeTranslations[lang] && homeTranslations[lang][key]) {
                    element.placeholder = homeTranslations[lang][key];
                    console.log(`Updated placeholder "${key}": "${homeTranslations[lang][key]}"`);
                    updatedCount++;
                }
            });

            console.log(`Total elements updated: ${updatedCount}`);
            updateAuthButtonText();
            console.log('Language set to:', lang);
        }

        function updateAuthButtonText() {
            const customerData = localStorage.getItem('customer');
            const customer = customerData ? JSON.parse(customerData) : null;
            const authButtonText = document.getElementById('authButtonText');

            if (authButtonText && homeTranslations[currentLang]) {
                const buttonText = customer ? homeTranslations[currentLang].personalCenter : homeTranslations[currentLang].register;
                authButtonText.textContent = buttonText;
            }
        }

        // ===== Auth Modal Functions =====
        function openAuthModal() {
            const customerData = localStorage.getItem('customer');
            const customer = customerData ? JSON.parse(customerData) : null;

            window.currentCustomer = customer;

            const authModal = document.getElementById('authModal');
            if (!authModal) {
                console.warn('Auth modal not found in home.php');
                return;
            }
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
                const customerObject = data.data || data;
                localStorage.setItem('customer', JSON.stringify(customerObject));

                const successMsg = currentLang === 'zh' ? '注册成功！' : 'Registration successful!';
                alert(successMsg);

                updateAuthButtonText();
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

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const authModal = document.getElementById('authModal');
            if (event.target === authModal) {
                closeAuthModal();
            }
        });
    </script>
</body>
</html>
