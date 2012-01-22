<div class="incident_status clearingfix">
	<div id="badge">
	<?php echo Kohana::lang('incident_status.waiting'); ?>
	</div>
	
	<div id="summary">
	<?php if ($summaries)
		foreach ($summaries as $summary)
 		{ ?>
 			<?php echo substr($summary->datetime,0,16) . " - "; ?>
 			<b>
 			<?php if ($summary->waiting == 1) echo Kohana::lang('incident_status.waiting')  . " - ";
				else if ($summary->taken_on == 1) echo Kohana::lang('incident_status.taken_on')  . " - ";
				else if ($summary->resolved == 1) echo Kohana::lang('incident_status.resolved')  . " - "; ?>
				</b>
 			    	<?php echo $summary->summary; ?> 
 			      	<br>
		<?php } ?>
	</div>
</div>
