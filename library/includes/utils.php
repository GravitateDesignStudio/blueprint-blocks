<?php
/**
 * Get term options for ACF select field
 */
function get_term_options($taxonomy) {
	$terms = get_terms( array(
		'taxonomy' => $taxonomy,
		'hide_empty' => false,
	));
	
	$term_options = array_map( function( $term ) {
		return array(
			$term->slug => $term->name,
		);
	}, $terms );
	
	return array_merge(...$term_options);
};
