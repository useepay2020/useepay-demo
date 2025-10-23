<?php
/**
 * Customer Creation Success View
 */
$id = $_GET['id'] ?? '';
$name = $_GET['name'] ?? '';
$email = $_GET['email'] ?? '';
$phone = $_GET['phone'] ?? '';
$merchantCustomerId = $_GET['merchantCustomerId'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>客户创建成功 - UseePay</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #333;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }

        .success-icon {
            font-size: 64px;
            color: #4caf50;
            margin-bottom: 20px;
        }

        h1 {
            color: #2e7d32;
            margin-bottom: 20px;
        }

        .customer-info {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            width: 150px;
            color: #666;
        }

        .info-value {
            flex: 1;
        }

        .btn-back {
            display: inline-block;
            padding: 12px 24px;
            background: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #43a047;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1>客户创建成功！</h1>
        <p>您已成功创建新客户，以下是客户信息：</p>
        
        <div class="customer-info">
            <div class="info-row">
                <div class="info-label">客户ID：</div>
                <div class="info-value"><?= htmlspecialchars($id) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">客户姓名：</div>
                <div class="info-value"><?= htmlspecialchars($name) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">电子邮箱：</div>
                <div class="info-value"><?= htmlspecialchars($email) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">电话号码：</div>
                <div class="info-value"><?= htmlspecialchars($phone) ?></div>
            </div>
            <?php if (!empty($merchantCustomerId)): ?>
            <div class="info-row">
                <div class="info-label">商户客户ID：</div>
                <div class="info-value"><?= htmlspecialchars($merchantCustomerId) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <div>
            <a href="<?= $basePath ?? '' ?>/" class="btn-back">返回首页</a>
            <a href="<?= $basePath ?? '' ?>/customer" class="btn-back" style="margin-left: 10px;">创建新客户</a>
        </div>
    </div>
</body>
</html>
