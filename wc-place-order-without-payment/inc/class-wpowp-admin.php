<?php

/**
 * Admin Class
 *
 * @package WPOWP
 * @since 2.3
 */

namespace WPOWP\Inc;

use WPOWP\Inc\Traits\Get_Instance;

if ( ! class_exists( 'WPOWP_Admin' ) ) {
	class WPOWP_Admin {

		use Get_Instance;

		/**
		* Default options
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
				__( 'Books Shortcode Reference', WPOWP_TEXT_DOMAIN ),
				__( 'Shortcode Reference', WPOWP_TEXT_DOMAIN ),
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
				'add_cart_text'                    => __( 'Buy Now', WPOWP_TEXT_DOMAIN ),
				'free_product_on_cart'             => false,
				'free_product_on_checkout'         => false,
				'free_product'                     => false,
				'free_product_text'                => __( 'FREE', WPOWP_TEXT_DOMAIN ),
				'quote_only'                       => false,
				'quote_button_postion'             => 'after_submit',
				'quote_button_text'                => __( 'Qoute Only', WPOWP_TEXT_DOMAIN ),
				'remove_shipping'                  => false,
				'remove_privacy_policy_text'       => false,
				'remove_checkout_terms_conditions' => false,
				'standard_add_cart'                => false,
				'order_button_text'                => __( 'Place Order', WPOWP_TEXT_DOMAIN ),
				'hide_place_order_button'          => false,
				'remove_taxes'                     => false,
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

			$template_file = ( true === $append_php ) ? WPOWP_TEMPLATES . $tab . '.php' : WPOWP_TEMPLATES . $tab;

			if ( ! file_exists( $template_file ) ) {
				return;
			}

			require_once $template_file; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		}

		/**
		 * Get settings
		 *
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
			wp_enqueue_style( 'wpowp-toastr', WPOWP_URL . 'admin/assets/css/wpowp-admin.css', array(), array(), false );

			wp_enqueue_script(
				'wpowp-admin-rest',
				WPOWP_URL . 'admin/assets/js/wpowp-admin-rest.js',
				array( 'wp-api' ),
				null,
				true
			);

			wp_localize_script(
				'wpowp-admin-rest',
				'wpowp_admin_rest',
				array(
					'confirm_reset_text' => WPOWP_ADMIN_CONFIRM_RESET_TEXT,
				)
			);

			wp_enqueue_script(
				'wpowp-toast',
				WPOWP_URL . 'admin/assets/js/wpowp-toastr.min.js',
				array( 'jquery' ),
				null,
				true
			);

		}

		/**
		 * Replace Footer
		 *
		 * @return void
		 * @since 2.3
		 */
		public function replace_footer( $text ) {

			if ( isset( $_GET['page'] ) && 'wpowp-settings' === sanitize_text_field( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$text = __( 'Like Place Order Without Payment? Give it a', WPOWP_TEXT_DOMAIN );

				$text .= ' <a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/wc-place-order-without-payment/reviews/?rate=5#new-post">';

				$text .= __( '★★★★★', WPOWP_TEXT_DOMAIN ) . '</a>' . __( ' rating. A huge thanks in advance!', WPOWP_TEXT_DOMAIN );
			}

			return $text;
		}

	}

	WPOWP_Admin::get_instance();

}
