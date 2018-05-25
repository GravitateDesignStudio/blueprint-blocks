<?php

$heading = isset($heading) ? $heading : get_sub_field('title');
$sub_heading = isset($sub_heading) ? $sub_heading : get_sub_field('sub-title');
$intro = isset($intro) ? $intro : get_sub_field('intro');

$center = isset($center) ? $center : get_sub_field('center');

if($heading){
?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get();?>">
			<div class="<?php echo GRAV_BLOCKS::css()->col()->get();?>">
				<h2 class="block-title__title"<?php if($center){?> style="text-align:center;"<?php } ?>>
					<?php echo $heading; ?>
				</h2>
				<?php if($sub_heading){ ?>
					<h3 class="block-title__sub-title"<?php if($center){?> style="text-align:center;"<?php } ?>>
						<?php echo $sub_heading; ?>
					</h3>
				<?php } ?>
				<?php if($intro){ ?>
					<p class="block-title__intro"<?php if($center){?> style="text-align:center;"<?php } ?>>
						<?php echo $intro; ?>
					</p>
				<?php } ?>
			</div>
		</div>
	</div>
<?php
}
