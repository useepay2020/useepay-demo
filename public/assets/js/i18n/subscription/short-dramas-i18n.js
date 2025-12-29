/**
 * Short Dramas Subscription Page - Internationalization (i18n)
 * Supports Chinese (zh) and English (en)
 */

const translations = {
    zh: {
        // Navigation
        backHome: '返回首页',
        register: '注册',
        personalCenter: '个人中心',

        // Header
        selectDramas: '选择您喜欢的短剧',
        dramasDescription: '订阅您喜爱的短剧，每部仅需 $0.99/月',

        // Drama Card
        episodes: '集',
        select: '选择',
        perMonth: '/月',

        // Subscription Summary
        subscriptionSummary: '订阅摘要',
        noDramasSelected: '未选择任何短剧',
        totalPrice: '总价格',
        billingCycle: '计费周期',
        subscribeNow: '立即订阅',

        // FAQ
        frequentlyAsked: '常见问题',
        faqQuestion1: '如何取消订阅？',
        faqAnswer1: '您可以随时在个人中心取消订阅，取消后将不再扣费。',
        faqQuestion2: '支持哪些支付方式？',
        faqAnswer2: '我们支持信用卡、Apple Pay、Google Pay 等多种支付方式。',
        faqQuestion3: '订阅后可以立即观看吗？',
        faqAnswer3: '是的，订阅成功后可以立即观看所有已订阅的短剧。',

        // Modal
        processing: '处理中...',
        pleaseWait: '请稍候',
        selectPaymentMethod: '选择支付方式',
        cancel: '取消',
        confirm: '确认',

        // Form
        email: '邮箱地址',
        password: '密码',
        confirmPassword: '确认密码',
        registerButton: '注册',

        // Messages
        processingSuccess: '订阅创建成功！',
        processingError: '订阅创建失败，请重试',
        paymentProcessing: '支付处理中',
        paymentProcessingMessage: '正在处理您的支付，请稍候...',
        paymentSuccess: '支付成功！',
        paymentError: '支付失败',
        pleaseEnterCardInfo: '请输入完整的卡信息',

        // Payment Method
        paymentMethod: '支付方式',
        cardNumber: '卡号',
        cardNumberPlaceholder: '请输入卡号',
        expiryDate: '有效期',
        expiryPlaceholder: 'MM/YY',
        cvv: 'CVV',
        cvvPlaceholder: '123',

        // Validation
        pleaseSelectDrama: '请选择至少一部短剧',
        pleaseEnterAllFields: '请填写所有字段',
        passwordsDoNotMatch: '两次输入的密码不一致',

        // Success/Error
        registrationSuccess: '注册成功！',
        registrationFailed: '注册失败',
    },

    en: {
        // Navigation
        backHome: 'Back to Home',
        register: 'Register',
        personalCenter: 'Personal Center',

        // Header
        selectDramas: 'Select Your Favorite Short Dramas',
        dramasDescription: 'Subscribe to your favorite short dramas for only $0.99/month each',

        // Drama Card
        episodes: 'Episodes',
        select: 'Select',
        perMonth: '/month',

        // Subscription Summary
        subscriptionSummary: 'Subscription Summary',
        noDramasSelected: 'No dramas selected',
        totalPrice: 'Total Price',
        billingCycle: 'Billing Cycle',
        subscribeNow: 'Subscribe Now',

        // FAQ
        frequentlyAsked: 'Frequently Asked Questions',
        faqQuestion1: 'How do I cancel my subscription?',
        faqAnswer1: 'You can cancel your subscription anytime from your personal center. No further charges will be made after cancellation.',
        faqQuestion2: 'What payment methods are supported?',
        faqAnswer2: 'We support multiple payment methods including credit cards, Apple Pay, Google Pay, and more.',
        faqQuestion3: 'Can I watch immediately after subscribing?',
        faqAnswer3: 'Yes, you can start watching all your subscribed dramas immediately after successful payment.',

        // Modal
        processing: 'Processing...',
        pleaseWait: 'Please wait',
        selectPaymentMethod: 'Select Payment Method',
        cancel: 'Cancel',
        confirm: 'Confirm',

        // Form
        email: 'Email Address',
        password: 'Password',
        confirmPassword: 'Confirm Password',
        registerButton: 'Register',

        // Messages
        processingSuccess: 'Subscription created successfully!',
        processingError: 'Failed to create subscription, please try again',
        paymentProcessing: 'Processing Payment',
        paymentProcessingMessage: 'Your payment is being processed, please wait...',
        paymentSuccess: 'Payment successful!',
        paymentError: 'Payment failed',
        pleaseEnterCardInfo: 'Please enter complete card information',

        // Payment Method
        paymentMethod: 'Payment Method',
        cardNumber: 'Card Number',
        cardNumberPlaceholder: 'Enter card number',
        expiryDate: 'Expiry Date',
        expiryPlaceholder: 'MM/YY',
        cvv: 'CVV',
        cvvPlaceholder: '123',

        // Validation
        pleaseSelectDrama: 'Please select at least one drama',
        pleaseEnterAllFields: 'Please fill in all fields',
        passwordsDoNotMatch: 'Passwords do not match',

        // Success/Error
        registrationSuccess: 'Registration successful!',
        registrationFailed: 'Registration failed',
    }
};

// Make translations globally available
window.translations = translations;
console.log('Short Dramas i18n loaded:', Object.keys(translations));
