<?php

	$format = isset($format) ? $format : get_sub_field('format');

	if ($format == 'gallery') {
		$num_columns_small =  isset($num_columns_small)  ? $num_columns_small  : (get_sub_field('num_columns_small')  ? get_sub_field('num_columns_small')  : 1); // Set Defaults for older Plugin Versions
		$num_columns_medium = isset($num_columns_medium) ? $num_columns_medium : (get_sub_field('num_columns_medium') ? get_sub_field('num_columns_medium') : 2); // Set Defaults for older Plugin Versions
		$num_columns_large =  isset($num_columns_large)  ? $num_columns_large  : (get_sub_field('num_columns_large')  ? get_sub_field('num_columns_large')  : 4); // Set Defaults for older Plugin Versions
		$num_columns_xlarge = isset($num_columns_xlarge) ? $num_columns_xlarge : (get_sub_field('num_columns_xlarge') ? get_sub_field('num_columns_xlarge') : 6); // Set Defaults for older Plugin Versions

		$grid_class = ' '.GRAV_BLOCKS::css()->grid($num_columns_small, $num_columns_medium, $num_columns_large, $num_columns_xlarge)->get();
	}

	$images = isset($images) ? $images : get_sub_field('images');

	if($images){ ?>

	<div class="block-inner image-gallery__format--<?php echo $format; ?>">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->add('align-center')->get(); if($format == 'gallery'){ echo ' '.$grid_class; } ?>">

			<?php if( $images ){

				if ($format != 'gallery') { ?>
				<div class="<?php echo GRAV_BLOCKS::css()->col(12)->get(); ?>">
					<div class="swiper-container">
						<div class="swiper-wrapper">
				<?php }

			    foreach ($images as $image) {
					$col = ($format == 'gallery') ? 'columns' : 'swiper-slide'; ?>
					<div class="<?php echo $col; ?>">
						<?php if($format == 'gallery' && $image['open_modal'] == 1){ ?>
							<a
								class="image-gallery__link--<?php echo GRAV_BLOCKS::$block_index; ?>"
								rel="image-gallery__link--<?php echo GRAV_BLOCKS::$block_index; ?>"
								href="<?php echo GRAV_BLOCKS::image($image['image'], '', 'url', 'large'); ?>">

						<?php } ?>

							<?php echo GRAV_BLOCKS::image($image['image'],'','img',($format == 'gallery') ? 'medium' : ''); ?>

						<?php if($format == 'gallery' && $image['open_modal'] == 1){ ?>
							</a>
						<?php } ?>
					</div>
			    <?php }

				if ($format != 'gallery') { ?>
					</div>
					<?php if (count($images) > 1 && $format != 'gallery'): ?>
						<div class="image-gallery__slider--pagination-<?php echo GRAV_BLOCKS::$block_index; ?> swiper-pagination"></div>
						<div class="image-gallery__slider--prev-<?php echo GRAV_BLOCKS::$block_index; ?> swiper-button-prev"></div>
						<div class="image-gallery__slider--next-<?php echo GRAV_BLOCKS::$block_index; ?> swiper-button-next"></div>
					<?php endif; ?>
					</div><!-- Close swiper wrapper -->
				</div>
				<?php }

			} ?>
		</div>
	</div>

<?php } ?>
