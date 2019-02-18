<?php

/*
*
* Gravitate Content Block
*
* Available Variables:
* $block 					= Name of Block Folder
* $block_backgrounds 		= Array for Background Options
* $block_background_image = Array for Background Image Option
*
* This file must return an array();
*
*/

$user = wp_get_current_user();

$user_name = $user->user_login;

$block_fields = array(
	array (
	    'key' => 'field_'.$block.'_block_group',
	    'label' => 'Global Block Group',
	    'name' => 'block_group',
	    'type' => 'post_object',
	    'instructions' => 'Please select a global block group to display. You can add global blocks <a href="/wp-admin/edit.php?post_type=global-block">here</a>.',
	    'required' => 0,
	    'conditional_logic' => 0,
	    'wrapper' => array (
	        'width' => '',
	        'class' => '',
	        'id' => '',
	    ),
	    'post_type' => 'global-block',
	    'allow_null' => 0,
	    'multiple' => 0,
	    'return_format' => 'id',     // object | id
	    'ui' => 1,
	),
);

return array (
	'label' => 'Global Block',
	'name' => $block,
	'display' => 'block',
	'min' => '',
	'max' => '',
	'sub_fields' => $block_fields,
	'grav_blocks_settings' => array(),
);
