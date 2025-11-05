/**
 * Checkout Page Renderer - 结算页面渲染器
 * 负责渲染 checkout 页面的 DOM 结构
 */

/**
 * Show custom alert modal
 * @param {string} message - Message to display
 * @param {string} type - Alert type: 'error', 'warning', 'info', 'success'
 */
function showAlertModal(message, type = 'error') {
    // Remove existing modal if any
    const existingModal = document.getElementById('customAlertModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Create modal HTML
    const modal = document.createElement('div');
    modal.id = 'customAlertModal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        animation: fadeIn 0.2s ease-in-out;
    `;

    const iconMap = {
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️',
        success: '✅'
    };

    const colorMap = {
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8',
        success: '#28a745'
    };

    modal.innerHTML = `
        <div style="
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease-out;
        ">
            <div style="
                text-align: center;
                font-size: 48px;
                margin-bottom: 20px;
            ">${iconMap[type] || iconMap.error}</div>
            <div style="
                font-size: 16px;
                line-height: 1.6;
                color: #333;
                text-align: center;
                margin-bottom: 25px;
                white-space: pre-line;
            ">${message}</div>
            <button id="alertModalClose" style="
                width: 100%;
                padding: 12px 24px;
                background: ${colorMap[type] || colorMap.error};
                color: white;
                border: none;
                border-radius: 6px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            " onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                确定 / OK
            </button>
        </div>
    `;

    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);

    document.body.appendChild(modal);

    // Close modal on button click
    const closeButton = document.getElementById('alertModalClose');
    closeButton.addEventListener('click', () => {
        modal.style.animation = 'fadeOut 0.2s ease-in-out';
        setTimeout(() => {
            modal.remove();
            style.remove();
        }, 200);
    });

    // Close modal on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeButton.click();
        }
    });

    // Close modal on ESC key
    const escHandler = (e) => {
        if (e.key === 'Escape') {
            closeButton.click();
            document.removeEventListener('keydown', escHandler);
        }
    };
    document.addEventListener('keydown', escHandler);
}

/**
 * Checkout Renderer Class
 */
class CheckoutRenderer {
    constructor(options) {
        this.translations = options.translations;
        this.currentLang = options.currentLang;
        this.cart = options.cart;
        this.paymentMethodsMap = options.paymentMethodsMap;
        this.getPaymentMethods = options.getPaymentMethods;
        this.calculateTotals = options.calculateTotals;
        this.getProductName = options.getProductName;
        this.handleSubmit = options.handleSubmit;
        this.renderPaymentMethodSection = options.renderPaymentMethodSection; // 外部传入的支付方式渲染函数
    }

    /**
     * Render empty cart view
     * @param {HTMLElement} container - Container element
     */
    renderEmptyCart(container) {
        const t = this.translations[this.currentLang];
        container.innerHTML = `
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <div class="empty-cart-text">${t.cartEmpty}</div>
                <a href="/payment/clothing-shop" class="shop-button">${t.startShopping}</a>
            </div>
        `;
    }

    /**
     * Generate payment methods HTML
     * @returns {string} Payment methods HTML
     */
    generatePaymentMethods() {
        const t = this.translations[this.currentLang];
        const cachedMethods = this.getPaymentMethods();
        console.log('Cached payment methods:', cachedMethods);
        
        // 获取所有支付方式
        let methodsToDisplay = [];
        
        // 如果有缓存的支付方式，先显示缓存的方式
        if (cachedMethods && cachedMethods.length > 0) {
            methodsToDisplay = [...cachedMethods];
            console.log('Using cached methods:', methodsToDisplay);
        } else {
            // 如果没有缓存，使用默认的前两个支付方式
            methodsToDisplay = ['card', 'apple_pay'];
            console.log('No cached methods, using default methods:', methodsToDisplay);
        }
        
        // 生成支付选项 HTML
        return methodsToDisplay.map((method, index) => {
            const methodInfo = this.paymentMethodsMap[method];
            if (!methodInfo) {
                console.warn('Unknown payment method:', method);
                return '';
            }
            
            const methodName = this.currentLang === 'zh' ? methodInfo.name_zh : methodInfo.name_en;
            const methodDesc = this.currentLang === 'zh' ? methodInfo.desc_zh : methodInfo.desc_en;
            const isFirst = index === 0; // 第一个支付方式默认选中
            
            return `
                <div class="payment-option">
                    <input type="radio" id="method_${method}" name="paymentMethod" value="${method}" ${isFirst ? 'checked' : ''}>
                    <label for="method_${method}">
                        <div class="payment-icon" style="font-size: 1.2rem;">${methodInfo.icon}</div>
                        <div class="payment-info">
                            <div class="payment-name">${methodName}</div>
                            <div class="payment-desc">${methodDesc}</div>
                        </div>
                    </label>
                </div>
            `;
        }).join('');
    }

    /**
     * Render customer information section
     * @returns {string} Customer info HTML
     */
    renderCustomerInfo() {
        const t = this.translations[this.currentLang];
        return `
            <div class="form-section">
                <h3>${t.customerInfo}</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>${t.firstName} <span class="required">${t.required}</span></label>
                        <input type="text" id="firstName" name="firstName" value="John" required>
                    </div>
                    <div class="form-group">
                        <label>${t.lastName} <span class="required">${t.required}</span></label>
                        <input type="text" id="lastName" name="lastName" value="Smith" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>${t.email} <span class="required">${t.required}</span></label>
                    <input type="email" id="email" name="email" value="john.smith@example.com" required>
                </div>
            </div>
        `;
    }

    /**
     * Render shipping address section
     * @returns {string} Shipping address HTML
     */
    renderShippingAddress() {
        const t = this.translations[this.currentLang];
        return `
            <div class="form-section">
                <h3>${t.shippingAddress}</h3>
                <div class="form-group">
                    <label>${t.address} <span class="required">${t.required}</span></label>
                    <input type="text" id="address" name="address" placeholder="${t.addressPlaceholder}" value="1234 Elm Street, Apt 5B" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>${t.city} <span class="required">${t.required}</span></label>
                        <input type="text" id="city" name="city" value="Los Angeles" required>
                    </div>
                    <div class="form-group">
                        <label>${t.state} <span class="required">${t.required}</span></label>
                        <input type="text" id="state" name="state" value="California" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>${t.zipCode} <span class="required">${t.required}</span></label>
                        <input type="text" id="zipCode" name="zipCode" value="90001" required>
                    </div>
                    <div class="form-group">
                        <label>${t.country} <span class="required">${t.required}</span></label>
                        <select id="country" name="country" required>
                            <option value="">${t.selectCountry}</option>
                            <option value="US" selected>美国 (United States)</option>
                            <option value="CN">中国 (China)</option>
                            <option value="UK">英国 (United Kingdom)</option>
                            <option value="CA">加拿大 (Canada)</option>
                            <option value="AU">澳大利亚 (Australia)</option>
                            <option value="JP">日本 (Japan)</option>
                            <option value="KR">韩国 (South Korea)</option>
                            <option value="DE">德国 (Germany)</option>
                            <option value="FR">法国 (France)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>${t.phone}</label>
                    <input type="tel" id="phone" name="phone" value="+1 (323) 555-0123">
                </div>
            </div>
        `;
    }

    /**
     * Render billing address section
     * @returns {string} Billing address HTML
     */
    renderBillingAddress() {
        const t = this.translations[this.currentLang];
        return `
            <div class="form-section">
                <h3>${t.billingAddress}</h3>
                <div class="same-as-shipping-wrapper">
                    <label>
                        <input type="checkbox" id="sameAsShipping" name="sameAsShipping" checked>
                        <span>${t.sameAsShipping}</span>
                    </label>
                </div>
                <div class="billing-address-section hidden" id="billingAddressSection">
                    <div class="form-group">
                        <label>${t.address} <span class="required">${t.required}</span></label>
                        <input type="text" id="billingAddress" name="billingAddress" placeholder="${t.addressPlaceholder}" value="1234 Elm Street, Apt 5B">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>${t.city} <span class="required">${t.required}</span></label>
                            <input type="text" id="billingCity" name="billingCity" value="Los Angeles">
                        </div>
                        <div class="form-group">
                            <label>${t.state} <span class="required">${t.required}</span></label>
                            <input type="text" id="billingState" name="billingState" value="California">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>${t.zipCode} <span class="required">${t.required}</span></label>
                            <input type="text" id="billingZipCode" name="billingZipCode" value="90001">
                        </div>
                        <div class="form-group">
                            <label>${t.country} <span class="required">${t.required}</span></label>
                            <select id="billingCountry" name="billingCountry">
                                <option value="">${t.selectCountry}</option>
                                <option value="US" selected>美国 (United States)</option>
                                <option value="CN">中国 (China)</option>
                                <option value="UK">英国 (United Kingdom)</option>
                                <option value="CA">加拿大 (Canada)</option>
                                <option value="AU">澳大利亚 (Australia)</option>
                                <option value="JP">日本 (Japan)</option>
                                <option value="KR">韩国 (South Korea)</option>
                                <option value="DE">德国 (Germany)</option>
                                <option value="FR">法国 (France)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Render payment method section
     * @returns {string} Payment method HTML
     */
    renderPaymentMethod() {
        // 调用外部传入的支付方式渲染函数
        if (this.renderPaymentMethodSection) {
            return this.renderPaymentMethodSection(
                this.translations[this.currentLang],
                this.generatePaymentMethods.bind(this)
            );
        }
        
        // 如果没有传入外部函数，返回空字符串
        console.warn('renderPaymentMethodSection function not provided');
        return '';
    }

    /**
     * Render order items
     * @returns {string} Order items HTML
     */
    renderOrderItems() {
        const t = this.translations[this.currentLang];
        return this.cart.map(item => `
            <div class="order-item">
                <div class="order-item-image">${item.image}</div>
                <div class="order-item-info">
                    <div class="order-item-name">${this.getProductName(item.id, this.currentLang)}</div>
                    <div class="order-item-details">
                        <span>${t.quantity}: ${item.quantity}</span>
                        <span class="order-item-price">$${(item.price * item.quantity).toFixed(2)}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    /**
     * Render order summary
     * @param {Object} totals - Order totals
     * @returns {string} Order summary HTML
     */
    renderOrderSummary(totals) {
        const t = this.translations[this.currentLang];
        return `
            <div class="order-summary">
                <h2 class="section-title">${t.orderSummary}</h2>
                <div id="orderItems">
                    ${this.renderOrderItems()}
                </div>
                <div class="order-totals">
                    <div class="total-row">
                        <span>${t.subtotal}</span>
                        <span>$${totals.subtotal}</span>
                    </div>
                    <div class="total-row">
                        <span>${t.shipping}</span>
                        <span>$${totals.shipping}</span>
                    </div>
                    <div class="total-row">
                        <span>${t.tax}</span>
                        <span>$${totals.tax}</span>
                    </div>
                    <div class="total-row grand-total">
                        <span>${t.orderTotal}</span>
                        <span>$${totals.totalAmount}</span>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Render checkout form
     * @param {Object} totals - Order totals
     * @returns {string} Checkout form HTML
     */
    renderCheckoutForm(totals) {
        const t = this.translations[this.currentLang];
        return `
            <div class="checkout-form">
                <h2 class="section-title">${t.checkoutInfo}</h2>
                
                <form id="checkoutForm">
                    ${this.renderCustomerInfo()}
                    ${this.renderShippingAddress()}
                    ${this.renderBillingAddress()}
                    ${this.renderPaymentMethod()}

                    <button type="submit" class="submit-button" id="submitButton">
                        ${t.confirmPay} $${totals.totalAmount}
                    </button>
                </form>
            </div>
        `;
    }

    /**
     * Setup event handlers after rendering
     */
    setupEventHandlers() {
        // Add form submit handler
        const checkoutForm = document.getElementById('checkoutForm');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', this.handleSubmit);
        }

        // Add one-page checkout toggle handler
        const onePageCheckbox = document.getElementById('onePageCheckout');
        const paymentMethodsList = document.getElementById('paymentMethodsContainer');
        
        if (onePageCheckbox && paymentMethodsList) {
            // Set initial state based on checkbox
            const updatePaymentMethodsVisibility = () => {
                if (onePageCheckbox.checked) {
                    paymentMethodsList.classList.remove('hidden');
                } else {
                    paymentMethodsList.classList.add('hidden');
                }
            };
            
            // Initialize visibility
            updatePaymentMethodsVisibility();
            
            // Add change event listener
            onePageCheckbox.addEventListener('change', updatePaymentMethodsVisibility);
        }

        // Add billing address toggle handler
        const sameAsShippingCheckbox = document.getElementById('sameAsShipping');
        const billingAddressSection = document.getElementById('billingAddressSection');
        
        if (sameAsShippingCheckbox && billingAddressSection) {
            // Set initial state based on checkbox
            const updateBillingAddressVisibility = () => {
                if (sameAsShippingCheckbox.checked) {
                    billingAddressSection.classList.add('hidden');
                    // Remove required attribute from billing address fields
                    document.getElementById('billingAddress')?.removeAttribute('required');
                    document.getElementById('billingCity')?.removeAttribute('required');
                    document.getElementById('billingState')?.removeAttribute('required');
                    document.getElementById('billingZipCode')?.removeAttribute('required');
                    document.getElementById('billingCountry')?.removeAttribute('required');
                } else {
                    billingAddressSection.classList.remove('hidden');
                    // Add required attribute to billing address fields
                    document.getElementById('billingAddress')?.setAttribute('required', 'required');
                    document.getElementById('billingCity')?.setAttribute('required', 'required');
                    document.getElementById('billingState')?.setAttribute('required', 'required');
                    document.getElementById('billingZipCode')?.setAttribute('required', 'required');
                    document.getElementById('billingCountry')?.setAttribute('required', 'required');
                }
            };
            
            // Initialize visibility
            updateBillingAddressVisibility();
            
            // Add change event listener
            sameAsShippingCheckbox.addEventListener('change', updateBillingAddressVisibility);
        }
    }

    /**
     * Calculate order totals
     * @param {Array} cart - Shopping cart items
     * @returns {Object} Order totals object
     */
    static calculateTotals(cart) {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const shipping = subtotal > 0 ? 9.99 : 0;
        const tax = subtotal * 0.08; // 8% tax
        const totalAmount = subtotal + shipping + tax;

        return {
            subtotal: subtotal.toFixed(2),
            shipping: shipping.toFixed(2),
            tax: tax.toFixed(2),
            totalAmount: totalAmount.toFixed(2),
            currency: 'USD'
        };
    }

    /**
     * Validate checkout form data
     * @param {Object} data - Form data object
     * @param {Object} translations - Translations object
     * @param {string} currentLang - Current language
     * @returns {boolean} True if valid, false otherwise
     */
    static validateForm(data, translations, currentLang) {
        // Validate customer information
        if (!data.firstName || !data.lastName || !data.email) {
            showAlertModal(translations[currentLang].fillCustomerInfo, 'warning');
            return false;
        }

        // Validate shipping address
        if (!data.address || !data.city || !data.state || !data.zipCode || !data.country) {
            showAlertModal(translations[currentLang].fillShippingAddress, 'warning');
            return false;
        }

        // Validate billing address if not same as shipping
        const sameAsShipping = document.getElementById('sameAsShipping')?.checked || false;
        if (!sameAsShipping) {
            if (!data.billingAddress || !data.billingCity || !data.billingState || !data.billingZipCode || !data.billingCountry) {
                showAlertModal(translations[currentLang].fillBillingAddress, 'warning');
                return false;
            }
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email)) {
            showAlertModal(translations[currentLang].invalidEmail, 'error');
            return false;
        }

        // Validate Korean payment methods require Korean billing country
        const koreanPaymentMethods = ['kakao_pay', 'naver_pay', 'payco', 'toss_pay'];
        const selectedPaymentMethod = data.paymentMethod;
        
        if (selectedPaymentMethod && koreanPaymentMethods.includes(selectedPaymentMethod)) {
            // Get billing country (use shipping country if same as shipping)
            const billingCountry = sameAsShipping ? data.country : data.billingCountry;
            
            if (billingCountry !== 'KR') {
                const errorMessage = currentLang === 'zh' 
                    ? `${selectedPaymentMethod.replace('_', ' ').toUpperCase()} 仅支持韩国账单地址，请选择韩国作为账单国家或更换支付方式。`
                    : `${selectedPaymentMethod.replace('_', ' ').toUpperCase()} only supports Korean billing address. Please select South Korea as billing country or choose another payment method.`;
                showAlertModal(errorMessage, 'error');
                return false;
            }
        }

        return true;
    }

    /**
     * Prepare checkout data from form
     * @param {Object} formData - Form data object
     * @param {Array} cart - Shopping cart items
     * @param {Function} getPaymentMethods - Function to get cached payment methods
     * @param {Function} calculateTotals - Function to calculate order totals
     * @returns {Object} Checkout data ready for API submission
     */
    static prepareCheckoutData(formData, cart, getPaymentMethods, calculateTotals) {
        const data = formData;
        const totals = calculateTotals();
        const onePageCheckoutEnabled = document.getElementById('onePageCheckout')?.checked || false;
        
        // Determine payment methods based on one-page checkout state
        let paymentMethods = [];
        if (onePageCheckoutEnabled) {
            // One-page checkout enabled: use only selected payment method
            paymentMethods = data.paymentMethod ? [data.paymentMethod] : [];
        } else {
            // One-page checkout disabled: use all cached payment methods
            const cachedMethods = getPaymentMethods();
            paymentMethods = cachedMethods && cachedMethods.length > 0 ? cachedMethods : (data.paymentMethod ? [data.paymentMethod] : []);
        }
        
        // Prepare shipping address data
        const shippingAddress = {
            address: data.address,
            city: data.city,
            state: data.state,
            zipCode: data.zipCode,
            country: data.country
        };
        
        // Prepare billing address data
        const sameAsShipping = document.getElementById('sameAsShipping')?.checked || false;
        const billingAddress = sameAsShipping ? shippingAddress : {
            address: data.billingAddress,
            city: data.billingCity,
            state: data.billingState,
            zipCode: data.billingZipCode,
            country: data.billingCountry
        };

        return {
            firstName: data.firstName,
            lastName: data.lastName,
            email: data.email,
            phone: data.phone,
            shippingAddress: shippingAddress,
            billingAddress: billingAddress,
            sameAsShipping: sameAsShipping,
            paymentMethods: paymentMethods,
            items: cart,
            totals: totals,
            onePageCheckout: onePageCheckoutEnabled
        };
    }

    /**
     * Render complete checkout page
     * @param {HTMLElement} container - Container element
     */
    render(container) {
        if (!container) {
            console.error('Container element not found');
            return;
        }

        // Check if cart is empty
        if (!this.cart || this.cart.length === 0) {
            this.renderEmptyCart(container);
            return;
        }

        // Calculate totals
        const totals = this.calculateTotals();

        // Render checkout form and order summary
        container.innerHTML = `
            ${this.renderCheckoutForm(totals)}
            ${this.renderOrderSummary(totals)}
        `;

        // Setup event handlers
        this.setupEventHandlers();
    }
}
