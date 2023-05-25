<?php
$heading = isset($heading) ? esc_html($heading) : esc_html(get_sub_field('title'));
$subheading = isset($subheading) ? esc_html($subheading) : esc_html(get_sub_field('sub-title'));

if (!$heading) return;

$center = get_sub_field('center') ? 'text-center' : '';
?>

<div class="block-inner">
	<h2 class="<?= $center ?>">
		<?= $heading ?>
	</h2>
	<?php if ($subheading) : ?>
		<h3 class="subheading <?= $center ?>">
			<?= $subheading ?>
		</h3>
	<?php endif ?>
</div>