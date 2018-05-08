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
	   'key' => 'field_'.$block.'_use_alternate_title',
	   'label' => 'Use Alternate Title',
	   'name' => 'use_alternate_title',
	   'type' => 'true_false',
	   'instructions' => 'Otherwise use the Title of the Page',
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
	),
	array (
		'key' => 'field_'.$block.'_title',
		'label' => 'Alternate Title',
		'name' => 'title',
		'type' => 'text',
		'column_width' => '',
		'default_value' => '',
		'instructions' => '',
		'placeholder' => '',
		'prepend' => '',
		'append' => '',
		'formatting' => 'none', 		// none | html
		'maxlength' => '',
		'conditional_logic' => array (
		    array (
		        array (
		            'field' => 'field_'.$block.'_use_alternate_title',
		            'operator' => '==',
		            'value' => 1,
		        ),
		    ),
		),
	),
	array (
		'key' => 'field_'.$block.'_sub_title',
		'label' => 'Sub Title',
		'name' => 'sub_title',
		'type' => 'text',
		'column_width' => '',
		'default_value' => '',
		'instructions' => '(Optional)',
		'placeholder' => '',
		'prepend' => '',
		'append' => '',
		'formatting' => 'none', 		// none | html
		'maxlength' => '',
	),
	array (
		'key' => 'field_'.$block.'_intro',
		'label' => 'Intro Text',
		'name' => 'intro',
		'type' => 'textarea',
		'instructions' => 'Short Description of the page. (Optional)',
		'default_value' => '',
		'placeholder' => '',
		'maxlength' => '',
		'rows' => '',
		'formatting' => 'html',
	),
	array (
		'key' => 'field_'.$block.'_3',
		'label' => 'Buttons',
		'name' => 'buttons',
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
		'min' => '1',
		'max' => '',
		'layout' => 'block',
		'button_label' => 'Add Button',
		'sub_fields' => array(
			GRAV_BLOCKS::get_link_fields( 'button' )
		),
	),
	array (
	    'key' => 'field_'.$block.'_content_alignment',
	    'label' => 'Content Alignment',
	    'name' => 'content_alignment',
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
	        'left' => 'Left',
	        'center' => 'Center',
	        'right' => 'Right',
	    ),
	    'other_choice' => 0,
	    'save_other_choice' => 0,
	    'default_value' => 'center',
	    'layout' => 'horizontal',
		'block_options' => 1,
	),
);

return array (
	'label' => 'Banner',
	'name' => $block,
	'display' => 'block',
	'min' => '',
	'max' => '',
	'sub_fields' => $block_fields,
	'grav_blocks_settings' => array(
		'repeater' => false,
		'repeater_label' => '',
		'icon' => 'gravicon-cta',
		'description' => '<div class="row">
				<div class="columns medium-6">
					<img src="'.plugins_url().'/gravitate-blocks/grav-blocks/calltoaction/cta.svg">
				</div>
				<div class="columns medium-6">
					<p>With this block, you can create buttons&nbsp;for any needed conversion. Whether it’s to direct the user to the contact page or download a white-paper, this block will allow multiple buttons, each with the ability to link to a current page on the site, a specified URL, a file to download, or video to play in a modal.</p>
					<p><strong>Available Fields:</strong></p>
					<ul>
						<li>Title</li>
						<li>Description</li>
						<li>Background</li>
						<li>Buttons <em>( Multiple )</em></li>
					</ul>
				</div>
			</div>'
	),
);