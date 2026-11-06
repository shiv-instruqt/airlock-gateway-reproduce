<?php


namespace SimpleJWTLogin\Modules;


use WP_REST_Response;

class WordPressData implements WordPressDataInterface {
	/**
	 * @param int $userID
	 *
	 * @return bool|\WP_User
	 */
	public function getUserDetailsById( $userID ) {
		return get_userdata( (int) $userID );
	}

	/**
	 * @param string $emailAddress
	 *
	 * @return bool|\WP_User
	 */
	public function getUserDetailsByEmail( $emailAddress ) {
		return get_user_by_email( $emailAddress );
	}

	/**
	 * @param \WP_User $user
	 */
	public function loginUser( $user ) {
		wp_set_current_user( $user->get( 'id' ) );
		wp_set_auth_cookie( $user->get( 'id' ) );

		do_action( 'wp_login', $user->user_login, $user );
	}

	/**
	 * @param string $url
	 */
	public function redirect( $url ) {
		wp_redirect( $url );
		exit;
	}

	/**
	 * @return string|void
	 */
	public function getAdminUrl() {
		return admin_url();
	}

	/**
	 * @return string|void
	 */
	public function getSiteUrl() {
		return site_url();
	}

	/**
	 * @param string $email
	 *
	 * @return bool
	 */
	public function checkUserExistsByEmail( $email ) {
		return username_exists( $email ) || email_exists( $email );
	}

	/**
	 * @param string $email
	 * @param string $password
	 * @param string $role
	 *
	 * @return int|\WP_Error
	 */
	public function createUser( $email, $password, $role ) {
		$userId = wp_create_user( $email, $password, $email );
		$user   = new \WP_User( $userId );
		$user->set_role( $role );

		return $userId;
	}

	/**
	 * @param string $option
	 *
	 * @return mixed|void
	 */
	public function getOptionFromDatabase( $option ) {
		return get_option( $option );
	}

	/**
	 * @param $email
	 *
	 * @return bool
	 */
	public function is_email( $email ) {
		return (bool) is_email( $email );
	}

	/**
	 * @param string $optionName
	 * @param string $value
	 */
	public function add_option( $optionName, $value ) {
		add_option( $optionName, $value );
	}

	/**
	 * @param string $optionName
	 * @param string $value
	 */
	public function update_option( $optionName, $value ) {
		update_option( $optionName, $value );
	}

	/**
	 * @param array $responseJson
	 *
	 * @return WP_REST_Response
	 */
	public function createResponse( $responseJson ) {
		$response = new WP_REST_Response( $responseJson );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public function sanitize_text_field( $text ) {
		return sanitize_text_field( $text );
	}

	/**
	 * @param \WP_User $user
	 *
	 * @return bool|int
	 */
	public function deleteUser( $user ) {
		$userId = $user->get( 'id' );
		$return = wp_delete_user( $userId );

		return $return === false
			? $return
			: $userId;
	}
}
