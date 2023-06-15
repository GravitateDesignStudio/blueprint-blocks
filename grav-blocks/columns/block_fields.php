<?php

/*
*
* Column Block
*
* Available Variables:
* $block 					= Name of Block Folder
* $block_backgrounds 		= Array for Background Options
* $block_background_image = Array for Background Image Option
*
* This file must return an array();
*
*/

// Fonts come from a custom Fontello font. The font is loade from the base-blocks plugin.
$content_column_choices = array(
    1 => '<i class="grav-icon icon-1-col-narrow"></i>',
    2 => '<i class="grav-icon icon-2-col"></i>',
    213 => '<i class="grav-icon icon-sidebar-left"></i>',
    231 => '<i class="grav-icon icon-sidebar-right"></i>',
    3 => '<i class="grav-icon icon-3-col"></i>',
    4 => '<i class="grav-icon icon-4-col"></i>'
);
$block_fields = array();
$max_columns = apply_filters('grav_blocks_content_columns_max', 4);

$block_fields[] = array (
    'key' => 'field_'.$block.'_num_columns',
    'label' => 'Columns Selector',
    'name' => 'num_columns',
    'type' => 'button_group',
    'choices' => $content_column_choices,
);

$conditional_2 = array(
    array(
        array(
            'field' => 'field_'.$block.'_num_columns',
            'operator' => '!=',
            'value' => 1,
        ),
    )
);

$conditional_3 = array (
    array(
        array(
            'field' => 'field_'.$block.'_num_columns',
            'operator' => '==',
            'value' => 3,
        ),
    ),
    array(
        array(
            'field' => 'field_'.$block.'_num_columns',
            'operator' => '==',
            'value' => 4,
        ),
    ),
);

$conditional_4 = 
    array(
        array(
            'field' => 'field_'.$block.'_num_columns',
            'operator' => '==',
            'value' => 4,
        ),
    );

$conditional = [
    1 => [],
    2 => $conditional_2,
    3 => $conditional_3,
    4 => $conditional_4,
];

for ($i = 1; $i <= $max_columns; $i++) {
	$block_fields[] = array (
	    'key' => 'field_'.$block.'_'.$i,
	    'label' => 'Column '.$i,
	    'name' => 'column_'.$i,
	    'type' => 'wysiwyg',
	    'conditional_logic' => $conditional[$i],
	    'tabs' => 'all',         // all | visual | text
	    'toolbar' => 'full',     // full | basic
	    'media_upload' => 1,
	);
}

return array (
	'label' => 'Columns',
	'name' => $block,
	'display' => 'block',
	'min' => '',
	'max' => '',
	'sub_fields' => $block_fields,
	'grav_blocks_settings' => array(
		'version' => '2.0',
		'icon' => 'gravicon-content-2col',
		'description' => '<div class="row"><div class="columns medium-6"><img src="'.plugins_url().'/blueprint-blocks/grav-blocks/columns/content_1.svg"><img src="'.plugins_url().'/blueprint-blocks/grav-blocks/columns/content_2.svg"><img src="'.plugins_url().'/blueprint-blocks/grav-blocks/columns/content_3.svg"></div><div class="columns medium-6"><p>Our most basic block. This block allows for the use of one, two or three columns of WordPress WYSIWYGs ( What You See Is What You Get ). The WYSIWYG allows you to add most of the basic types of content from images, to paragraph text as well as H1 – H6 headings. You can also create ordered and unordered lists as well as do type treatments like <strong>bold</strong> and <em>italic</em>.</p>
<p>While this block is very capable and can allow for a range of content types and layouts the control of the layout is not as precise. The tendency would be to try and use this block for much of your layouts, however with the research and strategy that has gone into each one of our blocks we highly suggest looking into them for their abilities to display your content in atheistically pleasing and user friendly way.</p></div></div>'
	),
);
