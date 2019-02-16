<?php
$format = isset($format) ? $format : get_sub_field('format');
$map_position = isset($map_position) ? $map_position : get_sub_field('map_position');
$markers = isset($markers) ? $markers : get_sub_field('markers');

$location_data = array();
$infowindow_data = array();

if ($markers) {
	foreach ($markers as $key => $marker) {
		$lat = $marker['location']['lat'];
		$lng = $marker['location']['lng'];

		$location_data[$key] = array(
			$marker['marker_name'],
			$lat,
			$lng
		);

		$infowindow_data[$key] = array(
			'marker_name' => '<h5>'.$marker['marker_name'].'</h5>',
			'marker_text' => trim($marker['info_window'], " \t\n\r\0\x0B"),
			'marker_link' => '',
			'marker_link_text' => '',
		);

		if ($marker['link_type'] !== 'none') {
			$infowindow_data[$key]['marker_link_text'] = $marker['link_text'];
			$infowindow_data[$key]['marker_link'] = ($marker['link_type'] == 'directions') ? 
				'https://www.google.com/maps/dir/Current+Location/'.$lat.','.$lng :
				$marker['link_'.$marker['link_type']];
		}
	}
}

$map_block_api_key = GRAV_BLOCKS_PLUGIN_SETTINGS::get_setting_value('google_maps_api_key');

if ($map_block_api_key) {
	$map_order = ' medium-order-1';
	$map_col = 12;
	$content_col = 12;

	if ($format !== 'map') {
		$map_order = ($map_position == 'right') ? 'medium-order-2' : 'medium-order-1';
		$content_order = ($map_position == 'right') ? 'medium-order-1' : 'medium-order-2';
		$map_col = ($format == 'small-map') ? 4 : 8;
		$content_col = ($format == 'small-map') ? 8 : 4;
	}

	$markers = get_sub_field('markers');

	if ($markers)
	{
		?>
		<div class="block-inner">
			<div class="<?php echo GRAV_BLOCKS::css()->row()->get(); ?> align-center">
				<?php /* map */ ?>
				<?php
				$map_classes = [
					GRAV_BLOCKS::css()->col(12, $map_col)->get(),
					$map_order,
					'map'
				];
				?>
				<div class="<?php echo implode(' ', $map_classes); ?>">
					<div data-zoom="<?php the_sub_field('zoom_offset'); ?>"
						id="<?php echo GRAV_BLOCKS::$block_index;?>_map"
						class="block-map__google-map">
					</div>
				</div>
				<?php
				/* content */
				if ($format !== 'map')
				{
					$content_classes = [
						GRAV_BLOCKS::css()->col(12, $content_col)->get(),
						$content_order,
						'content',
						GRAV_BLOCKS::get_wysiwyg_container_class()
					];

					?>
					<div class="<?php echo implode(' ', $content_classes); ?>">
						<?php the_sub_field('content'); ?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	$script_vars = [
		'locations' => json_encode($location_data),
		'infoWindows' => json_encode($infowindow_data),
		'markerClose' => apply_filters('grav_blocks_map_marker_close_image_url', plugin_dir_url(__FILE__).'/assets/map-close.png'),
		'markerUrl' => apply_filters('grav_blocks_map_marker_pin_image_url', plugin_dir_url(__FILE__).'/assets/map-marker.png'),
		'markerCloseSvg' => apply_filters('grav_blocks_map_marker_close_svg_url', plugin_dir_url(__FILE__).'/assets/map-close.svg'),
		'markerUrlSvg' => apply_filters('grav_blocks_map_marker_pin_svg_url', plugin_dir_url(__FILE__).'/assets/map-marker.svg'),
		'infoBubbleParams' => apply_filters('grav_blocks_map_infobubble_params', [
			'backgroundColor' => '#fff',
            'borderColor' => '#000',
            'padding' => 10,
            'borderRadius' => 0,
            'arrowSize' => 10,
		])
	];

	$custom_styles = GRAV_BLOCKS_PLUGIN_SETTINGS::get_setting_value('google_maps_styles');

	if ($custom_styles) {
		$script_vars['customMapStyles'] = stripcslashes($custom_styles);
	}

	$block_index = GRAV_BLOCKS::$block_index;

	add_action('wp_footer', function() use ($script_vars, $block_index) {
		?>
		<script type="text/javascript">
			var mapBlockConfig<?php echo $block_index ?> = <?php echo json_encode($script_vars); ?>;
		</script>
		<?php
	}, 1);
}
else
{
	?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get();?> align-center">
			<div class="<?php echo GRAV_BLOCKS::css()->col(12, 10)->get(); ?>">
				<h2>Please add a Google Maps API key to Gravitate Blocks General Settings</h2>
			</div>
		</div>
	</div>
	<?php
}
