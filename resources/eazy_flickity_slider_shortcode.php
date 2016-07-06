<?php
if (function_exists('eazy_flickity_slides')) {
 	//add eazy-flickity-slider shortcode 
	add_shortcode('eazy-flickity-slider', 'eazy_flickity_slider_shortcode');
	function eazy_flickity_slider_shortcode($atts) {   
		
		//set attributes
		$atts = shortcode_atts(
			array(
				'post_type' 							=>		'eazy_flickity_slide',
				'order' 									=>		'',
				'orderby' 								=>		'',
				'post_date' 							=>		'',
				'posts' 									=>		 -1,
				'height' 									=>		 NULL,
				'width' 									=>		 NULL,
				'eazy_flickity_slider' 		=>		 NULL,
				'slider_id'								=>		 NULL
			),
			$atts
		);
		//set variables based on attributes
		$order = $atts['order'];
		$orderby = $atts['orderby'];
		$post_dateb = $atts['post_date'];
		$posts = $atts['posts'];
		$height = $atts['height'];
		$width = $atts['width'];
		$eazy_flickity_slider = $atts['eazy_flickity_slider'];
		$flickity_open = NULL;
		$flickity_close = NULL;
		$post_parent = $atts['slider_id'];
		
		//set query options
		$eazyoptions = array( 
			'post_type' 						=>		'eazy_flickity_slide', 
			'order' 								=>		$order, 
			'orderby' 							=>		$orderby, 
			'posts_per_page' 				=>		$posts, 
			'height' 								=>		$height, 
			'width' 								=>		$width, 
			'eazy_flickity_slider' 	=>		$eazy_flickity_slider, 
		); 

		// get all of the images attached to the current post
    // function get_gallery_images() {
        // global $post;
       /* $photos = get_children( 
        	array(
						'post_parent' 		=> 	$eazy_flickity_slider, 
						'post_status' 		=> 	'inherit', 
						'post_type' 			=> 	'attachment', 
						'post_mime_type' 	=> 	'image', 
						'order' 					=> 	'ASC', 
						'orderby' 				=> 	'menu_order ID'
					) 
        );
        $galleryimages = array();
        if ($photos) {
            foreach ($photos as $photo) {
                // get the correct image html for the selected size
                $galleryimages[] = wp_get_attachment_url($photo->ID);
            }
        }
        // return $galleryimages;
        echo 'galleryimages';*/

        $gallery = get_post_gallery_images( $eazy_flickity_slider );

				$image_list = '<ul>';
$flickity_slides = array();
				// Loop through each image in each gallery
				foreach( $gallery as $image_url ) {

					$image_list .= '<li>' . '<img src="' . $image_url . '">' . '</li>';
										$flickity_slides[] = "
						<div class='gallery-cell'>
							<img src='". $image_url ."' alt='test'>
						</div>";
				}

				$image_list .= '</ul>';

				// Append our image list to the content of our post
				$content .= $image_list;
				print_r($content);
				// return $content;
				
        
        $sliderid = "slider-". $eazy_flickity_slider ."";
				$flickity_open = '<div class="gallery flickity-shortcode" id='.$sliderid.' >';
				// foreach ($galleryimages as $key => $value) {

				// 	$flickity_slides[] = "
				// 		<div class='gallery-cell'>
				// 			<img src='". $value ."' alt='test'>
				// 		</div>";
					$flickity_close = '</div>'; //closing div from class "gallery js flickity"
					
				// }
    // }
		// The Query
		// $eazyquery = new WP_Query( $eazyoptions );

		// The Loop
		/*if ($eazyquery->have_posts() ) :
			if (isset($eazy_flickity_slider)) : //Check if slider name is set, if it is set add slider name to id
				$sliderid = "slider-". $eazy_flickity_slider ."";
				$flickity_open = '<div class="gallery flickity-shortcode" id='.$sliderid.' >';
			else:
				$flickity_open = '<div class="gallery flickity-shortcode" id="all-slides">';
			endif; 

			while ( $eazyquery->have_posts() ) : $eazyquery->the_post(); // here show the attachment images, not thumbnails
				$thumb_id = get_post_thumbnail_id();
				$eazyimage_attributes = wp_get_attachment_image_src($thumb_id,'full', true);
				$flickity_slides[] = "
					<div class='gallery-cell'>
						<img src='".$eazyimage_attributes[0]."' alt='".get_post(get_post_thumbnail_id())->post_title."'>
					</div>";
			endwhile;

			$flickity_close = '</div>'; //closing div from class "gallery js flickity"
		endif;*/

		//restore original Post Data 
		wp_reset_postdata();

		// concatenate open, slides & close, return them as $slider
		$slider = $flickity_open;
		foreach ($flickity_slides as $key => $slide) {
			$slider .= $slide;
		}
		$slider .= $flickity_close;
		return $slider;  
 	}

	add_action( 'wp_enqueue_scripts', 'eazy_flickity_shortcode_scripts_styles' );
	function eazy_flickity_shortcode_scripts_styles() {
		wp_enqueue_script('eazy-flickity-shortcode-extra', EZ_FLICKITY_ELEMENTS_URL  . 'resources/js/flickity.shortcode.dimensions.js', array(), false, true );
			
			global $wp_query; 
			$posts = $wp_query->posts;
			$pattern = get_shortcode_regex();
			
			foreach ($posts as $post) {
				if (preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) &&
						array_key_exists( 2, $matches ) && in_array( 'eazy-flickity-slider', $matches[2] )) {
					foreach ($matches[0] as $key => $value) {
						$matches[$key] = $value;
						
						$eznameflag = '~eazy_flickity_slider="(.*?)"~';
						$ezwidthflag = '~width="(.*?)"~';
						$ezheightflag = '~height="(.*?)"~';
						$thiswidth = '';
						$thisheight = '';

						if (preg_match($eznameflag, $value, $m)) {
							$thisname = $m[1];
							//$matches[$key] .= ['sliderid' => $thisname];
						}
						if (preg_match($ezwidthflag, $value, $m)) {
							$thiswidth = $m[1];
							//$matches[$key] .= ['width' => $thiswidth];
						}
						if (preg_match($ezheightflag, $value, $m)) {
							$thisheight = $m[1];
							//$matches[$key] .= ['height' => $thisheight];
						}

						$matches[$key] = ['sliderid' => $thisname, 'width' => $thiswidth, 'height' => $thisheight];
					}
					break;  
				} //end if preg match
			} //end foreach
	 
		wp_localize_script( 'eazy-flickity-shortcode-extra', 'eazyoptions', $matches );
		}
}
