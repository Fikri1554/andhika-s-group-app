<?php $this->load->view("purchasing/menu"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script src="<?php echo base_url();?>assets/js/bootstrap-select.js"></script>
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-select.css">
	<script type="text/javascript">
		$(document).ready(function(){
			$("[id^=txtDate_]").datepicker({
				dateFormat: 'yy-mm-dd',
		        showButtonPanel: true,
		        changeMonth: true,
		        changeYear: true,
		        defaultDate: new Date(),
		    });
			$('[id^=slcErp]').selectpicker();
			$("#btnSaveView").click(function(){
				$("html, body").animate({ scrollTop: 0 }, "fast");
				var idPurch = $("#txtIdPurchase").val();
				var disc = $("#txtDiscount").val();
				var deliveryCost = $("#txtDelivery").val();
				var ppn = $("#idLblPPN").val();
				var note = $("#txtNote").val();
				var optPpn = $('input:radio[name^=optPpn]:checked').val();

				var cfm = confirm("Submit data..??");
				if(cfm)
				{
					$("#idLoadingView").show();
					$.post('<?php echo base_url("purchasing/savePO"); ?>/',
					{ idPurch : idPurch,disc : disc,deliveryCost : deliveryCost,ppn : ppn,note : note,optPpn : optPpn },
						function(data) 
						{
							alert(data);
							window.location = "<?php echo base_url('purchasing/exportPurchasing');?>/"+idPurch;
						},
					"json"
					);
				}
			});
		});
		function sendDataToERP()
		{
			$("#idLoading").show();
			var txtIdReqSendErp = $("#txtIdReqSendErp").val();
			var txtIdPurchListSendErp = $("#txtIdPurchSendErp").val();
			var supplierName = $("#slcErp_Supplier option:selected").text();
			var supplierCode = $("#slcErp_Supplier").val();
			var txtVesselSendErp = $("#txtVesselSendErp").val();
			var txtVslCompanySendErp = $("#txtVslCompanySendErp").val();

			var idDetail = "";
		   	var valIdDetail = $("input[name^='txtIdReqDetail']").map(function(){return $(this).val();}).get();
			for (var l = 0; l < valIdDetail.length; l++)
			{
			    if(idDetail == ""){ idDetail = valIdDetail[l]; }else{ idDetail += "*"+valIdDetail[l]; }
			}

			var itemCode = "";
		   	var valItemCode = $("select[name^='slcErp_kodeBrg']").map(function(){return $(this).val();}).get();
			for (var l = 0; l < valItemCode.length; l++)
			{
			    if(itemCode == ""){ itemCode = valItemCode[l]; }else{ itemCode += "*"+valItemCode[l]; }
			}

			var satuan = "";
		   	var valSatuan = $("select[name^='slcErp_satuan']").map(function(){return $(this).val();}).get();
			for (var l = 0; l < valSatuan.length; l++)
			{
			    if(satuan == ""){ satuan = valSatuan[l]; }else{ satuan += "*"+valSatuan[l]; }
			}

			var stName = "";
		   	var valStName = $("select[name^='slcErp_satuan']").map(function(){return $(this).find('option:selected').text();}).get();
			for (var l = 0; l < valStName.length; l++)
			{
			    if(stName == ""){ stName = valStName[l]; }else{ stName += "*"+valStName[l]; }
			}
			
			$("#btnSendErp").attr('disabled',true);
			$.post('<?php echo base_url("purchasing/sendToERP"); ?>/',
			{ txtIdReqSendErp : txtIdReqSendErp,txtIdPurchSendErp : txtIdPurchListSendErp,supplierName : supplierName,supplierCode : supplierCode,idDetail : idDetail,itemCode : itemCode,satuan : satuan,stName : stName,txtVslCompanySendErp : txtVslCompanySendErp,txtVesselSendErp : txtVesselSendErp },
				function(data) 
				{
					alert(data.stData);
					reloadPage();
				},
			"json"
			);
		}
		function createPO(idPurc = "")
		{
			$("#idLoading").show();
			$.post('<?php echo base_url("purchasing/viewPurchasing"); ?>/',
			{ id : idPurc },
				function(data) 
				{
					$("#lblOrderTo").append(data.headNya[0].order_company+"<br><i style='font-size:10px;'>("+data.headNya[0].order_name+")</i>");
					$("#lblPoDate").text(data.poDate);
					$("#lblSubject").text(data.headNya[0].subject);
					$("#lblShipTo").append(data.headNya[0].ship_company+"<br><i style='font-size:10px;'>("+data.headNya[0].ship_name+")</i>");
					$("#lblPoNo").text(data.headNya[0].po_no);
					$("#idTbodyViewDetail").append(data.trNya);
					$("#txtIdPurchase").val(data.headNya[0].id);
					$("#idDataTable").hide();
					$("#idViewDetail").show();
				},
			"json"
			);
			$("#idLoading").hide();
		}
		function getDataSendErp(idPurcList)
		{
			$("#idLoading").show();
			var orderTo = '';
			var shipTo = '';
			var poDate = '';
			var poNo = '';
			var subjectNya = '';

			$.post('<?php echo base_url("purchasing/getDataToErp"); ?>/',
			{ id : idPurcList },
				function(data) 
				{
					orderTo = data.headNya[0]['order_company'];
					shipTo = data.headNya[0]['ship_company'];
					poNo = data.headNya[0]['po_no'];
					subjectNya = data.headNya[0]['subject'];

					if(data.headNya[0]['order_name'] != '')
					{
						orderTo += " <i style='color:red;'>("+data.headNya[0]['order_name']+")</i>";
					}

					if(data.headNya[0]['ship_name'] != '')
					{
						shipTo += " <i style='color:red;'>("+data.headNya[0]['ship_name']+")</i>";
						$("#txtVslCompanySendErp").val(data.headNya[0]['ship_company']);
						$("#txtVesselSendErp").val(data.headNya[0]['ship_name']);
					}

					$("#txtIdReqSendErp").val(data.idRequest);
					$("#txtIdPurchSendErp").val(idPurcList);

					$("#lblSendErpOrderTo").html(orderTo);
					$("#lblSendErpShipTo").html(shipTo);
					$("#lblSendErpPoDate").html(data.poDate);
					$("#lblSendErpPoNo").html(poNo);
					$("#lblSendErpSubject").html(subjectNya);

					$("#idTbodySendErp").empty();
					$("#idTbodySendErp").append(data.trNya);

					$("#slcErp_Supplier").empty();
					$("#slcErp_Supplier").append(data.optSupplier);
					$('#slcErp_Supplier').selectpicker('refresh');

					$("#txtIdTempDbNya").val(data.tempDBNya);
					
					$("#idLoading").hide();
					$("#idDataTable").hide();
					$("#idFormSendERP").show(50);
				},
			"json"
			);
		}
		function cancelDataToErp(idPurc = "")
		{
			var cfm = confirm("Cancel Data..??");
			if(cfm)
			{
				$("#idLoading").show();
				$.post('<?php echo base_url("purchasing/cancelDataToErp"); ?>/',
				{ idPurc : idPurc },
					function(data) 
					{
						alert(data);
						reloadPage();
					},
				"json"
				);
				$("#idLoading").hide();
			}
		}
		function slcSatuanByItemCode(itemCode,idReqDetail,dbErp = "")
		{
			$("#idLoading").show();
			var tempDBNya = $("#txtIdTempDbNya").val();

			$.post('<?php echo base_url("MasErpApi/getOptItemSatuanErp"); ?>',
			{ itemCode : itemCode,tempDBNya : tempDBNya,dbErp : dbErp },
				function(data) 
				{
					$('[id^=slcErp_kodeBrg]').val(itemCode);

					$('[id^=slcSatuanErp_]').empty();
					$('[id^=slcSatuanErp_]').append(data);

					$("#idLoading").hide();
				},
			"json"
			);			
		}
		function slcTypeDisc()
		{
			var slcDic = $("#slcTypeDisc").val();
			$("#txtDiscount").val("0");
			$("#txtInputDisc").val("");
			$("#idLblPPN").val("0");

			if(slcDic == "angka")
			{
				$("#txtInputDisc").hide();				
			}else{
				$("#txtInputDisc").show();
			}			
			grandTotalPurchasing();
		}
		function saveBargeCharge()
		{
			var formData = new FormData();
			var optPpnBC = $('input:radio[name^=optPpnBC]:checked').val();
			var itemName = $("#slcBCErpItemCodeBC option:selected").text();
			var itemCode = $("#slcBCErpItemCodeBC").val();
			var itemName2 = $("#slcBCErpItemCodeBC2 option:selected").text();
			var itemCode2 = $("#slcBCErpItemCodeBC2").val();
			var satuanName = $("#slcSatuanBC option:selected").text();
			var satuanCode = $("#slcSatuanBC").val();
			var satuanName2 = $("#slcSatuanBC2 option:selected").text();
			var satuanCode2 = $("#slcSatuanBC2").val();
			var currBC = $("#slcCurrBC").val();
			var currBC2 = $("#slcCurrBC2").val();
			var discountBC = $("#txtDiscBC").val();
			var txtPOBargeCharge = $("#txtPOBargeCharge").val();
			var poHeaderBC = $("#txtPoHeaderBC").val();

			if(poHeaderBC == txtPOBargeCharge)
			{
				alert("PO NO tidak boleh sama..!!");
				$("#txtPOBargeCharge").focus();
				return false;
			}

			formData.append('txtIdPurchaseBC',$('#txtIdPurchaseBC').val());
			formData.append('txtPOBargeCharge',txtPOBargeCharge);
			formData.append('txtDate_poBargeCharge',$('#txtDate_poBargeCharge').val());

			formData.append('itemCode',itemCode);
			formData.append('itemName',itemName);
			formData.append('txtDetailBC',$('#txtDetailBC').val());
			formData.append('txtQtyBC',$('#txtQtyBC').val());
			formData.append('satuanCode',satuanCode);
			formData.append('satuanName',satuanName);
			formData.append('currBC',currBC);
			formData.append('txtPriceBC',$('#txtPriceBC').val());			

			formData.append('itemCode2',itemCode2);
			formData.append('itemName2',itemName2);
			formData.append('txtDetailBC2',$('#txtDetailBC2').val());
			formData.append('txtQtyBC2',$('#txtQtyBC2').val());
			formData.append('satuanCode2',satuanCode2);
			formData.append('satuanName2',satuanName2);
			formData.append('currBC2',currBC2);
			formData.append('txtPriceBC2',$('#txtPriceBC2').val());

			formData.append('discountBC',discountBC);
			formData.append('optPpnBC',optPpnBC);
			formData.append('txtTotalPpnBC',$('#txtTotalPpnBCHidden').val());
			
			$("#idLoadingModalBC").show();

			$.ajax("<?php echo base_url('purchasing/saveBargeCharge'); ?>",{
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
		}
		function getModalBargeCharge(id)
		{
			$("#txtIdPurchaseBC").val(id);

			$.post('<?php echo base_url("purchasing/getOptionItemCode"); ?>/',
			{ txtIdPurc : id },
				function(data) 
				{
					$('#slcBCErpItemCodeBC').empty();
					$('#slcBCErpItemCodeBC').append('<option value="">- Select -</option>');
					$('#slcBCErpItemCodeBC').append(data.optItemCode);
					$('#slcBCErpItemCodeBC').attr('onchange',"slcSatuanBargeCharge($('#slcBCErpItemCodeBC').val(),'"+data.dbErpByCmp+"','');");

					$('#slcBCErpItemCodeBC2').empty();
					$('#slcBCErpItemCodeBC2').append('<option value="">- Select -</option>');
					$('#slcBCErpItemCodeBC2').append(data.optItemCode);
					$('#slcBCErpItemCodeBC2').attr('onchange',"slcSatuanBargeCharge($('#slcBCErpItemCodeBC2').val(),'"+data.dbErpByCmp+"','2');");

					$("#txtPoHeaderBC").val(data.poHeader);
					$('#modalBargeCharge').modal('show');
				},
			"json"
			);
		}
		function getDataSendErpBargeCharge(idPurcList)
		{
			$("#idLoading").show();
			var orderTo = '';
			var shipTo = '';
			var poDate = '';
			var poNo = '';
			var subjectNya = '';

			$.post('<?php echo base_url("purchasing/getDataToErpBargeCharge"); ?>/',
			{ id : idPurcList },
				function(data) 
				{
					orderTo = data.headNya[0]['order_company'];
					shipTo = data.headNya[0]['ship_company'];
					poNo = data.headNya[0]['po_no_bargecharge'];
					subjectNya = data.headNya[0]['subject'];

					if(data.headNya[0]['order_name'] != '')
					{
						orderTo += " <i style='color:red;'>("+data.headNya[0]['order_name']+")</i>";
					}

					if(data.headNya[0]['ship_name'] != '')
					{
						shipTo += " <i style='color:red;'>("+data.headNya[0]['ship_name']+")</i>";
						$("#txtVslCompanySendErp").val(data.headNya[0]['ship_company']);
						$("#txtVesselSendErp").val(data.headNya[0]['ship_name']);
					}

					$("#txtIdReqSendErp").val(data.idRequest);
					$("#txtIdPurchSendErp").val(idPurcList);

					$("#lblSendErpOrderTo").html(orderTo);
					$("#lblSendErpShipTo").html(shipTo);
					$("#lblSendErpPoDate").html(data.poDate);
					$("#lblSendErpPoNo").html(poNo);
					$("#lblSendErpSubject").html(subjectNya);

					$("#idTbodySendErp").empty();
					$("#idTbodySendErp").append(data.trNya);

					$("#slcErp_Supplier").empty();
					$("#slcErp_Supplier").append(data.optSupplier);
					$('#slcErp_Supplier').selectpicker('refresh');

					$("#txtIdTempDbNya").val(data.tempDBNya);

					$("#btnSendErp").attr('onclick','sendDataToERPBargeCharge();');
					
					$("#idLoading").hide();
					$("#idDataTable").hide();
					$("#idFormSendERP").show(50);
				},
			"json"
			);
		}
		function sendDataToERPBargeCharge()
		{
			$("#idLoading").show();
			var txtIdReqSendErp = $("#txtIdReqSendErp").val();
			var txtIdPurchListSendErp = $("#txtIdPurchSendErp").val();
			var supplierName = $("#slcErp_Supplier option:selected").text();
			var supplierCode = $("#slcErp_Supplier").val();
			var txtVesselSendErp = $("#txtVesselSendErp").val();
			var txtVslCompanySendErp = $("#txtVslCompanySendErp").val();
			var itemCode = $("#slcErp_kodeBrg_BC_"+txtIdPurchListSendErp).val();
			var satuan = $("#slcSatuanErp_BC_"+txtIdPurchListSendErp).val();
			
			$("#btnSendErp").attr('disabled',true);
			$.post('<?php echo base_url("purchasing/sendToERPBargeCharge"); ?>/',
			{ txtIdReqSendErp : txtIdReqSendErp,txtIdPurchSendErp : txtIdPurchListSendErp,supplierName : supplierName,supplierCode : supplierCode,txtVslCompanySendErp : txtVslCompanySendErp,txtVesselSendErp : txtVesselSendErp,itemCode : itemCode,satuan : satuan },
				function(data) 
				{
					alert(data.stData);
					reloadPage();
				},
			"json"
			);
		}
		function slcSatuanBargeCharge(itemCode,dbErp = "",no = "")
		{
			$("#idLoading").show();
			var tempDBNya = $("#txtIdTempDbNya").val();

			$.post('<?php echo base_url("MasErpApi/getOptItemSatuanErp"); ?>',
			{ itemCode : itemCode,tempDBNya : tempDBNya,dbErp : dbErp },
				function(data) 
				{
					$('#slcBCErpItemCodeBC'+no).val(itemCode);

					$('#slcSatuanBC'+no).empty();
					$('#slcSatuanBC'+no).append(data);

					$("#idLoading").hide();
				},
			"json"
			);			
		}
		function hitungDisc()
		{
			var subTotal = $("#txtSubTotal").val();
			var input = $("#txtInputDisc").val();
			var hitung = 0;

			hitung = (parseFloat(subTotal) * parseFloat(input))/100;
			$("#txtDiscount").val(hitung);
			hitungPPN();
		}
		function grandTotalPurchasing(inpType = '')
		{
			var subTotal = $("#txtSubTotal").val();
			var disc = $("#txtDiscount").val();
			var delCost = $("#txtDelivery").val();
			var aftrDisc = 0;
			var grandTotal = 0;
			var ppn = $("#idLblPPN").val();
			
			if(disc == "")
			{
				disc = 0;
			}

			if(delCost == "")
			{
				delCost = 0;
			}

			if(ppn == "")
			{
				ppn = 0;
			}

			aftrDisc = parseFloat(subTotal) - parseFloat(disc);
			$("#idTtlAfterDisc").text(aftrDisc.toLocaleString());
			grandTotal = parseFloat(aftrDisc) + parseFloat(ppn) + parseFloat(delCost);

			$("#idGrandTotal").text(grandTotal.toLocaleString());
		}
		function hitungPPN()
		{
			var subTotal = $("#txtSubTotal").val();
			var disc = $("#txtDiscount").val();
			var aftrDisc = 0;
			var optPpn = $('input:radio[name^=optPpn]:checked').val();
			var ppn = $("#idLblPPN").val();
			var grandTotal = 0;
			var delCost = $("#txtDelivery").val();

			if(disc == "")
			{
				disc = 0;
			}

			if(delCost == "")
			{
				delCost = 0;
			}

			if(optPpn == "tidak")
			{
				$("#idLblPPN").val('0');
				grandTotalPurchasing();
			}
			else if(optPpn == "tidakdipungutpajak")
			{
				$("#idLblPPN").val('0');
				grandTotalPurchasing();
			}
			else if(optPpn == "inklusif")
			{
				aftrDisc = parseFloat(subTotal) - parseFloat(disc);

				var ttlInk = (parseFloat(aftrDisc) / 111) * 100;				
				ttlInk = parseFloat(aftrDisc) - parseFloat(ttlInk);

				var ttlAftr = aftrDisc - ttlInk;

				ttlInk = ttlInk.toFixed(2);

				$("#idTtlAfterDisc").text(ttlAftr.toLocaleString(undefined, { minimumFractionDigits: 2,maximumFractionDigits: 2 }));
				$("#idLblPPN").val(ttlInk);

				grandTotal = parseFloat(aftrDisc) + parseFloat(delCost);
				$("#idGrandTotal").text(grandTotal.toLocaleString());
			}
			else if(optPpn == "eksklusif")
			{
				aftrDisc = parseFloat(subTotal) - parseFloat(disc);
				ppn = (parseFloat(aftrDisc) * 11)/100;
				$("#idLblPPN").val(ppn);

				grandTotalPurchasing();
			}
		}
		function hitBargeCharge()
		{
			var qty = $("#txtQtyBC").val();
			var price = $("#txtPriceBC").val();
			var qty2 = $("#txtQtyBC2").val();
			var price2 = $("#txtPriceBC2").val();
			var amount = 0;
			var amount2 = 0;
			var subTotal = 0;
			var grandTotal = 0;
			var ttlPpn = 0;
			var optPpn = $('input:radio[name^=optPpn]:checked').val();
			var afterDisc = 0;
			var disc = $("#txtDiscBC").val();

			if(qty == ""){ qty = 0; }
			if(qty2 == ""){ qty2 = 0; }
			if(price == ""){ price = 0; }
			if(price2 == ""){ price2 = 0; }
			if(disc == ""){ disc = 0; }

			amount = parseFloat(qty) * parseFloat(price);
			amount2 = parseFloat(qty2) * parseFloat(price2);

			$("#txtAmountBC").val(amount.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));
			$("#txtAmountBC2").val(amount2.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));

			subTotal = parseFloat(amount) + parseFloat(amount2);
			afterDisc = parseFloat(subTotal) - parseFloat(disc);

			$("#txtSubTotalBC").val(subTotal.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));
			$("#txtAfterDiscBC").val(afterDisc.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));

			if(optPpn == "tidak")
			{
				$("#txtTotalPpnBC").val('0');
				$("#txtTotalPpnBCHidden").val('0');
			}
			else if(optPpn == "tidakdipungutpajak")
			{
				$("#txtTotalPpnBC").val('0');
				$("#txtTotalPpnBCHidden").val('0');
			}
			else if(optPpn == "inklusif")
			{
				var amountWithPpn = (parseFloat(afterDisc) * 11) / 100;
				$("#txtTotalPpnBC").val(amountWithPpn.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));
				$("#txtTotalPpnBCHidden").val(amountWithPpn);
			}
			else if(optPpn == "eksklusif")
			{
				var amountWithPpn2 = (parseFloat(afterDisc) * 11) / 100;

				$("#txtTotalPpnBC").val(amountWithPpn2.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));
				$("#txtTotalPpnBCHidden").val(amountWithPpn2);

				ttlPpn = amountWithPpn2;
			}

			if(optPpn == "inklusif")
			{
				grandTotal = afterDisc.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2});
			}else{
				grandTotal = parseFloat(afterDisc) + parseFloat(ttlPpn);
			}

			$("#txtGrandTotalBC").val(grandTotal.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2}));
		}
		function reloadPage()
		{
			window.location = "";
		}
		function reloadToMenu()
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
						<div class="col-md-1">
							<button id="btnBack" style="float:left;<?php echo $cssDisplay; ?>" class="btn btn-success btn-xs btn-block" name="btnBack" title="Back" onclick="reloadToMenu();">
								<i class="fa fa-reply-all"></i> Back
							</button>
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
											<th style="vertical-align: middle; width:15%;text-align:center;">PO No</th>
											<th style="vertical-align: middle; width:15%;text-align:center;">Date PO</th>
											<th style="vertical-align: middle; width:20%;text-align:center;">Vendor</th>
											<th style="vertical-align: middle; width:20%;text-align:center;">Ship</th>
											<th style="vertical-align: middle; width:5%;text-align:center;">Action</th>
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
				<div class="form-panel" id="idViewDetail" style="display:none;">
					<legend style="text-align: right;">
						<label id="lblForm">
							<span style="display:none;padding-right:10px;" id="idLoadingView"><img src="<?php echo base_url('assets/img/loading.gif'); ?>" >
							</span>Purchase Order
						</label>
					</legend>
					<div class="row">
						<div class="col-md-2 col-xs-12" align="right">
							<label style="float:left;">ORDER TO </label><label>:</label>
						</div>
						<div class="col-md-4 col-xs-12">
							<label id="lblOrderTo"></label>
						</div>
						<div class="col-md-2 col-xs-12" align="right">
							<label style="float:left;">SHIP TO </label><label>:</label>
						</div>
						<div class="col-md-4 col-xs-12">
							<label id="lblShipTo"></label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 col-xs-12" align="right">
							<label style="float:left;">PO DATE </label><label>:</label>
						</div>
						<div class="col-md-4 col-xs-12">
							<label id="lblPoDate"></label>
						</div>
						<div class="col-md-2 col-xs-12" align="right">
							<label style="float:left;">PO NO </label><label>:</label>
						</div>
						<div class="col-md-4 col-xs-12">
							<label id="lblPoNo"></label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 col-xs-12" align="right">
							<label style="float:left;">SUBJECT</label><label>:</label>
						</div>
						<div class="col-md-4 col-xs-12">
							<label id="lblSubject"></label>
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
											<th style="vertical-align: middle; width:8%;text-align:center;">Request</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Approved</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">QTY</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Unit Price</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Amount</th>
										</tr>
									</thead>
									<tbody id="idTbodyViewDetail">
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 col-xs-12">
							<div class="form-group" align="center">
								<input type="hidden" name="txtIdPurchase" id="txtIdPurchase" value="">
								<button id="btnSaveView" class="btn btn-primary btn-sm" name="btnSave" title="Save">
									<i class="fa fa-check-square-o"></i> Save
								</button>
								<button id="btnCancelView" onclick="reloadPage();" class="btn btn-danger btn-sm" name="btnCancel" title="Cancel">
									<i class="fa fa-ban"></i> Cancel
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="form-panel" id="idFormSendERP" style="display:none;">
					<legend style="text-align: right;">
						<button id="btnBack" onclick="reloadPage();" style="float: left;" class="btn btn-success btn-xs" name="btnBack" title="Back">
							<i class="fa fa-reply-all"></i> Back
						</button>
						<label id="lblForm">
							<i>:: Send To ERP ::</i>
						</label>
					</legend>
					<div class="row">
						<div class="col-md-1 col-xs-12" align="right">
							<label style="float:left;">ORDER TO </label><label>:</label>
						</div>
						<div class="col-md-5 col-xs-12" style="padding:0px;" id="lblSendErpOrderTo">
						</div>
						<div class="col-md-1 col-xs-12" align="right">
							<label style="float:left;">SHIP TO </label><label>:</label>
						</div>
						<div class="col-md-5 col-xs-12" style="padding:0px;" id="lblSendErpShipTo">
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 col-xs-12" align="right">
							<label style="float:left;">PO DATE </label><label>:</label>
						</div>
						<div class="col-md-5 col-xs-12" style="padding:0px;" id="lblSendErpPoDate">
						</div>
						<div class="col-md-1 col-xs-12" align="right">
							<label style="float:left;">PO NO </label><label>:</label>
						</div>
						<div class="col-md-5 col-xs-12" style="padding:0px;" id="lblSendErpPoNo">
						</div>
					</div>
					<div class="row">
						<div class="col-md-1 col-xs-12" align="right">
							<label style="float:left;">SUBJECT</label><label>:</label>
						</div>
						<div class="col-md-5 col-xs-12" style="padding:0px;" id="lblSendErpSubject">
						</div>
						<div class="col-md-1 col-xs-12" align="right">
							<label style="float:left;">SUPPLIER</label><label>:</label>
						</div>
						<div class="col-md-4 col-xs-12" style="padding:0px;">
							<select class="form-control" id="slcErp_Supplier" data-live-search="true">
							</select>
						</div>
					</div>
					<div class="row mt" id="idData1">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
									<thead>
										<tr style="background-color: #A70000;color: #FFF;height:40px;">
											<th style="vertical-align: middle; width:5%;text-align:center;">No</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Kode Barang</th>
											<th style="vertical-align: middle; width:38%;text-align:center;">Description</th>
											<th style="vertical-align: middle; width:7%;text-align:center;">QTY</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Satuan</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Unit Price</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Amount</th>
										</tr>
									</thead>
									<tbody id="idTbodySendErp"></tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-xs-12" align="center">
							<input type="hidden" id="txtIdReqSendErp" value="">
							<input type="hidden" id="txtIdPurchSendErp" value="">
							<input type="hidden" id="txtIdTempDbNya" value="">
							<input type="hidden" id="txtVslCompanySendErp" value="">
							<input type="hidden" id="txtVesselSendErp" value="">
							<button id="btnSendErp" class="btn btn-primary btn-xs btn-block" title="Send To ERP" onclick="sendDataToERP();">
								<i class="glyphicon glyphicon-cloud-upload"></i> Send
							</button>
						</div>
						<div class="col-md-6 col-xs-12" align="center">
							<button id="btnCancelSendErp" class="btn btn-danger btn-xs btn-block" title="Cancel" onclick="reloadPage();">
								<i class="fa fa-ban"></i> Cancel
							</button>
						</div>
					</div>
				</div>
			</section>
		</section>
	</section>
	<div class="modal fade" id="modalBargeCharge" role="dialog">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	        	<div class="modal-header" style="padding: 10px;background-color:#A70000;">
	          		<button type="button" class="close" data-dismiss="modal" style="opacity:unset;text-shadow:none;color:#FFF;">&times;</button>
	          		<h4 class="modal-title"><i>:: Data Form ::</i></h4>
	        	</div>
	        	<div class="modal-body" id="idModalDetail">
	        		<div class="row">
						<div class="col-md-12">
							<legend style="text-align: right;">
								<img id="idLoadingModalBC" src="<?php echo base_url('assets/img/loading.gif'); ?>" style="display:none;">
								<label><i>:: Create Barge Charge ::</i></label>
							</legend>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2 col-xs-12">
							<label for="txtPOBargeCharge">PO NO (Barge Charge) :</label>
							<input type="text" class="form-control input-sm" id="txtPOBargeCharge" value="" placeholder="Po No">
						</div>
						<div class="col-md-2 col-xs-12">
							<label for="txtDate_poBargeCharge">Date :</label>
							<input type="text" class="form-control input-sm" id="txtDate_poBargeCharge" value="" placeholder="Date">
						</div>
					</div>
					<div class="row">
						<div class="col-md-3 col-xs-12">
							<label for="slcBCErpItemCodeBC">Item Code :</label>
							<select class="form-control" id="slcBCErpItemCodeBC">								
							</select>
						</div>
						<div class="col-md-2 col-xs-12">
							<label for="txtDetailBC">Detail Item :</label>
							<input type="text" class="form-control input-sm" id="txtDetailBC" value="Barge Charge">
						</div>
						<div class="col-md-1 col-xs-12">
							<label for="txtQtyBC">QTY :</label>
							<input type="text" class="form-control input-sm" id="txtQtyBC" value="" placeholder="0" oninput="hitBargeCharge();">
						</div>
						<div class="col-md-1 col-xs-12">
							<label for="slcSatuanBC">Satuan :</label>
							<select class="form-control" id="slcSatuanBC">
							</select>
						</div>
						<div class="col-md-1 col-xs-12">
							<label>&nbsp</label>
							<select name="slcCurrBC[]" id="slcCurrBC" class="form-control input-sm">
								<option value="idr">IDR (Rp)</option>
								<option value="usd">USD ($)</option>
								<option value="sgd">SGD (S$)</option>
							</select>
						</div>
						<div class="col-md-2 col-xs-12">
							<label for="txtPriceBC">Unit Price :</label>
							<input type="text" class="form-control input-sm" id="txtPriceBC" value="0" oninput="hitBargeCharge();" style="text-align:right;">
						</div>
						<div class="col-md-2 col-xs-12">
							<label for="txtAmountBC">Amount :</label>
							<input type="text" class="form-control input-sm" id="txtAmountBC" value="0" style="text-align:right;" disabled>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3 col-xs-12">
							<label for="slcBCErpItemCodeBC2">Item Code :</label>
							<select class="form-control" id="slcBCErpItemCodeBC2">								
							</select>
						</div>
						<div class="col-md-2 col-xs-12">
							<label for="txtDetailBC2">Detail Item :</label>
							<input type="text" class="form-control input-sm" id="txtDetailBC2" value="">
						</div>
						<div class="col-md-1 col-xs-12">
							<label for="txtQtyBC2">QTY :</label>
							<input type="text" class="form-control input-sm" id="txtQtyBC2" value="" placeholder="0" oninput="hitBargeCharge();">
						</div>
						<div class="col-md-1 col-xs-12">
							<label for="slcSatuanBC2">Satuan :</label>
							<select class="form-control" id="slcSatuanBC2">
							</select>
						</div>
						<div class="col-md-1 col-xs-12">
							<label>&nbsp</label>
							<select name="slcCurrBC2[]" id="slcCurrBC2" class="form-control input-sm">
								<option value="idr">IDR (Rp)</option>
								<option value="usd">USD ($)</option>
								<option value="sgd">SGD (S$)</option>
							</select>
						</div>
						<div class="col-md-2 col-xs-12">
							<label for="txtPriceBC2">Unit Price :</label>
							<input type="text" class="form-control input-sm" id="txtPriceBC2" value="0" oninput="hitBargeCharge();" style="text-align:right;">
						</div>
						<div class="col-md-2 col-xs-12">
							<label for="txtAmountBC2">Amount :</label>
							<input type="text" class="form-control input-sm" id="txtAmountBC2" value="0" style="text-align:right;" disabled>
						</div>
					</div>
					<div class="row" style="margin-top:10px;">
						<div class="col-md-9 col-xs-12" style="text-align:right;">
							<label>Sub Total :</label>
						</div>
						<div class="col-md-3 col-xs-12" style="text-align:right;">
							<input type="text" class="form-control input-sm" id="txtSubTotalBC" value="" style="text-align:right;" disabled>
						</div>
					</div>
					<div class="row" style="margin-top:10px;">
						<div class="col-md-9 col-xs-12" style="text-align:right;">
							<label>Discount :</label>
						</div>
						<div class="col-md-3 col-xs-12" style="text-align:right;">
							<input type="text" class="form-control input-sm" oninput="hitBargeCharge();" id="txtDiscBC" value="" style="text-align:right;">
						</div>
					</div>
					<div class="row" style="margin-top:10px;">
						<div class="col-md-9 col-xs-12" style="text-align:right;">
							<label>After Discount :</label>
						</div>
						<div class="col-md-3 col-xs-12" style="text-align:right;">
							<input type="text" class="form-control input-sm" id="txtAfterDiscBC" value="" style="text-align:right;" disabled>
						</div>
					</div>
					<div class="row" style="margin-top:10px;">
						<div class="col-md-9 col-xs-12" style="text-align:right;">
							<label class="radio-inline">
								<input type="radio" name="optPpnBC[]" onchange="hitBargeCharge();" value="tidak" checked>Tidak Ada PPN
							</label>
							<label class="radio-inline">
								<input type="radio" name="optPpnBC[]" onchange="hitBargeCharge();" value="tidakdipungutpajak">PPN Tidak di pungut Pajak
							</label>
							<label class="radio-inline">
								<input type="radio" name="optPpnBC[]" onchange="hitBargeCharge();" value="inklusif">PPN Inklusif
							</label>
							<label class="radio-inline">
								<input type="radio" name="optPpnBC[]" onchange="hitBargeCharge();" value="eksklusif">PPN Eksklusif <i style="font-size:11px;color:red;">(+11%)</i>
							</label>
						</div>
						<div class="col-md-3 col-xs-12">
							<input type="text" class="form-control input-sm" id="txtTotalPpnBC" value="0" style="text-align:right;">
							<input type="hidden" id="txtTotalPpnBCHidden" value="">
						</div>
					</div>
					<div class="row" style="margin-top:10px;">
						<div class="col-md-9 col-xs-12" style="text-align:right;">
							<label>Grand Total :</label>
						</div>
						<div class="col-md-3 col-xs-12" style="text-align:right;">
							<input type="text" class="form-control input-sm" id="txtGrandTotalBC" value="" style="text-align:right;" disabled>
						</div>
					</div>
					<div class="row" style="margin-top:10px;">
						<div class="col-md-6 col-xs-12">
							<input type="hidden" id="txtIdPurchaseBC" value="">
							<input type="hidden" id="txtPoHeaderBC" value="">
							<button class="btn btn-primary btn-block btn-xs" title="Submit" id="btnAddField" onclick="saveBargeCharge();" type="button"><i class="glyphicon glyphicon-check"></i> Submit</button>
						</div>
						<div class="col-md-6 col-xs-12">
							<button class="btn btn-danger btn-block btn-xs" title="Submit" id="btnAddField" onclick="reloadPage();" type="button"><i class="fa fa-ban"></i> Cancel</button>
						</div>
					</div>
	        	</div>
			</div>
		</div>
	</div>
</body>
</html>

