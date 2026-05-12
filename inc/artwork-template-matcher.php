<?php
/**
 * Interior mockup matching logic.
 *
 * @package Art_Zone_Blank
 */

function art_zone_blank_is_template_category_match( $artwork, $template ) {
    if ( empty( $artwork['orientation'] ) || empty( $artwork['sizeType'] ) || empty( $template['supports'] ) ) {
        return false;
    }

    $supports     = $template['supports'];
    $orientations = isset( $supports['orientations'] ) ? (array) $supports['orientations'] : array();
    $size_types   = isset( $supports['sizeTypes'] ) ? (array) $supports['sizeTypes'] : array();

    if ( ! in_array( $artwork['orientation'], $orientations, true ) ) {
        return false;
    }

    return in_array( $artwork['sizeType'], $size_types, true );
}

function art_zone_blank_calculate_artwork_scene_proportion( $artwork, $template, $container_width_px = 100, $container_height_px = 100 ) {
    $scene_real_width  = isset( $template['sceneRealWidthCm'] ) ? (float) $template['sceneRealWidthCm'] : 0.0;
    $scene_real_height = isset( $template['sceneRealHeightCm'] ) ? (float) $template['sceneRealHeightCm'] : 0.0;
    $art_width         = isset( $artwork['widthCm'] ) ? (float) $artwork['widthCm'] : 0.0;
    $art_height        = isset( $artwork['heightCm'] ) ? (float) $artwork['heightCm'] : 0.0;

    if ( $scene_real_width <= 0 || $scene_real_height <= 0 || $art_width <= 0 || $art_height <= 0 ) {
        return array(
            'widthPx'       => 0.0,
            'heightPx'      => 0.0,
            'widthPercent'  => 0.0,
            'heightPercent' => 0.0,
        );
    }

    $width_percent  = ( $art_width / $scene_real_width ) * 100;
    $height_percent = ( $art_height / $scene_real_height ) * 100;

    return array(
        'widthPx'       => (float) $container_width_px * ( $art_width / $scene_real_width ),
        'heightPx'      => (float) $container_height_px * ( $art_height / $scene_real_height ),
        'widthPercent'  => $width_percent,
        'heightPercent' => $height_percent,
    );
}

function art_zone_blank_template_can_fit_artwork( $artwork, $template, $container_width_px = 100, $container_height_px = 100, $tolerance = 1.05 ) {
    if ( ! art_zone_blank_is_template_category_match( $artwork, $template ) ) {
        return false;
    }

    $slot       = isset( $template['slot'] ) && is_array( $template['slot'] ) ? $template['slot'] : array();
    $art_width  = isset( $artwork['widthCm'] ) ? (float) $artwork['widthCm'] : 0.0;
    $art_height = isset( $artwork['heightCm'] ) ? (float) $artwork['heightCm'] : 0.0;
    $slot_width = isset( $slot['maxWidthCm'] ) ? (float) $slot['maxWidthCm'] : 0.0;
    $slot_h     = isset( $slot['maxHeightCm'] ) ? (float) $slot['maxHeightCm'] : 0.0;
    $tolerance  = max( 1.0, (float) $tolerance );

    if ( $slot_width <= 0 || $slot_h <= 0 || $art_width <= 0 || $art_height <= 0 ) {
        return false;
    }

    return $art_width <= $slot_width * $tolerance
        && $art_height <= $slot_h * $tolerance;
}

function art_zone_blank_get_artwork_render_dimensions_for_template( $artwork, $template, $container_width_px = 100, $container_height_px = 100 ) {
    return art_zone_blank_calculate_artwork_scene_proportion( $artwork, $template, $container_width_px, $container_height_px );
}

function art_zone_blank_is_template_match( $artwork, $template ) {
    return art_zone_blank_template_can_fit_artwork( $artwork, $template );
}

function art_zone_blank_score_template_match( $artwork, $template ) {
    if ( ! art_zone_blank_is_template_match( $artwork, $template ) ) {
        return INF;
    }

    $size_types = isset( $template['supports']['sizeTypes'] ) ? (array) $template['supports']['sizeTypes'] : array();
    $sort_order = isset( $template['sortOrder'] ) ? (int) $template['sortOrder'] : 100;

    // Keep exact single-size templates before broader templates with the same sort order.
    $size_specificity = count( $size_types ) <= 1 ? 0 : 1;

    return ( $sort_order * 10 ) + $size_specificity;
}

function art_zone_blank_sort_scored_templates( $a, $b ) {
    if ( $a['_matchScore'] === $b['_matchScore'] ) {
        return (int) $a['sortOrder'] <=> (int) $b['sortOrder'];
    }

    return $a['_matchScore'] <=> $b['_matchScore'];
}

function art_zone_blank_get_matching_interior_templates( $artwork, $templates, $limit = 10 ) {
    $matches = array();

    foreach ( (array) $templates as $template ) {
        if ( ! art_zone_blank_is_template_match( $artwork, $template ) ) {
            continue;
        }

        $template['_matchScore'] = art_zone_blank_score_template_match( $artwork, $template );
        $matches[]               = $template;
    }

    usort( $matches, 'art_zone_blank_sort_scored_templates' );

    return array_slice( $matches, 0, max( 0, (int) $limit ) );
}

function art_zone_blank_get_fallback_interior_templates( $artwork, $templates, $limit = 3 ) {
    return art_zone_blank_get_matching_interior_templates( $artwork, $templates, $limit );
}

if ( ! function_exists( 'is_template_match' ) ) {
    function is_template_match( array $artwork, array $template ): bool {
        return art_zone_blank_is_template_match( $artwork, $template );
    }
}

if ( ! function_exists( 'is_template_category_match' ) ) {
    function is_template_category_match( array $artwork, array $template ): bool {
        return art_zone_blank_is_template_category_match( $artwork, $template );
    }
}

if ( ! function_exists( 'calculate_artwork_scene_proportion' ) ) {
    function calculate_artwork_scene_proportion( array $artwork, array $template, float $containerWidthPx, float $containerHeightPx ): array {
        return art_zone_blank_calculate_artwork_scene_proportion( $artwork, $template, $containerWidthPx, $containerHeightPx );
    }
}

if ( ! function_exists( 'template_can_fit_artwork' ) ) {
    function template_can_fit_artwork( array $artwork, array $template, float $containerWidthPx, float $containerHeightPx, float $tolerance = 1.05 ): bool {
        return art_zone_blank_template_can_fit_artwork( $artwork, $template, $containerWidthPx, $containerHeightPx, $tolerance );
    }
}

if ( ! function_exists( 'get_artwork_render_dimensions_for_template' ) ) {
    function get_artwork_render_dimensions_for_template( array $artwork, array $template, float $containerWidthPx, float $containerHeightPx ): array {
        return art_zone_blank_get_artwork_render_dimensions_for_template( $artwork, $template, $containerWidthPx, $containerHeightPx );
    }
}

if ( ! function_exists( 'score_template_match' ) ) {
    function score_template_match( array $artwork, array $template ): float {
        return art_zone_blank_score_template_match( $artwork, $template );
    }
}

if ( ! function_exists( 'get_matching_interior_templates' ) ) {
    function get_matching_interior_templates( array $artwork, array $templates, int $limit = 10 ): array {
        return art_zone_blank_get_matching_interior_templates( $artwork, $templates, $limit );
    }
}

if ( ! function_exists( 'get_fallback_interior_templates' ) ) {
    function get_fallback_interior_templates( array $artwork, array $templates, int $limit = 3 ): array {
        return art_zone_blank_get_fallback_interior_templates( $artwork, $templates, $limit );
    }
}
