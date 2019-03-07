<?php
	
/*
Plugin Name: Snappy List Builder
Plugin URI: http://wordpressplugincourse.com/plugins/snappy-list-builder
Description: The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subscribers with a custom download upon opt-in. Build unlimited lists. Import and export subscribers easily with .csv
Version: 1.0
Author: Joel Funk @ Code College
Author URI: http://joelfunk.codecollege.ca
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: snappy-list-builder
*/


/* !0. TABLE OF CONTENTS */

/*
	
	1. HOOKS
		1.1 - registers all our custom shortcodes
		1.2 - register custom admin column headers
		1.3 - register custom admin column data
		1.4 - register ajax actions
		1.5 - load external files to public website
		1.6 - Advanced Custom Fields Settings
		1.7 - register our custom menus
		1.8 - load external files in WordPress admin
		1.9 - register plugin options
	
	2. SHORTCODES
		2.1 - slb_register_shortcodes()
		2.2 - slb_form_shortcode()
		
	3. FILTERS
		3.1 - slb_subscriber_column_headers()
		3.2 - slb_subscriber_column_data()
		3.2.2 - custom admin title values
		3.2.3 - sets title value
		3.3 - slb_list_column_headers()
		3.4 - slb_list_column_data()
		3.5 - acf settings and dir
		3.6 - slb_admin_menus()
		
	4. EXTERNAL SCRIPTS
		4.1 - Include ACF
		4.2 - slb_public_scripts()
		4.3 - slb_admin_scripts()
		
	5. ACTIONS
		5.1 - slb_save_subscription()
		5.2 - slb_save_subscriber()
		5.3 - slb_add_subscription()
		
	6. HELPERS
		6.1 - slb_has_subscriptions()
		6.2 - slb_get_subscriber_id()
		6.3 - slb_get_subscritions()
		6.4 - slb_return_json()
		6.5 - slb_get_acf_key()
		6.6 - slb_get_subscriber_data()
		6.7 - slb_get_page_select()
		6.8 - slb_get_default_page_options()
		6.9 - slb_get_option()
		6.10 - slb_get_current_options()
		
	7. CUSTOM POST TYPES
		7.1 - subscribers
		7.2 - lists
	
	8. ADMIN PAGES
		8.1 - slb_dashboard_admin_page()
		8.2 - slb_import_admin_page()
		8.3 - slb_options_admin_page()
	
	9. SETTINGS
		9.1 - slb_register_options()

*/




/* !1. HOOKS */

// 1.1
// hint: registers all our custom shortcodes on init
add_action('init', 'slb_register_shortcodes');

// 1.2
// hint: register custom admin column headers
add_filter('manage_edit-slb_subscriber_columns','slb_subscriber_column_headers');
add_filter('manage_edit-slb_list_columns','slb_list_column_headers');

// 1.3
// hint: register custom admin column data
add_filter('manage_slb_subscriber_posts_custom_column','slb_subscriber_column_data',1,2);
add_filter('manage_slb_list_posts_custom_column','slb_list_column_data',1,2);
add_action('admin_head-edit.php', 'slb_register_custom_admin_titles');

// 1.4
// hint: register ajax actions
add_action('wp_ajax_nopriv_slb_save_subscription', 'slb_save_subscription'); // regular website visitor
add_action('wp_ajax_slb_save_subscription', 'slb_save_subscription'); // admin user

// 1.5
// load external files to public website
add_action('wp_enqueue_scripts', 'slb_public_scripts');

// 1.6
// Advanced Custom Fields Settings
add_filter('acf/settings/path', 'slb_acf_settings_path');
add_filter('acf/settings/dir', 'slb_acf_settings_dir');
add_filter('acf/settings/show_admin', '__return_false'); //turn this true to see on menu

// 1.7 
// hint: register our custom menus
add_action('admin_menu', 'slb_admin_menus');

// 1.8
// hint: load external files in WordPress admin
add_action('admin_enqueue_scripts', 'slb_admin_scripts');

// 1.9
// register plugin options
add_action('admin_init', 'slb_register_options');

/* !2. SHORTCODES */

