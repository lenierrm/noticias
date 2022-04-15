<?php
/*
*Plugin Name: Noticias
*Description: This plugin allow to create news from rss
*Author: Lenier
*Version: 1.0
*Text Domain: noticias
*Domain Path: /languages
*/
require_once plugin_dir_path( __FILE__ ) . 'inc/options.php';

add_action('init', 'noticias_init');

function noticias_init(){

    /**
    * Register "noticias" Post Type
    */
    $labels = array(
        'name'               => _x( 'News', 'post type general name', 'noticias' ),
        'singular_name'      => _x( 'New', 'post type singular name', 'noticias' ),
        'menu_name'          => _x( 'News', 'admin menu', 'noticias' ),
        'name_admin_bar'     => _x( 'New', 'add new on admin bar', 'noticias' ),
        'add_new'            => _x( 'Add New', 'Event', 'noticias' ),
        'add_new_item'       => __( 'Add New "New"', 'noticias' ),
        'new_item'           => __( 'New "New"', 'noticias' ),
        'edit_item'          => __( 'Edit "New"', 'noticias' ),
        'view_item'          => __( 'View "New"', 'noticias' ),
        'all_items'          => __( 'All News', 'noticias' ),
        'search_items'       => __( 'Search News', 'noticias' ),
        'parent_item_colon'  => __( 'Parent News:', 'noticias' ),
        'not_found'          => __( 'No News found.', 'noticias' ),
        'not_found_in_trash' => __( 'No News found in Trash.', 'noticias' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'noticias' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'noticias' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'thumbnail')
    );
    register_post_type( 'noticias', $args );

    /**
     * Show news
     */
    function show_news_grid( $atts ) {
        $params=shortcode_atts(array(
            'id'        => 'news_id',
        ),$atts);

        $out ='<div id="'.$params['id'].'">';

        $args = [
            'posts_per_page' => 6,
            'post_type'      => 'noticias',
            'fields'         => 'ids',
            'orderby'           => 'date',
            'order'             => 'ASC'
        ];

        $news = new WP_Query($args);
        while($news->have_posts()):$news->the_post();
            $title = get_the_title();
            $url = get_the_permalink();
            $img_url = get_the_post_thumbnail_url();
            $subtitle = get_the_content();
            
            $out .='<div><a href="'.$url.'">';
            $out .='<img src="'.$img_url.'" alt="'.$title.' image">';
            $out .='<div>'.$title.'</div>';
            $out .='<div>'.$subtitle.'</div>';
            $out .='</a></div>';

        endwhile;
        wp_reset_query();

        $out.= '</div>';
        return $out;
    }add_shortcode( 'show_news', 'show_news_grid' ); 



}


function noticias_enqueue_scripts(){
    wp_register_style( 'custom_style', plugins_url('/inc/style.min.css', __FILE__), false, '1.0.0', 'all');
    wp_enqueue_style( 'custom_style' );
}add_action( 'wp_enqueue_scripts', 'noticias_enqueue_scripts' );

