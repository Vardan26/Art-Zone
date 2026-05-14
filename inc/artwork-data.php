<?php

function art_zone_blank_query_all_languages( $args ) {
    if ( function_exists( 'pll_current_language' ) && is_array( $args ) && ! array_key_exists( 'lang', $args ) ) {
        $args['lang'] = '';
    }

    return $args;
}

function art_zone_blank_select_translated_posts( $posts ) {
    if ( ! function_exists( 'pll_current_language' ) || ! function_exists( 'pll_get_post_translations' ) ) {
        return $posts;
    }

    $current_language = pll_current_language( 'slug' );
    $default_language = function_exists( 'pll_default_language' ) ? pll_default_language( 'slug' ) : '';
    $groups           = array();
    $order            = array();

    foreach ( (array) $posts as $post ) {
        if ( ! $post instanceof WP_Post ) {
            continue;
        }

        $translations = pll_get_post_translations( $post->ID );
        $group_ids    = array();

        if ( is_array( $translations ) && ! empty( $translations ) ) {
            $group_ids = array_map( 'intval', array_values( $translations ) );
            sort( $group_ids );
        }

        $group_key = ! empty( $group_ids ) ? implode( ':', $group_ids ) : 'post:' . $post->ID;

        if ( ! isset( $groups[ $group_key ] ) ) {
            $groups[ $group_key ] = array(
                'first'        => $post,
                'translations' => array(),
            );
            $order[] = $group_key;
        }

        $language = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $post->ID, 'slug' ) : '';

        if ( is_string( $language ) && '' !== $language ) {
            $groups[ $group_key ]['translations'][ $language ] = $post;
        }
    }

    $selected_posts = array();

    foreach ( $order as $group_key ) {
        $group = $groups[ $group_key ];

        if ( $current_language && ! empty( $group['translations'][ $current_language ] ) ) {
            $selected_posts[] = $group['translations'][ $current_language ];
            continue;
        }

        if ( $default_language && ! empty( $group['translations'][ $default_language ] ) ) {
            $selected_posts[] = $group['translations'][ $default_language ];
            continue;
        }

        $selected_posts[] = $group['first'];
    }

    return $selected_posts;
}

function art_zone_blank_media_ids_to_urls( $value, $size = 'full' ) {
    $value = is_scalar( $value ) ? (string) $value : '';
    $parts = array_filter( array_map( 'trim', explode( ',', $value ) ) );
    $urls  = array();

    foreach ( $parts as $part ) {
        if ( ctype_digit( $part ) ) {
            $attachment_url = 'full' === $size
                ? wp_get_attachment_url( (int) $part )
                : wp_get_attachment_image_url( (int) $part, $size );

            if ( $attachment_url ) {
                $urls[] = $attachment_url;
            }

            continue;
        }

        if ( preg_match( '#^https?://#', $part ) ) {
            $urls[] = $part;
        }
    }

    return array_values( array_unique( $urls ) );
}

function art_zone_blank_get_attachment_image_url_with_fallback( $attachment_id, $sizes, $fallback = 'full' ) {
    $attachment_id = (int) $attachment_id;

    if ( $attachment_id <= 0 ) {
        return '';
    }

    foreach ( (array) $sizes as $size ) {
        $url = wp_get_attachment_image_url( $attachment_id, $size );

        if ( $url ) {
            return $url;
        }
    }

    return wp_get_attachment_image_url( $attachment_id, $fallback ) ?: '';
}

function art_zone_blank_artwork_type_labels() {
    return array(
        'painting'    => __( 'Painting', 'art-zone-blank' ),
        'sculpture'   => __( 'Sculpture', 'art-zone-blank' ),
        'drawing'     => __( 'Drawing', 'art-zone-blank' ),
        'photography' => __( 'Photography', 'art-zone-blank' ),
        'printmaking' => __( 'Printmaking', 'art-zone-blank' ),
        'carpet'      => __( 'Carpet', 'art-zone-blank' ),
        'digital-art' => __( 'Digital Art', 'art-zone-blank' ),
        'mixed-media' => __( 'Mixed Media', 'art-zone-blank' ),
        'ceramics'    => __( 'Ceramics', 'art-zone-blank' ),
        'installation' => __( 'Installation', 'art-zone-blank' ),
    );
}

