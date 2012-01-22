<script type="text/javascript" charset="utf-8">




// Send element mail to forward function
function reportForwardByEmail(r_id)
{

	to = document.getElementById('to'+r_id).value;
	sub = document.getElementById('subject'+r_id).value;
	msg = document.getElementById('message'+r_id).value;

	$.post("<?php echo url::site() . 'admin/forward_report_by_email/forward' ?>", {report_id: r_id , recipient: to , subject: sub , message: msg },
			function(data)
			{
					if (data == 'ok')
					{
						alert('<?php echo Kohana::lang('forward_report_by_email.email_send');?>');
					}
					else
					{
						alert(data);
					}	
			});
			
	
}

 

// Show/hide mail form
function showForm(id_report)
{
		 
	$a = document.getElementById(id_report).style.display;
		
	if ($a == "block")
	{
		document.getElementById(id_report).style.display ="none";
	}
	else
	{
		document.getElementById(id_report).style.display ="block"; 
	}

}


</script>

