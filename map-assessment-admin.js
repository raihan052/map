
(function($) {
    'use strict';

    $(function() {
        var map = L.map('map').setView([51.505, -0.09], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var greenMarker, redMarker;
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            draw: {
                polyline: true,
                polygon: false,
                circle: false,
                rectangle: false,
                marker: false
            },
            edit: {
                featureGroup: drawnItems
            }
        });
        map.addControl(drawControl);

        $('#set-green-marker').click(function() {
            if (greenMarker) map.removeLayer(greenMarker);
            greenMarker = L.marker(map.getCenter(), {
                draggable: true,
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                })
            }).addTo(map);
        });

        $('#set-red-marker').click(function() {
            if (redMarker) map.removeLayer(redMarker);
            redMarker = L.marker(map.getCenter(), {
                draggable: true,
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41]
                })
            }).addTo(map);
        });

        $('#create-question').click(function() {
            var question = $('#question-input').val();
            if (!question || !greenMarker || !redMarker) {
                alert('Please set both markers and enter a question.');
                return;
            }

            var data = {
                action: 'save_map_question',
                security: map_assessment_ajax.nonce,
                question: question,
                green_marker: greenMarker.getLatLng(),
                red_marker: redMarker.getLatLng()
            };

            $.post(map_assessment_ajax.ajax_url, data, function(response) {
                if (response.success) {
                    $('#shortcode-display').show();
                    $('#shortcode').val(response.data.shortcode);
                } else {
                    alert('Error saving question. Please try again.');
                }
            });
        });

        $('#results').click(function() {
            $.ajax({
                url: map_assessment_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_user_submissions',
                    security: map_assessment_ajax.nonce,
                    question_id: currentQuestionId // You need to set this when creating/editing a question
                },
                success: function(response) {
                    if (response.success) {
                        displayUserSubmissions(response.data.submissions);
                    } else {
                        alert('Error fetching submissions');
                    }
                }
            });
        });

        function displayUserSubmissions(submissions) {
            $('#user-submissions').show();
            var submissionList = $('#submission-list');
            submissionList.empty();

            submissions.forEach(function(submission) {
                var listItem = $('<li>').text('User ' + submission.user_id + ' submission');
                var viewButton = $('<button>').text('View/Edit').click(function() {
                    viewSubmission(submission);
                });
                listItem.append(viewButton);
                submissionList.append(listItem);
            });
        }

        function viewSubmission(submission) {
            drawnItems.clearLayers();
            var userRoute = L.geoJSON(JSON.parse(submission.user_route)).addTo(drawnItems);
            map.fitBounds(userRoute.getBounds());

            var feedbackForm = $('<form>').append(
                $('<textarea>').attr('id', 'feedback-text').val(submission.admin_feedback || ''),
                $('<button>').text('Send Feedback').click(function(e) {
                    e.preventDefault();
                    sendFeedback(submission.id, $('#feedback-text').val(), drawnItems.toGeoJSON());
                })
            );

            $('#feedback-area').html(feedbackForm);
        }

        function sendFeedback(submissionId, feedback, adminRoute) {
            $.ajax({
                url: map_assessment_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'save_admin_feedback',
                    security: map_assessment_ajax.nonce,
                    submission_id: submissionId,
                    feedback: feedback,
                    admin_route: JSON.stringify(adminRoute)
                },
                success: function(response) {
                    if (response.success) {
                        alert('Feedback sent successfully');
                    } else {
                        alert('Error sending feedback');
                    }
                }
            });
        }

        map.on(L.Draw.Event.CREATED, function (event) {
            var layer = event.layer;
            drawnItems.addLayer(layer);
        });
    });

})(jQuery);
