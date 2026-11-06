<?php

$mereyaPostsPagesArray = array(
	'select' => __('Select a post/page', 'mereya'),
);

$mereyaPostsPagesArgs = array(
	
	// Change these category SLUGS to suit your use.
	'ignore_sticky_posts' => 1,
	'post_type' => array('post', 'page'),
	'orderby' => 'date',
	'posts_per_page' => -1,
	'post_status' => 'publish',
	
);
$mereyaPostsPagesQuery = new WP_Query( $mereyaPostsPagesArgs );
	
if ( $mereyaPostsPagesQuery->have_posts() ) :
							
	while ( $mereyaPostsPagesQuery->have_posts() ) : $mereyaPostsPagesQuery->the_post();
			
		$mereyaPostsPagesId = get_the_ID();
		if(get_the_title() != ''){
				$mereyaPostsPagesTitle = get_the_title();
		}else{
				$mereyaPostsPagesTitle = get_the_ID();
		}
		$mereyaPostsPagesArray[$mereyaPostsPagesId] = $mereyaPostsPagesTitle;
	   
	endwhile; wp_reset_postdata();
							
endif;

$mereyaYesNo = array(
	'select' => __('Select', 'mereya'),
	'yes' => __('Yes', 'mereya'),
	'no' => __('No', 'mereya'),
);

$mereyaSliderType = array(
	'select' => __('Select', 'mereya'),
	'header' => __('WP Custom Header', 'mereya'),
	'header-one' => __('mereya Header', 'mereya'),
);

$mereyaServiceLayouts = array(
	'select' => __('Select', 'mereya'),
	'one' => __('One', 'mereya'),
	'two' => __('Two', 'mereya'),
);

$mereyaAvailableCats = array( 'select' => __('Select', 'mereya') );

$mereya_categories_raw = get_categories( array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 0, ) );

foreach( $mereya_categories_raw as $category ){
	
	$mereyaAvailableCats[$category->term_id] = $category->name;
	
}

$mereyaBusinessLayoutType = array( 
	'select' => __('Select', 'mereya'), 
	'one' => __('One', 'mereya'), 
	'two' => __('Two', 'mereya'),
	'three' => __('Three', 'mereya'),
	'four' => __('Four', 'mereya'),
	'five' => __('Five', 'mereya'),
	'woo-one' => __('Woocommerce One', 'mereya'),
);
