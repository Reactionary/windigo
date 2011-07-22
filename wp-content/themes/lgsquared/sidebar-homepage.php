<?php
/**
 * @package Windigo
 * @subpackage LGSquared
 * @since 1.0
 */
?>

		<div id="sidebar" class="widget-area" role="complementary">
			<ul class="xoxo">

<?php
	/* When we call the dynamic_sidebar() function, it'll spit out
	 * the widgets for that widget area. If it instead returns false,
	 * then the sidebar simply doesn't exist, so we'll hard-code in
	 * some default sidebar stuff just in case.
	 */
	        if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('HomepageSidebar') ) : ?>
		    <?php endif; // end primary widget area ?>
		        <li class="sidebar-banner">
		            <a href="http://windigointernational.com/?page_id=15"><img src="<?php bloginfo('template_directory');?>/images/parlor_banner.jpg" /></a>
		        </li>
		        <li class="sidebar-banner">
		            <a href="http://windigointernational.com/?page_id=340"><img src="<?php bloginfo('template_directory');?>/images/transworld_banner.jpg" /></a>
		        </li>
		        <li class="sidebar-banner">
		            <a href="http://store.windigointernational.com/products"><img src="<?php bloginfo('template_directory');?>/images/apparel_banner.jpg" /></a>
		        </li>
		        <li class="sidebar-banner">
		            <a href="http://hoodratding.com"><img src="<?php bloginfo('template_directory');?>/images/hoodrat_banner.jpg" /></a>
		        </li>
			</ul>
		</div><!-- #primary .widget-area -->
