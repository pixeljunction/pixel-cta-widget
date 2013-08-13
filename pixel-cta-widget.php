<?php
/*
Plugin Name: Pixel Call to Action Widget
Plugin URI: http://pixeljunction.co.uk
Description: A Call to Action Widget that allows you to pull in WordPress content (e.g. posts, pages) and show as a call to action image with title and link to the content.
Version: 0.1
Author: Mark Wilkinson
Author URI: http://markwilkinson.me
License: GPLv2 or later
*/

/* add image size for the cta boxes widget to use */
add_image_size( 'pxlcta', 300, 180, true );

/* load the metaboxes init file */
require_once dirname( __FILE__ ) . '/metabox.php';

/***************************************************************
* Function pxlcta_get_post_types()
* Gets all the public posts types on the site including posts
* pages.
* @return array of post types
***************************************************************/
function pxlcta_get_post_types() {
	
	/* register the default categories and post tags for each post type above */
	$pxlcta_post_type_args = array(
		'public'   => true,
		'_builtin' => false
	);
	
	$pxlcta_output = 'names'; // names or objects, note names is the default
	$pxlcta_operator = 'and'; // 'and' or 'or'
	
	/* get all the public post types using the args above */
	$pxlcta_post_types = get_post_types( $pxlcta_post_type_args, $pxlcta_output, $pxlcta_operator );
	
	/* add the normla post and page the post types array */
	$pxlcta_post_types[ 'post' ] = 'post';
	$pxlcta_post_types[ 'page' ] = 'page';
	
	/* setup blank array to store our post types in */
	$pxlcta_all_post_types = array();
	
	/* loop through each post type */
	foreach( $pxlcta_post_types as $key => $value ) {
		
		/* add post type name to the new array */
		$pxlcta_all_post_types[] = $value;
		
	}
	
	return $pxlcta_all_post_types;
	
}

/******************************************************************************************
* Function pxlcta_featured_image_posts_select
* Creates a Dropdown list of posts for a specified post type passed to the function
******************************************************************************************/
function pxlcta_featured_image_posts_select( $select_id, $post_type, $selected = 0 ) {
    
    /* set some args for getting posts */
    $pxlcta_posts_args = array(
    	'post_type'=> $post_type,
    	'post_status'=> 'publish',
    	'suppress_filters' => false,
    	'posts_per_page'=> -1,
    	'meta_query' => array(
			'relation' => 'AND',
			array( // only return posts that have a post thumbnail
				'key' => 'pxlcta_image',
				'compare' => 'EXISTS'
			)
		)
    );
    
    /* get an array of all the posts for this post type */
    $pxlcta_posts = get_posts( $pxlcta_posts_args );
    
    /* build the select input */
    echo '<select name="'. $select_id .'" id="'.$select_id.'">';
    echo '<option value = "" >--- Select CTA Content ---</option>';
    
    /* loop through each post outputing as an option in the select input */
    foreach ( $pxlcta_posts as $pxlcta_post ) {
        echo '<option value="', $pxlcta_post->ID, '"', $selected == $pxlcta_post->ID ? ' selected="selected"' : '', '>', $pxlcta_post->post_title, '</option>';
    }
    
    /* end the select input */
    echo '</select>';
}

/***************************************************************
* Class PXLCTA_Widget()
* Creates a widget for displaying the featured image from a
* selected page.
***************************************************************/
class PXLCTA_Widget extends WP_Widget {

	function pxlcta_widget() {
		
		/* set the classname and the description of the widget */
		$widget_ops = array( 'classname' => 'pxlcta_widget', 'description' => 'Widget to display Call To Action information based on the post meta of pages.' );
		
		/* initialise the widget to the WP_Widget class */
		$this->WP_Widget( 'pxlcta_widget', __( 'Pixel CTA Widget', 'pxlcta' ), $widget_ops );
		
	} // ends function
	
