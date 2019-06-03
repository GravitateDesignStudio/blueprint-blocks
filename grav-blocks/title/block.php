<?php
$heading = isset($heading) ? $heading : get_sub_field('title');

if ($heading)
{
	$subheading = isset($subheading) ? $subheading : get_sub_field('sub-title');
	$center = isset($center) ? $center : get_sub_field('center');

	$heading_tag = apply_filters('grav_blocks_title_heading_tag', 'h2');
	$subheading_tag = apply_filters('grav_blocks_title_subheading_tag', 'h3');

	$heading_row_col_sizes = apply_filters('grav_blocks_title_heading_row_column_sizes', [12, 12, 12]);
	$subheading_row_col_sizes = apply_filters('grav_blocks_title_subheading_row_column_sizes', [12, 10, 8]);

	?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->add(['align-center', 'block-title__row-heading'])->get(); ?>">
			<div class="<?php echo GRAV_BLOCKS::css()->col($heading_row_col_sizes[0], $heading_row_col_sizes[1], $heading_row_col_sizes[2])->get(); ?>">
				<<?php echo esc_attr($heading_tag); ?> class="block-title__title"<?php if ($center) { ?> style="text-align:center;"<?php } ?>>
					<?php echo esc_html($heading); ?>
				</<?php echo esc_attr($heading_tag); ?>>
			</div>
		</div>
		<?php
		if ($subheading)
		{
			?>
			<div class="<?php echo GRAV_BLOCKS::css()->row()->add(['align-center', 'block-title__row-subheading'])->get(); ?>">
				<div class="<?php echo GRAV_BLOCKS::css()->col($subheading_row_col_sizes[0], $subheading_row_col_sizes[1], $subheading_row_col_sizes[2])->get(); ?>">
					<<?php echo esc_attr($subheading_tag); ?> class="block-title__sub-title"<?php if ($center) { ?> style="text-align:center;"<?php } ?>>
						<?php echo esc_html($subheading); ?>
					</<?php echo esc_attr($subheading_tag); ?>>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}
