<?php print form::close(); ?>	

<br/>
<div id="reportForward_time_<?php echo $incident->incident_id; ?>"></div>
	
	<div>
	<?php
	//Show mail history	
	foreach($history as $h)
		{
			$user = ORM::factory("user")->where("id", $h->user_id)->find();
			$date = $h->date;
			$recipient = $h->recipient;
			echo Kohana::lang('forward_report_by_email.report_sent') . " " . $date . " ". strtolower(Kohana::lang('ui_main.from')) .
				  " " . $user->name . " " . Kohana::lang('ui_main.to') ." <b>" . $recipient . "</b>";
			echo "<br/>";
		}	
	?> 	
	</div>
	
	<br/>
	<?php 
	echo "<a href=\"javascript:showForm('$incident->incident_id')\">" . Kohana::lang('forward_report_by_email.forward') ."</a>";
	?>
	
		
	<!--Mail form-->
	<div id="<?php echo $incident->incident_id; ?>" style="display:none;">
	<?php print form::open(NULL, array('id' => 'form' . $incident->incident_id , 'name' => 'form'));	 ?>	
	<table class="table-list">
	<tr><td><?php echo Kohana::lang('forward_report_by_email.recipient');?>:</td><td><?php print form::input('to' . $incident->incident_id, $form['recipient']);?> </td></tr>
	<tr><td><?php echo Kohana::lang('forward_report_by_email.subject');?>:</td>
	<td><?php print form::input('subject' . $incident->incident_id, $form['subject'] . $incident->incident_id, 'SIZE=31' );?></td></tr>
	<tr><td><?php echo Kohana::lang('ui_admin.message');?>:</td>
	<td><?php print form::textarea('message' . $incident->incident_id, $form['message'] . url::site() . 'reports/view/' . 				  	         $incident->incident_id,  'COLS=40 ROWS=6 LANGUAGE=IT ');?> </td></tr>	
	</table>
	<input type="submit" value="<?php echo Kohana::lang('ui_main.send');?>" style="border: #d1d1d1 1px solid; background-color:#F2F7Fa; color: #5c5c5c; padding: 0px 9px; line-height:24px; text-decoration:none;">
		
	<?php print form::close();	 ?>	
	</div>
	


<!--Javascript form control-->
<script type="text/javascript" charset="utf-8">

		$(document).ready(function() { 	
			$("#form<?php echo $incident->incident_id; ?>").validate({
				
				submitHandler: function() { 
				reportForwardByEmail(<?php echo $incident->incident_id; ?>) ;return false;	
				
				},

				rules: 
				{
						to<?php echo $incident->incident_id; ?>: {
						required: true,
						email: true						
						},
						subject<?php echo $incident->incident_id; ?>: {
						required: true						
						},
						message<?php echo $incident->incident_id; ?>: {
						required: true						
						}	
				},	
				messages: 
				{
						to<?php echo $incident->incident_id; ?>: {
						required: "<?php echo Kohana::lang('forward_report_by_email.required');?>",
						email: "<?php echo Kohana::lang('forward_report_by_email.email');?>"
						
						},
						subject<?php echo $incident->incident_id; ?>: {
						required: "<?php echo Kohana::lang('forward_report_by_email.required');?>"
					
						},
						message<?php echo $incident->incident_id; ?>: {
						required: "<?php echo Kohana::lang('forward_report_by_email.required');?>"
						
						}
				}
				

	});

});

</script>		

