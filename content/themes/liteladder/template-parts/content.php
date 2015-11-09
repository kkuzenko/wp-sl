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
<?php $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>
<article class="card-block top-news-item clearfix" style="background-image: url(<?php echo $url; ?>)">
	<div class="meta">
		<p class="card-text clearfix">
			<span class="pull-left"><span class="cat-name"><?php the_category( sprintf( '<a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' ); ?></span></span>
			<span class="pull-right"><span class="post-date">Сегодня</span></span>
		</p>
		<?php the_title( sprintf( '<h2 class="card-text entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	</div>
</article><!-- #post-## -->
<?php set_query_var('first', false); ?>

<?php else: ?>
<article class="card-block hp-items-list--item clearfix">
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="news-image" title="<?php the_title_attribute(); ?>">
			<?php the_post_thumbnail('lg-news-list-crop'); ?>
		</a>
	<?php endif; ?>
	<div class="card-text">
		<span class="pull-left"><span class="cat-name"><?php the_category( sprintf( '<a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' ); ?></span></span>
		<span class="pull-right"><span class="post-date">Сегодня</span></span><br>
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		<?php $excerpt = get_the_excerpt(); ?>
		<p class="news-excerpt"><?php echo $excerpt;?></p>
	</div>
</article><!-- #post-## -->
<?php endif; ?>

