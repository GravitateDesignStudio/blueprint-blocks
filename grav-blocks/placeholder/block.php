<?php

if($image = get_sub_field('image')){ ?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get(); ?>">
			<?php if($padding){ ?><div class="columns small-12"><?php } ?>
					<?php echo GRAV_BLOCKS::image($image);?>
			<?php if($padding){ ?></div><?php } ?>
		</div>
	</div>
<?php } ?>
