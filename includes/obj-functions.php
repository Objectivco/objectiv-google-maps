<?php

/**
 * Check to see if page has a shortcode
 * @param  string  $shortcode
 * @return boolean
 *
 * @since 1.0
 */
function obj_has_shortcode( $shortcode = '' ) {

    global $post;
    $post_obj = get_post( $post->ID );
    $found = false;

    if ( ! $shortcode )
        return $found;

    if ( stripos( $post_obj->post_content, '[' . $shortcode ) !== false )
        $found = true;

    return $found;

}
