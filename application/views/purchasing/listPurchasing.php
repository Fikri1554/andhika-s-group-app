<?php $this->load->view("purchasing/menu"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript">
		$(document).ready(function(){
			$( "input[id^='txtDate']" ).datepicker({
				dateFormat: 'yy-mm-dd',
		        showButtonPanel: true,
		        changeMonth: true,
		        changeYear: true,
		        defaultDate: new Date(),
		    });
			$("#btnSaveDetail").click(function(){
				$("html, body").animate({ scrollTop: 0 }, "fast");
				var formData = new FormData();
				var idDetail1 = "";
				var idDetail2 = "";
				var idDetail3 = "";
				var qty1 = "";
				var qty2 = "";
				var qty3 = "";
				var curr1 = "";
				var curr2 = "";
				var curr3 = "";
				var price1 = "";
				var price2 = "";
				var price3 = "";
				var amount1 = "";
				var amount2 = "";
				var amount3 = "";
				for (var lan = 1; lan <= 3; lan++)
				{
		    		var valIdDetail = $("input[name^='txtIdReqDetail"+lan+"']").map(function(){return $(this).val();}).get();
		    		var valQty = $("input[name^='txtTtlApprove"+lan+"']").map(function(){return $(this).val();}).get();
		    		var valCurr = $("select[name^='slcCurrency"+lan+"']").map(function(){return $(this).val();}).get();
		    		var valPrice = $("input[name^='txtPrice"+lan+"']").map(function(){return $(this).val();}).get();
		    		var valAmount = $("input[name^='txtAmount"+lan+"']").map(function(){return $(this).val();}).get();
		    		for (var l = 0; l < valIdDetail.length; l++)
					{
						if(lan == 1)
						{
							if(idDetail1 == ""){ idDetail1 = valIdDetail[l]; }else{ idDetail1 += "*"+valIdDetail[l]; }
							if(qty1 == ""){ qty1 = valQty[l]; }else{ qty1 += "*"+valQty[l]; }
							if(curr1 == ""){ curr1 = valCurr[l]; }else{ curr1 += "*"+valCurr[l]; }
							if(price1 == ""){ price1 = valPrice[l]; }else{ price1 += "*"+valPrice[l]; }
							if(amount1 == ""){ amount1 = valAmount[l]; }else{ amount1 += "*"+valAmount[l]; }
						}
						else if(lan == 2)
						{
							if(idDetail2 == ""){ idDetail2 = valIdDetail[l]; }else{ idDetail2 += "*"+valIdDetail[l]; }
							if(qty2 == ""){ qty2 = valQty[l]; }else{ qty2 += "*"+valQty[l]; }
							if(curr2 == ""){ curr2 = valCurr[l]; }else{ curr2 += "*"+valCurr[l]; }
							if(price2 == ""){ price2 = valPrice[l]; }else{ price2 += "*"+valPrice[l]; }
							if(amount2 == ""){ amount2 = valAmount[l]; }else{ amount2 += "*"+valAmount[l]; }
						}
						else if(lan == 3)
						{
							if(idDetail3 == ""){ idDetail3 = valIdDetail[l]; }else{ idDetail3 += "*"+valIdDetail[l]; }
							if(qty3 == ""){ qty3 = valQty[l]; }else{ qty3 += "*"+valQty[l]; }
							if(curr3 == ""){ curr3 = valCurr[l]; }else{ curr3 += "*"+valCurr[l]; }
							if(price3 == ""){ price3 = valPrice[l]; }else{ price3 += "*"+valPrice[l]; }
							if(amount3 == ""){ amount3 = valAmount[l]; }else{ amount3 += "*"+valAmount[l]; }
						}					   	
					}
				}
				
				var reqDate = "";
		    	var valReqDate = $("input[id^='txtReqDate']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valReqDate.length; l++)
			    {
			    	if(valReqDate[l] == "")
			    	{
			    		valReqDate[l] = "-";
			    	}
			    	if(reqDate == ""){ reqDate = valReqDate[l]; }else{ reqDate += "*"+valReqDate[l]; }
			    }

			    var poNo = "";
		    	var valPoNo = $("input[id^='txtPoNo']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valPoNo.length; l++)
			    {
			    	if(valPoNo[l] == "")
			    	{
			    		valPoNo[l] = "-";
			    	}
			    	if(poNo == ""){ poNo = valPoNo[l]; }else{ poNo += "*"+valPoNo[l]; }
			    }

			    var poNoOri = "";
		    	var valPoNoOri = $("input[id^='txtIntPoNoOri']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valPoNoOri.length; l++)
			    {
			    	if(valPoNoOri[l] == "")
			    	{
			    		valPoNoOri[l] = "-";
			    	}
			    	if(poNoOri == ""){ poNoOri = valPoNoOri[l]; }else{ poNoOri += "*"+valPoNoOri[l]; }
			    }

			    var datePo = "";
		    	var valDatePo = $("input[id^='txtDatePO']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valDatePo.length; l++)
			    {
			    	if(valDatePo[l] == "")
			    	{
			    		valDatePo[l] = "-";
			    	}
			    	if(datePo == ""){ datePo = valDatePo[l]; }else{ datePo += "*"+valDatePo[l]; }
			    }

			    var subject = "";
		    	var valSubject = $("input[id^='txtSubject']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valSubject.length; l++)
			    {
			    	if(valSubject[l] == "")
			    	{
			    		valSubject[l] = "-";
			    	}
			    	if(subject == ""){ subject = valSubject[l]; }else{ subject += "*"+valSubject[l]; }
			    }

			    var picVendor = "";
		    	var valPicVendor = $("input[id^='txtOrderName']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valPicVendor.length; l++)
			    {
			    	if(valPicVendor[l] == "")
			    	{
			    		valPicVendor[l] = "-";
			    	}
			    	if(picVendor == ""){ picVendor = valPicVendor[l]; }else{ picVendor += "*"+valPicVendor[l]; }
			    }

			    var vendorCom = "";
		    	var valVenCom = $("input[id^='txtOrderCompany']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valVenCom.length; l++)
			    {
			    	if(valVenCom[l] == "")
			    	{
			    		valVenCom[l] = "-";
			    	}
			    	if(vendorCom == ""){ vendorCom = valVenCom[l]; }else{ vendorCom += "*"+valVenCom[l]; }
			    }

			    var vendorCode = "";
		    	var valVenCode = $("input[id^='txtVendorCode']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valVenCode.length; l++)
			    {
			    	if(valVenCode[l] == "")
			    	{
			    		valVenCode[l] = "-";
			    	}
			    	if(vendorCode == ""){ vendorCode = valVenCode[l]; }else{ vendorCode += "*"+valVenCode[l]; }
			    }

			    var ship = "";
		    	var valShip = $("input[id^='txtShipName']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valShip.length; l++)
			    {
			    	if(valShip[l] == "")
			    	{
			    		valShip[l] = "-";
			    	}
			    	if(ship == ""){ ship = valShip[l]; }else{ ship += "*"+valShip[l]; }
			    }

			    var shipCom = "";
		    	var valShipCom = $("select[id^='slcCompany']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valShipCom.length; l++)
			    {
			    	if(valShipCom[l] == "")
			    	{
			    		valShipCom[l] = "-";
			    	}
			    	if(shipCom == ""){ shipCom = valShipCom[l]; }else{ shipCom += "*"+valShipCom[l]; }
			    }

			    var idPurch = "";
		    	var valPurch = $("input[id^='txtIdPurchase']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valPurch.length; l++)
			    {
			    	if(valPurch[l] == "")
			    	{
			    		valPurch[l] = "-";
			    	}
			    	if(idPurch == ""){ idPurch = valPurch[l]; }else{ idPurch += "*"+valPurch[l]; }
			    }

				formData.append('idReq',$("#txtIdReq").val());
				formData.append('reqDate',reqDate);
				formData.append('poNo',poNo);
				formData.append('poNoOri',poNoOri);
				formData.append('datePo',datePo);
				formData.append('subject',subject);
				formData.append('picVendor',picVendor);
				formData.append('vendorCom',vendorCom);
				formData.append('vendorCode',vendorCode);
				formData.append('ship',ship);
				formData.append('shipCom',shipCom);
				formData.append('idPurch',idPurch);
				formData.append('idDetail1',idDetail1);
				formData.append('idDetail2',idDetail2);
				formData.append('idDetail3',idDetail3);
				formData.append('qty1',qty1);
				formData.append('curr1',curr1);
				formData.append('price1',price1);
				formData.append('amount1',amount1);
				formData.append('qty2',qty2);
				formData.append('curr2',curr2);
				formData.append('price2',price2);
				formData.append('amount2',amount2);
				formData.append('qty3',qty3);
				formData.append('curr3',curr3);
				formData.append('price3',price3);
				formData.append('amount3',amount3);


				$("#idLoading").show();
				$(this).attr("disabled","disabled");

				$.ajax("<?php echo base_url('purchasing/addPurchasing'); ?>",{
                    method: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        alert(response);
                        reloadPage();
                    }
                });
			});			
			$("#btnSearch").click(function(){
				var slcSearch = $("#slcSearch").val();
				var valSearch = $("#txtSearch").val();

				if(slcSearch == "vessel" && valSearch == "")
				{
					alert("Search Empty..!!");
					return false;
				}
				
				$("#idLoading").show();
				$.post('<?php echo base_url("purchasing/getListPurchasing"); ?>/search',
				{ valSearch : valSearch,slcSearch : slcSearch },
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
		function checkPurchasing(id = "",typeCheck = "")
		{
			$("#idLoading").show();
			$.post('<?php echo base_url("purchasing/editData"); ?>',
			{ id : id, typeEdit : "checkPurchasing",typeCheck : typeCheck },
				function(data) 
				{
					$("#idDataTable").hide();
					$("#txtIdReq").val(data.idReq);

					$("#txtPoNo1").val(data['poNo']['poNo1']);
					$("#txtIntPoNoOri1").val(data['poNo']['poNo1']);
					$("#txtPoNo2").val(data['poNo']['poNo2']);
					$("#txtIntPoNoOri2").val(data['poNo']['poNo2']);
					$("#txtPoNo3").val(data['poNo']['poNo3']);
					$("#txtIntPoNoOri3").val(data['poNo']['poNo3']);

					if(typeCheck == "custom")
					{
						$("#idLblReqDate1").val(data.lblReqDate);
						$("#txtReqDate1").val(data.reqDate);
						$("#txtShipName1").val(data.headNya[0].vessel);
						$("#txtShipCompany1").val(data.headNya[0].vessel_company);
						//$("#txtPoNo1").val(data.headNya[0].po_no);
						$("#txtOrderName1").val(data.pic_vendor1);
						$("#txtOrderCompany1").val(data.vendor_company1);
						$("#txtVendorCode1").val(data.vendor_code1);
						$("#linkQuot1").append(data.linkFile1);

						$("#idLblReqDate2").val(data.lblReqDate);
						$("#txtReqDate2").val(data.reqDate);
						$("#txtShipName2").val(data.headNya[0].vessel);
						$("#txtShipCompany2").val(data.headNya[0].vessel_company);
						//$("#txtPoNo2").val(data.headNya[0].po_no);
						$("#txtOrderName2").val(data.pic_vendor2);
						$("#txtOrderCompany2").val(data.vendor_company2);
						$("#txtVendorCode2").val(data.vendor_code2);
						$("#linkQuot2").append(data.linkFile2);

						$("#idLblReqDate3").val(data.lblReqDate);
						$("#txtReqDate3").val(data.reqDate);
						$("#txtShipName3").val(data.headNya[0].vessel);
						$("#txtShipCompany3").val(data.headNya[0].vessel_company);
						//$("#txtPoNo3").val(data.headNya[0].po_no);
						$("#txtOrderName3").val(data.pic_vendor3);
						$("#txtOrderCompany3").val(data.vendor_company3);
						$("#txtVendorCode3").val(data.vendor_code3);
						$("#linkQuot3").append(data.linkFile3);

						$("#idTbodyDetail1").empty();
						$("#idTbodyDetail1").append(data.trNya1);
						$("#idTbodyDetail2").empty();
						$("#idTbodyDetail2").append(data.trNya2);
						$("#idTbodyDetail3").empty();
						$("#idTbodyDetail3").append(data.trNya3);
					}else{
						$("#txtShipName1").val(data.headNya[0].vessel);
						//$("#txtPoNo1").val(data.headNya[0].po_no);
						$("#txtOrderName1").val(data.pic_vendor1);
						$("#txtOrderCompany1").val(data.vendor_company1);
						$("#txtVendorCode1").val(data.vendor_code1);
						$("#idLblReqDate1").val(data.lblReqDate);
						$("#txtReqDate1").val(data.reqDate);
						$("#idTbodyDetail1").empty();
						$("#idTbodyDetail1").append(data.trNya1);
						$("#linkQuot1").append(data.linkFile);
					}
					
					$("#idLoading").hide();
					$("#idFormDetail").show(200);
				},
			"json"
			);
		}
		function editPurchasing(id = "",typeCheck = "")
		{
			$("#idLoading").show();
			$.post('<?php echo base_url("purchasing/editData"); ?>',
			{ id : id, typeEdit : "editPurchasing",typeCheck : typeCheck },
				function(data) 
				{
					$("#idDataTable").hide();
					$("#txtIdReq").val(data.idReq);
					var noUrut = 1;
					$.each(data.dataPurc, function(i, item)
					{
						$("#idLblReqDate"+noUrut).val(data.reqDateLbl);
						$("#txtShipName"+noUrut).val(item.ship_name);
						$("#txtPoNo"+noUrut).val(item.po_no);
						$("#txtIntPoNoOri"+noUrut).val(item.po_no_int);
						$("#txtOrderName"+noUrut).val(item.order_name);
						$("#txtOrderCompany"+noUrut).val(item.order_company);							
						$("#txtReqDate"+noUrut).val(item.date_request);
						$("#txtDatePO"+noUrut).val(item.po_date);
						$("#txtSubject"+noUrut).val(item.subject);
						$("#slcCompany"+noUrut).val(item.ship_company);
						$("#txtIdPurchase"+noUrut).val(item.id);

						$("#idTbodyDetail"+noUrut).empty();
						if(noUrut == 1)
						{
							$("#idTbodyDetail"+noUrut).append(data.trNya1);
							$("#linkQuot"+noUrut).append(data.linkFile1);
						}
						else if(noUrut == 2)
						{
							$("#idTbodyDetail"+noUrut).append(data.trNya2);
							$("#linkQuot"+noUrut).append(data.linkFile2);
						}
						else if(noUrut == 3)
						{
							$("#idTbodyDetail"+noUrut).append(data.trNya3);
							$("#linkQuot"+noUrut).append(data.linkFile3);
						}						

						noUrut ++;
					});
					$("#idLoading").hide();
					$("#idFormDetail").show(200);
				},
			"json"
			);
		}
		function cekSubmitPurchasing(idReq = "")
		{
			var cfm = confirm("Submit data..??");
			if(cfm)
			{
				$("#idLoading").show();
				$.post('<?php echo base_url("purchasing/submitPurchasing"); ?>/',
				{ idReq : idReq },
					function(data) 
					{
						alert(data);
						window.location = "<?php echo base_url('purchasing/viewListPurchase');?>/"+idReq;
					},
				"json"
				);
			}			
		}
		function sumAmount(id,noUrut = "")
		{
			var amount = 0;
			var grandTotal = 0;
			var qty = $("#txtTtlApprove_"+id).val();
			var price = $("#txtPrice_"+id).val();
			amount = parseFloat(qty) * parseFloat(price);			

			$("#txtAmount"+noUrut+"_"+id).val(amount);
			var ttlAmount = $("input[id^='txtAmount"+noUrut+"']").map(function(){return $(this).val();}).get();
			for (var lan = 0; lan < ttlAmount.length; lan++)
			{
				grandTotal = parseFloat(grandTotal) + parseFloat(ttlAmount[lan]);
			}

			cekTotalApprove(id);
			$("#idTtlAmount"+noUrut).text(grandTotal.toLocaleString());
		}
		function cekTotalApprove(id)
		{
			var qty = $("#txtTtlApprove_"+id).val();
			var lblApprove = $("#idLblReq_"+id).text();

			if(parseFloat(qty) > parseFloat(lblApprove))
			{
				$("#txtTtlApprove_"+id).val(lblApprove);
			}
		}
		function createNoPO(idNo = "")
		{
			var dateNya = $("#txtDatePO"+idNo).val();
			var vsl = $("#txtShipName"+idNo).val();
			var company = $("#slcCompany"+idNo).val();
			var poNoOri = $("#txtIntPoNoOri"+idNo).val();

			$("#idLoading").show();

			$.post('<?php echo base_url("purchasing/createNoPO"); ?>',
			{ dateNya : dateNya,vsl : vsl,company : company,poNo : poNoOri },
				function(data) 
				{
					$("#txtPoNo"+idNo).val(data['formatNya']);
					$("#idLoading").hide();
				},
			"json"
			);
		}
		function reloadPage()
		{
			window.location = "<?php echo base_url('purchasing/getListPurchasing');?>";
		}
	</script>
</head>
<body>
	<section id="container">
		<section id="main-content">
			<section class="wrapper site-min-height" style="min-height:400px;">
				<h3>
					<i class="fa fa-angle-right"></i> Purchasing<span style="display:none;padding-left:20px;" id="idLoading"><img src="<?php echo base_url('assets/img/loading.gif'); ?>" ></span>
				</h3>
				<div class="form-panel" id="idDataTable">
					<div class="row" id="btnNavAtas">						
						<div class="col-md-2">
							<select class="form-control input-sm" id="slcSearch">
								<option value="all">All</option>
								<option value="complete">Complete</option>
								<option value="draft">Draft</option>
								<option value="vessel">Vessel</option>
							</select>
						</div>
						<div class="col-md-2">
							<input type="text" class="form-control input-sm" id="txtSearch" value="" placeholder="Search" autocomplete="off">
						</div>
						<div class="col-md-2">
							<button type="button" id="btnSearch" class="btn btn-warning btn-sm btn-block" title="Search"><i class="fa fa-search"></i> Search</button>
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
											<th style="vertical-align: middle; width:15%;text-align:center;">Req. Date</th>
											<th style="vertical-align: middle; width:15%;text-align:center;">App. No</th>
											<th style="vertical-align: middle; width:15%;text-align:center;">Vessel</th>
											<th style="vertical-align: middle; width:20%;text-align:center;">Vendor</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Status</th>
											<th style="vertical-align: middle; width:20%;text-align:center;">Action</th>
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
				<div class="form-panel" id="idFormDetail" style="display:none;">
					<legend style="text-align: right;"><label id="lblForm">Data Purchasing</label></legend>
					<div class = "panel-group" id = "idPurchase">
						<div class = "panel panel-danger">
							<div class = "panel-heading">
						         <h4 class = "panel-title">
						            <a data-toggle = "collapse" data-parent = "#idPurchase" href = "#idPurcs1">
						   			Purchasing 1
						        	</a>
						    	</h4>
							</div>
							<div id = "idPurcs1" class = "panel-collapse collapse in">
						         <div class = "panel-body">
									<div class="row">
										<div class="col-md-3 col-xs-12">
											<label for="idLblReqDate1">Request Date :</label>
											<input type="hidden" id="txtReqDate1" value="">
											<input type="hidden" id="txtIdPurchase1" value="">
											<input type="text" id="idLblReqDate1" class="form-control input-sm" disabled="disabled">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtPoNo1">PO No :</label>
											<input type="text" id="txtPoNo1" placeholder="No" class="form-control input-sm">
											<input type="hidden" id="txtIntPoNoOri1" value="">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtDatePO1">Date PO :</label>
											<input type="text" id="txtDatePO1" class="form-control input-sm" value="" placeholder="Date" onchange="createNoPO('1');" autocomplete="off">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtSubject1">Subject :</label>
											<input type="text" id="txtSubject1" class="form-control input-sm" value="" placeholder="Subject">
										</div>
									</div>
									<div class="row" style="padding: 10px 0px 10px 0px;">
										<div class="col-md-3 col-xs-12">
											<label for="txtOrderName1">PIC Order Name :</label>
											<input type="text" id="txtOrderName1" placeholder="Name" class="form-control input-sm">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtOrderCompany1">Vendor Company :</label>
											<input type="text" id="txtOrderCompany1" placeholder="Company" class="form-control input-sm">
											<input type="hidden" id="txtVendorCode1" value="">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtShipName1">Ship Name :</label>
											<input type="text" id="txtShipName1" placeholder="Name" class="form-control input-sm" disabled="disabled">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="slcCompany1">Ship Company :</label>
											<select name="slcCompany1" id="slcCompany1" class="form-control input-sm" onchange="createNoPO('1');">
												<option value="">- Select -</option>
												<?php echo $optCompany; ?>
											</select>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3 col-xs-12">
											<label id="linkQuot1" style="float:right;"></label>
										</div>
									</div>
									<div class="row mt" id="idData1">
										<div class="col-md-12">
											<div class="table-responsive">
												<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
													<thead>
														<tr style="background-color: #A70000;color: #FFF;height:40px;">
															<th style="vertical-align: middle; width:5%;text-align:center;">No</th>
															<th style="vertical-align: middle; width:29%;text-align:center;">Name of Article</th>
															<th style="vertical-align: middle; width:8%;text-align:center;">Unit</th>
															<th style="vertical-align: middle; width:8%;text-align:center;">Request</th>
															<th style="vertical-align: middle; width:8%;text-align:center;">Purchased Qty</th>
															<th style="vertical-align: middle; width:7%;text-align:center;">Currency</th>
															<th style="vertical-align: middle; width:10%;text-align:center;">Price</th>
															<th style="vertical-align: middle; width:10%;text-align:center;">Amount</th>
														</tr>
													</thead>
													<tbody id="idTbodyDetail1">
													</tbody>
												</table>
											</div>
										</div>
									</div>
						         </div>
						  	</div>
						</div>
						<div class = "panel panel-danger">
							<div class = "panel-heading">
						         <h4 class = "panel-title">
						            <a data-toggle = "collapse" data-parent = "#idPurchase" href = "#idPurcs2">
						   			Purchasing 2
						        	</a>
						    	</h4>
							</div>
							<div id = "idPurcs2" class = "panel-collapse collapse">
						         <div class = "panel-body">
									<div class="row">
										<div class="col-md-3 col-xs-12">
											<label for="idLblReqDate2">Request Date :</label>
											<input type="hidden" id="txtReqDate2" value="">
											<input type="hidden" id="txtIdPurchase2" value="">
											<input type="text" id="idLblReqDate2" class="form-control input-sm" disabled="disabled">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtPoNo2">PO No :</label>
											<input type="text" id="txtPoNo2" placeholder="No" class="form-control input-sm">
											<input type="hidden" id="txtIntPoNoOri2" value="">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtDatePO2">Date PO :</label>
											<input type="text" id="txtDatePO2" class="form-control input-sm" value="" placeholder="Date" onchange="createNoPO('2');">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtSubject2">Subject :</label>
											<input type="text" id="txtSubject2" class="form-control input-sm" value="" placeholder="Subject">
										</div>
									</div>
									<div class="row" style="padding: 10px 0px 10px 0px;">
										<div class="col-md-3 col-xs-12">
											<label for="txtOrderName2">PIC Order Name :</label>
											<input type="text" id="txtOrderName2" placeholder="Name" class="form-control input-sm">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtOrderCompany">Vendor Company :</label>
											<input type="text" id="txtOrderCompany2" placeholder="Company" class="form-control input-sm">
											<input type="hidden" id="txtVendorCode2" value="">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtShipName2">Ship Name :</label>
											<input type="text" id="txtShipName2" placeholder="Name" class="form-control input-sm" disabled="disabled">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtShipCompany">Ship Company :</label>
											<select name="slcCompany" id="slcCompany2" class="form-control input-sm" onchange="createNoPO('2');">
												<option value="">- Select -</option>
												<?php echo $optCompany; ?>
											</select>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3 col-xs-12">
											<label id="linkQuot2" style="float:right;"></label>
										</div>
									</div>
									<div class="row mt" id="idData2">
										<div class="col-md-12">
											<div class="table-responsive">
												<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
													<thead>
														<tr style="background-color: #A70000;color: #FFF;height:40px;">
															<th style="vertical-align: middle; width:5%;text-align:center;">No</th>
															<th style="vertical-align: middle; width:29%;text-align:center;">Name of Article</th>
															<th style="vertical-align: middle; width:8%;text-align:center;">Unit</th>
															<th style="vertical-align: middle; width:8%;text-align:center;">Request</th>
															<th style="vertical-align: middle; width:8%;text-align:center;">Quantity</th>
															<th style="vertical-align: middle; width:7%;text-align:center;"></th>
															<th style="vertical-align: middle; width:10%;text-align:center;">Price</th>
															<th style="vertical-align: middle; width:10%;text-align:center;">Amount</th>
														</tr>
													</thead>
													<tbody id="idTbodyDetail2">
													</tbody>
												</table>
											</div>
										</div>
									</div>
						         </div>
						  	</div>
						</div>
						<div class = "panel panel-danger">
							<div class = "panel-heading">
						         <h4 class = "panel-title">
						            <a data-toggle = "collapse" data-parent = "#idPurchase" href = "#idPurcs3">
						   			Purchasing 3
						        	</a>
						    	</h4>
							</div>
							<div id = "idPurcs3" class = "panel-collapse collapse">
						    	<div class = "panel-body">
									<div class="row">
										<div class="col-md-3 col-xs-12">
											<label for="idLblReqDate3">Request Date :</label>
											<input type="hidden" id="txtReqDate3" value="">
											<input type="hidden" id="txtIdPurchase3" value="">
											<input type="text" id="idLblReqDate3" class="form-control input-sm" disabled="disabled">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtPoNo3">PO No :</label>
											<input type="text" id="txtPoNo3" placeholder="No" class="form-control input-sm">
											<input type="hidden" id="txtIntPoNoOri3" value="">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtDatePO3">Date PO :</label>
											<input type="text" id="txtDatePO3" class="form-control input-sm" value="" placeholder="Date" onchange="createNoPO('3');">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtSubject3">Subject :</label>
											<input type="text" id="txtSubject3" class="form-control input-sm" value="" placeholder="Subject">
										</div>
									</div>
									<div class="row" style="padding: 10px 0px 10px 0px;">
										<div class="col-md-3 col-xs-12">
											<label for="txtOrderName3">PIC Order Name :</label>
											<input type="text" id="txtOrderName3" placeholder="Name" class="form-control input-sm">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtOrderCompany3">Vendor Company :</label>
											<input type="text" id="txtOrderCompany3" placeholder="Company" class="form-control input-sm">
											<input type="hidden" id="txtVendorCode3" value="">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtShipName3">Ship Name :</label>
											<input type="text" id="txtShipName3" placeholder="Name" class="form-control input-sm" disabled="disabled">
										</div>
										<div class="col-md-3 col-xs-12">
											<label for="txtShipCompany3">Ship Company :</label>
											<select name="slcCompany" id="slcCompany3" class="form-control input-sm" onchange="createNoPO('3');">
												<option value="">- Select -</option>
												<?php echo $optCompany; ?>
											</select>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3 col-xs-12">
											<label id="linkQuot3" style="float:right;"></label>
										</div>
									</div>
									<div class="row mt" id="idData3">
										<div class="col-md-12">
											<div class="table-responsive">
												<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
													<thead>
														<tr style="background-color: #A70000;color: #FFF;height:40px;">
															<th style="vertical-align: middle; width:5%;text-align:center;">No</th>
															<th style="vertical-align: middle; width:29%;text-align:center;">Name of Article</th>
															<th style="vertical-align: middle; width:8%;text-align:center;">Unit</th>
															<th style="vertical-align: middle; width:8%;text-align:center;">Request</th>
															<th style="vertical-align: middle; width:8%;text-align:center;">Quantity</th>
															<th style="vertical-align: middle; width:7%;text-align:center;"></th>
															<th style="vertical-align: middle; width:10%;text-align:center;">Price</th>
															<th style="vertical-align: middle; width:10%;text-align:center;">Amount</th>
														</tr>
													</thead>
													<tbody id="idTbodyDetail3">
													</tbody>
												</table>
											</div>
										</div>
									</div>
						         </div>
						  	</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-md-12 col-xs-12">
								<div class="form-group" align="center">
									<input type="hidden" name="txtIdReq" id="txtIdReq" value="">
									<button id="btnSaveDetail" class="btn btn-primary btn-sm" name="btnSave" title="Save">
										<i class="fa fa-check-square-o"></i> Save
									</button>
									<button id="btnCancelDetail" onclick="reloadPage();" class="btn btn-danger btn-sm" name="btnCancel" title="Cancel">
										<i class="fa fa-ban"></i> Cancel
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</section>
	</section>
</body>
</html>

