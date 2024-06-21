<?php $this->load->view("purchasing/menu"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<script type="text/javascript">
		$(document).ready(function(){
			$("input[id^='txtDate']").datepicker({
				dateFormat: 'yy-mm-dd',
		        showButtonPanel: true,
		        changeMonth: true,
		        changeYear: true,
		        defaultDate: new Date(),
		    });
		    $("#btnSave").click(function(){
		    	var id = $("#txtIdEdit").val();
		    	var vessel = $("#slcVessel").val();
		    	var dateReq = $("#txtDateReq").val();
		    	var appNo = $("#txtAppNo").val();
		    	var dept = $("#txtDept").val();

		    	$("#idLoading").show();
		    	$.post('<?php echo base_url("requestOffice/addRequest"); ?>',
				{ id : id,vessel : vessel,dateReq : dateReq,appNo : appNo,dept : dept },
					function(data) 
					{							
						alert(data);
						reloadPage();
					},
				"json"
				);
		    });
		    $("#btnSaveDetail").click(function(){
		    	var idReq = $("#txtIdReq").val()
		    	var idEdit = "";
		    	var valIdEdit = $("input[name^='txtIdEdit']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valIdEdit.length; l++)
			    {
			    	if(idEdit == ""){ idEdit = valIdEdit[l]; }else{ idEdit += "*"+valIdEdit[l]; }
			    }
			    var codeNo = "";
			    var valCodeNo = $("input[name^='txtCodePartNo']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valCodeNo.length; l++)
			    {
			    	if(codeNo == ""){ codeNo = valCodeNo[l]; }else{ codeNo += "*"+valCodeNo[l]; }
			    }
		    	var nameArtikel = "";
			    var valArtikel = $("input[name^='txtNameArticle']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valArtikel.length; l++)
			    {
			    	if(nameArtikel == ""){ nameArtikel = valArtikel[l]; }else{ nameArtikel += "*"+valArtikel[l]; }
			    }
			    var unit = "";
			    var valUnit = $("input[name^='txtUnit']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valUnit.length; l++)
			    {
			    	if(unit == ""){ unit = valUnit[l]; }else{ unit += "*"+valUnit[l]; }
			    }
			    var working = "";
			    var valWork = $("input[name^='txtWorkOnBoard']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valWork.length; l++)
			    {
			    	if(working == ""){ working = valWork[l]; }else{ working += "*"+valWork[l]; }
			    }
			    var stock = "";
			    var valStock = $("input[name^='txtStockOnBoard']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valStock.length; l++)
			    {
			    	if(stock == ""){ stock = valStock[l]; }else{ stock += "*"+valStock[l]; }
			    }
			    var reqNya = "";
			    var valReqNya = $("input[name^='txtTotalReq']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valReqNya.length; l++)
			    {
			    	if(reqNya == ""){ reqNya = valReqNya[l]; }else{ reqNya += "*"+valReqNya[l]; }
			    }
			    var mark = "";
		    	var valMark = $("select[name^='slcMarkDetail']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valMark.length; l++)
			    {
			    	if(mark == ""){ mark = valMark[l]; }else{ mark += "*"+valMark[l]; }
			    }
			    var remark = "";
		    	var valRemark = $("textarea[name^='txtRemark']").map(function(){return $(this).val();}).get();
			    for (var l = 0; l < valRemark.length; l++)
			    {
			    	if(valRemark[l] == "")
			    	{
			    		valRemark[l] = "-";
			    	}
			    	if(remark == ""){ remark = valRemark[l]; }else{ remark += "*"+valRemark[l]; }
			    }
			    
			    if(codeNo == "")
			    {
			    	alert("Code / Part No Empty..!!");
			    	return false;
			    }
			    if(nameArtikel == "")
			    {
			    	alert("Name of Article Empty..!!");
			    	return false;
			    }
			    if(unit == "")
			    {
			    	alert("Unit Empty..!!");
			    	return false;
			    }
			    
			    $("#idLoading").show();
			    $.post('<?php echo base_url("requestOffice/addRequestDetail"); ?>',
				{ id : idEdit,idReq : idReq,codeNo : codeNo,nameArtikel : nameArtikel,unit : unit,working : working,stock : stock,reqNya : reqNya,mark : mark,remark : remark },
					function(data) 
					{
						alert(data);
						reloadPage();
					},
				"json"
				);
		    });
		    $("#btnAddData").click(function(){
		    	$("#idDataTable").hide();
				$("#idForm").show(200);
		    });
		    $("#btnApproval").click(function(){
		    	var idReq = $("#txtIdReqModal").val();
		    	var typeApprove = $("#txtTypeApprove").val();
		    	var cfm = confirm("Approve Data..??");
		    	if(cfm)
		    	{
			    	$.post('<?php echo base_url("requestOffice/approveModalReq"); ?>',
					{ id : idReq,typeApprove : typeApprove },
						function(data) 
						{
							alert(data);
							reloadPage();
						},
					"json"
					);
			    }
		    });
		    $("#btnSearch").click(function(){
				var startDate = $("#txtSearchStart").val();
				var endDate = $("#txtSearchEnd").val();
				if(startDate == "")
				{
					alert("Start Date Empty..!!");
					return false;
				}
				if(endDate == "")
				{
					alert("End Date Empty..!!");
					return false;
				}
				$("#idLoading").show();
				$.post('<?php echo base_url("requestOffice/getRequest"); ?>/search/',
				{ startDate : startDate,endDate : endDate },
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
		function addDetail(id)
		{
			$("#idDataTable").hide();
			$("#idFormDetail").show(200);
			$("#txtIdReq").val(id);
		}
		function addRowDetail()
		{
			var id = $("#txtIdField").val();
			$("#idLoading").show();
			$(':button').prop('disabled', true);
			
			$.post('<?php echo base_url("requestOffice/addField"); ?>',
			{ idField : id },
				function(data) 
				{						
					$("#idFieldDetail").append(data.divNya);
					$("#txtIdField").val(data.noField);					
					setTimeout(function(){
						$("#idLoading").hide();
						$(':button').prop('disabled', false);
					},1500);			
				},
			"json"
			);
		}
		function submitData(id)
		{
			var cfm = confirm("Yakin di Submit..??");

			if(cfm)
			{
				$("#idLoading").show();

				$.post('<?php echo base_url("requestOffice/submitData"); ?>',
				{ idReq : id },
					function(data) 
					{
						alert(data);
						reloadPage();
					},
				"json"
				);
			}
		}
		function showModal(id)
		{
			$("#idLoading").show();

			$.post('<?php echo base_url("requestOffice/getModalDetailReq"); ?>',
			{ idReq : id },
				function(data) 
				{
					$("#idTbodyDetailReq").empty();
					$("#idTbodyDetailReq").append(data.trNya);
					$("#idUsrApp").text(data.userApprove);
					$("#idLoading").hide();
					$('#modalReqDetail').modal('show');
				},
			"json"
			);
		}
		function editData(id)
		{
			$("#idLoading").show();
			$("#lblForm").text("Edit Data");			
			
			$.post('<?php echo base_url("requestOffice/editData"); ?>',
			{ id : id, typeEdit : "editReq" },
				function(data) 
				{
					$("#idDataTable").hide();
					$.each(data, function(i, item)
					{
						$("#txtIdEdit").val(item.id);
						$("#slcVessel").val(item.vessel);
						$("#txtDateReq").val(item.date_request);
						$("#txtAppNo").val(item.app_no);
						$("#txtDept").val(item.department);
					});
					$("#idLoading").hide();
					$("#idForm").show(200);
				},
			"json"
			);
		}
		function editDataDetail(id)
		{
			$("#idLoading").show();
			$("#lblForm").text("Edit Data Detail");			
			
			$.post('<?php echo base_url("requestOffice/editData"); ?>',
			{ id : id, typeEdit : "editReqDetail" },
				function(data) 
				{
					$("#idDataTable").hide();
					$("#idFieldDetail").empty();
					$("#idFieldDetail").append(data.divNya);
					$("#txtIdField").val(data.idField);
					$("#txtIdReq").val(data.idReq);
					$("#idLoading").hide();
					$("#idFormDetail").show(200);
				},
			"json"
			);
		}
		function delData(id)
		{
			var cfm = confirm("Yakin Hapus..??");
			if(cfm)
			{
				$.post('<?php echo base_url("requestOffice/delData"); ?>',
				{ id : id,typeDel : "delReq" },
				function(data) 
				{
					reloadPage();
				},
				"json"
				);
			}			
		}
		function showModalApproveOffice(){
			
		}
		function removeDetail(id,idDel = "")
		{
			$("#idLoading").show();
			$("#idRemove_"+id).empty();			
			if(idDel != "")
			{
				$.post('<?php echo base_url("requestOffice/delData"); ?>',
				{ id : id,idDel : idDel,typeDel : "delReqDetail" },
				function(data)
				{
					alert(data);
				},
				"json"
				);
			}
			$("#idLoading").hide();
		}
		function modalUploadFile(id)
		{
			getDataFile(id);
			$('#modalUploadFile').modal('show');
		}
		function saveFileModal()
		{
			var formData = new FormData();

			var idReq = $("#txtIdReqModalFile").val();
			var idDetReq = $("#slcFileUpload").val();
			var fileUpload = $("#uploadFile").val();

			if(idDetReq == "")
			{
				alert("Select Item Empty..!!");
				return false;
			}

			if(fileUpload == "")
			{
				alert("File Upload Empty..!!");
				return false;
			}

			formData.append('idDetReq',idDetReq);
			formData.append('cekFileUpload',fileUpload);
			formData.append('fileUpload',$("#uploadFile").prop('files')[0]);

			$("#idLoadingModalFile").show();

			$.ajax("<?php echo base_url('requestOffice/saveFile'); ?>",{
            	method: "POST",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response){
                    alert(response);
                    getDataFile(idReq);
                    $('#uploadFile').val('');
                    $("#idLoadingModalFile").hide();
                }
        	});
		}
		function getDataFile(id)
		{
			$.post('<?php echo base_url("requestOffice/getDataFile"); ?>',
			{ id : id },
				function(data) 
				{
					$("#txtIdReqModalFile").val(id);
					$("#idTbodyFile").empty();
					$("#slcFileUpload").empty();

					$("#idTbodyFile").append(data.trNya);
					$("#slcFileUpload").append(data.opt);
				},
			"json"
			);
		}
		function delFile(idReq = '',idDet = '',nmFile = '')
		{
			var cfm = confirm("Yakin Hapus File..??");
			if(cfm)
			{
				$("#idLoadingModalFile").show();

				$.post('<?php echo base_url("requestOffice/delFile"); ?>',
				{ idDet : idDet,nmFile : nmFile },
					function(data) 
					{
						alert(data);
	                    getDataFile(idReq);
	                    $("#idLoadingModalFile").hide();
					},
				"json"
				);
			}
		}
		function reloadPage()
		{
			window.location = "<?php echo base_url('requestOffice/');?>";
		}
	</script>
</head>
<body>
	<section id="container">
		<section id="main-content">
			<section class="wrapper site-min-height" style="min-height:400px;">
				<h3>
					<i class="fa fa-angle-right"></i> Request Office<span style="display:none;padding-left:20px;" id="idLoading"><img src="<?php echo base_url('assets/img/loading.gif'); ?>" ></span>
				</h3>
				<div class="form-panel" id="idDataTable">
					<div class="row">
						<div class="col-md-2" id="btnNavAtas">
							<button type="button" id="btnAddData" class="btn btn-primary btn-sm btn-block" title="Add"><i class="fa fa-plus-square"></i> Add Data</button>
						</div>
						<div class="col-md-2">
							<input type="text" class="form-control input-sm" id="txtDateSearchStart" value="" placeholder="Start Date">
						</div>
						<div class="col-md-2">
							<input type="text" class="form-control input-sm" id="txtDateSearchEnd" value="" placeholder="End Date">
						</div>
						<div class="col-md-2">
							<button type="button" id="btnSearch" class="btn btn-warning btn-sm btn-block" title="Add"><i class="fa fa-search"></i> Search</button>
						</div>
						<div class="col-md-2">
							<button type="button" onclick="reloadPage();" id="btnSearch" class="btn btn-success btn-sm btn-block" title="Add"><i class="fa fa-refresh"></i> Refresh</button>
						</div>
					</div>
					<div class="row mt" id="idData1">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
									<thead>
										<tr style="background-color: #A70000;color: #FFF;height:40px;">
											<th style="vertical-align:middle;width:5%;text-align:center;" colspan="2">No</th>
											<th style="vertical-align:middle;width:10%;text-align:center;">Req. Date</th>
											<th style="vertical-align:middle;width:15%;text-align:center;">App. No</th>
											<th style="vertical-align:middle;width:15%;text-align:center;">Vessel</th>
											<th style="vertical-align:middle;width:10%;text-align:center;">Department</th>
											<th style="vertical-align:middle;width:10%;text-align:center;">Status</th>
											<th style="vertical-align:middle;width:10%;text-align:center;">Required</th>
											<th style="vertical-align:middle;width:25%;text-align:center;">Action</th>
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
				<div class="form-panel" id="idForm" style="display:none;">
					<div class="row">
						<div class="col-md-12">
							<legend><label id="lblForm"> Add Data</label></legend>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
								    <label for="slcVessel"><u>Vessel :</u></label>
								    <select name="slcVessel" id="slcVessel" class="form-control input-sm">
								    	<option value="-">- Select -</option>
								    	<?php echo $optVsl; ?>
								    </select>
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
								    <label for="txtDateReq"><u>Request Date :</u></label>
								    <input type="text" class="form-control input-sm" id="txtDateReq" value="" placeholder="Req. Date">
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
								    <label for="txtAppNo"><u>App No :</u></label>
								    <input type="text" class="form-control input-sm" id="txtAppNo" value="" placeholder="Req. No">
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
								    <label for="txtDept"><u>Department :</u></label>
								    <select name="txtDept" id="txtDept" class="form-control input-sm">
								    	<option value="PURCHASING">PURCHASING</option>
								    </select>
								</div>
							</div>
							</fieldset>
							<div class="col-md-12 col-xs-12">
								<div class="form-group" align="center">
									<input type="hidden" name="" id="txtIdEdit" value="">
									<button id="btnSave" class="btn btn-primary btn-sm" name="btnSave" title="Save">
										<i class="fa fa-check-square-o"></i>
										Save
									</button>
									<button id="btnCancel" onclick="reloadPage();" class="btn btn-danger btn-sm" name="btnCancel" title="Cancel">
										<i class="fa fa-ban"></i>
										Cancel
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-panel" id="idFormDetail" style="display:none;">
					<div id="idFieldDetail">
						<div class="row">
							<input type="hidden" name="txtIdEdit[]" id="txtIdEdit" value="">
							<div class="col-md-12">
								<legend><label id="lblForm"> Add Data Detail</label></legend>
								<div class="col-md-1 col-xs-12">
									<div class="form-group">
									    <label for="txtCodePartNo"><u>Part No:</u></label>
									    <input type="text" name="txtCodePartNo[]" class="form-control input-sm" id="txtCodePartNo" value="" placeholder="Code / Part No">
									</div>
								</div>
								<div class="col-md-2 col-xs-12">
									<div class="form-group">
									    <label for="txtNameArticle"><u>Name of Article:</u></label>
									    <input type="text" name="txtNameArticle[]" class="form-control input-sm" id="txtNameArticle" value="" placeholder="Name of Article">
									</div>
								</div>
								<div class="col-md-1 col-xs-12">
									<div class="form-group">
									    <label for="txtUnit"><u>Unit:</u></label>
									    <input type="text" name="txtUnit[]" class="form-control input-sm" id="txtUnit" value="">
									</div>
								</div>
								<div class="col-md-1 col-xs-12">
									<div class="form-group">
									    <label for="txtWorkOnBoard"><u>Working:</u></label>
									    <input type="text" name="txtWorkOnBoard[]" onkeypress="javascript:return isNumber(event)" class="form-control input-sm" id="txtWorkOnBoard" value="0">
									</div>
								</div>
								<div class="col-md-1 col-xs-12">
									<div class="form-group">
									    <label for="txtStockOnBoard"><u>Stock:</u></label>
									    <input type="text" name="txtStockOnBoard[]" onkeypress="javascript:return isNumber(event)" class="form-control input-sm" id="txtStockOnBoard" value="0">
									</div>
								</div>
								<div class="col-md-1 col-xs-12">
									<div class="form-group">
									    <label for="txtTotalReq"><u>Request:</u></label>
									    <input type="text" name="txtTotalReq[]" onkeypress="javascript:return isNumber(event)" class="form-control input-sm" id="txtTotalReq" value="0">
									</div>
								</div>
								<div class="col-md-2 col-xs-12">
									<div class="form-group">
									    <label for="txtTotalReq"><u>Mark Reference:</u></label>
									    <select name="slcMarkDetail[]" id="slcMarkDetail" class="form-control input-sm">
									    	<option value="">- Select -</option>
											<option value="A">TO BE REPLACE URGENTLY</option>
											<option value="B">BETTER TO BE REPLACE</option>
											<option value="C">STOCK FOR NEXT O/HAUL</option>
											<option value="D">STOCK FOR EMERGENCY</option>
									    </select>
									</div>
								</div>
								<div class="col-md-2 col-xs-12">
									<div class="form-group">
									    <label for="txtRemark"><u>Remark:</u></label>
									    <textarea name="txtRemark[]" class="form-control input-sm" id="txtRemark"></textarea>
									</div>
								</div>
								<div class="col-md-1 col-xs-12">
									<div class="form-group">
										<label for="txtTotalReq" style="font-weight: bold;">&nbsp</label>
										<button class="btn btn-primary btn-block btn-xs" title="Add" id="btnAddField" onclick="addRowDetail();" type="button"><i class="glyphicon glyphicon-plus"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 col-xs-12">
							<div class="form-group" align="center">
								<input type="hidden" name="txtIdReq" id="txtIdReq" value="">
								<input type="hidden" name="txtIdField" id="txtIdField" value="1">
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
			</section>
		</section>
	</section>
	<div class="modal fade" id="modalReqDetail" role="dialog">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	        	<div class="modal-header" style="padding: 10px;background-color:#A70000;">
	          		<button type="button" class="close" data-dismiss="modal" style="opacity:unset;text-shadow:none;color:#FFF;">&times;</button>
	          		<h4 class="modal-title">Data Detail Request</h4>
	        	</div>
	        	<div class="modal-body" id="idModalDetail">
	        		<div class="row">
						<div class="col-md-12">
							<legend style="text-align: right;"><label id="lblModal">View Data</label></legend>
							<div class="table-responsive">
								<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
									<thead>
										<tr style="background-color: #000;color: #FFF;height:40px;">
											<th style="vertical-align: middle; width:3%;text-align:center;">No</th>
											<th style="vertical-align: middle; width:25%;text-align:center;">Name of Article</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Code / Part No</th>
											<th style="vertical-align: middle; width:5%;text-align:center;">Unit</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Working on Board</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Stock on Board</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Request</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Approved Order</th>
											<th style="vertical-align: middle; width:8%;text-align:center;">Mark Reference</th>
											<th style="vertical-align: middle; width:20%;text-align:center;">Remark</th>
										</tr>
									</thead>
									<tbody id="idTbodyDetailReq">
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-md-4"></div>
						<div class="col-md-4" align="center">
							<button id="btnCancelModalDetail" onclick="reloadPage();" class="btn btn-danger btn-xs btn-block" title="Close">
								<i class="fa fa-ban"></i> Close</button>
						</div>						
						<div class="col-md-4" style="font-size:11px;" align="right">Approve By : <label id="idUsrApp" style="font-weight:bold;"></label></div>
						<div class="col-md-12" style="font-size:10px;">
							NOTE : 	<br>
							- A = TO BE REPLACE URGENTLY<br>
							- B = BETTER TO BE REPLACE<br>
							- C = STOCK FOR NEXT O/HAUL<br>
							- D = STOCK FOR EMERGENCY
						</div>
					</div>
	        	</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalUploadFile" role="dialog">
		<div class="modal-dialog modal-lg">
	    	<div class="modal-content">
	        	<div class="modal-header" style="padding: 10px;background-color:#A70000;">
	          		<button type="button" class="close" data-dismiss="modal" style="opacity:unset;text-shadow:none;color:#FFF;">&times;</button>
	          		<h4 class="modal-title">Detail Item</h4>
	        	</div>
	        	<div class="modal-body" id="idModalFile">
	        		<legend style="text-align: right;"><h4><img id="idLoadingModalFile" src="<?php echo base_url('assets/img/loading.gif'); ?>" style="display:none;"> Upload File</h4></legend>
	        		<div class="row">
	        			<div class="col-md-4 col-xs-12">
	        				<select name="slcFileUpload" id="slcFileUpload" class="form-control input-sm" style="margin-top:5px;">
							</select>
	        			</div>
	        			<div class="col-md-3 col-xs-12">
	        				<input type="File" class="form-control input-sm" value="" id="uploadFile" style="margin-top:5px;">
	        			</div>
	        			<div class="col-md-1 col-xs-12">
	        				<button class="btn btn-warning btn-xs btn-block" onclick="$('#uploadFile').val('');" style="margin-top:5px;">Clear</button>
	        			</div>
	        			<div class="col-md-2 col-xs-12">
	        				<button class="btn btn-info btn-xs btn-block" onclick="saveFileModal();" style="margin-top:5px;">Submit</button>
	        			</div>
	        		</div>
	        		<div class="row" style="margin-top:10px;">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-border table-striped table-bordered table-condensed table-advance table-hover">
									<thead>
										<tr style="background-color: #000;color: #FFF;height:40px;">
											<th style="vertical-align: middle; width:5%;text-align:center;">No</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">Code / Part No</th>
											<th style="vertical-align: middle; width:40%;text-align:center;">Name of Article</th>
											<th style="vertical-align: middle; width:10%;text-align:center;">File</th>
										</tr>
									</thead>
									<tbody id="idTbodyFile">
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-md-12" align="center">
							<input type="hidden" id="txtIdReqModalFile" value="">
							<button id="btnCancelModalFile" onclick="reloadPage();" class="btn btn-danger btn-sm" title="Close">
								<i class="fa fa-ban"></i> Close</button>
						</div>
					</div>
	        	</div>
			</div>
		</div>
	</div>
</body>
</html>

