/**
 * Toggle behavior for multi-level dropdown submenus rendered by
 * Teznevise_Nav_Walker (see inc/nav-walker.php). Handles tap-to-open on
 * touch/keyboard, closes on outside click, Escape, and blur.
 */
( function () {
	'use strict';

	function closeAll( except ) {
		document.querySelectorAll( '.main-nav .has-dropdown.submenu-open' ).forEach( function ( li ) {
			if ( li === except ) { return; }
			li.classList.remove( 'submenu-open' );
			var btn = li.querySelector( '.submenu-toggle' );
			if ( btn ) { btn.setAttribute( 'aria-expanded', 'false' ); }
			var link = li.querySelector( '> a' );
			if ( link ) { link.setAttribute( 'aria-expanded', 'false' ); }
		} );
	}

	document.addEventListener( 'click', function ( event ) {
		var toggle = event.target.closest( '.submenu-toggle' );
		if ( toggle ) {
			event.preventDefault();
			var li = toggle.closest( '.has-dropdown' );
			if ( ! li ) { return; }
			var isOpen = li.classList.contains( 'submenu-open' );
			closeAll( isOpen ? null : li );
			li.classList.toggle( 'submenu-open', ! isOpen );
			toggle.setAttribute( 'aria-expanded', String( ! isOpen ) );
			var link = li.querySelector( '> a' );
			if ( link ) { link.setAttribute( 'aria-expanded', String( ! isOpen ) ); }
			return;
		}
		if ( ! event.target.closest( '.main-nav' ) ) {
			closeAll( null );
		}
	}, { passive: false } );

	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key === 'Escape' ) {
			closeAll( null );
		}
	}, { passive: true } );

	document.querySelectorAll( '.main-nav .has-dropdown' ).forEach( function ( li ) {
		li.addEventListener( 'focusout', function ( event ) {
			if ( ! li.contains( event.relatedTarget ) ) {
				li.classList.remove( 'submenu-open' );
				var btn = li.querySelector( '.submenu-toggle' );
				if ( btn ) { btn.setAttribute( 'aria-expanded', 'false' ); }
			}
		} );
	} );
} )();
