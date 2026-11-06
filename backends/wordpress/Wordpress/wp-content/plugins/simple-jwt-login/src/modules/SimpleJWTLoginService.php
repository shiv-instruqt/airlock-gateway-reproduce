<?php

namespace SimpleJWTLogin\Modules;

use Exception;
use SimpleJWTLogin\ErrorCodes;
use SimpleJWTLogin\Libraries\JWT;
use SimpleJWTLogin\Modules\SimpleJWTLoginSettings;
use WP_REST_Response;
use WP_User;

class SimpleJWTLoginService {
	CONST JWT_LEEVAY = 60; //seconds

	/**
	 * @var array
	 */
	private $request;

	/**
	 * @var string
	 */
	private $jwt = '';

	/**
	 * @var SimpleJWTLoginSettings
	 */
	private $jwt_settings;
	/**
	 * @var WordPressData
	 */
	private $wordPressData;

	/**
	 * @param SimpleJWTLoginSettings $settings
	 *
	 * @return SimpleJWTLoginService
	 */
	public function withSettings( SimpleJWTLoginSettings $settings ) {
		$this->jwt_settings = $settings;

		$this->wordPressData = $settings->getWordPressData();

		return $this;
	}

	/**
	 * @param array $request
	 *
	 * @return $this
	 */
	public function withRequest( $request ) {
		$this->request = $request;

		return $this;
	}

	public function getRouteNamespace() {
		return $this->jwt_settings->getRouteNamespace();
	}

	/**
	 * @param string $userData
	 *
	 * @return bool|WP_User
	 */
	private function getUserDetails( $userData ) {
		$user = $this->jwt_settings->getJWTLoginBy() === SimpleJWTLoginSettings::JWT_LOGIN_BY_EMAIL
			? $this->wordPressData->getUserDetailsByEmail( $userData )
			: $this->wordPressData->getUserDetailsById( $userData );

		if ( $user === false ) {
			return false;
		}

		return $user;
	}

	/**
	 * @throws Exception
	 */
	public function doLogin() {
		$this->validateDoLogin();
		$login_parameter = $this->validateTokenByParameter( $this->jwt_settings->getJwtLoginByParameter() );

		$user = $this->getUserDetails( $login_parameter );
		if ( $user === false ) {
			throw new Exception(
				__( 'User not found.', 'simple-jwt-login' ),
				ErrorCodes::ERR_DO_LOGIN_USER_NOT_FOUND
			);
		}

		$this->wordPressData->loginUser( $user );
		$this->redirectAfterLogin();

	}

	/**
	 * @throws Exception
	 */
	private function validateDoLogin() {
		if ( $this->jwt_settings->getAllowAutologin() === false ) {
			throw new Exception(
				__( 'Auto-login is not enabled on this website.', 'simple-jwt-login' ),
				ErrorCodes::ERR_AUTO_LOGIN_NOT_ENABLED
			);
		}
		if ( ! isset( $this->request['jwt'] ) ) {
			throw new Exception( __( 'Wrong Request.', 'simple-jwt-login' ),
				ErrorCodes::ERR_VALIDATE_LOGIN_WRONG_REQUEST );
		}
		$this->jwt = $this->request['jwt'];
		if ( $this->jwt_settings->getRequireLoginAuthKey() && $this->validateAuthKey() === false ) {
			throw  new Exception(
				sprintf(
					__( 'Invalid Auth Code ( %s ) provided.', 'simple-jwt-login' ),
					$this->jwt_settings->getAuthCodeKey()
				),
				ErrorCodes::ERR_INVALID_AUTH_CODE_PROVIDED
			);
		}
		if ( ! empty( $this->jwt_settings->getAllowedLoginIps() ) ) {
			$client_ip = $this->getClientIP();
			if ( ! in_array(
				$client_ip,
				array_map( 'trim',
					explode( ',', $this->jwt_settings->getAllowedLoginIps()
					)
				) )
			) {
				throw new Exception(
					sprintf(
						__( 'This IP[ %s] is not allowed to auto-login.', 'simple-jwt-login' ),
						$client_ip
					),
					ErrorCodes::ERR_IP_IS_NOT_ALLOWED_TO_LOGIN
				);
			}
		}
	}

