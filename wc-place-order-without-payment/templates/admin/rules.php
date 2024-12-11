<?php

use WPOWP\Traits\Get_Instance;

use WPOWP\Modules\Rules as WPOWP_Rules;
use WPOWP\Helper as WPOWP_Helper;

// Include Header
include_once 'header.php'; // phpcs:ignore

global $wpowp_fs;

$rules_instance = WPOWP_Rules::get_instance();
?>

<div class="container">
		
	<h1><?php esc_html_e( 'Rules', WPOWP_TEXT_DOMAIN ); ?></h1>
	<p><?php esc_html_e( 'The options below facilitate the configuration of Place Order Without Pyment for WooCommerce PRO, enhancing customer engagement.', WPOWP_TEXT_DOMAIN ); ?></p>

	<div id="group-container"></div>

	<div id="button-container" style="display:none;">
		<!-- Add New Rule Group Button -->
		<button type="button" class="button button-secondary" id="add-group-btn">
			<?php esc_html_e( 'Add New Rule', WPOWP_TEXT_DOMAIN ); ?>
		</button>
		<?php if ( $wpowp_fs->is_paying() ) { ?>						
		<!-- Save Changes Button -->
		<button type="button" class="button button-primary" id="wctrpro-save-tyrules">
			<?php esc_html_e( 'Save changes', 'woocommerce' ); ?>
		</button>
		<?php } else { ?>
		<!-- Upgrade to Pro Button -->
		<span>
			<a href="<?php echo esc_url( $wpowp_fs->get_upgrade_url() ); ?>" class="button button-primary" target="_blank">
				<?php esc_html_e( 'Upgrade to Pro', WPOWP_TEXT_DOMAIN ); ?>
			</a>
		</span>		
		<?php } ?>
	</div>	

	<!-- Group Template -->
	<script type="text/template" id="group-template">
		<div class="rule-group border rounded shadow p-3 mb-4">
			<!-- Header: Move and Remove Buttons -->
			<div class="d-flex justify-content-between align-items-center mb-3">
				<div class="d-flex align-items-center">
					<a href="javascript:void(0)">
						<i class="text-secondary fas fa-bars"></i>
					</a>
				</div>
				<div class="d-flex align-items-center">
					<a class="btn btn-sm remove-group-btn" title="<?php echo esc_attr( 'Remove Group', WPOWP_TEXT_DOMAIN ); ?>">
						<i class="dashicons dashicons-remove text-danger"></i>
					</a>
				</div>
			</div>

			<!-- Toggle Switches for Order Options -->
			<div class="d-flex justify-content-around mb-2">
				<!-- Place Order Switch -->
				<div class="d-inline-block">
					<input class="my-1 placeOrderSwitch" type="checkbox" id="placeOrderSwitch" name="placeOrderSwitch" checked value="1"
						title="<?php echo esc_attr( 'Toggle to enable or disable Place Order Without Payment.', WPOWP_TEXT_DOMAIN ); ?>" />
					<label for="placeOrderSwitch"><?php esc_html_e( 'Place Order Without Payment', WPOWP_TEXT_DOMAIN ); ?></label>
				</div>

				<!-- Request Quote Switch -->
				<div class="d-inline-block">
					<input class="my-1 requestQuoteSwitch" type="checkbox" id="requestQuoteSwitch" name="requestQuoteSwitch" checked value="1"
						title="<?php echo esc_attr( 'Toggle to enable or disable Request Quote button.', WPOWP_TEXT_DOMAIN ); ?>" />
					<label for="requestQuoteSwitch"><?php esc_html_e( 'Request Quote', WPOWP_TEXT_DOMAIN ); ?></label>
				</div>

				<!-- Order Approval Switch (Commented Out) -->
				<!--
				<div class="d-inline-block">
					<input class="my-1 orderApprovalSwitch" type="checkbox" id="orderApprovalSwitch" name="orderApprovalSwitch" checked value="1"
						title="<?php echo esc_attr( 'Toggle to enable or disable order approval.', WPOWP_TEXT_DOMAIN ); ?>" />
					<label for="orderApprovalSwitch"><?php esc_html_e( 'Order Approval', WPOWP_TEXT_DOMAIN ); ?></label>
				</div>
				-->
			</div>

			<!-- Rule List (Dynamically Populated) -->
			<div class="rule-list"></div>

			<!-- Add Condition Button -->
			<button type="button" class="btn btn-secondary btn-sm add-rule-btn">
				<?php esc_html_e( 'Add Condition', WPOWP_TEXT_DOMAIN ); ?>
			</button>
		</div>
	</script>

	<!-- Rule Template -->
	<script type="text/template" id="rule-template">
		<div class="row mb-3 rule-row">
			<div class="col-md-3">
				<?php echo $rules_instance->create_dropdown_options(); // phpcs:ignore ?>
			</div>
			<div class="col-md-3">
				<?php echo $rules_instance->create_dropdown_operators(); // phpcs:ignore ?>
			</div>
			<div class="col-md-4">
				<!-- Value Input -->
				<div class="value-input">
					<input name="value" type="text" class="form-control input-value" placeholder="<?php echo esc_attr( 'Enter value', WPOWP_TEXT_DOMAIN ); ?>">
				</div>

				<!-- Value Select -->
				<div class="value-select" style="display:none;">
					<select name="value" class="form-select select-value">
						<option value=""><?php esc_html_e( 'Select a value', WPOWP_TEXT_DOMAIN ); ?></option>
					</select>
				</div>

				<!-- Value Multiselect -->
				<div class="value-multiselect" style="display:none;">
					<select name="value" class="form-select select2-multiselect multiselect-value" multiple>
						<option value=""><?php esc_html_e( 'Select multiple values', WPOWP_TEXT_DOMAIN ); ?></option>
					</select>
				</div>
			</div>

			<!-- Condition Selector -->
			<div class="col-md-1">
				<select class="form-select condition-selector">
					<option value="AND"><?php esc_html_e( ' and ', WPOWP_TEXT_DOMAIN ); ?></option>
					<option value="OR"><?php esc_html_e( ' or ', WPOWP_TEXT_DOMAIN ); ?></option>
				</select>
			</div>

			<!-- Remove Rule Button -->
			<div class="col-md-1 text-end">
				<a href="javascript:void(0)" class="btn btn-sm remove-rule-btn" title="<?php echo esc_attr( 'Remove Rule', WPOWP_TEXT_DOMAIN ); ?>">
					<i class="dashicons dashicons-remove text-danger"></i>
				</a>
			</div>
		</div>
	</script>

</div>
