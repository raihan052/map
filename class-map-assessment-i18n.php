
<?php

class Map_Assessment_i18n {
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'map-assessment',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}
