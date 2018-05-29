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
	    'key' => 'field_'.$format.'_format',
	    'label' => 'Format',
	    'name' => 'format',
	    'type' => 'radio',
	    'instructions' => '',
	    'required' => 0,
	    'conditional_logic' => 0,
	    'wrapper' => array (
	        'width' => '',
	        'class' => '',
	        'id' => '',
	    ),
	    'choices' => array (
	        'accordion' => 'Accordion',
			'tabs-top' => 'Tabs (top)',
			'tabs-left' => 'Tabs (left)'
	    ),
	    'other_choice' => 0,
	    'save_other_choice' => 0,
	    'default_value' => 'accordion',
	    'layout' => 'horizontal',
		'block_options' => 1
	),
	array (
	   'key' => 'field_'.$block.'_add_side_content',
	   'label' => 'Add Side Content',
	   'name' => 'add_side_content',
	   'type' => 'true_false',
	   'instructions' => '',
	   'required' => 0,
	   'conditional_logic' => 0,
	   'wrapper' => array (
	       'width' => '',
	       'class' => '',
	       'id' => '',
	   ),
	   'message' => '',
	   'ui' => 1,
	   'ui_on_text' => 'Yes',
	   'ui_off_text' => 'No',
	   'default_value' => 0,
	   'block_options' => 1
	),
	array (
	    'key' => 'field_'.$block.'_side_content_placement',
	    'label' => 'Side Content Placement',
	    'name' => 'side_content_placement',
	    'type' => 'radio',
	    'instructions' => '',
	    'required' => 0,
	    'conditional_logic' => array (
	        'status' => 1,
	        'rules' => array (
	            array (
	               'field' => 'field_'.$block.'_add_side_content',
	                'operator' => '==',
	               'value' => 1,
	          ),
	        ),
	        'allorany' => 'all',
	    ),
	    'wrapper' => array (
	        'width' => '',
	        'class' => '',
	        'id' => '',
	    ),
	    'choices' => array (
	        'left' => 'Left',
			'right' => 'Right'
	    ),
	    'other_choice' => 0,
	    'save_other_choice' => 0,
	    'default_value' => 'left',
	    'layout' => 'horizontal',
		'block_options' => 1
	),
	array (
	    'key' => 'field_'.$block.'_side_content',
	    'label' => 'Side Content',
	    'name' => 'side_content',
	    'type' => 'wysiwyg',
	    'instructions' => '',
	    'required' => 0,
	    'conditional_logic' => array (
	        'status' => 1,
	        'rules' => array (
	            array (
	               'field' => 'field_'.$block.'_add_side_content',
	                'operator' => '==',
	               'value' => 1,
	          ),
	        ),
	        'allorany' => 'all',
	    ),
	    'wrapper' => array (
	        'width' => '',
	        'class' => '',
	        'id' => '',
	    ),
	    'default_value' => '',
	    'tabs' => 'all',         // all | visual | text
	    'toolbar' => 'full',     // full | basic
	    'media_upload' => 1,
	),
	array (
		'key' => 'field_'.$block.'_sections',
		'label' => 'Sections',
		'name' => 'sections',
		'type' => 'repeater',
		'column_width' => '',
		'sub_fields' => array (
			array (
			    'key' => 'field_'.$block.'_title',
			    'label' => 'Title',
			    'name' => 'title',
			    'type' => 'text',
			    'instructions' => 'This will appear in the tab and/or the heading of the accordion section.',
			    'required' => 0,
			    'conditional_logic' => 0,
			    'wrapper' => array (
			        'width' => '',
			        'class' => '',
			        'id' => '',
			    ),
			    'default_value' => '',
			    'placeholder' => '',
			    'formatting' => 'none',       // none | html
			    'prepend' => '',
			    'append' => '',
			    'maxlength' => '',
			    'readonly' => 0,
			    'disabled' => 0,
			),
			array (
			    'key' => 'field_'.$block.'_content',
			    'label' => 'Content',
			    'name' => 'content',
			    'type' => 'wysiwyg',
			    'instructions' => '',
			    'required' => 0,
			    'conditional_logic' => 0,
			    'wrapper' => array (
			        'width' => '',
			        'class' => '',
			        'id' => '',
			    ),
			    'default_value' => '',
			    'tabs' => 'all',         // all | visual | text
			    'toolbar' => 'full',     // full | basic
			    'media_upload' => 1,
			),
		),
		'min' => '1',
		'max' => '',
		'layout' => 'row',
		'button_label' => 'Add Section',
	),
);

return array (
	'label' => 'Accordion/Tabs',
	'name' => $block,
	'display' => 'row',
	'min' => '',
	'max' => '',
	'sub_fields' => $block_fields,
	'grav_blocks_settings' => array(),
);
