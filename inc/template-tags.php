<?php
/**
 * Custom template tags for TezNevise theme
 * 
 * @package TezNevise
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('teznevise_posted_on')) {
    function teznevise_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') === get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
        }
        $time_string = sprintf($time_string, esc_attr(get_the_date('c')), esc_html(get_the_date()));
        $posted_on = sprintf(esc_html__('Posted on %s', 'teznevise'), '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>');
        echo '<span class="posted-on">' . $posted_on . '</span> ';
    }
}

if (!function_exists('teznevise_posted_by')) {
    function teznevise_posted_by() {
        $byline = sprintf(esc_html__('by %s', 'teznevise'), '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>');
        echo '<span class="byline"> ' . $byline . '</span>';
    }
}

if (!function_exists('teznevise_entry_footer')) {
    function teznevise_entry_footer() {
        if ('post' === get_post_type()) {
            $categories_list = get_the_category_list(esc_html__(', ', 'teznevise'));
            if ($categories_list && teznevise_categorized_blog()) {
                printf('<span class="cat-links">' . esc_html__('Posted in %1$s', 'teznevise') . '</span>', $categories_list);
            }
            $tags_list = get_the_tag_list('', esc_html__(', ', 'teznevise'));
            if ($tags_list) {
                printf('<span class="tags-links">' . esc_html__('Tagged %1$s', 'teznevise') . '</span>', $tags_list);
            }
            teznevise_posted_on();
            teznevise_posted_by();
            if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
                echo '<span class="comments-link">';
                comments_popup_link(sprintf(wp_kses(__('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'teznevise'), array('span' => array('class' => array()))), wp_kses_post(get_the_title())));
                echo '</span>';
            }
        }
    }
}

if (!function_exists('teznevise_categorized_blog')) {
    function teznevise_categorized_blog() {
        if (is_search() || is_archive() || is_home()) return true;
        if (is_singular()) return is_object_in_taxonomy(get_post_type(), 'category');
        return false;
    }
}