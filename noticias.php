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
            'id' => 'news_id',
        ),$atts);

        $out = '<div id="'.$params['id'].'" class="flex flex-wrap mt-20 mb-5">';
            $out .='<div class="md:basis-4/12 px-1 pb-4">';
                $out .='<div class="mx-11 sm:ml-20 md:ml-22 lg:ml-36 sm:mr-0">
                    <div class="text-primary text-3xl font-bold mb-2">Noticias</div>
                    <div class="text-secondary text-base">Conozca el quehacer y la actualidad de Aguas de La Habana</div>
                    <a href="/noticias" target="_blank"><div class="max-w-fit mt-4 px-2 py-1 rounded-md bg-gray-50 cursor-pointer dark:bg-transparent ring-1 ring-gray-700 hover:bg-secondary hover:text-white">Más Noticias</div></a>
                </div>';
            $out .='</div>';
            $out .='<div class="md:basis-8/12 grid lg:grid-cols-3 sm:grid-cols-2 grid-cols-1- gap-5 px-11 md:pl-0">';

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
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $date = get_the_date( 'd').' de '.
                $meses[get_the_date(get_the_date('n')-1)].
                get_the_date( ', Y');
            $img_url = get_the_post_thumbnail_url();
            $subtitle = get_the_content();
            
            $out .='<div class="border-0 rounded-md shadow-custom bg-white"><a href="'.$url.'">';
            $out .='<div class="py-2 px-4 text-secondary text-sm">'.$date.'</div>';
            $out .='<img src="'.$img_url.'" alt="'.$title.' image" class="text-center text-sm text-secondary/50">';
            $out .='<div class="p-4 pb-11"><div class="text-primary text-md font-bold pb-4">'.$title.'</div>';
            $out .='<div class="text-secondary text-sm text-ellipsis overflow-hidden">'.$subtitle.'</div></div>';
            $out .='</a></div>';

        endwhile;
        wp_reset_query();

        $out.= '</div></div>';

        return $out;
    }add_shortcode( 'show_news', 'show_news_grid' ); 


    /**
     * Fetch RSS
     */
    function fetch_rss_and_post_1 (){
        
        if(function_exists('fetch_feed')) {
 
            include_once(ABSPATH.WPINC.'/feed.php');
            $feed = fetch_feed('http://localhost/podcast.xml');
         
            $limit = $feed->get_item_quantity(6); // specify number of items
            $items = $feed->get_items(0, $limit); // create an array of items
         
        }
        if ($limit !== 0) {
            foreach ($items as $item) {
                echo $item;
                $noticia_post = array(
                    'post_title'    => $item->get_title(),
                    'post_content'  => $item->get_description(),
                    'post_status'   => "publish",
                    'post_type' => "noticias",
                );
                $post_id = wp_insert_post($noticia_post, true);
                if(false/*$post_id !== 0*/){
                    $image_url =/* isset($event['cover']['source']) ? $event['cover']['source'] :*/ ''; // Define the image URL here
                    $image_name  = '';
                    if (strpos($image_url, '?') !== false) {
                        $t = explode('?',$image_url);
                        $image_name = pathinfo(basename($t[0]))['extension'];            
                    }
                    $upload_dir = wp_upload_dir(); // Set upload folder
                    $image_data = file_get_contents($image_url); // Get image data
                    $unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
                    $filename = basename( $unique_file_name );// Create image file name
                    // Check folder permission and define file location
                    if( wp_mkdir_p( $upload_dir['path'] ) ) {
                        $file = $upload_dir['path'] . '/' . $filename;
                    } else {
                        $file = $upload_dir['basedir'] . '/' . $filename;
                    }
                    // Create the image  file on the server
                    file_put_contents( $file, $image_data );
                    // Check image file type
                    $wp_filetype = wp_check_filetype( $filename, null );
                    // Set attachment data
                    $attachment = array(
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title'     => sanitize_file_name( $filename ),
                        'post_content'   => '',
                        'post_status'    => 'inherit'
                    );
                    // Create the attachment
                    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
                    // Include image.php
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    // Define attachment metadata
                    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                    // Assign metadata to attachment
                    wp_update_attachment_metadata( $attach_id, $attach_data );
                    // And finally assign featured image to post
                    set_post_thumbnail( $post_id, $attach_id );
                }
            }
        }

    }add_shortcode('fetch_rss', 'fetch_rss_and_post_1');


}


function noticias_enqueue_scripts(){
    wp_register_style( 'custom_style', plugins_url('/inc/style.min.css', __FILE__), false, '1.0.0', 'all');
    wp_enqueue_style( 'custom_style' );
}add_action( 'wp_enqueue_scripts', 'noticias_enqueue_scripts' );
