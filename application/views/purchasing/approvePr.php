<?php $this->load->view("purchasing/menu"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#btnSearch").click(function(){
				var valSearch = $("#txtSearch").val();
				if(valSearch == "")
				{
					alert("Search Empty..!!");
					return false;
				}
				$("#idLoading").show();
				$.post('<?php echo base_url("approve/getApprovePr"); ?>/search/',
				{ valSearch : valSearch },
					function(data) 
					{
						$("#idTbody").empty();
						$("#idTbody").append(data.trNya);
						$("#idPage").empty();
						$("#idLoading").hide();
					},
				"json"
				);
			});
		});
		function submitApproveCheckRequest()
		{
			var cfm = confirm("Approve Data..??");
			if(cfm)
			{
				var idReq = $("#txtIdReqModalCheckReq").val();

				$.post('<?php echo base_url("approve/approveModalCheckRequest"); ?>',
				{ idReq : idReq },
					function(data)
					{
						alert(data);
						reloadPage();
					},
				"json"
				);
			}
		}
		function showModalCheckReq(idReq = "")
		{
			$("#idLoading").show();
			$('#modalCheckReq').modal('show');

			$.post('<?php echo base_url("approve/getModalDetailCheckRequest"); ?>',
			{ idReq : idReq },
				function(data) 
				{
					$("#txtIdReqModalCheckReq").val(idReq);
					$("#txtDateModalReq").text(data.dateNya);
					$("#txtAppNoModalReq").text(data.appNo);
					$("#txtVesselModalReq").text(data.Vessel);
					$("#txtDeptModalReq").text(data.Dept);

					$("#idTbodyCheckReq").empty();
					$("#idTbodyCheckReq").append(data.trNya);

					$("#idLoading").hide();
					$('#modalCheckReq').modal('show');
				},
			"json"
			);
		}
		function revisiCheckReq()
		{
			$("#lblModalCheckReq").text("Revise");
			var idReq = $("#txtIdReqModalCheckReq").val();
			$("#idModalCheckReq").empty();
			$("#idBtnDetailRequest").empty();
			var divNya = "";
				divNya += "<div class='row'>";
					divNya += "<div class='col-md-12'>";
						divNya += "<div class='col-md-2'>";
							divNya += "<label>Remark :</label>";
						divNya += "</div>";
						divNya += "<div class='col-md-10'>";
							divNya += "<textarea class='form-control input-sm' id='txtRevisiModalCheckReq'></textarea>";
						divNya += "</div>";
					divNya += "</div>";
					divNya += "<div class='col-md-12' align='center' style='padding:10px;'>";
						divNya += "<button id='btnSubmitRevisi' onclick='submitRevisiCheckReq("+idReq+");' class='btn btn-primary btn-sm' title='Revisi'><i class='fa fa-check-square-o'></i> Submit</button> <button id='btnCancelModalRevisiCheckReq' onclick='reloadPage();' class='btn btn-danger btn-sm' title='Cancel'><i class='fa fa-ban'></i> Cancel</button>";
					divNya += "</div>";
				divNya += "</div>";
			$("#idModalCheckReq").append(divNya);
		}
		function submitRevisiCheckReq(idReq = "")
		{
			var remark = $("#txtRevisiModalCheckReq").val();
			if(remark == "")
			{
				alert("Remark Empty..!!");
				return false;
			}
			$.post('<?php echo base_url("approve/addRevisiModalCheckRequest"); ?>',
			{ idReq : idReq,remark : remark },
				function(data)
				{
					alert(data);
					reloadPage();
				},
			"json"
			);
		}
		function reloadPage()
		{
			window.location = "<?php echo base_url('approve/getApprovePr');?>";
		}
	</script>
