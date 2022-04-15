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
     * Show news
     */
    function show_news_grid( $atts ) {
        $params=shortcode_atts(array(
            'id'        => 'news_id',
        ),$atts);

        $out ='<div id="'.$params['id'].'">';
        $out ='News';
        $out.= '</div>';
        return $out;
    }add_shortcode( 'show_news', 'show_news_grid' ); 


}
