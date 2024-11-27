
<?php

class Map_Assessment_Admin {

    private string $plugin_name;
    private string $version;

    public function __construct(string $plugin_name, string $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles(): void {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'map-assessment-admin.css', [], $this->version, 'all');
        wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css', [], '1.7.1', 'all');
        wp_enqueue_style('leaflet-draw', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css', [], '1.0.4', 'all');
    }

    public function enqueue_scripts(): void {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'map-assessment-admin.js', ['jquery'], $this->version, false);
        wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js', [], '1.7.1', false);
        wp_enqueue_script('leaflet-draw', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js', ['leaflet'], '1.0.4', false);
        wp_localize_script($this->plugin_name, 'map_assessment_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('map_assessment_nonce')
        ]);
    }

    public function add_plugin_admin_menu(): void {
        add_menu_page(
            'Map Assessment', 
            'Map Assessment', 
            'manage_options', 
            $this->plugin_name, 
            [$this, 'display_plugin_setup_page'],
            'dashicons-location-alt',
            6
        );
    }

    public function display_plugin_setup_page(): void {
        include_once('map-assessment-admin-display.php');
    }

    public function save_map_question(): void {
        check_ajax_referer('map_assessment_nonce', 'security');

        $question = sanitize_text_field($_POST['question'] ?? '');
        $green_marker = sanitize_text_field($_POST['green_marker'] ?? '');
        $red_marker = sanitize_text_field($_POST['red_marker'] ?? '');

        if (empty($question) || empty($green_marker) || empty($red_marker)) {
            wp_send_json_error(['message' => 'Invalid input data']);
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'map_assessment_questions';

        $result = $wpdb->insert(
            $table_name,
            [
                'question' => $question,
                'green_marker' => $green_marker,
                'red_marker' => $red_marker,
            ]
        );

        if ($result === false) {
            wp_send_json_error(['message' => 'Failed to save question']);
            return;
        }

        $question_id = $wpdb->insert_id;

        wp_send_json_success([
            'message' => 'Question saved successfully',
            'shortcode' => '[map_assessment id="' . $question_id . '"]'
        ]);
    }

    public function get_user_submissions(): void {
        check_ajax_referer('map_assessment_nonce', 'security');

        $question_id = intval($_POST['question_id'] ?? 0);

        if ($question_id <= 0) {
            wp_send_json_error(['message' => 'Invalid question ID']);
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'map_assessment_submissions';

        $submissions = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE question_id = %d",
            $question_id
        ));

        wp_send_json_success(['submissions' => $submissions]);
    }

    public function save_admin_feedback(): void {
        check_ajax_referer('map_assessment_nonce', 'security');

        $submission_id = intval($_POST['submission_id'] ?? 0);
        $feedback = sanitize_textarea_field($_POST['feedback'] ?? '');
        $admin_route = sanitize_text_field($_POST['admin_route'] ?? '');

        if ($submission_id <= 0 || empty($feedback) || empty($admin_route)) {
            wp_send_json_error(['message' => 'Invalid input data']);
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'map_assessment_submissions';

        $result = $wpdb->update(
            $table_name,
            [
                'admin_feedback' => $feedback,
                'admin_route' => $admin_route,
            ],
            ['id' => $submission_id]
        );

        if ($result === false) {
            wp_send_json_error(['message' => 'Failed to save feedback']);
        } else {
            wp_send_json_success(['message' => 'Feedback saved successfully']);
        }
    }
}
