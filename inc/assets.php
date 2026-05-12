<?php

function art_zone_blank_asset_version( $relative_path ) {
	$path = get_stylesheet_directory() . $relative_path;

	if ( file_exists( $path ) ) {
		return filemtime( $path );
	}

	return wp_get_theme()->get( 'Version' );
}

function art_zone_blank_enqueue_theme_script( $handle, $relative_path, $deps = array(), $in_footer = true ) {
	wp_enqueue_script(
		$handle,
		get_stylesheet_directory_uri() . $relative_path,
		$deps,
		art_zone_blank_asset_version( $relative_path ),
		$in_footer
	);
}

function art_zone_blank_is_current_page_template( $template ) {
	if ( is_page_template( $template ) ) {
		return true;
	}

	$current_template = basename( (string) get_page_template() );

	return $template === $current_template;
}

add_action(
	'wp_head',
	function () {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	},
	1
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$style_version = art_zone_blank_asset_version( '/assets/css/main.css' );

		wp_enqueue_style(
			'art-zone-blank-fonts',
			'https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,300;0,400;0,700;1,400&family=Noto+Serif+Armenian:wght@300;400;500;600;700&family=Manrope:wght@300;400;500;700&family=Noto+Sans+Armenian:wght@300;400;500;700&display=swap',
			array(),
			null
		);
		wp_enqueue_style(
			'art-zone-blank-style',
			get_stylesheet_directory_uri() . '/assets/css/main.css',
			array( 'art-zone-blank-fonts' ),
			$style_version
		);

		art_zone_blank_enqueue_theme_script( 'art-zone-blank-site', '/assets/js/site.js' );
		art_zone_blank_enqueue_theme_script( 'art-zone-blank-media-slider', '/assets/js/media-slider.js' );

		if ( is_front_page() ) {
			wp_enqueue_style(
				'art-zone-blank-home',
				get_stylesheet_directory_uri() . '/assets/css/home.css',
				array( 'art-zone-blank-style' ),
				art_zone_blank_asset_version( '/assets/css/home.css' )
			);
			art_zone_blank_enqueue_theme_script( 'art-zone-blank-home', '/assets/js/home.js' );
		}

		if ( is_singular( 'artwork' ) ) {
			wp_enqueue_style(
				'art-zone-blank-home',
				get_stylesheet_directory_uri() . '/assets/css/home.css',
				array( 'art-zone-blank-style' ),
				art_zone_blank_asset_version( '/assets/css/home.css' )
			);
			wp_enqueue_style(
				'art-zone-blank-artwork',
				get_stylesheet_directory_uri() . '/assets/css/artwork.css',
				array( 'art-zone-blank-style' ),
				art_zone_blank_asset_version( '/assets/css/artwork.css' )
			);
			wp_enqueue_style(
				'art-zone-blank-artwork-interiors',
				get_stylesheet_directory_uri() . '/assets/css/artwork-interiors.css',
				array( 'art-zone-blank-style' ),
				art_zone_blank_asset_version( '/assets/css/artwork-interiors.css' )
			);
			art_zone_blank_enqueue_theme_script( 'art-zone-blank-artwork-interiors', '/assets/js/artwork-interiors.js' );
			wp_enqueue_style(
				'art-zone-blank-artwork-frames',
				get_stylesheet_directory_uri() . '/assets/css/artwork-frames.css',
				array( 'art-zone-blank-style' ),
				art_zone_blank_asset_version( '/assets/css/artwork-frames.css' )
			);
			art_zone_blank_enqueue_theme_script( 'art-zone-blank-artwork-frames', '/assets/js/artwork-frames.js' );
		}

		if ( art_zone_blank_is_current_page_template( 'page-about.php' ) ) {
			wp_enqueue_style(
				'art-zone-blank-editorial',
				get_stylesheet_directory_uri() . '/assets/css/editorial.css',
				array( 'art-zone-blank-style' ),
				art_zone_blank_asset_version( '/assets/css/editorial.css' )
			);
			wp_enqueue_style(
				'art-zone-blank-about',
				get_stylesheet_directory_uri() . '/assets/css/about.css',
				array( 'art-zone-blank-editorial' ),
				art_zone_blank_asset_version( '/assets/css/about.css' )
			);
			art_zone_blank_enqueue_theme_script( 'art-zone-blank-about', '/assets/js/about.js' );
		}

		if ( art_zone_blank_is_current_page_template( 'page-studio.php' ) ) {
			wp_enqueue_style(
				'art-zone-blank-editorial',
				get_stylesheet_directory_uri() . '/assets/css/editorial.css',
				array( 'art-zone-blank-style' ),
				art_zone_blank_asset_version( '/assets/css/editorial.css' )
			);
		}

		if ( art_zone_blank_is_current_page_template( 'page-art-therapy.php' ) ) {
			wp_enqueue_style(
				'art-zone-blank-editorial',
				get_stylesheet_directory_uri() . '/assets/css/editorial.css',
				array( 'art-zone-blank-style' ),
				art_zone_blank_asset_version( '/assets/css/editorial.css' )
			);
			wp_enqueue_style(
				'art-zone-blank-art-therapy',
				get_stylesheet_directory_uri() . '/assets/css/art-therapy.css',
				array( 'art-zone-blank-editorial' ),
				art_zone_blank_asset_version( '/assets/css/art-therapy.css' )
			);
			art_zone_blank_enqueue_theme_script( 'art-zone-blank-art-therapy', '/assets/js/art-therapy.js' );
		}

		if ( art_zone_blank_is_current_page_template( 'page-portfolio.php' ) ) {
			wp_enqueue_style(
				'art-zone-blank-portfolio',
				get_stylesheet_directory_uri() . '/assets/css/portfolio.css',
				array( 'art-zone-blank-style' ),
				art_zone_blank_asset_version( '/assets/css/portfolio.css' )
			);
			art_zone_blank_enqueue_theme_script( 'art-zone-blank-portfolio', '/assets/js/portfolio.js' );
		}

		if ( art_zone_blank_is_current_page_template( 'page-contact.php' ) ) {
			wp_enqueue_style(
				'art-zone-blank-contact',
				get_stylesheet_directory_uri() . '/assets/css/contact.css',
				array( 'art-zone-blank-style' ),
				art_zone_blank_asset_version( '/assets/css/contact.css' )
			);
			wp_enqueue_style(
				'art-zone-blank-leaflet',
				'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
				array(),
				'1.9.4'
			);
			wp_enqueue_script(
				'art-zone-blank-leaflet',
				'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
				array(),
				'1.9.4',
				true
			);
			art_zone_blank_enqueue_theme_script( 'art-zone-blank-contact', '/assets/js/contact.js', array( 'art-zone-blank-leaflet' ) );
		}
	}
);

