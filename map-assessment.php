<?php
/**
 * Plugin Name: Map Assessment and Training System
 * Plugin URI: https://example.com/map-assessment-plugin
 * Description: A plugin for creating and managing map-based assessments and training.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: map-assessment
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die('Direct access is not allowed.');
}

// Define plugin constants
define('MAP_ASSESSMENT_VERSION', '1.0.0');
define('MAP_ASSESSMENT_PATH', plugin_dir_path(__FILE__));
define('MAP_ASSESSMENT_URL', plugin_dir_url(__FILE__));

// Load required files
require_once MAP_ASSESSMENT_PATH . 'class-map-assessment.php';
require_once MAP_ASSESSMENT_PATH . 'class-map-assessment-admin.php';
require_once MAP_ASSESSMENT_PATH . 'class-map-assessment-public.php';

// Initialize the plugin
function run_map_assessment() {
    $plugin = new Map_Assessment();
    $plugin->run();
}

// Run the plugin
add_action('plugins_loaded', 'run_map_assessment');
