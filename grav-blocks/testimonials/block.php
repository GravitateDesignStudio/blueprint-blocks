<?php
if(get_sub_field('testimonials'))
{
	?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get();?>">
			<div class="<?php echo GRAV_BLOCKS::css()->col()->get();?>">
				<div class="swiper-container">
					<div class="swiper-wrapper">

					<?php
					while(has_sub_field('testimonials'))
					{
						$image = get_sub_field('image');
						?>
						<div class="swiper-slide">
							<div class="<?php echo GRAV_BLOCKS::css()->add(($image ? 'has-image' : 'no-image'))->row()->get();?>">
								<?php if($image){?>
								<div class="<?php echo GRAV_BLOCKS::css()->add('col-image')->col(12, 2)->get();?>">
									<img src="<?php echo esc_attr($image['sizes']['thumbnail']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
								</div>
								<?php } ?>
								<div class="<?php echo GRAV_BLOCKS::css()->add('col-content')->col(12, ($image ? 10 : 12))->get();?>">
									<blockquote class="testimonial">
										<p><?php the_sub_field('testimonial');?></p>
										<footer>
											<cite class="attribution-title"><?php the_sub_field('attribution');?></cite>
											<?php if($attribution_sub_title = get_sub_field('attribution_sub_title')){ ?>
												<cite class="attribution-sub-title"><?php echo $attribution_sub_title;?></cite>
											<?php } ?>
										</footer>
									</blockquote>
								</div>
							</div>
						</div>
						<?php
					}

					if(count(get_sub_field('testimonials')) > 1)
					{
						?>
						<div class="swiper-pagination"></div>
						<div class="swiper-prev"></div>
    					<div class="swiper-next"></div>
						<?php
					}
					?>

					</div>
				 </div>
			 </div>
		</div>
	</div>
	<?php
}
