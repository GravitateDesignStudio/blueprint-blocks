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
		'key' => 'field_'.$block.'_1',
		'label' => 'Title',
		'name' => 'title',
		'type' => 'text',
		'column_width' => '',
		'default_value' => '',
		'placeholder' => '',
		'prepend' => '',
		'append' => '',
		'formatting' => 'none',
		'maxlength' => '',
	),
	array (
		'key' => 'field_'.$block.'_2',
		'label' => 'Sub Title',
		'name' => 'sub-title',
		'type' => 'text',
		'column_width' => '',
		'default_value' => '',
		'placeholder' => '',
		'prepend' => '',
		'append' => '',
		'formatting' => 'none',
		'maxlength' => '',
	),
	array (
	    'key' => 'field_'.$block.'_intro',
	    'label' => 'Intro',
	    'name' => 'intro',
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
	    'new_lines' => '',        // wpautop | br | ''
	    'readonly' => 0,
	    'disabled' => 0,
	),
	array (
		'key' => 'field_'.$block.'_3',
		'label' => 'Center Text',
		'name' => 'center',
		'type' => 'true_false',
		'message' => '',
		'default_value' => 1,
		'block_options' => 1
	),
);

return array (
	'label' => 'Title',
	'name' => $block,
	'display' => 'block',
	'min' => '',
	'max' => '',
	'sub_fields' => $block_fields,
	'grav_blocks_settings' => array(
		'icon' => 'gravicon-title',
		'description' => '<div class="row">
				<div class="columns medium-6">
					<img src="'.plugins_url().'/blueprint-blocks/grav-blocks/title/title.svg">
				</div>
				<div class="columns medium-6">
					<p>When you want to make a statement with your content and break it apart for ease of digestion this block allows you to put a title and subtitle above that content to help differentiate it.</p>
					<p><strong>Available Fields:</strong></p>
					<ul>
						<li>Background</li>
						<li>Title</li>
						<li>Sub Title</li>
						<li>Ability to center text <em>( default is left-aligned )</em></li>
					</ul>
				</div>
			</div>'
	),
);
