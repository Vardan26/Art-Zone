<?php
/*
Template Name: Art Therapy Page
*/

get_header();

$hero_video_url = art_zone_blank_media_mod_url( 'art_therapy_hero_video_url', '' );
$audio_url      = art_zone_blank_media_mod_url( 'art_therapy_audio_url', '' );
$hero_title     = art_zone_blank_mod( 'art_therapy_title', __( 'Art Therapy', 'art-zone-blank' ) );
$therapy_items  = art_zone_blank_get_art_therapy_items();
$fallback_image = art_zone_blank_media_mod_url( 'about_detail_image_url', '', 'az-editorial' );
$visuals        = array_values(
    array_filter(
        array_map(
            function ( $item ) {
                return ! empty( $item['image'] ) ? $item['image'] : '';
            },
            $therapy_items
        )
    )
);

if ( empty( $visuals ) && $fallback_image ) {
    $visuals[] = $fallback_image;
}
?>
<main id="primary" class="site-main art-therapy-page">
    <section class="art-therapy-page__hero">
        <div class="editorial-hero editorial-hero--flush editorial-hero--viewport art-therapy-page__hero-grid">
            <div class="editorial-hero__media art-therapy-page__hero-media">
                <?php if ( $hero_video_url ) : ?>
                    <video autoplay muted loop playsinline preload="metadata" aria-label="<?php esc_attr_e( 'Art therapy hero video', 'art-zone-blank' ); ?>">
                        <source src="<?php echo esc_url( $hero_video_url ); ?>">
                    </video>
                <?php elseif ( ! empty( $visuals[0] ) ) : ?>
                    <img src="<?php echo esc_url( $visuals[0] ); ?>" alt="<?php esc_attr_e( 'Art therapy visual', 'art-zone-blank' ); ?>">
                <?php endif; ?>

                <?php if ( $audio_url ) : ?>
                    <audio class="art-therapy-page__audio" autoplay muted loop preload="auto">
                        <source src="<?php echo esc_url( $audio_url ); ?>">
                    </audio>
                    <button type="button" class="art-therapy-page__audio-toggle" data-audio-toggle aria-pressed="false" aria-label="<?php esc_attr_e( 'Enable audio', 'art-zone-blank' ); ?>">
                        <span class="art-therapy-page__audio-toggle-icon" aria-hidden="true">
                            <?php echo art_zone_blank_icon( 'volume-xmark' ); ?>
                        </span>
                    </button>
                <?php endif; ?>
            </div>
            <div class="editorial-hero__panel art-therapy-page__hero-panel">
                <div class="art-therapy-page__hero-copy">
                    <h1 class="art-therapy-page__title"><?php echo esc_html( $hero_title ? $hero_title : ( get_the_title() ? get_the_title() : __( 'Art Therapy', 'art-zone-blank' ) ) ); ?></h1>
                    <div class="art-therapy-page__intro">
                        <?php
                        while ( have_posts() ) :
                            the_post();
                            the_content();
                        endwhile;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="art-therapy-page__entries section-shell">
        <?php if ( ! empty( $therapy_items ) ) : ?>
            <?php foreach ( $therapy_items as $index => $section ) : ?>
                <?php
                $layout   = ! empty( $section['layout'] ) ? $section['layout'] : 'split';
                $reverse  = 'split' === $layout && $index % 2 === 1;
                $classes  = array( 'editorial-entry', 'editorial-entry--' . $layout );

                if ( $reverse ) {
                    $classes[] = 'editorial-entry--reverse';
                }

                $visual = ! empty( $section['image'] ) ? $section['image'] : ( ! empty( $visuals[ $index ] ) ? $visuals[ $index ] : $fallback_image );
                ?>
                <article class="<?php echo esc_attr( implode( ' ', array_merge( $classes, array( 'art-therapy-page__entry' ) ) ) ); ?>">
                    <div class="editorial-entry__media">
                        <?php if ( $visual ) : ?>
                            <img src="<?php echo esc_url( $visual ); ?>" alt="<?php echo esc_attr( $section['title'] ); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="editorial-entry__copy art-therapy-page__entry-copy">
                        <h2 class="editorial-entry__title"><?php echo esc_html( $section['title'] ); ?></h2>
                        <div class="editorial-entry__body"><?php echo wp_kses_post( $section['content'] ); ?></div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else : ?>
            <article class="editorial-entry editorial-entry--full">
                <div class="editorial-entry__copy">
                    <h2 class="editorial-entry__title"><?php esc_html_e( 'No Art Therapy items yet', 'art-zone-blank' ); ?></h2>
                    <p class="editorial-entry__subheading"><?php esc_html_e( 'Add entries in the Art Therapy Items section of the dashboard to build this page.', 'art-zone-blank' ); ?></p>
                </div>
            </article>
        <?php endif; ?>
    </section>
</main>
<?php
get_footer();
