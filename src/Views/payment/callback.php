<?php
// Get language from URL parameter or default to 'zh'
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentLang = isset($_GET['lang']) ? $_GET['lang'] : 'zh';

// Translation array
$translations = [
    'zh' => [
        'page_title' => '支付结果',
        'payment_successful' => '支付成功！',
        'payment_failed' => '支付失败',
        'payment_processing' => '支付处理中',
        'payment_status' => '支付状态',
        'processing_payment' => '正在处理您的支付...',
        'payment_id' => '支付ID',
        'order_id' => '订单ID',
        'status' => '状态',
        'verified' => '已验证',
        'api_status' => 'API状态',
        'yes' => '是',
        'no' => '否',
        'error_details' => '错误详情',
        'continue_shopping' => '继续购物',
        'home' => '首页',
        'unknown' => '未知',
        'succeeded' => '成功',
        'failed' => '失败',
        'processing' => '处理中',
    ],
    'en' => [
        'page_title' => 'Payment Result',
        'payment_successful' => 'Payment Successful!',
        'payment_failed' => 'Payment Failed',
        'payment_processing' => 'Payment Processing',
        'payment_status' => 'Payment Status',
        'processing_payment' => 'Processing your payment...',
        'payment_id' => 'Payment ID',
        'order_id' => 'Order ID',
        'status' => 'Status',
        'verified' => 'Verified',
        'api_status' => 'API Status',
        'yes' => 'Yes',
        'no' => 'No',
        'error_details' => 'Error Details',
        'continue_shopping' => 'Continue Shopping',
        'home' => 'Home',
        'unknown' => 'Unknown',
        'succeeded' => 'Succeeded',
        'failed' => 'Failed',
        'processing' => 'Processing',
    ]
];