function art_zone_blank_artwork_category_labels() {
    return array(
        'abstract'     => __( 'Abstract', 'art-zone-blank' ),
        'portrait'     => __( 'Portrait', 'art-zone-blank' ),
        'landscape'    => __( 'Landscape', 'art-zone-blank' ),
        'still-life'   => __( 'Still Life', 'art-zone-blank' ),
        'figurative'   => __( 'Figurative', 'art-zone-blank' ),
        'narrative'    => __( 'Narrative', 'art-zone-blank' ),
        'decorative'   => __( 'Decorative', 'art-zone-blank' ),
        'urban-cityscape' => __( 'Urban / Cityscape', 'art-zone-blank' ),
        'nature'       => __( 'Nature', 'art-zone-blank' ),
    );
}

function art_zone_blank_artwork_material_labels() {
    return array(
        'canvas'    => __( 'Canvas', 'art-zone-blank' ),
        'paper'     => __( 'Paper', 'art-zone-blank' ),
        'wood'      => __( 'Wood', 'art-zone-blank' ),
        'metal'     => __( 'Metal', 'art-zone-blank' ),
        'stone'     => __( 'Stone', 'art-zone-blank' ),
        'clay'      => __( 'Clay', 'art-zone-blank' ),
        'glass'     => __( 'Glass', 'art-zone-blank' ),
        'fabric'    => __( 'Fabric', 'art-zone-blank' ),
        'plastic'   => __( 'Plastic', 'art-zone-blank' ),
        'digital-non-physical' => __( 'Digital (non-physical)', 'art-zone-blank' ),
    );
}

function art_zone_blank_artwork_medium_labels() {
    return array(
        'oil'         => __( 'Oil', 'art-zone-blank' ),
        'acrylic'     => __( 'Acrylic', 'art-zone-blank' ),
        'watercolor'  => __( 'Watercolor', 'art-zone-blank' ),
        'ink'         => __( 'Ink', 'art-zone-blank' ),
        'charcoal'    => __( 'Charcoal', 'art-zone-blank' ),
        'pastel'      => __( 'Pastel', 'art-zone-blank' ),
        'digital'     => __( 'Digital', 'art-zone-blank' ),
        'mixed-media' => __( 'Mixed Media', 'art-zone-blank' ),
        'textile-weaving-embroidery' => __( 'Textile (weaving / embroidery)', 'art-zone-blank' ),
        'ceramic-glaze-fired' => __( 'Ceramic (glaze / fired)', 'art-zone-blank' ),
    );
}

function art_zone_blank_parse_medium_values( $value ) {
    $value = trim( (string) $value );

    if ( '' === $value ) {
        return array();
    }

    return array_values(
        array_filter(
            array_map(
                'trim',
                preg_split( '/[,\/|]+/', $value )
            )
        )
    );
}

function art_zone_blank_unique_labels( $values ) {
    $labels = array();

    foreach ( $values as $value ) {
        $label = trim( (string) $value );

        if ( '' === $label ) {
            continue;
        }

        $labels[ sanitize_title( $label ) ] = $label;
    }

    return array_values( $labels );
}

function art_zone_blank_artwork_display_medium( $material_names, $medium_string ) {
    $parts = array_filter(
        array(
            ! empty( $material_names ) ? implode( ', ', (array) $material_names ) : '',
            ! empty( $medium_string ) ? (string) $medium_string : '',
        )
    );
    return implode( ' / ', array_values( $parts ) );
}

