<?php
/**
 * Interior mockup template data and artwork helpers.
 *
 * @package Art_Zone_Blank
 */

function art_zone_blank_get_interior_background_image( $post_id ) {
    $image_id = (int) get_post_meta( $post_id, 'artwork_interior_image_id', true );

    if ( $image_id ) {
        $image = wp_get_attachment_image_url( $image_id, 'az-interior-bg' );

        if ( $image ) {
            return $image;
        }
    }

    $featured = get_the_post_thumbnail_url( $post_id, 'az-interior-bg' );

    if ( $featured ) {
        return $featured;
    }

    $external = trim( (string) get_post_meta( $post_id, 'artwork_interior_background_url', true ) );

    return preg_match( '#^https?://#', $external ) ? $external : '';
}

function art_zone_blank_get_interior_background_thumbnail_image( $post_id ) {
    $image_id = (int) get_post_meta( $post_id, 'artwork_interior_image_id', true );

    if ( $image_id ) {
        $image = art_zone_blank_get_attachment_image_url_with_fallback(
            $image_id,
            array( 'az-thumb', 'medium_large', 'medium' )
        );

        if ( $image ) {
            return $image;
        }
    }

    $featured_id = get_post_thumbnail_id( $post_id );

    if ( $featured_id ) {
        $image = art_zone_blank_get_attachment_image_url_with_fallback(
            $featured_id,
            array( 'az-thumb', 'medium_large', 'medium' )
        );

        if ( $image ) {
            return $image;
        }
    }

    return art_zone_blank_get_interior_background_image( $post_id );
}

