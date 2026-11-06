<div class="frontpageFiveContainer">

	<div class="frontpageFiveWelcomeContainer">
		
			<?php
			
				$mereyaWelcomePostTitle = '';
				$mereyaWelcomePostDesc = '';
				$mereyaWelcomePostContent = '';

				if( '' != get_theme_mod('mereya_five_welcome_post') && 'select' != get_theme_mod('mereya_five_welcome_post') ){

					$mereyaWelcomePostId = get_theme_mod('mereya_five_welcome_post');

					if( ctype_alnum($mereyaWelcomePostId) ){

						$mereyaWelcomePost = get_post( $mereyaWelcomePostId );

						$mereyaWelcomePostTitle = $mereyaWelcomePost->post_title;
						$mereyaWelcomePostDesc = $mereyaWelcomePost->post_excerpt;
						$mereyaWelcomePostContent = $mereyaWelcomePost->post_content;

					}

				}			
			
			?>
			
			<h1><?php echo esc_html($mereyaWelcomePostTitle); ?></h1>
			<div class="frontpageFiveWelcomeContent">
				<p>
					<?php 
					
						if( '' != $mereyaWelcomePostDesc ){
							
							echo esc_html($mereyaWelcomePostDesc);
							
						}else{
							
							echo esc_html($mereyaWelcomePostContent);
							
						}
					
					?>
				</p>
			</div><!-- .frontpageFiveWelcomeContent -->	
		
	</div><!-- .frontpageFiveWelcomeContainer -->
	
	<div class="frontpageFiveProductsContainer">
		
		<div class="frontpageFiveProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_five_product_post_one') && 'select' != get_theme_mod('mereya_five_product_post_one') ):
			
				$mereyaProductOneTitle = '';
				$mereyaProductOneDesc = '';

					$mereyaProductOneId = get_theme_mod('mereya_five_product_post_one');

					if( ctype_alnum($mereyaProductOneId) ){

						$mereyaProductOne = get_post( $mereyaProductOneId );

						$mereyaProductOneTitle = $mereyaProductOne->post_title;
						$mereyaProductOneDesc = $mereyaProductOne->post_excerpt;
						$mereyaProductOneContent = mereya_limitedstring($mereyaProductOne->post_content, 150);
						$mereyaProductOneLink = get_permalink($mereyaProductOneId);
						
						if( has_post_thumbnail( $mereyaProductOneId ) ){
							
							$mereyaProductOneImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaProductOneId ), 'mereya-producttwo' );
							$mereyaProductOneImage = $mereyaProductOneImage[0];
							
						}else{
							
							$mereyaProductOneImage = get_template_directory_uri() . '/assets/images/service.jpg';
							
						}

					}
			
			
			?>
			<div class="frontpageFiveProductImage">
				<img src="<?php echo esc_url($mereyaProductOneImage); ?>" />
			</div><!-- .frontpageFiveProductImage -->
			<h2><a href="<?php echo esc_url($mereyaProductOneLink); ?>"><?php echo esc_html($mereyaProductOneTitle); ?></a></h2>
			<div class="frontpageFiveProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaProductOneDesc ){
							
							echo esc_html($mereyaProductOneDesc);
							
						}else{
							
							echo esc_html($mereyaProductOneContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontpageFiveProductContent -->
			<?php endif; ?>
			
		</div><!-- .frontpageFiveProductContainer -->
		
		<div class="frontpageFiveProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_five_product_post_two') && 'select' != get_theme_mod('mereya_five_product_post_two') ):
			
				$mereyaProductTwoTitle = '';
				$mereyaProductTwoDesc = '';



					$mereyaProductTwoId = get_theme_mod('mereya_five_product_post_two');

					if( ctype_alnum($mereyaProductTwoId) ){

						$mereyaProductTwo = get_post( $mereyaProductTwoId );

						$mereyaProductTwoTitle = $mereyaProductTwo->post_title;
						$mereyaProductTwoDesc = $mereyaProductTwo->post_excerpt;
						$mereyaProductTwoContent = mereya_limitedstring($mereyaProductTwo->post_content, 150);
						$mereyaProductTwoLink = get_permalink($mereyaProductTwoId);
						
						if( has_post_thumbnail( $mereyaProductTwoId ) ){
							
							$mereyaProductTwoImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaProductTwoId ), 'mereya-producttwo' );
							$mereyaProductTwoImage = $mereyaProductTwoImage[0];
							
						}else{
							
							$mereyaProductTwoImage = get_template_directory_uri() . '/assets/images/service.jpg';
							
						}						

					}

			
			
			?>
			<div class="frontpageFiveProductImage">
				<img src="<?php echo esc_url($mereyaProductTwoImage); ?>" />
			</div><!-- .frontpageFiveProductImage -->
			<h2><a href="<?php echo esc_url($mereyaProductTwoLink); ?>"><?php echo esc_html($mereyaProductTwoTitle); ?></a></h2>
			<div class="frontpageFiveProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaProductTwoDesc ){
							
							echo esc_html($mereyaProductTwoDesc);
							
						}else{
							
							echo esc_html($mereyaProductTwoContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontpageFiveProductContent -->			
			<?php endif; ?>
			
		</div><!-- .frontpageFiveProductContainer -->
		
		<div class="frontpageFiveProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_five_product_post_three') && 'select' != get_theme_mod('mereya_five_product_post_three') ):
			
				$mereyaProductThreeTitle = '';
				$mereyaProductThreeDesc = '';

					$mereyaProductThreeId = get_theme_mod('mereya_five_product_post_three');

					if( ctype_alnum($mereyaProductThreeId) ){

						$mereyaProductThree = get_post( $mereyaProductThreeId );

						$mereyaProductThreeTitle = $mereyaProductThree->post_title;
						$mereyaProductThreeDesc = $mereyaProductThree->post_excerpt;
						$mereyaProductThreeContent = mereya_limitedstring($mereyaProductThree->post_content, 150);
						$mereyaProductThreeLink = get_permalink($mereyaProductThreeId);
						
						if( has_post_thumbnail( $mereyaProductThreeId ) ){
							
							$mereyaProductThreeImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaProductThreeId ), 'mereya-producttwo' );
							$mereyaProductThreeImage = $mereyaProductThreeImage[0];
							
						}else{
							
							$mereyaProductThreeImage = get_template_directory_uri() . '/assets/images/service.jpg';
							
						}						

					}			
			
			?>
			<div class="frontpageFiveProductImage">
				<img src="<?php echo esc_url($mereyaProductThreeImage); ?>" />
			</div><!-- .frontpageFiveProductImage -->
			<h2><a href="<?php echo esc_url($mereyaProductThreeLink); ?>"><?php echo esc_html($mereyaProductThreeTitle); ?></a></h2>
			<div class="frontpageFiveProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaProductThreeDesc ){
							
							echo esc_html($mereyaProductThreeDesc);
							
						}else{
							
							echo esc_html($mereyaProductThreeContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontpageFiveProductContent -->			
			<?php endif; ?>
			
		</div><!-- .frontpageFiveProductContainer -->
		
		<div class="frontpageFiveProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_five_product_post_four') && 'select' != get_theme_mod('mereya_five_product_post_four') ):
			
				$mereyaProductFourTitle = '';
				$mereyaProductFourDesc = '';

					$mereyaProductFourId = get_theme_mod('mereya_five_product_post_four');

					if( ctype_alnum($mereyaProductFourId) ){

						$mereyaProductFour = get_post( $mereyaProductFourId );

						$mereyaProductFourTitle = $mereyaProductFour->post_title;
						$mereyaProductFourDesc = $mereyaProductFour->post_excerpt;
						$mereyaProductFourContent = mereya_limitedstring($mereyaProductFour->post_content, 150);
						$mereyaProductFourLink = get_permalink($mereyaProductFourId);
						
						if( has_post_thumbnail( $mereyaProductFourId ) ){
							
							$mereyaProductFourImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaProductFourId ), 'mereya-producttwo' );
							$mereyaProductFourImage = $mereyaProductFourImage[0];
							
						}else{
							
							$mereyaProductFourImage = get_template_directory_uri() . '/assets/images/service.jpg';
							
						}						

					}			
			
			?>
			<div class="frontpageFiveProductImage">
				<img src="<?php echo esc_url($mereyaProductFourImage); ?>" />
			</div><!-- .frontpageFiveProductImage -->
			<h2><a href="<?php echo esc_url($mereyaProductFourLink); ?>"><?php echo esc_html($mereyaProductFourTitle); ?></a></h2>
			<div class="frontpageFiveProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaProductFourDesc ){
							
							echo esc_html($mereyaProductFourDesc);
							
						}else{
							
							echo esc_html($mereyaProductFourContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontpageFiveProductContent -->	
			
		<?php endif; ?>	
		</div><!-- .frontpageFiveProductContainer -->		
		
		
	</div><!-- .frontpageFiveProductsContainer -->
	
</div><!-- .frontpageFiveContainer -->