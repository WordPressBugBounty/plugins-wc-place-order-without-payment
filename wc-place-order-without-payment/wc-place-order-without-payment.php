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
 * Version:           2.7.6
 * Author:            Nitin Prakash
 * Author URI:        https://nitin247.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpowp
 * Domain Path:       /languages/
 * Requires PHP:      7.4
 * Requires at least: 6.2
 * Tested up to: 7.0
 * WC requires at least: 8.2
 * WC tested up to: 10.9
 * Requires Plugins:  woocommerce
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! file_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) ) {
	wp_die( 'Plugin dependencies no installed!!!' );
} else {
	include_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
}

use WPOWP\WPOWP_Admin;
use WPOWP\WPOWP_Front;
use WPOWP\WPOWP_Rest_API;
use WPOWP\Modules\Rules as WPOWP_Rules;
use WPOWP\Traits\Get_Instance;
use WPOWP\Modules\PreOrderGateway as WPOWP_PreOrderPay;

defined( 'WPOWP_VERSION' ) or define( 'WPOWP_VERSION', '2.7.6' );
defined( 'WPOWP_FILE' ) or define( 'WPOWP_FILE', __FILE__ );
defined( 'WPOWP_BASE' ) or define( 'WPOWP_BASE', plugin_basename( WPOWP_FILE ) );
defined( 'WPOWP_DIR' ) or define( 'WPOWP_DIR', plugin_dir_path( WPOWP_FILE ) );
defined( 'WPOWP_URL' ) or define( 'WPOWP_URL', plugins_url( '/', WPOWP_FILE ) );
defined( 'WPOWP_PLUGIN_SLUG' ) or define( 'WPOWP_PLUGIN_SLUG', 'wpowp-settings' );
defined( 'WPOWP_PLUGIN_PREFIX' ) or define( 'WPOWP_PLUGIN_PREFIX', 'wpowp-' );
defined( 'WPOWP_FORM_PREFIX' ) or define( 'WPOWP_FORM_PREFIX', 'wpowp_' );
defined( 'WPOWP_TEMPLATES' ) or define( 'WPOWP_TEMPLATES', WPOWP_DIR . 'templates/' );


if ( ! function_exists( 'WPOWP\wpowp_fs' ) ) {
	// Create a helper function for easy SDK access.
	function wpowp_fs() {
		global $wpowp_fs;

		if ( ! isset( $wpowp_fs ) ) {
			// Activate multisite network integration.
			if ( ! defined( 'WP_FS__PRODUCT_4030_MULTISITE' ) ) {
				define( 'WP_FS__PRODUCT_4030_MULTISITE', true );
			}

			$wpowp_fs = fs_dynamic_init(
				array(
					'id'                  => '4030',
					'slug'                => 'wc-place-order-without-payment',
					'type'                => 'plugin',
					'public_key'          => 'pk_11c5a507e23c860c7e456326363ba',
					'is_premium'          => true,
					'premium_suffix'      => 'Pro',
					// If your plugin is a serviceware, set this option to false.
					'has_premium_version' => true,
					'has_addons'          => false,
					'has_paid_plans'      => true,
					'is_org_compliant'    => true,
					'trial'               => array(
						'days'               => 7,
						'is_require_payment' => false,
					),
					'menu'                => array(
						'slug'       => 'wpowp-settings',
						'first-path' => 'admin.php?page=wpowp-settings&tab=settings',
						'support'    => false,
						'contact'    => true,
					),
					'is_live'             => true,
					'anonymous_mode'      => true,
				)
			);
		}

		return $wpowp_fs;
	}

	// Init Freemius.
	wpowp_fs();
	// Signal that SDK was initiated.
	do_action( 'wpowp_fs_loaded' );

	wpowp_fs()->add_filter(
		'plugin_icon',
		function () {
			return WPOWP_DIR . 'assets/img/icon.png';
		}
	);
}

