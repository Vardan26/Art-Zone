<?php

function art_zone_blank_parse_ini_size( $value ) {
    $value = trim( (string) $value );

    if ( '' === $value ) {
        return 0;
    }

    $unit   = strtolower( substr( $value, -1 ) );
    $number = (float) $value;

    switch ( $unit ) {
        case 'g':
            return (int) round( $number * 1024 * 1024 * 1024 );
        case 'm':
            return (int) round( $number * 1024 * 1024 );
        case 'k':
            return (int) round( $number * 1024 );
        default:
            return (int) round( $number );
    }
}

function art_zone_blank_format_bytes( $bytes ) {
    $bytes = (int) $bytes;

    if ( $bytes >= 1024 * 1024 * 1024 ) {
        return round( $bytes / ( 1024 * 1024 * 1024 ), 1 ) . ' GB';
    }

    if ( $bytes >= 1024 * 1024 ) {
        return round( $bytes / ( 1024 * 1024 ), 1 ) . ' MB';
    }

    if ( $bytes >= 1024 ) {
        return round( $bytes / 1024, 1 ) . ' KB';
    }

    return $bytes . ' B';
}

function art_zone_blank_csv_rows_from_path( $path ) {
    $rows = array();

    if ( ! $path || ! file_exists( $path ) || ! is_readable( $path ) ) {
        return $rows;
    }

    $handle = fopen( $path, 'r' );

    if ( ! $handle ) {
        return $rows;
    }

    $headers = fgetcsv( $handle );

    if ( ! is_array( $headers ) ) {
        fclose( $handle );
        return $rows;
    }

    $headers = array_map(
        function ( $header ) {
            return sanitize_key( (string) $header );
        },
        $headers
    );

    while ( ( $data = fgetcsv( $handle ) ) !== false ) {
        if ( count( $data ) !== count( $headers ) ) {
            continue;
        }

        $rows[] = array_combine( $headers, $data );
    }

    fclose( $handle );

    return $rows;
}

function art_zone_blank_csv_value( $row, $keys, $default = '' ) {
    foreach ( $keys as $key ) {
        $key = sanitize_key( $key );

        if ( isset( $row[ $key ] ) && '' !== trim( (string) $row[ $key ] ) ) {
            return trim( (string) $row[ $key ] );
        }
    }

    return $default;
}

function art_zone_blank_csv_list( $row, $keys ) {
    $value = art_zone_blank_csv_value( $row, $keys, '' );

    if ( '' === $value ) {
        return array();
    }

    return array_values(
        array_filter(
            array_map(
                'trim',
                preg_split( '/[,|]/', $value )
            )
        )
    );
}

function art_zone_blank_humanize_slug( $slug ) {
    $value = trim( (string) $slug );
    $value = str_replace( array( '-', '_' ), ' ', $value );

    return ucwords( $value );
}

/**
 * Parse artwork metadata encoded in the manifest filename.
 *
 * Expected format: {Material}-{Medium}-{Width}x{Height}[-{Year}]_{category}[variant].jpg
 * Examples:
 *   Canvas-Oil-80x90_floral.jpg
 *   Canvas-Oil-72x60-1998_landscape.jpg
 *   Wood-Oil-30x50_figure.jpg
 *   Canvas-Oil-85x70_landscape-a.jpg
 *
 * Returns an array: material (slug), medium (label), width_cm, height_cm, year, category (slug).
 * Any field that cannot be parsed is returned as an empty string.
 */
function art_zone_blank_parse_filename_metadata( $filename ) {
    $result = array(
        'material'  => '',
        'medium'    => '',
        'width_cm'  => '',
        'height_cm' => '',
        'year'      => '',
        'category'  => '',
    );

    $name = pathinfo( trim( (string) $filename ), PATHINFO_FILENAME );

    // {Material}-{Medium}-{Width}x{Height}[-{Year}]_{Category}
    if ( ! preg_match(
        '/^([A-Za-z]+)-([A-Za-z]+)-(\d+)x(\d+)(?:-(\d{4}))?_(.+)$/i',
        $name,
        $m
    ) ) {
        return $result;
    }

    $result['material']  = strtolower( $m[1] );        // canvas, wood, paper …
    $result['medium']    = ucfirst( strtolower( $m[2] ) ); // Oil, Watercolor …
    $result['width_cm']  = $m[3];
    $result['height_cm'] = $m[4];
    $result['year']      = ! empty( $m[5] ) ? $m[5] : '';

    // Strip trailing single-letter variant suffix (e.g. landscape-a → landscape).
    $result['category'] = preg_replace( '/-[a-z]$/i', '', $m[6] );

    return $result;
}

function art_zone_blank_import_title_from_row( $row ) {
    $title = art_zone_blank_csv_value( $row, array( 'title', 'name' ), '' );

    if ( '' !== $title ) {
        return $title;
    }

    $subject = art_zone_blank_csv_value( $row, array( 'subject', 'subject_english' ), '' );

    if ( '' === $subject ) {
        return '';
    }

    return art_zone_blank_humanize_slug( sanitize_title( $subject ) );
}

/**
 * Pre-processes all import rows and injects a computed 'title' key into each row.
 * Subjects that appear more than once get a numeric suffix using a dash (e.g. "Flowers-1", "Flowers-2").
 * Subjects that appear only once get no suffix.
 */
function art_zone_blank_assign_import_titles( $rows ) {
    // First pass: count how many times each subject slug appears.
    $subject_counts = array();

    foreach ( $rows as $row ) {
        $raw = art_zone_blank_csv_value( $row, array( 'title', 'name' ), '' );

        if ( '' === $raw ) {
            $raw = art_zone_blank_csv_value( $row, array( 'subject', 'subject_english' ), '' );
        }

        if ( '' === $raw ) {
            continue;
        }

        $slug = sanitize_title( $raw );

        if ( ! isset( $subject_counts[ $slug ] ) ) {
            $subject_counts[ $slug ] = 0;
        }

        $subject_counts[ $slug ]++;
    }

    // Second pass: inject the final title into each row.
    $subject_seen = array();

    foreach ( $rows as &$row ) {
        $raw = art_zone_blank_csv_value( $row, array( 'title', 'name' ), '' );

        if ( '' === $raw ) {
            $raw = art_zone_blank_csv_value( $row, array( 'subject', 'subject_english' ), '' );
        }

        if ( '' === $raw ) {
            continue;
        }

        $slug  = sanitize_title( $raw );
        $title = art_zone_blank_humanize_slug( $slug );

        if ( isset( $subject_counts[ $slug ] ) && $subject_counts[ $slug ] > 1 ) {
            $subject_seen[ $slug ] = ( isset( $subject_seen[ $slug ] ) ? $subject_seen[ $slug ] : 0 ) + 1;
            $title .= '-' . $subject_seen[ $slug ];
        }

        $row['title'] = $title;
    }

    unset( $row );

    return $rows;
}

function art_zone_blank_find_attachment_by_filename( $filename ) {
    global $wpdb;

    $filename = basename( trim( (string) $filename ) );

    if ( '' === $filename ) {
        return 0;
    }

    $like = '%/' . $wpdb->esc_like( $filename );

    $attachment_id = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT p.ID
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'attachment'
              AND pm.meta_key = '_wp_attached_file'
              AND pm.meta_value LIKE %s
            ORDER BY p.ID DESC
            LIMIT 1",
            $like
        )
    );

    if ( $attachment_id ) {
        return $attachment_id;
    }

    $attachment = get_page_by_title( pathinfo( $filename, PATHINFO_FILENAME ), OBJECT, 'attachment' );

    return $attachment instanceof WP_Post ? (int) $attachment->ID : 0;
}

