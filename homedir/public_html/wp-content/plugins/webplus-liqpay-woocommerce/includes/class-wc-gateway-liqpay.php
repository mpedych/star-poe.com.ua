<?php
/**
 * Class WC_Gateway_Liqpay file.
 *
 * @package WooCommerce\Gateways
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * WC_Gateway_Liqpay Class.
 */
class WC_Gateway_Liqpay extends WC_Payment_Gateway
{

    /**
     * Constructor for the gateway.
     */
    public function __construct()
    {
        // Setup general properties.
        $this->setup_properties();

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Get settings.
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->instructions = $this->get_option('instructions');
        $this->lang = $this->get_option('lang', 'ru');
        $this->enable_for_methods = $this->get_option('enable_for_methods', array());
        $this->enable_for_virtual = $this->get_option('enable_for_virtual', 'yes') === 'yes';

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
        add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
    }

    /**
     * Setup general properties for the gateway.
     */
    protected function setup_properties()
    {
        $this->id = 'liqpay-webplus';
        $this->icon = apply_filters('woocommerce_cod_icon', '');
        $this->method_title = 'LiqPay';
        $this->method_description = 'Платежный сервис, который позволяет совершать моментальные платежи в интернете и платежных карт Visa, MasterCard во всём мире.';
        $this->has_fields = false;
    }

    /**
     * Get gateway icon.
     *
     * @return string
     */
    public function get_icon()
    {

        $icon_html = '<img src="' . plugin_dir_url(__DIR__) . 'img/symbol-liqpay.svg' . '" alt="' . esc_attr__('LiqPay', 'woocommerce') . '" />';

        return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields()
    {

        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'woocommerce'),
                'label' => 'Включить',
                'type' => 'checkbox',
                'description' => '',
                'default' => 'no',
            ),
            'title' => array(
                'title' => __('Title', 'woocommerce'),
                'type' => 'text',
                'description' => 'LiqPay - Моментальные платежи по всему миру',
                'default' => 'LiqPay - Моментальные платежи по всему миру',
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'woocommerce'),
                'type' => 'textarea',
                'description' => 'Платежный сервис, который позволяет совершать моментальные платежи в интернете и платежных карт Visa, MasterCard во всём мире.',
                'default' => 'Платежный сервис, который позволяет совершать моментальные платежи в интернете и платежных карт Visa, MasterCard во всём мире.',
                'desc_tip' => true,
            ),
            'instructions' => array(
                'title' => __('Instructions', 'woocommerce'),
                'type' => 'textarea',
                'description' => '',
                'default' => '',
                'desc_tip' => true,
            ),
            'public_key' => array(
                'title' => __('API public_key', 'woocommerce'),
                'type' => 'text',
                'description' => '',
                'default' => '',
                'desc_tip' => true,
                'placeholder' => '',
            ),
            'private_key' => array(
                'title' => __('API private_key', 'woocommerce'),
                'type' => 'text',
                'description' => '',
                'default' => '',
                'desc_tip' => true,
                'placeholder' => '',
            ),
            'lang' => array(
                'title' => __('Язык интерфейса сайта LiqPay', 'woocommerce'),
                'type' => 'select',
                'default' => 'ru',
                'options' => array(
                    'ru' => 'ru',
                    'en' => 'en',
                    'uk' => 'uk'
                )
            ),
            'plugin_details' => array(
                'title' => __('О плагине', 'woocommerce'),
                'type' => 'title',
                /* translators: %s: URL */
                'description' => sprintf(__('В этой версии плагина покупатели смогут только оплачивать товары из корзины вашего интернет магазина выбрав способ оплаты LiqPay.<br />Но в этой версии нет callback на ваш сайт после оплаты. Сallback – это обращения к вашему сайту по API с сервиса liqpay для уведомления Вас, что деньги поступили на счет и тем самым изменяет статус в ваших заказах в админ панели на “Обработка”.<br />Более полную версию плагина с callback вы можете заказать, обратившись по эмейлу указанному на <a href="%s">странице плагина</a>.<hr /> Рекоммендуем вам еще один мой плагин <a href="%s">WebPlus-Gallery</a> - это галерея слайдер. Очень красивая и удобная.', 'woocommerce'), 'https://wordpress.org/plugins/webplus-liqpay-woocommerce/', 'https://wordpress.org/plugins/webplus-gallery/'),
            ),
        );
    }

    /**
     * @param $order_id
     * @return string
     */
    private function getDescription($order_id)
    {
        switch ($this->lang) {
            case 'ru' :
                $description = 'Оплата заказа № ' . $order_id;
                break;
            case 'en' :
                $description = 'Order payment # ' . $order_id;
                break;
            case 'uk' :
                $description = 'Оплата замовлення № ' . $order_id;
                break;
            default :
                $description = 'Оплата заказа № ' . $order_id;
        }

        return $description;
    }

    /**
     * Process the payment and return the result.
     *
     * @param int $order_id Order ID.
     * @return array
     */
    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);
                                         
        if ($order->get_total() > 0) {

        } else {
            $order->payment_complete();
        }

        // Remove cart.
        WC()->cart->empty_cart();

            require_once(__DIR__ . '/classes/LiqPay.php');
            $LigPay = new LiqPay($this->get_option('public_key'), $this->get_option('private_key'));
            $url = $LigPay->cnb_link(array(
                'version' => '3',
                'action' => 'pay',
                'amount' => $order->get_total(),
                'currency' => $order->get_currency(),
                'description' => $this->getDescription($order->get_id()),
                'order_id' => $order->get_id(),
                'result_url' => $this->get_return_url($order),
                'language' => $this->get_option('lang')
                //'sandbox'=>'1' // и куда же без песочницы,
            ));


        return array(
            'result' => 'success',
            'redirect' => $url,
        );
    }

    /**
     * Output for the order received page.
     */
    public function thankyou_page()
    {
        if ($this->instructions) {
            echo wp_kses_post(wpautop(wptexturize($this->instructions)));
        }
    }

    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param WC_Order $order Order object.
     * @param bool $sent_to_admin Sent to admin.
     * @param bool $plain_text Email format: plain text or HTML.
     */
    public function email_instructions($order, $sent_to_admin, $plain_text = false)
    {
        if ($this->instructions && !$sent_to_admin && $this->id === $order->get_payment_method()) {
            echo wp_kses_post(wpautop(wptexturize($this->instructions)) . PHP_EOL);
        }
    }


}
