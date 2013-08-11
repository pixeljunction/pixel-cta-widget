<?php

/*
* This file declares the metaboxes to add to the post edit screens.
* and initialises the metabox class to load them into wp
*
* To learn all the available options for metaboxes, please see the
* example-functions.php file in pxjn/metaboxes folder
*/

/***************************************************************
* Function pxlcta_metabox()
* Defines the additional metaboxes you want to add.
***************************************************************/
function pxlcta_metabox( $meta_boxes ) {
	
	/* repeat this for each metabox you require */
	$meta_boxes[] = array(
		'id' => 'pxjn-page-cta-information',
		'title' => 'Call to Action Information',
		'pages' => apply_filters( 'pxlcta_metabox_post_types', pxlcta_get_post_types() ), // post type
		'context' => 'normal',
		'priority' => 'default',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => 'Call to Action Image',
				'desc' => 'Upload an image to use for any call to actions for this page. An aspect ratio of 16:10 works well.',
				'id' => 'pxlcta_image',
				'type' => 'file',
				'save_id' => true, // save ID using true
				'allow' => array( 'url', 'attachment' ) // limit to just attachments with array( 'attachment' )
			),
		),
	);

	return $meta_boxes;
}

add_filter( 'pxlcta_meta_boxes', 'pxlcta_metabox' );

/***************************************************************
* Function pxlcta_initialize_meta_boxes()
* Initialises the metaboxes class and adds it to wp ready to
* load in your metaboxes.
***************************************************************/
function pxlcta_initialize_meta_boxes() {
	
	/* check whether metabox class does not already exist */
	if ( !class_exists( 'pxlcta_Meta_Box' ) ) {
		
		/* load the metaboxes init file */
		require_once dirname( __FILE__ ) . '/metaboxes/init.php';
	
	}
	
}

add_action( 'init', 'pxlcta_initialize_meta_boxes', 9999 );