function art_zone_blank_find_existing_artwork( $title, $source_filename ) {
    if ( '' !== $source_filename ) {
        $matches = get_posts(
            array(
                'post_type'      => 'artwork',
                'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
                'posts_per_page' => 1,
                'meta_key'       => 'artwork_source_filename',
                'meta_value'     => $source_filename,
            )
        );

        if ( ! empty( $matches ) ) {
            return $matches[0];
        }
    }

    if ( '' !== $title ) {
        $post = get_page_by_title( $title, OBJECT, 'artwork' );

        if ( $post instanceof WP_Post ) {
            return $post;
        }
    }

    return null;
}

function art_zone_blank_recursive_delete_dir( $dir ) {
    if ( ! $dir || ! is_dir( $dir ) ) {
        return;
    }

    $items = scandir( $dir );

    if ( ! is_array( $items ) ) {
        return;
    }

    foreach ( $items as $item ) {
        if ( '.' === $item || '..' === $item ) {
            continue;
        }

        $path = trailingslashit( $dir ) . $item;

        if ( is_dir( $path ) ) {
            art_zone_blank_recursive_delete_dir( $path );
        } elseif ( file_exists( $path ) ) {
            unlink( $path );
        }
    }

    rmdir( $dir );
}

function art_zone_blank_import_images_from_zip( $zip_file ) {
    $result = array(
        'imported' => 0,
        'skipped'  => 0,
        'errors'   => array(),
    );

    if ( empty( $zip_file['tmp_name'] ) || ! class_exists( 'ZipArchive' ) ) {
        return $result;
    }

    $zip = new ZipArchive();

    if ( true !== $zip->open( $zip_file['tmp_name'] ) ) {
        $result['errors'][] = __( 'Could not open ZIP archive.', 'art-zone-blank' );
        return $result;
    }

    $upload_dir = wp_upload_dir();
    $temp_dir   = trailingslashit( $upload_dir['basedir'] ) . 'art-zone-import-' . wp_generate_password( 8, false, false );

    wp_mkdir_p( $temp_dir );
    $zip->extractTo( $temp_dir );
    $zip->close();

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator( $temp_dir, FilesystemIterator::SKIP_DOTS )
    );

    foreach ( $iterator as $file ) {
        if ( ! $file instanceof SplFileInfo || ! $file->isFile() ) {
            continue;
        }

        $filepath  = $file->getPathname();
        $filename  = basename( $filepath );
        $mime      = wp_check_filetype( $filename );
        $extension = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

        if ( empty( $mime['type'] ) || 0 !== strpos( $mime['type'], 'image/' ) ) {
            if ( ! in_array( $extension, array( 'heic', 'heif' ), true ) ) {
                continue;
            }
        }

        if ( ! in_array( $extension, array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'heic', 'heif' ), true ) ) {
            continue;
        }

        if ( art_zone_blank_find_attachment_by_filename( $filename ) ) {
            $result['skipped']++;
            continue;
        }

        $file_array = array(
            'name'     => $filename,
            'tmp_name' => $filepath,
        );

        $attachment_id = media_handle_sideload( $file_array, 0 );

        if ( is_wp_error( $attachment_id ) ) {
            $result['errors'][] = sprintf( '%s: %s', $filename, $attachment_id->get_error_message() );
            continue;
        }

        $result['imported']++;
    }

    art_zone_blank_recursive_delete_dir( $temp_dir );

    return $result;
}

function art_zone_blank_gallery_manifest_rows() {
    static $rows = null;

    if ( null !== $rows ) {
        return $rows;
    }

    $rows = array();
    $path = get_stylesheet_directory() . '/assets/gallery/manifest.csv';

    if ( ! file_exists( $path ) ) {
        return $rows;
    }

    $handle = fopen( $path, 'r' );

    if ( ! $handle ) {
        return $rows;
    }

    $headers = fgetcsv( $handle );

    if ( ! is_array( $headers ) ) {
        fclose( $handle );
        return $rows;
    }

    while ( ( $data = fgetcsv( $handle ) ) !== false ) {
        if ( count( $data ) !== count( $headers ) ) {
            continue;
        }

        $rows[] = array_combine( $headers, $data );
    }

    fclose( $handle );

    return $rows;
}

function art_zone_blank_manifest_import_csv() {
    $rows    = art_zone_blank_gallery_manifest_rows();
    $headers = array( 'filename', 'title', 'art_type', 'category', 'material', 'medium', 'year', 'dimensions', 'series', 'framing', 'description', 'quote', 'palette_note', 'exhibition_history', 'gallery_size' );
    $lines   = array( implode( ',', $headers ) );
    $sizes   = array( 'feature', 'side', 'offset', 'tall', 'small', 'small' );

    foreach ( $rows as $index => $row ) {
        $title = art_zone_blank_import_title_from_row( $row );
        $data  = array(
            basename( art_zone_blank_csv_value( $row, array( 'new_relative_path' ), '' ) ),
            $title,
            'Painting',
            art_zone_blank_csv_value( $row, array( 'style' ), '' ),
            art_zone_blank_csv_value( $row, array( 'material' ), '' ),
            '',
            '',
            '',
            '',
            '',
            art_zone_blank_csv_value( $row, array( 'notes' ), '' ),
            '',
            '',
            '',
            $sizes[ $index % count( $sizes ) ],
        );

        $escaped = array_map(
            function ( $value ) {
                $value = (string) $value;
                return '"' . str_replace( '"', '""', $value ) . '"';
            },
            $data
        );

        $lines[] = implode( ',', $escaped );
    }

    return implode( "\n", $lines );
}

function art_zone_blank_default_artwork_categories() {
    return art_zone_blank_artwork_category_labels();
}

function art_zone_blank_default_artwork_types() {
    return art_zone_blank_artwork_type_labels();
}

function art_zone_blank_default_artwork_materials() {
    return art_zone_blank_artwork_material_labels();
}

function art_zone_blank_default_artwork_mediums() {
    return art_zone_blank_artwork_medium_labels();
}

