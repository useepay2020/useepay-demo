<?php global $basePath;
/**
 * Home Page View
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UseePay API 演示</title>
    <!-- Home Page Styles -->
    <link rel="stylesheet" href="/assets/css/home.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <i class="fas fa-credit-card"></i>
                <span data-i18n="title">UseePay 支付体验站</span>
            </div>
            <p class="tagline" data-i18n="tagline">简单、安全、高效的支付解决方案</p>
        </div>
        <div class="lang-toggle-header">
            <button class="lang-btn active" onclick="setLanguage('zh')" id="langZh" data-i18n="zh">中文</button>
            <button class="lang-btn" onclick="setLanguage('en')" id="langEn" data-i18n="en">English</button>
        </div>
    </header>
    <main class="main-content" style="padding-top: 0;">
        <div class="container">
            <!-- 建站方式导航栏 - 靠左对齐 -->
            <div style="background: white; border-bottom: 2px solid #e9ecef; margin: 0 -20px 1.5rem -20px; display: flex; padding-left: 20px; height: 56px; align-items: center; box-sizing: border-box; overflow: visible; position: relative;">
                <button class="building-nav-btn" data-value="selfBuilt" style="flex: 0 0 auto; padding: 8px 20px; text-align: center; background: white; border: 1px solid #e9ecef; border-radius: 8px; cursor: pointer; font-size: 0.95rem; font-weight: 600; color: #666; transition: all 0.3s ease; margin-right: 8px; position: relative;" onclick="selectBuildingMethod(this)">
                    <i class="fas fa-hammer" style="margin-right: 0.5rem;"></i>
                    <span data-i18n="selfBuilt">自建站</span>
                </button>
                <button class="building-nav-btn" data-value="shopify" style="flex: 0 0 auto; padding: 8px 20px; text-align: center; background: white; border: 1px solid #e9ecef; border-radius: 8px; cursor: pointer; font-size: 0.95rem; font-weight: 600; color: #666; transition: all 0.3s ease; margin-right: 8px; position: relative;" onclick="selectBuildingMethod(this)">
                    <i class="fas fa-shopping-bag" style="margin-right: 0.5rem;"></i>
                    <span>Shopify</span>
                </button>
                <button class="building-nav-btn" data-value="shopline" style="flex: 0 0 auto; padding: 8px 20px; text-align: center; background: white; border: 1px solid #e9ecef; border-radius: 8px; cursor: pointer; font-size: 0.95rem; font-weight: 600; color: #666; transition: all 0.3s ease; margin-right: 8px; position: relative;" onclick="selectBuildingMethod(this)">
                    <i class="fas fa-store" style="margin-right: 0.5rem;"></i>
                    <span>ShopLine</span>
                </button>
            </div>

            <!-- 自建站板块：放置自建站的支付体验内容 -->
            <div id="selfBuiltSection">
                <!-- 支付模式选择区域 -->
                <div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                    <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 1.5rem;">
                        <i class="fas fa-cog"></i> <span data-i18n="selectMode">选择集成模式</span>
                    </h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                        <label class="payment-mode-option" style="display: flex; flex-direction: column; cursor: pointer; padding: 1.2rem; background: white; border: 2px solid #dee2e6; border-radius: 8px; transition: all 0.3s ease;">
                            <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                                <input type="radio" name="paymentMode" value="redirect" checked style="margin-right: 0.8rem; cursor: pointer; width: 18px; height: 18px;">
                                <strong style="font-size: 1.1rem; color: var(--text-color);" data-i18n="redirect">跳转收银台</strong>
                            </div>
                            <small style="color: var(--text-light); line-height: 1.5;" data-i18n="redirectDesc">跳转到 UseePay 托管的收银台页面，快速集成，安全可靠</small>
                        </label>
                        <label class="payment-mode-option" style="display: flex; flex-direction: column; cursor: pointer; padding: 1.2rem; background: white; border: 2px solid #dee2e6; border-radius: 8px; transition: all 0.3s ease;">
                            <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                                <input type="radio" name="paymentMode" value="embedded" style="margin-right: 0.8rem; cursor: pointer; width: 18px; height: 18px;">
                                <strong style="font-size: 1.1rem; color: var(--text-color);" data-i18n="embedded">内嵌收银台</strong>
                            </div>
                            <small style="color: var(--text-light); line-height: 1.5;" data-i18n="embeddedDesc">在您的页面中嵌入收银台组件，保持品牌一致性</small>
                        </label>
                        <label class="payment-mode-option" style="display: flex; flex-direction: column; cursor: pointer; padding: 1.2rem; background: white; border: 2px solid #dee2e6; border-radius: 8px; transition: all 0.3s ease;">
                            <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                                <input type="radio" name="paymentMode" value="api" style="margin-right: 0.8rem; cursor: pointer; width: 18px; height: 18px;">
                                <strong style="font-size: 1.1rem; color: var(--text-color);" data-i18n="api">纯 API 模式</strong>
                            </div>
                            <small style="color: var(--text-light); line-height: 1.5;" data-i18n="apiDesc">完全自定义支付流程和界面，灵活度最高</small>
                        </label>
                    </div>
                </div>

                <div class="cards">
                    <div class="card">
                        <h2><i class="fas fa-credit-card"></i> <span data-i18n="payment">一次支付</span></h2>
                        <p data-i18n="paymentDesc">处理一次性支付，支持多种支付方式。请选择您需要启用的支付方式：</p>

                        <div style="margin: 1.5rem 0; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                            <label style="display: block; margin-bottom: 1rem; font-weight: 600; color: var(--text-color);">
                                <i class="fas fa-wallet"></i> <span data-i18n="selectPaymentMethod">选择支付方式：</span>
                            </label>
                            <div id="paymentMethodsContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.8rem;">
                                <!-- Payment methods will be rendered here dynamically -->
                            </div>
                        </div>

                        <a href="#" class="btn btn-success" id="createPaymentBtn">
                            <i class="fas fa-shopping-cart"></i> <span data-i18n="createPayment">创建支付</span>
                        </a>
                    </div>

                    <div class="card">
                        <h2><i class="fas fa-calendar-alt"></i> <span data-i18n="installmentPayment">分期支付</span></h2>
                        <p data-i18n="installmentPaymentDesc">灵活的分期付款方案，支持多种分期支付方式。请选择您需要启用的支付方式：</p>

                        <div style="margin: 1.5rem 0; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                            <label style="display: block; margin-bottom: 1rem; font-weight: 600; color: var(--text-color);">
                                <i class="fas fa-wallet"></i> <span data-i18n="selectInstallmentMethod">选择支付方式：</span>
                            </label>
                            <div id="installmentMethodsContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.8rem;">
                                <!-- Installment methods will be rendered here dynamically -->
                            </div>
                        </div>

                        <a href="#" class="btn btn-success" id="createInstallmentBtn">
                            <i class="fas fa-credit-card"></i> <span data-i18n="createInstallment">创建分期支付</span>
                        </a>
                    </div>

                    <div class="card">
                        <h2><i class="fas fa-sync"></i> <span data-i18n="subscription">订阅管理</span></h2>
                        <p data-i18n="subscriptionDesc">设置和管理定期订阅，自动处理定期扣款和账单。请选择您需要启用的支付方式：</p>

                        <div style="margin: 1.5rem 0; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                            <label style="display: block; margin-bottom: 1rem; font-weight: 600; color: var(--text-color);">
                                <i class="fas fa-wallet"></i> <span data-i18n="selectSubscriptionMethod">选择支付方式：</span>
                            </label>
                            <div id="subscriptionMethodsContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.8rem;">
                                <!-- Subscription methods will be rendered here dynamically -->
                            </div>
                        </div>

                        <a href="/subscription/home" class="btn btn-warning" id="createSubscriptionBtn">
                            <i class="fas fa-plus"></i> <span data-i18n="createSubscription">创建订阅</span>
                        </a>
                    </div>

                    <div class="card">
                        <h2><i class="fas fa-bolt"></i> <span data-i18n="expressCheckout">Expression Checkout</span></h2>
                        <p data-i18n="expressCheckoutDesc">快速发起支付，支持多种支付方式。请选择您需要启用的支付方式：</p>

                        <div style="margin: 1.5rem 0; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                            <label style="display: block; margin-bottom: 1rem; font-weight: 600; color: var(--text-color);">
                                <i class="fas fa-wallet"></i> <span data-i18n="selectQuickPaymentMethod">选择支付方式：</span>
                            </label>
                            <div id="quickPaymentMethodsContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.8rem;">
                                <!-- Quick payment methods will be rendered here dynamically -->
                            </div>
                        </div>

                        <a href="#" class="btn btn-success" id="expressCheckoutBtn">
                            <i class="fas fa-zap"></i> <span data-i18n="startExpressCheckout">开始 Expression Checkout</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Shopify板块：默认隐藏，放置shopify插件教程 -->
            <div id="shopifySection" class="doc-section" style="display:none;">
                <div class="card">
                    <h2>
                        <i class="fas fa-shopping-bag"></i>
                        <span data-i18n="shopifyDocTitle">Shopify 集成教程</span>
                    </h2>
                    <p data-i18n="shopifyDocIntro">Shopify 商户通过安装 UseePay 插件完成收付款，请点击下方链接查看完整集成步骤：</p>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;margin-top:1rem;">
                        <div class="doc-card">
                            <h3 data-i18n="shopifyEmbeddedTitle">内嵌收银台</h3>
                            <div class="doc-tags">
                                <span class="tag" data-i18n="shopifyEmbeddedTag">适用于：信用卡</span>
                            </div>
                            <p data-i18n="shopifyEmbeddedDesc">将 UseePay 收银台直接嵌入 Shopify 结算流程，提升转化率，体验更自然。</p>
                            <a href="/plugin-docs/zh/shopify-embedded"
                               data-doc-slug="shopify-embedded"
                               class="btn btn-outline"
                                <span data-i18n="pluginDocButton">查看插件安装教程</span>
                            </a>
                        </div>

                        <div class="doc-card">
                            <h3 data-i18n="shopifyRedirectTitle">跳转收银台</h3>
                            <div class="doc-tags">
                                <span class="tag" data-i18n="shopifyRedirectTag">适用于：信用卡, GooglePay, ApplePay</span>
                            </div>
                            <p data-i18n="shopifyRedirectDesc">在 Shopify 结算页引导用户跳转到 UseePay 托管收银台，快速完成多种支付。</p>
                            <a href="/plugin-docs/zh/shopify-redirect"
                               class="btn btn-outline"
                               data-doc-slug="shopify-redirect">
                                <span data-i18n="pluginDocButton">查看插件安装教程</span>
                            </a>
                        </div>

                        <div class="doc-card">
                            <h3 data-i18n="shopifyLocalizedTitle">本地化收银台</h3>
                            <div class="doc-tags">
                                <span class="tag" data-i18n="shopifyLocalizedTag">适用于：本地化支付方式</span>
                            </div>
                            <p data-i18n="shopifyLocalizedDesc">如：ApplePay, GooglePay, Klarna, Afterpay, Pix, OXXO等本地化支付</p>
                            <a href="/plugin-docs/zh/shopify-localized"
                               class="btn btn-outline"
                               data-doc-slug="shopify-localized">
                                <span data-i18n="pluginDocButton">查看插件安装教程</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ShopLine板块：默认隐藏，放置shopline插件教程 -->
            <div id="shoplineSection" class="doc-section" style="display:none;">
                <div class="card">
                    <h2>
                        <i class="fas fa-store"></i>
                        <span data-i18n="shoplineDocTitle">ShopLine 集成教程</span>
                    </h2>
                    <p data-i18n="shoplineDocIntro">ShopLine 商户通过安装 UseePay 插件完成收付款，请点击下方链接查看完整集成步骤：</p>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;margin-top:1rem;">
                        <div class="doc-card">
                            <h3 data-i18n="shoplineEmbeddedTitle">内嵌收银台</h3>
                            <div class="doc-tags">
                                <span class="tag" data-i18n="shoplineEmbeddedTag">适用于：信用卡</span>
                            </div>
                            <p data-i18n="shoplineEmbeddedDesc">在 ShopLine 结算页面内嵌 UseePay 收银台，适用于信用卡支付。</p>
                            <a href="/plugin-docs/zh/shopline-embedded"
                               class="btn btn-outline"
                               data-doc-slug="shopline-embedded">
                                <span data-i18n="pluginDocButton">查看插件安装教程</span>
                            </a>
                        </div>

                        <div class="doc-card">
                            <h3 data-i18n="shoplineRedirectTitle">跳转收银台</h3>
                            <div class="doc-tags">
                                <span class="tag" data-i18n="shoplineRedirectTag">适用于：GooglePay, ApplePay</span>
                            </div>
                            <p data-i18n="shoplineRedirectDesc">在 ShopLine 结算页引导用户跳转到 UseePay 托管收银台，支持 Google Pay / Apple Pay。</p>
                            <a href="/plugin-docs/zh/shopline-redirect"
                               class="btn btn-outline"
                               data-doc-slug="shopline-redirect">
                                <span data-i18n="pluginDocButton">查看插件安装教程</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="features">
                <h3 data-i18n="features">主要特性</h3>
                <div class="feature-grid">
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <h4 data-i18n="security">安全可靠</h4>
                        <p data-i18n="securityDesc">采用银行级安全标准，保障交易安全</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-bolt"></i>
                        <h4 data-i18n="speed">快速接入</h4>
                        <p data-i18n="speedDesc">简单易用的API，快速集成到您的应用</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-globe"></i>
                        <h4 data-i18n="global">全球支付</h4>
                        <p data-i18n="globalDesc">支持全球多种支付方式和货币</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-chart-line"></i>
                        <h4 data-i18n="analytics">数据分析</h4>
                        <p data-i18n="analyticsDesc">详细的交易数据和分析报告</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Under Construction Modal -->
    <div id="underConstructionModal" class="under-construction-modal">
        <div class="under-construction-modal-content">
            <button class="under-construction-close" onclick="closeUnderConstructionModal()">×</button>
            <div class="under-construction-icon">
                <i class="fas fa-hard-hat"></i>
            </div>
            <h2 class="under-construction-title" data-i18n="underConstructionTitle">功能建设中</h2>
            <p class="under-construction-message" data-i18n="underConstructionMessage">该功能正在开发中，敬请期待！我们会尽快为您提供更好的体验。</p>
            <button class="under-construction-btn" onclick="closeUnderConstructionModal()" data-i18n="underConstructionBtn">知道了</button>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3 data-i18n="about">关于我们</h3>
                <p data-i18n="aboutDesc">UseePay 提供全球支付解决方案，帮助企业轻松接受全球支付。</p>
            </div>
            <div class="footer-section">
                <h3 data-i18n="quickLinks">快速链接</h3>
                <ul>
                    <li><a href="#" data-i18n="apiDocs">API 文档</a></li>
                    <li><a href="#" data-i18n="devCenter">开发者中心</a></li>
                    <li><a href="#" data-i18n="pricing">定价</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3 data-i18n="support">支持</h3>
                <ul>
                    <li><a href="#" data-i18n="helpCenter">帮助中心</a></li>
                    <li><a href="#" data-i18n="contact">联系我们</a></li>
                    <li><a href="#" data-i18n="status">服务状态</a></li>
                    <li><a href="#" data-i18n="privacy">隐私政策</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3 data-i18n="contactUs">联系我们</h3>
                <p data-i18n="email">邮箱: support@useepay.com</p>
                <p data-i18n="phone">电话: +86 400-123-4567</p>
                <div style="margin-top: 1rem;">
                    <a href="#" style="margin-right: 10px;"><i class="fab fa-github fa-lg"></i></a>
                    <a href="#" style="margin-right: 10px;"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" style="margin-right: 10px;"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#"><i class="fab fa-linkedin fa-lg"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> UseePay. <span data-i18n="copyright">保留所有权利。</span></p>
        </div>
    </footer>

    <!-- Payment Methods Configuration Module -->
    <script src="/assets/js/payment/payment-methods-config.js"></script>

    <script>
        // ===== 支付方式卡片配置 =====
        const paymentCardsConfig = {
            payment: {
                containerId: 'paymentMethodsContainer',
                inputName: 'paymentMethod',
                methods: ['card', 'apple_pay', 'google_pay', 'wechat', 'alipay', 'oxxo', 'kakao_pay', 'naver_pay', 'payco', 'toss_pay', 'samsung_pay', 'tmoney'],
                defaultChecked: ['card', 'apple_pay', 'google_pay'],
                cacheKey: 'paymentMethods'
            },
            installment: {
                containerId: 'installmentMethodsContainer',
                inputName: 'installmentMethod',
                methods: ['afterpay', 'klarna', 'affirm'],
                defaultChecked: ['afterpay', 'klarna', 'affirm'],
                cacheKey: 'installmentMethods'
            },
            subscription: {
                containerId: 'subscriptionMethodsContainer',
                inputName: 'subscriptionMethod',
                methods: ['card', 'apple_pay', 'google_pay', 'kakao_pay'],
                defaultChecked: ['card', 'apple_pay', 'google_pay'],
                cacheKey: 'subscriptionMethods'
            },
            quickPayment: {
                containerId: 'quickPaymentMethodsContainer',
                inputName: 'quickPaymentMethod',
                methods: ['card', 'apple_pay', 'google_pay', 'wechat', 'alipay'],
                defaultChecked: ['card', 'apple_pay', 'google_pay', 'wechat', 'alipay'],
                cacheKey: 'quickPaymentMethods'
            }
        };

        // ===== 国际化翻译 =====
        const translations = {
            zh: {
                title: 'UseePay 支付体验站',
                tagline: '简单、安全、高效的支付解决方案',
                zh: '中文',
                en: 'English',
                buildingMethod: '选择建站方式',
                selfBuilt: '自建站',
                selfBuiltDesc: '完全自定义，独立部署，完全掌控您的在线业务',
                shopifyTab: 'Shopify',
                shoplineTab: 'ShopLine',
                shopifyDesc: '全球领先的电商平台，快速上线，专业支持',
                shoplineDesc: '亚太地区领先的建站平台，本地化支持，易于使用',
                welcome: '欢迎使用 UseePay API 演示',
                selectMode: '选择集成模式',
                redirect: '跳转收银台',
                redirectDesc: '跳转到 UseePay 托管的收银台页面，快速集成，安全可靠',
                embedded: '内嵌收银台',
                embeddedDesc: '在您的页面中嵌入收银台组件，保持品牌一致性',
                api: '纯 API 模式',
                apiDesc: '完全自定义支付流程和界面，灵活度最高',
                payment: '一次支付',
                paymentDesc: '处理一次性支付，支持多种支付方式。请选择您需要启用的支付方式：',
                selectPaymentMethod: '选择支付方式：',
                card: '信用卡/借记卡',
                createPayment: '创建支付',
                installmentPayment: '分期支付',
                installmentPaymentDesc: '灵活的分期付款方案，支持多种分期支付方式。请选择您需要启用的支付方式：',
                selectInstallmentMethod: '选择支付方式：',
                createInstallment: '创建分期支付',
                subscription: '订阅管理',
                subscriptionDesc: '设置和管理定期订阅，自动处理定期扣款和账单。请选择您需要启用的支付方式：',
                selectSubscriptionMethod: '选择支付方式：',
                createSubscription: '创建订阅',
                expressCheckout: '快捷支付',
                expressCheckoutDesc: '快速发起支付，支持多种支付方式。请选择您需要启用的支付方式：',
                selectQuickPaymentMethod: '选择支付方式：',
                startExpressCheckout: '快捷支付',
                features: '主要特性',
                security: '安全可靠',
                securityDesc: '采用银行级安全标准，保障交易安全',
                speed: '快速接入',
                speedDesc: '简单易用的API，快速集成到您的应用',
                global: '全球支付',
                globalDesc: '支持全球多种支付方式和货币',
                analytics: '数据分析',
                analyticsDesc: '详细的交易数据和分析报告',
                about: '关于我们',
                aboutDesc: 'UseePay 提供全球支付解决方案，帮助企业轻松接受全球支付。',
                quickLinks: '快速链接',
                apiDocs: 'API 文档',
                devCenter: '开发者中心',
                pricing: '定价',
                support: '支持',
                helpCenter: '帮助中心',
                contact: '联系我们',
                status: '服务状态',
                privacy: '隐私政策',
                contactUs: '联系我们',
                email: '邮箱: support@useepay.com',
                phone: '电话: +86 400-123-4567',
                copyright: '保留所有权利。',
                morePaymentMethods: '更多支付方式...',
                wechat: '微信支付',
                alipay: '支付宝',
                validationError: '验证错误',
                selectAtLeastOnePaymentMethod: '请至少选择一种支付方式！',
                selectAtLeastOneInstallmentMethod: '请至少选择一种分期支付方式！',
                selectAtLeastOneSubscriptionMethod: '请至少选择一种订阅方式！',
                ok: '确定',
                afterpay: 'Afterpay',
                klarna: 'Klarna',
                affirm: 'Affirm',
                oxxo: 'OXXO',
                kakaoPay: 'Kakao Pay',
                naverPay: 'Naver Pay',
                payco: 'PAYCO',
                toss: 'Toss',
                underConstructionTitle: '功能建设中',
                underConstructionMessage: '该功能正在开发中，敬请期待！我们会尽快为您提供更好的体验。',
                underConstructionBtn: '知道了',

                // Shopify / ShopLine 插件文档区
                shopifyDocTitle: 'Shopify 集成教程',
                shopifyDocIntro: 'Shopify 商户通过安装 UseePay 插件完成收付款，请点击下方链接查看完整集成步骤：',
                shopifyEmbeddedTitle: '内嵌收银台',
                shopifyEmbeddedTag: '适用于：信用卡',
                shopifyEmbeddedDesc: '将 UseePay 收银台直接嵌入 Shopify 结算流程，提升转化率，体验更自然。',
                shopifyRedirectTitle: '跳转收银台',
                shopifyRedirectTag: '适用于：信用卡, GooglePay, ApplePay',
                shopifyRedirectDesc: '在 Shopify 结算页引导用户跳转到 UseePay 托管收银台，快速完成多种支付。',
                shopifyLocalizedTitle: '本地化收银台',
                shopifyLocalizedTag: '适用于：本地化支付方式',
                shopifyLocalizedDesc: '如：ApplePay, GooglePay, Klarna, Afterpay, Pix, OXXO等本地化支付',

                shoplineDocTitle: 'ShopLine 集成教程',
                shoplineDocIntro: 'ShopLine 商户通过安装 UseePay 插件完成收付款，请点击下方链接查看完整集成步骤：',
                shoplineEmbeddedTitle: '内嵌收银台',
                shoplineEmbeddedTag: '适用于：信用卡',
                shoplineEmbeddedDesc: '在 ShopLine 结算页面内嵌 UseePay 收银台，适用于信用卡支付。',
                shoplineRedirectTitle: '跳转收银台',
                shoplineRedirectTag: '适用于：GooglePay, ApplePay',
                shoplineRedirectDesc: '在 ShopLine 结算页引导用户跳转到 UseePay 托管收银台，支持 Google Pay / Apple Pay。',

                pluginDocButton: '查看插件安装教程'
            },
            en: {
                title: 'UseePay Demo',
                tagline: 'Simple, Secure, and Efficient Payment Solutions',
                zh: '中文',
                en: 'English',
                buildingMethod: 'Select Building Method',
                selfBuilt: 'Self-Built',
                selfBuiltDesc: 'Fully customizable, independent deployment, complete control of your online business',
                shopifyTab: 'Shopify',
                shoplineTab: 'ShopLine',
                shopifyDesc: 'Leading global e-commerce platform, quick launch, professional support',
                shoplineDesc: 'Leading website builder in Asia-Pacific, localized support, easy to use',
                welcome: 'Welcome to UseePay API Demo',
                selectMode: 'Select Integration Mode',
                redirect: 'Redirect Checkout',
                redirectDesc: 'Redirect to UseePay hosted checkout page, quick integration, secure and reliable',
                embedded: 'Embedded Checkout',
                embeddedDesc: 'Embed checkout component in your page, maintain brand consistency',
                api: 'Pure API Mode',
                apiDesc: 'Fully customize payment flow and interface, maximum flexibility',
                payment: 'One-Time Payment',
                paymentDesc: 'Process one-time payments, support multiple payment methods. Please select the payment methods you want to enable:',
                selectPaymentMethod: 'Select Payment Method:',
                card: 'Credit/Debit Card',
                createPayment: 'Create Payment',
                installmentPayment: 'Installment Payment',
                installmentPaymentDesc: 'Flexible installment payment plans, support multiple installment payment methods. Please select the payment methods you want to enable:',
                selectInstallmentMethod: 'Select Payment Method:',
                createInstallment: 'Create Installment Payment',
                subscription: 'Subscription Management',
                subscriptionDesc: 'Set up and manage recurring subscriptions, automatically handle recurring charges and billing. Please select the payment methods you want to enable:',
                selectSubscriptionMethod: 'Select Payment Method:',
                createSubscription: 'Create Subscription',
                expressCheckout: 'Expression Checkout',
                expressCheckoutDesc: 'Quickly initiate payments, support multiple payment methods. Please select the payment methods you want to enable:',
                selectQuickPaymentMethod: 'Select Payment Method:',
                startExpressCheckout: 'Start Expression Checkout',
                features: 'Key Features',
                security: 'Secure & Reliable',
                securityDesc: 'Bank-level security standards to ensure transaction security',
                speed: 'Quick Integration',
                speedDesc: 'Simple and easy-to-use API, quick integration into your application',
                global: 'Global Payments',
                globalDesc: 'Support multiple payment methods and currencies worldwide',
                analytics: 'Data Analytics',
                analyticsDesc: 'Detailed transaction data and analysis reports',
                about: 'About Us',
                aboutDesc: 'UseePay provides global payment solutions to help businesses easily accept payments worldwide.',
                quickLinks: 'Quick Links',
                apiDocs: 'API Documentation',
                devCenter: 'Developer Center',
                pricing: 'Pricing',
                support: 'Support',
                helpCenter: 'Help Center',
                contact: 'Contact Us',
                status: 'Service Status',
                privacy: 'Privacy Policy',
                contactUs: 'Contact Us',
                email: 'Email: support@useepay.com',
                phone: 'Phone: +86 400-123-4567',
                copyright: 'All rights reserved.',
                morePaymentMethods: 'More payment methods...',
                wechat: 'WeChat Pay',
                alipay: 'Alipay',
                validationError: 'Validation Error',
                selectAtLeastOnePaymentMethod: 'Please select at least one payment method!',
                selectAtLeastOneInstallmentMethod: 'Please select at least one installment method!',
                selectAtLeastOneSubscriptionMethod: 'Please select at least one subscription method!',
                ok: 'OK',
                afterpay: 'Afterpay',
                klarna: 'Klarna',
                affirm: 'Affirm',
                oxxo: 'OXXO',
                kakaoPay: 'Kakao Pay',
                naverPay: 'Naver Pay',
                payco: 'PAYCO',
                toss: 'Toss',
                underConstructionTitle: 'Under Construction',
                underConstructionMessage: 'This feature is currently under development. Stay tuned! We will provide you with a better experience soon.',
                underConstructionBtn: 'Got it',

                // Shopify / ShopLine plugin docs section
                shopifyDocTitle: 'Shopify Integration Guide',
                shopifyDocIntro: 'Shopify merchants can accept payments by installing the UseePay plugin. Click the cards below to view detailed integration steps.',
                shopifyEmbeddedTitle: 'Embedded Checkout',
                shopifyEmbeddedTag: 'Supported: Card',
                shopifyEmbeddedDesc: 'Embed the UseePay checkout page directly into your Shopify checkout flow for a smoother experience.',
                shopifyRedirectTitle: 'Redirect Checkout',
                shopifyRedirectTag: 'Supported: Card, Google Pay, Apple Pay',
                shopifyRedirectDesc: 'Redirect customers from Shopify checkout to the hosted UseePay checkout page to support multiple payment methods.',
                shopifyLocalizedTitle: 'Localized Checkout',
                shopifyLocalizedTag: 'Supported: Local payment methods',
                shopifyLocalizedDesc: 'Support local payment methods such as Apple Pay, Google Pay, Klarna, Afterpay, Pix, OXXO, etc.',

                shoplineDocTitle: 'ShopLine Integration Guide',
                shoplineDocIntro: 'ShopLine merchants can accept payments by installing the UseePay plugin. Click the cards below to view detailed integration steps.',
                shoplineEmbeddedTitle: 'Embedded Checkout',
                shoplineEmbeddedTag: 'Supported: Card',
                shoplineEmbeddedDesc: 'Embed the UseePay checkout page into your ShopLine checkout page for card payments.',
                shoplineRedirectTitle: 'Redirect Checkout',
                shoplineRedirectTag: 'Supported: Google Pay, Apple Pay',
                shoplineRedirectDesc: 'Redirect customers from ShopLine checkout to the hosted UseePay checkout page with Google Pay / Apple Pay.',

                pluginDocButton: 'View integration guide'
            }
        };

        // 当前语言
        let currentLang = localStorage.getItem('language') || 'zh';

        // 设置语言
        function setLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('language', lang);
            updateLanguage();
        }

        // 更新页面语言
        function updateLanguage() {
            const elements = document.querySelectorAll('[data-i18n]');
            elements.forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[currentLang][key]) {
                    el.textContent = translations[currentLang][key];
                }
            });

            // 更新语言按钮状态
            document.getElementById('langZh').classList.toggle('active', currentLang === 'zh');
            document.getElementById('langEn').classList.toggle('active', currentLang === 'en');

            // 根据当前语言更新插件文档按钮链接
            const docLinks = document.querySelectorAll('a[data-doc-slug]');
            docLinks.forEach(link => {
                const slug = link.getAttribute('data-doc-slug'); // e.g. 'shopify-embedded'
                const lang = (currentLang === 'en') ? 'en' : 'zh';
                link.href = `/plugin-docs/${lang}/${slug}`;
            });
        }

        // 页面加载时初始化语言
        document.addEventListener('DOMContentLoaded', function() {
            updateLanguage();
            // 初始化建站方式导航栏
            initBuildingMethodNav();
        });

        // 建站方式导航栏初始化
        function initBuildingMethodNav() {
            const firstBtn = document.querySelector('.building-nav-btn[data-value="selfBuilt"]');
            if (firstBtn) {
                firstBtn.classList.add('active');
            }
        }

        // 选择建站方式
        function selectBuildingMethod(button) {
            // 移除所有按钮的active类
            document.querySelectorAll('.building-nav-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // 添加active类到当前按钮
            button.classList.add('active');
            
            // 更新描述文本
            const value = button.getAttribute('data-value');
            const descElement = document.getElementById('buildingMethodDesc');
            
            const descriptions = {
                selfBuilt: currentLang === 'zh' ? translations.zh.selfBuiltDesc : translations.en.selfBuiltDesc,
                shopify: currentLang === 'zh' ? translations.zh.shopifyDesc : translations.en.shopifyDesc,
                shopline: currentLang === 'zh' ? translations.zh.shoplineDesc : translations.en.shoplineDesc
            };
            
            if (descElement) {
                descElement.textContent = descriptions[value] || '';
            }

            // 展示对应的板块内容(自建站,shopify,shopline)
            const selfBuiltSection = document.getElementById('selfBuiltSection');
            const shopifySection   = document.getElementById('shopifySection');
            const shoplineSection  = document.getElementById('shoplineSection');
            if (selfBuiltSection) {
                selfBuiltSection.style.display = (value === 'selfBuilt') ? 'block' : 'none';
            }
            if (shopifySection) {
                shopifySection.style.display   = (value === 'shopify')   ? 'block' : 'none';
            }
            if (shoplineSection) {
                shoplineSection.style.display  = (value === 'shopline')  ? 'block' : 'none';
            }

            // 保存选择到localStorage
            localStorage.setItem('selectedBuildingMethod', value);
        }

        // ===== 显示验证错误弹层 =====
        function showValidationModal(messageKey) {
            const modal = document.createElement('div');
            modal.className = 'under-construction-modal show';
            modal.style.display = 'flex';
            
            const title = translations[currentLang].validationError;
            const message = translations[currentLang][messageKey];
            const okText = translations[currentLang].ok;
            
            modal.innerHTML = `
                <div class="under-construction-modal-content">
                    <button class="under-construction-close" onclick="this.closest('.under-construction-modal').remove()">×</button>
                    <div class="under-construction-icon">⚠️</div>
                    <h2 class="under-construction-title">${title}</h2>
                    <p class="under-construction-message">${message}</p>
                    <button class="under-construction-btn" onclick="this.closest('.under-construction-modal').remove()">${okText}</button>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // 点击背景关闭
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }

        // ===== 初始化支付方式渲染 =====
        function initializePaymentMethodsCards() {
            console.log('=== Initializing Payment Methods Cards ===');
            
            // 遍历所有卡片配置
            Object.keys(paymentCardsConfig).forEach(cardKey => {
                const config = paymentCardsConfig[cardKey];
                const container = document.getElementById(config.containerId);
                
                if (!container) {
                    console.warn(`Container not found for ${cardKey}:`, config.containerId);
                    return;
                }
                
                console.log(`Rendering ${cardKey} payment methods...`);
                
                // 从浏览器缓存中获取支付方式列表
                let checkedMethods = config.defaultChecked;
                try {
                    const cachedMethods = localStorage.getItem(config.cacheKey);
                    if (cachedMethods) {
                        const parsedMethods = JSON.parse(cachedMethods);
                        if (Array.isArray(parsedMethods) && parsedMethods.length > 0) {
                            checkedMethods = parsedMethods;
                            console.log(`✓ Using cached methods for ${cardKey}:`, parsedMethods);
                        } else {
                            console.log(`⚠️ Cached methods empty for ${cardKey}, using defaults`);
                        }
                    } else {
                        console.log(`ℹ️ No cached methods for ${cardKey}, using defaults`);
                    }
                } catch (error) {
                    console.warn(`Failed to parse cached methods for ${cardKey}:`, error);
                    console.log(`Using default methods for ${cardKey}`);
                }
                
                // 渲染支付方式
                const html = renderPaymentMethods({
                    methods: config.methods,
                    inputName: config.inputName,
                    defaultChecked: checkedMethods
                });
                
                container.innerHTML = html;
                console.log(`✓ ${cardKey} payment methods rendered with ${checkedMethods.length} checked`);
            });
        }

        // 添加活动类到当前导航链接
        document.addEventListener('DOMContentLoaded', function() {
            // 初始化支付方式卡片
            initializePaymentMethodsCards();
            
            // shopify和shopline板块内容携带hash跳转，便于往回跳
            updateLanguage();
            initBuildingMethodNav();
            (function() {
                const hash = window.location.hash;
                let targetValue = 'selfBuilt';
                if (hash === '#shopify') targetValue = 'shopify';
                else if (hash === '#shopline') targetValue = 'shopline';

                const btn = document.querySelector('.building-nav-btn[data-value="' + targetValue + '"]');
                if (btn) selectBuildingMethod(btn);
            })();

            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('nav a');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
            
            // 处理支付模式选择
            // 从缓存中恢复集成模式的选中状态，如果没有缓存则默认选择跳转收银台
            function restoreIntegrationModeFromCache() {
                try {
                    const cachedIntegrationMode = localStorage.getItem('paymentIntegrationMode');
                    console.log('Cached integration mode:', cachedIntegrationMode);

                    // 默认集成模式：跳转收银台
                    const defaultMode = 'redirect';
                    let modeToUse = defaultMode;

                    if (cachedIntegrationMode) {
                        try {
                            modeToUse = cachedIntegrationMode;
                            console.log('Restoring cached integration mode:', modeToUse);
                        } catch (parseError) {
                            console.warn('Failed to parse cached integration mode, using default:', parseError);
                            modeToUse = defaultMode;
                        }
                    } else {
                        console.log('No cached integration mode, using default:', defaultMode);
                    }

                    // 先取消所有集成模式的选中
                    document.querySelectorAll('input[name="paymentMode"]').forEach(radio => {
                        radio.checked = false;
                    });

                    // 根据缓存或默认方式恢复选中状态
                    const modeRadio = document.querySelector(`input[name="paymentMode"][value="${modeToUse}"]`);
                    if (modeRadio) {
                        modeRadio.checked = true;
                        console.log(`✓ Integration mode "${modeToUse}" has been selected`);

                        // 更新样式
                        const label = modeRadio.closest('label');
                        if (label) {
                            label.style.borderColor = 'var(--success-color)';
                            label.style.background = '#f0f9f0';
                            label.style.boxShadow = '0 4px 12px rgba(76, 175, 80, 0.2)';
                        }
                    }
                } catch (e) {
                    console.error('Failed to restore integration mode from cache:', e);
                }
            }
            
            // 页面加载时恢复缓存的集成模式
            restoreIntegrationModeFromCache();
            
            const paymentModeRadios = document.querySelectorAll('input[name="paymentMode"]');
            const createPaymentBtn = document.getElementById('createPaymentBtn');
            
            // 为单选框标签添加交互效果
            paymentModeRadios.forEach(radio => {
                const label = radio.closest('label');
                
                label.addEventListener('mouseenter', function() {
                    if (!radio.checked) {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                    }
                });
                
                label.addEventListener('mouseleave', function() {
                    if (!radio.checked) {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'none';
                    }
                });
                
                radio.addEventListener('change', function() {
                    // 移除所有标签的选中样式
                    paymentModeRadios.forEach(r => {
                        const lbl = r.closest('label');
                        lbl.style.borderColor = '#dee2e6';
                        lbl.style.background = 'white';
                        lbl.style.transform = 'translateY(0)';
                        lbl.style.boxShadow = 'none';
                    });
                    // 为选中的标签添加样式
                    if (this.checked) {
                        label.style.borderColor = 'var(--success-color)';
                        label.style.background = '#f0f9f0';
                        label.style.transform = 'translateY(-2px)';
                        label.style.boxShadow = '0 4px 12px rgba(76, 175, 80, 0.2)';
                    }
                });
                
                // 初始化选中状态的样式
                if (radio.checked) {
                    label.style.borderColor = 'var(--success-color)';
                    label.style.background = '#f0f9f0';
                    label.style.boxShadow = '0 4px 12px rgba(76, 175, 80, 0.2)';
                }
            });
            
            // 更新复选框样式的函数
            function updateCheckboxStyles() {
                const allMethodCheckboxes = document.querySelectorAll('input[name="paymentMethod"], input[name="subscriptionMethod"]');
                allMethodCheckboxes.forEach(checkbox => {
                    const label = checkbox.closest('label');
                    
                    // 初始化选中状态的样式
                    if (checkbox.checked) {
                        label.style.background = '#e8f5e9';
                        label.style.borderLeft = '3px solid var(--success-color)';
                    } else {
                        label.style.background = 'white';
                        label.style.borderLeft = 'none';
                    }
                });
            }
            
            // 为支付方式和订阅方式复选框添加交互效果
            const allMethodCheckboxes = document.querySelectorAll('input[name="paymentMethod"], input[name="subscriptionMethod"]');
            allMethodCheckboxes.forEach(checkbox => {
                const label = checkbox.closest('label');
                
                label.addEventListener('mouseenter', function() {
                    this.style.background = '#e3f2fd';
                    this.style.transform = 'translateY(-1px)';
                });
                
                label.addEventListener('mouseleave', function() {
                    if (!checkbox.checked) {
                        this.style.background = 'white';
                    }
                    this.style.transform = 'translateY(0)';
                });
                
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        label.style.background = '#e8f5e9';
                        label.style.borderLeft = '3px solid var(--success-color)';
                    } else {
                        label.style.background = 'white';
                        label.style.borderLeft = 'none';
                    }
                });
                
                // 初始化选中状态的样式
                if (checkbox.checked) {
                    label.style.background = '#e8f5e9';
                    label.style.borderLeft = '3px solid var(--success-color)';
                }
            });
            
            // 恢复缓存后更新样式
            updateCheckboxStyles();
            
            // 处理按钮点击的通用函数
            function handleActionClick(actionType) {
                const selectedMode = document.querySelector('input[name="paymentMode"]:checked').value;
                console.log(`Selected ${actionType} mode:`, selectedMode);
                
                // 根据操作类型获取相应的支付方式
                let methodName = '';
                let actionText = '';
                if (actionType === 'payment') {
                    methodName = 'paymentMethod';
                    actionText = '支付';
                } else if (actionType === 'subscription') {
                    methodName = 'subscriptionMethod';
                    actionText = '订阅';
                } else if (actionType === 'installment') {
                    methodName = 'installmentMethod';
                    actionText = '分期支付';
                }
                
                const selectedMethods = Array.from(document.querySelectorAll(`input[name="${methodName}"]:checked`))
                    .map(cb => cb.value);
                console.log(`Selected ${actionType} methods:`, selectedMethods);
                
                // 验证：必须至少选择一个支付方式
                if (selectedMethods.length === 0) {
                    // 根据操作类型选择对应的国际化消息键
                    let messageKey = '';
                    if (actionType === 'payment') {
                        messageKey = 'selectAtLeastOnePaymentMethod';
                    } else if (actionType === 'subscription') {
                        messageKey = 'selectAtLeastOneSubscriptionMethod';
                    } else if (actionType === 'installment') {
                        messageKey = 'selectAtLeastOneInstallmentMethod';
                    }
                    
                    showValidationModal(messageKey);
                    console.warn(`No payment methods selected for ${actionType}`);
                    return;
                }
                
                // 根据不同模式显示不同的提示
                let message = '';
                
                switch(selectedMode) {
                    case 'redirect':
                        message = `即将跳转到 UseePay 收银台创建${actionText}...`;
                        break;
                    case 'embedded':
                        message = `正在加载内嵌收银台组件创建${actionText}...`;
                        break;
                    case 'api':
                        message = `正在初始化纯 API ${actionText}流程...`;
                        break;
                }
                
                // const methodsText = selectedMethods.length > 0
                //     ? '\n支付方式: ' + selectedMethods.join(', ')
                //     : '\n⚠️ 请至少选择一种支付方式';
                //
                // alert(message + '\n\n集成模式: ' + selectedMode + '\n操作类型: ' + actionText + methodsText);
                
                // ===== 新增：缓存处理逻辑 =====
                // 清理本地内存中的旧数据
                clearPaymentCache();
                
                // 缓存集成模式和支付方式到浏览器本地内存
                cachePaymentConfig(selectedMode, selectedMethods, actionType);
                
                // ===== 新增：支付操作跳转逻辑 =====
                // 如果是支付操作，缓存成功后跳转到服装商城页面
                if (actionType === 'payment' || actionType === 'installment') {
                    setTimeout(() => {
                        console.log('✓ 正在跳转到服装商城页面...');
                        window.location.href = '/payment/clothing-shop';
                    }, 500);
                } else if (actionType === 'subscription') {
                    setTimeout(() => {
                        console.log('✓ 正在跳转到产品订阅页面...');
                        window.location.href = '/subscription/home';
                    }, 500);
                }
            }
            
            // 清理本地内存中的支付配置缓存
            function clearPaymentCache() {
                try {
                    localStorage.removeItem('paymentIntegrationMode');
                    localStorage.removeItem('paymentActionType');
                    localStorage.removeItem('paymentCacheTimestamp');
                    console.log('✓ 本地内存缓存已清理');
                } catch (e) {
                    console.error('清理缓存失败:', e);
                }
            }
            
            // 缓存支付配置到浏览器本地内存
            function cachePaymentConfig(integrationMode, methods, actionType) {
                try {
                    const cacheData = {
                        integrationMode: integrationMode,
                        methods: methods,
                        actionType: actionType,
                        timestamp: new Date().toISOString()
                    };
                    
                    // 根据操作类型选择缓存键
                    let methodsKey = '';
                    if (actionType === 'payment') {
                        methodsKey = 'paymentMethods';
                    } else if (actionType === 'subscription') {
                        methodsKey = 'subscriptionMethods';
                    } else if (actionType === 'installment') {
                        methodsKey = 'installmentMethods';
                    }
                    
                    // 缓存集成模式
                    localStorage.setItem('paymentIntegrationMode', integrationMode);
                    
                    // 缓存支付方式
                    localStorage.setItem(methodsKey, JSON.stringify(methods));
                    
                    // 缓存操作类型
                    localStorage.setItem('paymentActionType', actionType);
                    
                    // 缓存时间戳
                    localStorage.setItem('paymentCacheTimestamp', cacheData.timestamp);
                    
                    console.log('✓ 支付配置已缓存到本地内存:', {
                        integrationMode: integrationMode,
                        methods: methods,
                        actionType: actionType,
                        timestamp: cacheData.timestamp
                    });
                    
                    // 显示缓存成功提示
                    showCacheNotification('支付配置已保存到本地内存', 'success');
                    
                } catch (e) {
                    console.error('缓存支付配置失败:', e);
                    showCacheNotification('缓存失败，请检查浏览器设置', 'error');
                }
            }
            
            // 显示缓存操作的通知
            function showCacheNotification(message, type = 'info') {
                // 创建通知元素
                const notification = document.createElement('div');
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 12px 20px;
                    border-radius: 4px;
                    font-size: 14px;
                    z-index: 10000;
                    animation: slideIn 0.3s ease-out;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                `;
                
                // 根据类型设置样式
                if (type === 'success') {
                    notification.style.background = '#4caf50';
                    notification.style.color = 'white';
                } else if (type === 'error') {
                    notification.style.background = '#f44336';
                    notification.style.color = 'white';
                } else {
                    notification.style.background = '#2196F3';
                    notification.style.color = 'white';
                }
                
                notification.textContent = message;
                document.body.appendChild(notification);
                
                // 3秒后移除通知
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease-out';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }
            
            // 添加动画样式
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes slideOut {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(400px);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
            
            // 处理创建支付按钮点击
            if (createPaymentBtn) {
                createPaymentBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    handleActionClick('payment');
                });
            }
            
            // 处理创建订阅按钮点击
            const createSubscriptionBtn = document.getElementById('createSubscriptionBtn');
            if (createSubscriptionBtn) {
                createSubscriptionBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    handleActionClick('subscription');
                });
            }
            
            // 处理创建分期支付按钮点击
            const createInstallmentBtn = document.getElementById('createInstallmentBtn');
            if (createInstallmentBtn) {
                createInstallmentBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    handleActionClick('installment');
                });
            }

            // 处理快捷支付按钮点击
            const expressCheckoutBtn = document.getElementById('expressCheckoutBtn');
            if (expressCheckoutBtn) {
                expressCheckoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    showUnderConstructionModal();
                });
            }
        });

        // Under Construction Modal Functions
        function showUnderConstructionModal() {
            const modal = document.getElementById('underConstructionModal');
            modal.classList.add('show');
        }

        function closeUnderConstructionModal() {
            const modal = document.getElementById('underConstructionModal');
            modal.classList.remove('show');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('underConstructionModal');
            if (e.target === modal) {
                closeUnderConstructionModal();
            }
        });
    </script>
</body>
</html>
