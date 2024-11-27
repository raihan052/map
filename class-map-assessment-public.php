
<?php

class Map_Assessment_Public {

    private string $plugin_name;
    private string $version;

    public function __construct(string $plugin_name, string $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles(): void {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'map-assessment-public.css', [], $this->version, 'all');
        wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css', [], '1.7.1', 'all');
        wp_enqueue_style('leaflet-draw', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css', [], '1.0.4', 'all');
    }

    public function enqueue_scripts(): void {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'map-assessment-public.js', ['jquery'], $this->version, false);
        wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js', [], '1.7.1', false);
        wp_enqueue_script('leaflet-draw', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js', ['leaflet'], '1.0.4', false);
        wp_localize_script($this->plugin_name, 'map_assessment_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('map_assessment_nonce')
        ]);
    }

    public function display_map_assessment($atts): string {
        $atts = shortcode_atts([
            'id' => 0,
        ], $atts, 'map_assessment');

        $question_id = intval($atts['id']);

        if ($question_id <= 0) {
            return 'Invalid question ID';
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'map_assessment_questions';
        $question = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $question_id));

        if (!$question) {
            return 'Question not found';
        }

        wp_enqueue_style($this->plugin_name);
        wp_enqueue_style('leaflet');
        wp_enqueue_style('leaflet-draw');
        wp_enqueue_script($this->plugin_name);
        wp_enqueue_script('leaflet');
        wp_enqueue_script('leaflet-draw');

        wp_localize_script($this->plugin_name, 'map_assessment_data', [
            'question_id' => $question_id,
            'green_marker_lat' => $question->green_marker_lat,
            'green_marker_lng' => $question->green_marker_lng,
            'red_marker_lat' => $question->red_marker_lat,
            'red_marker_lng' => $question->red_marker_lng,
        ]);

        ob_start();
        include('map-assessment-public-display.php');
        return ob_get_clean();
    }

    public function save_user_submission(): void {
        check_ajax_referer('map_assessment_nonce', 'security');

        $question_id = intval($_POST['question_id'] ?? 0);
        $user_route = sanitize_text_field($_POST['user_route'] ?? '');

        if ($question_id <= 0 || empty($user_route)) {
            wp_send_json_error(['message' => 'Invalid input data']);
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'map_assessment_submissions';

        $result = $wpdb->insert(
            $table_name,
            [
                'question_id' => $question_id,
                'user_id' => get_current_user_id(),
                'user_route' => $user_route,
            ]
        );

        if ($result === false) {
            wp_send_json_error(['message' => 'Failed to save submission']);
        } else {
            wp_send_json_success(['message' => 'Submission saved successfully']);
        }
    }

    public function get_user_feedback(): void {
        check_ajax_referer('map_assessment_nonce', 'security');

        $question_id = intval($_POST['question_id'] ?? 0);
        $user_id = get_current_user_id();

        if ($question_id <= 0) {
            wp_send_json_error(['message' => 'Invalid question ID']);
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'map_assessment_submissions';

        $submission = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE question_id = %d AND user_id = %d ORDER BY id DESC LIMIT 1",
            $question_id,
            $user_id
        ));

        if ($submission && $submission->admin_feedback) {
            wp_send_json_success([
                'feedback' => $submission->admin_feedback,
                'admin_route' => $submission->admin_route
            ]);
        } else {
            wp_send_json_error(['message' => 'No feedback available yet.']);
        }
    }
}