function art_zone_blank_import_row_to_artwork( $row ) {
    $title           = art_zone_blank_import_title_from_row( $row );
    $source_path     = art_zone_blank_csv_value( $row, array( 'filename', 'file', 'image', 'image_file', 'new_relative_path', 'new_filename' ), '' );
    $source_filename = $source_path ? basename( $source_path ) : '';
    $attachment_id   = $source_filename ? art_zone_blank_find_attachment_by_filename( $source_filename ) : 0;
    $content         = art_zone_blank_csv_value( $row, array( 'content', 'description', 'notes' ), '' );
    $excerpt         = art_zone_blank_csv_value( $row, array( 'excerpt', 'subject_armenian' ), '' );
    $type            = art_zone_blank_csv_value( $row, array( 'art_type', 'type', 'discipline' ), '' );
    $medium          = art_zone_blank_csv_value( $row, array( 'medium' ), '' );
    $dimensions      = art_zone_blank_csv_value( $row, array( 'dimensions', 'size_text' ), '' );
    $year            = art_zone_blank_csv_value( $row, array( 'year', 'date' ), '' );
    $series          = art_zone_blank_csv_value( $row, array( 'series' ), '' );
    $framing         = art_zone_blank_csv_value( $row, array( 'framing' ), '' );
    $quote           = art_zone_blank_csv_value( $row, array( 'quote' ), '' );
    $palette_note    = art_zone_blank_csv_value( $row, array( 'palette_note' ), '' );
    $history_note    = art_zone_blank_csv_value( $row, array( 'exhibition_history' ), '' );
    $gallery_size    = art_zone_blank_csv_value( $row, array( 'gallery_size', 'size', 'layout_size' ), 'small' );
    $types           = art_zone_blank_csv_list( $row, array( 'art_types', 'art_type', 'types', 'type', 'discipline' ) );
    $categories      = art_zone_blank_csv_list( $row, array( 'categories', 'category', 'genre', 'style' ) );
    $materials       = art_zone_blank_csv_list( $row, array( 'materials', 'material' ) );
    $medium_terms    = art_zone_blank_csv_list( $row, array( 'mediums', 'medium' ) );

    // Parse material, medium, dimensions, year, and category from the filename
    // (e.g. Canvas-Oil-80x90_floral.jpg or Canvas-Oil-72x60-1998_landscape.jpg).
    $filename_meta = art_zone_blank_parse_filename_metadata( $source_filename );

    if ( empty( $types ) && '' !== $type ) {
        $types = array( $type );
    }

    if ( '' === $medium && '' !== $filename_meta['medium'] ) {
        $medium = $filename_meta['medium'];
    }

    if ( empty( $materials ) && '' !== $filename_meta['material'] ) {
        $materials = array( $filename_meta['material'] );
    }

    if ( '' === $year && '' !== $filename_meta['year'] ) {
        $year = $filename_meta['year'];
    }

    if ( empty( $categories ) && '' !== $filename_meta['category'] ) {
        $categories = array( $filename_meta['category'] );
    }

    // Resolve width/height from CSV columns first, falling back to filename-parsed values.
    $width_cm  = art_zone_blank_csv_value( $row, array( 'width_cm' ), $filename_meta['width_cm'] );
    $height_cm = art_zone_blank_csv_value( $row, array( 'height_cm' ), $filename_meta['height_cm'] );

    $normalized = art_zone_blank_normalize_artwork_classification( $types, $categories, $materials, array_merge( $medium_terms, art_zone_blank_parse_medium_values( $medium ) ) );

    if ( '' === $title ) {
        return array(
            'status'  => 'skipped',
            'message' => __( 'Row skipped because no title/subject was found.', 'art-zone-blank' ),
        );
    }

    $existing = art_zone_blank_find_existing_artwork( $title, $source_filename );
    $postarr  = array(
        'post_type'    => 'artwork',
        'post_status'  => 'publish',
        'post_title'   => $title,
        'post_content' => $content,
        'post_excerpt' => $excerpt,
    );

    $status = 'created';

    if ( $existing instanceof WP_Post ) {
        $postarr['ID'] = $existing->ID;
        $status        = 'updated';
    }

    $post_id = wp_insert_post( $postarr, true );

    if ( is_wp_error( $post_id ) || ! $post_id ) {
        return array(
            'status'  => 'error',
            'message' => sprintf( __( 'Failed to import "%s".', 'art-zone-blank' ), $title ),
        );
    }

    delete_post_meta( $post_id, 'artwork_medium' );
    if ( '' !== $width_cm ) {
        update_post_meta( $post_id, 'artwork_width_cm', (float) $width_cm );
    }
    if ( '' !== $height_cm ) {
        update_post_meta( $post_id, 'artwork_height_cm', (float) $height_cm );
    }
    update_post_meta( $post_id, 'artwork_year', $year );
    update_post_meta( $post_id, 'artwork_series', $series );
    update_post_meta( $post_id, 'artwork_framing', $framing );
    update_post_meta( $post_id, 'artwork_quote', $quote );
    update_post_meta( $post_id, 'artwork_palette_note', $palette_note );
    update_post_meta( $post_id, 'artwork_exhibition_history', $history_note );
    update_post_meta( $post_id, 'artwork_gallery_size', in_array( $gallery_size, array( 'feature', 'side', 'offset', 'tall', 'small' ), true ) ? $gallery_size : 'small' );
    update_post_meta( $post_id, 'artwork_source_filename', $source_filename );

    if ( $attachment_id ) {
        update_post_meta( $post_id, 'artwork_image_id', $attachment_id );
        set_post_thumbnail( $post_id, $attachment_id );
    }

    if ( ! $attachment_id && '' !== $source_path ) {
        update_post_meta( $post_id, 'artwork_external_image', $source_path );
    }

    wp_set_object_terms( $post_id, array_values( $normalized['types'] ), 'artwork_type' );
    wp_set_object_terms( $post_id, array_values( $normalized['categories'] ), 'artwork_category' );
    wp_set_object_terms( $post_id, array_values( $normalized['materials'] ), 'artwork_material' );
    wp_set_object_terms( $post_id, array_values( $normalized['mediums'] ), 'artwork_medium' );

    return array(
        'status'          => $status,
        'post_id'         => $post_id,
        'title'           => $title,
        'attachment_id'   => $attachment_id,
        'source_filename' => $source_filename,
    );
}

