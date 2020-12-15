<?php
/*
Gravitate Content Block Handler Template

Available Variables:
$block_name
$block_hide
$block_index
$block_padding
$block_variables
$block_unique_id
$block_background
$block_background_image
$block_background_overlay
$block_attributes
$block_container_attributes
*/

BlueprintBlocks\Backgrounds::open_group_container(GRAV_BLOCKS::$background_groups, GRAV_BLOCKS::$block_index);

// check if we're inside an active background group and remove the background css
// classes from the block container
if (BlueprintBlocks\Backgrounds::is_active_group()) {
	$active_group_bg_name = BlueprintBlocks\Backgrounds::get_active_group_bg_name();

	// remove the background class name from the block container attributes array
	$block_attributes['class'] = array_filter($block_attributes['class'], function ($css_class) use ($active_group_bg_name) {
		return $css_class !== $active_group_bg_name;
	});

	// remove the background class name from the block container attributes string
	$block_container_attributes = str_replace([
		' ' . $active_group_bg_name,
		$active_group_bg_name . ' ',
		'"' . $active_group_bg_name . '"'
	], '', $block_container_attributes);
}

if ($block_name == 'global-block') {
	GRAV_BLOCKS::get_block($block_name, $block_variables, $block_attributes);
} else {
	?>
	<section <?php echo $block_container_attributes; ?>>
		<?php
		GRAV_BLOCKS::get_block($block_name, $block_variables, $block_attributes);
		?>
	</section>
	<?php
}

BlueprintBlocks\Backgrounds::close_group_container(GRAV_BLOCKS::$background_groups, GRAV_BLOCKS::$block_index);
