<?php
/**
 * Config class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Conf extends Instance
{
	protected static $_instance;

	private $_options = array();

	protected static $_default_options = array(
		'_ver'				=> Core::VER,
		'max_retries'		=> 4,
		'lockout_duration'	=> 20,
		'sms'				=> false,
		'whitelist'			=> array(),
		'blacklist'			=> array(),
	);

	protected function __construct()
	{
	}

	/**
	 * Init config
	 *
	 * @since  1.0
	 * @access public
	 */
	public function init()
	{
		$options = array();
		foreach ( self::$_default_options as $k => $v ) {
			$options[ $k ] = self::get_option( $k, $v );
		}

		$this->_options = $options;
	}

	/**
	 * Get option of dologin
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function v( $id )
	{
		$instance = self::get_instance();
		if ( isset( $instance->_options[ $id ] ) ) {
			return $instance->_options[ $id ];
		}

		return null;
	}


	/**
	 * Get option of dologin
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function get_option( $id, $default_v = false )
	{
		return get_option( 'dologin.' . $id, $default_v );
	}

	/**
	 * Update option of dologin
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function update( $id, $data )
	{
		if ( ! array_key_exists( $id, self::$_default_options ) ) {
			return;
		}

		// typecast
		$default_v = self::$_default_options[ $id ];
		if ( is_bool( $default_v ) ) {
			$data = (bool) $data;
		}
		elseif ( is_array( $default_v ) ) {
			if ( ! is_array( $data ) ) {
				$data = explode( "\n", $data );
			}
		}
		elseif ( ! is_string( $default_v ) ) {
			$data = (int) $data;
		}

		update_option( 'dologin.' . $id, $data );
	}

}