	/**
	 * Do the actual redirect after login
	 */
	private function redirectAfterLogin() {
		$redirect = $this->jwt_settings->getRedirect();
		switch ( $redirect ) {
			case SimpleJWTLoginSettings::REDIRECT_HOMEPAGE:
				$this->wordPressData->redirect( $this->wordPressData->getSiteUrl() );
				break;
			case SimpleJWTLoginSettings::REDIRECT_CUSTOM:
				$this->wordPressData->redirect( $this->jwt_settings->getCustomRedirectURL() );
				break;
			case SimpleJWTLoginSettings::REDIRECT_DASHBOARD:
			default:
				$this->wordPressData->redirect( $this->wordPressData->getAdminUrl() );
				break;
		}
	}


	/**
	 * @param $parameter
	 *
	 * @return mixed|string
	 * @throws Exception
	 */
	private function validateTokenByParameter( $parameter ) {
		JWT::$leeway = self::JWT_LEEVAY;
		$decoded     = (array) JWT::decode(
			$this->jwt,
			$this->jwt_settings->getDecryptionKey(),
			[ $this->jwt_settings->getJWTDecryptAlgorithm() ]
		);

		if ( strpos( $parameter, '.' ) !== false ) {
			$array = explode( '.', $parameter );
			foreach ( $array as $value ) {
				$decoded = (array) $decoded;
				if ( isset( $decoded[ $value ] ) ) {
					$decoded = $decoded[ $value ];
				} else {
					throw new Exception(
						sprintf(
							__( 'Unable to find user %s property in JWT.( Settings: %s )', 'simple-jwt-login' ),
							$value, $parameter
						),
						ErrorCodes::ERR_UNABLE_TO_FIND_PROPERTY_FOR_USER_IN_JWT
					);
				}
			}

			return (string) $decoded;
		}

		if ( ! isset( $decoded[ $parameter ] ) ) {
			throw new Exception( sprintf(
				__( 'Unable to find user %s property in JWT.', 'simple-jwt-login' ),
				$parameter
			),
				ErrorCodes::ERR_JWT_PARAMETER_FOR_USER_NOT_FOUND
			);
		}

		return $decoded[ $parameter ];
	}

