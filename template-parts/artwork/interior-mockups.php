<?php
/**
 * Artwork interior mockup selector.
 *
 * @package Art_Zone_Blank
 */

$artwork   = isset( $args['artwork'] ) && is_array( $args['artwork'] ) ? $args['artwork'] : array();
$templates = isset( $args['templates'] ) && is_array( $args['templates'] ) ? array_values( $args['templates'] ) : array();

if ( empty( $artwork['imageUrl'] ) || empty( $templates ) ) {
    return;
}

$active_template = $templates[0];
$active_slot     = isset( $active_template['slot'] ) && is_array( $active_template['slot'] ) ? $active_template['slot'] : array();

if ( empty( $active_template['backgroundImage'] ) || empty( $active_slot ) ) {
    return;
}

$heading_id  = 'artwork-interior-heading-' . (int) $artwork['postId'];
$picker_id   = 'artwork-interior-color-' . (int) $artwork['postId'];
$artwork_alt = ! empty( $artwork['imageAlt'] )
    ? sprintf( __( '%s shown in an interior setting', 'art-zone-blank' ), $artwork['imageAlt'] )
    : __( 'Artwork shown in an interior setting', 'art-zone-blank' );
$active_render = art_zone_blank_get_artwork_render_dimensions_for_template( $artwork, $active_template );

$active_style = sprintf(
    '--scene-width:%1$d;--scene-height:%2$d;--slot-x:%3$s;--slot-y:%4$s;--slot-width:%5$s;--slot-height:%6$s;--slot-align-x:%7$s;--slot-align-y:%8$s;--artwork-render-width:%9$s;--artwork-render-height:%10$s;--interior-bg:%11$s;',
    (int) $active_template['sceneImageWidthPx'],
    (int) $active_template['sceneImageHeightPx'],
    esc_attr( (string) (float) $active_slot['xPercent'] ),
    esc_attr( (string) (float) $active_slot['yPercent'] ),
    esc_attr( (string) (float) $active_slot['maxWidthPercent'] ),
    esc_attr( (string) (float) $active_slot['maxHeightPercent'] ),
    esc_attr( art_zone_blank_slot_alignment_to_css( 'x', $active_slot['alignX'] ) ),
    esc_attr( art_zone_blank_slot_alignment_to_css( 'y', $active_slot['alignY'] ) ),
    esc_attr( (string) (float) $active_render['widthPercent'] ),
    esc_attr( (string) (float) $active_render['heightPercent'] ),
    esc_attr( $active_template['backgroundColor'] )
);
?>

