/**
 * Pricing Page Internationalization (i18n)
 * 定价页面国际化配置
 */

const pricingTranslations = {
    zh: {
        // Navigation
        backHome: '返回首页',
        
        // Header
        selectPlan: '选择适合您的订阅计划',
        flexiblePricing: '灵活的定价选项，满足各种业务需求',
        
        // Billing Toggle
        monthlyBilling: '按月计费',
        annualBilling: '按年计费',
        saveBadge: '节省 20%',
        
        // Plan Names
        starter: '入门版',
        starterDesc: '适合个人和小型项目',
        professional: '专业版',
        professionalDesc: '适合成长中的企业',
        mostPopular: '最受欢迎',
        enterprise: '企业版',
        enterpriseDesc: '适合大型企业',
        
        // Buttons
        selectThisPlan: '选择此计划',
        contactSales: '联系销售',
        
        // Pricing
        perMonth: '/月',
        perYear: '/年',
        
        // Features
        transactions: '笔交易',
        starterTransactions: '最多 1,000 笔交易/月',
        professionalTransactions: '最多 50,000 笔交易/月',
        unlimitedTransactions: '无限交易',
        basicPaymentMethods: '基础支付方式支持',
        allPaymentMethods: '所有支付方式支持',
        standardSupport: '标准技术支持',
        prioritySupport: '优先技术支持',
        basicAnalytics: '基础分析报告',
        advancedAnalytics: '高级分析报告',
        customAnalytics: '自定义分析报告',
        apiAccess: 'API 访问',
        fullApiAccess: '完整 API 访问',
        dedicatedSupport: '24/7 专属支持',
        accountManager: '专属账户经理',
        
        // Feature Comparison Table
        featureComparison: '功能对比',
        feature: '功能',
        monthly: '月度交易数',
        paymentMethods: '支付方式',
        support: '技术支持',
        analytics: '分析报告',
        accountManagement: '专属账户经理',
        unlimited: '无限',
        standard: '标准',
        priority: '优先',
        basic: '基础',
        advanced: '高级',
        custom: '自定义',
        prioritySupport2: '优先支持',
        
        // FAQ
        faq: '常见问题',
        canUpgrade: '我可以随时升级或降级我的计划吗？',
        canUpgradeAnswer: '是的，您可以随时升级或降级您的订阅计划。升级会立即生效，降级将在下一个计费周期生效。我们会按比例计算费用。',
        exceedLimit: '如果我超过了我的交易限额会怎样？',
        exceedLimitAnswer: '如果您接近交易限额，我们会向您发送通知。您可以选择升级计划或联系我们讨论自定义解决方案。我们不会在没有通知的情况下阻止交易。',
        freeTrial: '你们提供免费试用吗？',
        freeTrialAnswer: '是的，我们为所有新用户提供 14 天的免费试用。您可以在试用期间体验所有功能，无需提供信用卡信息。',
        paymentMethods2: '你们接受哪些付款方式？',
        paymentMethodsAnswer: '我们接受所有主要的信用卡（Visa、MasterCard、American Express）、支付宝、微信支付和银行转账。所有交易都是安全加密的。',
        canCancel: '我可以取消我的订阅吗？',
        canCancelAnswer: '是的，您可以随时取消订阅。取消后，您将能够访问您的账户直到当前计费周期结束。我们不收取任何取消费用。',
        
        // Auth Modal
        login: '登录',
        register: '注册',
        registerTab: '注册',
        loginTab: '登录',
        email: '邮箱地址',
        password: '密码',
        confirmPassword: '确认密码',
        registerButton: '注册',
        loginButton: '登录',
        emailPlaceholder: '请输入邮箱地址',
        passwordPlaceholder: '请输入密码',
        confirmPasswordPlaceholder: '请再次输入密码',
        personalCenter: '个人中心',
        
        // Processing Modal
        processing: '处理中...',
        processingMessage: '正在创建您的订阅，请稍候...',
        processingSuccess: '订阅创建成功！',
        processingError: '订阅创建失败，请重试',
        
        // Payment Modal
        paymentProcessing: '支付处理中...',
        paymentProcessingMessage: '正在处理您的支付，请稍候...',
        paymentSuccess: '支付成功！',
        paymentError: '支付失败',
        selectPaymentMethod: '选择支付方式',
        cancel: '取消',
        confirm: '确认'
    },
    
    en: {
        // Navigation
        backHome: 'Back to Home',
        
        // Header
        selectPlan: 'Choose Your Subscription Plan',
        flexiblePricing: 'Flexible pricing options for all business needs',
        
        // Billing Toggle
        monthlyBilling: 'Monthly Billing',
        annualBilling: 'Annual Billing',
        saveBadge: 'Save 20%',
        
        // Plan Names
        starter: 'Starter',
        starterDesc: 'Perfect for individuals and small projects',
        professional: 'Professional',
        professionalDesc: 'Ideal for growing businesses',
        mostPopular: 'Most Popular',
        enterprise: 'Enterprise',
        enterpriseDesc: 'For large organizations',
        
        // Buttons
        selectThisPlan: 'Select This Plan',
        contactSales: 'Contact Sales',
        
        // Pricing
        perMonth: '/month',
        perYear: '/year',
        
        // Features
        transactions: 'transactions',
        starterTransactions: 'Up to 1,000 transactions/month',
        professionalTransactions: 'Up to 50,000 transactions/month',
        unlimitedTransactions: 'Unlimited transactions',
        basicPaymentMethods: 'Basic payment methods',
        allPaymentMethods: 'All payment methods',
        standardSupport: 'Standard support',
        prioritySupport: 'Priority support',
        basicAnalytics: 'Basic analytics',
        advancedAnalytics: 'Advanced analytics',
        customAnalytics: 'Custom analytics',
        apiAccess: 'API access',
        fullApiAccess: 'Full API access',
        dedicatedSupport: '24/7 dedicated support',
        accountManager: 'Dedicated account manager',
        
        // Feature Comparison Table
        featureComparison: 'Feature Comparison',
        feature: 'Feature',
        monthly: 'Monthly Transactions',
        paymentMethods: 'Payment Methods',
        support: 'Support',
        analytics: 'Analytics',
        accountManagement: 'Account Manager',
        unlimited: 'Unlimited',
        standard: 'Standard',
        priority: 'Priority',
        basic: 'Basic',
        advanced: 'Advanced',
        custom: 'Custom',
        prioritySupport2: 'Priority Support',
        
        // FAQ
        faq: 'Frequently Asked Questions',
        canUpgrade: 'Can I upgrade or downgrade my plan anytime?',
        canUpgradeAnswer: 'Yes, you can upgrade or downgrade your subscription plan at any time. Upgrades take effect immediately, while downgrades will be applied in the next billing cycle. We calculate fees proportionally.',
        exceedLimit: 'What happens if I exceed my transaction limit?',
        exceedLimitAnswer: 'If you approach your transaction limit, we will send you a notification. You can choose to upgrade your plan or contact us to discuss custom solutions. We will not block transactions without notice.',
        freeTrial: 'Do you offer a free trial?',
        freeTrialAnswer: 'Yes, we offer a 14-day free trial for all new users. You can experience all features during the trial period without providing credit card information.',
        paymentMethods2: 'What payment methods do you accept?',
        paymentMethodsAnswer: 'We accept all major credit cards (Visa, MasterCard, American Express), Alipay, WeChat Pay, and bank transfers. All transactions are securely encrypted.',
        canCancel: 'Can I cancel my subscription?',
        canCancelAnswer: 'Yes, you can cancel your subscription at any time. After cancellation, you will have access to your account until the end of the current billing cycle. We do not charge any cancellation fees.',
        
        // Auth Modal
        login: 'Login',
        register: 'Register',
        registerTab: 'Register',
        loginTab: 'Login',
        email: 'Email Address',
        password: 'Password',
        confirmPassword: 'Confirm Password',
        registerButton: 'Register',
        loginButton: 'Login',
        emailPlaceholder: 'Please enter email address',
        passwordPlaceholder: 'Please enter password',
        confirmPasswordPlaceholder: 'Please enter password again',
        personalCenter: 'Personal Center',
        
        // Processing Modal
        processing: 'Processing...',
        processingMessage: 'Creating your subscription, please wait...',
        processingSuccess: 'Subscription created successfully!',
        processingError: 'Failed to create subscription, please try again',
        
        // Payment Modal
        paymentProcessing: 'Processing Payment...',
        paymentProcessingMessage: 'Processing your payment, please wait...',
        paymentSuccess: 'Payment Successful!',
        paymentError: 'Payment Failed',
        selectPaymentMethod: 'Select Payment Method',
        cancel: 'Cancel',
        confirm: 'Confirm'
    }
};

// Export for use in pricing.php
if (typeof window !== 'undefined') {
    window.pricingTranslations = pricingTranslations;
}
