<?php
/**
 * Plugin Name: OptiImage - Upload Optimizer
 * Plugin URI: https://www.yudiz.com/wordpress-plugin-support/
 * Description: A plugin that efficiently optimizes images before uploading to reduce site load and increase site speed.
 * Version: 1.0
 * Author: Yudiz Solutions Limited
 * Author URI: https://www.yudiz.com/
 * Text Domain: optimize-image-before-upload
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Optimize_Image_Before_Upload
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Basic plugin definitions
 *
 * @package Optimize_Image_Before_Upload
 * @since 1.0.0
 */

// Constant for the Plugin directory.
if (!defined('OPT_IMG_DIR')) {
  define('OPT_IMG_DIR', dirname(__FILE__));
}

// Constant for the Plugin version.
if (!defined('OPT_IMG_VERSION')) {
  define('OPT_IMG_VERSION', '1.0.0');
}

// Constant for the Plugin URL.
if (!defined('OPT_IMG_URL')) {
  define('OPT_IMG_URL', plugin_dir_url(__FILE__));
}

// Constant for the Plugin assets directory.
if (!defined('OPT_IMG_INC_DIR')) {
  define('OPT_IMG_INC_DIR', OPT_IMG_DIR . '/includes');
}

// Constant for the Library status.
if (!defined('OPT_IMG_LIBRARY_STATUS')) {
  define('OPT_IMG_LIBRARY_STATUS', get_option('opt_img_library_status'));
}

// Constant for the Threshold size for optimization setting.
if (!defined('OPT_IMG_THRESHOLD_SIZE')) {
  define('OPT_IMG_THRESHOLD_SIZE', get_option('opt_img_threshold_size', 1) * 1024 * 1024);
}

// Constant for the Backend Optimization setting.
if (!defined('OPT_IMG_ENABLE_BACKEND_OPTIMIZATION')) {
  define('OPT_IMG_ENABLE_BACKEND_OPTIMIZATION', get_option('opt_img_enable_backend_optimization'));
}

/**
 * Load Text Domain
 *
 * This gets the plugin ready for translation.
 *
 * @since 1.0.0
 */
function opt_img_load_textdomain()
{
  load_plugin_textdomain('optimize-image-before-upload', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'opt_img_load_textdomain');

/**
 * Enqueue Scripts on Admin Side
 *
 * @since 1.0.0
 */
function opt_img_admin_scripts($hook_suffix)
{
  wp_enqueue_script('jquery');

  if ($hook_suffix === 'settings_page_opt-img-menu') {
    wp_enqueue_script('admin-notice-js', plugins_url('assets/js/admin-notice.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_enqueue_style(
      'instruction-page-css',
      plugins_url('assets/css/admin-settings-view.css', __FILE__),
      array(),
      filemtime(plugin_dir_path(__FILE__) . 'assets/css/admin-settings-view.css')
    );
  }
}
add_action('admin_enqueue_scripts', 'opt_img_admin_scripts');

/**
 * Activation Hook
 *
 * Register plugin activation hook.
 *
 * @since 1.0.0
 */
function opt_img_activate() {}
register_activation_hook(__FILE__, 'opt_img_activate');

/**
 * Deactivation Hook
 *
 * Register plugin deactivation hook.
 *
 * @since 1.0.0
 */
function opt_img_deactivate() {}
register_deactivation_hook(__FILE__, 'opt_img_deactivate');

/**
 * Add settings link on plugin page
 *
 * @param array $links Plugin action links.
 * @return array Modified plugin action links.
 */
function opt_img_settings_link($links)
{
  $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=opt-img-menu')) . '">' . esc_html__('Settings', 'optimize-image-before-upload') . '</a>';
  array_unshift($links, $settings_link);
  return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'opt_img_settings_link');

// Admin class containing admin panel functionalities of the plugin.
require_once OPT_IMG_INC_DIR . '/class-opt-img-admin-notice.php';
$opt_img_admin_notice = new Opt_Img_Admin_Notice();
$opt_img_admin_notice->add_hooks();

// User support class containing user support functionalities of the plugin.
require_once OPT_IMG_INC_DIR . '/class-opt-img-user-support.php';
$opt_img_user_support = new Opt_Img_User_Support();

// Multi-form support class containing supportive functionalities for various forms.
require_once OPT_IMG_INC_DIR . '/class-opt-img-multi-forms-support.php';
$opt_img_multi_form_support = new Opt_Img_Multi_Form_Support();
$opt_img_multi_form_support->add_hooks();

// Common functions file of plugin's core functions.
require_once OPT_IMG_INC_DIR . '/opt-img-common-functions.php';

// Admin settings functions file of plugin's settings handler functions.
require_once OPT_IMG_INC_DIR . '/opt-img-admin-settings.php';