function art_zone_blank_render_import_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $report              = get_transient( 'art_zone_blank_import_report_' . get_current_user_id() );
    $upload_max_filesize = art_zone_blank_parse_ini_size( ini_get( 'upload_max_filesize' ) );
    $post_max_size       = art_zone_blank_parse_ini_size( ini_get( 'post_max_size' ) );
    $import_error        = isset( $_GET['art_zone_import_error'] ) ? sanitize_key( wp_unslash( $_GET['art_zone_import_error'] ) ) : '';

    if ( $report ) {
        delete_transient( 'art_zone_blank_import_report_' . get_current_user_id() );
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Import Artworks', 'art-zone-blank' ); ?></h1>
        <p><?php esc_html_e( 'Upload artwork images to Media Library first. Then import a CSV that references those files by filename. The importer will create or update artwork posts automatically.', 'art-zone-blank' ); ?></p>
        <p>
            <?php
            printf(
                esc_html__( 'Current PHP upload limits: upload_max_filesize %1$s, post_max_size %2$s.', 'art-zone-blank' ),
                esc_html( art_zone_blank_format_bytes( $upload_max_filesize ) ),
                esc_html( art_zone_blank_format_bytes( $post_max_size ) )
            );
            ?>
        </p>
        <p>
            <a class="button button-secondary" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/import/artwork-import-sample.csv' ); ?>" download><?php esc_html_e( 'Download Sample CSV', 'art-zone-blank' ); ?></a>
            <a class="button button-secondary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=art_zone_blank_download_manifest_import_csv' ), 'art_zone_blank_download_manifest_import_csv' ) ); ?>"><?php esc_html_e( 'Download CSV From Current Manifest', 'art-zone-blank' ); ?></a>
        </p>

        <?php if ( 'post_too_large' === $import_error ) : ?>
            <div class="notice notice-error">
                <p>
                    <?php
                    printf(
                        esc_html__( 'The import request was larger than PHP allows, so the server discarded it before WordPress could process it. Reduce the ZIP size or increase upload_max_filesize/post_max_size above %s.', 'art-zone-blank' ),
                        esc_html( art_zone_blank_format_bytes( $post_max_size ) )
                    );
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $report ) ) : ?>
            <div class="notice notice-success">
                <p>
                    <?php
                    printf(
                        esc_html__( 'Import complete. Created: %1$d, Updated: %2$d, Skipped: %3$d, Missing images: %4$d.', 'art-zone-blank' ),
                        (int) $report['created'],
                        (int) $report['updated'],
                        (int) $report['skipped'],
                        (int) $report['missing_images']
                    );
                    ?>
                </p>
                <?php if ( isset( $report['images_imported'] ) || isset( $report['images_skipped'] ) ) : ?>
                    <p>
                        <?php
                        printf(
                            esc_html__( 'ZIP image import: added %1$d, skipped existing %2$d.', 'art-zone-blank' ),
                            (int) $report['images_imported'],
                            (int) $report['images_skipped']
                        );
                        ?>
                    </p>
                <?php endif; ?>
            </div>
            <?php if ( ! empty( $report['missing'] ) ) : ?>
                <div class="notice notice-warning">
                    <p><strong><?php esc_html_e( 'Rows without a Media Library image match:', 'art-zone-blank' ); ?></strong></p>
                    <ul style="list-style:disc;margin-left:20px;">
                        <?php foreach ( $report['missing'] as $item ) : ?>
                            <li><?php echo esc_html( $item ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if ( ! empty( $report['zip_errors'] ) ) : ?>
                <div class="notice notice-error">
                    <p><strong><?php esc_html_e( 'ZIP import errors:', 'art-zone-blank' ); ?></strong></p>
                    <ul style="list-style:disc;margin-left:20px;">
                        <?php foreach ( $report['zip_errors'] as $item ) : ?>
                            <li><?php echo esc_html( $item ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
            <?php wp_nonce_field( 'art_zone_blank_import_artworks', 'art_zone_blank_import_nonce' ); ?>
            <input type="hidden" name="action" value="art_zone_blank_import_artworks">
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="art-zone-blank-import-file"><?php esc_html_e( 'CSV File', 'art-zone-blank' ); ?></label></th>
                    <td><input id="art-zone-blank-import-file" type="file" name="artwork_import_csv" accept=".csv,text/csv" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="art-zone-blank-import-zip"><?php esc_html_e( 'Optional ZIP of Images', 'art-zone-blank' ); ?></label></th>
                    <td>
                        <input id="art-zone-blank-import-zip" type="file" name="artwork_import_zip" accept=".zip,application/zip">
                        <p class="description"><?php esc_html_e( 'If provided, images inside the ZIP will be uploaded to Media Library before CSV rows are matched by filename.', 'art-zone-blank' ); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button( __( 'Import Artworks', 'art-zone-blank' ) ); ?>
        </form>

        <h2><?php esc_html_e( 'Supported Columns', 'art-zone-blank' ); ?></h2>
        <p><code>filename, title, art_type, type, discipline, category, categories, genre, material, materials, medium, mediums, year, dimensions, series, framing, description, quote, palette_note, exhibition_history, gallery_size</code></p>
        <p><?php esc_html_e( 'Manifest-style columns are also supported: `new_relative_path`, `subject`, `style`, `material`, `notes`, `index`.', 'art-zone-blank' ); ?></p>
    </div>
    <?php
}

add_action(
    'admin_menu',
    function () {
        add_submenu_page(
            'edit.php?post_type=artwork',
            __( 'Import Artworks', 'art-zone-blank' ),
            __( 'Import', 'art-zone-blank' ),
            'manage_options',
            'art-zone-blank-import',
            'art_zone_blank_render_import_page'
        );
    }
);

add_action(
    'admin_init',
    function () {
        if ( ! is_admin() ) {
            return;
        }

        $script_name = isset( $_SERVER['SCRIPT_NAME'] ) ? wp_unslash( $_SERVER['SCRIPT_NAME'] ) : '';
        $content_len = isset( $_SERVER['CONTENT_LENGTH'] ) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
        $post_max    = art_zone_blank_parse_ini_size( ini_get( 'post_max_size' ) );

        if ( ! str_ends_with( $script_name, '/wp-admin/admin-post.php' ) || $content_len <= 0 || $post_max <= 0 || $content_len <= $post_max ) {
            return;
        }

        $target = admin_url( 'edit.php?post_type=artwork&page=art-zone-blank-import&art_zone_import_error=post_too_large' );
        wp_safe_redirect( $target );
        exit;
    }
);

add_action(
    'admin_post_art_zone_blank_import_artworks',
    function () {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to do that.', 'art-zone-blank' ) );
        }

        if ( ! isset( $_POST['art_zone_blank_import_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['art_zone_blank_import_nonce'] ) ), 'art_zone_blank_import_artworks' ) ) {
            wp_die( esc_html__( 'Invalid import request.', 'art-zone-blank' ) );
        }

        if ( empty( $_FILES['artwork_import_csv']['tmp_name'] ) ) {
            wp_safe_redirect( admin_url( 'edit.php?post_type=artwork&page=art-zone-blank-import' ) );
            exit;
        }

        $rows   = art_zone_blank_csv_rows_from_path( $_FILES['artwork_import_csv']['tmp_name'] );
        $rows   = art_zone_blank_assign_import_titles( $rows );
        $report = array(
            'created'         => 0,
            'updated'         => 0,
            'skipped'         => 0,
            'missing_images'  => 0,
            'missing'         => array(),
            'images_imported' => 0,
            'images_skipped'  => 0,
            'zip_errors'      => array(),
        );

        if ( ! empty( $_FILES['artwork_import_zip']['tmp_name'] ) ) {
            $zip_report                = art_zone_blank_import_images_from_zip( $_FILES['artwork_import_zip'] );
            $report['images_imported'] = (int) $zip_report['imported'];
            $report['images_skipped']  = (int) $zip_report['skipped'];
            $report['zip_errors']      = $zip_report['errors'];
        }

        foreach ( $rows as $row ) {
            $result = art_zone_blank_import_row_to_artwork( $row );

            if ( 'created' === $result['status'] ) {
                $report['created']++;
            } elseif ( 'updated' === $result['status'] ) {
                $report['updated']++;
            } else {
                $report['skipped']++;
            }

            if ( in_array( $result['status'], array( 'created', 'updated' ), true ) && empty( $result['attachment_id'] ) ) {
                $report['missing_images']++;
                $report['missing'][] = sprintf(
                    '%s (%s)',
                    $result['title'],
                    $result['source_filename'] ? $result['source_filename'] : __( 'no filename', 'art-zone-blank' )
                );
            }
        }

        set_transient( 'art_zone_blank_import_report_' . get_current_user_id(), $report, MINUTE_IN_SECONDS * 5 );
        wp_safe_redirect( admin_url( 'edit.php?post_type=artwork&page=art-zone-blank-import' ) );
        exit;
    }
);

add_action(
    'admin_post_art_zone_blank_download_manifest_import_csv',
    function () {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to do that.', 'art-zone-blank' ) );
        }

        check_admin_referer( 'art_zone_blank_download_manifest_import_csv' );

        $csv = art_zone_blank_manifest_import_csv();

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=artwork-import-from-manifest.csv' );
        echo $csv;
        exit;
    }
);

add_action(
    'init',
    function () {
        $sync_terms = function ( $taxonomy, $allowed_terms ) {
            foreach ( $allowed_terms as $slug => $name ) {
                $term = get_term_by( 'slug', $slug, $taxonomy );

                if ( ! $term ) {
                    wp_insert_term(
                        $name,
                        $taxonomy,
                        array(
                            'slug' => $slug,
                        )
                    );
                    continue;
                }

                if ( $term->name !== $name ) {
                    wp_update_term(
                        $term->term_id,
                        $taxonomy,
                        array(
                            'name' => $name,
                            'slug' => $slug,
                        )
                    );
                }
            }
        };

        $sync_terms( 'artwork_type', art_zone_blank_default_artwork_types() );
        $sync_terms( 'artwork_category', art_zone_blank_default_artwork_categories() );
        $sync_terms( 'artwork_material', art_zone_blank_default_artwork_materials() );
        $sync_terms( 'artwork_medium', art_zone_blank_default_artwork_mediums() );
    },
    15
);

add_action(
    'after_switch_theme',
    function () {
        update_option( 'art_zone_blank_flush_rewrite', 1 );
    }
);

add_action(
    'init',
    function () {
        if ( false === get_option( 'art_zone_blank_flush_rewrite_pending', false ) ) {
            update_option( 'art_zone_blank_flush_rewrite_pending', 1 );
        }

        if ( ! get_option( 'art_zone_blank_flush_rewrite_pending' ) && ! get_option( 'art_zone_blank_flush_rewrite' ) ) {
            return;
        }

        flush_rewrite_rules( false );
        delete_option( 'art_zone_blank_flush_rewrite' );
        update_option( 'art_zone_blank_flush_rewrite_pending', 0 );
    },
    30
);

function art_zone_blank_default_artworks() {
    $rows          = art_zone_blank_gallery_manifest_rows();
    $artworks      = array();
    $subject_count = array();
    $size_cycle    = array( 'feature', 'side', 'offset', 'tall', 'small', 'small' );

    foreach ( $rows as $row ) {
        $subject = sanitize_title( $row['subject'] );

        if ( ! isset( $subject_count[ $subject ] ) ) {
            $subject_count[ $subject ] = 0;
        }

        $subject_count[ $subject ]++;
    }

    foreach ( $rows as $index => $row ) {
        $subject_slug = sanitize_title( $row['subject'] );
        $title        = art_zone_blank_humanize_slug( $subject_slug );

        if ( ! empty( $subject_count[ $subject_slug ] ) && $subject_count[ $subject_slug ] > 1 ) {
            $title .= ' No. ' . str_pad( (string) $row['index'], 3, '0', STR_PAD_LEFT );
        }

        $classification = art_zone_blank_normalize_artwork_classification(
            array( 'painting' ),
            array( sanitize_title( $row['style'] ) ),
            array( sanitize_title( $row['material'] ) ),
            ''
        );
        $excerpt_parts = array_filter(
            array(
                art_zone_blank_humanize_slug( $row['style'] ),
                art_zone_blank_humanize_slug( $row['material'] ),
            )
        );

        $artworks[] = array(
            'title'      => $title,
            'content'    => ! empty( $row['notes'] ) ? $row['notes'] : art_zone_blank_humanize_slug( $row['subject'] ) . ' from the local gallery archive.',
            'excerpt'    => implode( ' / ', $excerpt_parts ),
            'types'      => array_values( $classification['types'] ),
            'mediums'    => array_values( $classification['mediums'] ),
            'dimensions' => '',
            'year'       => '',
            'size'       => $size_cycle[ $index % count( $size_cycle ) ],
            'categories' => array_values( $classification['categories'] ),
            'materials'  => array_values( $classification['materials'] ),
            'image'      => 'assets/gallery/' . ltrim( $row['new_relative_path'], '/' ),
        );
    }

    return $artworks;
}

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_artworks' ) ) {
            return;
        }

        $artwork_count = wp_count_posts( 'artwork' );

        if ( $artwork_count && ! empty( $artwork_count->publish ) ) {
            update_option( 'art_zone_blank_seeded_artworks', 1 );
            return;
        }

        foreach ( art_zone_blank_default_artworks() as $index => $artwork ) {
            $post_id = wp_insert_post(
                array(
                    'post_type'    => 'artwork',
                    'post_status'  => 'publish',
                    'post_title'   => $artwork['title'],
                    'post_content' => $artwork['content'],
                    'post_excerpt' => $artwork['excerpt'],
                    'menu_order'   => $index,
                )
            );

            if ( is_wp_error( $post_id ) || ! $post_id ) {
                continue;
            }

            update_post_meta( $post_id, 'artwork_year', $artwork['year'] );
            update_post_meta( $post_id, 'artwork_gallery_size', $artwork['size'] );
            update_post_meta( $post_id, 'artwork_external_image', $artwork['image'] );
            wp_set_object_terms( $post_id, $artwork['types'], 'artwork_type' );
            wp_set_object_terms( $post_id, $artwork['categories'], 'artwork_category' );
            wp_set_object_terms( $post_id, $artwork['materials'], 'artwork_material' );
            wp_set_object_terms( $post_id, $artwork['mediums'], 'artwork_medium' );
        }

        update_option( 'art_zone_blank_seeded_artworks', 1 );
    },
    20
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_artworks_v4' ) ) {
            return;
        }

        foreach ( art_zone_blank_default_artworks() as $artwork ) {
            $post = get_page_by_title( $artwork['title'], OBJECT, 'artwork' );

            if ( ! $post instanceof WP_Post ) {
                $post_id = wp_insert_post(
                    array(
                        'post_type'    => 'artwork',
                        'post_status'  => 'publish',
                        'post_title'   => $artwork['title'],
                        'post_content' => $artwork['content'],
                        'post_excerpt' => $artwork['excerpt'],
                    )
                );
            } else {
                $post_id = $post->ID;
            }

            if ( is_wp_error( $post_id ) || ! $post_id ) {
                continue;
            }

            update_post_meta( $post_id, 'artwork_year', $artwork['year'] );
            update_post_meta( $post_id, 'artwork_gallery_size', $artwork['size'] );
            update_post_meta( $post_id, 'artwork_external_image', $artwork['image'] );
            wp_set_object_terms( $post_id, $artwork['types'], 'artwork_type' );
            wp_set_object_terms( $post_id, $artwork['categories'], 'artwork_category' );
            wp_set_object_terms( $post_id, $artwork['materials'], 'artwork_material' );
            wp_set_object_terms( $post_id, $artwork['mediums'], 'artwork_medium' );
        }

        update_option( 'art_zone_blank_seeded_artworks_v4', 1 );
    },
    25
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_legacy_artworks_retired' ) ) {
            return;
        }

        $legacy_titles = array(
            'Ephemeral Bloom No. 4',
            'The Silent Grid',
            'Portrait of Solitude',
            'Digital Horizon 02',
            'Structure Study',
            'Void No. 12',
            'Amber Fault',
            'Rose Ledger',
            'Still Figure at Dusk',
            'Moon Valley Study',
            'Chalk Relief',
            'Black Current',
            'Muted Orchard',
            'Ochre Stack',
            'Night Witness',
            'Violet Escarpment',
            'Lime Ground',
            'Graphite Wind',
            'Mineral Choir',
            'Field Interview',
            'River of Dust',
            'Gesso Tablet',
            'Sum of Marks',
            'Sepia Current',
            'Veil of Roses',
            'Profile in Rain',
            'Gold Meridian',
            'White Archive',
            'Carbon Sweep',
        );

        foreach ( $legacy_titles as $title ) {
            $post = get_page_by_title( $title, OBJECT, 'artwork' );

            if ( $post instanceof WP_Post ) {
                wp_update_post(
                    array(
                        'ID'          => $post->ID,
                        'post_status' => 'draft',
                    )
                );
            }
        }

        update_option( 'art_zone_blank_legacy_artworks_retired', 1 );
    },
    26
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_normalized_artwork_classification_v2' ) ) {
            return;
        }

        $artwork_ids = get_posts(
            array(
                'post_type'      => 'artwork',
                'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
                'posts_per_page' => -1,
                'fields'         => 'ids',
            )
        );

        foreach ( $artwork_ids as $post_id ) {
            $normalized = art_zone_blank_normalize_artwork_classification(
                wp_get_object_terms( $post_id, 'artwork_type', array( 'fields' => 'slugs' ) ),
                wp_get_object_terms( $post_id, 'artwork_category', array( 'fields' => 'slugs' ) ),
                wp_get_object_terms( $post_id, 'artwork_material', array( 'fields' => 'slugs' ) ),
                array_merge(
                    wp_get_object_terms( $post_id, 'artwork_medium', array( 'fields' => 'slugs' ) ),
                    art_zone_blank_parse_medium_values( (string) get_post_meta( $post_id, 'artwork_medium', true ) )
                )
            );

            delete_post_meta( $post_id, 'artwork_medium' );
            wp_set_object_terms( $post_id, array_values( $normalized['types'] ), 'artwork_type' );
            wp_set_object_terms( $post_id, array_values( $normalized['categories'] ), 'artwork_category' );
            wp_set_object_terms( $post_id, array_values( $normalized['materials'] ), 'artwork_material' );
            wp_set_object_terms( $post_id, array_values( $normalized['mediums'] ), 'artwork_medium' );
        }

        update_option( 'art_zone_blank_normalized_artwork_classification_v2', 1 );
    },
    28
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_pruned_artwork_taxonomies_v2' ) ) {
            return;
        }

        $allowed = array(
            'artwork_type'     => array_keys( art_zone_blank_default_artwork_types() ),
            'artwork_category' => array_keys( art_zone_blank_default_artwork_categories() ),
            'artwork_material' => array_keys( art_zone_blank_default_artwork_materials() ),
            'artwork_medium'   => array_keys( art_zone_blank_default_artwork_mediums() ),
        );

        foreach ( $allowed as $taxonomy => $allowed_slugs ) {
            $terms = get_terms(
                array(
                    'taxonomy'   => $taxonomy,
                    'hide_empty' => false,
                )
            );

            if ( is_wp_error( $terms ) ) {
                continue;
            }

            foreach ( $terms as $term ) {
                if ( ! in_array( $term->slug, $allowed_slugs, true ) ) {
                    wp_delete_term( $term->term_id, $taxonomy );
                }
            }
        }

        update_option( 'art_zone_blank_pruned_artwork_taxonomies_v2', 1 );
    },
    29
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_art_therapy_page' ) ) {
            return;
        }

        $page = get_page_by_path( 'art-therapy' );

        if ( ! $page instanceof WP_Post ) {
            $page_id = wp_insert_post(
                array(
                    'post_type'    => 'page',
                    'post_status'  => 'publish',
                    'post_title'   => __( 'Art Therapy', 'art-zone-blank' ),
                    'post_name'    => 'art-therapy',
                    'post_content' => '<p>' . esc_html__( 'Use this page to present your art therapy offering, approach, session structure, and contact details.', 'art-zone-blank' ) . '</p><p>' . esc_html__( 'You can edit this content directly from the WordPress page editor.', 'art-zone-blank' ) . '</p>',
                )
            );

            if ( ! is_wp_error( $page_id ) && $page_id ) {
                update_post_meta( $page_id, '_wp_page_template', 'page-art-therapy.php' );
            }
        } elseif ( 'page-art-therapy.php' !== get_page_template_slug( $page->ID ) ) {
            update_post_meta( $page->ID, '_wp_page_template', 'page-art-therapy.php' );
        }

        update_option( 'art_zone_blank_seeded_art_therapy_page', 1 );
    },
    27
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_studio_page' ) ) {
            return;
        }

        $page = get_page_by_path( 'studio' );

        if ( ! $page instanceof WP_Post ) {
            $page_id = wp_insert_post(
                array(
                    'post_type'    => 'page',
                    'post_status'  => 'publish',
                    'post_title'   => __( 'Studio', 'art-zone-blank' ),
                    'post_name'    => 'studio',
                    'post_content' => '<p>' . esc_html__( 'A looser view into the studio: surfaces in progress, fragments, tools, and quiet details of the working space.', 'art-zone-blank' ) . '</p>',
                )
            );

            if ( ! is_wp_error( $page_id ) && $page_id ) {
                update_post_meta( $page_id, '_wp_page_template', 'page-studio.php' );
            }
        } elseif ( 'page-studio.php' !== get_page_template_slug( $page->ID ) ) {
            update_post_meta( $page->ID, '_wp_page_template', 'page-studio.php' );
        }

        update_option( 'art_zone_blank_seeded_studio_page', 1 );
    },
    28
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_contact_page' ) ) {
            return;
        }

        $page = get_page_by_path( 'contact' );

        if ( ! $page instanceof WP_Post ) {
            $page_id = wp_insert_post(
                array(
                    'post_type'    => 'page',
                    'post_status'  => 'publish',
                    'post_title'   => __( 'Contact Us', 'art-zone-blank' ),
                    'post_name'    => 'contact',
                    'post_content' => '<p>' . esc_html__( 'Use this page to share contact information, studio locations, maps, and social links.', 'art-zone-blank' ) . '</p>',
                )
            );

            if ( ! is_wp_error( $page_id ) && $page_id ) {
                update_post_meta( $page_id, '_wp_page_template', 'page-contact.php' );
            }
        } elseif ( 'page-contact.php' !== get_page_template_slug( $page->ID ) ) {
            update_post_meta( $page->ID, '_wp_page_template', 'page-contact.php' );
        }

        update_option( 'art_zone_blank_seeded_contact_page', 1 );
    },
    28
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_studio_items' ) ) {
            return;
        }

        $existing = get_posts(
            array(
                'post_type'      => 'studio_item',
                'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
                'posts_per_page' => 1,
                'fields'         => 'ids',
            )
        );

        if ( ! empty( $existing ) ) {
            update_option( 'art_zone_blank_seeded_studio_items', 1 );
            return;
        }

        update_option( 'art_zone_blank_seeded_studio_items', 1 );
    },
    29
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_art_therapy_items' ) ) {
            return;
        }

        $existing = get_posts(
            array(
                'post_type'      => 'art_therapy_item',
                'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
                'posts_per_page' => 1,
                'fields'         => 'ids',
            )
        );

        if ( ! empty( $existing ) ) {
            update_option( 'art_zone_blank_seeded_art_therapy_items', 1 );
            return;
        }

        update_option( 'art_zone_blank_seeded_art_therapy_items', 1 );
    },
    30
);

