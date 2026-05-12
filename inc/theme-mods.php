<?php

function art_zone_blank_translatable_mod_keys() {
    return array(
        'hero_title',
        'hero_kicker',
        'hero_button_text',
        'collection_title',
        'collection_years',
        'artist_name',
        'artist_label',
        'artist_bio',
        'artist_link_text',
        'home_video_label',
        'home_video_title',
        'home_video_text',
        'home_video_link_text',
        'cta_title',
        'cta_text',
        'cta_button_text',
        'footer_location',
        'about_eyebrow',
        'about_title',
        'about_intro_paragraph_1',
        'about_intro_paragraph_2',
        'about_intro_paragraph_3',
        'about_philosophy_title',
        'about_philosophy_quote',
        'about_detail_caption',
        'about_video_eyebrow',
        'about_video_title',
        'about_video_description',
        'portfolio_title',
        'portfolio_all_label',
        'portfolio_count_template',
        'art_therapy_title',
        'contact_hero_kicker',
        'contact_phone',
        'contact_email',
        'contact_address_1_label',
        'contact_address_1_text',
        'contact_address_2_label',
        'contact_address_2_text',
    );
}

function art_zone_blank_register_polylang_strings() {
    if ( ! function_exists( 'pll_register_string' ) ) {
        return;
    }

    foreach ( art_zone_blank_translatable_mod_keys() as $key ) {
        $value = get_theme_mod( 'art_zone_blank_' . $key, '' );

        if ( is_scalar( $value ) && '' !== trim( (string) $value ) ) {
            pll_register_string( 'Theme: ' . $key, (string) $value, 'Art Zone Theme', true );
        }
    }
}

add_action( 'init', 'art_zone_blank_register_polylang_strings', 40 );
add_action( 'customize_save_after', 'art_zone_blank_register_polylang_strings', 10 );

function art_zone_blank_mod( $key, $fallback = '' ) {
    $value = get_theme_mod( 'art_zone_blank_' . $key, $fallback );

    if ( function_exists( 'pll__' ) && in_array( $key, art_zone_blank_translatable_mod_keys(), true ) && is_scalar( $value ) ) {
        return pll__( (string) $value );
    }

    return $value;
}

function art_zone_blank_sanitize_media_value( $value ) {
    $value = is_scalar( $value ) ? trim( (string) $value ) : '';

    if ( '' === $value ) {
        return '';
    }

    if ( ctype_digit( $value ) ) {
        return $value;
    }

    return esc_url_raw( $value );
}

function art_zone_blank_sanitize_media_gallery_value( $value ) {
    $value = is_scalar( $value ) ? (string) $value : '';
    $parts = array_filter( array_map( 'trim', explode( ',', $value ) ) );
    $clean = array();

    foreach ( $parts as $part ) {
        if ( ctype_digit( $part ) ) {
            $clean[] = $part;
            continue;
        }

        $url = esc_url_raw( $part );

        if ( $url ) {
            $clean[] = $url;
        }
    }

    return implode( ',', array_unique( $clean ) );
}

function art_zone_blank_media_mod_url( $key, $fallback = '', $size = 'full' ) {
    $value = art_zone_blank_mod( $key, $fallback );

    if ( is_scalar( $value ) ) {
        $value = trim( (string) $value );
    } else {
        $value = '';
    }

    if ( '' === $value ) {
        return '';
    }

    if ( ctype_digit( $value ) ) {
        $attachment_url = 'full' === $size
            ? wp_get_attachment_url( (int) $value )
            : wp_get_attachment_image_url( (int) $value, $size );

        return $attachment_url ? $attachment_url : '';
    }

    return $value;
}
