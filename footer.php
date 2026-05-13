<footer class="site-footer" id="contact">
  <div class="section-shell site-footer__inner">
    <div class="site-footer__brand-block">
      <?php
      $brand_name       = art_zone_blank_mod( 'artist_name', get_bloginfo( 'name' ) );
      $footer_logo      = art_zone_blank_media_mod_url( 'brand_logo_short', '' );
      $footer_home_url  = art_zone_blank_home_url();
      ?>
      <p class="site-footer__brand">
        <a href="<?php echo esc_url( $footer_home_url ); ?>">
          <?php if ( $footer_logo ) : ?>
            <img class="site-footer__brand-image site-footer__brand-image--short" src="<?php echo esc_url( $footer_logo ); ?>" alt="<?php echo esc_attr( $brand_name ); ?>">
          <?php else : ?>
            <?php echo esc_html( $brand_name ); ?>
          <?php endif; ?>
        </a>
      </p>
      <p class="site-footer__copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'art-zone-blank' ); ?></p>
    </div>
    <nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer Menu', 'art-zone-blank' ); ?>">
      <?php
      wp_nav_menu(
          array(
              'theme_location' => 'footer',
              'container'      => false,
              'menu_class'     => 'footer-menu',
              'fallback_cb'    => function () {
                  $facebook_url  = art_zone_blank_mod( 'contact_social_facebook_url', '' );
                  $instagram_url = art_zone_blank_mod( 'contact_social_instagram_url', '' );
                  $portfolio_url = art_zone_blank_portfolio_url( '#collection' );
                  $contact_url   = art_zone_blank_contact_url( '#' );

                  echo '<ul class="footer-menu">';
                  echo '<li><a href="' . esc_url( $facebook_url ? $facebook_url : art_zone_blank_home_url() ) . '"' . ( $facebook_url ? ' target="_blank" rel="noopener noreferrer"' : '' ) . '>' . esc_html__( 'Facebook', 'art-zone-blank' ) . '</a></li>';
                  echo '<li><a href="' . esc_url( $instagram_url ? $instagram_url : art_zone_blank_home_url() ) . '"' . ( $instagram_url ? ' target="_blank" rel="noopener noreferrer"' : '' ) . '>' . esc_html__( 'Instagram', 'art-zone-blank' ) . '</a></li>';
                  echo '<li><a href="' . esc_url( $portfolio_url ) . '">' . esc_html__( 'Portfolio', 'art-zone-blank' ) . '</a></li>';
                  echo '<li><a href="' . esc_url( $contact_url ) . '">' . esc_html__( 'Contact', 'art-zone-blank' ) . '</a></li>';
                  echo '</ul>';
              },
          )
      );
      ?>
    </nav>
    <?php $footer_location = art_zone_blank_mod( 'footer_location', '' ); ?>
    <?php if ( $footer_location ) : ?>
      <p class="site-footer__location"><?php echo esc_html( $footer_location ); ?></p>
    <?php endif; ?>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