function art_zone_blank_artwork_type_aliases() {
    return array(
        'painting' => 'painting',
        'sculpture' => 'sculpture',
        'drawing' => 'drawing',
        'photography' => 'photography',
        'photo' => 'photography',
        'printmaking' => 'printmaking',
        'print' => 'printmaking',
        'carpet' => 'carpet',
        'textile' => 'carpet',
        'digital' => 'digital-art',
        'digital-art' => 'digital-art',
        'mixed-media' => 'mixed-media',
        'mixedmedia' => 'mixed-media',
        'ceramic' => 'ceramics',
        'ceramics' => 'ceramics',
        'installation' => 'installation',
    );
}

function art_zone_blank_artwork_category_aliases() {
    return array(
        'abstract' => 'abstract',
        'portrait' => 'portrait',
        'landscape' => 'landscape',
        'still-life' => 'still-life',
        'stilllife' => 'still-life',
        'figure' => 'figurative',
        'figurative' => 'figurative',
        'narrative' => 'narrative',
        'decorative' => 'decorative',
        'photo' => 'photography',
        'sculpture' => 'sculpture',
        'drawing' => 'drawing',
        'digital' => 'digital-art',
        'carpet' => 'carpet',
        'mixed-media' => 'mixed-media',
        'ceramic' => 'ceramics',
        'installation' => 'installation',
        'architecture' => 'urban-cityscape',
        'cityscape' => 'urban-cityscape',
        'urban' => 'urban-cityscape',
        'urban-cityscape' => 'urban-cityscape',
        'floral' => 'nature',
        'nature' => 'nature',
        'relief' => 'decorative',
        'bas-relief' => 'decorative',
        'barelef' => 'decorative',
    );
}

function art_zone_blank_artwork_material_aliases() {
    return array(
        'canvas' => 'canvas',
        'paper' => 'paper',
        'wood' => 'wood',
        'board' => 'wood',
        'metal' => 'metal',
        'stone' => 'stone',
        'clay' => 'clay',
        'glass' => 'glass',
        'fabric' => 'fabric',
        'linen' => 'fabric',
        'carpet' => 'fabric',
        'plastic' => 'plastic',
        'cardboard' => 'paper',
        'digital' => 'digital-non-physical',
        'digital-non-physical' => 'digital-non-physical',
    );
}

function art_zone_blank_artwork_medium_aliases() {
    return array(
        'oil' => 'oil',
        'acryl' => 'acrylic',
        'acrylic' => 'acrylic',
        'watercolor' => 'watercolor',
        'gouache' => 'watercolor',
        'ink' => 'ink',
        'charcoal' => 'charcoal',
        'pastel' => 'pastel',
        'digital' => 'digital',
        'mixed-media' => 'mixed-media',
        'mixedmedia' => 'mixed-media',
        'collage' => 'mixed-media',
        'collages' => 'mixed-media',
        'textile' => 'textile-weaving-embroidery',
        'textile-weaving-embroidery' => 'textile-weaving-embroidery',
        'ceramic' => 'ceramic-glaze-fired',
        'ceramic-glaze-fired' => 'ceramic-glaze-fired',
    );
}

