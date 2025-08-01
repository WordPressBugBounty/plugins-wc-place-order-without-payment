<?php

// Include Header
include_once 'header.php'; // phpcs:ignore

global $wpowp_fs;

$option = $this->get_settings(); // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable

?>

<div class="container">
	<h1><?php esc_html_e( 'Request Quote ( Quote Only )', 'wpowp' ); ?></h1>
	<form id="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-form" method="post" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
	<?php
	if ( $wpowp_fs->is_paying_or_trial() ) {
		?>
		<table class="form-table wpowp-content-table">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Enable Site-Wide', 'wpowp' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_quote_only">
								<input name="wpowp_quote_only" type="hidden" value="no" />
								<input name="wpowp_quote_only" type="checkbox" id="wpowp_quote_only" value="yes"
								<?php echo ( true === filter_var( $option['quote_only'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> />
								<p><?php esc_html_e( '( Enable Quote Only / Request Quote store-wide in WooCommerce )', 'wpowp' ); ?></p>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Hide Place Order Button', 'wpowp' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_hide_place_order_button">
								<input name="wpowp_hide_place_order_button" type="hidden" value="no" />
								<input name="wpowp_hide_place_order_button" type="checkbox" id="wpowp_hide_place_order_button" value="yes"
								<?php echo ( true === filter_var( $option['hide_place_order_button'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> />
								<p><?php esc_html_e( '( Hide Place Order Button )', 'wpowp' ); ?></p>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Hide Prices Sitewide', 'wpowp' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_hide_prices_sitewide">
								<input name="wpowp_hide_prices_sitewide" type="hidden" value="no" />
								<input name="wpowp_hide_prices_sitewide" type="checkbox" id="wpowp_hide_prices_sitewide" value="yes"
								<?php echo ( true === filter_var( $option['hide_prices_sitewide'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> />
								<p><?php esc_html_e( '( Disables the visibility of product prices across the entire website )', 'wpowp' ); ?></p>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><label
							for="wpowp_quote_button_postion"><?php esc_html_e( 'Quote Only Button Position', 'wpowp' ); ?></label>
					</th>
					<td>					
						<select class="regular-text wc-enhanced-select" name="wpowp_quote_button_postion" id="wpowp_quote_button_postion">
							<option value="after_submit" <?php echo ( 'after_submit' === $option['quote_button_postion'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'After Payment Button', 'wpowp' ); ?></option>
							<option value="before_submit" <?php echo ( 'before_submit' === $option['quote_button_postion'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'Before Payment Button', 'wpowp' ); ?></option>
						</select>
						<p><?php esc_html_e( '( Where to place Quote Only button )', 'wpowp' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Quote Button Text', 'wpowp' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_quote_button_text">
								<input class="regular-text" name="wpowp_quote_button_text" type="text" id="wpowp_quote_button_text" value="<?php echo esc_attr( $option['quote_button_text'] ); ?>"
								<?php echo ( true === filter_var( $option['quote_only'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> />
								<p><?php esc_html_e( '( Text for Quote button )', 'wpowp' ); ?></p>
							</label>
						</fieldset>
					</td>
				</tr>	
			</tbody>
		</table>		
		<?php
	}
	if ( $wpowp_fs->is_not_paying() ) {
		?>
				
		<table class="form-table wpowp-content-table">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Quote Only / Request Quote', 'wpowp' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_quote_only">
								<input name="wpowp_quote_only" type="checkbox" id="wpowp_quote_only" disabled value="disabled" />
								<p><?php esc_html_e( '( Enable Quote Only button  )', 'wpowp' ); ?></p>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Hide Place Order Button', 'wpowp' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_hide_place_order_button">
								<input name="wpowp_hide_place_order_button" type="hidden" value="no" />
								<input name="wpowp_hide_place_order_button" type="checkbox" id="wpowp_hide_place_order_button" disabled value="disabled" />
								<p><?php esc_html_e( '( Hide Place Order Button )', 'wpowp' ); ?></p>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Hide Price Sitewide', 'wpowp' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_hide_price_sitewide">
								<input name="wpowp_hide_price_sitewide" type="hidden" value="no" />
								<input name="wpowp_hide_price_sitewide" type="checkbox" id="wpowp_hide_price_sitewide" disabled value="disabled" />
								<p><?php esc_html_e( '( Hide Price Sitewide )', 'wpowp' ); ?></p>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><label
							for="wpowp_quote_button_postion"><?php esc_html_e( 'Quote Only Button Position', 'wpowp' ); ?></label>
					</th>
					<td>					
						<select class="regular-text wc-enhanced-select" name="wpowp_quote_button_postion" id="wpowp_quote_button_postion" disabled>
							<option value="after_submit"><?php esc_html_e( 'After Payment Button', 'wpowp' ); ?></option>
							<option value="before_submit"><?php esc_html_e( 'Before Payment Button', 'wpowp' ); ?></option>
						</select>
						<p><?php esc_html_e( '( Where to place Quote Only button )', 'wpowp' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label
							for="wpowp_quote_button_postion"><?php esc_html_e( 'Quote Only Button Position', 'wpowp' ); ?></label>
					</th>
					<td>					
						<select class="regular-text wc-enhanced-select" name="wpowp_quote_button_postion" id="wpowp_quote_button_postion" disabled>
							<option value="after_submit" <?php echo ( 'after_submit' === $option['quote_button_postion'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'After Payment Button', 'wpowp' ); ?></option>
							<option value="before_submit" <?php echo ( 'before_submit' === $option['quote_button_postion'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'Before Payment Button', 'wpowp' ); ?></option>
						</select>
						<p><?php esc_html_e( '( Where to place Quote Only button )', 'wpowp' ); ?></p>
					</td>
				</tr>	
			</tbody>
		</table>	
		<div><label><?php esc_html_e( 'When clicked it collapses payment methods. Ignores shipping and pushes order through without payment or shipping simply as a quote request.', 'wpowp' ); ?></label></div>		
					
	<?php } ?>

			</tbody>
		</table>
		<?php
		if ( $wpowp_fs->is_paying_or_trial() ) {
			?>
				<p class="submit"><input type="submit" name="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-submit" id="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-submit" class="button button-primary"
				value="<?php esc_attr_e( 'Save Changes', 'wpowp' ); ?>"></p>
		<?php } else { ?>				
				<!-- Upgrade to Pro Button -->
				<p class="submit-1">					
					<span>
						<a href="<?php echo esc_url( $wpowp_fs->get_upgrade_url() ); ?>" class="button button-primary" target="_blank">
							<?php esc_html_e( 'Upgrade to Pro', 'wpowp' ); ?>
						</a>
					</span>
					<span><?php esc_html_e( 'to avail the Request Quote feature', 'wpowp' ); ?></span>
				</p>				
		<?php } ?>            
	</form>
</div>
