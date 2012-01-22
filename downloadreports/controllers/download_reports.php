<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Download Reports Controller.
 * This controller will take care of downloading reports.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author Marco Gnazzo
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

Class Download_Reports_Controller extends Main_Controller 
{

	 function __construct()
	{
        	parent::__construct();
   	}

 

    function index()
    {
		$this->template->this_page = 'reports';
		$this->template->header->header_block = $this->themes->header_block();
		$this->template->header->this_page ='download';
   		$this->template->content = new View('download_reports');
		$this->template->content->calendar_img = url::base() . "media/img/icon-calendar.gif";
		$this->template->content->title = Kohana::lang('ui_admin.download_reports');
		
		// Select first and last incident
		$from = orm::factory('incident')->orderby('incident_date', 'asc')->find();
		$to = orm::factory('incident')->orderby('incident_date', 'desc')->find();
		
		
		$from_date = substr($from->incident_date,5,2) . "/" . substr($from->incident_date,8,2) . "/" .
					 substr($from->incident_date,0,4);
		
		$to_date = substr($to->incident_date,5,2) . "/" . substr($to->incident_date,8,2) . "/" .
					 substr($to->incident_date,0,4);
	
		$form = array(
          	  'data_point'   => '',
          	  'data_include' => '',
        	  'from_date'    => $from_date,
       	 	  'to_date'      => $to_date
     	);
        
        	$errors = $form;
        	$form_error = FALSE;
     
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

       			//  Add some filters
        		$post->pre_filter('trim', TRUE);

	        	// Add some rules, the input field, followed by a list of checks, carried out in order
        		$post->add_rules('data_point.*','required','numeric','between[1,15]');
			$post->add_rules('formato','required','numeric','between[0,1]');
			$post->add_rules('from_date','date_mmddyyyy');
			$post->add_rules('to_date','date_mmddyyyy');

            		// Validate the report dates, if included in report filter
            		if (!empty($_POST['from_date']) || !empty($_POST['to_date']))
          		{
                		// Valid FROM Date?
                		if (empty($_POST['from_date']) || (strtotime($_POST['from_date']) > strtotime("today"))) 
               			{
		                    $post->add_error('from_date','range');
                		}

                		// Valid TO date?
              			if (empty($_POST['to_date']) || (strtotime($_POST['to_date']) > strtotime("today"))) 
               			{
                    			$post->add_error('to_date','range');
                		}

                	// TO Date not greater than FROM Date?
               	 	if (strtotime($_POST['from_date']) > strtotime($_POST['to_date'])) 
                	{
                    		$post->add_error('to_date','range_greater');
                	}
            	}

            	// $post validate check
        	if ($post->validate())
			{ 
			
				$filter = "(1 = 2";
				$filter2 = "1 = 2";
				
				foreach($post->data_point as $item)
				{
					$filter .= " OR category_id=$item";
					$filter2 .= " OR id=$item";
				}
				
		
				// Report Date Filter
	           		if (!empty($post->from_date) && !empty($post->to_date))
	            		{
                    			$filter .= ") AND ( incident_date >= '" . date("Y-m-d H:i:s",strtotime($post->from_date)) . 
						"' AND incident_date <= '" . date("Y-m-d H:i:s",strtotime($post->to_date)) . "' ) ";
	              		}

				$incidents = orm::factory('incident')->
				join('incident_category','incident_category.incident_id','incident.id','INNER')->
				where($filter)->orderby('incident_date', 'desc')->find_all();
			
				// CSV selected			
				if ($post->formato == 0) 			
				{
					$report_csv = "#,INCIDENT TITLE,INCIDENT DATE,LOCATION,DESCRIPTION,CATEGORY,LATITUDE,LONGITUDE,APPROVED,VERIFIED\n";
			
					foreach ($incidents as $incident)
					{
						$new_report = array();
						array_push($new_report,'"'.$incident->id.'"');
						array_push($new_report,'"'.$this->_csv_text($incident->incident_title).'"');
						array_push($new_report,'"'.$incident->incident_date.'"');
						array_push($new_report,'"'.$this->_csv_text($incident->location->location_name).'"');
						array_push($new_report,'"'.$this->_csv_text($incident->incident_description).'"');
				
						$catstring = '"';
						$catcnt = 0;        
						
						foreach($incident->incident_category as $category)
						{
		    	            			if ($catcnt > 0)
							{
	            	        				$catstring .= ",";
							}
							if ($category->category->category_title)
							{
								$catstring .= $this->_csv_text($category->category->category_title);
							}
							$catcnt++;
						}

						$catstring .= '"';
						array_push($new_report,$catstring);							
						array_push($new_report,'"'.$incident->location->latitude.'"');
						array_push($new_report,'"'.$incident->location->longitude.'"');
				    
						if ($incident->incident_active)
						{
							array_push($new_report,"YES");
						}
						else
						{
							array_push($new_report,"NO");
						}
					    
						if ($incident->incident_verified)
						{
							array_push($new_report,"YES");
						}
						else
						{
							array_push($new_report,"NO");
						}
					    
					    	array_push($new_report,"\n");

						$repcnt = 0;
						foreach ($new_report as $column)
						{
							if ($repcnt > 0)
							{
								$report_csv .= ",";
							}
							$report_csv .= $column;
							$repcnt++;
						}

					}
		
		      	  	// Output to browser
		      	  	header("Content-type: text/x-csv");
		     	  	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		      	  	header("Content-Disposition: attachment; filename=" . time() . ".csv");
		     	  	header("Content-Length: " . strlen($report_csv));
		      	  	echo $report_csv;
		      	  	exit;
				}

				// KML selected
				else
				{ 
					$categories = ORM::factory('category')
						      ->where($filter2)->find_all();
		
					header("Content-Type: application/vnd.google-earth.kml+xml");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Content-Disposition: attachment; filename=".time().".kml");
						
					$view = new View("kml");
					$view->kml_name = htmlspecialchars(Kohana::config('settings.site_name'));
					$view->items = $incidents;
					$view->categories = $categories;
					$view->render(TRUE);
					exit;			
				}
			}
        		// Validation errors
        		else
			{ 
		        	// repopulate the form fields
			        $form = arr::overwrite($form, $post->as_array());

			        // populate the error fields, if any
			        $errors = arr::overwrite($errors, $post->errors('report'));
			        $form_error = TRUE;
		        }
     	
    		} 
	             
		$categories = ORM::factory('category')->where('category_visible', '1')
				->find_all();

		$this->template->content->categories = $categories;
		$this->template->content->form = $form;
    		$this->template->content->errors = $errors;
	    	$this->template->content->form_error = $form_error;

	}

	

private function _csv_text($text)
    {
        $text = stripslashes(htmlspecialchars($text));
        return $text;
    }


}

	

