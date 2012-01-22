<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Download Reports Hook.
 * This hook will take care of adding a link in the nav_main_top section.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */


// Hook into the nav main_top

class report {

public function __construct()
	{	
		
		plugin::add_stylesheet('downloadreports/views/css/download_reports');
		Event::add('ushahidi_action.nav_main_top', array($this, 'add'));
          
	}	

public function add()
	{	
		// Add plugin link to nav_main_top		
		echo "<li><a href='" . url::site() . "download_reports'>" . strtoupper(Kohana::lang('ui_main.download_reports')) . "</a></li>";
		
	}

}
new report();


