<?php
/**
 * Accessible dropdown walker for the Teznevise primary navigation.
 *
 * The static design reference (teznevise_work/) expects desktop submenus to
 * expose a toggle affordance (chevron), an aria-expanded state, and a
 * `.nav-dropdown` panel — none of which the default WordPress
 * Walker_Nav_Menu produces. This walker adds that markup on top of the
 * standard menu structure without changing menu-editing behaviour in wp-admin.
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
			$level_class = $depth > 0 ? ' nav-dropdown-l3' : ' nav-dropdown';
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
		 * @param string   $output Passed by reference.
		 * @param WP_Post  $item   Menu item data object.
		 * @param int      $depth  Depth of menu item.
		 * @param stdClass $args   Menu arguments.
		 * @param int      $id     Current item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$classes      = empty( $item->classes ) ? array() : (array) $item->classes;
			$has_children = in_array( 'menu-item-has-children', $classes, true );

			if ( $has_children ) {
				$classes[] = 'has-dropdown';
			}

			$class_names = implode( ' ', array_filter( $classes ) );
			$output     .= '<li class="' . esc_attr( $class_names ) . '">';

			$atts                  = array();
			$atts['href']          = ! empty( $item->url ) ? $item->url : '';
			$atts['class']         = 'nav-link';
			$atts['aria-current']  = $item->current ? 'page' : '';



			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( '' === $value ) {
					continue;
				}
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}

			$title = apply_filters( 'the_title', $item->title, $item->ID );
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

			$item_output  = isset( $args->before ) ? $args->before : '';
			$item_output .= '<a' . $attributes . '>';
			$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $title . ( isset( $args->link_after ) ? $args->link_after : '' );
			$item_output .= '</a>';
			if ( $has_children && 0 === $depth ) {
				$item_output .= ' <button type="button" class="nav-dropdown-toggle" aria-label="' . esc_attr( sprintf( __( 'باز کردن زیرمنوی %s', 'teznevise' ), wp_strip_all_tags( $title ) ) ) . '" aria-haspopup="true" aria-expanded="false"><span class="nav-chevron" aria-hidden="true"><i class="fa-solid fa-chevron-down"></i></span></button>';
			}
			$item_output .= isset( $args->after ) ? $args->after : '';

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
}