if ( ! class_exists( 'WPOWP_Loader' ) ) {

	final class WPOWP_Loader {

		use Get_Instance;

		/**
		 * Constructor
		 *
		 * @since 2.3
		 */
		private function __construct() {
			// Plugin Init
			add_action( 'init', array( $this, 'before_plugin_load' ) );
			// Add action links
			add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
			// Run Plugin
			// add_action( 'wp_loaded', array( $this, 'run_plugin' ), 20 );
			add_action( 'init', array( $this, 'init_admin' ), 20 );
			// HPOS Compatibility
			add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility' ), 30 );
			// Add WC_Email on WooCommerce Init
			add_action( 'woocommerce_init', array( $this, 'load_wc_email_class' ) );
			// Add Pre Order Gateway
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_pre_order_gateway' ) );
		}

		/**
		 * Before Plugin Load
		 *
		 * @return void
		 */
		public function before_plugin_load() {

			defined( 'WPOWP_NAME' ) or define( 'WPOWP_NAME', __( 'Place Order Without Payment', 'wpowp' ) );
			defined( 'WPOWP_SHORT_NAME' ) or define( 'WPOWP_SHORT_NAME', __( 'Place Order', 'wpowp' ) );
			defined( 'WPOWP_API_ERROR_TEXT' ) or define( 'WPOWP_API_ERROR_TEXT', __( 'Error Processing data!', 'wpowp' ) );
			defined( 'WPOWP_ADMIN_CONFIRM_RESET_TEXT' ) or define( 'WPOWP_ADMIN_CONFIRM_RESET_TEXT', __( 'This action is not recoverable. Are you sure you want to reset this setting?', 'wpowp' ) );

			if ( ! class_exists( 'woocommerce' ) ) {
				add_action( 'admin_notices', array( $this, 'wc_not_active' ) );
				return;
			}

			if ( $this->is_woocommerce_blocks_checkout() ) {
				add_action( 'admin_notices', array( $this, 'wc_block_checkout' ) );
				return;
			}

			load_plugin_textdomain( 'wpowp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Run Plugin
		 *
		 * @return void
		 */
		public function run_plugin() {

			// ensure singletons
			WPOWP_Front::get_instance();
			WPOWP_Rest_API::get_instance();

			// Register Store API injector EARLY
			$this->register_rest_injector();
			// Call Disable Payment
			$this->disable_payment();
		}

		/**
		 * Init Admin
		 *
		 * @return void
		 */
		public function init_admin() {

			if ( is_admin() ) {
				// Init Admin
				WPOWP_Admin::get_instance();
			}
		}

		/**
		 * Action Links
		 *
		 * @param  array $links
		 * @return array
		 */
		public function action_links( $links ) {
			$links = array_merge(
				array(
					'<a href="' . esc_url(
						admin_url( 'admin.php?page=wpowp-settings&tab=settings' )
					) . '">' . __( 'Settings', 'wpowp' ) . '</a>',
					'<a target="blank" href="' . esc_url( 'https://nitin247.com/support/' ) . '">' . __( 'Support Desk', 'wpowp' ) . '</a>',
				),
				$links
			);
			return $links;
		}

		/**
		 * WC not active.
		 *
		 * @return void
		 * @since 2.3
		 */
		public function wc_not_active() {
			$install_url = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'install-plugin',
						'plugin' => 'woocommerce',
					),
					admin_url( 'update.php' )
				),
				'install-plugin_woocommerce'
			);
			echo '<div class="notice notice-error is-dismissible"><p>';
			// translators: 1$-2$: opening and closing <strong> tags, 3$-4$: link tags, takes to woocommerce plugin on wp.org, 5$-6$: opening and closing link tags, leads to plugins.php in admin.
			printf( esc_html__( '%1$sPlace Order without payment for WooCommerce is inactive.%2$s The %3$sWooCommerce plugin%4$s must be active for Place Order without payment for WooCommerce to work. Please %5$sinstall & activate WooCommerce &raquo;%6$s', 'wpowp' ), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . esc_url( $install_url ) . '">', '</a>' );
			echo '</p></div>';
		}

		/**
		 * Show Quote Button.
		 *
		 * @return void
		 * @since 2.3
		 */
		public function show_quote_button() {

			$admin_instance = WPOWP_Admin::get_instance();
			$quote_btn_pos  = $admin_instance->get_settings( 'quote_button_postion' );

			// Add actions if the quote button should be shown
			add_action( 'woocommerce_review_order_' . $quote_btn_pos, array( $this, 'quote_button' ) );
			add_action( 'wc_ajax_checkout', array( $this, 'disable_payment' ), 0 );
		}

		/**
		 * Disable Payment.
		 *
		 * @return void
		 * @since 2.3
		 */
		public function disable_payment() {

			$skip_payment = false;
			$options      = WPOWP_Admin::get_instance()->get_settings();

			if ( wpowp_fs()->is_paying_or_trial() ) {

				// Skip Payment for premium only
				$process_rules = wpowp_process_rules();
				$allow_payment = isset( $process_rules['allowPaymentsSwitch'] ) ? filter_var( $process_rules['allowPaymentsSwitch'], FILTER_VALIDATE_BOOLEAN ) : false;

				// Show/Hide Quote button
				$quote_only     = filter_var( $options['quote_only'], FILTER_VALIDATE_BOOLEAN );
				$show_quote_btn = $quote_only || ( ! empty( $process_rules ) && $process_rules['requestQuoteSwitch'] );

				if ( $show_quote_btn ) {
					$this->show_quote_button();
				}

				// Skip Payment based on Saved Rules
				if ( ! empty( $process_rules ) && filter_var( $process_rules['placeOrderSwitch'], FILTER_VALIDATE_BOOLEAN ) ) {
					$skip_payment = true;
				}
			}

			// Allow payment gateways
			if ( $allow_payment ) {
					return;
			}

			// Skip Payment sitewide
			if ( ! empty( filter_var( $options['enable_sitewide'], FILTER_VALIDATE_BOOLEAN ) ) && ! is_admin() ) {
				$skip_payment = true;
			}

			// Skip payment if necessary or Directly disable payment - avoid calling skip_payment() which re-checks rules
			if ( ! $skip_payment || $this->exclude_elements() ) {
				return;
			}

			$remove_shipping                  = $options['remove_shipping'] ?? '';
			$remove_privacy_policy_text       = $options['remove_privacy_policy_text'] ?? '';
			$remove_checkout_terms_conditions = $options['remove_checkout_terms_conditions'] ?? '';

			if ( ( isset( $_GET['key'] ) && isset( $_GET['pay_for_order'] ) ) || is_account_page() ) {
				return;
			}

			add_filter( 'woocommerce_cart_needs_payment', '__return_false' );
			add_filter( 'woocommerce_order_needs_payment', '__return_false' );
			// add_filter( 'woocommerce_available_payment_gateways', '__return_empty_array' );

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

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_js' ) );
		}

		/**
		 * Exclude Elements
		 *
		 * @return void()
		 * @since 2.5.7
		 */
		public function exclude_elements( $order_id = 0 ) {

			$items_list = array();

			if ( ! is_checkout() || empty( WC()->cart->get_cart() ) ) {
				return false;
			}

			if ( 0 === $order_id && ! empty( WC()->cart->get_cart() ) ) {
				$items_list = WC()->cart->get_cart();
			}

			// Exclude Product IDS from Place Order Without Payment

			if ( has_filter( 'wpowp_exclude_products' ) && ! empty( $items_list ) ) {
				$exclude_products = apply_filters( 'wpowp_exclude_products', array() );
				if ( ! empty( $exclude_products ) && array_intersect( array_column( $items_list, 'product_id' ), explode( ',', $exclude_products ) ) ) {
					return true;
				}
			}

			// Exclude Product Categories from Place Order Without Payment

			if ( has_filter( 'wpowp_exclude_categories' ) && ! empty( $items_list ) ) {
				$filter_categories  = apply_filters( 'wpowp_exclude_categories', array() );
				$exclude_categories = ! empty( $filter_categories ) ? explode( ',', $filter_categories ) : array();
				if ( ! empty( $exclude_categories ) ) {
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

			$quote_only_text = WPOWP_Admin::get_instance()->get_settings( 'quote_button_text' );
			$quote_only_text = ( 'Quote Only' === trim( $quote_only_text ) ) ? __( 'Quote Only', 'wpowp' ) : $quote_only_text;
			$quote_btn_text  = apply_filters( 'wpowp_translate_quote_only_text', $quote_only_text );

			$quote_btn_label = apply_filters( 'wpowp_quote_btn_label', '' );

			if ( ! empty( $quote_btn_label ) ) {
				echo '<span class="wpowp-quote-only-label">' . esc_html( $quote_btn_label ) . '</span>';
			}

			echo '<input id="wpowp-order-type" type="hidden" name="wpowp_order_type" value="no" />';
			echo '<button type="submit" id="wpowp-quote-only" class="button wpowp-quote-only" href="#" onclick="wpowp_remove_payment_methods();">' . esc_html( $quote_btn_text ) . '</button>';
			// Remove WC Payment Methods on Quote Order Click
			echo '<script>function wpowp_remove_payment_methods(){ jQuery( "#wpowp-order-type" ).val( "quote" ); console.log( jQuery( "#wpowp-order-type" ).val() ) /* jQuery( ".wc_payment_methods, .payment_methods" ).remove(); */ }</script>';
		}

		/**
		 * Exclude Elements
		 *
		 * @return void()
		 * @since 2.5.7
		 */
		public function exclude_quote_only_elements( $order_id = 0 ) {

			$items_list = array();

			if ( 0 === $order_id && ! empty( WC()->cart->get_cart() ) ) {
				$items_list = WC()->cart->get_cart();
			}

			// Exclude Product IDS from Place Order Without Payment

			if ( has_filter( 'wpowp_quote_only_exclude_products' ) && ! empty( $items_list ) ) {
				$exclude_products = apply_filters( 'wpowp_quote_only_exclude_products', array() );
				if ( ! empty( $exclude_products ) && array_intersect( array_column( $items_list, 'product_id' ), explode( ',', $exclude_products ) ) ) {
					return true;
				}
			}

			// Exclude Product Categories from Place Order Without Payment

			if ( has_filter( 'wpowp_quote_only_exclude_categories' ) && ! empty( $items_list ) ) {
				$filter_categories  = apply_filters( 'wpowp_quote_only_exclude_categories', array() );
				$exclude_categories = ! empty( $filter_categories ) ? explode( ',', $filter_categories ) : array();
				if ( ! empty( $exclude_categories ) ) {
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
					WPOWP_URL . 'assets/js/wpowp-front.js',
					array( 'jquery' ),
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

			echo '<pre>' . print_r( $array ) . '</pre>'; // phpcs:ignore

			if ( 1 == $stop && defined( 'WP_DEBUG' ) && false == WP_DEBUG ) { // phpcs:ignore
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
			// translators: 1: opening <strong> tag, 2: closing </strong> tag, 5: opening <a> tag linking to documentation, 6: closing </a> tag.
			printf( esc_html__( 'Place Order without payment for WooCommerce requires Classic Checkout.The %1$s[woocommerce_checkout]%2$s shortcode must be placed on Checkout page. Read documentation %5$sNo Payment Method Provided Error%6$s here.', 'wpowp' ), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . esc_url( $document_url ) . '">', '</a>' );
			echo '</p></div>';
		}

		/**
		 * Declare Compatibility
		 *
		 * @return void
		 * @since 2.6.3
		 */
		public function declare_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WPOWP_FILE, true );
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'analytics', WPOWP_FILE, true );
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'new_navigation', WPOWP_FILE, true );
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', WPOWP_FILE, true );
			}
		}

		/**
		 * Load WC Email Classes
		 */
		public function load_wc_email_class() {
			if ( ! class_exists( 'WC_Email' ) ) {
				include_once WC()->plugin_path() . '/includes/emails/class-wc-email.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomFunction
			}
		}

		/**
		 * Front Process Rules
		 */
		public function front_process_rules( $rules = array() ) {

			if ( empty( $rules ) ) {
				$rules = WPOWP_Rest_API::get_instance()->fetch_rules( 0 );
			}

			$processed_rules = WPOWP_Rules::get_instance()->process_rules( $rules );
			return $processed_rules;
		}

		/**
		 * Register REST Injector
		 *
		 * Register the very-early REST injector and store-api hooks.
		 * MUST NOT call code that accesses WC()->cart or session.
		 */
		public function register_rest_injector() {

			add_filter(
				'rest_pre_dispatch',
				function ( $response, $server, $request ) {

					// Only Store API checkout
					if ( ! method_exists( $request, 'get_route' ) ) {
						return $response;
					}

					if ( strpos( $request->get_route(), '/wc/store/v1/checkout' ) === false ) {
						return $response;
					}

					// Load WC Cart
					if ( is_null( WC()->cart ) ) {
						wc_load_cart();
					}

					// EARLY decision (NO CART)
					if ( ! $this->should_inject_payment_method_early() ) {
						return $response;
					}

					// Inject COD so Store API validation passes
					if ( empty( $request->get_param( 'payment_method' ) ) ) {
						$request->set_param( 'payment_method', 'wpowp_preorder' );
					}

					// Flag for later phase
					$request->set_param( 'wpowp_skip_payment_injected', true );

					return $response;
				},
				1,
				3
			);

			// LATE phase: order exists, cart is safe
			add_action(
				'woocommerce_store_api_checkout_update_order_from_request',
				array( $this, 'blocks_skip_payment' ),
				20,
				2
			);
		}

		/**
		 * Should Inject Payment Method Early
		 *
		 * EARLY / SAFE rule evaluation
		 * Used ONLY to decide whether to inject a payment method.
		 * Must NOT touch WC()->cart.
		 */
		private function should_inject_payment_method_early() {

			// Sitewide option
			$opts = WPOWP_Admin::get_instance()->get_settings();
			if ( isset( $opts['enable_sitewide'] ) && filter_var( $opts['enable_sitewide'], FILTER_VALIDATE_BOOLEAN ) ) {
				return filter_var( $opts['enable_sitewide'], FILTER_VALIDATE_BOOLEAN );
			}

			// Lightweight rule evaluation (no cart)
			$process_rules = wpowp_process_rules();
			$allow_payment = isset( $process_rules['allowPaymentsSwitch'] ) ? filter_var( $process_rules['allowPaymentsSwitch'], FILTER_VALIDATE_BOOLEAN ) : false;
			$place_order   = isset( $process_rules['placeOrderSwitch'] ) ? filter_var( $process_rules['placeOrderSwitch'], FILTER_VALIDATE_BOOLEAN ) : false;

			// Do not inject payment method
			if ( $allow_payment ) {
				return false;
			}

			return $place_order;
		}

		/**
		 * Blocks Skip Payment
		 *
		 * Modify the in-memory Store API order to skip payment when rules indicate.
		 * Hook: woocommerce_store_api_checkout_update_order_from_request
		 *
		 * @param WC_Order|WC_Abstract_Order $order
		 * @param WP_REST_Request|array      $request
		 */
		public function blocks_skip_payment( $order, $request ) {

			if ( ! $order || ! is_object( $order ) ) {
				return;
			}

			// Injector flag
			$injected = (
			$request instanceof WP_REST_Request
			&& $request->get_param( 'wpowp_skip_payment_injected' )
			);

			$process_rules = wpowp_process_rules();
			$place_order   = isset( $process_rules['placeOrderSwitch'] ) ? filter_var( $process_rules['placeOrderSwitch'], FILTER_VALIDATE_BOOLEAN ) : false;

			// Full rules (cart-aware) only run here
			if ( ! $injected || ! $place_order ) {
				return;
			}
		}

		/**
		 * Add the PreOrderPay gateway to the list of available payment gateways.
		 *
		 * @param array $gateways The list of available payment gateways.
		 * @return array The list of available payment gateways with the PreOrderPay gateway added.
		 */
		public function add_pre_order_gateway( $gateways ) {
			$gateways[] = WPOWP_PreOrderPay::class;
			return $gateways;
		}
	}

	// Initiate Loader
	$loader = WPOWP_Loader::get_instance();

	// Register only the REST injector & Store API hooks very early (safe)
	add_action( 'init', array( $loader, 'register_rest_injector' ), 1 );

	// Run full plugin init later (safe for WC()->cart calls)
	add_action( 'wp_loaded', array( $loader, 'run_plugin' ), 10 );


	if ( ! function_exists( 'wpowp_debug' ) ) {
		function wpowp_debug( $array, $stop = 0 ) {
			WPOWP_Loader::get_instance()->pre( $array, $stop );
		}
	}

	if ( ! function_exists( 'wpowp_process_rules' ) ) {
		function wpowp_process_rules() {
			return WPOWP_Loader::get_instance()->front_process_rules();
		}
	}

	if ( ! function_exists( 'wpowp_disable_payment' ) ) {
		function wpowp_disable_payment() {
			return WPOWP_Loader::get_instance()->disable_payment();
		}
	}
}
