<?php

$testimonials = isset($testimonials) ? $testimonials : get_sub_field('testimonials');

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

							$image = $testimonial['image'];
							$text = $testimonial['testimonial'];
							$attribution = $testimonial['attribution'];
							$attribution_sub_title = $testimonial['attribution_sub_title'];

							if ($testimonial['use_post']) {
								echo $testimonial['testimonial_post'];
								$testimonial_post = get_post($testimonial['testimonial_post']);

								$image = $testimonial['image'];
								$text = $testimonial_post->post_content;
								$attribution = $testimonial_post->post_title;
								$attribution_sub_title = 'CPT';
								// $text = $testimonial['testimonial'];
								// $attribution = $testimonial['attribution'];
								// $attribution_sub_title = $testimonial['attribution_sub_title'];

							} ?>

							<div class="block-testimonials__testimonial swiper-slide">
								<div class="<?php echo GRAV_BLOCKS::css()->add(($image ? 'has-image' : 'no-image'))->row()->get();?>">
									<?php if($image){?>
									<div class="<?php echo GRAV_BLOCKS::css()->add('block-testimonials__testimonial--image')->col(12, 2)->get();?>">
										<img src="<?php echo esc_attr($image['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
									</div>
									<?php } ?>
									<div class="<?php echo GRAV_BLOCKS::css()->add('block-testimonials__testimonial--content')->col(12, ($image ? 10 : 12))->get();?>">
										<?php if ($testimonial_post) {
											var_dump($testimonial_post);
										} ?>
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
						<?php } ?>

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
