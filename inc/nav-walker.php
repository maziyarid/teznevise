<?php
/**
 * Accessible dropdown walker for the Teznevise primary navigation.
 *
 * Adds a *separate* disclosure button next to any top-level menu item that
 * has children, so the item's own link keeps navigating normally (clicking
 * it is never intercepted) while the button toggles the submenu. Submenus
 * use plain list/link semantics (no `role="menu"`), matching the WAI-ARIA
 * disclosure-navigation pattern rather than the more complex application
 * menu widget, since site navigation should not claim menu-widget semantics
 * it doesn't fully implement.
 *
 * Depth (3rd-level / `.nav-dropdown-l3`) design note: third-level items are
 * rendered as an always-visible nested list *inside* the already-open
 * second-level `.nav-dropdown` panel, not as their own separate disclosure.
 * Once a visitor opens the parent `.nav-dropdown-toggle`, everything nested
 * inside that panel -- including any third-level items -- is already
 * revealed and keyboard/tab-reachable, so a second nested toggle would add
 * complexity without adding accessibility value. If a future menu genuinely
 * needs a *collapsed-by-default* third level, extend `has_children` handling
 * below to depth 1 as well and give each nested toggle a unique id.
 *
 * @package Teznevise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Teznevise_Nav_Walker' ) && class_exists( 'Walker_Nav_Menu' ) ) {

	class Teznevise_Nav_Walker extends Walker_Nav_Menu {

		/**
		 * Start the list before the elements are added.
		 *
		 * @param string   $output Passed by reference.
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   Menu arguments.
		 */
		public function start_lvl( &$output, $depth = 0, $args = null ) {
			$indent      = str_repeat( "\t", $depth );
			$level_class = $depth > 0 ? ' nav-dropdown-l3' : ' nav-dropdown nav-panel mega';
			$output     .= "{$indent}<ul class=\"sub-menu{$level_class}\">\n";
		}

		/**
		 * End the list of after the elements are added.
		 *
		 * @param string   $output Passed by reference.
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   Menu arguments.
		 */
		public function end_lvl( &$output, $depth = 0, $args = null ) {
			$indent  = str_repeat( "\t", $depth );
			$output .= "{$indent}</ul>\n";
		}

		/**
		 * Start the element output.
		 *
		 * Preserves the standard WordPress link attributes (target, rel/XFN,
		 * title) that a site admin may configure per menu item, and runs the
		 * `nav_menu_link_attributes` filter so other plugins/themes hooking
		 * into menu rendering still behave as expected. Only top-level
		 * (`0 === $depth`) items with children get their own disclosure
		 * button -- see the class docblock for why nested (3rd-level) items
		 * are intentionally always-visible inside the open panel instead.
		 *
		 * @param string   $output Passed by reference.
		 * @param WP_Post  $item   Menu item data object.
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   Menu arguments.
		 * @param int      $id     Current item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$classes      = empty( $item->classes ) ? array() : (array) $item->classes;
			$has_children = in_array( 'menu-item-has-children', $classes, true );

			if ( $has_children && 0 === $depth ) {
				$classes[] = 'has-dropdown';
			}
			if ( 1 === (int) $depth ) {
				$classes[] = 'mega-col';
			}
			if ( 0 === $depth && function_exists( 'teznevise_nav_is_cta_duplicate' ) && teznevise_nav_is_cta_duplicate( isset( $item->url ) ? $item->url : '' ) ) {
				$classes[] = 'nav-hide-desktop';
			}

			$classes = apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth );
			$classes = array_map( 'sanitize_html_class', (array) $classes );
			$li_id   = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
			$li_id   = $li_id ? ' id="' . esc_attr( $li_id ) . '"' : '';
			$output .= '<li' . $li_id . ' class="' . esc_attr( implode( ' ', array_filter( $classes ) ) ) . '">';

			$atts                 = array();
			$atts['title']        = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target']       = ! empty( $item->target ) ? $item->target : '';
			$atts['rel']          = ! empty( $item->xfn ) ? $item->xfn : '';
			$atts['href']         = ! empty( $item->url ) ? $item->url : '';
			$atts['class']        = 'nav-link';
			$atts['aria-current'] = $item->current ? 'page' : '';

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( '' === $value || false === $value ) {
					continue;
				}
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}

			$title = apply_filters( 'the_title', $item->title, $item->ID );
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

			$href             = isset( $atts['href'] ) ? (string) $atts['href'] : '';
			$path             = (string) wp_parse_url( $href, PHP_URL_PATH );
			$is_group_heading = ( 1 === (int) $depth && $has_children && ( '' === $href || '#' === $href || '' === $path ) );

			$item_output = isset( $args->before ) ? $args->before : '';
			$icon        = '';
			if ( function_exists( 'teznevise_nav_icon' ) ) {
				$icon = teznevise_nav_icon( $href, $title );
			}
			if ( $is_group_heading ) {
				$item_output .= '<h4 class="mega-heading">';
				if ( $icon ) {
					$item_output .= '<i class="' . esc_attr( $icon ) . ' nav-item-icon" aria-hidden="true"></i>';
				}
				$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $title . ( isset( $args->link_after ) ? $args->link_after : '' );
				$item_output .= '</h4>';
			} else {
				$item_output .= '<a' . $attributes . '>';
				if ( $icon ) {
					$item_output .= '<i class="' . esc_attr( $icon ) . ' nav-item-icon" aria-hidden="true"></i>';
				}
				$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $title . ( isset( $args->link_after ) ? $args->link_after : '' );
				if ( $has_children && 0 === $depth ) {
					$item_output .= '<span class="nav-chevron" aria-hidden="true"></span>';
				}
				$item_output .= '</a>';
			}
			$item_output .= isset( $args->after ) ? $args->after : '';

			// A separate button beside the link, not the link itself, toggles the
			// submenu. This keeps the parent item's own destination reachable by
			// click, tap, or Enter on the link at all times. Restricted to
			// top-level items -- see class docblock for the depth>0 rationale.
			if ( $has_children && 0 === $depth ) {
				$menu_id_attr  = ! empty( $item->ID ) ? ' data-menu-item="' . esc_attr( $item->ID ) . '"' : '';
				$item_output  .= '<button type="button" class="nav-dropdown-toggle" aria-expanded="false"' . $menu_id_attr . ' aria-label="' . esc_attr__( 'نمایش زیرمنو', 'teznevise' ) . '">';
				$item_output  .= '<span class="nav-chevron" aria-hidden="true"></span>';
				$item_output  .= '</button>';
			}

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
}
