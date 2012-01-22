<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Forward Report By Email - Administrative Controller
 *
 * @author	   Marco Gnazzo
 */

class Forward_report_by_email_Controller extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
		
		$this->template->this_page = 'settings';

		// If this is not a super-user account, redirect to dashboard
		if(!$this->auth->logged_in('admin') && !$this->auth->logged_in('superadmin'))
		{
			echo "FAILED";
			return;
		}
	}
	
	
	// This is the function that send the email	
	public function forward()
	{
		$this->auto_render = FALSE;
		$this->template = "";
		
		if( $_POST && (isset($_POST['report_id']) && isset($_POST['recipient']) && isset($_POST['subject']) && isset($_POST['message'])) )		
		{
				
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);
			
			// uses PHP trim() to remove whitespace from beginning and end of all fields before validation
			$post->pre_filter('trim');
			
			// Add some rules, the input field, followed by a list of checks
        	$post->add_rules('report_id','required','numeric');
			$post->add_rules('recipient','required','valid::email');
			$post->add_rules('subject','required');
			$post->add_rules('message','required');
			
			// $post check
        	if ($post->validate())
			{ 			
				//Mail sender
				$site_email = Kohana::config('settings.site_email');
				
				//Send mail
				email::send($post->recipient, $site_email, $post->subject, $post->message, TRUE);
							
				//update history table 
				$history = new Forward_report_by_email_Model();
				$history->date = date("c");
				$history->recipient = $post->recipient;
				$history->incident_id = $post->report_id;
				$history->user_id = $this->user->id;
				$history->save();
					
				echo "ok";
				return;	
			}
			// We have validation errors, we need to show the errors
			else 
			{
				$errors = $post->errors();
				   				
				foreach ($errors as $key => $val)
  				{
       				echo Kohana::lang('forward_report_by_email.wrong_field') .': ' . Kohana::lang('forward_report_by_email.' . $key) . "\n"  ;
   				}
								
				return;
			}
						
		}	
	}
	
}