// 2.1
// hint: registers all our custom shortcodes
function slb_register_shortcodes() {
	
	add_shortcode('slb_form', 'slb_form_shortcode');
	
}

// 2.2
// hint: returns a html string for a email capture form
function slb_form_shortcode( $args, $content="") {
	
	// get the list id
	$list_id = 0;
	if( isset($args['id']) ) $list_id = (int)$args['id'];

	$title = '';
	if( isset($args['title']) ) $title = (string)$args['title'];
	
	// setup our output variable - the form html 
	$output = '
	
		<div class="slb">
		
			<form id="slb_form" name="slb_form" class="slb-form" method="post"
			action="/wp-admin/admin-ajax.php?action=slb_save_subscription" method="post">
			
				<input type="hidden" name="slb_list" value="'. $list_id .'">';
				
				
				if( strlen($title) ):
				
					$output .= '<h3 class="slb-title">'. $title .'</h3>';
				
				endif;
			
				$output .='<p class="slb-input-container">
				
					<label>Your Name</label><br />
					<input type="text" name="slb_fname" placeholder="First Name" />
					<input type="text" name="slb_lname" placeholder="Last Name" />
				
				</p>
				
				<p class="slb-input-container">
				
					<label>Your Email</label><br />
					<input type="email" name="slb_email" placeholder="ex. you@email.com" />
				
				</p>';
				
				// including content in our form html if content is passed into the function
				if( strlen($content) ):
				
					$output .= '<div class="slb-content">'. wpautop($content) .'</div>';
				
				endif;
				
				// completing our form html
				$output .= '<p class="slb-input-container">
				
					<input type="submit" name="slb_submit" value="Sign Me Up!" />
				
				</p>
			
			</form>
		
		</div>
	
	';
	
	// return our results/html
	return $output;
	
}




/* !3. FILTERS */

// 3.1
function slb_subscriber_column_headers( $columns ) {
	
	// creating custom column header data
	$columns = array(
		'cb'=>'<input type="checkbox" />',
		'title'=>__('Subscriber Name'),
		'email'=>__('Email Address'),	
	);
	
	// returning new columns
	return $columns;
	
}

// 3.2
function slb_subscriber_column_data( $column, $post_id ) {
	
	// setup our return text
	$output = '';
	
	switch( $column ) {
		
		case 'name':
			// get the custom name data
			$fname = get_field('slb_fname', $post_id );
			$lname = get_field('slb_lname', $post_id );
			$output .= $fname .' '. $lname;
			break;
		case 'email':
			// get the custom email data
			$email = get_field('slb_email', $post_id );
			$output .= $email;
			break;
		
	}
	
	// echo the output
	echo $output;
	
}

// 3.2.2
// hint: registers special custom admin title columns
function slb_register_custom_admin_titles(){
	add_filter(
		'the_title',
		'slb_custom_admin_titles',
		99,
		2
	);
}

//3.2.3
// hint: handles custom admin title "title" column data for post types without titles
function slb_custom_admin_titles($title, $post_id){
	global $post;
	$output = $title;
	if(isset($post->post_type)):
		switch($post->post_type){
			case 'slb_subscriber':
				$fname = get_field('slb_fname', $post_id);
				$lname = get_field('slb_lname', $post_id);
				$output = $fname . " " . $lname;
				break;		
		}
	endif;
	return $output;
}

// 3.3
function slb_list_column_headers( $columns ) {
	
	// creating custom column header data
	$columns = array(
		'cb'=>'<input type="checkbox" />',
		'title'=>__('List Name'),
		'shortcode'=>__('Shortcode'),	
	);
	
	// returning new columns
	return $columns;
	
}

// 3.4
function slb_list_column_data( $column, $post_id ) {
	
	// setup our return text
	$output = '';
	
	switch( $column ) {
		
		case 'shortcode':
			$output .= '[slb_form id="'. $post_id .'"]';
			break;
		
	}
	
	// echo the output
	echo $output;
	
}

// 3.5
function slb_acf_settings_path( $path ) {
    return $path;    
}

function slb_acf_settings_dir( $dir ) {
    return $dir; 
}

