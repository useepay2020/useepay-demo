<?php
/**
 * Embedded Checkout Page - 内嵌收银台
 * 基于 checkout.php，增加信用卡信息收集界面
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>内嵌收银台 - Embedded Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- UseePay SDK -->
    <script src="https://checkout-sdk.useepay.com/1.0.1/useepay.min.js"></script>
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
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            background: white;
            padding: 20px 40px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .back-button {
            background: #f1f3f5;
            color: #2d3436;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .back-button:hover {
            background: #e1e5e8;
        }

        .checkout-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        .checkout-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            color: #2d3436;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f3f5;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            font-size: 18px;
            color: #2d3436;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section h3::before {
            content: '';
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 8px;
        }

        label .required {
            color: #ff4757;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e8;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.2s;
            font-family: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .payment-methods {
            display: grid;
            gap: 15px;
        }

        .payment-option {
            position: relative;
        }

        .payment-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .payment-option label {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 2px solid #e1e5e8;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .payment-option input[type="radio"]:checked + label {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .payment-icon {
            width: 50px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .payment-info {
            flex: 1;
        }

        .payment-name {
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 3px;
        }

        .payment-desc {
            font-size: 12px;
            color: #636e72;
        }

        .card-info-section {
            display: none;
            background: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 0;
            border: 2px solid #e1e5e8;
        }

        .card-info-section.active {
            display: block;
        }

        .card-preview {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-family: 'Courier New', monospace;
        }

        .card-number {
            font-size: 22px;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        .card-details {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }

        .card-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .order-summary {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .order-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f1f3f5;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item-image {
            width: 60px;
            height: 60px;
            background: #f1f3f5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .order-item-details {
            flex: 1;
        }

        .order-item-name {
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 5px;
        }

        .order-item-price {
            font-size: 14px;
            color: #636e72;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .summary-row.total {
            font-size: 18px;
            font-weight: bold;
            color: #2d3436;
            padding-top: 12px;
            border-top: 2px solid #f1f3f5;
            margin-top: 12px;
        }

        .summary-row.total .amount {
            color: #667eea;
        }

        button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-top: 20px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.4);
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        @media (max-width: 968px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }

            .order-summary {
                position: static;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
            }

            .logo {
                font-size: 22px;
            }

            .checkout-form,
            .order-summary {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo" data-i18n="logo">🛍️ 时尚服装商城</div>
            <div style="display: flex; gap: 10px; align-items: center;">
                <a href="/" class="back-button" data-i18n="backToHome">← 返回首页</a>
                <a href="/payment/clothing-shop" class="back-button" data-i18n="backToShop">← 返回购物</a>
            </div>
        </header>

        <div class="checkout-content" id="checkoutContent">
            <!-- Content will be loaded by JavaScript -->
        </div>
    </div>

    <script>
        // Get UseePay public key from PHP config
        <?php
            global $config;
            $publicKey = $config['usee_pay']['api_public_key'];
        ?>
        window.USEEPAY_PUBLIC_KEY = '<?php echo $publicKey; ?>';
        console.log('UseePay Public Key configured:', window.USEEPAY_PUBLIC_KEY ? '✓' : '✗');
    </script>
    <!-- UseePay Elements Initializer (must be loaded first) -->
    <script src="/assets/js/useepay-elements-initializer.js"></script>
    <!-- Embedded Checkout -->
    <script src="/assets/js/embedded_checkout.js"></script>
</body>
</html>
