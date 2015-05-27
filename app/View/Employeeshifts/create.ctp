<?php 
	echo $this->Form->create('Employee_shift', array(
		'class' => 'form-horizontal', 
		'url' => '/admin/create_shift')
	); 
?>
	<fieldset>
		<legend>Create Employee's Shifts</legend>
		<div class="bg-padd bg-danger notice" style='display:none;'><?php echo $this->Session->flash();?></div>
		<div class='control-group'>
			<?php 
				echo $this->Form->input('description', 
					array(
						'id'			=> 	'description',
						'type' 			=> 	'text',
						'placeholder' 	=> 	'Description', 
						'label' 		=> 	'Shift description',
						'between' 		=> 	'<div class="control-group">',
						'after'			=>	'</div>'
					) 
				);
				echo $this->Form->input('f_time_in', 
					array(
						'id'			=> 	'f_time_in',
						'type' 			=> 	'time',
						'selected' 		=> 	'00:00:00',
						'placeholder' 	=> 	'TIME', 
						'label' 		=> 	'First Time-In',
						'between' 		=> 	'<div class="control-group">',
						'after'			=>	'</div>',
						'required'		=> true
					) 
				);
				echo $this->Form->input('f_time_out', 
					array(
						'id'			=> 	'f_time_out',
						'type' 			=> 	'time',
						'selected' 		=> 	'00:00:00',
						'placeholder' 	=> 	'TIME', 
						'label' 		=> 	'First Time-Out',
						'between' 		=> 	'<div class="control-group">',
						'after'			=>	'</div>'
					) 
				);
				
				$timeOptional = '<span> <a href="javascript:;" class="settime" timeSet="deactivated"><i class="icon-edit"></i></span></a>';
				echo $this->Form->input('l_time_in', 
					array(
						'id'			=> 	'l_time_in',
						'type' 			=> 	'time', 
						'selected' 		=> 	'00:00:00',
						'placeholder' 	=> 	'TIME', 
						'label' 		=> 	'Last Time-In',
						'between' 		=> 	'<div class="control-group">',
						'disabled'		=> 	true,
						'after'			=>	" $timeOptional </div>",
						'value'			=>	''
					) 
				);
				echo $this->Form->input('l_time_out', 
					array(
						'id'			=> 	'l_time_out',
						'type' 			=> 	'time',
						'selected' 		=> 	'00:00:00',
						'placeholder' 	=> 	'TIME', 
						'label' 		=> 	'Last Time-Out',
						'between' 		=> 	'<div class="control-group">',
						'disabled'		=> 	true,
						'after'			=>	" $timeOptional </div>"
					) 
				);
				echo $this->Form->input('overtime_start', 
					array(
						'id'			=> 	'overtime_start',
						'type' 			=> 	'time', 
						'selected' 		=> 	'00:00:00',
						'placeholder' 	=> 	'OVERTIME', 
						'label' 		=> 	'OVERTIME STARTS',
						'between' 		=> 	'<div class="control-group">',
						'disabled'		=> 	true,
						'after'			=>	" $timeOptional </div>"
					) 
			);
			?>
		</div>
		<div class='control-group'>
			<input type='submit' name='shift' class='btn btn-primary submits' id='btn-shifts-submit' value='Create'/>
			<input type='reset' name='shift' class='btn reset' id='btn-position-reset'/>	
		</div>
	</fieldset>
<?php echo $this->Form->end();?>
<?php echo $this->Html->script('admin/shift'); ?>