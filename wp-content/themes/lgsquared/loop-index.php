<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package Windigo
 * @subpackage LGSquared
 * @since 1.0
 */
?>

<!-- Start the Loop. -->
<?php 
$wp_query = new WP_Query('posts_per_page=3');
if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<div class="post">

<!-- Display the Title as a link to the Post's permalink. -->
<h2><?php the_title(); ?></h2>
<!-- <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2> -->

<!-- Display the date (November 16th, 2009 format) and a link to other posts by this posts author. -->
<small><?php the_time('F jS, Y') ?> by <?php the_author_posts_link() ?></small>

<!-- Display the Post's Content in a div box. -->
<div class="entry">
  <?php the_content(); ?>
</div>

<!-- Display a comma separated list of the Post's Categories. -->
<p class="postmetadata">Posted in <?php the_category(', '); ?></p>
</div> <!-- closes the first div box -->

<!-- Stop The Loop (but note the "else:" - see next line). -->
<?php endwhile; else: ?>

<!-- The very first "if" tested to see if there were any Posts to -->
<!-- display.  This "else" part tells what do if there weren't any. -->
<p>Sorry, no posts matched your criteria.</p>

<!-- REALLY stop The Loop. -->
<?php endif; 
?>
