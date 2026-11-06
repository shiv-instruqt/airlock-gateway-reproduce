<?php

namespace SimpleJWTLogin\Modules;

class SimpleJWTLoginSettings {
	const OPTIONS_KEY = 'simple_jwt_login_settings';
	const DEFAULT_AUTH_CODE_KEY = 'AUTH_KEY';
	const DEFAULT_USER_PROFILE = 'subscriber';

	const REDIRECT_DASHBOARD = 0;
	const REDIRECT_HOMEPAGE = 1;
	const REDIRECT_CUSTOM = 9;

	const JWT_LOGIN_BY_EMAIL = 0;
	const JWT_LOGIN_BY_WORDPRESS_USER_ID = 1;

	const SETTINGS_TYPE_INT = 0;
	const SETTINGS_TYPE_BOL = 1;
	const SETTINGS_TYPE_STRING = 2;

	const DELETE_USER_BY_EMAIL = 0;
	const DELETE_USER_BY_ID = 1;
	const DEFAULT_ROUTE_NAMESPACE = 'simple-jwt-login/v1/';

	/**
	 * null|array
	 */
	private $settings = null;

	/**
	 * @var array
	 */
	private $post;
	/**
	 * @var WordPressDataInterface
	 */
	private $wordPressData;

	private $needUpdateOnOptions = false;

	/**
	 * SimpleJWTLoginSettings constructor.
	 *
	 * @param WordPressDataInterface $wordPressData
	 */
	public function __construct( WordPressDataInterface $wordPressData ) {
		$this->wordPressData       = $wordPressData;
		$this->settings            = json_decode(
			$this->wordPressData->getOptionFromDatabase( self::OPTIONS_KEY ),
			true
		);
		$this->needUpdateOnOptions = empty( $this->settings )
			? false
			: true;

		$this->post = [];
	}

	/**
	 * @return WordPressDataInterface
	 */
	public function getWordPressData() {
		return $this->wordPressData;
	}

	/**
	 * This function makes sure that when save is pressed, all the data is saved
	 *
	 * @param array $post
	 *
	 * @return bool|void
	 * @throws \Exception
	 */
	public function watchForUpdates( $post ) {
		if ( empty( $post ) ) {
			return false;
		}
		$this->post = $post;
		$this->initGeneralSettingsFromPost();
		$this->initLoginConfigFromPost();
		$this->initRegisterConfigFromPost();
		$this->initAuthCodesFromPost();
		$this->initDeleteUserConfigFromPost();
		$this->saveSettingsInDatabase();

		return true;
	}

	private function initGeneralSettingsFromPost() {
		$this->assignSettingsPropertyFromPost( 'route_namespace', 'route_namespace', self::SETTINGS_TYPE_STRING );
		$this->assignSettingsPropertyFromPost( 'decryption_key', 'decryption_key', self::SETTINGS_TYPE_STRING );
		$this->assignSettingsPropertyFromPost( 'jwt_algorithm', 'jwt_algorithm', self::SETTINGS_TYPE_STRING );
	}

	/**
	 * @throws \Exception
	 */
	private function validateGeneralSettingsFromPost() {
		if ( ! isset( $this->post['route_namespace'] ) || isset( $this->post['route_namespace'] ) && empty( trim( $this->post['route_namespace'],
				' /' ) ) ) {
			throw new \Exception( __( 'Route namespace could not be empty.', 'simple-jwt-login' ) );
		}
		if ( ! isset( $this->post['decryption_key'] ) || empty( trim( $this->post['decryption_key'] ) ) ) {
			throw  new \Exception( __( 'JWT Decryption key is required.', 'simple-jwt-login' ) );
		}
		if ( ! isset( $this->post['jwt_login_by_parameter'] ) || empty( trim( $this->post['jwt_login_by_parameter'] ) ) ) {
			throw  new \Exception( __( 'JWT Parameter key is required.', 'simple-jwt-login' ) );
		}
	}

	private function initAuthCodesFromPost() {
		if ( isset( $this->post['auth_codes'] ) ) {
			$authCodes = $this->post['auth_codes'];
			foreach ( $authCodes as $key => $code ) {
				if ( trim( $code ) === '' ) {
					unset( $authCodes[ $key ] );
				}
			}
			$this->settings['auth_codes'] = $authCodes;
		} else {
			$this->settings['auth_codes'] = [];
		}

		$this->assignSettingsPropertyFromPost( 'auth_code_key', 'auth_code_key', self::SETTINGS_TYPE_STRING );
	}

	/**
	 * @throws \Exception
	 */
	private function validateAuthCodesFromPost() {
		if ( ! empty( $this->settings['require_login_auth'] ) || ! empty( $this->settings['require_register_auth'] ) || ! empty( $this->settings['require_delete_auth'] ) ) {
			if ( empty( $this->settings['auth_codes'] ) ) {
				throw new \Exception(
					__( 'Missing Auth Codes. Please add at least one Auth Code.', 'simple-jwt-login' )
				);
			}
		}
	}

