<?php

function art_zone_blank_current_language_slug( $fallback = '' ) {
    if ( function_exists( 'pll_current_language' ) ) {
        $language = pll_current_language( 'slug' );

        if ( is_string( $language ) && '' !== $language ) {
            return $language;
        }
    }

    return $fallback;
}

function art_zone_blank_home_url( $language_slug = null ) {
    if ( function_exists( 'pll_home_url' ) ) {
        $language_slug = $language_slug ? $language_slug : art_zone_blank_current_language_slug();
        $url           = pll_home_url( $language_slug );

        if ( is_string( $url ) && '' !== $url ) {
            return trailingslashit( $url );
        }
    }

    return trailingslashit( home_url( '/' ) );
}

function art_zone_blank_translate_post_id( $post_id, $language_slug = null ) {
    $post_id = (int) $post_id;

    if ( ! $post_id ) {
        return 0;
    }

    if ( function_exists( 'pll_get_post' ) ) {
        $language_slug = $language_slug ? $language_slug : art_zone_blank_current_language_slug();
        $translated_id = pll_get_post( $post_id, $language_slug );

        if ( $translated_id ) {
            return (int) $translated_id;
        }
    }

    return $post_id;
}

function art_zone_blank_permalink_for_post( $post_id, $fallback = '#', $language_slug = null ) {
    $translated_id = art_zone_blank_translate_post_id( $post_id, $language_slug );

    if ( $translated_id ) {
        $permalink = get_permalink( $translated_id );

        if ( is_string( $permalink ) && '' !== $permalink ) {
            return $permalink;
        }
    }

    return $fallback;
}

function art_zone_blank_find_page_id_by_template( $template, $language_slug = null ) {
    $pages = get_posts(
        array(
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_key'       => '_wp_page_template',
            'meta_value'     => $template,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        )
    );

    if ( empty( $pages ) ) {
        return 0;
    }

    foreach ( $pages as $page_id ) {
        $translated_id = art_zone_blank_translate_post_id( $page_id, $language_slug );

        if ( $translated_id ) {
            return (int) $translated_id;
        }
    }

    return (int) reset( $pages );
}

function art_zone_blank_find_page_id_by_paths( $slugs, $language_slug = null ) {
    foreach ( (array) $slugs as $slug ) {
        $page = get_page_by_path( $slug );

        if ( $page instanceof WP_Post ) {
            $translated_id = art_zone_blank_translate_post_id( $page->ID, $language_slug );

            if ( $translated_id ) {
                return (int) $translated_id;
            }
        }
    }

    return 0;
}

function art_zone_blank_resolve_page_permalink( $paths, $template, $fallback = '#', $language_slug = null ) {
    $page_id = art_zone_blank_find_page_id_by_paths( $paths, $language_slug );

    if ( ! $page_id && $template ) {
        $page_id = art_zone_blank_find_page_id_by_template( $template, $language_slug );
    }

    if ( $page_id ) {
        return art_zone_blank_permalink_for_post( $page_id, $fallback, $language_slug );
    }

    return $fallback;
}

function art_zone_blank_contextual_language_url( $language_slug, $fallback = '#' ) {
    if ( is_front_page() || is_home() ) {
        return art_zone_blank_home_url( $language_slug );
    }

    if ( is_page_template( 'page-portfolio.php' ) ) {
        return art_zone_blank_portfolio_url( $fallback, $language_slug );
    }

    if ( is_page_template( 'page-about.php' ) ) {
        return art_zone_blank_about_url( $fallback, $language_slug );
    }

    if ( is_page_template( 'page-art-therapy.php' ) ) {
        return art_zone_blank_art_therapy_url( $fallback, $language_slug );
    }

    if ( is_page_template( 'page-contact.php' ) ) {
        return art_zone_blank_contact_url( $fallback, $language_slug );
    }

    if ( is_home() || ( is_page() && (int) get_option( 'page_for_posts' ) === (int) get_queried_object_id() ) ) {
        return art_zone_blank_blog_url( $fallback, $language_slug );
    }

    if ( is_singular() && function_exists( 'pll_get_post' ) ) {
        $post_id = get_queried_object_id();

        if ( $post_id ) {
            $translated_id = pll_get_post( $post_id, $language_slug );

            if ( $translated_id ) {
                return get_permalink( $translated_id );
            }
        }
    }

    return $fallback;
}

