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
			    <div class="contentheader"><h2><?php the_title(); ?></h2></div>
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<div class="post" id="post-<?php the_ID(); ?>">
					<div class="entry">
						<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>

						<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

					</div>
				</div>
				
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'lgsquared' ) . '</span> %title' ); ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'lgsquared' ) . '</span>' ); ?></div>
				</div><!-- #nav-below -->
				
				<?php endwhile; endif; ?>
            </div><!-- #content -->
		    <?php get_sidebar('archive'); ?>
		    <?php get_footer(); ?>
		</div><!-- #container -->
