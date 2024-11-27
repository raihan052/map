
<div class="map-assessment-container">
    <h2><?php echo esc_html($question->question); ?></h2>
    <div id="map-assessment-map" style="height: 400px;"></div>
    <form id="map-assessment-form">
        <input type="hidden" name="question_id" value="<?php echo esc_attr($question_id); ?>">
        <button type="submit" class="btn btn-primary">Submit Answer</button>
    </form>
    <div id="feedback-container" style="display: none;">
        <h3>Feedback</h3>
        <p id="feedback-text"></p>
        <div id="admin-route-map" style="height: 400px;"></div>
    </div>
</div>
