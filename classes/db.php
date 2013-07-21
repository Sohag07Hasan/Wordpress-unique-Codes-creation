<?php
class SeamlessDonationDb{
	
	public $code;
	public $code_meta;
	public $db;
	
	function __construct(){
		global $wpdb;
		$tables = $this->get_tables();
		$this->code = $tables['code'];
		$this->code_meta = $tables['code_meta'];
		$this->db = $wpdb;
	}
	
	//tables names
	function get_tables(){
		return array(
				'code' => 'wp_codes',
				'code_meta' => 'wp_code_meta'
		);
	}
	
	
	//syhnchronizing database table
	function sync_db(){
				
		$sql = array();
		
		$sql[] = "create table if not exists $this->code(
			ID bigint not null auto_increment,
			code varchar(50) not null,
			blog_id bigint not null,
			min_value int not null,
			max_value int not null,
			time bigint not null,
			status tinyint not null default 1,
			primary key(ID),
			unique(code)
			
		)";
		
		$sql[] = "create table if not exists $this->code_meta(
			meta_id bigint not null auto_increment,
			code_id bigint not null,
			meta_key text not null,
			meta_value text not null,
			primary key(meta_id)
		)";
		
		
		foreach($sql as $s){
			$this->db->query($s);
		}
	}
	
	
	//retun a key object
	function get_key($ID = null){
		return $this->db->get_row("select * from $this->code where ID = '$ID'");
	}
	
	
	//return the meta values of a key
	function get_key_metas($code_id = null){
		$metas = array();
		$results = $this->db->get_results("select * from $this->code_meta where code_id = '$code_id'");

		if($results){
			foreach($results as $result){
				$metas[$result->meta_key] = $result->meta_value;
			}
		}
		
		return $metas;
	}
	
	
	/**
	 * create a code 
	 * */
	function create_code($posted = array()){
		if(empty($posted)) $posted = $_POST;
		
		if($posted['code_id'] > 0) return $this->update_code($posed);
		
		//functional area
		$uniqe_code = $this->generate_code();
		$data = array(
			'code' => $uniqe_code,
			'min_value' => $posted['code_min'],
			'max_value' => $posted['code_max'],
			'time' => current_time('timestamp'),
			'blog_id' => get_current_blog_id(),
							
		);
		
				
		$id = $this->db->insert($this->code, $data, array('%s', '%d', '%d', '%d', '%d'));
		
		if($id){
			return $this->db->insert_id;
		}
		else{
			return 0;
		}
		
	}
	
	
	//update an exising code
	function update_code($posted){
		$data = array(
			'min_value' => $posted['code_min'],
			'max_value' => $posted['code_max'],
			'blog_id' => get_current_blog_id(),
		);
		
		if($this->db->update($this->code, $data, array('ID' => $posted['code_id']), array('%d', '%d', '%d'), array('%d'))){
			return $posted['code_id'];
		}
		else{
			return 0;
		}
	}
	
	
	//generation of codes using wordrpess default function
	function generate_code(){
		if(!function_exists('wp_generate_password')) {
			include ABSPATH . 'wp-includes/pluggable.php';
		}
		
		$code = wp_generate_password(12, false, false);
		if($this->is_unique($code)){
			return $code;
		}
		else{
			return $this->generate_code();
		}
	}
	
	//uniqueness identification
	function is_unique($code = ''){
		$c = $this->db->get_var("select * from $this->code where code like '$code'");
		
		return $c ? false : true;
	}
	
	
	//update code meta
	function update_code_meta($code_id, $meta){
		if($meta){
			foreach($meta as $key => $value){
				if($this->db->get_var("select meta_id from $this->code_meta where meta_key like '$key' and code_id = '$code_id'")){
					$this->db->update($this->code_meta, array('meta_value' => $value), array('code_id' => $code_id, 'meta_key' => $key), array('%s'), array('%d', '%s'));
				}
				else{
					$this->db->insert($this->code_meta, array('code_id' => $code_id, 'meta_key' => $key, 'meta_value' => $value));
				}
			}
		}
	}
	
	
	//get codes
	function get_codes($args){
		$defaults = array(
			'limit' => 30,
			'offset' => 0,
			'orderby' => 'time',
			'order' => 'DESC'					
		);
		
		$args = wp_parse_args($args, $defaults);
		extract($args, EXTR_SKIP);
		
		$sql = "select * from $this->code order by $orderby $order limit $limit offset $offset";
		
		return $this->db->get_results($sql);
	}
	
	
	function delete_a_code($code_id){
		$this->db->query("delete from $this->code where ID = '$code_id'");
		$this->db->query("delete from $this->code_meta where code_id = '$code_id'");
	}
	
	
	/**
	 * get a code matching the donation amount
	 * */
	function get_used_code_by_amount($amount){
		$sql = "select * from $this->code where min_value <= '$amount' and max_value >= '$amount limit 1";
		return $this->db->get_row($sql);
	}
	
	
	/**
	 * changing the code status
	 * */
	function change_code_status($code_id, $status){
		return $this->db->update($this->code, array('status' => $status), array('ID' => $code_id), array('%d'), array('%d'));
	}
}