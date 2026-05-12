<?php
get_header();

while ( have_posts() ) :
    the_post();
    $post_id         = get_the_ID();
    $width_cm        = (float) get_post_meta( $post_id, 'artwork_width_cm', true );
    $height_cm       = (float) get_post_meta( $post_id, 'artwork_height_cm', true );
    $dimensions      = ( $width_cm > 0 && $height_cm > 0 ) ? $width_cm . ' × ' . $height_cm . ' cm' : '';
    $year            = get_post_meta( $post_id, 'artwork_year', true );
    $series          = get_post_meta( $post_id, 'artwork_series', true );
    $framing         = get_post_meta( $post_id, 'artwork_framing', true );
    $quote           = get_post_meta( $post_id, 'artwork_quote', true );
    $enquiry_url     = get_post_meta( $post_id, 'artwork_enquiry_url', true );
    $image           = art_zone_blank_get_artwork_image( $post_id );
    $framing_status  = get_post_meta( $post_id, 'artwork_framing_status', true );
    $use_drop_shadow = art_zone_blank_artwork_uses_drop_shadow( $post_id );
    $types           = art_zone_blank_get_artwork_term_names( $post_id, 'artwork_type' );
    $type            = implode( ', ', $types );
    $mediums         = art_zone_blank_get_artwork_term_names( $post_id, 'artwork_medium' );
    $medium          = implode( ', ', $mediums );
    $categories      = get_the_terms( $post_id, 'artwork_category' );
    $materials       = get_the_terms( $post_id, 'artwork_material' );
    $valid_categories = ! empty( $categories ) && ! is_wp_error( $categories );
    $primary_term     = $valid_categories ? $categories[0]->name : '';
    $description     = get_the_content() ? get_the_content() : get_the_excerpt();
    $enquiry_url     = $enquiry_url ? $enquiry_url : '#contact';

    $related_query = new WP_Query(
        array(
            'post_type'      => 'artwork',
            'post_status'    => 'publish',
            'posts_per_page' => 3,
            'post__not_in'   => array( $post_id ),
            'orderby'        => array(
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            ),
            'tax_query'      => $valid_categories ? array(
                array(
                    'taxonomy' => 'artwork_category',
                    'field'    => 'term_id',
                    'terms'    => wp_list_pluck( $categories, 'term_id' ),
                ),
            ) : array(),
        )
    );
    ?>
    <main id="primary" class="site-main artwork-detail" data-frame-root>
        <section class="artwork-detail__hero section-shell">
            <div class="artwork-detail__grid">
                <div class="artwork-detail__visual">
                    <div class="artwork-detail__frame">
                        <div class="artwork-frame<?php echo $use_drop_shadow ? ' artwork-frame--drop-shadow' : ''; ?>">
                            <?php if ( $image ) : ?>
                                <img class="artwork-detail__image" src="<?php echo esc_url( $image ); ?>" alt="<?php the_title_attribute(); ?>" fetchpriority="high" decoding="async">
                            <?php endif; ?>
                        </div>
                        <div class="artwork-detail__shadow artwork-detail__shadow--large" aria-hidden="true"></div>
                        <div class="artwork-detail__shadow artwork-detail__shadow--small" aria-hidden="true"></div>
                    </div>
                    <?php
                    $frames = art_zone_blank_get_artwork_frames();
                    if ( ! empty( $frames ) && 'not_applicable' !== $framing_status ) :
                        get_template_part(
                            'template-parts/artwork/frame-selector',
                            null,
                            array(
                                'frames'         => $frames,
                                'artworkWidthCm' => $width_cm,
                                'postId'         => $post_id,
                            )
                        );
                    endif;
                    ?>
                </div>

                <article class="artwork-detail__sidebar">
                    <div class="artwork-detail__stack">
                        <div>
                            <p class="artwork-detail__eyebrow"><?php echo esc_html( $type ); ?></p>
                            <h1 class="artwork-detail__title"><?php the_title(); ?></h1>
                            <div class="artwork-detail__headline-meta">
                                <?php if ( $year ) : ?>
                                    <span><?php echo esc_html( $year ); ?></span>
                                <?php endif; ?>
                                <span class="artwork-detail__headline-line" aria-hidden="true"></span>
                                <span><?php echo esc_html( $series ); ?></span>
                            </div>
                        </div>

                        <div class="artwork-detail__specs">
                            <?php if ( $primary_term ) : ?>
                                <div class="artwork-detail__spec">
                                    <span><?php esc_html_e( 'Genre', 'art-zone-blank' ); ?></span>
                                    <strong><?php echo esc_html( $primary_term ); ?></strong>
                                </div>
                            <?php endif; ?>

                            <div class="artwork-detail__spec">
                                <span><?php esc_html_e( 'Dimensions', 'art-zone-blank' ); ?></span>
                                <strong><?php echo esc_html( $dimensions ); ?></strong>
                            </div>

                            <?php
                            $media_label = art_zone_blank_artwork_display_medium(
                                ( ! empty( $materials ) && ! is_wp_error( $materials ) ) ? wp_list_pluck( $materials, 'name' ) : array(),
                                $medium
                            );
                            ?>
                            <?php if ( $media_label ) : ?>
                                <div class="artwork-detail__spec">
                                    <span><?php esc_html_e( 'Materials / Medium', 'art-zone-blank' ); ?></span>
                                    <strong><?php echo esc_html( $media_label ); ?></strong>
                                </div>
                            <?php endif; ?>

                            <?php if ( $framing ) : ?>
                                <div class="artwork-detail__spec">
                                    <span><?php esc_html_e( 'Framing', 'art-zone-blank' ); ?></span>
                                    <strong><?php echo esc_html( $framing ); ?></strong>
                                </div>
                            <?php endif; ?>

                        </div>


                        <?php if ( $quote ) : ?>
                            <div class="artwork-detail__text">
                                <div class="artwork-detail__description">
                                    <?php echo esc_html( $quote ); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="artwork-detail__actions">
                            <a class="artwork-detail__button" href="<?php echo esc_url( $enquiry_url ); ?>"><?php esc_html_e( 'Enquire About This Piece', 'art-zone-blank' ); ?></a>
                            <div class="artwork-detail__utility">
                                <button type="button"><?php esc_html_e( 'Share', 'art-zone-blank' ); ?></button>
                                <button type="button"><?php esc_html_e( 'View in AR', 'art-zone-blank' ); ?></button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <?php art_zone_blank_render_artwork_interior_mockups( $post_id ); ?>

        <?php if ( $related_query->have_posts() ) : ?>
            <section class="artwork-detail__related section-shell">
                <div class="artwork-detail__related-head">
                    <div>
                        <h2 class="artwork-detail__related-title"><?php esc_html_e( 'Related Works', 'art-zone-blank' ); ?></h2>
                    </div>
                    <a class="artwork-detail__related-link" href="<?php echo esc_url( art_zone_blank_portfolio_url( art_zone_blank_home_url() ) ); ?>"><?php esc_html_e( 'View Full Series', 'art-zone-blank' ); ?></a>
                </div>
                <div class="home-collection-simple__grid">
                    <?php
                    while ( $related_query->have_posts() ) :
                        $related_query->the_post();
                        $related_id       = get_the_ID();
                        $related_image    = art_zone_blank_get_artwork_image( $related_id, 'az-collection' );
                        $related_image_id = art_zone_blank_get_artwork_image_id( $related_id );
                        if ( ! $related_image && ! $related_image_id ) {
                            continue;
                        }
                        $related_medium = implode( ', ', art_zone_blank_get_artwork_term_names( $related_id, 'artwork_medium' ) );
                        $related_year   = get_post_meta( $related_id, 'artwork_year', true );
                        ?>
                        <article class="home-collection-simple__card">
                            <a class="home-collection-simple__media" href="<?php the_permalink(); ?>">
                                <?php if ( $related_image_id ) : ?>
                                    <?php echo wp_get_attachment_image( $related_image_id, 'az-collection', false, array(
                                        'class'    => 'home-collection-simple__image',
                                        'loading'  => 'lazy',
                                        'decoding' => 'async',
                                        'alt'      => esc_attr( get_the_title() ),
                                    ) ); ?>
                                <?php else : ?>
                                    <img class="home-collection-simple__image" src="<?php echo esc_url( $related_image ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" decoding="async">
                                <?php endif; ?>
                            </a>
                            <div class="home-collection-simple__meta">
                                <h3 class="home-collection-simple__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="home-collection-simple__details">
                                    <?php echo esc_html( implode( ' - ', array_filter( array( $related_medium, $related_year ) ) ) ); ?>
                                </p>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
    <?php
endwhile;

get_footer();