function art_zone_blank_normalize_artwork_classification( $types, $categories, $materials, $mediums = '' ) {
    $type_labels     = art_zone_blank_artwork_type_labels();
    $category_labels = art_zone_blank_artwork_category_labels();
    $material_labels = art_zone_blank_artwork_material_labels();
    $medium_labels   = art_zone_blank_artwork_medium_labels();
    $type_aliases    = art_zone_blank_artwork_type_aliases();
    $category_aliases = art_zone_blank_artwork_category_aliases();
    $material_aliases = art_zone_blank_artwork_material_aliases();
    $medium_aliases  = art_zone_blank_artwork_medium_aliases();
    $normalized      = array(
        'types'      => array(),
        'categories' => array(),
        'materials'  => array(),
        'mediums'    => array(),
    );

    $assign_term = function ( $slug, $source ) use ( &$normalized, $type_labels, $category_labels, $material_labels, $medium_labels, $type_aliases, $category_aliases, $material_aliases, $medium_aliases ) {
        if ( 'type' === $source && isset( $type_aliases[ $slug ] ) ) {
            $slug = $type_aliases[ $slug ];
        } elseif ( 'category' === $source && isset( $category_aliases[ $slug ] ) ) {
            $slug = $category_aliases[ $slug ];
        } elseif ( 'material' === $source && isset( $material_aliases[ $slug ] ) ) {
            $slug = $material_aliases[ $slug ];
        } elseif ( 'medium' === $source && isset( $medium_aliases[ $slug ] ) ) {
            $slug = $medium_aliases[ $slug ];
        }

        if ( isset( $type_labels[ $slug ] ) ) {
            $normalized['types'][ $slug ] = $type_labels[ $slug ];
        }

        if ( isset( $category_labels[ $slug ] ) ) {
            $normalized['categories'][ $slug ] = $category_labels[ $slug ];
        }

        if ( isset( $material_labels[ $slug ] ) ) {
            $normalized['materials'][ $slug ] = $material_labels[ $slug ];
        }

        if ( isset( $medium_labels[ $slug ] ) ) {
            $normalized['mediums'][ $slug ] = $medium_labels[ $slug ];
        }
    };

    foreach ( (array) $types as $type ) {
        $slug = sanitize_title( is_object( $type ) ? $type->slug : $type );

        if ( $slug ) {
            $assign_term( $slug, 'type' );
        }
    }

    foreach ( (array) $categories as $category ) {
        $slug = sanitize_title( is_object( $category ) ? $category->slug : $category );

        if ( $slug ) {
            $assign_term( $slug, 'category' );
        }
    }

    foreach ( (array) $materials as $material ) {
        $slug = sanitize_title( is_object( $material ) ? $material->slug : $material );

        if ( $slug ) {
            $assign_term( $slug, 'material' );
        }
    }

    foreach ( art_zone_blank_parse_medium_values( is_array( $mediums ) ? implode( ', ', $mediums ) : $mediums ) as $medium ) {
        $slug = sanitize_title( $medium );

        if ( $slug ) {
            $assign_term( $slug, 'medium' );
        }
    }

    return $normalized;
}

function art_zone_blank_get_artwork_term_names( $post_id, $taxonomy ) {
    $terms = get_the_terms( $post_id, $taxonomy );

    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return array();
    }

    return array_values( wp_list_pluck( $terms, 'name' ) );
}

function art_zone_blank_get_artwork_term_slugs( $post_id, $taxonomy ) {
    $terms = get_the_terms( $post_id, $taxonomy );

    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return array();
    }

    return array_values( wp_list_pluck( $terms, 'slug' ) );
}

function art_zone_blank_artwork_uses_drop_shadow( $post_id ) {
    return 'not_applicable' === get_post_meta( $post_id, 'artwork_framing_status', true );
}

function art_zone_blank_get_artwork_image_id( $post_id ) {
    $media_image_id = (int) get_post_meta( $post_id, 'artwork_image_id', true );

    if ( $media_image_id && wp_attachment_is_image( $media_image_id ) ) {
        return $media_image_id;
    }

    $featured_id = (int) get_post_thumbnail_id( $post_id );

    return $featured_id > 0 ? $featured_id : 0;
}

function art_zone_blank_get_artwork_image( $post_id, $size = 'az-artwork-detail' ) {
    $media_image_id = (int) get_post_meta( $post_id, 'artwork_image_id', true );

    if ( $media_image_id ) {
        $media_image = wp_get_attachment_image_url( $media_image_id, $size );

        if ( $media_image ) {
            return $media_image;
        }
    }

    $featured_image = get_the_post_thumbnail_url( $post_id, $size );

    if ( $featured_image ) {
        return $featured_image;
    }

    $image = get_post_meta( $post_id, 'artwork_external_image', true );

    if ( ! $image ) {
        return '';
    }

    $image = trim( (string) $image );

    if ( preg_match( '#^https?://#', $image ) ) {
        return $image;
    }

    // Relative path — resolve against the theme directory.
    return get_template_directory_uri() . '/' . ltrim( $image, '/' );
}

