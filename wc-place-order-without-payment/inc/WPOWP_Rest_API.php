<?php

/**
 * REST Class
 *
 * @package WPOWP
 * @since 2.3
 */

namespace WPOWP;

use WPOWP\Traits\Get_Instance;
use WPOWP\Helper;

if ( ! class_exists( 'WPOWP_Rest_API' ) ) {
	class WPOWP_Rest_API extends \WP_REST_Controller {

		use Get_Instance;

		protected $namespace = 'wpowp-api';
		protected $rest_base = 'action';
		protected $option    = 'wpowp-rules';
		protected $response  = array(
			'success' => false,
			'message' => WPOWP_API_ERROR_TEXT,
		);

		public function __construct() {
			// Init API Routes
			add_action( 'rest_api_init', array( $this, 'init_admin' ) );
		}

		/**
		 * Init REST API Admin
		 *
		 * @return void
		 */

		public function init_admin() {

			$this->rest_base . '/save-settings';

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/save-settings',
				array(
					array(
						'methods'             => \WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'save_settings' ),
						'permission_callback' => array( $this, 'get_write_api_permission_check' ),
						'args'                => array(
							'settings' => array(
								'description'       => __( 'Plugin settings', 'wpowp' ),
								'type'              => 'json',
								'validate_callback' => 'rest_validate_request_arg',
								'sanitize_callback' => array( $this, 'sanitize_request' ),
							),
						),
					),
				)
			);

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/reset-settings',
				array(
					array(
						'methods'             => \WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'reset_settings' ),
						'permission_callback' => array( $this, 'get_write_api_permission_check' ),
						'args'                => array(
							'settings' => array(
								'description'       => __( 'Plugin settings', 'wpowp' ),
								'type'              => 'json',
								'validate_callback' => 'rest_validate_request_arg',
								'sanitize_callback' => array( $this, 'sanitize_request' ),
							),
						),
					),
				)
			);

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/fetch-product',
				array(
					array(
						'methods'             => \WP_REST_Server::READABLE,
						'callback'            => array( $this, 'fetch_products' ),
						'permission_callback' => array( $this, 'get_write_api_permission_check' ),
						'args'                => array(
							'term' => array(
								'description'       => __( 'Term', 'wpowp' ),
								'type'              => 'json',
								'validate_callback' => 'rest_validate_request_arg',
								'sanitize_callback' => array( $this, 'sanitize_request' ),
							),
						),
					),
				)
			);

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/tyrules/save',
				array(
					array(
						'methods'             => \WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'save_rules' ),
						'permission_callback' => array( $this, 'get_write_api_permission_check' ),
						'args'                => array(
							'settings' => array(
								'description'       => __( 'Thank You Rules', 'wc-thanks-redirect' ),
								'type'              => 'json',
								'validate_callback' => 'rest_validate_request_arg',
								'sanitize_callback' => array( $this, 'sanitize_request' ),
							),
						),
					),
				)
			);

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/tyrules/fetch',
				array(
					array(
						'methods'             => \WP_REST_Server::READABLE,
						'callback'            => array( $this, 'fetch_rules' ),
						'permission_callback' => '__return_true',
					),
				)
			);

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/tyrules/search-product',
				array(
					array(
						'methods'             => \WP_REST_Server::READABLE,
						'callback'            => array( $this, 'search_product' ),
						'permission_callback' => array( $this, 'get_write_api_permission_check' ),
					),
				)
			);

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/tyrules/search-category',
				array(
					array(
						'methods'             => \WP_REST_Server::READABLE,
						'callback'            => array( $this, 'search_category' ),
						'permission_callback' => array( $this, 'get_write_api_permission_check' ),
					),
				)
			);

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/tyrules/search-tag',
				array(
					array(
						'methods'             => \WP_REST_Server::READABLE,
						'callback'            => array( $this, 'search_tags' ),
						'permission_callback' => array( $this, 'get_write_api_permission_check' ),
					),
				)
			);

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/tyrules/products/details',
				array(
					array(
						'methods'             => \WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'product_details' ),
						'permission_callback' => array( $this, 'get_write_api_permission_check' ),
					),
				)
			);

			register_rest_route(
				$this->namespace,
				$this->rest_base . '/tyrules/post/terms',
				array(
					array(
						'methods'             => \WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'get_postterms' ),
						'permission_callback' => array( $this, 'get_write_api_permission_check' ),
					),
				)
			);

		}

		public function save_settings( \WP_REST_Request $request ) {

			$default_settings = \WPOWP\WPOWP_Admin::get_instance()->get_settings( '', true );
			$posted_data      = $this->sanitize_clean( $request->get_param( 'settings' ) );
			$settings         = wp_parse_args( $posted_data, $default_settings );

			WPOWP_Admin::get_instance()->set_option( 'wpowp_settings', ( $settings ) );

			$this->response['success'] = true;
			$this->response['message'] = __( 'Settings Saved', 'wpowp' );

			return rest_ensure_response( $this->response );

		}

		public function reset_settings() {

			// Delete WPOWP Settings option
			delete_option( 'wpowp_settings' );

			$this->response['success'] = true;
			$this->response['message'] = __( 'Settings Reset', 'wpowp' );

			return rest_ensure_response( $this->response );

		}

		public function fetch_products( \WP_REST_Request $request ) {

			$default_settings = \WPOWP\WPOWP_Admin::get_instance()->get_settings( '', true );
			$posted_data      = $this->sanitize_clean( $request->get_param( 'settings' ) );
			$settings         = wp_parse_args( $posted_data, $default_settings );

			WPOWP_Admin::get_instance()->set_option( 'wpowp_settings', ( $settings ) );

			$this->response['success'] = true;
			$this->response['message'] = __( 'Settings Saved', 'wpowp' );

			return rest_ensure_response( $this->response );

		}

		public function sanitize_request( $data, $skip_clean = 0 ) {
			return ( 0 === $skip_clean ) ? json_decode( $data, true ) : wc_clean( json_decode( $data, true ) );
		}

		private function sanitize_clean( $posted_data ) {
			$data = array();
			if ( is_array( $posted_data ) ) {
				foreach ( $posted_data as $post_array ) {
					$data[ str_replace( WPOWP_FORM_PREFIX, '', $post_array['name'] ) ] = sanitize_text_field( $post_array['value'] );
				}
				return $data;
			} else {
				return sanitize_text_field( $data );
			}
		}

		public function save_rules( \WP_REST_Request $request ) {

			$posted_data = $request->get_params();
			$tyrules     = isset( $posted_data['rules'] ) ? $posted_data['rules'] : '';

			update_option( $this->option, $tyrules );

			return new \WP_REST_Response( $tyrules, 200 );

		}

		public function search_product() {

			$keyword         = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
			$variations_only = isset( $_GET['variations_only'] ) ? sanitize_text_field( $_GET['variations_only'] ) : false;

			$products = Helper::search_product( $keyword, $variations_only );
			return $products;

		}

		public function search_category() {

			$keyword   = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
			$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : 'post';

			$products = Helper::search_categories( $keyword, $post_type );
			return $products;

		}

		public function search_tags() {

			$keyword   = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
			$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : 'post';

			$products = Helper::search_tags( $keyword, $post_type );
			return $products;

		}

		public function product_details( \WP_REST_Request $request ) {
			// Get the product IDs from the request
			$product_ids = $request->get_param( 'ids' );

			// Check if product_ids is provided and is an array
			if ( empty( $product_ids ) || ! is_array( $product_ids ) ) {
				return new \WP_REST_Response(
					array(
						'success' => false,
						'message' => 'Invalid product IDs provided.',
					),
					400
				);
			}

			// Initialize an empty array to store product details
			$products = Helper::product_details( $product_ids );

			// Return the response
			return new \WP_REST_Response(
				array(
					'success' => true,
					'data'    => $products,
				),
				200
			);
		}

		public function get_postterms( \WP_REST_Request $request ) {
			// Get the product IDs from the request
			$term_ids = $request->get_param( 'ids' );
			$taxonomy = $request->get_param( 'taxonomy' );

			// Check if product_ids is provided and is an array
			if ( empty( $term_ids ) || ! is_array( $term_ids ) ) {
				return new \WP_REST_Response(
					array(
						'success' => false,
						'message' => 'Invalid term IDs provided.',
					),
					400
				);
			}

			// Initialize an empty array to store product details
			$terms = Helper::term_details( $term_ids, $taxonomy );

			// Return the response
			return new \WP_REST_Response(
				array(
					'success' => true,
					'data'    => $terms,
				),
				200
			);
		}

		public function fetch_rules( $rest_response = 1 ) {

			$tyrules = get_option( $this->option, array() );

			if ( $rest_response ) {
				return new \WP_REST_Response( array( 'data' => $tyrules ), 200 );
			} else {
				return $tyrules;
			}

		}

		public function option_name() {
			return $this->option;
		}

		/**
		 * Check user has write permission
		 *
		 * @return void
		 */

		public function get_write_api_permission_check() {
			return current_user_can( 'manage_options' ) ? true : false;
		}

	}	
}
