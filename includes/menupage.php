<?php 
	$action = admin_url('admin.php?page='.$_REQUEST['page']);
	if($CodeList->get_pagenum()){
		$action = add_query_arg(array('paged'=>$CodeList->get_pagenum()), $action);
	}	
?>

<div class="wrap">
	<h2>Codes for Seamless Donation</h2>
	
	<?php 
		if($message) echo '<div class="updated"><p>' . $message . '</p></div>';
	?>
	
	<form action="" method="post">
		<?php 
			$CodeList->prepare_items();
			$CodeList->display();
		?>
	</form>
	
</div>