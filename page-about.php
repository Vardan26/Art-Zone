<?php
/*
Template Name: About Page
*/

get_header();

$studio_items = art_zone_blank_get_studio_items();
$feature_video_url = art_zone_blank_media_mod_url( 'about_feature_video_url', '' );
?>
<main id="primary" class="site-main about-page">
    <section class="about-page__hero section-shell">
        <div class="editorial-hero editorial-hero--viewport about-page__hero-grid">
            <div class="editorial-hero__media about-page__portrait-wrap">
                    <img src="<?php echo esc_url( art_zone_blank_media_mod_url( 'about_portrait_image_url', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAMz8rttBQTAz-Qu50Kd6EMJGdz4BF2q7uSQKoKmWAw0AcSyqwfEO5S7hN1P8kTz2OSESRTs7we05xCWcG82YSF8fRRX3v4cFiQVl6vXNu1ROBzbzYZGMmJWoRTzLKqNGckqkq9GffKWiXjQ6PCt7BDPAClxTO40jodtzmFbdHsUN0ugZJugxKwa2XoH6KwfU4Bow96ixqpjFaKTQQitgYbbdcsFix6YpzQsnzVuvWskFJ4dWR3NbkZ00_7fiEWFvx955eYKKcMkUs', 'az-editorial' ) ); ?>" alt="<?php esc_attr_e( 'Artist in studio', 'art-zone-blank' ); ?>" fetchpriority="high" decoding="async">
            </div>
            <div class="editorial-hero__panel about-page__intro">
                <p class="about-page__eyebrow"><?php echo esc_html( art_zone_blank_mod( 'about_eyebrow', __( 'The Artist', 'art-zone-blank' ) ) ); ?></p>
                <h1 class="about-page__title"><?php echo esc_html( art_zone_blank_mod( 'about_title', __( 'A practice rooted in material, place, and observation.', 'art-zone-blank' ) ) ); ?></h1>
                <div class="about-page__copy">
                    <p><?php echo esc_html( art_zone_blank_mod( 'about_intro_paragraph_1', __( 'This artist lives and works from their studio, where daily practice shapes a body of work rooted in close observation of the natural world.', 'art-zone-blank' ) ) ); ?></p>
                    <p><?php echo esc_html( art_zone_blank_mod( 'about_intro_paragraph_2', __( 'The paintings grow from a sustained attention to materials — their weight, their surface, the way light moves across them over time.', 'art-zone-blank' ) ) ); ?></p>
                    <p><?php echo esc_html( art_zone_blank_mod( 'about_intro_paragraph_3', __( 'Each work begins in the studio but carries the memory of place: weather, season, and the specific silence of a chosen landscape.', 'art-zone-blank' ) ) ); ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-page__philosophy">
        <div class="section-shell about-page__philosophy-grid">
            <div>
                <h2 class="about-page__subhead about-page__subhead--italic"><?php echo esc_html( art_zone_blank_mod( 'about_philosophy_title', __( 'The Philosophy', 'art-zone-blank' ) ) ); ?></h2>
                <blockquote class="about-page__quote">
                    <?php echo esc_html( art_zone_blank_mod( 'about_philosophy_quote', __( '"I work with stone paints made through my own unique technology, searching for a language that can hold the power of nature on canvas."', 'art-zone-blank' ) ) ); ?>
                </blockquote>
            </div>
            <div class="about-page__detail">
                <div class="about-page__detail-media">
                    <img loading="lazy" decoding="async" src="<?php echo esc_url( art_zone_blank_media_mod_url( 'about_detail_image_url', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBj7dlP0cvdY6Pc-YILi0Ra4oaOt1Q9SVVYRf_Z3MOYcsRmjzUxutcAbofADKjetO1F7MEDokRzE5i7KFVDcl5NS3ANcuBsjcTI0FrDHr69vSJ69EkitIN33vYHxmf_4C1v22UrSvXwDIDaSigYwf4tp9weDagVGeliin6IwqMyA4-CmGZCgHxUddi9yHnUbMlol0BKQCbyPIShL7pkV99qY093wweRUHKcNUnvQunk2nUcVfemIvdzly98Ur9CwQ_O9LUES3hnwbA', 'az-editorial' ) ); ?>" alt="<?php esc_attr_e( 'Studio detail', 'art-zone-blank' ); ?>">
                </div>
                <p class="about-page__detail-caption"><?php echo esc_html( art_zone_blank_mod( 'about_detail_caption', __( 'Studio Detail: Stone Paint Process and Surface Work', 'art-zone-blank' ) ) ); ?></p>
            </div>
        </div>
    </section>

    <section class="about-page__studio section-shell">
        <div class="about-page__studio-head section-heading section-heading--simple">
            <div>
                <h2 class="section-title about-page__commission-title"><?php echo esc_html( art_zone_blank_mod( 'about_video_title', __( 'A small gallery from the studio.', 'art-zone-blank' ) ) ); ?></h2>
            </div>
        </div>
        <div class="about-page__studio-grid">
            <?php foreach ( $studio_items as $index => $item ) : ?>
                <article class="about-page__studio-entry <?php echo esc_attr( 0 === $index % 2 ? 'about-page__studio-entry--top' : 'about-page__studio-entry--bottom' ); ?>">
                    <?php $slider_images = ! empty( $item['images'] ) ? $item['images'] : array( $item['image'] ); ?>
                    <div class="about-page__studio-media">
                        <div class="about-page__studio-slider" data-slider-interval="3000">
                            <?php foreach ( $slider_images as $slide_index => $slide_image ) : ?>
                                <div class="about-page__studio-slide <?php echo esc_attr( 0 === $slide_index ? 'is-active' : '' ); ?>">
                                    <img src="<?php echo esc_url( $slide_image ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy" decoding="async">
                                </div>
                            <?php endforeach; ?>
                            <?php if ( count( $slider_images ) > 1 ) : ?>
                                <div class="about-page__studio-dots" aria-label="<?php esc_attr_e( 'Studio item image navigation', 'art-zone-blank' ); ?>">
                                    <?php foreach ( $slider_images as $slide_index => $slide_image ) : ?>
                                        <button type="button" class="about-page__studio-dot <?php echo esc_attr( 0 === $slide_index ? 'is-active' : '' ); ?>" data-slide-index="<?php echo esc_attr( $slide_index ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Show image %d', 'art-zone-blank' ), $slide_index + 1 ) ); ?>"></button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="about-page__studio-copy">
                        <p class="editorial-entry__eyebrow"><?php esc_html_e( 'Studio Note', 'art-zone-blank' ); ?></p>
                        <h3 class="editorial-entry__title"><?php echo esc_html( $item['title'] ); ?></h3>
                        <?php if ( ! empty( $item['subheading'] ) ) : ?>
                            <p class="editorial-entry__subheading"><?php echo esc_html( $item['subheading'] ); ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php
get_footer();
