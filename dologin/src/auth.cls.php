<?php
/**
 * Login Auth class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Auth extends Instance
{
	protected static $_instance;

	private $_visitor_geo_data = array();
	private $_err_added = false;

	/**
	 * Init
	 *
	 * @since  1.0
	 * @access public
	 */
	public function init()
	{
		add_action( 'login_head', array( $this, 'login_head' ) );
		add_filter( 'authenticate', array( $this, 'authenticate' ), 2, 3 );
		add_action( 'wp_login_failed', array( $this, 'wp_login_failed' ) );

	}

	/**
	 * Login page display messages
	 *
	 * @since  1.0
	 * @access public
	 */
	public function login_head()
	{
		global $error;

		if ( $this->_err_added ) {
			return;
		}

		// check whitelist
		if ( ! $this->try_whitelist() ) {
			$error .= Lang::msg( 'not_in_whitelist' );
			return;
		}

		// check blacklist
		if ( $this->try_blacklist() ) {
			$error .= Lang::msg( 'in_blacklist' );
			return;
		}

		// Check if has login error
		if ( $err_msg = $this->_has_login_err( true ) ) {
			$error .= $err_msg;
			return;
		}
	}

	/**
	 * Check if has login error limit
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _has_login_err( $msg_only = false )
	{
		global $wpdb;

		$q = 'SELECT COUNT(*) FROM ' . Data::tb_failure() . ' WHERE ip = %s AND dateline > %s ';

		$ip = IP::me();
		if ( Conf::val( 'gdpr' ) ) {
			$ip = md5( $ip );
		}

		$err_count = $wpdb->get_var( $wpdb->prepare( $q, array( $ip, time() - Conf::val( 'duration' ) * 60 ) ) );

		if ( ! $err_count ) {
			return false;
		}

		$max_retries = Conf::val( 'max_retries' );

		// Block visit
		if ( $err_count < $max_retries ) {
			if ( $msg_only ) {
				return Lang::msg( 'max_retries', $max_retries - $err_count );
			}
			return false;
		}

		// Can try but has failure
		return Lang::msg( 'max_retries_hit' );
	}

	/**
	 * Authenticate
	 *
	 * @since  1.0
	 * @access public
	 */
	public function authenticate( $user, $username, $password )
	{
		$in_whitelist = $this->try_whitelist();
		if ( is_wp_error( $user ) || $in_whitelist === 'hit' ) {
			return $user;
		}

		$error = new \WP_Error();

		if ( ! $in_whitelist ) {
			$error->add( 'not_in_whitelist', Lang::msg( 'not_in_whitelist' ) );
			$this->_err_added = true;
		}

		if ( $this->try_blacklist() ) {
			$error->add( 'in_blacklist', Lang::msg( 'in_blacklist' ) );
			$this->_err_added = true;
		}

		if ( ! $this->_err_added ) {
			if ( $err_msg = $this->_has_login_err() ) {
				$error->add( 'in_blacklist', $err_msg );
				$this->_err_added = true;
			}
		}

		if ( $this->_err_added ) {
			// bypass verifying user info
			remove_filter( 'authenticate', 'wp_authenticate_username_password', 20 );
			remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );
			return $error;
		}

		return $user;
	}

	/**
	 * Log login failure
	 *
	 * @since  1.0
	 * @access public
	 */
	public function wp_login_failed( $user )
	{
		global $wpdb;
		// create table
		Data::get_instance()->create_tb_failure();

		$ip = IP::me();

		// Parse Geo info
		$ip_geo_list = IP::geo( $ip );
		unset( $ip_geo_list[ 'ip' ] );
		$ip_geo = array();
		foreach ( $ip_geo_list as $k => $v ) {
			$ip_geo[] = $k . ':' . $v;
		}
		$ip_geo = implode( ', ', $ip_geo );

		// GDPR compliance
		if ( Conf::val( 'gdpr' ) ) {
			$ip = md5( $ip );
		}

		// Parse gateway
		$gateway = 'WP Login';
		if ( isset( $_POST[ 'woocommerce-login-nonce' ] ) ) {
			$gateway = 'WooCommerce';
		}
		elseif ( isset( $GLOBALS[ 'wp_xmlrpc_server' ] ) && is_object( $GLOBALS[ 'wp_xmlrpc_server' ] ) ) {
			$gateway = 'XMLRPC';
		}

		$q = 'INSERT INTO ' . Data::tb_failure() . ' SET ip = %s, ip_geo = %s, username = %s, gateway = %s, dateline = %s' ;
		$wpdb->query( $wpdb->prepare( $q, array( $ip, $ip_geo, $user, $gateway, time() ) ) );
	}

	/**
	 * Validate if hit whitelist
	 *
	 * @since  1.0
	 * @access public
	 */
	private function try_whitelist()
	{
		$list = Conf::val( 'whitelist' );
		if ( ! $list ) {
			return true;
		}

		if ( $this->maybe_hit_rule( $list ) ) {
			return 'hit';
		}

		return false;
	}

	/**
	 * Validate if hit blacklist
	 *
	 * @since  1.0
	 * @access public
	 */
	private function try_blacklist()
	{
		$list = Conf::val( 'blacklist' );
		if ( ! $list ) {
			return false;
		}

		if ( $this->maybe_hit_rule( $list ) ) {
			return 'hit';
		}

		return false;
	}

	/**
	 * Validate if hit whitelist
	 *
	 * @since  1.0
	 * @access public
	 */
	private function maybe_hit_rule( $list )
	{
		if ( ! $this->_visitor_geo_data ) {
			$this->_visitor_geo_data = IP::geo();
		}

		foreach ( $list as $v ) {
			$v = explode( ',', $v );

			// Go through each rule
			foreach ( $v as $v2 ) {

				if ( ! strpos( $v2, ':' ) ) { // Optional `ip:` case
					$curr_k = 'ip';
				}
				else {
					list( $curr_k, $v2 ) = explode( ':', $v2, 2 );
				}

				$curr_k = trim( $curr_k );
				$v2 = trim( $v2 );

				// Invalid rule
				if ( ! $v2 ) {
					continue 2;
				}

				// Rule set not match
				if ( empty( $this->_visitor_geo_data[ $curr_k ] ) ) {
					continue 2;
				}

				$v2 = strtolower( $v2 );
				$visitor_v = strtolower( $this->_visitor_geo_data[ $curr_k ] );
				$visitor_v = trim( $visitor_v );

				// If has IP wildcard range, convert $v2
				if ( $curr_k == 'ip' && strpos( $v2, '*' ) !== false ) {
					// If is same ip type (both are ipv4 or v6)
					$visitor_ip_type = \WP_Http::is_ip_address( $visitor_v );
					if ( $visitor_ip_type == \WP_Http::is_ip_address( $v2 ) ) {
						$ip_separator = $visitor_ip_type == 4 ? '.' : ':';
						$uip = explode( $ip_separator, $visitor_v );
						$v2 = explode( $ip_separator, $v2 );
						foreach ( $uip as $k3 => $v3 ) {
							if ( $v2[ $k3 ] == '*' ) {
								$v2[ $k3 ] = $v3;
							}
						}
						$v2 = implode( $ip_separator, $v2 );
					}

				}

				if ( $visitor_v != $v2 ) {
					continue 2;
				}
			}

			return true;
		}

		return false;
	}

}