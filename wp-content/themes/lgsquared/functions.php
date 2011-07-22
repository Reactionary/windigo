<?php

	$functions_path = TEMPLATEPATH . '/functions/';
	
	//Theme Options
	require_once $functions_path . 'admin-options.php';

	// removes detailed login error information for security
	add_filter('login_errors',create_function('$a', "return null;"));
	
	// Removes Trackbacks from the comment cout
	add_filter('get_comments_number', 'comment_count', 0);
	function comment_count( $count ) {
		if ( ! is_admin() ) {
			global $id;
			$comments_by_type = &separate_comments(get_comments('status=approve&post_id=' . $id));
			return count($comments_by_type['comment']);
		} else {
			return $count;
		}
	}
	
	add_theme_support( 'post-thumbnails', array( 'page' ) );
	set_post_thumbnail_size( 131, 178 );
	
	if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'name'=>'HomepageSidebar',
		'description' => 'Widgets for the homepage sidebar.'
	));
	register_sidebar(array(
		'name'=>'ContactSidebar',
		'description' => 'Widgets for the contact area sidebar.'
	));
?>