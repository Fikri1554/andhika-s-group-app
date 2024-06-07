<?php
$nama_dokumen = "dataRequest";
require("pdf/mpdf60/mpdf.php");
$mpdf = new mPDF('utf-8', 'A4');
ob_start(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
	<title>Export PDF</title>
</head>
<body>
	<div style="width:100%;min-height:500px;">		
		<div class="reportPDF" style="width:100%;min-height:0px;">
			<div align="center" style="padding-top: -40px;border-bottom:5px double black;">
				<img style="width:100%;" src="<?php echo base_url('assets/img/PT. ADNYANA.png'); ?>".>
			</div>
			<table style="width:100%;font-size: 11px;margin-top:5px;">
				<tr>
					<td style="width:80px;vertical-align:top;">Tanggal</td>
					<td style="width:10px;vertical-align:top;">:</td>
					<td style="width:220px;"><?php echo $tglReq; ?></td>
					<td style="width:80px;vertical-align:top;">Department</td>
					<td style="width:10px;vertical-align:top;">:</td>
					<td style="width:220px;"><?php echo $department; ?></td>
				</tr>
				<tr>
					<td style="width:80px;vertical-align:top;">App No</td>
					<td style="width:10px;vertical-align:top;">:</td>
					<td style="width:220px;"><?php echo $appNo; ?></td>
					<td style="width:80px;vertical-align:top;">Vessel</td>
					<td style="width:10px;vertical-align:top;">:</td>
					<td style="width:220px;"><?php echo $vessel; ?></td>
				</tr>
			</table>
			<div><h3 style="text-align:center;"><u>Data Detail Request</u></h3></div>
			<table style="width: 100%;font-size: 11px;margin-top:-10px;">
				<thead>
					<tr style="background-color: #A70000;">
						<td style="color:#FFF;width:20px;height:30px;vertical-align:middle;font-weight:bold;" align="center">No</td>
						<td style="color:#FFF;width:80px;height:30px;vertical-align:middle;font-weight:bold;" align="center">Code / Part No</td>
						<td style="color:#FFF;width:200px;height:30px;vertical-align:middle;font-weight:bold;" align="center">Name of Article</td>
						<td style="color:#FFF;width:60px;height:30px;vertical-align:middle;font-weight:bold;" align="center">Unit</td>
						<td style="color:#FFF;width:60px;height:30px;vertical-align:middle;font-weight:bold;" align="center">Working</td>
						<td style="color:#FFF;width:60px;height:30px;vertical-align:middle;font-weight:bold;" align="center">Stock</td>
						<td style="color:#FFF;width:60px;height:30px;vertical-align:middle;font-weight:bold;" align="center">Request</td>
						<td style="color:#FFF;width:60px;height:30px;vertical-align:middle;font-weight:bold;" align="center">Mark Ref.</td>
					</tr>
				</thead>
				<tbody id="idBody">
					<?php echo $trNya; ?>
				</tbody>
			</table>
			<table style="font-size: 10px;margin-top: 10px;">
				<tr>
					<td>
						NOTE : 	<br>
							- A = TO BE REPLACE URGENTLY<br>
							- B = BETTER TO BE REPLACE<br>
							- C = STOCK FOR NEXT O/HAUL<br>
							- D = STOCK FOR EMERGENCY
					</td>
				</tr>
			</table>
		</div>
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