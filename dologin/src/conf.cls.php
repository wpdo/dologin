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
		'_ver'				=> '',
		'max_retries'		=> 4,
		'duration'			=> 20,
		'auto_upgrade'		=> true,
		'gdpr'				=> false,
		'sms'				=> false,
		'sms_force'			=> false,
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
		// Load all options
		$options = array();
		foreach ( self::$_default_options as $k => $v ) {
			$options[ $k ] = $this->_get_option( $k, $v );
		}

		$this->_options = $options;

		// Update options if not exists
		! defined( 'DOLOGIN_CUR_V' ) && define( 'DOLOGIN_CUR_V', $this->_options[ '_ver' ] ) ;

		if ( ! DOLOGIN_CUR_V || DOLOGIN_CUR_V != Core::VER ) {
			if ( ! DOLOGIN_CUR_V ) {
				Util::version_check( 'new' );
			}
			else {
				// DB update
				Data::get_instance()->conf_upgrade();
			}

			foreach ( self::$_default_options as $k => $v ) {
				self::add( $k, $v );
			}

			self::update( '_ver', Core::VER );
		}
	}

	/**
	 * Get one current option
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function val( $id )
	{
		$instance = self::get_instance();
		if ( isset( $instance->_options[ $id ] ) ) {
			return $instance->_options[ $id ];
		}

		return null;
	}

	/**
	 * Get all options
	 *
	 * @since  1.1
	 * @access private
	 */
	public function get_options()
	{
		return $this->_options;
	}

	/**
	 * Add one option
	 *
	 * @since  1.4.1
	 * @access public
	 */
	public static function add( $id, $v )
	{
		add_option( 'dologin.' . $id, $v );
	}

	/**
	 * Delete one option
	 *
	 * @since  1.4.1
	 * @access public
	 */
	public static function delete( $id )
	{
		delete_option( 'dologin.' . $id );
	}

	/**
	 * Get option from DB
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _get_option( $id, $default_v = false )
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

		// Change current setting
		self::get_instance()->_options[ $id ] = $data;

	}

}
