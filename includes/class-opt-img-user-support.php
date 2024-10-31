<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Opt_Img_User_Support Class
 *
 * Handles the rendering of the Opt Image Plugin Support page.
 *
 * @package Optimize_Image_Before_Upload
 * @since 1.0.0
 */
class Opt_Img_User_Support
{

    /**
     * Constructor
     *
     * Initializes the class and adds necessary hooks.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_menu'));
    }

    /**
     * Add Opt Image Plugin Menu Page
     *
     * Adds the Opt Image Plugin Menu page to the WordPress admin menu.
     *
     * @since 1.0.0
     */
    public function add_plugin_menu()
    {
        add_options_page(
            __('OptiImage - Upload Optimizer', 'optimize-image-before-upload'),
            __('OptiImage - Upload Optimizer', 'optimize-image-before-upload'),
            'manage_options',
            'opt-img-menu',
            array($this, 'render_menu_screen')
        );
    }

    /**
     * Render Opt Image Plugin Menu Page
     *
     * Renders the Opt Image Plugin Menu page content.
     *
     * @since 1.0.0
     */
    public function render_menu_screen()
    {
?>
        <div class="wrap">
            <h1><?php esc_html_e('OptiImage - Upload Optimizer Settings', 'optimize-image-before-upload'); ?></h1>
            <?php
            $opt_img_lib_status = get_option('opt_img_lib_status');

            if ('enabled' === $opt_img_lib_status) {
                update_option('opt_img_library_status', "1");
                include plugin_dir_path(__FILE__) . 'views/opt-img-settings-view.php';
            } else {
                update_option('opt_img_library_status', "0");
                include plugin_dir_path(__FILE__) . 'views/opt-img-support-view.php';
            }
            ?>
        </div>
<?php
    }
}