	private function initLoginConfigFromPost() {
		$this->assignSettingsPropertyFromPost( 'jwt_login_by', 'jwt_login_by', self::SETTINGS_TYPE_INT );
		$this->assignSettingsPropertyFromPost( 'jwt_login_by_parameter', 'jwt_login_by_parameter',
			self::SETTINGS_TYPE_STRING );
		$this->assignSettingsPropertyFromPost( 'allow_autologin', 'allow_autologin', self::SETTINGS_TYPE_BOL );
		if ( isset( $this->post['redirect'] ) ) {
			$this->settings['redirect']     = (int) $this->post['redirect'];
			$this->settings['redirect_url'] = (int) $this->post['redirect'] === self::REDIRECT_CUSTOM && isset( $this->post['redirect_url'] )
				? (string) $this->post['redirect_url']
				: '';
		}

		$this->assignSettingsPropertyFromPost( 'require_login_auth', 'require_login_auth', self::SETTINGS_TYPE_BOL );
	}

	/**
	 * @throws \Exception
	 */
	private function validateLoginConfigFromPost() {
		if ( isset( $this->post['redirect'] ) && (int) $this->post['redirect'] === self::REDIRECT_CUSTOM &&
		     (
			     empty( $this->post['redirect_url'] )
			     || ! filter_var( $this->post['redirect_url'], FILTER_VALIDATE_URL )
		     )
		) {
			throw new \Exception( __( 'Invalid custom URL provided', 'simple-jwt-login' ) );
		}
	}

	private function initRegisterConfigFromPost() {
		$this->assignSettingsPropertyFromPost( 'allow_register', 'allow_register', self::SETTINGS_TYPE_BOL );
		$this->assignSettingsPropertyFromPost( 'new_user_profile', 'new_user_profile', self::SETTINGS_TYPE_STRING );
		$this->assignSettingsPropertyFromPost( 'login_ip', 'login_ip', self::SETTINGS_TYPE_STRING );
		$this->assignSettingsPropertyFromPost( 'register_ip', 'register_ip', self::SETTINGS_TYPE_STRING );
		$this->assignSettingsPropertyFromPost( 'register_domain', 'register_domain', self::SETTINGS_TYPE_STRING );
		$this->assignSettingsPropertyFromPost( 'require_register_auth', 'require_register_auth',
			self::SETTINGS_TYPE_BOL );
	}

	/**
	 * @param string $settingsProperty
	 * @param string $postKey
	 * @param string $type
	 */
	private function assignSettingsPropertyFromPost( $settingsProperty, $postKey, $type = null ) {
		if ( isset( $this->post[ $postKey ] ) ) {
			switch ( $type ) {
				case self::SETTINGS_TYPE_INT:
					$value = intval( $this->post[ $postKey ] );
					break;
				case self::SETTINGS_TYPE_BOL:
					$value = (bool) $this->post[ $postKey ];
					break;
				case self::SETTINGS_TYPE_STRING:
					$value = $this->wordPressData->sanitize_text_field( $this->post[ $postKey ] );
					break;
				default:
					$value = $this->post[ $postKey ];
					break;
			}
			$this->settings[ $settingsProperty ] = $value;
		}
	}

	/**
	 * Save Data
	 * @throws \Exception
	 */
	private function saveSettingsInDatabase() {
		$this->validateGeneralSettingsFromPost();
		$this->validateAuthCodesFromPost();
		$this->validateLoginConfigFromPost();
		$this->validateDeleteUserConfigFromPost();

		if ( $this->needUpdateOnOptions ) {
			$this->wordPressData->update_option( self::OPTIONS_KEY, json_encode( $this->settings ) );
		} else {
			$this->wordPressData->add_option( self::OPTIONS_KEY, json_encode( $this->settings ) );
		}
	}

	/**
	 * @return bool
	 */
	public function getAllowAutologin() {
		return ! empty( $this->settings['allow_autologin'] )
			? true
			: false;
	}

	/**
	 * @return bool
	 */
	public function getAllowRegister() {
		return ! empty( $this->settings['allow_register'] )
			? true
			: false;
	}

	/**
	 * @return string
	 */
	public function getDecryptionKey() {
		return isset( $this->settings['decryption_key'] )
			? $this->settings['decryption_key']
			: '';
	}

	/**
	 * @return int
	 */
	public function getJWTLoginBy() {
		return isset( $this->settings['jwt_login_by'] )
			? (int) $this->settings['jwt_login_by']
			: self::JWT_LOGIN_BY_EMAIL;
	}

	/**
	 * @return string
	 * @since v.1.2.0
	 */
	public function getJwtLoginByParameter() {
		return isset( $this->settings['jwt_login_by_parameter'] )
			? $this->settings['jwt_login_by_parameter']
			: $this->getOldVersionJWTEmailParameter();
	}

	/**
	 * Return JWT parameter for old version plugins
	 * @return string
	 * @deprecated  since v 1.2.0
	 */
	private function getOldVersionJWTEmailParameter() {
		return isset( $this->settings['jwt_email_parameter'] )
			? $this->settings['jwt_email_parameter']
			: '';
	}

