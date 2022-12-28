<?php
/**
* Plugin Name: ibl-POST-endpoint
* Description: Test.
* Version: 0.1
* Author: Ronald Lussier
**/
add_action('rest_api_init', 'ibl_2_url_endpoint');

function ibl_2_url_endpoint(){
    register_rest_route(
        'ibl/v1/', //Namespace
        'receive-callback', //Endpoint
        array(
            'methods' => 'POST',
            'callback' => 'ibl_2_receive_callback'
        )
        );
}

//$request_data object is from the WP_REQUEST class
function ibl_2_receive_callback($request_data) {
    $data = array();

    $parameters = $request_data->get_params();

    $message = $parameters['message'];

    if (isset($message)) {

        $data['status'] = 'OK';

        $data['received_data'] = array(
        'name' => $message
    );

    $data['message'] = 'You have reached the server';    
    } else {
        $data['status'] = 'Failed';
        $data['message'] = 'Parameters Missing!';

    }

    $my_post = array(
    'post_title'    => 'My post',
    'post_content'  => $data['received_data']['name'],
    'post_status'   => 'draft',
    'post_author'   => 1,
); 

    // Insert the post into the database
    wp_insert_post( $my_post );

    return $data;
}

add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
  
function my_custom_dashboard_widgets() {
global $wp_meta_boxes;
 
wp_add_dashboard_widget('custom_help_widget', 'Theme Support', 'custom_dashboard_help');
}
 
function custom_dashboard_help() {
echo '<p>Welcome to Custom Blog Theme! Need help? Contact the developer <a href="mailto:yourusername@gmail.com">here</a>. For WordPress Tutorials visit: <a href="https://www.wpbeginner.com" target="_blank">WPBeginner</a></p>';
$the_query = new WP_Query( array( 'post_type' => 'post','posts_per_page' => -1 ) );

// The Loop
if ( $the_query->have_posts() ) {
    echo '<ul>';
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
        echo '<li>' . the_content() . '</li>';
    }
    echo '</ul>';
    /* Restore original Post Data */
    wp_reset_postdata();
} else {
    // no posts found
}
}

function my_cors_headers() {
    header('Access-Control-Allow-Origin: *');
}
add_action('rest_pre_serve_request', 'my_cors_headers');


