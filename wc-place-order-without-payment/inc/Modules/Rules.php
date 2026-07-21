<?php

/**
 * @package     WPOWP
 * @since       2.7.3
 */

namespace WPOWP\Modules;

use WPOWP\Traits\Get_Instance;
use WPOWP\Helper;

class Rules {

	use Get_Instance;

	protected $options   = array();
	protected $operators = array();
	protected $rules     = array();

	/**
	 * Constructor
	 *
	 * @since 4.1.6
	 */
	public function __construct() {
		// Define options and option groups
		$this->options = array(
			'products' => array(
				'product_name'      => __( 'Product ', 'wpowp' ),
				'product_variation' => __( 'Product Variation', 'wpowp' ),
				'product_category'  => __( 'Product Category', 'wpowp' ),
				'product_tag'       => __( 'Product Tag', 'wpowp' ),
			),
			'users'    => array(
				'user_role' => __( 'User Role ', 'wpowp' ),
				// 'logged_in' => __( 'Logged In', 'wpowp' ),
			),
		);

		// Define operators for each option type
		$this->operators = array(
			'default'            => array(
				'in'     => __( 'Includes', 'wpowp' ),
				'not_in' => __( 'Does Not Include', 'wpowp' ),
				// 'is'     => __( 'Is', 'wpowp' ),
				// 'is_not' => __( 'Is Not', 'wpowp' ),
			),
			'product_name'       => array(
				'='      => __( 'Is', 'wpowp' ),
				'!='     => __( 'Is Not', 'wpowp' ),
				'in'     => __( 'Includes', 'wpowp' ),
				'not_in' => __( 'Does Not Include', 'wpowp' ),
			),
			'order_total'        => array(
				'='  => __( 'Equals', 'wpowp' ),
				'!=' => __( 'Not Equals', 'wpowp' ),
				'>'  => __( 'Greater Than', 'wpowp' ),
				'<'  => __( 'Less Than', 'wpowp' ),
			),
			'order_status'       => array(
				'='  => __( 'Is', 'wpowp' ),
				'!=' => __( 'Is Not', 'wpowp' ),
			),
			'customer_email'     => array(
				'='    => __( 'Equals', 'wpowp' ),
				'!='   => __( 'Not Equals', 'wpowp' ),
				'like' => __( 'Contains', 'wpowp' ),
			),
			'returning_customer' => array(
				'='  => __( 'Yes', 'wpowp' ),
				'!=' => __( 'No', 'wpowp' ),
			),
			'order_date'         => array(
				'='  => __( 'Equals', 'wpowp' ),
				'!=' => __( 'Not Equals', 'wpowp' ),
				'>'  => __( 'After', 'wpowp' ),
				'<'  => __( 'Before', 'wpowp' ),
			),
		);
	}

	// Method to get option groups
	public function get_option_groups() {
		return $this->options;
	}

	// Method to get operators for a given option (fallback to default if no specific operators found)
	public function get_operators( $option ) {
		if ( isset( $this->operators[ $option ] ) ) {
			return $this->operators[ $option ];
		}

		// Return default operators if no specific operators are found for the option
		return $this->operators['default'];
	}

	// Method to generate dropdown options for option groups
	public function create_dropdown_options() {
		$optionGroups = $this->get_option_groups();

		$html  = '<select name="rule_options" class="form-select item-selector" required>';
		$html .= '<option value="">' . esc_html__( 'Select Option', 'thank-you-page-pro' ) . '</option>';

		foreach ( $optionGroups as $groupName => $options ) {
			$html .= '<optgroup label="' . ucfirst( $groupName ) . '">';

			foreach ( $options as $value => $label ) {
				$html .= '<option value="' . $value . '">' . $label . '</option>';
			}

			$html .= '</optgroup>';
		}

		$html .= '</select>';

		return $html;
	}

