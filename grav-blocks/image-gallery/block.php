<?php

	$format = isset($format) ? $format : get_sub_field('format');

	if($images = get_sub_field('images')){ ?>

	<div class="block-inner image-gallery__format--<?php echo $format; ?>">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get(); ?>">
			<?php if( have_rows('images') ){
				if ($format != 'gallery') { ?>
				<div class="<?php echo GRAV_BLOCKS::css()->col(12)->get(); ?>">
					<div class="swiper-container">
						<div class="swiper-wrapper">
				<?php }
			    while ( have_rows('images') ){ the_row();
					$col = ($format == 'gallery') ? GRAV_BLOCKS::css()->col(12, 6, 4)->get() : 'swiper-slide'; ?>
					<div class="<?php echo $col; ?>">
						<?php if($format == 'gallery' && get_sub_field('open_modal') == 1){ ?>
							<a
								class="image-gallery__link--<?php echo GRAV_BLOCKS::$block_index; ?>"
								rel="image-gallery__link--<?php echo GRAV_BLOCKS::$block_index; ?>"
								href="<?php echo GRAV_BLOCKS::image(get_sub_field('image'), '', 'url', 'large'); ?>">

						<?php } ?>

							<?php echo GRAV_BLOCKS::image(get_sub_field('image'),'','img',($format == 'gallery') ? 'medium' : ''); ?>

						<?php if($format == 'gallery' && get_sub_field('open_modal') == 1){ ?>
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