function art_zone_blank_get_dashboard_interior_templates() {
    $posts = get_posts(
        art_zone_blank_query_all_languages(
            array(
                'post_type'      => 'artwork_interior',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => array(
                    'menu_order' => 'ASC',
                    'date'       => 'DESC',
                ),
            )
        )
    );

    $templates = array();

    foreach ( $posts as $post ) {
        $image = art_zone_blank_get_interior_background_image( $post->ID );

        if ( ! $image ) {
            continue;
        }

        $scene_image_width  = get_post_meta( $post->ID, 'artwork_interior_scene_image_width_px', true );
        $scene_image_height = get_post_meta( $post->ID, 'artwork_interior_scene_image_height_px', true );
        $slot_x             = get_post_meta( $post->ID, 'artwork_interior_slot_x_percent', true );
        $slot_y             = get_post_meta( $post->ID, 'artwork_interior_slot_y_percent', true );
        $slot_width_cm      = get_post_meta( $post->ID, 'artwork_interior_slot_max_width_cm', true );
        $slot_height_cm     = get_post_meta( $post->ID, 'artwork_interior_slot_max_height_cm', true );
        $scene_real_width   = get_post_meta( $post->ID, 'artwork_interior_scene_real_width_cm', true );
        $scene_real_height  = get_post_meta( $post->ID, 'artwork_interior_scene_real_height_cm', true );

        // Legacy dashboard entries used pixel scene/slot fields. Normalize them temporarily.
        if ( '' === $scene_image_width ) {
            $scene_image_width = get_post_meta( $post->ID, 'artwork_interior_scene_width', true );
        }

        if ( '' === $scene_image_height ) {
            $scene_image_height = get_post_meta( $post->ID, 'artwork_interior_scene_height', true );
        }

        if ( '' === $slot_x && $scene_image_width ) {
            $legacy_slot_x = (float) get_post_meta( $post->ID, 'artwork_interior_slot_x', true );
            $slot_x        = $legacy_slot_x > 0 ? ( $legacy_slot_x / (float) $scene_image_width ) * 100 : '';
        }

        if ( '' === $slot_y && $scene_image_height ) {
            $legacy_slot_y = (float) get_post_meta( $post->ID, 'artwork_interior_slot_y', true );
            $slot_y        = $legacy_slot_y > 0 ? ( $legacy_slot_y / (float) $scene_image_height ) * 100 : '';
        }

        if ( '' === $slot_width_cm && $scene_real_width ) {
            $legacy_slot_width_percent = get_post_meta( $post->ID, 'artwork_interior_slot_max_width_percent', true );
            if ( '' !== $legacy_slot_width_percent ) {
                $slot_width_cm = ( (float) $legacy_slot_width_percent / 100 ) * (float) $scene_real_width;
            }
        }

        if ( '' === $slot_height_cm && $scene_real_height ) {
            $legacy_slot_height_percent = get_post_meta( $post->ID, 'artwork_interior_slot_max_height_percent', true );
            if ( '' !== $legacy_slot_height_percent ) {
                $slot_height_cm = ( (float) $legacy_slot_height_percent / 100 ) * (float) $scene_real_height;
            }
        }

        if ( '' === $slot_width_cm && $scene_image_width ) {
            $legacy_slot_width = (float) get_post_meta( $post->ID, 'artwork_interior_slot_width', true );
            $slot_width_cm     = $legacy_slot_width > 0 && $scene_real_width ? ( $legacy_slot_width / (float) $scene_image_width ) * (float) $scene_real_width : '';
        }

        if ( '' === $slot_height_cm && $scene_image_height ) {
            $legacy_slot_height = (float) get_post_meta( $post->ID, 'artwork_interior_slot_height', true );
            $slot_height_cm     = $legacy_slot_height > 0 && $scene_real_height ? ( $legacy_slot_height / (float) $scene_image_height ) * (float) $scene_real_height : '';
        }

        $templates[] = array(
            'id'                 => 'interior-' . $post->ID,
            'title'              => get_the_title( $post ),
            'backgroundImage'    => $image,
            'backgroundThumbImage' => art_zone_blank_get_interior_background_thumbnail_image( $post->ID ),
            'backgroundColor'    => get_post_meta( $post->ID, 'artwork_interior_background_color', true ),
            'sceneImageWidthPx'  => $scene_image_width,
            'sceneImageHeightPx' => $scene_image_height,
            'sceneRealWidthCm'   => $scene_real_width,
            'sceneRealHeightCm'  => $scene_real_height,
            'slot'               => array(
                'xPercent'         => $slot_x,
                'yPercent'         => $slot_y,
                'maxWidthCm'       => $slot_width_cm,
                'maxHeightCm'      => $slot_height_cm,
                'alignX'           => get_post_meta( $post->ID, 'artwork_interior_slot_align_x', true ),
                'alignY'           => get_post_meta( $post->ID, 'artwork_interior_slot_align_y', true ),
            ),
            'supports'        => array(
                'orientations' => (array) get_post_meta( $post->ID, 'artwork_interior_orientations', true ),
                'sizeTypes'    => (array) get_post_meta( $post->ID, 'artwork_interior_size_types', true ),
            ),
            'roomType'        => get_post_meta( $post->ID, 'artwork_interior_room_type', true ),
            'sizeType'        => get_post_meta( $post->ID, 'artwork_interior_size_group', true ), // Legacy fallback for old dashboard data.
            'sortOrder'       => get_post_meta( $post->ID, 'artwork_interior_sort_order', true ),
            'isActive'           => '0' !== (string) get_post_meta( $post->ID, 'artwork_interior_is_active', true ),
            'withFrontArt'       => '1' === (string) get_post_meta( $post->ID, 'artwork_interior_with_front_art', true ),
            'staticBackground'   => '1' === (string) get_post_meta( $post->ID, 'artwork_interior_static_background', true ),
        );
    }

    return $templates;
}

