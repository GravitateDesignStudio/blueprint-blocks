<?php
if($heading = get_sub_field('title')){
?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get();?>">
			<div class="<?php echo GRAV_BLOCKS::css()->col()->get();?>">
				<h2 class="block-title__title"<?php if($center = get_sub_field('center')){?> style="text-align:center;"<?php } ?>>
					<?php echo $heading; ?>
				</h2>
				<?php if($sub_heading = get_sub_field('sub-title')){ ?>
					<h3 class="block-title__sub-title"<?php if($center){?> style="text-align:center;"<?php } ?>>
						<?php echo $sub_heading; ?>
					</h3>
				<?php } ?>
				<?php if($intro = get_sub_field('intro')){ ?>
					<p class="block-title__intro"<?php if($center){?> style="text-align:center;"<?php } ?>>
						<?php echo $intro; ?>
					</p>
				<?php } ?>
			</div>
		</div>
	</div>
<?php
}
