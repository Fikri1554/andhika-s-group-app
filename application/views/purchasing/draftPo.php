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
				var slcVenOther = "";
				var slcVenOtherQty = "";

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
					    slcVenOther = "";
					    slcVenOtherQty = "";

				    	var valSlcVen = $("select[name^='slcVendor']").map(function(){return $(this).val();}).get();
					    for (var l = 0; l < valSlcVen.length; l++)
					    {
					    	if(slcVen == ""){ slcVen = valSlcVen[l]; }else{ slcVen += "*"+valSlcVen[l]; }

					    	if(valSlcVen[l] == "other")
					    	{
					    		var vndChk1 = "-";
					    		var vndChk2 = "-";
					    		var vndChk3 = "-";
					    		var vndChkQty1 = "0";
					    		var vndChkQty2 = "0";
					    		var vndChkQty3 = "0";

					    		if($("#idChkVndrCstm1_"+valIdDet[l]).is(":checked"))
					    		{
					    			vndChk1 = $("#idChkVndrCstm1_"+valIdDet[l]).val();
					    			vndChkQty1 = $("#idTxtVndrCstm1_"+valIdDet[l]).val();
					    		}

					    		if($("#idChkVndrCstm2_"+valIdDet[l]).is(":checked"))
					    		{
					    			vndChk2 = $("#idChkVndrCstm2_"+valIdDet[l]).val();
					    			vndChkQty2 = $("#idTxtVndrCstm2_"+valIdDet[l]).val();
					    		}

					    		if($("#idChkVndrCstm3_"+valIdDet[l]).is(":checked"))
					    		{
					    			vndChk3 = $("#idChkVndrCstm3_"+valIdDet[l]).val();
					    			vndChkQty3 = $("#idTxtVndrCstm3_"+valIdDet[l]).val();
					    		}

					    		if(slcVenOther == "")
					    		{
					    			slcVenOther = valIdDet[l]+"#"+vndChk1+"#"+vndChk2+"#"+vndChk3;
					    		}else{
					    			slcVenOther += "*"+valIdDet[l]+"#"+vndChk1+"#"+vndChk2+"#"+vndChk3;
					    		}

					    		if(slcVenOtherQty == "")
					    		{
					    			slcVenOtherQty = valIdDet[l]+"#"+vndChkQty1+"#"+vndChkQty2+"#"+vndChkQty3;
					    		}else{
					    			slcVenOtherQty += "*"+valIdDet[l]+"#"+vndChkQty1+"#"+vndChkQty2+"#"+vndChkQty3;
					    		}
					    	}
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
					{ idReq : idReq,typeApprove : typeApprove,idDet : idDet,slcVen : slcVen,remark : remark,typeCheck : typeCheck,slcVenOther : slcVenOther,slcVenOtherQty : slcVenOtherQty },
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
				var typeApprove = $("#txtTypeApprove").val();

				$("#idTableModal").empty();
				$("#idBtnDetailRequest").empty();
				$("#lblModal").text("Revisi Data");
				$("#idLblHeaderModal").text("Revisi");
				
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
							divNya += '<button id="btnSubmitRevisi" onclick="submitRevisi('+idReq+','+"'"+typeApprove+"'"+');" class="btn btn-primary btn-sm" title="Revisi"><i class="fa fa-check-square-o"></i> Submit</button>';
							divNya += " <button id='btnCancelModalRevisi' onclick='reloadPage();' class='btn btn-danger btn-sm' title='Cancel'><i class='fa fa-ban'></i> Cancel</button>";
						divNya += "</div>";
					divNya += "</div>";
				$("#idTableModal").append(divNya);
			});
			$("#btnPendingModal").click(function(){
				var idReq = $("#txtIdReqModal").val();
				var typeApprove = $("#txtTypeApprove").val();

				$("#idTableModal").empty();
				$("#idBtnDetailRequest").empty();
				$("#lblModal").text("Pending Data");
				$("#idLblHeaderModal").text("Pending");
				
				var divNya = "";
					divNya += "<div class='row'>";
						divNya += "<div class='col-md-12'>";
							divNya += "<div class='col-md-2'>";
								divNya += "<label>Remark :</label>";
							divNya += "</div>";
							divNya += "<div class='col-md-10'>";
								divNya += "<textarea class='form-control input-sm' id='txtPendingModal'></textarea>";
							divNya += "</div>";
						divNya += "</div>";
						divNya += "<div class='col-md-12' align='center' style='padding:10px;'>";
							divNya += '<button id="btnSubmitPending" onclick="submitPending('+idReq+','+"'"+typeApprove+"'"+');" class="btn btn-primary btn-sm" title="Pending"><i class="fa fa-check-square-o"></i> Submit</button>';
							divNya += " <button id='btnCancelModalRevisi' onclick='reloadPage();' class='btn btn-danger btn-sm' title='Cancel'><i class='fa fa-ban'></i> Cancel</button>";
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
				$.post('<?php echo base_url("approve/getApproveDraftPo"); ?>/search/',
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
				$("#btnPendingModal").css('display','none');
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
					$("#lblVndName1").empty();
					$("#lblVndName2").empty();
					$("#lblVndName3").empty();
					$("#lblVnd1").append(data.fileVendor1);
					$("#lblVnd2").append(data.fileVendor2);
					$("#lblVnd3").append(data.fileVendor3);
					$("#lblVndName1").append(data.lblVndName1);
					$("#lblVndName2").append(data.lblVndName2);
					$("#lblVndName3").append(data.lblVndName3);

					if(typeApprove == "kadiv purch" || typeApprove == "" || typeApprove == "kadiv shipMgmt" || typeApprove == "coo" || typeApprove == "finance")
					{
						if(data.headReq[0].type_check1 == "quot1")
						{
							$("#idChkVndr1").attr("checked",true);
							$('input:checkbox').prop('disabled', true);
						}
						else if(data.headReq[0].type_check1 == "quot2")
						{
							$("#idChkVndr2").attr("checked",true);
							$('input:checkbox').prop('disabled', true);
						}
						else if(data.headReq[0].type_check1 == "quot3")
						{
							$("#idChkVndr3").attr("checked",true);
							$('input:checkbox').prop('disabled', true);
						}else{
							$("#idChkCustom").attr("checked",true);
							$('input:checkbox').prop('disabled', true);
						}
						if(data.headReq[0].remark_check1 != "")
						{
							$("#idNoteModalDetail").empty();
							$("#idNoteModalDetail").append(data.remarkModal);
							// $("#txtRemark").val(data.headReq[0].remark_check1);
							$("#txtRemark").attr("disabled",false);
						}						
					}

					$("#idThQuot1").css("background-color",data.bgTheadColor1);
					$("#idThQuot2").css("background-color",data.bgTheadColor2);
					$("#idThQuot3").css("background-color",data.bgTheadColor3);
					$("#idThQuot4").css("background-color",data.bgTheadColor4);

					$("#trQtyQuot1").css("background-color",data.bgTheadColor1);
					$("#trPriceQuot1").css("background-color",data.bgTheadColor1);
					$("#trTtlQuot1").css("background-color",data.bgTheadColor1);
					$("#trQtyQuot2").css("background-color",data.bgTheadColor2);
					$("#trPriceQuot2").css("background-color",data.bgTheadColor2);
					$("#trTtlQuot2").css("background-color",data.bgTheadColor2);
					$("#trQtyQuot3").css("background-color",data.bgTheadColor3);
					$("#trPriceQuot3").css("background-color",data.bgTheadColor3);
					$("#trTtlQuot3").css("background-color",data.bgTheadColor3);
					$("#trCustom").css("background-color",data.bgTheadColor4);

					if(typeApprove == "finance")
					{
						$("#divIdBtnPending").css("display","");
					}

					$("#idLoading").hide();
					$('#modalReqDetail').modal('show');
				},
			"json"
			);
		}
		function submitRevisi(idReq,typeApprove)
		{
			var idReqNya = idReq;
			var remark = $("#txtRevisiModal").val();
			if(remark == "")
			{
				alert("Remark Empty..!!");
				return false;
			}
			$.post('<?php echo base_url("approve/addRevisiModal"); ?>',
			{ idReq : idReqNya,remark : remark,typeApprove : typeApprove },
				function(data)
				{
					alert(data);
					reloadPage();
				},
			"json"
			);
		}
		function submitPending(idReq,typeApprove)
		{
			var idReqNya = idReq;
			var remark = $("#txtPendingModal").val();
			if(remark == "")
			{
				alert("Remark Empty..!!");
				return false;
			}
			$.post('<?php echo base_url("approve/addPendingModal"); ?>',
			{ idReq : idReqNya,remark : remark,typeApprove : typeApprove },
				function(data)
				{
					alert(data);
					reloadPage();
				},
			"json"
			);
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
		function slcCustomVendor(id)
		{
			$("#idDivCustom_"+id).empty();
			var valNya = $("#slcVendor_"+id).val();

			if(valNya == "other")
			{				
				var divCst = "";

				divCst += '<div class="row">';
					divCst += '<div class="col-md-6">';
						divCst += '<input type="checkbox" id="idChkVndrCstm1_'+id+'" value="quot1">&nbspVendor&nbsp1';
					divCst += '</div>';
					divCst += '<div class="col-md-6">';
						divCst += '<input type="text" id="idTxtVndrCstm1_'+id+'" value="" class="form-control input-sm" placeholder="Qty 1">';
					divCst += '</div>';
				divCst += '</div>';
				divCst += '<div class="row">';
					divCst += '<div class="col-md-6">';
						divCst += '<input type="checkbox" id="idChkVndrCstm2_'+id+'" value="quot2">&nbspVendor&nbsp2';
					divCst += '</div>';
					divCst += '<div class="col-md-6">';
						divCst += '<input type="text" id="idTxtVndrCstm2_'+id+'" value="" class="form-control input-sm" placeholder="Qty 2">';
					divCst += '</div>';
				divCst += '</div>';
				divCst += '<div class="row">';
					divCst += '<div class="col-md-6">';
						divCst += '<input type="checkbox" id="idChkVndrCstm3_'+id+'" value="quot3">&nbspVendor&nbsp3';
					divCst += '</div>';
					divCst += '<div class="col-md-6">';
						divCst += '<input type="text" id="idTxtVndrCstm3_'+id+'" value="" class="form-control input-sm" placeholder="Qty 3">';
					divCst += '</div>';
				divCst += '</div>';
				$("#idDivCustom_"+id).append(divCst);
			}
		}
		function reloadPage()
		{
			window.location = "<?php echo base_url('approve/getApproveDraftPo');?>";
		}
	</script>
</head>
<body>
	<section id="container">
		<section id="main-content">
			<section class="wrapper site-min-height" style="min-height:400px;">
				<h3>
					<i class="fa fa-angle-right"></i> DRAFT PO<span style="display:none;padding-left:20px;" id="idLoading"><img src="<?php echo base_url('assets/img/loading.gif'); ?>" ></span>
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
										<tr style="background-color: #A70000;color: #FFF;height:20px;">
											<th style="vertical-align: middle; width:5%;text-align:center;" rowspan="2">No</th>
											<th style="vertical-align: middle; width:10%;text-align:center;" rowspan="2">Req. Date</th>
											<th style="vertical-align: middle; width:15%;text-align:center;" rowspan="2">App. No</th>
											<th style="vertical-align: middle; width:15%;text-align:center;" rowspan="2">Vessel<br><i>( Department )</i></th>											
											<th style="vertical-align:middle;text-align:center;" colspan="2">Approve</th>
											<th style="vertical-align:middle;text-align:center;" colspan="3">Check</th>
											<th style="vertical-align: middle; width:15%;text-align:center;" rowspan="2">Status</th>
										</tr>
										<tr style="background-color: #A70000;color: #FFF;height:20px;">
											<th style="vertical-align:middle;text-align:center;">Kadept Purch</th>
											<th style="vertical-align:middle;text-align:center;">Kadiv Purch</th>
											<th style="vertical-align:middle;text-align:center;">Kadiv ShipMgmt</th>
											<th style="vertical-align:middle;text-align:center;">COO</th>
											<th style="vertical-align:middle;text-align:center;">Finance</th>
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
	          		<h4 class="modal-title" id="idLblHeaderModal">Data Approve</h4>
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
											<th colspan="3" id="idThQuot1" style="vertical-align: middle; width:10%;text-align:center;">
												<input type="checkbox" id="idChkVndr1" value="quot1"> 
												<label id="lblVnd1" style="color:#FFF;">Vendor 1</label>
												<p style="margin: 0px;" id="lblVndName1"></p>
											</th>
											<th colspan="3" id="idThQuot2" style="vertical-align: middle; width:10%;text-align:center;">
												<input type="checkbox" id="idChkVndr2" value="quot2">
												<label id="lblVnd2" style="color:#FFF;">Vendor 2</label>
												<p style="margin: 0px;" id="lblVndName2"></p>
											</th>
											<th colspan="3" id="idThQuot3" style="vertical-align: middle; width:10%;text-align:center;">
												<input type="checkbox" id="idChkVndr3" value="quot3">
												<label id="lblVnd3" style="color:#FFF;">Vendor 3</label>
												<p style="margin: 0px;" id="lblVndName3"></p>
											</th>
											<th colspan="3" id="idThQuot4" style="vertical-align: middle; width:10%;text-align:center;">
												<input type="checkbox" id="idChkCustom" value="custom"> Custom</th>
										</tr>
										<tr style="background-color: #A70000;color: #FFF;height:40px;">
											<th id="trQtyQuot1" style="vertical-align: middle; text-align:center;">Qty</th>
											<th id="trPriceQuot1" style="vertical-align: middle; text-align:center;">Price</th>
											<th id="trTtlQuot1" style="vertical-align: middle; text-align:center;">Total</th>
											<th id="trQtyQuot2" style="vertical-align: middle; text-align:center;">Qty</th>
											<th id="trPriceQuot2" style="vertical-align: middle; text-align:center;">Price</th>
											<th id="trTtlQuot2" style="vertical-align: middle; text-align:center;">Total</th>
											<th id="trQtyQuot3" style="vertical-align: middle; text-align:center;">Qty</th>
											<th id="trPriceQuot3" style="vertical-align: middle; text-align:center;">Price</th>
											<th id="trTtlQuot3" style="vertical-align: middle; text-align:center;">Total</th>
											<th id="trCustom" style="vertical-align: middle; text-align:center;">Select Vendor</th>
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
							<div class="col-md-3 col-xs-12">
								<button id="btnApproval" class="btn btn-primary btn-xs btn-block" name="btnApproval" title="Save">
								<i class="fa fa-check-square-o"></i> Submit</button>
							</div>
							<div class="col-md-3 col-xs-12">
								<button id="btnCancelModalDetail" onclick="reloadPage();" class="btn btn-danger btn-xs btn-block" title="Cancel">
								<i class="fa fa-ban"></i> Cancel</button>
							</div>
							<div class="col-md-3 col-xs-12">
								<button id="btnRevisiModal" class="btn btn-info btn-xs btn-block" title="Revisi">
								<i class="fa fa-pencil-square-o"></i> Revisi</button>
							</div>
							<div class="col-md-3 col-xs-12" style="display:none;" id="divIdBtnPending">
								<button id="btnPendingModal" class="btn btn-warning btn-xs btn-block" title="Pending">
								<i class="fa fa-pause"></i> Pending</button>
							</div>
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

