<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Status Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Marco Gnazzo
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class incident_status {
	/**
	 *  Registers the main event add method
	**/ 
	public function __construct()
	{
		$this->waiting = "1";	
		$this->taken_on = "";
		$this->resolved = "";
		$this->summary = "";
				
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	/**
	* Adds all the events to the main Ushahidi application	
	**/
	public function add()
	{
		// Only add the events if we are on that controller
		if (Router::$controller == 'reports')
		{
			switch (Router::$method)
			{
				// Hook into the Report Add/Edit Form in Admin
				case 'edit':
					// Hook into the form itself
					Event::add('ushahidi_action.report_form_admin', array($this, '_report_form'));
					
					// Hook into the report_edit (post_SAVE) event
					Event::add('ushahidi_action.report_edit', array($this, '_report_form_submit'));
					break;
				
				// Hook into the User Report view 
				case 'view':
					plugin::add_stylesheet('incidentstatus/views/css/incident_status');
					Event::add('ushahidi_action.report_meta', array($this, '_report_view'));
					break;
			}
		}
		
	}
	
	
	/**
	* Add incident_status Form input to the Report Submit Form
	**/
	public function _report_form()
	{
		// Load the View
		$form = View::factory('form');
		// Get the ID of the Report
		$id = Event::$data;
		
		if ($id)
		{
			// Load last incident_status
			$report_item = ORM::factory('incident_status')
				->where('incident_id', $id)->orderby('id', 'DESC')
				->find();
			// Load all summaries	
			$summary = ORM::factory('incident_status')
				->where('incident_id', $id)->find_all();
							
			if ($report_item->loaded)
			{
				$this->waiting = $report_item->waiting;			
				$this->taken_on = $report_item->taken_on;
				$this->resolved = $report_item->resolved;								
				$this->summary = $summary;						
			}
		}
		
		$form->waiting = $this->waiting;
		$form->taken_on = $this->taken_on;
		$form->resolved = $this->resolved;
		$form->summaries = $this->summary;
		$form->render(TRUE);
	}
	
	/**
	 * Handle Form Submission and Save Data
	 */
	public function _report_form_submit()
	{
		$id = Event::$data;

		if ($_POST)
		{
			$report_last = ORM::factory('incident_status')
				->where('incident_id', $id->id)->orderby('id', 'DESC')
				->find();
				
			$report_item = ORM::factory('incident_status');
			
			
			if (!$report_last->loaded)
				{
						
					$report = ORM::factory('incident')
					->where('id', $id->id)->find();
					
					$report_ite = ORM::factory('incident_status');	
					$report_ite->incident_id = $id->id;			
					$report_ite->waiting = 1;
					$report_ite->taken_on = 0;
					$report_ite->resolved = 0;		
					$report_ite->summary = "";
					$report_ite->datetime = $report->incident_date;
					$report_ite->save();
					
				}		
			
			$report_last = ORM::factory('incident_status')
				->where('incident_id', $id->id)->orderby('id', 'DESC')
				->find();
						
			$waiting = 0;
			$taken_on = 0;
			$taken = 0;
			
			switch ($_POST['status']) 
			{
   			case 0:
       			$waiting = 1;			
	        	break;

    			case 1:
       		 	$taken_on = 1;		
      			break;

    			case 2:
	        	$taken = 1;
	        	break;
			}		
			
			// If status has changed save it
			if (($report_last->waiting != $waiting ) || ($report_last->taken_on != $taken_on)
				|| ($report_last->resolved != $taken) || ($_POST['summary'] != ""))
			{			
				$report_item->incident_id = $id->id;			
				$report_item->waiting = $waiting;
				$report_item->taken_on = $taken_on;
				$report_item->resolved = $taken;		
				$report_item->summary = $_POST['summary'];
				$report_item->datetime = date("Y-m-d G:i:s");
				$report_item->save();
			}		
		} 
	}
	
	/**
	 * Render the Incident Status Information to the Report on the front end
	 */
	public function _report_view()
	{
		$id = Event::$data;
		if ($id)
		{
			$report_item = ORM::factory('incident_status')
				->where('incident_id', $id)->orderby('id', 'DESC')
				->find();
			
			$summary = ORM::factory('incident_status')
				->where('incident_id', $id)
				->find_all();
			
			if ($report_item->loaded)
			{
				if (($report_item->taken_on) || ($report_item->resolved))
				{
					$report = View::factory('report');
					$report->resolved = $report_item->resolved;
					$report->taken_on = $report_item->taken_on;
					$report->summaries = $summary;
					$report->render(TRUE);
				}
				else 
				{
					$report = View::factory('report_waiting');
					$report->resolved = $report_item->resolved;
					$report->summaries = $summary;
					$report->render(TRUE);		
				}
			}
			else 
				{
					$report = View::factory('report_waiting');
					$report->resolved = $report_item->resolved;
					$report->summaries = $report_item->summary;
					$report->render(TRUE);	
				}
		}
		
	}
	
}

new incident_status;
