<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Save Image Compression Quality Settings
 *
 * This function is hooked into the WordPress 'admin_init' action. It checks if
 * the settings form for image compression quality has been submitted. If so,
 * it retrieves the submitted compression quality value from the POST request
 * and updates the corresponding option in the WordPress database.
 *
 * The compression quality setting allows users to specify the desired level of
 * compression for image optimization, ranging from 0 (maximum compression, lowest quality)
 * to 100 (minimum compression, highest quality).
 *
 * @since 1.0.0
 *
 * @return void
 */
function opt_img_save_settings()
{
    if (isset($_POST['opt_img_save_settings'])) {
        if (
            isset($_POST['opt_img_image_compression_nonce']) &&
            wp_verify_nonce(sanitize_key($_POST['opt_img_image_compression_nonce']), 'opt_img_save_image_compression_settings')
        ) {
            // Process the form data for compression quality.
            if (isset($_POST['opt_img_compression_quality'])) {
                $compression_quality = absint($_POST['opt_img_compression_quality']);
                update_option('opt_img_compression_quality', $compression_quality);
            }

            // Process the form data for threshold size.
            if (isset($_POST['opt_img_threshold_size'])) {
                $threshold_size = absint($_POST['opt_img_threshold_size']);
                update_option('opt_img_threshold_size', $threshold_size);
            }

            // Process the form data for Backend Optimization Settings.
            if (isset($_POST['opt_img_enable_backend_optimization'])) {
                $opt_img_enable_backend_optimization = sanitize_text_field(wp_unslash($_POST['opt_img_enable_backend_optimization']));
                update_option('opt_img_enable_backend_optimization', $opt_img_enable_backend_optimization);
            }

            // Add success message.
            add_settings_error(
                'opt_img_settings_messages',
                'opt_img_settings_updated',
                esc_html__('Settings saved successfully.', 'optimize-image-before-upload'),
                'updated'
            );
        } else {
            // Handle nonce verification failure.
            add_settings_error(
                'opt_img_settings_messages',
                'opt_img_settings_error',
                esc_html__('An error occurred while saving the settings. Please try again.', 'optimize-image-before-upload'),
                'error'
            );
        }
    }
}
add_action('admin_init', 'opt_img_save_settings');
