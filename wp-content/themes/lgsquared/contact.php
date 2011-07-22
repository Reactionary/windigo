<?php
/**
 * @package Windigo
 * @subpackage LGSquared
 * @since 1.0
 */

 /*
 Template Name: Contact
 */

get_header(); ?>
        <div id="banner">
            <img src="<?php bloginfo('template_directory');?>/images/contact_banner.jpg" />
        </div>
		<div id="container">
			<div id="content" role="main">
			    <div class="contentheader"><h2>Contact Us</h2></div>
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	            	<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
	            <?php endwhile; endif; ?>
            </div><!-- #content -->
		    <?php get_sidebar('contact'); ?>
		    <?php get_footer(); ?>
		</div><!-- #container -->
