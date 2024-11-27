
(function($) {
    'use strict';

    $(function() {
        var map = L.map('map').setView([51.505, -0.09], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var greenMarker = L.marker([map_assessment_data.green_marker_lat, map_assessment_data.green_marker_lng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            })
        }).addTo(map);

        var redMarker = L.marker([map_assessment_data.red_marker_lat, map_assessment_data.red_marker_lng], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41]
            })
        }).addTo(map);

        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawing = false;
        var currentPolyline;

        $('#toggle-drawing').click(function() {
            drawing = !drawing;
            if (drawing) {
                $(this).text('Stop Drawing').removeClass('btn-success').addClass('btn-warning');
                currentPolyline = L.polyline([], {color: 'blue'}).addTo(drawnItems);
                map.on('click', onMapClick);
            } else {
                $(this).text('Start Drawing').removeClass('btn-warning').addClass('btn-success');
                map.off('click', onMapClick);
            }
        });

        function onMapClick(e) {
            if (drawing && currentPolyline) {
                currentPolyline.addLatLng(e.latlng);
            }
        }

        $('#undo').click(function() {
            if (currentPolyline) {
                var latlngs = currentPolyline.getLatLngs();
                if (latlngs.length > 0) {
                    latlngs.pop();
                    currentPolyline.setLatLngs(latlngs);
                }
            }
        });

        $('#clear').click(function() {
            drawnItems.clearLayers();
            currentPolyline = null;
        });

        $('#brightness').click(function() {
            $('body').toggleClass('dimmed');
        });

        $('#home').click(function() {
            map.setView([51.505, -0.09], 13);
        });

        $('#submit-route').click(function() {
            var route = drawnItems.toGeoJSON();
            if (route.features.length === 0) {
                alert('Please draw a route before submitting.');
                return;
            }

            var data = {
                action: 'save_user_submission',
                security: map_assessment_ajax.nonce,
                question_id: map_assessment_data.question_id,
                user_route: JSON.stringify(route)
            };

            $.post(map_assessment_ajax.ajax_url, data, function(response) {
                if (response.success) {
                    alert('Route submitted successfully!');
                    checkForFeedback();
                } else {
                    alert('Error submitting route. Please try again.');
                }
            });
        });

        function checkForFeedback() {
            var data = {
                action: 'get_user_feedback',
                security: map_assessment_ajax.nonce,
                question_id: map_assessment_data.question_id
            };

            $.post(map_assessment_ajax.ajax_url, data, function(response) {
                if (response.success && response.data.feedback) {
                    displayFeedback(response.data.feedback, response.data.admin_route);
                }
            });
        }

        function displayFeedback(feedback, adminRoute) {
            $('#feedback-area').html('<h3>Admin Feedback:</h3><p>' + feedback + '</p>');
            
            if (adminRoute) {
                var adminPolyline = L.geoJSON(JSON.parse(adminRoute), {
                    style: {color: 'red', weight: 3}
                }).addTo(map);
                map.fitBounds(adminPolyline.getBounds());
            }
        }

        // Check for feedback on page load
        checkForFeedback();
    });

})(jQuery);
