<?php
/*
Theme Name: LGSquared
Theme URI: http://wordpress.org/
Description: Universal Template for The Richest Guys projects
Author: Jarad Comingdeer
Version: 1.0
*/

get_header(); ?>
        <div id="banner">
            <link rel="stylesheet" href="<?php bloginfo('template_directory');?>/slider/nivo-slider.css" type="text/css" media="screen" />
      <script src="<?php bloginfo('template_directory');?>/slider/jquery.nivo.slider.pack.js" type="text/javascript"></script>
      
      <div id="slider" class="nivoSlider">
        <a href="http://windigosurfboards.com/?page_id=63"><img src="images/banners/Winner_CincoDeMustache.jpg" alt="Cinco De Mustache Winner" /></a>
        <a href="http://store.windigosurfboards.com/product/cash"><img src="images/banners/Banner5.jpg" alt="The Cash - Nathan Colwell" /></a>
      </div>
      
      <script type="text/javascript">
      jQuery(window).load(function() {
        jQuery('#slider').nivoSlider({
          effect: '<?php echo get_option('mytheme_effect'); ?>',
          animSpeed:<?php echo get_option('mytheme_animSpeed'); ?>,
          pauseTime:<?php echo get_option('mytheme_pauseTime'); ?>,
          startSlide:<?php echo get_option('mytheme_startSlide'); ?>,
          slices:<?php echo get_option('mytheme_slices'); ?>,
          directionNav:<?php echo get_option('mytheme_directionNav'); ?>,
          directionNavHide:<?php echo get_option('mytheme_directionNavHide'); ?>,
          controlNav:<?php echo get_option('mytheme_controlNav'); ?>,
          controlNavThumbs:<?php echo get_option('mytheme_controlNavThumbs'); ?>,
          keyboardNav:<?php echo get_option('mytheme_keyboardNav'); ?>,
          pauseOnHover:<?php echo get_option('mytheme_pauseOnHover'); ?>,
          captionOpacity:<?php echo get_option('mytheme_captionOpacity'); ?>
        });
      });
      </script>
        </div>
    <div id="container">
      <div id="content" role="main">
          <div class="contentheader"><h2>Latest News</h2><!-- <a href="#">Full Blog</a> --></div>
                <div id="blogposts">
                    <?php
                    /* Run the loop to output the posts.
                     * If you want to overload this in a child theme then include a file
                     * called loop-index.php and that will be used instead.
                     */
                     get_template_part( 'loop', 'index' );
                    ?>
                </div>
            </div><!-- #content -->
        <?php get_sidebar('homepage'); ?>
        <?php get_footer(); ?>
    </div><!-- #container -->
