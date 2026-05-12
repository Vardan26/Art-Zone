<?php
/*
Template Name: Studio Page
*/

get_header();

$studio_items = art_zone_blank_get_studio_items();
?>
<main id="primary" class="site-main studio-page">
    <section class="editorial-hero studio-page__hero section-shell">
        <p class="section-eyebrow"><?php esc_html_e( 'Studio', 'art-zone-blank' ); ?></p>
        <h1 class="studio-page__title"><?php echo esc_html( get_the_title() ? get_the_title() : __( 'Studio', 'art-zone-blank' ) ); ?></h1>
        <div class="studio-page__intro">
            <?php
            while ( have_posts() ) :
                the_post();
                the_content();
            endwhile;
            ?>
        </div>
    </section>

    <section class="studio-page__entries section-shell" aria-label="<?php esc_attr_e( 'Studio journal', 'art-zone-blank' ); ?>">
        <?php if ( ! empty( $studio_items ) ) : ?>
            <?php foreach ( $studio_items as $index => $item ) : ?>
                <?php
                $layout   = 'full' === $item['layout'] ? 'full' : 'split';
                $reverse  = 'split' === $layout && $index % 2 === 1;
                $classes  = array( 'editorial-entry', 'editorial-entry--' . $layout );

                if ( $reverse ) {
                    $classes[] = 'editorial-entry--reverse';
                }
                ?>
                <article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
                    <div class="editorial-entry__media">
                        <img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" loading="lazy" decoding="async">
                    </div>
                    <div class="editorial-entry__copy">
                        <p class="editorial-entry__eyebrow"><?php esc_html_e( 'Studio Note', 'art-zone-blank' ); ?></p>
                        <h2 class="editorial-entry__title"><?php echo esc_html( $item['title'] ); ?></h2>
                        <?php if ( ! empty( $item['subheading'] ) ) : ?>
                            <p class="editorial-entry__subheading"><?php echo esc_html( $item['subheading'] ); ?></p>
                        <?php endif; ?>
                        <div class="editorial-entry__body"><?php echo wp_kses_post( $item['content'] ); ?></div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else : ?>
            <article class="editorial-entry editorial-entry--empty">
                <div class="editorial-entry__copy">
                    <p class="editorial-entry__eyebrow"><?php esc_html_e( 'Studio', 'art-zone-blank' ); ?></p>
                    <h2 class="editorial-entry__title"><?php esc_html_e( 'No studio items yet', 'art-zone-blank' ); ?></h2>
                    <p class="editorial-entry__subheading"><?php esc_html_e( 'Add items from the Studio Items section in the dashboard to build this page.', 'art-zone-blank' ); ?></p>
                </div>
            </article>
        <?php endif; ?>
    </section>
</main>
<?php
get_footer();
