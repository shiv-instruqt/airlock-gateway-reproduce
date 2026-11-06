<?php
	
	$mereyaHeaderPostId = '';
	$mereyaHeaderPostTitle = '';
	$mereyaHeaderPostDesc = '';

	if( '' != get_theme_mod('mereya_header_one_post') && 'select' != get_theme_mod('mereya_header_one_post') ){

		$mereyaHeaderPostId = get_theme_mod('mereya_header_one_post');

		if( ctype_alnum($mereyaHeaderPostId) ){

			$mereyaHeaderPost = get_post( $mereyaHeaderPostId );

			$mereyaHeaderPostTitle = $mereyaHeaderPost->post_title;
			$mereyaHeaderPostDesc = $mereyaHeaderPost->post_excerpt;
			$mereyaHeaderPostContent = $mereyaHeaderPost->post_content;
			$mereyaHeaderPostUrl = get_the_permalink($mereyaHeaderPostId);
			
			$mereyaHeaderPostThumbnail = get_the_post_thumbnail_url($mereyaHeaderPostId,'mereya-producttwo');

		}

	}			
	
	if( '' != $mereyaHeaderPostId ):

?>

<div class="header-one-container">

	<div class="header-one-image">
		
		<?php
		
			if( '' != $mereyaHeaderPostThumbnail ){
				
				echo '<img src="' . esc_url($mereyaHeaderPostThumbnail) . '">';
				
			}
		
		?>
		
	</div><!-- .header-one-image -->
	
	<div class="header-one-content">
		
		<h2><?php echo esc_html($mereyaHeaderPostTitle); ?></h2>
		<p>
			<?php 
					
				if( '' != $mereyaHeaderPostDesc ){
							
					echo esc_html($mereyaHeaderPostDesc);
							
				}else{
							
					echo esc_html($mereyaHeaderPostContent);
							
				}
					
			?>		
		</p>
		<?php if( '' != $mereyaHeaderPostUrl ): ?>
		<p>
			<a class="readMore" href="<?php echo esc_url($mereyaHeaderPostUrl); ?>"><?php _e('Read More', 'mereya') ?></a>
		</p>
		<?php endif; ?>
		
	</div><!-- .header-one-content -->
	
</div><!-- .header-one-container -->

<?php endif; ?>