// 3.6
// hint: registers custom plugin admin menus
function slb_admin_menus() {
	
	/* main menu */
	
		$top_menu_item = 'slb_dashboard_admin_page';
	    
	    add_menu_page( '', 'List Builder', 'manage_options', 'slb_dashboard_admin_page', 'slb_dashboard_admin_page', 'dashicons-email-alt' );
    
    /* submenu items */
    
	    // dashboard
	    add_submenu_page( $top_menu_item, '', 'Dashboard', 'manage_options', $top_menu_item, $top_menu_item );
	    
	    // email lists
	    add_submenu_page( $top_menu_item, '', 'Email Lists', 'manage_options', 'edit.php?post_type=slb_list' );
	    
	    // subscribers
	    add_submenu_page( $top_menu_item, '', 'Subscribers', 'manage_options', 'edit.php?post_type=slb_subscriber' );
	    
	    // import subscribers
	    add_submenu_page( $top_menu_item, '', 'Import Subscribers', 'manage_options', 'slb_import_admin_page', 'slb_import_admin_page' );
	    
	    // plugin options
	    add_submenu_page( $top_menu_item, '', 'Plugin Options', 'manage_options', 'slb_options_admin_page', 'slb_options_admin_page' );

}





/* !4. EXTERNAL SCRIPTS */

// 4.1
// Include ACF
include_once( plugin_dir_path( __FILE__ ) .'lib/advanced-custom-fields/acf.php' );

// 4.2
// hint: loads external files into PUBLIC website
function slb_public_scripts() {
	// register scripts with WordPress's internal library
	wp_register_script('snappy-list-builder-js-public', plugins_url('/js/public/snappy-list-builder.js',__FILE__), array('jquery'),'',true);
	wp_register_style('snappy-list-builder-css-public', plugins_url('/css/public/snappy-list-builder.css',__FILE__));
	
	// add to que of scripts that get loaded into every page
	wp_enqueue_script('snappy-list-builder-js-public');
	wp_enqueue_style('snappy-list-builder-css-public');
	
}

// 4.3
// hint: loads external files into wordpress ADMIN
function slb_admin_scripts() {
	
	// register scripts with WordPress's internal library
	wp_register_script('snappy-list-builder-js-private', plugins_url('/js/private/snappy-list-builder.js',__FILE__), array('jquery'),'',true);
	
	// add to que of scripts that get loaded into every admin page
	wp_enqueue_script('snappy-list-builder-js-private');
	
}



/* !5. ACTIONS */

// 5.1
// hint: saves subscription data to an existing or new subscriber
function slb_save_subscription() {
	
	// setup default result data
	$result = array(
		'status' => 0,
		'message' => 'Subscription was not saved. ',
		'error'=>'',
		'errors'=>array()
	);
	
	try {
		
		// get list_id
		$list_id = (int)$_POST['slb_list'];
	
		// prepare subscriber data
		$subscriber_data = array(
			'fname'=> esc_attr( $_POST['slb_fname'] ),
			'lname'=> esc_attr( $_POST['slb_lname'] ),
			'email'=> esc_attr( $_POST['slb_email'] ),
		);
		
		// setup our errors array
		$errors = array();
		
		// form validation
		if( !strlen( $subscriber_data['fname'] ) ) $errors['fname'] = 'First name is required.';
		if( !strlen( $subscriber_data['email'] ) ) $errors['email'] = 'Email address is required.';
		if( strlen( $subscriber_data['email'] ) && !is_email( $subscriber_data['email'] ) ) $errors['email'] = 'Email address must be valid.';
		
		// IF there are errors
		if( count($errors) ):
		
			// append errors to result structure for later use
			$result['error'] = 'Some fields are still required. ';
			$result['errors'] = $errors;
		
		else: 
		// IF there are no errors, proceed...
		
			// attempt to create/save subscriber
			$subscriber_id = slb_save_subscriber( $subscriber_data );
			
			// IF subscriber was saved successfully $subscriber_id will be greater than 0
			if( $subscriber_id ):
			
				// IF subscriber already has this subscription
				if( slb_subscriber_has_subscription( $subscriber_id, $list_id ) ):
				
					// get list object
					$list = get_post( $list_id );
					
					// return detailed error
					$result['error'] = esc_attr( $subscriber_data['email'] .' is already subscribed to '. $list->post_title .'.');
					
				else: 
				
					// save new subscription
					$subscription_saved = slb_add_subscription( $subscriber_id, $list_id );
			
					// IF subscription was saved successfully
					if( $subscription_saved ):
					
						// subscription saved!
						$result['status']=1;
						$result['message']='Subscription saved';
						
					else: 
					
						// return detailed error
						$result['error'] = 'Unable to save subscription.';
					
					
					endif;
				
				endif;
			
			endif;
		
		endif;
		
	} catch ( Exception $e ) {
		
	}
	
	// return result as json
	slb_return_json($result);
	
}

