<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class reportHistoryStock extends MY_Controller {
	
	public $table;
		
	function __construct()
	{
		parent::__construct();
		$this->prefix_apps = config_item('db_prefix');
		$this->prefix = config_item('db_prefix2');
		$this->load->model('model_receivinglist', 'm');
		$this->load->model('model_stock', 'stock');
	}
	
	public function print_reportHistoryStock(){
		
		$this->table_receiving = $this->prefix.'receiving';
		$this->table_receiving_detail = $this->prefix.'receive_detail';
		$this->table_distribution = $this->prefix.'distribution';
		$this->table_distribution_detail = $this->prefix.'distribution_detail';
		$this->table_items = $this->prefix.'items';
		$this->table_item_category = $this->prefix.'item_category';
		$this->table_stock = $this->prefix.'stock';
		$this->table_stock_rekap = $this->prefix.'stock_rekap';
		
		$session_user = $this->session->userdata('user_username');					
		$user_fullname = $this->session->userdata('user_fullname');					
		
		if(empty($session_user)){
			die('Sesi Login sudah habis, Silahkan Login ulang!');
		}
		
		extract($_GET);
		
		if(empty($month)){ $month = date('m'); }
		if(empty($year)){ $year = date('Y'); }			
		
		$data_post = array(
			'do'	=> '',
			'report_data'	=> array(),
			'report_place_default'	=> '',
			'report_name'	=> 'HISTORY STOCK',
			'month'	=> $month,
			'year'	=> $year,
			'user_fullname'	=> $user_fullname
		);
		
		$get_opt = get_option_value(array('report_place_default','hide_empty_stock_on_report'));
		if(!empty($get_opt['report_place_default'])){
			$data_post['report_place_default'] = $get_opt['report_place_default'];
		}
		if(!empty($get_opt['hide_empty_stock_on_report'])){
			$data_post['hide_empty_stock_on_report'] = $get_opt['hide_empty_stock_on_report'];
		}
		
		//PREPARING DAYS
		$mkDay = strtotime("01-".$month."-".$year);
		$total_days = date("t", $mkDay);
		$default_data = array(
			'item_name'	=> '',
			'item_id'	=> '',
			'category_name'	=> '',
			'category_id'	=> '',
			'item_code'	=> '',
			'satuan'	=> '',
			'stock_awal'	=> 0,
			'total_in'	=> 0,
			'total_out'	=> 0,
			'stock_akhir'	=> 0					
		);
		
		for($i=1; $i <= $total_days; $i++){
			
			$i_txt = $i;
			if(strlen($i_txt) == 1){
				$i_txt = '0'.$i_txt;
			}
			$default_data['in_'.$i_txt] = 0;
			$default_data['out_'.$i_txt] = 0;
		}
		
		//echo '<pre>';
		//print_r($default_data);
		//die();
				
		$qdate_from = date($year."-".$month."-01");
		$qdate_till = date($year."-".$month."-".$total_days);
		
		$all_item = array();
		$all_item_id = array();
		
		//GET CATEGORY
		$allCat = array();
		$allCat_item = array();
		$allCat_item_id = array();
		$this->db->from($this->table_item_category);
		$getCat = $this->db->get();
		if($getCat->num_rows() > 0){
			foreach($getCat->result_array() as $dt){
				$allCat[$dt['id']] = $dt['item_category_name'];
			}
		}
		
		
		if(empty($storehouse_id)){
			die('select Warehouse!');
			//$storehouse_id = $this->stock->get_primary_storehouse();
			//$storehouse_id = -1;
		}
		
		if($storehouse_id == "null"){
			die('Select Warehouse!');
		}
		
		//die($storehouse_id);
		
		//GET ALL ITEM
		$this->db->select("x.item_id, c.*, c.id as item_id, d.unit_name as satuan, e.item_category_name");
		$this->db->from($this->prefix."stock as x");
		$this->db->join($this->table_items.' as c',"c.id = x.item_id");
		$this->db->join($this->prefix.'unit as d','d.id = c.unit_id','LEFT');
		$this->db->join($this->table_item_category.' as e','e.id = c.category_id','LEFT');
		$this->db->where("c.is_deleted = 0");
		
		if(!empty($storehouse_id)){
			$this->db->where('x.storehouse_id', $storehouse_id);	
		}else{
			$this->db->where('x.storehouse_id', -1);
		}
		
		$this->db->group_by("x.item_id");
		$this->db->order_by("c.item_code","ASC");
		$get_items = $this->db->get();
		if($get_items->num_rows() > 0){
			foreach($get_items->result_array() as $dtR){
				if(!in_array($dtR['item_id'], $all_item_id)){
					//create data
					$all_item_id[] = $dtR['item_id'];
					$preparing_data = $default_data;
					$preparing_data['item_id'] = $dtR['item_id'];
					$preparing_data['item_name'] = $dtR['item_name'];
					$preparing_data['category_name'] = $dtR['item_category_name'];
					$preparing_data['category_id'] = $dtR['category_id'];
					$preparing_data['item_code'] = $dtR['item_code'];
					$preparing_data['satuan'] = $dtR['satuan'];
					$all_item[$dtR['item_id']] = $preparing_data;
				}
		
				if(!in_array($dtR['item_id'], $allCat_item_id)){
					$allCat_item_id[] = $dtR['item_id'];
		
					if(empty($allCat_item[$dtR['category_id']])){
						$allCat_item[$dtR['category_id']] = array();
					}
						
					$allCat_item[$dtR['category_id']][] = $dtR['item_id'];
				}
			}
		}
		
		
		$add_where = "(a.trx_date = '".$qdate_from."')";
		
		$item_with_stock_awal = array();
		//cek stock rekap
		$all_item_rekap = array();
		$this->db->select("a.*");
		$this->db->from($this->table_stock_rekap." as a");
		$this->db->where($add_where);
		if(!empty($storehouse_id)){
			$this->db->where('a.storehouse_id', $storehouse_id);
		}
		$this->db->order_by("a.trx_date","ASC");
		$get_rekap = $this->db->get();
		if($get_rekap->num_rows() > 0){
			foreach($get_rekap->result_array() as $dtR){
				//$all_item_rekap[$dtR['item_id']] = $dtR;
				
				//STOK AWAL
				if(!empty($all_item[$dtR['item_id']])){
					$all_item[$dtR['item_id']]['stock_awal'] += $dtR['total_stock_kemarin'];
					$all_item[$dtR['item_id']]['stock_akhir'] += $dtR['total_stock_kemarin'];
					
					if($all_item[$dtR['item_id']]['stock_awal'] > 0){
						if(!in_array($dtR['item_id'], $item_with_stock_awal)){
							$item_with_stock_awal[] = $dtR['item_id'];
						}
					}
				}
				
			}
		}
		
		
		//$all_item_list = $all_item;
		
		//$all_item = array();
		
		//echo count($get_rekap->result_array()).'<pre>';
		//print_r($get_rekap->result_array());
		//die();
		
		//VERSI STOCK TRX
		$available_stok_trx = array();
		$add_where = "(a.trx_date >= '".$qdate_from."' AND a.trx_date <= '".$qdate_till."')";
		$this->db->select("a.*");
		$this->db->from($this->table_stock." as a");
		$this->db->where($add_where);
		if(!empty($storehouse_id)){
			$this->db->where('a.storehouse_id', $storehouse_id);
		}
		
		$get_trx = $this->db->get();
		if($get_trx->num_rows() > 0){
			foreach($get_trx->result_array() as $dtR){
				
				/*if(empty($all_item[$dtR['item_id']])){
					if(!empty($all_item_list[$dtR['item_id']])){
						
						if(!in_array($dtR['item_id'], $available_stok_trx)){
							$available_stok_trx[] = $dtR['item_id'];
							$all_item[$dtR['item_id']] = $all_item_list[$dtR['item_id']];
						}
						
					}
				}*/
				
				if(!empty($all_item[$dtR['item_id']])){
					//get day
					$mkday_recv = strtotime($dtR['trx_date']);
					$get_day = date("d",$mkday_recv);
						
					if($dtR['trx_type'] == 'in'){
						$all_item[$dtR['item_id']]['total_in'] += $dtR['trx_qty'];
						$all_item[$dtR['item_id']]['stock_akhir'] += $dtR['trx_qty'];						
						$all_item[$dtR['item_id']]['in_'.$get_day] += $dtR['trx_qty'];
					}
					
					if($dtR['trx_type'] == 'out'){
						$all_item[$dtR['item_id']]['total_out'] += $dtR['trx_qty'];
						$all_item[$dtR['item_id']]['stock_akhir'] -= $dtR['trx_qty'];
						$all_item[$dtR['item_id']]['out_'.$get_day] += $dtR['trx_qty'];
					}
					
					//if($all_item[$dtR['item_id']]['stock_akhir'] <= 0.00001){
					//	$all_item[$dtR['item_id']]['stock_akhir'] = 0;
					//}
					
					
				}
			}
		}
		
		//MAKE SURE ITEM WITH STOCK SHOWN
		if(!empty($get_opt['hide_empty_stock_on_report'])){
			$newItem = array();
			foreach($all_item as $itemID => $itemDt){
				if(empty($itemDt['stock_awal']) AND empty($itemDt['total_in']) AND empty($itemDt['total_out'])){
					
				}else{
					$newItem[$itemID] = $itemDt;
				}
			}
			
			$all_item = $newItem;
		}
		
		//echo '<pre>';
		//print_r($all_item);
		//print_r($allCat_item);
		//die();
		
		//GROUPING BY
		
		$data_post['report_data'] = $all_item;
		$data_post['total_days'] = $total_days;
		$data_post['category_data'] = $allCat;
		$data_post['category_item_data'] = $allCat_item;
		
		//DO-PRINT
		if(!empty($do)){
			$data_post['do'] = $do;
		}else{
			$do = '';
		}
		
		$useview = 'print_reportHistoryStock';
		if($do == 'excel'){
			$useview = 'excel_reportHistoryStock';
		}else{
			if(count($data_post['report_data']) > 1500){
				die('data item '.count($data_post['report_data']).', this report has long time execution<br/>Please try export to Excel!');
			}
		}
		
				
				
		$this->load->view('../../inventory/views/'.$useview, $data_post);	
	}
	

}