</head>
<body>
	<section id="container">
		<section id="main-content">
			<section class="wrapper site-min-height" style="min-height:400px;">
				<h3>
					<i class="fa fa-angle-right"></i> Approve PR<span style="display:none;padding-left:20px;" id="idLoading"><img src="<?php echo base_url('assets/img/loading.gif'); ?>" ></span>
				</h3>
				<div class="form-panel" id="idDataTable">
					<div class="row" id="btnNavAtas">
						<div class="col-md-2">
							<input type="text" class="form-control input-sm" id="txtSearch" value="" placeholder="Search Vessel">
						</div>
						<div class="col-md-2">
							<button type="button" id="btnSearch" class="btn btn-warning btn-sm btn-block" title="Add"><i class="fa fa-search"></i> Search</button>
						</div>
						<div class="col-md-2">
							<button type="button" onclick="reloadPage();" id="btnSearch" class="btn btn-success btn-sm btn-block" title="Refresh"><i class="fa fa-refresh"></i> Refresh</button>
						</div>
					</div>
					<div class="row mt" id="idData1">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
									<thead>
										<tr style="background-color: #A70000;color: #FFF;height:40px;">
											<th style="vertical-align: middle; width:5%;text-align:center;">No</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Req. Date</th>
											<th style="vertical-align: middle; width:15%;text-align:center;">App. No</th>
											<th style="vertical-align: middle; width:15%;text-align:center;">Vessel</th>
											<th style="vertical-align: middle; width:15%;text-align:center;">Department</th>
											<th style="vertical-align: middle; width:15%;text-align:center;">Status</th>
											<th style="vertical-align: middle; width:15%;text-align:center;">Required</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Action</th>
										</tr>
									</thead>
									<tbody id="idTbody">
										<?php echo $trNya; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</section>
		</section>
	</section>
	<div class="modal fade" id="modalCheckReq" role="dialog">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	        	<div class="modal-header" style="padding: 10px;background-color:#A70000;">
	          		<button type="button" class="close" data-dismiss="modal" style="opacity:unset;text-shadow:none;color:#FFF;">&times;</button>
	          		<h4 class="modal-title" id="lblModalCheckReq">Approve PR</h4>
	        	</div>
	        	<div class="modal-body" id="idModalCheckReq">
	        		<div class="row">
						<div class="col-md-3 col-xs-12">
							<div class="col-md-4 col-xs-4">
								<label style="font-size:12px;">DATE</label><b style="float:right;">:</b>
							</div>
							<div class="col-md-8 col-xs-8">
								<label id="txtDateModalReq" style="font-weight:bold;"></label>
							</div>
						</div>
						<div class="col-md-3 col-xs-12">
							<div class="col-md-4 col-xs-4">
								<label>APP NO</label><b style="float:right;">:</b>
							</div>
							<div class="col-md-8 col-xs-8">
								<label id="txtAppNoModalReq" style="font-weight:bold;"></label>
							</div>
						</div>
						<div class="col-md-3 col-xs-12">
							<div class="col-md-4 col-xs-4">
								<label>VESSEL</label><b style="float:right;">:</b>
							</div>
							<div class="col-md-8 col-xs-8">
								<label id="txtVesselModalReq" style="font-weight:bold;"></label>
							</div>
						</div>
						<div class="col-md-3 col-xs-12">
							<div class="col-md-4 col-xs-4">
								<label>DEPT</label><b style="float:right;">:</b>
							</div>
							<div class="col-md-8 col-xs-8">
								<label id="txtDeptModalReq" style="font-weight:bold;"></label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 col-xs-12">
							<div class="table-responsive" id="idTableModal">
								<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
									<thead>
										<tr style="background-color: #A70000;color: #FFF;height:40px;">
											<th style="vertical-align: middle; width:3%;text-align:center;">No</th>
											<th style="vertical-align: middle; width:25%;text-align:center;">Name of Article</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Code /<br>Part No</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Unit</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Working on<br>Board</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Stock on<br>Board</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Request</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Approved<br>Order</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Mark<br>Reference</th>
											<th style="vertical-align: middle; width:20%;text-align:center;">Remark</th>
										</tr>
									</thead>
									<tbody id="idTbodyCheckReq">
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-md-12 col-xs-12" style="font-size:10px;">
							NOTE : 	<br>
							- A = TO BE REPLACE URGENTLY<br>
							- B = BETTER TO BE REPLACE<br>
							- C = STOCK FOR NEXT O/HAUL<br>
							- D = STOCK FOR EMERGENCY
						</div>
					</div>
					<div class="row" style="margin-top:5px;">
						<div class="col-md-4 col-xs-12">
							<input type="hidden" id="txtIdReqModalCheckReq" value="">
							<button class="btn btn-primary btn-xs btn-block" onclick="submitApproveCheckRequest();"><i class="fa fa-check-square-o"></i> Approve</button>
						</div>
						<div class="col-md-4 col-xs-12">
							<button class="btn btn-danger btn-xs btn-block" onclick="reloadPage();"><i class="fa fa-ban"></i> Cancel</button>
						</div>
						<div class="col-md-4 col-xs-12">
							<button class="btn btn-warning btn-xs btn-block" onclick="revisiCheckReq();"><i class="fa fa-reply-all"></i> Revisi</button>
						</div>
					</div>
	        	</div>
			</div>
		</div>
	</div>
</body>
</html>

