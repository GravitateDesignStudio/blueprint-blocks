<?php
/*
* Gravitate Global Blocks File
* Version: 1.0.0
*
*/

class GRAV_GLOBAL_BLOCKS
{
	//create Global Blocks cpt
	public static function setup_global_blocks_cpt()
	{
		$single_label = 'Global Block';
		$plural_label = 'Global Blocks';
		$name = strtolower(sanitize_title($single_label));
		$slug = $name;

		register_post_type(
			$name,
			array(
				'label' => $plural_label,
				'description' => 'Add global blocks and deploy them throughout your site',
				'public' => false,
				'publicly_queryable'  => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' => false,
				'capability_type' => 'page',
				'map_meta_cap' => true,
				'hierarchical' => false,
				'rewrite' => array('with_front' => false, 'slug' => $slug),
				'query_var' => true,
				'exclude_from_search' => true,
				'can_export' => true,
				'has_archive' => false,
				'menu_icon' => 'dashicons-gravitate',
				'menu_position' => 100,
				'supports' => array('title'),
				'labels' => array(
					'name' => $plural_label,
					'singular_name' => $single_label,
					'menu_name' => $plural_label,
					'add_new' => 'Add '.$single_label,
					'add_new_item' => 'Add New '.$single_label,
					'edit' => 'Edit',
					'edit_item' => 'Edit '.$single_label,
					'new_item' => 'New '.$single_label,
					'view' => 'View '.$single_label,
					'view_item' => 'View '.$single_label,
					'search_items' => 'Search '.$plural_label,
					'not_found' => 'No '.$plural_label.' Found',
					'not_found_in_trash' => 'No '.$plural_label.' Found in Trash',
					'parent' => 'Parent '.$single_label,
				)
			)
		);
	}

}
