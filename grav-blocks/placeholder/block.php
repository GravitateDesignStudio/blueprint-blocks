<?php

$image = get_sub_field('image');

if($image){ ?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get(); ?>">
			<div class="columns small-12">
				<?php echo GRAV_BLOCKS::image($image);?>
			</div>
		</div>
	</div>
<?php } ?>
