<?php

/*
 * This class will create a list table
* */


if( ! class_exists( 'WP_List_Table' ) ) {
	if(!class_exists('WP_Internal_Pointers')){
		require_once( ABSPATH . '/wp-admin/includes/template.php' );
	}
	require_once( ABSPATH . '/wp-admin/includes/class-wp-list-table.php' );
}


class SeamlessDonationCodeLists extends WP_List_Table{

	private $per_page;
	private $total_items;
	private $current_page;
	
	private $sync_lists = array();
	
	/*columns of the talbe*/
	function get_columns(){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'code' => __('Code'),
			'min_value' => __('Minimum'),
			'max_value' => __('Maxium'),		
			'status' => __('Status'),
			'time' => __('Creation Time'),
		);
	
		return $columns;
	}
	
	/*preparing items*/
	function prepare_items(){
	
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
	
		$this->_column_headers = array($columns, $hidden, $sortable);
	
		//paginations
		$this->_set_pagination_parameters();
		
		//contains all items
		$this->items = $this->populate_table_data();
	
	}
	
	
	//make some column sortable
	function get_sortable_columns(){
		$sortable_columns = array(
			'time' => array('time', false),
			//'status' => array('status', false),
			'min_value' => array('min_value', false),
			'max_value' => array('max_value', false)		
		);
	
		return $sortable_columns;
	}
	
	/*
	* total items
	* */
	private function _set_pagination_parameters(){
		$SdDb = new SeamlessDonationDb();
	
		$this->current_page = $this->get_pagenum();
	
		$this->total_items = $SdDb->db->get_var("select count(ID) from $SdDb->code");
		$this->per_page = 20;
	
		$this->set_pagination_args( array(
				'total_items' => $this->total_items,                  //WE have to calculate the total number of items
				'per_page'    => $this->per_page,                     //WE have to determine how many items to show on a page
				'total_pages' => ceil($this->total_items/$this->per_page)   //WE have to calculate the total number of pages
		) );
	
	}
		
	
	function populate_table_data(){
		$SdDb = new SeamlessDonationDb();
		$prepared_data = array();
		
		//pagination
		$current_page = ($this->current_page > 0) ? $this->current_page - 1 : 0;
		$offset = (int) $current_page * (int) $this->per_page;
		
		$args = array(
			'limit' => $this->per_page,
			'offset' => $offset,
		);
		
		if(isset($_REQUEST['orderby']) && !empty($_REQUEST['orderby'])){
			$args['orderby'] = $_REQUEST['orderby'];
		}
		
		if(isset($_REQUEST['order']) && !empty($_REQUEST['order'])){
			$args['order'] = $_REQUEST['order'];
		}
		
		$codes = $SdDb->get_codes($args);
		
		if($codes){
			foreach($codes as $code){
				
				/*
				$donation_detail = '';
				
				if(function_exists('dgx_donate_get_donation_detail_link')){				
					$meta = $SdDb->get_key_metas($code->ID);
					
					//var_dump($meta);
					
					$donation_id = $meta['post_id'];					
					$donation_detail_link = dgx_donate_get_donation_detail_link($donation_id);
					if($donation_detail_link){
						$donation_detail = " <a href='$donation_detail_link'>View</a>";
					}
				}
				*/
				 
				$prepared_data[] = array(
					'ID' => $code->ID,
					'code' => $code->code,
					'min_value' => $code->min_value,
					'max_value' => $code->max_value,
					'status' => ($code->status == 1) ? 'Not Used' : 'Already Used',
					'time' => date('Y-m-d', $code->time)										
				);
			}
		}
		
		return $prepared_data;
	}
	
	
	//primary key column is a must
	/* checkbox for bulk action*/
	function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="code_id[]" value="%s" />', $item['ID']
		);
	}
	
	/* default column checking */
	function column_default($item, $column_name){
		
		//var_dump($item); diee();
		
		switch($column_name){
			case "ID":
			case "code":
			case "min_value":
			case "max_value":
			case "status":
			case "time":
				return $item[$column_name];
				break;
			default:
				var_dump($item);
					
		}
	}
	
	
	//bulk actions initialization
	function get_bulk_actions() {
		$actions = array(
				'delete'    => 'Delete'
		);
		return $actions;
	}
	
	
	//extra 
	/*adding extra actions when hovering first column  */
	function column_code($item){
	
		$delete_href = sprintf('?page=%s&action=%s&code_id=%s', $_REQUEST['page'],'delete',$item['ID']);
	
		if($this->get_pagenum()){
			$delete_href = add_query_arg(array('paged'=>$this->get_pagenum()), $delete_href);
		}
	
		$actions = array(
				'edit' => sprintf('<a href="?page=%s&action=%s&code_id=%s">Edit</a>', 'addnew-code','edit',$item['ID']),
				'delete' => "<a href='$delete_href'>Delete</a>"
		);
	
	
		return sprintf('%1$s %2$s', $item['code'], $this->row_actions($actions) );
	}

	
	/*
	 * handling bulk actions
	 * */
	function handle_bulk_actions(){
		if($this->current_action() == 'delete'){
			$SdDb = new SeamlessDonationDb();
			$code_ids = $_REQUEST['code_id'];
		
			if(!is_array($code_ids)){
				$code_ids = array($code_ids);
			}
			
			foreach($code_ids as $code_id){
				$SdDb->delete_a_code($code_id);
			}
			
			$message = count($code_ids) . ' deleted';
			return $message;			
		}
		else{
			return null;
		}
		
	}
	
}
