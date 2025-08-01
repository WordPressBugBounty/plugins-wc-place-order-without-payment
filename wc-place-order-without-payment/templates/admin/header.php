<?php

global $wpowp_fs;

$current_tab = str_replace( 'admin/', '', $tab ); // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable

$div = '<nav class="mt-3 nav nav-pills justify-content-center">';

$div .= '<li class="nav-item"><a class="nav-link ' . ( 'settings' === $current_tab ? 'active' : '' ) . '"  href="' . admin_url( 'admin.php?page=' . WPOWP_PLUGIN_SLUG ) . '&tab=settings">' . __( 'Settings', 'wpowp' ) . '</a>&nbsp;</li>&nbsp;';

if ( $wpowp_fs->is_paying_or_trial() ) {
	$div .= '<li class="nav-item"><a class="nav-link ' . ( 'rules' === $current_tab ? 'active' : '' ) . '"  href="' . admin_url( 'admin.php?page=' . WPOWP_PLUGIN_SLUG ) . '&tab=rules">' . __( 'Rules', 'wpowp' ) . '</a>&nbsp;</li>&nbsp;';
} else {
	$div .= '<li class="nav-item"><a class="nav-link ' . ( 'rules' === $current_tab ? 'active' : '' ) . '"  href="' . admin_url( 'admin.php?page=' . WPOWP_PLUGIN_SLUG ) . '&tab=rules">' . __( 'Rules (PRO)', 'wpowp' ) . '</a>&nbsp;</li>&nbsp;';
}

if ( $wpowp_fs->is_paying_or_trial() ) {
	$div .= '<li class="nav-item"><a class="nav-link ' . ( 'quote-only' === $current_tab ? 'active' : '' ) . '"  href="' . admin_url( 'admin.php?page=' . WPOWP_PLUGIN_SLUG ) . '&tab=quote-only">' . __( 'Request Quote', 'wpowp' ) . '</a>&nbsp;</li>&nbsp;';
} else {
	$div .= '<li class="nav-item"><a class="nav-link ' . ( 'quote-only' === $current_tab ? 'active' : '' ) . '"  href="' . admin_url( 'admin.php?page=' . WPOWP_PLUGIN_SLUG ) . '&tab=quote-only">' . __( 'Request Quote (PRO)', 'wpowp' ) . '</a>&nbsp;</li>&nbsp;';
}

$div .= '<li class="nav-item"><a class="nav-link ' . '" target="_blank"  href="https://nitin247.com/docs/place-order-without-payment">' . __( 'Documentation', 'wpowp' ) . '</a>&nbsp;</li>&nbsp;';

$div .= "</nav>";

echo wp_kses_post( $div );

