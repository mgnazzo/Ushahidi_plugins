<?php
/**
 * Performs install/uninstall methods for the incidentstatus plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author Marco Gnazzo
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Incidentstatus_Install {

	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db = Database::instance();
	}

	/**
	 * Creates the required database tables for the actionable plugin
	 */
	public function run_install()
	{
		
		// Creates the database tables
		$this->db->query('
			CREATE TABLE IF NOT EXISTS `'.Kohana::config('database.default.table_prefix').'incident_status` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `incident_id` int(11) NOT NULL,
  				  `waiting` tinyint(4) NOT NULL DEFAULT \'1\',
				  `taken_on` tinyint(4) NOT NULL DEFAULT \'0\',
				  `resolved` tinyint(4) NOT NULL DEFAULT \'0\',
				  `summary` varchar(255) DEFAULT NULL,
				  `datetime` datetime DEFAULT NULL,	
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
		
	}

	/**
	 * Deletes the database tables
	 */
	public function uninstall()
	{
		$this->db->query('DROP TABLE `'.Kohana::config('database.default.table_prefix').'incident_status`');
		
	}
}
