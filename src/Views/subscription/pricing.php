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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            margin-bottom: 60px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2d3436;
        }

        header h1 {
            font-size: 48px;
            color: #2d3436;
            margin-bottom: 20px;
            font-weight: 700;
        }

        header p {
            font-size: 18px;
            color: #636e72;
            margin-bottom: 30px;
        }

        .toggle-billing {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .toggle-switch {
            display: flex;
            background: #e8eef5;
            border-radius: 50px;
            padding: 4px;
            cursor: pointer;
        }

        .toggle-switch label {
            padding: 8px 20px;
            cursor: pointer;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-weight: 500;
            color: #636e72;
        }

        .toggle-switch input {
            display: none;
        }

        .toggle-switch input:checked + label {
            background: white;
            color: #2d3436;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .billing-label {
            font-size: 14px;
            color: #636e72;
        }

        .save-badge {
            background: #ff6b6b;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .pricing-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .pricing-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .pricing-card.featured {
            border: 2px solid #1e90ff;
            transform: scale(1.05);
        }

        .pricing-card.featured:hover {
            transform: scale(1.05) translateY(-8px);
        }

        .featured-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #1e90ff 0%, #00d4ff 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .plan-name {
            font-size: 24px;
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 10px;
        }

        .plan-description {
            font-size: 14px;
            color: #636e72;
            margin-bottom: 20px;
        }

        .price {
            font-size: 48px;
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 5px;
        }

        .price-currency {
            font-size: 24px;
            vertical-align: super;
        }

        .price-period {
            font-size: 14px;
            color: #636e72;
            margin-bottom: 30px;
        }

        .cta-button {
            background: linear-gradient(135deg, #1e90ff 0%, #00d4ff 100%);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(30, 144, 255, 0.3);
        }

        .cta-button.secondary {
            background: white;
            color: #1e90ff;
            border: 2px solid #1e90ff;
        }

        .cta-button.secondary:hover {
            background: #f0f8ff;
        }

        .features-list {
            flex: 1;
            margin-bottom: 30px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 14px;
            color: #2d3436;
        }

        .feature-icon {
            color: #00d4ff;
            font-size: 18px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .feature-item.disabled {
            color: #b2bec3;
        }

        .feature-item.disabled .feature-icon {
            color: #b2bec3;
        }

        .comparison-section {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 60px;
        }

        .comparison-section h2 {
            font-size: 32px;
            color: #2d3436;
            margin-bottom: 40px;
            text-align: center;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #e8eef5;
        }

        .comparison-table th {
            background: #f5f7fa;
            font-weight: 600;
            color: #2d3436;
        }

        .comparison-table tr:last-child td {
            border-bottom: none;
        }

        .comparison-table .feature-name {
            font-weight: 500;
            color: #2d3436;
            width: 30%;
        }

        .comparison-table .check {
            color: #00d4ff;
            font-size: 20px;
        }

        .comparison-table .cross {
            color: #b2bec3;
            font-size: 20px;
        }

        .faq-section {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .faq-section h2 {
            font-size: 32px;
            color: #2d3436;
            margin-bottom: 40px;
            text-align: center;
        }

        .faq-item {
            margin-bottom: 20px;
            border-bottom: 1px solid #e8eef5;
            padding-bottom: 20px;
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-question {
            font-size: 16px;
            font-weight: 600;
            color: #2d3436;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            color: #1e90ff;
        }

        .faq-question .icon {
            font-size: 14px;
            transition: transform 0.3s ease;
        }

        .faq-question.active .icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            font-size: 14px;
            color: #636e72;
            margin-top: 12px;
            display: none;
            line-height: 1.6;
        }

        .faq-answer.show {
            display: block;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #1e90ff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            gap: 12px;
            color: #00d4ff;
        }

        .register-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #1e90ff 0%, #00d4ff 100%);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .register-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(30, 144, 255, 0.3);
        }

        .register-button:active {
            transform: translateY(0);
        }

        /* Auth Modal Styles */
        .auth-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .auth-modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 40px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .modal-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #2d3436;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            color: #b2bec3;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .modal-close:hover {
            color: #2d3436;
        }

        .modal-tabs {
            display: flex;
            gap: 0;
            margin-bottom: 30px;
            border-bottom: 2px solid #e8eef5;
        }

        .modal-tab {
            flex: 1;
            padding: 12px 0;
            text-align: center;
            background: none;
            border: none;
            font-size: 14px;
            font-weight: 600;
            color: #b2bec3;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
        }

        .modal-tab.active {
            color: #1e90ff;
            border-bottom-color: #1e90ff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e8eef5;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1e90ff;
            box-shadow: 0 0 0 3px rgba(30, 144, 255, 0.1);
        }

        .submit-button {
            width: 100%;
            background: linear-gradient(135deg, #1e90ff 0%, #00d4ff 100%);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(30, 144, 255, 0.3);
        }

        .form-content {
            display: none;
        }

        .form-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 32px;
            }

            .pricing-grid {
                grid-template-columns: 1fr;
            }

            .pricing-card.featured {
                transform: scale(1);
            }

            .pricing-card.featured:hover {
                transform: translateY(-8px);
            }

            .comparison-table {
                font-size: 14px;
            }

            .comparison-table th,
            .comparison-table td {
                padding: 12px;
            }

            .comparison-table .feature-name {
                width: 40%;
            }
        }

        /* Processing Modal Styles */
        .processing-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .processing-modal.show {
            display: flex;
        }

        .processing-modal-content {
            background: white;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .processing-spinner {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            border: 4px solid #f0f0f0;
            border-top: 4px solid #1e90ff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .processing-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 10px;
        }

        .processing-message {
            font-size: 14px;
            color: #636e72;
            line-height: 1.6;
        }

        .processing-status {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e8eef5;
            font-size: 12px;
            color: #95a5a6;
        }

        .processing-status.success {
            color: #27ae60;
        }

        .processing-status.error {
            color: #e74c3c;
        }

        .status-icon {
            font-size: 24px;
            margin-right: 8px;
        }
    </style>
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

    <script>
        // ===== 国际化翻译 =====
        const translations = {
            zh: {
                backHome: '返回首页',
                selectPlan: '选择适合您的订阅计划',
                flexiblePricing: '灵活的定价选项，满足各种业务需求',
                monthlyBilling: '按月计费',
                annualBilling: '按年计费',
                saveBadge: '节省 20%',
                starter: '入门版',
                starterDesc: '适合个人和小型项目',
                professional: '专业版',
                professionalDesc: '适合成长中的企业',
                mostPopular: '最受欢迎',
                enterprise: '企业版',
                enterpriseDesc: '适合大型企业',
                selectThisPlan: '选择此计划',
                contactSales: '联系销售',
                transactions: '笔交易',
                basicPaymentMethods: '基础支付方式支持',
                allPaymentMethods: '所有支付方式支持',
                standardSupport: '标准技术支持',
                prioritySupport: '优先技术支持',
                basicAnalytics: '基础分析报告',
                advancedAnalytics: '高级分析报告',
                customAnalytics: '自定义分析报告',
                apiAccess: 'API 访问',
                fullApiAccess: '完整 API 访问',
                dedicatedSupport: '24/7 专属支持',
                accountManager: '专属账户经理',
                featureComparison: '功能对比',
                feature: '功能',
                monthly: '月度交易数',
                paymentMethods: '支付方式',
                support: '技术支持',
                analytics: '分析报告',
                accountManagement: '专属账户经理',
                unlimited: '无限',
                faq: '常见问题',
                canUpgrade: '我可以随时升级或降级我的计划吗？',
                canUpgradeAnswer: '是的，您可以随时升级或降级您的订阅计划。升级会立即生效，降级将在下一个计费周期生效。我们会按比例计算费用。',
                exceedLimit: '如果我超过了我的交易限额会怎样？',
                exceedLimitAnswer: '如果您接近交易限额，我们会向您发送通知。您可以选择升级计划或联系我们讨论自定义解决方案。我们不会在没有通知的情况下阻止交易。',
                freeTrial: '你们提供免费试用吗？',
                freeTrialAnswer: '是的，我们为所有新用户提供 14 天的免费试用。您可以在试用期间体验所有功能，无需提供信用卡信息。',
                paymentMethods2: '你们接受哪些付款方式？',
                paymentMethodsAnswer: '我们接受所有主要的信用卡（Visa、MasterCard、American Express）、支付宝、微信支付和银行转账。所有交易都是安全加密的。',
                canCancel: '我可以取消我的订阅吗？',
                canCancelAnswer: '是的，您可以随时取消订阅。取消后，您将能够访问您的账户直到当前计费周期结束。我们不收取任何取消费用。',
                standard: '标准',
                priority: '优先',
                basic: '基础',
                advanced: '高级',
                custom: '自定义',
                prioritySupport2: '优先支持',
                monthly: '月度',
                annual: '年度',
                perMonth: '/月',
                perYear: '/年',
                starterTransactions: '最多 1,000 笔交易/月',
                professionalTransactions: '最多 50,000 笔交易/月',
                unlimitedTransactions: '无限交易',
                login: '注册',
                register: '注册',
                registerTab: '注册',
                loginTab: '注册',
                email: '邮箱地址',
                password: '密码',
                confirmPassword: '确认密码',
                registerButton: '注册',
                emailPlaceholder: '请输入邮箱地址',
                passwordPlaceholder: '请输入密码',
                confirmPasswordPlaceholder: '请再次输入密码',
                personalCenter: '个人中心',
                processing: '处理中...',
                processingMessage: '正在创建您的订阅，请稍候...',
                processingSuccess: '订阅创建成功！',
                processingError: '订阅创建失败，请重试'
            },
            en: {
                backHome: 'Back to Home',
                selectPlan: 'Choose Your Subscription Plan',
                flexiblePricing: 'Flexible pricing options for all business needs',
                monthlyBilling: 'Monthly Billing',
                annualBilling: 'Annual Billing',
                saveBadge: 'Save 20%',
                starter: 'Starter',
                starterDesc: 'Perfect for individuals and small projects',
                professional: 'Professional',
                professionalDesc: 'Ideal for growing businesses',
                mostPopular: 'Most Popular',
                enterprise: 'Enterprise',
                enterpriseDesc: 'For large organizations',
                selectThisPlan: 'Select This Plan',
                contactSales: 'Contact Sales',
                transactions: 'transactions',
                basicPaymentMethods: 'Basic payment methods',
                allPaymentMethods: 'All payment methods',
                standardSupport: 'Standard support',
                prioritySupport: 'Priority support',
                basicAnalytics: 'Basic analytics',
                advancedAnalytics: 'Advanced analytics',
                customAnalytics: 'Custom analytics',
                apiAccess: 'API access',
                fullApiAccess: 'Full API access',
                dedicatedSupport: '24/7 dedicated support',
                accountManager: 'Dedicated account manager',
                featureComparison: 'Feature Comparison',
                feature: 'Feature',
                monthly: 'Monthly Transactions',
                paymentMethods: 'Payment Methods',
                support: 'Support',
                analytics: 'Analytics',
                accountManagement: 'Account Manager',
                unlimited: 'Unlimited',
                faq: 'Frequently Asked Questions',
                canUpgrade: 'Can I upgrade or downgrade my plan anytime?',
                canUpgradeAnswer: 'Yes, you can upgrade or downgrade your subscription plan at any time. Upgrades take effect immediately, while downgrades will be applied in the next billing cycle. We calculate fees proportionally.',
                exceedLimit: 'What happens if I exceed my transaction limit?',
                exceedLimitAnswer: 'If you approach your transaction limit, we will send you a notification. You can choose to upgrade your plan or contact us to discuss custom solutions. We will not block transactions without notice.',
                freeTrial: 'Do you offer a free trial?',
                freeTrialAnswer: 'Yes, we offer a 14-day free trial for all new users. You can experience all features during the trial period without providing credit card information.',
                paymentMethods2: 'What payment methods do you accept?',
                paymentMethodsAnswer: 'We accept all major credit cards (Visa, MasterCard, American Express), Alipay, WeChat Pay, and bank transfers. All transactions are securely encrypted.',
                canCancel: 'Can I cancel my subscription?',
                canCancelAnswer: 'Yes, you can cancel your subscription at any time. After cancellation, you will have access to your account until the end of the current billing cycle. We do not charge any cancellation fees.',
                standard: 'Standard',
                priority: 'Priority',
                basic: 'Basic',
                advanced: 'Advanced',
                custom: 'Custom',
                prioritySupport2: 'Priority Support',
                monthly: 'Monthly',
                annual: 'Annual',
                perMonth: '/month',
                perYear: '/year',
                starterTransactions: 'Up to 1,000 transactions/month',
                professionalTransactions: 'Up to 50,000 transactions/month',
                unlimitedTransactions: 'Unlimited transactions',
                login: 'Login',
                register: 'Register',
                registerTab: 'Register',
                loginTab: 'Login',
                email: 'Email Address',
                password: 'Password',
                confirmPassword: 'Confirm Password',
                registerButton: 'Register',
                loginButton: 'Login',
                emailPlaceholder: 'Please enter email address',
                passwordPlaceholder: 'Please enter password',
                confirmPasswordPlaceholder: 'Please enter password again',
                personalCenter: 'Personal Center',
                processing: 'Processing...',
                processingMessage: 'Creating your subscription, please wait...',
                processingSuccess: 'Subscription created successfully!',
                processingError: 'Failed to create subscription, please try again'
            }
        };

        let currentLang = localStorage.getItem('language') || 'zh';

        // Initialize language on page load
        document.addEventListener('DOMContentLoaded', function() {
            setLanguage(currentLang);
        });

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
            const currentBillingType = document.querySelector('input[name="billing"]:checked').value;
            const periodText = currentBillingType === 'annual' ? translations[lang].perYear : translations[lang].perMonth;
            document.getElementById('starter-period').innerHTML = `<span data-i18n="${currentBillingType === 'annual' ? 'perYear' : 'perMonth'}">${periodText}</span>`;
            document.getElementById('professional-period').innerHTML = `<span data-i18n="${currentBillingType === 'annual' ? 'perYear' : 'perMonth'}">${periodText}</span>`;
            document.getElementById('enterprise-period').innerHTML = `<span data-i18n="${currentBillingType === 'annual' ? 'perYear' : 'perMonth'}">${periodText}</span>`;
            
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
            if (customer) {
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
            
            // Initialize payment response handler for subscription
            const paymentHandler = new PaymentResponseHandler({
                translations: translations,
                currentLang: currentLang,
                submitButton: null,
                totals: {}
            });

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

                    // Close modal after 1.5 seconds and process payment result
                    setTimeout(() => {
                        closeProcessingModal();
                        paymentHandler.processPaymentResult(result, orderData);
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
            // Update register form
            document.querySelector('label[for="register-email"]').textContent = translations[lang].email;
            document.querySelector('label[for="register-password"]').textContent = translations[lang].password;
            document.querySelector('label[for="register-confirm-password"]').textContent = translations[lang].confirmPassword;
            document.getElementById('register-email').placeholder = translations[lang].emailPlaceholder;
            document.getElementById('register-password').placeholder = translations[lang].passwordPlaceholder;
            document.getElementById('register-confirm-password').placeholder = translations[lang].confirmPasswordPlaceholder;
            document.querySelector('#register-form .submit-button').textContent = translations[lang].registerButton;
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
    <script src="/assets/js/payment-response-handler.js"></script>

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
