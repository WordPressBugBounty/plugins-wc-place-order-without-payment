<?php
/**
 * Customer order pending email (HTML)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<p><?php printf( esc_html__( 'Dear %s,', 'wpowp' ), esc_html( $order->get_billing_first_name() ) ); ?></p>

<p>
	<?php
	printf(
		esc_html__( 'Thank you for your recent purchase at %1$s. We wanted to inform you that the status of your order #%2$d, placed on %3$s, has been updated to "Pending".', 'wpowp' ),
		esc_html( get_bloginfo( 'name' ) ),
		esc_html( $order->get_id() ),
		esc_html( wc_format_datetime( $order->get_date_created() ) )
	);
	?>
</p>

<h3><?php esc_html_e( 'What does this mean?', 'wpowp' ); ?></h3>
<ul>
	<li><?php esc_html_e( 'Your order is awaiting payment.', 'wpowp' ); ?></li>
	<li><?php esc_html_e( 'Please complete your payment to proceed with the order.', 'wpowp' ); ?></li>
</ul>

<?php
$formatted_order_total = sprintf(
	get_woocommerce_price_format(),
	get_woocommerce_currency_symbol( $order->get_currency() ),
	number_format( $order->get_total(), wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() )
);
?>

<h3><?php esc_html_e( 'Order Summary:', 'wpowp' ); ?></h3>
<p>
	<?php printf( esc_html__( 'Order Number: %d', 'wpowp' ), esc_html( $order->get_id() ) ); ?><br>
	<?php printf( esc_html__( 'Order Date: %s', 'wpowp' ), esc_html( wc_format_datetime( $order->get_date_created() ) ) ); ?><br>
	<?php printf( esc_html__( 'Order Total: %s', 'wpowp' ), esc_html( $formatted_order_total ) ); ?>
</p>

<h3><?php esc_html( $email_heading ); ?></h3>
<p>
	<?php esc_html_e( 'To complete your order, please click the link below to make the payment:', 'wpowp' ); ?>
</p>
<p>
	<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" target="_blank" style="display: inline-block; background-color: #0071a1; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
		<?php esc_html_e( 'Pay Now', 'wpowp' ); ?>
	</a>
</p>

<p>
	<?php esc_html_e( 'If you have already made the payment or believe this status change is in error, please contact our support team:', 'wpowp' ); ?>
</p>
<p>
	<?php
	printf(
		esc_html__( 'Email: %1$s | Phone: %2$s', 'wpowp' ),
		esc_html( get_option( 'admin_email' ) ),
		esc_html( get_option( 'woocommerce_store_phone' ) )
	);
	?>
</p>

<?php
/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
?>
