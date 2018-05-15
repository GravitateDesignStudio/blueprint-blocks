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
	        'slider-full' => 'Slider (Full Width)',
			'gallery' => 'Gallery'
	    ),
	    'other_choice' => 0,
	    'save_other_choice' => 0,
	    'default_value' => 'slider',
	    'layout' => 'horizontal',
		'block_options' => 1
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
			GRAV_BLOCKS::get_link_fields(array ('name' => 'link', 'show_text' => false)),
	    ),
	),
	// array (
	// 	'key' => 'field_'.$block.'_2',
	// 	'label' => 'Add Padding',
	// 	'name' => 'padding',
	// 	'type' => 'true_false',
	// 	'column_width' => '',
	// 	'message' => '',
	// 	'default_value' => 0,
	// ),
);

return array (
	'label' => 'Image Gallery',
	'name' => $block,
	'display' => 'row',
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
