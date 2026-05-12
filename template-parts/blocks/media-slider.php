<?php
/**
 * Generic media slider block.
 *
 * Expected args:
 * - images: array of image URLs
 * - class_name: optional extra class string
 * - interval: optional autoplay interval in ms
 * - aria_label: optional aria label
 */

$args = wp_parse_args(
    $args ?? array(),
    array(
        'images'     => array(),
        'class_name' => '',
        'interval'   => 3000,
        'aria_label' => __( 'Image slider', 'art-zone-blank' ),
    )
);

$images = array_values(
    array_filter(
        (array) $args['images'],
        function ( $image ) {
            return ! empty( $image );
        }
    )
);

if ( empty( $images ) ) {
    return;
}

$class_name = trim( 'media-slider-block ' . $args['class_name'] );
?>
<section class="<?php echo esc_attr( $class_name ); ?>">
    <div
        class="media-slider-block__frame"
        data-slider-interval="<?php echo esc_attr( (int) $args['interval'] ); ?>"
        aria-label="<?php echo esc_attr( $args['aria_label'] ); ?>"
    >
        <?php foreach ( $images as $index => $image ) : ?>
            <div class="media-slider-block__slide <?php echo esc_attr( 0 === $index ? 'is-active' : '' ); ?>">
                <img src="<?php echo esc_url( $image ); ?>" alt="">
            </div>
        <?php endforeach; ?>
    </div>
</section>
