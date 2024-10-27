<?php

// Include Header
include_once 'header.php'; // phpcs:ignore

global $wpowp_fs;

$option = $this->get_settings(); // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable

?>

<div class="container">
	<h1><?php esc_html_e( 'Request Quote ( Quote Only )', WPOWP_TEXT_DOMAIN ); ?></h1>
	<form id="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-form" method="post" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
	<?php
	if ( $wpowp_fs->is_paying() ) {
		?>
		<table class="form-table wpowp-content-table">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Enable Site-Wide', WPOWP_TEXT_DOMAIN ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_quote_only">
								<input name="wpowp_quote_only" type="hidden" value="no" />
								<input name="wpowp_quote_only" type="checkbox" id="wpowp_quote_only" value="yes"
								<?php echo ( true === filter_var( $option['quote_only'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> />
								<p><?php esc_html_e( '( Enable Quote Only / Request Quote store-wide in WooCommerce )', WPOWP_TEXT_DOMAIN ); ?></p>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Hide Place Order Button', WPOWP_TEXT_DOMAIN ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_hide_place_order_button">
								<input name="wpowp_hide_place_order_button" type="hidden" value="no" />
								<input name="wpowp_hide_place_order_button" type="checkbox" id="wpowp_hide_place_order_button" value="yes"
								<?php echo ( true === filter_var( $option['hide_place_order_button'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> />
								<p><?php esc_html_e( '( Hide Place Order Button )', WPOWP_TEXT_DOMAIN ); ?></p>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><label
							for="wpowp_quote_button_postion"><?php esc_html_e( 'Quote Only Button Position', WPOWP_TEXT_DOMAIN ); ?></label>
					</th>
					<td>					
						<select class="regular-text wc-enhanced-select" name="wpowp_quote_button_postion" id="wpowp_quote_button_postion">
							<option value="after_submit" <?php echo ( 'after_submit' === $option['quote_button_postion'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'After Payment Button', WPOWP_TEXT_DOMAIN ); ?></option>
							<option value="before_submit" <?php echo ( 'before_submit' === $option['quote_button_postion'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'Before Payment Button', WPOWP_TEXT_DOMAIN ); ?></option>
						</select>
						<p><?php esc_html_e( '( Where to place Quote Only button )', WPOWP_TEXT_DOMAIN ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Quote Button Text', WPOWP_TEXT_DOMAIN ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_quote_button_text">
								<input class="regular-text" name="wpowp_quote_button_text" type="text" id="wpowp_quote_button_text" value="<?php echo esc_attr( $option['quote_button_text'] ); ?>"
								<?php echo ( true === filter_var( $option['quote_only'], FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : ''; ?> />
								<p><?php esc_html_e( '( Text for Quote button )', WPOWP_TEXT_DOMAIN ); ?></p>
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
					<th scope="row"><?php esc_html_e( 'Quote Only / Request Quote', WPOWP_TEXT_DOMAIN ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_quote_only">
								<input name="wpowp_quote_only" type="checkbox" id="wpowp_quote_only" disabled value="disabled" />
								<p><?php esc_html_e( '( Enable Quote Only button  )', WPOWP_TEXT_DOMAIN ); ?></p>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Hide Place Order Button', WPOWP_TEXT_DOMAIN ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span>checkbox</span>
							</legend>
							<label for="wpowp_hide_place_order_button">
								<input name="wpowp_hide_place_order_button" type="hidden" value="no" />
								<input name="wpowp_hide_place_order_button" type="checkbox" id="wpowp_hide_place_order_button" disabled value="disabled" />
								<p><?php esc_html_e( '( Hide Place Order Button )', WPOWP_TEXT_DOMAIN ); ?></p>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><label
							for="wpowp_quote_button_postion"><?php esc_html_e( 'Quote Only Button Position', WPOWP_TEXT_DOMAIN ); ?></label>
					</th>
					<td>					
						<select class="regular-text wc-enhanced-select" name="wpowp_quote_button_postion" id="wpowp_quote_button_postion" disabled>
							<option value="after_submit"><?php esc_html_e( 'After Payment Button', WPOWP_TEXT_DOMAIN ); ?></option>
							<option value="before_submit"><?php esc_html_e( 'Before Payment Button', WPOWP_TEXT_DOMAIN ); ?></option>
						</select>
						<p><?php esc_html_e( '( Where to place Quote Only button )', WPOWP_TEXT_DOMAIN ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label
							for="wpowp_quote_button_postion"><?php esc_html_e( 'Quote Only Button Position', WPOWP_TEXT_DOMAIN ); ?></label>
					</th>
					<td>					
						<select class="regular-text wc-enhanced-select" name="wpowp_quote_button_postion" id="wpowp_quote_button_postion" disabled>
							<option value="after_submit" <?php echo ( 'after_submit' === $option['quote_button_postion'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'After Payment Button', WPOWP_TEXT_DOMAIN ); ?></option>
							<option value="before_submit" <?php echo ( 'before_submit' === $option['quote_button_postion'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'Before Payment Button', WPOWP_TEXT_DOMAIN ); ?></option>
						</select>
						<p><?php esc_html_e( '( Where to place Quote Only button )', WPOWP_TEXT_DOMAIN ); ?></p>
					</td>
				</tr>	
			</tbody>
		</table>	
		<div><label><?php esc_html_e( 'When clicked it collapses payment methods. Ignores shipping and pushes order through without payment or shipping simply as a quote request.', WPOWP_TEXT_DOMAIN ); ?></label></div>		
					
	<?php } ?>

			</tbody>
		</table>
		<?php
		if ( $wpowp_fs->is_paying() ) {
			?>
				<p class="submit"><input type="submit" name="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-submit" id="<?php echo esc_attr( WPOWP_PLUGIN_PREFIX ); ?>settings-submit" class="button button-primary"
				value="<?php esc_attr_e( 'Save Changes', WPOWP_TEXT_DOMAIN ); ?>"></p>
		<?php } else { ?>				
				<!-- Upgrade to Pro Button -->
				<p class="submit-1">					
					<span>
						<a href="<?php echo esc_url( $wpowp_fs->get_upgrade_url() ); ?>" class="button button-primary" target="_blank">
							<?php esc_html_e( 'Upgrade to Pro', WPOWP_TEXT_DOMAIN ); ?>
						 </a>
					</span>
					<span><?php esc_html_e( 'to avail the Request Quote feature', WPOWP_TEXT_DOMAIN ); ?></span>
				</p>				
		<?php } ?>            
	</form>
</div>
