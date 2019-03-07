<?php

function slb_register_slb_list() {

	/**
	 * Post Type: Lists.
	 */

	$labels = array(
		"name" => __( "Lists", "twentynineteen" ),
		"singular_name" => __( "List", "twentynineteen" ),
	);

	$args = array(
		"label" => __( "Lists", "twentynineteen" ),
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"publicly_queryable" => true,
		"show_ui" => true,
		"delete_with_user" => false,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "slb_list", "with_front" => false ),
		"query_var" => true,
		"supports" => array( "title" ),
	);

	register_post_type( "slb_list", $args );
}

add_action( 'init', 'slb_register_slb_list' );
