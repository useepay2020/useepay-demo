<?php
/**
 * Subscription Checkout Page
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订阅结账 - UseePay Demo</title>
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
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        header {
            grid-column: 1 / -1;
            margin-bottom: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2d3436;
            margin-bottom: 10px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #1e90ff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            gap: 12px;
            color: #00d4ff;
        }

        .checkout-form {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            font-size: 18px;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section h3 i {
            color: #1e90ff;
            font-size: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            padding: 12px;
            border: 1px solid #e8eef5;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #1e90ff;
            box-shadow: 0 0 0 3px rgba(30, 144, 255, 0.1);
        }

        .order-summary {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            height: fit-content;
            position: sticky;
            top: 40px;
        }

        .order-summary h3 {
            font-size: 20px;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 30px;
        }

        .plan-details {
            background: #f5f7fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .plan-name {
            font-size: 16px;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 10px;
        }

        .plan-info {
            font-size: 14px;
            color: #636e72;
            margin-bottom: 15px;
        }

        .plan-info span {
            display: block;
            margin-bottom: 5px;
        }

        .summary-divider {
            height: 1px;
            background: #e8eef5;
            margin: 20px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #636e72;
            margin-bottom: 12px;
        }

        .summary-row.total {
            font-size: 18px;
            font-weight: 600;
            color: #2d3436;
            margin-top: 20px;
        }

        .summary-row.total .amount {
            color: #1e90ff;
            font-size: 24px;
        }

        .payment-methods {
            margin-bottom: 30px;
        }

        .payment-methods h4 {
            font-size: 14px;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 15px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border: 2px solid #e8eef5;
            border-radius: 8px;
            cursor: pointer;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            border-color: #1e90ff;
            background: #f0f8ff;
        }

        .payment-option input[type="radio"] {
            cursor: pointer;
        }

        .payment-option input[type="radio"]:checked + label {
            color: #1e90ff;
            font-weight: 600;
        }

        .payment-option label {
            cursor: pointer;
            flex: 1;
            font-size: 14px;
            color: #636e72;
        }

        .subscribe-button {
            width: 100%;
            background: linear-gradient(135deg, #1e90ff 0%, #00d4ff 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .subscribe-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(30, 144, 255, 0.3);
        }

        .subscribe-button:active {
            transform: translateY(0);
        }

        .terms {
            font-size: 12px;
            color: #b2bec3;
            text-align: center;
            margin-top: 15px;
        }

        .terms a {
            color: #1e90ff;
            text-decoration: none;
        }

        .terms a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .order-summary {
                position: static;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">💳 UseePay Demo</div>
            <a href="/subscription/pricing" class="back-button">
                <i class="fas fa-arrow-left"></i>
                返回定价页面
            </a>
        </header>

        <div class="checkout-form">
            <form id="subscriptionForm" onsubmit="handleSubmit(event)">
                <!-- Billing Information -->
                <div class="form-section">
                    <h3>
                        <i class="fas fa-user"></i>
                        账户信息
                    </h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">名字 *</label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">姓氏 *</label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                    </div>
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="email">邮箱地址 *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="phone">电话号码</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                    </div>
                </div>

                <!-- Billing Address -->
                <div class="form-section">
                    <h3>
                        <i class="fas fa-map-marker-alt"></i>
                        账单地址
                    </h3>
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="address">街道地址 *</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">城市 *</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="state">州/省 *</label>
                            <input type="text" id="state" name="state" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="zipCode">邮编 *</label>
                            <input type="text" id="zipCode" name="zipCode" required>
                        </div>
                        <div class="form-group">
                            <label for="country">国家 *</label>
                            <input type="text" id="country" name="country" required>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="form-section">
                    <h3>
                        <i class="fas fa-credit-card"></i>
                        支付方式
                    </h3>
                    <div class="payment-methods">
                        <div class="payment-option">
                            <input type="radio" id="card" name="paymentMethod" value="card" checked>
                            <label for="card">
                                <i class="fas fa-credit-card"></i>
                                信用卡 / 借记卡
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="alipay" name="paymentMethod" value="alipay">
                            <label for="alipay">
                                <i class="fas fa-mobile-alt"></i>
                                支付宝
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="wechat" name="paymentMethod" value="wechat">
                            <label for="wechat">
                                <i class="fas fa-mobile-alt"></i>
                                微信支付
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="subscribe-button">
                    <i class="fas fa-lock"></i>
                    立即订阅
                </button>

                <div class="terms">
                    点击"立即订阅"即表示您同意我们的
                    <a href="#">服务条款</a>
                    和
                    <a href="#">隐私政策</a>
                </div>
            </form>
        </div>

        <div class="order-summary">
            <h3>订阅摘要</h3>

            <div class="plan-details">
                <div class="plan-name" id="planName">专业版</div>
                <div class="plan-info">
                    <span>
                        <strong>计费周期：</strong>
                        <span id="billingCycle">月度</span>
                    </span>
                    <span>
                        <strong>开始日期：</strong>
                        <span id="startDate">今天</span>
                    </span>
                    <span>
                        <strong>自动续期：</strong>
                        <span>是</span>
                    </span>
                </div>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row">
                <span>订阅费用</span>
                <span id="planPrice">¥299.00</span>
            </div>
            <div class="summary-row">
                <span>税费 (8%)</span>
                <span id="taxAmount">¥23.92</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row total">
                <span>总计</span>
                <span class="amount" id="totalAmount">¥322.92</span>
            </div>

            <div style="background: #f0f8ff; border-radius: 8px; padding: 12px; margin-top: 20px; font-size: 12px; color: #636e72;">
                <i class="fas fa-info-circle" style="color: #1e90ff; margin-right: 8px;"></i>
                您可以在任何时间取消订阅。取消后，您将能够访问您的账户直到当前计费周期结束。
            </div>
        </div>
    </div>

    <script>
        // Set today's date
        const today = new Date();
        const dateStr = today.toLocaleDateString('zh-CN', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('startDate').textContent = dateStr;

        function handleSubmit(event) {
            event.preventDefault();

            const formData = {
                firstName: document.getElementById('firstName').value,
                lastName: document.getElementById('lastName').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value,
                city: document.getElementById('city').value,
                state: document.getElementById('state').value,
                zipCode: document.getElementById('zipCode').value,
                country: document.getElementById('country').value,
                paymentMethod: document.querySelector('input[name="paymentMethod"]:checked').value,
                plan: new URLSearchParams(window.location.search).get('plan') || 'professional',
                billing: new URLSearchParams(window.location.search).get('billing') || 'monthly'
            };

            console.log('Subscription form data:', formData);
            alert('订阅表单已提交！\n\n' + JSON.stringify(formData, null, 2));

            // 这里可以发送到后端进行处理
            // fetch('/api/subscription', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify(formData)
            // })
        }
    </script>
</body>
</html>