<section class="artwork-interior-mockups section-shell" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>" data-interior-selector>
    <div class="artwork-interior-mockups__head">
        <div>
            <h2 id="<?php echo esc_attr( $heading_id ); ?>" class="artwork-interior-mockups__title"><?php esc_html_e( 'Visualize in Your Interior', 'art-zone-blank' ); ?></h2>
        </div>
        <label class="artwork-interior-mockups__color" for="<?php echo esc_attr( $picker_id ); ?>"<?php echo ! empty( $active_template['staticBackground'] ) ? ' hidden' : ''; ?>>
            <span><?php esc_html_e( 'Wall color', 'art-zone-blank' ); ?></span>
            <input id="<?php echo esc_attr( $picker_id ); ?>" type="color" value="<?php echo esc_attr( $active_template['backgroundColor'] ); ?>" data-interior-color>
        </label>
    </div>

    <div class="artwork-interior-mockups__layout">
        <article class="interior-mockup-card interior-mockup-card--main">
            <div class="interior-mockup" style="<?php echo esc_attr( $active_style ); ?>" data-interior-stage>
                <div class="interior-mockup__wall-color" aria-hidden="true"></div>
                <img
                    class="interior-mockup__bg"
                    src="<?php echo esc_url( $active_template['backgroundImage'] ); ?>"
                    alt=""
                    loading="lazy"
                    decoding="async"
                    aria-hidden="true"
                    data-interior-bg
                >
                <div class="interior-mockup__slot<?php echo ! empty( $active_template['withFrontArt'] ) ? ' withFrontArt' : ''; ?>" data-interior-slot>
                    <div class="artwork-frame interior-mockup__artwork-frame<?php echo ! empty( $artwork['useDropShadow'] ) ? ' artwork-frame--drop-shadow' : ''; ?>">
                        <img
                            class="interior-mockup__artwork"
                            src="<?php echo esc_url( $artwork['imageUrl'] ); ?>"
                            alt="<?php echo esc_attr( $artwork_alt ); ?>"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                </div>
            </div>
            <!-- <h3 class="interior-mockup-card__title" data-interior-title><?php echo esc_html( $active_template['title'] ); ?></h3> -->
        </article>

        <?php if ( count( $templates ) > 1 ) : ?>
            <div class="artwork-interior-mockups__choices" aria-label="<?php echo esc_attr__( 'Choose an interior design', 'art-zone-blank' ); ?>">
                <?php foreach ( $templates as $index => $template ) : ?>
                    <?php
                    $slot = isset( $template['slot'] ) && is_array( $template['slot'] ) ? $template['slot'] : array();

                    if ( empty( $template['backgroundImage'] ) || empty( $slot ) ) {
                        continue;
                    }

                    $render = art_zone_blank_get_artwork_render_dimensions_for_template( $artwork, $template );
                    ?>
                    <div
                        class="interior-choice<?php echo 0 === $index ? ' is-active' : ''; ?>"
                        aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>"
                        data-interior-choice
                        data-title="<?php echo esc_attr( $template['title'] ); ?>"
                        data-bg="<?php echo esc_attr( esc_url( $template['backgroundImage'] ) ); ?>"
                        data-bg-color="<?php echo esc_attr( $template['backgroundColor'] ); ?>"
                        data-scene-width="<?php echo esc_attr( (int) $template['sceneImageWidthPx'] ); ?>"
                        data-scene-height="<?php echo esc_attr( (int) $template['sceneImageHeightPx'] ); ?>"
                        data-slot-x="<?php echo esc_attr( (float) $slot['xPercent'] ); ?>"
                        data-slot-y="<?php echo esc_attr( (float) $slot['yPercent'] ); ?>"
                        data-slot-width="<?php echo esc_attr( (float) $slot['maxWidthPercent'] ); ?>"
                        data-slot-height="<?php echo esc_attr( (float) $slot['maxHeightPercent'] ); ?>"
                        data-slot-align-x="<?php echo esc_attr( art_zone_blank_slot_alignment_to_css( 'x', $slot['alignX'] ) ); ?>"
                        data-slot-align-y="<?php echo esc_attr( art_zone_blank_slot_alignment_to_css( 'y', $slot['alignY'] ) ); ?>"
                        data-artwork-width="<?php echo esc_attr( (float) $render['widthPercent'] ); ?>"
                        data-artwork-height="<?php echo esc_attr( (float) $render['heightPercent'] ); ?>"
                        data-with-front-art="<?php echo ! empty( $template['withFrontArt'] ) ? '1' : '0'; ?>"
                        data-static-bg="<?php echo ! empty( $template['staticBackground'] ) ? '1' : '0'; ?>"
                    >
                        <span class="interior-choice__thumb" style="<?php echo esc_attr( '--interior-bg:' . $template['backgroundColor'] . ';' ); ?>">
                            <span class="interior-choice__wall-color" aria-hidden="true"></span>
                            <img src="<?php echo esc_url( ! empty( $template['backgroundThumbImage'] ) ? $template['backgroundThumbImage'] : $template['backgroundImage'] ); ?>" alt="" loading="lazy" decoding="async" aria-hidden="true">
                        </span>
                        <span class="interior-choice__title"><?php echo esc_html( $template['title'] ); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
