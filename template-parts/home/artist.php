<?php
$artist_name      = art_zone_blank_mod( 'artist_name', __( 'Hayk Shahbazyan', 'art-zone-blank' ) );
$artist_label     = art_zone_blank_mod( 'artist_label', __( 'The Artist', 'art-zone-blank' ) );
$artist_bio       = art_zone_blank_mod( 'artist_bio', '' );
$artist_link_text = art_zone_blank_mod( 'artist_link_text', __( 'Learn more about the journey', 'art-zone-blank' ) );
$artist_link_url  = art_zone_blank_mod( 'artist_link_url', '' );
$artist_image_url = art_zone_blank_media_mod_url( 'artist_image_url', '', 'az-editorial' );

if ( '' === trim( (string) $artist_link_url ) || in_array( $artist_link_url, array( '#', '#artist', '/#artist' ), true ) ) {
    $artist_link_url = art_zone_blank_about_url( '#artist' );
}
?>
<section class="home-artist" id="artist">
    <div class="section-shell home-artist__inner">
        <div class="home-artist__portrait">
            <div class="home-artist__portrait-frame">
                <img
                    class="home-artist__portrait-image"
                    src="<?php echo esc_url( $artist_image_url ); ?>"
                    alt="<?php echo esc_attr( $artist_name ); ?>"
                    loading="lazy"
                    decoding="async"
                >
            </div>
            <div class="home-artist__portrait-shadow" aria-hidden="true"></div>
        </div>
        <div class="home-artist__content">
            <p class="section-eyebrow"><?php echo esc_html( $artist_label ); ?></p>
            <h2 class="section-title"><?php echo esc_html( $artist_name ); ?></h2>
            <div class="home-artist__rule" aria-hidden="true"></div>
            <div class="home-artist__body">
                <p class="home-artist__bio"><?php echo esc_html( $artist_bio ); ?></p>
            </div>
            <a class="text-link" href="<?php echo esc_url( $artist_link_url ); ?>">
                <?php echo esc_html( $artist_link_text ); ?>
            </a>
        </div>
    </div>
</section>
