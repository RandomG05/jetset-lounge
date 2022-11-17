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
		$set_width = 1330;
		$total_cols = 13;
		
		//update-0120.001
		if(!empty($filter_column)){
			extract($filter_column);
		}
		
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
					<td class="xcenter" width="130" rowspan="2">TANGGAL</td>
					<td class="xcenter" width="80" rowspan="2">QTY BILLING</td>
					<td class="xcenter" width="120" rowspan="2">TOTAL BILLING</td>
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
					$total_qty = 0;
					$total_billing = 0;
					$total_tax = 0;
					$total_service = 0;
					$grand_total = 0;
					//$grand_total_cash = 0;
					//$grand_total_credit = 0;	
					$grand_sub_total = 0;
					$grand_net_sales_total = 0;
					$grand_total_pembulatan = 0;			
					$grand_total_payment = array();
					$grand_discount_total = 0;
					$grand_discount_billing_total = 0;
					$grand_total_dp = 0;
					$grand_total_compliment = 0;
				
					$grand_discount_total_before = 0;
					$grand_discount_billing_total_before = 0;
					$grand_discount_total_after = 0;
					$grand_discount_billing_total_after = 0;
					
					$grand_total_post_nontrx = 0;
					$grand_total_post_sales = 0;
					
					foreach($report_data as $det){
						
						?>
						<tr class="tbl-data">
							<td class="first xcenter"><?php echo $no; ?></td>
							<td class="xcenter"><?php echo $det['date']; ?></td>
							<td class="xcenter"><?php echo $det['qty_billing']; ?></td>
							<td class="xright"><?php echo $det['total_billing_show']; ?></td>
							<?php
							if($diskon_sebelum_pajak_service == 1 OR count($display_discount_type) > 1){
								if(count($display_discount_type) > 1){
									?>
									<td class="xright"><?php echo $det['discount_total_before_show']; ?></td>
									<td class="xright"><?php echo $det['discount_billing_total_before_show']; ?></td>
									<?php
								}else
								{
									?>
									<td class="xright"><?php echo $det['discount_total_show']; ?></td>
									<td class="xright"><?php echo $det['discount_billing_total_show']; ?></td>
									<?php
								}
								
							}
							
							if($diskon_sebelum_pajak_service == 0 OR count($display_discount_type) > 1){
								if(count($display_discount_type) > 1){
									?>
									<td class="xright"><?php echo $det['discount_total_after_show']; ?></td>
									<td class="xright"><?php echo $det['discount_billing_total_after_show']; ?></td>
									<?php
								}else
								{
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
							}?>
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
										?>
										<td class="xright"><?php echo $det['total_payment_'.$key_id.'_show']; ?></td>
										<?php
										if(empty($grand_total_payment[$key_id])){
											$grand_total_payment[$key_id] = 0;
										}
										
										$grand_total_payment[$key_id] += $det['total_payment_'.$key_id];
									}
								}
							}
							
							
							if(!empty($only_txmark)){
								$color_nontrx_total = 'green';
								$color_sales_total = 'green';
								if($det['total_post_nontrx'] != $det['tax_total']){
									$color_nontrx_total = 'red';
								}
								if($det['total_post_sales'] != $det['net_sales_total']){
									$color_sales_total = 'red';
								}
								?>
								<td class="xright"><font color="<?php echo $color_nontrx_total; ?>"><b><?php echo priceFormat($det['total_post_nontrx']); ?></b></font></td>
								<td class="xright"><font color="<?php echo $color_sales_total; ?>"><b><?php echo priceFormat($det['total_post_sales']); ?></b></font></td>
								<?php
								if(!empty($det['total_post_nontrx'])){
									$grand_total_post_nontrx += $det['total_post_nontrx'];
									$grand_total_post_sales += $det['total_post_sales'];
								}
							}
							?>
							
						</tr>
						<?php	
						
						$total_qty +=  $det['qty_billing'];
						$total_billing +=  $det['total_billing'];
						$total_tax +=  $det['tax_total'];
						$total_service +=  $det['service_total'];
						$grand_total +=  $det['grand_total'];
						//$grand_total_cash +=  $det['total_cash'];
						//$grand_total_credit +=  $det['total_credit'];
						$grand_sub_total += $det['sub_total'];
						$grand_net_sales_total += $det['net_sales_total'];
						$grand_total_pembulatan += $det['total_pembulatan'];
						$grand_discount_total +=  $det['discount_total'];
						$grand_discount_billing_total +=  $det['discount_billing_total'];
						$grand_total_dp +=  $det['total_dp'];
						$grand_total_compliment +=  $det['total_compliment'];
						
						$grand_discount_total_before += $det['discount_total_before'];
						$grand_discount_billing_total_before += $det['discount_billing_total_before'];
						$grand_discount_total_after += $det['discount_total_after'];
						$grand_discount_billing_total_after += $det['discount_billing_total_after'];
						
						$no++;
					}
					
					?>
					<tr class="tbl-total">
						<td class="first xright xbold" colspan="2">TOTAL</td>
						<td class="xcenter xbold"><?php echo $total_qty; ?></td>
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
							foreach($grand_total_payment as $dt){
								?>
								<td class="xright xbold"><?php echo priceFormat($dt); ?></td>
								<?php 
							}
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
						<td class="first xcenter" colspan="<?php echo $total_cols; ?>" class="first xleft">Data Not Found</td>
					</tr>
				<?php
				}
				?>
				<tr class="tbl-sign">
					<td colspan="<?php echo $total_cols; ?>" class="first xleft">
						<br/>
						<br/>
						<div class="fleft" style="width:200px;">
							<br/><br/><br/><br/>
							Printed: <?php echo date("d-m-Y H:i:s");?>
							<?php if(!empty($only_txmark)){ echo ' / TRX-ON';} ?>
						</div>
						<div class="fright" style="width:250px;">
							Prepared by:<br/><br/><br/><br/>
							----------------------------
						</div>
						<div class="fright" style="width:250px;">
							Approved by:<br/><br/><br/><br/>
							----------------------------
						</div>
						
						<div class="fclear"></div>
						<br/>
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