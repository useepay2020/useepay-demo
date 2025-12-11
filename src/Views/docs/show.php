<?php
    // Bilingual configuration in Chinese and English, based on the $docLang (zh/en) input from DocController
    $docLang = isset($docLang) && $docLang === 'en' ? 'en' : 'zh';

    $i18n = [
        'zh' => [
            'htmlLang'       => 'zh-CN',
            'pageTitleSuffix'=> '插件安装文档',
            'siteName'       => 'UseePay 支付体验站',
            'headerMain'     => 'UseePay 插件集成文档',
            'headerSub'      => 'Shopify / ShopLine 等建站平台',
            'backText'       => '返回支付体验站',
            'metaHint'       => '若有任何疑问，请联系UseePay运营~',
        ],
        'en' => [
            'htmlLang'       => 'en',
            'pageTitleSuffix'=> 'Plugin Integration Docs',
            'siteName'       => 'UseePay Demo Site',
            'headerMain'     => 'UseePay Plugin Integration Docs',
            'headerSub'      => 'Shopify / ShopLine and other platforms',
            'backText'       => 'Back to Demo Site',
            'metaHint'       => 'If you have any questions, please contact UseePay support.',
        ],
    ];

    $t = $i18n[$docLang];
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($t['htmlLang']); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(($title ?? $t['pageTitleSuffix']) . ' - ' . $t['siteName']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f7ff;
            color: #333;
        }

        .doc-page-header {
            background: linear-gradient(135deg, #4a6bdf, #2a4ab3);
            color: #fff;
            padding: 1.2rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .doc-page-header-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .doc-page-header-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .doc-page-header-title i {
            font-size: 1.6rem;
        }

        .doc-page-header-title span {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .doc-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .doc-back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            color: #4a6bdf;
            text-decoration: none;
        }

        .doc-back-link:hover {
            text-decoration: underline;
        }

        .doc-title {
            margin: 0 0 1rem 0;
            font-size: 1.6rem;
            color: #2a4ab3;
        }

        .doc-meta {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 1.5rem;
        }

        .doc-content {
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .doc-content h1,
        .doc-content h2,
        .doc-content h3,
        .doc-content h4 {
            color: #2a4ab3;
            margin-top: 1.6rem;
            margin-bottom: 0.8rem;
        }

        .doc-content h1 { font-size: 1.6rem; }
        .doc-content h2 { font-size: 1.4rem; }
        .doc-content h3 { font-size: 1.2rem; }

        .doc-content p {
            margin: 0.6rem 0;
        }

        .doc-content ul,
        .doc-content ol {
            padding-left: 1.4rem;
            margin: 0.6rem 0;
        }

        .doc-content code {
            background: #f6f8fa;
            padding: 0.1rem 0.3rem;
            border-radius: 3px;
            font-family: Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 0.9em;
        }

        .doc-content pre code {
            display: block;
            padding: 0.8rem;
            overflow-x: auto;
        }

        .doc-content table {
            border-collapse: collapse;
            margin: 1rem 0;
            width: 100%;
            font-size: 0.9rem;
        }

        .doc-content table th,
        .doc-content table td {
            border: 1px solid #e2e8f0;
            padding: 0.5rem 0.6rem;
        }

        .doc-content table th {
            background: #f8fafc;
        }

        .doc-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0.5rem auto;
        }

        @media (max-width: 768px) {
            .doc-container {
                margin: 1rem;
                padding: 1.3rem;
            }

            .doc-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="doc-page-header">
        <div class="doc-page-header-inner">
            <div class="doc-page-header-title">
                <i class="fas fa-plug"></i>
                <div>
                    <div><?php echo htmlspecialchars($t['headerMain']); ?></div>
                    <span><?php echo htmlspecialchars($t['headerSub']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Determine which sector the slug originates from(Self-built, Shopify, Shopify)
    $backHash = '';
    if (!empty($docSlug)) {
        if (strpos($docSlug, 'shopify-') === 0) {
            $backHash = '#shopify';
        } elseif (strpos($docSlug, 'shopline-') === 0) {
            $backHash = '#shopline';
        } else {
            $backHash = '#selfBuilt';
        }
    }
    ?>
    <div class="doc-container">
        <a href="/<?php echo $backHash; ?>" class="doc-back-link">
            <i class="fas fa-arrow-left"></i>
            <span><?php echo htmlspecialchars($t['backText']); ?></span>
        </a>

        <h1 class="doc-title"><?php echo htmlspecialchars($title ?? $t['pageTitleSuffix']); ?></h1>

        <?php if (!empty($docSlug)): ?>
            <div class="doc-meta">
                <?php echo htmlspecialchars($t['metaHint']); ?>
            </div>
        <?php endif; ?>

        <div class="doc-content">
            <?php
            // Output HTML generated by Parsedown
            echo $html;
            ?>
        </div>
    </div>
</body>
</html>