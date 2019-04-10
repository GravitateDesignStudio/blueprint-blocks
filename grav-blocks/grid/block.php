<?php
$alt_title_location = get_sub_field('move_title');

$block_format = isset($block_format) ? $block_format : get_sub_field('format');
$block_format = $block_format ? $block_format : 'grid'; // Set Defualt

$num_columns_small =  isset($num_columns_small)  ? $num_columns_small  : (get_sub_field('num_columns_small')  ? get_sub_field('num_columns_small')  : 1); // Set Defaults for older Plugin Versions
$num_columns_medium = isset($num_columns_medium) ? $num_columns_medium : (get_sub_field('num_columns_medium') ? get_sub_field('num_columns_medium') : 2); // Set Defaults for older Plugin Versions
$num_columns_large =  isset($num_columns_large)  ? $num_columns_large  : (get_sub_field('num_columns_large')  ? get_sub_field('num_columns_large')  : 4); // Set Defaults for older Plugin Versions
$num_columns_xlarge = isset($num_columns_xlarge) ? $num_columns_xlarge : (get_sub_field('num_columns_xlarge') ? get_sub_field('num_columns_xlarge') : 6); // Set Defaults for older Plugin Versions

$grid_class = ' '.GRAV_BLOCKS::css()->grid($num_columns_small, $num_columns_medium, $num_columns_large, $num_columns_xlarge)->get();

$block_title = isset($block_title) ? $block_title : get_sub_field('grid_title');
$grid_items = isset($grid_items) ? $grid_items : get_sub_field('grid_items');

if ($grid_items)
{
	?>
	<div class="block-inner">
		<?php
		if ($block_title)
		{
			?>
			<div class="<?php echo GRAV_BLOCKS::css()->row()->add('block-title-container')->get(); ?>">
				<div class="<?php echo GRAV_BLOCKS::css()->col()->get(); ?>">
					<h2 class="block-title"><?php echo $block_title; ?></h2>
				</div>
			</div>
			<?php
		}
		
		?>
		<div class="<?php echo GRAV_BLOCKS::css()->add('block-grid__items-container')->get(); ?>"
			data-columns-small="<?php echo $num_columns_small; ?>"
			data-columns-medium="<?php echo $num_columns_medium; ?>"
			data-columns-large="<?php echo $num_columns_large; ?>"
			data-columns-xlarge="<?php echo $num_columns_xlarge; ?>"
		>
			<div class="<?php echo GRAV_BLOCKS::css()->row()->get(); ?> <?php echo GRAV_BLOCKS::css()->add('block-grid__items')->get().$grid_class; ?>">
				<?php
				if ($grid_items)
				{
					foreach ($grid_items as $grid_item)
					{
						$image = $grid_item['item_image'] ?? '';
						$title = $grid_item['item_title'] ?? '';
						$link_type = $grid_item['link_type'] ?? '';
						$link = ($link_type && $link_type !== 'none') ? $grid_item['link_' . $link_type] : '';

						?>
						<div class="columns block-grid__item">
							<?php
							if ($link)
							{
								?>
								<a class="block-link-<?php echo esc_attr($link_type);?> block-grid__item--link" href="<?php echo esc_url($link); ?>" title="<?php echo esc_attr($image['alt']); ?>">
								<?php
							}
							?>
							<div class="block-grid__item--container">
								<?php
								if ($image)
								{
									?>
									<div class="block-grid__item--image-container">
										<div class="block-grid__item--image">
											<?php echo GRAV_BLOCKS::image($image); ?>
										</div>
									</div>
									<?php
								}

								if ($title)
								{
									?>
									<h3 class="block-grid__item--title"><span><?php echo $title; ?></span></h3>
									<?php
								}

								if ($content = $grid_item['item_content'])
								{
									?>
									<p class="block-grid__item--content"><span><?php echo $content; ?></span></p>
									<?php
								}
								?>
							</div>
							<?php
							if ($link)
							{
								if ($button_text = $grid_item['button_text'])
								{
									?>
									<span class="button"><?php echo $button_text; ?></span>
									<?php
								}
								
								?>
								</a>
								<?php
							}
							?>
						</div>
					<?php }
				}
				?>
			</div>
		</div>
	</div>
	<?php
}
