<div class="row">
<h4><?php echo Kohana::lang('incident_status.incident_status'); ?></h4>
<?php if ($summaries)
		foreach ($summaries as $summary)
 				{ ?>
 			    	<?php echo substr($summary->datetime,0,16) . " - "; ?>
 			    	<b>
 			    	<?php if ($summary->waiting == 1) echo Kohana::lang('incident_status.waiting') . " - ";
							else if ($summary->taken_on == 1) echo Kohana::lang('incident_status.taken_on')  . " - ";
							else if ($summary->resolved == 1) echo Kohana::lang('incident_status.resolved')  . " - "; ?>
					</b>
 			    	<?php echo $summary->summary; ?> 
 			    	
				   	<br>
					<?php } ?>
</div>

<!-- report is waiting_on -->
<div class="row">
	<h4 style="color: #009200; padding: 10px 0 5px;">
		<?php print form::radio('status', 0, $waiting) . Kohana::lang('incident_status.waiting'); ?>
		<span><?php echo Kohana::lang('incident_status.waiting_span'); ?></span>
	</h4>
</div>


<!-- report was taken_on -->
<div class="row">
	<h4 style="color: #009200; padding: 5px 0 5px;">
		<?php print form::radio('status', 1, $taken_on) . Kohana::lang('incident_status.taken_on'); ?>
		<span><?php echo Kohana::lang('incident_status.taken_on_span'); ?></span>
	</h4>
</div>

<!-- report has been resolved -->
<div class="row">
  <h4 style="color: #009200; padding: 5px 0 5px;">
    <?php print form::radio('status', 2, $resolved) . Kohana::lang('incident_status.resolved'); ?>
    <span><?php echo Kohana::lang('incident_status.resolved_span'); ?></span>
  </h4>
</div>

<div class ="row">
<h4 style="color: #009200; padding: 0px 0 0px;">
<span><?php echo Kohana::lang('incident_status.comment'); ?></span>
</h4>
<textarea name="summary" id="summary" style=" height: 60px;"></textarea>
</div>