function art_zone_blank_normalize_interior_template( $template ) {
    if ( ! is_array( $template ) || empty( $template['id'] ) || empty( $template['backgroundImage'] ) ) {
        return null;
    }

    if ( isset( $template['isActive'] ) && ! filter_var( $template['isActive'], FILTER_VALIDATE_BOOLEAN ) ) {
        return null;
    }

    $slot     = isset( $template['slot'] ) && is_array( $template['slot'] ) ? $template['slot'] : array();
    $supports = isset( $template['supports'] ) && is_array( $template['supports'] ) ? $template['supports'] : array();

    $scene_width  = isset( $template['sceneImageWidthPx'] ) ? (int) $template['sceneImageWidthPx'] : ( isset( $template['sceneWidth'] ) ? (int) $template['sceneWidth'] : 0 );
    $scene_height = isset( $template['sceneImageHeightPx'] ) ? (int) $template['sceneImageHeightPx'] : ( isset( $template['sceneHeight'] ) ? (int) $template['sceneHeight'] : 0 );
    $real_width   = isset( $template['sceneRealWidthCm'] ) ? (float) $template['sceneRealWidthCm'] : 0.0;
    $real_height  = isset( $template['sceneRealHeightCm'] ) ? (float) $template['sceneRealHeightCm'] : 0.0;

    if ( $scene_width <= 0 || $scene_height <= 0 || $real_width <= 0 || $real_height <= 0 ) {
        return null;
    }

    $legacy_x      = isset( $slot['x'] ) ? (float) $slot['x'] : null;
    $legacy_y      = isset( $slot['y'] ) ? (float) $slot['y'] : null;
    $legacy_width  = isset( $slot['width'] ) ? (float) $slot['width'] : null;
    $legacy_height = isset( $slot['height'] ) ? (float) $slot['height'] : null;
    $max_width_cm  = isset( $slot['maxWidthCm'] ) && '' !== $slot['maxWidthCm'] ? (float) $slot['maxWidthCm'] : 0.0;
    $max_height_cm = isset( $slot['maxHeightCm'] ) && '' !== $slot['maxHeightCm'] ? (float) $slot['maxHeightCm'] : 0.0;

    if ( $max_width_cm <= 0 && isset( $slot['maxWidthPercent'] ) && '' !== $slot['maxWidthPercent'] ) {
        $max_width_cm = ( (float) $slot['maxWidthPercent'] / 100 ) * $real_width;
    }

    if ( $max_height_cm <= 0 && isset( $slot['maxHeightPercent'] ) && '' !== $slot['maxHeightPercent'] ) {
        $max_height_cm = ( (float) $slot['maxHeightPercent'] / 100 ) * $real_height;
    }

    if ( $max_width_cm <= 0 && null !== $legacy_width ) {
        $max_width_cm = ( $legacy_width / $scene_width ) * $real_width;
    }

    if ( $max_height_cm <= 0 && null !== $legacy_height ) {
        $max_height_cm = ( $legacy_height / $scene_height ) * $real_height;
    }

    $normalized_slot = array(
        'xPercent'         => isset( $slot['xPercent'] ) && '' !== $slot['xPercent'] ? (float) $slot['xPercent'] : ( null !== $legacy_x ? ( $legacy_x / $scene_width ) * 100 : 0.0 ),
        'yPercent'         => isset( $slot['yPercent'] ) && '' !== $slot['yPercent'] ? (float) $slot['yPercent'] : ( null !== $legacy_y ? ( $legacy_y / $scene_height ) * 100 : 0.0 ),
        'maxWidthCm'       => $max_width_cm,
        'maxHeightCm'      => $max_height_cm,
        'maxWidthPercent'  => $real_width > 0 ? ( $max_width_cm / $real_width ) * 100 : 0.0,
        'maxHeightPercent' => $real_height > 0 ? ( $max_height_cm / $real_height ) * 100 : 0.0,
        'alignX'           => isset( $slot['alignX'] ) ? sanitize_key( $slot['alignX'] ) : 'center',
        'alignY'           => isset( $slot['alignY'] ) ? sanitize_key( $slot['alignY'] ) : 'center',
    );

    if ( $normalized_slot['maxWidthCm'] <= 0 || $normalized_slot['maxHeightCm'] <= 0 ) {
        return null;
    }

    if ( ! in_array( $normalized_slot['alignX'], array( 'left', 'center', 'right' ), true ) ) {
        $normalized_slot['alignX'] = 'center';
    }

    if ( ! in_array( $normalized_slot['alignY'], array( 'top', 'center', 'bottom' ), true ) ) {
        $normalized_slot['alignY'] = 'center';
    }

    $orientations = isset( $supports['orientations'] ) ? (array) $supports['orientations'] : array();
    $orientations = array_values(
        array_intersect(
            array_map( 'sanitize_key', $orientations ),
            array( 'portrait', 'landscape', 'square' )
        )
    );

    if ( empty( $orientations ) ) {
        return null;
    }

    $size_types = isset( $supports['sizeTypes'] ) ? (array) $supports['sizeTypes'] : array();

    if ( empty( $size_types ) && ! empty( $template['sizeType'] ) ) {
        $size_types = array( $template['sizeType'] );
    }

    if ( empty( $size_types ) && ! empty( $template['sizeGroup'] ) ) {
        $size_types = array( $template['sizeGroup'] );
    }

    $size_types = array_values(
        array_intersect(
            array_map( 'sanitize_key', $size_types ),
            array( 'xs', 'sm', 'md', 'lg', 'xl' )
        )
    );

    if ( empty( $size_types ) ) {
        return null;
    }

    $sort_order     = isset( $template['sortOrder'] ) && '' !== $template['sortOrder'] ? (int) $template['sortOrder'] : 100;
    $background_hex = ! empty( $template['backgroundColor'] ) ? sanitize_hex_color( $template['backgroundColor'] ) : '';

    return array(
        'id'                 => sanitize_key( $template['id'] ),
        'title'              => isset( $template['title'] ) ? sanitize_text_field( $template['title'] ) : '',
        'backgroundImage'    => esc_url_raw( $template['backgroundImage'] ),
        'backgroundThumbImage' => ! empty( $template['backgroundThumbImage'] ) ? esc_url_raw( $template['backgroundThumbImage'] ) : esc_url_raw( $template['backgroundImage'] ),
        'backgroundColor'    => $background_hex ? $background_hex : '#e8dfd2',
        'sceneImageWidthPx'  => $scene_width,
        'sceneImageHeightPx' => $scene_height,
        'sceneWidth'         => $scene_width,
        'sceneHeight'        => $scene_height,
        'sceneRealWidthCm'   => $real_width,
        'sceneRealHeightCm'  => $real_height,
        'slot'               => $normalized_slot,
        'supports'           => array(
            'orientations' => $orientations,
            'sizeTypes'    => $size_types,
        ),
        'roomType'        => isset( $template['roomType'] ) ? sanitize_key( $template['roomType'] ) : '',
        'sortOrder'       => $sort_order,
        'isActive'          => true,
        'withFrontArt'      => ! empty( $template['withFrontArt'] ),
        'staticBackground'  => ! empty( $template['staticBackground'] ),
    );
}

