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
		$this->table = $this->prefix.'billing';
		$this->table2 = $this->prefix.'billing_detail';		
		$session_user = $this->session->userdata('user_username');	
		$role_id = $this->session->userdata('role_id');	
		
		$opt_value = array(
			'no_midnight',
			'cashier_max_pembulatan',
			'cashier_pembulatan_keatas',
			'payment_id_cash',
			'payment_id_debit',
			'payment_id_credit',
			'jam_operasional_from','jam_operasional_to','jam_operasional_extra',
			'hide_hold_bill_yesterday',
			'nontrx_override_on'
		);
		$get_opt = get_option_value($opt_value);
		
		$hide_hold_bill_yesterday = 0;
		if(!empty($get_opt['hide_hold_bill_yesterday'])){
			$hide_hold_bill_yesterday = $get_opt['hide_hold_bill_yesterday'];
		}
		
		$nontrx_override_on = 0;
		if(!empty($get_opt['nontrx_override_on'])){
			$nontrx_override_on = $get_opt['nontrx_override_on'];
		}
		
		$no_midnight = 0;
		if(!empty($get_opt['no_midnight'])){
			$no_midnight = 1;
		}
		
		//is_active_text
		$sortAlias = array(
			'is_active_text' => 'a.is_active',
			'billing_date' => 'a.created',
			'updated_date' => 'a.updated',
			'updated_time' => 'a.updated',
			'payment_time' => 'a.payment_date',
			'table_no' => 'a.table_no',
			'billing_no_show' => 'a.billing_no',
			'txmark_no_show' => 'a.txmark_no',
			'tax_total_show' => 'a.tax_total',
			'payment_note2' => 'a.payment_type_name',
			'updatedby' => 'a.updatedby'
		);		
		
		// Default Parameter
		$params = array(
			'fields'		=> 'a.id, a.table_id, a.table_no, a.billing_no, a.payment_date,
								a.billing_status, a.billing_notes, a.total_pembulatan, a.total_billing, a.grand_total, a.total_paid, a.payment_id, a.bank_id,
								a.card_no, a.include_tax, a.tax_percentage, a.tax_total, a.include_service, a.service_percentage, a.service_total, 
								a.discount_id, a.discount_notes, a.discount_percentage, a.discount_price, a.discount_total, a.voucher_no, a.total_hpp, 
								a.is_active, a.total_dp, a.compliment_total, a.total_cash, a.total_credit, a.createdby, a.updatedby, 
								a.merge_id, a.merge_main_status, a.split_from_id, a.total_guest, a.total_crew, a.total_gh, a.lock_billing, a.qc_notes,
								a.created, a.updated, a.takeaway_no_tax, a.takeaway_no_service, a.is_compliment,  
								a.discount_perbilling, a.total_return, a.compliment_total_tax_service, a.is_half_payment,
								a.sales_id, a.sales_percentage, a.sales_price, a.sales_type, a.customer_id, a.block_table,
								a.id as billing_id, b.table_name, b.table_no, b.table_desc, b.floorplan_id, c.floorplan_name, a.is_reservation,
								a.txmark, a.txmark_no,
								d.payment_type_name, e.user_firstname, e.user_lastname, f.bank_name, 
								g.billing_no as merge_billing_no, h.sales_name, h.sales_company, i.customer_name',
			'primary_key'	=> 'id',
			'table'			=> $this->table.' as a',
			'join'			=> array(
									'many', 
									array( 
										array($this->prefix.'table as b','b.id = a.table_id','LEFT'),
										array($this->prefix.'floorplan as c','c.id = b.floorplan_id','LEFT'),
										array($this->prefix.'payment_type as d','d.id = a.payment_id','LEFT'),
										array($this->prefix_apps.'users as e','e.user_username = a.updatedby','LEFT'),
										array($this->prefix.'bank as f','f.id = a.bank_id','LEFT'),
										array($this->prefix.'billing as g','g.id = a.merge_id','LEFT'),
										array($this->prefix.'sales as h','h.id = a.sales_id','LEFT'),
										array($this->prefix.'customer as i','i.id = a.customer_id','LEFT')
									) 
								),
			'where'			=> array('a.is_deleted' => 0),
			'order'			=> array('a.id' => 'DESC'),
			'sort_alias'	=> $sortAlias,
			'single'		=> false,
			'output'		=> 'array' //array, object, json
		);
		
		//DROPDOWN & SEARCHING
		$is_dropdown = $this->input->post('is_dropdown');
		$searching = $this->input->post('query');
		$billing_status = $this->input->post('billing_status');
		$is_peruser = $this->input->post('is_peruser');
		$report_paid_order = $this->input->post('report_paid_order');
		$use_payment_date = $this->input->post('use_payment_date');
		$sorting_by = $this->input->post('sorting_by');
		
		//FILTER
		$shift_billing = $this->input->post('shift_billing');
		$user_cashier = $this->input->post('user_cashier');
		$skip_date = $this->input->post('skip_date');
		$date_from = $this->input->post('date_from');
		$date_till = $this->input->post('date_till');
		$keywords = $this->input->post('keywords');
		$use_range_date = $this->input->post('use_range_date');
		$by_product_order = $this->input->post('by_product_order');
		$txmark_only = $this->input->post('txmark_only');
		
		//update-2001.002
		$table_id = $this->input->post('table_id');
		//$use_range_date = 0;
		
		if(!empty($keywords)){
			$searching = $keywords;
		}
		
		if(!empty($is_peruser)){
			if(!in_array($role_id, array(1,2))){
				$params['where'][] = "(a.updatedby = '".$session_user."')";
			}
		}
		if(!empty($user_cashier)){
			//$this->db->where('a.updatedby', $user_cashier);
			$params['where'][] = "(a.updatedby = '".$user_cashier."')";
		}
		if(!empty($txmark_only)){
			$params['where'][] = "(a.txmark = 1)";
		}
		if(!empty($table_id)){
			$params['where'][] = "(a.table_id = ".$table_id.")";
		}
		if(!empty($report_paid_order)){
			$params['order'] = array('a.id' => $report_paid_order);
		}
		if(!empty($is_dropdown)){
			$params['order'] = array('a.billing_no' => 'ASC');
		}
		if(!empty($searching)){
			
			if(!empty($by_product_order)){
				
			}else{
				$params['where'][] = "(a.billing_no LIKE '%".$searching."%')";
			}
			
		}
		
		//merge_bill_id
		$merge_bill_id = $this->input->post('merge_bill_id', true);
		if(!empty($merge_bill_id)){
			$params['where'][] = "a.id IN (".$merge_bill_id.")";
			$billing_status = 'hold';
		}
		
		if(!empty($billing_status)){
			$params['where'][] = "(a.billing_status = '".$billing_status."')";
			
			if($billing_status == 'cancel'){
				$params['where'][] = "(a.merge_id IS NULL OR a.merge_id = 0)";
			}
			
			//update-2007.001
			if($nontrx_override_on == 1 AND $billing_status == 'paid'){
				$params['where'][] = "(a.txmark = 1)";
			}
			
		}else{
			$params['where'][] = "(a.billing_status = '-')";
		}
		
		//update-2002.001
		if(isset($_POST['use_range_date'])){
			if(!empty($use_range_date)){
				$skip_date = false;
			}else{
				$skip_date = true;
			}
		}
		
		if($skip_date == true){
		
		}else{
		
			//update-2001.002
			$use_datenow = false;
			if(empty($date_from)){
				$date_from = date('d-m-Y H:i:s');
				$use_datenow = true;
			}
			
			if(!empty($date_from)){
			
				$mktime_dari = strtotime($date_from);
				
				//update-2001.002
				if($use_datenow == true){
					
					$billing_time = date('G');
					$datenowstr = strtotime(date("d-m-Y H:i:s"));
					$datenowstr0 = strtotime(date("d-m-Y 00:00:00"));
					
					$jam_operasional_from = 7;
					$jam_operasional_from_Hi = '07:00';
					if(!empty($get_opt['jam_operasional_from'])){
						$jm_opr_mktime = strtotime(date("d-m-Y")." ".$get_opt['jam_operasional_from']);
						$jam_operasional_from = date('G',$jm_opr_mktime);
						$jam_operasional_from_Hi = date('H:i',$jm_opr_mktime);
					}
					
					$jam_operasional_to = 23;
					$jam_operasional_to_Hi = '23:00';
					if(!empty($get_opt['jam_operasional_to'])){
						if($get_opt['jam_operasional_to'] == '24:00'){
							$get_opt['jam_operasional_to'] = '23:59:59';
						}
						$jm_opr_mktime = strtotime(date("d-m-Y")." ".$get_opt['jam_operasional_to']);
						$jam_operasional_to = date('G',$jm_opr_mktime);
						$jam_operasional_to_Hi = date('H:i',$jm_opr_mktime);
					}
					
					$jam_operasional_extra = 0;
					if(!empty($get_opt['jam_operasional_extra'])){
						$jam_operasional_extra = $get_opt['jam_operasional_extra'];
					}
					
					if($billing_time < $jam_operasional_from){
						//extra / early??
			
						//check extra
						$datenowstrmin1 = $datenowstr0-ONE_DAY_UNIX;
						$datenowstr_oprfrom = strtotime(date("d-m-Y", $datenowstrmin1)." ".$jam_operasional_from_Hi.":00");
						$datenowstr_oprto_org = strtotime(date("d-m-Y", $datenowstrmin1)." ".$jam_operasional_to_Hi.":00");
						$datenowstr_oprto = strtotime(date("d-m-Y", $datenowstrmin1)." ".$jam_operasional_to_Hi.":00");
						//add extra
						if(!empty($jam_operasional_extra)){
							$datenowstr_oprto += ($jam_operasional_extra*3600);
						}
						
						if($datenowstr < $datenowstr_oprto){
							$date_from = date('d-m-Y H:i:s', $datenowstr_oprfrom);
							$date_till = date('d-m-Y H:i:s', $datenowstr_oprto);
						}else{
							$date_from = date('d-m-Y H:i:s', $datenowstr_oprfrom+ONE_DAY_UNIX);
							$date_till = date('d-m-Y H:i:s', $datenowstr_oprto+ONE_DAY_UNIX);
						}
						
					}else{
			
						$datenowstr_oprfrom = strtotime(date("d-m-Y", $datenowstr0)." ".$jam_operasional_from_Hi.":00");
						$datenowstr_oprto_org = strtotime(date("d-m-Y", $datenowstr0)." ".$jam_operasional_to_Hi.":00");
						$datenowstr_oprto = strtotime(date("d-m-Y", $datenowstr0)." ".$jam_operasional_to_Hi.":00");
						//add extra
						if(!empty($jam_operasional_extra)){
							$datenowstr_oprto += ($jam_operasional_extra*3600);
						}
						
						if($datenowstr < $datenowstr_oprto){
							$date_from = date('d-m-Y H:i:s', $datenowstr_oprfrom);
							$date_till = date('d-m-Y H:i:s', $datenowstr_oprto);
						}
						
					}
				}
				
				$mktime_dari = strtotime($date_from);
				$qdate_from = date("Y-m-d H:i:s",strtotime($date_from));
				
				//if($billing_status == 'paid' || $billing_status == 'cancel'){
					if(empty($date_till)){ $date_till = date('d-m-Y H:i:s'); }
					$qdate_till = date("Y-m-d H:i:s",strtotime($date_till));
				//}
				
				$qdate_till_max = date("Y-m-d H:i:s",strtotime($qdate_till)+ONE_DAY_UNIX);
				
				//jam_operasional
				$mktime_dari = strtotime($date_from);
				$mktime_sampai = strtotime($date_till);
				$ret_dt = check_report_jam_operasional($get_opt, $mktime_dari, $mktime_sampai);
				$qdate_from = $ret_dt['qdate_from'];
				$qdate_till = $ret_dt['qdate_till'];
				$qdate_till_max = $ret_dt['qdate_till_max'];
				
				//update-2003.001
				$qdate_from_mk = strtotime($qdate_from);
				
				if(!empty($use_payment_date)){
					//07:00:00
					//$params['where'][] = "(a.payment_date >= '".$qdate_from." 00:00:00' AND a.payment_date <= '".$qdate_till_max." 23:59:59')";
					$params['where'][] = "(a.payment_date >= '".$qdate_from."' AND a.payment_date <= '".$qdate_till_max."')";
				}else{
				
					//exception
					if($billing_status == 'hold' OR $billing_status == 'paid'){						
						
						if($billing_status == 'paid'){
							$qdate_from = date("Y-m-d H:i:s",strtotime($qdate_from));
						}else{
							$qdate_from = date("Y-m-d H:i:s",strtotime($qdate_from)-ONE_DAY_UNIX);
							
							//update-2002.001
							//$qdate_till_max = date("Y-m-d H:i:s",strtotime($qdate_till_max)-ONE_DAY_UNIX);
							$qdate_till_max = date("Y-m-d H:i:s",strtotime($qdate_till_max));
						}
					}
					
					if($no_midnight == 1){
						$qdate_from = date("Y-m-d H:i:s",strtotime($qdate_from));
					}
				
					if($billing_status == 'paid'){
						//$params['where'][] = "(a.payment_date >= '".$qdate_from." 00:00:01' AND a.payment_date <= '".$qdate_till_max." 06:00:00')";
						$params['where'][] = "(a.payment_date >= '".$qdate_from."' AND a.payment_date <= '".$qdate_till_max."')";
					}else{
						//$params['where'][] = "(a.updated >= '".$qdate_from." 00:00:01' AND a.updated <= '".$qdate_till_max." 06:00:00')";
						//$params['where'][] = "(a.updated >= '".$qdate_from."' AND a.updated <= '".$qdate_till_max."')";
						//$params['where'][] = "(a.updated >= '".$qdate_from."' AND a.updated <= '".$qdate_till_max."')";
						$params['where'][] = "(a.updated >= '2000-01-24 00:01:01' AND a.updated <= '".$qdate_till_max."')";
					}
					
					
				}
				
				//update-2003.001
				if(empty($searching)){
					if(!empty($hide_hold_bill_yesterday)){
						$lastest_billing_no = date("ymd", $qdate_from_mk).'0000';
						$params['where'][] = "(a.billing_no >= '".$lastest_billing_no."')";
					}				
				}						
			}
		}
		
		if(!empty($by_product_order)){
			
			$this->db->select("DISTINCT(a.billing_id), b.product_name");
			$this->db->from($this->table2." as a");
			$this->db->join($this->prefix.'product as b',"b.id = a.product_id","LEFT");
			
			if(!empty($searching)){
				$this->db->where("b.product_name LIKE '%".$searching."%'");
			}else{
				$this->db->where("a.product_id = -1");
			}
			
			$get_det = $this->db->get();
			
			$all_bill_id = array();
			if($get_det->num_rows() > 0){
				foreach($get_det->result() as $dt){
					if(!in_array($dt->billing_id, $all_bill_id)){
						$all_bill_id[] = $dt->billing_id;
					}
				}
			}
			
			if(!empty($all_bill_id)){
				$all_bill_id_txt = implode(",", $all_bill_id);
				$params['where'][] = "a.id IN (".$all_bill_id_txt.")";
			}
			
		}
		
		//SORTING BY
		if(!empty($sorting_by)){
			$params['order'] = array('a.'.$sorting_by => 'DESC');
		}
				
		//get data -> data, totalCount
		$get_data = $this->m->find_all($params);
		
		
		if(empty($get_opt['cashier_max_pembulatan'])){
			$get_opt['cashier_max_pembulatan'] = 0;
		}
		if(empty($get_opt['cashier_pembulatan_keatas'])){
			$get_opt['cashier_pembulatan_keatas'] = 0;
		}
  		
		$payment_id_cash = 1;
		if(empty($get_opt['payment_id_cash'])){
			$payment_id_cash = $get_opt['payment_id_cash'];
		}
  		
		$payment_id_debit = 1;
		if(empty($get_opt['payment_id_debit'])){
			$payment_id_debit = $get_opt['payment_id_debit'];
		}
  		
		$payment_id_credit = 1;
		if(empty($get_opt['payment_id_credit'])){
			$payment_id_credit = $get_opt['payment_id_credit'];
		}
  		
		$all_bil_id = array();
  		$newData = array();
		$no = 1;
		if(!empty($get_data['data'])){
			foreach ($get_data['data'] as $s){
				$s['is_active_text'] = ($s['is_active'] == '1') ? '<span style="color:green;">Active</span>':'<span style="color:red;">Inactive</span>';
				$s['item_no'] = $no;
				$s['payment_time'] = date("H:i",strtotime($s['payment_date']));
				$s['payment_date'] = date("d-m-Y H:i",strtotime($s['payment_date']));
				$s['billing_date'] = date("d-m-Y H:i",strtotime($s['created']));
				
				if(empty($s['group_date'])){
					$s['group_date'] = date("d-m-Y",strtotime($s['created']));
				}else{
					$s['group_date'] = date("d-m-Y",strtotime($s['group_date']));
				}
				
				$s['created_datetime'] = date("d.m.Y H:i",strtotime($s['created']));
				
				$s['created_date'] = date("d-m-Y H:i",strtotime($s['created']));
				$s['updated_time'] = date("H:i",strtotime($s['updated']));
				$s['updated_date'] = date("d-m-Y H:i",strtotime($s['updated']));
				
				if(!in_array($s['id'], $all_bil_id)){
					$all_bil_id[] = $s['id'];
				}				
				
				if(empty($s['tax_total'])){
					$s['tax_total'] = 0;
				}
				
				if(empty($s['service_total'])){
					$s['service_total'] = 0;
				}
				
				if(empty($s['discount_total'])){
					$s['discount_total'] = 0;
				}
				
				if(empty($s['total_dp'])){
					$s['total_dp'] = 0;
				}
				
				if(empty($s['compliment_total'])){
					$s['compliment_total'] = 0;
				}
				
				if(!empty($s['include_tax']) OR !empty($s['include_service'])){
					$s['total_billing_display'] = $s['total_billing'];
					
					if(!empty($s['include_tax'])){
						$s['total_billing_display'] += $s['tax_total'];
					}
					if(!empty($s['include_service'])){
						$s['total_billing_display'] += $s['service_total'];
					}
					
				}else{
					$s['total_billing_display'] = $s['total_billing'];
				}
				
				//SUB TOTAL
				$s['subtotal_billing'] = $s['total_billing']-$s['discount_total'];
				$s['total_billing_display'] = $s['subtotal_billing'];
				
				$s['total_billing_show'] = priceFormat($s['total_billing']);
				$s['total_paid_show'] = priceFormat($s['total_paid']);
				$s['tax_total_show'] = priceFormat($s['tax_total']);
				$s['service_total_show'] = priceFormat($s['service_total']);
				$s['discount_total_show'] = priceFormat($s['discount_total']);
				$s['compliment_total_show'] = priceFormat($s['compliment_total']);
				$s['total_dp_show'] = priceFormat($s['total_dp']);
				$s['user_fullname'] = $s['user_firstname'].' '.$s['user_lastname'];
						
					
				if(!empty($s['is_compliment'])){
					$s['total_billing'] = $s['total_billing'] + $s['tax_total'] + $s['service_total'];
					//DEPRECATED
					//if(!empty($s['include_tax']) OR !empty($s['include_service'])){
					//	$s['total_billing'] = $s['total_billing'];
					//}
					$s['service_total'] = 0;
					$s['tax_total'] = 0;
				}	
				
				if(empty($s['grand_total'] )){
					$s['grand_total'] = $s['total_billing'] + $s['tax_total'] + $s['service_total'];
					if(!empty($s['include_tax']) OR !empty($s['include_service'])){
						$s['grand_total'] = $s['total_billing'];
					}
				}
				
				
				$s['grand_total_show'] = priceFormat($s['grand_total']);
				
				$s['total_qty_order'] = 0;
				$s['total_qty_deliver'] = 0;
				$s['order_total'] = 0;
				$s['order_total_show'] = 0;
				$s['total_hpp'] = 0;
				$s['total_hpp_show'] = 0;
				$s['total_profit'] = 0;
				$s['total_profit_show'] = 0;
				$s['percent_status_order'] = 0;
				
				//NOTES
				$s['payment_note'] = '';
				$s['payment_note2'] = '';
				if(!empty($s['is_compliment'])){
					$s['payment_note'] = 'COMPLIMENT';
					$s['payment_note2'] = 'COMPLIMENT';
				}
				
				if($s['billing_status'] == 'paid'){
					if(!empty($s['is_half_payment'])){
						$s['payment_note'] = 'HALF PAYMENT';
						$s['payment_note2'] = 'HALF';
						
						$s['total_paid'] = $s['total_cash'];
						$s['total_paid_show'] = priceFormat($s['total_paid']);
						
					}else{
											
						if($s['payment_id'] != $payment_id_cash){
							if(empty($s['total_credit'])){
								$s['total_credit'] = $s['total_billing'];
							}
							$s['total_cash'] = 0;
						}else{
							if(empty($s['total_cash'])){
								$s['total_cash'] = $s['total_billing'];
							}
							$s['total_credit'] = 0;
						}
						
					}
				}
				
				//if(strtolower($s['payment_type_name']) != 'cash'){
				if($s['payment_id'] != $payment_id_cash){
					$s['payment_note'] = strtoupper($s['bank_name']).' '.$s['card_no'];
					
					if(empty($s['payment_note2'])){
						$s['payment_note2'] = $s['payment_type_name'].' / '.strtoupper($s['bank_name']);
					}else{
						$s['payment_note2'] .= '-'.$s['payment_type_name'].' / '.strtoupper($s['bank_name']);
					}
					
				}else{
					if(empty($s['payment_note2'])){
						$s['payment_note2'] = 'CASH';
					}
				}
				
				if(empty($s['payment_id'])){
					$s['payment_id'] = 1;
					$s['payment_type_name'] = 'Cash';
				}
				
				$s['total_cash_show'] = priceFormat($s['total_cash']);
				$s['total_credit_show'] = priceFormat($s['total_credit']);
				
				$s['split_merge_status'] = '';
				if(!empty($s['split_from_id'])){
					$s['split_merge_status'] = 'SPLIT';
				}
				if(!empty($s['merge_id'])){
					$s['split_merge_status'] = 'MERGE';
				}
				
				$s['max_pembulatan'] = $get_opt['cashier_max_pembulatan'];
				$s['pembulatan_keatas'] = $get_opt['cashier_pembulatan_keatas'];
				
				//sales
				$s['sales_name_company_fee'] = '-- NO SALES --';
				if(!empty($s['sales_id'])){
					$sales_type_simple = 'A';
					if($s['sales_type'] == 'before_tax'){
						$sales_type_simple = 'B';
					}
					if(!empty($s['sales_percentage'])){
						$jenis_fee = $s['sales_percentage'].'%';
					}else{
						$jenis_fee = $s['sales_price'];
					}
					
					$s['sales_name_company_fee'] = $s['sales_name'].' / '.$s['sales_company'].' ('.$sales_type_simple.' '.$jenis_fee.')';
				}
				
				$s['billing_no_show'] = $s['billing_no'];
				if(!empty($s['is_reservation'])){
					$s['billing_no_show'] = 'R'.$s['billing_no'];
				}
				
				$s['txmark_no_show'] = '-';
				if(!empty($s['txmark_no'])){
					$s['txmark_no_show'] = '<span style="color:green;font-weight:bold;">'.$s['txmark_no'].'</span>';
				}
				
				//update-2001.002
				$s['table_button'] = 0;
				$s['billing_info'] = '';
				$s['billing_color'] = '0bab00';
				if(!empty($table_id) AND !empty($billing_status)){
					
					if($no == 1){
						$backup_id = $s['id'];
						$s['id'] = 0;
						
						$s['billing_info'] = '<div style="font-size:12px; margin:5px 0px 5px;">Table yg dipilih:</div>';
						$s['billing_info'] .= '<div style="font-size:22px; margin:15px 0px 20px;"><b>'.$s['table_no'].'</b></div>';
						$s['billing_info'] .= '<div style="font-size:10px;">Klik u/ lihat Table Lainnya</div>';
						
						$s['billing_color'] = '008abf';
						$s['table_button'] = 1;
						array_push($newData, $s);
						
						$s['billing_color'] = '0bab00';
						$s['table_button'] = 0;
						$s['id'] = $backup_id;
					}
					
					if(empty($s['qc_notes'])){
						$s['qc_notes'] = '-';
					}
					
					$s['billing_info'] = '<div style="font-size:12px; margin:5px 0px 5px; line-height:14px;"><b>No.'.$s['billing_no'].'</b></div>';
					$s['billing_info'] .= '<div style="font-size:14px; margin:10px 0px 20px; line-height:18px;"><b>Rp. '.$s['grand_total_show'].'</b></div>';
					$s['billing_info'] .= '<div style="font-size:12px; margin:0px 0px 5px; line-height:14px;"><b>'.$s['qc_notes'].'</b></div>';
					$s['billing_info'] .= '<div style="font-size:10px; margin:0px 0px 0px; line-height: 14px;">Tamu: '.$s['total_guest'].' Orang</div>';
					
				}
				
				$newData[$s['id']] = $s;
				//array_push($newData, $s);
				
				$no++;
			}
		}
		
		$all_bil_id_txt = implode("','", $all_bil_id);
		$this->db->select("billing_id, order_qty, product_price, product_price_hpp, order_status, free_item, package_item");
		$this->db->from($this->table2);
		$this->db->where("billing_id IN ('".$all_bil_id_txt."')");
		$this->db->where("is_deleted = 0");
		$get_detail = $this->db->get();
		if($get_detail->num_rows() > 0){
			foreach($get_detail->result() as $detail){
				
				$total_qty = $detail->order_qty;
				
				
				//FREE				
				if($detail->free_item == 1 AND $detail->package_item == 0){
					$detail->product_price = 0;
				}		

				//package_item
				if($detail->package_item == 1){
					$detail->product_price = 0;
				}
				
				$total_order = $detail->order_qty*$detail->product_price;
				$total_hpp = $detail->order_qty*$detail->product_price_hpp;
				
				if($detail->order_status == 'delivered'){
					$newData[$detail->billing_id]['total_qty_deliver'] += $total_qty;
				}else{
					$newData[$detail->billing_id]['total_qty_order'] += $total_qty;
				}
				
				$newData[$detail->billing_id]['total_hpp'] += $total_hpp;
				$newData[$detail->billing_id]['order_total'] += $total_order;
				$newData[$detail->billing_id]['order_total_show'] = 'Rp '.priceFormat($newData[$detail->billing_id]['order_total']);
								
				$total_qty_order = ($newData[$detail->billing_id]['total_qty_deliver']+$newData[$detail->billing_id]['total_qty_order']);
				if(empty($total_qty_order)){
					$total_qty_order = 1;
				}
				$percent_status_order = ($newData[$detail->billing_id]['total_qty_deliver'] / $total_qty_order) * 100;
				$newData[$detail->billing_id]['percent_status_order'] = $percent_status_order;
				
			}
		}	
		
		$newData_switch = $newData;
		$newData = array();
		if(!empty($newData_switch)){
			foreach($newData_switch as $dt){
						
				$dt['total_profit'] = $dt['total_billing']-$dt['total_hpp'];				
				$dt['total_hpp_show'] = 'Rp '.priceFormat($dt['total_hpp']);
				$dt['total_profit_show'] = 'Rp '.priceFormat($dt['total_profit']);
				$newData[] = $dt;
			}
		}

				
		$get_data['data'] = $newData;
		
      	die(json_encode($get_data));
	}

	public function get_customerVisit(){
		
	}
}