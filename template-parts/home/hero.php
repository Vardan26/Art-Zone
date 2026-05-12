<?php
$hero_title            = art_zone_blank_mod( 'hero_title', get_bloginfo( 'name' ) );
$hero_kicker           = art_zone_blank_mod( 'hero_kicker', get_bloginfo( 'description' ) );
$hero_button_text      = art_zone_blank_mod( 'hero_button_text', '' );
$hero_button_url       = art_zone_blank_mod( 'hero_button_url', '' );
$hero_image_url        = art_zone_blank_media_mod_url( 'hero_image_url', '', 'az-hero' );
$hero_video_url        = art_zone_blank_media_mod_url( 'hero_video_url', '' );
$hero_video_url_webm   = art_zone_blank_media_mod_url( 'hero_video_url_webm', '' );
?>
<section class="home-hero" id="top">
    <div class="home-hero__backdrop" aria-hidden="true">
        <?php if ( $hero_video_url ) : ?>
            <video
                class="home-hero__video"
                autoplay
                muted
                loop
                playsinline
                preload="none"
                poster="<?php echo esc_url( $hero_image_url ); ?>"
            >
                <?php if ( $hero_video_url_webm ) : ?>
                    <source src="<?php echo esc_url( $hero_video_url_webm ); ?>" type="video/webm">
                <?php endif; ?>
                <source src="<?php echo esc_url( $hero_video_url ); ?>" type="video/mp4">
            </video>
        <?php elseif ( $hero_image_url ) : ?>
            <img
                class="home-hero__image"
                src="<?php echo esc_url( $hero_image_url ); ?>"
                alt="<?php esc_attr_e( 'Main hero artwork', 'art-zone-blank' ); ?>"
                fetchpriority="high"
                decoding="async"
            >
        <?php endif; ?>
    </div>
    <div class="home-hero__inner section-shell">
        <div class="home-hero__content">
            <h1 class="home-hero__title"><?php echo esc_html( $hero_title ); ?></h1>
            <p class="home-hero__copy"><?php echo esc_html( $hero_kicker ); ?></p>
            <?php if ( $hero_button_text && $hero_button_url ) : ?>
                <a class="button button--light home-hero__cta" href="<?php echo esc_url( $hero_button_url ); ?>">
                    <?php echo esc_html( $hero_button_text ); ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="home-hero__scroll-wrap">
            <a class="home-hero__scroll" href="#collection"><?php esc_html_e( 'Scroll to explore', 'art-zone-blank' ); ?></a>
        </div>
    </div>
</section>