function art_zone_blank_get_interior_templates() {
    $templates = art_zone_blank_get_dashboard_interior_templates();
    $templates = apply_filters( 'art_zone_blank_interior_templates', $templates );

    return array_values(
        array_filter(
            array_map( 'art_zone_blank_normalize_interior_template', (array) $templates )
        )
    );
}

function art_zone_blank_get_artwork_dimensions( $post_id ) {
    return array(
        'widthCm'  => (float) get_post_meta( $post_id, 'artwork_width_cm', true ),
        'heightCm' => (float) get_post_meta( $post_id, 'artwork_height_cm', true ),
    );
}

function art_zone_blank_get_artwork_orientation( $width_cm, $height_cm ) {
    if ( $width_cm > $height_cm ) {
        return 'landscape';
    }

    if ( $height_cm > $width_cm ) {
        return 'portrait';
    }

    return 'square';
}

function art_zone_blank_get_artwork_aspect_ratio( $width_cm, $height_cm ) {
    if ( $width_cm <= 0 || $height_cm <= 0 ) {
        return 0.0;
    }

    return $width_cm / $height_cm;
}

function art_zone_blank_get_artwork_size_type( $width_cm, $height_cm ) {
    $longest_side = max( (float) $width_cm, (float) $height_cm );

    if ( $longest_side <= 40 ) {
        return 'xs';
    }

    if ( $longest_side <= 70 ) {
        return 'sm';
    }

    if ( $longest_side <= 110 ) {
        return 'md';
    }

    if ( $longest_side <= 150 ) {
        return 'lg';
    }

    return 'xl';
}

function art_zone_blank_get_artwork_size_group( $width_cm, $height_cm ) {
    return art_zone_blank_get_artwork_size_type( $width_cm, $height_cm );
}

function art_zone_blank_get_artwork_aspect_type( $width_cm, $height_cm ) {
    $orientation = art_zone_blank_get_artwork_orientation( $width_cm, $height_cm );
    $ratio       = art_zone_blank_get_artwork_aspect_ratio( $width_cm, $height_cm );

    if ( 'square' === $orientation ) {
        return 'square';
    }

    if ( 'portrait' === $orientation ) {
        return $ratio <= 0.8 ? 'narrow' : 'standard';
    }

    return $ratio >= 1.5 ? 'wide' : 'standard';
}

