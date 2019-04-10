<?php
$format = isset($format) ? $format : get_sub_field('format');
$map_position = isset($map_position) ? $map_position : get_sub_field('map_position');
$markers = isset($markers) ? $markers : get_sub_field('markers');

$marker_data = array_reduce(is_array($markers) ? $markers : [], function ($records, $marker) {
	if (!isset($marker['location']) || !isset($marker['location']['lat']) || !isset($marker['location']['lng'])) {
		return $records;
	}

	$lat = $marker['location']['lat'];
	$lng = $marker['location']['lng'];

	$marker_data = [
		'name' => $marker['marker_name'],
		'lat' => $lat,
		'lng' => $lng,
		'infowindow_data' => [
			'marker_text' => trim($marker['info_window'], " \t\n\r\0\x0B"),
			'marker_link' => '',
			'marker_link_text' => ''
		]
	];

	if ($marker['link_type'] !== 'none') {
		$marker_data['infowindow_data']['marker_link_text'] = $marker['link_text'];
		$marker_data['infowindow_data']['marker_link'] = ($marker['link_type'] == 'directions') ? 
			'https://www.google.com/maps/dir/Current+Location/'.$lat.','.$lng :
			$marker['link_'.$marker['link_type']];
	}

	$records[] = $marker_data;
	
	return $records;
}, []);

$map_block_api_key = GRAV_BLOCKS_PLUGIN_SETTINGS::get_setting_value('google_maps_api_key');

if ($map_block_api_key) {
	$column_sizes = [
		'map' => [
			'small' => 12,
			'medium' => 12,
			'large' => 12,
			'small-offset' => 0,
			'medium-offset' => 0,
			'large-offset' => 0,
			'small-order' => 0,
			'medium-order' => 0,
			'large-order' => 0
		]
	];

	if ($format !== 'map') {
		$column_sizes['map']['medium'] = ($format === 'small-map') ? 4 : 8;
		$column_sizes['map']['large'] = ($format === 'small-map') ? 4 : 8;
		$column_sizes['map']['medium-order'] = ($map_position === 'right') ? 2 : 1;
		$column_sizes['map']['large-order'] = ($map_position === 'right') ? 2 : 1;

		$column_sizes['content'] = [
			'small' => 12,
			'medium' => ($format === 'small-map') ? 8 : 4,
			'large' => ($format === 'small-map') ? 8 : 4,
			'small-offset' => 0,
			'medium-offset' => 0,
			'large-offset' => 0,
			'small-order' => 0,
			'medium-order' => ($map_position === 'right') ? 1 : 2,
			'large-order' => ($map_position === 'right') ? 1 : 2
		];
	}

	$column_sizes = apply_filters('grav_blocks_map_column_sizes', $column_sizes, $format, $map_position);

	$markers = get_sub_field('markers');

	if ($markers)
	{
		?>
		<div class="block-inner">
			<div class="<?php echo GRAV_BLOCKS::css()->row()->get(); ?> align-center">
				<?php /* map */ ?>
				<?php
				$map_classes = [
					GRAV_BLOCKS::css()
						->col($column_sizes['map']['small'], $column_sizes['map']['medium'], $column_sizes['map']['large'])
						->col_offset($column_sizes['map']['small-offset'], $column_sizes['map']['medium-offset'], $column_sizes['map']['large-offset'])
						->col_order($column_sizes['map']['small-order'], $column_sizes['map']['medium-order'], $column_sizes['map']['large-order'])
						->get(),
					'map'
				];
				?>
				<div class="<?php echo implode(' ', $map_classes); ?>">
					<div id="<?php echo GRAV_BLOCKS::$block_index;?>_map" class="block-map__google-map"></div>
				</div>
				<?php
				/* content */
				if ($format !== 'map')
				{
					$content_classes = [
						GRAV_BLOCKS::css()
							->col($column_sizes['content']['small'], $column_sizes['content']['medium'], $column_sizes['content']['large'])
							->col_offset($column_sizes['content']['small-offset'], $column_sizes['content']['medium-offset'], $column_sizes['content']['large-offset'])
							->col_order($column_sizes['content']['small-order'], $column_sizes['content']['medium-order'], $column_sizes['content']['large-order'])
							->get(),
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

	$zoom_value = get_sub_field('zoom_offset');

	$script_vars = [
		'markers' => json_encode($marker_data),
		'markerClose' => apply_filters('grav_blocks_map_marker_close_image_url', plugin_dir_url(__FILE__).'assets/map-close.png'),
		'markerUrl' => apply_filters('grav_blocks_map_marker_pin_image_url', plugin_dir_url(__FILE__).'assets/map-marker.png'),
		'markerCloseSvg' => apply_filters('grav_blocks_map_marker_close_svg_url', plugin_dir_url(__FILE__).'assets/map-close.svg'),
		'markerUrlSvg' => apply_filters('grav_blocks_map_marker_pin_svg_url', plugin_dir_url(__FILE__).'assets/map-marker.svg'),
		'snazzyInfoWindowParams' => apply_filters('grav_blocks_map_snazzyinfowindow_params', []),
		'mapPaddingBottom' => apply_filters('grav_blocks_map_padding_bottom', '62.5%'),
		'zoom' => $zoom_value ? (int)$zoom_value : 8
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
