<?php
/**
 * Data structure class
 *
 * @since 1.0
 */
namespace dologin;

defined( 'WPINC' ) || exit;

class Data extends Instance
{
	protected static $_instance;

	const TB_FAILURE = 'dologin_failure' ;

	private $_charset_collate ;
	private $_tb_failure ;

	/**
	 * Init
	 *
	 * @since  1.0
	 * @access protected
	 */
	protected function __construct()
	{
		global $wpdb ;

		$this->_charset_collate = $wpdb->get_charset_collate() ;

		$this->_tb_failure = self::tb_failure() ;
	}

	/**
	 * Get table failure
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function tb_failure()
	{
		global $wpdb ;
		return $wpdb->prefix . self::TB_FAILURE ;
	}

	/**
	 * Check if table existed or not
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function tb_failure_exist()
	{
		global $wpdb ;

		$instance = self::get_instance() ;

		return $wpdb->get_var( "SHOW TABLES LIKE '$instance->_tb_failure'" ) ;
	}

	/**
	 * Create table failure
	 *
	 * @since  1.0
	 * @access public
	 */
	public function create_tb_failure()
	{
		if ( defined( __NAMESPACE__ . '_DID_' . __FUNCTION__ ) ) {
			return;
		}
		define( __NAMESPACE__ . '_DID_' . __FUNCTION__, true );

		global $wpdb;

		// Check if table exists first
		if ( self::tb_failure_exist() ) {
			return;
		}

		$sql = sprintf(
			'CREATE TABLE IF NOT EXISTS `%1$s` (' . $this->_tb_structure( 'failure' ) . ') %2$s;',
			$this->_tb_failure,
			$this->_charset_collate
		);

		$res = $wpdb->query( $sql );
	}

	/**
	 * Get data structure of one table
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _tb_structure( $tb )
	{
		return File::read( DOLOGIN_DIR . 'src/data_structure/' . $tb . '.sql' ) ;
	}


}