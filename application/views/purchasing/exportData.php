<?php
$nama_dokumen = $fileName;
require("pdf/mpdf60/mpdf.php");
$mpdf = new mPDF('utf-8', 'A4');
ob_start(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
	<title>Export PDF</title>
</head>
<body>
	<div style="width:100%;padding-bottom:170px;">		
		<div class="reportPDF" style="width:100%;min-height:0px;">
			<div align="center" style="padding-top: -10px;border-bottom:5px double black;">
				<table style="width:100%;">
					<tr>
						<td style="width:75%;vertical-align:top;" rowspan="4">
							<img style="width:70%;" src="<?php echo base_url('assets/img/'.$headNya[0]->ship_company.'.png'); ?>">
						</td>
						<td style="width:25%;vertical-align:middle;font-size:14px;text-align:center;"><b><u>PURCHASE ORDER</u></b></td>
					</tr>
				</table>
				<!-- <img style="width:100%;" src="<?php echo base_url('assets/img/'.$headNya[0]->ship_company.'.png'); ?>".> -->
			</div>
			<table style="width:100%;font-size: 11px;margin-top:5px;" border="1">
				<tr>
					<td style="width:80px;vertical-align:top;border:0px;font-weight:bold;">ORDER TO</td>
					<td style="width:10px;vertical-align:top;border:0px;">:</td>
					<td style="width:220px;border:0px;"><?php echo $headNya[0]->order_company."<br><i>( ".$headNya[0]->order_name." )</i>"; ?></td>
					<td style="width:80px;vertical-align:top;border:0px;font-weight:bold;">SHIP TO</td>
					<td style="width:10px;vertical-align:top;border:0px;">:</td>
					<td style="width:220px;border:0px;"><?php echo $headNya[0]->ship_company."<br><i>( ".$headNya[0]->ship_name." )</i>"; ?></td>
				</tr>
				<tr>
					<td style="width:80px;vertical-align:top;border:0px;font-weight:bold;">PO DATE</td>
					<td style="width:10px;vertical-align:top;border:0px;">:</td>
					<td style="width:220px;border:0px;"><?php echo $poDate; ?></td>
					<td style="width:80px;vertical-align:top;border:0px;font-weight:bold;">PO NO</td>
					<td style="width:10px;vertical-align:top;border:0px;">:</td>
					<td style="width:220px;border:0px;"><?php echo $headNya[0]->po_no; ?></td>
				</tr>
				<tr>
					<td style="width:80px;vertical-align:top;border:0px;font-weight:bold;">SUBJECT</td>
					<td style="width:10px;vertical-align:top;border:0px;">:</td>
					<td style="width:220px;border:0px;" colspan="3"><?php echo $headNya[0]->subject; ?></td>
				</tr>
			</table>
			<table style="width:100%;font-size:11px;margin-top:5px;" border="1">
				<thead>
					<tr style="background-color: #A70000;">
						<td style="color:#FFF;width:30px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">NO</td>
						<td style="color:#FFF;width:70px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">P/N</td>
						<td style="color:#FFF;width:350px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">DESCRIPTION</td>
						<td style="color:#FFF;width:70px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">QTY</td>
						<td style="color:#FFF;width:70px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">UNIT PRICE</td>
						<td style="color:#FFF;width:80px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">AMOUNT</td>
					</tr>
				</thead>
				<tbody id="idBody">
					<?php echo $trNya; ?>
				</tbody>
			</table>			
		</div>
	</div>
	<div class="footer" style="position:fixed;bottom:0px;">
		<table style="font-size:12px;" cellspacing="5">
			<tr>
				<td colspan="2">Approved By :</td>
			</tr>
			<?php echo $trTTDnya; ?>
			<tr>
				<td colspan="2" style="font-size:9px;padding-top:-5px;">TEC 6.7.1/Rev 0.0/06-03-2020</td>
			</tr>
		</table>
	</div>
</body>
</html>
 
<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf->WriteHTML(utf8_encode($html));
$mpdf->Output($nama_dokumen.".pdf" ,'I');
exit;
?>