<?php

namespace UseePayDemo\Controllers;

require_once __DIR__ . '/BaseController.php';

class CheckoutController extends BaseController
{
    /**
     * Display clothing shop page
     */
    public function clothingShop()
    {
        $viewPath = __DIR__ . '/../Views/payment/clothing_shop.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            $this->errorResponse('Clothing shop page not found', 404);
        }
    }

    /**
     * Display embedded checkout page
     */
    public function embeddedCheckout()
    {
        $viewPath = __DIR__ . '/../Views/payment/embedded_checkout.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            $this->errorResponse('Embedded checkout page not found', 404);
        }
    }
    /**
     * Display checkout page
     */
    public function checkout()
    {
        $viewPath = __DIR__ . '/../Views/payment/checkout.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            $this->errorResponse('Checkout page not found', 404);
        }
    }

    /**
     * Display api checkout page
     */
    public function apiCheckout()
    {
        $viewPath = __DIR__ . '/../Views/payment/api_checkout.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            $this->errorResponse('Api Checkout page not found', 404);
        }
    }

}
