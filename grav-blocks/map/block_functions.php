<?php
$google_maps_api_key = GRAV_BLOCKS_PLUGIN_SETTINGS::get_setting_value('google_maps_api_key');

if ($google_maps_api_key) {
    wp_enqueue_script('google_maps_api','https://maps.googleapis.com/maps/api/js?key='.$google_maps_api_key, array('jquery'), true, true);
    wp_enqueue_script('infobubble_js', plugin_dir_url(__FILE__).'infobubble.js', array('jquery'), true, true);
    wp_enqueue_script('map_block_js', plugin_dir_url(__FILE__).'map-block.js', array('jquery'), true, true);
}