function art_zone_blank_language_switcher_items() {
    if ( ! function_exists( 'pll_the_languages' ) ) {
        return array();
    }

    $languages = pll_the_languages(
        array(
            'raw'                     => 1,
            'hide_if_empty'           => 0,
            'hide_if_no_translation'  => 0,
            'hide_current'            => 0,
        )
    );

    if ( empty( $languages ) || ! is_array( $languages ) ) {
        return array();
    }

    $labels = array(
        'en' => __( 'ENG', 'art-zone-blank' ),
        'hy' => __( 'ARM', 'art-zone-blank' ),
    );

    return array_values(
        array_filter(
            array_map(
                function ( $language ) use ( $labels ) {
                    if ( empty( $language['slug'] ) ) {
                        return null;
                    }

                    $slug = (string) $language['slug'];
                    $url  = ! empty( $language['url'] ) ? $language['url'] : '';

                    if ( '' === $url ) {
                        return null;
                    }

                    $context_url = art_zone_blank_contextual_language_url( $slug, $url );

                    return array(
                        'slug'    => $slug,
                        'label'   => isset( $labels[ $slug ] ) ? $labels[ $slug ] : strtoupper( $slug ),
                        'url'     => $context_url,
                        'current' => ! empty( $language['current_lang'] ),
                    );
                },
                $languages
            )
        )
    );
}

function art_zone_blank_page_link( $slug, $fallback = '#' ) {
    $page = get_page_by_path( $slug );

    if ( $page instanceof WP_Post ) {
        return get_permalink( $page );
    }

    return $fallback;
}

function art_zone_blank_portfolio_url( $fallback = '#', $language_slug = null ) {
    return art_zone_blank_resolve_page_permalink(
        array( 'portfolio', 'gallery', 'works' ),
        'page-portfolio.php',
        $fallback,
        $language_slug
    );
}

function art_zone_blank_about_url( $fallback = '#', $language_slug = null ) {
    return art_zone_blank_resolve_page_permalink(
        array( 'about', 'artist', 'about-the-artist' ),
        'page-about.php',
        $fallback,
        $language_slug
    );
}

function art_zone_blank_art_therapy_url( $fallback = '#', $language_slug = null ) {
    return art_zone_blank_resolve_page_permalink(
        array( 'art-therapy', 'therapy', 'commission', 'commissions' ),
        'page-art-therapy.php',
        $fallback,
        $language_slug
    );
}

function art_zone_blank_blog_url( $fallback = '#', $language_slug = null ) {
    $posts_page_id = (int) get_option( 'page_for_posts' );

    if ( $posts_page_id ) {
        return art_zone_blank_permalink_for_post( $posts_page_id, $fallback, $language_slug );
    }

    return art_zone_blank_resolve_page_permalink(
        array( 'blog', 'journal', 'news' ),
        '',
        $fallback,
        $language_slug
    );
}

function art_zone_blank_contact_url( $fallback = '#', $language_slug = null ) {
    return art_zone_blank_resolve_page_permalink(
        array( 'contact', 'contact-us', 'contacts' ),
        'page-contact.php',
        $fallback,
        $language_slug
    );
}

