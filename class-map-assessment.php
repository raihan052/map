<?php

if (!class_exists('Map_Assessment')) {
    class Map_Assessment {
        protected Map_Assessment_Loader $loader;
        protected string $plugin_name;
        protected string $version;

        public function __construct() {
            $this->version = MAP_ASSESSMENT_VERSION;
            $this->plugin_name = 'map-assessment';
            $this->load_dependencies();
            $this->set_locale();
            $this->define_admin_hooks();
            $this->define_public_hooks();
        }

        private function load_dependencies(): void {
            require_once MAP_ASSESSMENT_PATH . 'class-map-assessment-loader.php';
            require_once MAP_ASSESSMENT_PATH . 'class-map-assessment-i18n.php';

            $this->loader = new Map_Assessment_Loader();
        }

        private function set_locale(): void {
            $plugin_i18n = new Map_Assessment_i18n();
            $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
        }

        private function define_admin_hooks(): void {
            $plugin_admin = new Map_Assessment_Admin($this->get_plugin_name(), $this->get_version());

            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
            $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
            $this->loader->add_action('wp_ajax_save_map_question', $plugin_admin, 'save_map_question');
            $this->loader->add_action('wp_ajax_get_user_submissions', $plugin_admin, 'get_user_submissions');
            $this->loader->add_action('wp_ajax_save_admin_feedback', $plugin_admin, 'save_admin_feedback');
        }

        private function define_public_hooks(): void {
            $plugin_public = new Map_Assessment_Public($this->get_plugin_name(), $this->get_version());

            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
            $this->loader->add_shortcode('map_assessment', $plugin_public, 'display_map_assessment');
            $this->loader->add_action('wp_ajax_save_user_submission', $plugin_public, 'save_user_submission');
            $this->loader->add_action('wp_ajax_nopriv_save_user_submission', $plugin_public, 'save_user_submission');
            $this->loader->add_action('wp_ajax_get_user_feedback', $plugin_public, 'get_user_feedback');
            $this->loader->add_action('wp_ajax_nopriv_get_user_feedback', $plugin_public, 'get_user_feedback');
        }

        public function run(): void {
            $this->loader->run();
        }

        public function get_plugin_name(): string {
            return $this->plugin_name;
        }

        public function get_loader(): Map_Assessment_Loader {
            return $this->loader;
        }

        public function get_version(): string {
            return $this->version;
        }
    }
}
