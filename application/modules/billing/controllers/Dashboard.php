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
	
	public function print_reportSales(){
		
		$this->table = $this->prefix.'billing';
		$this->table2 = $this->prefix.'billing_detail';
		
		//update-2009.002
		$this->table_trx = $this->prefix.'billing_trx';
		
		$session_user = $this->session->userdata('user_username');					
		$user_fullname = $this->session->userdata('user_fullname');					
		
		if(empty($session_user)){
			die('Sesi Login sudah habis, Silahkan Login ulang!');
		}
		
		extract($_GET);

		if(empty($date_from)){ $date_from = date('Y-m-d'); }
		if(empty($date_till)){ $date_till = date('Y-m-d'); }
		
		if(empty($sorting)){
			$sorting = 'payment_date';
		}
		if(empty($sortingDesc)){
			$sortingDesc = 'ASC';
		}
		if(empty($only_txmark)){
			$only_txmark = 0;
		}
		
		$data_post = array(
			'do'	=> '',
			'report_data'	=> array(),
			'report_place_default'	=> '',
			'report_name'	=> 'SALES REPORT',
			'tipe_sales'	=> 'Semua Tipe Sales',
			'date_from'	=> $date_from,
			'date_till'	=> $date_till,
			'user_shift'	=> 'Semua Shift',
			'cashier_name'	=> '',
			'user_fullname'	=> $user_fullname,
			'diskon_sebelum_pajak_service' => 0,
			'display_discount_type'	=> array(),
			'only_txmark'	=> $only_txmark,
			'filter_column'	=> array(),
			'user_kasir'	=> ''
		);
		
		$display_discount_type = array();

		//update-0120.001
		if(!empty($shift_billing)){
			if($shift_billing == 'null'){
				$shift_billing = 0;
			}
		}
		if(!empty($kasir_billing)){
			if($kasir_billing == 'null'){
				$kasir_billing = '';
			}
		}
		
		if(empty($tipe_sales)){
			$tipe_sales = 'all_sales';
		}
		
		//filter-column
		$show_payment = json_decode($show_payment);
		$show_compliment = json_decode($show_compliment);
		$show_tax = json_decode($show_tax);
		$show_service = json_decode($show_service);
		$show_dp = json_decode($show_dp);
		$show_pembulatan = json_decode($show_pembulatan);
		$show_note = json_decode($show_note);
		$show_shift_kasir = json_decode($show_shift_kasir);
		$format_nominal = json_decode($format_nominal);
		
		$data_post['filter_column'] = array(
			'show_payment' => $show_payment,
			'show_compliment' => $show_compliment,
			'show_tax' => $show_tax,
			'show_service' => $show_service,
			'show_dp' => $show_dp,
			'show_pembulatan' => $show_pembulatan,
			'show_note' => $show_note,
			'show_shift_kasir' => $show_shift_kasir,
			'format_nominal' => $format_nominal
		);

		
		$get_opt = get_option_value(array('report_place_default','diskon_sebelum_pajak_service',
		'cashier_max_pembulatan','cashier_pembulatan_keatas','role_id_kasir','maxday_cashier_report',
		'jam_operasional_from','jam_operasional_to','jam_operasional_extra','nontrx_override_on'));
		if(!empty($get_opt['report_place_default'])){
			$data_post['report_place_default'] = $get_opt['report_place_default'];
		}
		
		if(!empty($get_opt['diskon_sebelum_pajak_service'])){
			$data_post['diskon_sebelum_pajak_service'] = $get_opt['diskon_sebelum_pajak_service'];
		}
		if(empty($get_opt['cashier_max_pembulatan'])){
			$get_opt['cashier_max_pembulatan'] = 0;
		}
		if(empty($get_opt['cashier_pembulatan_keatas'])){
			$get_opt['cashier_pembulatan_keatas'] = 0;
		}
		
		//update-2009.002
		$all_bil_id_trx = array();
		if(!empty($get_opt['nontrx_override_on'])){
			if($only_txmark == 0){
				$only_txmark = 1;
				$data_post['only_txmark'] = $only_txmark;
			}
		}
		
		if(empty($date_from) OR empty($date_till)){
			die('Billing Paid Not Found!');
		}else{
				
			$mktime_dari = strtotime($date_from);
			$mktime_sampai = strtotime($date_till);
						
			$ret_dt = check_maxview_cashierReport($get_opt, $mktime_dari, $mktime_sampai);
			
			//$qdate_from = date("Y-m-d",strtotime($date_from));
			//$qdate_till = date("Y-m-d",strtotime($date_till));
			//$qdate_till_max = date("Y-m-d",strtotime($date_till)+ONE_DAY_UNIX);
			//$add_where = "(a.payment_date >= '".$qdate_from." 07:00:00' AND a.payment_date <= '".$qdate_till_max." 06:00:00')";
			
			//laporan = jam_operasional
			$qdate_from = $ret_dt['qdate_from'];
			$qdate_till = $ret_dt['qdate_till'];
			$qdate_till_max = $ret_dt['qdate_till_max'];
			
			//update-0120.001
			$where_shift_billing = "(a.payment_date >= '".$qdate_from."' AND a.payment_date <= '".$qdate_till_max."')";
				
			//update-0120.001
			if(!empty($shift_billing)){
				$where_shift_billing .= " AND a.shift = ".$shift_billing;
				$data_post['user_shift'] = '';
			}
			if(!empty($kasir_billing)){
				$where_shift_billing .= " AND a.updatedby = '".$kasir_billing."'";
				$data_post['user_kasir'] = '';
			}
			$this->db->select("a.*, a.id as billing_id, a.updated as billing_date, d.payment_type_name, e.bank_name,
								g.nama_shift, g2.customer_name, g2.customer_representative, g3.sales_name, CONCAT(h.user_firstname,' ',h.user_lastname) as nama_kasir");
			$this->db->from($this->table." as a");
			$this->db->join($this->prefix.'payment_type as d','d.id = a.payment_id','LEFT');
			$this->db->join($this->prefix.'bank as e','e.id = a.bank_id','LEFT');
			$this->db->join($this->prefix.'shift as g','g.id = a.shift','LEFT');
			$this->db->join($this->prefix.'customer as g2','g2.id = a.customer_id','LEFT');
			$this->db->join($this->prefix.'sales as g3','g3.id = a.sales_id','LEFT');
			$this->db->join($this->prefix_apps.'users as h','h.user_username = a.updatedby','LEFT');
			$this->db->where("a.billing_status", 'paid');
			$this->db->where("a.is_deleted", 0);
			
			//update-0120.001
			$this->db->where($where_shift_billing);
			//$this->db->where("a.billing_no IN ('2002060003','2002060006')");
			
			if(!empty($only_txmark)){
				$this->db->where("a.txmark",1);
			}
			
			//if(empty($sorting)){
				$this->db->order_by("a.payment_date","ASC");
			//}else{
			//	$this->db->order_by('a.'.$sorting,"ASC");
			//}
			
			//update-2001.002
			if(!empty($tipe_sales)){
				switch($tipe_sales){
					case 'sales_no_discount': 
						$this->db->where("(a.discount_id IS NULL OR a.discount_id = 0)");
						$data_post['tipe_sales'] = 'Tanpa Discount/Potongan';
						break;
					
					case 'sales_only_discount': 
						$this->db->where("(a.discount_id > 0)");
						$data_post['tipe_sales'] = 'Discount/Potongan';
						break;
					
					case 'sales_no_compliment': 
						$this->db->where("(a.is_compliment = 0 AND a.compliment_total = 0)");
						$data_post['tipe_sales'] = 'Tanpa Compliment';
						break;
						
					case 'sales_only_compliment': 
						$this->db->where("((a.is_compliment = 1 AND a.compliment_total > 0) OR (a.is_compliment = 0 AND a.compliment_total > 0))");
						$data_post['tipe_sales'] = 'Compliment';
						break;
					
					case 'sales_no_customer': 
						$this->db->where("(a.customer_id = 0)");
						$data_post['tipe_sales'] = 'Tanpa Customer/Member';
						break;
					
					case 'sales_only_customer': 
						$this->db->where("(a.customer_id > 0)");
						$data_post['tipe_sales'] = 'Customer/Member';
						break;
					
					case 'sales_no_marketing': 
						$this->db->where("(a.sales_id = 0)");
						$data_post['tipe_sales'] = 'Tanpa Marketing/Sales-Fee';
						break;
					
					case 'sales_only_marketing': 
						$this->db->where("(a.sales_id > 0)");
						$data_post['tipe_sales'] = 'Marketing/Sales-Fee';
						break;
					
					default: 
						//nothing	
						break;
					
				}
				
			}
			
			$get_dt = $this->db->get();
			if($get_dt->num_rows() > 0){
				$data_post['report_data'] = $get_dt->result_array();				
			}
			
			//PAYMENT DATA
			$dt_payment_name = array();
			$this->db->select('*');
			$this->db->from($this->prefix.'payment_type');
			$get_dt_p = $this->db->get();
			if($get_dt_p->num_rows() > 0){
				foreach($get_dt_p->result_array() as $dtP){
					$dt_payment_name[$dtP['id']] = strtoupper($dtP['payment_type_name']);
				}
			}
			
			//update-2002.003
			$all_bil_id = array();
			if(!empty($data_post['report_data'])){
				foreach ($data_post['report_data'] as $s){
					
					if(!in_array($s['id'], $all_bil_id)){
						$all_bil_id[] = $s['id'];
					}		
					
				}
			}
			
			//update-2002.003
			$recap_sort = array();
			$total_billing = array();
			$total_hpp = array();
			$discount_item = array();
			if(!empty($all_bil_id)){
				$all_bil_id_txt = implode(",",$all_bil_id);
				$this->db->from($this->table2);
				$this->db->where('billing_id IN ('.$all_bil_id_txt.')');
				$this->db->where('is_deleted', 0);
				$get_detail = $this->db->get();
				if($get_detail->num_rows() > 0){
					foreach($get_detail->result() as $dtRow){
						
						$total_qty = $dtRow->order_qty;
						
						if(empty($total_hpp[$dtRow->billing_id])){
							$total_hpp[$dtRow->billing_id] = 0;
						}
						
						$total_hpp[$dtRow->billing_id] += $dtRow->product_price_hpp * $total_qty;
						
						//update-2002.003
						$dtRow->product_price_real_before = $dtRow->product_price_real;
						if((!empty($dtRow->include_tax) AND empty($dtRow->include_service)) OR (empty($dtRow->include_tax) AND !empty($dtRow->include_service))){
							if($dtRow->product_price != ($dtRow->product_price_real+$dtRow->tax_total+$dtRow->service_total)){
								$all_percentage = 100 + $dtRow->tax_percentage + $dtRow->service_percentage;
								$dtRow->product_price_real = priceFormat(($dtRow->product_price/($all_percentage/100)), 0, ".", "");
							}
						
							if($dtRow->is_compliment == 1){
								$dtRow->product_price_real = $dtRow->product_price_real_before;
							}
						}
						
						$total_billing[$dtRow->billing_id] += $dtRow->product_price_real * $total_qty;
						
						if($sortingDesc == 'DESC'){
							if(empty($recap_sort[$dtRow->billing_id])){
								$recap_sort[$dtRow->billing_id] = 0;
							}
							if($sorting == 'total_hpp'){
								$recap_sort[$dtRow->billing_id] += ($dtRow->product_price_hpp * $total_qty);
							}
							if($sorting == 'total_profit'){
								$recap_sort[$dtRow->billing_id] -= ($dtRow->product_price_hpp * $total_qty);
							}
							
						}
						
					}
				}
				
				//update-2009.002
				if(!empty($only_txmark)){
					$this->db->from($this->table_trx);
					$this->db->where('id IN ('.$all_bil_id_txt.')');
					$get_billing_trx = $this->db->get();
					if($get_billing_trx->num_rows() > 0){
						foreach($get_billing_trx->result_array() as $dtTrx){
							if(!in_array($dtTrx['id'], $all_bil_id_trx)){
								$all_bil_id_trx[] = $dtTrx['id'];
							}		
							
						}
					}
				}
			}
			
			//$all_bil_id = array();
			$newData = array();
			$dt_payment = array();
			if(!empty($data_post['report_data'])){
				foreach ($data_post['report_data'] as $s){
					
					//update-0120.001
					if(!empty($shift_billing) AND empty($data_post['user_shift'])){
						if(!empty($s['nama_shift'])){
							$data_post['user_shift'] = $s['nama_shift'];
						}
					}
					if(!empty($kasir_billing) AND empty($data_post['user_kasir'])){
						if(!empty($s['nama_kasir'])){
							$data_post['user_kasir'] = $s['nama_kasir'];
						}
					}
					
					if(empty($display_discount_type[$s['diskon_sebelum_pajak_service']])){
						$display_discount_type[$s['diskon_sebelum_pajak_service']] = array();
					}
					if(!in_array($s['billing_id'], $display_discount_type[$s['diskon_sebelum_pajak_service']])){
						$display_discount_type[$s['diskon_sebelum_pajak_service']][] = $s['billing_id'];
					}
					
					$s['billing_date'] = date("d-m-Y H:i",strtotime($s['created']));					
					$s['payment_date'] = date("d-m-Y H:i",strtotime($s['payment_date']));
					
					//if(!in_array($s['id'], $all_bil_id)){
					//	$all_bil_id[] = $s['id'];
					//}		
					
					$s['total_billing_awal'] = $s['total_billing'];
					
					//update-2002.003
					//CHECK REAL TOTAL BILLING
					if(!empty($s['include_tax']) OR !empty($s['include_service'])){
						//update-2002.003
						$s['total_billing'] = $total_billing[$s['id']];
						$s['total_billing_awal'] = $s['total_billing'];
						
						/*if(!empty($s['include_tax']) AND !empty($s['include_service'])){
							$s['total_billing'] = $s['total_billing'] - ($s['tax_total'] + $s['service_total']);
						}else{
							if(!empty($s['include_tax'])){
								$s['total_billing'] = $s['total_billing'] - ($s['tax_total']);
							}
							if(!empty($s['include_service'])){
								$s['total_billing'] = $s['total_billing'] - ($s['service_total']);
							}
						}*/
					}
					
					//update-2001.002
					//COMPLIMENT
					if(!empty($s['is_compliment']) OR !empty($s['compliment_total'])){
						//$s['total_billing'] = $s['total_billing'] + $s['tax_total'] + $s['service_total'];
						if($s['total_billing'] <= $s['compliment_total']){
							$s['service_total'] = 0;
							$s['tax_total'] = 0;
						}
					}
					
					//update-2001.002
					if($s['diskon_sebelum_pajak_service'] == 1){
						
						//update-2002.003
						//if(!empty($s['include_tax']) OR !empty($s['include_service'])){
						//	$s['total_billing'] = ($s['total_billing_awal'] - ($s['tax_total'] + $s['service_total']));
						//}
						
						$s['sub_total'] = $s['total_billing'] - $s['discount_total'] + $s['tax_total'] + $s['service_total'] - $s['compliment_total'];
						$s['net_sales_total'] = $s['total_billing'] - $s['discount_total'] - $s['compliment_total'];
						
						//GRAND TOTAL
						$s['grand_total'] = $s['sub_total'];
						
					}else
					{
						//update-2001.002
						$s['sub_total'] = $s['total_billing'] + $s['tax_total'] + $s['service_total'] - $s['discount_total'] - $s['compliment_total'];
						$s['net_sales_total'] = $s['total_billing'] - $s['discount_total'] - $s['compliment_total'];
						
						//GRAND TOTAL
						$s['grand_total'] = $s['sub_total'];
						//$s['grand_total'] -= $s['discount_total'];
						//$s['grand_total'] -= $s['discount_billing_total'];
						
					}
					
					//SPLIT DISCOUNT TYPE
					if(!empty($s['discount_total']) AND $s['discount_perbilling'] == 1){
						$s['discount_billing_total'] = $s['discount_total'];
						$s['discount_total'] = 0;
					}else{
						$s['discount_billing_total'] = 0;
					}
					
					$s['grand_total'] += $s['total_pembulatan'];
					//$s['grand_total'] -= $s['compliment_total'];
					
					if($s['grand_total'] <= 0){
						$s['grand_total'] = 0;
					}
					
					$s['total_pembulatan_show'] = priceFormat($s['total_pembulatan']);
					
					if($s['total_pembulatan'] < 0){
						$s['total_pembulatan_show'] = "(".priceFormat($s['total_pembulatan']).")";
					}
					
					$s['sub_total_show'] = priceFormat($s['sub_total']);
					$s['net_sales_total_show'] = priceFormat($s['net_sales_total']);
					$s['grand_total_show'] = priceFormat($s['grand_total']);
					$s['total_billing_show'] = priceFormat($s['total_billing']);
					$s['total_paid_show'] = priceFormat($s['total_paid']);
					$s['tax_total_show'] = priceFormat($s['tax_total']);
					$s['service_total_show'] = priceFormat($s['service_total']);
					$s['discount_total_show'] = priceFormat($s['discount_total']);
					$s['discount_billing_total_show'] = priceFormat($s['discount_billing_total']);
					
					//DP
					$s['total_dp_show'] = priceFormat($s['total_dp']);
					
					$s['total_compliment'] = 0;
					$s['total_compliment_show'] = 0;

					$s['total_hpp'] = 0;
					$s['total_hpp_show'] = 0;
					$s['total_profit'] = 0;
					$s['total_profit_show'] = 0;
					
					//CARD NO 
					$card_no = '';
					if(strlen($s['card_no']) > 30){
						$card_no = $s['card_no'];
						$card_no = str_replace(";","",$card_no);
						$card_no = str_replace("?","",$card_no);
						$card_no_exp = explode("=", $card_no);
						if(!empty($card_no_exp[0])){
							$card_no = trim($card_no_exp[0]);
						}
					}else{
						$card_no = trim($s['card_no']);
					}
					
					//NOTES
					$s['payment_note'] = '';
					if(!empty($s['is_compliment']) OR !empty($s['compliment_total'])){
						$s['payment_note'] .= 'COMPLIMENT ';
						//$s['total_compliment'] = $s['grand_total'];
						$s['total_compliment'] = $s['compliment_total'];
						$s['total_compliment_show'] = priceFormat($s['total_compliment']);
					}
					
					//update-2001.002
					if(!empty($s['is_half_payment'])){
						if(!empty($s['payment_note'])){
							$s['payment_note'] .= ', ';
						}
						$s['payment_note'] .= 'HALF PAYMENT ';
					}
					
					if(strtolower($s['payment_type_name']) != 'cash'){
						if(!empty($s['payment_note'])){
							$s['payment_note'] .= '<br/>';
						}
						$s['payment_note'] .= strtoupper($s['payment_type_name']).': '.strtoupper($s['bank_name']).' '.$card_no.' ';
					}
					
					/*if(!empty($s['billing_notes'])){
						if(!empty($s['payment_note'])){
							$s['payment_note'] .= '<br/>';
						}
						$s['payment_note'] .= $s['billing_notes'];
					}*/
					
					/*if(!empty($s['customer_id'])){
						if(!empty($s['payment_note'])){
							$s['payment_note'] .= '<br/>';
						}
						$s['payment_note'] .= 'Cust/Member: '.$s['customer_name'];
					}*/
					
					if(!empty($s['sales_id'])){
						if(!empty($s['payment_note'])){
							$s['payment_note'] .= '<br/>';
						}
						$s['payment_note'] .= 'Marketing/Sales: '.$s['sales_name'];
					}
					
					//update-2001.002
					$get_billing_id = $s['billing_id'];
					if($sortingDesc == 'DESC'){
						if(empty($recap_sort[$get_billing_id])){
							$recap_sort[$get_billing_id] = 0;
						}
						if($sorting == 'qty_menu'){
							$recap_sort[$get_billing_id] += $total_qty;
						}
						if($sorting == 'total_billing'){
							$recap_sort[$get_billing_id] += $s['total_billing'];
						}
						if($sorting == 'all_discount_total'){
							$recap_sort[$get_billing_id] +=  ($s['discount_total']+$s['discount_billing_total']);
						}
						if($sorting == 'discount_total'){
							$recap_sort[$get_billing_id] +=  $s['discount_total'];
						}
						if($sorting == 'discount_perbilling'){
							$recap_sort[$get_billing_id] += $s['discount_billing_total'];
						}
						if($sorting == 'compliment_total'){
							$recap_sort[$get_billing_id] += $s['compliment_total'];
						}
						if($sorting == 'net_sales_total'){
							$recap_sort[$get_billing_id] += $s['net_sales_total'];
						}
						if($sorting == 'tax_total'){
							$recap_sort[$get_billing_id] += $s['tax_total'];
						}
						if($sorting == 'service_total'){
							$recap_sort[$get_billing_id] += $s['service_total']; 
						}
						if($sorting == 'total_pembulatan'){
							$recap_sort[$get_billing_id] += $s['total_pembulatan'];
						}
						if($sorting == 'grand_total'){
							$recap_sort[$get_billing_id] += $s['grand_total'];
						}
						if($sorting == 'total_dp'){
							$recap_sort[$get_billing_id] += $s['total_dp'];
						}
						if($sorting == 'half_payment'){
							if($s['is_half_payment'] == 1){
								$recap_sort[$get_billing_id] += $s['grand_total'];
							}
						}
						if($sorting == 'payment_cash'){
							if($s['payment_id'] == 1){
								$recap_sort[$get_billing_id] += $s['total_cash'];
							}else{
								if($s['is_half_payment'] == 1){
									$recap_sort[$get_billing_id] += $s['total_cash'];
								}
							}
						}
						if($sorting == 'payment_debit'){
							if($s['payment_id'] == 2){
								if($s['is_half_payment'] == 1){
									$recap_sort[$get_billing_id] += $s['total_credit'];
								}else{
									$recap_sort[$get_billing_id] += $s['grand_total'];
								}
							}
						}
						if($sorting == 'payment_credit'){
							if($s['payment_id'] == 3){
								if($s['is_half_payment'] == 1){
									$recap_sort[$get_billing_id] += $s['total_credit'];
								}else{
									$recap_sort[$get_billing_id] += $s['grand_total'];
								}
							}
						}
						if($sorting == 'payment_ar'){
							if($s['payment_id'] == 4){
								if($s['is_half_payment'] == 1){
									$recap_sort[$get_billing_id] += $s['total_credit'];
								}else{
									$recap_sort[$get_billing_id] += $s['grand_total'];
								}
							}
						}
						if($sorting == 'total_profit'){
							$recap_sort[$get_billing_id] += $s['net_sales_total'];
						}
					}else{
						if($sorting == 'payment_date'){
							$recap_sort[$get_billing_id] = strtotime($s['payment_date']);
						}
						if($sorting == 'billing_no'){
							$recap_sort[$get_billing_id] = $s['billing_no'];
						}
						if($sorting == 'discount_notes'){
							$recap_sort[$get_billing_id] = $s['discount_notes'];
						}
						if($sorting == 'discount_type'){
							if(!empty($discount_billing_total)){
								$recap_sort[$get_billing_id] = 2;
							}else
							if(!empty($discount_total)){
								$recap_sort[$get_billing_id] = 1;
							}else{
								$recap_sort[$get_billing_id] = 0;
							}
						}
					}
					
					//update-2009.002
					$s['post_nontrx'] = 0;
					$s['post_nontrx_txt'] = '<font color="red"><b>Blm Terkirim</b></font>';
					$s['post_sales_txt'] = '<font color="red"><b>Blm Terkirim</b></font>';
					if(!empty($only_txmark)){
						if(in_array($s['id'], $all_bil_id_trx)){
							$s['post_nontrx'] = 1;
							$s['post_nontrx_txt'] = '<font color="green"><b>'.priceFormat($s['tax_total']).'</b></font>';
							$s['post_sales_txt'] = '<font color="green"><b>'.priceFormat($s['net_sales_total']).'</b></font>';
						}
					}
									
					$newData[$s['id']] = $s;
					//array_push($newData, $s);
					
				}
			}
			
			$newData_switch = $newData;
			$newData = array();
			if(!empty($newData_switch)){
				
				//update-2001.002
				if($sortingDesc == 'ASC'){
					asort($recap_sort);
				}else{
					arsort($recap_sort);
				}
				
				if(!empty($recap_sort)){
					foreach($recap_sort as $billing_id => $val){
						if(!empty($newData_switch[$billing_id])){
							$dt = $newData_switch[$billing_id];
							
							if(!empty($total_hpp[$dt['billing_id']])){
								$dt['total_hpp'] = $total_hpp[$dt['billing_id']];
							}
							$dt['total_hpp_show'] = priceFormat($dt['total_hpp']);
							
							$dt['total_profit'] = $dt['net_sales_total']-$dt['total_hpp'];
							$dt['total_profit_show'] = priceFormat($dt['total_profit']);
							
							$newData[] = $dt;
						}
					}
				}
			}
	
			$data_post['report_data'] = $newData;
			$data_post['payment_data'] = $dt_payment_name;
			$data_post['display_discount_type'] = $display_discount_type;
			//$data_post['total_hpp'] = $total_hpp;
		}
		
		//DO-PRINT
		if(!empty($do)){
			$data_post['do'] = $do;
		}else{
			$do = '';
		}
		
		if(empty($useview)){
			$useview = 'print_reportSales';
			$data_post['report_name'] = 'SALES REPORT';
			
			if($do == 'excel'){
				$useview = 'excel_reportSales';
			}
			
		}else{
			$useview = 'print_reportProfitSales';
			$data_post['report_name'] = 'SALES PROFIT REPORT';
			
			if($do == 'excel'){
				$useview = 'excel_reportProfitSales';
			}
			
		}
		
		$this->load->view('../../billing/views/'.$useview, $data_post);	
	}
}