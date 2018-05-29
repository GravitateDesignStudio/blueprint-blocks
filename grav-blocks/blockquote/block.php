<?php

$quoted_text = isset($quoted_text) ? $quoted_text :get_sub_field('quoted_text');
$attribution = isset($attribution) ? $attribution : get_sub_field('attribution');
$center = isset($center) ? $center : get_sub_field('center');

if($quoted_text){ ?>
	<div class="block-inner">
		<div class="<?php echo GRAV_BLOCKS::css()->row()->get();?>">
			<div class="<?php echo GRAV_BLOCKS::css()->col()->get();?>">
				<blockquote<?php if(get_sub_field('center')){?> style="text-align:center;"<?php } ?>><?php the_sub_field('quoted_text');?>
					<?php if ($attribution) { ?>
						<footer>
							<cite<?php if($center){?> style="text-align:center;"<?php } ?>>-<?php echo $attribution; ?></cite>
						</footer>
					<?php } ?>
				</blockquote>
			</div>
		</div>
	</div>
<?php
}
