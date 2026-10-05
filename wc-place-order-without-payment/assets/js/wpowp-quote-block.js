/**
 * Replace the registered "Request a Quote" checkbox field with a button on
 * the WooCommerce Blocks checkout, matching the classic checkout's
 * button-based UX. No custom CSS is added here - the injected button reuses
 * the native WooCommerce Blocks button classes, the same way the classic
 * checkout button relies only on the theme's own ".button" style.
 */
( function () {
	if ( typeof wpowpQuoteBlock === 'undefined' ) {
		return;
	}

	var fieldId    = wpowpQuoteBlock.fieldId;
	var buttonText = wpowpQuoteBlock.label;
	var position   = wpowpQuoteBlock.position;
	var buttonId   = 'wpowp-quote-only';

	function setup() {
		if ( document.getElementById( buttonId ) ) {
			return;
		}

		var checkbox = document.getElementById( fieldId );

		if ( ! checkbox ) {
			return;
		}

		var checkboxWrapper = checkbox.closest( '.wc-block-components-checkbox' );
		var actionsRow       = document.querySelector( '.wc-block-checkout__actions_row' );
		var placeOrderButton = document.querySelector( '.wc-block-components-checkout-place-order-button' );

		if ( ! checkboxWrapper || ! actionsRow || ! placeOrderButton ) {
			return;
		}

		checkboxWrapper.style.display = 'none';

		var quoteButton = document.createElement( 'button' );
		quoteButton.type      = 'button';
		quoteButton.id        = buttonId;
		quoteButton.className = 'wc-block-components-button wp-element-button';
		quoteButton.textContent = buttonText;

		quoteButton.addEventListener( 'click', function ( event ) {
			event.preventDefault();

			if ( ! checkbox.checked ) {
				checkbox.click();
			}

			placeOrderButton.click();
		} );

		if ( 'before_submit' === position ) {
			actionsRow.insertBefore( quoteButton, placeOrderButton );
		} else {
			actionsRow.insertBefore( quoteButton, placeOrderButton.nextSibling );
		}
	}

	document.addEventListener( 'DOMContentLoaded', setup );

	var observer = new MutationObserver( setup );
	observer.observe( document.body, { childList: true, subtree: true } );
} )();
