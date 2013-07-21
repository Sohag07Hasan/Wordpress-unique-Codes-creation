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
			'min' => __('Minimum Range'),
			'max' => __('Maxium Range'),		
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
			'status' => array('status', false),
			'min' => array('min', false),
			'max' => array('max', false)		
		);
	
		return $sortable_columns;
	}
	
	/*
	* total items
	* */
	private function _set_pagination_parameters(){
		$SdDb = new SeamlessDonationDb();
	
		$this->current_page = $this->get_pagenum();
	
		$this->total_items = $SdDb->db->get_var("select count(ID) from $SdDb->meta");
		$this->per_page = 30;
	
		$this->set_pagination_args( array(
				'total_items' => $this->total_items,                  //WE have to calculate the total number of items
				'per_page'    => $this->per_page,                     //WE have to determine how many items to show on a page
				'total_pages' => ceil($this->total_items/$this->per_page)   //WE have to calculate the total number of pages
		) );
	
	}
		
	
	function populate_table_data(){
		
	}
	
}