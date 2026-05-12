<?php
/*
Template Name: Portfolio Gallery
*/

get_header();

$all_items     = art_zone_blank_gallery_items();
$initial_items = array_slice( $all_items, 0, 20 );
$pending_items = array_slice( $all_items, 20 );

// Pre-format data needed by the JS card builder so PHP helpers are not needed client-side.
$pending_json = array_map(
    function ( $item ) {
        $meta_parts = array_filter(
            array(
                $item['year'],
                $item['dimensions'],
                art_zone_blank_artwork_display_medium( $item['materials'], $item['medium'] ),
            )
        );
        return array(
            'title'         => $item['title'],
            'image'         => $item['image'],
            'imageLightbox' => ! empty( $item['image_lightbox'] ) ? $item['image_lightbox'] : $item['image'],
            'permalink'     => $item['permalink'],
            'size'          => $item['size'],
            'meta'          => implode( ' - ', $meta_parts ),
            'typeSlugs'     => $item['type_slugs'],
            'categorySlugs' => $item['category_slugs'],
            'materialSlugs' => $item['material_slugs'],
            'mediumSlugs'   => $item['medium_slugs'],
            'artworkWidth'  => $item['width_cm'],
            'artworkHeight' => $item['height_cm'],
        );
    },
    $pending_items
);
$interior_templates_js = array_map(
    function ( $tpl ) {
        $slot = isset( $tpl['slot'] ) && is_array( $tpl['slot'] ) ? $tpl['slot'] : array();
        return array(
            'thumbUrl'   => $tpl['backgroundThumbImage'],
            'bgColor'    => $tpl['backgroundColor'],
            'sceneRealW' => (float) ( isset( $tpl['sceneRealWidthCm'] ) ? $tpl['sceneRealWidthCm'] : 0 ),
            'sceneRealH' => (float) ( isset( $tpl['sceneRealHeightCm'] ) ? $tpl['sceneRealHeightCm'] : 0 ),
            'sortOrder'  => (int) ( isset( $tpl['sortOrder'] ) ? $tpl['sortOrder'] : 100 ),
            'slot'       => array(
                'x'      => (float) ( isset( $slot['xPercent'] ) ? $slot['xPercent'] : 0 ),
                'y'      => (float) ( isset( $slot['yPercent'] ) ? $slot['yPercent'] : 0 ),
                'w'      => (float) ( isset( $slot['maxWidthPercent'] ) ? $slot['maxWidthPercent'] : 0 ),
                'h'      => (float) ( isset( $slot['maxHeightPercent'] ) ? $slot['maxHeightPercent'] : 0 ),
                'maxW'   => (float) ( isset( $slot['maxWidthCm'] ) ? $slot['maxWidthCm'] : 0 ),
                'maxH'   => (float) ( isset( $slot['maxHeightCm'] ) ? $slot['maxHeightCm'] : 0 ),
                'alignX' => art_zone_blank_slot_alignment_to_css( 'x', isset( $slot['alignX'] ) ? $slot['alignX'] : 'center' ),
                'alignY' => art_zone_blank_slot_alignment_to_css( 'y', isset( $slot['alignY'] ) ? $slot['alignY'] : 'center' ),
            ),
            'supports'   => array(
                'orientations' => array_values( (array) ( isset( $tpl['supports']['orientations'] ) ? $tpl['supports']['orientations'] : array() ) ),
                'sizeTypes'    => array_values( (array) ( isset( $tpl['supports']['sizeTypes'] ) ? $tpl['supports']['sizeTypes'] : array() ) ),
            ),
        );
    },
    art_zone_blank_get_interior_templates()
);
$type_terms = get_terms(
    art_zone_blank_query_all_languages(
        array(
        'taxonomy'   => 'artwork_type',
        'hide_empty' => true,
        )
    )
);
$category_terms = get_terms(
    art_zone_blank_query_all_languages(
        array(
        'taxonomy'   => 'artwork_category',
        'hide_empty' => true,
        )
    )
);
$material_terms = get_terms(
    art_zone_blank_query_all_languages(
        array(
        'taxonomy'   => 'artwork_material',
        'hide_empty' => true,
        )
    )
);
$medium_terms = get_terms(
    art_zone_blank_query_all_languages(
        array(
        'taxonomy'   => 'artwork_medium',
        'hide_empty' => true,
        )
    )
);
$portfolio_title = art_zone_blank_mod( 'portfolio_title', __( 'Works', 'art-zone-blank' ) );
$count_template  = art_zone_blank_mod( 'portfolio_count_template', __( 'Showing %1$s of %2$s works', 'art-zone-blank' ) );
?>
<main id="primary" class="site-main gallery-page" data-initial-visible="20" data-load-step="10" data-count-template="<?php echo esc_attr( $count_template ); ?>" data-total="<?php echo esc_attr( (string) count( $all_items ) ); ?>">
    <div class="gallery-page__shell section-shell">
        <header class="gallery-page__header">
            <div class="gallery-page__header-row">
                <div class="gallery-page__header-main">
                    <h1 class="gallery-page__title"><?php echo esc_html( $portfolio_title ? $portfolio_title : ( get_the_title() ? get_the_title() : __( 'Works', 'art-zone-blank' ) ) ); ?></h1>
                </div>
                <div class="gallery-page__header-actions">
                    <button class="gallery-filter-toggle" type="button" aria-expanded="false" aria-controls="gallery-filters-panel" data-filter-toggle>
                        <i class="fa-solid fa-sliders" aria-hidden="true"></i>
                        <span><?php esc_html_e( 'Filters', 'art-zone-blank' ); ?></span>
                    </button>
                </div>
            </div>
            <div id="gallery-filters-panel" class="gallery-filters" aria-label="<?php esc_attr_e( 'Gallery filters', 'art-zone-blank' ); ?>" hidden>
                <div class="gallery-filters__group gallery-filters__group--primary">
                    <p class="gallery-filters__label"><?php esc_html_e( 'Type', 'art-zone-blank' ); ?></p>
                    <div class="gallery-filters__items">
                        <button class="gallery-filters__item is-active" type="button" data-filter-group="type" data-filter-value="all"><?php echo esc_html( art_zone_blank_mod( 'portfolio_all_label', __( 'All', 'art-zone-blank' ) ) ); ?></button>
                        <?php if ( ! is_wp_error( $type_terms ) ) : ?>
                            <?php foreach ( $type_terms as $term ) : ?>
                                <button class="gallery-filters__item" type="button" data-filter-group="type" data-filter-value="<?php echo esc_attr( $term->slug ); ?>">
                                    <?php echo esc_html( $term->name ); ?>
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="gallery-filters__group gallery-filters__group--secondary">
                    <p class="gallery-filters__label"><?php esc_html_e( 'Genre', 'art-zone-blank' ); ?></p>
                    <div class="gallery-filters__items">
                        <button class="gallery-filters__item is-active" type="button" data-filter-group="category" data-filter-value="all"><?php echo esc_html( art_zone_blank_mod( 'portfolio_all_label', __( 'All', 'art-zone-blank' ) ) ); ?></button>
                        <?php if ( ! is_wp_error( $category_terms ) ) : ?>
                            <?php foreach ( $category_terms as $term ) : ?>
                                <button class="gallery-filters__item" type="button" data-filter-group="category" data-filter-value="<?php echo esc_attr( $term->slug ); ?>">
                                    <?php echo esc_html( $term->name ); ?>
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="gallery-filters__group gallery-filters__group--tertiary">
                    <p class="gallery-filters__label"><?php esc_html_e( 'Medium', 'art-zone-blank' ); ?></p>
                    <div class="gallery-filters__items">
                        <button class="gallery-filters__item is-active" type="button" data-filter-group="medium" data-filter-value="all"><?php echo esc_html( art_zone_blank_mod( 'portfolio_all_label', __( 'All', 'art-zone-blank' ) ) ); ?></button>
                        <?php if ( ! is_wp_error( $medium_terms ) ) : ?>
                            <?php foreach ( $medium_terms as $term ) : ?>
                                <button class="gallery-filters__item" type="button" data-filter-group="medium" data-filter-value="<?php echo esc_attr( $term->slug ); ?>">
                                    <?php echo esc_html( $term->name ); ?>
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="gallery-filters__group gallery-filters__group--quaternary">
                    <p class="gallery-filters__label"><?php esc_html_e( 'Material', 'art-zone-blank' ); ?></p>
                    <div class="gallery-filters__items">
                        <button class="gallery-filters__item is-active" type="button" data-filter-group="material" data-filter-value="all"><?php echo esc_html( art_zone_blank_mod( 'portfolio_all_label', __( 'All', 'art-zone-blank' ) ) ); ?></button>
                        <?php if ( ! is_wp_error( $material_terms ) ) : ?>
                            <?php foreach ( $material_terms as $term ) : ?>
                                <button class="gallery-filters__item" type="button" data-filter-group="material" data-filter-value="<?php echo esc_attr( $term->slug ); ?>">
                                    <?php echo esc_html( $term->name ); ?>
                                </button>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <section class="gallery-grid" aria-label="<?php esc_attr_e( 'Works gallery', 'art-zone-blank' ); ?>">
            <?php foreach ( $initial_items as $index => $item ) : ?>
                <?php
                $meta_parts = array_filter(
                    array(
                        $item['year'],
                        $item['dimensions'],
                        art_zone_blank_artwork_display_medium( $item['materials'], $item['medium'] ),
                    )
                );
                $type_slugs     = ! empty( $item['type_slugs'] ) ? implode( ' ', $item['type_slugs'] ) : '';
                $category_slugs = ! empty( $item['category_slugs'] ) ? implode( ' ', $item['category_slugs'] ) : '';
                $material_slugs = ! empty( $item['material_slugs'] ) ? implode( ' ', $item['material_slugs'] ) : '';
                $medium_slugs   = ! empty( $item['medium_slugs'] ) ? implode( ' ', $item['medium_slugs'] ) : '';
                ?>
                <article
                    class="gallery-card gallery-card--<?php echo esc_attr( $item['size'] ); ?>"
                    data-types="<?php echo esc_attr( $type_slugs ); ?>"
                    data-categories="<?php echo esc_attr( $category_slugs ); ?>"
                    data-materials="<?php echo esc_attr( $material_slugs ); ?>"
                    data-mediums="<?php echo esc_attr( $medium_slugs ); ?>"
                    data-title="<?php echo esc_attr( $item['title'] ); ?>"
                    data-image="<?php echo esc_url( ! empty( $item['image_lightbox'] ) ? $item['image_lightbox'] : $item['image'] ); ?>"
                    data-image-preview="<?php echo esc_url( $item['image'] ); ?>"
                    data-permalink="<?php echo esc_url( $item['permalink'] ); ?>"
                    data-meta="<?php echo esc_attr( implode( ' - ', $meta_parts ) ); ?>"
                    data-artwork-width="<?php echo esc_attr( (string) (float) $item['width_cm'] ); ?>"
                    data-artwork-height="<?php echo esc_attr( (string) (float) $item['height_cm'] ); ?>"
                >
                    <a class="gallery-card__image-wrap" href="<?php echo esc_url( $item['permalink'] ); ?>">
                        <?php if ( ! empty( $item['image_id'] ) ) : ?>
                            <?php echo wp_get_attachment_image( $item['image_id'], 'az-card-lg', false, array(
                                'class'    => 'gallery-card__image',
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                                'alt'      => esc_attr( $item['title'] ),
                                'sizes'    => '(max-width: 600px) 100vw, (max-width: 1024px) 50vw, 400px',
                            ) ); ?>
                        <?php else : ?>
                            <img
                                class="gallery-card__image"
                                src="<?php echo esc_url( $item['image'] ); ?>"
                                alt="<?php echo esc_attr( $item['title'] ); ?>"
                                loading="lazy"
                                decoding="async"
                            >
                        <?php endif; ?>
                        <div class="gallery-card__overlay">
                            <p class="gallery-card__overlay-title"><?php echo esc_html( $item['title'] ); ?></p>
                            <p class="gallery-card__overlay-meta"><?php echo esc_html( implode( ' - ', $meta_parts ) ); ?></p>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </section>

        <div class="gallery-page__sentinel" aria-hidden="true"></div>
    </div>

    <?php if ( ! empty( $pending_json ) ) : ?>
    <script>window.artZoneGalleryData = <?php echo wp_json_encode( $pending_json, JSON_HEX_TAG | JSON_HEX_AMP ); ?>;</script>
    <?php endif; ?>
    <?php if ( ! empty( $interior_templates_js ) ) : ?>
    <script>window.artZoneInteriorTemplates = <?php echo wp_json_encode( $interior_templates_js, JSON_HEX_TAG | JSON_HEX_AMP ); ?>;</script>
    <?php endif; ?>

    <div class="gallery-lightbox" hidden aria-hidden="true">
        <div class="gallery-lightbox__backdrop" data-lightbox-close></div>
        <div class="gallery-lightbox__dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Artwork preview', 'art-zone-blank' ); ?>">
            <button type="button" class="gallery-lightbox__close" data-lightbox-close aria-label="<?php esc_attr_e( 'Close preview', 'art-zone-blank' ); ?>">&times;</button>
            <button type="button" class="gallery-lightbox__nav gallery-lightbox__nav--prev" data-lightbox-prev aria-label="<?php esc_attr_e( 'Previous artwork', 'art-zone-blank' ); ?>">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
            </button>
            <button type="button" class="gallery-lightbox__nav gallery-lightbox__nav--next" data-lightbox-next aria-label="<?php esc_attr_e( 'Next artwork', 'art-zone-blank' ); ?>">
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </button>
            <div class="gallery-lightbox__media">
                <img class="gallery-lightbox__image" src="" alt="">
            </div>
            <div class="gallery-lightbox__content">
                <p class="gallery-lightbox__title"></p>
                <p class="gallery-lightbox__meta"></p>
                <ul class="gallery-lightbox__features" aria-hidden="true">
                    <li><?php esc_html_e( 'Live Interior Preview', 'art-zone-blank' ); ?></li>
                    <li><?php esc_html_e( 'Frame Customization', 'art-zone-blank' ); ?></li>
                    <li><?php esc_html_e( 'Artwork Detail Zoom', 'art-zone-blank' ); ?></li>
                </ul>
                <div class="gallery-lightbox__rooms" aria-hidden="true" hidden></div>
                <a class="button button--dark gallery-lightbox__link" href=""><?php esc_html_e( 'Explore This Artwork', 'art-zone-blank' ); ?></a>
            </div>
        </div>
    </div>
</main>
<?php
get_footer();
