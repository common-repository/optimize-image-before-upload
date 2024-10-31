<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
?>

<form method="post" action="">
    <?php
    wp_nonce_field('opt_img_save_image_compression_settings', 'opt_img_image_compression_nonce');

    // Get the compression quality value from the options table, default to '55' if not set.
    $compression_quality = !empty(get_option('opt_img_compression_quality')) ? get_option('opt_img_compression_quality') : '55';

    // Get the threshold size value from the options table, default to '0' if not set.
    $threshold_size = !empty(get_option('opt_img_threshold_size')) ? get_option('opt_img_threshold_size') : '0';
    ?>

    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="opt_img_enable_backend_optimization">
                    <?php esc_html_e('Enable backend media optimization', 'optimize-image-before-upload'); ?>
                </label>
            </th>
            <td>
                <label class="switch">
                    <!-- Hidden input to handle the unchecked state -->
                    <input type="hidden" name="opt_img_enable_backend_optimization" value="0" />
                    <!-- Checkbox input -->
                    <input type="checkbox" name="opt_img_enable_backend_optimization" id="opt_img_enable_backend_optimization" value="1" <?php checked(get_option('opt_img_enable_backend_optimization', '1'), '1'); ?> />
                    <span class="slider"></span>
                </label>
                <p class="description">
                    <?php esc_html_e('Enable this option if you want to optimize images during direct upload to the WordPress media library.', 'optimize-image-before-upload'); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="opt_img_compression_quality">
                    <?php esc_html_e('Compression Quality', 'optimize-image-before-upload'); ?>
                </label>
            </th>
            <td>
                <input type="number" name="opt_img_compression_quality" id="opt_img_compression_quality" value="<?php echo esc_attr($compression_quality); ?>" min="0" max="100" />
                <p class="description">
                    <?php esc_html_e('Set the desired compression quality for optimized images (0-100). A value of 0 gives maximum compression and lowest quality, while 100 gives minimum compression and highest quality.', 'optimize-image-before-upload'); ?>
                    <br />
                    <?php esc_html_e('(Default: 55)', 'optimize-image-before-upload'); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="opt_img_threshold_size">
                    <?php esc_html_e('Threshold Image Size to Optimize', 'optimize-image-before-upload'); ?><br>
            </th>
            <td>
                <input type="number" name="opt_img_threshold_size" id="opt_img_threshold_size" value="<?php echo esc_attr($threshold_size); ?>" min="0" /><b><?php esc_html_e(' MB', 'optimize-image-before-upload'); ?></b>
                <p class="description">
                    <?php esc_html_e('Set the desired threshold image size for optimization. Images larger than this size (in MB) will be optimized.', 'optimize-image-before-upload'); ?>
					<br />
					<b><?php esc_html_e('Set 0 MB to optimize every images with any size.', 'optimize-image-before-upload'); ?></b>
                    <br />
                    <?php esc_html_e('(Default: 0 MB)', 'optimize-image-before-upload'); ?>
                </p>
            </td>
        </tr>
    </table>

    <?php submit_button(__('Save Changes', 'optimize-image-before-upload'), 'primary', 'opt_img_save_settings'); ?>
</form>