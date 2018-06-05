<?php

	$media_type = isset($media_type) ? $media_type : get_sub_field('media_type');
	if(!$media_type)
	{
		$media_type = 'image';
	}

	$video_url = isset($video_url) ? $video_url : get_sub_field('video_'.get_sub_field('video_type'));

	$embed = isset($embed) ? $embed : get_sub_field('embed');
	$video_attributes = isset($video_attributes) ? $video_attributes : get_sub_field('video_attributes');

	$placement = isset($placement) ? $placement : get_sub_field('image_placement');
	$col_width = isset($col_width) ? $col_width : get_sub_field('image_size');
	$content = isset($content) ? $content : get_sub_field('content');
	$col_array = GRAV_BLOCKS::column_width_options();

	$col_total = ($col_width > 4 )? 12 : 10;
	$col_total = apply_filters('grav_block_mediacontent_columns', $col_total, $col_width, $placement);
	$col_content_width = $col_total-$col_width;
	$col_class = 'col-option-'.$placement.'-'.sanitize_title($col_array[$col_width]);

	$content_col_classes = GRAV_BLOCKS::css()->col(12, $col_content_width)->add($col_class)->get();
	$media_col_classes = GRAV_BLOCKS::css()->col(12, $col_width)->add($col_class.', block-media-content__col-media')->get();
	if($placement == 'right'){
		$media_col_classes = GRAV_BLOCKS::css()->col(12, $col_width)->add('medium-order-2, '.$col_class.', block-media-content__col-media')->get();
		$content_col_classes = GRAV_BLOCKS::css()->col(12, $col_content_width)->add('medium-order-1, '.$col_class)->get();
	}

	if ($media_type == 'image' && $col_width >= 6) {

		$image_format = isset($image_format) ? $image_format : get_sub_field('image_format');
	}

?>

<div class="block-inner <?php echo $placement.'-'.sanitize_title($col_array[$col_width]); if($image_format){ echo ' ' . $image_format; } ?>">
	<div class="<?php echo GRAV_BLOCKS::css()->row()->add('align-' . $placement)->get(); ?>">
		<div class="<?php echo $media_col_classes; ?>">
			<?php if($link = GRAV_BLOCKS::get_link_url('link')){ ?>
				<a class="block-link-<?php echo esc_attr(get_sub_field('link_type'));?>" href="<?php echo esc_url($link); ?>">
			<?php } ?>

			<div class="block-media-content__media-type--<?php echo $media_type; ?>-container">
				<?php if($media_type === 'video' && $video_url){ ?>
					<video src="<?php echo $video_url;?>" <?php echo implode(' ', $video_attributes);?>></video>
				<?php } ?>

				<?php if($media_type === 'embed'){ ?>
					<?php echo $embed;?>
				<?php } ?>

				<?php if($media_type === 'image'){ ?>
				<?php echo GRAV_BLOCKS::image(get_sub_field('image'), array(), 'img', 'large');?>
				<?php } ?>
			</div>

			<?php if($link){ ?>
				</a>
			<?php } ?>
		</div>
		<div class="<?php echo $content_col_classes; ?> block-media-content__col-content">
			<?php echo $content; ?>
		</div>
	</div>
</div>
