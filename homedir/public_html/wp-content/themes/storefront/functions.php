<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
/* Кнопка Войти / Выйти в Главном меню */
add_filter( 'wp_nav_menu_items', 'tb_loginout_menu_link', 10, 2 );

function tb_loginout_menu_link( $items, $args ) {
   $logInUri = "https://star-poe.com.ua/my-account/";
   $logOutUri = "https://star-poe.com.ua/my-account/customer-logout/?_wpnonce=a5304b55e0";

   if ($args->theme_location == 'primary') {
      if (is_user_logged_in()) {
         $items .= '<li class="right"><a href="'. $logOutUri .'">'. __("Log Out") .'</a></li>';
      } else {
         $items .= '<li class="right"><a href="'. $logInUri .'">'. __("Log In") .'</a></li>';
      }
   }
   return $items;
}


/* Текст в меню */
add_filter ( 'woocommerce_account_menu_items', 'rename_editaddress' );
function rename_editaddress( $menu_links ){
	
	$menu_links['edit-address'] = 'Адреси';
	return $menu_links;
}

/* Поле номера телефона и валидация */
add_action( 'woocommerce_register_form', 'custom_add_phone_number_to_registration' );
function custom_add_phone_number_to_registration() {
    ?>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="reg_phone">Номер телефону <span class="required">*</span></label>
        <input type="tel" class="woocommerce-Input woocommerce-Input--text input-text" name="phone" required id="reg_phone" value="<?php if ( ! empty( $_POST['phone'] ) ) esc_attr_e( $_POST['phone'] ); ?>" autocomplete="tel"><br>
		<span id="error-msg" class="" style="color:red"></span>
    </p>
    <?php
}

add_action( 'woocommerce_created_customer', 'custom_save_phone_number_to_payment_address', 10, 3 );
function custom_save_phone_number_to_payment_address( $customer_id, $new_customer_data, $password_generated ) {
    $phone = sanitize_text_field( $_POST['phone'] );
    update_user_meta( $customer_id, 'billing_phone', $phone );
}

function add_phone_field_scripts() {
    if ( is_account_page() || is_checkout() ) {
        wp_enqueue_script( 'intlTelInput', '//cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js', array( 'jquery' ), false, true );
        wp_enqueue_style( 'intlTelInput', '//cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css', array(), false );
        wp_enqueue_script( 'intlTelInput-init', get_template_directory_uri() . '/assets/js/intlTelInput-init.js', array( 'jquery', 'intlTelInput' ), false, true );
    }
}
add_action( 'wp_enqueue_scripts', 'add_phone_field_scripts' );

// Добавление поля ввода пароля в форму регистрации
add_action( 'woocommerce_register_form', 'add_register_password_field' );
function add_register_password_field() {
    ?>
    <p class="form-row form-row-wide">
        <label for="reg_password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" required autocomplete="new-password" />
    </p>
    <?php
}

// Сохранение введенного пароля в качестве пароля для аккаунта
add_filter( 'woocommerce_new_customer_data', 'save_new_customer_password', 10, 1 );
function save_new_customer_password( $new_customer_data ) {
    if ( isset( $_POST['password'] ) && ! empty( $_POST['password'] ) ) {
        $new_customer_data['user_pass'] = $_POST['password'];
    }
    return $new_customer_data;
}

// Сохранение введенного email в качестве логина для аккаунта
add_filter( 'woocommerce_new_customer_data', 'save_new_customer_login', 10, 1 );
function save_new_customer_login( $new_customer_data ) {
    if ( isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ) {
        $new_customer_data['user_login'] = $_POST['email'];
    }
    return $new_customer_data;
}


