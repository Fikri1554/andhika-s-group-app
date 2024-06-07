<?php $this->load->view("purchasing/menu"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#btnDownload").click(function(){
				var idReq = $("#txtIdReqView").val();
				window.open("<?php echo base_url('purchasing/exportPurchasingBargeCharge');?>/"+idReq+"/download","_blank");
			});
		});
		function backPage()
		{
			window.location = "<?php echo base_url('purchasing/viewListPurchase');?>/"+"<?php echo $headNya[0]->id_request; ?>";
		}
	</script>
</head>
<body>
	<section id="container">
		<section id="main-content">
			<section class="wrapper site-min-height" style="min-height:400px;">
				<div class="form-panel" id="idViewDetail">
					<legend style="text-align: right;">
						<button id="btnBack" onclick="backPage();" style="float: left;" class="btn btn-success btn-xs" title="Back">
							<i class="fa fa-reply-all"></i> Back
						</button>
						<label id="lblForm">
							<span style="display:none;padding-right:10px;" id="idLoadingView"><img src="<?php echo base_url('assets/img/loading.gif'); ?>" >
							</span>Daftar Purchase
						</label>
					</legend>
					<div class="row">
						<div class="col-md-2 col-xs-12" align="right">
							<label style="float:left;">ORDER TO </label><label>:</label>
						</div>
						<div class="col-md-4 col-xs-12" style="padding:0px;">
							<?php echo $headNya[0]->order_company."<br><i>( ".$headNya[0]->order_name." )</i>"; ?>
						</div>
						<div class="col-md-2 col-xs-12" align="right">
							<label style="float:left;">SHIP TO </label><label>:</label>
						</div>
						<div class="col-md-4 col-xs-12" style="padding:0px;">
							<?php echo $headNya[0]->ship_company."<br><i>( ".$headNya[0]->ship_name." )</i>"; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 col-xs-12" align="right">
							<label style="float:left;">PO DATE </label><label>:</label>
						</div>
						<div class="col-md-4 col-xs-12" style="padding:0px;">
							<?php echo $poDate; ?>
						</div>
						<div class="col-md-2 col-xs-12" align="right">
							<label style="float:left;">PO NO </label><label>:</label>
						</div>
						<div class="col-md-4 col-xs-12" style="padding:0px;">
							<?php echo $headNya[0]->po_no_bargecharge; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 col-xs-12" align="right">
							<label style="float:left;">SUBJECT</label><label>:</label>
						</div>
						<div class="col-md-4 col-xs-12" style="padding:0px;">
							<?php echo $headNya[0]->subject; ?>
						</div>
					</div>
					<div class="row mt" id="idData1">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
									<thead>
										<tr style="background-color: #A70000;color: #FFF;height:40px;">
											<th style="vertical-align: middle; width:5%;text-align:center;">No</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">P/N</th>
											<th style="vertical-align: middle; width:30%;text-align:center;">Description</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">QTY</th>
											<th style="vertical-align: middle; width:9%;text-align:center;">Unit Price</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Amount</th>
										</tr>
									</thead>
									<tbody id="idTbodyViewDetail">
										<?php echo $trNya; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 col-xs-12" align="center">
							<input type="hidden" name="txtIdReqView" id="txtIdReqView" value="<?php echo $headNya[0]->id; ?>">
							<button id="btnDownload" class="btn btn-primary btn-sm" name="btnDownload" title="Download">
								<i class="glyphicon glyphicon-cloud-download"></i> Download
							</button>
						</div>
					</div>
				</div>				
			</section>
		</section>
	</section>
</body>
</html>

