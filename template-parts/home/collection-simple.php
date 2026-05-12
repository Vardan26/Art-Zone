<?php
$works = art_zone_blank_get_artwork_items( 3 );

if ( empty( $works ) ) {
    return;
}
?>
<section class="home-collection-simple section-shell" id="collection">
    <div class="section-heading section-heading--simple">
        <div class="home-collection-simple__heading">
            <h2 class="section-title"><?php esc_html_e( 'Collection', 'art-zone-blank' ); ?></h2>
        </div>
    </div>
    <div class="home-collection-simple__grid">
        <?php foreach ( $works as $work ) : ?>
            <article class="home-collection-simple__card">
                <a class="home-collection-simple__media" href="<?php echo esc_url( $work['permalink'] ); ?>">
                    <?php if ( ! empty( $work['image_id'] ) ) : ?>
                        <?php echo wp_get_attachment_image( $work['image_id'], 'az-collection', false, array(
                            'class'   => 'home-collection-simple__image',
                            'loading' => 'lazy',
                            'decoding' => 'async',
                            'alt'     => esc_attr( $work['title'] ),
                        ) ); ?>
                    <?php else : ?>
                        <img
                            class="home-collection-simple__image"
                            src="<?php echo esc_url( $work['image'] ); ?>"
                            alt="<?php echo esc_attr( $work['title'] ); ?>"
                            loading="lazy"
                            decoding="async"
                        >
                    <?php endif; ?>
                </a>
                <div class="home-collection-simple__meta">
                    <h3 class="home-collection-simple__title">
                        <a href="<?php echo esc_url( $work['permalink'] ); ?>"><?php echo esc_html( $work['title'] ); ?></a>
                    </h3>
                    <p class="home-collection-simple__details">
                        <?php echo esc_html( implode( ' - ', array_filter( array( $work['medium'], $work['year'] ) ) ) ); ?>
                    </p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
