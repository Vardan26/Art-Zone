<?php

function art_zone_blank_get_admin_attachment_preview_url( $attachment_id ) {
    $attachment_id = (int) $attachment_id;

    if ( ! $attachment_id ) {
        return '';
    }

    foreach ( array( 'medium', 'large', 'full' ) as $size ) {
        $image_url = wp_get_attachment_image_url( $attachment_id, $size );

        if ( $image_url ) {
            return $image_url;
        }
    }

    $image_url = wp_get_attachment_url( $attachment_id );

    return $image_url ? $image_url : '';
}

add_action(
    'add_meta_boxes',
    function () {
        add_meta_box(
            'art-zone-blank-artwork-details',
            __( 'Artwork Details', 'art-zone-blank' ),
            function ( $post ) {
                wp_nonce_field( 'art_zone_blank_artwork_details', 'art_zone_blank_artwork_details_nonce' );
                $width_cm   = (float) get_post_meta( $post->ID, 'artwork_width_cm', true );
                $height_cm  = (float) get_post_meta( $post->ID, 'artwork_height_cm', true );
                $year       = get_post_meta( $post->ID, 'artwork_year', true );
                $size       = get_post_meta( $post->ID, 'artwork_gallery_size', true );
                $image      = get_post_meta( $post->ID, 'artwork_external_image', true );
                $image_id   = (int) get_post_meta( $post->ID, 'artwork_image_id', true );
                $series     = get_post_meta( $post->ID, 'artwork_series', true );
                $framing         = get_post_meta( $post->ID, 'artwork_framing', true );
                $framing_status  = get_post_meta( $post->ID, 'artwork_framing_status', true );
                if ( ! $framing_status ) {
                    $framing_status = 'framing_available';
                }
                $quote      = get_post_meta( $post->ID, 'artwork_quote', true );
                $palette    = get_post_meta( $post->ID, 'artwork_palette_note', true );
                $history    = get_post_meta( $post->ID, 'artwork_exhibition_history', true );
                $cta_url    = get_post_meta( $post->ID, 'artwork_enquiry_url', true );
                $image_url  = $image_id ? art_zone_blank_get_admin_attachment_preview_url( $image_id ) : '';

                if ( ! $image_url ) {
                    $featured_id = get_post_thumbnail_id( $post->ID );
                    $image_url   = $featured_id ? art_zone_blank_get_admin_attachment_preview_url( $featured_id ) : '';
                }

                if ( ! $image_url && $image ) {
                    $resolved_image = art_zone_blank_get_artwork_image( $post->ID );
                    $image_url      = $resolved_image ? $resolved_image : '';
                }
                ?>
                <div class="art-zone-media-picker" data-input="#artwork_image_id" data-frame-title="<?php echo esc_attr__( 'Select artwork image', 'art-zone-blank' ); ?>" data-button-text="<?php echo esc_attr__( 'Use image', 'art-zone-blank' ); ?>">
                    <p><strong><?php esc_html_e( 'Artwork Image', 'art-zone-blank' ); ?></strong></p>
                    <p class="description"><?php esc_html_e( 'Use Media Library for artwork images. This image will be used across the homepage, portfolio page, and artwork detail page.', 'art-zone-blank' ); ?></p>
                    <input type="hidden" id="artwork_image_id" name="artwork_image_id" value="<?php echo esc_attr( $image_id ); ?>">
                    <div class="art-zone-media-picker__preview">
                        <?php if ( $image_url ) : ?>
                            <img src="<?php echo esc_url( $image_url ); ?>" alt="" style="max-width:220px;height:auto;display:block;">
                        <?php endif; ?>
                    </div>
                    <p>
                        <button type="button" class="button art-zone-media-picker__select"><?php esc_html_e( 'Select Image', 'art-zone-blank' ); ?></button>
                        <button type="button" class="button-link art-zone-media-picker__clear"><?php esc_html_e( 'Remove', 'art-zone-blank' ); ?></button>
                    </p>
                </div>
                <p><label><?php esc_html_e( 'Dimensions (cm)', 'art-zone-blank' ); ?></label></p>
                <p style="display:flex;gap:12px;align-items:center;">
                    <span style="display:flex;align-items:center;gap:6px;">
                        <input id="artwork_width_cm" name="artwork_width_cm" type="number" min="0" step="0.1" style="width:90px;" value="<?php echo esc_attr( $width_cm > 0 ? $width_cm : '' ); ?>">
                        <label for="artwork_width_cm" style="margin:0;font-weight:normal;"><?php esc_html_e( 'W', 'art-zone-blank' ); ?></label>
                    </span>
                    <span style="color:#999;" aria-hidden="true">×</span>
                    <span style="display:flex;align-items:center;gap:6px;">
                        <input id="artwork_height_cm" name="artwork_height_cm" type="number" min="0" step="0.1" style="width:90px;" value="<?php echo esc_attr( $height_cm > 0 ? $height_cm : '' ); ?>">
                        <label for="artwork_height_cm" style="margin:0;font-weight:normal;"><?php esc_html_e( 'H', 'art-zone-blank' ); ?></label>
                    </span>
                </p>
                <p><label for="artwork_year"><?php esc_html_e( 'Year', 'art-zone-blank' ); ?></label></p>
                <p><input id="artwork_year" name="artwork_year" type="text" class="widefat" value="<?php echo esc_attr( $year ); ?>"></p>
                <p><label for="artwork_series"><?php esc_html_e( 'Series Label', 'art-zone-blank' ); ?></label></p>
                <p><input id="artwork_series" name="artwork_series" type="text" class="widefat" value="<?php echo esc_attr( $series ); ?>"></p>
                <p><label for="artwork_framing_status"><?php esc_html_e( 'Frame Status', 'art-zone-blank' ); ?></label></p>
                <p>
                    <select id="artwork_framing_status" name="artwork_framing_status" class="widefat">
                        <?php
                        $framing_status_options = array(
                            'framing_available' => __( 'Framing Available', 'art-zone-blank' ),
                            'no_frame'          => __( 'No Frame', 'art-zone-blank' ),
                            'with_frame'        => __( 'With Frame', 'art-zone-blank' ),
                            'not_applicable'    => __( 'Not Applicable', 'art-zone-blank' ),
                        );
                        foreach ( $framing_status_options as $value => $label ) :
                            ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $framing_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p><label for="artwork_framing"><?php esc_html_e( 'Framing Notes', 'art-zone-blank' ); ?></label></p>
                <p><input id="artwork_framing" name="artwork_framing" type="text" class="widefat" value="<?php echo esc_attr( $framing ); ?>"></p>
                <p><label for="artwork_gallery_size"><?php esc_html_e( 'Gallery Layout Size', 'art-zone-blank' ); ?></label></p>
                <p>
                    <select id="artwork_gallery_size" name="artwork_gallery_size" class="widefat">
                        <?php foreach ( array( 'feature', 'side', 'offset', 'tall', 'small' ) as $option ) : ?>
                            <option value="<?php echo esc_attr( $option ); ?>" <?php selected( $size, $option ); ?>><?php echo esc_html( ucfirst( $option ) ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p><label for="artwork_external_image"><?php esc_html_e( 'Fallback Image URL / Legacy Path', 'art-zone-blank' ); ?></label></p>
                <p><input id="artwork_external_image" name="artwork_external_image" type="url" class="widefat" value="<?php echo esc_attr( $image ); ?>"></p>
                <p><label for="artwork_enquiry_url"><?php esc_html_e( 'Enquiry URL', 'art-zone-blank' ); ?></label></p>
                <p><input id="artwork_enquiry_url" name="artwork_enquiry_url" type="url" class="widefat" value="<?php echo esc_attr( $cta_url ); ?>"></p>
                <p><label for="artwork_quote"><?php esc_html_e( 'Poetic Quote', 'art-zone-blank' ); ?></label></p>
                <p><textarea id="artwork_quote" name="artwork_quote" class="widefat" rows="3"><?php echo esc_textarea( $quote ); ?></textarea></p>
                <p><label for="artwork_palette_note"><?php esc_html_e( 'Palette Note', 'art-zone-blank' ); ?></label></p>
                <p><textarea id="artwork_palette_note" name="artwork_palette_note" class="widefat" rows="4"><?php echo esc_textarea( $palette ); ?></textarea></p>
                <p><label for="artwork_exhibition_history"><?php esc_html_e( 'Exhibition History', 'art-zone-blank' ); ?></label></p>
                <p><textarea id="artwork_exhibition_history" name="artwork_exhibition_history" class="widefat" rows="4"><?php echo esc_textarea( $history ); ?></textarea></p>
                <p class="description"><?php esc_html_e( 'Image priority: Artwork Image field, then Featured Image, then fallback legacy path/URL.', 'art-zone-blank' ); ?></p>
                <?php
            },
            'artwork',
            'normal',
            'default'
        );

        add_meta_box(
            'art-zone-blank-studio-details',
            __( 'Studio Item Details', 'art-zone-blank' ),
            function ( $post ) {
                wp_nonce_field( 'art_zone_blank_studio_details', 'art_zone_blank_studio_details_nonce' );
                $image_id    = (int) get_post_meta( $post->ID, 'studio_item_image_id', true );
                $gallery_ids = (string) get_post_meta( $post->ID, 'studio_item_gallery_ids', true );
                $gallery_url = art_zone_blank_media_ids_to_urls( $gallery_ids );
                $image_url   = $image_id ? art_zone_blank_get_admin_attachment_preview_url( $image_id ) : '';

                if ( ! $image_url ) {
                    $featured_id = get_post_thumbnail_id( $post->ID );
                    $image_url   = $featured_id ? art_zone_blank_get_admin_attachment_preview_url( $featured_id ) : '';
                }
                $layout = get_post_meta( $post->ID, 'studio_item_layout', true );
                ?>
                <div class="art-zone-media-picker" data-input="#studio_item_image_id" data-frame-title="<?php echo esc_attr__( 'Select studio image', 'art-zone-blank' ); ?>" data-button-text="<?php echo esc_attr__( 'Use image', 'art-zone-blank' ); ?>">
                    <p><strong><?php esc_html_e( 'Studio Image', 'art-zone-blank' ); ?></strong></p>
                    <p class="description"><?php esc_html_e( 'Fallback image for places that need one image only. If a slider gallery is set below, its first image will be used automatically where needed.', 'art-zone-blank' ); ?></p>
                    <input type="hidden" id="studio_item_image_id" name="studio_item_image_id" value="<?php echo esc_attr( $image_id ); ?>">
                    <div class="art-zone-media-picker__preview">
                        <?php if ( $image_url ) : ?>
                            <img src="<?php echo esc_url( $image_url ); ?>" alt="" style="max-width:220px;height:auto;display:block;">
                        <?php endif; ?>
                    </div>
                    <p>
                        <button type="button" class="button art-zone-media-picker__select"><?php esc_html_e( 'Select Image', 'art-zone-blank' ); ?></button>
                        <button type="button" class="button-link art-zone-media-picker__clear"><?php esc_html_e( 'Remove', 'art-zone-blank' ); ?></button>
                    </p>
                </div>
                <hr>
                <div class="art-zone-media-gallery-picker" data-input="#studio_item_gallery_ids" data-frame-title="<?php echo esc_attr__( 'Select studio slider images', 'art-zone-blank' ); ?>" data-button-text="<?php echo esc_attr__( 'Use images', 'art-zone-blank' ); ?>">
                    <p><strong><?php esc_html_e( 'Studio Slider Images', 'art-zone-blank' ); ?></strong></p>
                    <p class="description"><?php esc_html_e( 'Select up to 3 images for the About page slider behind this studio item. The first 3 selected images will be used.', 'art-zone-blank' ); ?></p>
                    <input type="hidden" id="studio_item_gallery_ids" name="studio_item_gallery_ids" value="<?php echo esc_attr( $gallery_ids ); ?>">
                    <div class="art-zone-media-gallery-picker__preview" style="display:flex;flex-wrap:wrap;gap:8px;">
                        <?php foreach ( array_slice( $gallery_url, 0, 3 ) as $url ) : ?>
                            <img src="<?php echo esc_url( $url ); ?>" alt="" style="width:92px;height:92px;object-fit:cover;display:block;">
                        <?php endforeach; ?>
                    </div>
                    <p>
                        <button type="button" class="button art-zone-media-gallery-picker__select"><?php esc_html_e( 'Select Images', 'art-zone-blank' ); ?></button>
                        <button type="button" class="button-link art-zone-media-gallery-picker__clear"><?php esc_html_e( 'Remove All', 'art-zone-blank' ); ?></button>
                    </p>
                </div>
                <p><label for="studio_item_layout"><?php esc_html_e( 'Layout Style', 'art-zone-blank' ); ?></label></p>
                <p>
                    <select id="studio_item_layout" name="studio_item_layout" class="widefat">
                        <option value="split" <?php selected( $layout, 'split' ); ?>><?php esc_html_e( 'Split', 'art-zone-blank' ); ?></option>
                        <option value="full" <?php selected( $layout, 'full' ); ?>><?php esc_html_e( 'Full Width', 'art-zone-blank' ); ?></option>
                    </select>
                </p>
                <p class="description"><?php esc_html_e( 'Use title as the small heading, excerpt as the subheading, and the main editor content as the body copy.', 'art-zone-blank' ); ?></p>
                <?php
            },
            'studio_item',
            'normal',
            'default'
        );

        add_meta_box(
            'art-zone-blank-artwork-interior-details',
            __( 'Interior Template Details', 'art-zone-blank' ),
            function ( $post ) {
                wp_nonce_field( 'art_zone_blank_artwork_interior_details', 'art_zone_blank_artwork_interior_details_nonce' );

                $image_id       = (int) get_post_meta( $post->ID, 'artwork_interior_image_id', true );
                $image_url_meta = get_post_meta( $post->ID, 'artwork_interior_background_url', true );
                $image_url      = $image_id ? art_zone_blank_get_admin_attachment_preview_url( $image_id ) : '';

                if ( ! $image_url && $image_url_meta ) {
                    $image_url = esc_url( $image_url_meta );
                }

                $scene_width   = get_post_meta( $post->ID, 'artwork_interior_scene_image_width_px', true );
                $scene_height  = get_post_meta( $post->ID, 'artwork_interior_scene_image_height_px', true );
                $real_width    = get_post_meta( $post->ID, 'artwork_interior_scene_real_width_cm', true );
                $real_height   = get_post_meta( $post->ID, 'artwork_interior_scene_real_height_cm', true );
                $slot_x        = get_post_meta( $post->ID, 'artwork_interior_slot_x_percent', true );
                $slot_y        = get_post_meta( $post->ID, 'artwork_interior_slot_y_percent', true );
                $slot_width    = get_post_meta( $post->ID, 'artwork_interior_slot_max_width_cm', true );
                $slot_height   = get_post_meta( $post->ID, 'artwork_interior_slot_max_height_cm', true );
                $slot_align_x  = get_post_meta( $post->ID, 'artwork_interior_slot_align_x', true );
                $slot_align_y  = get_post_meta( $post->ID, 'artwork_interior_slot_align_y', true );
                $orientations  = (array) get_post_meta( $post->ID, 'artwork_interior_orientations', true );
                $size_types    = (array) get_post_meta( $post->ID, 'artwork_interior_size_types', true );
                $room_type     = get_post_meta( $post->ID, 'artwork_interior_room_type', true );
                $size_group    = get_post_meta( $post->ID, 'artwork_interior_size_group', true );
                $sort_order    = get_post_meta( $post->ID, 'artwork_interior_sort_order', true );
                $bg_color      = get_post_meta( $post->ID, 'artwork_interior_background_color', true );
                $is_active        = get_post_meta( $post->ID, 'artwork_interior_is_active', true );
                $with_front_art   = get_post_meta( $post->ID, 'artwork_interior_with_front_art', true );
                $static_background = get_post_meta( $post->ID, 'artwork_interior_static_background', true );

                if ( empty( $size_types ) && $size_group ) {
                    $size_types = array( $size_group );
                }

                if ( '' === $scene_width ) {
                    $scene_width = get_post_meta( $post->ID, 'artwork_interior_scene_width', true );
                }

                if ( '' === $scene_height ) {
                    $scene_height = get_post_meta( $post->ID, 'artwork_interior_scene_height', true );
                }

                if ( '' === $slot_x && $scene_width ) {
                    $legacy_slot_x = (float) get_post_meta( $post->ID, 'artwork_interior_slot_x', true );
                    $slot_x        = $legacy_slot_x > 0 ? round( ( $legacy_slot_x / (float) $scene_width ) * 100, 2 ) : '';
                }

                if ( '' === $slot_y && $scene_height ) {
                    $legacy_slot_y = (float) get_post_meta( $post->ID, 'artwork_interior_slot_y', true );
                    $slot_y        = $legacy_slot_y > 0 ? round( ( $legacy_slot_y / (float) $scene_height ) * 100, 2 ) : '';
                }

                if ( '' === $slot_width && $real_width ) {
                    $legacy_slot_width_percent = get_post_meta( $post->ID, 'artwork_interior_slot_max_width_percent', true );
                    $slot_width                = '' !== $legacy_slot_width_percent ? round( ( (float) $legacy_slot_width_percent / 100 ) * (float) $real_width, 2 ) : '';
                }

                if ( '' === $slot_height && $real_height ) {
                    $legacy_slot_height_percent = get_post_meta( $post->ID, 'artwork_interior_slot_max_height_percent', true );
                    $slot_height                = '' !== $legacy_slot_height_percent ? round( ( (float) $legacy_slot_height_percent / 100 ) * (float) $real_height, 2 ) : '';
                }

                if ( '' === $slot_width && $scene_width ) {
                    $legacy_slot_width = (float) get_post_meta( $post->ID, 'artwork_interior_slot_width', true );
                    $slot_width        = $legacy_slot_width > 0 && $real_width ? round( ( $legacy_slot_width / (float) $scene_width ) * (float) $real_width, 2 ) : '';
                }

                if ( '' === $slot_height && $scene_height ) {
                    $legacy_slot_height = (float) get_post_meta( $post->ID, 'artwork_interior_slot_height', true );
                    $slot_height        = $legacy_slot_height > 0 && $real_height ? round( ( $legacy_slot_height / (float) $scene_height ) * (float) $real_height, 2 ) : '';
                }

                $slot_align_x = $slot_align_x ? $slot_align_x : 'center';
                $slot_align_y = $slot_align_y ? $slot_align_y : 'center';

                if ( '' === $is_active ) {
                    $is_active = '1';
                }

                if ( ! $bg_color ) {
                    $bg_color = '#e8dfd2';
                }
                ?>
                <div class="art-zone-media-picker" data-input="#artwork_interior_image_id" data-frame-title="<?php echo esc_attr__( 'Select interior background', 'art-zone-blank' ); ?>" data-button-text="<?php echo esc_attr__( 'Use background', 'art-zone-blank' ); ?>">
                    <p><strong><?php esc_html_e( 'Background Image', 'art-zone-blank' ); ?></strong></p>
                    <p class="description"><?php esc_html_e( 'Use a fixed scene image. Image size controls aspect ratio; real scene size controls proportional artwork scale.', 'art-zone-blank' ); ?></p>
                    <input type="hidden" id="artwork_interior_image_id" name="artwork_interior_image_id" value="<?php echo esc_attr( $image_id ); ?>">
                    <div class="art-zone-media-picker__preview">
                        <?php if ( $image_url ) : ?>
                            <img src="<?php echo esc_url( $image_url ); ?>" alt="" style="max-width:260px;height:auto;display:block;">
                        <?php endif; ?>
                    </div>
                    <p>
                        <button type="button" class="button art-zone-media-picker__select"><?php esc_html_e( 'Select Image', 'art-zone-blank' ); ?></button>
                        <button type="button" class="button-link art-zone-media-picker__clear"><?php esc_html_e( 'Remove', 'art-zone-blank' ); ?></button>
                    </p>
                </div>
                <p><label for="artwork_interior_background_url"><?php esc_html_e( 'Fallback Background URL', 'art-zone-blank' ); ?></label></p>
                <p><input id="artwork_interior_background_url" name="artwork_interior_background_url" type="url" class="widefat" value="<?php echo esc_attr( $image_url_meta ); ?>"></p>

                <p><label><?php esc_html_e( 'Source Image Size (pixels)', 'art-zone-blank' ); ?></label></p>
                <p style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                    <input name="artwork_interior_scene_image_width_px" type="number" min="1" step="1" placeholder="<?php echo esc_attr__( 'Width px', 'art-zone-blank' ); ?>" value="<?php echo esc_attr( $scene_width ); ?>" style="width:120px;">
                    <input name="artwork_interior_scene_image_height_px" type="number" min="1" step="1" placeholder="<?php echo esc_attr__( 'Height px', 'art-zone-blank' ); ?>" value="<?php echo esc_attr( $scene_height ); ?>" style="width:120px;">
                </p>

                <p><label><?php esc_html_e( 'Real Reference Scene Size (cm)', 'art-zone-blank' ); ?></label></p>
                <p class="description"><?php esc_html_e( 'Approximate real-world width and height represented by the full interior scene.', 'art-zone-blank' ); ?></p>
                <p style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                    <input name="artwork_interior_scene_real_width_cm" type="number" min="1" step="0.1" placeholder="<?php echo esc_attr__( 'Width cm', 'art-zone-blank' ); ?>" value="<?php echo esc_attr( $real_width ); ?>" style="width:120px;">
                    <input name="artwork_interior_scene_real_height_cm" type="number" min="1" step="0.1" placeholder="<?php echo esc_attr__( 'Height cm', 'art-zone-blank' ); ?>" value="<?php echo esc_attr( $real_height ); ?>" style="width:120px;">
                </p>

                <p><label><?php esc_html_e( 'Artwork Slot Boundary', 'art-zone-blank' ); ?></label></p>
                <p class="description"><?php esc_html_e( 'Position stays in percentages. Max width and height are real centimeters, used both for fit checks and for converting the boundary back into responsive percentages.', 'art-zone-blank' ); ?></p>
                <p style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                    <input name="artwork_interior_slot_x_percent" type="number" min="0" max="100" step="0.1" placeholder="X %" value="<?php echo esc_attr( $slot_x ); ?>" style="width:95px;">
                    <input name="artwork_interior_slot_y_percent" type="number" min="0" max="100" step="0.1" placeholder="Y %" value="<?php echo esc_attr( $slot_y ); ?>" style="width:95px;">
                    <input name="artwork_interior_slot_max_width_cm" type="number" min="1" step="0.1" placeholder="<?php echo esc_attr__( 'Max W cm', 'art-zone-blank' ); ?>" value="<?php echo esc_attr( $slot_width ); ?>" style="width:110px;">
                    <input name="artwork_interior_slot_max_height_cm" type="number" min="1" step="0.1" placeholder="<?php echo esc_attr__( 'Max H cm', 'art-zone-blank' ); ?>" value="<?php echo esc_attr( $slot_height ); ?>" style="width:110px;">
                    <select name="artwork_interior_slot_align_x">
                        <?php foreach ( array( 'left', 'center', 'right' ) as $align ) : ?>
                            <option value="<?php echo esc_attr( $align ); ?>" <?php selected( $slot_align_x, $align ); ?>><?php echo esc_html( ucfirst( $align ) ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="artwork_interior_slot_align_y">
                        <?php foreach ( array( 'top', 'center', 'bottom' ) as $align ) : ?>
                            <option value="<?php echo esc_attr( $align ); ?>" <?php selected( $slot_align_y, $align ); ?>><?php echo esc_html( ucfirst( $align ) ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p><strong><?php esc_html_e( 'Supported Artwork Orientation', 'art-zone-blank' ); ?></strong></p>
                <p style="display:flex;gap:16px;flex-wrap:wrap;">
                    <?php foreach ( array( 'portrait', 'landscape', 'square' ) as $orientation ) : ?>
                        <label>
                            <input type="checkbox" name="artwork_interior_orientations[]" value="<?php echo esc_attr( $orientation ); ?>" <?php checked( in_array( $orientation, $orientations, true ) ); ?>>
                            <?php echo esc_html( ucfirst( $orientation ) ); ?>
                        </label>
                    <?php endforeach; ?>
                </p>

                <p><strong><?php esc_html_e( 'Supported Artwork Size Types', 'art-zone-blank' ); ?></strong></p>
                <p class="description"><?php esc_html_e( 'Size type is computed from the artwork longest side in centimeters: xs, sm, md, lg, xl.', 'art-zone-blank' ); ?></p>
                <p style="display:flex;gap:16px;flex-wrap:wrap;">
                    <?php foreach ( array( 'xs', 'sm', 'md', 'lg', 'xl' ) as $size_type ) : ?>
                        <label>
                            <input type="checkbox" name="artwork_interior_size_types[]" value="<?php echo esc_attr( $size_type ); ?>" <?php checked( in_array( $size_type, $size_types, true ) ); ?>>
                            <?php echo esc_html( strtoupper( $size_type ) ); ?>
                        </label>
                    <?php endforeach; ?>
                </p>

                <p style="display:grid;grid-template-columns:repeat(3,minmax(120px,1fr));gap:12px;">
                    <label><?php esc_html_e( 'Room Type', 'art-zone-blank' ); ?><br><input name="artwork_interior_room_type" type="text" class="widefat" value="<?php echo esc_attr( $room_type ); ?>"></label>
                    <label><?php esc_html_e( 'Sort Order', 'art-zone-blank' ); ?><br><input name="artwork_interior_sort_order" type="number" step="1" class="widefat" value="<?php echo esc_attr( $sort_order ); ?>"></label>
                    <label id="artwork_interior_bg_color_wrap"><?php esc_html_e( 'Default Wall Color', 'art-zone-blank' ); ?><br><input name="artwork_interior_background_color" type="color" value="<?php echo esc_attr( $bg_color ); ?>"></label>
                </p>

                <p style="display:flex;gap:20px;flex-wrap:wrap;">
                    <label>
                        <input type="checkbox" name="artwork_interior_is_active" value="1" <?php checked( '1', (string) $is_active ); ?>>
                        <?php esc_html_e( 'Active', 'art-zone-blank' ); ?>
                    </label>
                    <label>
                        <input type="checkbox" name="artwork_interior_with_front_art" value="1" <?php checked( '1', (string) $with_front_art ); ?>>
                        <?php esc_html_e( 'With Front Art', 'art-zone-blank' ); ?>
                    </label>
                    <label>
                        <input type="checkbox" id="artwork_interior_static_background" name="artwork_interior_static_background" value="1" <?php checked( '1', (string) $static_background ); ?>>
                        <?php esc_html_e( 'With Static Background', 'art-zone-blank' ); ?>
                    </label>
                </p>
                <script>
                (function() {
                    var cb   = document.getElementById('artwork_interior_static_background');
                    var wrap = document.getElementById('artwork_interior_bg_color_wrap');
                    function toggle() { wrap.style.display = cb.checked ? 'none' : ''; }
                    toggle();
                    cb.addEventListener('change', toggle);
                })();
                </script>
                <?php
            },
            'artwork_interior',
            'normal',
            'default'
        );

        add_meta_box(
            'art-zone-blank-art-therapy-details',
            __( 'Art Therapy Item Details', 'art-zone-blank' ),
            function ( $post ) {
                wp_nonce_field( 'art_zone_blank_art_therapy_details', 'art_zone_blank_art_therapy_details_nonce' );
                $image_id  = (int) get_post_meta( $post->ID, 'art_therapy_item_image_id', true );
                $image_url = $image_id ? art_zone_blank_get_admin_attachment_preview_url( $image_id ) : '';

                if ( ! $image_url ) {
                    $featured_id = get_post_thumbnail_id( $post->ID );
                    $image_url   = $featured_id ? art_zone_blank_get_admin_attachment_preview_url( $featured_id ) : '';
                }

                $layout = get_post_meta( $post->ID, 'art_therapy_item_layout', true );
                ?>
                <div class="art-zone-media-picker" data-input="#art_therapy_item_image_id" data-frame-title="<?php echo esc_attr__( 'Select art therapy image', 'art-zone-blank' ); ?>" data-button-text="<?php echo esc_attr__( 'Use image', 'art-zone-blank' ); ?>">
                    <p><strong><?php esc_html_e( 'Art Therapy Image', 'art-zone-blank' ); ?></strong></p>
                    <p class="description"><?php esc_html_e( 'Choose the image shown for this art therapy entry.', 'art-zone-blank' ); ?></p>
                    <input type="hidden" id="art_therapy_item_image_id" name="art_therapy_item_image_id" value="<?php echo esc_attr( $image_id ); ?>">
                    <div class="art-zone-media-picker__preview">
                        <?php if ( $image_url ) : ?>
                            <img src="<?php echo esc_url( $image_url ); ?>" alt="" style="max-width:220px;height:auto;display:block;">
                        <?php endif; ?>
                    </div>
                    <p>
                        <button type="button" class="button art-zone-media-picker__select"><?php esc_html_e( 'Select Image', 'art-zone-blank' ); ?></button>
                        <button type="button" class="button-link art-zone-media-picker__clear"><?php esc_html_e( 'Remove', 'art-zone-blank' ); ?></button>
                    </p>
                </div>
                <p><label for="art_therapy_item_layout"><?php esc_html_e( 'Layout Style', 'art-zone-blank' ); ?></label></p>
                <p>
                    <select id="art_therapy_item_layout" name="art_therapy_item_layout" class="widefat">
                        <option value="split" <?php selected( $layout, 'split' ); ?>><?php esc_html_e( 'Split', 'art-zone-blank' ); ?></option>
                        <option value="full" <?php selected( $layout, 'full' ); ?>><?php esc_html_e( 'Full Width', 'art-zone-blank' ); ?></option>
                    </select>
                </p>
                <p class="description"><?php esc_html_e( 'Use title as the section title, excerpt as the subheading, and the main editor content as the body copy.', 'art-zone-blank' ); ?></p>
                <?php
            },
            'art_therapy_item',
            'normal',
            'default'
        );
    }
);

add_action(
    'save_post_artwork_interior',
    function ( $post_id ) {
        if ( ! isset( $_POST['art_zone_blank_artwork_interior_details_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['art_zone_blank_artwork_interior_details_nonce'] ) ), 'art_zone_blank_artwork_interior_details' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $numeric_fields = array(
            'artwork_interior_scene_image_width_px'      => 'int',
            'artwork_interior_scene_image_height_px'     => 'int',
            'artwork_interior_scene_real_width_cm'       => 'float',
            'artwork_interior_scene_real_height_cm'      => 'float',
            'artwork_interior_slot_x_percent'            => 'float',
            'artwork_interior_slot_y_percent'            => 'float',
            'artwork_interior_slot_max_width_cm'         => 'float',
            'artwork_interior_slot_max_height_cm'        => 'float',
            'artwork_interior_sort_order'                => 'int',
        );

        foreach ( $numeric_fields as $field => $type ) {
            if ( ! isset( $_POST[ $field ] ) ) {
                continue;
            }

            $raw = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
            $val = 'int' === $type ? (int) $raw : (float) $raw;
            update_post_meta( $post_id, $field, $val > 0 || in_array( $field, array( 'artwork_interior_slot_x_percent', 'artwork_interior_slot_y_percent', 'artwork_interior_sort_order' ), true ) ? $val : '' );
        }

        if ( isset( $_POST['artwork_interior_image_id'] ) ) {
            update_post_meta( $post_id, 'artwork_interior_image_id', absint( wp_unslash( $_POST['artwork_interior_image_id'] ) ) );
        }

        if ( isset( $_POST['artwork_interior_background_url'] ) ) {
            update_post_meta( $post_id, 'artwork_interior_background_url', esc_url_raw( wp_unslash( $_POST['artwork_interior_background_url'] ) ) );
        }

        $orientations = isset( $_POST['artwork_interior_orientations'] ) ? (array) wp_unslash( $_POST['artwork_interior_orientations'] ) : array();
        $orientations = array_values( array_intersect( array_map( 'sanitize_key', $orientations ), array( 'portrait', 'landscape', 'square' ) ) );
        update_post_meta( $post_id, 'artwork_interior_orientations', $orientations );

        $size_types = isset( $_POST['artwork_interior_size_types'] ) ? (array) wp_unslash( $_POST['artwork_interior_size_types'] ) : array();
        $size_types = array_values( array_intersect( array_map( 'sanitize_key', $size_types ), array( 'xs', 'sm', 'md', 'lg', 'xl' ) ) );
        update_post_meta( $post_id, 'artwork_interior_size_types', $size_types );

        if ( isset( $_POST['artwork_interior_room_type'] ) ) {
            update_post_meta( $post_id, 'artwork_interior_room_type', sanitize_key( wp_unslash( $_POST['artwork_interior_room_type'] ) ) );
        }

        if ( isset( $_POST['artwork_interior_slot_align_x'] ) ) {
            $align_x = sanitize_key( wp_unslash( $_POST['artwork_interior_slot_align_x'] ) );
            update_post_meta( $post_id, 'artwork_interior_slot_align_x', in_array( $align_x, array( 'left', 'center', 'right' ), true ) ? $align_x : 'center' );
        }

        if ( isset( $_POST['artwork_interior_slot_align_y'] ) ) {
            $align_y = sanitize_key( wp_unslash( $_POST['artwork_interior_slot_align_y'] ) );
            update_post_meta( $post_id, 'artwork_interior_slot_align_y', in_array( $align_y, array( 'top', 'center', 'bottom' ), true ) ? $align_y : 'center' );
        }

        if ( isset( $_POST['artwork_interior_background_color'] ) ) {
            $color = sanitize_hex_color( wp_unslash( $_POST['artwork_interior_background_color'] ) );
            update_post_meta( $post_id, 'artwork_interior_background_color', $color ? $color : '#e8dfd2' );
        }

        update_post_meta( $post_id, 'artwork_interior_is_active', isset( $_POST['artwork_interior_is_active'] ) ? '1' : '0' );
        update_post_meta( $post_id, 'artwork_interior_with_front_art', isset( $_POST['artwork_interior_with_front_art'] ) ? '1' : '0' );
        update_post_meta( $post_id, 'artwork_interior_static_background', isset( $_POST['artwork_interior_static_background'] ) ? '1' : '0' );
    }
);

add_action(
    'save_post_artwork',
    function ( $post_id ) {
        if ( ! isset( $_POST['art_zone_blank_artwork_details_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['art_zone_blank_artwork_details_nonce'] ) ), 'art_zone_blank_artwork_details' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $fields = array(
            'artwork_image_id'              => 'absint',
            'artwork_year'                  => 'sanitize_text_field',
            'artwork_series'                => 'sanitize_text_field',
            'artwork_framing'               => 'sanitize_text_field',
            'artwork_gallery_size'          => 'sanitize_text_field',
            'artwork_external_image'        => 'esc_url_raw',
            'artwork_enquiry_url'           => 'esc_url_raw',
            'artwork_quote'                 => 'sanitize_textarea_field',
            'artwork_palette_note'          => 'sanitize_textarea_field',
            'artwork_exhibition_history'    => 'sanitize_textarea_field',
        );

        foreach ( $fields as $field => $sanitize_callback ) {
            if ( ! isset( $_POST[ $field ] ) ) {
                continue;
            }

            update_post_meta(
                $post_id,
                $field,
                call_user_func( $sanitize_callback, wp_unslash( $_POST[ $field ] ) )
            );
        }

        if ( isset( $_POST['artwork_framing_status'] ) ) {
            $allowed_statuses = array( 'framing_available', 'no_frame', 'with_frame', 'not_applicable' );
            $status           = sanitize_key( wp_unslash( $_POST['artwork_framing_status'] ) );
            update_post_meta( $post_id, 'artwork_framing_status', in_array( $status, $allowed_statuses, true ) ? $status : 'framing_available' );
        }

        foreach ( array( 'artwork_width_cm', 'artwork_height_cm' ) as $dim_field ) {
            if ( isset( $_POST[ $dim_field ] ) ) {
                $val = max( 0.0, (float) sanitize_text_field( wp_unslash( $_POST[ $dim_field ] ) ) );
                update_post_meta( $post_id, $dim_field, $val > 0 ? $val : '' );
            }
        }

        delete_post_meta( $post_id, 'artwork_medium' );
    }
);

add_action(
    'save_post_studio_item',
    function ( $post_id ) {
        if ( ! isset( $_POST['art_zone_blank_studio_details_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['art_zone_blank_studio_details_nonce'] ) ), 'art_zone_blank_studio_details' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['studio_item_image_id'] ) ) {
            update_post_meta( $post_id, 'studio_item_image_id', absint( wp_unslash( $_POST['studio_item_image_id'] ) ) );
        }

        if ( isset( $_POST['studio_item_gallery_ids'] ) ) {
            update_post_meta( $post_id, 'studio_item_gallery_ids', art_zone_blank_sanitize_media_gallery_value( wp_unslash( $_POST['studio_item_gallery_ids'] ) ) );
        }

        if ( isset( $_POST['studio_item_layout'] ) ) {
            $layout = sanitize_text_field( wp_unslash( $_POST['studio_item_layout'] ) );
            update_post_meta( $post_id, 'studio_item_layout', in_array( $layout, array( 'split', 'full' ), true ) ? $layout : 'split' );
        }
    }
);

add_action(
    'save_post_art_therapy_item',
    function ( $post_id ) {
        if ( ! isset( $_POST['art_zone_blank_art_therapy_details_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['art_zone_blank_art_therapy_details_nonce'] ) ), 'art_zone_blank_art_therapy_details' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['art_therapy_item_image_id'] ) ) {
            update_post_meta( $post_id, 'art_therapy_item_image_id', absint( wp_unslash( $_POST['art_therapy_item_image_id'] ) ) );
        }

        if ( isset( $_POST['art_therapy_item_layout'] ) ) {
            $layout = sanitize_text_field( wp_unslash( $_POST['art_therapy_item_layout'] ) );
            update_post_meta( $post_id, 'art_therapy_item_layout', in_array( $layout, array( 'split', 'full' ), true ) ? $layout : 'split' );
        }
    }
);

add_action(
    'add_meta_boxes_artwork_frame',
    function ( $post ) {
        add_meta_box(
            'art-zone-blank-artwork-frame-details',
            __( 'Frame Details', 'art-zone-blank' ),
            function ( $post ) {
                wp_nonce_field( 'art_zone_blank_artwork_frame_details', 'art_zone_blank_artwork_frame_details_nonce' );

                $material       = get_post_meta( $post->ID, 'frame_material', true );
                $color          = get_post_meta( $post->ID, 'frame_background_color', true );
                $thickness      = get_post_meta( $post->ID, 'frame_thickness_cm', true );
                $is_active      = get_post_meta( $post->ID, 'frame_is_active', true );
                $png_id         = (int) get_post_meta( $post->ID, 'frame_png_id', true );
                $png_url        = $png_id ? art_zone_blank_get_admin_attachment_preview_url( $png_id ) : '';
                $slice          = get_post_meta( $post->ID, 'frame_slice', true );
                if ( '' === $slice ) {
                    $slice = '30';
                }

                if ( '' === $is_active ) {
                    $is_active = '1';
                }

                if ( ! $color ) {
                    $color = '#8b7355';
                }
                ?>
                <p><label for="frame_material"><?php esc_html_e( 'Material', 'art-zone-blank' ); ?></label></p>
                <p><input id="frame_material" name="frame_material" type="text" class="widefat" placeholder="<?php echo esc_attr__( 'e.g. White Oak, Black Aluminium', 'art-zone-blank' ); ?>" value="<?php echo esc_attr( $material ); ?>"></p>

                <p style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <label>
                        <?php esc_html_e( 'Frame Color', 'art-zone-blank' ); ?><br>
                        <input name="frame_background_color" type="color" value="<?php echo esc_attr( $color ); ?>">
                    </label>
                    <label>
                        <?php esc_html_e( 'Thickness (cm)', 'art-zone-blank' ); ?><br>
                        <input name="frame_thickness_cm" type="number" min="0.1" step="0.1" style="width:100px;" value="<?php echo esc_attr( $thickness ); ?>">
                    </label>
                </p>

                <div class="art-zone-media-picker" data-input="#frame_png_id" data-frame-title="<?php echo esc_attr__( 'Select frame PNG', 'art-zone-blank' ); ?>" data-button-text="<?php echo esc_attr__( 'Use image', 'art-zone-blank' ); ?>">
                    <p><strong><?php esc_html_e( 'Frame PNG', 'art-zone-blank' ); ?></strong></p>
                    <p class="description"><?php esc_html_e( 'Square PNG used as a CSS border-image (9-slice). The artwork shows through the transparent centre.', 'art-zone-blank' ); ?></p>
                    <input type="hidden" id="frame_png_id" name="frame_png_id" value="<?php echo esc_attr( $png_id ); ?>">
                    <div class="art-zone-media-picker__preview">
                        <?php if ( $png_url ) : ?>
                            <img src="<?php echo esc_url( $png_url ); ?>" alt="" style="max-width:160px;height:auto;display:block;">
                        <?php endif; ?>
                    </div>
                    <p>
                        <button type="button" class="button art-zone-media-picker__select"><?php esc_html_e( 'Select Image', 'art-zone-blank' ); ?></button>
                        <button type="button" class="button-link art-zone-media-picker__clear"><?php esc_html_e( 'Remove', 'art-zone-blank' ); ?></button>
                    </p>
                </div>

                <p>
                    <label for="frame_slice"><strong><?php esc_html_e( 'Slice (px)', 'art-zone-blank' ); ?></strong></label><br>
                    <input id="frame_slice" name="frame_slice" type="number" min="1" step="1" style="width:100px;" value="<?php echo esc_attr( $slice ); ?>">
                    <span class="description"><?php esc_html_e( 'Pixels from each edge of the PNG to cut the 9 slices. Match the corner size of your frame image.', 'art-zone-blank' ); ?></span>
                </p>

                <p>
                    <label>
                        <input type="checkbox" name="frame_is_active" value="1" <?php checked( '1', (string) $is_active ); ?>>
                        <?php esc_html_e( 'Active', 'art-zone-blank' ); ?>
                    </label>
                </p>
                <?php
            },
            'artwork_frame',
            'normal',
            'default'
        );
    }
);

add_action(
    'save_post_artwork_frame',
    function ( $post_id ) {
        if ( ! isset( $_POST['art_zone_blank_artwork_frame_details_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['art_zone_blank_artwork_frame_details_nonce'] ) ), 'art_zone_blank_artwork_frame_details' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['frame_material'] ) ) {
            update_post_meta( $post_id, 'frame_material', sanitize_text_field( wp_unslash( $_POST['frame_material'] ) ) );
        }

        if ( isset( $_POST['frame_background_color'] ) ) {
            $color = sanitize_hex_color( wp_unslash( $_POST['frame_background_color'] ) );
            update_post_meta( $post_id, 'frame_background_color', $color ? $color : '#8b7355' );
        }

        if ( isset( $_POST['frame_thickness_cm'] ) ) {
            $thickness = max( 0.0, (float) sanitize_text_field( wp_unslash( $_POST['frame_thickness_cm'] ) ) );
            update_post_meta( $post_id, 'frame_thickness_cm', $thickness > 0 ? $thickness : '' );
        }

        if ( isset( $_POST['frame_png_id'] ) ) {
            update_post_meta( $post_id, 'frame_png_id', absint( wp_unslash( $_POST['frame_png_id'] ) ) );
        }

        if ( isset( $_POST['frame_slice'] ) ) {
            $slice = max( 1, (int) sanitize_text_field( wp_unslash( $_POST['frame_slice'] ) ) );
            update_post_meta( $post_id, 'frame_slice', $slice );
        }

        update_post_meta( $post_id, 'frame_is_active', isset( $_POST['frame_is_active'] ) ? '1' : '0' );
    }
);
