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
		'label' => 'Grid Title',
		'name' => 'grid_title',
		'type' => 'text',
		'column_width' => '',
		'default_value' => '',
		'instructions' => '',
		'placeholder' => '',
		'prepend' => '',
		'append' => '',
		'formatting' => 'none',
		'maxlength' => '',
	),
	array (
	    'key' => 'field_'.$block.'_num_columns_small',
	    'label' => 'Number of Columns on Small Screens',
	    'name' => 'num_columns_small',
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
	    'conditional_logic' => 0,
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
	    'conditional_logic' => 0,
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
	    'conditional_logic' => 0,
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
		'key' => 'field_'.$block.'_2',
		'label' => 'Grid Items',
		'name' => 'grid_items',
		'type' => 'repeater',
		'column_width' => '',
		'instructions' => '',
		'sub_fields' => array (
			array (
				'key' => 'field_'.$block.'_8',
				'label' => 'Item Title',
				'name' => 'item_title',
				'type' => 'text',
				'column_width' => '',
				'default_value' => '',
				'instructions' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
				'conditional_logic' => 0,
			),
			array (
				'key' => 'field_'.$block.'_9',
				'label' => 'Image',
				'name' => 'item_image',
				'instructions' => '',
				'type' => 'image',
				'column_width' => '',
				'save_format' => 'object',
				'library' => 'all',
				'preview_size' => 'medium',
			),
			GRAV_BLOCKS::get_link_fields(array('name' => 'link', 'show_text' => false)),
			array (
			    'key' => 'field_'.$block.'_button_text',
			    'label' => 'Button Text',
			    'name' => 'button_text',
			    'type' => 'text',
			    'instructions' => '(Optional) If a value is added, you will see a button at the bottom of the grid item.',
			    'required' => 0,
			    'conditional_logic' => array (
			        'status' => 1,
			        'rules' => array (
			            array (
			               'field' => 'field_'.$block.'_link_type',
			                'operator' => '!=',
			               'value' => 'none',
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
				'key' => 'field_'.$block.'_10',
				'label' => 'Content',
				'name' => 'item_content',
				'type' => 'textarea',
				'column_width' => '',
				'default_value' => '',
				'conditional_logic' => 0,
			),
		),
		'min' => '1',
		'max' => '',
		'layout' => 'row',
		'button_label' => 'Add Grid Item',
	),
);

return array (
	'name' => $block,
	'label' => 'Grid',
	'display' => 'block',
	'sub_fields' => $block_fields,
	'min' => '',
	'max' => '',
	'grav_blocks_settings' => array(
		'icon' => 'gravicon-gallery',
		'description' => '<div class="row">
				<div class="columns medium-6">
					<img src="'.plugins_url().'/blueprint-blocks/grav-blocks/grid/gallery.svg">
				</div>
				<div class="columns medium-6">
					<p>When you want to display more than one image, this flexible block is the way to go. It allows for multiple grid items each with an ability for a title, image, link and description.</p>
					<p><strong>Available Fields:</strong></p>
					<ul>
						<li>Background</li>
						<li>Grid Title</li>
						<li>Grid Item
							<ul>
								<li>Item Title</li>
								<li>Image</li>
								<li>Link <em>( Page, URL, File, Video )</em></li>
								<li>Description</li>
							</ul>
						</li>
					</ul>
				</div>
			</div>'
	),
);