add_filter(
	'upload_mimes',
	function ( $mimes ) {
		$mimes['heic'] = 'image/heic';
		$mimes['heif'] = 'image/heif';

		return $mimes;
	}
);

add_filter(
	'wp_check_filetype_and_ext',
	function ( $data, $file, $filename ) {
		$extension = strtolower( pathinfo( (string) $filename, PATHINFO_EXTENSION ) );

		if ( in_array( $extension, array( 'heic', 'heif' ), true ) ) {
			$data['ext']             = $extension;
			$data['type']            = 'image/' . $extension;
			$data['proper_filename'] = $filename;
		}

		return $data;
	},
	10,
	3
);

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->post_type, array( 'artwork', 'artwork_interior', 'artwork_frame', 'studio_item', 'art_therapy_item' ), true ) ) {
			return;
		}

		$script_path    = get_stylesheet_directory() . '/assets/js/artwork-admin.js';
		$script_version = file_exists( $script_path ) ? filemtime( $script_path ) : wp_get_theme()->get( 'Version' );

		wp_enqueue_media();
		wp_enqueue_script(
			'art-zone-blank-artwork-admin',
			get_stylesheet_directory_uri() . '/assets/js/artwork-admin.js',
			array( 'jquery' ),
			$script_version,
			true
		);
	}
);