	/* build the widget output to the widget area */
    function widget( $args, $instance ) {
    
    	extract( $args );
    	
    	/* output the before widget content declared when sidebar is registered */
		echo $before_widget;

		$pxlcta_query_args = array(
			'post_type' => apply_filters( 'pxlcta_post_types', array( 'page' ) ),
			'post__in' => array( $instance[ 'postid' ] ),
			'posts_per_page' => '1'
		);

		/* start the snippet query */
	    $pxlcta_query = new WP_Query( $pxlcta_query_args );

	    /* begin the query loop */
	    while( $pxlcta_query->have_posts() ) : $pxlcta_query->the_post(); ?>
	    	
	    	<div id="post-<?php the_ID(); ?>" <?php post_class( 'pxlcta-content' ); ?>>
	    	
	    		<?php
	    		
	    			/* add action for hooking in output before the title */
	    			do_action( 'pxlcta_before_title', get_the_ID() );
	    		
	    			/* check whether there is a title added to the widget */
	    			if( isset( $instance[ 'title' ] ) ) {
		    			
		    			/* output the widget title */
		    			echo $before_title; echo $instance[ 'title' ]; echo $after_title;
		    		
		    		/* no widget title is entered */
	    			} else {
		    			
		    			/* output the post title */
		    			echo $before_title; the_title(); echo $after_title;
		    			
	    			} // end if widget has widget title
	    			
	    			/* add action for hooking in output before the featured image */
	    			do_action( 'pxlcta_before_featured_image', get_the_ID() );
    			
    				/* get the id of the cta image from post meta */
    				$cta_image_id = get_post_meta( get_the_ID(), 'pxlcta_image_id', true );
    				
    				/* check we have an id */
    				if( $cta_image_id > 0 ) {
    				
    					?>
    					
    					<div class="featured-image">
    					
    						<?php
    							
    							/* get the attachment image based on size provided in the filter */
    							$pxlcta_image = wp_get_attachment_image_src( $cta_image_id, apply_filters( 'pxlcta_cta_image_size', 'pxlcta' ) );
    							
    							/* check whether we have a link url */
    							if( ! empty( $instance[ 'link_url' ] ) ) {
	    							
	    							/* set the link url to a variable to use later */
	    							$pxlcta_link_url = $instance[ 'link_url' ];
	    						
	    						/* no link url is provided */	
    							} else {
	    							
	    							/* set the link url to the contents permalink */
	    							$pxlcta_link_url = get_permalink();
	    							
    							} // end if link url
    						
    						?>
    					
    						<a href="<?php echo esc_url( $pxlcta_link_url ); ?>"><img src="<?php echo esc_url( $pxlcta_image[0] ); ?>" alt="CTA Image" class="cta-img" /></a>
    					
    					</div>
    					
    					<?php
	    				
    				} // end if have id
    			
    			?>
    		
    		</div>
    		
    		<?php
    			
    			/* add action for hooking in output after the featured image */
    			do_action( 'pxlcta_after_featured_image', get_the_ID() );
    		
    		?>
	    
		<?php /* end the loop */
		endwhile;

		/* reset the query */
		wp_reset_query();

		/* output the after widget content declared when sidebar is registered */
		echo $after_widget;
    
    }
    
    /* widget update function */
    function update( $new_instance, $old_instance ) {
    
        $instance = $old_instance;
        $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
        $instance[ 'postid' ] = (int) $new_instance[ 'postid' ];
        $instance[ 'link_url' ] = $new_instance[ 'link_url' ];
        return $instance;
        
    }
    
    /* widget dashboard output */
    function form( $instance ) {
    
    	if( isset( $instance[ 'title' ] ) ) {
	    	$title = esc_attr( $instance[ 'title' ] );
    	} else {
	    	$title = '';
    	}
    	
    	if( isset( $instance[ 'postid' ] ) ) {
       		$postid = esc_attr( $instance[ 'postid' ] );
       	} else {
	       	$postid = '';
       	}
       	
       	if( isset( $instance[ 'link_url' ] ) ) {
       		$link_url = esc_attr( $instance[ 'link_url' ] );
       	} else {
	       	$link_url = '';
       	}

    	?>
    	
    	<p>
	    	<label for="<?php echo $this->get_field_id( 'title' ); ?>">
	        	<?php _e( 'Title:' ); ?>
	        	<input class="widefat" id="<?php echo $this->get_field_id( 'title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	        	<p class="description">Leave blank to use the post/content title.</p>
	        </label>
        </p>
        
        <p>
        	<label for="<?php echo $this->get_field_id( 'postid' ); ?>">
	        	<?php _e( 'Choose Content:' ); ?><br />
	        	<?php pxlcta_featured_image_posts_select( $this->get_field_name( 'postid' ), apply_filters( 'pxlcta_post_types', array( 'page' ) ), $postid ); ?>
	        </label>
		</p>
		
		<p>
        	<label for="<?php echo $this->get_field_id( 'link_url' ); ?>">
	        	<?php _e( 'Link URL:' ); ?><br />
	        	<input class="widefat" id="<?php echo $this->get_field_id( 'link_url' ); ?>" name="<?php echo $this->get_field_name( 'link_url' ); ?>" type="text" value="<?php echo $link_url; ?>" />
	        	<p class="description">Add a URL to link this Call To Action to. Leave blank to link to the content permalink.</p>
	        </label>
		</p>
        
        <?php
    
    } // ends form function

}

/******************************************************************
* Function pxlcta_register_widget
* Register the custom widgets in this plugin to run with wordpress
*******************************************************************/
function pxlcta_register_widget() {
	
	register_widget( 'pxlcta_widget' );

}

/* hook new widget into wordpress */
add_action( 'widgets_init', 'pxlcta_register_widget' );