// 5.2
// hint: creates a new subscriber or updates and existing one
function slb_save_subscriber( $subscriber_data ) {
	
	// setup default subscriber id
	// 0 means the subscriber was not saved
	$subscriber_id = 0;
	
	try {
		
		$subscriber_id = slb_get_subscriber_id( $subscriber_data['email'] );
		
		// IF the subscriber does not already exists...
		if( !$subscriber_id ):
		
			// add new subscriber to database	
			$subscriber_id = wp_insert_post( 
				array(
					'post_type'=>'slb_subscriber',
					'post_title'=>$subscriber_data['fname'] .' '. $subscriber_data['lname'],
					'post_status'=>'publish',
				), 
				true
			);
		
		endif;
		
		// add/update custom meta data
		update_field(slb_get_acf_key('slb_fname'), $subscriber_data['fname'], $subscriber_id);
		update_field(slb_get_acf_key('slb_lname'), $subscriber_data['lname'], $subscriber_id);
		update_field(slb_get_acf_key('slb_email'), $subscriber_data['email'], $subscriber_id);
		
	} catch( Exception $e ) {
		
		// a php error occurred
		
	}
	
	// return subscriber_id
	return $subscriber_id;
	
}

// 5.3
// hint: adds list to subscribers subscriptions
function slb_add_subscription( $subscriber_id, $list_id ) {
	
	// setup default return value
	$subscription_saved = false;
	
	// IF the subscriber does NOT have the current list subscription
	if( !slb_subscriber_has_subscription( $subscriber_id, $list_id ) ):
	
		// get subscriptions and append new $list_id
		$subscriptions = slb_get_subscriptions( $subscriber_id );
		$subscriptions[]=$list_id;
		
		// update slb_subscriptions
		update_field( slb_get_acf_key('slb_subscriptions'), $subscriptions, $subscriber_id );
		
		// subscriptions updated!
		$subscription_saved = true;
	
	endif;
	
	// return result
	return $subscription_saved;
	
}





/* !6. HELPERS */

// 6.1
// hint: returns true or false
function slb_subscriber_has_subscription( $subscriber_id, $list_id ) {
	
	// setup default return value
	$has_subscription = false;
	
	// get subscriber
	$subscriber = get_post($subscriber_id);
	
	// get subscriptions
	$subscriptions = slb_get_subscriptions( $subscriber_id );
	
	// check subscriptions for $list_id
	if( in_array($list_id, $subscriptions) ):
	
		// found the $list_id in $subscriptions
		// this subscriber is already subscribed to this list
		$has_subscription = true;
	
	else:
	
		// did not find $list_id in $subscriptions
		// this subscriber is not yet subscribed to this list
	
	endif;
	
	return $has_subscription;
	
}

// 6.2
// hint: retrieves a subscriber_id from an email address
function slb_get_subscriber_id( $email ) {
	
	$subscriber_id = 0;
	
	try {
	
		// check if subscriber already exists
		$subscriber_query = new WP_Query( 
			array(
				'post_type'		=>	'slb_subscriber',
				'posts_per_page' => 1,
				'meta_key' => 'slb_email',
				'meta_query' => array(
				    array(
				        'key' => 'slb_email',
				        'value' => $email,  // or whatever it is you're using here
				        'compare' => '=',
				    ),
				),
			)
		);
		
		// IF the subscriber exists...
		if( $subscriber_query->have_posts() ):
		
			// get the subscriber_id
			$subscriber_query->the_post();
			$subscriber_id = get_the_ID();
			
		endif;
	
	} catch( Exception $e ) {
		
		// a php error occurred
		
	}
		
	// reset the Wordpress post object
	wp_reset_query();
	
	return (int)$subscriber_id;
	
}