	/**
	 * @return string
	 */
	public function getJWTDecryptAlgorithm() {
		return isset( $this->settings['jwt_algorithm'] )
			? $this->settings['jwt_algorithm']
			: 'HS256';
	}

	/**
	 * @return array
	 */
	public function getAuthCodes() {
		return isset( $this->settings['auth_codes'] )
			? $this->settings['auth_codes']
			: [];
	}

	/**
	 * @return string
	 */
	public function getAuthCodeKey() {
		return ! empty( $this->settings['auth_code_key'] )
			? $this->settings['auth_code_key']
			: self::DEFAULT_AUTH_CODE_KEY;
	}

	/**
	 * @return string
	 */
	public function getNewUSerProfile() {
		return isset( $this->settings['new_user_profile'] )
			? $this->settings['new_user_profile']
			: self::DEFAULT_USER_PROFILE;
	}

	/**
	 * @return int
	 */
	public function getRedirect() {
		return isset( $this->settings['redirect'] )
			? (int) $this->settings['redirect']
			: self::REDIRECT_DASHBOARD;
	}

	/**
	 * @return string
	 */
	public function getCustomRedirectURL() {
		return isset( $this->settings['redirect_url'] )
			? $this->settings['redirect_url']
			: '';
	}

	/**
	 * @return string
	 */
	public function getAllowedLoginIps() {
		return isset( $this->settings['login_ip'] )
			? $this->settings['login_ip']
			: '';
	}

	/**
	 * @return string
	 */
	public function getAllowedRegisterIps() {
		return isset( $this->settings['register_ip'] )
			? $this->settings['register_ip']
			: '';
	}

	/**
	 * @return string
	 */
	public function getAllowedRegisterDomain() {
		return isset( $this->settings['register_domain'] )
			? $this->settings['register_domain']
			: '';
	}

	/**
	 * @return bool
	 */
	public function getRequireLoginAuthKey() {
		return isset ( $this->settings['require_login_auth'] )
			? (bool) $this->settings['require_login_auth']
			: true;
	}

	/**
	 * @return bool
	 */
	public function getRequireRegisterAuthKey() {
		return isset ( $this->settings['require_register_auth'] )
			? (bool) $this->settings['require_register_auth']
			: true;
	}

	/**
	 * @param string $route
	 * @param array  $params
	 *
	 * @return string
	 */
	public function generateExampleLink( $route, $params ) {
		$url = $this->wordPressData->getSiteUrl() . '/?rest_route=/' . $this->getRouteNamespace() . $route;
		foreach ( $params as $key => $value ) {
			$url .= sprintf( '&amp;%s=<b>%s</b>', $key, $value );
		}

		return $url;
	}

	public function getAllowDelete() {
		return isset( $this->settings['allow_delete'] )
			? (bool) $this->settings['allow_delete']
			: false;
	}

	public function getRequireDeleteAuthKey() {
		return isset( $this->settings['require_delete_auth'] )
			? (bool) $this->settings['require_delete_auth']
			: true;
	}

	public function getAllowedDeleteIps() {
		return isset( $this->settings['delete_ip'] )
			? $this->settings['delete_ip']
			: '';
	}

	public function getDeleteUserBy() {
		return isset( $this->settings['delete_user_by'] )
			? (int) $this->settings['delete_user_by']
			: self::DELETE_USER_BY_EMAIL;
	}

	public function getJwtDeleteByParameter() {
		return isset( $this->settings['jwt_delete_by_parameter'] )
			? $this->settings['jwt_delete_by_parameter']
			: '';
	}

	private function initDeleteUserConfigFromPost() {
		$this->assignSettingsPropertyFromPost( 'allow_delete', 'allow_delete', self::SETTINGS_TYPE_BOL );
		$this->assignSettingsPropertyFromPost( 'require_delete_auth', 'require_delete_auth', self::SETTINGS_TYPE_BOL );
		$this->assignSettingsPropertyFromPost( 'delete_ip', 'delete_ip', self::SETTINGS_TYPE_STRING );
		$this->assignSettingsPropertyFromPost( 'delete_user_by', 'delete_user_by', self::SETTINGS_TYPE_INT );
		$this->assignSettingsPropertyFromPost( 'jwt_delete_by_parameter', 'jwt_delete_by_parameter',
			self::SETTINGS_TYPE_STRING );
	}

	/**
	 * @throws \Exception
	 */
	private function validateDeleteUserConfigFromPost() {
		if ( ! empty( $this->post['allow_delete'] ) && ( ! isset( $this->post['jwt_delete_by_parameter'] ) || empty( trim( $this->post['jwt_delete_by_parameter'] ) ) ) ) {
			throw new \Exception( __( 'Missing JWT parameter for Delete User.', 'simple-jwt-login' ) );
		}
	}

	public function getRouteNamespace() {
		$return = isset( $this->settings['route_namespace'] )
			? $this->settings['route_namespace']
			: self::DEFAULT_ROUTE_NAMESPACE;

		return rtrim( ltrim( $return, '/' ), '/' ) . '/';
	}
}