function art_zone_blank_default_interior_seed_templates() {
    $base_uri = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/interiors/';

    return array(
        array(
            'id'               => 'xs-portrait-close-wall',
            'title'            => __( 'Close Wall Portrait', 'art-zone-blank' ),
            'background_url'   => $base_uri . 'xs-portrait-close-wall.svg',
            'background_color' => '#e8dfd2',
            'scene_image_width_px'  => 1200,
            'scene_image_height_px' => 1500,
            'scene_real_width_cm'   => 220,
            'scene_real_height_cm'  => 275,
            'slot_x_percent'        => 39.58,
            'slot_y_percent'        => 20.00,
            'slot_max_width_cm' => 45.83,
            'slot_max_height_cm' => 66.00,
            'slot_align_x'          => 'center',
            'slot_align_y'          => 'center',
            'orientations'     => array( 'portrait' ),
            'size_types'       => array( 'xs', 'sm' ),
            'room_type'        => 'wall',
            'sort_order'       => 10,
        ),
        array(
            'id'               => 'sm-portrait-console-wall',
            'title'            => __( 'Console Wall Portrait', 'art-zone-blank' ),
            'background_url'   => $base_uri . 'sm-portrait-console-wall.svg',
            'background_color' => '#e8dfd2',
            'scene_image_width_px'  => 1600,
            'scene_image_height_px' => 1200,
            'scene_real_width_cm'   => 320,
            'scene_real_height_cm'  => 240,
            'slot_x_percent'        => 40.63,
            'slot_y_percent'        => 14.17,
            'slot_max_width_cm' => 60.00,
            'slot_max_height_cm' => 86.00,
            'slot_align_x'          => 'center',
            'slot_align_y'          => 'center',
            'orientations'     => array( 'portrait' ),
            'size_types'       => array( 'sm', 'md' ),
            'room_type'        => 'living',
            'sort_order'       => 20,
        ),
        array(
            'id'               => 'md-portrait-living-room',
            'title'            => __( 'Living Room Portrait', 'art-zone-blank' ),
            'background_url'   => $base_uri . 'md-portrait-living-room.svg',
            'background_color' => '#dccbb8',
            'scene_image_width_px'  => 1600,
            'scene_image_height_px' => 1200,
            'scene_real_width_cm'   => 360,
            'scene_real_height_cm'  => 270,
            'slot_x_percent'        => 38.13,
            'slot_y_percent'        => 10.83,
            'slot_max_width_cm' => 85.50,
            'slot_max_height_cm' => 126.01,
            'slot_align_x'          => 'center',
            'slot_align_y'          => 'center',
            'orientations'     => array( 'portrait' ),
            'size_types'       => array( 'sm', 'md', 'lg' ),
            'room_type'        => 'living',
            'sort_order'       => 30,
        ),
        array(
            'id'               => 'lg-landscape-sofa-wall',
            'title'            => __( 'Sofa Wall Landscape', 'art-zone-blank' ),
            'background_url'   => $base_uri . 'lg-landscape-sofa-wall.svg',
            'background_color' => '#d8d3ca',
            'scene_image_width_px'  => 1800,
            'scene_image_height_px' => 1200,
            'scene_real_width_cm'   => 420,
            'scene_real_height_cm'  => 280,
            'slot_x_percent'        => 30.83,
            'slot_y_percent'        => 14.58,
            'slot_max_width_cm' => 160.99,
            'slot_max_height_cm' => 100.32,
            'slot_align_x'          => 'center',
            'slot_align_y'          => 'center',
            'orientations'     => array( 'landscape' ),
            'size_types'       => array( 'md', 'lg', 'xl' ),
            'room_type'        => 'living',
            'sort_order'       => 40,
        ),
        array(
            'id'               => 'xl-portrait-gallery-wall',
            'title'            => __( 'Gallery Wall Portrait', 'art-zone-blank' ),
            'background_url'   => $base_uri . 'xl-portrait-gallery-wall.svg',
            'background_color' => '#eee9df',
            'scene_image_width_px'  => 1500,
            'scene_image_height_px' => 1800,
            'scene_real_width_cm'   => 300,
            'scene_real_height_cm'  => 360,
            'slot_x_percent'        => 33.67,
            'slot_y_percent'        => 12.22,
            'slot_max_width_cm' => 98.01,
            'slot_max_height_cm' => 164.02,
            'slot_align_x'          => 'center',
            'slot_align_y'          => 'center',
            'orientations'     => array( 'portrait' ),
            'size_types'       => array( 'md', 'lg', 'xl' ),
            'room_type'        => 'gallery',
            'sort_order'       => 50,
        ),
        array(
            'id'               => 'square-medium-minimal-wall',
            'title'            => __( 'Minimal Wall Square', 'art-zone-blank' ),
            'background_url'   => $base_uri . 'square-medium-minimal-wall.svg',
            'background_color' => '#eeeae2',
            'scene_image_width_px'  => 1400,
            'scene_image_height_px' => 1400,
            'scene_real_width_cm'   => 280,
            'scene_real_height_cm'  => 280,
            'slot_x_percent'        => 33.57,
            'slot_y_percent'        => 18.57,
            'slot_max_width_cm' => 92.01,
            'slot_max_height_cm' => 92.01,
            'slot_align_x'          => 'center',
            'slot_align_y'          => 'center',
            'orientations'     => array( 'square' ),
            'size_types'       => array( 'xs', 'sm', 'md', 'lg' ),
            'room_type'        => 'minimal',
            'sort_order'       => 60,
        ),
    );
}

