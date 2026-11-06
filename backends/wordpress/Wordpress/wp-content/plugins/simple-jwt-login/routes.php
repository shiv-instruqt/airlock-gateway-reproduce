<?php

use SimpleJWTLogin\Modules\RouteService;
use SimpleJWTLogin\Modules\SimpleJWTLoginService;
use SimpleJWTLogin\Modules\SimpleJWTLoginSettings;
use SimpleJWTLogin\Modules\WordPressData;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once( ABSPATH . 'wp-admin/includes/user.php' );

add_action( 'rest_api_init', function () {
	$jwtService = new SimpleJWTLoginService();
	$jwtService->withSettings( new SimpleJWTLoginSettings( new WordPressData() ) );
	$routeService    = new RouteService();
	$availableRoutes = $routeService->getAllRoutes();

	foreach ( $availableRoutes as $route ) {
		register_rest_route( $jwtService->getRouteNamespace(), $route['name'], [
				'methods'  => $route['method'],
				'callback' => function ( $request ) use ( $route, $routeService, $jwtService ) {
					try {
						$jwtService->withRequest( $request );
						$routeService->withService( $jwtService );

						return $routeService->makeAction( $route['name'], $route['method'] );
					} catch ( Exception $e ) {
						@header( 'Content-Type: application/json; charset=UTF-8' );
						wp_send_json_error( [
							'message'   => $e->getMessage(),
							'errorCode' => $e->getCode()
						],
							400
						);

						return false;
					}
				}
			]
		);
	}
} );
