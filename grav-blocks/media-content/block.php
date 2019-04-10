<?php
$media_type = isset($media_type) ? $media_type : get_sub_field('media_type');

if (!$media_type) {
	$media_type = 'image';
}

$video_url = isset($video_url) ? $video_url : get_sub_field('video_'.get_sub_field('video_type'));

$embed = isset($embed) ? $embed : get_sub_field('embed');
$video_attributes = isset($video_attributes) ? $video_attributes : get_sub_field('video_attributes');

$placement = isset($placement) ? $placement : get_sub_field('image_placement');
$media_column_width = isset($media_column_width) ? (int)$media_column_width : (int)get_sub_field('image_size');
$content = isset($content) ? $content : get_sub_field('content');
$col_array = GRAV_BLOCKS::column_width_options();
$col_class = 'col-option-'.$placement.'-'.sanitize_title($col_array[$media_column_width]);

$col_widths = [
	'small' => [
		'content' => 12,
		'media' => 12
	],
	'medium' => [
		'content' => ($media_column_width > 6) ? 12 : 12 - $media_column_width,
		'media' => ($media_column_width > 6) ? 12 : $media_column_width
	],
	'large' => [
		'content' => 10 - $media_column_width,
		'media' => $media_column_width
	]
];

$col_widths = apply_filters('grav_block_mediacontent_column_widths', $col_widths, $media_column_width, $placement);

$column_content_add_classes = [$col_class, GRAV_BLOCKS::get_wysiwyg_container_class()];
$column_media_add_classes = [$col_class];

if ($placement === 'right') {
	if ($col_widths['medium']['content'] < 12) {
		$column_content_add_classes[] = 'medium-order-1';
		$column_media_add_classes[] = 'medium-order-2';
	}

	if ($col_widths['large']['content'] < 12) {
		$column_content_add_classes[] = 'large-order-1';
		$column_media_add_classes[] = 'large-order-2';
	}
}

$column_content_classes = GRAV_BLOCKS::css()->col(
	$col_widths['small']['content'],
	$col_widths['medium']['content'],
	$col_widths['large']['content']
)->add(implode(', ', $column_content_add_classes))->get();

$column_content_classes = implode(' ', apply_filters('grav_block_mediacontent_content_classes', explode(' ', $column_content_classes), $media_column_width, $placement));

$column_media_classes = GRAV_BLOCKS::css()->col(
	$col_widths['small']['media'],
	$col_widths['medium']['media'],
	$col_widths['large']['media']
)->add(implode(', ', $column_media_add_classes))->get();

$column_media_classes = implode(' ', apply_filters('grav_block_mediacontent_media_classes', explode(' ', $column_media_classes), $media_column_width, $placement));

$image_format = '';

if ($media_type == 'image' && $media_column_width >= 6) {
	$image_format = isset($image_format) ? $image_format : get_sub_field('image_format');
}

$container_classes = [
	'block-inner',
	'media-placement--'.$placement,
	'media-size--'.sanitize_title($col_array[$media_column_width])
];

if ($image_format) {
	$container_classes[] = $image_format;
}

$row_classes = explode(' ', GRAV_BLOCKS::css()->row()->get());
$row_classes = apply_filters('grav_block_mediacontent_row_classes', $row_classes, $media_column_width, $placement);

?>
<div class="<?php echo implode(' ', $container_classes); ?>">
	<div class="<?php echo implode(' ', $row_classes); ?>">
		<div class="<?php echo $column_media_classes; ?> block-media-content__col-media">
			<?php
			$link = GRAV_BLOCKS::get_link_url('link');

			if ($link && $media_type != 'embed')
			{
				$link_type = get_sub_field('link_type');
				$link_classes = ['block-link-'.esc_attr($link_type)];
				$link_classes = apply_filters('grav_blocks_media_content_link_classes', $link_classes, $link_type, $link);

				?>
				<a class="<?php echo esc_attr(implode(' ', $link_classes)); ?>" href="<?php echo esc_url($link); ?>" <?php if ($link_type === 'video') { ?>data-modal-video<?php } ?>>
				<?php
			}

			?>
			<div class="block-media-content__media-container block-media-content__media-type--<?php echo esc_attr($media_type); ?>-container">
				<?php
				if (get_sub_field('link_type') == 'video')
				{
					do_action('grav_blocks_get_video_link_button', $block);
				}

				if ($media_type === 'video' && $video_url)
				{
					?>
					<video src="<?php echo esc_url($video_url); ?>" <?php echo implode(' ', $video_attributes);?>></video>
					<?php
				}

				if ($media_type === 'embed')
				{
					echo $embed;
				}

				if ($media_type === 'image')
				{
					echo GRAV_BLOCKS::image(get_sub_field('image'), array(), 'img', 'large');
				}
				?>
			</div>

			<?php
			if ($link && $media_type != 'embed')
			{
				?>
				</a>
				<?php
			}
			?>
		</div>
		<div class="<?php echo $column_content_classes; ?> block-media-content__col-content">
			<?php echo $content; ?>
		</div>
	</div>
</div>
