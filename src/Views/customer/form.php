<?php
/**
 * Customer Form View
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>创建客户 - UseePay</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
            text-align: center;
        }

        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .required {
            color: #e74c3c;
            margin-left: 2px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }

        input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        input::placeholder {
            color: #aaa;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.show {
            display: block;
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .input-hint {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>创建客户</h1>
        <p class="subtitle">填写客户信息以创建新客户</p>

        <div id="message" class="message"></div>

        <form id="customerForm">
            <div class="form-group">
                <label for="name">客户姓名 <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="张三"
                    placeholder="请输入客户姓名" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">电子邮箱 <span class="required">*</span></label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="zhangsan@example.com"
                    placeholder="example@email.com" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="phone">电话号码 <span class="required">*</span></label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    value="13800138000"
                    placeholder="请输入电话号码" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="merchantCustomerId">商户客户ID</label>
                <input 
                    type="text" 
                    id="merchantCustomerId" 
                    name="merchantCustomerId" 
                    value="CUST_TEST_<?= time() ?>"
                    placeholder="留空将自动生成"
                >
                <div class="input-hint">可选字段，留空系统将自动生成唯一ID</div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                提交创建客户
            </button>
        </form>
    </div>

    <script>
        const form = document.getElementById('customerForm');
        const submitBtn = document.getElementById('submitBtn');
        const messageDiv = document.getElementById('message');
        const baseUrl = window.location.origin + '<?php echo isset($basePath) ? $basePath : ''; ?>';

        function showMessage(text, type) {
            messageDiv.textContent = text;
            messageDiv.className = 'message ' + type + ' show';
            setTimeout(() => {
                messageDiv.classList.remove('show');
            }, 5000);
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading"></span>正在提交...';

            try {
                // Get form data
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });

                // Log the request for debugging
                console.log('Submitting to:', baseUrl + '/api/customers/create');
                console.log('Request data:', data);

                const response = await fetch(baseUrl + '/api/customers/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                // First check if the response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response:', text);
                    throw new Error('服务器返回了非JSON格式的响应');
                }
                const result = await response.json();
                if (result.success) {
                    // Show success message
                    showMessage('客户创建成功！正在跳转...', 'success');
                    
                    // Redirect to success page after a short delay
                    setTimeout(() => {
                        window.location.href = baseUrl + '/customer/success?' + new URLSearchParams({
                            id: result.data?.id || '',
                            name: result.data?.name || result.data?.firstName || '',
                            email: result.data?.email || '',
                            phone: result.data?.phone || '',
                            merchantCustomerId: result.data?.merchantCustomerId || ''
                        }).toString();
                    }, 1500);
                } else {
                    // 显示错误信息
                    const errorMsg = result.data?.error?.message || result.message || '创建客户失败';
                    showMessage('错误: ' + errorMsg, 'error');
                    
                    // 跳转到错误页面
                    setTimeout(() => {
                        window.location.href = baseUrl + '/customer/error?' + new URLSearchParams({
                            error: errorMsg
                        }).toString();
                    }, 2000);
                }
            } catch (error) {
                console.error('Error:', error);
                const errorMessage = error.message || '请求失败，请稍后重试';
                showMessage('错误: ' + errorMessage, 'error');
                
                // Re-enable submit button on error
                submitBtn.disabled = false;
                submitBtn.innerHTML = '提交创建客户';
            }
        });
    </script>
</body>
</html>
