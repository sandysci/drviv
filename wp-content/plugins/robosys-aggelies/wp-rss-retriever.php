<?php
/**
 * Plugin Name: CLASSIFIEDS Extractor
 * Description: A fast and easy to use plugin which uses shortcode to fetch and display an RSS feed.
 * Version: 0.0.1
 * Author: Robotic Systems
 */

add_action( 'wp_enqueue_scripts', 'robosys_aggelies_css');

function robosys_aggelies_css() {
    global $post;
    if( has_shortcode( $post->post_content, 'robosys_aggelies') ) {
        wp_enqueue_style('classifieds-extractor', plugin_dir_url( __FILE__) . 'inc/css/classifieds-extractor.css');
    }
}

add_shortcode( 'robosys_aggelies', 'robosys_aggelies_func' );

function robosys_aggelies_func( $atts, $content = null ){
	extract( shortcode_atts( array(
        //'url' => '#',
		'url' => 'http://www.kefaloniapress.gr/aggelies/classified',
		'count' => '10',
        'orderby' => 'default',
        'title' => 'true',
		'excerpt' => '0',
        'category' => 'true',
		'read_more' => 'true',
		'new_window' => 'true',
        'thumbnail' => 'false',
        'source' => 'true',
        'date' => 'true',
        'cache' => '43200'
	), $atts ) );

    update_option( 'wp_rss_cache', $cache );

    //multiple urls
    $urls = explode(',', $url);




    add_filter( 'wp_feed_cache_transient_lifetime', 'robosys_aggelies_cache' );

    $rss = fetch_feed( $urls );

    remove_filter( 'wp_feed_cache_transient_lifetime', 'robosys_aggelies_cache' );

    if ( ! is_wp_error( $rss ) ) :

        if ($orderby == 'date' || $orderby == 'date_reverse') {
            $rss->enable_order_by_date(true);
        }
        $maxitems = $rss->get_item_quantity( $count ); 
        $rss_items = $rss->get_items( 0, $maxitems );
        if ( $new_window != 'false' ) {
            $newWindowOutput = 'target="_blank" ';
        } else {
            $newWindowOutput = NULL;
        }

        if ($orderby == 'date_reverse') {
            $rss_items = array_reverse($rss_items);
        }

    endif;
    $output = '<div class="robosys_aggelies">';
        $output .= '<ul class="robosys_aggelies_list">';
            if ( !isset($maxitems) ) : 
                $output .= '<li>' . _e( 'No count', 'wp-classifieds-extractor' ) . '</li>';
            else : 
                //loop through each feed item and display each item.
                foreach ( $rss_items as $item ) :
                    //variables
                    $content = $item->get_content();
                    $the_title = $item->get_title();
                    $enclosure = $item->get_enclosure();
                    //$the_category = $item->get_cat_name($cat_ID);

                    //build output
                    $output .= '<li class="robosys_aggelies_item"><div class="robosys_aggelies_item_wrapper">';
                        //title
                        if ($title == 'true') {
                            $output .= '<a class="robosys_aggelies_title" ' . $newWindowOutput . 'href="' . esc_url( $item->get_permalink() ) . '"
                                title="' . $the_title . '">';
                                $output .= $the_title;    
                            $output .= '</a>';   
                        }


                        //category

                        // $categ = $product->get_categories();
                        // echo $categ;

                       /* if ($category == 'true') {
                            $output .= '<a class="robosys_aggelies_title" ' . $newWindowOutput . '
                                category="' . $the_category . '">';
                                $output .= $the_category;
                            $output .= '</a>';   
                        }
*/

                        //thumbnail
                        if ($thumbnail != 'false' && $enclosure) {
                            $thumbnail_image = $enclosure->get_thumbnail();                     
                            if ($thumbnail_image) {
                                //use thumbnail image if it exists
                                $resize = robosys_aggelies_resize_thumbnail($thumbnail);
                                $class = robosys_aggelies_get_image_class($thumbnail_image);
                                $output .= '<div class="robosys_aggelies_image"' . $resize . '><img' . $class . ' src="' . $thumbnail_image . '" alt="' . $title . '"></div>';
                            } else {
                                //if not than find and use first image in content
                                preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $first_image);
                                if ($first_image){    
                                    $resize = robosys_aggelies_resize_thumbnail($thumbnail);                                
                                    $class = robosys_aggelies_get_image_class($first_image["src"]);
                                    $output .= '<div class="robosys_aggelies_image"' . $resize . '><img' . $class . ' src="' . $first_image["src"] . '" alt="' . $title . '"></div>';
                                }
                            }
                        }
                        //content
                        $output .= '<div class="robosys_aggelies_container">';
                        if ( $excerpt != 'none' ) {
                            if ( $excerpt >= 0 ) {
                                $output .= esc_html(implode(' ', array_slice(explode(' ', strip_tags($content)), 0, $excerpt))) . "...";
                            } else {
                                $output .= $content;
                            }
                            if( $read_more == 'true' ) {
                                $output .= ' <a class="robosys_aggelies_readmore" ' . $newWindowOutput . 'href="' . esc_url( $item->get_permalink() ) . '"
                                        title="' . sprintf( __( 'Posted %s', 'wp-classifieds-extractor' ), $item->get_date('j F Y | g:i a') ) . '">';
                                        $output .= __( 'Read more &raquo;', 'wp-classifieds-extractor' );
                                $output .= '</a>';
                            }
                        }

                        //metadata
                        if ($source == 'true' || $date == 'true') {
                            $output .= '<div class="robosys_aggelies_metadata">';
                                $source_title = $item->get_feed()->get_title();
                                $time = $item->get_date('F j, Y - g:i a');
                                if ($source == 'true' && $source_title) {
                                    $output .= '<span class="robosys_aggelies_source">' . sprintf( __( 'Source: %s', 'wp-classifieds-extractor' ), $source_title ) . '</span>';
                                }
                                if ($source == 'true' && $date == 'true') {
                                    $output .= ' | ';
                                }
                                if ($date == 'true' && $time) {
                                    $output .= '<span class="robosys_aggelies_date">' . sprintf( __( 'Published: %s', 'wp-classifieds-extractor' ), $time ) . '</span>';
                                }
                            $output .= '</div>';
                        }
                    $output .= '</div></div></li>';
                endforeach;
            endif;
        $output .= '</ul>';
    $output .= '</div>';

    return $output;
}

add_option( 'wp_rss_cache', 43200 );

function robosys_aggelies_cache() {
    //change the default feed cache
    $cache = get_option( 'wp_rss_cache', 43200 );
    return $cache;
}

function robosys_aggelies_get_image_class($image_src) {
    list($width, $height) = getimagesize($image_src);
    if ($height > $width) {
        $class = ' class="portrait"';
    } else {
        $class = '';
    }
    return $class;
}

function robosys_aggelies_resize_thumbnail($thumbnail) {
    if (is_numeric($thumbnail)){
        $resize = ' style="width:' . $thumbnail . 'px; height:' . $thumbnail . 'px;"';
    } else {
        $resize = '';
    }
    return $resize;
}