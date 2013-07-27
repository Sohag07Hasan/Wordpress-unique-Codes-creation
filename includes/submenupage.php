<?php 
	$action = admin_url('admin.php?page=addnew-code');
	
	$SdDb = new SeamlessDonationDb();
	
	if($_REQUEST['code_id'] > 0){
		$code = $SdDb->get_key($_REQUEST['code_id']);
	
		if($code){
			$metas = $SdDb->get_key_metas($_REQUEST['code_id']);
		}
	
		//var_dump($code);
		//var_dump($metas);
		
		
	}
	
	$specific_code = $SdDb->get_used_code_by_amount(15);
	
	var_dump($specific_code);
	
?>

<div class="wrap">
	<h2> Generate a Code </h2>
	
	<?php 
		if($_REQUEST['message'] == 1){
			?>
			<div class="updated"><p> Code Information saved </p></div>
			<?php 
		}
		
	?>
	
	<form action="<?php echo $action; ?>" method="post">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
		<input type="hidden" name="add-new-code" value="Y" />
		
		<?php 
			if($_REQUEST['code_id'] > 0 && !empty($code)){
				echo '<input type="hidden" name="code_id" value="'.$code->ID.'" />';
			}
		?>
		
		<table class="form-table" >
			<tbody>
				<tr>
					<th scope="row"><label for="code_code">Code</label></th>
					<td><input placeholder="will be generated automatically" readonly id="code_code" size="40" type="text" name="code_code" value="<?php echo $code->code; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="code_min">Minimum value to Donate ( Integer )</label></th>
					<td><input size="40" type="text" name="code_min" value="<?php echo $code->min_value; ?>" id="code_min" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="code_max">Maximum value to Donate (Integer)</label></th>
					<td><input size="40" type="text" name="code_max" value="<?php echo $code->max_value; ?>" id="code_max" /></td>
				</tr>
				
				<tr>
					<th scope="row"><label for="code_msg"> Procedure Message </label></th>
					<td>
						<!--  <input size="40" type="text" name="code_msg" value="<?php echo $metas['message']; ?>" id="code_msg" />  -->
						<textarea name="code_msg" id="code_msg" rows="5" cols="43"><?php echo $metas['message']; ?></textarea>
					</td>
				</tr>				
				
			</tbody>
		</table>
		
		<p>
			<?php if($_REQUEST['code_id'] > 0) : ?>
				<input type="submit" value="Update Code" class="button button-primary" />
			<?php else: ?>
				<input type="submit" value="Generate Code" class="button button-primary" />
			<?php endif; ?>
		</p>
				
	</form>
	
</div>