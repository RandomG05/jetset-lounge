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
		$set_width = 1290;
		$total_cols = 13;
		
		//update-0120.001
		if(!empty($filter_column)){
			extract($filter_column);
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
			$set_width -= 90;
			$total_cols -= 1;
		}
		if($show_service == false){
			$set_width -= 90;
			$total_cols -= 1;
		}
		if($show_compliment == false){
			$set_width -= 110;
			$total_cols -= 1;
		}
	?>
	<div class="report_area" style="width:<?php echo $set_width.'px'; ?>;">
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
		<table width="<?php echo $set_width; ?>">
			<!-- HEADER -->
			<tr class="tbl-header">
				<td class="first xcenter" width="40" rowspan="2">NO</td>
				<td class="xcenter" width="110" rowspan="2">CODE</td>
				<td class="xcenter" width="260" rowspan="2">PRODUCT / ITEM</td>
				<td class="xcenter" width="60" rowspan="2">TOTAL QTY</td>
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
				
				
				if($show_compliment == true){
				?>
				<td class="xcenter" width="110" rowspan="2">COMPLIMENT</td>
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
				
				?>
				<td class="xcenter" width="100" rowspan="2">PEMBULATAN<br/>*AVERAGE</td>	
				<td class="xcenter" width="110" rowspan="2">GRAND TOTAL</td>
				<?php
				if($show_payment == true){
					?>
					<td class="xcenter" width="<?php echo count($payment_data)*100; ?>" colspan="<?php echo count($payment_data); ?>">PAYMENT + DP *AVERAGE</td>	
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
			<?php
			if(!empty($report_data)){
			
				$no = 1;
				$total_qty = 0;
				$total_billing = 0;
				$total_sub_total = 0;
				$total_net_sales_total = 0;
				$total_tax = 0;
				$total_service = 0;
				$total_pembulatan = 0;
				$grand_total = 0;
				$grand_total_payment = array();
				$discount_total = 0;
				$discount_billing_total = 0;
				$grand_discount_total = 0;
				$grand_discount_billing_total = 0;
				$compliment_total = 0;
				
				$grand_discount_total_before = 0;
				$grand_discount_billing_total_before = 0;
				$grand_discount_total_after = 0;
				$grand_discount_billing_total_after = 0;
				
				foreach($report_data as $det){
					
					if(empty($det['product_name'])){
						$det['product_name'] = '#'.$det['product_id'].' deleted';
					}
						
					if(empty($det['product_code'])){
						$det['product_code'] = 'N/A';
					}
					
					?>
					<tr class="tbl-data">
						<td class="first xcenter"><?php echo $no; ?></td>
						<td class="xleft"><?php echo $det['product_code']; ?></td>
						<td class="xleft"><?php echo $det['product_name']; ?></td>
						<td class="xcenter"><?php echo $det['total_qty']; ?></td>
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
						<td class="xright"><?php echo $det['compliment_total_show']; ?></td>
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
						?>
						<td class="xright"><?php echo $det['total_pembulatan_show']; ?></td>
						<td class="xright"><?php echo $det['grand_total_show']; ?></td>
						<?php
						if($show_payment == true){
							if(!empty($payment_data)){
								foreach($payment_data as $key_id => $dtPay){
									
									$total_payment = 0;
									if(!empty($det['payment_'.$key_id])){
										$total_payment = $det['payment_'.$key_id];
									}
									
									if(empty($grand_total_payment[$key_id])){
										$grand_total_payment[$key_id] = 0;
									}
									
									if(empty($cat_grand_total_payment[$key_id])){
										$cat_grand_total_payment[$key_id] = 0;
									}
									
									$cat_grand_total_payment[$key_id] += $total_payment;
									$grand_total_payment[$key_id] += $total_payment;
									
									$total_payment_show = priceFormat($total_payment);
									
									?>
									<td class="xright"><?php echo $total_payment_show; ?></td>
									<?php
																	
								}
							}
						}
						?>
					</tr>
					<?php	
					
					$total_qty +=  $det['total_qty'];
					$total_billing +=  $det['total_billing'];
					$total_sub_total +=  $det['sub_total'];
					$total_net_sales_total +=  $det['net_sales_total'];
					$total_tax +=  $det['tax_total'];
					$total_service +=  $det['service_total'];
					$total_pembulatan +=  $det['total_pembulatan'];
					$grand_total +=  $det['grand_total'];
					
					if($diskon_sebelum_pajak_service == 1 OR count($display_discount_type) > 1){
						if(count($display_discount_type) > 1){
							$grand_discount_total_before += $det['discount_total_before'];
							$grand_discount_billing_total_before += $det['discount_billing_total_before'];
							
						}else
						{
							$grand_discount_total += $det['discount_total'];
							$grand_discount_billing_total += $det['discount_billing_total'];
						}
					}
				
					if($diskon_sebelum_pajak_service == 0 OR count($display_discount_type) > 1){
						if(count($display_discount_type) > 1){
							$grand_discount_total_after += $det['discount_total_after'];
							$grand_discount_billing_total_after += $det['discount_billing_total_after'];
							
						}else
						{
							$grand_discount_total += $det['discount_total'];
							$grand_discount_billing_total += $det['discount_billing_total'];
						}
					}
				
					$compliment_total +=  $det['compliment_total'];
					$no++;
				}
				
				?>
				<tr class="tbl-total">
					<td class="first xright xbold" colspan="<?php echo 3; ?>">TOTAL</td>
					<td class="xcenter xbold"><?php echo priceFormat($total_qty); ?></td>
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
					<td class="xright xbold"><?php echo priceFormat($compliment_total); ?></td>
					<?php
					}
					?>
					<td class="xright xbold"><?php echo priceFormat($total_net_sales_total); ?></td>
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
					
					?>
					<td class="xright xbold"><?php echo priceFormat($total_pembulatan); ?></td>
					<td class="xright xbold"><?php echo priceFormat($grand_total); ?></td>
					<?php
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
					?>
				</tr>
				<?php
			}else{
			?>
				<tr class="tbl-data">
					<td colspan="<?php echo $total_cols; ?>" class="first xleft">Data Not Found</td>
				</tr>
			<?php
			}
			?>
			
			<tr>
				<td colspan="<?php echo $total_cols-4; ?>" class="first xleft">
					*untuk payment bersifat asumsi dan simulasi, bisa jadi hasil perhitungan tidak sesuai dgn total laporan sales<br/>
					hal ini dipengaruhi perhitungan balancing total terhadap item<br/>
					*DP masuk ke Payment<br/>
					<br/>
					Printed: <?php echo date("d-m-Y H:i:s"); ?>
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