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
$wp_query = new WP_Query('posts_per_page=10');
if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<a href="<?php the_permalink() ?>">
		<div class="postlink">
			<h2><?php the_title(); ?></h2>
			<small><?php the_time('F jS, Y') ?></small>
			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'homepage-thumb' ); } ?>
		</div>
	</a>

<!-- Stop The Loop (but note the "else:" - see next line). -->
<?php endwhile; else: ?>

<!-- The very first "if" tested to see if there were any Posts to -->
<!-- display.  This "else" part tells what do if there weren't any. -->
<p>Sorry, no posts matched your criteria.</p>

<!-- REALLY stop The Loop. -->
<?php endif; 
?>
