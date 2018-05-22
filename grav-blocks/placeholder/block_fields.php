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

$block_fields = array(
	array (
	    'key' => 'field_'.$block.'_ph_message',
	    'label' => 'Explaination',
	    'name' => 'ph_message',
	    'type' => 'message',
	    'instructions' => '',
	    'required' => 0,
	    'conditional_logic' => 0,
	    'wrapper' => array (
	        'width' => '',
	        'class' => '',
	        'id' => '',
	    ),
	    'message' => 'This block will serve as a placeholder for a custom block',
	    'new_lines' => 'wpautop',    // wpautop | br | ''
	    'esc_html' => 0,             // uses the WordPress esc_html function
	),
	array (
	    'key' => 'field_'.$block.'_block_description',
	    'label' => 'Description',
	    'name' => 'block_description',
	    'type' => 'textarea',
	    'instructions' => '',
	    'required' => 0,
	    'conditional_logic' => 0,
	    'wrapper' => array (
	        'width' => '',
	        'class' => '',
	        'id' => '',
	    ),
	    'default_value' => '',
	    'placeholder' => '',
	    'maxlength' => '',
	    'rows' => '',
	    'new_lines' => 'wpautop',        // wpautop | br | ''
	    'readonly' => 0,
	    'disabled' => 0,
	),
	array (
		'key' => 'field_'.$block.'_image',
		'label' => 'Image',
		'name' => 'image',
		'type' => 'image',
		'column_width' => '',
		'save_format' => 'object',
		'preview_size' => 'medium',
		'library' => 'all',
	),
);

return array (
	'label' => 'Placeholder',
	'name' => $block,
	'display' => 'row',
	'min' => '',
	'max' => '',
	'sub_fields' => $block_fields,
	'grav_blocks_settings' => array(),
);
