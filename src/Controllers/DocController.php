<?php

namespace UseePayDemo\Controllers;

require_once __DIR__ . '/BaseController.php';

/**
 * DocContorller: Display of control plugin installation documentation
 *
 * @Author: zhen.wang
 * @Date: 2025/12/10 10:07
 * @Description: Read Markdown from /resources/doc/..., parse it into HTML, and render it to the page
 */

class DocController extends BaseController
{
    /**
     * slug -> markdown file
     * Only allow access to documents configured here
     */
    private $docs = [
        'shopify-embedded'  => 'shopify-embedded.md',
        'shopify-redirect'  => 'shopify-redirect.md',
        'shopify-localized' => 'shopify-localized.md',
        'shopline-embedded' => 'shopline-embedded.md',
        'shopline-redirect' => 'shopline-redirect.md',
    ];

    /**
     * Display the documentation corresponding to slug
     */
    public function show($slug, $lang = 'zh')
    {
        if (!isset($this->docs[$slug])) {
            http_response_code(404);
            echo 'Document not found';
            return;
        }

        $lang = ($lang === 'en') ? 'en' : 'zh';

        // resources/docs/{lang}/xxx.md
        $filePath = __DIR__ . "/../../resources/docs/{$lang}/" . $this->docs[$slug];

        if (!file_exists($filePath)) {
            http_response_code(404);
            echo 'Document file missing';
            return;
        }

        $markdown = file_get_contents($filePath);
        $parsedown = new \Parsedown();
        $html = $parsedown->text($markdown);

        $title   = $this->getTitleFromSlug($slug, $lang);
        $docSlug = $slug;
        $docLang = $lang;

        require __DIR__ . '/../Views/docs/show.php';
    }

    /**
     * Return a user-friendly title based on the slug and language
     */
    private function getTitleFromSlug($slug, $lang = 'zh')
    {
        $lang = ($lang === 'en') ? 'en' : 'zh';

        $titles = [
            'zh' => [
                'shopify-embedded'  => 'Shopify 内嵌收银台插件安装与配置',
                'shopify-redirect'  => 'Shopify 跳转收银台插件安装与配置',
                'shopify-localized' => 'Shopify 本地化收银台（ApplePay / GooglePay / Klarna 等）',
                'shopline-embedded' => 'ShopLine 内嵌收银台插件安装与配置',
                'shopline-redirect' => 'ShopLine 跳转收银台插件安装与配置',
            ],
            'en' => [
                'shopify-embedded'  => 'Shopify Embedded Checkout Setup & Configuration',
                'shopify-redirect'  => 'Shopify Redirect Checkout Setup & Configuration',
                'shopify-localized' => 'Shopify Localized Checkout (Apple Pay / Google Pay / Klarna, etc.)',
                'shopline-embedded' => 'ShopLine Embedded Checkout Setup & Configuration',
                'shopline-redirect' => 'ShopLine Redirect Checkout Setup & Configuration',
            ],
        ];

        if (isset($titles[$lang][$slug])) {
            return $titles[$lang][$slug];
        }

        // default title
        return $lang === 'en' ? 'Plugin Integration Docs' : '插件安装文档';
    }
}