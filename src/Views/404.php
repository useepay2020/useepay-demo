<?php
// Set the response code to 404
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 页面未找到 - UseePay Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="error-container">
        <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
            <div class="text-6xl text-red-500 mb-4">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-4">404 - 页面未找到</h1>
            <p class="text-gray-600 mb-8">抱歉，您请求的页面不存在或已被移除。</p>
            <a href="<?= $basePath ?>/" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                <i class="fas fa-home mr-2"></i>返回首页
            </a>
        </div>
    </div>
</body>
</html>
