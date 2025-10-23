<?php
/**
 * Customer Creation Error View
 */
$error = $_GET['error'] ?? '创建客户时发生未知错误';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>操作失败 - UseePay</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
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

        .error-icon {
            font-size: 64px;
            color: #f44336;
            margin-bottom: 20px;
        }

        h1 {
            color: #d32f2f;
            margin-bottom: 20px;
        }

        .error-details {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-radius: 4px;
        }

        .btn-back {
            display: inline-block;
            padding: 12px 24px;
            background: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #e53935;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .suggestion {
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">✕</div>
        <h1>操作失败</h1>
        <p>很抱歉，创建客户时遇到问题：</p>
        
        <div class="error-details">
            <?= nl2br(htmlspecialchars($error)) ?>
        </div>

        <div class="suggestion">
            <p>请检查您输入的信息是否正确，或者稍后重试。</p>
            <p>如果问题仍然存在，请联系我们的支持团队。</p>
        </div>

        <div>
            <a href="javascript:history.back()" class="btn-back">返回上一页</a>
            <a href="<?= $basePath ?? '' ?>/customer" class="btn-back" style="margin-left: 10px;">重新填写</a>
            <a href="<?= $basePath ?? '' ?>/" class="btn-back" style="margin-left: 10px; background: #666;">返回首页</a>
        </div>
    </div>
</body>
</html>
