<?php

/*

Plugin Name: View Me

Plugin URI:

Description: This plugin to add vote in posts

Author: Abbas

Version: 0.1

Author URI:

*/



define('VIEWMESURL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );

define('VIEWMEPATH', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );



function viewme_enqueuescripts()

{

	wp_enqueue_script('viewme', VIEWMESURL.'/js/viewme.js', array('jquery'));

	wp_localize_script( 'viewme', 'viewmeajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}

add_action('wp_enqueue_scripts', viewme_enqueuescripts);









function viewme_getvotelink()

{

$viewmelink = "";



$post_ID = get_the_ID();

$viewmecount = get_post_meta($post_ID, '_viewmecount', true) != '' ? get_post_meta($post_ID, '_viewmecount', true) : '0';



$link = $viewmecount.' <a onclick="viewmeaddvote('.$post_ID.');">'.'View (by View Me)'.'</a>';



$viewmelink = '<div id="viewme-'.$post_ID.'">';

$viewmelink .= '<span>'.$link.'</span>';

$viewmelink .= '</div>';



return $viewmelink;

}



function viewme_printvotelink($content)

{

return $content.viewme_getvotelink();

}

//add_filter('the_content', viewme_printvotelink);







// function viewme_addvote()

// {

// 		$results = '';

// 		global $wpdb;

// 		$post_ID = $_POST['postid'];

// 		$viewmecount = get_post_meta($post_ID, '_viewmecount', true) != '' ? get_post_meta($post_ID, '_viewmecount', true) : '0';

// 		$viewmecountNew = $viewmecount + 1;

// 		update_post_meta($post_ID, '_viewmecount', $viewmecountNew);



// 		$results.='<div class="votescore" >'.$viewmecountNew.'</div>';



// 		// Return the String

// 		die($results);

// 	}





function viewme_viewvotestore()

{

		$results = '';

		global $wpdb;

		$post_ID = $_POST['postid'];

		$like = $_POST['vote'];

		$viewmecount = get_post_meta($post_ID, '_viewmecount', true) != '' ? get_post_meta($post_ID, '_viewmecount', true) : '0';

		$viewmecountNew = $viewmecount + 1;

		update_post_meta($post_ID, '_viewmecount', $viewmecountNew);


		if ( is_user_logged_in() ) {
			$user_id        = get_current_user_id();                            // Get our current user ID


			$user_meta = get_user_meta( get_current_user_id(), 'vvi', true);

			if ( $user_meta ) {
		    	$um_val    = $user_meta . ',' . sanitize_text_field( $_POST['postid'] );      // Sanitize our user meta value
		    } else {
		    	$um_val = sanitize_text_field( $_POST['postid'] );
		    }

		    

			update_user_meta( $user_id, 'vvi', $um_val );  
		}


		if ($like > 0) {

		    $com = '+1';

		    $data = array(

				'comment_post_ID' => $post_ID,

				'comment_author' => 'admin',

				'comment_author_email' => 'info@artssspot.com',

				'comment_author_url' => 'http://www.artssspot.com',

				'comment_content' => $com,

				'comment_author_IP' => '127.0.0.1',

				'comment_agent' => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; fr; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3',

				'comment_date' => date('Y-m-d H:i:s'),

				'comment_date_gmt' => date('Y-m-d H:i:s'),

				'comment_approved' => 1,

			);



			$comment_id = wp_insert_comment($data);

		}



		// Return the String

		die($results);

	}





function viewme_getposts()

{

		$results = '';

		global $wpdb;



		$blogposts = get_posts(array(

		    'numberposts'   => -1, // get all posts.

		    'fields'        => 'ids', // Only get post IDs

		));







$query = new WP_Query(array(

            'orderby'   => 'meta_value_num',

            'meta_key'  => '_viewmecount',

            'numberposts'   => -1, // get all posts.

            'meta_key' => 'visto',

        ));



$results = json_encode($query->get_posts());



		// Return the String

		die($results);



	}







function viewme_resetvote()

{

		global $wpdb;

		$viewmevotecountNew = 0;

		$blogposts = get_posts(array(

		    'numberposts'   => -1, // get all posts.

		    'fields'        => 'ids', // Only get post IDs

		));



	    foreach ( $blogposts as $post ) {           // Update our user meta

	        update_post_meta($post, '_viewmevotescount', $viewmevotecountNew); 

	    }



	}





function viewme_resetview()

{

		global $wpdb;

		$viewmecountNew = 1;

		$blogposts = get_posts(array(

		    'numberposts'   => -1, // get all posts.

		    'fields'        => 'ids', // Only get post IDs

		));



	    foreach ( $blogposts as $post ) {           // Update our user meta

	        update_post_meta($post, '_viewmecount', $viewmecountNew); 

	    }

	}





function viewme_usersave() 

{

		global $wpdb;



		$post_view_string = $_POST['vvi'];

		$posts_viewed = explode(",", $post_view_string);



		$post_vote_string = $_POST['vvo'];

		$posts_voted = explode(",", $post_vote_string);



	    if( ! isset( $_POST ) || empty( $_POST ) || ! is_user_logged_in() ) {



	        // If we don't - return custom error message and exit

	        header( 'HTTP/1.1 400 Empty POST Values' );

	        echo 'Could Not Verify POST Values.';

	        exit;

	    }

	    $user_id        = get_current_user_id();                            // Get our current user ID

    	$um_val1         = sanitize_text_field( $post_view_string );

    	update_user_meta( $user_id , 'vvi', $um_val1 );                            // Get our current user ID

    	$um_val2         = sanitize_text_field( $post_vote_string );

    	update_user_meta( $user_id , 'vvo', $um_val2 );  

	}





function viewme_save() 

{

		global $wpdb;



		$post_view_string = $_POST['vvi'];

		$posts_viewed = explode(",", $post_view_string);



		$post_vote_string = $_POST['vvo'];

		$posts_voted = explode(",", $post_vote_string);



		$user_meta = get_user_meta( get_current_user_id(), 'vvi', true);



		if ($user_meta == 'done') {

			die($user_meta);

		} else {

	        foreach ( $posts_voted as $post ) {   

				$post_ID = $post;

				$viewmevotecount = get_post_meta($post_ID, '_viewmevotescount', true) != '' ? get_post_meta($post_ID, '_viewmevotescount', true) : '0';

				$viewmevotecountNew = $viewmevotecount + 1;

				update_post_meta($post_ID, '_viewmevotescount', $viewmevotecountNew); 

		    }



	        foreach ( $posts_viewed as $post ) {   

				$post_ID = $post;

				$viewmecount = get_post_meta($post_ID, '_viewmecount', true) != '' ? get_post_meta($post_ID, '_viewmecount', true) : '0';

				$viewmecountNew = $viewmecount + 1;

				update_post_meta($post_ID, '_viewmecount', $viewmecountNew); 

		    }



		    if( ! isset( $_POST ) || empty( $_POST ) || ! is_user_logged_in() ) {



		        // If we don't - return custom error message and exit

		        header( 'HTTP/1.1 400 Empty POST Values' );

		        echo 'Could Not Verify POST Values.';

		        exit;

		    }

		    $user_id         = get_current_user_id();                            // Get our current user ID

    	update_user_meta( $user_id , 'vvi', 'done' );                            // Get our current user ID

    	update_user_meta( $user_id , 'vvo', 'done' );  

    	}

	}



		// creating Ajax call for WordPress

		// add_action( 'wp_ajax_nopriv_viewme_addvote', 'viewme_addvote' );

		// add_action( 'wp_ajax_viewme_addvote', 'viewme_addvote' );



		add_action( 'wp_ajax_nopriv_viewme_resetvote', 'viewme_resetvote' );

		add_action( 'wp_ajax_viewme_resetvote', 'viewme_resetvote' );



		add_action( 'wp_ajax_nopriv_viewme_resetview', 'viewme_resetview' );

		add_action( 'wp_ajax_viewme_resetview', 'viewme_resetview' );



		// add_action( 'wp_ajax_nopriv_viewme_voteviewstore', 'viewme_voteviewstore' );

		// add_action( 'wp_ajax_viewme_voteviewstore', 'viewme_voteviewstore' );



		add_action( 'wp_ajax_nopriv_viewme_usersave', 'viewme_usersave' );

		add_action( 'wp_ajax_viewme_usersave', 'viewme_usersave' );



		add_action( 'wp_ajax_nopriv_viewme_save', 'viewme_save' );

		add_action( 'wp_ajax_viewme_save', 'viewme_save' );



		add_action( 'wp_ajax_nopriv_viewme_getposts', 'viewme_getposts' );

		add_action( 'wp_ajax_viewme_getposts', 'viewme_getposts' );




		add_action( 'wp_ajax_nopriv_viewme_viewvotestore', 'viewme_viewvotestore' );

		add_action( 'wp_ajax_viewme_viewvotestore', 'viewme_viewvotestore' );







// admin display



add_filter( 'manage_edit-post_columns', 'viewme_extra_post_columns' );

add_filter( 'manage_edit-post_columns', 'viewmevotes_extra_post_columns' );



function viewme_extra_post_columns( $columns ) {

$columns[ 'viewmecount' ] = __( 'Views' );

return $columns;

}	



function viewmevotes_extra_post_columns( $columns ) {

$columns[ 'viewmevotescount' ] = __( 'Votes' );

return $columns;

}	





function viewme_post_column_row( $column ) {

	if ( $column != 'viewmecount' )

	return;



	global $post;

	$post_id = $post->ID;

	$viewmecount = get_post_meta($post_id, '_viewmecount', true) != '' ? get_post_meta($post_id, '_viewmecount', true) : '1';

	echo $viewmecount;



}



function viewmevotes_post_column_row( $column ) {

	if ( $column != 'viewmevotescount' )

	return;



	global $post;

	$post_id = $post->ID;

	$viewmevotescount = get_post_meta($post_id, '_viewmevotescount', true) != '' ? get_post_meta($post_id, '_viewmevotescount', true) : '0';

	echo $viewmevotescount;



}



add_action( 'manage_posts_custom_column', 'viewme_post_column_row', 10, 2 );	

add_action( 'manage_posts_custom_column', 'viewmevotes_post_column_row', 10, 2 );	







?>