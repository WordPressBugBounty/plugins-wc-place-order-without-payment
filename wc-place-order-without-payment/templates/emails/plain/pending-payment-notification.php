<?php
/**
 * Customer order pending email (plain text)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '= ' . esc_attr( $email_heading ) . " =\n\n";

/* Greeting */
echo sprintf( esc_html__( 'Dear %s,', 'wpowp' ), esc_html( $order->get_billing_first_name() ) ) . "\n\n";

/* Order status update */
echo sprintf(
	esc_html__( 'Thank you for your recent purchase at %1$s. We wanted to inform you that the status of your order #%2$d, placed on %3$s, has been updated to "Pending".', 'wpowp' ),
	esc_html( get_bloginfo( 'name' ) ),
	esc_attr( $order->get_id() ),
	esc_html( wc_format_datetime( $order->get_date_created() ) )
) . "\n\n";

/* Explanation of status */
echo esc_html__( 'What does this mean?', 'wpowp' ) . "\n";
echo esc_html__( '- Your order is awaiting payment.', 'wpowp' ) . "\n";
echo esc_html__( '  Please complete your payment to proceed with the order.', 'wpowp' ) . "\n\n";

$formatted_order_total = sprintf(
	get_woocommerce_price_format(),
	get_woocommerce_currency_symbol( $order->get_currency() ),
	number_format( $order->get_total(), wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() )
);

/* Order summary */
echo esc_html__( 'Order Summary:', 'wpowp' ) . "\n";
echo sprintf( esc_html__( 'Order Number: %d', 'wpowp' ), esc_attr( $order->get_id() ) ) . "\n";
echo sprintf( esc_html__( 'Order Date: %s', 'wpowp' ), esc_html( wc_format_datetime( $order->get_date_created() ) ) ) . "\n";
echo sprintf( esc_html__( 'Order Total: %s', 'wpowp' ), esc_html( $formatted_order_total ) ) . "\n\n";

/* Pay Order link */
echo esc_html( $email_heading );
echo esc_html__( 'To complete your order, please use the following link to make the payment:', 'wpowp' ) . "\n";
echo esc_url( $order->get_checkout_payment_url() ) . "\n\n";

/* Support contact */
echo esc_html__( 'If you have already made the payment or believe this status change is in error, please contact our support team:', 'wpowp' ) . "\n";
echo sprintf(
	esc_html__( 'Email: %1$s | Phone: %2$s', 'wpowp' ),
	esc_html( get_option( 'admin_email' ) ),
	esc_html( get_option( 'woocommerce_store_phone' ) )
) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/* Footer text */
echo wp_kses_post( wpautop( wptexturize( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ) ) );
