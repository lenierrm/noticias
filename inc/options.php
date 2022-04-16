<?php 
/**
 * register noticias_settings_init to the admin_init action hook
 */
function noticias_settings_init(){
    // register the settings for "reading" page
    register_setting('noticias', 'noticias_page_id');

    // register a new section in the "reading" page
    add_settings_section(
        'noticias_settings_section',
        __('"Noticias" Settings Section','noticias'),
        'noticias_settings_section_description',
        'noticias'
    );

    // register the fields in the "rss_source_settings_section" section, inside the "reading" page
    add_settings_field(
        'rss_source_settings_field',
        __('RSS Source url','noticias'),
        'noticias_settings_field_rss_source',
        'noticias',
        'noticias_settings_section'
    );

}add_action('admin_init', 'noticias_settings_init');


/**
 * callback functions
 */
// section content Description
function noticias_settings_section_description(){
    echo '<p>'.__('Please insert the RSS Source url to feetch the content', 'noticias').'</p>';
    echo '<p>'.__('To fetch RSS feeds, insert the url and press "Save Settings" button.').'</p>';
    echo '<p>'.__('To show an event splash use the shortcode [show_news] or [show_news id="your_custom_id"]').'</p>';
    
}


// field content rss_source
function noticias_settings_field_rss_source(){
    // get the value of the setting we've registered with register_setting()
    $option_value = get_option('noticias_page_id');
    // output the field
    ?>
    <input id="rss_source" type="text" name="noticias_page_id" value="<?= isset($option_value) ? esc_attr($option_value) : ''; ?>">
    <?php    
}


/**
 * top level menu (Plugin Option Menu)
 */
function noticias_options_page() {
    // add top level menu page
    add_menu_page(
    __('News Options', 'noticias'),
    __('News Options', 'noticias'),
    'manage_options',
    'noticias',
    'noticias_options_page_html'
    );
}add_action( 'admin_menu', 'noticias_options_page' );


function noticias_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // add error/update messages
    
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'noticias_messages', 'noticias_message', __( 'Settings Saved', 'noticias' ), 'updated' ); 
        
        //Run rss feed fetch shortcode
        echo do_shortcode('[fetch_rss]');      
    }

    // show error/update messages
    settings_errors( 'noticias_messages' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "wporg"
            settings_fields( 'noticias' );
            // output setting sections and their fields
            // (sections are registered for "wporg", each field is registered to a specific section)
            do_settings_sections( 'noticias' );
            // output save settings button
            submit_button( __('Save Settings', 'noticias') );
            ?>
        </form>
    </div>
    <?php
}