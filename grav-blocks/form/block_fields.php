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

$gforms = array(0 => '- None');

foreach(GRAV_BLOCKS::get_gravity_forms() as $gform)
{
	$gforms[$gform['id']] = $gform['title'];
}

$form_background_colors = apply_filters( 'grav_block_background_colors', $block_backgrounds ?? [], $block );


$block_fields = array(
	array (
	    'key' => 'field_'.$block.'_form_type',
	    'label' => 'Form Type',
	    'name' => 'form_type',
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
	        'gravity' => 'Gravity Form', // Leave this blank to allow for older versions to work.
			'embed' => 'Embed',
			'shortcode' => 'Shortcode'
	    ),
	    'other_choice' => 0,
	    'save_other_choice' => 0,
	    'default_value' => 'gravity',
	    'layout' => 'horizontal',
		'block_options' => 1
	),
	array (
	    'key' => 'field_'.$block.'_form_title',
	    'label' => 'Form Title',
	    'name' => 'form_title',
	    'type' => 'text',
	    'instructions' => '(Optional)',
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
	    'key' => 'field_'.$block.'_gravity_form',
	    'label' => 'Gravity Form',
	    'name' => 'gravity_form',
	    'type' => 'select',
	    'instructions' => '',
	    'required' => 1,
	    'conditional_logic' => array (
	        'status' => 1,
	        'rules' => array (
	            array (
	               'field' => 'field_'.$block.'_form_type',
	                'operator' => '==',
	               'value' => 'gravity',
	          ),
	        ),
	        'allorany' => 'all',
	    ),
	    'wrapper' => array (
	        'width' => '',
	        'class' => '',
	        'id' => '',
	    ),
	    'choices' => $gforms,
	    'default_value' => array (
	    ),
	    'allow_null' => 0,
	    'multiple' => 0,         // allows for multi-select
	    'ui' => 0,               // creates a more stylized UI
	    'ajax' => 0,
	    'placeholder' => '',
	    'disabled' => 0,
	    'readonly' => 0,
	),
	array (
	    'key' => 'field_'.$block.'_embed_form',
	    'label' => 'Embed',
	    'name' => 'embed_form',
	    'type' => 'textarea',
	    'instructions' => '',
	    'required' => 1,
		'conditional_logic' => array (
			array (
				array (
		            'field' => 'field_'.$block.'_form_type',
		            'operator' => '==',
		            'value' => 'embed',
		        ),
			),
		),
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
	    'key' => 'field_'.$block.'_shortcode_form',
	    'label' => 'Shortcode',
	    'name' => 'shortcode_form',
	    'type' => 'text',
	    'instructions' => '',
	    'required' => 1,
	    'conditional_logic' => array (
	        'status' => 1,
	        'rules' => array (
	            array (
	               'field' => 'field_'.$block.'_form_type',
	                'operator' => '==',
	               'value' => 'shortcode',
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
	    'placeholder' => '',
	    'formatting' => 'none',       // none | html
	    'prepend' => '',
	    'append' => '',
	    'maxlength' => '',
	    'readonly' => 0,
	    'disabled' => 0,
	),
	array (
	    'key' => 'field_'.$block.'_form_footer_text',
	    'label' => 'Form Footer Text',
	    'name' => 'form_footer_text',
	    'type' => 'wysiwyg',
	    'instructions' => '(Optional)',
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
	array (
	    'key' => 'field_'.$block.'_form_background',
	    'label' => 'Form Background',
	    'name' => 'form_background',
	    'type' => 'select',
	    'instructions' => '',
	    'required' => 0,
	    'conditional_logic' => 0,
	    'wrapper' => array (
	        'width' => '',
	        'class' => '',
	        'id' => '',
	    ),
	    'choices' => $form_background_colors,
	    'default_value' => array (
	    ),
	    'allow_null' => 0,
	    'multiple' => 0,         // allows for multi-select
	    'ui' => 0,               // creates a more stylized UI
	    'ajax' => 0,
	    'placeholder' => '',
	    'disabled' => 0,
	    'readonly' => 0,
		'block_options' => 1
	),
	array (
		'key' => 'field_'.$block.'_form_placement',
		'label' => 'Form Placement',
		'name' => 'form_placement',
		'prefix' => '',
		'type' => 'radio',
		'instructions' => '',
		'required' => 0,
		'conditional_logic' => 0,
		'column_width' => '',
		'choices' => array (
			'left' => 'Left',
			'right' => 'Right',
		),
		'other_choice' => 0,
		'save_other_choice' => 0,
		'default_value' => 'left',
		'layout' => 'horizontal',
		'block_options' => 1
	),
	array (
		'key' => 'field_'.$block.'_content',
		'label' => 'Content',
		'name' => 'content',
		'prefix' => '',
		'type' => 'wysiwyg',
		'instructions' => '',
		'required' => 0,
		'conditional_logic' => 0,
		'column_width' => '',
		'default_value' => '',
		'tabs' => 'all',
		'toolbar' => 'full',
		'media_upload' => 0,
	),
);

return array (
	'name' => $block,
	'label' => 'Form',
	'display' => 'block',
	'sub_fields' => $block_fields,
	'min' => '',
	'max' => '',
	'grav_blocks_settings' => array(),
);
