<?php
$column_num = isset($column_num) ? $column_num : get_sub_field('num_columns');
$cta_columns = isset($cta_columns) ? $cta_columns : array();
$title_tag = apply_filters('grav_blocks_calltoaction_title_tag', 'h2');
$description_tag = apply_filters('grav_blocks_calltoaction_description_tag', 'h4');

if ($column_num === false) {
	$column_num = 1;
}

if ($column_num)
{
	?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get();?> align-center">
			<?php
			for ($i = 1; $i <= $column_num; $i++)
			{
				$buttons = isset($cta_columns['buttons_' . $i]) ? $cta_columns['buttons_' . $i] : get_sub_field('buttons_' . $i);
				$title = isset($cta_columns['title_' . $i]) ? $cta_columns['title_' . $i] : get_sub_field('title_' . $i);
				$description = isset($cta_columns['description_' . $i]) ? $cta_columns['description_' . $i] : get_sub_field('description_' . $i);
				$form = isset($cta_columns['form_' . $i]) ? $cta_columns['form_' . $i] : get_sub_field('form_' . $i);
				$align_content = isset($cta_columns['alignment_' . $i]) ? $cta_columns['alignment_' . $i] : get_sub_field('alignment_' . $i);
				$background = isset($cta_columns['block_background_' . $i]) ? $cta_columns['block_background_' . $i] : get_sub_field('block_background_' . $i);
				$background_image = isset($cta_columns['block_background_image_' . $i]) ? $cta_columns['block_background_image_' . $i] : get_sub_field('block_background_image_' . $i);

				if ($title || $description || $buttons || $form)
				{
					?>
					<div class="<?php
							echo GRAV_BLOCKS::css()->add('block-calltoaction__content,' . $background)->col(12, ($column_num > 1 ? (12/$column_num) : 8))->col_center(false, true)->text_align($align_content)->get();
						?>"
						<?php if($background == 'block-bg-image') {
							echo GRAV_BLOCKS::image_background($background_image);
						} ?>>
						<?php if($title){ ?>
							<<?php echo esc_attr($title_tag); ?> class="block-calltoaction__title"><?php echo esc_html($title); ?></<?php echo esc_attr($title_tag); ?>>
						<?php } ?>
						<?php if($description){ ?>
							<<?php echo esc_attr($description_tag); ?> class="block-calltoaction__description"><?php echo esc_html($description); ?></<?php echo esc_attr($description_tag); ?>>
						<?php } ?>
						<?php

						if ($buttons)
						{
							?>
							<div class="block-calltoaction__buttons">
								<?php
								foreach ($buttons as $button)
								{
									if (!empty($button['button_'.$button['button_type']]))
									{
										$link = $button['button_'.$button['button_type']];

										if ($button['button_type'] === 'video')
										{
											$link = GRAV_BLOCKS::get_video_url($link);
										}
									}
									else
									{
										$link = '#';
									}

									if ($button['button_type'] && $button['button_type'] != 'none')
									{
										?>
										<a class="button block-link-<?php echo esc_attr($button['button_type']);?>" href="<?php echo esc_url($link);?>">
											<?php echo esc_html($button['button_text']);?>
										</a>
										<?php
									}
								}
								?>
							</div>
							<?php
						}

						if ($form)
						{
							?>
							<div class="block-calltoaction__form">
								<?php
								if (function_exists('gravity_form'))
								{
									gravity_form($form, false, false, false, null, true);
								}
								?>
							</div>
							<?php
						}
						?>
					</div>
					<?php
					}
				}
			?>
 		</div>
	</div>
	<?php
}
