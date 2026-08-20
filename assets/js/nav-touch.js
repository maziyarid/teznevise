/**
 * Touch-device support for the dropdown submenus defined in redesign.css.
 * On a device with no hover capability, the first tap on a parent link
 * (.menu-item-has-children > a) opens its submenu instead of navigating;
 * a second tap on the same link follows it. Tapping outside or pressing
 * Escape closes any open submenu. Devices with real hover are untouched
 * and keep using redesign.css's existing :hover/:focus-within behavior.
 */
( function () {
	'use strict';

	if ( window.matchMedia && window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) {
		return;
	}

	function closeAll( except ) {
		document.querySelectorAll( '.nav-links li.submenu-open' ).forEach( function ( li ) {
			if ( li === except ) { return; }
			li.classList.remove( 'submenu-open' );
		} );
	}

	document.addEventListener( 'click', function ( event ) {
		var link = event.target.closest( '.nav-links .menu-item-has-children > a' );
		if ( link ) {
			var li = link.parentElement;
			if ( li.querySelector( ':scope > .nav-dropdown-toggle' ) ) {
				return;
			}
			if ( ! li.classList.contains( 'submenu-open' ) ) {
				event.preventDefault();
				var parentOpen = link.closest( '.sub-menu' ) ? li.parentElement.closest( 'li.submenu-open' ) : null;
				closeAll( li );
				li.classList.add( 'submenu-open' );
				if ( parentOpen ) { parentOpen.classList.add( 'submenu-open' ); }
			}
			return;
		}
		if ( ! event.target.closest( '.nav-links' ) ) {
			closeAll( null );
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key === 'Escape' ) {
			closeAll( null );
		}
	} );
} )();