function art_zone_blank_seed_artwork_interiors() {
    foreach ( art_zone_blank_default_interior_seed_templates() as $template ) {
        $existing = get_posts(
            array(
                'post_type'      => 'artwork_interior',
                'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
                'posts_per_page' => 1,
                'meta_key'       => 'artwork_interior_seed_id',
                'meta_value'     => $template['id'],
                'fields'         => 'ids',
            )
        );

        if ( ! empty( $existing ) ) {
            $post_id = (int) $existing[0];
        } else {
            $post_id = wp_insert_post(
                array(
                    'post_type'   => 'artwork_interior',
                    'post_status' => 'publish',
                    'post_title'  => $template['title'],
                    'post_name'   => $template['id'],
                    'menu_order'  => (int) $template['sort_order'],
                )
            );
        }

        if ( is_wp_error( $post_id ) || ! $post_id ) {
            continue;
        }

        wp_update_post(
            array(
                'ID'         => $post_id,
                'post_title' => $template['title'],
                'menu_order' => (int) $template['sort_order'],
            )
        );

        update_post_meta( $post_id, 'artwork_interior_seed_id', $template['id'] );
        update_post_meta( $post_id, 'artwork_interior_background_url', esc_url_raw( $template['background_url'] ) );
        update_post_meta( $post_id, 'artwork_interior_background_color', sanitize_hex_color( $template['background_color'] ) );
        update_post_meta( $post_id, 'artwork_interior_scene_image_width_px', (int) $template['scene_image_width_px'] );
        update_post_meta( $post_id, 'artwork_interior_scene_image_height_px', (int) $template['scene_image_height_px'] );
        update_post_meta( $post_id, 'artwork_interior_scene_real_width_cm', (float) $template['scene_real_width_cm'] );
        update_post_meta( $post_id, 'artwork_interior_scene_real_height_cm', (float) $template['scene_real_height_cm'] );
        update_post_meta( $post_id, 'artwork_interior_slot_x_percent', (float) $template['slot_x_percent'] );
        update_post_meta( $post_id, 'artwork_interior_slot_y_percent', (float) $template['slot_y_percent'] );
        update_post_meta( $post_id, 'artwork_interior_slot_max_width_cm', (float) $template['slot_max_width_cm'] );
        update_post_meta( $post_id, 'artwork_interior_slot_max_height_cm', (float) $template['slot_max_height_cm'] );
        update_post_meta( $post_id, 'artwork_interior_slot_align_x', sanitize_key( $template['slot_align_x'] ) );
        update_post_meta( $post_id, 'artwork_interior_slot_align_y', sanitize_key( $template['slot_align_y'] ) );
        update_post_meta( $post_id, 'artwork_interior_orientations', $template['orientations'] );
        update_post_meta( $post_id, 'artwork_interior_size_types', $template['size_types'] );
        update_post_meta( $post_id, 'artwork_interior_room_type', sanitize_key( $template['room_type'] ) );
        update_post_meta( $post_id, 'artwork_interior_sort_order', (int) $template['sort_order'] );
        update_post_meta( $post_id, 'artwork_interior_is_active', '1' );
    }
}

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_artwork_interiors_v3' ) ) {
            return;
        }

        art_zone_blank_seed_artwork_interiors();
        update_option( 'art_zone_blank_seeded_artwork_interiors_v3', 1 );
    },
    31
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_cleaned_seeded_editorial_items' ) ) {
            return;
        }

        $targets = array(
            'studio_item'      => array(
                'Morning Table',
                'Material Study',
                'A Wider Studio Moment',
                'Tools and Fragments',
            ),
            'art_therapy_item' => array(
                'A Space To Slow Down',
                'Working Through Materials',
                'Held, Spacious, Personal',
            ),
        );

        foreach ( $targets as $post_type => $titles ) {
            foreach ( $titles as $title ) {
                $post = get_page_by_title( $title, OBJECT, $post_type );

                if ( $post instanceof WP_Post ) {
                    wp_trash_post( $post->ID );
                }
            }
        }

        update_option( 'art_zone_blank_cleaned_seeded_editorial_items', 1 );
    },
    31
);

