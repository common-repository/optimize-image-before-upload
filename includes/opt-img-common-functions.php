<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Common Functions
 *
 * It contains common functions used in the plugin.
 *
 * @package Optimize_Image_Before_Upload
 * @since 1.0.0
 */

/**
 * Optimize images using Imagick
 *
 * This function optimizes an image using the Imagick library. It sets the image
 * compression to JPEG, sets the compression quality as per the need, and strips any
 * unnecessary metadata from the image. The function returns the optimized
 * Imagick object.
 *
 * @since 1.0.0
 *
 * @param string $image_path The path to the image file to be optimized.
 *
 * @return \Imagick The optimized Imagick object containing the image data.
 */
function opt_img_imagick_image_process($image_path)
{
    // Include necessary files for WP_Filesystem.
    require_once ABSPATH . 'wp-admin/includes/file.php';
    $wp_filesystem = WP_Filesystem();

    // Get compression settings.
    $compression_quality = get_option('opt_img_compression_quality');

    // Image processing using Imagick.
    $imagick = new \Imagick(realpath($image_path));

    // Get the file extension.
    $file_extension = pathinfo($image_path, PATHINFO_EXTENSION);

    // Set the image compression type based on the file extension.
    if ('jpg' === $file_extension || 'jpeg' === $file_extension) {
        $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
    } elseif ('png' === $file_extension) {
        $imagick->stripImage();
        $imagick->quantizeImage(256, \Imagick::COLORSPACE_RGB, 0, false, false); // 0 = no dithering.
        $imagick->setImageFormat('png8');
        $imagick->setImageCompression(\Imagick::COMPRESSION_ZIP);
        $imagick->setOption('png:compression-level', 9);
        $imagick->setOption('png:compression-filter', 5); // Equivalent to \Imagick::FILTER_PAETH.
        $imagick->setOption('png:compression-strategy', 3); // Equivalent to \Imagick::FILTER_ENTROPY.
    }

    $imagick->setImageCompressionQuality($compression_quality);
    $imagick->stripImage();

    return $imagick;
}