function art_zone_blank_get_artwork_media_dimensions( $post_id ) {
    $media_image_id = (int) get_post_meta( $post_id, 'artwork_image_id', true );

    if ( $media_image_id ) {
        $meta = wp_get_attachment_metadata( $media_image_id );

        if ( is_array( $meta ) && ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
            return array(
                'width'  => (int) $meta['width'],
                'height' => (int) $meta['height'],
            );
        }
    }

    $featured_id = get_post_thumbnail_id( $post_id );

    if ( $featured_id ) {
        $meta = wp_get_attachment_metadata( $featured_id );

        if ( is_array( $meta ) && ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
            return array(
                'width'  => (int) $meta['width'],
                'height' => (int) $meta['height'],
            );
        }
    }

    return array(
        'width'  => 0,
        'height' => 0,
    );
}

function art_zone_blank_parse_artwork_real_dimensions( $dimensions_string ) {
    if ( ! $dimensions_string ) {
        return array( 'width' => 0, 'height' => 0 );
    }

    if ( preg_match( '/(\d+(?:[.,]\d+)?)\s*[x×X]\s*(\d+(?:[.,]\d+)?)\s*(cm|mm|in|inch|m)?/i', $dimensions_string, $m ) ) {
        $w    = (float) str_replace( ',', '.', $m[1] );
        $h    = (float) str_replace( ',', '.', $m[2] );
        $unit = strtolower( isset( $m[3] ) ? $m[3] : 'cm' );

        if ( 'mm' === $unit ) {
            $w /= 10;
            $h /= 10;
        } elseif ( 'in' === $unit || 'inch' === $unit ) {
            $w *= 2.54;
            $h *= 2.54;
        } elseif ( 'm' === $unit ) {
            $w *= 100;
            $h *= 100;
        }

        return array( 'width' => round( $w, 1 ), 'height' => round( $h, 1 ) );
    }

    return array( 'width' => 0, 'height' => 0 );
}

function art_zone_blank_get_artwork_grid_size( $post_id ) {
    // 1. Use actual pixel dimensions from the attached Media Library image.
    $dimensions = art_zone_blank_get_artwork_media_dimensions( $post_id );
    $width      = (int) $dimensions['width'];
    $height     = (int) $dimensions['height'];

    if ( $width > 0 && $height > 0 ) {
        $aspect_ratio = $width / $height;

        if ( $aspect_ratio > 1.5 ) {
            return 'wide';
        }

        if ( $aspect_ratio < 0.67 ) {
            return 'tall';
        }

        if ( $width > 3500 && $height > 3500 ) {
            return 'big';
        }

        return 'small';
    }

    // 2. Fall back to the real physical dimensions stored by the importer.
    //    These reflect the actual artwork proportions even before an image is attached.
    $real_width  = (float) get_post_meta( $post_id, 'artwork_width_cm', true );
    $real_height = (float) get_post_meta( $post_id, 'artwork_height_cm', true );

    if ( $real_width > 0 && $real_height > 0 ) {
        $aspect_ratio = $real_width / $real_height;

        if ( $aspect_ratio > 1.5 ) {
            return 'wide';
        }

        if ( $aspect_ratio < 0.67 ) {
            return 'tall';
        }

        return 'small';
    }

    // 3. Last resort: map the legacy gallery_size meta set by old seeding.
    $legacy_size = get_post_meta( $post_id, 'artwork_gallery_size', true );
    $legacy_map  = array(
        'feature' => 'wide',
        'side'    => 'tall',
        'offset'  => 'tall',
        'tall'    => 'tall',
        'small'   => 'small',
        'wide'    => 'wide',
        'big'     => 'big',
    );

    return isset( $legacy_map[ $legacy_size ] ) ? $legacy_map[ $legacy_size ] : 'small';
}