// ─── Default pages ────────────────────────────────────────────────────────────

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_home_page' ) ) {
            return;
        }

        // Skip if a static front page is already configured.
        if ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) ) {
            update_option( 'art_zone_blank_seeded_home_page', 1 );
            return;
        }

        $existing = get_page_by_path( 'home' ) ?: get_page_by_path( 'welcome' );

        if ( $existing instanceof WP_Post ) {
            $page_id = $existing->ID;
        } else {
            $page_id = wp_insert_post(
                array(
                    'post_type'    => 'page',
                    'post_status'  => 'publish',
                    'post_title'   => __( 'Home', 'art-zone-blank' ),
                    'post_name'    => 'home',
                    'post_content' => '',
                )
            );
        }

        if ( ! is_wp_error( $page_id ) && $page_id ) {
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $page_id );
        }

        update_option( 'art_zone_blank_seeded_home_page', 1 );
    },
    22
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_portfolio_page' ) ) {
            return;
        }

        $page = get_page_by_path( 'portfolio' ) ?: get_page_by_path( 'gallery' ) ?: get_page_by_path( 'works' );

        if ( ! $page instanceof WP_Post ) {
            $page_id = wp_insert_post(
                array(
                    'post_type'    => 'page',
                    'post_status'  => 'publish',
                    'post_title'   => __( 'Portfolio', 'art-zone-blank' ),
                    'post_name'    => 'portfolio',
                    'post_content' => '',
                )
            );

            if ( ! is_wp_error( $page_id ) && $page_id ) {
                update_post_meta( $page_id, '_wp_page_template', 'page-portfolio.php' );
            }
        } elseif ( 'page-portfolio.php' !== get_page_template_slug( $page->ID ) ) {
            update_post_meta( $page->ID, '_wp_page_template', 'page-portfolio.php' );
        }

        update_option( 'art_zone_blank_seeded_portfolio_page', 1 );
    },
    23
);

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_about_page' ) ) {
            return;
        }

        $page = get_page_by_path( 'about' ) ?: get_page_by_path( 'artist' ) ?: get_page_by_path( 'about-the-artist' );

        if ( ! $page instanceof WP_Post ) {
            $page_id = wp_insert_post(
                array(
                    'post_type'    => 'page',
                    'post_status'  => 'publish',
                    'post_title'   => __( 'About', 'art-zone-blank' ),
                    'post_name'    => 'about',
                    'post_content' => '',
                )
            );

            if ( ! is_wp_error( $page_id ) && $page_id ) {
                update_post_meta( $page_id, '_wp_page_template', 'page-about.php' );
            }
        } elseif ( 'page-about.php' !== get_page_template_slug( $page->ID ) ) {
            update_post_meta( $page->ID, '_wp_page_template', 'page-about.php' );
        }

        update_option( 'art_zone_blank_seeded_about_page', 1 );
    },
    23
);