	// Method to generate dropdown operators based on the selected option
	public function create_dropdown_operators( $selectedOption = '' ) {
		$operators = $this->get_operators( $selectedOption );

		$html  = '<select class="form-select operator-selector" required>';
		$html .= '<option value="">' . esc_html__( 'Select Operator', 'thank-you-page-pro' ) . '</option>';
		foreach ( $operators as $value => $label ) {
			$html .= '<option value="' . $value . '">' . $label . '</option>';
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 * Create dropdown pages
	 *
	 * @return string
	 */
	public function create_dropdown_pages() {
		$html  = '<select name="rule_pages" class="form-select url-dropdown" required>';
		$html .= '<option value="">' . esc_html__( 'Select Page', 'thank-you-page-pro' ) . '</option>';

		$pages = Helper::get_instance()->get_pages();

		foreach ( $pages as $page ) {
			$html .= '<option value="' . $page['url'] . '">' . $page['text'] . '</option>';
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 * Validate WooCommerce Order on Thank You page and redirect based on rules.
	 *
	 * @param int $order_id Order ID.
	 */
	public function process_rules( $saved_rules = array() ) {

		// if Saved rules is empty, return
		if ( empty( $saved_rules ) || ! is_array( $saved_rules ) ) {
			return;
		}

		if ( ! WC()->cart ) {
			return;
		}

		// ---- Request-level cache ----
		static $cache = array();
		$cart_hash    = WC()->cart->get_cart_hash();

		if ( isset( $cache[ $cart_hash ] ) ) {
			return $cache[ $cart_hash ];
		}

		// ---- Normalize cart ONCE ----
		$cart_data = $this->build_cart_data();

		if ( empty( $cart_data ) ) {
			return;
		}

		// foreach ( $saved_rules as $rule_group ) {

		// $switch                                    = array();
		// $switch['placeOrderSwitch']                = ( isset( $rule_group['placeOrderSwitch'] ) ) ? $rule_group['placeOrderSwitch'] : '';
		// $switch['allowPaymentsSwitch']             = ( isset( $rule_group['allowPaymentsSwitch'] ) ) ? $rule_group['allowPaymentsSwitch'] : '';
		// $switch['requestQuoteSwitch']              = ( isset( $rule_group['requestQuoteSwitch'] ) ) ? $rule_group['requestQuoteSwitch'] : '';
		// $switch['orderButtonTextSwitch']           = ( isset( $rule_group['orderButtonTextSwitch'] ) ) ? $rule_group['orderButtonTextSwitch'] : '';
		// $switch['removeShippingFieldsRatesSwitch'] = ( isset( $rule_group['removeShippingFieldsRatesSwitch'] ) ) ? $rule_group['removeShippingFieldsRatesSwitch'] : '';
		// $switch['removeTaxRatesSwitch']            = ( isset( $rule_group['removeTaxRatesSwitch'] ) ) ? $rule_group['removeTaxRatesSwitch'] : '';
		// $switch['removeCheckoutPrivacySwitch']     = ( isset( $rule_group['removeCheckoutPrivacySwitch'] ) ) ? $rule_group['removeCheckoutPrivacySwitch'] : '';
		// $switch['removeCheckoutTermsSwitch']       = ( isset( $rule_group['removeCheckoutTermsSwitch'] ) ) ? $rule_group['removeCheckoutTermsSwitch'] : '';

		// $rules = $rule_group['rules'];

		// Process the rules based on condition
		// $valid = $this->evaluate_rules( $rules, $cart_data );

		// Redirect or disable payment methods if valid
		// if ( $valid ) {
		// return $switch;
		// }
		// }

		// Loop through each group of rules
		foreach ( $saved_rules as $group ) {

			if ( empty( $group['rules'] ) ) {
				continue;
			}

			if ( ! $this->evaluate_rules( $group['rules'], $cart_data ) ) {
				continue;
			}

			// Normalize switches once
			$result = array(
				'placeOrderSwitch'                => ! empty( $group['placeOrderSwitch'] ),
				'allowPaymentsSwitch'             => ! empty( $group['allowPaymentsSwitch'] ),
				'requestQuoteSwitch'              => ! empty( $group['requestQuoteSwitch'] ),
				'orderButtonTextSwitch'           => ! empty( $group['orderButtonTextSwitch'] ),
				'removeShippingFieldsRatesSwitch' => ! empty( $group['removeShippingFieldsRatesSwitch'] ),
				'removeTaxRatesSwitch'            => ! empty( $group['removeTaxRatesSwitch'] ),
				'removeCheckoutPrivacySwitch'     => ! empty( $group['removeCheckoutPrivacySwitch'] ),
				'removeCheckoutTermsSwitch'       => ! empty( $group['removeCheckoutTermsSwitch'] ),
			);

			$cache[ $cart_hash ] = $result;
			return $result;
		}

		$cache[ $cart_hash ] = null;
		return;
	}

	/**
	 * Evaluate multiple rules based on the specified condition.
	 *
	 * @param array $rules Array of rules to evaluate.
	 * @param array $order_data Order data to match against.
	 * @return bool True if the rules are satisfied, false otherwise.
	 */
	private function evaluate_rules( $rules, $order_data ) {

		$condition = $rules[0]['condition'] ?? 'AND'; // Default condition

		foreach ( $rules as $index => $rule ) { // phpcs:ignore
			$item     = $rule['item'] ?? '';
			$operator = $rule['operator'] ?? '';
			$value    = $rule['value'] ?? '';

			if ( ! empty( $value ) && ! empty( $item ) ) {

				$data_value = $this->get_cart_data_value( $item, $order_data );
				$rule_valid = $this->evaluate_rule( $data_value, $operator, $value );

				// Handle AND/OR conditions
				if ( $condition === 'AND' ) {
					if ( ! $rule_valid ) {
						return false; // If any rule is invalid in AND condition, return false
					}
				} elseif ( $condition === 'OR' ) {
					if ( $rule_valid ) {
						return true; // If any rule is valid in OR condition, return true
					}
				}
			}
		}

		// If all rules are evaluated, return the result based on the condition
		return $condition === 'AND' ? true : false;
	}

	/**
	 * Get the value from order data based on the rule item.
	 *
	 * @param string $item Rule item.
	 * @param array  $order_data Order data.
	 * @return mixed Value from the order data.
	 */
	private function get_cart_data_value( $item, $cart_data ) {
		switch ( $item ) {
			case 'payment_method':
				return $cart_data['payment_method'];
			case 'product_name':
				return array_keys( $cart_data['product_name'] );
			case 'product_variation':
				return array_keys( $cart_data['product_variation'] );
			case 'product_category':
				return $cart_data['product_category'];
			case 'product_tag':
				return $cart_data['product_tag'];
			case 'user_role':
				return $cart_data['user_role'];
			default:
				return '';
		}
	}

	/**
	 * Evaluate a single rule.
	 *
	 * @param mixed  $data_value The value to compare.
	 * @param string $operator The operator to use for comparison.
	 * @param mixed  $value The value to compare against.
	 * @return bool True if the rule is satisfied, false otherwise.
	 */
	private function evaluate_rule( $data_value, $operator, $value ) {
		switch ( $operator ) {
			case '=':
				return $data_value == $value;
			case '!=':
				return $data_value != $value;
			case '>':
				return $data_value > $value;
			case '<':
				return $data_value < $value;
			case 'in':
				if ( is_array( $data_value ) ) {
					// Check if there is any intersection between the two arrays
					return ! empty( array_intersect( $data_value, (array) $value ) );
				} else {
					// Check if the single value is in the array
					return in_array( $data_value, (array) $value, true );
				}
			case 'not_in':
				return is_array( $data_value ) ? ! array_intersect( $data_value, $value ) : ! in_array( $data_value, $value, true );
			default:
				return false;
		}
	}

	/**
	 * Build cart data for evaluation.
	 *
	 * @return array Cart data.
	 */
	private function build_cart_data() {

		$items = WC()->cart->get_cart();
		if ( empty( $items ) ) {
			return array();
		}

		$data = array(
			'product_name'      => array(),
			'product_variation' => array(),
			'product_category'  => array(),
			'product_tag'       => array(),
			'user_role'         => wp_get_current_user()->roles,
		);

		foreach ( $items as $item ) {

			if ( empty( $item['data'] ) ) {
				continue;
			}

			$product    = $item['data'];
			$product_id = (int) $item['product_id'];

			// Name
			$data['product_name'][ $product_id ] = $product->get_name();

			// Variation
			if ( $product->is_type( 'variation' ) ) {
				$data['product_variation'][ $product->get_id() ] = $product->get_attributes();
			}

			// Categories
			$cats = get_the_terms( $product_id, 'product_cat' );
			if ( $cats && ! is_wp_error( $cats ) ) {
				foreach ( $cats as $cat ) {
					$data['product_category'][ $cat->term_id ] = $cat->name;
				}
			}

			// Tags
			$tags = get_the_terms( $product_id, 'product_tag' );
			if ( $tags && ! is_wp_error( $tags ) ) {
				foreach ( $tags as $tag ) {
					$data['product_tag'][ $tag->term_id ] = $tag->name;
				}
			}
		}

		// Normalize
		$data['product_category'] = array_values( $data['product_category'] );
		$data['product_tag']      = array_values( $data['product_tag'] );

		return $data;
	}
}
