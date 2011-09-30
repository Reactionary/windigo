<?php
/**
 * @package Windigo
 * @subpackage LGSquared
 * @since 1.0
 */
?>

		<div id="sidebar" class="widget-area" role="complementary">
			<ul class="xoxo">
				<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Archive Sidebar') ) : ?>
		    <?php endif; // end primary widget area ?>
			</ul>
		</div><!-- #primary .widget-area -->
