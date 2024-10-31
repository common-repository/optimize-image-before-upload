<?php

/**
 * Support View
 *
 * This page will render when the Imagick library doesn't exist.
 *
 * @package Optimize_Image_Before_Upload
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Get the current PHP version.
$php_version = phpversion();

// Get the extension directory.
$extension_dir = ini_get('extension_dir');
?>

<div class="notice notice-warning">
    <p>
        <strong><?php echo esc_html(__('The Imagick library is required for the OptiImage - Upload Optimizer plugin to function correctly.', 'optimize-image-before-upload')); ?></strong>
    </p>
    <p>
        <?php echo esc_html(__('Please follow the instructions below to install the Imagick library on your server.', 'optimize-image-before-upload')); ?>
    </p>

    <h2>
        <?php echo esc_html(__('Installation Instructions', 'optimize-image-before-upload')); ?>
    </h2>

    <ol>
        <li>
            <strong>
                <?php echo esc_html(__('Shared Hosting:', 'optimize-image-before-upload')); ?>
            </strong>
            <?php echo esc_html(__('Contact your hosting provider and ask them to install or enable the Imagick library on your server.', 'optimize-image-before-upload')); ?>
        </li>
        <li>
            <strong>
                <?php echo esc_html(__('MAMP (macOS):', 'optimize-image-before-upload')); ?>
            </strong>
            <ol>
                <li><?php echo esc_html(__('Install ImageMagick using Homebrew:', 'optimize-image-before-upload')); ?> <code>brew install imagemagick</code></li>
                <li>
                    <?php
                    $imagick_extension_path = sprintf(
                        /* translators: %s: PHP version */
                        esc_html__('/Applications/MAMP/bin/php/php%s/lib/php/extensions/no-debug-non-zts-[timestamp]/imagick.so', 'optimize-image-before-upload'),
                        $php_version
                    );
                    ?>
                    <p>
                        <?php
                        printf(
                            /* translators: %s: Imagick extension path */
                            esc_html__('Locate the Imagick extension in your MAMP installation directory: %s', 'optimize-image-before-upload'),
                            '<code>' . esc_html($imagick_extension_path) . '</code>'
                        );
                        ?>
                    </p>
                </li>
                <li>
                    <?php
                    $php_ini_path = sprintf(
                        /* translators: %s: PHP version */
                        esc_html__('/Applications/MAMP/bin/php/php%s/conf/php.ini', 'optimize-image-before-upload'),
                        $php_version
                    );
                    ?>
                    <p>
                        <?php
                        printf(
                            /* translators: %s: PHP configuration file path */
                            esc_html__('Open the php.ini file for your PHP version in MAMP: %s', 'optimize-image-before-upload'),
                            '<code>' . esc_html($php_ini_path) . '</code>'
                        );
                        ?>
                    </p>
                </li>
                <li>
                    <p>
                        <?php
                        printf(
                            /* translators: %s: Imagick extension path */
                            esc_html__('Add the following line to the php.ini file: %s', 'optimize-image-before-upload'),
                            '<code>extension=imagick</code>'
                        );
                        ?>
                    </p>
                </li>
                <li><?php echo esc_html(__('Restart MAMP for the changes to take effect.', 'optimize-image-before-upload')); ?></li>
            </ol>
        </li>
        <li>
            <strong>
                <?php echo esc_html(__('XAMPP (Windows):', 'optimize-image-before-upload')); ?>
            </strong>
            <ol>
                <li>
                    <?php
                    printf(
                        /* translators: %s: ImageMagick download URL */
                        esc_html__('Download the ImageMagick binaries for Windows from the official website: %s', 'optimize-image-before-upload'),
                        '<a href="https://imagemagick.org/script/download.php#windows" target="_blank">https://imagemagick.org/script/download.php#windows</a>'
                    );
                    ?>
                </li>
                <li><?php echo esc_html(__('Install ImageMagick on your system.', 'optimize-image-before-upload')); ?></li>
                <li>
                    <?php
                    printf(
                        /* translators: %s: Imagick extension download URL */
                        esc_html__('Download the Imagick extension for your PHP version from: %s', 'optimize-image-before-upload'),
                        '<a href="https://windows.php.net/downloads/pecl/releases/imagick/" target="_blank">https://windows.php.net/downloads/pecl/releases/imagick/</a>'
                    );
                    ?>
                </li>
                <li>
                    <p>
                        <?php
                        printf(
                            /* translators: %s: XAMPP installation directory */
                            esc_html__('Copy the php_imagick.dll file to the ext directory of your XAMPP installation: %s', 'optimize-image-before-upload'),
                            '<code>C:\xampp\php\ext\</code>'
                        );
                        ?>
                    </p>
                </li>
                <li>
                    <p>
                        <?php
                        printf(
                            /* translators: %s: PHP configuration file path */
                            esc_html__('Open the php.ini file in your XAMPP installation: %s', 'optimize-image-before-upload'),
                            '<code>C:\xampp\php\php.ini</code>'
                        );
                        ?>
                    </p>
                </li>
                <li><?php echo esc_html(__('Add the following line to the php.ini file:', 'optimize-image-before-upload')); ?> <code>extension=php_imagick.dll</code></li>
                <li><?php echo esc_html(__('Restart XAMPP for the changes to take effect.', 'optimize-image-before-upload')); ?></li>
            </ol>
        </li>
        <li>
            <strong>
                <?php echo esc_html(__('LAMP (Linux):', 'optimize-image-before-upload')); ?>
            </strong>
            <ol>
                <li><?php echo esc_html(__('Install ImageMagick and the Imagick extension using the package manager for your Linux distribution.', 'optimize-image-before-upload')); ?></li>
                <li>
                    <strong>
                        <?php echo esc_html(__('Ubuntu/Debian:', 'optimize-image-before-upload')); ?>
                    </strong>
                    <code>sudo apt-get install imagemagick php-imagick</code>
                </li>
                <li>
                    <strong>
                        <?php echo esc_html(__('CentOS/RHEL:', 'optimize-image-before-upload')); ?>
                    </strong>
                    <code>sudo yum install ImageMagick php-imagick</code>
                </li>
                <li>
                    <?php
                    printf(
                        /* translators: %1$s: Apache restart command for Ubuntu/Debian, %2$s: Apache restart command for CentOS/RHEL */
                        esc_html__('Restart Apache for the changes to take effect: %1$s or %2$s', 'optimize-image-before-upload'),
                        '<code>sudo service apache2 restart</code>',
                        '<code>sudo systemctl restart httpd</code>'
                    );
                    ?>
                </li>
            </ol>
        </li>
    </ol>

    <p>
        <?php echo esc_html(__('After completing the installation, the Imagick library should be available for use by the OptiImage - Upload Optimizer plugin.', 'optimize-image-before-upload')); ?>
    </p>

    <div class="opt-img-support-box">
        <h3><?php echo esc_html(__('Need Further Assistance?', 'optimize-image-before-upload')); ?></h3>
        <p>
            <?php echo esc_html(__('If you are still facing issues after following the installation instructions, our support team is here to help!', 'optimize-image-before-upload')); ?>
        </p>
        <a href="https://www.yudiz.com/wordpress-plugin-support/" target="_blank" class="button button-primary opt-img-support-button">
            <?php echo esc_html(__('Contact Support', 'optimize-image-before-upload')); ?>
        </a>
    </div>
</div>