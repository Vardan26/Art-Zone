<?php
/*
Template Name: Contact Page
*/

get_header();

$hero_image = art_zone_blank_media_mod_url( 'contact_hero_image_url', 'assets/placeholders/detail.jpg', 'az-hero' );
$hero_text  = art_zone_blank_mod( 'contact_hero_kicker', __( 'Let’s talk about my next project.', 'art-zone-blank' ) );
$phone      = art_zone_blank_mod( 'contact_phone', __( '+374 10 123 456', 'art-zone-blank' ) );
$email      = art_zone_blank_mod( 'contact_email', __( 'studio@example.com', 'art-zone-blank' ) );
$address_1  = array(
    'label' => art_zone_blank_mod( 'contact_address_1_label', __( 'Studio', 'art-zone-blank' ) ),
    'text'  => art_zone_blank_mod( 'contact_address_1_text', __( '15 Abovyan St, Yerevan 0001, Armenia', 'art-zone-blank' ) ),
    'lat'   => (float) art_zone_blank_mod( 'contact_address_1_lat', '40.1824244411345' ),
    'lng'   => (float) art_zone_blank_mod( 'contact_address_1_lng', '44.517204082032904' ),
);
$address_2  = array(
    'label' => art_zone_blank_mod( 'contact_address_2_label', '' ),
    'text'  => art_zone_blank_mod( 'contact_address_2_text', '' ),
    'lat'   => (float) art_zone_blank_mod( 'contact_address_2_lat', '0' ),
    'lng'   => (float) art_zone_blank_mod( 'contact_address_2_lng', '0' ),
);
$map_url     = art_zone_blank_mod( 'contact_map_url', '' );
$socials    = array_filter(
    array(
        array(
            'label' => __( 'Instagram', 'art-zone-blank' ),
            'url'   => art_zone_blank_mod( 'contact_social_instagram_url', '' ),
            'icon'  => 'instagram',
        ),
        array(
            'label' => __( 'Facebook', 'art-zone-blank' ),
            'url'   => art_zone_blank_mod( 'contact_social_facebook_url', '' ),
            'icon'  => 'facebook',
        ),
        array(
            'label' => __( 'YouTube', 'art-zone-blank' ),
            'url'   => art_zone_blank_mod( 'contact_social_youtube_url', '' ),
            'icon'  => 'youtube',
        ),
        array(
            'label' => __( 'WhatsApp', 'art-zone-blank' ),
            'url'   => art_zone_blank_mod( 'contact_social_whatsapp_url', '' ),
            'icon'  => 'whatsapp',
        ),
    ),
    function ( $item ) {
        return ! empty( $item['url'] );
    }
);
$map_markers = array_values(
    array_filter(
        array( $address_1, $address_2 ),
        function ( $address ) {
            return ! empty( $address['lat'] ) && ! empty( $address['lng'] );
        }
    )
);
?>
<main id="primary" class="site-main contact-page">
    <section class="contact-page__hero">
        <div class="contact-page__hero-shell">
            <div class="contact-page__panel-grid">
                <div class="contact-page__hero-card">
                    <?php if ( $hero_image ) : ?>
                        <img class="contact-page__hero-image" src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( get_the_title() ? get_the_title() : __( 'Contact Us', 'art-zone-blank' ) ); ?>" fetchpriority="high" decoding="async">
                    <?php endif; ?>
                    <div class="contact-page__hero-overlay" aria-hidden="true"></div>
                </div>
    
                <div class="contact-page__details">
                    <?php if ( ! empty( $hero_text ) ) : ?>
                        <p class="section-title contact-page__panel-kicker"><?php echo esc_html( $hero_text ); ?></p>
                    <?php endif; ?>
                    <div class="contact-page__details-group">
                        <div class="contact-page__detail-card">
                            <p class="contact-page__detail-label"><?php echo art_zone_blank_icon( 'phone' ); ?><span><?php esc_html_e( 'Phone', 'art-zone-blank' ); ?></span></p>
                            <a class="contact-page__detail-value" href="<?php echo esc_url( 'tel:' . preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
                        </div>
                        <div class="contact-page__detail-card">
                            <p class="contact-page__detail-label"><?php echo art_zone_blank_icon( 'envelope' ); ?><span><?php esc_html_e( 'Email', 'art-zone-blank' ); ?></span></p>
                            <a class="contact-page__detail-value" href="<?php echo esc_url( 'mailto:' . antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
                        </div>
                        <?php foreach ( array( $address_1, $address_2 ) as $address ) : ?>
                            <?php if ( ! empty( $address['text'] ) ) : ?>
                                <div class="contact-page__detail-card">
                                    <p class="contact-page__detail-label"><?php echo art_zone_blank_icon( 'location-dot' ); ?><span><?php echo esc_html( $address['label'] ); ?></span></p>
                                    <p class="contact-page__location-text"><?php echo esc_html( $address['text'] ); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ( ! empty( $socials ) ) : ?>
            <div class="contact-page__socials contact-page__socials--band">
                <div class="section-shell">
                    <div class="contact-page__social-list contact-page__social-list--band">
                        <?php foreach ( $socials as $social ) : ?>
                            <a class="contact-page__social-link contact-page__social-link--band" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo art_zone_blank_icon( $social['icon'] ); ?>
                                <span><?php echo esc_html( $social['label'] ); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <section class="contact-page__locations section-shell">
        <div class="contact-page__location-map">
            <?php if ( ! empty( $map_markers ) ) : ?>
                <div
                    class="contact-page__map-canvas"
                    data-map-markers="<?php echo esc_attr( wp_json_encode( $map_markers ) ); ?>"
                    aria-label="<?php esc_attr_e( 'Studio locations map', 'art-zone-blank' ); ?>"
                ></div>
            <?php else : ?>
                <iframe
                    src="<?php echo esc_url( $map_url ); ?>"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen
                    title="<?php esc_attr_e( 'Studio locations map', 'art-zone-blank' ); ?>"
                ></iframe>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
