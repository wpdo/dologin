<?php
/**
 * SMS class
 *
 * @since 1.3
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class SMS extends Instance
{
	protected static $_instance;

	private $_dry_run = false;

	/**
	 * Return current usre's phone number
	 *
	 * @since 1.3
	 */
	public function current_user_phone()
	{
		$uid = get_current_user_id();
		$phone = get_user_meta( $uid, 'phone_number', true );
		return $phone;
	}

	/**
	 * Verify SMS after u+p authenticated
	 *
	 * @since  1.3
	 */
	public function authenticate_sms( $user, $username, $password )
	{
		global $wpdb;

		if ( $this->_dry_run ) {
			return $user;
		}

		if ( empty( $username ) || empty( $password ) ) {
			return $user;
		}

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		// If sms is optional and the user doesn't have phone set, bypass
		$phone = get_user_meta( $user->ID, 'phone_number', true );
		if ( ! $phone ) {
			if ( ! Conf::val( 'sms_force' ) ) {
				return $user;
			}
		}

		$error = new \WP_Error();

		// Validate dynamic code
		if ( empty( $_POST[ 'dologin-two_factor_code' ] ) ) {
			$error->add( 'sms_missing', Lang::msg( 'sms_missing' ) );
			define( 'DOLOGIN_ERR', true );
			return $error;
		}

		$tb_sms = Data::get_instance()->tb( 'sms' );

		$q = "SELECT id, code FROM $tb_sms WHERE user_id = %d AND used = 0";
		$row = $wpdb->get_row( $wpdb->prepare( $q, array( $user->ID ) ) );

		if ( $row->id ) {
			$wpdb->query( $wpdb->prepare( "UPDATE $tb_sms SET used = 1 WHERE id = %d", array( $row->id ) ) );
		}

		if ( ! $row->code || $row->code != $_POST[ 'dologin-two_factor_code' ] ) {
			$error->add( 'sms_wrong', Lang::msg( 'sms_wrong' ) );
			define( 'DOLOGIN_ERR', true );
			return $error;
		}

		return $user;
	}

	/**
	 * Send SMS
	 *
	 * @since  1.3
	 */
	public function send()
	{
		global $wpdb;

		if ( ! Conf::val( 'sms' ) ) {
			return REST::ok( array( 'bypassed' => 1 ) );
		}

		if ( empty( $_POST[ 'user' ] ) || empty( $_POST[ 'pswd' ] ) ) {
			return REST::err( 'Empty username/password' );
		}

		// Verify u & p first
		$this->_dry_run = true;
		$user = wp_authenticate( $_POST[ 'user' ], $_POST[ 'pswd' ] );
		$this->_dry_run = false;
		if ( is_wp_error( $user ) ) {
			return REST::err( $user->get_error_message() );
		}

		// Search if the user has number set in phone
		$phone = get_user_meta( $user->ID, 'phone_number', true );

		if ( ! $phone ) {
			if ( ! Conf::val( 'sms_force' ) ) {
				return REST::ok( array( 'bypassed' => 1 ) );
			}
			return REST::err( 'No phone number under this user profile' );
		}

		// Generate dynamic code
		$code = s::rrand( 4, 1 );
		$rid = s::rrand( 2, 1 );
		$ip_info = ip::geo();
		$info = "Dynamic Code:$code.(Tag:$rid) From: {$ip_info[country]}-{$ip_info[city]}.";

		$tb_sms = Data::get_instance()->tb( 'sms' );

		// Expire old ones
		$wpdb->query( $wpdb->prepare( "UPDATE $tb_sms SET used = -1 WHERE user_id = %d AND used = 0", array( $user->ID ) ) );

		// Save to db
		$s = array(
			'user_id'	=> $user->ID,
			'sms'		=> $info,
			'code'		=> $code,
			'used'		=> 0,
			'dateline'	=> time(),
		);
		$q = 'INSERT INTO ' . $tb_sms . ' ( ' . implode( ',', array_keys( $s ) ) . ' ) VALUES ( ' . implode( ',', array_fill( 0, count( $s ), '%s' ) ) . ' )' ;
		$wpdb->query( $wpdb->prepare( $q, $s ) );
		$id = $wpdb->insert_id;

		// Send
		$url = 'https://doapi.us/text?format=json';
		$data = array( 'phone' => $phone, 'content' => $info, 'app' => 'wp-' . home_url() );

		$res = wp_remote_post( $url, array( 'body' => $data, 'timeout' => 15, 'sslverify' => false ) ); // id=>xx

		if ( is_wp_error( $res ) ) {
			$error_message = $res->get_error_message();
			return REST::err( $error_message );
		}

		$res = $res[ 'body' ];

		// Update log
		$wpdb->query( $wpdb->prepare( "UPDATE $tb_sms SET res = %s WHERE id = %d", array( $res, $id ) ) );

		$res_json = json_decode( $res, true );

		// Expected response
		if ( ! empty( $res_json[ '_res' ] ) && $res_json[ '_res' ] == 'ok' ) {
			return REST::ok( array( 'info' => "Tag:$rid. Sent to ***" . substr( $phone, -4 ) . '.' ) );
		}

		if ( ! empty( $res_json[ '_msg' ] ) ) {
			return REST::err( $res_json[ '_msg' ] );
		}

		return REST::err( 'Unknown error' );
	}

}