function art_zone_blank_get_artwork_items( $limit = -1 ) {
    // Over-fetch from DB so the image filter below doesn't exhaust a tight limit.
    $db_limit = $limit > -1 ? -1 : $limit;

    $posts = get_posts(
        art_zone_blank_query_all_languages(
            array(
                'post_type'      => 'artwork',
                'post_status'    => 'publish',
                'posts_per_page' => $db_limit,
                'orderby'        => array(
                    'menu_order' => 'ASC',
                    'date'       => 'DESC',
                ),
            )
        )
    );

    $posts = art_zone_blank_select_translated_posts( $posts );

    $items = array_map(
        function ( $post ) {
            $types        = get_the_terms( $post, 'artwork_type' );
            $categories   = get_the_terms( $post, 'artwork_category' );
            $materials    = get_the_terms( $post, 'artwork_material' );
            $mediums      = get_the_terms( $post, 'artwork_medium' );
            $medium_names = is_array( $mediums ) ? wp_list_pluck( $mediums, 'name' ) : array();
            $width_cm     = (float) get_post_meta( $post->ID, 'artwork_width_cm', true );
            $height_cm    = (float) get_post_meta( $post->ID, 'artwork_height_cm', true );
            return array(
                'ID'             => $post->ID,
                'title'          => get_the_title( $post ),
                'types'          => is_array( $types ) ? wp_list_pluck( $types, 'name' ) : array(),
                'type_slugs'     => is_array( $types ) ? wp_list_pluck( $types, 'slug' ) : array(),
                'medium'         => implode( ', ', $medium_names ),
                'mediums'        => $medium_names,
                'medium_slugs'   => is_array( $mediums ) ? wp_list_pluck( $mediums, 'slug' ) : array(),
                'width_cm'       => $width_cm,
                'height_cm'      => $height_cm,
                'dimensions'     => ( $width_cm > 0 && $height_cm > 0 ) ? $width_cm . ' × ' . $height_cm . ' cm' : '',
                'year'           => get_post_meta( $post->ID, 'artwork_year', true ),
                'size'           => art_zone_blank_get_artwork_grid_size( $post->ID ),
                'permalink'      => get_permalink( $post ),
                'image'          => art_zone_blank_get_artwork_image( $post->ID, 'az-card-lg' ),
                'image_id'       => art_zone_blank_get_artwork_image_id( $post->ID ),
                'image_lightbox' => art_zone_blank_get_artwork_image( $post->ID, 'az-lightbox' ),
                'categories'     => is_array( $categories ) ? wp_list_pluck( $categories, 'name' ) : array(),
                'category_slugs' => is_array( $categories ) ? wp_list_pluck( $categories, 'slug' ) : array(),
                'materials'      => is_array( $materials ) ? wp_list_pluck( $materials, 'name' ) : array(),
                'material_slugs' => is_array( $materials ) ? wp_list_pluck( $materials, 'slug' ) : array(),
            );
        },
        $posts
    );

    $items = array_values(
        array_filter(
            $items,
            function ( $item ) {
                return ! empty( $item['image'] );
            }
        )
    );

    if ( $limit > -1 ) {
        $items = array_slice( $items, 0, $limit );
    }

    return $items;
}

function art_zone_blank_featured_works() {
    return art_zone_blank_get_artwork_items( 6 );
}

function art_zone_blank_gallery_items() {
    return art_zone_blank_get_artwork_items();
}

function art_zone_blank_get_studio_item_image( $post_id ) {
    $media_image_id = (int) get_post_meta( $post_id, 'studio_item_image_id', true );

    if ( $media_image_id ) {
        $media_image = wp_get_attachment_image_url( $media_image_id, 'az-editorial' );

        if ( $media_image ) {
            return $media_image;
        }
    }

    $gallery_images = art_zone_blank_get_studio_item_gallery_images( $post_id );

    if ( ! empty( $gallery_images ) ) {
        return $gallery_images[0];
    }

    $featured_image = get_the_post_thumbnail_url( $post_id, 'az-editorial' );

    if ( $featured_image ) {
        return $featured_image;
    }

    return '';
}

