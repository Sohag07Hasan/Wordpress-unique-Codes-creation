<?php
class SeamlessDonationCodeManipulation{

	static function init(){
		add_action('admin_menu', array(get_class(), 'admin_menu'));
		register_activation_hook(SDCODEMANIPLATION_FILE, array(get_class(), 'manage_db'));
		
		//form submission
		add_action('init', array(get_class(), 'code_generation_form_submission_handler'));
	}
	
	//manupage
	static function admin_menu(){
		add_menu_page('seamless donation codes', 'Donation Codes', 'manage_options', 'donation_code', array(get_class(), 'menu_page_seamless_codes'));
		add_submenu_page('donation_code', ucwords('new or edit a code'), 'Add New', 'manage_options', 'addnew-code', array(get_class(), 'submenu_add_code'));
	}
	
	
	//menu page
	static function menu_page_seamless_codes(){
		include SDCODEMANIPLATION_DIR . '/includes/menupage.php';
	}
	
	
	//sub menu page
	static function submenu_add_code(){
		include SDCODEMANIPLATION_DIR . '/includes/submenupage.php';
	}
	
	//get a list table
	static function get_list_table(){
		
	}
	
	static function manage_db(){
		$SdDb = new SeamlessDonationDb();
		return $SdDb->sync_db();
	}
	
	
	static function code_generation_form_submission_handler(){
		if($_POST['page'] == 'addnew-code'):
			$url = admin_url('admin.php?page=addnew-code');
			$info = array();
		
			$SdDb = new SeamlessDonationDb();
			if(isset($_POST['code_id']) && !empty($_POST['code_id'])){
				$id = $SdDb->update_code($_POST);
			}
			else{
				$id = $SdDb->create_code($_POST);
			}
			
			if($id > 0){
				$SdDb->update_code_meta($id, array('message' => $_POST['code_msg']));
				$info['code_id'] = $id;
				$info['message'] = 1;
			}
			else{
				$info['code_id'] = 0;
				$info['message'] = 2;
			}
			
			$url = add_query_arg($info, $url);
			return self::do_redirect($url);
						
		endif;
		
	}
	
	
	//do a http redirect
	static function do_redirect($url){
		if(!function_exists('wp_redirect')){
			include ABSPATH . '/wp-includes/pluggable.php';
		}
	
		wp_redirect($url);
		die();
	}
}