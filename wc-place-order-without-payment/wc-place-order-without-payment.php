<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://nitin247.com/
 * @since             1.0.0
 * @package           wc_place_order_without_payment
 *
 * @wordpress-plugin
 * Plugin Name:       Place Order Without Payment for WooCommerce
 * Plugin URI:        https://nitin247.com/plugin/woocommerce-place-order-without-payment/
 * Description:       Place Order Without Payment for WooCommerce will allow users to place orders directly.This plugin will customize checkout page and offers to direct place order without payment.
 * Version:           2.6.2
 * Author:            Nitin Prakash
 * Author URI:        https://nitin247.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpowp
 * Domain Path:       /languages/
 * Requires PHP:      7.4
 * Requires at least: 6.0
 * Tested up to: 6.6
 * WC requires at least: 8.0
 * WC tested up to: 9.1
 * Requires Plugins:  woocommerce
 */
namespace WPOWP;

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
defined( 'WPOWP_FILE' ) or define( 'WPOWP_FILE', __FILE__ );
defined( 'WPOWP_BASE' ) or define( 'WPOWP_BASE', plugin_basename( WPOWP_FILE ) );
defined( 'WPOWP_DIR' ) or define( 'WPOWP_DIR', plugin_dir_path( WPOWP_FILE ) );
defined( 'WPOWP_URL' ) or define( 'WPOWP_URL', plugins_url( '/', WPOWP_FILE ) );
defined( 'WPOWP_VERSION' ) or define( 'WPOWP_VERSION', '2.6.2' );
defined( 'WPOWP_TEXT_DOMAIN' ) or define( 'WPOWP_TEXT_DOMAIN', 'wpowp' );
defined( 'WPOWP_NAME' ) or define( 'WPOWP_NAME', __( 'Place Order Without Payment', WPOWP_TEXT_DOMAIN ) );
defined( 'WPOWP_SHORT_NAME' ) or define( 'WPOWP_SHORT_NAME', __( 'Place Order', WPOWP_TEXT_DOMAIN ) );
defined( 'WPOWP_PLUGIN_SLUG' ) or define( 'WPOWP_PLUGIN_SLUG', 'wpowp-settings' );
defined( 'WPOWP_PLUGIN_PREFIX' ) or define( 'WPOWP_PLUGIN_PREFIX', 'wpowp-' );
defined( 'WPOWP_FORM_PREFIX' ) or define( 'WPOWP_FORM_PREFIX', 'wpowp_' );
defined( 'WPOWP_TEMPLATES' ) or define( 'WPOWP_TEMPLATES', WPOWP_DIR . 'templates/' );
defined( 'WPOWP_API_ERROR_TEXT' ) or define( 'WPOWP_API_ERROR_TEXT', __( 'Error Processing data!', WPOWP_TEXT_DOMAIN ) );
defined( 'WPOWP_ADMIN_CONFIRM_RESET_TEXT' ) or define( 'WPOWP_ADMIN_CONFIRM_RESET_TEXT', __( 'This action is not recoverable. Are you sure you want to reset this setting?', WPOWP_TEXT_DOMAIN ) );
if ( !function_exists( 'WPOWP\\wpowp_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wpowp_fs() {
        global $wpowp_fs;
        if ( !isset( $wpowp_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_4030_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_4030_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $wpowp_fs = fs_dynamic_init( array(
                'id'             => '4030',
                'slug'           => 'wc-place-order-without-payment',
                'type'           => 'plugin',
                'public_key'     => 'pk_11c5a507e23c860c7e456326363ba',
                'is_premium'     => false,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                    'slug'       => 'wpowp-settings',
                    'first-path' => 'admin.php?page=wpowp-settings&tab=settings',
                ),
                'is_live'        => true,
            ) );
        }
        return $wpowp_fs;
    }

    // Init Freemius.
    wpowp_fs();
    // Signal that SDK was initiated.
    do_action( 'wpowp_fs_loaded' );
}
if ( !class_exists( 'WPOWP\\WPOWP_Loader' ) ) {
    class WPOWP_Loader {
        private static $instance;

        /**
         * Get Instance
         *
         * @since 2.3
         * @return object initialized object of class.
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor
         *
         * @since 2.3
         */
        private function __construct() {
            add_action( 'init', array($this, 'before_plugin_load') );
            // On plugin init
            add_action( 'wp', array($this, 'on_plugin_load') );
            // Add action links
            add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'action_links') );
            // Load dependency files
            add_action( 'plugins_loaded', array($this, 'load_classes') );
        }

        /**
         * Before Plugin Load
         *
         * @return void
         */
        public function before_plugin_load() {
            if ( !class_exists( 'woocommerce' ) ) {
                add_action( 'admin_notices', array($this, 'wc_not_active') );
                return;
            }
            if ( $this->is_woocommerce_blocks_checkout() ) {
                add_action( 'admin_notices', array($this, 'wc_block_checkout') );
                return;
            }
        }

        /**
         * Before Plugin Load
         *
         * @return void
         */
        public function on_plugin_load() {
            load_plugin_textdomain( WPOWP_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
            // Run plugin
            $this->run_plugin();
        }

        /**
         * Run Plugin
         *
         * @return void
         */
        private function run_plugin() {
            // Skip Payment based on logic
            if ( !is_admin() ) {
                $this->skip_payment();
            }
        }

        /**
         * Action Links
         *
         * @param  array $links
         * @return array
         */
        public function action_links( $links ) {
            $links = array_merge( array('<a href="' . esc_url( admin_url( 'admin.php?page=wpowp-settings&tab=settings' ) ) . '">' . __( 'Settings', 'wpowp' ) . '</a>', '<a target="blank" href="' . esc_url( 'https://nitin247.com/support/' ) . '">' . __( 'Support Desk', 'wpowp' ) . '</a>'), $links );
            return $links;
        }

        /**
         * Load Classses
         *
         * @return void
         */
        public function load_classes() {
            // Define autoload folders in plugin
            $folders = array('inc/traits', 'compatibility', 'inc');
            if ( wpowp_fs()->is_paying() ) {
                $folders = wp_parse_args( array('inc/premium'), $folders );
            }
            $this->load_recursively( $folders );
        }

        /**
         * Load Recursively
         *
         * @param  array $folders
         * @return void
         */
        private function load_recursively( $folders ) {
            if ( is_array( $folders ) ) {
                foreach ( $folders as $folder ) {
                    foreach ( glob( WPOWP_DIR . "{$folder}/*.php" ) as $filename ) {
                        include_once $filename;
                        // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
                    }
                }
            }
        }

        /**
         * WC not active.
         *
         * @return void
         * @since 2.3
         */
        public function wc_not_active() {
            $install_url = wp_nonce_url( add_query_arg( array(
                'action' => 'install-plugin',
                'plugin' => 'woocommerce',
            ), admin_url( 'update.php' ) ), 'install-plugin_woocommerce' );
            echo '<div class="notice notice-error is-dismissible"><p>';
            // translators: 1$-2$: opening and closing <strong> tags, 3$-4$: link tags, takes to woocommerce plugin on wp.org, 5$-6$: opening and closing link tags, leads to plugins.php in admin.
            echo sprintf(
                esc_html__( '%1$sPlace Order without payment for WooCommerce is inactive.%2$s The %3$sWooCommerce plugin%4$s must be active for Place Order without payment for WooCommerce to work. Please %5$sinstall & activate WooCommerce &raquo;%6$s', WPOWP_TEXT_DOMAIN ),
                '<strong>',
                '</strong>',
                '<a href="http://wordpress.org/extend/plugins/woocommerce/">',
                '</a>',
                '<a href="' . esc_url( $install_url ) . '">',
                '</a>'
            );
            echo '</p></div>';
        }

        /**
         * Skip Payment.
         *
         * @return void
         * @since 2.3
         */
        public function skip_payment() {
            // Check skip payment login
            $quote_only = \WPOWP\Inc\WPOWP_Admin::get_instance()->get_settings( 'quote_only' );
            $quote_btn_pos = \WPOWP\Inc\WPOWP_Admin::get_instance()->get_settings( 'quote_button_postion' );
            if ( false === filter_var( $quote_only, FILTER_VALIDATE_BOOLEAN ) ) {
                $this->disable_payment();
            } else {
                add_action( 'woocommerce_review_order_' . $quote_btn_pos, array($this, 'quote_button') );
                add_action( 'wc_ajax_checkout', array($this, 'disable_payment'), 0 );
            }
        }

        /**
         * Disable Payment.
         *
         * @return void
         * @since 2.3
         */
        public function disable_payment() {
            if ( true === $this->exclude_elements() ) {
                return;
            }
            $remove_shipping = $quote_btn_text = \WPOWP\Inc\WPOWP_Admin::get_instance()->get_settings( 'remove_shipping' );
            $remove_privacy_policy_text = $quote_btn_text = \WPOWP\Inc\WPOWP_Admin::get_instance()->get_settings( 'remove_privacy_policy_text' );
            $remove_checkout_terms_conditions = $quote_btn_text = \WPOWP\Inc\WPOWP_Admin::get_instance()->get_settings( 'remove_checkout_terms_conditions' );
            if ( isset( $_GET['key'] ) && is_wc_endpoint_url( 'order-pay' ) || is_account_page() ) {
                // phpcs:ignore
                return;
            }
            add_filter( 'woocommerce_cart_needs_payment', '__return_false' );
            add_filter( 'woocommerce_order_needs_payment', '__return_false' );
            // TODO: Add code to remove selected gateways from Order Pay Page
            add_filter( 'woocommerce_available_payment_gateways', '__return_empty_array' );
            // Conditionally remove shipping rates
            if ( true === filter_var( $remove_shipping, FILTER_VALIDATE_BOOLEAN ) ) {
                add_filter( 'woocommerce_package_rates', '__return_empty_array' );
                add_filter( 'woocommerce_cart_needs_shipping', '__return_false' );
            }
            // Remove checkout privacy text
            if ( true === filter_var( $remove_privacy_policy_text, FILTER_VALIDATE_BOOLEAN ) ) {
                remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20 );
            }
            // Remove checkout terms and condition
            if ( true === filter_var( $remove_checkout_terms_conditions, FILTER_VALIDATE_BOOLEAN ) ) {
                remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_terms_and_conditions_page_content', 30 );
            }
            add_action( 'wp_enqueue_scripts', array($this, 'enqueue_js') );
        }

        /**
         * Exclude Elements
         *
         * @return void()
         * @since 2.5.7
         */
        public function exclude_elements( $order_id = 0 ) {
            $items_list = array();
            if ( empty( WC()->cart->get_cart() ) ) {
                return false;
            }
            if ( 0 === $order_id && !empty( WC()->cart->get_cart() ) ) {
                $items_list = WC()->cart->get_cart();
            }
            // Exclude Product IDS from Place Order Without Payment
            if ( has_filter( 'wpowp_exclude_products' ) && !empty( $items_list ) ) {
                $exclude_products = apply_filters( 'wpowp_exclude_products', array() );
                if ( !empty( $exclude_products ) && array_intersect( array_column( $items_list, 'product_id' ), explode( ',', $exclude_products ) ) ) {
                    return true;
                }
            }
            // Exclude Product Categories from Place Order Without Payment
            if ( has_filter( 'wpowp_exclude_categories' ) && !empty( $items_list ) ) {
                $filter_categories = apply_filters( 'wpowp_exclude_categories', array() );
                $exclude_categories = ( !empty( $filter_categories ) ? explode( ',', $filter_categories ) : array() );
                if ( !empty( $exclude_categories ) ) {
                    $items = array_column( $items_list, 'product_id' );
                    foreach ( $items as $item ) {
                        if ( has_term( $exclude_categories, 'product_cat', $item ) ) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        /**
         * Quote Button
         *
         * @return void()
         * @since 2.3
         */
        public function quote_button() {
            if ( true === $this->exclude_quote_only_elements() ) {
                return;
            }
            $quote_only_text = \WPOWP\Inc\WPOWP_Admin::get_instance()->get_settings( 'quote_button_text' );
            $quote_only_text = ( 'Quote Only' === trim( $quote_only_text ) ? __( 'Quote Only', WPOWP_TEXT_DOMAIN ) : $quote_only_text );
            $quote_btn_text = apply_filters( 'wpowp_translate_quote_only_text', $quote_only_text );
            echo '<button type="submit" id="wpowp-quote-only" class="button wpowp-quote-only" href="#" onclick="wpowp_remove_payment_methods();">' . esc_html( $quote_btn_text ) . '</button>';
            // Remove WC Payment Methods on Quote Order Click
            echo '<script>function wpowp_remove_payment_methods(){ jQuery( ".wc_payment_methods, .payment_methods" ).remove(); }</script>';
        }

        /**
         * Exclude Elements
         *
         * @return void()
         * @since 2.5.7
         */
        public function exclude_quote_only_elements( $order_id = 0 ) {
            $items_list = array();
            if ( 0 === $order_id && !empty( WC()->cart->get_cart() ) ) {
                $items_list = WC()->cart->get_cart();
            }
            // Exclude Product IDS from Place Order Without Payment
            if ( has_filter( 'wpowp_quote_only_exclude_products' ) && !empty( $items_list ) ) {
                $exclude_products = apply_filters( 'wpowp_quote_only_exclude_products', array() );
                if ( !empty( $exclude_products ) && array_intersect( array_column( $items_list, 'product_id' ), explode( ',', $exclude_products ) ) ) {
                    return true;
                }
            }
            // Exclude Product Categories from Place Order Without Payment
            if ( has_filter( 'wpowp_quote_only_exclude_categories' ) && !empty( $items_list ) ) {
                $filter_categories = apply_filters( 'wpowp_quote_only_exclude_categories', array() );
                $exclude_categories = ( !empty( $filter_categories ) ? explode( ',', $filter_categories ) : array() );
                if ( !empty( $exclude_categories ) ) {
                    $items = array_column( $items_list, 'product_id' );
                    foreach ( $items as $item ) {
                        if ( has_term( $exclude_categories, 'product_cat', $item ) ) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        /**
         * Enqueue JS
         *
         * @param  void
         * @return void
         */
        public function enqueue_js() {
            if ( is_checkout() ) {
                wp_enqueue_script(
                    'wpowp-front',
                    WPOWP_URL . 'front/assets/js/wpowp-front.js',
                    array('jquery'),
                    null,
                    true
                );
            }
        }

        /**
         * Pre
         *
         * @return void()
         * @since 2.3
         */
        public function pre( $array, $stop ) {
            echo '<pre>' . print_r( $array ) . '</pre>';
            // phpcs:ignore
            if ( 1 == $stop && defined( 'WP_DEBUG' ) && false == WP_DEBUG ) {
                // phpcs:ignore
                wp_die();
            }
        }

        /**
         * Is WooCommerce Blocks Checkout
         *
         * @return bool
         * @since 2.6.1
         */
        public function is_woocommerce_blocks_checkout() {
            return \Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::is_checkout_block_default();
        }

        /**
         * WC Block Checkout.
         *
         * @return void
         * @since 2.6.1
         */
        public function wc_block_checkout() {
            $document_url = 'https://nitin247.com/docs/place-order-without-payment/no-payment-method-provided-error/?utm_source=wpowp&utm_campaign=wp-install&utm_medium=plugin&utm_term=WPOWP';
            echo '<div class="notice notice-error is-dismissible"><p>';
            echo sprintf(
                esc_html__( 'Place Order without payment for WooCommerce requires Classic Checkout.The %1$s[woocommerce_checkout]%2$s shortcode must be placed on Checkout page. Read documentation %5$sNo Payment Method Provided Error%6$s here.', WPOWP_TEXT_DOMAIN ),
                '<strong>',
                '</strong>',
                '<a href="http://wordpress.org/extend/plugins/woocommerce/">',
                '</a>',
                '<a href="' . esc_url( $document_url ) . '">',
                '</a>'
            );
            echo '</p></div>';
        }

    }

    // Initiate Loader
    WPOWP_Loader::get_instance();
    if ( !function_exists( 'wpowp_debug' ) ) {
        function wpowp_debug(  $array, $stop = 0  ) {
            WPOWP_Loader::get_instance()->pre( $array, $stop );
        }

    }
}
