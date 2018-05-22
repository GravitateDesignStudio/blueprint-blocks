<?php

$testimonials = isset($testimonials) ? $testimonials : get_sub_field('testimonials');

if(apply_filters( 'grav_blocks_testimonials_cpt', $testimonial_cpts)) {
	// TODO: Figure this out
	$schema = apply_filters('grav_blocks_testimonials_cpt_schema', $testimonial_cpts_schema);
}

if($testimonials)
{
	?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get();?>">
			<div class="<?php echo GRAV_BLOCKS::css()->col()->get();?>">
				<div class="swiper-container">
					<div class="swiper-wrapper">

						<?php
						foreach ($testimonials as $testimonial) {

							if ($testimonial['use_post']) {
								global $post;
								$post = get_post($testimonial['testimonial_post']);
								setup_postdata( $post );
								$post_type = get_post_type( $testimonial_post );
								$image = wp_get_attachment_image(get_post_thumbnail_id());
								$text = get_the_content();
								$attribution = get_the_title();
								$attribution_sub_title = get_field('attribution_sub_title');

							} else {

								$image = $testimonial['image'];
								$text = $testimonial['testimonial'];
								$attribution = $testimonial['attribution'];
								$attribution_sub_title = $testimonial['attribution_sub_title'];

							} ?>

							<div class="block-testimonials__testimonial swiper-slide">
								<div class="<?php echo GRAV_BLOCKS::css()->add(($image ? 'has-image' : 'no-image'))->row()->get();?>">
									<?php if($image){ ?>
									<div class="<?php echo GRAV_BLOCKS::css()->add('block-testimonials__testimonial--image')->col(12, 2)->get();?>">
										<?php if (is_array($image)): ?>
											<img src="<?php echo esc_attr($image['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
										<?php else: ?>
											<?php echo $image; ?>
										<?php endif; ?>

									</div>
									<?php } ?>
									<div class="<?php echo GRAV_BLOCKS::css()->add('block-testimonials__testimonial--content')->col(12, ($image ? 10 : 12))->get();?>">
										<blockquote class="testimonial">
											<p><?php echo $text; ?></p>
											<footer>
												<cite class="block-testimonials__testimonial--attribution-title"><?php echo $attribution; ?></cite>
												<?php if($attribution_sub_title){ ?>
													<cite class="block-testimonials__testimonial--attribution-sub-title"><?php echo $attribution_sub_title;?></cite>
												<?php } ?>
											</footer>
										</blockquote>
									</div>
								</div>
							</div>
						<?php
						if ($testimonial['use_post']) {
							wp_reset_postdata();
						}
					 } ?>

					</div>

					<?php if(count($testimonials) > 1)
					{
						?>
						<div class="swiper-pagination"></div>
						<div class="swiper-button-prev"></div>
    					<div class="swiper-button-next"></div>
						<?php
					} ?>

				 </div>
			 </div>
		</div>
	</div>
	<?php
}
