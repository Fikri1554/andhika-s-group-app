<?php $this->load->view("purchasing/menu"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#btnApproval").click(function(){
				var idReq = $("#txtIdReqModal").val();
				var typeApprove = $("#txtTypeApprove").val();
				var typeCheck = "";
				var idDet = "";
				var slcVen = "";
				var remark = "";

				var cfm = confirm("Approve Data..??");
				if(cfm)
				{
					if($("#idChkVndr1").is(":checked"))
					{
						typeCheck = $("#idChkVndr1").val();
					}
					if($("#idChkVndr2").is(":checked"))
					{
						typeCheck = $("#idChkVndr2").val();
					}
					if($("#idChkVndr3").is(":checked"))
					{
						typeCheck = $("#idChkVndr3").val();
					}
					if($("#idChkCustom").is(":checked"))
					{
						typeCheck = $("#idChkCustom").val();
						idDet = "";
			    		var valIdDet = $("input[name^='txtIdDetail']").map(function(){return $(this).val();}).get();
					    for (var l = 0; l < valIdDet.length; l++)
					    {
					    	if(idDet == ""){ idDet = valIdDet[l]; }else{ idDet += "*"+valIdDet[l]; }
					    }

					    slcVen = "";
				    	var valSlcVen = $("select[name^='slcVendor']").map(function(){return $(this).val();}).get();
					    for (var l = 0; l < valSlcVen.length; l++)
					    {
					    	if(slcVen == ""){ slcVen = valSlcVen[l]; }else{ slcVen += "*"+valSlcVen[l]; }
					    }
					}

					if(typeCheck == "")
					{
						alert("Select Vendor Empty..!!");
						return false;
					}
					remark = $("#txtRemark").val();
					
					$("#idLoadingModal").show();
				    $.post('<?php echo base_url("approve/approveModal"); ?>',
					{ idReq : idReq,typeApprove : typeApprove,idDet : idDet,slcVen : slcVen,remark : remark,typeCheck : typeCheck },
						function(data) 
						{
							alert(data);
							reloadPage();
						},
					"json"
					);
				}
			});
			$("#btnRevisiModal").click(function(){
				var idReq = $("#txtIdReqModal").val();
				$("#idTableModal").empty();
				$("#idBtnDetailRequest").empty();
				$("#lblModal").text("Revisi Data");
				var divNya = "";
					divNya += "<div class='row'>";
						divNya += "<div class='col-md-12'>";
							divNya += "<div class='col-md-2'>";
								divNya += "<label>Remark :</label>";
							divNya += "</div>";
							divNya += "<div class='col-md-10'>";
								divNya += "<textarea class='form-control input-sm' id='txtRevisiModal'></textarea>";
							divNya += "</div>";
						divNya += "</div>";
						divNya += "<div class='col-md-12' align='center' style='padding:10px;'>";
							divNya += "<button id='btnSubmitRevisi' onclick='submitRevisi("+idReq+");' class='btn btn-primary btn-sm' title='Revisi'><i class='fa fa-check-square-o'></i> Submit</button> <button id='btnCancelModalRevisi' onclick='reloadPage();' class='btn btn-danger btn-sm' title='Cancel'><i class='fa fa-ban'></i> Cancel</button>";
						divNya += "</div>";
					divNya += "</div>";
				$("#idTableModal").append(divNya);
			});
			$("#idChkVndr1").click(function(){
				if (this.checked) {
					$('input:checkbox').not(this).prop('disabled', true);
				}else{
					$('input:checkbox').not(this).prop('disabled', false);
				}
				// 
			});
			$("#idChkVndr2").click(function(){
				if (this.checked) {
					$('input:checkbox').not(this).prop('disabled', true);
				}else{
					$('input:checkbox').not(this).prop('disabled', false);
				}
				// 
			});
			$("#idChkVndr3").click(function(){
				if (this.checked) {
					$('input:checkbox').not(this).prop('disabled', true);
				}else{
					$('input:checkbox').not(this).prop('disabled', false);
				}
				// 
			});
			$("#idChkCustom").click(function(){
				if (this.checked) {
					$('input:checkbox').not(this).prop('disabled', true);
				}else{
					$('input:checkbox').not(this).prop('disabled', false);
				}
			});
			$("#btnSearch").click(function(){
				var idSlcType = $("#idSlcType").val();
				var valSearch = $("#txtSearch").val();

				if(valSearch == "")
				{
					alert("Search Empty..!!");
					return false;
				}
				$("#idLoading").show();
				$.post('<?php echo base_url("approve/getApprove"); ?>/search/',
				{ valSearch : valSearch,idSlcType : idSlcType },
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
		function showModal(id,typeApprove="",typeView="")
		{
			$("#idLoading").show();
			$("#lblModal").text("Data Detail");

			if(typeView != "")
			{
				$("#btnApproval").css('display','none');
				$("#btnRevisiModal").css('display','none');
				$("#btnCancelModalDetail").text("Close");
			}else{
				$("#btnApproval").css('display','');
				$("#btnRevisiModal").css('display','');
			}
			
			$.post('<?php echo base_url("approve/getModalDetailReq"); ?>',
			{ idReq : id,typeApprove : typeApprove },
				function(data) 
				{
					$("#idTbodyDetailReq").empty();
					$("#idTbodyDetailReq").append(data.trNya);
					$("#txtIdReqModal").val(data.idReq);
					$("#txtTypeApprove").val(typeApprove);
					$("#lblVnd1").empty();
					$("#lblVnd2").empty();
					$("#lblVnd3").empty();
					$("#lblVnd1").append(data.fileVendor1);
					$("#lblVnd2").append(data.fileVendor2);
					$("#lblVnd3").append(data.fileVendor3);
					if(typeApprove == "approve2")
					{
						if(data.headReq[0].type_check1 == "quot1")
						{
							$("#idChkVndr1").attr("checked",true);
							$('input:checkbox').not("#idChkVndr1").prop('disabled', true);
						}
						else if(data.headReq[0].type_check1 == "quot2")
						{
							$("#idChkVndr2").attr("checked",true);
							$('input:checkbox').not("#idChkVndr2").prop('disabled', true);
						}
						else if(data.headReq[0].type_check1 == "quot3")
						{
							$("#idChkVndr3").attr("checked",true);
							$('input:checkbox').not("#idChkVndr3").prop('disabled', true);
						}else{
							$("#idChkCustom").attr("checked",true);
							$('input:checkbox').not("#idChkCustom").prop('disabled', true);
						}
						if(data.headReq[0].remark_check1 != "")
						{
							$("#idLblRemark").append("Note : <br>"+data.headReq[0].remark_check1);
						}						
					}
					if(typeApprove == "")
					{
						$("#lblModal").text("View data");
						if(data.headReq[0].type_check2 == "quot1")
						{
							$("#idChkVndr1").attr("checked",true);
						}
						else if(data.headReq[0].type_check2 == "quot2")
						{
							$("#idChkVndr2").attr("checked",true);
						}
						else if(data.headReq[0].type_check2 == "quot3")
						{
							$("#idChkVndr3").attr("checked",true);
						}else{
							$("#idChkCustom").attr("checked",true);							
						}
						if(data.headReq[0].remark_check2 != "")
						{
							$("#txtRemark").val(data.headReq[0].remark_check2);
						}						
					}
					$("#idLoading").hide();
					$('#modalReqDetail').modal('show');
				},
			"json"
			);
		}
		function submitRevisi(idReq)
		{
			var idReqNya = idReq;
			var remark = $("#txtRevisiModal").val();
			if(remark == "")
			{
				alert("Remark Empty..!!");
				return false;
			}
			$.post('<?php echo base_url("approve/addRevisiModal"); ?>',
			{ idReq : idReqNya,remark : remark },
				function(data)
				{
					alert(data);
					reloadPage();
				},
			"json"
			);
		}
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
		function submitApproveOffice()
		{
			var cfm = confirm("Approve Data..??");
			if(cfm)
			{
				var idReq = $("#txtIdReqModalApproveOffice").val();

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
		function showModalApproveOffice(idReq = "")
		{
			$("#idLoading").show();

			$.post('<?php echo base_url("approve/getModalDetailApproveOffice"); ?>',
			{ idReq : idReq },
				function(data) 
				{
					$("#txtIdReqModalApproveOffice").val(idReq);
					$("#txtDateModalApproveOffice").text(data.dateNya);
					$("#txtAppNoModalApproveOffice").text(data.appNo);
					$("#txtVesselModalApproveOffice").text(data.Vessel);
					$("#txtDeptModalApproveOffice").text(data.Dept);

					$("#idTbodyApproveOffice").empty();
					$("#idTbodyApproveOffice").append(data.trNya);

					$("#idLoading").hide();
					$('#modalApproveOffice').modal('show');
				},
			"json"
			);
		}
		function revisiCheckReq()
		{
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
		function revisiApproveOffice()
		{
			var idReq = $("#txtIdReqModalApproveOffice").val();
			$("#idModalApproveOffice").empty();
			var divNya = "";
				divNya += "<div class='row'>";
					divNya += "<div class='col-md-12'>";
						divNya += "<div class='col-md-2'>";
							divNya += "<label>Remark :</label>";
						divNya += "</div>";
						divNya += "<div class='col-md-10'>";
							divNya += "<textarea class='form-control input-sm' id='txtRevisiModalApproveOffice'></textarea>";
						divNya += "</div>";
					divNya += "</div>";
					divNya += "<div class='col-md-12' align='center' style='padding:10px;'>";
						divNya += "<button id='btnSubmitRevisi' onclick='submitRevisiApproveOffice("+idReq+");' class='btn btn-primary btn-sm' title='Revisi'><i class='fa fa-check-square-o'></i> Submit</button> <button id='btnCancelModal' onclick='reloadPage();' class='btn btn-danger btn-sm' title='Cancel'><i class='fa fa-ban'></i> Cancel</button>";
					divNya += "</div>";
				divNya += "</div>";
			$("#idModalApproveOffice").append(divNya);
		}
		function submitRevisiApproveOffice(idReq = "")
		{
			var remark = $("#txtRevisiModalApproveOffice").val();
			if(remark == "")
			{
				alert("Remark Empty..!!");
				return false;
			}
			$.post('<?php echo base_url("approve/addRevisiModalApproveOffice"); ?>',
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
			window.location = "<?php echo base_url('approve/getApprove');?>";
		}
	</script>
</head>
<body>
	<section id="container">
		<section id="main-content">
			<section class="wrapper site-min-height" style="min-height:400px;">
				<h3>
					<i class="fa fa-angle-right"></i> Approval<span style="display:none;padding-left:20px;" id="idLoading"><img src="<?php echo base_url('assets/img/loading.gif'); ?>" ></span>
				</h3>
				<div class="form-panel" id="idDataTable">
					<div class="row" id="btnNavAtas">
						<div class="col-md-2">
							<select class="form-control input-sm" id="idSlcType">
								<option value="appNo">App No</option>
								<option value="vessel">Vessel</option>
							</select>
						</div>
						<div class="col-md-2">
							<input type="text" class="form-control input-sm" id="txtSearch" value="" placeholder="Search Text">
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
							<div id="idPage"><?php echo $listPage; ?></div>
						</div>
					</div>
				</div>
			</section>
		</section>
	</section>
	<div class="modal fade" id="modalReqDetail" role="dialog">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	        	<div class="modal-header" style="padding: 10px;background-color:#A70000;">
	          		<button type="button" class="close" data-dismiss="modal" style="opacity:unset;text-shadow:none;color:#FFF;">&times;</button>
	          		<h4 class="modal-title">Data Approve</h4>
	        	</div>
	        	<div class="modal-body" id="idModalDetail">
	        		<div class="row">
						<div class="col-md-12">							
							<legend style="text-align: right;">
								<span style="display:none;" id="idLoadingModal"><img src="<?php echo base_url('assets/img/loading.gif'); ?>" ></span>
								<label id="lblModal"></label>
							</legend>
							<div class="table-responsive" id="idTableModal">
								<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
									<thead>
										<tr style="background-color: #A70000;color: #FFF;height:40px;">
											<th rowspan="2" style="vertical-align: middle; width:3%;text-align:center;">No</th>
											<th rowspan="2" style="vertical-align: middle; width:20%;text-align:center;">Name of Article</th>
											<th rowspan="2" style="vertical-align: middle; width:5%;text-align:center;">Unit</th>
											<th rowspan="2" style="vertical-align: middle; width:5%;text-align:center;">Request</th>
											<th colspan="3" style="vertical-align: middle; width:10%;text-align:center;">
												<input type="checkbox" id="idChkVndr1" value="quot1"> <label id="lblVnd1" style="color:#FFF;">Vendor 1</label></th>
											<th colspan="3" style="vertical-align: middle; width:10%;text-align:center;">
												<input type="checkbox" id="idChkVndr2" value="quot2"> <label id="lblVnd2" style="color:#FFF;">Vendor 2</label></th>
											<th colspan="3" style="vertical-align: middle; width:10%;text-align:center;">
												<input type="checkbox" id="idChkVndr3" value="quot3"> <label id="lblVnd3" style="color:#FFF;">Vendor 3</label></th>
											<th colspan="3" style="vertical-align: middle; width:10%;text-align:center;">
												<input type="checkbox" id="idChkCustom" value="custom"> Custom</th>
										</tr>
										<tr style="background-color: #A70000;color: #FFF;height:40px;">
											<th style="vertical-align: middle; text-align:center;">Qty</th>
											<th style="vertical-align: middle; text-align:center;">Price</th>
											<th style="vertical-align: middle; text-align:center;">Total</th>
											<th style="vertical-align: middle; text-align:center;">Qty</th>
											<th style="vertical-align: middle; text-align:center;">Price</th>
											<th style="vertical-align: middle; text-align:center;">Total</th>
											<th style="vertical-align: middle; text-align:center;">Qty</th>
											<th style="vertical-align: middle; text-align:center;">Price</th>
											<th style="vertical-align: middle; text-align:center;">Total</th>
											<th style="vertical-align: middle; text-align:center;">Select Vendor</th>
										</tr>
									</thead>
									<tbody id="idTbodyDetailReq">
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-md-12" align="center" id="idBtnDetailRequest">
							<input type="hidden" name="txtIdReqModal" id="txtIdReqModal" value="">
							<input type="hidden" name="txtTypeApprove" id="txtTypeApprove" value="">
							<div class="col-md-4 col-xs-12">
							<button id="btnApproval" class="btn btn-primary btn-xs btn-block" name="btnApproval" title="Save">
								<i class="fa fa-check-square-o"></i> Approve</button>
							</div>
							<div class="col-md-4 col-xs-12">
							<button id="btnCancelModalDetail" onclick="reloadPage();" class="btn btn-danger btn-xs btn-block" title="Cancel">
								<i class="fa fa-ban"></i> Cancel</button>
							</div>
							<div class="col-md-4 col-xs-12">
							<button id="btnRevisiModal" class="btn btn-warning btn-xs btn-block" title="Revisi">
								<i class="fa fa-pencil-square-o"></i> Revisi</button>
							</div>
						</div>
					</div>
	        	</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalCheckReq" role="dialog">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	        	<div class="modal-header" style="padding: 10px;background-color:#A70000;">
	          		<button type="button" class="close" data-dismiss="modal" style="opacity:unset;text-shadow:none;color:#FFF;">&times;</button>
	          		<h4 class="modal-title">Approve Superintendent</h4>
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
	<div class="modal fade" id="modalApproveOffice" role="dialog">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	        	<div class="modal-header" style="padding: 10px;background-color:#A70000;">
	          		<button type="button" class="close" data-dismiss="modal" style="opacity:unset;text-shadow:none;color:#FFF;">&times;</button>
	          		<h4 class="modal-title">Approve Office</h4>
	        	</div>
	        	<div class="modal-body" id="idModalApproveOffice">
	        		<div class="row">
						<div class="col-md-3 col-xs-12">
							<div class="col-md-4 col-xs-4">
								<label style="font-size:12px;">DATE</label><b style="float:right;">:</b>
							</div>
							<div class="col-md-8 col-xs-8">
								<label id="txtDateModalApproveOffice" style="font-weight:bold;"></label>
							</div>
						</div>
						<div class="col-md-3 col-xs-12">
							<div class="col-md-4 col-xs-4">
								<label>APP NO</label><b style="float:right;">:</b>
							</div>
							<div class="col-md-8 col-xs-8">
								<label id="txtAppNoModalApproveOffice" style="font-weight:bold;"></label>
							</div>
						</div>
						<div class="col-md-3 col-xs-12">
							<div class="col-md-4 col-xs-4">
								<label>VESSEL</label><b style="float:right;">:</b>
							</div>
							<div class="col-md-8 col-xs-8">
								<label id="txtVesselModalApproveOffice" style="font-weight:bold;"></label>
							</div>
						</div>
						<div class="col-md-3 col-xs-12">
							<div class="col-md-4 col-xs-4">
								<label>DEPT</label><b style="float:right;">:</b>
							</div>
							<div class="col-md-8 col-xs-8">
								<label id="txtDeptModalApproveOffice" style="font-weight:bold;"></label>
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
											<th style="vertical-align: middle; width:8%;text-align:center;">Mark<br>Reference</th>
											<th style="vertical-align: middle; width:20%;text-align:center;">Remark</th>
										</tr>
									</thead>
									<tbody id="idTbodyApproveOffice">
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
							<input type="hidden" id="txtIdReqModalApproveOffice" value="">
							<button class="btn btn-primary btn-xs btn-block" onclick="submitApproveOffice();"><i class="fa fa-check-square-o"></i> Approve</button>
						</div>
						<div class="col-md-4 col-xs-12">
							<button class="btn btn-danger btn-xs btn-block" onclick="reloadPage();"><i class="fa fa-ban"></i> Cancel</button>
						</div>
						<div class="col-md-4 col-xs-12">
							<button class="btn btn-warning btn-xs btn-block" onclick="revisiApproveOffice();"><i class="fa fa-reply-all"></i> Revisi</button>
						</div>
					</div>
	        	</div>
			</div>
		</div>
	</div>
</body>
</html>

