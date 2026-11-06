<div class="frontPageContainer">
	
	<div class="frontPageServices">
		
		<div class="frontPageWelcome">
		
			<?php
			
				$mereyaWelcomePostTitle = '';
				$mereyaWelcomePostDesc = '';
				$mereyaWelcomePostContent = '';

				if( '' != get_theme_mod('mereya_welcome_post') && 'select' != get_theme_mod('mereya_welcome_post') ){

					$mereyaWelcomePostId = get_theme_mod('mereya_welcome_post');

					if( ctype_alnum($mereyaWelcomePostId) ){

						$mereyaWelcomePost = get_post( $mereyaWelcomePostId );

						$mereyaWelcomePostTitle = $mereyaWelcomePost->post_title;
						$mereyaWelcomePostDesc = $mereyaWelcomePost->post_excerpt;
						$mereyaWelcomePostContent = $mereyaWelcomePost->post_content;

					}

				}			
			
			?>
			
			<h1><?php echo esc_html($mereyaWelcomePostTitle); ?></h1>
			<div class="frontWelcomeContent">
				<p>
					<?php 
					
						if( '' != $mereyaWelcomePostDesc ){
							
							echo esc_html($mereyaWelcomePostDesc);
							
						}else{
							
							echo esc_html($mereyaWelcomePostContent);
							
						}
					
					?>
				</p>
			</div><!-- .frontWelcomeContent -->			
			
		</div><!-- .frontPageWelcome -->
		
		<div class="frontPageServiceItems">
			
			<?php

				$mereya_left_cat = '';

				if(get_theme_mod('mereya_services_cat')){
					$mereya_left_cat = get_theme_mod('mereya_services_cat');
					$mereya_left_cat_num = -1;
				}else{
					$mereya_left_cat = 0;
					$mereya_left_cat_num = 5;
				}		

				$mereya_left_args = array(
				   // Change these category SLUGS to suit your use.
				   'ignore_sticky_posts' => 1,
				   'post_type' => array('post'),
				   'posts_per_page'=> $mereya_left_cat_num,
				   'cat' => $mereya_left_cat
				);

				$mereya_left = new WP_Query($mereya_left_args);		

				if ( $mereya_left->have_posts() ) : while ( $mereya_left->have_posts() ) : $mereya_left->the_post();
   			?> 			
			<div class="frontPageServiceItem">
				
				<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail('mereya-home-posts');
						}else{
							echo '<img src="' . esc_url( esc_url( get_template_directory_uri() ) ) . '/assets/images/frontitemimage.png" />';
						}						
				?>
				<?php the_title( '<h3><a href="' . esc_url( get_permalink() ) . '">', '</a></h3>' ); ?>
				<p>
					<?php  
						
						//$frontPostExcerpt = '';
						//$frontPostExcerpt = get_the_excerpt();
					
						if( has_excerpt() ){
							echo esc_html(get_the_excerpt());
						}else{
							echo esc_html(mereya_limitedstring(get_the_content(), 50));
						}
					
					?>
				</p>				
				
			</div><!-- .frontPageServiceItem -->
			<?php endwhile; wp_reset_postdata(); endif;?>
			
		</div><!-- .frontPageServiceItems -->
		
	</div><!-- .frontPageServices -->
	
	<div class="frontPagePortfolio">
		
		<h1><?php _e('Portfolio', 'mereya'); ?></h1>
		
		<div class="frontPagePortfolioItems">
			
			<?php

				$mereya_portfolio_cat = '';

				if(get_theme_mod('mereya_portfolio_cat')){
					$mereya_portfolio_cat = get_theme_mod('mereya_portfolio_cat');
					$mereya_portfolio_cat_num = -1;
				}else{
					$mereya_portfolio_cat = 0;
					$mereya_portfolio_cat_num = 5;
				}		

				$mereya_portfolio_args = array(
				   // Change these category SLUGS to suit your use.
				   'ignore_sticky_posts' => 1,
				   'post_type' => array('post'),
				   'posts_per_page'=> $mereya_portfolio_cat_num,
				   'cat' => $mereya_portfolio_cat
				);

				$mereya_portfolio = new WP_Query($mereya_portfolio_args);		

				if ( $mereya_portfolio->have_posts() ) : while ( $mereya_portfolio->have_posts() ) : $mereya_portfolio->the_post();
   			?>			
			<div class="frontPagePortfolioItem">
				
				<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail();
						}else{
							echo '<img src="' . esc_url( esc_url( get_template_directory_uri() ) ) . '/assets/images/service.jpg" />';
						}						
				?>
				<?php the_title( '<h3>', '</h3>' ); ?>				
				
			</div><!-- .frontPagePortfolioItem -->
			<?php endwhile; wp_reset_postdata(); endif;?>
			
		</div><!-- .frontPagePortfolioItems -->
		
	</div><!-- .frontPagePortfolio -->
	
	<div class="frontPageNews">
		
			<h1><?php _e('News', 'mereya'); ?></h1>
			
			<?php

				$mereya_right_cat = '';

				if(get_theme_mod('mereya_news_cat')){
					$mereya_right_cat = get_theme_mod('mereya_news_cat');
				}else{
					$mereya_right_cat = 0;
				}		

				$mereya_right_args = array(
				   // Change these category SLUGS to suit your use.
				   'ignore_sticky_posts' => 1,
				   'post_type' => array('post'),
				   'posts_per_page'=> 4,
				   'cat' => $mereya_right_cat
				);

				$mereya_right = new WP_Query($mereya_right_args);		

				if ( $mereya_right->have_posts() ) : while ( $mereya_right->have_posts() ) : $mereya_right->the_post();
   			?> 			
			<div class="frontNewsItem">
				
				<?php the_title( '<h3>', '</h3>' ); ?>
				<p>
					<?php  
						
						//$frontPostExcerpt = '';
						//$frontPostExcerpt = get_the_excerpt();
					
						if( has_excerpt() ){
							echo esc_html(get_the_excerpt());
						}else{
							echo esc_html(mereya_limitedstring(get_the_content(), 100));
						}
					
					?>				
				</p>
				<p class="readmore"><a href="<?php echo esc_url(get_the_permalink()); ?>">Read More</a></p>
				
			</div><!-- .frontNewsItem -->
			<?php endwhile; wp_reset_postdata(); endif;?>			
		
	</div><!-- .frontPageNews -->	
	
</div><!-- .frontPageContainer -->	