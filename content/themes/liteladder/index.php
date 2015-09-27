<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package liteladder
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<div class="container events">
			<div class="row">
				<div class="col-xs-12">
					<h5>Events</h5>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
					<div class="card ">
						<a href="#"><img class="card-img-top img-responsive" src="<?php echo get_template_directory_uri(); ?>/img/slheader.png" alt="Card image cap"></a>
						<div class="card-block clearfix">
							<p class="card-text"><a href="#">Starladder Long Tournament Name Super Pro 12</a></p>
							<a href="#" class="btn btn-sm btn-secondary-outline pull-left">Live</a>
							<a href="#" class="pull-right">$85'000</a>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
					<div class="card ">
						<a href="#"><img class="card-img-top img-responsive" src="<?php echo get_template_directory_uri(); ?>/img/slheader.png" alt="Card image cap"></a>
						<div class="card-block clearfix">
							<p class="card-text"><a href="#">Starladder Long Tournament Name Super Pro 12</a></p>
							<a href="#" class="btn btn-sm btn-secondary-outline pull-left">Live</a>
							<a href="#" class="pull-right">$85'000</a>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
					<div class="card ">
						<a href="#"><img class="card-img-top img-responsive" src="<?php echo get_template_directory_uri(); ?>/img/slheader.png" alt="Card image cap"></a>
						<div class="card-block clearfix">
							<p class="card-text"><a href="#">Starladder Long Tournament Name Super Pro 12</a></p>
							<a href="#" class="btn btn-sm btn-secondary-outline pull-left">Live</a>
							<a href="#" class="pull-right">$85'000</a>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
					<div class="card ">
						<a href="#"><img class="card-img-top img-responsive" src="<?php echo get_template_directory_uri(); ?>/img/slheader.png" alt="Card image cap"></a>
						<div class="card-block clearfix">
							<p class="card-text"><a href="#">Starladder Long Tournament Name Super Pro 12</a></p>
							<a href="#" class="btn btn-sm btn-secondary-outline pull-left">Live</a>
							<a href="#" class="pull-right">$85'000</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-6 news">
					<h5>News</h5>

					<div class="card ">
						<a href="#"><img class="card-img-top img-responsive" src="<?php echo get_template_directory_uri(); ?>/img/slheader.png" alt="Card image cap"></a>
						<div class="card-block clearfix">
							<h2 class="card-text"><a href="#">Virtus.Pro проходят дальше по нижней сетке</a></h2>
							<p class="card-text">Волумюч аппарэат ючю но, хёнк мальорум факилиз ад жят, йн агам чонэт мэль. Жят экз квюиж жюмо, пауло дыкоры квюоджё нык экз, квуй кхоро дольорэ йн. Зальы тебиквюэ мыдиокрым эи зыд.</p>
						</div>
						<div class="card-block hp-items-list--item clearfix">
							<a href="#"><img class="pull-left " src="<?php echo get_template_directory_uri(); ?>/img/slheader.png" alt="Card image cap"></a>
							<div class="card-block pull-right">
								<h2 class="card-text"><a href="#">Virtus.Pro проходят дальше по нижней сетке</a></h2>
								<p class="card-text">Волумюч аппарэат ючю но, хёнк мальорум факилиз ад жят, йн агам чонэт мэль. Жят экз квюиж жюмо, пауло дыкоры квюоджё нык экз, квуй кхоро дольорэ йн. Зальы тебиквюэ мыдиокрым эи зыд.</p>
							</div>
						</div>
					</div>

				</div>
				<div class="col-xs-12 col-sm-12 col-md-6 tournaments">
					<?php if(has_action('show_tournaments')) { ?>
					<h5>Tournaments</h5>
					<?php do_action('show_tournaments'); } ?>
				</div>
			</div>
		</div>
		<?php if ( have_posts() ) : ?>
		<div class="container events">
			
			<?php if ( is_home() && ! is_front_page()  ) : ?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>
			<?php endif; ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
			<div class="col-lg-12">		
				<?php

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content', get_post_format() );
				?>
			</div>	
			<?php endwhile; ?>

			<?php the_posts_navigation(); ?>
			
		</div>	
		<?php else : ?>

			<?php // get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		
		</main><!-- #main -->
		
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
