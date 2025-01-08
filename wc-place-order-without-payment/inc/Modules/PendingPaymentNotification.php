<?php
namespace WPOWP\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class PendingPaymentNotification
 *
 * @package WPOWP\Modules
 * @since 1.0.0
 */
class PendingPaymentNotification extends \WC_Email {

	/**
	 * Construct
	 */
	public function __construct() {
		$this->id             = 'wpowp_pending_payment_email';
		$this->title          = __( 'Pending Payment', 'wpowp' );
		$this->description    = __( 'This email is sent when an order status changes to pending payment.', 'wpowp' );
		$this->template_html  = 'emails/pending-payment-notification.php';
		$this->template_plain = 'emails/plain/pending-payment-notification.php';
		$this->template_base  = WPOWP_DIR . 'templates/';
		$this->customer_email = true;
		$this->subject        = __( 'Your Order #{order_number} Requires Payment', 'wpowp' );
		$this->heading        = __( 'Pay Now to Secure Your Order', 'wpowp' );		

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Trigger
	 *
	 * @param integer $order_id
	 * @param object  $order
	 *
	 * @return void
	 */
	public function trigger( $order_id, $order = false ) {		

		if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( is_a( $order, 'WC_Order' ) && $this->is_enabled() ) {

			$this->object = $order;

			$this->find[]    = '{order_date}';
			$this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->get_date_created() ) );

			$this->find[]    = '{order_number}';
			$this->replace[] = $this->object->get_order_number();

			$this->recipient = $this->object->get_billing_email();
			$this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}
	}

	/**
	 * Get content html.
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template(
			$this->template_html,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'email'              => $this,
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => false,
			),
			$this->template_base,
			$this->template_base
		);
		return ob_get_clean();
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template(
			$this->template_plain,
			array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'email'         => $this,
				'sent_to_admin' => false,
				'plain_text'    => true,
			),
			$this->template_base,
			$this->template_base
		);
		return ob_get_clean();
	}

	/**
	 * Initialise Settings Form Fields
	 */
	public function init_form_fields() {
		$this->form_fields = apply_filters(
			'wpowp_offline_form_fields',
			array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'wpowp' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'wpowp' ),
					'default' => 'yes',
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'wpowp' ),
					'type'        => 'text',
					'description' => __( 'This controls the email subject line. Leave blank to use the default subject: "Your Order #{order_number} Requires Payment".', 'wpowp' ),
					'placeholder' => __( 'Your Order #{order_number} Requires Payment', 'wpowp' ),
					'default'     => __( 'Your Order #{order_number} Requires Payment', 'wpowp' ),
				),
				'heading'    => array(
					'title'       => __( 'Email Heading', 'wpowp' ),
					'type'        => 'text',
					'description' => __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: "Pay Now to Secure Your Order".', 'wpowp' ),
					'placeholder' => __( 'Pay Now to Secure Your Order', 'wpowp' ),
					'default'     => __( 'Pay Now to Secure Your Order', 'wpowp' ),
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'wpowp' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'wpowp' ),
					'default'     => 'html',
					'class'       => 'wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
				),
			)
		);
	}
}
