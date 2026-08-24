<?php
/**
 * Teznevise REST API Endpoints
 * 
 * Handles API routes for page data and chat functionality.
 * Ensures React components can reliably communicate with WordPress backend.
 */

if ( ! defined( "ABSPATH" ) ) {
    exit;
}

/**
 * Register REST API routes
 */
add_action( "rest_api_init", function() {
    register_rest_route( "teznevise/v1", "/page-data/(?P<id>\\d+)", array(
        "methods"      => "GET",
        "callback"     => "teznevise_get_page_data",
        "permission_callback" => "__return_true",
        "args"         => array(
            "id" => array(
                "validate_callback" => function( $param ) {
                    return is_numeric( $param );
                },
            ),
        ),
    ) );

    register_rest_route( "teznevise/v1", "/chat-config", array(
        "methods"      => "GET",
        "callback"     => "teznevise_get_chat_config",
        "permission_callback" => "__return_true",
    ) );

    register_rest_route( "teznevise/v1", "/chat-message", array(
        "methods"      => "POST",
        "callback"     => "teznevise_handle_chat_message",
        "permission_callback" => "__return_true",
        "args"         => array(
            "message" => array(
                "required"          => true,
                "type"              => "string",
                "sanitize_callback" => "sanitize_text_field",
            ),
        ),
    ) );
} );

/**
 * Get page data with metadata
 */
function teznevise_get_page_data( $request ) {
    $id = intval( $request["id"] );
    $page = get_post( $id );

    if ( ! $page ) {
        return new WP_Error( "page_not_found", "Page not found", array( "status" => 404 ) );
    }

    return array(
        "id"       => $id,
        "title"    => $page->post_title,
        "content"  => $page->post_content,
        "excerpt"  => $page->post_excerpt,
        "status"   => $page->post_status,
        "type"     => $page->post_type,
        "date"     => $page->post_date,
        "meta"     => get_post_meta( $id ),
        "permalink" => get_permalink( $id ),
    );
}

/**
 * Get chat widget configuration
 */
function teznevise_get_chat_config( $request ) {
    return array(
        "status"   => "ready",
        "enabled"  => true,
        "api_url"  => rest_url( "teznevise/v1" ),
        "site_url" => site_url(),
        "messages" => array(
            "welcome" => "سلام! چطور می‌تونم کمکتون کنم؟", // "Hello! How can I help you?"
            "error"   => "متأسفانه خطایی رخ داد. لطفاً دوباره تلاش کنید.", // "Sorry, an error occurred. Please try again."
        ),
    );
}

/**
 * Handle incoming chat messages
 */
function teznevise_handle_chat_message( $request ) {
    $message = $request->get_param( "message" );

    if ( empty( $message ) ) {
        return new WP_Error( "empty_message", "Message cannot be empty", array( "status" => 400 ) );
    }

    // Log message if needed
    error_log( "Chat message received: " . $message );

    return array(
        "status"   => "success",
        "message"  => "پیام شما دریافت شد. با تشکر!", // "Your message was received. Thank you!"
        "timestamp" => current_time( "mysql" ),
    );
}