	/**
	 * @return bool
	 */
	private function validateAuthKey() {
		$authCodeKey = $this->jwt_settings->getAuthCodeKey();
		if ( ! isset( $this->request[ $authCodeKey ] ) ) {
			return false;
		}
		foreach ( $this->jwt_settings->getAuthCodes() as $code ) {
			if ( $code === $this->request[ $authCodeKey ] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @throws Exception
	 */
	public function validateRegisterUser() {
		if ( $this->jwt_settings->getAllowRegister() === false ) {
			throw  new Exception(
				__( 'Register is not allowed.', 'simple-jwt-login' ),
				ErrorCodes::ERR_REGISTER_IS_NOT_ALLOWED
			);
		}

		if ( $this->jwt_settings->getRequireRegisterAuthKey() && $this->validateAuthKey() === false ) {
			throw  new Exception(
				sprintf(
					__( 'Invalid Auth Code ( %s ) provided.', 'simple-jwt-login' ),
					$this->jwt_settings->getAuthCodeKey()
				),
				ErrorCodes::ERR_REGISTER_INVALID_AUTH_KEY
			);
		}

		if ( ! empty( $this->jwt_settings->getAllowedRegisterIps() ) ) {
			$client_ip = $this->getClientIP();
			if ( ! in_array( $client_ip, array_map( 'trim',
					explode( ',', $this->jwt_settings->getAllowedRegisterIps() ) )
			)
			) {
				throw new Exception(
					sprintf(
						__( 'This IP[%s] is not allowed to register users.', 'simple-jwt-login' ),
						$client_ip
					),
					ErrorCodes::ERR_REGISTER_IP_IS_NOT_ALLOWED
				);
			}
		}

		if ( ! isset( $this->request['email'] ) || ! isset( $this->request['password'] ) ) {
			throw new Exception(
				__( 'Missing email or password.', 'simple-jwt-login' ),
				ErrorCodes::ERR_REGISTER_MISSING_EMAIL_OR_PASSWORD
			);
		}

		if ( $this->wordPressData->is_email( $this->request['email'] ) === false ) {
			throw  new Exception(
				__( 'Invalid email address.', 'simple-jwt-login' ),
				ErrorCodes::ERR_REGISTER_INVALID_EMAIL_ADDRESS
			);
		}

		if ( ! empty( $this->jwt_settings->getAllowedRegisterDomain() ) ) {
			$parts = explode( '@', $this->request['email'] );
			if ( ! isset( $parts[1] ) || isset( $parts[1] ) && ! in_array( $parts[1], array_map( 'trim',
					explode( ',', $this->jwt_settings->getAllowedRegisterDomain() )
				) )
			) {
				throw new Exception(
					__( 'This website does not allows users from this domain.', 'simple-jwt-login' ),
					ErrorCodes::ERR_REGISTER_DOMAIN_FOR_USER
				);
			}
		}
	}

	/**
	 * @return WP_REST_Response
	 * @throws Exception
	 */
	public function createUser() {
		if ( $this->wordPressData->checkUserExistsByEmail( $this->request['email'] ) == false ) {
			$user_id = $this->wordPressData->createUser(
				$this->request['email'],
				$this->request['password'],
				$this->jwt_settings->getNewUSerProfile()
			);

			return $this->wordPressData->createResponse( [
				'message' => __( 'User was successfully created.', 'simple-jwt-login' ),
				'id'      => $user_id
			] );
		} else {
			throw new Exception(
				__( 'User already exists.', 'simple-jwt-login' ),
				ErrorCodes::ERR_REGISTER_USER_ALREADY_EXISTS
			);
		}
	}

	/**
	 * @return string|null
	 */
	private function getClientIP() {
		$ip = null;
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) )   //check ip from share internet
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )   //to check ip is pass from proxy
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * Main Function for Delete user route
	 * @throws Exception
	 */
	public function deleteUser() {
		if ( ! isset( $this->request['jwt'] ) ) {
			throw new \Exception( __( 'The `jwt` parameter is missing.', 'simple-jwt-login' ),
				ErrorCodes::ERR_DELETE_MISSING_JWT );
		}

		if ( $this->jwt_settings->getAllowDelete() === false ) {
			throw  new Exception( __( 'Delete is not enabled.', 'simple-jwt-login' ),
				ErrorCodes::ERR_DELETE_IS_NOT_ENABLED );
		}
		if ( $this->jwt_settings->getRequireDeleteAuthKey() && ! isset( $this->request[ $this->jwt_settings->getAuthCodeKey() ] ) ) {
			throw new Exception(
				sprintf( __( 'Missing AUTH KEY ( %s ).', 'simple-jwt-login' ), $this->jwt_settings->getAuthCodeKey() ),
				ErrorCodes::ERR_DELETE_MISSING_AUTH_KEY
			);
		}

		$allowedIpsString = trim( $this->jwt_settings->getAllowedDeleteIps() );
		if ( ! empty( $allowedIpsString ) ) {
			$allowedIps = explode( ',', $allowedIpsString );
			$clientIp   = $this->getClientIP();
			if ( ! empty( $allowedIps ) && ! in_array( $clientIp, $allowedIps ) ) {
				throw new \Exception(
					sprintf( __( 'You are not allowed to delete users from this IP: %s', 'simple-jwt-login' ),
						$clientIp ),
					ErrorCodes::ERR_DELETE_INVALID_CLIENT_IP
				);
			}
		}

		$this->jwt         = $this->request['jwt'];
		$registerParameter = $this->validateTokenByParameter( $this->jwt_settings->getJwtDeleteByParameter() );
		$user              = $this->jwt_settings->getDeleteUserBy() === SimpleJWTLoginSettings::DELETE_USER_BY_ID
			? $this->wordPressData->getUserDetailsById( $registerParameter )
			: $this->wordPressData->getUserDetailsByEmail( $registerParameter );

		if ( $user === false ) {
			throw new Exception(
				__( 'User not found.', 'simple-jwt-login' ),
				ErrorCodes::ERR_DO_LOGIN_USER_NOT_FOUND
			);
		}

		$result = $this->wordPressData->deleteUser( $user );
		if ( $result === false ) {
			throw new Exception(
				__( 'User not found.', 'simple-jwt-login' ),
				ErrorCodes::ERR_DO_LOGIN_USER_NOT_FOUND
			);
		}

		return $this->wordPressData->createResponse( [
			'message' => __( 'User was successfully deleted.', 'simple-jwt-login' ),
			'id'      => $result
		] );
	}
}
