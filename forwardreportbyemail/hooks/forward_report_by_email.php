<?php defined('SYSPATH') or die('No direct script access.');
/**
 *  Forward Report By Email - sets up the hooks
 *
 * @author	   Marco Gnazzo
 */

class forwardreportbyemail {
		  
	// Registers the main event add method	 
	public function __construct()
	{
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
		
		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');		
		$this->group_name = "";
   		
	}
	
		  
	// Adds all the events to the main Ushahidi application
	public function add()
	{
		
		if(Router::$controller == "reports")
		{
			//make sure this is admin user
			if(strpos(url::current(), "admin/reports") === 0)
			{
				Event::add('ushahidi_action.header_scripts_admin', array($this, 'add_js'));	
				Event::add('ushahidi_action.report_extra_admin', array($this, 'add_forward'));
				
			}
		}
		
	}
	
	

	// Add javascript code
	public function add_js()
	{
		$view = view::factory("forward_report_by_email_js");
		$view->render(true);
	}
	
	
	  
	// Creates the forward to link
	public function add_forward()
	{
		$incident = event::$data;
				
		$form = array(
          	  'recipient' 	=> '',
          	  'subject' 	=> Kohana::config('settings.site_name') . ' - ' . Kohana::lang("ui_main.report") . ' n°',
        	  'message'	=> Kohana::lang("forward_report_by_email.message1") . ": \n"
        	  			. Kohana::lang("forward_report_by_email.message2") . ": \n",
		  'r_id'    	=> ''
		);

				
		//get the forwarded reports
		$history = ORM::factory("forward_report_by_email")
			->where("incident_id", $incident->incident_id)
			->find_all();
				
		$view = view::factory("forward_report_by_email");
		$view->form = $form;
		$view->history = $history;
		$view->incident = $incident;		
		$view->render(TRUE);
				
	}
	
}

new forwardreportbyemail;
