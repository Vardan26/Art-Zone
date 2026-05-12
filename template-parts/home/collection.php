<?php
$works = art_zone_blank_featured_works();
?>
<section class="home-collection section-shell" id="collection">
    <div class="section-heading">
        <div>
            <p class="section-eyebrow"><?php esc_html_e( 'Selected Works', 'art-zone-blank' ); ?></p>
            <h2 class="section-title"><?php echo esc_html( art_zone_blank_mod( 'collection_title', __( 'Featured Collection', 'art-zone-blank' ) ) ); ?></h2>
        </div>
        <div class="section-heading__line" aria-hidden="true"></div>
        <p class="section-meta"><?php echo esc_html( art_zone_blank_mod( 'collection_years', __( '2022 - 2024', 'art-zone-blank' ) ) ); ?></p>
    </div>
    <div class="works-grid">
        <?php foreach ( $works as $index => $work ) : ?>
            <?php
            $has_image = ! empty( $work['image'] );
            $link      = ! empty( $work['permalink'] ) ? $work['permalink'] : '#';
            ?>
            <article class="work-card work-card--<?php echo esc_attr( $work['size'] ? $work['size'] : ( ( $index % 6 ) + 1 ) ); ?>">
                <a
                    class="work-card__media<?php echo $has_image ? ' work-card__media--image' : ''; ?>"
                    href="<?php echo esc_url( $link ); ?>"
                    <?php if ( $has_image ) : ?>
                        style="<?php echo esc_attr( 'background-image:url(' . esc_url_raw( $work['image'] ) . ');' ); ?>"
                    <?php endif; ?>
                >
                    <?php if ( ! $has_image ) : ?>
                        <span class="work-card__placeholder" aria-hidden="true"><?php echo esc_html( $index + 1 ); ?></span>
                    <?php endif; ?>
                </a>
                <div class="work-card__meta">
                    <h3 class="work-card__title">
                        <a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $work['title'] ); ?></a>
                    </h3>
                    <div class="work-card__details">
                        <span><?php echo esc_html( $work['medium'] ); ?></span>
                        <span><?php echo esc_html( $work['year'] ); ?></span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
