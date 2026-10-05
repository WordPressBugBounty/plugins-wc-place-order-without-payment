<?php
// Include Header
include_once 'header.php'; // phpcs:ignore

global $wpowp_fs;

$option = $this->get_settings(); // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable

?>

<div class="container-1 py-4">
	<div class="fs-3"><?php esc_html_e( 'Request Quote ( Quote Only )', 'wpowp' ); ?></div>

	<form id="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-form" method="post" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">

		<div class="card-1 mb-4">
			<div class="card-header py-3">
				<h5 class="card-title mb-0"><?php esc_html_e( 'Quote Settings', 'wpowp' ); ?></h5>
			</div>
			<div class="card-body">
				<?php
				$is_pro   = $wpowp_fs->is_paying_or_trial();
				$disabled = $is_pro ? '' : 'disabled';
				?>

				<div class="row">
					<!-- Enable Site-Wide -->
					<div class="col-md-4 mb-3">
						<div class="form-check-1 form-switch-1">
							<input class="form-check-input" type="checkbox" name="wpowp_quote_only" id="wpowp_quote_only" value="yes"
								<?php echo ( true === filter_var( $option['quote_only'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> <?php echo esc_attr( $disabled ); ?> />
							<label class="form-check-label fw-bold" for="wpowp_quote_only"><?php esc_html_e( 'Enable Site-Wide', 'wpowp' ); ?></label>
							<div class="form-text"><?php esc_html_e( '( Enable Quote Only / Request Quote store-wide in WooCommerce )', 'wpowp' ); ?></div>
						</div>
					</div>

					<!-- Hide Place Order Button -->
					<div class="col-md-4 mb-3">
						<div class="form-check-1 form-switch-1">
							<input class="form-check-input" type="checkbox" name="wpowp_hide_place_order_button" id="wpowp_hide_place_order_button" value="yes"
								<?php echo ( true === filter_var( $option['hide_place_order_button'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> <?php echo esc_attr( $disabled ); ?> />
							<label class="form-check-label fw-bold" for="wpowp_hide_place_order_button"><?php esc_html_e( 'Hide Place Order Button', 'wpowp' ); ?></label>
							<div class="form-text"><?php esc_html_e( '( Hide Place Order Button )', 'wpowp' ); ?></div>
						</div>
					</div>

					<!-- Order Status -->
					<div class="col-md-4 mb-3">
						<label for="wpowp_order_status_quote" class="form-label fw-bold"><?php esc_html_e( 'Order Status', 'wpowp' ); ?></label>
						<select class="form-select" name="wpowp_order_status_quote" id="wpowp_order_status_quote" <?php echo esc_attr( $disabled ); ?>>
							<?php
							if ( ! empty( $this->order_status_list() ) ) {
								$status_list = $this->order_status_list();
								foreach ( $status_list as $key => $label ) {
									echo '<option value="' . wp_kses_post( $key ) . '" ' . ( $key === $option['order_status_quote'] ? 'selected' : '' ) . '>' . wp_kses_post( $label ) . '</option>';
								}
							}
							?>
						</select>
						<div class="form-text"><?php esc_html_e( '( Order status after request quote )', 'wpowp' ); ?></div>
					</div>

					<!-- Quote Only Button Position -->
					<div class="col-md-4 mb-3">
						<label for="wpowp_quote_button_postion" class="form-label fw-bold"><?php esc_html_e( 'Quote Only Button Position', 'wpowp' ); ?></label>
						<select class="form-select" name="wpowp_quote_button_postion" id="wpowp_quote_button_postion" <?php echo esc_attr( $disabled ); ?>>
							<option value="after_submit" <?php echo ( 'after_submit' === $option['quote_button_postion'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'After Payment Button', 'wpowp' ); ?></option>
							<option value="before_submit" <?php echo ( 'before_submit' === $option['quote_button_postion'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'Before Payment Button', 'wpowp' ); ?></option>
						</select>
						<div class="form-text"><?php esc_html_e( '( Where to place Quote Only button )', 'wpowp' ); ?></div>
					</div>

					<!-- Quote Button Text -->
					<div class="col-md-4 mb-3">
						<label for="wpowp_quote_button_text" class="form-label fw-bold"><?php esc_html_e( 'Quote Button Text', 'wpowp' ); ?></label>
						<input class="form-control" name="wpowp_quote_button_text" type="text" id="wpowp_quote_button_text" value="<?php echo esc_attr( $option['quote_button_text'] ); ?>" <?php echo esc_attr( $disabled ); ?> />
						<div class="form-text"><?php esc_html_e( '( Text for Quote button )', 'wpowp' ); ?></div>
					</div>
				</div>
			</div>
		</div>		

		<div class="d-flex align-items-center gap-3 mt-4">
			<?php if ( $is_pro ) { ?>
				<button type="submit" name="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-submit" id="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-submit" class="button button-primary">
					<?php esc_attr_e( 'Save Changes', 'wpowp' ); ?>
				</button>
			<?php } ?>
		</div>
	</form>
</div>
