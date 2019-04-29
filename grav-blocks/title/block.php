<?php
$heading = isset($heading) ? $heading : get_sub_field('title');

if ($heading)
{
	$subheading = isset($subheading) ? $subheading : get_sub_field('sub-title');
	$center = isset($center) ? $center : get_sub_field('center');

	$heading_tag = apply_filters('grav_blocks_title_heading_tag', 'h2');
	$subheading_tag = apply_filters('grav_blocks_title_subheading_tag', 'h3');

	?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get(); ?>">
			<div class="<?php echo GRAV_BLOCKS::css()->col()->get(); ?>">
				<<?php echo esc_attr($heading_tag); ?> class="block-title__title"<?php if ($center) { ?> style="text-align:center;"<?php } ?>>
					<?php echo esc_html($heading); ?>
				</<?php echo esc_attr($heading_tag); ?>>
				<?php
				if ($subheading)
				{
					?>
					<<?php echo esc_attr($subheading_tag); ?> class="block-title__sub-title"<?php if ($center) { ?> style="text-align:center;"<?php } ?>>
						<?php echo esc_html($subheading); ?>
					</<?php echo esc_attr($subheading_tag); ?>>
					<?php
				}
				?>
			</div>
		</div>
	</div>
	<?php
}
