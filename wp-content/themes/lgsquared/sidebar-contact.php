<?php
/**
 * @package Windigo
 * @subpackage LGSquared
 * @since 1.0
 */
?>

		<div id="sidebar" class="widget-area" role="complementary">
			<ul class="xoxo">
				<li class="sidebar-banner">
		            <a href="http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=Windigo+Surfboards,+Via+Del+Norte,+Oceanside,+CA&aq=0&sll=33.214132,-117.352738&sspn=0.011723,0.022724&gl=us&g=392+Via+Del+Monte,+Oceanside,+San+Diego,+California+92058&ie=UTF8&hq=Windigo+Surfboards,&hnear=Via+Del+Norte,+Oceanside,+California&ll=33.214402,-117.352717&spn=0.011274,0.022724&z=16&iwloc=A"><img src="<?php bloginfo('template_directory');?>/images/windigo_map.jpg" /></a>
		        </li>
<?php
	/* When we call the dynamic_sidebar() function, it'll spit out
	 * the widgets for that widget area. If it instead returns false,
	 * then the sidebar simply doesn't exist, so we'll hard-code in
	 * some default sidebar stuff just in case.
	 */
	        	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('ContactSidebar') ) : ?>
		    	<?php endif; // end primary widget area ?>
			</ul>
		</div><!-- #primary .widget-area -->
