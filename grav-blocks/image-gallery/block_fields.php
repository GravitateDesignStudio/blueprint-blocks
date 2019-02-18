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
	    'key' => 'field_'.$block.'_format',
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
	        'slider' => 'Slider',
	        'slider-full' => 'Slider (Full Width & Height)',
			'gallery' => 'Gallery'
	    ),
	    'other_choice' => 0,
	    'save_other_choice' => 0,
	    'default_value' => 'slider',
	    'layout' => 'horizontal',
		'block_options' => 1
	),
	array (
		'key' => 'field_'.$block.'_num_columns_small',
		'label' => 'Number of Columns on Small Screens',
		'name' => 'num_columns_small',
		'type' => 'radio',
		'instructions' => '',
		'required' => 0,
		'conditional_logic' => array (
		    'status' => 1,
		    'rules' => array (
		        array (
		           'field' => 'field_' . $block . '_format',
		            'operator' => '==',
		           'value' => 'gallery',
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
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
		),
		'other_choice' => 0,
		'save_other_choice' => 0,
		'default_value' => '1',
		'layout' => 'horizontal',
		'block_options' => 1,
	),
	array (
		'key' => 'field_'.$block.'_num_columns_medium',
		'label' => 'Number of Columns on Medium Screens',
		'name' => 'num_columns_medium',
		'type' => 'radio',
		'instructions' => '',
		'required' => 0,
		'conditional_logic' => array (
		    'status' => 1,
		    'rules' => array (
		        array (
		           'field' => 'field_' . $block . '_format',
		            'operator' => '==',
		           'value' => 'gallery',
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
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
		),
		'other_choice' => 0,
		'save_other_choice' => 0,
		'default_value' => '2',
		'layout' => 'horizontal',
		'block_options' => 1,
	),
	array (
		'key' => 'field_'.$block.'_num_columns_large',
		'label' => 'Number of Columns on Large Screens',
		'name' => 'num_columns_large',
		'type' => 'radio',
		'instructions' => '',
		'required' => 0,
		'conditional_logic' => array (
		    'status' => 1,
		    'rules' => array (
		        array (
		           'field' => 'field_' . $block . '_format',
		            'operator' => '==',
		           'value' => 'gallery',
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
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
		),
		'other_choice' => 0,
		'save_other_choice' => 0,
		'default_value' => '4',
		'layout' => 'horizontal',
		'block_options' => 1,
	),
	array (
		'key' => 'field_'.$block.'_num_columns_xlarge',
		'label' => 'Number of Columns on Extra Large Screens',
		'name' => 'num_columns_xlarge',
		'type' => 'radio',
		'instructions' => '',
		'required' => 0,
		'conditional_logic' => array (
		    'status' => 1,
		    'rules' => array (
		        array (
		           'field' => 'field_' . $block . '_format',
		            'operator' => '==',
		           'value' => 'gallery',
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
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
		),
		'other_choice' => 0,
		'save_other_choice' => 0,
		'default_value' => '6',
		'layout' => 'horizontal',
		'block_options' => 1,
	),
	array (
	    'key' => 'field_'.$block.'_images',
	    'label' => 'Images',
	    'name' => 'images',
	    'type' => 'repeater',
	    'instructions' => '',
	    'required' => 0,
	    'conditional_logic' => 0,
	    'wrapper' => array (
	        'width' => '',
	        'class' => '',
	        'id' => '',
	    ),
	    'collapsed' => '',
	    'min' => 1,
	    'max' => '',
	    'layout' => 'block',         // table | block | row
	    'button_label' => 'Add Image',
	    'sub_fields' => array (
			array (
			    'key' => 'field_'.$block.'_image',
			    'label' => 'Image',
			    'name' => 'image',
			    'instructions' => '',
			    'type' => 'image',
			    'required' => 1,
			    'conditional_logic' => 0,
			    'wrapper' => array (
			        'width' => '',
			        'class' => '',
			        'id' => '',
			    ),
			    'return_format' => 'object',       // array | url | id
			    'preview_size' => 'medium',
			    'library' => 'all',       // all | uploadedTo
			    'min_width' => '',
			    'min_height' => '',
			    'min_size' => '',
			    'max_width' => '',
			    'max_height' => '',
			    'max_size' => '',
			    'mime_types' => '',
			),
			array (
			    'key' => 'field_'.$block.'_caption',
			    'label' => 'Caption',
			    'name' => 'caption',
			    'type' => 'text',
			    'instructions' => '',
			    'required' => 0,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
						   'field' => 'field_'.$block.'_format',
							'operator' => '==',
						   	'value' => 'gallery',
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
			   'key' => 'field_'.$block.'_open_modal',
			   'label' => 'Open Image in Modal',
			   'name' => 'open_modal',
			   'type' => 'true_false',
			   'instructions' => '',
			   'required' => 0,
			   'conditional_logic' => array (
			       'status' => 1,
			       'rules' => array (
			           array (
			              'field' => 'field_'.$block.'_format',
			               'operator' => '==',
			              'value' => 'gallery',
			         ),
			       ),
			       'allorany' => 'all',
			   ),
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
			),
			// GRAV_BLOCKS::get_link_fields(array ('name' => 'link', 'show_text' => false)),
	    ),
	),
);

return array (
	'label' => 'Image Gallery',
	'name' => $block,
	'display' => 'block',
	'min' => '',
	'max' => '',
	'sub_fields' => $block_fields,
	'grav_blocks_settings' => array(
		'icon' => 'gravicon-media',
		'description' => '<div class="row">
				<div class="columns medium-6">
					<img src="'.plugins_url().'/blueprint-blocks/grav-blocks/image-gallery/image-gallery.svg">
					<img src="'.plugins_url().'/blueprint-blocks/grav-blocks/image-gallery/image-gallery-alt.svg">
				</div>
				<div class="columns medium-6">
					<p>This block that allows for a full width image, or an image that is contained within the content width. This image also has the ability to link to a page, URL, file download or even play a video in a modal.</p>
					<p><strong>Available Fields:</strong></p>
					<ul>
						<li>Background<em> ( for a two layered image effect )</em></li>
						<li>Image</li>
						<li>Add Padding <em>( constrains image to width of content instead of full screen )</em></li>
						<li>Link <em>( Page, URL, File, Video )</em></li>
					</ul>
				</div>
			</div>'
	),
);
