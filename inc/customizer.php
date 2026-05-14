<?php

add_action(
    'customize_register',
    function ( $wp_customize ) {
        $site_identity_settings = array(
            'brand_logo_long'  => array(
                'label' => __( 'Long Logo', 'art-zone-blank' ),
                'type'  => 'image',
            ),
            'brand_logo_short' => array(
                'label' => __( 'Short Logo', 'art-zone-blank' ),
                'type'  => 'image',
            ),
        );

        foreach ( $site_identity_settings as $setting_id => $config ) {
            $wp_customize->add_setting(
                'art_zone_blank_' . $setting_id,
                array(
                    'default'           => '',
                    'sanitize_callback' => 'art_zone_blank_sanitize_media_value',
                )
            );

            $wp_customize->add_control(
                new WP_Customize_Image_Control(
                    $wp_customize,
                    'art_zone_blank_' . $setting_id,
                    array(
                        'label'   => $config['label'],
                        'section' => 'title_tagline',
                    )
                )
            );
        }

        $wp_customize->add_section(
            'art_zone_blank_colors',
            array(
                'title'    => __( 'Brand Colors', 'art-zone-blank' ),
                'priority' => 25,
            )
        );

        $color_settings = array(
            'brand_color_bg'   => array(
                'label'   => __( 'Background Color', 'art-zone-blank' ),
                'default' => '',
            ),
            'brand_color_text' => array(
                'label'   => __( 'Text Color', 'art-zone-blank' ),
                'default' => '',
            ),
            'brand_color_dark' => array(
                'label'   => __( 'Accent / Button Color', 'art-zone-blank' ),
                'default' => '',
            ),
        );

        foreach ( $color_settings as $setting_id => $config ) {
            $wp_customize->add_setting(
                'art_zone_blank_' . $setting_id,
                array(
                    'default'           => $config['default'],
                    'sanitize_callback' => 'sanitize_hex_color',
                    'transport'         => 'postMessage',
                )
            );

            $wp_customize->add_control(
                new WP_Customize_Color_Control(
                    $wp_customize,
                    'art_zone_blank_' . $setting_id,
                    array(
                        'label'   => $config['label'],
                        'section' => 'art_zone_blank_colors',
                    )
                )
            );
        }

        $wp_customize->add_section(
            'art_zone_blank_home',
            array(
                'title'    => __( 'Homepage', 'art-zone-blank' ),
                'priority' => 30,
            )
        );

        $wp_customize->add_section(
            'art_zone_blank_about',
            array(
                'title'    => __( 'About Page', 'art-zone-blank' ),
                'priority' => 31,
            )
        );

        $wp_customize->add_section(
            'art_zone_blank_portfolio',
            array(
                'title'    => __( 'Portfolio Page', 'art-zone-blank' ),
                'priority' => 32,
            )
        );

        $wp_customize->add_section(
            'art_zone_blank_art_therapy',
            array(
                'title'    => __( 'Art Therapy Page', 'art-zone-blank' ),
                'priority' => 33,
            )
        );

        $wp_customize->add_section(
            'art_zone_blank_contact',
            array(
                'title'    => __( 'Contact Page', 'art-zone-blank' ),
                'priority' => 34,
            )
        );

        $settings = array(
            'hero_title'       => array(
                'label'   => __( 'Hero Title', 'art-zone-blank' ),
                'default' => '',
            ),
            'hero_kicker'      => array(
                'label'   => __( 'Hero Kicker', 'art-zone-blank' ),
                'default' => __( 'Contemporary artist', 'art-zone-blank' ),
            ),
            'hero_button_text' => array(
                'label'   => __( 'Hero Button Text', 'art-zone-blank' ),
                'default' => __( 'View Portfolio', 'art-zone-blank' ),
            ),
            'hero_button_url'  => array(
                'label'   => __( 'Hero Button URL', 'art-zone-blank' ),
                'default' => '',
            ),
            'hero_image_url'   => array(
                'label'   => __( 'Hero Image', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'image',
            ),
            'hero_video_url'      => array(
                'label'   => __( 'Hero Background Video (MP4)', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'video',
            ),
            'hero_video_url_webm' => array(
                'label'   => __( 'Hero Background Video (WebM)', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'video',
            ),
            'collection_title' => array(
                'label'   => __( 'Collection Title', 'art-zone-blank' ),
                'default' => __( 'Featured Collection', 'art-zone-blank' ),
            ),
            'collection_years' => array(
                'label'   => __( 'Collection Years', 'art-zone-blank' ),
                'default' => '',
            ),
            'artist_name'      => array(
                'label'   => __( 'Artist Name', 'art-zone-blank' ),
                'default' => '',
            ),
            'artist_label'     => array(
                'label'   => __( 'Artist Eyebrow', 'art-zone-blank' ),
                'default' => __( 'The Artist', 'art-zone-blank' ),
            ),
            'artist_bio'       => array(
                'label'   => __( 'Artist Bio', 'art-zone-blank' ),
                'default' => __( 'This artist\'s practice is rooted in a sustained engagement with materials, place, and the painted surface. Their work is held in private collections and has been exhibited internationally.', 'art-zone-blank' ),
                'type'    => 'textarea',
            ),
            'artist_image_url' => array(
                'label'   => __( 'Artist Image', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'image',
            ),
            'artist_link_text' => array(
                'label'   => __( 'Artist Link Text', 'art-zone-blank' ),
                'default' => __( 'Learn more about the journey', 'art-zone-blank' ),
            ),
            'artist_link_url'  => array(
                'label'   => __( 'Artist Link URL', 'art-zone-blank' ),
                'default' => '',
            ),
            'home_video_label' => array(
                'label'   => __( 'Home Video Eyebrow', 'art-zone-blank' ),
                'default' => __( 'Studio Motion', 'art-zone-blank' ),
            ),
            'home_video_title' => array(
                'label'   => __( 'Home Video Title', 'art-zone-blank' ),
                'default' => __( 'A moving glimpse into the studio atmosphere.', 'art-zone-blank' ),
            ),
            'home_video_text'  => array(
                'label'   => __( 'Home Video Text', 'art-zone-blank' ),
                'default' => __( 'A quiet moving image can hold the same material presence as a still surface. Use this section for process, atmosphere, or a fragment of the artist at work.', 'art-zone-blank' ),
                'type'    => 'textarea',
            ),
            'home_video_link_text' => array(
                'label'   => __( 'Home Video Link Text', 'art-zone-blank' ),
                'default' => __( 'Discover the studio', 'art-zone-blank' ),
            ),
            'home_video_link_url'  => array(
                'label'   => __( 'Home Video Link URL', 'art-zone-blank' ),
                'default' => '',
            ),
            'home_video_url'      => array(
                'label'   => __( 'Home Studio Video (MP4)', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'video',
            ),
            'home_video_url_webm' => array(
                'label'   => __( 'Home Studio Video (WebM)', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'video',
            ),
            'cta_title'        => array(
                'label'   => __( 'CTA Title', 'art-zone-blank' ),
                'default' => __( 'Bring a vision to life.', 'art-zone-blank' ),
            ),
            'cta_text'         => array(
                'label'   => __( 'CTA Text', 'art-zone-blank' ),
                'default' => __( 'I accept a limited number of private and commercial commissions each year. Start a conversation about your next piece.', 'art-zone-blank' ),
                'type'    => 'textarea',
            ),
            'cta_button_text'  => array(
                'label'   => __( 'CTA Button Text', 'art-zone-blank' ),
                'default' => __( 'Request Commission', 'art-zone-blank' ),
            ),
            'cta_button_url'   => array(
                'label'   => __( 'CTA Button URL', 'art-zone-blank' ),
                'default' => '',
            ),
            'footer_location'  => array(
                'label'   => __( 'Footer Location', 'art-zone-blank' ),
                'default' => '',
            ),
        );

        foreach ( $settings as $setting_id => $config ) {
            $setting_type = isset( $config['type'] ) ? $config['type'] : 'text';
            $sanitize_cb  = 'textarea' === $setting_type ? 'sanitize_textarea_field' : 'sanitize_text_field';

            if ( in_array( $setting_type, array( 'image', 'video' ), true ) ) {
                $sanitize_cb = 'art_zone_blank_sanitize_media_value';
            } elseif ( str_ends_with( $setting_id, '_url' ) ) {
                $sanitize_cb = 'esc_url_raw';
            }

            $wp_customize->add_setting(
                'art_zone_blank_' . $setting_id,
                array(
                    'default'           => $config['default'],
                    'sanitize_callback' => $sanitize_cb,
                )
            );

            if ( 'image' === $setting_type ) {
                $wp_customize->add_control(
                    new WP_Customize_Image_Control(
                        $wp_customize,
                        'art_zone_blank_' . $setting_id,
                        array(
                            'label'   => $config['label'],
                            'section' => 'art_zone_blank_home',
                        )
                    )
                );
                continue;
            }

            if ( 'video' === $setting_type ) {
                $wp_customize->add_control(
                    new WP_Customize_Media_Control(
                        $wp_customize,
                        'art_zone_blank_' . $setting_id,
                        array(
                            'label'     => $config['label'],
                            'section'   => 'art_zone_blank_home',
                            'mime_type' => 'video',
                        )
                    )
                );
                continue;
            }

            $wp_customize->add_control(
                'art_zone_blank_' . $setting_id,
                array(
                    'label'   => $config['label'],
                    'section' => 'art_zone_blank_home',
                    'type'    => 'textarea' === $setting_type ? 'textarea' : 'text',
                )
            );
        }

        $about_settings = array(
            'about_eyebrow'           => array(
                'label'   => __( 'Hero Eyebrow', 'art-zone-blank' ),
                'default' => __( 'The Artist', 'art-zone-blank' ),
            ),
            'about_title'             => array(
                'label'   => __( 'Hero Title', 'art-zone-blank' ),
                'default' => __( 'A practice rooted in material, place, and observation.', 'art-zone-blank' ),
            ),
            'about_intro_paragraph_1' => array(
                'label'   => __( 'Hero Paragraph 1', 'art-zone-blank' ),
                'default' => __( 'This artist lives and works from their studio, where daily practice shapes a body of work rooted in close observation of the natural world.', 'art-zone-blank' ),
                'type'    => 'textarea',
            ),
            'about_intro_paragraph_2' => array(
                'label'   => __( 'Hero Paragraph 2', 'art-zone-blank' ),
                'default' => __( 'The paintings grow from a sustained attention to materials — their weight, their surface, the way light moves across them over time.', 'art-zone-blank' ),
                'type'    => 'textarea',
            ),
            'about_intro_paragraph_3' => array(
                'label'   => __( 'Hero Paragraph 3', 'art-zone-blank' ),
                'default' => __( 'Each work begins in the studio but carries the memory of place: weather, season, and the specific silence of a chosen landscape.', 'art-zone-blank' ),
                'type'    => 'textarea',
            ),
            'about_philosophy_title'  => array(
                'label'   => __( 'Philosophy Title', 'art-zone-blank' ),
                'default' => __( 'The Philosophy', 'art-zone-blank' ),
            ),
            'about_philosophy_quote'  => array(
                'label'   => __( 'Philosophy Quote', 'art-zone-blank' ),
                'default' => __( '"Paint is not a medium — it is a material with its own logic. My work begins when I stop trying to control it."', 'art-zone-blank' ),
                'type'    => 'textarea',
            ),
            'about_detail_caption'    => array(
                'label'   => __( 'Detail Caption', 'art-zone-blank' ),
                'default' => __( 'Studio Detail: Stone Paint Process and Surface Work', 'art-zone-blank' ),
            ),
            'about_video_eyebrow'     => array(
                'label'   => __( 'Video Section Eyebrow', 'art-zone-blank' ),
                'default' => __( 'Studio Videos', 'art-zone-blank' ),
            ),
            'about_video_title'       => array(
                'label'   => __( 'Video Section Title', 'art-zone-blank' ),
                'default' => __( 'Watch the studio process unfold.', 'art-zone-blank' ),
            ),
            'about_video_description' => array(
                'label'   => __( 'Video Section Description', 'art-zone-blank' ),
                'default' => __( 'A selection of video blocks can be used here to show painting sessions, material preparation, or short studio fragments.', 'art-zone-blank' ),
                'type'    => 'textarea',
            ),
            'about_portrait_image_url' => array(
                'label'   => __( 'Portrait Image', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'image',
            ),
            'about_detail_image_url' => array(
                'label'   => __( 'Detail Image', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'image',
            ),
            'about_feature_video_url' => array(
                'label'   => __( 'Feature Video', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'video',
            ),
        );

        foreach ( $about_settings as $setting_id => $config ) {
            $setting_type = isset( $config['type'] ) ? $config['type'] : 'text';
            $sanitize_cb  = 'textarea' === $setting_type ? 'sanitize_textarea_field' : 'sanitize_text_field';

            if ( in_array( $setting_type, array( 'url', 'image', 'video' ), true ) ) {
                $sanitize_cb = in_array( $setting_type, array( 'image', 'video' ), true ) ? 'art_zone_blank_sanitize_media_value' : 'esc_url_raw';
            } elseif ( str_ends_with( $setting_id, '_url' ) ) {
                $sanitize_cb = 'esc_url_raw';
            }

            $wp_customize->add_setting(
                'art_zone_blank_' . $setting_id,
                array(
                    'default'           => $config['default'],
                    'sanitize_callback' => $sanitize_cb,
                )
            );

            if ( 'image' === $setting_type ) {
                $wp_customize->add_control(
                    new WP_Customize_Image_Control(
                        $wp_customize,
                        'art_zone_blank_' . $setting_id,
                        array(
                            'label'   => $config['label'],
                            'section' => 'art_zone_blank_about',
                        )
                    )
                );
                continue;
            }

            if ( 'video' === $setting_type ) {
                $section = 'art_zone_blank_about';
                $label   = $config['label'];

                if ( 'about_feature_video_url' === $setting_id ) {
                    $section = 'art_zone_blank_home';
                    $label   = __( 'CTA Background Video', 'art-zone-blank' );
                }

                $wp_customize->add_control(
                    new WP_Customize_Media_Control(
                        $wp_customize,
                        'art_zone_blank_' . $setting_id,
                        array(
                            'label'     => $label,
                            'section'   => $section,
                            'mime_type' => 'video',
                        )
                    )
                );
                continue;
            }

            $wp_customize->add_control(
                'art_zone_blank_' . $setting_id,
                array(
                    'label'   => $config['label'],
                    'section' => 'art_zone_blank_about',
                    'type'    => 'textarea' === $setting_type ? 'textarea' : 'text',
                )
            );
        }

        $portfolio_settings = array(
            'portfolio_title'          => array(
                'label'   => __( 'Portfolio Heading', 'art-zone-blank' ),
                'default' => __( 'Works', 'art-zone-blank' ),
            ),
            'portfolio_all_label'      => array(
                'label'   => __( 'All Filter Label', 'art-zone-blank' ),
                'default' => __( 'All', 'art-zone-blank' ),
            ),
            'portfolio_count_template' => array(
                'label'   => __( 'Count Text Template', 'art-zone-blank' ),
                'default' => __( 'Showing %1$s of %2$s works', 'art-zone-blank' ),
            ),
        );

        foreach ( $portfolio_settings as $setting_id => $config ) {
            $setting_type = isset( $config['type'] ) ? $config['type'] : 'text';
            $sanitize_cb  = 'textarea' === $setting_type ? 'sanitize_textarea_field' : 'sanitize_text_field';

            if ( 'url' === $setting_type || str_ends_with( $setting_id, '_url' ) ) {
                $sanitize_cb = 'esc_url_raw';
            }

            $wp_customize->add_setting(
                'art_zone_blank_' . $setting_id,
                array(
                    'default'           => $config['default'],
                    'sanitize_callback' => $sanitize_cb,
                )
            );

            $wp_customize->add_control(
                'art_zone_blank_' . $setting_id,
                array(
                    'label'   => $config['label'],
                    'section' => 'art_zone_blank_portfolio',
                    'type'    => 'textarea' === $setting_type ? 'textarea' : ( 'url' === $setting_type ? 'url' : 'text' ),
                )
            );
        }

        $art_therapy_settings = array(
            'art_therapy_title' => array(
                'label'   => __( 'Hero Heading', 'art-zone-blank' ),
                'default' => __( 'Art Therapy', 'art-zone-blank' ),
            ),
        );

        foreach ( $art_therapy_settings as $setting_id => $config ) {
            $setting_type = isset( $config['type'] ) ? $config['type'] : 'text';
            $sanitize_cb  = 'textarea' === $setting_type ? 'sanitize_textarea_field' : 'sanitize_text_field';

            if ( 'url' === $setting_type || str_ends_with( $setting_id, '_url' ) ) {
                $sanitize_cb = 'esc_url_raw';
            }

            $wp_customize->add_setting(
                'art_zone_blank_' . $setting_id,
                array(
                    'default'           => $config['default'],
                    'sanitize_callback' => $sanitize_cb,
                )
            );

            $wp_customize->add_control(
                'art_zone_blank_' . $setting_id,
                array(
                    'label'   => $config['label'],
                    'section' => 'art_zone_blank_art_therapy',
                    'type'    => 'textarea' === $setting_type ? 'textarea' : ( 'url' === $setting_type ? 'url' : 'text' ),
                )
            );
        }

        $wp_customize->add_setting(
            'art_zone_blank_art_therapy_hero_video_url',
            array(
                'default'           => '',
                'sanitize_callback' => 'art_zone_blank_sanitize_media_value',
            )
        );

        $wp_customize->add_control(
            new WP_Customize_Media_Control(
                $wp_customize,
                'art_zone_blank_art_therapy_hero_video_url',
                array(
                    'label'     => __( 'Hero Video', 'art-zone-blank' ),
                    'section'   => 'art_zone_blank_art_therapy',
                    'mime_type' => 'video',
                )
            )
        );

        $wp_customize->add_setting(
            'art_zone_blank_art_therapy_audio_url',
            array(
                'default'           => '',
                'sanitize_callback' => 'art_zone_blank_sanitize_media_value',
            )
        );

        $wp_customize->add_control(
            new WP_Customize_Media_Control(
                $wp_customize,
                'art_zone_blank_art_therapy_audio_url',
                array(
                    'label'     => __( 'Background Audio', 'art-zone-blank' ),
                    'section'   => 'art_zone_blank_art_therapy',
                    'mime_type' => 'audio',
                )
            )
        );

        $contact_settings = array(
            'contact_hero_image_url'       => array(
                'label'   => __( 'Hero Image', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'image',
            ),
            'contact_hero_kicker'          => array(
                'label'   => __( 'Hero Kicker', 'art-zone-blank' ),
                'default' => __( 'Let’s talk about your next project.', 'art-zone-blank' ),
            ),
            'contact_phone'                => array(
                'label'   => __( 'Phone', 'art-zone-blank' ),
                'default' => __( '+374 10 123 456', 'art-zone-blank' ),
            ),
            'contact_email'                => array(
                'label'   => __( 'Email', 'art-zone-blank' ),
                'default' => __( 'studio@example.com', 'art-zone-blank' ),
            ),
            'contact_address_1_label'      => array(
                'label'   => __( 'Address 1 Label', 'art-zone-blank' ),
                'default' => __( 'Studio One', 'art-zone-blank' ),
            ),
            'contact_address_1_text'       => array(
                'label'   => __( 'Address 1', 'art-zone-blank' ),
                'default' => __( '15 Abovyan St, Yerevan 0001, Armenia', 'art-zone-blank' ),
                'type'    => 'textarea',
            ),
            'contact_address_1_lat'        => array(
                'label'   => __( 'Address 1 Latitude', 'art-zone-blank' ),
                'default' => '0',
            ),
            'contact_address_1_lng'        => array(
                'label'   => __( 'Address 1 Longitude', 'art-zone-blank' ),
                'default' => '0',
            ),
            'contact_address_2_label'      => array(
                'label'   => __( 'Address 2 Label', 'art-zone-blank' ),
                'default' => __( 'Studio Two', 'art-zone-blank' ),
            ),
            'contact_address_2_text'       => array(
                'label'   => __( 'Address 2', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'textarea',
            ),
            'contact_address_2_lat'        => array(
                'label'   => __( 'Address 2 Latitude', 'art-zone-blank' ),
                'default' => '0',
            ),
            'contact_address_2_lng'        => array(
                'label'   => __( 'Address 2 Longitude', 'art-zone-blank' ),
                'default' => '0',
            ),
            'contact_map_url'              => array(
                'label'   => __( 'Combined Map Embed URL', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'url',
            ),
            'contact_social_instagram_url' => array(
                'label'   => __( 'Instagram URL', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'url',
            ),
            'contact_social_facebook_url'  => array(
                'label'   => __( 'Facebook URL', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'url',
            ),
            'contact_social_youtube_url'   => array(
                'label'   => __( 'YouTube URL', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'url',
            ),
            'contact_social_whatsapp_url'  => array(
                'label'   => __( 'WhatsApp URL', 'art-zone-blank' ),
                'default' => '',
                'type'    => 'url',
            ),
        );

        foreach ( $contact_settings as $setting_id => $config ) {
            $setting_type = isset( $config['type'] ) ? $config['type'] : 'text';
            $sanitize_cb  = 'textarea' === $setting_type ? 'sanitize_textarea_field' : 'sanitize_text_field';

            if ( 'image' === $setting_type ) {
                $sanitize_cb = 'art_zone_blank_sanitize_media_value';
            } elseif ( 'url' === $setting_type || str_ends_with( $setting_id, '_url' ) ) {
                $sanitize_cb = 'esc_url_raw';
            }

            $wp_customize->add_setting(
                'art_zone_blank_' . $setting_id,
                array(
                    'default'           => $config['default'],
                    'sanitize_callback' => $sanitize_cb,
                )
            );

            if ( 'image' === $setting_type ) {
                $wp_customize->add_control(
                    new WP_Customize_Image_Control(
                        $wp_customize,
                        'art_zone_blank_' . $setting_id,
                        array(
                            'label'   => $config['label'],
                            'section' => 'art_zone_blank_contact',
                        )
                    )
                );
                continue;
            }

            $wp_customize->add_control(
                'art_zone_blank_' . $setting_id,
                array(
                    'label'   => $config['label'],
                    'section' => 'art_zone_blank_contact',
                    'type'    => 'textarea' === $setting_type ? 'textarea' : ( 'url' === $setting_type ? 'url' : 'text' ),
                )
            );
        }
    }
);
