<?php
/**
 * Artwork frame data functions.
 *
 * @package Art_Zone_Blank
 */

function art_zone_blank_get_artwork_frames() {
    $posts = get_posts(
        array(
            'post_type'      => 'artwork_frame',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => array(
                'menu_order' => 'ASC',
                'title'      => 'ASC',
            ),
        )
    );

    $frames = array();

    foreach ( $posts as $post ) {
        if ( '0' === (string) get_post_meta( $post->ID, 'frame_is_active', true ) ) {
            continue;
        }

        $png_id  = (int) get_post_meta( $post->ID, 'frame_png_id', true );
        $png_url = '';
        $thumb_url = '';
        if ( $png_id ) {
            $src     = wp_get_attachment_image_url( $png_id, 'full' );
            $png_url = $src ? $src : '';
            $thumb_url = art_zone_blank_get_attachment_image_url_with_fallback(
                $png_id,
                array( 'az-thumb', 'medium', 'thumbnail' )
            );
        }

        $slice_raw = get_post_meta( $post->ID, 'frame_slice', true );

        $frames[] = array(
            'id'          => $post->ID,
            'title'       => get_the_title( $post ),
            'material'    => get_post_meta( $post->ID, 'frame_material', true ),
            'color'       => get_post_meta( $post->ID, 'frame_background_color', true ) ?: '#8b7355',
            'thicknessCm' => (float) get_post_meta( $post->ID, 'frame_thickness_cm', true ),
            'framePngUrl' => $png_url,
            'frameThumbUrl' => $thumb_url ? $thumb_url : $png_url,
            'frameSlice'  => '' !== $slice_raw ? (int) $slice_raw : 30,
        );
    }

    return $frames;
}
