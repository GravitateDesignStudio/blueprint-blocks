<?php

$format = isset($format) ? $format : get_sub_field('format');
$map_position = isset($map_position) ? $map_position : get_sub_field('map_position');

$markers = isset($markers) ? $markers : get_sub_field('markers');

$location_data = array();
$infowindow_data = array();

if( $markers ){
	foreach ($markers as $marker) {

		$location_data[] = array(
			$marker['marker_name'],
			$marker['lattitude'],
			$marker['longitude']);

		$infowindow_data[] = array (
			'marker_name' => "<h3>" . $marker['marker_name'] . "</h3>",
			'marker_text' => trim($marker['marker_name'], " \t\n\r\0\x0B"),
		);

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
		$content_col = 4;
	}

	if($markers = get_sub_field('markers'))
	{ ?>

		<div class="block-inner">
			<div class="<?php echo GRAV_BLOCKS::css()->row()->get();?> align-center">
				<!-- Map -->
				<div class="<?php echo GRAV_BLOCKS::css()->col(12, $map_col)->get() . $map_order;?> map">
					<div
					data-zoom="<?php the_sub_field('zoom_offset'); ?>"
					id="<?php echo GRAV_BLOCKS::$block_index;?>_map" class="google-map">

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
	wp_localize_script( 'map_block_js', 'marker_url', plugin_dir_url(__FILE__) . '/assets/map-marker.png' );

} else { ?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get();?> align-center">
			<div class="<?php echo GRAV_BLOCKS::css()->col(12, 10)->get(); ?>">
				<h2>Please add a Google Map API key to Gravitate Blocks General Settings</h2>
			</div>
		</div>

	</div>
<?php }
