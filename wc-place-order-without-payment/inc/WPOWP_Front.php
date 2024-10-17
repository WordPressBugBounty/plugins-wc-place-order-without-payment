<?php

/**
 * FRONT Class
 *
 * @package WPOWP
 * @since 2.3
 */

namespace WPOWP;

use WPOWP\Traits\Get_Instance;

if ( ! class_exists( 'WPOWP_Front' ) ) {
	class WPOWP_Front {

		use Get_Instance;

		private $settings = '';

		/**
		 * Constructor
		 *
		 * return void
		 */
		public function __construct() {

			$this->settings = WPOWP_Admin::get_instance()->get_settings();
			$this->handle_front( $this->settings );			
			// Update Order status
			add_action( 'woocommerce_thankyou', array( $this, 'update_order_status' ), 10, 1 );
			// Hide Place Order button
			add_filter( 'woocommerce_order_button_html', array( $this, 'hide_place_order_button' ) );
		}

		/**
		 * Handle Front
		 *
		 * @param  array $settings
		 * @return void
		 */
		public function handle_front( $settings ) {

			if ( ! empty( $settings ) && is_array( $settings ) ) {

				$skip_cart = $settings['skip_cart'];

				// SKip Cart functionality
				if ( true === filter_var( $skip_cart, FILTER_VALIDATE_BOOLEAN ) ) {
					add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'skip_cart' ) );
					add_filter( 'wc_add_to_cart_message_html', '__return_empty_string' );
					add_filter( 'option_woocommerce_enable_ajax_add_to_cart', '__return_false' );
					add_filter( 'woocommerce_get_price_html', array( $this, 'free_product' ), 10, 2 );
				}

				if ( false === filter_var( $settings['standard_add_cart'], FILTER_VALIDATE_BOOLEAN ) ) {
					add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'cart_btntext' ) );
					add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'cart_btntext' ) );
				}

				if ( true === filter_var( $settings['free_product'], FILTER_VALIDATE_BOOLEAN ) ) {
					add_filter( 'woocommerce_get_price_html', array( $this, 'free_product' ), 10, 2 );

					if ( true === filter_var( $settings['free_product_on_cart'], FILTER_VALIDATE_BOOLEAN ) ) {
						// Cart and minicart
						add_filter( 'woocommerce_cart_item_price', array( $this, 'cart_item_price_html' ), 10, 3 );
					}

					if ( true === filter_var( $settings['free_product_on_checkout'], FILTER_VALIDATE_BOOLEAN ) ) {
						// Cart and Checkout
						add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'checkout_item_subtotal_html' ), 10, 3 );
					}
				}

				// Checkout Order button text
				if ( ! empty( $settings['order_button_text'] ) ) {
					// Add WC Order Button FilterI
					add_filter( 'woocommerce_order_button_text', array( $this, 'order_btntext' ) );
				}

				if ( true === filter_var( $settings['remove_taxes'], FILTER_VALIDATE_BOOLEAN ) ) {
					add_filter( 'woocommerce_cart_tax_totals', array( $this, 'remove_cart_tax_totals' ), 10, 2 );
					add_filter( 'woocommerce_calculated_total', array( $this, 'exclude_tax_cart_total' ), 10, 2 );
					add_filter( 'woocommerce_subscriptions_calculated_total', array( $this, 'exclude_tax_cart_total' ), 10, 2 );
				}
			}

		}
		
		/**
		 * Skip Cart
		 *
		 * @return URL
		 */
		public function skip_cart() {
			return wc_get_checkout_url();
		}

		/**
		 * Cart BtnText
		 *
		 * @return string
		 */
		public function cart_btntext() {
			$add_cart_label = $this->settings['add_cart_text'];
			$add_cart_label = ( 'Buy Now' === trim( $add_cart_label ) ) ? __( 'Buy Now', WPOWP_TEXT_DOMAIN ) : $add_cart_label;
			$add_cart_txt   = apply_filters( 'wpowp_translate_add_cart_txt', $add_cart_label );

			return ( false === filter_var( $this->settings['standard_add_cart'], FILTER_VALIDATE_BOOLEAN ) ) ? esc_html( $add_cart_txt ) : '';
		}

		/**
		 * Free Product
		 *
		 * @param  float $price
		 * @param  object $product
		 * @return $price
		 */
		public function free_product( $price, $product ) {

			$free_price_label = $this->settings['free_product_text'];
			$free_price_label = ( 'FREE' === trim( $free_price_label ) ) ? __( 'FREE', WPOWP_TEXT_DOMAIN ) : $free_price_label;
			$free_price_txt   = apply_filters( 'wpowp_translate_free_product_text', $free_price_label );

			if ( $product->is_type( 'variable' ) ) {

				$prices    = $product->get_variation_prices( true );
				$min_price = current( $prices['price'] );
				if ( 0 === $min_price ) {
					$max_price     = end( $prices['price'] );
					$min_reg_price = current( $prices['regular_price'] );
					$max_reg_price = end( $prices['regular_price'] );
					if ( $min_price !== $max_price ) {
						$price  = wc_format_price_range( $free_price_txt, $max_price );
						$price .= $product->get_price_suffix();
					} elseif ( $product->is_on_sale() && $min_reg_price === $max_reg_price ) {
						$price  = wc_format_sale_price( wc_price( $max_reg_price ), $free_price_txt );
						$price .= $product->get_price_suffix();
					} else {
						$price = $free_price_txt;
					}
				}
			} elseif ( 0 === absint( $product->get_price() ) ) {
				$price = '<span class="woocommerce-Price-amount amount">' . esc_html( $free_price_txt ) . '</span>';
			}

			return $price;
		}

		/**
		 * Free Product
		 *
		 * @param  string $price_html
		 * @param  object $cart_item
		 * @param  object $cart_item_key
		 * @return $price
		 */
		function cart_item_price_html( $price_html, $cart_item, $cart_item_key ) { // phpcs:ignore
			if ( 0 === absint( $cart_item['data']->get_price() ) ) {
				return '<span class="woocommerce-Price-amount amount">' . $this->settings['free_product_text'] . '</span>';
			}
			return $price_html;
		}

		/**
		 * Free Product
		 *
		 * @param  string $subtotal_html
		 * @param  object $cart_item
		 * @param  object $cart_item_key
		 * @return $price
		 */
		function checkout_item_subtotal_html( $subtotal_html, $cart_item, $cart_item_key ) { // phpcs:ignore
			if ( 0 === absint( $cart_item['data']->get_price() ) ) {
				return '<span class="woocommerce-Price-amount amount">' . $this->settings['free_product_text'] . '</span>';
			}
			return $subtotal_html;
		}

		/**
		 * Update Order Status
		 *
		 * @param  int $order_id
		 * @return void
		 */
		public function update_order_status( $order_id ) {

			if ( absint( $order_id ) > 0 ) {

				$order        = new \WC_Order( $order_id );
				$order_status = $order->get_status();

				$wpowp_ordered = false;
				
				if( empty( $order->get_meta( 'wpowp-order', false ) ) ){
				    // Add the meta data
                    $order->update_meta_data( 'wpowp-order', 'Place Order', false );
                    // Save the order data
                    $order->save();
				}else{
				    $wpowp_ordered = true;
				}

				if ( has_filter( 'wpowp_skip_update_order_status' ) ) {

					$skip_status_update = apply_filters( 'wpowp_skip_update_order_status', false );

					// If filter is true and elcluded elements in Order Items skip Order Update
					if ( false !== $skip_status_update && $this->exclude_elements( $order_id ) ) {
						return;
					}
				}

				if ( ! $wpowp_ordered && 'pending' !== $order_status && 'completed' !== $order_status ) {

					$option_order_status = \WPOWP\WPOWP_Admin::get_instance()->get_settings( 'order_status' );
					$status              = apply_filters( 'wpowp_filter_order_status', wp_kses_post( $option_order_status ) );
					// Update Order status
					$order->update_status( $status );
				}
			}

		}

		/**
		 * Order BtnText
		 *
		 * @return string
		 */
		public function order_btntext() {
			$order_btntext     = $this->settings['order_button_text'];
			$order_btntext     = ( 'Place Order' === trim( $order_btntext ) ) ? __( 'Place Order', WPOWP_TEXT_DOMAIN ) : $order_btntext;
			$order_button_text = apply_filters( 'wpowp_translate_add_cart_txt', $order_btntext );
			return esc_html( $order_button_text );
		}

		/**
		 * Exclude Elements
		 *
		 * @return void()
		 * @since 2.5.7
		 */
		public function exclude_elements( $order_id = 0 ) {

			$items_list = array();

			if ( absint( $order_id ) > 0 ) {

				$order       = new \WC_Order( $order_id );
				$order_items = $order->get_items();

				if ( ! empty( $order_items ) ) {

					foreach ( $order_items as $line_item ) {
						$items_list[] = $line_item->get_product_id();
					}

					// Exclude Product IDS from Place Order Without Payment

					if ( has_filter( 'wpowp_exclude_products' ) && ! empty( $items_list ) ) {
						$exclude_products = apply_filters( 'wpowp_exclude_products', array() );
						if ( ! empty( $exclude_products ) && array_intersect( $items_list, explode( ',', $exclude_products ) ) ) {
							return true;
						}
					}

					// Exclude Product Categories from Place Order Without Payment

					if ( has_filter( 'wpowp_exclude_categories' ) && ! empty( $items_list ) ) {
						$filter_categories  = apply_filters( 'wpowp_exclude_categories', array() );
						$exclude_categories = ! empty( $filter_categories ) ? explode( ',', $filter_categories ) : array();
						if ( ! empty( $exclude_categories ) ) {
							$items = $items_list;
							foreach ( $items as $item ) {
								if ( has_term( $exclude_categories, 'product_cat', $item ) ) {
									return true;
								}
							}
						}
					}
				}
			}

			return false;
		}

		/**
		 * Hide Place Order Button
		 *
		 * @return void
		 * @since 2.5.9
		 */
		public function hide_place_order_button( $button ) {
			$hide_order_btn = $this->settings['hide_place_order_button'];

			if ( ( true === filter_var( $hide_order_btn, FILTER_VALIDATE_BOOLEAN ) ) ) {
				return '';
			}

			return $button;
		}

		/**
		 * Exclude tax cart total
		 *
		 * @return void
		 * @since 2.6.0
		 */
		public function exclude_tax_cart_total( $total, $instance ) {

			// If it is the cart subtract the tax
			if ( is_cart() ) {
				$total = round( WC()->cart->cart_contents_total + WC()->cart->shipping_total + WC()->cart->fee_total, WC()->cart->dp );
			}

			return $total;
		}

		/**
		 * Remove Cart Total Taxes
		 *
		 * @return void
		 * @since 2.6.0
		 */
		public function remove_cart_tax_totals( $tax_totals, $instance ) {

			if ( is_cart() || is_checkout() ) {
				$tax_totals = array();
			}

			return $tax_totals;
		}

	}

	WPOWP_Front::get_instance();

}
