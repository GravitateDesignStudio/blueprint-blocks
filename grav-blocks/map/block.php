<?php

$format = isset($format) ? $format : get_sub_field('format');
$map_position = isset($map_position) ? $map_position : get_sub_field('map_position');

$markers = isset($markers) ? $markers : get_sub_field('markers');

$location_data = array();
$infowindow_data = array();

if( $markers ){
	foreach ($markers as $key => $marker) {

		$location_data[$key] = array(
			$marker['marker_name'],
			$marker['lattitude'],
			$marker['longitude']);

		$infowindow_data[$key] = array (
			'marker_name' => "<h5>" . $marker['marker_name'] . "</h5>",
			'marker_text' => trim($marker['info_window'], " \t\n\r\0\x0B"),
			'marker_link' => '',
			'marker_link_text' => '',

		);

		if ($marker['link_type'] != 'none') {
			$infowindow_data[$key]['marker_link'] = ($marker['link_type'] == 'directions') ? 'https://www.google.com/maps/dir/Current+Location/' . $marker['lattitude'] .',' .$marker['longitude'] : $marker['link_' . $marker['link_type']];
			$infowindow_data[$key]['marker_link_text'] = $marker['link_text'];
		}

	}
}

if ($mapBlockApiKey = GRAV_BLOCKS_PLUGIN_SETTINGS::get_setting_value('google_maps_api_key')) {

	$map_order = ' medium-order-1';

	$map_col = 12;

	$content_col = 12;

	if ($format != 'map') {

		$map_order = ($map_position == 'right') ? ' medium-order-2' : ' medium-order-1';
		$content_order = ($map_position == 'right') ? ' medium-order-1' : ' medium-order-2';

		$map_col = ($format == 'small-map') ? 4 : 8;
		$content_col = ($format == 'small-map') ? 8 : 4;
	}

	if($markers = get_sub_field('markers'))
	{ ?>

		<div class="block-inner">
			<div class="<?php echo GRAV_BLOCKS::css()->row()->get();?> align-center">
				<!-- Map -->
				<div class="<?php echo GRAV_BLOCKS::css()->col(12, $map_col)->get() . $map_order;?> map">
					<div
					data-zoom="<?php the_sub_field('zoom_offset'); ?>"
					id="<?php echo GRAV_BLOCKS::$block_index;?>_map" class="block-map__google-map">

					</div>

				</div>
				<!-- Content -->
				<?php if ($format != 'map'): ?>
					<div class="<?php echo GRAV_BLOCKS::css()->col(12, $content_col)->get() . $content_order; ?> content">
						<?php the_sub_field('content'); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	wp_localize_script( 'map_block_js', 'locations', json_encode($location_data));
	wp_localize_script( 'map_block_js', 'infoWindows', json_encode($infowindow_data));
	wp_localize_script( 'map_block_js', 'customMapStyles', get_sub_field('custom_styles'));
	wp_localize_script( 'map_block_js', 'markerClose', plugin_dir_url(__FILE__) . '/assets/map-close.png');
	wp_localize_script( 'map_block_js', 'marker_url', plugin_dir_url(__FILE__) . '/assets/map-marker.png' );

} else { ?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get();?> align-center">
			<div class="<?php echo GRAV_BLOCKS::css()->col(12, 10)->get(); ?>">
				<h2>Please add a Google Maps API key to Gravitate Blocks General Settings</h2>
			</div>
		</div>

	</div>
<?php }
