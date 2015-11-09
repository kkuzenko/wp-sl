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

        <div class="container topEvents">
            <div class="row">
                <div class="col-xs-12">
                    <div id="sliderWrapper">

                        <div class="slide" style="background-image: url(<?php echo get_template_directory_uri(); ?>/img/sl_top_event.jpg);">
                            <div class="content">
                                <h2 class="title">
                                    Финал StarSeries
                                </h2>
                                <div class="content-info">
                                    <div class="dates">
                                        <i class="fa fa-calendar-o fa-fw"></i>
                                        <span class="prime">Январь</span>
                                        <span class="sub">19-21</span>
                                    </div>
                                    <div class="prize">
                                        <i class="fa fa-usd fa-fw"></i>
                                        <span class="prime">Призовой фонд</span>
                                        <span class="sub">550 000</span>
                                    </div>
                                    <div class="location">
                                        <i class="fa fa-map-marker fa-fw"></i>
                                        <span class="prime">Республика Беларусь, г.Минск</span>
                                        <span class="sub">Стадион &laquo;Арена&raquo;</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="slide">
                            <div class="content">
                                <h2 class="title">
                                    Финал StarSeries 2
                                </h2>
                                <div class="content-info">
                                    <div class="dates">
                                        <i class="fa fa-calendar-o fa-fw"></i>
                                        <span class="prime">Январь</span>
                                        <span class="sub">19-21</span>
                                    </div>
                                    <div class="prize">
                                        <i class="fa fa-usd fa-fw"></i>
                                        <span class="prime">Призовой фонд</span>
                                        <span class="sub">550 000</span>
                                    </div>
                                    <div class="location">
                                        <i class="fa fa-map-marker fa-fw"></i>
                                        <span class="prime">Республика Беларусь, г.Минск</span>
                                        <span class="sub">Стадион &laquo;Арена&raquo;</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <?php
            $events = get_terms( 'events' );
            if ( count($events) > 0 ) : ?>
            <div class="row events">
                <?php foreach ($events as $event) :
                $event_meta = json_decode(get_option('event_meta_'. $event->term_id),true);
                $event_url = get_term_link( $event );
                ?>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card ">
                        <a href="<?php echo $event_url; ?>"><img class="card-img-top img-responsive" src="<?php echo get_template_directory_uri(); ?>/img/slheader.png" alt="Card image cap"></a>
                        <div class="card-block clearfix">
                            <p class="card-text discipline"><a href="<?php echo $event_url; ?>"><?php echo $event_meta['discipline']; ?></a></p>
                            <p class="card-text event-name"><a href="<?php echo $event_url; ?>"><?php echo $event->name; ?></a></p>
                            <a href="<?php echo $event_url; ?>" class="btn btn-sm btn-secondary-outline pull-left event-status"><?php echo $event_meta['status']; ?></a>
                            <a href="<?php echo $event_url; ?>" class="pull-right event-prize"><?php echo format_prize( $event_meta['prize'] ); ?></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>


        <div class="container">
            <div class="row">
                <?php if ( have_posts() ) : ?>
                <div class="col-xs-12 col-sm-12 col-md-6 news" id="newsCol">

                    <div class="card ">
                    <?php set_query_var( 'first', true ); ?>
                    <?php while ( have_posts() ) : the_post(); ?>
                    <?php foreach(get_the_category() as $category) : ?>
                    <?php if ($category->cat_name != 'Uncategorized') : ?>
                        <?php get_template_part( 'template-parts/content', get_post_format() ); ?>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php endwhile; ?>

                    <a class="more-news" href="#">Читать больше новостей</a>
                    </div>

                </div>
                <?php else : ?>

                    <?php // get_template_part( 'template-parts/content', 'none' ); ?>

                <?php endif; ?>
                <div class="col-xs-12 col-sm-12 col-md-6" >
                    <div class="twitter-feed" id="twitterCol">

                    </div>
                </div>
            </div>

        </div>



    </main><!-- #main -->

</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
