<?php

/**
 * Admin Class
 *
 * @package WPOWP
 * @since 2.3
 */

namespace WPOWP;

use WPOWP\Traits\Get_Instance;

use WPOWP\Modules\Rules as WPOWP_Rules;
use WPOWP\Helper as WPOWP_Helper;
use WPOWP\Modules\PendingPaymentNotification as WPOWP_PendingPaymentEmail;

if ( ! class_exists( 'WPOWP_Admin' ) ) {
	class WPOWP_Admin {

		use Get_Instance;

		/**
		 * Default options
		 *
		 * @var array
		 */

		private $settings = array();

		/**
		 * Construct
		 *
		 * @return void
		 * @since 2.3
		 */
		public function __construct() {
			$this->default_settings();
			// Add Admin menu
			add_action( 'admin_menu', array( $this, 'menu_admin' ), 9 );
			add_action( 'admin_init', array( $this, 'init_admin' ), 10 );
			// Admin Footer
			add_filter( 'admin_footer_text', array( $this, 'replace_footer' ) );
			add_filter( 'update_footer', array( $this, 'replace_version' ), 99 );

			if ( wpowp_fs()->is_paying_or_trial() ) {
				// Add to WooCommerce Email Classes
				add_filter( 'woocommerce_email_classes', array( $this, 'add_order_notification_email' ) );
				// Trigger the custom email when order status changes to pending
				add_action( 'woocommerce_order_status_changed', array( $this, 'send_order_notification' ), 10, 3 );
			}
		}

		/**
		 * Menu Admin
		 *
		 * @return void
		 * @since 2.3
		 */
		public function menu_admin() {
			add_menu_page( WPOWP_SHORT_NAME, WPOWP_SHORT_NAME, 'manage_options', WPOWP_PLUGIN_SLUG, array( $this, 'menu_settings' ), 'dashicons-store', 26 );
			add_submenu_page(
				'admin.php?page=wpowp-settings',
				__( WPOWP_SHORT_NAME, 'wpowp' ),
				__( WPOWP_SHORT_NAME, 'wpowp' ),
				'manage_options',
				'books-shortcode-ref',
				'books_ref_page_callback'
			);
		}

		/**
		 * Default Settings
		 *
		 * @return void
		 * @since 2.3
		 */
		private function default_settings() {
			$this->settings = array(
				'skip_cart'                        => false,
				'order_status'                     => 'processing', // Default order status after place order
				'add_cart_text'                    => __( 'Buy Now', 'wpowp' ),
				'free_product_on_cart'             => false,
				'free_product_on_checkout'         => false,
				'free_product'                     => false,
				'free_product_text'                => __( 'FREE', 'wpowp' ),
				'quote_only'                       => false,
				'quote_button_postion'             => 'after_submit',
				'quote_button_text'                => __( 'Qoute Only', 'wpowp' ),
				'remove_shipping'                  => false,
				'remove_privacy_policy_text'       => false,
				'remove_checkout_terms_conditions' => false,
				'standard_add_cart'                => false,
				'order_button_text'                => __( 'Place Order', 'wpowp' ),
				'hide_place_order_button'          => false,
				'remove_taxes'                     => false,
				'enable_sitewide'                  => true,
				'hide_price'                       => 'no',
				'hide_additional_info_tab'         => 'no',
				'hide_prices_sitewide'             => 'no',
			);
		}

		/**
		 * Menu Settings
		 *
		 * @return void
		 * @since 2.3
		 */
		public function menu_settings() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$default_tab = WPOWP_PLUGIN_SLUG;

			$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $default_tab; // phpcs:ignore
			$tab = 'admin/' . str_replace( 'wpowp-', '', $tab );

			$this->load_admin( $tab );
		}

		/**
		 * Order Status List
		 *
		 * @return void
		 * @since 2.3
		 */
		private function order_status_list() {
			$order_statuses = wc_get_order_statuses();
			$statuses       = array();
			foreach ( $order_statuses as $key => $status ) {
				$statuses[ str_replace( 'wc-', '', $key ) ] = $status;
			}
			return $statuses;
		}

		/**
		 * Load Admin
		 *
		 * @param  string  $tab
		 * @param  boolean $append_php
		 * @return void
		 * @since 2.3
		 */
		public function load_admin( $tab, $append_php = true ) {

			$allowed_tabs  = array( 'admin/settings', 'admin/rules', 'admin/quote-only' );
			$template_file = ( true === $append_php ) ? WPOWP_TEMPLATES . $tab . '.php' : WPOWP_TEMPLATES . $tab;

			if ( ! in_array( $tab, $allowed_tabs, true ) || ! file_exists( $template_file ) ) {
				wp_die( esc_html_e( 'Invalid path', 'wpowp' ) );
			}

			require_once $template_file; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		}

		/**
		 * Get settings
		 *
		 * @param  string  $setting_name
		 * @param  boolean $skip_merge
		 * @return settings
		 * @since 2.3
		 */
		public function get_settings( $setting_name = '', $skip_merge = false ) {

			$option = get_option( 'wpowp_settings', true );

			if ( true === $skip_merge ) {
				$this->settings = $option;
			} else {
				$this->settings = wp_parse_args( $option, $this->settings );
			}

			if ( ! empty( $setting_name ) && isset( $this->settings[ $setting_name ] ) ) {
				return $this->settings[ $setting_name ];
			}

			return $this->settings;
		}

		/**
		 * Set Option
		 *
		 * $param string $option_name
		 * $param string $option_value
		 *
		 * @return array
		 * @since 2.3
		 */
		public function set_option( $option_name, $option_value ) {

			if ( ! empty( $option_name ) && ! empty( $option_value ) ) {
				return update_option( $option_name, $option_value );
			}

			return 0;
		}

		/**
		 * Init Admin
		 *
		 * @return void
		 * @since 2.3
		 */
		public function init_admin() {

			if ( isset( $_GET['page'] ) && $_GET['page'] === 'wpowp-settings' ) { // phpcs:ignore

				wp_enqueue_style( 'wpowp-bootstrap', WPOWP_URL . 'assets/css/bootstrap.min.css', array(), '5.0.2' );
				wp_enqueue_style( 'wpowp-select2', plugins_url( '', WC_PLUGIN_FILE ) . '/assets/css/select2.css', array(), array(), false );
				wp_enqueue_style( 'wpowp-toastr', WPOWP_URL . 'assets/css/wpowp-admin.css', array(), array(), false );
				wp_enqueue_style( 'dashicons' );

				wp_enqueue_script( 'wpowp-bootstrap', WPOWP_URL . 'assets/js/bootstrap.min.js', array( 'jquery' ), WPOWP_VERSION, true );
				wp_enqueue_script( 'selectWoo' );

				if ( isset( $_GET['tab'] ) && 'rules' === $_GET['tab'] ) { // phpcs:ignore
					wp_enqueue_script( 'wpowp-rules', WPOWP_URL . 'assets/js/wpowp-rules.js', array( 'wp-api' ), WPOWP_VERSION, true );
				}
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-sortable' );

				wp_enqueue_script(
					'wpowp-admin-rest',
					WPOWP_URL . 'assets/js/wpowp-admin-rest.js',
					array( 'wp-api' ),
					null,
					true
				);

				$helper_instance = WPOWP_Helper::get_instance();

				$localized_data = array(
					'nonce'       => wp_create_nonce( 'wp_rest' ),
					'restApiBase' => get_rest_url() . 'wpowp-api/action/',
					'labels'      => array(
						'add_rule'           => __( 'Add Rule', 'wpowp' ),
						'confirm_reset_text' => WPOWP_ADMIN_CONFIRM_RESET_TEXT,
					),
					'lists'       => array(
						'user_roles' => $helper_instance->get_roles_list(),
					),
				);

				wp_localize_script(
					'wpowp-admin-rest',
					'wpowp_admin_rest',
					$localized_data
				);

				wp_enqueue_script(
					'wpowp-toast',
					WPOWP_URL . 'assets/js/wpowp-toastr.min.js',
					array( 'jquery' ),
					null,
					true
				);
			}
		}

		/**
		 * Replace Footer
		 *
		 * @param string $text
		 * @return void
		 * @since 2.3
		 */
		public function replace_footer( $text ) {

			if ( isset( $_GET['page'] ) && 'wpowp-settings' === sanitize_text_field( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$text = __( 'Like Place Order Without Payment? Give it a', 'wpowp' );

				$text .= ' <a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/wc-place-order-without-payment/reviews/?rate=5#new-post">';

				$text .= __( '★★★★★', 'wpowp' ) . '</a>' . __( ' rating. A huge thanks in advance!', 'wpowp' );
			}

			return $text;
		}

		/**
		 * Replace Version
		 *
		 * @param string $text
		 * @return void
		 * @since 2.3
		 */
		public function replace_version( $text ) {

			if ( isset( $_GET['page'] ) && 'wpowp-settings' === sanitize_text_field( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$text = __( 'Version ', 'wpowp' ) . WPOWP_VERSION;
			}

			return $text;
		}

		/**
		 * Add the pending payment notification email class to the list of email classes.
		 *
		 * @param array $email_classes The list of email classes.
		 * @return array The updated list of email classes.
		 * @since 2.6.6
		 * @version 2.6.6
		 */
		public function add_order_notification_email( $email_classes ) {
			$email_classes['WPOWP_Pending_Payment_Email'] = new WPOWP_PendingPaymentEmail();
			return $email_classes;
		}

		/**
		 * Send order notification when order status changes to pending, approved, rejected.
		 *
		 * @param int    $order_id The ID of the order.
		 * @param string $old_status The old order status.
		 * @param string $new_status The new order status.
		 * @return void
		 * @since 2.6.6
		 */
		public function send_order_notification( $order_id, $old_status, $new_status ) {
			// Check if the new status is 'pending'
			if ( $new_status === 'pending' ) {
				$email = new WPOWP_PendingPaymentEmail();
				$email->trigger( $order_id );
			}
		}
	}

}
