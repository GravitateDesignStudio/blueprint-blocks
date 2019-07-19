<?php
$alt_title_location = get_sub_field('move_title');

$block_format = isset($block_format) ? $block_format : get_sub_field('format');
$block_format = $block_format ? $block_format : 'grid'; // Set Defualt

$image_aspect_ratio = get_sub_field('image_aspect_ratio') ?? '';

$num_columns_small = $num_columns_small ?? get_sub_field('num_columns_small') ?? 1;
$num_columns_medium = $num_columns_medium ?? get_sub_field('num_columns_medium') ?? 2;
$num_columns_large = $num_columns_large ?? get_sub_field('num_columns_large') ?? 4;
$num_columns_xlarge = $num_columns_xlarge ?? get_sub_field('num_columns_xlarge') ?? 6;

$grid_classes = explode(' ', GRAV_BLOCKS::css()->grid($num_columns_small, $num_columns_medium, $num_columns_large, $num_columns_xlarge)->get());

$grid_items = isset($grid_items) ? $grid_items : get_sub_field('grid_items');

if ($grid_items)
{
	?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->add('block-grid__items-container')->get(); ?>"
			data-columns-small="<?php echo $num_columns_small; ?>"
			data-columns-medium="<?php echo $num_columns_medium; ?>"
			data-columns-large="<?php echo $num_columns_large; ?>"
			data-columns-xlarge="<?php echo $num_columns_xlarge; ?>"
		>
			<div class="<?php echo GRAV_BLOCKS::css()->row()->add(array_merge(['block-grid__items'], $grid_classes))->get(); ?>">
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
								<a class="block-link-<?php echo esc_attr($link_type);?> block-grid__item-link" href="<?php echo esc_url($link); ?>" title="<?php echo esc_attr($image['alt']); ?>">
								<?php
							}
							?>
							<div class="block-grid__item-container">
								<?php
								if ($image)
								{
									$image_container_classes = ['block-grid__item-image-container'];

									if ($image_aspect_ratio) {
										$image_container_classes[] = 'block-grid__item-image-container--'.$image_aspect_ratio;
									}

									?>
									<div class="<?php echo implode(' ', $image_container_classes); ?>">
										<?php echo GRAV_BLOCKS::image($image, ['class' => 'block-grid__item-image']); ?>
									</div>
									<?php
								}

								if ($content = $grid_item['item_content'])
								{
									?>
									<div class="block-grid__item-content"><?php echo $content; ?></div>
									<?php
								}
								?>
							</div>
							<?php
							if ($link)
							{
								if ($button_text = $grid_item['button_text'])
								{
									$button_classes = ['button'];

									if ($grid_item['link_style']) {
										$button_classes[] = $grid_item['link_style'];
									}

									?>
									<div class="block-grid__item-button-container">
										<span class="<?php echo implode(' ', $button_classes); ?>"><?php echo $button_text; ?></span>
									</div>
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
