
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div id="map-assessment-admin">
        <div class="row">
            <div class="col-md-6">
                <h2>Create New Map Assessment</h2>
                <form id="map-assessment-form">
                    <div class="form-group">
                        <label for="question">Question:</label>
                        <input type="text" id="question" name="question" class="form-control" required>
                    </div>
                    <div id="map" style="height: 400px;"></div>
                    <button type="submit" class="btn btn-primary">Save Question</button>
                </form>
            </div>
            <div class="col-md-6">
                <h2>User Submissions</h2>
                <div id="user-submissions"></div>
            </div>
        </div>
    </div>
</div>
