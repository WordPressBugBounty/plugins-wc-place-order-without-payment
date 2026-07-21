<?php

/**
 * @package     WPOWP
 * @since       2.7.5
 */

namespace WPOWP\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PreOrderGateway
 *
 * @package WPOWP\Modules
 * @since 2.7.5
 */

class PreOrderGateway extends \WC_Payment_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->id                 = 'wpowp_preorder';
		$this->method_title       = __( 'PreOrder (WPOWP)', 'wpowp' );
		$this->method_description = __( 'Gateway used for pre-orders where payment is skipped.', 'wpowp' );
		$this->has_fields         = false;

		// Required for Blocks compatibility
		$this->supports = array(
			'products',
		);

		$this->enabled = 'yes';
		$this->title   = __( 'Pre-Order (No Payment Required)', 'wpowp' );

		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array( $this, 'process_admin_options' )
		);
	}

	/**
	 * Validate the fields
	 *
	 * @return bool
	 */
	public function validate_fields() {
		return true;
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return array(
				'result'   => 'failure',
				'redirect' => '',
			);
		}

		$order->add_order_note(
			__( 'Order placed as Pre Order (payment skipped).', 'wpowp' )
		);

		// IMPORTANT:
		// Do NOT call payment_complete()
		// Let WPOWP logic control order status

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}
}
