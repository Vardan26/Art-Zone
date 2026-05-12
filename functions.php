<?php
add_action( 'after_setup_theme', function () {
    load_theme_textdomain( 'art-zone-blank', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    // Hero & full-bleed backgrounds
    add_image_size( 'az-hero',      1920, 1080, true );
    add_image_size( 'az-hero-tall', 1080, 1440, true );
    // Artwork display
    add_image_size( 'az-artwork-detail', 1400, 1400, false );
    add_image_size( 'az-lightbox',       1800, 1800, false );
    // Grid cards (4:5 ratio pair — WordPress auto-builds srcset between them)
    add_image_size( 'az-card-lg', 800, 1000, false );
    add_image_size( 'az-card-sm', 480,  600, false );
    // Collection & related works
    add_image_size( 'az-collection', 640, 800, false );
    // Editorial content images (max-width only, proportional height)
    add_image_size( 'az-editorial', 1200, 0, false );
    // Interior mockup scene background
    add_image_size( 'az-interior-bg', 1400, 900, false );
    // Shared thumbnail for interior selector + frame choice buttons
    add_image_size( 'az-thumb', 160, 160, false );
    register_nav_menus(
        array(
            'primary' => __( 'Primary Menu', 'art-zone-blank' ),
            'footer'  => __( 'Footer Menu', 'art-zone-blank' ),
        )
    );
} );

require_once get_stylesheet_directory() . '/inc/icons.php';
require_once get_stylesheet_directory() . '/inc/theme-mods.php';
require_once get_stylesheet_directory() . '/inc/artwork-data.php';
require_once get_stylesheet_directory() . '/inc/artwork-template-matcher.php';
require_once get_stylesheet_directory() . '/inc/artwork-interiors.php';
require_once get_stylesheet_directory() . '/inc/artwork-frames.php';
require_once get_stylesheet_directory() . '/inc/navigation.php';
require_once get_stylesheet_directory() . '/inc/assets.php';
require_once get_stylesheet_directory() . '/inc/content-types.php';
require_once get_stylesheet_directory() . '/inc/customizer.php';
require_once get_stylesheet_directory() . '/inc/seed.php';
require_once get_stylesheet_directory() . '/inc/admin-metaboxes.php';
