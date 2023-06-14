<?php
$heading = isset($heading) ? esc_html($heading) : esc_html(get_sub_field('title'));
$subheading = isset($subheading) ? esc_html($subheading) : esc_html(get_sub_field('sub-title'));

$heading_element = get_sub_field('heading_element') != 'h2' ? get_sub_field('heading_element') : '';
$subheading_element = get_sub_field('subheading_element') != 'h3' ? get_sub_field('subheading_element') : '';
$center = get_sub_field('center') ? 'text-center ' : '';

if (!$heading) return;
?>

<div class="block-inner row">
	<div class="column">
		<h2 class="<?= $center ?><?= $heading_element ?>">
			<?= $heading ?>
		</h2>
		<?php if ($subheading) : ?>
			<h3 class="subheading <?= $center ?><?= $subheading_element ?>">
				<?= $subheading ?>
			</h3>
		<?php endif ?>
	</div>
</div>