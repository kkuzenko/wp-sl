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
                <?php if ( have_posts() ) : ?>
                <div class="col-xs-12 col-sm-12 col-md-6 news">
                    <h5>News</h5>
                    <div class="card ">
                    <?php set_query_var( 'first', true ); ?>
                    <?php while ( have_posts() ) : the_post(); ?>

                    <?php get_template_part( 'template-parts/content', get_post_format() ); ?>
                    <?php endwhile; ?>
                    <?php the_posts_navigation(); ?>
                    </div>
                </div>
                <?php else : ?>

                    <?php // get_template_part( 'template-parts/content', 'none' ); ?>

                <?php endif; ?>
                <div class="col-xs-12 col-sm-12 col-md-6 tournaments">
                    <?php if(has_action('show_tournaments')) { do_action('show_tournaments'); } ?>
                </div>
            </div>

        </div>



    </main><!-- #main -->

</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
