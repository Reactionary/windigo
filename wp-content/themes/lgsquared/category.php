<?php
/**
 * @package Windigo
 * @subpackage LGSquared
 * @since 1.0
 */

/*
Theme Name: LGSquared
Theme URI: http://wordpress.org/
Description: Universal Template for The Richest Guys projects
Author: Jarad Comingdeer
Version: 1.0
*/

get_header(); ?>
	<div id="banner">
		<img src="<?php bloginfo('template_directory');?>/images/contact_banner.jpg" />
  </div>
	<div id="container">
		<div id="content" role="main">
			<?php
				/* Queue the first post, that way we know
				 * what date we're dealing with (if that is the case).
				 *
				 * We reset this later so we can run the loop
				 * properly with a call to rewind_posts().
				 */
				if ( have_posts() )
					the_post();
			?>
			<div class="contentheader">
				<h2 class="page-title">
					<?php printf( __( 'Category: %s', 'lgsquared' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?>
				</h2>
			</div>

			<?php
				$category_description = category_description();
				if ( ! empty( $category_description ) )
					echo '<div class="archive-meta">' . $category_description . '</div>';

			/* Run the loop for the category page to output the posts.
			 * If you want to overload this in a child theme then include a file
			 * called loop-category.php and that will be used instead.
			 */
			get_template_part( 'loop', 'category' );
			?>
    </div><!-- #content -->
		<?php get_sidebar('archive'); ?>
		<?php get_footer(); ?>
	</div><!-- #container -->
	