// 6.3
// hint: returns an array of list_id's
function slb_get_subscriptions( $subscriber_id ) {
	
	$subscriptions = array();
	
	// get subscriptions (returns array of list objects)
	$lists = get_field( slb_get_acf_key('slb_subscriptions'), $subscriber_id );
	
	// IF $lists returns something
	if( $lists ):
	
		// IF $lists is an array and there is one or more items
		if( is_array($lists) && count($lists) ):
			// build subscriptions: array of list id's
			foreach( $lists as &$list):
				$subscriptions[]= (int)$list->ID;
			endforeach;
		elseif( is_numeric($lists) ):
			// single result returned
			$subscriptions[]= $lists;
		endif;
	
	endif;
	
	return (array)$subscriptions;
	
}

// 6.4
function slb_return_json( $php_array ) {
	
	// encode result as json string
	$json_result = json_encode( $php_array );
	
	// return result
	die( $json_result );
	
	// stop all other processing 
	exit;
	
}


//6.5
// hint: gets the unique act field key from the field name
function slb_get_acf_key($field_name){
    $field_key = $field_name;
    switch($field_name){
        case 'slb_fname':
            $field_key = 'field_5c6cf98ac0022';
            break;
        case 'slb_lname':
            $field_key = 'field_5c6cf9a0c0023';
            break;
        case 'slb_email':
            $field_key = 'field_5c6cf9bcc0024';
            break;
        case 'slb_subscriptions':
            $field_key = 'field_5c6cf9e7c0025';
            break;
    }

    return $field_key;
}


// 6.6
// hint: returns an array of subscriber data including subscriptions
function slb_get_subscriber_data( $subscriber_id ) {
	
	// setup subscriber_data
	$subscriber_data = array();
	
	// get subscriber object
	$subscriber = get_post( $subscriber_id );
	
	// IF subscriber object is valid
	if( isset($subscriber->post_type) && $subscriber->post_type == 'slb_subscriber' ):
	
		$fname = get_field( slb_get_acf_key('slb_fname'), $subscriber_id);
		$lname = get_field( slb_get_acf_key('slb_lname'), $subscriber_id);
	
		// build subscriber_data for return
		$subscriber_data = array(
			'name'=> $fname .' '. $lname,
			'fname'=>$fname,
			'lname'=>$lname,
			'email'=>get_field( slb_get_acf_key('slb_email'), $subscriber_id),
			'subscriptions'=>slb_get_subscriptions( $subscriber_id )
		);
		
	
	endif;
	
	// return subscriber_data
	return $subscriber_data;
	
}

// 6.7
// hint: returns html for a page selector
function slb_get_page_select( $input_name="slb_page", $input_id="", $parent=-1, $value_field="id", $selected_value="" ) {
	
	// get WP pages
	$pages = get_pages( 
		array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'post_type' => 'page',
			'parent' => $parent,
			'status'=>array('draft','publish'),	
		)
	);
	
	// setup our select html
	$select = '<select name="'. $input_name .'" ';
	
	// IF $input_id was passed in
	if( strlen($input_id) ):
	
		// add an input id to our select html
		$select .= 'id="'. $input_id .'" ';
	
	endif;
	
	// setup our first select option
	$select .= '><option value="">- Select One -</option>';
	
	// loop over all the pages
	foreach ( $pages as &$page ): 
	
		// get the page id as our default option value
		$value = $page->ID;
		
		// determine which page attribute is the desired value field
		switch( $value_field ) {
			case 'slug':
				$value = $page->post_name;
				break;
			case 'url':
				$value = get_page_link( $page->ID );
				break;
			default:
				$value = $page->ID;
		}
		
		// check if this option is the currently selected option
		$selected = '';
		if( $selected_value == $value ):
			$selected = ' selected="selected" ';
		endif;
	
		// build our option html
		$option = '<option value="' . $value . '" '. $selected .'>';
		$option .= $page->post_title;
		$option .= '</option>';
		
		// append our option to the select html
		$select .= $option;
		
	endforeach;
	
	// close our select html tag
	$select .= '</select>';
	
	// return our new select 
	return $select;
	
}