// Helper function to get translated text
function t($key) {
    global $currentLang, $translations;
    return isset($translations[$currentLang][$key]) ? $translations[$currentLang][$key] : $key;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('page_title'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .callback-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }

        .callback-icon {
            font-size: 60px;
            margin-bottom: 20px;
            animation: slideDown 0.6s ease-out;
        }

        .callback-icon.success {
            color: #10b981;
        }

        .callback-icon.error {
            color: #ef4444;
        }

        .callback-icon.warning {
            color: #f59e0b;
        }

        .callback-title {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .callback-message {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .callback-details {
            background: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: #374151;
        }

        .detail-value {
            color: #6b7280;
            word-break: break-all;
            text-align: right;
            max-width: 60%;
        }

        .detail-value.success {
            color: #10b981;
            font-weight: 500;
        }

        .detail-value.error {
            color: #ef4444;
            font-weight: 500;
        }

        .callback-actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }

        .status-badge.success {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.error {
            background: #fee2e2;
            color: #7f1d1d;
        }

        .status-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-details {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
            text-align: left;
            color: #7f1d1d;
        }

        .lang-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #667eea;
            transition: all 0.3s ease;
        }

        .lang-toggle:hover {
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 600px) {
            .callback-container {
                padding: 30px 20px;
            }

            .callback-title {
                font-size: 24px;
            }

            .callback-actions {
                flex-direction: column;
            }

            .detail-row {
                flex-direction: column;
            }

            .detail-value {
                text-align: left;
                max-width: 100%;
                margin-top: 5px;
            }

            .lang-toggle {
                top: 10px;
                right: 10px;
                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <button class="lang-toggle" onclick="toggleLanguage()" id="langToggle"><?php echo $currentLang === 'zh' ? 'EN' : '中文'; ?></button>
    
    <div class="callback-container">
        <?php
        // Get callback data from session
        $data = isset($_SESSION['payment_callback']) ? $_SESSION['payment_callback'] : [];
        
        // Determine status
        $isSuccess = isset($data['success']) && $data['success'] === true;
        $status = isset($data['status']) ? $data['status'] : 'unknown';
        $statusClass = $isSuccess ? 'success' : ($status === 'processing' ? 'warning' : 'error');
        
        // Get icon
        $icons = [
            'success' => '<i class="fas fa-check-circle"></i>',
            'error' => '<i class="fas fa-times-circle"></i>',
            'warning' => '<i class="fas fa-clock"></i>'
        ];
        $icon = isset($icons[$statusClass]) ? $icons[$statusClass] : '<i class="fas fa-question-circle"></i>';
        
        // Get title based on status
        if ($statusClass === 'success') {
            $title = t('payment_successful');
        } elseif ($statusClass === 'warning') {
            $title = t('payment_processing');
        } else {
            $title = t('payment_failed');
        }
        ?>

        <div class="callback-icon <?php echo $statusClass; ?>">
            <?php echo $icon; ?>
        </div>

        <h1 class="callback-title"><?php echo htmlspecialchars($title); ?></h1>

        <p class="callback-message">
            <?php echo htmlspecialchars(isset($data['message']) ? $data['message'] : t('processing_payment')); ?>
        </p>

        <div class="callback-details">
            <div class="detail-row">
                <span class="detail-label"><?php echo t('payment_id'); ?>:</span>
                <span class="detail-value"><?php echo htmlspecialchars(isset($data['payment_id']) ? $data['payment_id'] : 'N/A'); ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label"><?php echo t('order_id'); ?>:</span>
                <span class="detail-value"><?php echo htmlspecialchars(isset($data['merchant_order_id']) ? $data['merchant_order_id'] : 'N/A'); ?></span>
            </div>

            <div class="detail-row">
                <span class="detail-label"><?php echo t('status'); ?>:</span>
                <span class="detail-value <?php echo $statusClass; ?>">
                    <?php 
                    if (isset($data['status'])) {
                        $statusKey = str_replace('_', '', $data['status']);
                        echo htmlspecialchars(t($statusKey));
                    } else {
                        echo t('unknown');
                    }
                    ?>
                </span>
            </div>

            <?php if (isset($data['verified'])): ?>
            <div class="detail-row">
                <span class="detail-label"><?php echo t('verified'); ?>:</span>
                <span class="detail-value <?php echo $data['verified'] ? 'success' : 'error'; ?>">
                    <?php echo $data['verified'] ? t('yes') : t('no'); ?>
                </span>
            </div>
            <?php endif; ?>

            <?php if (isset($data['api_status'])): ?>
            <div class="detail-row">
                <span class="detail-label"><?php echo t('api_status'); ?>:</span>
                <span class="detail-value"><?php echo htmlspecialchars($data['api_status']); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!$isSuccess && isset($data['message'])): ?>
        <div class="error-details">
            <strong><?php echo t('error_details'); ?>:</strong><br>
            <?php echo htmlspecialchars($data['message']); ?>
        </div>
        <?php endif; ?>

        <div class="status-badge <?php echo $statusClass; ?>">
            <?php echo strtoupper($statusClass); ?>
        </div>

        <div class="callback-actions">
<!--            <a href="/payment/clothing-shop" class="btn btn-primary">-->
<!--                <i class="fas fa-shopping-bag"></i> --><?php //echo t('continue_shopping'); ?>
<!--            </a>-->
            <a href="/" class="btn btn-secondary">
                <i class="fas fa-home"></i> <?php echo t('home'); ?>
            </a>
        </div>
    </div>

    <script>
        // Initialize language from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const cachedLang = localStorage.getItem('language') || 'zh';
            const currentLang = '<?php echo $currentLang; ?>';
            
            // If cached language is different from current, redirect with correct language
            if (cachedLang !== currentLang) {
                const url = new URL(window.location);
                url.searchParams.set('lang', cachedLang);
                window.location.href = url.toString();
            }
        });

        // Log callback data for debugging
        console.log('Payment Callback Data:', <?php echo json_encode($data); ?>);

        // Toggle language
        function toggleLanguage() {
            const currentLang = '<?php echo $currentLang; ?>';
            const newLang = currentLang === 'zh' ? 'en' : 'zh';
            localStorage.setItem('language', newLang);
            const url = new URL(window.location);
            url.searchParams.set('lang', newLang);
            window.location.href = url.toString();
        }

        // Auto-redirect on success after 3 seconds (optional)
        // Uncomment to enable auto-redirect
        /*
        <?php if ($isSuccess): ?>
        setTimeout(() => {
            window.location.href = '/payment/clothing-shop';
        }, 3000);
        <?php endif; ?>
        */
    </script>
</body>
</html>
