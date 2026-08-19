<?php
/**
 * Custom nav walker for accessible dropdown submenus.
 *
 * Adds aria-haspopup/aria-expanded and a keyboard/touch-friendly toggle
 * button to menu items with children, so multi-level menus work on both
 * hover (desktop) and tap (mobile) without relying on hidden CSS assumptions.
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Teznevise_Nav_Walker' ) ) {
	class Teznevise_Nav_Walker extends Walker_Nav_Menu {

		public function start_lvl( &$output, $depth = 0, $args = null ) {
			$indent = str_repeat( "\t", $depth );
			$output .= "\n$indent<ul class=\"sub-menu dropdown-panel\" role=\"menu\">\n";
		}

		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$indent = $depth ? str_repeat( "\t", $depth ) : '';

			$classes       = empty( $item->classes ) ? array() : (array) $item->classes;
			$has_children  = in_array( 'menu-item-has-children', $classes, true );
			$class_names   = join( ' ', array_filter( $classes ) );
			$li_class      = 'menu-item' . ( $has_children ? ' has-dropdown' : '' ) . ( $class_names ? ' ' . $class_names : '' );

			$output .= sprintf( '%s<li id="menu-item-%d" class="%s">', $indent, $item->ID, esc_attr( $li_class ) );

			$atts           = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target ) ? $item->target : '';
			$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
			$atts['href']   = ! empty( $item->url ) ? $item->url : '';

			if ( $has_children ) {
				$atts['aria-haspopup'] = 'true';
				$atts['aria-expanded'] = 'false';
			}

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( '' === $value ) { continue; }
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}

			$title = apply_filters( 'the_title', $item->title, $item->ID );
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

			$before = isset( $args->before ) ? $args->before : '';
			$after  = isset( $args->after ) ? $args->after : '';
			$link_before = isset( $args->link_before ) ? $args->link_before : '';
			$link_after  = isset( $args->link_after ) ? $args->link_after : '';

			$item_output  = $before;
			$item_output .= '<a' . $attributes . '>';
			$item_output .= $link_before . $title . $link_after;
			$item_output .= '</a>';

			if ( $has_children ) {
				$item_output .= '<button type="button" class="submenu-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'باز کردن زیرمنو', 'teznevise' ) . '"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button>';
			}

			$item_output .= $after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
}
