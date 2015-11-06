<?php
	/* Template Name: DarkLadder Events */
?>
<?php get_header(); ?>
<?php
$custom_terms = get_terms('events');
foreach($custom_terms as $custom_term) {
	wp_reset_query();
	$args = array('post_type' => 'post',
	              'tax_query' => array(
		              array(
			              'taxonomy' => 'events',
			              'field' => 'slug',
			              'terms' => $custom_term->slug,
		              ),
	              ),
	);

	$loop = new WP_Query($args);
	if($loop->have_posts()) {
		echo '<h2>'.$custom_term->name.'</h2>';

		while($loop->have_posts()) : $loop->the_post();
			echo '<a href="'.get_permalink().'">'.get_the_title().'</a><br>';
		endwhile;
	}
}
?>
DARK
<?php get_sidebar(); ?>
<?php get_footer(); ?>