<?php 
	echo $this->Html->script('bootstrap-datepicker');
	echo $this->Html->script('holiday/holiday.js');
?>

<style type="text/css">
	.datepicker{z-index:1151 !important;}
	.error{color:red;}

</style>
<div class="main-content">
	<h2>Holiday Management</h2>
	<button class="btn btn-default" data-target="#modalHoliday" data-toggle="modal"><i class="icon-plus-sign"></i>Add</button>
	<table class="table" id="holiday-list">
		<thead>
			<th>Date</th>
			<th>Description</th>
			<th>Rate</th>
			<th>Occurence</th>
		</thead>
		<?php foreach($holidays as $value):?>
		<tr id="<?php echo $value['Holiday']['id']?>">
			<td><?php echo $value['Holiday']['date']?></td>
			<td><?php echo nl2br($value['Holiday']['description'])?></td>
			<td><?php echo $value['Holiday']['rate']?></td>
			<td>
				<?php switch ($value['Holiday']['recurring']) {
				case '1':
					echo 'Yearly';
					break;
				case '2':
					echo 'Monthly';
					break;
				default:
					// recurring = 0;
					echo 'Once';
					break;
				}
				?>
			</td>
			<td>
				<button class="btn btn-danger" onclick="deleteHoliday('<?php echo $value['Holiday']['id']?>')">Delete</button>
				<a href="<?php echo Router::url(array('controller'=>'holidays','action'=>'edit',$value['Holiday']['id']))?>"><button class="btn btn-primary" >Edit</button></a>
			</td>

		</tr>
		<?php endforeach;?>

	</table>
</div>

<!-- Modal -->
<div class="modal hide fade" id="modalHoliday" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" id="dismiss-holiday-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Holiday</h4>
        </div>
      </div>
      <div class="modal-body">
      	<form id="holiday-form">
			<table class="table holiday-tbl">
				
				<tr>
					<td>Date</td>
					<td>
						<input type="text" name="date" id="holiday-date">
						<br/>
						<span class="alert alert-danger error hide" role="alert" id="date"></span>
					</td>
				</tr>
				<tr>
					<td>Description</td>
					<td> 
						<textarea class="description-holiday" name="description" id="holiday-description"></textarea>
						<br/>
						<span class="alert alert-danger error hide" id="description"></span>
					</td>
				</tr>
				<tr>
					<td>Rate</td>
					<td>
						<input type="text" name="rate" class="rate" onkeyup="formatRate()" maxlength="4" id="holiday-rate">
						<br/>
						<span class="alert alert-danger error hide" id="rate"></span>
					</td>
				</tr>
				<tr>
					<td>Occurence</td>
					<td>
						<select name="recurring" id="holiday-recurring">
							<option value="0">Once</option>
							<option value="1">Yearly</option>
							<option value="2">Monthly</option>
						</select>
						<br/>
						<span class="alert alert-danger error hide" id="recurring"></span>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<button class="btn btn-primary" id="holiday-btn">Create</button>
						<!-- <input type="submit" id="holiday-btn" class="btn btn-primary" value="Create"> -->
					</td>
				</tr>
				
			</table>
		</form>
	  </div>    		 	
      </div>
    </div>
  </div>
</div>
<!-- End of Modal -->

<script type="text/javascript">
	$('#holiday-btn').click(function(e){
		e.preventDefault();
		var url = "<?php echo Router::url(array('controller'=>'holidays','action'=>'createHoliday'))?>";
		$.post(url,$('#holiday-form').serialize(),function(data){
			if(data == 1) {
				$('#holiday-form')[0].reset();
				$('.error').text('');
				$('.error').hide();
				$('#dismiss-holiday-modal').trigger('click');
				setTimeout(function(){
					window.location.reload();
				},600);


			} else {
				var error = $.parseJSON(data);
				$.each(error,function(index, value){
					$('#'+index).text(value).show();
				});
				
			}
		});
	});

	function deleteHoliday(id){
		var url = "<?php echo Router::url(array('controller'=>'holidays','action'=>'deleteHoliday'))?>";
		if (confirm('Are you sure you want to delete this holiday ?')) {
			$.post(url,{"id":id},function(data){
				if (data == 1) {
					$('#'+id).remove();
				}
			});
		}
		
	}

</script>

