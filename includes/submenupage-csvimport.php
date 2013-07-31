<?php 
	set_time_limit(0);

	if(!empty($_FILES['csv_keywords'])){
		$csv = self::get_csv_parser();
		
	}
?>

<style>
	table.csvtable td{
		padding: 15px;
	}
</style>
<div class="wrap">
	
	<h2>CSV Importer for keywords</h2>
	
	<p></p>
	
	<form action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="csv-keyword_parser" value="y" />
		
		<table class="csvtable">
			<tr>
				<td><input type="file" name="csv_keywords"></td>
				<td><input type="submit" value="Import" class="button button-primary"></td>
			</tr>		
		</table>
		
	</form>
		

</div>