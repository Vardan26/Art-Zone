<?php

add_action(
	'customize_preview_init',
	function () {
		wp_add_inline_script(
			'customize-preview',
			"(function(){
				var map = {
					'art_zone_blank_brand_color_bg':   '--az-color-bg',
					'art_zone_blank_brand_color_text': '--az-color-text',
					'art_zone_blank_brand_color_dark': '--az-color-dark',
				};
				Object.keys(map).forEach(function(setting){
					wp.customize(setting, function(value){
						value.bind(function(newval){
							document.documentElement.style.setProperty(map[setting], newval || '');
						});
					});
				});
			}());"
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$bg   = get_theme_mod( 'art_zone_blank_brand_color_bg', '' );
		$text = get_theme_mod( 'art_zone_blank_brand_color_text', '' );
		$dark = get_theme_mod( 'art_zone_blank_brand_color_dark', '' );

		$props = array();

		if ( $bg ) {
			$props[] = '--az-color-bg: ' . esc_attr( $bg ) . ';';
		}

		if ( $text ) {
			$props[] = '--az-color-text: ' . esc_attr( $text ) . ';';
		}

		if ( $dark ) {
			$props[] = '--az-color-dark: ' . esc_attr( $dark ) . ';';
		}

		if ( empty( $props ) ) {
			return;
		}

		wp_add_inline_style( 'art-zone-blank-style', ':root { ' . implode( ' ', $props ) . ' }' );
	},
	20
);
