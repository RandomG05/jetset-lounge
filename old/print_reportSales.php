<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/desktop/css/report.css'; ?>"/>	
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/desktop/css/report.css'; ?>" media="print"/>	
	</head>
<body>
	<?php
		$set_width = 1800;
		$total_cols = 15;
		
		//update-0120.001
		if(!empty($filter_column)){
			extract($filter_column);
		}
		
		//update-2009.002
		if(!empty($only_txmark)){
			$show_tax = true;
			$set_width += 180;
			$total_cols += 2;
		}

		$payment_data_content = '';
		if($show_payment == true){
			if(!empty($payment_data)){
				foreach($payment_data as $key_id => $dtPay){
					$payment_data_content .= '<td class="xcenter" width="100">'.$dtPay.'</td>';
					$set_width += 100;
					$total_cols++;
				}
			}
		}
		
		if(count($display_discount_type) > 1){
			$set_width += 200;
			$total_cols += 2;
		}
		
		if($show_tax == false){
			$set_width -= 100;
			$total_cols -= 1;
		}
		if($show_service == false){
			$set_width -= 100;
			$total_cols -= 1;
		}
		if($show_compliment == false){
			$set_width -= 100;
			$total_cols -= 1;
		}
		if($show_pembulatan == false){
			$set_width -= 100;
			$total_cols -= 1;
		}
		if($show_dp == false){
			$set_width -= 100;
			$total_cols -= 1;
		}
		if($show_note == false){
			$set_width -= 300;
			$total_cols -= 1;
		}
		if($show_shift_kasir == false){
			$set_width -= 150;
			$total_cols -= 1;
		}
	?>
	<div class="report_area" style="width:<?php echo $set_width.'px'; ?>;">
		<table width="<?php echo $set_width; ?>">
			<!-- HEADER -->
			<thead>
				<tr class="tbl-title">
					<td colspan="<?php echo $total_cols ?>">
						<div>
							<div class="logo">
								
								<!-- <img height="80" src="<?php echo base_url(); ?>assets/resources/client_logo/<?php echo $this->session->userdata('client_logo'); ?>"> -->
								
							</div>
										
							<div class="title_report"><?php echo $report_name; ?></div>
							<div class="subtitle_report" style="margin-bottom:5px;">
							<?php
							if($date_from == $date_till){
								echo 'Tanggal : '.$date_from;
							}else{
								echo 'Tanggal : '.$date_from.' s/d '.$date_till; 
							}
							
							if(!empty($user_shift)){ 
								echo ' &nbsp; | &nbsp; Shift: '.$user_shift; 
							}else{
								echo ' &nbsp; | &nbsp; Shift: Semua Shift';
							}
							
							if(!empty($user_kasir)){ 
								echo ' &nbsp; | &nbsp; Kasir: '.$user_kasir;
							}else{
								echo ' &nbsp; | &nbsp; Kasir: Semua Kasir';
							}
							
							if(!empty($tipe_sales)){ 
								echo ' &nbsp; | &nbsp; Tipe Sales: '.$tipe_sales; 
							}
							?>			
							</div>
						</div>
					</td>
				</tr>
				<tr class="tbl-header">
					<td class="first xcenter" width="50" rowspan="2">NO</td>
					<td class="xcenter" width="130" rowspan="2">PAYMENT DATE</td>
					<td class="xcenter" width="80" rowspan="2">BILLING NO.</td>
					<td class="xcenter" width="110" rowspan="2">TOTAL BILLING</td>
					<?php
					if($diskon_sebelum_pajak_service == 1 OR count($display_discount_type) > 1){
						if(count($display_discount_type) > 1){
							?>
							<td class="xcenter" width="220" colspan="2">DISCOUNT BEFORE TAX-SERVICE</td>	
							<?php
						}else{
							?>
							<td class="xcenter" width="220" colspan="2">DISCOUNT</td>	
							<?php
						}
					}
					
					if($diskon_sebelum_pajak_service == 0 OR count($display_discount_type) > 1){
						if(count($display_discount_type) > 1){
							?>
							<td class="xcenter" width="220" colspan="2">DISCOUNT AFTER TAX-SERVICE</td>	
							<?php
						}else{
							?>
							<td class="xcenter" width="220" colspan="2">DISCOUNT AFTER TAX-SERVICE</td>	
							<?php
						}
						
					}
					
					//update-2001.002
					if($show_compliment == true){
						?>
						<td class="xcenter" width="100" rowspan="2">COMPLIMENT</td>
						<?php
					}
					?>
					<td class="xcenter" width="100" rowspan="2">NET SALES</td>
					<?php
					
					if($show_tax == true){
					?>
					<td class="xcenter" width="90" rowspan="2">TAX</td>
					<?php
					}
					if($show_service == true){
					?>
					<td class="xcenter" width="90" rowspan="2">SERVICE</td>
					<?php
					}
					
					if($show_pembulatan == true){
						?>
						<td class="xcenter" width="100" rowspan="2">PEMBULATAN</td>	
						<?php
					}
					?>
					<td class="xcenter" width="120" rowspan="2">GRAND TOTAL</td>
					<?php
					if($show_dp == true){
						?>
						<td class="xcenter" width="100" rowspan="2">DP</td>
						<?php
					}
					
					if($show_payment == true){
						?>
						<td class="xcenter" width="<?php echo count($payment_data)*100; ?>" colspan="<?php echo count($payment_data); ?>">PAYMENT</td>	
						<?php
					}
					
					if($show_note == true){
					?>
					<td class="xcenter" width="300" rowspan="2">NOTE</td>
					<?php
					}
					
					if($show_shift_kasir == true){
					?>
					<td class="xcenter" width="150" rowspan="2">SHIFT/KASIR</td>
					<?php
					}
					
					if(!empty($only_txmark)){
					?>
					<td class="xcenter" width="80" rowspan="2">POST TAX</td>
					<td class="xcenter" width="100" rowspan="2">POST SALES</td>
					<?php
					}
					?>
					
				</tr>
				<tr class="tbl-header">
					
					<?php
					if($diskon_sebelum_pajak_service == 1 OR count($display_discount_type) > 1){
						?>
						<td class="xcenter" width="110">ITEM</td>
						<td class="xcenter" width="110">BILLING</td>
						<?php
					}
					
					if($diskon_sebelum_pajak_service == 0 OR count($display_discount_type) > 1){
						?>
						<td class="xcenter" width="110">ITEM</td>
						<td class="xcenter" width="110">BILLING</td>
						<?php
					}
					
					if($show_payment == true){
						echo $payment_data_content;
					}
					?>
					
				</tr>
			</thead>
			<tbody>
				<?php
				if(!empty($report_data)){
				
					$no = 1;
					$total_billing = 0;
					$total_tax = 0;
					$total_service = 0;
					$grand_total = 0;
					$grand_total_dp = 0;
					$grand_sub_total = 0;
					$grand_net_sales_total = 0;
					$grand_total_pembulatan = 0;
					$grand_discount_total = 0;
					$grand_discount_billing_total = 0;
					$grand_total_compliment = 0;
					$grand_total_payment = array();
					
					$grand_discount_total_before = 0;
					$grand_discount_billing_total_before = 0;
					$grand_discount_total_after = 0;
					$grand_discount_billing_total_after = 0;
					
					$total_post_nontrx = 0;
					$grand_total_post_nontrx = 0;
					$grand_total_post_sales = 0;
					
					foreach($report_data as $det){
						
						if(!empty($only_txmark)){
							$det['billing_no'] = $det['txmark_no'];
						}
						?>
						<tr class="tbl-data">
							<td class="first xcenter"><?php echo $no; ?></td>
							<td class="xcenter"><?php echo $det['payment_date']; ?></td>
							<td class="xcenter"><?php echo $det['billing_no']; ?></td>
							<td class="xright"><?php echo $det['total_billing_show']; ?></td>
							<?php
							if($diskon_sebelum_pajak_service == 1 OR count($display_discount_type) > 1){
								if(count($display_discount_type) > 1){
									if(count($display_discount_type) > 1 AND $det['diskon_sebelum_pajak_service'] == 1){
										?>
										<td class="xright"><?php echo $det['discount_total_show']; ?></td>
										<td class="xright"><?php echo $det['discount_billing_total_show']; ?></td>
										<?php
									}else{
										?>
										<td class="xright">-</td>
										<td class="xright">-</td>
										<?php
									}
								}else{
									?>
									<td class="xright"><?php echo $det['discount_total_show']; ?></td>
									<td class="xright"><?php echo $det['discount_billing_total_show']; ?></td>
									<?php
								}
							}
							
							if($diskon_sebelum_pajak_service == 0 OR count($display_discount_type) > 1){
								if(count($display_discount_type) > 1){
									if(count($display_discount_type) > 1 AND $det['diskon_sebelum_pajak_service'] == 0){
										?>
										<td class="xright"><?php echo $det['discount_total_show']; ?></td>
										<td class="xright"><?php echo $det['discount_billing_total_show']; ?></td>
										<?php
									}else{
										?>
										<td class="xright">-</td>
										<td class="xright">-</td>
										<?php
									}
								}else{
									?>
									<td class="xright"><?php echo $det['discount_total_show']; ?></td>
									<td class="xright"><?php echo $det['discount_billing_total_show']; ?></td>
									<?php
								}
								
							}
							
							if($show_compliment == true){
							?>
							<td class="xright"><?php echo $det['total_compliment_show']; ?></td>
							<?php
							}
							?>
							<td class="xright"><?php echo $det['net_sales_total_show']; ?></td>
							<?php
							if($show_tax == true){
							?>
							<td class="xright"><?php echo $det['tax_total_show']; ?></td>
							<?php
							}
							if($show_service == true){
							?>
							<td class="xright"><?php echo $det['service_total_show']; ?></td>
							<?php
							}
							
							if($show_pembulatan == true){
							?>
							<td class="xright"><?php echo $det['total_pembulatan_show']; ?></td>
							<?php
							}
							?>
							<td class="xright"><?php echo $det['grand_total_show']; ?></td>
							<?php
							if($show_dp == true){
							?>
							<td class="xright"><?php echo $det['total_dp_show']; ?></td>
							<?php
							}
							
							if($show_payment == true){
								if(!empty($payment_data)){
									foreach($payment_data as $key_id => $dtPay){
										
										$tot_payment = 0;
										$tot_payment_show = 0;
										if($det['payment_id'] == $key_id){
											
											//$tot_payment = $det['grand_total'];	
											//$tot_payment_show = $det['grand_total_show'];	
											
											if($key_id == 2 OR $key_id == 3 OR $key_id == 4){
												$tot_payment = $det['total_credit'];	
											}else{
												$tot_payment = $det['total_cash'];	
											}
											
											//FIX PEMBULATAN
											/*if($tot_payment < $det['grand_total']){
												$gap = ($det['grand_total'] - $det['total_dp']) - $tot_payment;
												if($gap < 100){
													//$tot_payment += $det['total_pembulatan'];
												}
											}*/
											
											if($tot_payment <= 0){
												$tot_payment = 0;
											}
											
											$tot_payment_show = priceFormat($tot_payment);
											
											//credit half payment
											if(!empty($det['is_half_payment']) AND $key_id != 1){
												$tot_payment = $det['total_credit'];
												$tot_payment_show = priceFormat($tot_payment);
											}else{
												
												$tot_payment_show = priceFormat($tot_payment);	
											}
											
										}else{
											//cash
											if(!empty($det['is_half_payment']) AND $key_id == 1){
												$tot_payment = $det['total_cash'];
												$tot_payment_show = priceFormat($tot_payment);
											}
										}
										
										
										if(empty($grand_total_payment[$key_id])){
											$grand_total_payment[$key_id] = 0;
										}									
										
										if(!empty($det['is_compliment'])){
											$tot_payment = 0;
											$tot_payment_show = 0;
										}
										
										if(!empty($det['discount_total']) AND !empty($tot_payment)){
											//$tot_payment = $tot_payment - $det['discount_total'];
											//$tot_payment_show = priceFormat($tot_payment);
										}
										
										$grand_total_payment[$key_id] += $tot_payment;
										?>
										<td class="xright" width="100"><?php echo $tot_payment_show; ?></td>
										<?php
																		
									}
								}
							}
							
							if($show_note == true){
							?>
							<td class="xleft"><?php echo $det['payment_note']; ?></td>
							<?php
							}
							
							if($show_shift_kasir == true){
							?>
							<td class="xleft"><?php echo $det['nama_shift'].'/'.$det['nama_kasir']; ?></td>
							<?php
							}
							
							if(!empty($only_txmark)){
								?>
								<td class="xright"><?php echo $det['post_nontrx_txt']; ?></td>
								<td class="xright"><?php echo $det['post_sales_txt']; ?></td>
								<?php
								if(!empty($det['post_nontrx'])){
									$total_post_nontrx++;
									$grand_total_post_nontrx += $det['tax_total'];
									$grand_total_post_sales += $det['net_sales_total'];
								}
							}
							?>
						</tr>
						<?php	
						
						$total_billing +=  $det['total_billing'];
						$total_tax +=  $det['tax_total'];
						$total_service +=  $det['service_total'];
						$grand_total +=  $det['grand_total'];
						$grand_total_compliment += $det['total_compliment'];
						$grand_sub_total += $det['sub_total'];
						$grand_net_sales_total += $det['net_sales_total'];
						$grand_total_pembulatan += $det['total_pembulatan'];
						$grand_total_dp += $det['total_dp'];
						
						if($diskon_sebelum_pajak_service == 1 OR count($display_discount_type) > 1){
							
							if(count($display_discount_type) > 1 AND $det['diskon_sebelum_pajak_service'] == 1){
								$grand_discount_total_before += $det['discount_total'];
								$grand_discount_billing_total_before += $det['discount_billing_total'];
							}else{
								$grand_discount_total += $det['discount_total'];
								$grand_discount_billing_total += $det['discount_billing_total'];
							}
						}
					
						if($diskon_sebelum_pajak_service == 0 OR count($display_discount_type) > 1){
							
							if(count($display_discount_type) > 1 AND $det['diskon_sebelum_pajak_service'] == 0){
								$grand_discount_total_after += $det['discount_total'];
								$grand_discount_billing_total_after += $det['discount_billing_total'];
							}else{
								$grand_discount_total += $det['discount_total'];
								$grand_discount_billing_total += $det['discount_billing_total'];
							}
						}
					
					
						$no++;
					}
					
					?>
					<tr class="tbl-total">
						<td class="first xright xbold" colspan="<?php echo 3; ?>">TOTAL</td>
						<td class="xright xbold"><?php echo priceFormat($total_billing); ?></td>
						<?php
						if($diskon_sebelum_pajak_service == 1 OR count($display_discount_type) > 1){
							if(count($display_discount_type) > 1){
								?>
								<td class="xright xbold"><?php echo priceFormat($grand_discount_total_before); ?></td>
								<td class="xright xbold"><?php echo priceFormat($grand_discount_billing_total_before); ?></td>
								<?php
							}else{
								?>
								<td class="xright xbold"><?php echo priceFormat($grand_discount_total); ?></td>
								<td class="xright xbold"><?php echo priceFormat($grand_discount_billing_total); ?></td>
								<?php
							}
							
						}
						
						
						if($diskon_sebelum_pajak_service == 0 OR count($display_discount_type) > 1){
							if(count($display_discount_type) > 1){
								?>
								<td class="xright xbold"><?php echo priceFormat($grand_discount_total_after); ?></td>
								<td class="xright xbold"><?php echo priceFormat($grand_discount_billing_total_after); ?></td>
								<?php
							}else{
								?>
								<td class="xright xbold"><?php echo priceFormat($grand_discount_total); ?></td>
								<td class="xright xbold"><?php echo priceFormat($grand_discount_billing_total); ?></td>
								<?php
							}
							
						}
						
						if($show_compliment == true){
						?>
						<td class="xright xbold"><?php echo priceFormat($grand_total_compliment); ?></td>
						<?php
						}
						?>
						<td class="xright xbold"><?php echo priceFormat($grand_net_sales_total); ?></td>
						<?php
						
						if($show_tax == true){
						?>
						<td class="xright xbold"><?php echo priceFormat($total_tax); ?></td>
						<?php
						}
						
						if($show_service == true){
						?>
						<td class="xright xbold"><?php echo priceFormat($total_service); ?></td>
						<?php
						}
						
						if($show_pembulatan == true){
						?>
						<td class="xright xbold"><?php echo priceFormat($grand_total_pembulatan); ?></td>
						<?php
						}
						?>
						<td class="xright xbold"><?php echo priceFormat($grand_total); ?></td>
						<?php
						if($show_dp == true){
						?>
						<td class="xright xbold"><?php echo priceFormat($grand_total_dp); ?></td>
						<?php
						}
						
						if($show_payment == true){
							if(!empty($payment_data)){
								foreach($payment_data as $key_id => $dtPay){
									
									$total = 0;
									if(!empty($grand_total_payment[$key_id])){
										$total = priceFormat($grand_total_payment[$key_id]);
									}							
									?>
									<td class="xright xbold"><?php echo $total; ?></td>
									<?php
								}
							}
						}
						
						if($show_note == true){
						?>
						<td class="xright xbold">&nbsp;</td>
						<?php
						}
						
						if($show_shift_kasir == true){
						?>
						<td class="xright xbold">&nbsp;</td>
						<?php
						}
						
						if(!empty($only_txmark)){
							$color_total = 'green';
							$color_total_sales = 'green';
							if($total_tax != $grand_total_post_nontrx){
								$color_total = 'red';
							}
							if($grand_net_sales_total != $grand_total_post_sales){
								$color_total_sales = 'red';
							}
							?>
							<td class="xright"><font color="<?php echo $color_total; ?>"><b><?php echo priceFormat($grand_total_post_nontrx); ?></b></font></td>
							<td class="xright"><font color="<?php echo $color_total_sales; ?>"><b><?php echo priceFormat($grand_total_post_sales); ?></b></font></td>
							<?php
						}
						?>
						
					</tr>
					<?php
				}else{
				?>
					<tr class="tbl-data">
						<td class="first xcenter" colspan="<?php echo $total_cols; ?>">Data Not Found</td>
					</tr>
				<?php
				}
				?>
				
				<tr>
					<td colspan="<?php echo $total_cols-4; ?>" class="first xleft">
						<br/>
						<br/>
						<br/>
						<br/>
						Printed: <?php echo date("d-m-Y H:i:s"); ?>
						<?php if(!empty($only_txmark)){ echo ' / TRX-ON';} ?>
						<br/>
					</td>
					<td colspan="2" class="xcenter">
						<br/>
						Prepared by:<br/><br/><br/><br/>
						----------------------------
					</td>
					<td colspan="2" class="xcenter">
						<br/>
						Approved by:<br/><br/><br/><br/>
						----------------------------
					</td>
				</tr>	
			</tbody>
		</table>
				
		
	</div>
	
	<?php
		if($do == 'print'){
		?>
		<script type="text/javascript">
			window.print();
		</script>
		<?php
		}
	?>
</body>
</html>