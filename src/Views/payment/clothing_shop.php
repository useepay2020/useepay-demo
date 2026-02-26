<?php
/**
 * Fashion Store - Clothing Shop Payment Page
 * 时尚服装商城 - 支付页面
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>时尚服装商城 - Fashion Store</title>
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
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
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

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .home-button {
            background: #f1f3f5;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #2d3436;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .home-button:hover {
            background: #e1e5e8;
        }

        .cart-button {
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cart-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .cart-count {
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        /* Products Grid */
        .products-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 32px;
            font-weight: bold;
            color: #2d3436;
            margin-bottom: 30px;
            text-align: center;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .product-image {
            width: 100%;
            height: 350px;
            object-fit: cover;
            background: #f5f5f5;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 20px;
        }

        .product-category {
            color: #667eea;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .product-name {
            font-size: 20px;
            font-weight: bold;
            color: #2d3436;
            margin-bottom: 10px;
        }

        .product-description {
            color: #636e72;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-price {
            font-size: 24px;
            font-weight: bold;
            color: #2d3436;
        }

        .add-to-cart-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .add-to-cart-btn:hover {
            transform: scale(1.05);
        }

        .add-to-cart-btn:active {
            transform: scale(0.95);
        }

        /* Cart Modal */
        .cart-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .cart-modal.active {
            display: flex;
        }

        .cart-content {
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            padding: 30px;
            position: relative;
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f3f5;
        }

        .cart-title {
            font-size: 24px;
            font-weight: bold;
            color: #2d3436;
        }

        .close-cart {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #636e72;
            transition: color 0.2s;
        }

        .close-cart:hover {
            color: #2d3436;
        }

        .cart-items {
            margin-bottom: 25px;
        }

        .cart-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            border-bottom: 1px solid #f1f3f5;
            align-items: center;
        }

        .cart-item-image {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            background: #f5f5f5;
            flex-shrink: 0;
            overflow: hidden;
        }
        
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 5px;
        }

        .cart-item-price {
            color: #667eea;
            font-weight: bold;
        }

        .cart-item-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            background: #f1f3f5;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
        }

        .quantity-btn:hover {
            background: #e1e5e8;
        }

        .quantity {
            font-weight: 600;
            min-width: 30px;
            text-align: center;
        }

        .remove-btn {
            background: #ff4757;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s;
        }

        .remove-btn:hover {
            background: #ff3838;
        }

        .cart-empty {
            text-align: center;
            padding: 40px;
            color: #636e72;
        }

        .cart-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #636e72;
        }

        .summary-row.total {
            font-size: 20px;
            font-weight: bold;
            color: #2d3436;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            margin-top: 15px;
        }

        .checkout-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s;
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
        }

        .checkout-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #2ecc71;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s;
            z-index: 2000;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
            }

            .logo {
                font-size: 22px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }

            .cart-content {
                width: 95%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo" data-i18n="logo">🛍️ 时尚服装商城</div>
            <div class="header-right">
                <button class="home-button" onclick="goHome()" title="返回首页">
                    <span>←</span>
                    <span data-i18n="home">首页</span>
                </button>
                <button class="cart-button" onclick="toggleCart()">
                    <span data-i18n="cart">购物车</span>
                    <span class="cart-count" id="cartCount">0</span>
                </button>
            </div>
        </header>

        <section class="products-section">
            <h2 class="section-title" data-i18n="featured">精选商品</h2>
            <div class="products-grid" id="productsGrid"></div>
        </section>
    </div>

    <!-- Cart Modal -->
    <div class="cart-modal" id="cartModal">
        <div class="cart-content">
            <div class="cart-header">
                <h3 class="cart-title" data-i18n="cartTitle">购物车</h3>
                <button class="close-cart" onclick="toggleCart()">×</button>
            </div>
            <div class="cart-items" id="cartItems"></div>
            <div class="cart-summary" id="cartSummary" style="display: none;">
                <div class="summary-row">
                    <span data-i18n="totalItems">商品总数：</span>
                    <span id="totalItems">0</span>
                </div>
                <div class="summary-row total">
                    <span data-i18n="total">总计：</span>
                    <span id="totalPrice">$0.00</span>
                </div>
                <button class="checkout-btn" onclick="checkout()" data-i18n="checkout">结算</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>

    <script>
        // Language translations
        const translations = {
            zh: {
                logo: '🛍️ 时尚服装商城',
                home: '首页',
                cart: '购物车',
                featured: '精选商品',
                cartTitle: '购物车',
                totalItems: '商品总数：',
                total: '总计：',
                checkout: '结算',
                addToCart: '加入购物车',
                cartEmpty: '购物车是空的<br>快去选购商品吧！',
                remove: '删除',
                addedToCart: '已加入购物车',
                products: {
                    1: { name: '经典白色T恤', category: '上装', description: '100%纯棉材质，舒适透气，经典百搭款式，适合日常休闲穿着' },
                    2: { name: '修身牛仔裤', category: '裤装', description: '高弹力牛仔面料，修身显瘦，经典蓝色水洗效果，时尚百搭' },
                    3: { name: '连帽卫衣', category: '上装', description: '加厚保暖，柔软舒适，潮流设计，多色可选，秋冬必备单品' },
                    4: { name: '运动休闲裤', category: '裤装', description: '轻薄透气，弹力舒适，适合运动和日常穿着，多功能口袋设计' },
                    5: { name: '针织开衫', category: '上装', description: '精选羊毛混纺，柔软亲肤，简约设计，适合春秋季节穿着' },
                    6: { name: '时尚风衣', category: '外套', description: '防风防水，修身剪裁，经典款式，商务休闲两相宜' },
                    7: { name: '短袖衬衫', category: '上装', description: '纯棉面料，透气舒适，商务休闲风格，多场合适用' },
                    8: { name: '休闲短裤', category: '裤装', description: '轻薄透气，舒适宽松，夏季必备单品，多色可选' }
                }
            },
            en: {
                logo: '🛍️ Fashion Store',
                home: 'Home',
                cart: 'Cart',
                featured: 'Featured Products',
                cartTitle: 'Shopping Cart',
                totalItems: 'Total Items:',
                total: 'Total:',
                checkout: 'Checkout',
                addToCart: 'Add to Cart',
                cartEmpty: 'Your cart is empty<br>Start shopping now!',
                remove: 'Remove',
                addedToCart: 'Added to cart',
                products: {
                    1: { name: 'Classic White T-Shirt', category: 'Tops', description: '100% pure cotton, comfortable and breathable, classic versatile style for daily casual wear' },
                    2: { name: 'Slim Fit Jeans', category: 'Bottoms', description: 'High-stretch denim fabric, slim fit, classic blue wash effect, fashionable and versatile' },
                    3: { name: 'Hooded Sweatshirt', category: 'Tops', description: 'Thick and warm, soft and comfortable, trendy design, multiple colors available, autumn and winter essential' },
                    4: { name: 'Athletic Casual Pants', category: 'Bottoms', description: 'Lightweight and breathable, elastic comfort, suitable for sports and daily wear, multi-functional pocket design' },
                    5: { name: 'Knit Cardigan', category: 'Tops', description: 'Selected wool blend, soft and skin-friendly, simple design, suitable for spring and autumn' },
                    6: { name: 'Fashion Trench Coat', category: 'Outerwear', description: 'Windproof and waterproof, slim cut, classic style, suitable for business and casual' },
                    7: { name: 'Short Sleeve Shirt', category: 'Tops', description: 'Pure cotton fabric, breathable and comfortable, business casual style, suitable for multiple occasions' },
                    8: { name: 'Casual Shorts', category: 'Bottoms', description: 'Lightweight and breathable, comfortable and loose, summer essential, multiple colors available' }
                }
            }
        };

        // Current language
        let currentLang = localStorage.getItem('language') || 'zh';

        // Update language
        function updateLanguage() {
            const elements = document.querySelectorAll('[data-i18n]');
            elements.forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[currentLang][key]) {
                    el.innerHTML = translations[currentLang][key];
                }
            });
        }

        // Product Data
        const products = [
            { id: 1, price: 18.99, image: '/assets/images/products/tshirt-white.jpg' },
            { id: 2, price: 42.99, image: '/assets/images/products/jeans.jpg' },
            { id: 3, price: 36.99, image: '/assets/images/products/hoodie.jpg' },
            { id: 4, price: 26.99, image: '/assets/images/products/pants-casual.jpg' },
            { id: 5, price: 56.99, image: '/assets/images/products/cardigan.jpg' },
            { id: 6, price: 99.99, image: '/assets/images/products/trench-coat.jpg' },
            { id: 7, price: 25.99, image: '/assets/images/products/shirt.jpg' },
            { id: 8, price: 21.99, image: '/assets/images/products/shorts.jpg' }
        ];

        // Shopping Cart
        let cart = [];

        // Initialize
        function init() {
            updateLanguage();
            renderProducts();
            loadCart();
            updateCartCount();
        }

        // Get product info
        function getProductInfo(productId) {
            const product = products.find(p => p.id === productId);
            const info = translations[currentLang].products[productId];
            // Merge product data (id, price, image) with translated info (name, category, description)
            return { 
                id: product.id,
                price: product.price,
                image: product.image,
                ...info 
            };
        }

        // Render Products
        function renderProducts() {
            const grid = document.getElementById('productsGrid');
            const addToCartText = translations[currentLang].addToCart;
            grid.innerHTML = products.map(product => {
                const info = getProductInfo(product.id);
                return `
                    <div class="product-card">
                        <div class="product-image">
                            <img src="${product.image}" alt="${info.name}" loading="lazy">
                        </div>
                        <div class="product-info">
                            <div class="product-category">${info.category}</div>
                            <h3 class="product-name">${info.name}</h3>
                            <p class="product-description">${info.description}</p>
                            <div class="product-footer">
                                <span class="product-price">$${product.price.toFixed(2)}</span>
                                <button class="add-to-cart-btn" onclick="addToCart(${product.id})">
                                    ${addToCartText}
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Add to Cart
        function addToCart(productId) {
            const product = getProductInfo(productId);
            const existingItem = cart.find(item => item.id === productId);

            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    ...product,
                    quantity: 1
                });
            }

            // Debug: Verify image is included in cart item
            console.log('Added to cart:', { id: product.id, name: product.name, image: product.image });

            saveCart();
            updateCartCount();
            const message = `${product.name} ${translations[currentLang].addedToCart}`;
            showToast(message);
        }

        // Remove from Cart
        function removeFromCart(productId) {
            cart = cart.filter(item => item.id !== productId);
            saveCart();
            updateCartCount();
            renderCart();
        }

        // Update Quantity
        function updateQuantity(productId, change) {
            const item = cart.find(item => item.id === productId);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    removeFromCart(productId);
                } else {
                    saveCart();
                    renderCart();
                }
            }
        }

        // Toggle Cart Modal
        function toggleCart() {
            const modal = document.getElementById('cartModal');
            modal.classList.toggle('active');
            if (modal.classList.contains('active')) {
                renderCart();
            }
        }

        // Render Cart
        function renderCart() {
            const cartItems = document.getElementById('cartItems');
            const cartSummary = document.getElementById('cartSummary');
            const removeText = translations[currentLang].remove;

            if (cart.length === 0) {
                cartItems.innerHTML = `<div class="cart-empty">${translations[currentLang].cartEmpty}</div>`;
                cartSummary.style.display = 'none';
                return;
            }

            // Debug: Check cart items
            console.log('Rendering cart with items:', cart.map(item => ({ id: item.id, image: item.image })));

            cartItems.innerHTML = cart.map(item => {
                const info = getProductInfo(item.id);
                // Use item.image from cart data (already saved)
                const imageUrl = item.image || '/assets/images/products/placeholder.jpg';
                return `
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="${imageUrl}" alt="${info.name}" onerror="this.src='/assets/images/products/placeholder.jpg'">
                        </div>
                        <div class="cart-item-info">
                            <div class="cart-item-name">${info.name}</div>
                            <div class="cart-item-price">$${item.price.toFixed(2)}</div>
                        </div>
                        <div class="cart-item-controls">
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                            <span class="quantity">${item.quantity}</span>
                            <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                            <button class="remove-btn" onclick="removeFromCart(${item.id})">${removeText}</button>
                        </div>
                    </div>
                `;
            }).join('');

            // Update Summary
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalPrice').textContent = `$${totalPrice.toFixed(2)}`;
            cartSummary.style.display = 'block';
        }

        // Update Cart Count
        function updateCartCount() {
            const count = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cartCount').textContent = count;
        }

        // Save Cart to LocalStorage
        function saveCart() {
            localStorage.setItem('fashionCart', JSON.stringify(cart));
        }

        // Load Cart from LocalStorage
        function loadCart() {
            const saved = localStorage.getItem('fashionCart');
            if (saved) {
                cart = JSON.parse(saved);
            }
        }

        // Show Toast Notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 2000);
        }

        // Go Home
        function goHome() {
            // 返回首页
            window.location.href = '/';
        }

        // Checkout - 根据缓存的集成模式进行跳转
        function checkout() {
            if (cart.length === 0) return;

            // 从 localStorage 读取集成模式
            const integrationMode = localStorage.getItem('paymentIntegrationMode');
            console.log('Integration mode:', integrationMode);

            let checkoutUrl = '/payment/checkout'; // 默认跳转收银台

            // 根据集成模式选择对应的结算页面
            if (integrationMode === 'embedded') {
                checkoutUrl = '/payment/embedded_checkout';
                console.log('Redirecting to embedded checkout');
            } else if (integrationMode === 'api') {
                checkoutUrl = '/payment/api_checkout';
                console.log('Redirecting to API checkout');
            } else if (integrationMode === 'redirect') {
                checkoutUrl = '/payment/checkout';
                console.log('Redirecting to redirect checkout');
            } else {
                // 如果没有缓存或其他模式，默认使用跳转收银台
                console.log('No integration mode found, using default checkout');
            }

            // 跳转到对应的结算页面
            window.location.href = checkoutUrl;
        }

        // Close modal when clicking outside
        document.getElementById('cartModal').addEventListener('click', function(e) {
            if (e.target === this) {
                toggleCart();
            }
        });

        // Initialize on page load
        init();
    </script>
</body>
</html>
