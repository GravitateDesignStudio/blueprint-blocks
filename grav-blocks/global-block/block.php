<?php
$block_group = isset($block_group) ? $block_group : get_sub_field('block_group');
if($block_group){

	GRAV_BLOCKS::display(array(
		'object' => $block_group
	));

}
