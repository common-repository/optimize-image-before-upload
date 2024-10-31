<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Admin Notice Class
 *
 * Manage Admin Notice Class
 *
 * @package Optimize_Image_Before_Upload
 * @since 1.0.0
 */
class Opt_Img_Admin_Notice
{

	/**
	 * Scripts instance.
	 *
	 * @var Opt_Img_Scripts
	 */
	public $scripts;

	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		global $opt_img_scripts;
		$this->scripts = $opt_img_scripts;
	}

	/**
	 * Add hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_hooks()
	{
		if (!extension_loaded('imagick') || !class_exists('Imagick')) {
			update_option('opt_img_lib_status', 'disabled');
			set_transient('opt_img_activation_notice', true, HOUR_IN_SECONDS);
			add_action('admin_init', array($this, 'handle_plugin_activation_deactivation'));
			return;
		} else {
			update_option('opt_img_lib_status', 'enabled');
			return;
		}
	}

	/**
	 * Handle plugin activation/deactivation.
	 *
	 * This function handles the deactivation of the plugin.
	 * When called, it deactivates the plugin and displays an admin notice
	 * if the Imagick library is not installed.
	 *
	 * @since 1.0.0
	 */
	public function handle_plugin_activation_deactivation()
	{
		// Admin Notice
		unset($_GET['activate']);
		add_action('admin_notices', array($this, 'display_imagick_inactive_notice'));
	}

	/**
	 * Display admin notice during plugin activation.
	 *
	 * This function displays an admin notice during plugin activation if the Imagick
	 * library is not installed. It includes a button to redirect the user to the
	 * support page.
	 *
	 * @since 1.0.0
	 */
	public function display_imagick_inactive_notice()
	{
		// Check if the transient is set.
		if (get_transient('opt_img_activation_notice')) {
			// Get the support page URL.
			$support_page_url = add_query_arg(
				array(
					'page' => 'opt-img-menu',
				),
				admin_url('options-general.php')
			);

			// Display the admin notice.
?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php esc_html_e('The Imagick library is required for the OptiImage - Upload Optimizer plugin to function correctly. Please install the Imagick library or visit the support page for instructions.', 'optimize-image-before-upload'); ?>
				</p>
				<p>
					<a href="<?php echo esc_url($support_page_url); ?>" class="button button-primary">
						<?php esc_html_e('Visit Support Page', 'optimize-image-before-upload'); ?>
					</a>
				</p>
			</div>
<?php
			// Delete the transient to avoid displaying the notice repeatedly.
			delete_transient('opt_img_activation_notice');
		}
	}
}
