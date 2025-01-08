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
		
	<h1><?php esc_html_e( 'Rules', 'wpowp' ); ?></h1>
	<p><?php esc_html_e( 'The options below facilitate the configuration of Place Order Without Pyment for WooCommerce PRO, enhancing customer engagement.', 'wpowp' ); ?></p>

	<div id="group-container"></div>

	<div id="button-container" style="display:none;">
		<!-- Add New Rule Group Button -->
		<button type="button" class="button button-secondary" id="add-group-btn">
			<?php esc_html_e( 'Add New Rule', 'wpowp' ); ?>
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
				<?php esc_html_e( 'Upgrade to Pro', 'wpowp' ); ?>
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
					<a href="javascript:void(0)" class="text-decoration-none">
						<i class="text-secondary dashicons dashicons-menu"></i>
					</a>
				</div>
				<div class="d-flex align-items-center">
					<a class="btn btn-sm remove-group-btn" title="<?php echo esc_attr( 'Remove Group', 'wpowp' ); ?>">
						<i class="dashicons dashicons-remove text-danger"></i>
					</a>
				</div>
			</div>

			<!-- Toggle Switches for Order Options -->
			<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 mb-3">
				<!-- Place Order Switch -->
				<div class="col">
					<input class="my-1 placeOrderSwitch" type="checkbox" id="placeOrderSwitch" name="placeOrderSwitch" checked value="1"
						title="<?php echo esc_attr( 'Toggle to enable or disable Place Order Without Payment.', 'wpowp' ); ?>" />
					<label for="placeOrderSwitch"><?php esc_html_e( 'Place Order Without Payment', 'wpowp' ); ?></label>
				</div>

				<!-- Request Quote Switch -->
				<div class="col">
					<input class="my-1 requestQuoteSwitch" type="checkbox" id="requestQuoteSwitch" name="requestQuoteSwitch" checked value="1"
						title="<?php echo esc_attr( 'Toggle to enable or disable Request Quote button.', 'wpowp' ); ?>" />
					<label for="requestQuoteSwitch"><?php esc_html_e( 'Request Quote', 'wpowp' ); ?></label>
				</div>

				<!-- Order Button Text Switch -->
				<div class="col">
					<input class="my-1 orderButtonTextSwitch" type="checkbox" id="orderButtonTextSwitch" name="orderButtonTextSwitch" value="0"
						title="<?php echo esc_attr( 'Toggle to enable or disable Custom Order Button Text.', 'wpowp' ); ?>" />
					<label for="orderButtonTextSwitch"><?php esc_html_e( 'Custom Order Button Text', 'wpowp' ); ?></label>
				</div>

				<!-- Remove Shipping Fields and rates Switch -->
				<!-- <div class="col">
					<input class="my-1 removeShippingFieldsRatesSwitch" type="checkbox" id="removeShippingFieldsRatesSwitch" name="removeShippingFieldsRatesSwitch" value="0"
						title="<?php echo esc_attr( 'Toggle to enable or disable Remove shipping fields and rates.', 'wpowp' ); ?>" />
					<label for="removeShippingFieldsRatesSwitch"><?php esc_html_e( 'Remove shipping fields, rates', 'wpowp' ); ?></label>
				</div>				 -->

				<!-- Remove Tax rates Switch -->
				<!-- <div class="col">
					<input class="my-1 removeTaxRatesSwitch" type="checkbox" id="removeTaxRatesSwitch" name="removeTaxRatesSwitch" value="0"
						title="<?php echo esc_attr( 'Toggle to enable or disable Remove Tax rates.', 'wpowp' ); ?>" />
					<label for="removeTaxRatesSwitch"><?php esc_html_e( 'Remove Tax rates', 'wpowp' ); ?></label>
				</div> -->

				<!-- Remove Checkout Privacy Switch -->
				<!-- <div class="col">
					<input class="my-1 removeCheckoutPrivacySwitch" type="checkbox" id="removeCheckoutPrivacySwitch" name="removeCheckoutPrivacySwitch" value="0"
						title="<?php echo esc_attr( 'Toggle to enable or disable Remove checkout privacy text.', 'wpowp' ); ?>" />
					<label for="removeCheckoutPrivacySwitch"><?php esc_html_e( 'Remove checkout privacy text', 'wpowp' ); ?></label>
				</div> -->

				<!-- Remove Checkout Terms Switch -->
				<!-- <div class="col">
					<input class="my-1 removeCheckoutTermsSwitch" type="checkbox" id="removeCheckoutTermsSwitch" name="removeCheckoutTermsSwitch" value="0"
						title="<?php echo esc_attr( 'Toggle to enable or disable Remove checkout terms and conditions.', 'wpowp' ); ?>" />
					<label for="removeCheckoutTermsSwitch"><?php esc_html_e( 'Remove checkout terms', 'wpowp' ); ?></label>
				</div> -->

				<!-- Order Approval Switch (Commented Out) -->
				<!--
				<div class="d-inline-block">
					<input class="my-1 orderApprovalSwitch" type="checkbox" id="orderApprovalSwitch" name="orderApprovalSwitch" checked value="1"
						title="<?php echo esc_attr( 'Toggle to enable or disable order approval.', 'wpowp' ); ?>" />
					<label for="orderApprovalSwitch"><?php esc_html_e( 'Order Approval', 'wpowp' ); ?></label>
				</div>
				-->
			</div>

			<!-- Rule List (Dynamically Populated) -->
			<div class="rule-list"></div>

			<!-- Add Condition Button -->
			<button type="button" class="btn btn-secondary btn-sm add-rule-btn">
				<?php esc_html_e( 'Add Condition', 'wpowp' ); ?>
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
					<input name="value" type="text" class="form-control input-value" placeholder="<?php echo esc_attr( 'Enter value', 'wpowp' ); ?>">
				</div>

				<!-- Value Select -->
				<div class="value-select" style="display:none;">
					<select name="value" class="form-select select-value">
						<option value=""><?php esc_html_e( 'Select a value', 'wpowp' ); ?></option>
					</select>
				</div>

				<!-- Value Multiselect -->
				<div class="value-multiselect" style="display:none;">
					<select name="value" class="form-select select2-multiselect multiselect-value" multiple>
						<option value=""><?php esc_html_e( 'Select multiple values', 'wpowp' ); ?></option>
					</select>
				</div>
			</div>

			<!-- Condition Selector -->
			<div class="col-md-1">
				<select class="form-select condition-selector">
					<option value="AND"><?php esc_html_e( ' and ', 'wpowp' ); ?></option>
					<option value="OR"><?php esc_html_e( ' or ', 'wpowp' ); ?></option>
				</select>
			</div>

			<!-- Remove Rule Button -->
			<div class="col-md-1 text-end">
				<a href="javascript:void(0)" class="btn btn-sm remove-rule-btn" title="<?php echo esc_attr( 'Remove Rule', 'wpowp' ); ?>">
					<i class="dashicons dashicons-remove text-danger"></i>
				</a>
			</div>
		</div>
	</script>

</div>
