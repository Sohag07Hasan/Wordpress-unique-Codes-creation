<?php 
	set_time_limit(0);

	if(!empty($_FILES['csv_keywords'])){
		$info = pathinfo($_FILES['csv_keywords']['name']);
		
		if($info['extension'] == 'csv'){		
			$csv = self::get_csv_parser();
			$csv->delimiter = ',';
			$csv->parse($_FILES['csv_keywords']['tmp_name']);
			
			$SdDb = new SeamlessDonationDb();
			
			$imported = 0;
			$skipped = 0;
			
			foreach($csv->data as $key => $row){
				
				//print_r($row); continue;
				
				if($SdDb->csv_import_code($row)){
					$imported ++;
				}
				else{
					$skipped++;
				}
			}

			$message = "<div class='updated'><p>Imported: {$imported}</p><p>Skipped: {$skipped}</p></div>";
		}
		else{
			$message = "<div class='error'> This is not a csv file </div>";
		}
		
	}
?>

<style>
	table.csvtable td{
		padding: 15px;
	}
</style>
<div class="wrap">
	
	<h2>CSV Importer for keywords</h2>
	
	<?php 
		if($_POST['csv_keyword_parser'] == 'y'){
			echo $message;
		}
	?>
	
	<p>Please find the sample csv inside the plguin directory</p>
	
	<form action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="csv_keyword_parser" value="y" />
		
		<table class="csvtable">
			<tr>
				<td><input type="file" name="csv_keywords"></td>
				<td><input type="submit" value="Import" class="button button-primary"></td>
			</tr>		
		</table>
		
	</form>
		

</div>