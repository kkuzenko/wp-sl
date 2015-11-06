<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package liteladder
 */
?>
<?php  $first = get_query_var( 'first' );  ?>
<?php if ($first) : ?>
<?php if ( has_post_thumbnail() ) : ?>
	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
		<?php the_post_thumbnail('full', array('class' => 'card-img-top img-responsive')); ?>
	</a>
<?php endif; ?>
<article class="card-block clearfix">
	<?php the_title( sprintf( '<h2 class="card-text entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	<div class="card-text"><?php the_excerpt(); ?></div>
</article><!-- #post-## -->
<?php set_query_var('first', false); ?>

<?php else: ?>
<article class="card-block hp-items-list--item clearfix">
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="news-image" title="<?php the_title_attribute(); ?>">
			<?php the_post_thumbnail(); ?>
		</a>
	<?php endif; ?>
	<?php the_category( sprintf( '<p class="card-text"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></p>' ); ?>
	<?php the_title( sprintf( '<h2 class="card-text entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	<div class="card-text"><?php the_excerpt(); ?></div>
</article><!-- #post-## -->
<?php endif; ?>

