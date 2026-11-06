<div class="wooOneContainer">

	<div class="wooOneWelcomeContainer">
		
			<?php
			
				$mereyaWelcomePostTitle = '';
				$mereyaWelcomePostDesc = '';
				$mereyaWelcomePostContent = '';

				if( '' != get_theme_mod('mereya_wooone_welcome_post') && 'select' != get_theme_mod('mereya_wooone_welcome_post') ){

					$mereyaWelcomePostId = get_theme_mod('mereya_wooone_welcome_post');

					if( ctype_alnum($mereyaWelcomePostId) ){

						$mereyaWelcomePost = get_post( $mereyaWelcomePostId );

						$mereyaWelcomePostTitle = $mereyaWelcomePost->post_title;
						$mereyaWelcomePostDesc = $mereyaWelcomePost->post_excerpt;
						$mereyaWelcomePostContent = $mereyaWelcomePost->post_content;

					}

				}			
			
			?>
			
			<h1><?php echo esc_html($mereyaWelcomePostTitle); ?></h1>
			<div class="wooOneWelcomeContent">
				<p>
					<?php 
					
						if( '' != $mereyaWelcomePostDesc ){
							
							echo esc_html($mereyaWelcomePostDesc);
							
						}else{
							
							echo esc_html($mereyaWelcomePostContent);
							
						}
					
					?>
				</p>
			</div><!-- .wooOneWelcomeContent -->	
		
	</div><!-- .wooOneWelcomeContainer -->
	
	
	<div class="new-arrivals-container">
		
		<?php 
					
			if( 'no' != get_theme_mod('mereya_show_wooone_heading') ): 
			
				$mereyaWooOneLatestHeading = __('Latest Products', 'mereya');	
				$mereyaWooOneLatestText = __('Some of our latest products', 'mereya');
			
					
				if( '' != get_theme_mod('mereya_wooone_latest_heading') ){
					$mereyaWooOneLatestHeading = get_theme_mod('mereya_wooone_latest_heading');
				}
				
				if( '' != get_theme_mod('mereya_wooone_latest_text') ){
					$mereyaWooOneLatestText = get_theme_mod('mereya_wooone_latest_text');
				}				
			
					
		?>
		<div class="new-arrivals-title">
		
			<h3><?php echo esc_html($mereyaWooOneLatestHeading); ?></h3>
			<p><?php echo esc_html($mereyaWooOneLatestText); ?></p>
		
		</div><!-- .new-arrivals-title -->
		<?php endif; ?>
		
		<?php
			
			$mereyaWooOnePaged = get_query_var( 'page' ) ? get_query_var( 'page' ) : 1;
			
			$mereya_front_page_ecom = array(
				'post_type' => 'product',
				'paged' => $mereyaWooOnePaged
			);
			$mereya_front_page_ecom_the_query = new WP_Query( $mereya_front_page_ecom );
			
			$mereya_front_page_temp_query = $wp_query;
			$wp_query   = NULL;
			$wp_query   = $mereya_front_page_ecom_the_query;
			
		?>		
		
		<div class="new-arrivals-content">
		<?php if ( have_posts() && post_type_exists('product') ) : ?>
		
		
			<div class="mereya-woocommerce-content">
			
				<ul class="products">
			
					<?php /* Start the Loop */ ?>
					<?php while ( $mereya_front_page_ecom_the_query->have_posts() ) : $mereya_front_page_ecom_the_query->the_post(); ?>			
					<?php wc_get_template_part( 'content', 'product' ); ?>
					<?php endwhile; ?>
				
				</ul><!-- .products -->
				
				<?php //the_posts_navigation(); ?>
				
				<?php mereya_pagination( $mereyaWooOnePaged, $mereya_front_page_ecom_the_query->max_num_pages); // Pagination Function ?>
				
			</div><!-- .mereya-woocommerce-content -->
			
		<?php else : ?>
		
			<p><?php echo __('Please install wooCommerce and add products.', 'mereya') ?></p>

		<?php 
			
			endif; 
			wp_reset_postdata();
			$wp_query = NULL;
			$wp_query = $mereya_front_page_temp_query;
		?>			
		
		
		</div><!-- .new-arrivals-content -->		
	
	</div><!-- .new-arrivals-container -->	

</div>