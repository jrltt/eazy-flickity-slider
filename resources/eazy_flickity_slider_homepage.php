<?php
if (function_exists('eazy_flickity_slides')) {
	function eazy_flickity_slider_homepage() {
		$args = array(
			'post_type' 						=>		'eazy_flickity_slide',
			'eazy_flickity_slider' 	=>		'homepage'
		);

		$the_query = new WP_Query($args);

		if ( $the_query->have_posts() ) : ?>
			<div class="eazy-flickity-homepage-slider">
			<?php while ($the_query->have_posts()): ?>
				<?php $the_query->the_post(); ?>
					<div class="gallery-cell">
						<?php the_post_thumbnail('full' ); ?>
					</div>
			<?php endwhile; ?>
			</div>
		<?php endif; 
		wp_reset_postdata();
	}
}