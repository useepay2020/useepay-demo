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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #4a6bdf;
            --primary-light: #6d8bf0;
            --primary-dark: #2a4ab3;
            --success-color: #4caf50;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --text-color: #333;
            --text-light: #666;
            --bg-color: #f5f7ff;
            --card-bg: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(91, 137, 255, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(76, 175, 80, 0.1) 0%, transparent 20%);
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-right: 2rem;
        }

        .lang-toggle-header {
            position: absolute;
            top: 1.5rem;
            right: 2rem;
            display: flex;
            gap: 0.5rem;
        }

        .lang-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .lang-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .lang-btn.active {
            background: white;
            color: var(--primary-color);
            border-color: white;
        }

        header::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 100px;
            background: white;
            transform: skewY(-3deg);
            transform-origin: 100%;
            z-index: 1;
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .logo i {
            font-size: 2.8rem;
        }

        .tagline {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .main-content {
            position: relative;
            z-index: 2;
            padding: 2rem 0 4rem;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .card h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card h2 i {
            font-size: 1.5em;
        }

        .card p {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            min-height: 60px;
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-align: center;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-success {
            background: var(--success-color);
        }

        .btn-danger {
            background: var(--danger-color);
        }

        .btn-warning {
            background: var(--warning-color);
        }

        .features {
            margin-top: 3rem;
        }

        .features h3 {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
            font-size: 1.8rem;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .feature {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .feature:hover {
            transform: translateY(-5px);
        }

        .feature i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: inline-block;
        }

        .feature h4 {
            margin-bottom: 0.8rem;
            color: var(--text-color);
        }

        .feature p {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        footer {
            background: #2d3748;
            color: #a0aec0;
            padding: 3rem 0;
            margin-top: 4rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-section h3 {
            color: white;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.8rem;
        }

        .footer-section a {
            color: #a0aec0;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Under Construction Modal Styles */
        .under-construction-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }

        .under-construction-modal.show {
            display: flex;
        }

        .under-construction-modal-content {
            background: white;
            border-radius: 16px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
            animation: slideUp 0.3s ease;
        }

        .under-construction-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: transparent;
            border: none;
            font-size: 28px;
            color: #999;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .under-construction-close:hover {
            background: #f5f5f5;
            color: #333;
            transform: rotate(90deg);
        }

        .under-construction-icon {
            font-size: 80px;
            color: var(--warning-color);
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        .under-construction-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 15px;
        }

        .under-construction-message {
            font-size: 16px;
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .under-construction-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .under-construction-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 107, 223, 0.3);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
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

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        @media (max-width: 768px) {
            .cards {
                grid-template-columns: 1fr;
            }
            
            .card {
                margin: 0 1rem;
            }
            
            .feature-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }

            .under-construction-modal-content {
                padding: 30px 20px;
            }

            .under-construction-icon {
                font-size: 60px;
            }

            .under-construction-title {
                font-size: 24px;
            }
        }
    </style>
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
            <div style="background: white; border-bottom: 2px solid #e9ecef; margin: 0 -20px 1.5rem -20px; display: flex; padding-left: 20px;">
                <button class="building-nav-btn" data-value="selfBuilt" style="flex: 0 0 auto; padding: 1rem 2rem; text-align: center; background: transparent; border: none; cursor: pointer; font-size: 1rem; font-weight: 600; color: var(--text-light); transition: all 0.3s ease; border-bottom: 3px solid transparent; margin-bottom: -2px; position: relative;" onclick="selectBuildingMethod(this)">
                    <i class="fas fa-hammer" style="margin-right: 0.5rem;"></i>
                    <span data-i18n="selfBuilt">自建站</span>
                </button>
                <button class="building-nav-btn" data-value="shopify" style="flex: 0 0 auto; padding: 1rem 2rem; text-align: center; background: transparent; border: none; cursor: pointer; font-size: 1rem; font-weight: 600; color: var(--text-light); transition: all 0.3s ease; border-bottom: 3px solid transparent; margin-bottom: -2px; position: relative;" onclick="selectBuildingMethod(this)">
                    <i class="fas fa-shopping-bag" style="margin-right: 0.5rem;"></i>
                    <span>Shopify</span>
                </button>
                <button class="building-nav-btn" data-value="shopline" style="flex: 0 0 auto; padding: 1rem 2rem; text-align: center; background: transparent; border: none; cursor: pointer; font-size: 1rem; font-weight: 600; color: var(--text-light); transition: all 0.3s ease; border-bottom: 3px solid transparent; margin-bottom: -2px; position: relative;" onclick="selectBuildingMethod(this)">
                    <i class="fas fa-store" style="margin-right: 0.5rem;"></i>
                    <span>ShopLine</span>
                </button>
            </div>
            
            <style>
                .building-nav-btn.active {
                    color: var(--primary-color);
                    border-bottom-color: var(--primary-color);
                }
                
                .building-nav-btn:hover {
                    color: var(--primary-color);
                }
            </style>

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
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.8rem;">
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="card" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fas fa-credit-card" style="margin-right: 0.5rem; color: #1a73e8; font-size: 1.1rem;"></i>
                                <span style="font-size: 0.95rem;" data-i18n="card">国际信用卡/借记卡</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="apple_pay" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fab fa-apple" style="margin-right: 0.5rem; color: #000000; font-size: 1.2rem;"></i>
                                <span style="font-size: 0.95rem;">Apple Pay</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="google_pay" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fab fa-google" style="margin-right: 0.5rem; color: #4285F4; font-size: 1.1rem;"></i>
                                <span style="font-size: 0.95rem;">Google Pay</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="wechat" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fab fa-weixin" style="margin-right: 0.5rem; color: #09B83E; font-size: 1.2rem;"></i>
                                <span style="font-size: 0.95rem;" data-i18n="wechat">微信支付</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="alipay" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fab fa-alipay" style="margin-right: 0.5rem; color: #1677FF; font-size: 1.2rem;"></i>
                                <span style="font-size: 0.95rem;" data-i18n="alipay">支付宝</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="oxxo" style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fas fa-store" style="margin-right: 0.5rem; color: #EC0000; font-size: 1.1rem;"></i>
                                <span style="font-size: 0.95rem;" data-i18n="oxxo">OXXO</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="kakao_pay" style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <svg style="margin-right: 0.5rem; width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="11" fill="#FFE812"/>
                                    <path d="M12 5C8.13 5 5 7.68 5 11c0 2.04 1.23 3.82 3.04 4.63L7.84 18l3.05-1.61c.5.08 1.02.12 1.54.12 3.87 0 7-2.68 7-6s-3.13-6-7-6z" fill="#000000"/>
                                </svg>
                                <span style="font-size: 0.95rem;" data-i18n="kakaoPay">Kakao Pay</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="naver_pay" style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <svg style="margin-right: 0.5rem; width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="24" height="24" fill="#00C73C"/>
                                    <path d="M6 8h3v8H6V8zm5 0h3v8h-3V8zm5 0h3v8h-3V8z" fill="white"/>
                                </svg>
                                <span style="font-size: 0.95rem;" data-i18n="naverPay">Naver Pay</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="payco" style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <svg style="margin-right: 0.5rem; width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="24" height="24" fill="#0066FF"/>
                                    <text x="12" y="16" font-size="14" font-weight="bold" fill="white" text-anchor="middle">P</text>
                                </svg>
                                <span style="font-size: 0.95rem;" data-i18n="payco">Payco</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="toss_pay" style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <svg style="margin-right: 0.5rem; width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="24" height="24" fill="#0066FF" rx="4"/>
                                    <path d="M12 6L8 14h3v4h2v-4h3L12 6z" fill="white"/>
                                </svg>
                                <span style="font-size: 0.95rem;" data-i18n="toss">Toss</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="samsung_pay" style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <svg style="margin-right: 0.5rem; width: 24px; height: 24px;" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="512" height="512" rx="80" fill="#1428A0"/>
                                    <path d="M256 140c-50 0-90 25-90 60 0 20 15 35 40 45-30 8-50 25-50 50 0 40 45 70 100 70s100-30 100-70c0-25-20-42-50-50 25-10 40-25 40-45 0-35-40-60-90-60zm0 35c25 0 45 12 45 30s-20 30-45 30-45-12-45-30 20-30 45-30zm0 110c30 0 55 15 55 35s-25 35-55 35-55-15-55-35 25-35 55-35z" fill="white"/>
                                </svg>
                                <span style="font-size: 0.95rem;" data-i18n="samsungPay">Samsung Pay</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="paymentMethod" value="tmoney" style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <svg style="margin-right: 0.5rem; width: 24px; height: 24px;" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="tmoneyGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#FF6B00;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#FF9500;stop-opacity:1" />
                                        </linearGradient>
                                    </defs>
                                    <rect width="512" height="512" rx="80" fill="url(#tmoneyGradient)"/>
                                    <path d="M150 180h212v40H150zm106 80c-60 0-106 35-106 85 0 45 46 80 106 80s106-35 106-80c0-50-46-85-106-85zm0 125c-35 0-60-20-60-40s25-40 60-40 60 20 60 40-25 40-60 40z" fill="white"/>
                                    <circle cx="256" cy="200" r="25" fill="white"/>
                                </svg>
                                <span style="font-size: 0.95rem;" data-i18n="tmoney">T-money</span>
                            </label>
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
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.8rem;">
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="installmentMethod" value="afterpay" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fas fa-calendar-check" style="margin-right: 0.5rem; color: #B2FCE4; font-size: 1.1rem;"></i>
                                <span style="font-size: 0.95rem;" data-i18n="afterpay">Afterpay</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="installmentMethod" value="klarna" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fas fa-shopping-bag" style="margin-right: 0.5rem; color: #FFB3C7; font-size: 1.1rem;"></i>
                                <span style="font-size: 0.95rem;" data-i18n="klarna">Klarna</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="installmentMethod" value="affirm" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fas fa-check-circle" style="margin-right: 0.5rem; color: #0FA0EA; font-size: 1.1rem;"></i>
                                <span style="font-size: 0.95rem;" data-i18n="affirm">Affirm</span>
                            </label>
                            <div style="display: flex; align-items: center; justify-content: center; padding: 0.6rem; background: #f8f9fa; border-radius: 6px; border: 1px dashed #dee2e6;">
                                <i class="fas fa-ellipsis-h" style="margin-right: 0.5rem; color: #6c757d;"></i>
                                <span style="font-size: 0.9rem; color: #6c757d; font-style: italic;" data-i18n="morePaymentMethods">更多支付方式...</span>
                            </div>
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
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.8rem;">
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="subscriptionMethod" value="card" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fas fa-credit-card" style="margin-right: 0.5rem; color: #1a73e8; font-size: 1.1rem;"></i>
                                <span style="font-size: 0.95rem;" data-i18n="card">国际信用卡/借记卡</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="subscriptionMethod" value="apple_pay" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fab fa-apple" style="margin-right: 0.5rem; color: #000000; font-size: 1.2rem;"></i>
                                <span style="font-size: 0.95rem;">Apple Pay</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="subscriptionMethod" value="google_pay" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fab fa-google" style="margin-right: 0.5rem; color: #4285F4; font-size: 1.1rem;"></i>
                                <span style="font-size: 0.95rem;">Google Pay</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="subscriptionMethod" value="kakao_pay" style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <svg style="margin-right: 0.5rem; width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="11" fill="#FFE812"/>
                                    <path d="M12 5C8.13 5 5 7.68 5 11c0 2.04 1.23 3.82 3.04 4.63L7.84 18l3.05-1.61c.5.08 1.02.12 1.54.12 3.87 0 7-2.68 7-6s-3.13-6-7-6z" fill="#000000"/>
                                </svg>
                                <span style="font-size: 0.95rem;" data-i18n="kakaoPay">Kakao Pay</span>
                            </label>
                            <div style="display: flex; align-items: center; justify-content: center; padding: 0.6rem; background: #f8f9fa; border-radius: 6px; border: 1px dashed #dee2e6;">
                                <i class="fas fa-ellipsis-h" style="margin-right: 0.5rem; color: #6c757d;"></i>
                                <span style="font-size: 0.9rem; color: #6c757d; font-style: italic;" data-i18n="morePaymentMethods">更多支付方式...</span>
                            </div>
                        </div>
                    </div>
                    
                    <a href="/subscription/pricing" class="btn btn-warning" id="createSubscriptionBtn">
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
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.8rem;">
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="quickPaymentMethod" value="card" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fas fa-credit-card" style="margin-right: 0.5rem; color: #1a73e8; font-size: 1.1rem;"></i>
                                <span style="font-size: 0.95rem;" data-i18n="card">国际信用卡/借记卡</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="quickPaymentMethod" value="apple_pay" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fab fa-apple" style="margin-right: 0.5rem; color: #000000; font-size: 1.2rem;"></i>
                                <span style="font-size: 0.95rem;">Apple Pay</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="quickPaymentMethod" value="google_pay" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fab fa-google" style="margin-right: 0.5rem; color: #4285F4; font-size: 1.1rem;"></i>
                                <span style="font-size: 0.95rem;">Google Pay</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="quickPaymentMethod" value="wechat" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fab fa-weixin" style="margin-right: 0.5rem; color: #09B83E; font-size: 1.2rem;"></i>
                                <span style="font-size: 0.95rem;" data-i18n="wechat">微信支付</span>
                            </label>
                            <label style="display: flex; align-items: center; cursor: pointer; padding: 0.6rem; background: white; border-radius: 6px; transition: all 0.2s;">
                                <input type="checkbox" name="quickPaymentMethod" value="alipay" checked style="margin-right: 0.6rem; cursor: pointer; width: 16px; height: 16px;">
                                <i class="fab fa-alipay" style="margin-right: 0.5rem; color: #1677FF; font-size: 1.2rem;"></i>
                                <span style="font-size: 0.95rem;" data-i18n="alipay">支付宝</span>
                            </label>
                            <div style="display: flex; align-items: center; justify-content: center; padding: 0.6rem; background: #f8f9fa; border-radius: 6px; border: 1px dashed #dee2e6;">
                                <i class="fas fa-ellipsis-h" style="margin-right: 0.5rem; color: #6c757d;"></i>
                                <span style="font-size: 0.9rem; color: #6c757d; font-style: italic;" data-i18n="morePaymentMethods">更多支付方式...</span>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#" class="btn btn-success" id="expressCheckoutBtn">
                        <i class="fas fa-zap"></i> <span data-i18n="startExpressCheckout">开始 Expression Checkout</span>
                    </a>
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

    <script>
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
                underConstructionBtn: '知道了'
            },
            en: {
                title: 'UseePay Demo',
                tagline: 'Simple, Secure, and Efficient Payment Solutions',
                zh: '中文',
                en: 'English',
                buildingMethod: 'Select Building Method',
                selfBuilt: 'Self-Built',
                selfBuiltDesc: 'Fully customizable, independent deployment, complete control of your online business',
                shopifyDesc: 'Leading global e-commerce platform, quick launch, professional support',
                shoplineDesc: 'Leading building platform in Asia-Pacific, localized support, easy to use',
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
                underConstructionBtn: 'Got it'
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

            // 更新语言按钮的活动状态
            document.getElementById('langZh').classList.toggle('active', currentLang === 'zh');
            document.getElementById('langEn').classList.toggle('active', currentLang === 'en');
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
            
            // 保存选择到localStorage
            localStorage.setItem('selectedBuildingMethod', value);
        }

        // 添加活动类到当前导航链接
        document.addEventListener('DOMContentLoaded', function() {
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

            // 从缓存中恢复支付方式的选中状态，如果没有缓存则使用默认方式
            function restorePaymentMethodsFromCache() {
                try {
                    const cachedPaymentMethods = localStorage.getItem('paymentMethods');
                    const cachedSubscriptionMethods = localStorage.getItem('subscriptionMethods');
                    const cachedInstallmentMethods = localStorage.getItem('installmentMethods');
                    
                    // 默认支付方式：信用卡、Apple Pay、Google Pay
                    const defaultPaymentMethods = ['card', 'apple_pay', 'google_pay'];
                    
                    // 处理支付方式
                    let paymentMethodsToUse = defaultPaymentMethods;
                    if (cachedPaymentMethods) {
                        try {
                            paymentMethodsToUse = JSON.parse(cachedPaymentMethods);
                            console.log('Restoring cached payment methods:', paymentMethodsToUse);
                        } catch (parseError) {
                            console.warn('Failed to parse cached payment methods, using default:', parseError);
                            paymentMethodsToUse = defaultPaymentMethods;
                        }
                    } else {
                        console.log('No cached payment methods, using default:', defaultPaymentMethods);
                    }
                    
                    // 先取消所有支付方式的选中
                    document.querySelectorAll('input[name="paymentMethod"]').forEach(cb => {
                        cb.checked = false;
                    });
                    
                    // 根据缓存或默认方式恢复选中状态
                    paymentMethodsToUse.forEach(method => {
                        const checkbox = document.querySelector(`input[name="paymentMethod"][value="${method}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                    
                    // 处理订阅方式
                    if (cachedSubscriptionMethods) {
                        try {
                            const methods = JSON.parse(cachedSubscriptionMethods);
                            console.log('Restoring cached subscription methods:', methods);
                            
                            // 先取消所有订阅方式的选中
                            document.querySelectorAll('input[name="subscriptionMethod"]').forEach(cb => {
                                cb.checked = false;
                            });
                            
                            // 根据缓存恢复选中状态
                            methods.forEach(method => {
                                const checkbox = document.querySelector(`input[name="subscriptionMethod"][value="${method}"]`);
                                if (checkbox) {
                                    checkbox.checked = true;
                                }
                            });
                        } catch (parseError) {
                            console.warn('Failed to parse cached subscription methods:', parseError);
                        }
                    } else {
                        console.log('No cached subscription methods, using defaults');
                        // 订阅方式默认也选择相同的三种
                        document.querySelectorAll('input[name="subscriptionMethod"]').forEach(cb => {
                            cb.checked = false;
                        });
                        defaultPaymentMethods.forEach(method => {
                            const checkbox = document.querySelector(`input[name="subscriptionMethod"][value="${method}"]`);
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });
                    }
                    
                    // 处理分期支付方式
                    if (cachedInstallmentMethods) {
                        try {
                            const methods = JSON.parse(cachedInstallmentMethods);
                            console.log('Restoring cached installment methods:', methods);
                            
                            // 先取消所有分期支付方式的选中
                            document.querySelectorAll('input[name="installmentMethod"]').forEach(cb => {
                                cb.checked = false;
                            });
                            
                            // 根据缓存恢复选中状态
                            methods.forEach(method => {
                                const checkbox = document.querySelector(`input[name="installmentMethod"][value="${method}"]`);
                                if (checkbox) {
                                    checkbox.checked = true;
                                }
                            });
                        } catch (parseError) {
                            console.warn('Failed to parse cached installment methods:', parseError);
                        }
                    } else {
                        console.log('No cached installment methods, using defaults');
                        // 分期支付方式默认选择 Afterpay、Klarna 和 Affirm
                        document.querySelectorAll('input[name="installmentMethod"]').forEach(cb => {
                            cb.checked = false;
                        });
                        const defaultInstallmentMethods = ['afterpay', 'klarna', 'affirm'];
                        defaultInstallmentMethods.forEach(method => {
                            const checkbox = document.querySelector(`input[name="installmentMethod"][value="${method}"]`);
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });
                    }
                } catch (e) {
                    console.error('Failed to restore payment methods from cache:', e);
                }
            }
            
            // 页面加载时恢复缓存的集成模式和支付方式
            restoreIntegrationModeFromCache();
            restorePaymentMethodsFromCache();
            
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
                if (actionType === 'payment') {
                    methodName = 'paymentMethod';
                } else if (actionType === 'subscription') {
                    methodName = 'subscriptionMethod';
                } else if (actionType === 'installment') {
                    methodName = 'installmentMethod';
                }
                
                const selectedMethods = Array.from(document.querySelectorAll(`input[name="${methodName}"]:checked`))
                    .map(cb => cb.value);
                console.log(`Selected ${actionType} methods:`, selectedMethods);
                
                // 根据不同模式显示不同的提示
                let message = '';
                let actionText = '';
                
                if (actionType === 'payment') {
                    actionText = '支付';
                } else if (actionType === 'subscription') {
                    actionText = '订阅';
                } else if (actionType === 'installment') {
                    actionText = '分期支付';
                }
                
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
                        window.location.href = '/subscription/pricing';
                    }, 500);
                }
            }
            
            // 清理本地内存中的支付配置缓存
            function clearPaymentCache() {
                try {
                    localStorage.removeItem('paymentIntegrationMode');
                    localStorage.removeItem('paymentMethods');
                    localStorage.removeItem('subscriptionMethods');
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