// 6.8
// hint: returns default option values as an associative array
function slb_get_default_options() {
	
	$defaults = array();
	
	try {
		
		// get front page id
		$front_page_id = get_option('page_on_front');
	
		// setup default email footer
		$default_email_footer = '
			<p>
				Sincerely, <br /><br />
				The '. get_bloginfo('name') .' Team<br />
				<a href="'. get_bloginfo('url') .'">'. get_bloginfo('url') .'</a>
			</p>
		';
		
		// setup defaults array
		$defaults = array(
			'slb_manage_subscription_page_id'=>$front_page_id,
			'slb_confirmation_page_id'=>$front_page_id,
			'slb_reward_page_id'=>$front_page_id,
			'slb_default_email_footer'=>$default_email_footer,
			'slb_download_limit'=>3,
		);
	
	} catch( Exception $e) {
		
		// php error
		
	}
	
	// return defaults
	return $defaults;
	
	
}

// 6.9
// hint: returns the requested page option value or it's default
function slb_get_option( $option_name ) {
	
	// setup return variable
	$option_value = '';	
	
	
	try {
		
		// get default option values
		$defaults = slb_get_default_options();
		
		// get the requested option
		switch( $option_name ) {
			
			case 'slb_manage_subscription_page_id':
				// subscription page id
				$option_value = (get_option('slb_manage_subscription_page_id')) ? get_option('slb_manage_subscription_page_id') : $defaults['slb_manage_subscription_page_id'];
				break;
			case 'slb_confirmation_page_id':
				// confirmation page id
				$option_value = (get_option('slb_confirmation_page_id')) ? get_option('slb_confirmation_page_id') : $defaults['slb_confirmation_page_id'];
				break;
			case 'slb_reward_page_id':
				// reward page id
				$option_value = (get_option('slb_reward_page_id')) ? get_option('slb_reward_page_id') : $defaults['slb_reward_page_id'];
				break;
			case 'slb_default_email_footer':
				// email footer
				$option_value = (get_option('slb_default_email_footer')) ? get_option('slb_default_email_footer') : $defaults['slb_default_email_footer'];
				break;
			case 'slb_download_limit':
				// reward download limit
				$option_value = (get_option('slb_download_limit')) ? (int)get_option('slb_download_limit') : $defaults['slb_download_limit'];
				break;
			
		}
		
	} catch( Exception $e) {
		
		// php error
		
	}
	
	// return option value or it's default
	return $option_value;
	
}

// 6.10
// hint: get's the current options and returns values in associative array
function slb_get_current_options() {
	
	// setup our return variable
	$current_options = array();
	
	try {
	
		// build our current options associative array
		$current_options = array(
			'slb_manage_subscription_page_id' => slb_get_option('slb_manage_subscription_page_id'),
			'slb_confirmation_page_id' => slb_get_option('slb_confirmation_page_id'),
			'slb_reward_page_id' => slb_get_option('slb_reward_page_id'),
			'slb_default_email_footer' => slb_get_option('slb_default_email_footer'),
			'slb_download_limit' => slb_get_option('slb_download_limit'),
		);
	
	} catch( Exception $e ) {
		
		// php error
	
	}
	
	// return current options
	return $current_options;
	
}



/* !7. CUSTOM POST TYPES */

// 7.1
// subscribers
include_once( plugin_dir_path( __FILE__ ) . 'cpt/slb_subscriber.php');

//7.2
// lists
include_once( plugin_dir_path( __FILE__ ) . 'cpt/slb_list.php');




/* !8. ADMIN PAGES */
// 8.1
// hint: dashboard admin page
function slb_dashboard_admin_page() {
	
	
	$output = '
		<div class="wrap">
			
			<h2>Snappy List Builder</h2>
			
			<p>The ultimate email list building plugin for WordPress. Capture new subscribers. Reward subscribers with a custom download upon opt-in. Build unlimited lists. Import and export subscribers easily with .csv</p>
		
		</div>
	';
	
	echo $output;
	
}

