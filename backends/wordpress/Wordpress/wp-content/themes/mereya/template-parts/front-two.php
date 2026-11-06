<div class="frontTwoContainer">

	<div class="frontTwoWelcomeContainer">
		
			<?php
			
				$mereyaWelcomePostTitle = '';
				$mereyaWelcomePostDesc = '';
				$mereyaWelcomePostContent = '';

				if( '' != get_theme_mod('mereya_two_welcome_post') && 'select' != get_theme_mod('mereya_two_welcome_post') ){

					$mereyaWelcomePostId = get_theme_mod('mereya_two_welcome_post');

					if( ctype_alnum($mereyaWelcomePostId) ){

						$mereyaWelcomePost = get_post( $mereyaWelcomePostId );

						$mereyaWelcomePostTitle = $mereyaWelcomePost->post_title;
						$mereyaWelcomePostDesc = $mereyaWelcomePost->post_excerpt;
						$mereyaWelcomePostContent = $mereyaWelcomePost->post_content;

					}

				}			
			
			?>
			
			<h1><?php echo esc_html($mereyaWelcomePostTitle); ?></h1>
			<div class="frontTwoWelcomeContent">
				<p>
					<?php 
					
						if( '' != $mereyaWelcomePostDesc ){
							
							echo esc_html($mereyaWelcomePostDesc);
							
						}else{
							
							echo esc_html($mereyaWelcomePostContent);
							
						}
					
					?>
				</p>
			</div><!-- .frontTwoWelcomeContent -->	
		
	</div><!-- .frontTwoWelcomeContainer -->
	
	<div class="frontPageTwoProductsContainer">
		
		<div class="frontPageTwoProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_two_product_post_one') && 'select' != get_theme_mod('mereya_two_product_post_one') ):
			
				$mereyaProductOneTitle = '';
				$mereyaProductOneDesc = '';

					$mereyaProductOneId = get_theme_mod('mereya_two_product_post_one');

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
			<div class="frontPageTwoProductImage">
				<img src="<?php echo esc_url($mereyaProductOneImage); ?>" />
			</div><!-- .frontPageTwoProductImage -->
			<h2><a href="<?php echo esc_url($mereyaProductOneLink); ?>"><?php echo esc_html($mereyaProductOneTitle); ?></a></h2>
			<div class="frontPageTwoProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaProductOneDesc ){
							
							echo esc_html($mereyaProductOneDesc);
							
						}else{
							
							echo esc_html($mereyaProductOneContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontTwoWelcomeContent -->			
			<?php endif; ?>
			
		</div><!-- .frontPageTwoProductContainer -->
		
		<div class="frontPageTwoProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_two_product_post_two') && 'select' != get_theme_mod('mereya_two_product_post_two') ):
			
				$mereyaProductTwoTitle = '';
				$mereyaProductTwoDesc = '';

					$mereyaProductTwoId = get_theme_mod('mereya_two_product_post_two');

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
			<div class="frontPageTwoProductImage">
				<img src="<?php echo esc_url($mereyaProductTwoImage); ?>" />
			</div><!-- .frontPageTwoProductImage -->
			<h2><a href="<?php echo esc_url($mereyaProductTwoLink); ?>"><?php echo esc_html($mereyaProductTwoTitle); ?></a></h2>
			<div class="frontPageTwoProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaProductTwoDesc ){
							
							echo esc_html($mereyaProductTwoDesc);
							
						}else{
							
							echo esc_html($mereyaProductTwoContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontTwoWelcomeContent -->			
			<?php endif; ?>
			
		</div><!-- .frontPageTwoProductContainer -->
		
		<div class="frontPageTwoProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_two_product_post_three') && 'select' != get_theme_mod('mereya_two_product_post_three') ):
			
				$mereyaProductThreeTitle = '';
				$mereyaProductThreeDesc = '';

					$mereyaProductThreeId = get_theme_mod('mereya_two_product_post_three');

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
			<div class="frontPageTwoProductImage">
				<img src="<?php echo esc_url($mereyaProductThreeImage); ?>" />
			</div><!-- .frontPageTwoProductImage -->
			<h2><a href="<?php echo esc_url($mereyaProductThreeLink); ?>"><?php echo esc_html($mereyaProductThreeTitle); ?></a></h2>
			<div class="frontPageTwoProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaProductThreeDesc ){
							
							echo esc_html($mereyaProductThreeDesc);
							
						}else{
							
							echo esc_html($mereyaProductThreeContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontTwoWelcomeContent -->			
			<?php endif; ?>
			
		</div><!-- .frontPageTwoProductContainer -->		
		
	</div><!-- .frontPageTwoProductsContainer -->
	
</div><!-- .frontPageTwoContainer -->