function art_zone_blank_menu_item_route_url( $item, $theme_location = '' ) {
    if ( ! $item instanceof WP_Post ) {
        return null;
    }

    $object_id     = isset( $item->object_id ) ? (int) $item->object_id : 0;
    $object        = isset( $item->object ) ? (string) $item->object : '';
    $type          = isset( $item->type ) ? (string) $item->type : '';
    $title_slug    = sanitize_title( $item->title );
    $language_slug = art_zone_blank_current_language_slug();

    if ( 'page' === $object && $object_id ) {
        $template = get_page_template_slug( $object_id );

        if ( 'page-portfolio.php' === $template ) {
            return art_zone_blank_portfolio_url( '#', $language_slug );
        }

        if ( 'page-about.php' === $template ) {
            return art_zone_blank_about_url( '#', $language_slug );
        }

        if ( 'page-art-therapy.php' === $template ) {
            return art_zone_blank_art_therapy_url( '#', $language_slug );
        }

        if ( 'page-contact.php' === $template ) {
            return art_zone_blank_contact_url( '#', $language_slug );
        }

        if ( $object_id === (int) get_option( 'page_for_posts' ) ) {
            return art_zone_blank_blog_url( '#', $language_slug );
        }
    }

    if ( in_array( $theme_location, array( 'primary', 'footer' ), true ) ) {
        if ( 'home' === $title_slug ) {
            return art_zone_blank_home_url( $language_slug );
        }

        if ( 'portfolio' === $title_slug ) {
            return art_zone_blank_portfolio_url( '#', $language_slug );
        }

        if ( in_array( $title_slug, array( 'studio', 'blog', 'journal' ), true ) ) {
            return art_zone_blank_blog_url( '#', $language_slug );
        }

        if ( 'about' === $title_slug ) {
            return art_zone_blank_about_url( '#', $language_slug );
        }

        if ( in_array( $title_slug, array( 'commission', 'art-therapy', 'art-therapy-page' ), true ) ) {
            return art_zone_blank_art_therapy_url( '#', $language_slug );
        }

        if ( in_array( $title_slug, array( 'contact', 'contact-us', 'contacts' ), true ) ) {
            return art_zone_blank_contact_url( '#', $language_slug );
        }
    }

    if ( 'custom' === $type && ! empty( $item->url ) ) {
        $parsed_path = wp_parse_url( $item->url, PHP_URL_PATH );

        if ( is_string( $parsed_path ) ) {
            $path_parts = array_values( array_filter( explode( '/', trim( $parsed_path, '/' ) ) ) );
            $route_slug = end( $path_parts );

            if ( 'portfolio' === $route_slug ) {
                return art_zone_blank_portfolio_url( '#', $language_slug );
            }

            if ( 'about' === $route_slug ) {
                return art_zone_blank_about_url( '#', $language_slug );
            }

            if ( 'art-therapy' === $route_slug ) {
                return art_zone_blank_art_therapy_url( '#', $language_slug );
            }

            if ( 'contact' === $route_slug ) {
                return art_zone_blank_contact_url( '#', $language_slug );
            }

            if ( 'blog' === $route_slug ) {
                return art_zone_blank_blog_url( '#', $language_slug );
            }
        }
    }

    return null;
}

add_filter(
    'nav_menu_css_class',
    function ( $classes, $item, $args ) {
        if ( ! is_singular( 'artwork' ) ) {
            return $classes;
        }

        if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
            return $classes;
        }

        $route_url  = art_zone_blank_menu_item_route_url( $item, 'primary' );
        $portfolio_url = art_zone_blank_portfolio_url();

        if ( is_string( $route_url ) && is_string( $portfolio_url ) && rtrim( $route_url, '/' ) === rtrim( $portfolio_url, '/' ) ) {
            $classes[] = 'current-menu-item';
        }

        return $classes;
    },
    10,
    3
);

add_filter(
    'wp_nav_menu_objects',
    function ( $items, $args ) {
        if ( empty( $args->theme_location ) || ! in_array( $args->theme_location, array( 'primary', 'footer' ), true ) ) {
            return $items;
        }

        foreach ( $items as $item ) {
            $route_url = art_zone_blank_menu_item_route_url( $item, $args->theme_location );

            if ( is_string( $route_url ) && '' !== $route_url && '#' !== $route_url ) {
                $item->url = $route_url;

                $title_slug = sanitize_title( $item->title );

                if ( in_array( $title_slug, array( 'studio', 'blog', 'journal' ), true ) ) {
                    $item->title = __( 'Blog', 'art-zone-blank' );
                }

                if ( in_array( $title_slug, array( 'commission', 'art-therapy', 'art-therapy-page' ), true ) ) {
                    $item->title = __( 'Art Therapy', 'art-zone-blank' );
                }

                if ( in_array( $title_slug, array( 'contact-us', 'contacts' ), true ) ) {
                    $item->title = __( 'Contact', 'art-zone-blank' );
                }
            }
        }

        return $items;
    },
    10,
    2
);