function art_zone_blank_get_studio_item_gallery_images( $post_id ) {
    $gallery_images = art_zone_blank_media_ids_to_urls( get_post_meta( $post_id, 'studio_item_gallery_ids', true ), 'az-editorial' );

    if ( ! empty( $gallery_images ) ) {
        return array_slice( array_values( array_unique( $gallery_images ) ), 0, 3 );
    }

    $single_image = art_zone_blank_get_studio_item_image_without_gallery( $post_id );

    return $single_image ? array( $single_image ) : array();
}

function art_zone_blank_get_studio_item_image_without_gallery( $post_id ) {
    $media_image_id = (int) get_post_meta( $post_id, 'studio_item_image_id', true );

    if ( $media_image_id ) {
        $media_image = wp_get_attachment_image_url( $media_image_id, 'az-editorial' );

        if ( $media_image ) {
            return $media_image;
        }
    }

    $featured_image = get_the_post_thumbnail_url( $post_id, 'az-editorial' );

    return $featured_image ? $featured_image : '';
}

function art_zone_blank_get_studio_items() {
    $posts = get_posts(
        art_zone_blank_query_all_languages(
            array(
                'post_type'      => 'studio_item',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => array(
                    'menu_order' => 'ASC',
                    'date'       => 'DESC',
                ),
            )
        )
    );

    $posts = art_zone_blank_select_translated_posts( $posts );

    return array_values(
        array_filter(
            array_map(
                function ( $post ) {
                    $image = art_zone_blank_get_studio_item_image( $post->ID );

                    if ( empty( $image ) ) {
                        return null;
                    }

                    return array(
                        'ID'         => $post->ID,
                        'title'      => get_the_title( $post ),
                        'eyebrow'    => get_post_meta( $post->ID, 'studio_item_layout', true ),
                        'subheading' => get_the_excerpt( $post ),
                        'content'    => apply_filters( 'the_content', $post->post_content ),
                        'image'      => $image,
                        'images'     => art_zone_blank_get_studio_item_gallery_images( $post->ID ),
                        'layout'     => get_post_meta( $post->ID, 'studio_item_layout', true ) ?: 'split',
                    );
                },
                $posts
            )
        )
    );
}

function art_zone_blank_get_art_therapy_item_image( $post_id ) {
    $media_image_id = (int) get_post_meta( $post_id, 'art_therapy_item_image_id', true );

    if ( $media_image_id ) {
        $media_image = wp_get_attachment_image_url( $media_image_id, 'az-editorial' );

        if ( $media_image ) {
            return $media_image;
        }
    }

    $featured_image = get_the_post_thumbnail_url( $post_id, 'az-editorial' );

    if ( $featured_image ) {
        return $featured_image;
    }

    return '';
}

function art_zone_blank_get_art_therapy_items() {
    $posts = get_posts(
        art_zone_blank_query_all_languages(
            array(
                'post_type'      => 'art_therapy_item',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => array(
                    'menu_order' => 'ASC',
                    'date'       => 'DESC',
                ),
            )
        )
    );

    $posts = art_zone_blank_select_translated_posts( $posts );

    return array_values(
        array_filter(
            array_map(
                function ( $post ) {
                    $image = art_zone_blank_get_art_therapy_item_image( $post->ID );

                    return array(
                        'ID'         => $post->ID,
                        'title'      => get_the_title( $post ),
                        'subheading' => get_the_excerpt( $post ),
                        'content'    => apply_filters( 'the_content', $post->post_content ),
                        'image'      => $image,
                        'layout'     => get_post_meta( $post->ID, 'art_therapy_item_layout', true ) ?: 'split',
                    );
                },
                $posts
            )
        )
    );
}