// 8.2
// hint: import subscribers admin page
function slb_import_admin_page() {
	
	
	$output = '
		<div class="wrap">
			
			<h2>Import Subscribers</h2>
			
			<p>Page description...</p>
		
		</div>
	';
	
	echo $output;
	
}

// 8.3
// hint: plugin options admin page
function slb_options_admin_page() {
	
	// get the default values for our options
	$options = slb_get_current_options();
	
	echo('<div class="wrap">
		
		<h2>Snappy List Builder Options</h2>
		
		<form action="options.php" method="post">');
		
			// outputs a unique nounce for our plugin options
			settings_fields('slb_plugin_options');
			// generates a unique hidden field with our form handling url
			@do_settings_sections('slb_plugin_options');
			
			echo('<table class="form-table">
			
				<tbody>
			
					<tr>
						<th scope="row"><label for="slb_manage_subscription_page_id">Manage Subscriptions Page</label></th>
						<td>
							'. slb_get_page_select( 'slb_manage_subscription_page_id', 'slb_manage_subscription_page_id', 0, 'id', $options['slb_manage_subscription_page_id'] ) .'
							<p class="description" id="slb_manage_subscription_page_id-description">This is the page where Snappy List Builder will send subscribers to manage their subscriptions. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_manage_subscriptions]</strong>.</p>
						</td>
					</tr>
					
			
					<tr>
						<th scope="row"><label for="slb_confirmation_page_id">Opt-In Page</label></th>
						<td>
							'. slb_get_page_select( 'slb_confirmation_page_id', 'slb_confirmation_page_id', 0, 'id', $options['slb_confirmation_page_id'] ) .'
							<p class="description" id="slb_confirmation_page_id-description">This is the page where Snappy List Builder will send subscribers to confirm their subscriptions. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_confirm_subscription]</strong>.</p>
						</td>
					</tr>
					
			
					<tr>
						<th scope="row"><label for="slb_reward_page_id">Download Reward Page</label></th>
						<td>
							'. slb_get_page_select( 'slb_reward_page_id', 'slb_reward_page_id', 0, 'id', $options['slb_reward_page_id'] ) .'
							<p class="description" id="slb_reward_page_id-description">This is the page where Snappy List Builder will send subscribers to retrieve their reward downloads. <br />
								IMPORTANT: In order to work, the page you select must contain the shortcode: <strong>[slb_download_reward]</strong>.</p>
						</td>
					</tr>
			
					<tr>
						<th scope="row"><label for="slb_default_email_footer">Email Footer</label></th>
						<td>');
						
							
							// wp_editor will act funny if it's stored in a string so we run it like this...
							wp_editor( $options['slb_default_email_footer'], 'slb_default_email_footer', array( 'textarea_rows'=>8 ) );
							
							
							echo('<p class="description" id="slb_default_email_footer-description">The default text that appears at the end of emails generated by this plugin.</p>
						</td>
					</tr>
			
					<tr>
						<th scope="row"><label for="slb_download_limit">Reward Download Limit</label></th>
						<td>
							<input type="number" name="slb_download_limit" value="'. $options['slb_download_limit'] .'" class="" />
							<p class="description" id="slb_download_limit-description">The amount of downloads a reward link will allow before expiring.</p>
						</td>
					</tr>
			
				</tbody>
				
			</table>');
		
			// outputs the WP submit button html
			@submit_button();
		
		
		echo('</form>
	
	</div>');
	
}




/* !9. SETTINGS */

// 9.1
// hint: registers all our plugin options
function slb_register_options() {
	// plugin options
	register_setting('slb_plugin_options', 'slb_manage_subscription_page_id');
	register_setting('slb_plugin_options', 'slb_confirmation_page_id');
	register_setting('slb_plugin_options', 'slb_reward_page_id');
	register_setting('slb_plugin_options', 'slb_default_email_footer');
	register_setting('slb_plugin_options', 'slb_download_limit');
}
