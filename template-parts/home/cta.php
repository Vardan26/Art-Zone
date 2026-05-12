<?php
$cta_url = art_zone_blank_mod( 'cta_button_url', '' );
$cta_video_url = art_zone_blank_media_mod_url( 'about_feature_video_url', '' );

if ( '' === trim( (string) $cta_url ) || in_array( $cta_url, array( '#', '#contact', '/#contact' ), true ) ) {
    $cta_url = art_zone_blank_art_therapy_url( '#contact' );
}
?>
<section class="home-cta">
    <?php if ( ! empty( $cta_video_url ) ) : ?>
        <div class="home-cta__video-wrap" aria-hidden="true">
            <video class="home-cta__video" autoplay muted loop playsinline preload="metadata">
                <source src="<?php echo esc_url( $cta_video_url ); ?>">
            </video>
        </div>
    <?php endif; ?>
    <div class="section-shell home-cta__inner">
        <h2 class="home-cta__title"><?php echo esc_html( art_zone_blank_mod( 'cta_title', __( 'Bring a vision to life.', 'art-zone-blank' ) ) ); ?></h2>
        <p class="home-cta__text"><?php echo esc_html( art_zone_blank_mod( 'cta_text', '' ) ); ?></p>
        <a class="button button--light" href="<?php echo esc_url( $cta_url ); ?>">
            <?php echo esc_html( art_zone_blank_mod( 'cta_button_text', __( 'Request Commission', 'art-zone-blank' ) ) ); ?>
        </a>
    </div>
</section>
