<?php
// Include Header
include_once 'header.php'; // phpcs:ignore

global $wpowp_fs;

$option = $this->get_settings(); // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable

$is_pro   = $wpowp_fs->is_paying_or_trial();
$disabled = $is_pro ? '' : 'disabled';

?>
<div class="pt-2">	

	<form id="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-form" method="post" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">

		<!-- General Settings Card -->
		<div class="card-1">			
			<div class="fs-3 mb-3"><?php esc_html_e( 'General Settings', 'wpowp' ); ?></div>
			<div class="card-body">
				<div class="row">
					<!-- Enable Site-Wide (Pro) -->
					<?php if ( $wpowp_fs->is_paying_or_trial() ) { ?>
					<div class="col-md-4 mb-4">
						<div class="form-check-1 form-switch-1">
							<input class="form-check-input" type="checkbox" name="wpowp_enable_sitewide" id="wpowp_enable_sitewide" value="yes"
								<?php echo ( true === filter_var( $option['enable_sitewide'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> />
							<input type="hidden" name="wpowp_enable_sitewide_hidden" value="no" />
							<label class="form-check-label fw-bold" for="wpowp_enable_sitewide"><?php esc_html_e( 'Enable Site-Wide', 'wpowp' ); ?></label>
							<div class="form-text"><?php esc_html_e( '( Enable Place Order Without Payment store-wide in WooCommerce )', 'wpowp' ); ?></div>
						</div>
					</div>
					<?php } ?>

					<!-- Order Status -->
					<div class="col-md-4 mb-3">
						<label for="wpowp_order_status" class="form-label fw-bold"><?php esc_html_e( 'Order Status', 'wpowp' ); ?></label>
						<select class="form-select" name="wpowp_order_status" id="wpowp_order_status">
							<?php
							if ( ! empty( $this->order_status_list() ) ) {
								$status_list = $this->order_status_list();
								foreach ( $status_list as $key => $label ) {
									echo '<option value="' . wp_kses_post( $key ) . '" ' . ( $key === $option['order_status'] ? 'selected' : '' ) . '>' . wp_kses_post( $label ) . '</option>';
								}
							}
							?>
						</select>
						<div class="form-text"><?php esc_html_e( '( Order status after placing order )', 'wpowp' ); ?></div>
					</div>

					<!-- Skip Cart -->
					<div class="col-md-4 mb-3">
						<div class="form-check-1 form-switch-1">
							<input class="form-check-input" type="checkbox" name="wpowp_skip_cart" id="wpowp_skip_cart" value="yes"
								<?php echo ( true === filter_var( $option['skip_cart'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> />
							<input type="hidden" name="wpowp_skip_cart_hidden" value="no" />
							<label class="form-check-label fw-bold" for="wpowp_skip_cart"><?php esc_html_e( 'Skip Cart', 'wpowp' ); ?></label>
							<div class="form-text"><?php esc_html_e( '( Skip Cart & Go to Checkout on Add to Cart )', 'wpowp' ); ?></div>
						</div>
					</div>

					<!-- Standard WooCommerce Buttons -->
					<div class="col-md-4 mb-3">
						<div class="form-check-1 form-switch-1">
							<input class="form-check-input" type="checkbox" name="wpowp_standard_add_cart" id="wpowp_standard_add_cart" value="yes"
								<?php echo ( true === filter_var( $option['standard_add_cart'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> />
							<input type="hidden" name="wpowp_standard_add_cart_hidden" value="no" />
							<label class="form-check-label fw-bold" for="wpowp_standard_add_cart"><?php esc_html_e( 'Standard WooCommerce Buttons', 'wpowp' ); ?></label>
							<div class="form-text"><?php esc_html_e( '( Standard WooCommerce button on shop pages )', 'wpowp' ); ?></div>
						</div>
					</div>

					<!-- Hide Price -->
					<div class="col-md-4 mb-3">
						<label class="form-label fw-bold d-block"><?php esc_html_e( 'Hide Price (Logged-Out Users)', 'wpowp' ); ?></label>
						<div class="form-check-1 form-check-inline">
							<input class="form-check-input" type="radio" name="wpowp_hide_price" id="wpowp_hide_price_none" value="no" <?php echo ( 'no' === $option['hide_price'] ) ? 'checked' : ''; ?> />
							<label class="form-check-label" for="wpowp_hide_price_none"><?php esc_html_e( 'None', 'wpowp' ); ?></label>
						</div>
						<div class="form-check-1 form-check-inline">
							<input class="form-check-input" type="radio" name="wpowp_hide_price" id="wpowp_hide_price_logout" value="logged_out" <?php echo ( 'logged_out' === $option['hide_price'] ) ? 'checked' : ''; ?> />
							<label class="form-check-label" for="wpowp_hide_price_logout"><?php esc_html_e( 'Logged Out user', 'wpowp' ); ?></label>
						</div>
						<div class="form-text"><?php esc_html_e( '( Hides product price for logged-out visitors only. To hide prices for everyone, use "Hide Prices (All Visitors)" below. )', 'wpowp' ); ?></div>
					</div>

					<!-- Hide Additional Information Tab -->
					<div class="col-md-4 mb-3">
						<label class="form-label fw-bold d-block"><?php esc_html_e( 'Hide Additional Information Tab', 'wpowp' ); ?></label>
						<div class="form-check-1 form-check-inline">
							<input class="form-check-input" type="radio" name="wpowp_hide_additional_info_tab" id="wpowp_hide_info_none" value="no" <?php echo ( 'no' === $option['hide_additional_info_tab'] ) ? 'checked' : ''; ?> />
							<label class="form-check-label" for="wpowp_hide_info_none"><?php esc_html_e( 'None', 'wpowp' ); ?></label>
						</div>
						<div class="form-check-1 form-check-inline">
							<input class="form-check-input" type="radio" name="wpowp_hide_additional_info_tab" id="wpowp_hide_info_logout" value="logged_out" <?php echo ( 'logged_out' === $option['hide_additional_info_tab'] ) ? 'checked' : ''; ?> />
							<label class="form-check-label" for="wpowp_hide_info_logout"><?php esc_html_e( 'Logged Out user', 'wpowp' ); ?></label>
						</div>
						<div class="form-text"><?php esc_html_e( '( Hide Additional Information Tab on Product Page )', 'wpowp' ); ?></div>
					</div>
				</div>

				<!-- <hr class="my-4"> -->

				<div class="row">
					<!-- Hide Prices Sitewide -->
					<div class="col-12">
						<div class="form-check-1 form-switch-1">
							<input class="form-check-input" type="checkbox" name="wpowp_hide_prices_sitewide" id="wpowp_hide_prices_sitewide" value="yes"
								<?php echo ( true === filter_var( $option['hide_prices_sitewide'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> <?php echo esc_attr( $disabled ); ?> />
							<label class="form-check-label fw-bold" for="wpowp_hide_prices_sitewide">
								<?php esc_html_e( 'Hide Prices (All Visitors)', 'wpowp' ); ?>
								<?php if ( ! $is_pro ) { ?>
									<span class="badge bg-primary"><?php esc_html_e( 'PRO', 'wpowp' ); ?></span>
								<?php } ?>
							</label>
							<div class="form-text"><?php esc_html_e( '( Disables the visibility of product prices across the entire website, for all visitors including logged-in users. )', 'wpowp' ); ?></div>
							<div class="form-text text-danger">
								<span class="dashicons dashicons-warning align-middle" style="font-size:16px;width:16px;height:16px;"></span>
								<?php esc_html_e( 'If enabled without also enabling "Hide Place Order Button" on the Request Quote tab, customers can complete checkout and pay without ever seeing a price.', 'wpowp' ); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Pro Settings Card -->
		<div class="card-1 mb-4">
			<div class="card-header py-3 d-flex justify-content-between align-items-center">
				<div class="fs-3 card-title mb-0"><?php esc_html_e( 'Advanced Settings', 'wpowp' ); ?></div>
				<?php if ( ! $wpowp_fs->is_paying_or_trial() ) { ?>
					<span class="badge bg-primary"><?php esc_html_e( 'PRO Version', 'wpowp' ); ?></span>
				<?php } ?>
			</div>
			<div class="card-body">
				<div class="row">
					<!-- Add to cart text -->
					<div class="col-md-4 mb-3">
						<label for="wpowp_add_cart_text" class="form-label fw-bold"><?php esc_html_e( 'Add to cart text', 'wpowp' ); ?></label>
						<input name="wpowp_add_cart_text" class="form-control" type="text" value="<?php echo esc_attr( $option['add_cart_text'] ); ?>" <?php echo esc_attr( $disabled ); ?> />
						<div class="form-text"><?php esc_html_e( '( Works if Standard Add to cart is unchecked )', 'wpowp' ); ?></div>
					</div>

					<!-- Order button text -->
					<div class="col-md-4 mb-3">
						<label for="wpowp_order_button_text" class="form-label fw-bold"><?php esc_html_e( 'Order button text', 'wpowp' ); ?></label>
						<input name="wpowp_order_button_text" class="form-control" type="text" value="<?php echo esc_attr( $option['order_button_text'] ); ?>" <?php echo esc_attr( $disabled ); ?> />
					</div>

					<!-- Remove shipping fields & rates -->
					<div class="col-md-4 mb-3">
						<label for="wpowp_remove_shipping" class="form-label fw-bold"><?php esc_html_e( 'Remove shipping fields & rates', 'wpowp' ); ?></label>
						<select class="form-select" name="wpowp_remove_shipping" <?php echo esc_attr( $disabled ); ?>>
							<option value="no" <?php echo ( false === filter_var( $option['remove_shipping'], FILTER_VALIDATE_BOOLEAN ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'No', 'wpowp' ); ?></option>
							<option value="yes" <?php echo ( true === filter_var( $option['remove_shipping'], FILTER_VALIDATE_BOOLEAN ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Yes', 'wpowp' ); ?></option>
						</select>
					</div>

					<!-- Remove Tax Rates -->
					<div class="col-md-4 mb-3">
						<label for="wpowp_remove_taxes" class="form-label fw-bold"><?php esc_html_e( 'Remove Tax Rates', 'wpowp' ); ?></label>
						<select class="form-select" name="wpowp_remove_taxes" <?php echo esc_attr( $disabled ); ?>>
							<option value="no" <?php echo ( false === filter_var( $option['remove_taxes'], FILTER_VALIDATE_BOOLEAN ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'No', 'wpowp' ); ?></option>
							<option value="yes" <?php echo ( true === filter_var( $option['remove_taxes'], FILTER_VALIDATE_BOOLEAN ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Yes', 'wpowp' ); ?></option>
						</select>
					</div>

					<!-- Remove checkout privacy text -->
					<div class="col-md-4 mb-3">
						<label for="wpowp_remove_privacy_policy_text" class="form-label fw-bold"><?php esc_html_e( 'Remove checkout privacy text', 'wpowp' ); ?></label>
						<select class="form-select" name="wpowp_remove_privacy_policy_text" <?php echo esc_attr( $disabled ); ?>>
							<option value="no" <?php echo ( false === filter_var( $option['remove_privacy_policy_text'], FILTER_VALIDATE_BOOLEAN ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'No', 'wpowp' ); ?></option>
							<option value="yes" <?php echo ( true === filter_var( $option['remove_privacy_policy_text'], FILTER_VALIDATE_BOOLEAN ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Yes', 'wpowp' ); ?></option>
						</select>
					</div>

					<!-- Remove checkout terms and conditions -->
					<div class="col-md-4 mb-3">
						<label for="wpowp_remove_checkout_terms_conditions" class="form-label fw-bold"><?php esc_html_e( 'Remove checkout terms and conditions', 'wpowp' ); ?></label>
						<select class="form-select" name="wpowp_remove_checkout_terms_conditions" <?php echo esc_attr( $disabled ); ?>>
							<option value="no" <?php echo ( false === filter_var( $option['remove_checkout_terms_conditions'], FILTER_VALIDATE_BOOLEAN ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'No', 'wpowp' ); ?></option>
							<option value="yes" <?php echo ( true === filter_var( $option['remove_checkout_terms_conditions'], FILTER_VALIDATE_BOOLEAN ) ) ? 'selected' : ''; ?>><?php esc_html_e( 'Yes', 'wpowp' ); ?></option>
						</select>
					</div>
				</div>

				<hr class="my-4">

				<div class="row">
					<!-- Free Product -->
					<div class="col-md-6 mb-3">
						<div class="form-check-1 form-switch-1">
							<input class="form-check-input" type="checkbox" name="wpowp_free_product" id="wpowp_free_product" value="yes" <?php echo ( true === filter_var( $option['free_product'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> <?php echo esc_attr( $disabled ); ?> />
							<label class="form-check-label fw-bold" for="wpowp_free_product"><?php esc_html_e( 'Free Product', 'wpowp' ); ?></label>
							<div class="form-text"><?php esc_html_e( '( For WooCommerce price label of $0.00, show custom text, such as the word “FREE”)', 'wpowp' ); ?></div>
						</div>
					</div>

					<!-- On Checkout -->
					<div class="col-md-6 mb-3">
						<div class="form-check-1 form-switch-1">
							<input class="form-check-input" type="checkbox" name="wpowp_free_product_on_checkout" id="wpowp_free_product_on_checkout" value="yes" <?php echo ( true === filter_var( $option['free_product_on_checkout'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> <?php echo esc_attr( $disabled ); ?> />
							<label class="form-check-label fw-bold" for="wpowp_free_product_on_checkout"><?php esc_html_e( 'On Checkout', 'wpowp' ); ?></label>
							<div class="form-text"><?php esc_html_e( '( On Checkout page for WooCommerce price label of $0.00, show custom text, such as the word “FREE”)', 'wpowp' ); ?></div>
						</div>
					</div>

					<!-- On Cart -->
					<div class="col-md-6 mb-3">
						<div class="form-check-1 form-switch-1">
							<input class="form-check-input" type="checkbox" name="wpowp_free_product_on_cart" id="wpowp_free_product_on_cart" value="yes" <?php echo ( true === filter_var( $option['free_product_on_cart'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> <?php echo esc_attr( $disabled ); ?> />
							<label class="form-check-label fw-bold" for="wpowp_free_product_on_cart"><?php esc_html_e( 'On Cart', 'wpowp' ); ?></label>
							<div class="form-text"><?php esc_html_e( '( On Cart page for WooCommerce price label of $0.00, show custom text, such as the word “FREE”)', 'wpowp' ); ?></div>
						</div>
					</div>

					<!-- Free product text -->
					<div class="col-md-6 mb-3">
						<label for="wpowp_free_product_text" class="form-label fw-bold"><?php esc_html_e( 'Free product text', 'wpowp' ); ?></label>
						<input name="wpowp_free_product_text" class="form-control" type="text" value="<?php echo esc_attr( $option['free_product_text'] ); ?>" <?php echo esc_attr( $disabled ); ?> />
					</div>
				</div>
			</div>
		</div>

		<div class="d-flex align-items-center gap-3 mt-4">
			<button type="submit" name="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-submit" id="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-submit" class="button button-primary">
				<?php esc_attr_e( 'Save Changes', 'wpowp' ); ?>
			</button>
			<button type="button" name="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-reset" id="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-reset" class="button button-secondary">
				<?php esc_attr_e( 'Reset Settings', 'wpowp' ); ?>
			</button>
		</div>
		<p class="text-muted small mt-2">
			<i class="dashicons dashicons-warning"></i> <?php esc_html_e( 'Note: Resetting settings will delete all configurations, so use this feature wisely.', 'wpowp' ); ?>
		</p>
	</form>
</div>
