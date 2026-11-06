<div class="front-three-container">

	<div class="front-three-services">
	
		<?php 
					
				$mereyaWelcomePostTitle = '';
				$mereyaWelcomePostDesc = '';
				$mereyaWelcomePostContent = '';
				
				if( '' != get_theme_mod('mereya_three_welcome_post') && 'select' != get_theme_mod('mereya_three_welcome_post') ){
					
					$mereyaWelcomePostId = get_theme_mod('mereya_three_welcome_post');
					
					if( ctype_alnum($mereyaWelcomePostId) ){

						$mereyaWelcomePost = get_post( $mereyaWelcomePostId );
					
						$mereyaWelcomePostTitle = $mereyaWelcomePost->post_title;
						$mereyaWelcomePostDesc = $mereyaWelcomePost->post_excerpt;
						$mereyaWelcomePostContent = $mereyaWelcomePost->post_content;
						
					}
					
				}
				
				
				
		?>
		<div class="frontpage-welcome-container">

			<h1><?php echo esc_html($mereyaWelcomePostTitle); ?></h1>
			<div>
			<?php 
					
				if( '' != $mereyaWelcomePostDesc ){
							
					echo esc_html($mereyaWelcomePostDesc);
							
				}else{
							
					echo esc_html($mereyaWelcomePostContent);
							
				}
					
			?>			
			</div>

		</div><!-- .frontpage-welcome-container -->

		
		<div class="bizthree-items">
		
			<?php
				
				if( '' != get_theme_mod('mereya_three_products_one') && 'select' != get_theme_mod('mereya_three_products_one') ):
				
				$mereyaProductOneTitle = '';
				$mereyaProductOneDesc = '';
				$mereyaProductOneUrl = '';
				$mereyaProductOneImage = '';			
				
				$mereyaProductOneId = get_theme_mod('mereya_three_products_one');
				
				if( ctype_alnum($mereyaProductOneId) ){

					$mereyaProductOne = get_post( $mereyaProductOneId );
				
					$mereyaProductOneTitle = $mereyaProductOne->post_title;
					$mereyaProductOneDesc = $mereyaProductOne->post_excerpt;
					$mereyaProductOneContent = mereya_limitedstring($mereyaProductOne->post_content, 150);
					$mereyaProductOneUrl = get_permalink( $mereyaProductOneId );
					
					if( has_post_thumbnail($mereyaProductOneId) ){
						$mereyaProductOneImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaProductOneId ), 'full' );
						$mereyaProductOneImage = $mereyaProductOneImage[0];
					}				
					
				}			
				
			?>
			<div class="bizthree-item">
			
				<div class="bizthree-image">
					
					<?php 

						if( '' != $mereyaProductOneImage ){
							echo '<a href="' . esc_url($mereyaProductOneUrl) . '"><img src="' . esc_url($mereyaProductOneImage) . '" /></a>';
						}else{
							echo '<img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/service.jpg" />';
						}
							
					?>				
					
				</div><!-- .bizthree-image -->
				
				<div class="bizthree-content">
				
					<h3><a href="<?php echo esc_url($mereyaProductOneUrl); ?>"><?php echo esc_html($mereyaProductOneTitle); ?></a></h3>
					<div>
					<?php 
					
						if( '' != $mereyaProductOneDesc ){
							
							echo esc_html($mereyaProductOneDesc);
							
						}else{
							
							echo esc_html($mereyaProductOneContent);
							
						}
					
					?>		
					</div>	
				
				</div><!-- .bizthree-content -->		
			
			</div><!-- .bizthree-item -->
			<?php endif; ?>
			
			<?php 
			
				if( '' != get_theme_mod('mereya_three_products_two') && 'select' != get_theme_mod('mereya_three_products_two') ):
				
				$mereyaProductTwoTitle = '';
				$mereyaProductTwoDesc = '';
				$mereyaProductTwoUrl = '';
				$mereyaProductTwoImage = '';			
				
				$mereyaProductTwoId = get_theme_mod('mereya_three_products_two');
				
				if( ctype_alnum($mereyaProductTwoId) ){

					$mereyaProductTwo = get_post( $mereyaProductTwoId );
				
					$mereyaProductTwoTitle = $mereyaProductTwo->post_title;
					$mereyaProductTwoDesc = $mereyaProductTwo->post_excerpt;
					$mereyaProductTwoContent = mereya_limitedstring($mereyaProductTwo->post_content, 150);
					$mereyaProductTwoUrl = get_permalink( $mereyaProductTwoId );
					
					if( has_post_thumbnail($mereyaProductTwoId) ){
						$mereyaProductTwoImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaProductTwoId ), 'full' );
						$mereyaProductTwoImage = $mereyaProductTwoImage[0];
					}				
					
				}			
				
			?>
			<div class="bizthree-item">
			
				<div class="bizthree-image">
					
					<?php 

						if( '' != $mereyaProductTwoImage ){
							echo '<a href="' . esc_url($mereyaProductTwoUrl) . '"><img src="' . esc_url($mereyaProductTwoImage) . '" /></a>';
						}else{
							echo '<img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/service.jpg" />';
						}
							
					?>				
					
				</div><!-- .bizthree-image -->
				
				<div class="bizthree-content">
				
					<h3><a href="<?php echo esc_url($mereyaProductTwoUrl); ?>"><?php echo esc_html($mereyaProductTwoTitle); ?></a></h3>
					<div>
					<?php 
					
						if( '' != $mereyaProductTwoDesc ){
							
							echo esc_html($mereyaProductTwoDesc);
							
						}else{
							
							echo esc_html($mereyaProductTwoContent);
							
						}
					
					?>		
					</div>	
				
				</div><!-- .bizthree-content -->		
			
			</div><!-- .bizthree-item -->
			<?php endif; ?>

			<?php 
			
				if( '' != get_theme_mod('mereya_three_products_three') && 'select' != get_theme_mod('mereya_three_products_three') ):
				
				$mereyaProductThreeTitle = '';
				$mereyaProductThreeDesc = '';
				$mereyaProductThreeUrl = '';
				$mereyaProductThreeImage = '';			
				
				$mereyaProductThreeId = get_theme_mod('mereya_three_products_three');
				
				if( ctype_alnum($mereyaProductThreeId) ){

					$mereyaProductThree = get_post( $mereyaProductThreeId );
				
					$mereyaProductThreeTitle = $mereyaProductThree->post_title;
					$mereyaProductThreeDesc = $mereyaProductThree->post_excerpt;
					$mereyaProductThreeContent = mereya_limitedstring($mereyaProductThree->post_content, 150);
					$mereyaProductThreeUrl = get_permalink( $mereyaProductThreeId );
					
					if( has_post_thumbnail($mereyaProductThreeId) ){
						$mereyaProductThreeImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaProductThreeId ), 'full' );
						$mereyaProductThreeImage = $mereyaProductThreeImage[0];
					}				
					
				}			
				
			?>
			<div class="bizthree-item">
			
				<div class="bizthree-image">
					
					<?php 

						if( '' != $mereyaProductThreeImage ){
							echo '<a href="' . esc_url($mereyaProductThreeUrl) . '"><img src="' . esc_url($mereyaProductThreeImage) . '" /></a>';
						}else{
							echo '<img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/service.jpg" />';
						}
							
					?>				
					
				</div><!-- .bizthree-image -->
				
				<div class="bizthree-content">
				
					<h3><a href="<?php echo esc_url($mereyaProductThreeUrl); ?>"><?php echo esc_html($mereyaProductThreeTitle); ?></a></h3>
					<div>
					<?php 
					
						if( '' != $mereyaProductThreeDesc ){
							
							echo esc_html($mereyaProductThreeDesc);
							
						}else{
							
							echo esc_html($mereyaProductThreeContent);
							
						}
					
					?>		
					</div>	
				
				</div><!-- .bizthree-content -->		
			
			</div><!-- .bizthree-item -->
			<?php endif; ?>		
		
		</div><!-- .bizthree-items -->
	
	</div><!-- .front-three-services -->
	
	<div class="front-three-portfolio">
	
		<h3><?php echo __( 'Portfolio', 'mereya' ); ?></h3>
		
		<div class="front-three-portfolio-content">
		
			<?php
				
				if( '' != get_theme_mod('mereya_three_portfolio_one') && 'select' != get_theme_mod('mereya_three_portfolio_one') ):
				
				$mereyaPortfolioOneTitle = '';
				$mereyaPortfolioOneImage = '';			
				
				$mereyaPortfolioOneId = get_theme_mod('mereya_three_portfolio_one');
				
				if( ctype_alnum($mereyaPortfolioOneId) ){

					$mereyaProductOne = get_post( $mereyaPortfolioOneId );
				
					$mereyaPortfolioOneTitle = $mereyaProductOne->post_title;
					$mereyaPortfolioOneUrl = get_permalink( $mereyaProductOneId );
					
					if( has_post_thumbnail($mereyaPortfolioOneId) ){
						$mereyaPortfolioOneImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaPortfolioOneId ), 'full' );
						$mereyaPortfolioOneImage = $mereyaPortfolioOneImage[0];
					}				
					
				}			
				
			?>

			<div class="front-three-portfolio-item">
			
				<?php
					
					if( '' != $mereyaPortfolioOneImage ){
						
						echo '<img src="' . esc_url( $mereyaPortfolioOneImage ) . '" />';
						
					}else{
						
						echo '<img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/service.jpg" />';
						
					}
					
				?>
				<p><?php echo esc_html($mereyaPortfolioOneTitle); ?></p>
			
			</div><!-- .front-three-portfolio-item -->
			
			<?php endif; ?>	
			
			<?php
				
				if( '' != get_theme_mod('mereya_three_portfolio_two') && 'select' != get_theme_mod('mereya_three_portfolio_two') ):
				
				$mereyaPortfolioTwoTitle = '';
				$mereyaPortfolioTwoImage = '';			
				
				$mereyaPortfolioTwoId = get_theme_mod('mereya_three_portfolio_two');
				
				if( ctype_alnum($mereyaPortfolioTwoId) ){

					$mereyaProductTwo = get_post( $mereyaPortfolioTwoId );
				
					$mereyaPortfolioTwoTitle = $mereyaProductTwo->post_title;
					$mereyaPortfolioTwoUrl = get_permalink( $mereyaProductTwoId );
					
					if( has_post_thumbnail($mereyaPortfolioTwoId) ){
						$mereyaPortfolioTwoImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaPortfolioTwoId ), 'full' );
						$mereyaPortfolioTwoImage = $mereyaPortfolioTwoImage[0];
					}				
					
				}			
				
			?>

			<div class="front-three-portfolio-item">
			
				<?php
					
					if( '' != $mereyaPortfolioTwoImage ){
						
						echo '<img src="' . esc_url( $mereyaPortfolioTwoImage ) . '" />';
						
					}else{
						
						echo '<img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/service.jpg" />';
						
					}
					
				?>
				<p><?php echo esc_html($mereyaPortfolioTwoTitle); ?></p>
			
			</div><!-- .front-three-portfolio-item -->
			
			<?php endif; ?>	

			<?php
				
				if( '' != get_theme_mod('mereya_three_portfolio_three') && 'select' != get_theme_mod('mereya_three_portfolio_three') ):
				
				$mereyaPortfolioThreeTitle = '';
				$mereyaPortfolioThreeImage = '';			
				
				$mereyaPortfolioThreeId = get_theme_mod('mereya_three_portfolio_three');
				
				if( ctype_alnum($mereyaPortfolioThreeId) ){

					$mereyaProductThree = get_post( $mereyaPortfolioThreeId );
				
					$mereyaPortfolioThreeTitle = $mereyaProductThree->post_title;
					$mereyaPortfolioThreeUrl = get_permalink( $mereyaProductThreeId );
					
					if( has_post_thumbnail($mereyaPortfolioThreeId) ){
						$mereyaPortfolioThreeImage = wp_get_attachment_image_src( get_post_thumbnail_id( $mereyaPortfolioThreeId ), 'full' );
						$mereyaPortfolioThreeImage = $mereyaPortfolioThreeImage[0];
					}				
					
				}			
				
			?>

			<div class="front-three-portfolio-item">
			
				<?php
					
					if( '' != $mereyaPortfolioThreeImage ){
						
						echo '<img src="' . esc_url( $mereyaPortfolioThreeImage ) . '" />';
						
					}else{
						
						echo '<img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/service.jpg" />';
						
					}
					
				?>
				<p><?php echo esc_html($mereyaPortfolioThreeTitle); ?></p>
			
			</div><!-- .front-three-portfolio-item -->
			
			<?php endif; ?>				
		
		</div><!-- .front-three-portfolio-content -->
	
	</div><!-- .front-three-portfolio -->

</div><!-- .front-three-container -->