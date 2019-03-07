<?php

function slb_register_slb_subscriber() {

	/**
	 * Post Type: Subscribers.
	 */

	$labels = array(
		"name" => __( "Subscribers", "twentynineteen" ),
		"singular_name" => __( "Subscriber", "twentynineteen" ),
	);

	$args = array(
		"label" => __( "Subscribers", "twentynineteen" ),
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
		"rewrite" => array( "slug" => "slb_subscriber", "with_front" => false ),
		"query_var" => true,
		"supports" => false,
	);

	register_post_type( "slb_subscriber", $args );
}

add_action( 'init', 'slb_register_slb_subscriber' );


if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_5c6cf978c1c05',
	'title' => 'Subscriber Details',
	'fields' => array(
		array(
			'key' => 'field_5c6cf98ac0022',
			'label' => 'First Name',
			'name' => 'slb_fname',
			'type' => 'text',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5c6cf9a0c0023',
			'label' => 'Last Name',
			'name' => 'slb_lname',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5c6cf9bcc0024',
			'label' => 'Email Address',
			'name' => 'slb_email',
			'type' => 'email',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
		),
		array(
			'key' => 'field_5c6cf9e7c0025',
			'label' => 'Subscriptions',
			'name' => 'slb_subscriptions',
			'type' => 'post_object',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array(
				0 => 'slb_list',
			),
			'taxonomy' => '',
			'allow_null' => 1,
			'multiple' => 1,
			'return_format' => 'object',
			'ui' => 1,
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'slb_subscriber',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'acf_after_title',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => array(
		0 => 'permalink',
		1 => 'the_content',
		2 => 'excerpt',
		3 => 'discussion',
		4 => 'comments',
		5 => 'revisions',
		6 => 'slug',
		7 => 'author',
		8 => 'format',
		9 => 'page_attributes',
		10 => 'featured_image',
		11 => 'categories',
		12 => 'tags',
		13 => 'send-trackbacks',
	),
	'active' => true,
	'description' => '',
));

endif;