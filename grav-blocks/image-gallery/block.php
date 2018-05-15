<?php

	$format = isset($format) ? $format : get_sub_field('format'); echo $format; ?>

<?php if($images = get_sub_field('images')){ ?>
	<div class="block-inner <?php echo $format; ?>">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get(); ?>">
			<?php if( have_rows('images') ){
			    ?>

			    <?php
			    while ( have_rows('images') ){ the_row(); ?>
					<div class="<?php echo GRAV_BLOCKS::css()->col(12)->get(); ?>">
						<?php if($link = GRAV_BLOCKS::get_link_url('link')){ ?>
							<a class="block-link-<?php echo esc_attr(get_sub_field('link_type'));?>" href="<?php echo esc_url($link); ?>">
						<?php } ?>

							<?php echo GRAV_BLOCKS::image(get_sub_field('image'));?>

						<?php if($link){ ?>
							</a>
						<?php } ?>
					</div>
			    <?php }
			} ?>
		</div>
	</div>
<?php } ?>
