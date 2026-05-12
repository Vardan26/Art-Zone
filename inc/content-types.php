<?php

add_filter(
    'pll_get_post_types',
    function ( $post_types, $is_settings ) {
        $post_types['artwork']          = 'artwork';
        $post_types['studio_item']      = 'studio_item';
        $post_types['art_therapy_item'] = 'art_therapy_item';

        return $post_types;
    },
    10,
    2
);

add_filter(
    'pll_get_taxonomies',
    function ( $taxonomies, $is_settings ) {
        $taxonomies['artwork_type']     = 'artwork_type';
        $taxonomies['artwork_category'] = 'artwork_category';
        $taxonomies['artwork_material'] = 'artwork_material';
        $taxonomies['artwork_medium']   = 'artwork_medium';

        return $taxonomies;
    },
    10,
    2
);

add_filter(
    'use_block_editor_for_post_type',
    function ( $use_block_editor, $post_type ) {
        if ( 'attachment' === $post_type ) {
            return false;
        }

        return $use_block_editor;
    },
    10,
    2
);

add_action(
    'init',
    function () {
        register_post_type(
            'artwork',
            array(
                'labels'       => array(
                    'name'          => __( 'Artworks', 'art-zone-blank' ),
                    'singular_name' => __( 'Artwork', 'art-zone-blank' ),
                ),
                'public'       => true,
                'has_archive'  => true,
                'menu_icon'    => 'dashicons-format-image',
                'rewrite'      => array( 'slug' => 'artwork' ),
                'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
                'show_in_rest' => true,
            )
        );

        register_post_type(
            'studio_item',
            array(
                'labels'       => array(
                    'name'          => __( 'Studio Items', 'art-zone-blank' ),
                    'singular_name' => __( 'Studio Item', 'art-zone-blank' ),
                ),
                'public'       => false,
                'show_ui'      => true,
                'show_in_menu' => true,
                'menu_icon'    => 'dashicons-format-gallery',
                'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
                'show_in_rest' => true,
            )
        );

        register_post_type(
            'artwork_interior',
            array(
                'labels'       => array(
                    'name'          => __( 'Artwork Interiors', 'art-zone-blank' ),
                    'singular_name' => __( 'Artwork Interior', 'art-zone-blank' ),
                ),
                'public'       => false,
                'show_ui'      => true,
                'show_in_menu' => true,
                'menu_icon'    => 'dashicons-format-gallery',
                'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
                'show_in_rest' => true,
            )
        );

        register_post_type(
            'art_therapy_item',
            array(
                'labels'       => array(
                    'name'          => __( 'Art Therapy Items', 'art-zone-blank' ),
                    'singular_name' => __( 'Art Therapy Item', 'art-zone-blank' ),
                ),
                'public'       => false,
                'show_ui'      => true,
                'show_in_menu' => true,
                'menu_icon'    => 'dashicons-heart',
                'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
                'show_in_rest' => true,
            )
        );

        register_post_type(
            'artwork_frame',
            array(
                'labels'       => array(
                    'name'          => __( 'Artwork Frames', 'art-zone-blank' ),
                    'singular_name' => __( 'Artwork Frame', 'art-zone-blank' ),
                ),
                'public'       => false,
                'show_ui'      => true,
                'show_in_menu' => true,
                'menu_icon'    => 'dashicons-screenoptions',
                'supports'     => array( 'title', 'page-attributes' ),
                'show_in_rest' => true,
            )
        );

        register_taxonomy(
            'artwork_type',
            'artwork',
            array(
                'labels'       => array(
                    'name'          => __( 'Art Types', 'art-zone-blank' ),
                    'singular_name' => __( 'Art Type', 'art-zone-blank' ),
                ),
                'public'       => true,
                'hierarchical' => true,
                'rewrite'      => array( 'slug' => 'artwork-type' ),
                'show_in_rest' => true,
            )
        );

        register_taxonomy(
            'artwork_category',
            'artwork',
            array(
                'labels'       => array(
                    'name'          => __( 'Genres', 'art-zone-blank' ),
                    'singular_name' => __( 'Genre', 'art-zone-blank' ),
                ),
                'public'       => true,
                'hierarchical' => true,
                'rewrite'      => array( 'slug' => 'artwork-category' ),
                'show_in_rest' => true,
            )
        );

        register_taxonomy(
            'artwork_material',
            'artwork',
            array(
                'labels'       => array(
                    'name'          => __( 'Materials', 'art-zone-blank' ),
                    'singular_name' => __( 'Material', 'art-zone-blank' ),
                ),
                'public'       => true,
                'hierarchical' => true,
                'rewrite'      => array( 'slug' => 'artwork-material' ),
                'show_in_rest' => true,
            )
        );

        register_taxonomy(
            'artwork_medium',
            'artwork',
            array(
                'labels'       => array(
                    'name'          => __( 'Mediums', 'art-zone-blank' ),
                    'singular_name' => __( 'Medium', 'art-zone-blank' ),
                ),
                'public'       => true,
                'hierarchical' => true,
                'rewrite'      => array( 'slug' => 'artwork-medium' ),
                'show_in_rest' => true,
            )
        );
    }
);