function art_zone_blank_get_artwork_data( $post_id ) {
    $dimensions   = art_zone_blank_get_artwork_dimensions( $post_id );
    $width_cm     = (float) $dimensions['widthCm'];
    $height_cm    = (float) $dimensions['heightCm'];
    $image_url    = art_zone_blank_get_artwork_image( $post_id, 'az-card-sm' );
    $aspect_ratio = art_zone_blank_get_artwork_aspect_ratio( $width_cm, $height_cm );

    if ( $width_cm <= 0 || $height_cm <= 0 || empty( $image_url ) ) {
        return array();
    }

    return array(
        'postId'         => (int) $post_id,
        'imageUrl'       => $image_url,
        'imageAlt'       => get_the_title( $post_id ),
        'useDropShadow'  => art_zone_blank_artwork_uses_drop_shadow( $post_id ),
        'widthCm'       => $width_cm,
        'heightCm'      => $height_cm,
        'orientation'   => art_zone_blank_get_artwork_orientation( $width_cm, $height_cm ),
        'aspectRatio'   => $aspect_ratio,
        'sizeType'      => art_zone_blank_get_artwork_size_type( $width_cm, $height_cm ),
        'aspectType'    => art_zone_blank_get_artwork_aspect_type( $width_cm, $height_cm ),
        'longestSideCm' => max( $width_cm, $height_cm ),
    );
}

function art_zone_blank_slot_alignment_to_css( $axis, $value ) {
    $value = sanitize_key( $value );

    if ( 'x' === $axis ) {
        if ( 'left' === $value ) {
            return 'flex-start';
        }

        if ( 'right' === $value ) {
            return 'flex-end';
        }

        return 'center';
    }

    if ( 'top' === $value ) {
        return 'flex-start';
    }

    if ( 'bottom' === $value ) {
        return 'flex-end';
    }

    return 'center';
}

function art_zone_blank_render_artwork_interior_mockups( $post_id ) {
    $artwork = art_zone_blank_get_artwork_data( $post_id );

    if ( empty( $artwork ) ) {
        return;
    }

    $templates = art_zone_blank_get_interior_templates();
    $matches   = art_zone_blank_get_matching_interior_templates( $artwork, $templates, 10 );

    if ( empty( $matches ) ) {
        return;
    }

    get_template_part(
        'template-parts/artwork/interior-mockups',
        null,
        array(
            'artwork'   => $artwork,
            'templates' => $matches,
        )
    );
}

if ( ! function_exists( 'get_interior_templates' ) ) {
    function get_interior_templates() {
        return art_zone_blank_get_interior_templates();
    }
}

if ( ! function_exists( 'get_artwork_dimensions' ) ) {
    function get_artwork_dimensions( int $post_id ): array {
        return art_zone_blank_get_artwork_dimensions( $post_id );
    }
}

if ( ! function_exists( 'get_artwork_orientation' ) ) {
    function get_artwork_orientation( float $widthCm, float $heightCm ): string {
        return art_zone_blank_get_artwork_orientation( $widthCm, $heightCm );
    }
}

if ( ! function_exists( 'get_artwork_aspect_ratio' ) ) {
    function get_artwork_aspect_ratio( float $widthCm, float $heightCm ): float {
        return art_zone_blank_get_artwork_aspect_ratio( $widthCm, $heightCm );
    }
}

if ( ! function_exists( 'get_artwork_size_group' ) ) {
    function get_artwork_size_group( float $widthCm, float $heightCm ): string {
        return art_zone_blank_get_artwork_size_group( $widthCm, $heightCm );
    }
}

if ( ! function_exists( 'get_artwork_size_type' ) ) {
    function get_artwork_size_type( float $widthCm, float $heightCm ): string {
        return art_zone_blank_get_artwork_size_type( $widthCm, $heightCm );
    }
}

if ( ! function_exists( 'get_artwork_aspect_type' ) ) {
    function get_artwork_aspect_type( float $widthCm, float $heightCm ): string {
        return art_zone_blank_get_artwork_aspect_type( $widthCm, $heightCm );
    }
}

if ( ! function_exists( 'get_artwork_data' ) ) {
    function get_artwork_data( int $post_id ): array {
        return art_zone_blank_get_artwork_data( $post_id );
    }
}

if ( ! function_exists( 'render_artwork_interior_mockups' ) ) {
    function render_artwork_interior_mockups( int $post_id ): void {
        art_zone_blank_render_artwork_interior_mockups( $post_id );
    }
}
