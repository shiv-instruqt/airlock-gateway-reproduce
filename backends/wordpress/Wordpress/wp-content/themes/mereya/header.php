<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package mereya
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'mereya' ); ?></a>
	
<div class="outerContainer">

	<header id="masthead" class="site-header">
		
		<div class="responsive-container">
		
			<div class="site-branding">
				
				<?php
				
					$custom_logo_id = get_theme_mod( 'custom_logo' );
					$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
					if ( has_custom_logo() ) {
							echo '<a href="' . esc_url( home_url( '/' ) ) . '"><img src="'. esc_url( $logo[0] ) .'"></a>';
					} else {
							echo '<h1>'. esc_html(get_bloginfo( 'name' )) .'</h1>';
							$mereya_description = get_bloginfo( 'description', 'display' );
							if ( $mereya_description || is_customize_preview() ){
								echo '<p class="site-description">' . esc_html($mereya_description) . '</p>';
							}
					}			
				
				?>
				
				<span class="menu-button"></span>

			</div><!-- .site-branding -->

			<nav id="site-navigation" class="main-navigation">
				
				<div class="menu-container">
				
					<?php
					wp_nav_menu( array(
						'theme_location' => 'menu-1',
						'menu_id'        => 'primary-menu',
						'depth' => 1,
						'container_class' => 'mereya-menu',
					) );
					?>
				
				</div><!-- .menu-container -->

			</nav><!-- #site-navigation -->
		
		</div>
		
	</header><!-- #masthead -->
	
	<?php 

		if( is_front_page() && 'no' != get_theme_mod('mereya_show_slider') ){
			
			if( 'header-one' == get_theme_mod( 'header_type' ) ){
				
				get_template_part( 'template-parts/header/header-one' );
				
			}else{
				
				get_template_part( 'template-parts/header/wp-header' );
				
			}
			
		}

	?>

	<div id="content" class="site-content">
