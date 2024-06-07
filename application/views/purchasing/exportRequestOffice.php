<?php
$nama_dokumen = "dataRequest";
require("pdf/mpdf60/mpdf.php");
$mpdf = new mPDF('utf-8','A4-L');
ob_start(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
	<title>Export PDF</title>
</head>
<body>
	<div style="width:100%;padding-bottom:170px;">
		<div class="reportPDF">
			<div align="left">
				<table style="width:100%;">
					<tr>
						<td style="width:60%;vertical-align:top;" rowspan="4">
							<img style="width:50%;" src="<?php echo base_url('assets/img/PT. ADNYANA.png'); ?>">
						</td>
						<td style="width:15%;vertical-align:middle;font-size:12px;text-align:center;border:2px solid black;"><b>OFFICE</b></td>
						<td style="width:10%;vertical-align:bottom;font-size:12px;padding-left:20px;">DATE</td>
						<td style="width:25%;vertical-align:bottom;font-size:12px;border-bottom: 1px ridge black;"><?php echo $tglReq; ?></td>
					</tr>
					<tr>
						<td style="width:15%;vertical-align:middle;font-size:12px;" rowspan="3"></td>
						<td style="vertical-align:bottom;font-size:12px;padding-left:20px;">APP NO</td>
						<td style="vertical-align:bottom;font-size:12px;border-bottom: 1px ridge black;"><?php echo $appNo; ?></td>
					</tr>
					<tr>
						<td style="vertical-align:bottom;font-size:12px;padding-left:20px;">VESSEL</td>
						<td style="vertical-align:bottom;font-size:12px;border-bottom: 1px ridge black;"><?php echo $vessel; ?></td>
					</tr>
					<tr>
						<td style="vertical-align:bottom;font-size:12px;padding-left:20px;">DEPT</td>
						<td style="vertical-align:bottom;font-size:12px;border-bottom: 1px ridge black;"><?php echo $department; ?></td>
					</tr>
				</table>
			</div>
			<div style="margin-top:-20px;"><h4 style="text-align:center;"><u>APPLICATION FOR SUPPLY</u></h4></div>
			<table style="width:100%;font-size:11px;margin-top:-10px;height:100px;" border="1">
				<thead>
					<tr style="background-color: #A70000;">
						<td style="color:#FFF;width:20px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">No</td>
						<td style="color:#FFF;width:200px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">Name of Article</td>
						<td style="color:#FFF;width:50px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">Code /<br>Part No</td>
						<td style="color:#FFF;width:40px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">Unit</td>
						<td style="color:#FFF;width:50px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">Working<br>on Board</td>
						<td style="color:#FFF;width:50px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">Stock<br>on Board</td>
						<td style="color:#FFF;width:50px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">Request</td>
						<td style="color:#FFF;width:50px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">Approved<br>Order</td>
						<td style="color:#FFF;width:50px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">Mark<br>Reference</td>
						<td style="color:#FFF;width:170px;height:30px;vertical-align:middle;font-weight:bold;border:0px;" align="center">Remark</td>
					</tr>
				</thead>
				<tbody id="idBody">
					<?php echo $trNya; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="footer" style="position: fixed;bottom: 0px;">
		<table style="width:90%;margin-top:5px;margin-bottom:-10px;" border="0" cellspacing="10">
			<tr>
				<td style="width:20%;vertical-align:top;text-align:center;font-size:11px;">
					<label>Made By</label><br>
					<?php echo $madeQrCode; ?>
				</td>
				<td style="width:20%;vertical-align:top;text-align:center;font-size:11px;">
					<label>Management Approval</label><br>
					<?php echo $mngnAppvrQrCode; ?>
				</td>
				<td style="width:20%;vertical-align:top;text-align:center;font-size:11px;">
				</td>
				<td style="width:20%;vertical-align:top;text-align:center;font-size:11px;">
				</td>
				<td style="width:20%;font-size:10px;">
					NOTE : 	<br>
						- A = TO BE REPLACE URGENTLY<br>
						- B = BETTER TO BE REPLACE<br>
						- C = STOCK FOR NEXT O/HAUL<br>
						- D = STOCK FOR EMERGENCY
				</td>					
			</tr>
			<tr>
				<td style="vertical-align:top;text-align:center;border-bottom:2px solid black;font-size:11px;"><?php echo $madeBy; ?></td>
				<td style="vertical-align:top;text-align:center;border-bottom:2px solid black;font-size:11px;"><?php echo $mngnAppvrName; ?></td>
				<td style="vertical-align:top;text-align:center;"></td>
				<td style="vertical-align:top;text-align:center;"></td>
			</tr>
			<tr>
				<td style="vertical-align:top;text-align:center;font-size:10px;padding-top:-5px;"><?php echo $madeDate; ?></td>
				<td style="vertical-align:top;text-align:center;font-size:10px;padding-top:-5px;"><?php echo $mngnAppvrDate; ?></td>
				<td style="vertical-align:top;text-align:center;font-size:10px;padding-top:-5px;"></td>
				<td style="vertical-align:top;text-align:center;font-size:10px;padding-top:-5px;"></td>
			</tr>
		</table>
		<b style="font-size:10px;">Form 02S/Rev.04/17-12-2021</b>
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