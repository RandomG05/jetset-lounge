<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard extends MY_Controller {
	
	public $table;
		
	function __construct()
	{
		parent::__construct();
		$this->prefix_apps = config_item('db_prefix');
		$this->prefix = config_item('db_prefix2');
		$this->load->model('model_databilling', 'm');
		$this->load->model('model_billingdetail', 'm2');
	}

	public function gridData(){

	}
	
	public function gridData_customerVisit(){
		$this->table = $this->prefix.'billing';
		$params = array(
			'fields' 		=> "a.customer_id, i.customer_name",
			'primary_key'	=> "id",
			'table'			=> $this->table.'as a',
			'join'			=> array(
									'many',
									array(
										array($this->prefix.'customer as i','i.id = a.customer_id', 'LEFT')
									)
								),
			'where'			=> array('a.is_deleted' => 0),
			'order'			=> array('a.id' => 'DESC'),
			'single'		=> false,
			'output'		=> 'array'
		);
		$getData = $this->m->find_all($params);
		die(json_encode($getData));
	}

	public function gridData_categoryPurchase(){

	}

	
}