<?php

Class LiqpayMenu
{
    public $slug = 'admin.php?page=wc-settings&tab=checkout&section=liqpay-webplus';

    public function __construct()
    {
        add_action('admin_menu', array($this, 'register_admin_menu'));
    }

    public function register_admin_menu()
    {
        add_menu_page('WebPlus LiqPay', 'WebPlus LiqPay', 'manage_options', $this->slug, false, plugin_dir_url(__DIR__) . 'img/liqpay.png', 26);
        add_submenu_page($this->slug, 'Pro версия', 'Pro версия', 'manage_options', 'pro-liqpay-webplus-version', array($this, 'page_pro'), 1);
    }

    public function page_pro()
    {
        $html = "<h1>WebPlus Gateway for LiqPay on WooCommerce</h1>";
        $html .= sprintf(__('В этой версии плагина покупатели смогут только оплачивать товары из корзины вашего интернет магазина выбрав способ оплаты LiqPay.<br />Но в этой версии нет callback на ваш сайт после оплаты. Сallback – это обращения к вашему сайту по API с сервиса liqpay для уведомления Вас, что деньги поступили на счет и тем самым изменяет статус в ваших заказах в админ панели на “Обработка” и высылает вам письмо что заказ оплачен.<br />Более полную версию плагина с callback вы можете заказать, обратившись по эмейлу указанному на <a href="%s">странице плагина</a>.', 'woocommerce'), 'https://wordpress.org/plugins/webplus-liqpay-woocommerce/');
        $html .= "<hr />";
        $html .= "<h3>Еще плагины</h3>";
        $html .= sprintf(__('Рекоммендуем вам еще один мой плагин <a href="%s">WebPlus-Gallery</a> - это галерея слайдер. Очень красивая и удобная.', 'woocommerce'),'https://wordpress.org/plugins/webplus-gallery/');
        echo $html;
    }
}