<?php
defined( 'ABSPATH' ) || exit;

global $wpowp_fs;

$is_pro = $wpowp_fs->is_paying_or_trial();

$current_tab = str_replace( 'admin/', '', $tab );

$tabs = array(
	'settings'   => array(
		'label' => __( 'General Settings', 'wpowp' ),
		'icon'  => 'dashicons-admin-generic',
	),
	'rules'      => array(
		'label' => $is_pro ? __( 'Rules', 'wpowp' ) : __( 'Rules(PRO)', 'wpowp' ),
		'icon'  => 'dashicons-networking',
	),
	'quote-only' => array(
		'label' => $is_pro ? __( 'Request Quote', 'wpowp' ) : __( 'Request Quote(PRO)', 'wpowp' ),
		'icon'  => 'dashicons-format-chat',
	),
);

?>

<div class="wrap">

	<!-- Hero -->

	<div class="card-1 shadow-sm border-0 mb-2">

		<div class="card-body">

			<div class="d-flex justify-content-between align-items-center flex-wrap">

				<div>

					<h3 class="mt-3">

						<span class="dashicons dashicons-cart text-primary align-middle me-1 fs-5"></span>

						<?php esc_html_e( 'Place Order Without Payment', 'wpowp' ); ?>

					</h3>

					<p class="text-muted mt-3">

						<?php
						esc_html_e(
							'Allow customers to place orders without immediate payment and create quote workflows.',
							'wpowp'
						);
						?>

					</p>

				</div>

				<div class="d-flex m-3 gap-1">

					<a
						class="btn btn-outline-secondary btn-sm"
						target="_blank"
						href="https://nitin247.com/docs/place-order-without-payment">

						<span class="dashicons dashicons-media-document"></span>

						Docs

					</a>

					<a
						class="btn btn-outline-secondary btn-sm"
						href="<?php echo esc_url( $wpowp_fs->contact_url() ); ?>">

						<span class="dashicons dashicons-sos"></span>

						Support

					</a>

					<?php if ( ! $wpowp_fs->is_paying_or_trial() ) : ?>

						<div class="btn-group">

							<a
								class="btn btn-primary btn-sm fw-semibold"
								target="_blank"
								href="<?php echo esc_url( $wpowp_fs->get_upgrade_url() ); ?>">
								<span class="dashicons dashicons-star-filled"></span>
								<?php esc_html_e( 'Upgrade PRO', 'wpowp' ); ?>
							</a>

							<button
								type="button"
								class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split"
								data-bs-toggle="offcanvas"
								data-bs-target="#wpowpProFeatures">
							</button>

						</div>

						<div
							class="offcanvas offcanvas-end wpowp-pro-panel"
							tabindex="-1"
							id="wpowpProFeatures"
							aria-labelledby="wpowpProFeaturesLabel">

							<div class="offcanvas-header border-bottom">

								<h5
									class="offcanvas-title"
									id="wpowpProFeaturesLabel">

									<span class="dashicons dashicons-star-filled text-warning"></span>

									<?php esc_html_e( 'Place Order Without Payment PRO', 'wpowp' ); ?>

								</h5>

								<button
									type="button"
									class="btn-close"
									data-bs-dismiss="offcanvas"
									aria-label="<?php esc_attr_e( 'Close', 'wpowp' ); ?>">
								</button>

							</div>

							<div class="d-flex flex-column" style="height:100%;">

								<div class="offcanvas-body flex-grow-1 overflow-auto">

									<p class="text-muted mb-3">

										<?php
										esc_html_e(
											'Create powerful B2B, wholesale, preorder and quote workflows in WooCommerce.',
											'wpowp'
										);
										?>

									</p>

									<div class="row g-2">

										<div class="col-12">
											<div class="border rounded p-3">
												<div class="fw-semibold">

													<?php
													esc_html_e(
														'⚙️ Advanced Rules Engine',
														'wpowp'
													);
													?>

												</div>
												<div class="small text-muted">

													<?php
													esc_html_e(
														'Unlimited Rules • Cart Total • Products • Categories • User Roles',
														'wpowp'
													);
													?>

												</div>
											</div>
										</div>

										<div class="col-12">
											<div class="border rounded p-3">
												<div class="fw-semibold">

													<?php
													esc_html_e(
														'🛒 Request Quote System',
														'wpowp'
													);
													?>

												</div>
												<div class="small text-muted">

													<?php
													esc_html_e(
														'Hide Prices • Replace Add to Cart • Quote-Only Catalog',
														'wpowp'
													);
													?>

												</div>
											</div>
										</div>

										<div class="col-12">
											<div class="border rounded p-3">
												<div class="fw-semibold">

													<?php
													esc_html_e(
														'💳 Payment & Checkout Control',
														'wpowp'
													);
													?>

												</div>
												<div class="small text-muted">

													<?php
													esc_html_e(
														'Disable Payment Gateways • Remove Checkout Fields • Checkout Optimization',
														'wpowp'
													);
													?>

												</div>
											</div>
										</div>

										<div class="col-12">
											<div class="border rounded p-3">
												<div class="fw-semibold">

													<?php
													esc_html_e(
														'🚀 Store Customization',
														'wpowp'
													);
													?>

												</div>
												<div class="small text-muted">

													<?php
													esc_html_e(
														'Custom Button Labels • FREE Product Labels • Site-Wide Controls',
														'wpowp'
													);
													?>

												</div>
											</div>
										</div>

										<div class="col-12">
											<div class="border rounded p-3">
												<div class="fw-semibold">

													<?php
													esc_html_e(
														'⚡ Professional Features',
														'wpowp'
													);
													?>

												</div>
												<div class="small text-muted">

													<?php
													esc_html_e(
														'Preorders • Wholesale Workflows • Priority Support • Future Updates',
														'wpowp'
													);
													?>

												</div>
											</div>
										</div>

									</div>

								</div>

								<div class="border-top bg-white p-3 offcanvas-footer">

									<div class="d-grid">

										<a
											class="btn btn-primary"
											target="_blank"
											href="<?php echo esc_url( $wpowp_fs->get_upgrade_url() ); ?>">
											<span class="dashicons dashicons-star-filled"></span>
											<?php esc_html_e( 'Upgrade to PRO', 'wpowp' ); ?>

										</a>

									</div>

								</div>

							</div>

						</div>

					<?php endif; ?>

				</div>

			</div>

			<!-- Navigation -->

			<div class="pb-3 d-flex flex-wrap gap-2">

				<?php foreach ( $tabs as $slug => $item ) : ?>

					<?php
					$url = admin_url(
						'admin.php?page=' .
						WPOWP_PLUGIN_SLUG .
						'&tab=' .
						$slug
					);
					?>

				<a
					class="btn <?php echo ( $current_tab === $slug ) ? 'btn-primary' : 'btn-outline-secondary'; ?> btn-sm"
					href="<?php echo esc_url( $url ); ?>">

					<span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>

					<?php echo esc_html( $item['label'] ); ?>

				</a>

				<?php endforeach; ?>

			</div>

		</div>