// ─── Default primary nav menu ─────────────────────────────────────────────────

add_action(
    'init',
    function () {
        if ( get_option( 'art_zone_blank_seeded_primary_menu' ) ) {
            return;
        }

        // Skip if a menu is already assigned to the primary location.
        $locations = get_theme_mod( 'nav_menu_locations', array() );

        if ( ! empty( $locations['primary'] ) && get_term( (int) $locations['primary'], 'nav_menu' ) instanceof WP_Term ) {
            update_option( 'art_zone_blank_seeded_primary_menu', 1 );
            return;
        }

        $menu_id = wp_create_nav_menu( __( 'Primary Menu', 'art-zone-blank' ) );

        if ( is_wp_error( $menu_id ) ) {
            return;
        }

        $front_page_id = (int) get_option( 'page_on_front' );
        $portfolio_id  = art_zone_blank_find_page_id_by_paths( array( 'portfolio', 'gallery', 'works' ) )
                         ?: art_zone_blank_find_page_id_by_template( 'page-portfolio.php' );
        $about_id      = art_zone_blank_find_page_id_by_paths( array( 'about', 'artist', 'about-the-artist' ) )
                         ?: art_zone_blank_find_page_id_by_template( 'page-about.php' );
        $contact_id    = art_zone_blank_find_page_id_by_paths( array( 'contact', 'contact-us', 'contacts' ) )
                         ?: art_zone_blank_find_page_id_by_template( 'page-contact.php' );

        $items = array(
            array( 'title' => __( 'Home',      'art-zone-blank' ), 'id' => $front_page_id ),
            array( 'title' => __( 'Portfolio', 'art-zone-blank' ), 'id' => $portfolio_id ),
            array( 'title' => __( 'About',     'art-zone-blank' ), 'id' => $about_id ),
            array( 'title' => __( 'Contact',   'art-zone-blank' ), 'id' => $contact_id ),
        );

        $order = 1;

        foreach ( $items as $item ) {
            if ( ! $item['id'] ) {
                continue;
            }

            wp_update_nav_menu_item(
                $menu_id,
                0,
                array(
                    'menu-item-title'     => $item['title'],
                    'menu-item-object'    => 'page',
                    'menu-item-object-id' => $item['id'],
                    'menu-item-type'      => 'post_type',
                    'menu-item-status'    => 'publish',
                    'menu-item-position'  => $order++,
                )
            );
        }

        $locations['primary'] = $menu_id;
        set_theme_mod( 'nav_menu_locations', $locations );

        update_option( 'art_zone_blank_seeded_primary_menu', 1 );
    },
    36
);
