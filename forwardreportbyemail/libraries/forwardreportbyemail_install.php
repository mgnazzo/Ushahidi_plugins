<?php
/**
 * Forward Report By Email - Install
 *
 * @author	   Marco Gnazzo
 */

class Forwardreportbyemail_Install {

	  
	// Constructor to load the shared database library
	public function __construct()
	{
		$this->db = Database::instance();
	}

	 
	// Creates the required database tables for the actionable plugin
	public function run_install()
	{
		
		// Create the database tables
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.Kohana::config('database.default.table_prefix').'forwardreport_by_email` (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`date` datetime DEFAULT NULL,
					`recipient` varchar(255) DEFAULT NULL,
					`user_id` int(10) unsigned DEFAULT NULL,
					`incident_id` int(11) NOT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
		
	}

	
	// Deletes the database tables for the actionable module
	public function uninstall()
	{
		$this->db->query('DROP TABLE `'.Kohana::config('database.default.table_prefix').'forwardreport_by_email`');
	}
}
