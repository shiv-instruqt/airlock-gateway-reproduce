// JavaScript Document
jQuery(document).ready(function() {
	
	var mereyaViewPortWidth = '',
		mereyaViewPortHeight = '';

	function mereyaViewport(){

		mereyaViewPortWidth = jQuery(window).width(),
		mereyaViewPortHeight = jQuery(window).outerHeight(true);	
		
		if( mereyaViewPortWidth > 1200 ){
			
			jQuery('.main-navigation').removeAttr('style');
			
			var mereyaSiteHeaderHeight = jQuery('.site-header').outerHeight();
			var mereyaSiteHeaderWidth = jQuery('.site-header').width();
			var mereyaSiteHeaderPadding = ( mereyaSiteHeaderWidth * 2 )/100;
			var mereyaMenuHeight = jQuery('.menu-container').height();
			
			var mereyaMenuButtonsHeight = jQuery('.site-buttons').height();
			
			var mereyaMenuPadding = ( mereyaSiteHeaderHeight - ( (mereyaSiteHeaderPadding * 2) + mereyaMenuHeight ) )/2;
			var mereyaMenuButtonsPadding = ( mereyaSiteHeaderHeight - ( (mereyaSiteHeaderPadding * 2) + mereyaMenuButtonsHeight ) )/2;
		
			
			jQuery('.menu-container').css({'padding-top':mereyaMenuPadding});
			jQuery('.site-buttons').css({'padding-top':mereyaMenuButtonsPadding});
			
			
		}else{

			jQuery('.menu-container, .site-buttons, .header-container-overlay, .site-header').removeAttr('style');

		}	
	
	}

	jQuery(window).on("resize",function(){
		
		mereyaViewport();
		
	});
	
	mereyaViewport();


	jQuery('.site-branding .menu-button').on('click', function(){
				
		if( mereyaViewPortWidth > 1200 ){

		}else{
			jQuery('.main-navigation').slideToggle();
		}				
		
				
	});	

		
	
});		