<?php
get_header();
?>
<main id="primary" class="site-main home-page">
    <?php get_template_part( 'template-parts/home/hero' ); ?>
    <?php get_template_part( 'template-parts/home/artist' ); ?>
    <!-- <?php get_template_part( 'template-parts/home/collection' ); ?> -->
    <?php get_template_part( 'template-parts/home/collection-simple' ); ?>
    <?php get_template_part( 'template-parts/home/video-feature' ); ?>
    <?php get_template_part( 'template-parts/home/cta' ); ?>
</main>
<?php
get_footer();
