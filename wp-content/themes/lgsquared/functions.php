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
	
	if ( function_exists( 'add_theme_support' ) ) { 
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 150, 150, true ); // default Post Thumbnail dimensions (cropped)
		// additional image sizes
		add_image_size( 'homepage-thumb', 560, 120, true); //unlimited width, 120 pixels high and hard crop mode
	}
	
	if ( function_exists('register_sidebar') ) {
		register_sidebar(array(
			'name'=>'Homepage Sidebar',
			'description' => 'Widgets for the homepage sidebar.'
		));
		register_sidebar(array(
			'name'=>'Archive Sidebar',
			'description' => 'Widgets for the homepage sidebar.'
		));
		register_sidebar(array(
			'name'=>'Contact Sidebar',
			'description' => 'Widgets for the contact area sidebar.'
		));
	}
	
	if ( ! function_exists( 'lgs_posted_on' ) ) :
		function lgs_posted_on() {
			printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'lgsquared' ),
				'meta-prep meta-prep-author',
				sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
					get_permalink(),
					esc_attr( get_the_time() ),
					get_the_date()
				),
				sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
					get_author_posts_url( get_the_author_meta( 'ID' ) ),
					sprintf( esc_attr__( 'View all posts by %s', 'lgsquared' ), get_the_author() ),
					get_the_author()
				)
			);
		}
	endif;
?>