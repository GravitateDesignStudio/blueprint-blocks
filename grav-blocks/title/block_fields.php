<?php
/*
*
* Title Block
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
		'wrapper' => array (
			'width' => '60',
		),
	),
	array (
		'key' => 'field_'.$block.'_heading_element',
		'label' => 'Heading Size',
		'name' => 'heading_element',
		'type' => 'button_group',
		'instructions' => 'Select a heading element for the title. There should only be 1 H1 per page.',
		'wrapper' => array (
			'width' => '40',
		),
		'choices' => array (
			'h2' => 'H2',
			'h3' => 'H3',
			'h4' => 'H4',
		),
		'default_value' => 'h2',
	),
	array (
		'key' => 'field_'.$block.'_2',
		'label' => 'Sub Title',
		'name' => 'sub-title',
		'type' => 'text',
		'wrapper' => array (
			'width' => '60',
		),
	),
	array (
		'key' => 'field_'.$block.'_subheading_element',
		'label' => 'Sub Title Size',
		'name' => 'subheading_element',
		'type' => 'button_group',
		'instructions' => 'Select a heading element for the subtitle. It should be smaller than the title.',
		'required' => 0,
		'conditional_logic' => 0,  //  acf_condtional
		'wrapper' => array (
			'width' => '40',
		),
		'choices' => array (
			'h3' => 'H3',
			'h4' => 'H4',
			'h5' => 'H5',
			'h6' => 'H6'
		),
		'default_value' => 'h3',
	),
	array (
		'key' => 'field_'.$block.'_3',
		'label' => 'Center Text',
		'name' => 'center',
		'type' => 'true_false',
		'message' => '',
		'default_value' => 1,
		'ui' => true
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
