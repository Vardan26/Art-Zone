<?php
$video_label     = art_zone_blank_mod( 'home_video_label', __( 'Studio Motion', 'art-zone-blank' ) );
$video_title     = art_zone_blank_mod( 'home_video_title', __( 'A moving glimpse into the studio atmosphere.', 'art-zone-blank' ) );
$video_text      = art_zone_blank_mod( 'home_video_text', __( 'A quiet moving image can hold the same material presence as a still surface. Use this section for process, atmosphere, or a fragment of the artist at work.', 'art-zone-blank' ) );
$video_link_text = art_zone_blank_mod( 'home_video_link_text', __( 'Discover the studio', 'art-zone-blank' ) );
$video_link_url  = art_zone_blank_mod( 'contact_social_youtube_url', '' );
$video_url       = art_zone_blank_media_mod_url( 'home_video_url', '' );
$video_url_webm  = art_zone_blank_media_mod_url( 'home_video_url_webm', '' );

if ( '' === trim( (string) $video_url ) ) {
    return;
}
?>
<section class="home-video-feature">
    <div class="section-shell home-video-feature__inner">
        <div class="home-video-feature__content">
            <p class="section-eyebrow"><?php echo esc_html( $video_label ); ?></p>
            <h2 class="section-title"><?php echo esc_html( $video_title ); ?></h2>
            <div class="home-artist__rule" aria-hidden="true"></div>
            <div class="home-video-feature__body">
                <p class="home-artist__bio"><?php echo esc_html( $video_text ); ?></p>
            </div>
            <?php if ( ! empty( $video_link_url ) ) : ?>
                <a class="text-link" href="<?php echo esc_url( $video_link_url ); ?>" target="_blank" rel="noopener noreferrer">
                    <?php echo esc_html( $video_link_text ); ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="home-video-feature__media">
            <div class="home-video-feature__frame">
                <video
                    class="home-video-feature__video"
                    muted
                    loop
                    playsinline
                    preload="none"
                    data-lazy-video
                >
                    <?php if ( $video_url_webm ) : ?>
                        <source data-src="<?php echo esc_url( $video_url_webm ); ?>" type="video/webm">
                    <?php endif; ?>
                    <source data-src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
                </video>
            </div>
            <div class="home-video-feature__shadow" aria-hidden="true"></div>
        </div>
    </div>
</section>
