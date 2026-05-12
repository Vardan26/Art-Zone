<?php
/**
 * Artwork frame selector.
 *
 * @package Art_Zone_Blank
 */

$frames      = isset( $args['frames'] ) && is_array( $args['frames'] ) ? $args['frames'] : array();
$artwork_w   = isset( $args['artworkWidthCm'] ) ? (float) $args['artworkWidthCm'] : 0.0;
$slider_id   = 'artwork-frame-thickness-' . ( isset( $args['postId'] ) ? (int) $args['postId'] : 0 );
$default_pct = 5;
?>

<div class="artwork-frame-selector" data-frame-selector>
    <div class="artwork-frame-selector__head">
        <p class="artwork-detail__eyebrow"><?php esc_html_e( 'Style This Artwork', 'art-zone-blank' ); ?></p>
    </div>

    <div class="artwork-frame-selector__choices" role="group" aria-label="<?php echo esc_attr__( 'Choose a frame', 'art-zone-blank' ); ?>">
        <div
            class="artwork-frame-choice is-active"
            aria-pressed="true"
            data-frame-choice
            data-frame-png-url=""
            data-frame-slice="0"
        >
            <span class="artwork-frame-choice__thumb artwork-frame-choice__thumb--none" aria-hidden="true"></span>
            <span class="artwork-frame-choice__label"><?php esc_html_e( 'No Frame', 'art-zone-blank' ); ?></span>
        </div>

        <?php foreach ( $frames as $frame ) : ?>
            <div
                    class="artwork-frame-choice"
                    aria-pressed="false"
                    data-frame-choice
                    data-frame-png-url="<?php echo esc_attr( $frame['framePngUrl'] ); ?>"
                    data-frame-thumb-url="<?php echo esc_attr( ! empty( $frame['frameThumbUrl'] ) ? $frame['frameThumbUrl'] : $frame['framePngUrl'] ); ?>"
                    data-frame-slice="<?php echo esc_attr( $frame['frameSlice'] ); ?>"
                    data-frame-thickness-pct="<?php echo esc_attr( ( $artwork_w > 0 && $frame['thicknessCm'] > 0 ) ? round( ( $frame['thicknessCm'] / $artwork_w ) * 100, 2 ) : 0 ); ?>"
                >
                <span
                    class="artwork-frame-choice__thumb"
                    <?php if ( $frame['framePngUrl'] ) : ?>
                    style="border-image-source:url('<?php echo esc_url( $frame['framePngUrl'] ); ?>');border-image-slice:<?php echo esc_attr( $frame['frameSlice'] ); ?>;border-image-repeat:round;"
                    <?php else : ?>
                    style="background-color:<?php echo esc_attr( $frame['color'] ); ?>;"
                    <?php endif; ?>
                    aria-hidden="true"
                ></span>
                <span class="artwork-frame-choice__label">
                    <?php echo esc_html( $frame['title'] ); ?>
                    <?php if ( $frame['material'] ) : ?>
                        <span class="artwork-frame-choice__meta"><?php echo esc_html( $frame['material'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( $frame['thicknessCm'] > 0 ) : ?>
                        <span class="artwork-frame-choice__meta"><?php echo esc_html( $frame['thicknessCm'] . ' cm' ); ?></span>
                    <?php endif; ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- <div class="artwork-frame-selector__thickness">
        <label class="artwork-frame-selector__thickness-label" for="<?php echo esc_attr( $slider_id ); ?>">
            <span><?php esc_html_e( 'Thickness', 'art-zone-blank' ); ?></span>
            <span class="artwork-frame-selector__thickness-value" data-frame-thickness-value><?php echo esc_html( $default_pct . '%' ); ?></span>
        </label>
        <input
            id="<?php echo esc_attr( $slider_id ); ?>"
            type="range"
            class="artwork-frame-selector__thickness-input"
            min="1"
            max="25"
            step="0.5"
            value="<?php echo esc_attr( $default_pct ); ?>"
            data-frame-thickness
            disabled
        >
    </div> -->
</div>
