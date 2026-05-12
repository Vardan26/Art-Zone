<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="az-page-loader" aria-hidden="true"></div>
<?php $header_classes = array( 'site-header' ); ?>
<?php $language_items = art_zone_blank_language_switcher_items(); ?>
<?php $brand_name = art_zone_blank_mod( 'artist_name', get_bloginfo( 'name' ) ); ?>
<?php $header_logo = art_zone_blank_media_mod_url( 'brand_logo_long', '' ); ?>
<?php if ( is_front_page() ) : ?>
  <?php $header_classes[] = 'site-header--home-overlay'; ?>
<?php endif; ?>
<header class="<?php echo esc_attr( implode( ' ', $header_classes ) ); ?>">
  <div class="section-shell site-header__inner">
    <p class="site-header__brand">
      <a href="<?php echo esc_url( art_zone_blank_home_url() ); ?>">
        <?php if ( $header_logo ) : ?>
          <img class="site-header__brand-image site-header__brand-image--long" src="<?php echo esc_url( $header_logo ); ?>" alt="<?php echo esc_attr( $brand_name ); ?>">
        <?php else : ?>
          <?php echo esc_html( $brand_name ); ?>
        <?php endif; ?>
      </a>
    </p>
    <nav id="site-header-navigation" class="site-header__nav" aria-label="<?php esc_attr_e( 'Primary Menu', 'art-zone-blank' ); ?>">
      <?php
      wp_nav_menu(
          array(
              'theme_location' => 'primary',
              'container'      => false,
              'menu_class'     => 'site-menu',
              'fallback_cb'    => function () {
                  $home_url      = art_zone_blank_home_url();
                  $portfolio_url = art_zone_blank_portfolio_url( '#collection' );
                  $contact_url      = art_zone_blank_contact_url( '#' );
                  $about_url     = art_zone_blank_about_url( '#artist' );
                  $therapy_url   = art_zone_blank_art_therapy_url( '#contact' );
                  echo '<ul class="site-menu">';
                  echo '<li><a href="' . esc_url( $home_url ) . '">' . esc_html__( 'Home', 'art-zone-blank' ) . '</a></li>';
                  echo '<li><a href="' . esc_url( $portfolio_url ) . '">' . esc_html__( 'Portfolio', 'art-zone-blank' ) . '</a></li>';
                  // echo '<li><a href="' . esc_url( $blog_url ) . '">' . esc_html__( 'Blog', 'art-zone-blank' ) . '</a></li>';
                  echo '<li><a href="' . esc_url( $about_url ) . '">' . esc_html__( 'About', 'art-zone-blank' ) . '</a></li>';
                  echo '<li><a href="' . esc_url( $therapy_url ) . '">' . esc_html__( 'Art Therapy', 'art-zone-blank' ) . '</a></li>';
                  echo '<li><a href="' . esc_url( $contact_url ) . '">' . esc_html__( 'Contacts', 'art-zone-blank' ) . '</a></li>';
                  echo '</ul>';
              },
          )
      );
      ?>
      <?php if ( ! empty( $language_items ) ) : ?>
        <div class="site-header__lang" aria-label="<?php esc_attr_e( 'Language selector', 'art-zone-blank' ); ?>">
          <?php foreach ( $language_items as $language_item ) : ?>
            <a
              class="site-header__lang-link<?php echo $language_item['current'] ? ' is-current' : ''; ?>"
              href="<?php echo esc_url( $language_item['url'] ); ?>"
              lang="<?php echo esc_attr( $language_item['slug'] ); ?>"
              hreflang="<?php echo esc_attr( $language_item['slug'] ); ?>"
              <?php echo $language_item['current'] ? 'aria-current="true"' : ''; ?>
            >
              <?php echo esc_html( $language_item['label'] ); ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </nav>
    <button class="site-header__menu-toggle" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'art-zone-blank' ); ?>" aria-expanded="false" aria-controls="site-header-navigation" data-menu-toggle>
      <span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'art-zone-blank' ); ?></span>
      <span class="site-header__menu-icon" aria-hidden="true"></span>
    </button>
  </div>
</header>
