<div class="frontFourContainer">

	<div class="frontFourWelcomeContainer">
		
			<?php
			
				$mereyaWelcomePostFourTitle = '';
				$mereyaWelcomePostFourDesc = '';
				$mereyaWelcomePostFourContent = '';

				if( '' != get_theme_mod('mereya_four_welcome_post') && 'select' != get_theme_mod('mereya_four_welcome_post') ){

					$mereyaWelcomePostFourId = get_theme_mod('mereya_four_welcome_post');

					if( ctype_alnum($mereyaWelcomePostFourId) ){

						$mereyaWelcomePostFour = get_post( $mereyaWelcomePostFourId );

						$mereyaWelcomePostFourTitle = $mereyaWelcomePostFour->post_title;
						$mereyaWelcomePostFourDesc = $mereyaWelcomePostFour->post_excerpt;
						$mereyaWelcomePostFourContent = $mereyaWelcomePostFour->post_content;

					}

				}			
			
			?>
			
			<h1><?php echo esc_html($mereyaWelcomePostFourTitle); ?></h1>
			<div class="frontFourWelcomeContent">
				<p>
					<?php 
					
						if( '' != $mereyaWelcomePostFourDesc ){
							
							echo esc_html($mereyaWelcomePostFourDesc);
							
						}else{
							
							echo esc_html($mereyaWelcomePostFourContent);
							
						}
					
					?>
				</p>
			</div><!-- .frontFourWelcomeContent -->	
		
	</div><!-- .frontFourWelcomeContainer -->
	
	<div class="frontPageFourProductsContainer">
		
		<div class="frontPageFourProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_four_product_post_one') && 'select' != get_theme_mod('mereya_four_product_post_one') ):
			
				$mereyaFourProductOneTitle = '';
				$mereyaFourProductOneDesc = '';

					$mereyaFourProductOneId = get_theme_mod('mereya_four_product_post_one');

					if( ctype_alnum($mereyaFourProductOneId) ){

						$mereyaFourProductOne = get_post( $mereyaFourProductOneId );

						$mereyaFourProductOneTitle = $mereyaFourProductOne->post_title;
						$mereyaFourProductOneDesc = $mereyaFourProductOne->post_excerpt;
						$mereyaFourProductOneContent = mereya_limitedstring($mereyaFourProductOne->post_content, 50);
						$mereyaFourProductOneLink = get_permalink($mereyaFourProductOneId);
						
						if( has_post_thumbnail( $mereyaFourProductOneId ) ){
							
							$mereyaFourProductOneImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaFourProductOneId ), 'mereya-productfour' );
							$mereyaFourProductOneImage = $mereyaFourProductOneImage[0];
							
						}else{
							
							$mereyaFourProductOneImage = get_template_directory_uri() . '/assets/images/frontitemimage.png';
							
						}

					}
			
			?>
			<div class="frontPageFourProductImage">
				<img src="<?php echo esc_url($mereyaFourProductOneImage); ?>" />
			</div><!-- .frontPageFourProductImage -->
			<h2><a href="<?php echo esc_url($mereyaFourProductOneLink); ?>"><?php echo esc_html($mereyaFourProductOneTitle); ?></a></h2>
			<div class="frontPageFourProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaFourProductOneDesc ){
							
							echo esc_html($mereyaFourProductOneDesc);
							
						}else{
							
							echo esc_html($mereyaFourProductOneContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontFourWelcomeContent -->
			<?php endif; ?>
			
		</div><!-- .frontPageFourProductContainer -->
		
		<div class="frontPageFourProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_four_product_post_two') && 'select' != get_theme_mod('mereya_four_product_post_two') ):
			
				$mereyaFourProductTwoTitle = '';
				$mereyaFourProductTwoDesc = '';

					$mereyaFourProductTwoId = get_theme_mod('mereya_four_product_post_two');

					if( ctype_alnum($mereyaFourProductTwoId) ){

						$mereyaFourProductTwo = get_post( $mereyaFourProductTwoId );

						$mereyaFourProductTwoTitle = $mereyaFourProductTwo->post_title;
						$mereyaFourProductTwoDesc = $mereyaFourProductTwo->post_excerpt;
						$mereyaFourProductTwoContent = mereya_limitedstring($mereyaFourProductTwo->post_content, 50);
						$mereyaFourProductTwoLink = get_permalink($mereyaFourProductTwoId);
						
						if( has_post_thumbnail( $mereyaFourProductTwoId ) ){
							
							$mereyaFourProductTwoImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaFourProductTwoId ), 'mereya-productfour' );
							$mereyaFourProductTwoImage = $mereyaFourProductTwoImage[0];
							
						}else{
							
							$mereyaFourProductTwoImage = get_template_directory_uri() . '/assets/images/frontitemimage.png';
							
						}						

					}
			
			?>
			<div class="frontPageFourProductImage">
				<img src="<?php echo esc_url($mereyaFourProductTwoImage); ?>" />
			</div><!-- .frontPageFourProductImage -->
			<h2><a href="<?php echo esc_url($mereyaFourProductTwoLink); ?>"><?php echo esc_html($mereyaFourProductTwoTitle); ?></a></h2>
			<div class="frontPageFourProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaFourProductTwoDesc ){
							
							echo esc_html($mereyaFourProductTwoDesc);
							
						}else{
							
							echo esc_html($mereyaFourProductTwoContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontFourWelcomeContent -->			
			<?php endif; ?>
			
		</div><!-- .frontPageFourProductContainer -->
		
		<div class="frontPageFourProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_four_product_post_three') && 'select' != get_theme_mod('mereya_four_product_post_three') ):
			
				$mereyaFourProductThreeTitle = '';
				$mereyaFourProductThreeDesc = '';

					$mereyaFourProductThreeId = get_theme_mod('mereya_four_product_post_three');

					if( ctype_alnum($mereyaFourProductThreeId) ){

						$mereyaFourProductThree = get_post( $mereyaFourProductThreeId );

						$mereyaFourProductThreeTitle = $mereyaFourProductThree->post_title;
						$mereyaFourProductThreeDesc = $mereyaFourProductThree->post_excerpt;
						$mereyaFourProductThreeContent = mereya_limitedstring($mereyaFourProductThree->post_content, 50);
						$mereyaFourProductThreeLink = get_permalink($mereyaFourProductThreeId);
						
						if( has_post_thumbnail( $mereyaFourProductThreeId ) ){
							
							$mereyaFourProductThreeImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaFourProductThreeId ), 'mereya-productfour' );
							$mereyaFourProductThreeImage = $mereyaFourProductThreeImage[0];
							
						}else{
							
							$mereyaFourProductThreeImage = get_template_directory_uri() . '/assets/images/frontitemimage.png';
							
						}						

					}			
			
			?>
			<div class="frontPageFourProductImage">
				<img src="<?php echo esc_url($mereyaFourProductThreeImage); ?>" />
			</div><!-- .frontPageFourProductImage -->
			<h2><a href="<?php echo esc_url($mereyaFourProductThreeLink); ?>"><?php echo esc_html($mereyaFourProductThreeTitle); ?></a></h2>
			<div class="frontPageFourProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaFourProductThreeDesc ){
							
							echo esc_html($mereyaFourProductThreeDesc);
							
						}else{
							
							echo esc_html($mereyaFourProductThreeContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontFourWelcomeContent -->			
			<?php endif; ?>
			
		</div><!-- .frontPageFourProductContainer -->
		
		<div class="frontPageFourProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_four_product_post_three') && 'select' != get_theme_mod('mereya_four_product_post_three') ):
			
				$mereyaFourProductFourTitle = '';
				$mereyaFourProductFourDesc = '';

					$mereyaFourProductFourId = get_theme_mod('mereya_four_product_post_three');

					if( ctype_alnum($mereyaFourProductFourId) ){

						$mereyaFourProductFour = get_post( $mereyaFourProductFourId );

						$mereyaFourProductFourTitle = $mereyaFourProductFour->post_title;
						$mereyaFourProductFourDesc = $mereyaFourProductFour->post_excerpt;
						$mereyaFourProductFourContent = mereya_limitedstring($mereyaFourProductFour->post_content, 50);
						$mereyaFourProductFourLink = get_permalink($mereyaFourProductFourId);
						
						if( has_post_thumbnail( $mereyaFourProductFourId ) ){
							
							$mereyaFourProductFourImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaFourProductFourId ), 'mereya-productfour' );
							$mereyaFourProductFourImage = $mereyaFourProductFourImage[0];
							
						}else{
							
							$mereyaFourProductFourImage = get_template_directory_uri() . '/assets/images/frontitemimage.png';
							
						}						

					}
			
			?>
			<div class="frontPageFourProductImage">
				<img src="<?php echo esc_url($mereyaFourProductFourImage); ?>" />
			</div><!-- .frontPageFourProductImage -->
			<h2><a href="<?php echo esc_url($mereyaFourProductFourLink); ?>"><?php echo esc_html($mereyaFourProductFourTitle); ?></a></h2>
			<div class="frontPageFourProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaFourProductFourDesc ){
							
							echo esc_html($mereyaFourProductFourDesc);
							
						}else{
							
							echo esc_html($mereyaFourProductFourContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontFourWelcomeContent -->			
			<?php endif; ?>
			
		</div><!-- .frontPageFourProductContainer -->
		
		<div class="frontPageFourProductContainer">
			
			<?php
			
				if( '' != get_theme_mod('mereya_four_product_post_three') && 'select' != get_theme_mod('mereya_four_product_post_three') ):
			
				$mereyaFourProductFiveTitle = '';
				$mereyaFourProductFiveDesc = '';

					$mereyaFourProductFiveId = get_theme_mod('mereya_four_product_post_three');

					if( ctype_alnum($mereyaFourProductFiveId) ){

						$mereyaFourProductFive = get_post( $mereyaFourProductFiveId );

						$mereyaFourProductFiveTitle = $mereyaFourProductFive->post_title;
						$mereyaFourProductFiveDesc = $mereyaFourProductFive->post_excerpt;
						$mereyaFourProductFiveContent = mereya_limitedstring($mereyaFourProductFive->post_content, 50);
						$mereyaFourProductFiveLink = get_permalink($mereyaFourProductFiveId);
						
						if( has_post_thumbnail( $mereyaFourProductFiveId ) ){
							
							$mereyaFourProductFiveImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaFourProductFiveId ), 'mereya-productfour' );
							$mereyaFourProductFiveImage = $mereyaFourProductFiveImage[0];
							
						}else{
							
							$mereyaFourProductFiveImage = get_template_directory_uri() . '/assets/images/frontitemimage.png';
							
						}						

					}		
			
			?>
			<div class="frontPageFourProductImage">
				<img src="<?php echo esc_url($mereyaFourProductFiveImage); ?>" />
			</div><!-- .frontPageFourProductImage -->
			<h2><a href="<?php echo esc_url($mereyaFourProductFiveLink); ?>"><?php echo esc_html($mereyaFourProductFiveTitle); ?></a></h2>
			<div class="frontPageFourProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaFourProductFiveDesc ){
							
							echo esc_html($mereyaFourProductFiveDesc);
							
						}else{
							
							echo esc_html($mereyaFourProductFiveContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontFourWelcomeContent -->			
			<?php endif; ?>
			
		</div><!-- .frontPageFourProductContainer -->
		
		<div class="frontPageFourProductContainer">
			
			<?php
				
				if( '' != get_theme_mod('mereya_four_product_post_three') && 'select' != get_theme_mod('mereya_four_product_post_three') ):
			
				$mereyaFourProductSixTitle = '';
				$mereyaFourProductSixDesc = '';

					$mereyaFourProductSixId = get_theme_mod('mereya_four_product_post_three');

					if( ctype_alnum($mereyaFourProductSixId) ){

						$mereyaFourProductSix = get_post( $mereyaFourProductSixId );

						$mereyaFourProductSixTitle = $mereyaFourProductSix->post_title;
						$mereyaFourProductSixDesc = $mereyaFourProductSix->post_excerpt;
						$mereyaFourProductSixContent = mereya_limitedstring($mereyaFourProductSix->post_content, 50);
						$mereyaFourProductSixLink = get_permalink($mereyaFourProductSixId);
						
						if( has_post_thumbnail( $mereyaFourProductSixId ) ){
							
							$mereyaFourProductSixImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaFourProductSixId ), 'mereya-productfour' );
							$mereyaFourProductSixImage = $mereyaFourProductSixImage[0];
							
						}else{
							
							$mereyaFourProductSixImage = get_template_directory_uri() . '/assets/images/frontitemimage.png';
							
						}						

					}
			
			?>
			<div class="frontPageFourProductImage">
				<img src="<?php echo esc_url($mereyaFourProductSixImage); ?>" />
			</div><!-- .frontPageFourProductImage -->
			<h2><a href="<?php echo esc_url($mereyaFourProductSixLink); ?>"><?php echo esc_html($mereyaFourProductSixTitle); ?></a></h2>
			<div class="frontPageFourProductContent">
				
				<p>
					<?php 
					
						if( '' != $mereyaFourProductSixDesc ){
							
							echo esc_html($mereyaFourProductSixDesc);
							
						}else{
							
							echo esc_html($mereyaFourProductSixContent);
							
						}
					
					?>
				</p>
				
			</div><!-- .frontFourWelcomeContent -->			
			<?php endif; ?>
			
		</div><!-- .frontPageFourProductContainer -->		
		
	</div><!-- .frontPageFourProductsContainer -->
	
</div><!-- .frontPageFourContainer -->