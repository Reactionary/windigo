<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package Windigo
 * @subpackage LGSquared
 * @since 1.0
 */
?>
	</div><!-- #main -->
	<div id="footer" role="contentinfo">
        <div id="site-info">
            <img class="forklogo" src="<?php bloginfo('template_directory');?>/images/footer_fork.jpg" />
            <a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                &copy; &nbsp; <?php bloginfo( 'name' ); ?>. &nbsp; All Rights Reserved.
            </a>
        </div><!-- #site-info -->
    </div><!-- #footer -->

</div><!-- #wrapper -->

<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>

	<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
		var pageTracker = _gat._getTracker("UA-11061971-1");
		pageTracker._trackPageview();
		} catch(err) {}
	</script>
</body>
</html>
