<?php
/**
 * Bottom mobile navigation with icons (React parity).
 *
 * @package Teznevise
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = array();
$locations = get_nav_menu_locations();
if ( ! empty( $locations['bottom'] ) ) {
	$menu_items = wp_get_nav_menu_items( $locations['bottom'] );
	if ( $menu_items ) {
		foreach ( $menu_items as $mi ) {
			if ( (int) $mi->menu_item_parent ) {
				continue;
			}
			$label = $mi->title;
			if ( false !== strpos( $label, 'ابزار' ) ) {
				$label = 'ابزارها';
			} elseif ( false !== strpos( $label, 'تماس' ) ) {
				$label = 'تماس';
			} elseif ( false !== strpos( $label, 'خانه' ) ) {
				$label = 'خانه';
			}
			$items[] = array(
				'label' => $label,
				'url'   => $mi->url,
			);
		}
	}
}
if ( ! $items ) {
	$items = array(
		array( 'label' => __( 'خانه', 'teznevise' ), 'url' => home_url( '/' ) ),
		array( 'label' => __( 'ابزارها', 'teznevise' ), 'url' => home_url( '/tools/' ) ),
		array( 'label' => __( 'بلاگ', 'teznevise' ), 'url' => teznevise_posts_url() ),
		array( 'label' => __( 'تماس', 'teznevise' ), 'url' => 'tel:' . teznevise_get_contact( 'phone_intl' ) ),
	);
}
$items = array_slice( $items, 0, 5 );
$count = count( $items );
?>
<nav class="bottom-nav" data-nav-count="<?php echo esc_attr( (string) $count ); ?>" aria-label="<?php esc_attr_e( 'ناوبری سریع', 'teznevise' ); ?>">
	<?php foreach ( $items as $item ) : ?>
		<a class="bottom-nav-item" href="<?php echo esc_url( $item['url'] ); ?>">
			<i class="<?php echo esc_attr( function_exists( 'teznevise_bottom_icon' ) ? teznevise_bottom_icon( $item['url'], $item['label'] ) : 'fa-solid fa-circle' ); ?>" aria-hidden="true"></i>
			<span><?php echo esc_html( $item['label'] ); ?></span>
		</a>
	<?php endforeach; ?>
</nav>
