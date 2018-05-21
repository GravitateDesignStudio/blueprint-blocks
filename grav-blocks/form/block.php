<?php

	$form_type = isset($form_type) ? $form_type : get_sub_field('form_type');

	$gform = isset($gform) ? $gform : get_sub_field('gravity_form');
	$embed = isset($embed) ? $embed : get_sub_field('embed_form');
	$shortcode = isset($shortcode) ? $shortcode : get_sub_field('shortcode_form');

	$form_title = isset($form_title) ? $form_title : get_sub_field('form_title');
	$form_bg = isset($form_bg) ? $form_bg : get_sub_field('form_background');
	$form_footer = isset($form_footer) ? $form_footer : get_sub_field('form_footer_text');

	$placement = isset($placement) ? $placement : get_sub_field('form_placement');
	$content = isset($content) ? $content : get_sub_field('content');

	$col_class = 'option-'.$placement.'-'.sanitize_title($col_array[$col_width]);

	$bottom_classes = GRAV_BLOCKS::css()->col(12, 8)->add($col_class)->get();
	$top_classes = GRAV_BLOCKS::css()->col(12, 4)->add($col_class.', block-form__form,' . $form_bg)->get();
	if($placement == 'right'){
		$top_classes = GRAV_BLOCKS::css()->col(12, 4)->add('medium-order-2, '.$col_class.', block-form__form,' . $form_bg)->get();
		$bottom_classes = GRAV_BLOCKS::css()->col(12, 8)->add('medium-order-1, '.$col_class)->get();
	}

?>

<div class="block-inner <?php echo $placement.'-'.sanitize_title($col_array[$col_width]); echo $image_format; ?>">
	<div class="<?php echo GRAV_BLOCKS::css()->row()->add('align-' . $placement)->get(); ?>">
		<div class="<?php echo $top_classes; ?>">
			<?php if($link = GRAV_BLOCKS::get_link_url('link')){ ?>
				<a class="block-link-<?php echo esc_attr(get_sub_field('link_type'));?>" href="<?php echo esc_url($link); ?>">
			<?php } ?>

			<div class="block-form__form-type--<?php echo $form_type; ?>-container">
				<?php if ($form_title) { ?>
					<h4 class="block-form_form--title"><?php echo esc_attr($form_title); ?></h4>
				<?php } ?>
				<?php if($form_type === 'gravity'){ ?>
					<?php if(function_exists('gravity_form')){ gravity_form($gform, false, false, false, null, true); } ?>
				<?php } ?>

				<?php if($form_type === 'embed'){ ?>
					<?php echo $embed;?>
				<?php } ?>

				<?php if($form_type === 'shortcode'){ ?>
					<?php echo do_shortcode( $shortcode ); ?>
				<?php } ?>

				<?php if ($form_footer): ?>
					<footer class="block-form__form--footer">
						<?php echo $form_footer; ?>
					</footer>
				<?php endif; ?>
			</div>

			<?php if($link){ ?>
				</a>
			<?php } ?>
		</div>
		<div class="<?php echo $bottom_classes; ?> block-form__content">
			<?php echo $content; ?>
		</div>
	</div>
</div>
