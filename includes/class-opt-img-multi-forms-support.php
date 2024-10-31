<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Opt_Img_Multi_Form_Support Class
 *
 * It provides Multiform Image Optimization support
 *
 * @package Optimize_Image_Before_Upload
 * @since 1.0.0
 */
class Opt_Img_Multi_Form_Support
{

	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		// Empty constructor.
	}

	/**
	 * Add hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_hooks()
	{
		if (OPT_IMG_LIBRARY_STATUS == "1") {
			add_filter('wp_handle_upload', array($this, 'handle_html_wpforms_image_optimization'), 10, 2);
			add_filter('wpcf7_mail_components', array($this, 'handle_cf7_image_optimization'), 10, 3);
			add_filter('gform_entry_post_save', array($this, 'handle_gravity_forms_image_optimization'), 10, 2);
			add_action('elementor_pro/forms/new_record', array($this, 'handle_elementor_forms_image_optimization'), 10, 2);
		}
	}

	/**
	 * Optimize images uploaded through HTML forms and WP Forms.
	 *
	 * This function hooks into the 'wp_handle_upload' filter and optimizes images
	 * uploaded through HTML forms. It replaces the original file with the optimized
	 * version in the same directory, keeping the original file name.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $upload  The upload data array.
	 * @param string $context The upload context (e.g., 'upload', 'sideload', 'media').
	 *
	 * @return array The updated upload data array with the optimized image file.
	 */
	public function handle_html_wpforms_image_optimization($upload, $context)
	{
		// Check if the uploaded file is an image.
		if (!empty($upload['type']) && strstr($upload['type'], 'image/')) {
			$upload_dir    = dirname($upload['file']) . '/';
			$original_file = $upload['file'];
			$original_size = filesize($original_file);

			// Check if backend optimization is enabled in admin settings for media uploads
			$backend_optimization_enabled = get_option('opt_img_enable_backend_optimization', '1'); // Assuming default is enabled

			// Check if conditions for WordPress media optimization are met
			if ($context == "upload" && $backend_optimization_enabled == "1") {
				if ($original_size >= OPT_IMG_THRESHOLD_SIZE) {
					// Optimize image using common function.
					$imagick = opt_img_imagick_image_process($original_file);

					// Get the original file name.
					$original_file_name = basename($original_file);

					// Move optimized file to the upload folder with the original name.
					$optimized_file = $upload_dir . $original_file_name;
					$imagick->writeImage($optimized_file);

					// Get optimized size.
					$optimized_size = filesize($optimized_file);

					// Update the file path in the upload data array.
					$upload['file'] = $optimized_file;
				}
			}
			// Check if conditions for WP Forms optimization are met
			elseif ($context == "sideload") {
				// Optimize image using common function.
				$imagick = opt_img_imagick_image_process($original_file);

				// Get the original file name.
				$original_file_name = basename($original_file);

				// Move optimized file to the upload folder with the original name.
				$optimized_file = $upload_dir . $original_file_name;
				$imagick->writeImage($optimized_file);

				// Get optimized size.
				$optimized_size = filesize($optimized_file);

				// Update the file path in the upload data array.
				$upload['file'] = $optimized_file;
			}
		}

		// Return the upload data array (modified or original)
		return $upload;
	}

	/**
	 * Optimize images uploaded through Contact Form 7 before sending the email.
	 *
	 * This function hooks into the 'wpcf7_mail_components' filter and optimizes images
	 * uploaded through Contact Form 7 forms. It replaces the original file paths in
	 * the 'uploads' array with the optimized file paths.
	 *
	 * @since 1.0.0
	 *
	 * @param array             $components The mail components array.
	 * @param WPCF7_ContactForm $form       The Contact Form 7 form object.
	 * @param WPCF7_Mail        $mail       The Contact Form 7 mail object.
	 *
	 * @return array The updated mail components array with optimized image paths.
	 */
	public function handle_cf7_image_optimization($components, $form, $mail)
	{
		// Include necessary files for WP_Filesystem.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		$creds = request_filesystem_credentials(site_url());
		if (!WP_Filesystem($creds)) {
			return $components;
		}
		global $wp_filesystem;

		$submission       = WPCF7_Submission::get_instance();
		$files            = $submission->uploaded_files();
		$uploads_dir      = wp_upload_dir();
		$uploads_path     = trailingslashit($uploads_dir['basedir']);
		$cf7_uploads_path = $uploads_path . 'wpcf7_uploads/';

		foreach ($files as $name => $paths) {
			$optimized_data = array();
			$paths          = is_array($paths) ? $paths : array($paths);

			foreach ($paths as $index => $path) {
				$file_type = wp_check_filetype($path);

				if (strstr($file_type['type'], 'image/')) {
					// Get the file size.
					$file_size = filesize($path);

					if ($file_size >= OPT_IMG_THRESHOLD_SIZE) {
						$imagick         = opt_img_imagick_image_process($path);
						$optimized_data[] = array(
							'name' => basename($path),
							'path' => $path,
							'data' => $imagick->getImageBlob(),
							'size' => $imagick->getImageLength(),
						);
					} else {
						// If the file size is less than the threshold size, use the raw image (non-optimized).
						$raw_image_data   = $wp_filesystem->get_contents($path);
						$optimized_data[] = array(
							'name' => basename($path),
							'path' => $path,
							'data' => $raw_image_data,
							'size' => $file_size,
						);
					}
				}
			}

			if (!empty($optimized_data)) {
				$optimized_paths = array();

				foreach ($optimized_data as $file) {
					if (isset($file['data']) && isset($cf7_uploads_path) && isset($wp_filesystem)) {
						$optimized_file = $file['path'];

						if (is_string($file['data'])) {
							// If the data is a string, write it directly.
							$wp_filesystem->put_contents($optimized_file, $file['data'], FS_CHMOD_FILE);
						} else {
							// If the data is a file path, copy the file.
							$wp_filesystem->copy($file['data'], $optimized_file);
						}

						$optimized_paths[] = $optimized_file;
					}
				}

				// Initialize 'uploads' key if it doesn't exist.
				if (!isset($components['uploads'])) {
					$components['uploads'] = array();
				}

				if (isset($name)) {
					$components['uploads'][$name] = $optimized_paths;
				}
			}
		}

		return $components;
	}

	/**
	 * Optimize images uploaded through Gravity Forms.
	 *
	 * Optimized image will be stored at uploads folder with original name
	 * (ORG will be stored at Gravity form folder (Default functionality)).
	 *
	 * @since 1.0.0
	 *
	 * @param array $entry Gravity Forms entry array.
	 * @param array $form  Gravity Forms form array.
	 *
	 * @return array Updated entry array with optimized image paths.
	 */
	public function handle_gravity_forms_image_optimization($entry, $form)
	{
		$uploads_dir  = wp_upload_dir();
		$uploads_path = trailingslashit($uploads_dir['basedir']);

		foreach ($form['fields'] as $field) {
			if ($field->type === 'fileupload') {
				$field_id    = $field->id;
				$field_value = rgar($entry, $field_id);

				if (!empty($field_value)) {
					$optimized_path = '';

					// Convert $field_value to a string.
					$file_path = is_array($field_value) ? implode(',', $field_value) : $field_value;

					// Check if the file path is a URL.
					$is_url = filter_var($file_path, FILTER_VALIDATE_URL);

					if ($is_url) {
						// Convert the URL to a local file path.
						$local_file_path = str_replace(site_url('/'), ABSPATH, $file_path);
					} else {
						$local_file_path = $file_path;
					}

					$file_type = wp_check_filetype($local_file_path);

					if (strstr($file_type['type'], 'image/')) {
						$uploadDir    = dirname($local_file_path) . '/';
						$originalFile = $local_file_path;

						// Get the file size.
						$file_size = filesize($originalFile);

						if ($file_size >= OPT_IMG_THRESHOLD_SIZE) {
							$imagick            = opt_img_imagick_image_process($originalFile);
							$originalFileName   = basename($local_file_path);
							$optimizedFile      = $uploadDir . $originalFileName;
							$imagick->writeImage($optimizedFile);
							$optimized_path     = $optimizedFile;
						} else {
							$originalFileName   = basename($local_file_path);
							$optimizedFile      = $uploadDir . $originalFileName;
							copy($originalFile, $optimizedFile);
							$optimized_path     = $optimizedFile;
						}
					}

					$entry[$field_id] = $optimized_path;
				}
			}
		}

		return $entry;
	}

	/**
	 * Optimize images uploaded through Elementor Forms.
	 *
	 * Optimized image will be stored in the same directory as the original image with the original name.
	 *
	 * @since 1.0.0
	 *
	 * @param object $record       Elementor form record object.
	 * @param object $ajax_handler Elementor AJAX handler object.
	 *
	 * @return void
	 */
	public function handle_elementor_forms_image_optimization($record, $ajax_handler)
	{
		$uploads_dir  = wp_upload_dir();
		$uploads_path = trailingslashit($uploads_dir['basedir']);

		$form_fields = $record->get('fields');

		foreach ($form_fields as $field_id => $field_value) {
			if (!empty($field_value['value'])) {
				$optimized_path = '';

				// Convert $field_value to a string.
				$file_path = is_array($field_value['value']) ? implode(',', $field_value['value']) : $field_value['value'];

				// Check if the file path is a URL.
				$is_url = filter_var($file_path, FILTER_VALIDATE_URL);

				if ($is_url) {
					// Convert the URL to a local file path.
					$local_file_path = str_replace(site_url('/'), ABSPATH, $file_path);
				} else {
					$local_file_path = $file_path;
				}

				$file_type = wp_check_filetype($local_file_path);

				if (strstr($file_type['type'], 'image/')) {
					$uploadDir    = dirname($local_file_path) . '/';
					$originalFile = $local_file_path;

					// Get the file size.
					$file_size = filesize($originalFile);

					if ($file_size >= OPT_IMG_THRESHOLD_SIZE) {
						// Optimize image using common function.
						$imagick = opt_img_imagick_image_process($originalFile);

						// Get the original file name.
						$originalFileName = basename($local_file_path);

						// Move optimized file to the WordPress uploads folder with the original name.
						$optimizedFile = $uploadDir . $originalFileName;

						$imagick->writeImage($optimizedFile);

						$optimized_path = $optimizedFile;
					} else {
						// Original image size is less than or equal to 1MB, copy it to the destination folder.
						$originalFileName = basename($local_file_path);
						$optimizedFile    = $uploadDir . $originalFileName;
						copy($originalFile, $optimizedFile);
						$optimized_path   = $optimizedFile;
					}
				}

				$form_fields[$field_id]['value'] = $optimized_path;
			}
		}

		$record->set('fields', $form_fields);
	}
}
